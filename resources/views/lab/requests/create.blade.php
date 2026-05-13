<x-layouts.member title="Demande de soutien Lab — La Fabrique">

<div style="max-width:680px;">
    <div style="margin-bottom:28px;">
        <div style="font-size:var(--text-xs);font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--fg-tertiary);margin-bottom:4px;">Lab</div>
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:600;color:var(--fg-primary);margin:0;letter-spacing:-.02em;">Faire une demande de soutien</h1>
        <p style="margin:8px 0 0;color:var(--fg-secondary);font-size:var(--text-sm);line-height:1.6;">Décrivez votre besoin et les référents du Lab reviendront vers vous.</p>
    </div>

    <form method="POST" action="{{ route('lab.requests.store') }}"
          style="background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:32px;">
        @csrf

        <div style="margin-bottom:20px;">
            <label for="circle_id" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Cercle demandeur <span style="color:var(--color-brique-600);">*</span>
            </label>
            <select id="circle_id" name="circle_id"
                    style="width:100%;padding:10px 12px;border:1px solid var(--border-default);border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);"
                    class="{{ $errors->has('circle_id') ? 'is-invalid' : '' }}">
                <option value="">— Sélectionner un cercle —</option>
                @foreach($circles as $circle)
                    <option value="{{ $circle->id }}"
                        {{ (old('circle_id', $preselectedCircleId) == $circle->id) ? 'selected' : '' }}>
                        {{ $circle->name }}
                    </option>
                @endforeach
            </select>
            @error('circle_id')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:20px;">
            <label for="lab_service_id" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Service Lab visé <span style="font-weight:400;color:var(--fg-tertiary);">(optionnel)</span>
            </label>
            <select id="lab_service_id" name="lab_service_id"
                    style="width:100%;padding:10px 12px;border:1px solid var(--border-default);border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);"
                    class="{{ $errors->has('lab_service_id') ? 'is-invalid' : '' }}">
                <option value="">— Aucun service en particulier —</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}"
                        {{ (old('lab_service_id', $preselectedServiceId) == $service->id) ? 'selected' : '' }}>
                        {{ $service->title }}
                    </option>
                @endforeach
            </select>
            @error('lab_service_id')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:20px;">
            <label for="message" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Message <span style="color:var(--color-brique-600);">*</span>
            </label>
            <textarea id="message" name="message" rows="5"
                      placeholder="Décrivez votre besoin, le contexte, ce que vous attendez du Lab…"
                      style="width:100%;padding:10px 12px;border:1px solid var(--border-default);border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);resize:vertical;box-sizing:border-box;"
                      class="{{ $errors->has('message') ? 'is-invalid' : '' }}">{{ old('message') }}</textarea>
            @error('message')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:28px;">
            <label for="desired_date" style="display:block;font-size:var(--text-sm);font-weight:500;color:var(--fg-primary);margin-bottom:6px;">
                Date souhaitée <span style="font-weight:400;color:var(--fg-tertiary);">(optionnelle)</span>
            </label>
            <input type="date" id="desired_date" name="desired_date"
                   value="{{ old('desired_date') }}"
                   min="{{ date('Y-m-d') }}"
                   style="padding:10px 12px;border:1px solid var(--border-default);border-radius:var(--radius-md);font-size:var(--text-sm);background:var(--bg-base);color:var(--fg-primary);"
                   class="{{ $errors->has('desired_date') ? 'is-invalid' : '' }}">
            @error('desired_date')
                <span style="display:block;margin-top:4px;font-size:var(--text-xs);color:var(--color-brique-600);">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:flex;gap:12px;align-items:center;">
            <button type="submit" class="fb-btn fb-btn-primary">Envoyer la demande</button>
            <a href="{{ route('lab.services.index') }}" class="fb-btn fb-btn-ghost">Annuler</a>
        </div>
    </form>
</div>

</x-layouts.member>
