<?php

namespace App\Notifications;

use App\Models\LabInternalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class LabInternalRequestReceived extends Notification
{
    use Queueable;

    public function __construct(private readonly LabInternalRequest $labRequest) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $link = url(route('lab.requests.index'));

        return (new MailMessage)
            ->subject('Nouvelle demande de soutien — '.$this->labRequest->circle->name)
            ->view('emails.lab-internal-request', [
                'labRequest' => $this->labRequest,
                'link' => $link,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'requester_name' => $this->labRequest->user->name ?: $this->labRequest->user->email,
            'circle_name' => $this->labRequest->circle->name,
            'service_title' => $this->labRequest->labService?->title,
            'message' => Str::limit($this->labRequest->message, 200),
            'link' => route('lab.requests.index'),
        ];
    }
}
