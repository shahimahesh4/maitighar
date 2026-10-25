<div>
    @if($isOpen && $product)
        <div 
            class="fixed inset-0 z-[110] overflow-y-auto"
            aria-labelledby="quick-view-title" 
            role="dialog" 
            aria-modal="true"
            x-data
            @keydown.escape.window="$wire.closeModal()"
        >
            <!-- Dimmed Backdrop -->
            <div 
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                wire:click="closeModal"
                aria-hidden="true"
            ></div>

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 md:p-6 text-center">
                <div 
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-4xl border border-slate-100 p-5 sm:p-8"
                    @click.stop
                >
                    <!-- Close button -->
                    <button 
                        type="button" 
                        wire:click="closeModal"
                        class="absolute top-4 right-4 sm:top-5 sm:right-5 w-9 h-9 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition focus:outline-none z-10"
                        aria-label="Close Quick View"
                    >
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 items-start">
                        
                        <!-- Left Column: Product Images -->
                        <div class="flex flex-col gap-3">
                            <div class="relative w-full aspect-square rounded-2xl bg-slate-50 border border-slate-100 overflow-hidden flex items-center justify-center p-4">
                                @if($product->on_sale && $product->discount_percentage)
                                    <span class="absolute top-3 left-3 bg-[#EC0205] text-white text-[11px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow-sm z-10">
                                        -{{ $product->discount_percentage }}%
                                    </span>
                                @elseif($product->on_sale)
                                    <span class="absolute top-3 left-3 bg-[#EC0205] text-white text-[11px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow-sm z-10">
                                        Sale
                                    </span>
                                @endif

                                <img 
                                    src="{{ $selectedImage ?: $product->getImageUrl() }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-full object-contain transition-all duration-300"
                                >
                            </div>

                            @php
                                $gallery = $product->getGalleryImageUrls();
                            @endphp
                            @if(count($gallery) > 1)
                                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                                    @foreach($gallery as $imgUrl)
                                        <button 
                                            type="button"
                                            wire:click="selectImage('{{ $imgUrl }}')"
                                            class="w-14 h-14 rounded-xl border p-1 bg-slate-50 overflow-hidden flex-shrink-0 transition {{ ($selectedImage ?: $product->getImageUrl()) === $imgUrl ? 'border-[#EAA036] ring-2 ring-[#EAA036]/30' : 'border-slate-200 hover:border-slate-400' }}"
                                        >
                                            <img src="{{ $imgUrl }}" alt="Thumbnail" class="w-full h-full object-contain">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Right Column: Product Details -->
                        <div class="flex flex-col">
                            <!-- Category & SKU Badges -->
                            <div class="flex items-center justify-between gap-2 mb-2">
                                @if($product->category)
                                    <span class="inline-flex items-center text-[11px] font-bold uppercase tracking-wider text-[#EAA036] bg-amber-50 px-2.5 py-1 rounded-md">
                                        {{ $product->category->name }}
                                    </span>
                                @endif

                                @if($product->sku)
                                    <span class="text-[11px] font-mono font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md">
                                        SKU: {{ $product->sku }}
                                    </span>
                                @endif
                            </div>

                            <!-- Product Title -->
                            <h2 id="quick-view-title" class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">
                                {{ $product->name }}
                            </h2>

                            <!-- Star Rating -->
                            <div class="flex items-center gap-2 mt-2 mb-3">
                                <div class="flex items-center gap-0.5 text-amber-400 text-sm">
                                    @for($star = 1; $star <= 5; $star++)
                                        <i class="bi {{ $star <= round($product->average_rating ?? 5) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-xs text-slate-400">({{ $product->reviews_count ?? 5 }} reviews)</span>
                                <span class="text-xs font-semibold {{ $product->in_stock ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-2 py-0.5 rounded-full ml-auto">
                                    {{ $product->in_stock ? 'In Stock (' . $product->stock_quantity . ' available)' : 'Out of Stock' }}
                                </span>
                            </div>

                            <!-- Price Display -->
                            <div class="flex items-baseline gap-3 pb-4 mb-4 border-b border-slate-100">
                                <span class="text-2xl sm:text-3xl font-extrabold text-[#0C1E8C]">
                                    ${{ number_format($product->sale_price, 2) }}
                                </span>
                                @if($product->regular_price && $product->regular_price > $product->sale_price)
                                    <del class="text-sm sm:text-base text-slate-400 font-semibold">
                                        ${{ number_format($product->regular_price, 2) }}
                                    </del>
                                    <span class="text-xs font-bold text-[#EC0205] bg-red-50 px-2 py-0.5 rounded-md">
                                        Save ${{ number_format($product->regular_price - $product->sale_price, 2) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Short Description -->
                            @if($product->short_description)
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4 line-clamp-3">
                                    {{ $product->short_description }}
                                </p>
                            @endif

                            <!-- Key Specifications (Quick List) -->
                            @if(!empty($product->specifications) && is_array($product->specifications))
                                <div class="mb-5 space-y-1.5 bg-slate-50/70 p-3 rounded-xl border border-slate-100">
                                    @foreach(array_slice($product->specifications, 0, 3) as $spec)
                                        @if(is_array($spec) && isset($spec['label'], $spec['value']))
                                            <div class="text-xs flex items-center justify-between text-slate-600">
                                                <span class="font-semibold text-slate-500">{{ $spec['label'] }}:</span>
                                                <span class="font-bold text-slate-800">{{ $spec['value'] }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <!-- Quantity & Action Buttons -->
                            <div class="flex items-center gap-3 mt-auto pt-2">
                                <!-- Quantity Counter -->
                                <div class="inline-flex items-center border border-slate-200 rounded-xl bg-white p-1 shadow-sm">
                                    <button 
                                        type="button" 
                                        wire:click="decrementQuantity" 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-100 transition font-bold disabled:opacity-40"
                                        {{ $quantity <= 1 ? 'disabled' : '' }}
                                        aria-label="Decrease quantity"
                                    >
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <span class="w-10 text-center font-bold text-sm text-slate-800">
                                        {{ $quantity }}
                                    </span>
                                    <button 
                                        type="button" 
                                        wire:click="incrementQuantity" 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-100 transition font-bold"
                                        aria-label="Increase quantity"
                                    >
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>

                                <!-- Add to Bag Button -->
                                <button 
                                    type="button"
                                    wire:click="addToCart"
                                    class="flex-1 h-11 px-5 rounded-xl bg-[#0C1E8C] hover:bg-[#09176b] text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-sm transition active:scale-[0.98]"
                                >
                                    <i class="bi bi-bag-plus text-base"></i>
                                    <span>Add to Bag</span>
                                </button>

                                <!-- Wishlist Button -->
                                <button 
                                    type="button"
                                    wire:click="toggleWishlist"
                                    class="w-11 h-11 rounded-xl border border-slate-200 hover:border-red-200 hover:bg-red-50 text-slate-600 hover:text-red-600 flex items-center justify-center shadow-sm transition"
                                    title="Add to Wishlist"
                                    aria-label="Toggle Wishlist"
                                >
                                    <i class="bi {{ $isWishlisted ? 'bi-heart-fill text-red-500' : 'bi-heart' }} text-lg"></i>
                                </button>
                            </div>

                            <!-- Full Product Details Link -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <a 
                                    wire:navigate 
                                    href="{{ route('product-details', $product->slug) }}" 
                                    class="font-bold text-[#EAA036] hover:text-[#d9821a] flex items-center gap-1 group"
                                >
                                    <span>View Full Product Details & Specs</span>
                                    <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </a>

                                <span class="text-slate-400">
                                    <i class="bi bi-shield-check text-emerald-500 mr-1"></i>Authentic Nepali Craft
                                </span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
