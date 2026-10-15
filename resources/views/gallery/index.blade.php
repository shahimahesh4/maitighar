<x-layouts.app title="Photo Gallery | Maitighar" meta-description="Explore authentic craftsmanship, cultural exhibitions, artisan showcases, and special moments from Maitighar.">
    <div class="kba-gallery-page">
        <section class="kba-gallery-hero">
            <div class="kba-gallery-hero-orb" aria-hidden="true"></div>
            <div class="kba-gallery-hero-inner">
                <nav class="kba-page-breadcrumbs" aria-label="Breadcrumb"><a wire:navigate href="{{ route('index') }}"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a><i class="bi bi-chevron-right"></i><span aria-current="page">Gallery</span></nav>
                <span class="kba-cms-eyebrow"><i class="bi bi-images"></i> Moments from our collection</span>
                <h1>Our Photo Gallery</h1>
                <p>Explore handcrafted artistry, artisan creativity, cultural showcases, and memorable moments from Maitighar.</p>
                <div class="kba-gallery-summary"><span><strong>{{ $albums->total() }}</strong> {{ Str::plural('album', $albums->total()) }}</span><i></i><span>Curated with care</span></div>
            </div>
        </section>

        <main class="kba-gallery-albums" id="albums">
            <header class="kba-gallery-section-head">
                <div><span>Browse collections</span><h2>Stories in every frame</h2></div>
                <p>Select an album to see every photograph in the collection.</p>
            </header>

            @forelse($albums as $album)
                @if($loop->first)<div class="kba-album-grid">@endif
                <a wire:navigate href="{{ route('gallery.show', $album) }}" class="kba-album-card">
                    <div class="kba-album-cover">
                        <img src="{{ $album->cover_url }}" alt="{{ $album->title }} album cover" width="800" height="600" loading="{{ $loop->index < 4 ? 'eager' : 'lazy' }}">
                        <span class="kba-album-count"><i class="bi bi-images"></i>{{ $album->image_count }} {{ Str::plural('photo', $album->image_count) }}</span>
                        <span class="kba-album-open" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
                    </div>
                    <div class="kba-album-copy"><h2>{{ $album->title }}</h2>@if($album->description)<p>{{ $album->description }}</p>@endif<span>View album <i class="bi bi-arrow-right"></i></span></div>
                </a>
                @if($loop->last)</div>@endif
            @empty
                <div class="kba-gallery-empty"><span><i class="bi bi-camera"></i></span><h2>Our first album is coming soon</h2><p>Beautiful academy moments are being prepared for this gallery.</p></div>
            @endforelse

            @if($albums->hasPages())<div class="kba-gallery-pagination">{{ $albums->links() }}</div>@endif
        </main>
    </div>
</x-layouts.app>
