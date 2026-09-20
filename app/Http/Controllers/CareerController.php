<?php

namespace App\Http\Controllers;

use App\Mail\ApplicantStatusMail;
use App\Models\Applicant;
use App\Models\ApplicantStatus;
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

        $request->merge([
            'expected_salary' => preg_replace('/\D/', '', (string) $request->input('expected_salary')),
        ]);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
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
            'job_vacancy_id' => ['nullable', 'uuid', 'exists:job_vacancies,id'],
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
            $applicant = DB::transaction(function () use ($request, $validated, &$storedFiles): Applicant {
                $now = now();
                $nameSlug = Str::slug($validated['full_name']) ?: 'applicant';
                $random = Str::random(5);

                $photoName = $this->moveUploadedFile($request, 'photo', 'files/photo', $nameSlug, $random);
                $storedFiles[] = ['files/photo', $photoName];

                $cvName = $this->moveUploadedFile($request, 'cv', 'files/cv', $nameSlug, $random);
                $storedFiles[] = ['files/cv', $cvName];

                $legacyApplicantId = ((int) Applicant::withTrashed()
                    ->lockForUpdate()
                    ->max('legacy_applicant_id')) + 1;

                $applicant = Applicant::create([
                    'legacy_applicant_id' => $legacyApplicantId,
                    'job_vacancy_id' => $validated['job_vacancy_id'] ?? null,
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
                    'cv' => $cvName,
                    'photo' => $photoName,
                    'agreement' => implode('||', $validated['agreement']),
                    'legacy_created_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

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
            foreach ($storedFiles as [$directory, $filename]) {
                File::delete(public_path($directory.'/'.$filename));
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

        try {
            return [
                'brand' => $brand,
                'genders' => Gender::where('is_active', true)->orderBy('id')->get(),
                'maritalStatuses' => MaritalStatus::where('is_active', true)->orderBy('id')->get(),
                'educationLevels' => EducationLevel::orderBy('legacy_value')->orderBy('name')->get(),
                'jobVacancies' => JobVacancy::orderBy('name')->get(),
                'turnstileSiteKey' => config('services.turnstile.site_key'),
            ];
        } catch (QueryException) {
            return [
                'brand' => $brand,
                'genders' => collect(),
                'maritalStatuses' => collect(),
                'educationLevels' => collect(),
                'jobVacancies' => collect(),
                'turnstileSiteKey' => config('services.turnstile.site_key'),
            ];
        }
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

    private function moveUploadedFile(Request $request, string $field, string $directory, string $nameSlug, string $random): string
    {
        $file = $request->file($field);
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeOriginalName = Str::slug($originalName) ?: $field;
        $filename = now()->format('ymdHis').'_'.$nameSlug.'_'.$random.'_'.$safeOriginalName.'.'.$extension;
        $targetDirectory = public_path($directory);

        File::ensureDirectoryExists($targetDirectory);
        $file->move($targetDirectory, $filename);

        return $filename;
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
