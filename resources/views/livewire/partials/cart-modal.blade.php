<div>
    @if($isOpen && $product)
        <div 
            class="fixed inset-0 z-[100] overflow-y-auto"
            aria-labelledby="cart-modal-title" 
            role="dialog" 
            aria-modal="true"
            x-data
            @keydown.escape.window="$wire.close()"
        >
            <!-- Dimmed Backdrop -->
            <div 
                class="fixed inset-0 bg-black/55 backdrop-blur-sm transition-opacity" 
                wire:click="close"
                aria-hidden="true"
            ></div>

            <!-- Modal Dialog Container -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
                <div 
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-xl sm:max-w-2xl p-6 sm:p-7 border border-gray-100"
                    @click.stop
                >
                    <!-- Close button (top right) -->
                    <button 
                        type="button" 
                        wire:click="close"
                        class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                        aria-label="Close"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Top Green Banner Header -->
                    <div class="flex items-center justify-center gap-2 mb-6 pt-0.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <h3 id="cart-modal-title" class="text-emerald-600 font-semibold text-[15px] sm:text-base tracking-normal">
                            Successfully added to your cart.
                        </h3>
                    </div>

                    <!-- Main Content Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        
                        <!-- Left Column: Product Info (7 cols on desktop for ample breathing room) -->
                        <div class="md:col-span-7 flex items-center gap-4">
                            <!-- Product Image Thumbnail -->
                            <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-xl border border-gray-100 bg-gray-50/40 p-1.5 flex items-center justify-center overflow-hidden shadow-sm">
                                <img 
                                    src="{{ $product['image'] }}" 
                                    alt="{{ $product['name'] }}" 
                                    class="max-w-full max-h-full object-contain"
                                >
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm sm:text-[15px] font-medium text-gray-900 leading-snug line-clamp-2 mb-1.5">
                                    {{ $product['name'] }}
                                </h4>

                                <!-- Prices (Compact, crisp font, no line breaking between Rs. and amount) -->
                                <div class="flex items-center gap-2.5 mb-2 flex-wrap">
                                    @if(!empty($product['regular_price']))
                                        <span class="whitespace-nowrap text-xs text-gray-400 line-through">
                                            Rs.&nbsp;{{ number_format($product['regular_price'], 2) }}
                                        </span>
                                    @endif
                                    <span class="whitespace-nowrap font-semibold text-[#FF5B4D] text-sm sm:text-[15px]">
                                        Rs.&nbsp;{{ number_format($product['sale_price'], 2) }}
                                    </span>
                                </div>

                                <div class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                    <span>QTY:</span>
                                    <span class="text-gray-900 font-semibold">{{ $quantity }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Checkout & Cart Totals (5 cols on desktop with subtle divider) -->
                        <div class="md:col-span-5 border-t pt-5 md:border-t-0 md:pt-0 md:border-l md:pl-6 border-gray-100 flex flex-col justify-between">
                            <!-- Checkout Button -->
                            <a 
                                href="{{ route('checkout') }}" 
                                class="w-full block text-center bg-[#FF5B4D] hover:bg-[#e44c3e] active:scale-[0.99] text-white font-semibold text-sm py-2.5 px-4 rounded-lg shadow-sm hover:shadow transition"
                            >
                                Checkout
                            </a>

                            <!-- Order Subtotal (Refined, small price font) -->
                            <div class="text-center my-3.5">
                                <p class="text-xs text-gray-500 font-medium mb-0.5">
                                    Order subtotal
                                </p>
                                <p class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight whitespace-nowrap mb-0.5">
                                    Rs.&nbsp;{{ number_format($subtotal, 2) }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    Your cart contains {{ $total_items }} {{ \Illuminate\Support\Str::plural('item', $total_items) }}
                                </p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                <button 
                                    type="button" 
                                    wire:click="close" 
                                    class="w-full block text-center bg-[#F4F4F6] hover:bg-gray-200 text-gray-700 font-medium text-xs sm:text-sm py-2 px-3 rounded-lg transition"
                                >
                                    Continue Shopping
                                </button>
                                
                                <a 
                                    href="{{ route('cart-products') }}" 
                                    class="w-full block text-center bg-[#F4F4F6] hover:bg-gray-200 text-gray-700 font-medium text-xs sm:text-sm py-2 px-3 rounded-lg transition"
                                >
                                    View Cart
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
