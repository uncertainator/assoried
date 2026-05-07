<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markRead(DatabaseNotification $notification): RedirectResponse
    {
        abort_if($notification->notifiable_id !== Auth::id(), 403);

        $notification->markAsRead();

        return back();
    }
}
