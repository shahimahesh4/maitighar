<div class="min-h-screen bg-slate-50 py-4 sm:py-6">
    <div class="mx-auto w-[calc(100%-32px)] max-w-[1320px]">
        <div class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#091b38] via-[#0C1E8C] to-[#071355] px-6 py-8 text-white shadow-xl shadow-slate-900/10 sm:px-9 sm:py-10">
            <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full border border-white/10"></div>
            <div class="absolute -bottom-32 right-20 h-72 w-72 rounded-full border border-white/10"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl border border-white/20 bg-white/10 text-xl font-bold backdrop-blur-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-[.22em] text-red-400">{{ $user->role === 'student' ? 'Student portal' : 'Customer Account' }}</p>
                        <h1 class="text-2xl font-extrabold sm:text-3xl">Welcome back, {{ Str::before($user->name, ' ') }}</h1>
                        <p class="mt-1 text-sm text-white/70">Here’s everything happening with your account.</p>
                    </div>
                </div>
                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold backdrop-blur-sm">
                    <i class="bi bi-patch-check-fill text-red-400"></i>
                    {{ $roleLabel }} account
                </span>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[310px_minmax(0,1fr)]">
            <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-3 shadow-sm lg:sticky lg:top-6">
                <nav aria-label="Dashboard navigation" class="grid grid-cols-2 gap-1 sm:grid-cols-4 lg:grid-cols-1">
                    <a href="#overview" class="flex items-center gap-3 rounded-2xl bg-blue-50 px-4 py-3 text-xs font-bold text-[#0C1E8C]"><i class="bi bi-grid-1x2-fill"></i><span>Overview</span></a>
                    <a wire:navigate href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-[#0C1E8C]"><i class="bi bi-person-gear"></i><span>Edit profile</span></a>
                    <a wire:navigate href="{{ route('my-orders') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-[#0C1E8C]"><i class="bi bi-box-seam"></i><span>My orders</span></a>
                    <a wire:navigate href="{{ route('wishlist') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-[#0C1E8C]"><i class="bi bi-heart"></i><span>Wishlist</span></a>
                    <a wire:navigate href="{{ route('support-tickets') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-[#0C1E8C]"><i class="bi bi-headset"></i><span>Support tickets</span></a>
                    @if($user->role === 'student')
                        <a href="#student" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-[#0C1E8C]"><i class="bi bi-mortarboard"></i><span>My learning</span></a>
                    @endif
                </nav>
                <div class="mt-3 space-y-3 border-t border-slate-100 px-2 py-4 text-left text-slate-500">
                    <div class="flex items-start gap-2">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-blue-50 text-xs text-[#0C1E8C]"><i class="bi bi-envelope"></i></span>
                        <div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email</p><p class="break-words text-[13px] font-semibold leading-5 text-slate-700">{{ $user->email }}</p></div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-blue-50 text-xs text-[#0C1E8C]"><i class="bi bi-telephone"></i></span>
                        <div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Phone</p><p class="mt-0.5 text-[13px] font-semibold leading-5 text-slate-700">{{ $user->phone ?: 'Phone not added' }}</p></div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-blue-50 text-xs text-[#0C1E8C]"><i class="bi bi-geo-alt"></i></span>
                        <div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Address</p><p class="mt-0.5 break-words text-[13px] font-semibold leading-5 text-slate-700">{{ $user->address ?: 'Address not added' }}</p></div>
                    </div>
                </div>
            </aside>

            <main id="overview" class="min-w-0 space-y-6">
                <section aria-label="Account summary" class="grid grid-cols-2 gap-3 xl:grid-cols-4">
                    @foreach([
                        ['Orders', $stats['orders'], 'bi-bag-check', 'bg-blue-50 text-[#0C1E8C]'],
                        ['Active', $stats['active_orders'], 'bi-truck', 'bg-blue-50 text-blue-700'],
                        ['Wishlist', $stats['wishlist'], 'bi-heart', 'bg-rose-50 text-rose-700'],
                        ['Total spent', \App\Support\Money::format($stats['spent']), 'bi-wallet2', 'bg-emerald-50 text-emerald-700'],
                    ] as [$label, $value, $icon, $tone])
                        <article class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                            <span class="grid h-10 w-10 place-items-center rounded-xl {{ $tone }}"><i class="bi {{ $icon }}"></i></span>
                            <p class="mt-4 truncate text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $value }}</p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $label }}</p>
                        </article>
                    @endforeach
                </section>

                @if($user->role === 'student')
                    <section id="student" class="grid gap-5 md:grid-cols-2">
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="text-xs font-bold uppercase tracking-[.18em] text-[#0C1E8C]">Learning support</p>
                            <h2 class="mt-2 text-xl font-extrabold text-slate-900">Assigned trainer</h2>
                            @if($trainer)
                                <div class="mt-6 flex items-center gap-4">
                                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-[#0C1E8C] text-lg font-bold text-white">{{ strtoupper(substr($trainer->name, 0, 1)) }}</span>
                                    <div class="min-w-0"><p class="font-extrabold text-slate-900">{{ $trainer->name }}</p><p class="mt-1 break-all text-sm leading-6 text-slate-500">{{ $trainer->email }}</p></div>
                                </div>
                                <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600"><i class="bi bi-info-circle mr-2 text-[#0C1E8C]"></i>Contact the academy office to coordinate your class schedule.</div>
                            @else
                                <div class="mt-5 rounded-2xl bg-slate-50 p-5 text-sm leading-6 text-slate-500">A trainer has not been assigned yet. The academy team will update this dashboard after assignment.</div>
                            @endif
                        </article>
                    </section>
                @endif

                <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6">
                        <div><h2 class="text-lg font-extrabold text-slate-900">Recent orders</h2><p class="mt-1 text-sm text-slate-500">Your latest purchases and their progress.</p></div>
                        <a wire:navigate href="{{ route('my-orders') }}" class="text-sm font-bold text-[#0C1E8C] hover:underline">View all</a>
                    </div>
                    @forelse($orders as $order)
                        <a wire:navigate href="{{ route('my-order-details', $order) }}" class="flex items-center gap-4 border-b border-slate-100 px-5 py-4 transition last:border-0 hover:bg-slate-50 sm:px-6">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-red-50 text-[#EC0205]"><i class="bi bi-bag"></i></span>
                            <div class="min-w-0 flex-1"><p class="font-bold text-slate-900">Order #{{ $order->id }}</p><p class="mt-1 text-xs text-slate-500">{{ $order->created_at->format('d M Y') }} · {{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}</p></div>
                            <div class="hidden text-right sm:block"><p class="font-extrabold text-slate-900">{{ \App\Support\Money::format($order->grand_total) }}</p><p class="mt-1 text-xs font-semibold capitalize text-[#0C1E8C]">{{ $order->status }}</p></div>
                            <i class="bi bi-chevron-right text-slate-300"></i>
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center"><i class="bi bi-bag text-3xl text-[#0C1E8C]"></i><h3 class="mt-3 font-bold text-slate-800">No orders yet</h3><p class="mt-1 text-sm text-slate-500">Discover our authentic Nepali collection.</p><a wire:navigate href="{{ route('all-products') }}" class="mt-5 inline-flex rounded-xl bg-[#EC0205] hover:bg-[#C90204] px-5 py-2.5 text-sm font-bold text-white">Start shopping</a></div>
                    @endforelse
                </section>

                <section>
                    <article class="rounded-3xl bg-[#091b38] p-6 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/10 text-[#EC0205]"><i class="bi bi-headset"></i></span>
                            <h2 class="mt-4 text-xl font-extrabold">Need help?</h2>
                            <p class="mt-2 text-sm leading-6 text-white/65">Our team can help with your orders, questions, or account support.</p>
                        </div>
                        <a wire:navigate href="{{ route('support-tickets') }}" class="inline-flex items-center gap-2 rounded-xl bg-white hover:bg-red-50 px-5 py-3 text-sm font-bold text-[#0C1E8C] self-start md:self-center shrink-0">Open support ticket <i class="bi bi-arrow-right"></i></a>
                    </article>
                </section>
            </main>
        </div>
    </div>
</div>
