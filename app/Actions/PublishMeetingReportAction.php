<?php

namespace App\Actions;

use App\Enums\MeetingReportStatus;
use App\Enums\MembershipStatus;
use App\Models\MeetingReport;
use App\Models\User;
use App\Notifications\MeetingReportPublishedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class PublishMeetingReportAction
{
    public function execute(MeetingReport $report, User $publisher): void
    {
        $alreadyPublished = $report->meeting->reports()
            ->where('status', MeetingReportStatus::Published)
            ->where('id', '!=', $report->id)
            ->exists();

        if ($alreadyPublished) {
            throw ValidationException::withMessages([
                'status' => 'Un compte-rendu publié existe déjà pour cette réunion.',
            ]);
        }

        $report->update([
            'status' => MeetingReportStatus::Published,
            'published_at' => now(),
        ]);

        $circle = $report->meeting->circle;

        $members = $circle->users()
            ->wherePivot('status', MembershipStatus::Approved->value)
            ->get();

        try {
            Notification::send($members, new MeetingReportPublishedNotification($report));
        } catch (\Throwable $e) {
            logger()->error('MeetingReportPublishedNotification failed: '.$e->getMessage());
        }
    }
}
