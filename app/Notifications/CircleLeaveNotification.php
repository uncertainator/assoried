<?php

namespace App\Notifications;

use App\Models\Circle;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CircleLeaveNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $member,
        private readonly Circle $circle,
    ) {}

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
            ->subject($this->member->name.' a quitté le cercle « '.$this->circle->name.' »')
            ->line($this->member->name.' ('.$this->member->email.') a quitté le cercle « '.$this->circle->name.' ».')
            ->action('Gérer les demandes', $link);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'member_name' => $this->member->name,
            'member_email' => $this->member->email,
            'circle_name' => $this->circle->name,
            'link' => route('referent.requests.index'),
        ];
    }
}
