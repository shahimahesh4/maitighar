<div class="min-h-[70vh] bg-slate-50 py-8 sm:py-11">
    <div class="mx-auto w-[calc(100%-32px)] max-w-[1150px]">
        <nav class="mb-5 flex items-center gap-2 text-[11px] text-slate-500" aria-label="Breadcrumb">
            <a wire:navigate href="{{ route('index') }}" class="inline-flex items-center gap-1.5 font-semibold transition hover:text-[#EC0205]"><i class="bi bi-house-door"></i>Home</a><i class="bi bi-chevron-right text-[9px] text-slate-300"></i>
            <a wire:navigate href="{{ route('my-orders') }}" class="font-semibold transition hover:text-[#EC0205]">My orders</a><i class="bi bi-chevron-right text-[9px] text-slate-300"></i>
            <span class="font-bold text-slate-800">Order #{{ $order->id }}</span>
        </nav>

        <header class="relative overflow-hidden rounded-[28px] border border-blue-900/30 bg-gradient-to-r from-[#091b38] via-[#0C1E8C] to-[#071355] px-6 py-8 text-white shadow-[0_20px_55px_rgba(12,30,140,.18)] sm:px-9">
            <span class="pointer-events-none absolute -right-14 -top-28 h-72 w-72 rounded-full border border-white/10"></span><span class="pointer-events-none absolute -bottom-36 right-40 h-72 w-72 rounded-full border border-white/10"></span>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-[10px] font-extrabold uppercase tracking-[.22em] text-red-400">Order details</p><h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Order #{{ $order->id }}</h1><p class="mt-2 text-xs leading-5 text-white/65">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p></div>
                <div class="flex flex-wrap gap-2"><span class="rounded-full border border-white/15 bg-white/10 px-3.5 py-2 text-[10px] font-extrabold uppercase tracking-wide backdrop-blur-sm">{{ $order->status }}</span><span class="rounded-full border border-white/20 bg-white/10 px-3.5 py-2 text-[10px] font-extrabold uppercase tracking-wide text-white">{{ $order->payment_status }}</span></div>
            </div>
        </header>

        @if($order->status === 'cancelled')
            <div class="mt-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700" role="alert"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-red-100"><i class="bi bi-x-circle"></i></span><div><h2 class="text-xs font-extrabold">Order cancelled</h2><p class="mt-1 text-[11px] leading-4 text-red-600">This order has been cancelled and cannot be processed further.</p></div></div>
        @else
            <section class="mt-5 rounded-[26px] border border-slate-200 bg-white px-5 py-6 shadow-sm sm:px-7" aria-labelledby="tracking-title">
                <div class="mb-6 flex items-center justify-between gap-4"><div><p class="text-[9px] font-extrabold uppercase tracking-[.18em] text-[#0C1E8C]">Live progress</p><h2 id="tracking-title" class="mt-1 text-base font-extrabold text-slate-900">Order tracking</h2></div><span class="hidden rounded-full bg-blue-50 px-3 py-1.5 text-[9px] font-bold uppercase tracking-wide text-[#0C1E8C] sm:inline">Current: {{ $order->status }}</span></div>
                <div class="relative grid gap-5 sm:grid-cols-4 sm:gap-2">
                    <div class="absolute bottom-5 left-4 top-5 w-px bg-slate-200 sm:bottom-auto sm:left-[12.5%] sm:right-[12.5%] sm:top-5 sm:h-px sm:w-auto"></div>
                    @foreach($trackingSteps as $step)
                        <div class="relative z-10 flex items-center gap-3 sm:flex-col sm:gap-2 sm:text-center">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full border-2 text-xs shadow-sm {{ $step['circle_class'] }}">@if($step['completed'])<i class="bi bi-check-lg"></i>@else<i class="bi {{ $step['icon'] }}"></i>@endif</span>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $step['label_class'] }}">{{ $step['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Order information">
            @foreach([
                ['bi-person', 'Customer', $order->address?->full_name ?? 'Customer'],
                ['bi-calendar3', 'Order date', $order->created_at->format('d M Y')],
                ['bi-box-seam', 'Order status', Str::headline($order->status)],
                ['bi-wallet2', 'Payment', Str::headline($order->payment_status)],
            ] as [$icon, $label, $value])
                <article class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-blue-50 text-sm text-[#0C1E8C]"><i class="bi {{ $icon }}"></i></span><div class="min-w-0"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p><p class="mt-1 truncate text-xs font-extrabold text-slate-800">{{ $value }}</p></div></article>
            @endforeach
        </section>

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <main class="space-y-5">
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-[0_10px_34px_rgba(70,46,22,.06)]">
                    <header class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6"><div><p class="text-[9px] font-extrabold uppercase tracking-[.18em] text-[#0C1E8C]">Items ordered</p><h2 class="mt-1 text-base font-extrabold text-slate-900">Products</h2></div><span class="rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-[#0C1E8C]">{{ $order->items->sum('quantity') }} {{ Str::plural('item', $order->items->sum('quantity')) }}</span></header>
                    <div class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            <article wire:key="order-item-{{ $item->id }}" class="flex items-center gap-4 px-4 py-4 sm:px-6">
                                <span class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-1.5"><img src="{{ $item->product?->getImageUrl() ?? asset('images/logo.png') }}" alt="{{ $item->product?->name ?? 'Product' }}" class="h-full w-full object-contain"></span>
                                <div class="min-w-0 flex-1"><h3 class="line-clamp-2 text-xs font-extrabold leading-5 text-slate-900">{{ $item->product?->name ?? 'Product unavailable' }}</h3><p class="mt-1 text-[10px] text-slate-400">{{ \App\Support\Money::format($item->unit_amount) }} × {{ $item->quantity }}</p></div>
                                <strong class="text-right text-xs font-extrabold text-[#EC0205]">{{ \App\Support\Money::format($item->total_amount) }}</strong>
                            </article>
                        @endforeach
                    </div>
                </section>

                @if($order->address)
                    <section class="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_10px_34px_rgba(70,46,22,.05)] sm:p-6">
                        <div class="flex items-start gap-4"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-[#0C1E8C]"><i class="bi bi-geo-alt"></i></span><div><p class="text-[9px] font-extrabold uppercase tracking-[.18em] text-[#0C1E8C]">Shipping address</p><h2 class="mt-1 text-sm font-extrabold text-slate-900">{{ $order->address->full_name }}</h2><p class="mt-2 text-xs leading-5 text-slate-500">{{ $order->address->street_address }}, {{ $order->address->city }}@if($order->address->state), {{ $order->address->state }}@endif @if($order->address->zip_code)– {{ $order->address->zip_code }}@endif</p><p class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-600"><i class="bi bi-telephone text-[#0C1E8C]"></i>{{ $order->address->phone }}</p></div></div>
                    </section>
                @endif
            </main>

            <aside class="space-y-4 lg:sticky lg:top-[104px]">
                <section class="overflow-hidden rounded-[26px] border border-slate-200 bg-white shadow-sm">
                    <div class="bg-gradient-to-br from-[#091b38] to-[#0C1E8C] px-6 py-5 text-white"><p class="text-[9px] font-extrabold uppercase tracking-[.18em] text-red-400">Payment overview</p><h2 class="mt-1 text-lg font-extrabold">Price summary</h2></div>
                    <div class="p-6">
                        <dl class="space-y-3.5 text-xs">
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Subtotal</dt><dd class="font-bold text-slate-900">{{ \App\Support\Money::format($order->grand_total - $order->shipping_amount + $order->discount_amount) }}</dd></div>
                            @if((float) $order->discount_amount > 0)<div class="flex justify-between gap-4"><dt class="text-slate-500">Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</dt><dd class="font-bold text-emerald-600">−{{ \App\Support\Money::format($order->discount_amount) }}</dd></div>@endif
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Shipping</dt><dd class="font-bold text-emerald-600">{{ \App\Support\Money::format($order->shipping_amount) }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-slate-500">Payment method</dt><dd class="text-right font-bold text-slate-900">{{ Str::headline($order->payment_method) }}</dd></div>
                        </dl>
                        <div class="my-5 border-t border-dashed border-slate-200"></div>
                        <div class="flex items-end justify-between gap-4"><span class="text-sm font-extrabold text-slate-900">Total</span><strong class="text-xl font-extrabold text-[#EC0205]">{{ \App\Support\Money::format($order->grand_total) }}</strong></div>
                    </div>
                </section>
                <a wire:navigate href="{{ route('my-orders') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-xs font-bold text-slate-700 shadow-sm transition hover:border-[#0C1E8C] hover:text-[#0C1E8C] hover:bg-slate-50"><i class="bi bi-arrow-left"></i>Back to my orders</a>
                <a wire:navigate href="{{ route('contact') }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#0C1E8C] hover:bg-[#091b38] px-5 text-xs font-bold text-white transition"><i class="bi bi-headset"></i>Need order help?</a>
            </aside>
        </div>
    </div>
</div>
