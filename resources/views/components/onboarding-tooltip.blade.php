@props(['onboardingCompleted' => false])

<div
    x-data="onboardingFlow({{ $onboardingCompleted ? 'true' : 'false' }})"
    @start-onboarding.window="active = true; step = 0; positionTooltip()"
    x-show="active"
    x-cloak
>
    {{-- Overlay --}}
    <div
        style="position:fixed;inset:0;background:rgba(0,0,0,0.25);z-index:1000;"
        @click.stop
    ></div>

    {{-- Tooltip --}}
    <div
        x-ref="tooltip"
        style="position:fixed;z-index:1001;width:280px;background:#fff;border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,0.18);padding:20px 20px 16px;"
        :style="tooltipStyle"
    >
        {{-- Arrow --}}
        <div style="position:absolute;left:-8px;top:20px;width:0;height:0;border-top:8px solid transparent;border-bottom:8px solid transparent;border-right:8px solid #fff;"></div>

        {{-- Step indicator --}}
        <div style="display:flex;gap:6px;margin-bottom:14px;">
            <template x-for="i in 3" :key="i">
                <div :style="(i - 1) === step ? 'width:20px;height:6px;border-radius:3px;background:var(--brique-600,#b84a3b);' : 'width:8px;height:6px;border-radius:3px;background:var(--border-subtle,#e5e7eb);'"></div>
            </template>
        </div>

        {{-- Content --}}
        <div style="font-size:15px;font-weight:700;color:var(--fg-primary,#111);margin-bottom:6px;" x-text="steps[step].title"></div>
        <div style="font-size:14px;color:var(--fg-secondary,#555);line-height:1.5;margin-bottom:18px;" x-text="steps[step].body"></div>

        {{-- Actions --}}
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
            <button
                @click="finish()"
                style="font-size:13px;color:var(--fg-tertiary,#888);background:none;border:none;cursor:pointer;padding:0;"
            >Passer</button>
            <div style="display:flex;gap:8px;">
                <button
                    x-show="step > 0"
                    @click="step--; positionTooltip()"
                    style="font-size:13px;padding:6px 14px;border:1px solid var(--border-subtle,#e5e7eb);border-radius:6px;background:#fff;cursor:pointer;"
                >← Précédent</button>
                <button
                    @click="step < 2 ? (step++, positionTooltip()) : finish()"
                    style="font-size:13px;padding:6px 14px;border-radius:6px;background:var(--brique-600,#b84a3b);color:#fff;border:none;cursor:pointer;"
                    x-text="step < 2 ? 'Suivant →' : 'Terminer'"
                ></button>
            </div>
        </div>
    </div>
</div>

<script>
function onboardingFlow(alreadyCompleted) {
    return {
        active: false,
        step: 0,
        completed: alreadyCompleted,
        tooltipStyle: '',
        steps: [
            { target: 'nav-cercles',  title: 'Vos cercles',  body: 'Rejoignez et gérez vos cercles thématiques depuis ce menu.' },
            { target: 'nav-sondages', title: 'Sondages',      body: 'Consultez et répondez aux sondages de vos cercles.' },
            { target: 'nav-scrutins', title: 'Scrutins',      body: 'Participez aux votes officiels de l\'association.' },
        ],
        init() {
            if (!alreadyCompleted) {
                this.$nextTick(() => {
                    this.active = true;
                    this.positionTooltip();
                });
            }
            window.addEventListener('resize', () => {
                if (this.active) this.positionTooltip();
            });
        },
        positionTooltip() {
            this.$nextTick(() => {
                const target = document.getElementById(this.steps[this.step].target);
                if (!target) return;
                const rect = target.getBoundingClientRect();
                this.tooltipStyle = `top:${Math.max(8, rect.top - 4)}px;left:${rect.right + 16}px;`;
            });
        },
        async finish() {
            this.active = false;
            if (this.completed) return;
            this.completed = true;
            try {
                await fetch('{{ route("member.onboarding.complete") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
            } catch (_) {}
        },
    };
}
</script>
