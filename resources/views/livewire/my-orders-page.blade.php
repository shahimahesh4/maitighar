<main class="bg-[#f5f6f8]">
<div class="mx-auto w-[calc(100%-32px)] max-w-[1320px] py-4 sm:py-6 lg:py-8">
    <nav class="mb-5 flex items-center gap-2 text-xs font-medium text-stone-500 sm:mb-7" aria-label="Breadcrumb">
        <a wire:navigate href="{{ route('index') }}" class="inline-flex items-center gap-1.5 transition hover:text-[#EC0205]"><i class="bi bi-house-door" aria-hidden="true"></i> Home</a>
        <i class="bi bi-chevron-right text-[9px] text-stone-400" aria-hidden="true"></i>
        <span class="font-semibold text-stone-800" aria-current="page">My Orders</span>
    </nav>

    <section class="relative mb-6 overflow-hidden rounded-[28px] border border-slate-200 bg-[#eef1f5] px-5 py-7 text-slate-900 shadow-[0_16px_40px_rgba(15,23,42,.07)] sm:px-8 sm:py-9 lg:px-10">
        <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full border border-slate-300/70"></div>
        <div class="pointer-events-none absolute -bottom-28 right-20 h-56 w-56 rounded-full border border-slate-300/70"></div>
        <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.22em] text-red-400"><i class="bi bi-bag-check" aria-hidden="true"></i> Your purchase history</p>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">My Orders</h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-600 sm:text-base">Review your purchases, follow delivery progress and open any order for complete details.</p>
            </div>
            <a wire:navigate href="{{ route('all-products') }}" class="inline-flex w-fit items-center justify-center gap-2 rounded-full bg-[#EC0205] px-5 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-[#C90204] focus:outline-none focus:ring-2 focus:ring-white/70">Continue shopping <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </div>
    </section>

    <section class="mb-7 grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Order summary">
        @foreach ([
            ['label' => 'Total orders', 'value' => $summary['total'], 'icon' => 'bi-box-seam', 'color' => 'bg-blue-50 text-[#0C1E8C]'],
            ['label' => 'In progress', 'value' => $summary['active'], 'icon' => 'bi-truck', 'color' => 'bg-blue-50 text-blue-700'],
            ['label' => 'Delivered', 'value' => $summary['delivered'], 'icon' => 'bi-check2-circle', 'color' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Total spent', 'value' => \App\Support\Money::format($summary['spent']), 'icon' => 'bi-wallet2', 'color' => 'bg-rose-50 text-rose-700'],
        ] as $stat)
            <article class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-[0_8px_24px_rgba(55,38,20,.05)] sm:p-4">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $stat['color'] }} sm:h-11 sm:w-11"><i class="bi {{ $stat['icon'] }} text-lg" aria-hidden="true"></i></span>
                <span class="min-w-0"><strong class="block truncate text-base font-bold text-slate-900 sm:text-lg">{{ $stat['value'] }}</strong><span class="block truncate text-[10px] font-bold uppercase tracking-wider text-stone-400 sm:text-[11px]">{{ $stat['label'] }}</span></span>
            </article>
        @endforeach
    </section>

    @if ($orders->isEmpty())
        <section class="rounded-[28px] border border-slate-200 bg-white px-5 py-14 text-center shadow-[0_14px_40px_rgba(55,38,20,.06)] sm:py-20">
            <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-red-50 text-[#EC0205]"><i class="bi bi-bag-heart text-3xl" aria-hidden="true"></i></span>
            <h2 class="mt-6 text-xl font-bold text-slate-900 sm:text-2xl">Your order story starts here</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">You have not placed an order yet. Explore our handcrafted collection and find something made for you.</p>
            <a wire:navigate href="{{ route('all-products') }}" class="mt-7 inline-flex items-center gap-2 rounded-full bg-[#EC0205] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#C90204] focus:outline-none focus:ring-2 focus:ring-red-500/40">Explore products <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </section>
    @else
        <section aria-labelledby="order-list-title">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div><p class="text-[10px] font-bold uppercase tracking-[.2em] text-[#0C1E8C]">Purchase history</p><h2 id="order-list-title" class="mt-1 text-xl font-bold text-slate-900 sm:text-2xl">Recent orders</h2></div>
                <p class="text-xs font-medium text-stone-500">{{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}</p>
            </div>

            <div class="space-y-4">
                @foreach ($orders as $order)
                    <article wire:key="order-card-{{ $order->id }}" class="group overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-[0_10px_30px_rgba(55,38,20,.05)] transition duration-300 hover:-translate-y-0.5 hover:border-[#d9c29f] hover:shadow-[0_16px_38px_rgba(55,38,20,.09)]">
                        <div class="grid gap-5 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-[1.2fr_.85fr_.85fr_.9fr_auto] lg:items-center">
                            <div class="flex min-w-0 items-center gap-3.5">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-blue-50 text-[#0C1E8C]"><i class="bi bi-receipt-cutoff text-xl" aria-hidden="true"></i></span>
                                <div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Order number</p><h3 class="mt-0.5 text-lg font-bold text-slate-900">#{{ $order->id }}</h3><p class="mt-0.5 text-xs text-stone-500">Placed {{ $order->created_at->format('d M, Y') }}</p></div>
                            </div>
                            <div><p class="mb-1.5 text-[10px] font-bold uppercase tracking-wider text-stone-400">Order status</p><span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $orderPresentation::statusClass($order->status) }} capitalize"><span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>{{ $order->status }}</span></div>
                            <div><p class="mb-1.5 text-[10px] font-bold uppercase tracking-wider text-stone-400">Payment</p><span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $orderPresentation::paymentStatusClass($order->payment_status) }} capitalize"><i class="bi bi-credit-card" aria-hidden="true"></i>{{ $order->payment_status }}</span></div>
                            <div><p class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Order total</p><p class="mt-1 text-lg font-bold text-[#EC0205]">{{ \App\Support\Money::format($order->grand_total) }}</p></div>
                            <a wire:navigate href="{{ route('my-order-details', $order) }}" aria-label="View details for order {{ $order->id }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-full border border-slate-300 px-5 text-sm font-bold text-slate-800 transition hover:border-[#0C1E8C] hover:bg-[#0C1E8C] hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 sm:col-span-2 lg:col-span-1 lg:justify-self-end">View details <i class="bi bi-arrow-right transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($orders->hasPages())
                <div class="mt-7 rounded-2xl border border-slate-200 bg-white px-4 py-3">{{ $orders->links() }}</div>
            @endif
        </section>
    @endif
</div>
</main>
