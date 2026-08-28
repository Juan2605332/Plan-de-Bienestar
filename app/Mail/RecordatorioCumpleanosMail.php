<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioCumpleanosMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Collection $funcionarios) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio de cumpleaños - Bienestar SENA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorio-cumpleanos',
        );
    }
}
