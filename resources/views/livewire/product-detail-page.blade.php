<div class="kba-product-detail-page w-full max-w-[1320px] py-6 px-4 sm:px-6 sm:py-8 lg:px-8 mx-auto" x-data="{ mainImage: @js($mainImage), activeTab: @js($initialTab) }" @variation-image-changed.window="mainImage = $event.detail.image">
    <!-- Breadcrumb -->
    <nav class="kba-product-detail-breadcrumb flex mb-6 text-xs text-gray-500 dark:text-slate-400 font-medium" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a wire:navigate href="/" class="inline-flex items-center hover:text-[#EC0205]">
                    <i class="bi bi-house-door-fill mr-1.5 text-sm"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right mx-1 text-gray-400 dark:text-slate-650 text-[10px]"></i>
                    <a wire:navigate href="/products" class="hover:text-[#EC0205]">Products</a>
                </div>
            </li>
            @if($product->category)
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right mx-1 text-gray-400 dark:text-slate-650 text-[10px]"></i>
                    <a wire:navigate href="/products?selected_categories[0]={{ $product->category->id }}" class="hover:text-[#EC0205]">{{ $product->category->name }}</a>
                </div>
            </li>
            @endif
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right mx-1 text-gray-400 dark:text-slate-650 text-[10px]"></i>
                    <span class="text-gray-800 dark:text-white font-semibold truncate max-w-[150px]">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="kba-product-detail-shell bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-3xl p-6 sm:p-10 shadow-sm">
        <div class="kba-product-detail-grid flex flex-col lg:flex-row gap-12">
            <!-- Left: Product Images -->
            <div class="kba-product-detail-gallery w-full lg:w-1/2 space-y-6">
                <!-- Large Main Image View -->
                <div class="kba-product-main-image relative bg-slate-50 dark:bg-slate-950/20 border border-gray-100 dark:border-slate-800/80 rounded-2xl aspect-square overflow-hidden flex items-center justify-center shadow-sm">
                    <img x-bind:src="mainImage" alt="{{ $product->name }}" class="object-contain w-full h-full p-6 transition duration-300">
                    
                    @if($product->on_sale)
                        <span class="absolute top-4 left-4 bg-red-500 text-white font-bold text-xs uppercase tracking-wider px-3 py-1 rounded-full shadow">Sale</span>
                    @endif
                </div>

                <!-- Scrolling Product Gallery -->
                @if(count($productGallery) > 1)
                    <div class="kba-product-thumbnails flex snap-x gap-3 overflow-x-auto pb-2" aria-label="Product image gallery">
                        @foreach ($productGallery as $index => $image)
                            <button
                                type="button"
                                data-image="{{ $image }}"
                                x-on:click="mainImage = $el.dataset.image"
                                class="h-20 w-20 flex-none snap-start overflow-hidden rounded-xl border bg-stone-50 p-1 transition hover:border-[#0C1E8C] focus:border-[#0C1E8C] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:border-slate-800 dark:bg-slate-950/20"
                                x-bind:class="mainImage === $el.dataset.image ? 'border-[#0C1E8C] ring-2 ring-blue-100' : 'border-slate-200'"
                                aria-label="View product image {{ $index + 1 }}"
                            >
                                <img src="{{ $image }}" alt="{{ $product->name }} gallery image {{ $index + 1 }}" class="h-full w-full object-contain">
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Shipping Feature List -->
                <div class="kba-product-shipping pt-6 border-t border-gray-100 dark:border-slate-800/85">
                    <div class="flex items-center space-x-3 text-sm text-gray-600 dark:text-slate-400">
                        <i class="bi bi-truck text-xl text-[#0C1E8C]"></i>
                        <div>
                            <span class="font-bold text-gray-800 dark:text-white">Delivery options available</span>
                            <p class="text-xs text-gray-400 mt-0.5">Shipping cost and delivery time are calculated at checkout.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Information -->
            <div class="kba-product-detail-info w-full lg:w-1/2 space-y-6 text-left">
                <div class="kba-product-detail-heading space-y-2">
                    <span class="text-xs text-[#0C1E8C] dark:text-amber-400 font-bold uppercase tracking-widest">{{ $product->brand->name ?? 'Brand' }}</span>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-800 dark:text-white leading-tight">
                        {{ $product->name }}
                    </h1>
                    
                    <!-- Ratings summary -->
                    <div class="flex items-center space-x-2 pt-1">
                        <div class="flex text-yellow-400">
                            @for ($i = 0; $i < $fullStars; $i++)
                                <i class="bi bi-star-fill text-xs"></i>
                            @endfor
                            @if ($hasHalfStar)
                                <i class="bi bi-star-half text-xs"></i>
                            @endif
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="bi bi-star text-xs"></i>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500 dark:text-slate-400 font-medium">
                            {{ $product->average_rating }} ({{ $product->approvedReviews->count() }} {{ Str::plural('review', $product->approvedReviews->count()) }})
                        </span>
                    </div>
                    
                    <!-- Stock Badge -->
                    <div class="pt-2">
                        @if($isVariable)
                            @if($selectedVariationId)
                                @if($isAvailable)
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>
                                        In Stock ({{ $displayStock }} available)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-400"></span>
                                        Out of Stock
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                                    Select Options
                                </span>
                            @endif
                        @else
                            @if($product->in_stock)
                                <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>
                                    In Stock
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-400"></span>
                                    Out of Stock
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Price and Info -->
                <div class="kba-product-detail-price py-4 border-y border-gray-100 dark:border-slate-800/80">
                    <div class="flex flex-wrap items-end gap-x-3 gap-y-1">
                        @if($isVariable)
                            @if($displayPrice !== null)
                                <p class="text-3xl font-extrabold text-[#EC0205]">
                                    {{ \App\Support\Money::format($displayPrice) }}
                                </p>
                                @if($displayRegularPrice && $displayRegularPrice > $displayPrice)
                                    <del class="pb-1 text-base font-semibold text-gray-400">{{ \App\Support\Money::format($displayRegularPrice) }}</del>
                                    @php
                                        $varDiscount = (int) round((($displayRegularPrice - $displayPrice) / $displayRegularPrice) * 100);
                                    @endphp
                                    <span class="mb-1 rounded-full bg-red-50 px-2 py-1 text-xs font-bold text-red-600">
                                        {{ $varDiscount }}% OFF
                                    </span>
                                @endif
                            @else
                                <p class="text-2xl sm:text-3xl font-extrabold text-[#EC0205]">
                                    {{ $product->getFormattedPriceRange() }}
                                </p>
                            @endif
                        @else
                            <p class="text-3xl font-extrabold text-[#EC0205]">
                                {{ \App\Support\Money::format($product->sale_price) }}
                            </p>
                            @if($product->discount_percentage)
                                <del class="pb-1 text-base font-semibold text-gray-400">{{ \App\Support\Money::format($product->regular_price) }}</del>
                                <span class="mb-1 rounded-full bg-red-50 px-2 py-1 text-xs font-bold text-red-600">
                                    {{ $product->discount_percentage }}% OFF
                                </span>
                            @endif
                        @endif
                    </div>
                    @php
                        $activeSku = $isVariable ? ($selectedVariationSku ?: $product->sku) : $product->sku;
                    @endphp
                    @if($activeSku)
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-2 font-mono">SKU: {{ $activeSku }}</p>
                    @endif
                </div>

                <!-- Short Description -->
                <div class="kba-product-short-description text-gray-600 dark:text-slate-300 text-sm leading-relaxed prose dark:prose-invert">
                    @if($product->short_description)
                        {!! \App\Support\HtmlSanitizer::sanitize(preg_match('/<[^>]+>/', $product->short_description) ? $product->short_description : Str::markdown($product->short_description)) !!}
                    @else
                        <p>No description provided for this product.</p>
                    @endif
                </div>

                <!-- Attributes & Variations Selector -->
                @if($isVariable && !empty($variationAttributes))
                    <div class="kba-variation-selectors space-y-4 py-4 border-b border-gray-100 dark:border-slate-800/80">
                        @foreach($variationAttributes as $attr)
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">
                                        {{ $attr['name'] }}:
                                        <span class="font-normal text-gray-500 normal-case ml-1">
                                            @php
                                                $selectedValId = $selectedAttributes[$attr['id']] ?? null;
                                                $selectedVal = $selectedValId ? collect($attr['values'])->firstWhere('id', $selectedValId) : null;
                                            @endphp
                                            {{ $selectedVal ? $selectedVal['value'] : 'Select an option' }}
                                        </span>
                                    </label>
                                </div>

                                @if(($attr['type'] ?? 'select') === 'color')
                                    <!-- Colour Swatches -->
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        @foreach($attr['values'] as $val)
                                            @php
                                                $isSelected = ($selectedAttributes[$attr['id']] ?? null) == $val['id'];
                                                $bg = $val['color_code'] ?: '#cccccc';
                                                $isWhite = in_array(strtolower($val['color_code'] ?? ''), ['#ffffff', '#fff', '#f9fafb']);
                                            @endphp
                                            <button
                                                type="button"
                                                wire:click="selectAttribute({{ $attr['id'] }}, {{ $val['id'] }})"
                                                title="{{ $val['value'] }}"
                                                class="relative w-8 h-8 rounded-full border-2 transition-all flex items-center justify-center p-0.5 shadow-sm {{ $isSelected ? 'ring-2 ring-[#EC0205] border-white scale-110 shadow-md' : 'border-gray-300 hover:border-gray-500 hover:scale-105' }}"
                                                style="background-color: {{ $bg }};"
                                            >
                                                @if($isSelected)
                                                    <svg class="w-4 h-4 {{ $isWhite ? 'text-gray-900' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Text Buttons (Size, Material, etc.) -->
                                    <div class="flex flex-wrap items-center gap-2">
                                        @foreach($attr['values'] as $val)
                                            @php
                                                $isSelected = ($selectedAttributes[$attr['id']] ?? null) == $val['id'];
                                            @endphp
                                            <button
                                                type="button"
                                                wire:click="selectAttribute({{ $attr['id'] }}, {{ $val['id'] }})"
                                                class="px-4 py-2 text-xs font-bold rounded-xl border transition-all {{ $isSelected ? 'bg-[#EC0205] text-white border-[#EC0205] shadow-sm ring-2 ring-red-200' : 'bg-gray-50 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border-gray-200 dark:border-slate-700 hover:border-[#EC0205] hover:text-[#EC0205]' }}"
                                            >
                                                {{ $val['value'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if($variationNotice)
                            <p class="text-xs font-medium {{ $selectedVariationId && !$isAvailable ? 'text-red-600' : 'text-amber-600' }} mt-1">
                                {{ $variationNotice }}
                            </p>
                        @endif
                    </div>
                @endif

                <!-- Actions -->
                <div class="kba-product-actions space-y-4 pt-4">
                    <div class="kba-product-actions-row flex items-center gap-4">
                        <div class="kba-product-quantity w-32">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Quantity</label>
                            <div class="flex items-center bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl overflow-hidden">
                                <button type="button" wire:click="decreaseQty" class="p-2.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition w-10 flex items-center justify-center font-bold">
                                    -
                                </button>
                                <span class="flex-1 text-center font-bold text-sm text-gray-800 dark:text-white">
                                    {{ $quantity }}
                                </span>
                                <button type="button" wire:click="increaseQty" class="p-2.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition w-10 flex items-center justify-center font-bold">
                                    +
                                </button>
                            </div>
                        </div>

                        <div class="kba-product-purchase flex-1 pt-6 flex items-center gap-2">
                            @if($isVariable)
                                @if(!$selectedVariationId)
                                    <button type="button" disabled class="flex-1 py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-slate-800 dark:text-slate-500">
                                        Select Options
                                    </button>
                                @elseif(!$isAvailable)
                                    <button type="button" disabled class="flex-1 py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-slate-800 dark:text-slate-500">
                                        Out of Stock
                                    </button>
                                @else
                                    <button type="button" wire:click="addToCart({{ $product->id }})" class="kba-product-add-button flex-1 py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-[#EC0205] text-white hover:bg-[#C90204] shadow-md shadow-red-500/15 transition hover-lift">
                                        <svg class="kba-product-add-icon w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="addToCart({{ $product->id }})">Add to Cart</span>
                                        <span wire:loading wire:target="addToCart({{ $product->id }})">Adding...</span>
                                    </button>
                                @endif
                            @else
                                @if($product->in_stock)
                                    <button type="button" wire:click="addToCart({{ $product->id }})" class="kba-product-add-button flex-1 py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-[#EC0205] text-white hover:bg-[#C90204] shadow-md shadow-red-500/15 transition hover-lift">
                                        <svg class="kba-product-add-icon w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="addToCart({{ $product->id }})">Add to Cart</span>
                                        <span wire:loading wire:target="addToCart({{ $product->id }})">Adding...</span>
                                    </button>
                                @else
                                    <button type="button" disabled class="flex-1 py-3 px-6 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-slate-800 dark:text-slate-500">
                                        Out of Stock
                                    </button>
                                @endif
                            @endif

                            <button type="button" wire:click="toggleWishlist" class="kba-product-wishlist-button p-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700/60 transition shadow-sm" title="Add to Wishlist">
                                @if($isWishlisted)
                                    <i class="bi bi-heart-fill text-red-500 text-lg"></i>
                                @else
                                    <i class="bi bi-heart text-gray-500 dark:text-slate-400 text-lg"></i>
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
</div>
        </div>
    </div>

    @if(!empty($detailTabs))
        <section class="kba-product-detail-tabs mt-8 overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex gap-1 overflow-x-auto border-b border-stone-200 px-5 sm:px-8">
                @foreach($detailTabs as $tabKey => $tabLabel)
                    <button type="button" x-on:click="activeTab = '{{ $tabKey }}'" class="relative whitespace-nowrap px-4 py-5 text-sm font-bold transition" x-bind:class="activeTab === '{{ $tabKey }}' ? 'text-[#0C1E8C]' : 'text-slate-500 hover:text-[#0C1E8C]'">
                        {{ $tabLabel }}
                        <span class="absolute inset-x-3 bottom-0 h-0.5 bg-[#0C1E8C]" x-show="activeTab === '{{ $tabKey }}'"></span>
                    </button>
                @endforeach
            </div>
            <div class="p-6 sm:p-9">
                @foreach($detailTabs as $tabKey => $tabLabel)
                    <div x-show="activeTab === '{{ $tabKey }}'" @if($tabKey !== $initialTab) x-cloak @endif>
                        @if($tabKey === 'specifications')
                            <div class="overflow-hidden rounded-xl border border-stone-200 bg-stone-50/60 dark:border-slate-800 dark:bg-slate-900/60 max-w-3xl">
                                <dl>
                                    @foreach($descriptiveAttributes as $attr)
                                        <div class="grid grid-cols-[minmax(130px,1fr)_2fr] border-b border-stone-200 dark:border-slate-800 last:border-0">
                                            <dt class="bg-slate-50 dark:bg-slate-800/80 px-5 py-3 text-xs sm:text-sm font-bold text-stone-700 dark:text-slate-200 border-r border-stone-200 dark:border-slate-800">{{ $attr['name'] }}</dt>
                                            <dd class="px-5 py-3 text-xs sm:text-sm text-stone-600 dark:text-slate-300">{{ $attr['values'] }}</dd>
                                        </div>
                                    @endforeach
                                    @foreach($product->specifications ?? [] as $specification)
                                        @if(!empty($specification['label']) || !empty($specification['value']))
                                            <div class="grid grid-cols-[minmax(130px,1fr)_2fr] border-b border-stone-200 dark:border-slate-800 last:border-0">
                                                <dt class="bg-slate-50 dark:bg-slate-800/80 px-5 py-3 text-xs sm:text-sm font-bold text-stone-700 dark:text-slate-200 border-r border-stone-200 dark:border-slate-800">{{ $specification['label'] ?? '' }}</dt>
                                                <dd class="px-5 py-3 text-xs sm:text-sm text-stone-600 dark:text-slate-300">{{ $specification['value'] ?? '' }}</dd>
                                            </div>
                                        @endif
                                    @endforeach
                                </dl>
                            </div>
                        @else
                            <div class="prose max-w-none text-stone-600 dark:prose-invert">
                                {!! \App\Support\HtmlSanitizer::sanitize($detailTabContent[$tabKey]) !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif
    <!-- Reviews and Feedback Section -->
    <div class="kba-product-reviews bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-3xl p-6 sm:p-10 shadow-sm mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left: Ratings Stats -->
            <div class="space-y-6 text-left">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Customer Reviews</h3>
                
                <div class="flex items-center space-x-4">
                    <span class="text-5xl font-extrabold text-gray-800 dark:text-white">{{ $product->average_rating }}</span>
                    <div>
                        <div class="flex text-yellow-400">
                            @for ($i = 0; $i < $fullStars; $i++)
                                <i class="bi bi-star-fill text-base"></i>
                            @endfor
                            @if ($hasHalfStar)
                                <i class="bi bi-star-half text-base"></i>
                            @endif
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="bi bi-star text-base"></i>
                            @endfor
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Based on {{ $product->approvedReviews->count() }} ratings</p>
                    </div>
                </div>

                <!-- Rating bars -->
                <div class="space-y-2">
                    @foreach($ratingDistribution as $star => $percentage)
                        <div class="flex items-center text-sm">
                            <span class="w-12 text-gray-600 dark:text-slate-400 font-medium">{{ $star }} Star</span>
                            <div class="flex-1 mx-3 bg-gray-100 dark:bg-slate-800 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="w-8 text-right text-gray-400 text-xs">{{ $percentage }}%</span>
                        </div>
                    @endforeach
                </div>

                <!-- Write a Review Form -->
                <div class="pt-6 border-t border-gray-100 dark:border-slate-800/80">
                    <h4 class="font-bold text-gray-800 dark:text-white text-sm mb-4">Share your thoughts</h4>
                    @auth
                        <form wire:submit.prevent="submitReview" class="space-y-4" data-kba-recaptcha-form>
                        <input type="hidden" id="recaptchaToken" wire:model="recaptchaToken">
                        @error('recaptchaToken') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Rating</label>
                                <select wire:model="rating" class="w-full py-2 px-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-xs focus:border-[#0C1E8C] focus:ring-[#0C1E8C] dark:text-white">
                                    <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; (5 Stars)</option>
                                    <option value="4">&#9733;&#9733;&#9733;&#9733;&#9734; (4 Stars)</option>
                                    <option value="3">&#9733;&#9733;&#9733;&#9734;&#9734; (3 Stars)</option>
                                    <option value="2">&#9733;&#9733;&#9734;&#9734;&#9734; (2 Stars)</option>
                                    <option value="1">&#9733;&#9734;&#9734;&#9734;&#9734; (1 Star)</option>
                                </select>
                                @error('rating') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Comment</label>
                                <textarea wire:model="comment" rows="3" placeholder="Write your review here..." class="w-full py-2 px-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-xs focus:border-[#0C1E8C] focus:ring-[#0C1E8C] dark:text-white"></textarea>
                                @error('comment') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 bg-[#0C1E8C] text-white rounded-xl text-xs font-semibold hover:bg-amber-700 shadow-md shadow-amber-500/10 transition hover-lift">
                                Submit Review
                            </button>
                        </form>
                    @else
                        <div class="p-4 bg-gray-50 dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700 rounded-2xl text-center">
                            <p class="text-xs text-gray-400">Please login to write a product review.</p>
                            <a wire:navigate href="{{ route('login') }}" class="inline-block mt-3 text-xs font-semibold text-[#0C1E8C] hover:text-amber-500">Log In &rarr;</a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Right: Approved reviews list -->
            <div class="lg:col-span-2 space-y-6 text-left">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-slate-800/80 pb-3">
                    Reviews ({{ $product->approvedReviews->count() }})
                </h3>

                @if ($product->approvedReviews->isEmpty())
                    <div class="py-12 text-center text-gray-400">
                        <i class="bi bi-chat-left-dots text-4xl mb-3 block text-gray-300"></i>
                        <p class="text-sm">No reviews yet for this product. Be the first to leave one!</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-slate-800/60 max-h-[500px] overflow-y-auto pr-3 space-y-4">
                        @foreach ($product->approvedReviews as $rev)
                            <div class="pt-4 first:pt-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-[#0C1E8C] dark:text-amber-400 font-bold text-xs">
                                            {{ strtoupper(substr($rev->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="block text-xs font-bold text-gray-800 dark:text-white">{{ $rev->user->name }}</span>
                                            <span class="block text-[10px] text-gray-400">{{ $rev->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="flex text-yellow-400 text-xs">
                                        @for ($i = 0; $i < $rev->rating; $i++)
                                            <i class="bi bi-star-fill"></i>
                                        @endfor
                                        @for ($i = 0; $i < (5 - $rev->rating); $i++)
                                            <i class="bi bi-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if ($rev->comment)
                                    <p class="mt-2 text-xs text-gray-600 dark:text-slate-300 leading-relaxed bg-gray-50 dark:bg-slate-800/30 p-3 rounded-xl border border-gray-100/50 dark:border-slate-800/40">
                                        {{ $rev->comment }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
