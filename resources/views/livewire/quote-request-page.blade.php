<main class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-rose-50/30 pb-9 pt-6 sm:-mt-14 sm:pb-14 sm:pt-5">
    <div class="mx-auto w-full max-w-5xl px-4 sm:px-6">
        <a wire:navigate href="{{ route('product-details', $product->slug) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#0C1E8C] transition hover:-translate-x-0.5 hover:text-[#EC0205]"><i class="bi bi-arrow-left"></i> Back to product</a>

        <section class="mt-5 overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_22px_65px_rgba(12,30,140,.10)]">
            <header class="relative overflow-hidden border-b border-rose-100 bg-gradient-to-br from-[#071b38] via-[#0C1E8C] to-[#c80611] px-6 py-8 text-white sm:px-10 sm:py-10">
                <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full border border-white/15 bg-white/5" aria-hidden="true"></div>
                <div class="relative grid gap-7 md:grid-cols-[1fr_auto] md:items-end">
                    <div>
                        <p class="flex items-center gap-2 text-xs font-bold tracking-[.12em] text-rose-200"><i class="bi bi-chat-quote-fill"></i> Product enquiry</p>
                        <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Request a quote</h1>
                        <p class="mt-3 max-w-xl text-sm leading-6 text-white/80">Share your details and our team will prepare a personalised quote for you.</p>
                    </div>
                    <div class="flex items-center gap-4 rounded-2xl border border-white/15 bg-white/10 p-3 backdrop-blur-sm md:max-w-lg">
                        <img src="{{ $product->getThumbnailUrl() }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-xl bg-white object-cover p-1 shadow-sm">
                        <div class="min-w-0"><p class="text-xs font-semibold tracking-wide text-rose-200">Selected product</p><p class="mt-1 font-bold leading-snug text-white">{{ $product->name }}</p><p class="mt-1 text-xs text-white/70">{{ $product->sku ?: 'SKU available on request' }}</p></div>
                    </div>
                </div>
            </header>

            <div class="p-6 sm:p-10">
                @if(session('quote_success'))
                    <div class="mb-7 flex gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-900"><i class="bi bi-check-circle-fill mt-0.5 text-emerald-600"></i><span>{{ session('quote_success') }}</span></div>
                @endif

                <form wire:submit="submit" class="space-y-8">
                    <fieldset>
                        <legend class="text-lg font-extrabold text-slate-900">Your personal details</legend>
                        <p class="mt-1 text-sm leading-6 text-slate-500">We use these details only to prepare and send your quote.</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <label class="block text-sm font-bold text-slate-700">Full name<span class="relative mt-2 block"><i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-[#0C1E8C]"></i><input wire:model="full_name" autocomplete="name" class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-slate-900 shadow-sm outline-none transition hover:border-slate-300 placeholder:text-slate-400 focus:border-[#EC0205] focus:bg-white focus:ring-4 focus:ring-red-100" placeholder="Your full name" required></span>@error('full_name')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                            <label class="block text-sm font-bold text-slate-700">Email<span class="relative mt-2 block"><i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-[#0C1E8C]"></i><input wire:model="email" type="email" autocomplete="email" class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-slate-900 shadow-sm outline-none transition hover:border-slate-300 placeholder:text-slate-400 focus:border-[#EC0205] focus:bg-white focus:ring-4 focus:ring-red-100" placeholder="you@example.com" required></span>@error('email')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                            <label class="block text-sm font-bold text-slate-700">Phone<span class="relative mt-2 block"><i class="bi bi-telephone absolute left-4 top-1/2 -translate-y-1/2 text-[#0C1E8C]"></i><input wire:model="phone" type="tel" autocomplete="tel" class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-slate-900 shadow-sm outline-none transition hover:border-slate-300 placeholder:text-slate-400 focus:border-[#EC0205] focus:bg-white focus:ring-4 focus:ring-red-100" placeholder="Your phone number" required></span>@error('phone')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                            <label class="block text-sm font-bold text-slate-700">City<span class="relative mt-2 block"><i class="bi bi-geo-alt absolute left-4 top-1/2 -translate-y-1/2 text-[#0C1E8C]"></i><input wire:model="city" autocomplete="address-level2" class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-slate-900 shadow-sm outline-none transition hover:border-slate-300 placeholder:text-slate-400 focus:border-[#EC0205] focus:bg-white focus:ring-4 focus:ring-red-100" placeholder="Your city" required></span>@error('city')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                            <label class="block sm:col-span-2 text-sm font-bold text-slate-700">Address<span class="relative mt-2 block"><i class="bi bi-house-door absolute left-4 top-1/2 -translate-y-1/2 text-[#0C1E8C]"></i><input wire:model="address" autocomplete="street-address" class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-slate-900 shadow-sm outline-none transition hover:border-slate-300 placeholder:text-slate-400 focus:border-[#EC0205] focus:bg-white focus:ring-4 focus:ring-red-100" placeholder="Street address, suburb and postcode" required></span>@error('address')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                        </div>
                    </fieldset>

                    <fieldset class="border-t border-slate-100 pt-7">
                        <legend class="text-lg font-extrabold text-slate-900">Quote requirements</legend>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <label class="block text-sm font-bold text-slate-700">Quantity<span class="relative mt-2 block"><i class="bi bi-box-seam absolute left-4 top-1/2 -translate-y-1/2 text-[#0C1E8C]"></i><input wire:model="quantity" type="number" min="1" max="9999" class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-slate-900 shadow-sm outline-none transition hover:border-slate-300 focus:border-[#EC0205] focus:bg-white focus:ring-4 focus:ring-red-100" required></span>@error('quantity')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                            <p class="rounded-xl bg-rose-50 px-4 py-3 text-sm leading-6 text-slate-600"><i class="bi bi-shield-check mr-1.5 text-[#EC0205]"></i>We will confirm availability and pricing before any order is placed.</p>
                            <label class="block sm:col-span-2 text-sm font-bold text-slate-700">Message <span class="font-medium text-slate-400">(optional)</span><span class="relative mt-2 block"><i class="bi bi-chat-left-text absolute left-4 top-4 text-[#0C1E8C]"></i><textarea wire:model="message" rows="4" class="block w-full resize-y rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-slate-900 shadow-sm outline-none transition hover:border-slate-300 placeholder:text-slate-400 focus:border-[#EC0205] focus:bg-white focus:ring-4 focus:ring-red-100" placeholder="Tell us about quantity, customisation, or delivery requirements."></textarea></span>@error('message')<span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>@enderror</label>
                        </div>
                    </fieldset>

                    <button class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#C90204] to-[#EC0205] px-5 py-4 text-sm font-extrabold text-white shadow-lg shadow-red-600/25 transition hover:-translate-y-0.5 hover:from-[#A80203] hover:to-[#C90204] disabled:cursor-not-allowed disabled:opacity-70" type="submit" wire:loading.attr="disabled" wire:target="submit"><i class="bi bi-send"></i><span wire:loading.remove wire:target="submit">Send quote request</span><span wire:loading wire:target="submit">Sending request…</span></button>

                    <div class="grid gap-3 border-t border-slate-100 pt-7 sm:grid-cols-3">
                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-4"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-red-100 text-[#C90204]"><i class="bi bi-send-check"></i></span><p class="text-xs leading-5 text-slate-500"><strong class="block text-sm text-slate-800">Send your enquiry</strong>Tell us what you need.</p></div>
                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-4"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-blue-100 text-[#0C1E8C]"><i class="bi bi-search-heart"></i></span><p class="text-xs leading-5 text-slate-500"><strong class="block text-sm text-slate-800">We review it</strong>Our team checks availability.</p></div>
                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-4"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-700"><i class="bi bi-envelope-check"></i></span><p class="text-xs leading-5 text-slate-500"><strong class="block text-sm text-slate-800">Receive your quote</strong>We will contact you directly.</p></div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</main>
