<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class KodeOtpMail extends Mailable
{
    public function __construct(
        public string $kode,
        public int $menit,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Kode Verifikasi PASKA JAYA');
    }

    public function content(): Content
    {
        return new Content(text: 'emails.otp');
    }
}
