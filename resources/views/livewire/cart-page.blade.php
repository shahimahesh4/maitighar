<div class="min-h-[70vh] bg-slate-50 py-7 sm:py-10">
    <div class="kba-cart-container mx-auto w-full max-w-[1150px] px-4 sm:px-6">
        <nav class="mb-5 flex items-center gap-2 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a wire:navigate href="{{ route('index') }}" class="inline-flex items-center gap-1.5 font-semibold transition hover:text-[#EC0205]"><i class="bi bi-house-door"></i>Home</a>
            <i class="bi bi-chevron-right text-[9px] text-slate-300"></i><span class="font-bold text-slate-800">Shopping bag</span>
        </nav>

        <header class="relative mb-7 overflow-hidden rounded-[26px] border border-blue-900/30 bg-gradient-to-r from-[#091b38] via-[#0C1E8C] to-[#071355] px-5 py-7 text-white shadow-[0_18px_45px_rgba(12,30,140,.18)] sm:px-8 sm:py-8">
            <span class="pointer-events-none absolute -right-10 -top-24 h-60 w-60 rounded-full border border-white/10"></span>
            <span class="pointer-events-none absolute -bottom-28 right-32 h-52 w-52 rounded-full border border-white/10"></span>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="mb-2 text-[10px] font-extrabold uppercase tracking-[.22em] text-red-400">Your selection</p><h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Shopping bag</h1><p class="mt-2 max-w-xl text-xs leading-5 text-white/75">Review your products, adjust quantities, and continue to secure checkout.</p></div>
                @if(!empty($cart_items))<span class="inline-flex w-fit items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-bold backdrop-blur-sm"><i class="bi bi-bag-check-fill text-white"></i>{{ $cartQuantity }} {{ Str::plural('item', $cartQuantity) }}</span>@endif
            </div>
        </header>

        @if(empty($cart_items))
            <section class="relative mx-auto max-w-3xl overflow-hidden rounded-[30px] border border-slate-200 bg-white px-6 py-14 text-center shadow-[0_16px_45px_rgba(12,30,140,.06)] sm:px-12 sm:py-16">
                <span class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-red-50"></span>
                <span class="relative mx-auto grid h-20 w-20 place-items-center rounded-[24px] border border-red-100 bg-red-50 text-3xl text-[#EC0205] shadow-sm"><i class="bi bi-bag-heart"></i></span>
                <h2 class="relative mt-6 text-xl font-extrabold text-slate-900">Your bag is ready for something beautiful</h2>
                <p class="relative mx-auto mt-3 max-w-md text-xs leading-5 text-slate-500">Explore authentic products selected by Maitighar and save your favourites here.</p>
                <a wire:navigate href="{{ route('all-products') }}" class="relative mt-7 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#EC0205] to-[#C90204] px-6 py-3 text-xs font-extrabold text-white shadow-lg shadow-red-600/25 transition hover:-translate-y-0.5 hover:from-[#C90204] hover:to-[#A80203] hover:shadow-xl">Explore products <i class="bi bi-arrow-right"></i></a>
            </section>
        @else
            <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_330px]">
                <section aria-labelledby="cart-products-title" class="min-w-0">
                    <div class="mb-4 flex items-end justify-between gap-4 px-1">
                        <div><h2 id="cart-products-title" class="text-lg font-extrabold text-slate-900">Your products</h2><p class="mt-1 text-[11px] text-slate-500">Quantities and totals update automatically.</p></div>
                        <a wire:navigate href="{{ route('all-products') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0C1E8C] transition hover:text-[#EC0205]"><i class="bi bi-plus-lg"></i><span class="hidden sm:inline">Add products</span></a>
                    </div>

                    <div class="space-y-3">
                        @foreach($cartItems as $item)
                            <article wire:key="cart-item-{{ $item['product_id'] }}" class="group relative overflow-hidden rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-[#0C1E8C]/30 hover:shadow-md sm:p-5">
                                <div class="flex gap-4 sm:gap-5">
                                    <div class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-[19px] border border-slate-200 bg-slate-50 p-2 sm:h-28 sm:w-28"><img src="{{ $item['image_url'] }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}'" alt="{{ $item['name'] }}" class="h-full w-full object-contain transition duration-300 group-hover:scale-105"></div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0"><p class="text-[9px] font-extrabold uppercase tracking-[.16em] text-[#0C1E8C]">Authentic Heritage</p><h3 class="mt-1 line-clamp-2 text-sm font-extrabold leading-snug text-slate-900 sm:text-base">{{ $item['name'] }}</h3><p class="mt-1.5 text-[11px] font-semibold text-slate-400">{{ \App\Support\Money::format($item['unit_amount']) }} per item</p></div>
                                            <button type="button" wire:click="removeItem({{ $item['product_id'] }})" wire:loading.attr="disabled" wire:target="removeItem({{ $item['product_id'] }})" class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-transparent text-slate-400 transition hover:border-red-100 hover:bg-red-50 hover:text-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-400 disabled:opacity-50" aria-label="Remove {{ $item['name'] }} from bag" title="Remove product"><i class="bi bi-trash3"></i></button>
                                        </div>
                                        <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-3 sm:flex-row sm:items-end sm:justify-between">
                                            <div><span class="mb-1.5 block text-[9px] font-bold uppercase tracking-wider text-slate-400">Quantity</span><div class="inline-flex h-9 items-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-inner">
                                                <button type="button" wire:click="decrementQty({{ $item['product_id'] }})" wire:loading.attr="disabled" wire:target="decrementQty({{ $item['product_id'] }}),incrementQty({{ $item['product_id'] }})" class="grid h-full w-9 place-items-center text-slate-500 transition hover:bg-slate-200 hover:text-[#0C1E8C] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#0C1E8C]" aria-label="Decrease {{ $item['name'] }} quantity"><i class="bi bi-dash-lg"></i></button>
                                                <span class="min-w-9 border-x border-slate-200 text-center text-xs font-extrabold text-slate-900" aria-live="polite">{{ $item['quantity'] }}</span>
                                                <button type="button" wire:click="incrementQty({{ $item['product_id'] }})" wire:loading.attr="disabled" wire:target="decrementQty({{ $item['product_id'] }}),incrementQty({{ $item['product_id'] }})" class="grid h-full w-9 place-items-center text-slate-500 transition hover:bg-slate-200 hover:text-[#0C1E8C] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#0C1E8C]" aria-label="Increase {{ $item['name'] }} quantity"><i class="bi bi-plus-lg"></i></button>
                                            </div></div>
                                            <div class="text-left sm:text-right"><span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400">Item total</span><strong class="mt-1 block text-base font-extrabold text-[#EC0205]">{{ \App\Support\Money::format($item['total_amount']) }}</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <a wire:navigate href="{{ route('all-products') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[11px] font-bold text-slate-700 shadow-sm transition hover:border-[#0C1E8C] hover:text-[#0C1E8C] hover:bg-slate-50"><i class="bi bi-arrow-left"></i>Continue shopping</a>
                </section>

                <aside class="space-y-4 lg:sticky lg:top-[104px]">
                    <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                        <div class="relative overflow-hidden bg-gradient-to-br from-[#091b38] via-[#0C1E8C] to-[#071355] px-6 py-6 text-white"><span class="absolute -right-8 -top-12 h-32 w-32 rounded-full border border-white/10"></span><p class="text-[9px] font-extrabold uppercase tracking-[.2em] text-red-400">Order overview</p><div class="mt-2 flex items-end justify-between gap-3"><h2 class="text-lg font-extrabold">Summary</h2><span class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-bold">{{ $cartQuantity }} {{ Str::plural('item', $cartQuantity) }}</span></div></div>
                        <div class="p-6">
                            <dl class="space-y-3.5 text-xs"><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Products subtotal</dt><dd class="font-bold text-slate-900">{{ \App\Support\Money::format($grand_total) }}</dd></div><div class="flex items-center justify-between gap-4"><dt class="text-slate-500">Delivery</dt><dd class="font-bold text-slate-600">Calculated at checkout</dd></div></dl>
                            <div class="my-5 border-t border-dashed border-slate-200"></div>
                            <div class="flex items-end justify-between gap-4"><span class="text-sm font-extrabold text-slate-900">Total</span><strong class="text-xl font-extrabold text-[#EC0205]">{{ \App\Support\Money::format($grand_total) }}</strong></div>
                            <p class="mt-2 text-right text-[10px] leading-4 text-slate-400">Taxes included where applicable</p>
                            <a wire:navigate href="{{ route('checkout') }}" class="mt-6 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#EC0205] to-[#C90204] px-5 text-xs font-extrabold text-white shadow-lg shadow-red-600/25 transition hover:-translate-y-0.5 hover:from-[#C90204] hover:to-[#A80203] hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#EC0205]">Proceed to checkout <i class="bi bi-arrow-right"></i></a>
                            <p class="mt-3 flex items-center justify-center gap-1.5 text-[9px] font-semibold text-slate-400"><i class="bi bi-lock-fill text-emerald-600"></i>Secure checkout and protected information</p>
                        </div>
                    </section>
                    <div class="grid grid-cols-3 divide-x divide-slate-200 rounded-2xl border border-slate-200 bg-white px-2 py-4 text-center shadow-sm">
                        @foreach([['bi-shield-check','Secure'],['bi-truck','Delivery'],['bi-headset','Support']] as [$icon, $label])<div class="px-1"><i class="bi {{ $icon }} text-base text-[#0C1E8C]"></i><span class="mt-1 block text-[8px] font-extrabold uppercase tracking-wide text-slate-400">{{ $label }}</span></div>@endforeach
                    </div>
                </aside>
            </div>
        @endif
    </div>
</div>
