<x-layouts.app :title="$album->title.' | Video Gallery'" :meta-description="$album->description ?: 'Watch videos from '.$album->title.' at Maitighar.'" :og-image="$album->cover_url">
    <div class="kba-gallery-page kba-video-gallery-page">
        <section class="kba-album-hero kba-video-album-hero">
            <div class="kba-album-hero-inner">
                <div class="kba-album-hero-copy">
                    <nav class="kba-page-breadcrumbs" aria-label="Breadcrumb"><a wire:navigate href="{{ route('index') }}"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a><i class="bi bi-chevron-right"></i><a wire:navigate href="{{ route('videos.index') }}">Videos</a><i class="bi bi-chevron-right"></i><span aria-current="page">{{ $album->title }}</span></nav>
                    <span class="kba-cms-eyebrow"><i class="bi bi-camera-video"></i> Video collection</span>
                    <h1>{{ $album->title }}</h1>
                    @if($album->description)<p>{{ $album->description }}</p>@endif
                    <div class="kba-gallery-summary"><span><strong>{{ $album->video_count }}</strong> {{ Str::plural('video', $album->video_count) }}</span><i></i><a wire:navigate href="{{ route('videos.index') }}"><i class="bi bi-arrow-left"></i> All video albums</a></div>
                </div>
            </div>
        </section>

        <main class="kba-album-gallery" data-kba-video-gallery>
            <header class="kba-gallery-section-head"><div><span>Album videos</span><h2>Watch the collection</h2></div><p>Select any video to open the popup player. Use previous and next controls to continue watching.</p></header>
            @if($videos->isNotEmpty())
                <div class="kba-video-grid">
                    @foreach($videos as $index => $video)
                        <button type="button" class="kba-video-tile" data-kba-video-open data-index="{{ $index }}" aria-label="Play {{ $video['title'] }}">
                            <span class="kba-video-thumbnail"><img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }} video thumbnail" width="720" height="405" loading="{{ $index < 6 ? 'eager' : 'lazy' }}"><i class="bi bi-play-fill"></i><b>{{ $video['type'] === 'youtube' ? 'YouTube' : 'Video' }}</b></span>
                            <span class="kba-video-title">{{ $video['title'] }}</span>
                        </button>
                    @endforeach
                </div>
                @if($videos->hasPages())<div class="kba-gallery-pagination">{{ $videos->onEachSide(1)->links() }}</div>@endif
                <div class="kba-video-lightbox" data-kba-video-lightbox aria-hidden="true" role="dialog" aria-modal="true" aria-label="Video player">
                    <div class="kba-lightbox-backdrop" data-kba-video-close></div>
                    <div class="kba-lightbox-toolbar"><span data-kba-video-counter></span><button type="button" data-kba-video-close aria-label="Close video player"><i class="bi bi-x-lg"></i></button></div>
                    <button type="button" class="kba-lightbox-nav kba-lightbox-prev" data-kba-video-prev aria-label="Previous video"><i class="bi bi-chevron-left"></i></button>
                    <figure><div class="kba-video-player" data-kba-video-player></div><figcaption data-kba-video-caption></figcaption></figure>
                    <button type="button" class="kba-lightbox-nav kba-lightbox-next" data-kba-video-next aria-label="Next video"><i class="bi bi-chevron-right"></i></button>
                </div>
                <script type="application/json" data-kba-video-items>@json($videos->items())</script>
            @else
                <div class="kba-gallery-empty"><span><i class="bi bi-camera-video"></i></span><h2>No videos in this album yet</h2><p>Our team is preparing this collection.</p><a wire:navigate href="{{ route('videos.index') }}">Browse other video albums</a></div>
            @endif
        </main>
    </div>
</x-layouts.app>
