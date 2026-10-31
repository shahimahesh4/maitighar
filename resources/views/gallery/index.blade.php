<x-layouts.app :title="$photoGalleryTitle.' | Maitighar'" :meta-description="$photoGalleryDescription">
    @php($albumPreviews = $albums->getCollection()->values()->map(fn ($album) => ['url' => $album->cover_url, 'alt' => $album->title.' album cover'])->all())
    <div class="kba-gallery-page">
        <section class="kba-cms-hero kba-gallery-page-hero" aria-labelledby="photo-gallery-heading" style="--kba-gallery-page-hero-image: url('{{ $photoGalleryBannerUrl }}');">
            <div class="kba-cms-hero-inner">
                <div class="kba-cms-hero-copy">
                    <nav class="kba-page-breadcrumbs" aria-label="Breadcrumb"><a wire:navigate href="{{ route('index') }}"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a><i class="bi bi-chevron-right"></i><span aria-current="page">Gallery</span></nav>
                    <h1 id="photo-gallery-heading">{{ $photoGalleryTitle }}</h1>
                </div>
            </div>
        </section>

        <main class="kba-gallery-albums" id="albums" data-kba-gallery>
            <header class="kba-gallery-section-head">
                <div><span>Browse collections</span><h2>Stories in every frame</h2></div>
                <p>{{ $photoGalleryDescription }} Browse {{ $albums->total() }} {{ Str::plural('album', $albums->total()) }}.</p>
            </header>

            @forelse($albums as $album)
                @if($loop->first)<div class="kba-album-grid">@endif
                <article class="kba-album-card">
                    <div class="kba-album-cover">
                        <img src="{{ $album->cover_url }}" alt="{{ $album->title }} album cover" width="800" height="600" loading="{{ $loop->index < 4 ? 'eager' : 'lazy' }}">
                        <span class="kba-album-count"><i class="bi bi-images"></i>{{ $album->image_count }} {{ Str::plural('photo', $album->image_count) }}</span>
                        <button type="button" class="kba-album-preview" data-kba-lightbox-open data-index="{{ $loop->index }}" aria-label="Preview {{ $album->title }} album cover"><i class="bi bi-arrows-fullscreen" aria-hidden="true"></i><span>Preview</span></button>
                    </div>
                    <div class="kba-album-copy"><h2>{{ $album->title }}</h2>@if($album->description)<p>{{ $album->description }}</p>@endif<a wire:navigate href="{{ route('gallery.show', $album) }}">View album <i class="bi bi-arrow-right"></i></a></div>
                </article>
                @if($loop->last)</div>@endif
            @empty
                <div class="kba-gallery-empty"><span><i class="bi bi-camera"></i></span><h2>Our first album is coming soon</h2><p>Beautiful academy moments are being prepared for this gallery.</p></div>
            @endforelse

            @if($albums->isNotEmpty())
                <div class="kba-lightbox" data-kba-lightbox aria-hidden="true" role="dialog" aria-modal="true" aria-label="Album cover preview">
                    <div class="kba-lightbox-backdrop" data-kba-lightbox-close></div>
                    <div class="kba-lightbox-toolbar"><span data-kba-lightbox-counter></span><button type="button" data-kba-lightbox-close aria-label="Close album preview"><i class="bi bi-x-lg"></i></button></div>
                    <button type="button" class="kba-lightbox-nav kba-lightbox-prev" data-kba-lightbox-prev aria-label="Previous album"><i class="bi bi-chevron-left"></i></button>
                    <figure><img alt="Selected album cover" data-kba-lightbox-image><figcaption data-kba-lightbox-caption></figcaption></figure>
                    <button type="button" class="kba-lightbox-nav kba-lightbox-next" data-kba-lightbox-next aria-label="Next album"><i class="bi bi-chevron-right"></i></button>
                </div>
                <script type="application/json" data-kba-gallery-images>@json($albumPreviews)</script>
            @endif

            @if($albums->hasPages())<div class="kba-gallery-pagination">{{ $albums->links() }}</div>@endif
        </main>
    </div>
</x-layouts.app>
