<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'status',
        'legacy_created_at',
    ];
}
