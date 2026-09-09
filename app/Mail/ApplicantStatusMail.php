<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  array<string, mixed>  $brand
     */
    public function __construct(
        public Applicant $applicant,
        public array $brand,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address((string) $this->brand['email'], (string) $this->brand['name']),
            replyTo: [
                new Address((string) $this->brand['email'], (string) $this->brand['name']),
            ],
            subject: $this->subjectText(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.applicants.status',
            with: [
                'applicant' => $this->applicant,
                'brand' => $this->brand,
            ],
        );
    }

    private function subjectText(): string
    {
        return match ((int) $this->applicant->status?->value) {
            1 => 'Anda Diundang Interview - '.$this->brand['name'],
            2 => 'Selamat, Anda Diterima - '.$this->brand['name'],
            default => 'Lamaran Anda Sudah Terkirim - '.$this->brand['name'],
        };
    }
}
