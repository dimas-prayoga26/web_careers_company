<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CareerFormOptionsTest extends TestCase
{
    public function test_career_tables_do_not_use_legacy_columns(): void
    {
        foreach (['applicants', 'job_vacancies', 'education_levels'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->markTestSkipped('Career tables are not available in this test database.');
            }
        }

        $legacyColumns = [
            'applicants' => ['legacy_applicant_id', 'job_applied_legacy_value', 'legacy_created_at'],
            'job_vacancies' => ['legacy_vacancy_id', 'legacy_value', 'legacy_status_value', 'legacy_created_at'],
            'education_levels' => ['legacy_education_level_id', 'legacy_value', 'legacy_created_at'],
        ];

        foreach ($legacyColumns as $table => $columns) {
            foreach ($columns as $column) {
                $this->assertFalse(
                    Schema::hasColumn($table, $column),
                    "{$table}.{$column} should not exist.",
                );
            }
        }
    }

    public function test_career_form_shows_gender_and_marital_status_options(): void
    {
        if (! Schema::hasTable('meta_data_gender') || ! Schema::hasTable('meta_data_marital_statuses')) {
            $this->markTestSkipped('Career metadata tables are not available in this test database.');
        }

        $response = $this->get('https://careers.rnb.co.id/');

        $response->assertOk();
        $response->assertSee('Male');
        $response->assertSee('Female');
        $response->assertSee('Single');
        $response->assertSee('Married');
    }
}
