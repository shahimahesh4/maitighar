<main class="mx-auto w-[calc(100%-32px)] max-w-[1150px] py-8 sm:py-10 lg:py-12">
    <nav class="mb-5 flex items-center gap-2 text-xs font-medium text-stone-500 sm:mb-6" aria-label="Breadcrumb">
        <a wire:navigate href="{{ route('index') }}" class="inline-flex items-center gap-1.5 transition hover:text-[#EC0205]"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a>
        <i class="bi bi-chevron-right text-[9px] text-stone-400" aria-hidden="true"></i>
        <span class="font-semibold text-stone-800" aria-current="page">Wishlist</span>
    </nav>

    <header class="relative mb-7 overflow-hidden rounded-[28px] bg-gradient-to-br from-[#091b38] via-[#0C1E8C] to-[#071355] px-5 py-7 text-white shadow-[0_20px_50px_rgba(12,30,140,.18)] sm:px-8 sm:py-9 lg:px-10">
        <span class="pointer-events-none absolute -right-16 -top-28 h-72 w-72 rounded-full border border-white/10"></span>
        <span class="pointer-events-none absolute -bottom-36 right-24 h-64 w-64 rounded-full border border-white/10"></span>
        <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 flex items-center gap-2 text-[10px] font-extrabold uppercase tracking-[.22em] text-red-400"><i class="bi bi-heart" aria-hidden="true"></i> Saved especially for you</p>
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">My Wishlist</h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-white/65">Keep your favourite handcrafted items together and move them to your shopping bag whenever you are ready.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2.5 text-xs font-bold backdrop-blur-sm"><i class="bi bi-heart-fill text-red-400" aria-hidden="true"></i>{{ $wishlist_items->count() }} {{ \Illuminate\Support\Str::plural('item', $wishlist_items->count()) }}</span>
                <a wire:navigate href="{{ route('all-products') }}" class="inline-flex items-center gap-2 rounded-full bg-[#EC0205] px-4 py-2.5 text-xs font-bold text-white transition hover:-translate-y-0.5 hover:bg-[#C90204]">Shop more <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </header>

    @if ($wishlist_items->isEmpty())
        <section class="rounded-[28px] border border-slate-200 bg-white px-5 py-14 text-center shadow-[0_14px_40px_rgba(12,30,140,.06)] sm:py-20">
            <span class="relative mx-auto grid h-20 w-20 place-items-center rounded-full bg-red-50 text-[#EC0205]">
                <i class="bi bi-heart text-3xl" aria-hidden="true"></i>
                <i class="bi bi-gem absolute -right-1 top-0 text-base text-[#0C1E8C]" aria-hidden="true"></i>
            </span>
            <h2 class="mt-6 text-xl font-bold text-slate-900 sm:text-2xl">Save the products you love</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-500">Your wishlist is waiting for its first favourite. Browse our collection and tap the heart on anything you would like to keep.</p>
            <a wire:navigate href="{{ route('all-products') }}" class="mt-7 inline-flex items-center gap-2 rounded-full bg-[#EC0205] px-6 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-[#C90204] focus:outline-none focus:ring-4 focus:ring-red-100">Explore products <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </section>
    @else
        <section aria-labelledby="wishlist-products-title">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[.2em] text-[#0C1E8C]">Your collection</p>
                    <h2 id="wishlist-products-title" class="mt-1 text-xl font-bold text-slate-900 sm:text-2xl">Saved products</h2>
                </div>
                <p class="hidden text-xs text-stone-500 sm:block">Add an item to your bag when you are ready to order.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @foreach ($wishlist_items as $item)
                    @if ($item->product)
                        <article wire:key="wishlist-item-{{ $item->id }}" class="group flex min-w-0 flex-col overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1.5 hover:border-[#0C1E8C]/30 hover:shadow-md">
                            <div class="relative aspect-square overflow-hidden rounded-t-[19px] bg-gradient-to-br from-slate-50 to-slate-100">
                                <a wire:navigate href="{{ route('product-details', $item->product->slug) }}" class="block h-full w-full p-3 sm:p-5">
                                    <img src="{{ $item->product->getThumbnailUrl() }}" alt="{{ $item->product->name }}" width="360" height="360" loading="lazy" decoding="async" class="h-full w-full object-contain mix-blend-multiply transition duration-500 group-hover:scale-105">
                                </a>
                                <button type="button" wire:click="removeItem({{ $item->id }})" wire:loading.attr="disabled" wire:target="removeItem({{ $item->id }})" class="absolute right-2.5 top-2.5 grid h-9 w-9 place-items-center rounded-full border border-white/80 bg-white/90 text-[#EC0205] shadow-md backdrop-blur transition hover:scale-105 hover:bg-[#EC0205] hover:text-white disabled:opacity-60 sm:right-3.5 sm:top-3.5" aria-label="Remove {{ $item->product->name }} from wishlist" title="Remove from wishlist">
                                    <i wire:loading.remove wire:target="removeItem({{ $item->id }})" class="bi bi-heart-fill text-sm" aria-hidden="true"></i>
                                    <i wire:loading wire:target="removeItem({{ $item->id }})" class="bi bi-arrow-repeat kba-spin text-sm" aria-hidden="true"></i>
                                </button>
                                @if ($item->product->in_stock)
                                    <span class="absolute bottom-2.5 left-2.5 rounded-full bg-white/90 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-emerald-700 shadow-sm backdrop-blur sm:bottom-3.5 sm:left-3.5"><i class="bi bi-check-circle-fill mr-1" aria-hidden="true"></i>In stock</span>
                                @else
                                    <span class="absolute bottom-2.5 left-2.5 rounded-full bg-white/90 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-red-600 shadow-sm backdrop-blur sm:bottom-3.5 sm:left-3.5">Out of stock</span>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col p-3 sm:p-4">
                                <span class="text-[9px] font-bold uppercase tracking-[.14em] text-[#0C1E8C]">{{ $item->product->brand->name ?? 'Handcrafted' }}</span>
                                <a wire:navigate href="{{ route('product-details', $item->product->slug) }}" class="mt-1.5 line-clamp-2 min-h-10 text-xs font-bold leading-5 text-[#352c24] transition hover:text-[#EC0205] sm:text-sm">{{ $item->product->name }}</a>
                                <div class="mt-auto pt-3">
                                    <p class="mb-3 text-sm font-extrabold text-[#EC0205] sm:text-base">{{ \App\Support\Money::format($item->product->sale_price) }}</p>
                                    <button type="button" wire:click="addToCart({{ $item->product->id }})" wire:loading.attr="disabled" wire:target="addToCart({{ $item->product->id }})" @disabled(! $item->product->in_stock) class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-[#0C1E8C] px-3 text-[10px] font-bold text-white transition hover:bg-[#091b38] disabled:cursor-not-allowed disabled:bg-stone-200 disabled:text-stone-400 sm:text-xs">
                                        <span wire:loading.remove wire:target="addToCart({{ $item->product->id }})"><i class="bi bi-bag-plus mr-1" aria-hidden="true"></i>{{ $item->product->in_stock ? 'Move to bag' : 'Unavailable' }}</span>
                                        <span wire:loading wire:target="addToCart({{ $item->product->id }})"><i class="bi bi-arrow-repeat kba-spin mr-1" aria-hidden="true"></i>Adding…</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
</main>
