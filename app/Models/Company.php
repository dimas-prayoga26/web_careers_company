<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'legal_name',
        'city',
        'province',
        'postal_code',
        'country',
        'industry',
        'primary_color',
        'secondary_color',
        'vision',
        'mission',
        'description',
        'phone',
        'email',
        'website',
        'is_active',
    ];

    public function jobVacancies(): HasMany
    {
        return $this->hasMany(JobVacancy::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
