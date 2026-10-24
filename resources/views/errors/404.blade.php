<x-layouts.app title="Page Not Found | Maitighar" meta-description="The requested page could not be found. Explore Maitighar authentic Nepali handicrafts, cultural products, and contact information." robots="noindex, follow">
    <section class="mx-auto grid min-h-[65vh] w-[calc(100%-32px)] max-w-[1320px] place-items-center py-16 text-center">
        <div class="max-w-xl rounded-[32px] border border-slate-200 bg-white px-7 py-12 shadow-[0_24px_70px_rgba(12,30,140,.08)] sm:px-12">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-red-50 text-2xl text-[#EC0205]"><i class="bi bi-compass" aria-hidden="true"></i></span>
            <p class="mt-6 text-xs font-extrabold uppercase tracking-[.22em] text-[#0C1E8C]">Error 404</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">This page has moved or no longer exists.</h1>
            <p class="mx-auto mt-4 max-w-md text-sm leading-7 text-slate-600">Return home or continue exploring our authentic Nepali handicrafts, heritage collections, and cultural gifts.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a wire:navigate href="{{ route('index') }}" class="rounded-full bg-[#EC0205] hover:bg-[#C90204] px-6 py-3 text-sm font-bold text-white transition-colors">Return home</a>
                <a wire:navigate href="{{ route('all-products') }}" class="rounded-full border border-slate-300 hover:border-[#0C1E8C] px-6 py-3 text-sm font-bold text-[#0C1E8C] hover:bg-slate-50 transition-colors">Explore products</a>
            </div>
        </div>
    </section>
</x-layouts.app>
