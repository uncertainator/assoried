<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAuditLogs');

        $logs = AuditLog::with(['actor', 'targetUser'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(50);

        return view('audit-logs.index', compact('logs'));
    }
}
