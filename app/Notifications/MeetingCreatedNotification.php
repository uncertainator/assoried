<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Meeting $meeting) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle réunion — '.$this->meeting->circle->name)
            ->view('emails.meeting-created', [
                'meeting' => $this->meeting,
                'link' => url(route('member.meetings.show', $this->meeting)),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'meeting_title' => $this->meeting->title,
            'circle_name' => $this->meeting->circle->name,
            'scheduled_at' => $this->meeting->scheduled_at->toIso8601String(),
            'link' => route('member.meetings.show', $this->meeting),
        ];
    }
}
