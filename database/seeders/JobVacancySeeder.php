<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class JobVacancySeeder extends Seeder
{
    /**
     * Seed the job vacancies shown in the legacy careers form.
     */
    public function run(): void
    {
        $now = now();
        $statusColumn = collect(DB::select("SHOW COLUMNS FROM job_vacancies LIKE 'status'"))->first();
        $usesNumericStatus = $statusColumn && str_contains((string) $statusColumn->Type, 'int');

        $vacancies = [
            ['legacy_vacancy_id' => 2, 'legacy_value' => 2, 'name' => 'Video Editor 3D Modelling', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2023-02-28 04:19:10'],
            ['legacy_vacancy_id' => 7, 'legacy_value' => 7, 'name' => 'Creative Design', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2023-03-10 05:22:48'],
            ['legacy_vacancy_id' => 14, 'legacy_value' => 13, 'name' => 'Accounting', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2024-09-18 16:21:06'],
            ['legacy_vacancy_id' => 18, 'legacy_value' => 16, 'name' => 'IT Programmer', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2026-04-06 15:28:10'],
            ['legacy_vacancy_id' => 15, 'legacy_value' => 14, 'name' => 'Web Developer', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2025-01-22 17:52:02'],
            ['legacy_vacancy_id' => 11, 'legacy_value' => 11, 'name' => 'Interior Design', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2024-05-27 09:14:13'],
            ['legacy_vacancy_id' => 13, 'legacy_value' => 12, 'name' => 'Mobile Developer', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2024-08-05 10:18:11'],
            ['legacy_vacancy_id' => 10, 'legacy_value' => 10, 'name' => 'Marketing', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2024-02-28 15:37:24'],
            ['legacy_vacancy_id' => 17, 'legacy_value' => 15, 'name' => 'Graphics Designer & Illustrator', 'status' => 'active', 'legacy_status_value' => 1, 'legacy_created_at' => '2026-04-06 15:27:59'],
            ['legacy_vacancy_id' => 1, 'legacy_value' => 1, 'name' => 'Animasi Film', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2023-02-28 04:19:10'],
            ['legacy_vacancy_id' => 9, 'legacy_value' => 9, 'name' => '2D 3D Animator', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2023-04-04 14:43:52'],
            ['legacy_vacancy_id' => 3, 'legacy_value' => 3, 'name' => 'Administrasi', 'status' => 'active', 'legacy_status_value' => 1, 'legacy_created_at' => '2023-02-28 04:19:10'],
            ['legacy_vacancy_id' => 6, 'legacy_value' => 6, 'name' => 'Marketing Communication', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2023-02-28 04:19:10'],
            ['legacy_vacancy_id' => 5, 'legacy_value' => 5, 'name' => 'Social Media Specialist', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2023-02-28 04:19:10'],
            ['legacy_vacancy_id' => 8, 'legacy_value' => 8, 'name' => 'Driver', 'status' => 'inactive', 'legacy_status_value' => 2, 'legacy_created_at' => '2023-03-10 05:22:48'],
        ];

        foreach ($vacancies as $vacancy) {
            $payload = [
                'name' => $vacancy['name'],
                'status' => $usesNumericStatus ? $vacancy['legacy_status_value'] : $vacancy['status'],
                'legacy_created_at' => $vacancy['legacy_created_at'],
                'updated_at' => $now,
            ];

            foreach (['legacy_vacancy_id', 'legacy_value', 'legacy_status_value'] as $column) {
                if (Schema::hasColumn('job_vacancies', $column)) {
                    $payload[$column] = $vacancy[$column];
                }
            }

            $existingId = DB::table('job_vacancies')
                ->where('name', $vacancy['name'])
                ->value('id');

            if ($existingId) {
                DB::table('job_vacancies')
                    ->where('id', $existingId)
                    ->update($payload);

                continue;
            }

            DB::table('job_vacancies')->insert(array_merge([
                'id' => (string) Str::uuid(),
                'created_at' => $now,
            ], $payload));
        }
    }
}
