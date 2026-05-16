<?php

namespace App\Actions;

use App\Enums\MembershipStatus;
use App\Models\Circle;
use App\Models\Meeting;
use App\Models\User;
use App\Notifications\MeetingCreatedNotification;
use Illuminate\Support\Facades\Notification;

class CreateMeetingAction
{
    public function execute(Circle $circle, User $creator, array $validated): Meeting
    {
        $meeting = $circle->meetings()->create([
            'created_by' => $creator->id,
            'title' => $validated['title'],
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'location' => $validated['location'] ?? null,
            'visio_url' => $validated['visio_url'] ?? null,
        ]);

        foreach ($validated['agenda_items'] as $index => $item) {
            $meeting->agendaItems()->create([
                'position' => $index + 1,
                'title' => $item['title'],
                'duration_minutes' => $item['duration_minutes'] ?? null,
            ]);
        }

        $members = $circle->users()
            ->wherePivot('status', MembershipStatus::Approved->value)
            ->get();

        try {
            Notification::send($members, new MeetingCreatedNotification($meeting));
        } catch (\Throwable $e) {
            logger()->error('MeetingCreatedNotification failed: '.$e->getMessage());
        }

        return $meeting;
    }
}
