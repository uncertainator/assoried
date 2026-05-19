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
                    @php
                        $color = match($event->tag) {
                            'Atelier'     => ['border' => 'var(--brique-500)', 'tag' => 'var(--brique-600)'],
                            'Information' => ['border' => 'var(--ocre-400)',   'tag' => 'var(--ocre-600)'],
                            default       => ['border' => 'var(--mousse-500)', 'tag' => 'var(--mousse-500)'],
                        };
                    @endphp
                    <article class="fb-event-card" style="border-top-color:{{ $color['border'] }};">
                        <div class="fb-event-tag" style="color:{{ $color['tag'] }};text-transform:uppercase;">
                            {{ $event->tag ?? $event->circle->name }}
                        </div>
                        <h2 class="fb-event-title">{{ $event->title }}</h2>
                        @if ($event->description)
                            <p class="fb-event-body">{{ $event->description }}</p>
                        @endif
                        <div class="fb-event-meta">
                            <span class="fb-mono">{{ $event->starts_at->isoFormat('D MMMM YYYY · HH:mm') }}</span>
                            @if ($event->location)
                                <span class="fb-event-place">{{ $event->location }}</span>
                            @endif
                        </div>
                        @if ($event->foot_type)
                            <div class="fb-event-foot">
                                @if ($event->foot_type === 'places_limitees')
                                    <span class="fb-badge fb-badge-mousse">Places limitées</span>
                                @else
                                    <span class="fb-badge fb-badge-ocre">Entrée libre</span>
                                @endif
                                <a href="{{ route('evenements.show', $event) }}" class="fb-btn fb-btn-ghost fb-btn-sm">Je m'inscris →</a>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

</x-layouts.app>
