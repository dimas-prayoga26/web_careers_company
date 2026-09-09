<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    protected $table = 'meta_data_marital_statuses';

    protected $fillable = [
        'name',
        'is_active',
    ];
}
