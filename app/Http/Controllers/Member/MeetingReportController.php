<?php

namespace App\Http\Controllers\Member;

use App\Actions\PublishMeetingReportAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeetingReportRequest;
use App\Http\Requests\UpdateMeetingReportRequest;
use App\Models\Meeting;
use App\Models\MeetingReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MeetingReportController extends Controller
{
    public function create(Meeting $meeting): View
    {
        $this->authorize('create', [MeetingReport::class, $meeting]);

        $meeting->load(['agendaItems', 'circle']);

        return view('member.meeting-reports.create', compact('meeting'));
    }

    public function store(StoreMeetingReportRequest $request, Meeting $meeting): RedirectResponse
    {
        $report = $meeting->reports()->create([
            'created_by' => $request->user()->id,
            'participants' => $request->input('participants'),
            'agenda_notes' => $request->input('agenda_notes', []),
            'decisions' => array_values(array_filter($request->input('decisions', []))),
            'open_points' => array_values(array_filter($request->input('open_points', []))),
            'free_notes' => $request->input('free_notes'),
        ]);

        return redirect()
            ->route('member.meeting-reports.show', $report)
            ->with('success', 'Compte-rendu enregistré en brouillon.');
    }

    public function show(MeetingReport $report): View
    {
        $this->authorize('view', $report);

        $report->load(['meeting.agendaItems', 'meeting.circle', 'creator']);

        return view('member.meeting-reports.show', compact('report'));
    }

    public function edit(MeetingReport $report): View
    {
        $this->authorize('update', $report);

        $report->load(['meeting.agendaItems', 'meeting.circle']);

        return view('member.meeting-reports.edit', compact('report'));
    }

    public function update(UpdateMeetingReportRequest $request, MeetingReport $report): RedirectResponse
    {
        $report->update([
            'participants' => $request->input('participants'),
            'agenda_notes' => $request->input('agenda_notes', []),
            'decisions' => array_values(array_filter($request->input('decisions', []))),
            'open_points' => array_values(array_filter($request->input('open_points', []))),
            'free_notes' => $request->input('free_notes'),
        ]);

        return redirect()
            ->route('member.meeting-reports.show', $report)
            ->with('success', 'Brouillon mis à jour.');
    }

    public function publish(MeetingReport $report, PublishMeetingReportAction $action): RedirectResponse
    {
        $this->authorize('publish', $report);

        $action->execute($report, request()->user());

        return redirect()
            ->route('member.meeting-reports.show', $report)
            ->with('success', 'Compte-rendu publié. Les membres du cercle ont été notifiés.');
    }
}
