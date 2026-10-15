<x-layouts.app :title="$album->title.' | Photo Gallery'" :meta-description="$album->description ?: 'View the '.$album->title.' photo album from Maitighar.'" :og-image="$album->cover_url">
    <div class="kba-gallery-page">
        <section class="kba-album-hero">
            <div class="kba-album-hero-inner">
                <div class="kba-album-hero-copy">
                    <nav class="kba-page-breadcrumbs" aria-label="Breadcrumb"><a wire:navigate href="{{ route('index') }}"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a><i class="bi bi-chevron-right"></i><a wire:navigate href="{{ route('gallery.index') }}">Gallery</a><i class="bi bi-chevron-right"></i><span aria-current="page">{{ $album->title }}</span></nav>
                    <span class="kba-cms-eyebrow"><i class="bi bi-camera"></i> Photo collection</span>
                    <h1>{{ $album->title }}</h1>
                    @if($album->description)<p>{{ $album->description }}</p>@endif
                    <div class="kba-gallery-summary"><span><strong>{{ $album->image_count }}</strong> {{ Str::plural('photo', $album->image_count) }}</span><i></i><a wire:navigate href="{{ route('gallery.index') }}"><i class="bi bi-arrow-left"></i> All albums</a></div>
                </div>
                <img src="{{ $album->cover_url }}" alt="{{ $album->title }} album cover" width="720" height="520">
            </div>
        </section>

        <main class="kba-album-gallery" data-kba-gallery>
            <header class="kba-gallery-section-head"><div><span>Album photographs</span><h2>Explore the collection</h2></div><p>Click any photo to view it full screen. Use arrow keys to move between images.</p></header>

            @if($images->isNotEmpty())
                <div class="kba-photo-grid">
                    @foreach($images as $index => $image)
                        <button type="button" class="kba-photo-tile" data-kba-lightbox-open data-index="{{ $index }}" aria-label="Open {{ $image['alt'] }}">
                            <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" width="700" height="700" loading="{{ $index < 8 ? 'eager' : 'lazy' }}">
                            <span aria-hidden="true"><i class="bi bi-arrows-fullscreen"></i></span>
                        </button>
                    @endforeach
                </div>

                @if($images->hasPages())<div class="kba-gallery-pagination">{{ $images->onEachSide(1)->links() }}</div>@endif

                <div class="kba-lightbox" data-kba-lightbox aria-hidden="true" role="dialog" aria-modal="true" aria-label="Album photo viewer">
                    <div class="kba-lightbox-backdrop" data-kba-lightbox-close></div>
                    <div class="kba-lightbox-toolbar"><span data-kba-lightbox-counter></span><button type="button" data-kba-lightbox-close aria-label="Close photo viewer"><i class="bi bi-x-lg"></i></button></div>
                    <button type="button" class="kba-lightbox-nav kba-lightbox-prev" data-kba-lightbox-prev aria-label="Previous photo"><i class="bi bi-chevron-left"></i></button>
                    <figure><img alt="Selected album photo" data-kba-lightbox-image><figcaption data-kba-lightbox-caption></figcaption></figure>
                    <button type="button" class="kba-lightbox-nav kba-lightbox-next" data-kba-lightbox-next aria-label="Next photo"><i class="bi bi-chevron-right"></i></button>
                </div>
                <script type="application/json" data-kba-gallery-images>@json($images->items())</script>
            @else
                <div class="kba-gallery-empty"><span><i class="bi bi-image"></i></span><h2>No photos in this album yet</h2><p>Our team is preparing this collection.</p><a wire:navigate href="{{ route('gallery.index') }}">Browse other albums</a></div>
            @endif
        </main>
    </div>
</x-layouts.app>
