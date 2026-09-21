<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'job_vacancy_id',
        'brand_key',
        'slug',
        'applicant_status_id',
        'full_name',
        'nickname',
        'place_of_birth',
        'date_of_birth',
        'email',
        'phone',
        'gender_id',
        'marital_status_id',
        'address',
        'expected_salary',
        'self_resume',
        'portfolio_web_address',
        'cv',
        'photo',
        'agreement',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(ApplicantStatus::class, 'applicant_status_id');
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function maritalStatus(): BelongsTo
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id');
    }

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(ApplicantEducation::class);
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(ApplicantWorkExperience::class);
    }
}
