<div x-data="{ search: '' }" class="kba-category-page">
    <section class="kba-category-hero" aria-labelledby="categories-title">
        <div class="kba-category-hero__orb kba-category-hero__orb--one" aria-hidden="true"></div>
        <div class="kba-category-hero__orb kba-category-hero__orb--two" aria-hidden="true"></div>
        <div class="kba-category-hero__inner">
            <nav class="kba-category-breadcrumb" aria-label="Breadcrumb">
                <a wire:navigate href="{{ route('index') }}"><i class="bi bi-house-door" aria-hidden="true"></i> Home</a>
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                <span aria-current="page">Categories</span>
            </nav>

            <div class="kba-category-hero__content">
                <h1 id="categories-title">Discover Nepalese heritage</h1>
            </div>
        </div>
    </section>

    <main class="kba-category-content">
        <div class="kba-category-toolbar">
            <div>
                <p class="kba-category-kicker">Browse the collection</p>
                <h2>Find your next treasure</h2>
            </div>
            <label class="kba-category-search">
                <span class="sr-only">Search categories</span>
                <i class="bi bi-search" aria-hidden="true"></i>
                <input x-model="search" type="search" placeholder="Search categories" autocomplete="off">
            </label>
        </div>

        <p class="kba-category-total"><span>{{ $categories->count() }}</span> curated {{ Str::plural('collection', $categories->count()) }}</p>

        <div x-ref="categoryGrid" class="kba-category-grid" aria-label="Product categories">
            @foreach ($categories as $category)
                <a
                    wire:navigate
                    x-show="!search || @js(strtolower($category->name)).includes(search.toLowerCase())"
                    x-transition.opacity.duration.150ms
                    class="kba-category-card group"
                    href="{{ route('all-products', ['selected_categories' => [$category->id]]) }}"
                    wire:key="cat-card-{{ $category->id }}"
                >
                    <div class="kba-category-card__media">
                        @if($category->image)
                            <img src="{{ $category->getImageUrl() }}" alt="" width="640" height="460" @if($loop->iteration > 4) loading="lazy" @endif decoding="async">
                        @else
                            <div class="kba-category-card__fallback" aria-hidden="true"><i class="bi bi-gem"></i></div>
                        @endif
                        <span class="kba-category-card__wash" aria-hidden="true"></span>
                    </div>
                    <div class="kba-category-card__body">
                        <p>{{ $category->products_count }} {{ Str::plural('item', $category->products_count) }}</p>
                        <h3>{{ $category->name }}</h3>
                        <span>Explore collection <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </a>
            @endforeach
        </div>

        <div x-cloak x-show="search && ![...$refs.categoryGrid.querySelectorAll('.kba-category-card')].some(card => getComputedStyle(card).display !== 'none')" class="kba-category-empty" role="status">
            <i class="bi bi-search" aria-hidden="true"></i>
            <h2>No matching collections</h2>
            <p>Try a different category name, or browse all of our handcrafted collections.</p>
            <button type="button" x-on:click="search = ''">Clear search</button>
        </div>
    </main>
</div>
