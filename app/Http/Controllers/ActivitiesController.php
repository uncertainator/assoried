<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\MeetingReport;
use Illuminate\View\View;

class ActivitiesController extends Controller
{
    public function index(): View
    {
        $circles = Circle::where('is_active', true)
            ->with([
                'referent:id,name',
                'actions' => fn ($q) => $q
                    ->whereIn('status', ['todo', 'in_progress'])
                    ->orderBy('due_date'),
                'meetings' => fn ($q) => $q
                    ->where('is_public', true)
                    ->where('scheduled_at', '>=', now())
                    ->orderBy('scheduled_at')
                    ->limit(5),
            ])
            ->withCount([
                'memberships as members_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->orderBy('name')
            ->get();

        // Charger les 3 derniers CR publiés par cercle en une seule requête
        $circleIds = $circles->pluck('id');

        $reportsByCircle = MeetingReport::whereHas(
            'meeting',
            fn ($q) => $q->whereIn('circle_id', $circleIds)
        )
            ->where('status', 'published')
            ->with('meeting:id,circle_id,title,scheduled_at')
            ->orderByDesc('published_at')
            ->get()
            ->groupBy(fn ($r) => $r->meeting->circle_id)
            ->map(fn ($reports) => $reports->take(3));

        foreach ($circles as $circle) {
            $circle->setRelation('recentReports', $reportsByCircle->get($circle->id, collect()));
        }

        return view('activities', compact('circles'));
    }
}
