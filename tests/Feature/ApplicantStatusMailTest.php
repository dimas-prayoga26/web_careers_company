<?php

namespace Tests\Feature;

use App\Mail\ApplicantStatusMail;
use App\Models\Applicant;
use App\Models\ApplicantStatus;
use App\Models\JobVacancy;
use Tests\TestCase;

class ApplicantStatusMailTest extends TestCase
{
    /**
     * @return array<string, string>
     */
    private function brand(): array
    {
        return [
            'name' => 'Coffeeniskala',
            'email' => 'noreply@coffeeniskala.com',
            'logo' => 'assets/logo.png',
        ];
    }

    public function test_it_displays_only_the_current_applicant_status(): void
    {
        $applicant = new Applicant([
            'full_name' => 'Dwi Saputra',
            'email' => 'dwi@example.com',
            'created_at' => now(),
        ]);
        $applicant->setRelation('status', new ApplicantStatus([
            'value' => 3,
            'name' => 'User Interview',
        ]));
        $applicant->setRelation('jobVacancy', new JobVacancy([
            'name' => 'IT Programmer',
        ]));

        $mailable = new ApplicantStatusMail($applicant, $this->brand());

        $mailable->assertHasSubject('Status Lamaran Anda: User Interview - Coffeeniskala');
        $mailable->assertSeeInHtml('Status Saat Ini');
        $mailable->assertSeeInHtml('User Interview');
        $mailable->assertSeeInHtml('IT Programmer');
        $mailable->assertDontSeeInHtml('Track Record Lamaran');
        $mailable->assertDontSeeInHtml('Diterima');
    }
}
