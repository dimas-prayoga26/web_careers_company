<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantDocument extends Model
{
    use HasUuids;

    public const TYPE_CV = 'cv';

    public const TYPE_PHOTO = 'photo';

    protected $fillable = [
        'applicant_id',
        'applicant_upload_request_id',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_at',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }
}
