<div>
    @if($isOpen && $product)
        <div class="fixed inset-0 z-[150]" role="dialog" aria-modal="true" aria-labelledby="cart-drawer-title" x-data @keydown.escape.window="$wire.close()">
            <button type="button" wire:click="close" class="absolute inset-0 w-full cursor-default bg-slate-950/55 backdrop-blur-[2px]" aria-label="Close cart drawer"></button>

            <aside class="absolute inset-y-0 right-0 flex w-full max-w-[430px] flex-col overflow-hidden bg-white shadow-2xl" @click.stop>
                <header class="flex min-h-[76px] items-center justify-between bg-[#EB0303] px-5 text-white shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-white/15 text-xl"><i class="bi bi-bag-check" aria-hidden="true"></i></span>
                        <div><p class="text-[10px] font-bold uppercase tracking-[.16em] text-white/70">Maitighar</p><h2 id="cart-drawer-title" class="text-lg font-extrabold">Your Cart</h2></div>
                    </div>
                    <button type="button" wire:click="close" class="grid h-10 w-10 place-items-center rounded-full text-xl transition hover:bg-white/15" aria-label="Close cart drawer"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                </header>

                <div class="border-b border-rose-100 bg-rose-50 px-5 py-3 text-sm font-semibold text-[#A20008]"><i class="bi bi-check-circle-fill mr-1.5" aria-hidden="true"></i> Added to your cart</div>

                <div class="flex-1 overflow-y-auto p-5">
                    <article class="flex gap-4 border-b border-slate-100 pb-5">
                        <div class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 p-2"><img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-full w-full object-contain"></div>
                        <div class="min-w-0 flex-1">
                            <h3 class="line-clamp-2 text-[15px] font-bold leading-snug text-slate-900">{{ $product['name'] }}</h3>
                            @if(!empty($product['options_text']))<p class="mt-1 text-xs text-slate-500">{{ $product['options_text'] }}</p>@endif
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <span class="rounded-full border border-rose-100 bg-rose-50 px-3 py-1 text-xs font-bold text-[#A20008]">Qty: {{ $quantity }}</span>
                                <div class="text-right">@if(!empty($product['regular_price']))<del class="mr-1 text-xs text-slate-400">{{ \App\Support\Money::format($product['regular_price']) }}</del>@endif<strong class="text-base text-[#EB0303]">{{ \App\Support\Money::format($product['sale_price']) }}</strong></div>
                            </div>
                        </div>
                    </article>
                </div>

                <footer class="border-t border-slate-200 bg-white p-5 shadow-[0_-8px_28px_rgba(15,23,42,.06)]">
                    <div class="mb-4 flex items-center justify-between gap-4"><span class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Subtotal</span><strong class="text-xl font-extrabold text-[#EB0303]">{{ \App\Support\Money::format($subtotal) }}</strong></div>
                    <p class="mb-4 text-xs text-slate-500">{{ $total_items }} {{ \Illuminate\Support\Str::plural('item', $total_items) }} in your cart. Shipping is calculated at checkout.</p>
                    <div class="grid gap-2">
                        <a wire:navigate href="{{ route('cart-products') }}" class="flex min-h-12 items-center justify-center rounded-xl border border-[#EB0303] bg-white px-4 text-sm font-extrabold text-[#A20008] transition hover:bg-rose-50">View Cart</a>
                        <a wire:navigate href="{{ route('checkout') }}" class="flex min-h-12 items-center justify-center rounded-xl bg-[#EB0303] px-4 text-sm font-extrabold text-white shadow-[0_10px_22px_rgba(235,3,3,.25)] transition hover:bg-[#C90204]">Checkout <i class="bi bi-arrow-right ml-2" aria-hidden="true"></i></a>
                        <button type="button" wire:click="close" class="py-1 text-xs font-semibold text-slate-500 transition hover:text-[#EB0303]">Continue shopping</button>
                    </div>
                </footer>
            </aside>
        </div>
    @endif
</div>
