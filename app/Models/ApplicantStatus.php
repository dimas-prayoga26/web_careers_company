<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ApplicantStatus extends Model
{
    use HasUuids;

    protected $fillable = [
        'value',
        'name',
    ];
}
