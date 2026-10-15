<x-layouts.app title="Video Gallery | Maitighar" meta-description="Watch artisan craft making, cultural demonstrations, product showcases, and special moments from Maitighar.">
    <div class="kba-gallery-page kba-video-gallery-page">
        <section class="kba-gallery-hero kba-video-gallery-hero">
            <div class="kba-gallery-hero-orb" aria-hidden="true"></div>
            <div class="kba-gallery-hero-inner">
                <nav class="kba-page-breadcrumbs" aria-label="Breadcrumb"><a wire:navigate href="{{ route('index') }}"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a><i class="bi bi-chevron-right"></i><span aria-current="page">Video Gallery</span></nav>
                <span class="kba-cms-eyebrow"><i class="bi bi-play-circle"></i> Watch, learn and discover</span>
                <h1>Our Video Gallery</h1>
                <p>Explore artisan craft demonstrations, cultural traditions, product showcases, and inspiring moments from Maitighar.</p>
                <div class="kba-gallery-summary"><span><strong>{{ $albums->total() }}</strong> {{ Str::plural('album', $albums->total()) }}</span><i></i><span>YouTube &amp; studio videos</span></div>
            </div>
        </section>
        <main class="kba-gallery-albums">
            <header class="kba-gallery-section-head"><div><span>Video collections</span><h2>Explore our stories in motion</h2></div><p>Select an album to watch all related videos in a beautiful popup player.</p></header>
            @forelse($albums as $album)
                @if($loop->first)<div class="kba-album-grid">@endif
                <a wire:navigate href="{{ route('videos.show', $album) }}" class="kba-album-card kba-video-album-card">
                    <div class="kba-album-cover"><img src="{{ $album->cover_url }}" alt="{{ $album->title }} video album cover" width="800" height="600" loading="{{ $loop->index < 4 ? 'eager' : 'lazy' }}"><span class="kba-video-cover-play"><i class="bi bi-play-fill"></i></span><span class="kba-album-count"><i class="bi bi-camera-video"></i>{{ $album->video_count }} {{ Str::plural('video', $album->video_count) }}</span></div>
                    <div class="kba-album-copy"><h2>{{ $album->title }}</h2>@if($album->description)<p>{{ $album->description }}</p>@endif<span>View videos <i class="bi bi-arrow-right"></i></span></div>
                </a>
                @if($loop->last)</div>@endif
            @empty
                <div class="kba-gallery-empty"><span><i class="bi bi-camera-video"></i></span><h2>Our first video album is coming soon</h2><p>Videos are being prepared for this gallery.</p></div>
            @endforelse
            @if($albums->hasPages())<div class="kba-gallery-pagination">{{ $albums->links() }}</div>@endif
        </main>
    </div>
</x-layouts.app>
