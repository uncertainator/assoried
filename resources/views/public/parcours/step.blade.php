<x-layouts.app title="Trouver mon service — La Fabrique">

<section class="fb-section" style="padding-top:64px;padding-bottom:80px;">
    <div style="max-width:640px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:40px;">
            <div class="fb-eyebrow">Orientation</div>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:8px 0 0;letter-spacing:-.02em;">
                Trouvez votre service
            </h1>
        </div>

        <div style="background:var(--bg-surface-2);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow-sm);">
            <p style="font-size:1.125rem;font-weight:600;color:var(--fg-primary);margin:0 0 24px;">
                {{ $question->label }}
            </p>

            <form method="POST" action="{{ route('parcours.choose', $question) }}">
                @csrf

                @error('option_id')
                    <div style="background:var(--error-bg,#fef2f2);border:1px solid var(--error-border,#fca5a5);border-radius:var(--radius-md);padding:12px 16px;margin-bottom:16px;color:var(--error-fg,#dc2626);font-size:14px;">
                        {{ $message }}
                    </div>
                @enderror

                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach ($question->options as $option)
                        <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:1px solid var(--border-subtle);border-radius:var(--radius-md);cursor:pointer;transition:border-color .15s;"
                               x-data
                               :style="$el.querySelector('input').checked ? 'border-color:var(--brique-400);background:var(--brique-50,#fdf4f0);' : ''"
                               @click="$el.querySelector('input').checked = true; $el.dispatchEvent(new Event('change'))">
                            <input type="radio" name="option_id" value="{{ $option->id }}"
                                   {{ (int) $preselectedOptionId === $option->id ? 'checked' : '' }}
                                   style="accent-color:var(--brique-500);">
                            <span style="font-size:15px;color:var(--fg-primary);">{{ $option->label }}</span>
                        </label>
                    @endforeach
                </div>

                <div style="display:flex;gap:10px;margin-top:28px;align-items:center;">
                    <button type="submit" class="fb-btn fb-btn-primary">Continuer →</button>
                    @if ($hasHistory)
                        <a href="{{ route('parcours.back') }}" class="fb-btn fb-btn-ghost" style="font-size:14px;">← Retour</a>
                    @endif
                </div>
            </form>
        </div>

    </div>
</section>

</x-layouts.app>
