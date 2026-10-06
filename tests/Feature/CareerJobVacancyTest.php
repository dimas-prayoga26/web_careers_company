<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobVacancy;
use Tests\TestCase;

class CareerJobVacancyTest extends TestCase
{
    public function test_active_scope_filters_to_active_job_vacancies(): void
    {
        $query = JobVacancy::active();

        $this->assertStringContainsString('where', $query->toSql());
        $this->assertStringContainsString('status', $query->toSql());
        $this->assertSame([JobVacancy::STATUS_ACTIVE], $query->getBindings());
    }

    public function test_company_scope_filters_to_company_job_vacancies(): void
    {
        $company = new Company;
        $company->id = 'company-id';

        $query = JobVacancy::forCompany($company);

        $this->assertStringContainsString('where', $query->toSql());
        $this->assertStringContainsString('company_id', $query->toSql());
        $this->assertSame(['company-id'], $query->getBindings());
    }
}
