<div class="min-h-[70vh] bg-slate-50 py-8 sm:py-11">
    <div class="mx-auto w-[calc(100%-32px)] max-w-[1150px]">
        <nav class="mb-5 flex items-center gap-2 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a wire:navigate href="{{ route('index') }}" class="inline-flex items-center gap-1.5 font-semibold transition hover:text-[#EC0205]"><i class="bi bi-house-door"></i>Home</a>
            <i class="bi bi-chevron-right text-[9px] text-slate-300"></i>
            <span class="font-bold text-slate-800">Order confirmed</span>
        </nav>

        <section class="relative overflow-hidden rounded-[30px] border border-blue-900/30 bg-gradient-to-r from-[#091b38] via-[#0C1E8C] to-[#071355] px-6 py-9 text-white shadow-[0_20px_55px_rgba(12,30,140,.18)] sm:px-9 sm:py-10">
            <span class="pointer-events-none absolute -right-14 -top-28 h-72 w-72 rounded-full border border-white/10"></span>
            <span class="pointer-events-none absolute -bottom-36 right-36 h-72 w-72 rounded-full border border-white/10"></span>
            <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-4 sm:items-center">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl border border-white/20 bg-[#EC0205] text-2xl text-white shadow-lg"><i class="bi bi-check2"></i></span>
                    <div><p class="text-[10px] font-extrabold uppercase tracking-[.22em] text-red-400">Thank you for your order</p><h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Order successfully placed</h1><p class="mt-2 max-w-xl text-xs leading-5 text-white/65">We have received your order and will keep you updated as it moves through our process.</p></div>
                </div>
                <div class="w-fit rounded-2xl border border-white/15 bg-white/10 px-5 py-3 backdrop-blur-sm"><span class="block text-[9px] font-bold uppercase tracking-wider text-white/55">Order reference</span><strong class="mt-1 block text-lg">#{{ $order->id }}</strong></div>
            </div>
        </section>

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_330px]">
            <main class="space-y-5">
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_10px_34px_rgba(70,46,22,.06)]">
                    <header class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6"><div><p class="text-[9px] font-extrabold uppercase tracking-[.18em] text-[#0C1E8C]">Your purchase</p><h2 class="mt-1 text-base font-extrabold text-slate-900">Order items</h2></div><span class="rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-[#0C1E8C]">{{ $order->items->sum('quantity') }} {{ Str::plural('item', $order->items->sum('quantity')) }}</span></header>
                    <div class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            <article class="flex items-center gap-4 px-5 py-4 sm:px-6">
                                <span class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-1.5"><img src="{{ $item->product?->getImageUrl() ?? asset('images/logo.png') }}" alt="{{ $item->product?->name ?? 'Product' }}" class="h-full w-full object-contain"></span>
                                <div class="min-w-0 flex-1"><h3 class="line-clamp-2 text-xs font-extrabold leading-5 text-slate-900">{{ $item->product?->name ?? 'Product unavailable' }}</h3><p class="mt-1 text-[10px] text-slate-400">{{ $item->quantity }} × {{ \App\Support\Money::format($item->unit_amount) }}</p></div>
                                <strong class="text-xs font-extrabold text-[#EC0205]">{{ \App\Support\Money::format($item->total_amount) }}</strong>
                            </article>
                        @endforeach
                    </div>
                </section>

                @if($order->address)
                    <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_10px_34px_rgba(70,46,22,.05)] sm:p-6">
                        <div class="flex items-start gap-4"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-[#0C1E8C]"><i class="bi bi-geo-alt"></i></span><div><p class="text-[9px] font-extrabold uppercase tracking-[.18em] text-[#0C1E8C]">Delivery details</p><h2 class="mt-1 text-sm font-extrabold text-slate-900">{{ $order->address->full_name }}</h2><p class="mt-2 text-xs leading-5 text-slate-500">{{ $order->address->street_address }}, {{ $order->address->city }}@if($order->address->state), {{ $order->address->state }}@endif @if($order->address->zip_code)– {{ $order->address->zip_code }}@endif</p><p class="mt-1 text-xs font-semibold text-slate-600">{{ $order->address->phone }}</p></div></div>
                    </section>
                @endif
            </main>

            <aside class="space-y-4 lg:sticky lg:top-[104px]">
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_15px_42px_rgba(70,46,22,.1)]">
                    <div class="bg-gradient-to-br from-[#091b38] to-[#0C1E8C] px-6 py-5 text-white"><p class="text-[9px] font-extrabold uppercase tracking-[.18em] text-red-400">Order summary</p><h2 class="mt-1 text-lg font-extrabold">Payment details</h2></div>
                    <div class="p-6">
                        <dl class="space-y-3.5 text-xs">
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Payment method</dt><dd class="text-right font-bold text-slate-900">{{ Str::headline($order->payment_method) }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Payment status</dt><dd class="rounded-full bg-blue-50 px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-wide text-[#0C1E8C]">{{ $order->payment_status }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Order status</dt><dd class="font-bold capitalize text-slate-900">{{ $order->status }}</dd></div>
                            @if((float) $order->discount_amount > 0)<div class="flex justify-between gap-4"><dt class="text-slate-500">Discount</dt><dd class="font-bold text-emerald-600">−{{ \App\Support\Money::format($order->discount_amount) }}</dd></div>@endif
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Delivery</dt><dd class="font-bold text-emerald-600">{{ \App\Support\Money::format($order->shipping_amount) }}</dd></div>
                        </dl>
                        <div class="my-5 border-t border-dashed border-slate-200"></div>
                        <div class="flex items-end justify-between gap-4"><span class="text-sm font-extrabold text-slate-900">Total paid</span><strong class="text-xl font-extrabold text-[#EC0205]">{{ \App\Support\Money::format($order->grand_total) }}</strong></div>
                    </div>
                </section>

                <div class="grid gap-2">
                    <a wire:navigate href="{{ route('my-order-details', $order) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#EC0205] to-[#C90204] px-5 text-xs font-extrabold text-white shadow-lg shadow-red-600/25 transition hover:-translate-y-0.5 hover:from-[#C90204] hover:to-[#A80203]">Track this order <i class="bi bi-arrow-right"></i></a>
                    <a wire:navigate href="{{ route('all-products') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-xs font-bold text-slate-700 transition hover:border-[#0C1E8C] hover:text-[#0C1E8C] hover:bg-slate-50">Continue shopping</a>
                </div>
                <p class="flex items-start gap-2 rounded-2xl border border-slate-200 bg-white p-4 text-[10px] leading-4 text-slate-500"><i class="bi bi-info-circle mt-0.5 text-[#0C1E8C]"></i><span>Need help with this order? Contact our academy support team and mention order #{{ $order->id }}.</span></p>
            </aside>
        </div>
    </div>
</div>
