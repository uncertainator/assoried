<?php

namespace App\Notifications;

use App\Models\CircleMembership;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CircleJoinRequestNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly CircleMembership $membership) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $link = $notifiable->isReferent()
            ? url(route('referent.requests.index'))
            : url(route('admin.requests.index'));

        return (new MailMessage)
            ->subject('Nouvelle demande d\'inscription — '.$this->membership->circle->name)
            ->view('emails.circle-join-request', [
                'membership' => $this->membership,
                'link'       => $link,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $link = $notifiable->isReferent()
            ? route('referent.requests.index')
            : route('admin.requests.index');

        return [
            'applicant_name'  => $this->membership->user->name,
            'applicant_email' => $this->membership->user->email,
            'circle_name'     => $this->membership->circle->name,
            'link'            => $link,
        ];
    }
}
