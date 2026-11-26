@push('head')
    <link rel="preload" as="image" href="{{ asset('images/banners/palpali_dhaka_collection_banner.webp') }}" fetchpriority="high">
@endpush

<div class="kba-home bg-[#f8fafc]/50">
    <!-- Full-Width Hero Revolution Slider -->
    @if($home['show_slider'])
    <section class="w-full">
        <livewire:slider location="homepage-hero" />
    </section>
    @endif

    <!-- Maitighar Service Promise -->
    @if($home['show_service_promise'])
    <section class="kba-container kba-trust-section" aria-label="Maitighar service promise">
        <div class="kba-trust-panel">
            <div class="kba-trust-grid">
                @foreach($home['service_promises'] as $promise)
                    <article class="kba-trust-item">
                        <span class="kba-trust-icon"><i class="bi {{ $promise['icon'] ?? 'bi-patch-check' }}"></i></span>
                        <h3>{{ $promise['title'] ?? '' }}</h3>
                        <p>{{ $promise['description'] ?? '' }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($home['show_new_arrivals'])
    <section class="kba-products-section" id="gallery">
        <div class="kba-section-heading">
            <span></span>
            <h2>{{ $home['new_arrivals_title'] }}</h2>
            <span></span>
        </div>

        <div class="kba-product-carousel">
            <button type="button" class="kba-product-carousel-nav kba-product-carousel-nav--previous" aria-label="Show previous new arrivals">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="swiper kba-product-swiper kba-new-arrivals-swiper" data-autoplay-speed="4000" data-transition-speed="800" aria-label="New arrival products">
                <div class="swiper-wrapper kba-product-grid kba-carousel-track">
            @forelse ($new_arrivals as $product)
                <article class="swiper-slide kba-product-card" wire:key="new-arrival-product-{{ $product->id }}">
                    <div class="kba-product-image-wrap">
                        @if($product->on_sale)
                            <span class="kba-sale-badge">-{{ (($product->id % 3) + 1) * 10 + 3 }}%</span>
                        @endif
                        <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-image">
                            <img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" width="360" height="360" loading="lazy" decoding="async">
                        </a>
                        <div class="kba-product-action-bar">
                            @if($wishlistEnabled)<button type="button" wire:click.prevent="toggleWishlist({{ $product->id }})" class="kba-action-btn kba-action-wishlist {{ in_array($product->id, $wishlistProductIds ?? []) ? 'is-active' : '' }}" title="Add to Wishlist" aria-label="Wishlist {{ $product->name }}">
                                <i class="bi {{ in_array($product->id, $wishlistProductIds ?? []) ? 'bi-heart-fill text-red-500' : 'bi-heart' }}"></i>
                            </button>
                            @endif
                            @if($commerceEnabled)
                            <button type="button" wire:click.prevent="addToCart({{ $product->id }})" class="kba-action-btn kba-action-cart" title="Add to Bag" aria-label="Add {{ $product->name }} to bag">
                                <i wire:loading.remove wire:target="addToCart({{ $product->id }})" class="bi bi-bag"></i>
                                <i wire:loading wire:target="addToCart({{ $product->id }})" class="bi bi-arrow-repeat kba-spin"></i>
                            </button>
                            @endif
                            @if($quickViewEnabled)<button type="button" @click.prevent="$dispatch('open-quick-view', { productId: {{ $product->id }} })" class="kba-action-btn kba-action-quickview" title="Quick View" aria-label="Quick view {{ $product->name }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            @endif
                        </div>
                        @if($commerceEnabled)
                        <button type="button" wire:click.prevent="addToCart({{ $product->id }})" class="kba-hover-add-to-cart" aria-label="Add {{ $product->name }} to cart">
                            <i wire:loading.remove wire:target="addToCart({{ $product->id }})" class="bi bi-cart3"></i>
                            <i wire:loading wire:target="addToCart({{ $product->id }})" class="bi bi-arrow-repeat kba-spin"></i>
                            Add to Cart
                        </button>
                        @else
                        <a wire:navigate href="{{ route('quote.request', ['product' => $product->slug]) }}" class="kba-hover-add-to-cart" aria-label="{{ $quoteLabel }} for {{ $product->name }}"><i class="bi bi-chat-quote"></i> {{ $quoteLabel }}</a>
                        @endif
                    </div>
                    <div class="kba-product-info">
                        <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-title">{{ $product->name }}</a>
                        @php $cardRating = round((float) ($product->average_rating ?: 5)); @endphp
                        <div @class(['kba-rating', 'hidden' => ! $reviewsEnabled]) role="img" aria-label="Rated {{ $cardRating }} out of 5">
                            @for($star = 1; $star <= 5; $star++)
                                <i class="bi {{ $star <= $cardRating ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                            @endfor
                            @if(($product->approved_reviews_count ?? 0) > 0)
                                <span class="text-[10px] text-stone-400 ms-1 font-normal">({{ $product->approved_reviews_count }})</span>
                            @endif
                        </div>
                        @if($showPrices)<div class="kba-price">
                            @if($product->isVariable())
                                <strong>{{ $product->getFormattedPriceRange() }}</strong>
                            @elseif($product->on_sale && $product->discount_percentage)
                                <del>{{ \App\Support\Money::format($product->regular_price) }}</del>
                                <strong>{{ \App\Support\Money::format($product->sale_price) }}</strong>
                            @else
                                <strong>{{ \App\Support\Money::format($product->sale_price) }}</strong>
                            @endif
                        </div>@endif
                    </div>
                </article>
            @empty
                <div class="kba-empty-products"><i class="bi bi-gem"></i><p>New handcrafted products are being prepared.</p><a wire:navigate href="{{ route('all-products') }}">Browse the shop</a></div>
            @endforelse
                </div>
            </div>
            <button type="button" class="kba-product-carousel-nav kba-product-carousel-nav--next" aria-label="Show more new arrivals">
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
        </div>

        <div class="kba-shop-all-wrap">
            <a wire:navigate href="{{ route('all-products') }}" class="kba-shop-all">View All Products <i class="bi bi-arrow-right"></i></a>
        </div>
    </section>
    @endif

    @if($home['show_women_collection'])
    <section class="kba-cultural-collection kba-cultural-collection--women" aria-labelledby="cultural-collection-title">
        <div class="kba-cultural-collection__copy">
            <p><span></span> Traditional Nepali <span></span></p>
            <h2 id="cultural-collection-title">Cultural <em>Dress</em><span>For Women</span></h2>
            <div class="kba-cultural-collection__divider"><i></i></div>
            <h3>{{ $home['women_collection_description'] }}</h3>
            <a wire:navigate href="{{ $wearer_values->get('women') ? route('product-wearer.products', ['wearer' => $wearer_values->get('women')->slug]) : route('all-products') }}" class="kba-cultural-collection__cta">Explore Collection <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>

        <div class="kba-cultural-collection__products"
             aria-label="Cultural Clothing subcategories">
            @if ($women_cultural_subcategories->count() > 3)
                <button type="button" class="kba-cultural-carousel-nav kba-cultural-carousel-nav--previous" aria-label="Show previous Cultural Clothing subcategories">
                    <i class="bi bi-chevron-left" aria-hidden="true"></i>
                </button>
            @endif
            <div class="swiper kba-cultural-swiper" data-autoplay-speed="3200" data-transition-speed="800">
                <div class="swiper-wrapper kba-cultural-collection__track">
            @foreach ($women_cultural_subcategories as $subcategory)
                <a wire:navigate href="{{ $wearer_values->get('women') ? route('product-category-wearer.products', ['category' => $subcategory->slug, 'wearer' => $wearer_values->get('women')->slug]) : route('product-category.products', ['category' => $subcategory->slug]) }}" class="swiper-slide kba-cultural-product" wire:key="cultural-subcategory-{{ $subcategory->id }}">
                    <div class="kba-cultural-product__image">
                        <img src="{{ $subcategory->getImageUrl() }}" alt="{{ $subcategory->name }}" loading="lazy" decoding="async">
                    </div>
                    <span>{{ $subcategory->name }}</span>
                </a>
            @endforeach
                </div>
            </div>
            @if ($women_cultural_subcategories->count() > 3)
                <button type="button" class="kba-cultural-carousel-nav kba-cultural-carousel-nav--next" aria-label="Show more Cultural Clothing subcategories">
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </button>
            @endif
        </div>
    </section>
    @endif

    @if($home['show_featured_products'])
    <section class="kba-products-section kba-sale-section" id="on-sale">
        <div class="kba-section-heading">
            <span></span>
            <h2>{{ $home['featured_products_title'] }}</h2>
            <span></span>
        </div>

        <div class="kba-product-carousel">
            <button type="button" class="kba-product-carousel-nav kba-product-carousel-nav--previous" aria-label="Show previous sale products">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="swiper kba-sale-product-swiper" data-autoplay-speed="4000" data-transition-speed="800" aria-label="On-sale products">
                <div class="swiper-wrapper kba-product-grid kba-carousel-track">
            @forelse ($featured_products as $product)
                <article class="swiper-slide kba-product-card" wire:key="sale-product-{{ $product->id }}">
                    <div class="kba-product-image-wrap">
                        <span class="kba-sale-badge">-{{ (($product->id % 3) + 1) * 10 + 3 }}%</span>
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
                        @php
                            $cardRating = round((float) ($product->average_rating ?: 5));
                        @endphp
                        <div @class(['kba-rating', 'hidden' => ! $reviewsEnabled]) role="img" aria-label="Rated {{ $cardRating }} out of 5">
                            @for($star = 1; $star <= 5; $star++)
                                <i class="bi {{ $star <= $cardRating ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                            @endfor
                            @if(($product->approved_reviews_count ?? 0) > 0)
                                <span class="text-[10px] text-stone-400 ms-1 font-normal">({{ $product->approved_reviews_count }})</span>
                            @endif
                        </div>
                        @if($showPrices)<div class="kba-price">
                            @if($product->isVariable())
                                <strong>{{ $product->getFormattedPriceRange() }}</strong>
                            @else
                                <del>{{ \App\Support\Money::format($product->regular_price) }}</del>
                                <strong>{{ \App\Support\Money::format($product->sale_price) }}</strong>
                            @endif
                        </div>@endif
                    </div>
                </article>
            @empty
                @foreach($new_arrivals->where('on_sale', true)->take(2) as $product)
                    <article class="swiper-slide kba-product-card" wire:key="fallback-sale-{{ $product->id }}">
                        <div class="kba-product-image-wrap">
                            <span class="kba-sale-badge">Sale</span>
                            <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-image">
                                <img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" width="360" height="360" loading="lazy" decoding="async">
                            </a>
                        </div>
                        <div class="kba-product-info">
                            <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-title">{{ $product->name }}</a>
                            @php
                                $cardRating = round((float) ($product->average_rating ?: 5));
                            @endphp
                            <div @class(['kba-rating', 'hidden' => ! $reviewsEnabled]) role="img" aria-label="Rated {{ $cardRating }} out of 5">
                                @for($star = 1; $star <= 5; $star++)
                                    <i class="bi {{ $star <= $cardRating ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                                @endfor
                                @if(($product->approved_reviews_count ?? 0) > 0)
                                    <span class="text-[10px] text-stone-400 ms-1 font-normal">({{ $product->approved_reviews_count }})</span>
                                @endif
                            </div>
                            @if($showPrices)<div class="kba-price">
                                <strong>{{ $product->isVariable() ? $product->getFormattedPriceRange() : \App\Support\Money::format($product->sale_price) }}</strong>
                            </div>@endif
                        </div>
                    </article>
                @endforeach
            @endforelse
                </div>
            </div>
            <button type="button" class="kba-product-carousel-nav kba-product-carousel-nav--next" aria-label="Show more sale products">
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
        </div>

        <div class="kba-shop-all-wrap">
            <a wire:navigate href="{{ route('all-products') }}" class="kba-shop-all">View All Products <i class="bi bi-arrow-right"></i></a>
        </div>
    </section>
    @endif

    @if($home['show_men_collection'])
    <section class="kba-cultural-collection kba-cultural-collection--mens" aria-labelledby="cultural-collection-repeat-title">
        <div class="kba-cultural-collection__copy">
            <p><span></span> Traditional Nepali <span></span></p>
            <h2 id="cultural-collection-repeat-title">Cultural <em>Dress</em><span>For Men</span></h2>
            <div class="kba-cultural-collection__divider"><i></i></div>
            <h3>{{ $home['men_collection_description'] }}</h3>
            <a wire:navigate href="{{ $wearer_values->get('men') ? route('product-wearer.products', ['wearer' => $wearer_values->get('men')->slug]) : route('all-products') }}" class="kba-cultural-collection__cta">Explore Collection <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>

        <div class="kba-cultural-collection__products" aria-label="Cultural Clothing subcategories">
            @if ($men_cultural_subcategories->count() > 3)
                <button type="button" class="kba-cultural-carousel-nav kba-cultural-carousel-nav--previous" aria-label="Show previous Cultural Clothing subcategories">
                    <i class="bi bi-chevron-left" aria-hidden="true"></i>
                </button>
            @endif
            <div class="swiper kba-cultural-swiper" data-autoplay-speed="3200" data-transition-speed="800">
                <div class="swiper-wrapper kba-cultural-collection__track">
                    @foreach ($men_cultural_subcategories as $subcategory)
                        <a wire:navigate href="{{ $wearer_values->get('men') ? route('product-category-wearer.products', ['category' => $subcategory->slug, 'wearer' => $wearer_values->get('men')->slug]) : route('product-category.products', ['category' => $subcategory->slug]) }}" class="swiper-slide kba-cultural-product" wire:key="cultural-subcategory-after-sale-{{ $subcategory->id }}">
                            <div class="kba-cultural-product__image">
                                <img src="{{ $subcategory->getImageUrl() }}" alt="{{ $subcategory->name }}" loading="lazy" decoding="async">
                            </div>
                            <span>{{ $subcategory->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @if ($men_cultural_subcategories->count() > 3)
                <button type="button" class="kba-cultural-carousel-nav kba-cultural-carousel-nav--next" aria-label="Show more Cultural Clothing subcategories">
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </button>
            @endif
        </div>
    </section>
    @endif

    @if($home['show_related_products'])
    <section class="kba-related-showcase" id="related-products" aria-labelledby="related-products-title">
        <div class="kba-related-showcase__inner">
            <aside class="kba-related-intro">
                <span>Related products</span>
                <h2 id="related-products-title">You May<br><em>Also Like</em></h2>
                <p>{{ $home['related_products_description'] }}</p>
                <ul>
                    <li><i class="bi bi-flower1"></i><span><strong>Authentic Craft</strong><small>100% Nepali Handmade</small></span></li>
                    <li><i class="bi bi-gem"></i><span><strong>Premium Quality</strong><small>Timeless &amp; durable</small></span></li>
                    <li><i class="bi bi-gift"></i><span><strong>Perfect for Gifting</strong><small>Share the Nepali heritage</small></span></li>
                </ul>
            </aside>
            <div class="kba-related-products">
                <a wire:navigate href="{{ route('all-products') }}" class="kba-related-view-all">View All Products <i class="bi bi-arrow-right"></i></a>
                <div class="kba-related-grid">
                    @forelse ($new_arrivals->take(4) as $product)
                        <article class="kba-related-card" wire:key="related-product-{{ $product->id }}">
                            <div class="kba-related-card__image">
                                @if($product->on_sale)<span>-{{ (($product->id % 3) + 1) * 10 + 3 }}%</span>@endif
                                @if($wishlistEnabled)<button type="button" wire:click.prevent="toggleWishlist({{ $product->id }})" class="{{ in_array($product->id, $wishlistProductIds ?? []) ? 'is-active' : '' }}" aria-label="Wishlist {{ $product->name }}"><i class="bi {{ in_array($product->id, $wishlistProductIds ?? []) ? 'bi-heart-fill' : 'bi-heart' }}"></i></button>@endif
                                <a wire:navigate href="{{ route('product-details', $product->slug) }}"><img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" loading="lazy" decoding="async"></a>
                            </div>
                            <div class="kba-related-card__body">
                                <a wire:navigate href="{{ route('product-details', $product->slug) }}">{{ $product->name }}</a>
                                @php $cardRating = round((float) ($product->average_rating ?: 5)); @endphp
                                <div @class(['kba-related-rating', 'hidden' => ! $reviewsEnabled]) role="img" aria-label="Rated {{ $cardRating }} out of 5">@for($star = 1; $star <= 5; $star++)<i class="bi {{ $star <= $cardRating ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor <small>({{ $product->approved_reviews_count ?? 0 }})</small></div>
                                @if($showPrices)<div class="kba-related-price">@if($product->isVariable())<strong>{{ $product->getFormattedPriceRange() }}</strong>@elseif($product->on_sale && $product->discount_percentage)<del>{{ \App\Support\Money::format($product->regular_price) }}</del><strong>{{ \App\Support\Money::format($product->sale_price) }}</strong>@else<strong>{{ \App\Support\Money::format($product->sale_price) }}</strong>@endif</div>@endif
                                @if($commerceEnabled)<button type="button" wire:click.prevent="addToCart({{ $product->id }})"><i wire:loading.remove wire:target="addToCart({{ $product->id }})" class="bi bi-cart3"></i><i wire:loading wire:target="addToCart({{ $product->id }})" class="bi bi-arrow-repeat kba-spin"></i> Add to Cart</button>@else<a wire:navigate href="{{ route('quote.request', ['product' => $product->slug]) }}" class="kba-related-quote">{{ $quoteLabel }} <i class="bi bi-chat-quote"></i></a>@endif
                            </div>
                        </article>
                    @empty
                        <div class="kba-empty-products"><i class="bi bi-gem"></i><p>New handcrafted products are being prepared.</p><a wire:navigate href="{{ route('all-products') }}">Browse the shop</a></div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Highlights Section (Trending Now | 1st Banner: Personalized Box Customization | Top Selling) -->
    @if($home['show_highlights'])
    <section class="kba-container py-4 my-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
            
            <!-- Left Column: Trending Now (1 col) -->
            <div class="flex flex-col">
                <div class="flex items-center justify-between mb-3 px-1">
                    <a wire:navigate href="{{ route('all-products') }}" class="group inline-flex items-center gap-1.5 text-xl font-extrabold text-[#091b38] dark:text-white font-['DM_Sans',sans-serif] uppercase tracking-tight hover:text-[#EC0205] transition-colors">
                        Trending Now
                        <i class="bi bi-arrow-up-right text-base text-stone-400 group-hover:text-[#EC0205] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all"></i>
                    </a>
                </div>
                <div class="rounded-3xl border border-stone-200/90 bg-white p-4 sm:p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex-1 flex flex-col justify-between">
                    <div class="divide-y divide-stone-100 dark:divide-slate-800">
                        @foreach($trending_products as $p)
                            <div class="flex items-center gap-3 py-3 first:pt-1 last:pb-1">
                                <a wire:navigate href="{{ route('product-details', $p->slug) }}" class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-stone-100/80 dark:bg-slate-800/80 flex items-center justify-center p-2 flex-shrink-0 group overflow-hidden border border-stone-200/50">
                                    @if($p->on_sale && $p->discount_percentage)
                                        <span class="absolute top-1 left-1 bg-[#EC0205] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-sm z-10 leading-none">
                                            -{{ $p->discount_percentage }}%
                                        </span>
                                    @endif
                                    <img src="{{ $p->getThumbnailUrl() }}" alt="{{ $p->name }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal group-hover:scale-105 transition-transform">
                                </a>
                                <div class="min-w-0 flex-1">
                                    <a wire:navigate href="{{ route('product-details', $p->slug) }}" class="block text-xs sm:text-[13px] font-bold text-stone-800 dark:text-stone-200 truncate hover:text-[#EC0205] transition-colors leading-snug">
                                        {{ $p->name }}
                                    </a>
                                    <div @class(['flex items-center gap-1.5 mt-1', 'hidden' => ! $reviewsEnabled])>
                                        <div class="flex text-amber-400 text-[10px]">
                                            @for($s = 1; $s <= 5; $s++)
                                                <i class="bi {{ $s <= round($p->approved_reviews_avg_rating ?? 5) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-[10px] text-stone-400">({{ $p->approved_reviews_count ?: 9 }} Review{{ ($p->approved_reviews_count ?: 9) > 1 ? 's' : '' }})</span>
                                    </div>
                                    <div @class(['mt-1 flex items-baseline gap-1.5 whitespace-nowrap overflow-x-hidden', 'hidden' => ! $showPrices])>
                                        @if($p->on_sale && $p->discount_percentage)
                                            <span class="text-[11px] sm:text-xs font-bold text-[#EC0205] whitespace-nowrap leading-none tracking-tight">{{ \App\Support\Money::format($p->sale_price) }}</span>
                                            <del class="text-[10px] sm:text-[11px] font-normal text-stone-400 whitespace-nowrap leading-none tracking-tight">{{ \App\Support\Money::format($p->regular_price) }}</del>
                                        @else
                                            <span class="text-[11px] sm:text-xs font-bold text-stone-900 dark:text-white whitespace-nowrap leading-none tracking-tight">{{ \App\Support\Money::format($p->sale_price) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Middle Section: Maitighar Essentials Banner - Spans 2 Columns -->
            <div class="md:col-span-2 lg:col-span-2 flex flex-col justify-start">
                <div class="h-8 mb-3 hidden lg:block" aria-hidden="true"></div>
                <a wire:navigate 
                   href="{{ $home['essentials_banner_link'] ?: route('all-products') }}"
                   class="group block relative w-full rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/90 dark:border-slate-800 bg-[#e7d8c6]/20 flex-1 flex flex-col justify-center">
                    <img src="{{ $home['essentials_banner_image'] ? \Illuminate\Support\Facades\Storage::disk('public')->url($home['essentials_banner_image']) : asset('images/banners/maitighar_meaningful_essentials_banner.webp') }}"
                         alt="Maitighar - Meaningful Everyday Essentials" 
                         width="1024"
                         height="653"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-auto lg:h-full object-contain lg:object-cover group-hover:scale-[1.02] transition-transform duration-500 rounded-2xl sm:rounded-3xl block select-none">
                </a>
            </div>

            <!-- Right Column: Top Selling (1 col) -->
            <div class="flex flex-col">
                <div class="flex items-center justify-between mb-3 px-1">
                    <a wire:navigate href="{{ route('all-products') }}" class="group inline-flex items-center gap-1.5 text-xl font-extrabold text-[#091b38] dark:text-white font-['DM_Sans',sans-serif] uppercase tracking-tight hover:text-[#EC0205] transition-colors">
                        Top Selling
                        <i class="bi bi-arrow-up-right text-base text-stone-400 group-hover:text-[#EC0205] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all"></i>
                    </a>
                </div>
                <div class="rounded-3xl border border-stone-200/90 bg-white p-4 sm:p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex-1 flex flex-col justify-between">
                    <div class="divide-y divide-stone-100 dark:divide-slate-800">
                        @foreach($top_selling_products as $p)
                            <div class="flex items-center gap-3 py-3 first:pt-1 last:pb-1">
                                <a wire:navigate href="{{ route('product-details', $p->slug) }}" class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-stone-100/80 dark:bg-slate-800/80 flex items-center justify-center p-2 flex-shrink-0 group overflow-hidden border border-stone-200/50">
                                    @if($p->on_sale && $p->discount_percentage)
                                        <span class="absolute top-1 left-1 bg-[#EC0205] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-sm z-10 leading-none">
                                            -{{ $p->discount_percentage }}%
                                        </span>
                                    @endif
                                    <img src="{{ $p->getThumbnailUrl() }}" alt="{{ $p->name }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal group-hover:scale-105 transition-transform">
                                </a>
                                <div class="min-w-0 flex-1">
                                    <a wire:navigate href="{{ route('product-details', $p->slug) }}" class="block text-xs sm:text-[13px] font-bold text-stone-800 dark:text-stone-200 truncate hover:text-[#EC0205] transition-colors leading-snug">
                                        {{ $p->name }}
                                    </a>
                                    <div @class(['flex items-center gap-1.5 mt-1', 'hidden' => ! $reviewsEnabled])>
                                        <div class="flex text-amber-400 text-[10px]">
                                            @for($s = 1; $s <= 5; $s++)
                                                <i class="bi {{ $s <= round($p->approved_reviews_avg_rating ?? 5) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-[10px] text-stone-400">({{ $p->approved_reviews_count ?: 8 }} Review{{ ($p->approved_reviews_count ?: 8) > 1 ? 's' : '' }})</span>
                                    </div>
                                    <div @class(['mt-1 flex items-baseline gap-1.5 whitespace-nowrap overflow-x-hidden', 'hidden' => ! $showPrices])>
                                        @if($p->on_sale && $p->discount_percentage)
                                            <span class="text-[11px] sm:text-xs font-bold text-[#EC0205] whitespace-nowrap leading-none tracking-tight">{{ \App\Support\Money::format($p->sale_price) }}</span>
                                            <del class="text-[10px] sm:text-[11px] font-normal text-stone-400 whitespace-nowrap leading-none tracking-tight">{{ \App\Support\Money::format($p->regular_price) }}</del>
                                        @else
                                            <span class="text-[11px] sm:text-xs font-bold text-stone-900 dark:text-white whitespace-nowrap leading-none tracking-tight">{{ \App\Support\Money::format($p->sale_price) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>    </section>
    @endif

</div>
