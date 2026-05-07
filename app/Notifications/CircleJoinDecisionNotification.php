<?php

namespace App\Notifications;

use App\Enums\MembershipStatus;
use App\Models\CircleMembership;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CircleJoinDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly CircleMembership $membership) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->emailSubject())
            ->view('emails.circle-join-decision', [
                'membership' => $this->membership,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'decision'         => $this->membership->status->value,
            'circle_name'      => $this->membership->circle->name,
            'rejection_reason' => $this->membership->rejection_reason,
        ];
    }

    private function emailSubject(): string
    {
        return $this->membership->status === MembershipStatus::Approved
            ? 'Votre demande d\'inscription a été acceptée — '.$this->membership->circle->name
            : 'Votre demande d\'inscription a été refusée — '.$this->membership->circle->name;
    }
}
