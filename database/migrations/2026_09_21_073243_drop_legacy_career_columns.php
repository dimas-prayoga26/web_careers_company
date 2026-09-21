<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dropColumnsIfPresent('applicants', [
            'legacy_applicant_id',
            'job_applied_legacy_value',
            'legacy_created_at',
        ]);

        $this->dropColumnsIfPresent('job_vacancies', [
            'legacy_vacancy_id',
            'legacy_value',
            'legacy_status_value',
            'legacy_created_at',
        ]);

        $this->dropColumnsIfPresent('education_levels', [
            'legacy_education_level_id',
            'legacy_value',
            'legacy_created_at',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('applicants')) {
            Schema::table('applicants', function (Blueprint $table): void {
                if (! Schema::hasColumn('applicants', 'legacy_applicant_id')) {
                    $table->unsignedBigInteger('legacy_applicant_id')->nullable()->after('id');
                }

                if (! Schema::hasColumn('applicants', 'job_applied_legacy_value')) {
                    $table->unsignedInteger('job_applied_legacy_value')->nullable()->after('address');
                }

                if (! Schema::hasColumn('applicants', 'legacy_created_at')) {
                    $table->timestamp('legacy_created_at')->nullable()->after('updated_at');
                }
            });
        }

        if (Schema::hasTable('job_vacancies')) {
            Schema::table('job_vacancies', function (Blueprint $table): void {
                if (! Schema::hasColumn('job_vacancies', 'legacy_vacancy_id')) {
                    $table->unsignedBigInteger('legacy_vacancy_id')->nullable()->after('id');
                }

                if (! Schema::hasColumn('job_vacancies', 'legacy_value')) {
                    $table->unsignedInteger('legacy_value')->nullable()->after('legacy_vacancy_id');
                }

                if (! Schema::hasColumn('job_vacancies', 'legacy_status_value')) {
                    $table->unsignedInteger('legacy_status_value')->nullable()->after('status');
                }

                if (! Schema::hasColumn('job_vacancies', 'legacy_created_at')) {
                    $table->timestamp('legacy_created_at')->nullable()->after('updated_at');
                }
            });
        }

        if (Schema::hasTable('education_levels')) {
            Schema::table('education_levels', function (Blueprint $table): void {
                if (! Schema::hasColumn('education_levels', 'legacy_education_level_id')) {
                    $table->unsignedBigInteger('legacy_education_level_id')->nullable()->after('id');
                }

                if (! Schema::hasColumn('education_levels', 'legacy_value')) {
                    $table->unsignedInteger('legacy_value')->nullable()->after('legacy_education_level_id');
                }

                if (! Schema::hasColumn('education_levels', 'legacy_created_at')) {
                    $table->timestamp('legacy_created_at')->nullable()->after('updated_at');
                }
            });
        }
    }

    /**
     * @param  list<string>  $columns
     */
    private function dropColumnsIfPresent(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existingColumns = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn($table, $column),
        ));

        if ($existingColumns === []) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($existingColumns): void {
            $table->dropColumn($existingColumns);
        });
    }
};
