<?php

namespace App\Http\Controllers;

use App\Mail\ApplicantStatusMail;
use App\Models\Applicant;
use App\Models\ApplicantDocument;
use App\Models\ApplicantStatus;
use App\Models\Company;
use App\Models\EducationLevel;
use App\Models\Gender;
use App\Models\JobVacancy;
use App\Models\MaritalStatus;
use App\Support\CareerBrand;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CareerController extends Controller
{
    public function index(Request $request): View
    {
        return view('careers.index', $this->formOptions($request));
    }

    public function store(Request $request): RedirectResponse
    {
        $brand = CareerBrand::resolve($request);
        $company = $this->companyForRequest($request, $brand);

        $request->merge([
            'expected_salary' => preg_replace('/\D/', '', (string) $request->input('expected_salary')),
        ]);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'brand_key' => ['nullable', 'string', 'max:64'],
            'nickname' => ['required', 'string', 'max:255'],
            'pob' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{1,13}$/'],
            'gender' => ['required', 'integer', 'exists:meta_data_gender,id'],
            'marital_status' => ['required', 'integer', 'exists:meta_data_marital_statuses,id'],
            'address' => ['required', 'string'],
            'educational_level' => ['required', 'array', 'min:1'],
            'educational_level.*' => ['required', 'uuid', 'exists:education_levels,id'],
            'educational_institution' => ['required', 'array', 'min:1'],
            'educational_institution.*' => ['required', 'string'],
            'department' => ['required', 'array', 'min:1'],
            'department.*' => ['required', 'string'],
            'gpa' => ['required', 'array', 'min:1'],
            'gpa.*' => ['required', 'string'],
            'start_education' => ['required', 'array', 'min:1'],
            'start_education.*' => ['required', 'integer', 'between:1900,2099'],
            'graduate_education' => ['required', 'array', 'min:1'],
            'graduate_education.*' => ['required', 'integer', 'between:1900,2099'],
            'company_name' => ['required', 'array', 'min:1'],
            'company_name.*' => ['required', 'string'],
            'role' => ['required', 'array', 'min:1'],
            'role.*' => ['required', 'string'],
            'company_location' => ['required', 'array', 'min:1'],
            'company_location.*' => ['required', 'string'],
            'start_date' => ['required', 'array', 'min:1'],
            'start_date.*' => ['required', 'date_format:Y-m'],
            'end_date' => ['required', 'array', 'min:1'],
            'end_date.*' => ['required', 'date_format:Y-m'],
            'job_vacancy_id' => [
                'nullable',
                'uuid',
                Rule::exists((new JobVacancy)->getTable(), 'id')->where(function ($query) use ($company): void {
                    $query->where('status', JobVacancy::STATUS_ACTIVE);

                    if ($company) {
                        $query->where('company_id', $company->getKey());

                        return;
                    }

                    $query->whereRaw('1 = 0');
                }),
            ],
            'expected_salary' => ['required', 'numeric', 'min:0'],
            'self_resume' => ['required', 'string'],
            'portfolio_web_address' => ['required', 'url', 'regex:/^https:\/\//i', 'max:1000'],
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'agreement' => ['required', 'array', 'size:5'],
            'agreement.*' => ['required', 'in:1,2,3,4,5'],
            'cf-turnstile-response' => ['required', 'string'],
        ]);

        if (! $this->verifyTurnstile($request)) {
            return back()
                ->withErrors(['cf-turnstile-response' => 'Cloudflare verification failed.'])
                ->withInput();
        }

        $storedFiles = [];

        try {
            $applicant = DB::transaction(function () use ($request, $validated, $brand, &$storedFiles): Applicant {
                $now = now();
                $nameSlug = Str::slug($validated['full_name']) ?: 'applicant';
                $random = Str::random(5);

                $photoDocument = $this->moveUploadedFile($request, 'photo', 'files/photo', $nameSlug, $random);
                $storedFiles[] = $photoDocument['file_path'];

                $cvDocument = $this->moveUploadedFile($request, 'cv', 'files/cv', $nameSlug, $random);
                $storedFiles[] = $cvDocument['file_path'];

                $applicant = Applicant::create([
                    'job_vacancy_id' => $validated['job_vacancy_id'] ?? null,
                    'brand_key' => $brand['key'],
                    'slug' => $this->uniqueSlug($validated['full_name']),
                    'applicant_status_id' => ApplicantStatus::where('value', 0)->value('id'),
                    'full_name' => $validated['full_name'],
                    'nickname' => $validated['nickname'],
                    'place_of_birth' => $validated['pob'],
                    'date_of_birth' => $validated['dob'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'gender_id' => $validated['gender'],
                    'marital_status_id' => $validated['marital_status'],
                    'address' => $validated['address'],
                    'expected_salary' => $validated['expected_salary'],
                    'self_resume' => $validated['self_resume'] ?? null,
                    'portfolio_web_address' => $validated['portfolio_web_address'] ?? null,
                    'agreement' => implode('||', $validated['agreement']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $applicant->documents()->create(array_merge($photoDocument, [
                    'document_type' => ApplicantDocument::TYPE_PHOTO,
                    'uploaded_at' => $now,
                ]));

                $applicant->documents()->create(array_merge($cvDocument, [
                    'document_type' => ApplicantDocument::TYPE_CV,
                    'uploaded_at' => $now,
                ]));

                foreach ($validated['educational_level'] as $index => $educationLevelId) {
                    $applicant->educations()->create([
                        'education_level_id' => $educationLevelId,
                        'sequence' => $index + 1,
                        'institution' => $validated['educational_institution'][$index] ?? null,
                        'gpa' => $validated['gpa'][$index] ?? null,
                        'department' => $validated['department'][$index] ?? null,
                        'start_period' => $validated['start_education'][$index] ?? null,
                        'graduate_period' => $validated['graduate_education'][$index] ?? null,
                    ]);
                }

                foreach ($validated['company_name'] as $index => $companyName) {
                    $applicant->workExperiences()->create([
                        'sequence' => $index + 1,
                        'company_name' => $companyName,
                        'role' => $validated['role'][$index] ?? null,
                        'company_location' => $validated['company_location'][$index] ?? null,
                        'start_period' => $validated['start_date'][$index] ?? null,
                        'end_period' => $validated['end_date'][$index] ?? null,
                    ]);
                }

                return $applicant->load(['jobVacancy', 'status']);
            });
        } catch (Throwable $exception) {
            foreach ($storedFiles as $filePath) {
                File::delete(public_path($filePath));
            }

            report($exception);

            return redirect()->route('careers.failed', [
                'message' => 'Failed to save registration data. Please try again.',
            ])->withInput();
        }

        try {
            Mail::mailer($this->mailerForBrand($brand))
                ->to($applicant->email)
                ->send(new ApplicantStatusMail($applicant, $brand));
        } catch (Throwable $exception) {
            report($exception);
        }

        return redirect()->route('careers.success');
    }

    public function success(Request $request): View
    {
        return view('careers.success', [
            'brand' => CareerBrand::resolve($request),
        ]);
    }

    public function failed(Request $request): View
    {
        return view('careers.failed', [
            'brand' => CareerBrand::resolve($request),
            'message' => $request->query('message', 'Failed to upload'),
        ]);
    }

    /**
     * @return array{brand: array<string, mixed>, genders: Collection, maritalStatuses: Collection, educationLevels: Collection, jobVacancies: Collection, turnstileSiteKey: mixed}
     */
    private function formOptions(Request $request): array
    {
        $brand = CareerBrand::resolve($request);

        return [
            'brand' => $brand,
            'genders' => $this->activeGenders(),
            'maritalStatuses' => $this->activeMaritalStatuses(),
            'educationLevels' => $this->educationLevels(),
            'jobVacancies' => $this->activeJobVacancies($request, $brand),
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ];
    }

    private function activeGenders(): Collection
    {
        try {
            return Gender::where('is_active', true)->orderBy('id')->get();
        } catch (QueryException) {
            return collect();
        }
    }

    private function activeMaritalStatuses(): Collection
    {
        try {
            return MaritalStatus::where('is_active', true)->orderBy('id')->get();
        } catch (QueryException) {
            return collect();
        }
    }

    private function educationLevels(): Collection
    {
        try {
            return EducationLevel::orderBy('name')->get();
        } catch (QueryException) {
            return collect();
        }
    }

    /**
     * @param  array<string, mixed>  $brand
     */
    private function activeJobVacancies(Request $request, array $brand): Collection
    {
        try {
            $company = $this->companyForRequest($request, $brand);

            if (! $company) {
                return collect();
            }

            return JobVacancy::active()
                ->forCompany($company)
                ->orderBy('name')
                ->get();
        } catch (QueryException) {
            return collect();
        }
    }

    /**
     * @param  array<string, mixed>  $brand
     */
    private function companyForRequest(Request $request, array $brand): ?Company
    {
        $websiteHosts = $this->companyWebsiteHosts($request, $brand);

        if ($websiteHosts === []) {
            return null;
        }

        try {
            return Company::where('is_active', true)
                ->whereNotNull('website')
                ->get(['id', 'website'])
                ->first(fn (Company $company): bool => in_array(
                    $this->normalizedWebsiteHost($company->website),
                    $websiteHosts,
                    true,
                ));
        } catch (QueryException) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $brand
     * @return list<string>
     */
    private function companyWebsiteHosts(Request $request, array $brand): array
    {
        $hosts = [
            $this->normalizedCareerHost($request->host()),
            $this->normalizedWebsiteHost((string) ($brand['website'] ?? '')),
        ];

        return array_values(array_unique(array_filter($hosts)));
    }

    private function normalizedCareerHost(string $host): ?string
    {
        $host = $this->normalizedHost($host);

        if (! $host) {
            return null;
        }

        if (Str::startsWith($host, 'careers.')) {
            $host = Str::after($host, 'careers.');
        }

        return $this->normalizedHost($host);
    }

    private function normalizedWebsiteHost(?string $website): ?string
    {
        $website = trim((string) $website);

        if ($website === '') {
            return null;
        }

        if (! Str::contains($website, '://')) {
            $website = 'https://'.$website;
        }

        return $this->normalizedHost((string) parse_url($website, PHP_URL_HOST));
    }

    private function normalizedHost(string $host): ?string
    {
        $host = Str::of($host)
            ->lower()
            ->before(':')
            ->trim('.')
            ->toString();

        if ($host === '') {
            return null;
        }

        return Str::startsWith($host, 'www.')
            ? Str::after($host, 'www.')
            : $host;
    }

    private function verifyTurnstile(Request $request): bool
    {
        $secretKey = config('services.turnstile.secret_key');

        if (! $secretKey) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secretKey,
                    'response' => $request->input('cf-turnstile-response'),
                    'remoteip' => $request->ip(),
                ]);

            return (bool) $response->json('success');
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * @return array{file_path: string, original_name: string, mime_type: string|null, file_size: int|null}
     */
    private function moveUploadedFile(Request $request, string $field, string $directory, string $nameSlug, string $random): array
    {
        $file = $request->file($field);
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeOriginalName = Str::slug($originalName) ?: $field;
        $filename = now()->format('ymdHis').'_'.$nameSlug.'_'.$random.'_'.$safeOriginalName.'.'.$extension;
        $targetDirectory = public_path($directory);
        $filePath = $directory.'/'.$filename;
        $document = [
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ];

        File::ensureDirectoryExists($targetDirectory);
        $file->move($targetDirectory, $filename);

        return $document;
    }

    private function uniqueSlug(string $fullName): string
    {
        $base = now()->format('ymdHis').'-'.(Str::slug($fullName) ?: 'applicant');
        $slug = $base;

        while (Applicant::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(5));
        }

        return $slug;
    }

    /**
     * @param  array<string, mixed>  $brand
     */
    private function mailerForBrand(array $brand): string
    {
        return (string) ($brand['mailer'] ?? config('mail.default'));
    }
}
