<x-layouts.app :title="$page->title . ' — La Fabrique'">

<style>
    .fb-prose { line-height:1.8; }
    .fb-prose h2 { font-family:var(--font-display); font-size:1.5rem; font-weight:600; margin:32px 0 12px; letter-spacing:-.01em; }
    .fb-prose h3 { font-family:var(--font-display); font-size:1.2rem; font-weight:600; margin:24px 0 10px; }
    .fb-prose p { margin:0 0 16px; }
    .fb-prose ul, .fb-prose ol { margin:0 0 16px; padding-left:24px; }
    .fb-prose li { margin:4px 0; }
    .fb-prose a { color:var(--brique-600); text-decoration:underline; }
</style>

<section style="max-width:720px;margin:64px auto;padding:0 24px;">
    <h1 class="fb-h1">{{ $page->title }}</h1>

    <div class="fb-body fb-prose">{!! $page->content !!}</div>

    <div style="margin-top:40px;">
        <a href="{{ route('home') }}" class="fb-btn fb-btn-outline">← Retour à l'accueil</a>
    </div>
</section>

</x-layouts.app>
