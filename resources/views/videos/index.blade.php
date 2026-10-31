<x-layouts.app :title="$videoGalleryTitle.' | Maitighar'" :meta-description="$videoGalleryDescription">
    <div class="kba-gallery-page kba-video-gallery-page">
        <section class="kba-cms-hero kba-gallery-page-hero" aria-labelledby="video-gallery-heading" style="--kba-gallery-page-hero-image: url('{{ $videoGalleryBannerUrl }}');">
            <div class="kba-cms-hero-inner">
                <div class="kba-cms-hero-copy">
                    <nav class="kba-page-breadcrumbs" aria-label="Breadcrumb"><a wire:navigate href="{{ route('index') }}"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a><i class="bi bi-chevron-right"></i><span aria-current="page">Video Gallery</span></nav>
                    <h1 id="video-gallery-heading">{{ $videoGalleryTitle }}</h1>
                </div>
            </div>
        </section>
        <main class="kba-gallery-albums">
            <header class="kba-gallery-section-head"><div><span>Video collections</span><h2>Explore our stories in motion</h2></div><p>{{ $videoGalleryDescription }} Browse {{ $albums->total() }} {{ Str::plural('album', $albums->total()) }}.</p></header>
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
