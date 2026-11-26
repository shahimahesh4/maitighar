@php($commerceEnabled = \App\Support\Storefront::commerceEnabled())
@php($quoteLabel = \App\Support\Storefront::quoteLabel())
@php($showPrices = \App\Support\Storefront::pricesVisible())
@php($reviewsEnabled = \App\Support\Storefront::reviewsEnabled())
@php($wishlistEnabled = \App\Support\Storefront::wishlistEnabled())
@php($quickViewEnabled = \App\Support\Storefront::quickViewEnabled())
<div
    x-data="{ filtersOpen: false }"
    x-on:keydown.escape.window="filtersOpen = false"
>
    <!-- Full-Width Catalog Hero Header -->
    <section class="kba-catalog-hero-full" aria-labelledby="catalog-title">
        <div class="kba-catalog-hero-inner">
            <nav class="kba-catalog-breadcrumb" aria-label="Breadcrumb">
                <a wire:navigate href="/"><i class="bi bi-house-door-fill mr-1.5"></i>Home</a>
                <i class="bi bi-chevron-right text-[10px] opacity-60"></i>
                <span class="font-medium text-slate-700">Products</span>
            </nav>
            <h1 id="catalog-title">All Products</h1>
            <p>Explore authentic Nepali handicrafts, cultural treasures, and heritage creations handmade with love.</p>
        </div>
    </section>

    <div class="kba-catalog-page mx-auto w-[calc(100%-32px)] max-w-[1320px] py-6 sm:py-8">
        <div class="mb-5 flex items-center justify-between gap-4 lg:hidden">
        <button type="button" x-on:click="filtersOpen = true" class="inline-flex items-center gap-2 rounded-xl border border-[#0C1E8C] bg-blue-50 px-4 py-2.5 text-sm font-bold text-[#0C1E8C] shadow-sm" aria-controls="product-filter-drawer" x-bind:aria-expanded="filtersOpen">
            <i class="bi bi-funnel-fill"></i>
            Filter
        </button>
        <span class="text-xs font-semibold text-stone-500">{{ $products->total() }} products</span>
    </div>

    <div x-cloak x-show="filtersOpen" x-transition.opacity x-on:click="filtersOpen = false" class="fixed inset-0 z-[120] bg-stone-950/55 backdrop-blur-[2px] lg:hidden" aria-hidden="true"></div>

    <div class="kba-catalog-layout flex flex-col lg:flex-row gap-8">
        <!-- Filter Sidebar -->
        <aside id="product-filter-drawer" x-bind:class="filtersOpen ? 'translate-x-0' : 'translate-x-full'" class="kba-catalog-filters fixed inset-y-0 right-0 z-[130] w-[min(88vw,390px)] space-y-5 overflow-y-auto bg-white p-4 shadow-2xl transition-transform duration-300 ease-out lg:static lg:z-auto lg:w-1/4 lg:translate-x-0 lg:space-y-6 lg:overflow-visible lg:bg-transparent lg:p-0 lg:shadow-none" aria-label="Product filters">
            <div class="sticky top-0 z-10 -mx-4 -mt-4 flex items-center justify-between border-b border-stone-200 bg-white px-5 py-4 lg:hidden">
                <h2 class="text-xl font-extrabold text-stone-900">Filters</h2>
                <button type="button" x-on:click="filtersOpen = false" class="grid h-11 w-11 place-items-center rounded-full bg-[#0C1E8C] text-xl text-white shadow-sm" aria-label="Close filters">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <!-- Categories Filter -->
            <div class="kba-filter-card bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 dark:text-gray-200 text-sm uppercase tracking-wider mb-4 border-b border-gray-100 dark:border-slate-800/80 pb-2">Categories</h3>
                <div class="max-h-72 space-y-1 overflow-y-auto scrollbar-none pr-2">
                    @foreach ($categories as $category)
                        <div
                            x-data="{ open: @js(collect($selected_categories)->intersect($category->children->pluck('slug')->push($category->slug))->isNotEmpty()) }"
                            class="rounded-lg"
                        >
                            <div class="flex items-center gap-1">
                                <label for="cat-{{ $category->slug }}" class="flex min-w-0 flex-1 items-center py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 cursor-pointer select-none">
                                    <input type="checkbox" wire:model.live="selected_categories" id="cat-{{ $category->slug }}" value="{{ $category->slug }}" class="mr-3 h-4 w-4 rounded border-gray-300 text-[#0C1E8C] focus:ring-[#0C1E8C]">
                                    <span class="truncate">{{ $category->name }}</span>
                                </label>

                                @if ($category->children->isNotEmpty())
                                    <button
                                        type="button"
                                        x-on:click="open = ! open"
                                        x-bind:aria-expanded="open"
                                        aria-label="Toggle {{ $category->name }} subcategories"
                                        class="grid h-8 w-8 shrink-0 place-items-center rounded-md text-[#0C1E8C] transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-[#0C1E8C]/30"
                                    >
                                        <i class="bi bi-chevron-down text-xs transition-transform duration-200" x-bind:class="open && 'rotate-180'" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </div>

                            @if ($category->children->isNotEmpty())
                                <div x-cloak x-show="open" x-transition.opacity.duration.150ms class="ml-4 border-l border-blue-100 pl-3">
                                    @foreach ($category->children as $child)
                                        <label for="cat-{{ $child->slug }}" class="flex items-center py-2 text-xs text-gray-600 dark:text-slate-300 cursor-pointer select-none">
                                            <input type="checkbox" wire:model.live="selected_categories" id="cat-{{ $child->slug }}" value="{{ $child->slug }}" class="mr-3 h-4 w-4 rounded border-gray-300 text-[#0C1E8C] focus:ring-[#0C1E8C]">
                                            <span class="truncate">{{ $child->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($wearerAttribute?->values->isNotEmpty())
                <div class="kba-filter-card bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 text-sm uppercase tracking-wider mb-4 border-b border-gray-100 dark:border-slate-800/80 pb-2">Shop by wearer</h3>
                    <div class="space-y-3">
                        @foreach ($wearerAttribute->values as $value)
                            <label for="wearer-{{ $value->id }}" class="flex items-center text-xs text-gray-600 dark:text-slate-300 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="selected_wearers" id="wearer-{{ $value->slug }}" value="{{ $value->slug }}" class="w-4 h-4 text-[#0C1E8C] border-gray-300 rounded focus:ring-[#0C1E8C] mr-3">
                                <span>{{ $value->value }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Brands Filter -->
            <div class="kba-filter-card bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 dark:text-gray-200 text-sm uppercase tracking-wider mb-4 border-b border-gray-100 dark:border-slate-800/80 pb-2">Brands</h3>
                <div class="space-y-3 max-h-48 overflow-y-auto scrollbar-none pr-2">
                    @foreach ($brands as $brand)
                        <label for="brand-{{ $brand->slug }}" class="flex items-center text-xs text-gray-600 dark:text-slate-300 cursor-pointer select-none">
                            <input type="checkbox" wire:model.live="selected_brands" id="brand-{{ $brand->slug }}" value="{{ $brand->slug }}" class="w-4 h-4 text-[#0C1E8C] border-gray-300 rounded focus:ring-[#0C1E8C] mr-3">
                            <span>{{ $brand->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Status Filter -->
            <div class="kba-filter-card bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 dark:text-gray-200 text-sm uppercase tracking-wider mb-4 border-b border-gray-100 dark:border-slate-800/80 pb-2">Status</h3>
                <div class="space-y-3">
                    <label for="filter-featured" class="flex items-center text-xs text-gray-600 dark:text-slate-300 cursor-pointer select-none">
                        <input type="checkbox" id="filter-featured" wire:model.live="featured" value="1" class="w-4 h-4 text-[#0C1E8C] border-gray-300 rounded focus:ring-[#0C1E8C] mr-3">
                        <span>Featured Products</span>
                    </label>
                    <label for="filter-onsale" class="flex items-center text-xs text-gray-600 dark:text-slate-300 cursor-pointer select-none">
                        <input type="checkbox" id="filter-onsale" wire:model.live="on_sale" value="1" class="w-4 h-4 text-[#0C1E8C] border-gray-300 rounded focus:ring-[#0C1E8C] mr-3">
                        <span>On Sale</span>
                    </label>
                </div>
            </div>

            <!-- Price Filter -->
            @if($showPrices)
            <div class="kba-filter-card bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-800 dark:text-gray-200 text-sm uppercase tracking-wider mb-4 border-b border-gray-100 dark:border-slate-800/80 pb-2">Price Limit</h3>
                <div class="space-y-4">
                    <div class="flex justify-between text-xs font-bold text-[#0C1E8C]">
                        <span>Max Range:</span>
                        <span>{{ \App\Support\Money::format($price_range ?: $priceMaximum) }}</span>
                    </div>
                    <input type="range" wire:model.live="price_range" min="0" max="{{ $priceMaximum }}" step="5" class="kba-price-range w-full h-1.5 bg-slate-200 dark:bg-slate-800 rounded-lg appearance-none cursor-pointer">
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>{{ \App\Support\Money::format(0) }}</span>
                        <span>{{ \App\Support\Money::format($priceMaximum) }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div class="sticky bottom-0 -mx-4 border-t border-stone-200 bg-white px-4 py-4 lg:static lg:mx-0 lg:border-0 lg:bg-transparent lg:p-0">
                <button type="button" wire:click="clearFilters" class="w-full rounded-xl border border-[#0C1E8C] bg-white px-4 py-3 text-sm font-bold text-[#0C1E8C] transition hover:bg-blue-50">
                    Clear All Filters
                </button>
            </div>
        </aside>

        <!-- Products Listing -->
        <div class="kba-catalog-results w-full lg:w-3/4 space-y-6">
            <!-- Header bar with sorting options -->
            <div class="kba-catalog-toolbar bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-2xl p-3 shadow-sm grid gap-3 md:grid-cols-[minmax(220px,1fr)_auto_auto] md:items-center">
                <label class="kba-catalog-search relative block" for="catalog-search">
                    <i class="bi bi-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input id="catalog-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search products by name…" class="h-11 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-slate-800 outline-none transition focus:border-[#0C1E8C] focus:bg-white focus:ring-4 focus:ring-blue-100">
                </label>
                <p class="whitespace-nowrap text-xs font-medium text-gray-500 dark:text-slate-400">
                    <span class="text-gray-800 dark:text-white font-bold">{{ $products->count() }}</span> of {{ $products->total() }} products
                </p>
                <div class="flex items-center gap-2 md:justify-end">
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Sort by:</span>
                    <select wire:model.live="sort" aria-label="Sort products" class="h-10 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-700 outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100 md:min-w-[168px]">
                        <option value="latest">Latest Arrivals</option>
                        @if($showPrices)<option value="price">Price (Low to High)</option>@endif
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            @if($products->isEmpty())
                <div class="kba-catalog-empty bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-2xl p-16 text-center shadow-sm">
                    <i class="bi bi-search text-5xl text-slate-350 block mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mt-4">No Products Found</h3>
                    <p class="text-gray-500 dark:text-slate-400 text-sm mt-1">Try updating your filters or search criteria.</p>
                </div>
            @else
                <div class="kba-product-grid kba-catalog-product-grid">
                    @foreach ($products as $product)
                        <article class="kba-product-card" wire:key="product-card-{{ $product->id }}">
                            <div class="kba-product-image-wrap">
                                @if($product->on_sale && $product->discount_percentage)
                                    <span class="kba-sale-badge">-{{ $product->discount_percentage }}%</span>
                                @elseif($product->on_sale)
                                    <span class="kba-sale-badge">Sale</span>
                                @endif

                                <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-image">
                                    <img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" width="360" height="360" loading="lazy" decoding="async">
                                </a>

                                <div class="kba-product-action-bar">
                                    @if($wishlistEnabled)<button type="button"
                                            wire:click.prevent="toggleWishlist({{ $product->id }})" 
                                            class="kba-action-btn kba-action-wishlist {{ in_array($product->id, $wishlistProductIds ?? []) ? 'is-active' : '' }}" 
                                            title="Add to Wishlist" 
                                            aria-label="Wishlist {{ $product->name }}">
                                        <i class="bi {{ in_array($product->id, $wishlistProductIds ?? []) ? 'bi-heart-fill text-red-500' : 'bi-heart' }}"></i>
                                    </button>
                                    @endif
                                    @if($commerceEnabled)<button type="button"
                                            wire:click.prevent="addToCart({{ $product->id }})" 
                                            class="kba-action-btn kba-action-cart" 
                                            title="Add to Bag" 
                                            aria-label="Add {{ $product->name }} to bag">
                                        <i wire:loading.remove wire:target="addToCart({{ $product->id }})" class="bi bi-bag"></i>
                                        <i wire:loading wire:target="addToCart({{ $product->id }})" class="bi bi-arrow-repeat kba-spin"></i>
                                    </button>
                                    @endif
                                    @if($quickViewEnabled)<button type="button"
                                            @click.prevent="$dispatch('open-quick-view', { productId: {{ $product->id }} })" 
                                            class="kba-action-btn kba-action-quickview" 
                                            title="Quick View" 
                                            aria-label="Quick view {{ $product->name }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @endif
                                </div>
                                @if($commerceEnabled)<button type="button" wire:click.prevent="addToCart({{ $product->id }})" class="kba-hover-add-to-cart" aria-label="Add {{ $product->name }} to cart">
                                    <i wire:loading.remove wire:target="addToCart({{ $product->id }})" class="bi bi-cart3"></i>
                                    <i wire:loading wire:target="addToCart({{ $product->id }})" class="bi bi-arrow-repeat kba-spin"></i>
                                    Add to Cart
                                </button>
                                @else<a wire:navigate href="{{ route('quote.request', ['product' => $product->slug]) }}" class="kba-hover-add-to-cart" aria-label="{{ $quoteLabel }} for {{ $product->name }}"><i class="bi bi-chat-quote"></i> {{ $quoteLabel }}</a>@endif
                            </div>

                            <div class="kba-product-info">
                                <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-title">{{ $product->name }}</a>
                                @if($reviewsEnabled)<div class="kba-rating" role="img" aria-label="Rated {{ $product->average_rating ?: 5 }} out of 5">
                                    @for($star = 1; $star <= 5; $star++)
                                        <i class="bi {{ $star <= round((float) ($product->average_rating ?: 5)) ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                                    @endfor
                                </div>@endif
                                @if($showPrices)<div class="kba-price">
                                    @if($product->isVariable())
                                        <strong>{{ $product->getFormattedPriceRange() }}</strong>
                                    @elseif($product->on_sale && $product->discount_percentage)
                                        <del>{{ \App\Support\Money::format($product->regular_price) }}</del>
                                        <strong>{{ \App\Support\Money::format($product->sale_price) }}</strong>
                                    @else
                                        <strong class="regular">{{ \App\Support\Money::format($product->sale_price) }}</strong>
                                    @endif
                                </div>@endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-8 flex justify-end">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
</div>
