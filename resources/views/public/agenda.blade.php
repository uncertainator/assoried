<x-layouts.app title="Agenda public — La Fabrique">

<section class="fb-section" style="padding-top:64px;">
    <div style="max-width:1280px;margin:0 auto;padding:0 48px;">
        <div class="fb-section-head">
            <div class="fb-eyebrow">Agenda</div>
            <h1 class="fb-h2">Événements à venir</h1>
        </div>

        @if ($events->isEmpty())
            <p style="color:var(--fg-tertiary);font-size:15px;margin-top:32px;">Aucun événement public à venir pour le moment.</p>
        @else
            <div class="fb-events-grid">
                @foreach ($events as $event)
                    <article class="fb-event-card" style="border-top-color:var(--brique-500);">
                        <div class="fb-event-tag" style="color:var(--brique-600);text-transform:uppercase;">
                            {{ $event->circle->name }}
                        </div>
                        <h2 class="fb-event-title">{{ $event->title }}</h2>
                        <div class="fb-event-meta">
                            <span class="fb-mono">{{ $event->starts_at->isoFormat('D MMMM YYYY · HH:mm') }}</span>
                            @if ($event->location)
                                <span class="fb-event-place">{{ $event->location }}</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

</x-layouts.app>
