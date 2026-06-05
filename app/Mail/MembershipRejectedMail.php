<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembershipRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ?string $reason = null) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre demande d\'adhésion',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.membership-rejected',
        );
    }
}
