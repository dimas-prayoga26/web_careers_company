<?php

namespace Tests\Feature;

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
}
