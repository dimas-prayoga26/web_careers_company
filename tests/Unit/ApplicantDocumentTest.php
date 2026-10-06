<?php

namespace Tests\Unit;

use App\Models\Applicant;
use App\Models\ApplicantDocument;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class ApplicantDocumentTest extends TestCase
{
    public function test_applicant_has_many_documents(): void
    {
        $relation = (new Applicant)->documents();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertSame(ApplicantDocument::class, $relation->getRelated()::class);
        $this->assertSame('applicant_id', $relation->getForeignKeyName());
    }

    public function test_applicant_document_belongs_to_applicant(): void
    {
        $relation = (new ApplicantDocument)->applicant();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertSame(Applicant::class, $relation->getRelated()::class);
        $this->assertSame('applicant_id', $relation->getForeignKeyName());
    }

    public function test_applicant_document_accepts_upload_metadata(): void
    {
        $document = new ApplicantDocument([
            'document_type' => ApplicantDocument::TYPE_CV,
            'file_path' => 'files/cv/resume.pdf',
            'original_name' => 'resume.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => '2026-10-05 10:00:00',
        ]);

        $this->assertSame(ApplicantDocument::TYPE_CV, $document->document_type);
        $this->assertSame('files/cv/resume.pdf', $document->file_path);
        $this->assertSame('resume.pdf', $document->original_name);
        $this->assertSame('application/pdf', $document->mime_type);
        $this->assertSame(1024, $document->file_size);
        $this->assertSame('2026-10-05 10:00:00', $document->uploaded_at->format('Y-m-d H:i:s'));
    }
}
