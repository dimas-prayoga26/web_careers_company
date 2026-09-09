<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantEducation extends Model
{
    use HasUuids;

    protected $table = 'applicant_educations';

    protected $fillable = [
        'applicant_id',
        'education_level_id',
        'sequence',
        'institution',
        'gpa',
        'department',
        'start_period',
        'graduate_period',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }
}
