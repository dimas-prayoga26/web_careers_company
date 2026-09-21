<?php

namespace Database\Seeders;

use App\Models\JobVacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobVacancySeeder extends Seeder
{
    /**
     * Seed the job vacancies shown in the careers form.
     */
    public function run(): void
    {
        $now = now();

        $vacancies = [
            ['name' => 'Video Editor 3D Modelling', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Creative Design', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Accounting', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'IT Programmer', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Web Developer', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Interior Design', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Mobile Developer', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Marketing', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Graphics Designer & Illustrator', 'status' => JobVacancy::STATUS_ACTIVE],
            ['name' => 'Animasi Film', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => '2D 3D Animator', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Administrasi', 'status' => JobVacancy::STATUS_ACTIVE],
            ['name' => 'Marketing Communication', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Social Media Specialist', 'status' => JobVacancy::STATUS_INACTIVE],
            ['name' => 'Driver', 'status' => JobVacancy::STATUS_INACTIVE],
        ];

        foreach ($vacancies as $vacancy) {
            $payload = [
                'name' => $vacancy['name'],
                'status' => $vacancy['status'],
                'updated_at' => $now,
            ];

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
