@auth
@php
    $pendingCount = 0;
    $user = Auth::user();
    if ($user->isReferent() && ($assignedCircle = $user->assignedCircle()->first())) {
        $pendingCount = \App\Models\CircleMembership::where('circle_id', $assignedCircle->id)
            ->where('status', 'pending')
            ->count();
    } elseif ($user->isAdmin()) {
        $pendingCount = \App\Models\CircleMembership::where('status', 'pending')->count();
    }
@endphp
@if ($pendingCount > 0)
    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 5px;background:#c85226;color:#fff;font-size:11px;font-weight:600;border-radius:9px;margin-left:6px;line-height:1;">{{ $pendingCount }}</span>
@endif
@endauth
