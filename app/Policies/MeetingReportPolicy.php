<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\MeetingReport;
use App\Models\User;

class MeetingReportPolicy
{
    public function create(User $user, Meeting $meeting): bool
    {
        return $meeting->circle->isManagedBy($user);
    }

    public function update(User $user, MeetingReport $report): bool
    {
        return $report->isDraft() && $report->meeting->circle->isManagedBy($user);
    }

    public function publish(User $user, MeetingReport $report): bool
    {
        return $report->isDraft() && $report->meeting->circle->isManagedBy($user);
    }

    public function view(User $user, MeetingReport $report): bool
    {
        return $report->isPublished() || $report->meeting->circle->isManagedBy($user);
    }
}
