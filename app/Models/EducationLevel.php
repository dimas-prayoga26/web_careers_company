<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EducationLevel extends Model
{
    use HasUuids;

    protected $fillable = [
        'legacy_education_level_id',
        'legacy_value',
        'name',
        'legacy_created_at',
    ];
}
