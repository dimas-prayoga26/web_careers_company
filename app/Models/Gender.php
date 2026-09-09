<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'meta_data_gender';

    protected $fillable = [
        'name',
        'is_active',
    ];
}
