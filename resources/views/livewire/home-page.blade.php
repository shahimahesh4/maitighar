@push('head')
    <link rel="preload" as="image" href="{{ asset('images/banners/main_banner_headphones.png') }}" fetchpriority="high">
@endpush

<div class="kba-home bg-[#f8fafc]/50">
    <!-- Hero / Banners Section (Matching Reference Design) -->
    <section class="kba-container pt-5 pb-3">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            
            <!-- Left Large Hero Banner (Col 8) -->
            <div class="lg:col-span-8 relative group rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm aspect-[16/9] sm:aspect-[16/8.2] lg:h-[370px] bg-[#1a082e]">
                <a wire:navigate href="{{ route('all-products') }}" class="block w-full h-full relative" aria-label="Authentic Nepalese Handicrafts & Heritage">
                    <img src="{{ asset('images/banners/main_banner_headphones.png') }}" 
                         alt="Authentic Nepalese Handicrafts & Heritage Collection" 
                         class="w-full h-full object-cover object-center group-hover:scale-[1.01] transition-transform duration-300">
                </a>

                <!-- Pagination dots indicator -->
                <div class="absolute bottom-3.5 left-1/2 -translate-x-1/2 flex items-center gap-1.5 pointer-events-auto z-10" aria-label="Slider pagination">
                    <span class="w-7 h-2 rounded-full bg-white shadow-md block transition-all" aria-current="true"></span>
                    <span class="w-2 h-2 rounded-full bg-white/60 hover:bg-white shadow block transition-all cursor-pointer"></span>
                    <span class="w-2 h-2 rounded-full bg-white/60 hover:bg-white shadow block transition-all cursor-pointer"></span>
                </div>
            </div>

            <!-- Right Stacked Banners (Col 4) -->
            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4 sm:gap-5 justify-between">
                
                <!-- Top Right Banner (Summer Sale) -->
                <div class="flex-1 rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm relative group bg-sky-100 aspect-[16/8] sm:aspect-auto lg:h-[175px]">
                    <a wire:navigate href="{{ route('all-products', ['on_sale' => 1]) }}" class="block w-full h-full" aria-label="Summer Sale Offers">
                        <img src="{{ asset('images/banners/summer_sale_banner.png') }}" 
                             alt="Summer Sale" 
                             class="w-full h-full object-cover object-center group-hover:scale-[1.02] transition-transform duration-200">
                    </a>
                </div>

                <!-- Bottom Right Banner (Awesome Big Sale Offer) -->
                <div class="flex-1 rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm relative group bg-[#fef08a] aspect-[16/8] sm:aspect-auto lg:h-[175px]">
                    <a wire:navigate href="{{ route('all-products') }}" class="block w-full h-full" aria-label="Awesome Big Sale Offer">
                        <img src="{{ asset('images/banners/big_sale_banner.png') }}" 
                             alt="Awesome Big Sale Offer" 
                             class="w-full h-full object-cover object-center group-hover:scale-[1.02] transition-transform duration-200">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust & Features Badges Section (Directly beneath banners) -->
    <section class="kba-container py-3 mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6 sm:gap-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                
                <!-- 1. Worldwide Delivery -->
                <div class="flex flex-col items-center text-center px-2 pt-3 sm:pt-0">
                    <div class="w-12 h-12 flex items-center justify-center text-[#EC0205] text-3xl mb-2.5">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Worldwide Delivery</h3>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">200 countries and regions worldwide</p>
                </div>

                <!-- 2. 30 Day Return Policy -->
                <div class="flex flex-col items-center text-center px-2 pt-3 sm:pt-0">
                    <div class="w-12 h-12 flex items-center justify-center text-[#EC0205] text-3xl mb-2.5">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">30 Day Return Policy</h3>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">Merchandise must be returned within 30 days</p>
                </div>

                <!-- 3. Secure Payment -->
                <div class="flex flex-col items-center text-center px-2 pt-3 sm:pt-0">
                    <div class="w-12 h-12 flex items-center justify-center text-[#EC0205] text-3xl mb-2.5">
                        <i class="bi bi-credit-card-2-front"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Secure Payment</h3>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">Pay with popular and secure payment methods</p>
                </div>

                <!-- 4. International Brands -->
                <div class="flex flex-col items-center text-center px-2 pt-3 sm:pt-0">
                    <div class="w-12 h-12 flex items-center justify-center text-[#EC0205] text-3xl mb-2.5">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">International Brands</h3>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">200 countries and regions worldwide</p>
                </div>

                <!-- 5. 24/7 Online Support -->
                <div class="flex flex-col items-center text-center px-2 pt-3 sm:pt-0 col-span-2 md:col-span-1">
                    <div class="w-12 h-12 flex items-center justify-center text-[#EC0205] text-3xl mb-2.5">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">24/7 Online Support</h3>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">200 countries and regions worldwide</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Shop by Category Section -->
    @if(isset($featured_categories) && $featured_categories->isNotEmpty())
        <section class="kba-container py-3 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <span class="text-xs font-bold text-[#EA0202] uppercase tracking-wider">Curated Collections</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">Shop By Category</h2>
                </div>
                <a wire:navigate href="{{ route('product-categories') }}" class="text-xs sm:text-sm font-semibold text-[#EA0202] hover:underline flex items-center gap-1">
                    All Categories <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                @foreach ($featured_categories as $cat)
                    <a wire:navigate href="{{ route('all-products', ['selected_categories' => [$cat->id]]) }}" class="group flex flex-col items-center bg-white border border-slate-200/80 hover:border-[#EA0202] rounded-2xl p-4 text-center transition-all hover:shadow-md">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-slate-50 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                            @if($cat->image)
                                <img src="{{ $cat->getImageUrl() }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="bi bi-grid text-2xl text-slate-400 group-hover:text-[#EA0202]"></i>
                            @endif
                        </div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-[#EA0202] line-clamp-1 transition-colors">
                            {{ $cat->name }}
                        </h3>
                        <span class="text-[11px] text-slate-400 mt-0.5">Explore items</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="kba-products-section" id="gallery">
        <div class="kba-section-heading">
            <span></span>
            <h2>New Arrivals</h2>
            <span></span>
        </div>

        <div class="kba-product-grid">
            @forelse ($new_arrivals as $product)
                <article class="kba-product-card" wire:key="new-arrival-{{ $product->id }}">
                    <div class="kba-product-image-wrap">
                        @if($product->on_sale)
                            <span class="kba-sale-badge">-{{ (($product->id % 3) + 1) * 10 + 3 }}%</span>
                        @endif
                        <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-image">
                            <img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" width="360" height="360" loading="lazy" decoding="async">
                        </a>
                        <button type="button" wire:click.prevent="addToCart({{ $product->id }})" class="kba-quick-cart" aria-label="Add {{ $product->name }} to cart" title="Add to bag">
                            <i wire:loading.remove wire:target="addToCart({{ $product->id }})" class="bi bi-bag-plus"></i>
                            <i wire:loading wire:target="addToCart({{ $product->id }})" class="bi bi-arrow-repeat kba-spin"></i>
                        </button>
                    </div>
                    <div class="kba-product-info">
                        <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-title">{{ $product->name }}</a>
                        @if(($product->average_rating ?? 0) > 0)
                            <div class="kba-rating" role="img" aria-label="Rated {{ $product->average_rating }} out of 5">
                                @for($star = 1; $star <= 5; $star++)
                                    <i class="bi {{ $star <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                                @endfor
                            </div>
                        @endif
                        <div class="kba-price">
                            @if($product->discount_percentage)
                                <del>{{ \App\Support\Money::format($product->regular_price) }}</del>
                                <strong>{{ \App\Support\Money::format($product->sale_price) }}</strong>
                            @else
                                <strong class="regular">{{ \App\Support\Money::format($product->sale_price) }}</strong>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="kba-empty-products">
                    <i class="bi bi-gem"></i>
                    <p>New handcrafted products are being prepared.</p>
                    <a wire:navigate href="{{ route('all-products') }}">Browse the shop</a>
                </div>
            @endforelse
        </div>
    </section>

    <section class="kba-products-section kba-sale-section" id="on-sale">
        <div class="kba-section-heading">
            <span></span>
            <h2>On-Sale Products</h2>
            <span></span>
        </div>

        <div class="kba-product-grid">
            @forelse ($sale_products as $product)
                <article class="kba-product-card" wire:key="sale-product-{{ $product->id }}">
                    <div class="kba-product-image-wrap">
                        <span class="kba-sale-badge">-{{ (($product->id % 3) + 1) * 10 + 3 }}%</span>
                        <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-image">
                            <img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" width="360" height="360" loading="lazy" decoding="async">
                        </a>
                        <button type="button" wire:click.prevent="addToCart({{ $product->id }})" class="kba-quick-cart" aria-label="Add {{ $product->name }} to cart" title="Add to bag">
                            <i wire:loading.remove wire:target="addToCart({{ $product->id }})" class="bi bi-bag-plus"></i>
                            <i wire:loading wire:target="addToCart({{ $product->id }})" class="bi bi-arrow-repeat kba-spin"></i>
                        </button>
                    </div>
                    <div class="kba-product-info">
                        <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-title">{{ $product->name }}</a>
                        @if(($product->average_rating ?? 0) > 0)
                            <div class="kba-rating" role="img" aria-label="Rated {{ $product->average_rating }} out of 5">
                                @for($star = 1; $star <= 5; $star++)
                                    <i class="bi {{ $star <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                                @endfor
                            </div>
                        @endif
                        <div class="kba-price">
                            <del>{{ \App\Support\Money::format($product->regular_price) }}</del>
                            <strong>{{ \App\Support\Money::format($product->sale_price) }}</strong>
                        </div>
                    </div>
                </article>
            @empty
                @foreach($new_arrivals->where('on_sale', true)->take(2) as $product)
                    <article class="kba-product-card" wire:key="fallback-sale-{{ $product->id }}">
                        <div class="kba-product-image-wrap">
                            <span class="kba-sale-badge">Sale</span>
                            <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-image">
                                <img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" width="360" height="360" loading="lazy" decoding="async">
                            </a>
                        </div>
                        <div class="kba-product-info">
                            <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="kba-product-title">{{ $product->name }}</a>
                            <div class="kba-price"><strong>{{ \App\Support\Money::format($product->sale_price) }}</strong></div>
                        </div>
                    </article>
                @endforeach
            @endforelse
        </div>

        <div class="kba-shop-all-wrap">
            <a wire:navigate href="{{ route('all-products') }}" class="kba-shop-all">View All Products <i class="bi bi-arrow-right"></i></a>
        </div>
    </section>

</div>
