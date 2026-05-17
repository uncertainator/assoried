<x-layouts.app :title="$page->title . ' — La Fabrique'">

<section style="max-width:720px;margin:64px auto;padding:0 24px;">
    <h1 class="fb-h1">{{ $page->title }}</h1>

    <div class="fb-body" style="line-height:1.8;white-space:pre-line;">{{ $page->content }}</div>

    <div style="margin-top:40px;">
        <a href="{{ route('home') }}" class="fb-btn fb-btn-outline">← Retour à l'accueil</a>
    </div>
</section>

</x-layouts.app>
