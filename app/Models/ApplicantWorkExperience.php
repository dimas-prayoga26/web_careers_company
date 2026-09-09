<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantWorkExperience extends Model
{
    use HasUuids;

    protected $table = 'applicant_work_experiences';

    protected $fillable = [
        'applicant_id',
        'sequence',
        'company_name',
        'role',
        'company_location',
        'start_period',
        'end_period',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
