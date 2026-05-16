<?php

namespace App\Notifications;

use App\Models\MeetingReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingReportPublishedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly MeetingReport $report) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Compte-rendu publié — '.$this->report->meeting->circle->name)
            ->view('emails.meeting-report-published', [
                'report' => $this->report,
                'meeting' => $this->report->meeting,
                'link' => url(route('member.meeting-reports.show', $this->report)),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'meeting_title' => $this->report->meeting->title,
            'circle_name' => $this->report->meeting->circle->name,
            'published_at' => $this->report->published_at->toIso8601String(),
            'link' => route('member.meeting-reports.show', $this->report),
        ];
    }
}
