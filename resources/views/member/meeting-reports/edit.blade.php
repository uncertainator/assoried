<x-layouts.member title="Modifier le compte-rendu">

<div class="ea-topbar">
    <div>
        <h1 class="ea-greeting">Modifier le compte-rendu</h1>
        <div class="ea-greeting-sub">{{ $report->meeting->title }} · {{ $report->meeting->circle->name }}</div>
    </div>
    <a href="{{ route('member.meeting-reports.show', $report) }}" class="fb-btn fb-btn-ghost fb-btn-sm">
        ← Voir le compte-rendu
    </a>
</div>

@if (session('success'))
    <div style="background:var(--success-bg, #f0fdf4);border:1px solid var(--success-border, #bbf7d0);border-radius:8px;padding:12px 16px;font-size:14px;color:var(--success-fg, #15803d);margin-bottom:20px;">
        {{ session('success') }}
    </div>
@endif

@php $meeting = $report->meeting; @endphp

<form
    id="update-report-form"
    action="{{ route('member.meeting-reports.update', $report) }}"
    method="POST"
    style="background:var(--surface-raised);border:1px solid var(--border-subtle);border-radius:10px;padding:24px 28px;max-width:720px;display:flex;flex-direction:column;gap:20px;"
>
    @csrf
    @method('PUT')

    @include('member.meeting-reports._form')

    <div style="display:flex;gap:12px;padding-top:4px;border-top:1px solid var(--border-subtle);">
        <button type="submit" class="fb-btn fb-btn-primary">
            Mettre à jour le brouillon
        </button>

        <button
            type="submit"
            form="publish-report-form"
            class="fb-btn fb-btn-secondary"
            onclick="return confirm('Publier ce compte-rendu ? Il ne sera plus modifiable et les membres du cercle seront notifiés.')"
        >
            Publier
        </button>

        <a href="{{ route('member.meetings.show', $meeting) }}" class="fb-btn fb-btn-ghost">
            Annuler
        </a>
    </div>
</form>

<form
    id="publish-report-form"
    action="{{ route('member.meeting-reports.publish', $report) }}"
    method="POST"
    style="display:none;"
>
    @csrf
</form>

</x-layouts.member>
