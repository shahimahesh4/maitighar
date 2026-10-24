<div class="w-full max-w-[1320px] py-6 px-4 sm:px-6 sm:py-8 lg:px-8 mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-xs text-slate-500 font-medium" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a wire:navigate href="/" class="inline-flex items-center text-slate-600 hover:text-[#EA0202] transition-colors">
                    <i class="bi bi-house-door-fill mr-1.5 text-sm"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right mx-1 text-slate-400 text-[10px]"></i>
                    <span class="text-slate-800 font-bold">Categories</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="text-center max-w-2xl mx-auto mb-14 space-y-3">
        <span class="text-xs text-[#EA0202] font-bold uppercase tracking-widest">Handcrafted Collections</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Explore Nepali Handicrafts & Heritage
        </h1>
        <div class="w-16 h-1 bg-[#EA0202] mx-auto rounded-full"></div>
        <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
            Discover our curated collections of authentic Nepalese handicrafts, traditional handmade heritage treasures, bamboo artistry, and cultural crafts crafted with love.
        </p>
    </div>

    <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($categories as $category)
            <a wire:navigate class="group flex flex-col bg-white border border-slate-200/90 shadow-sm rounded-2xl hover:shadow-xl hover:border-[#EA0202] transition-all hover:-translate-y-1 duration-200" href="/products?selected_categories[0]={{ $category->id }}" wire:key="cat-card-{{ $category->id }}">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-red-50 rounded-xl flex items-center justify-center group-hover:bg-[#EA0202] text-[#EA0202] group-hover:text-white transition duration-300 shadow-sm">
                                @if($category->image)
                                    <img class="h-10 w-10 object-contain rounded-lg" src="{{ $category->getImageUrl() }}" alt="{{ $category->name }}">
                                @else
                                    <i class="bi bi-box-seam text-2xl transition duration-300"></i>
                                @endif
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-800 group-hover:text-[#EA0202] transition text-base line-clamp-1">
                                    {{ $category->name }}
                                </h2>
                                <span class="text-xs text-slate-400 group-hover:text-slate-600 transition">View products →</span>
                            </div>
                        </div>
                        <div class="text-slate-300 group-hover:text-[#EA0202] group-hover:translate-x-1 transition-all">
                            <i class="bi bi-chevron-right text-base"></i>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
