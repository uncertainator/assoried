@php $indent = $depth * 24; @endphp

<div style="margin-left:{{ $indent }}px;margin-top:{{ $depth > 0 ? 12 : 0 }}px;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
        <span style="font-weight:600;color:var(--fg-primary);font-size:14px;">
            @if ($depth === 0)<span class="fb-badge fb-badge-mousse" style="margin-right:6px;">Départ</span>@endif
            {{ $question->label }}
        </span>
        <a href="{{ route('admin.parcours.questions.edit', $question) }}"
           style="font-size:11px;color:var(--fg-tertiary);text-decoration:underline;">modifier</a>
    </div>

    @foreach ($question->options as $option)
        <div style="margin-left:20px;margin-bottom:6px;display:flex;align-items:flex-start;gap:8px;">
            <span style="color:var(--fg-tertiary);font-size:13px;padding-top:1px;">→</span>
            <div>
                <span style="font-size:13px;color:var(--fg-secondary);">{{ $option->label }}</span>
                @if ($option->service_id && $option->service)
                    <span class="fb-badge fb-badge-mousse" style="margin-left:6px;font-size:11px;">
                        {{ $option->service->name }}
                    </span>
                @elseif ($option->next_question_id && $option->nextQuestion)
                    @include('admin.parcours._tree-node', [
                        'question' => $option->nextQuestion,
                        'depth' => $depth + 1,
                    ])
                @else
                    <span class="fb-badge fb-badge-brique" style="margin-left:6px;font-size:11px;">
                        Non configurée
                    </span>
                @endif
            </div>
        </div>
    @endforeach

    @if ($question->options->isEmpty())
        <div style="margin-left:20px;font-size:12px;color:var(--fg-tertiary);font-style:italic;">
            Aucune option définie
        </div>
    @endif
</div>
