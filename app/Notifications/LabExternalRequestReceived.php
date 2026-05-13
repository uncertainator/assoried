<?php

namespace App\Notifications;

use App\Models\LabExternalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class LabExternalRequestReceived extends Notification
{
    use Queueable;

    public function __construct(private readonly LabExternalRequest $externalRequest) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $link = url(route('lab.external.index'));

        return (new MailMessage)
            ->subject('Nouvelle demande externe Lab — '.ucfirst($this->externalRequest->type))
            ->view('emails.lab-external-request', [
                'externalRequest' => $this->externalRequest,
                'link' => $link,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->externalRequest->type,
            'nom_contact' => $this->externalRequest->nom_contact,
            'email' => $this->externalRequest->email,
            'message' => Str::limit($this->externalRequest->message, 200),
            'link' => route('lab.external.index'),
        ];
    }
}
