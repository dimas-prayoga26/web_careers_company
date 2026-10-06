<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobVacancy extends Model
{
    use HasUuids;

    public const STATUS_ACTIVE = 1;

    public const STATUS_INACTIVE = 2;

    protected $fillable = [
        'company_id',
        'name',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @param  Builder<JobVacancy>  $query
     * @return Builder<JobVacancy>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * @param  Builder<JobVacancy>  $query
     * @return Builder<JobVacancy>
     */
    public function scopeForCompany(Builder $query, Company $company): Builder
    {
        return $query->where('company_id', $company->getKey());
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
