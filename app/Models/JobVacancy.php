<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasUuids;

    public const STATUS_ACTIVE = 1;

    public const STATUS_INACTIVE = 2;

    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * @param  Builder<JobVacancy>  $query
     * @return Builder<JobVacancy>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }
}
