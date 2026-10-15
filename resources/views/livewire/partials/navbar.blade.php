<div class="w-full bg-white font-sans shadow-sm">
    <!-- Top Header Bar (Dark Charcoal matching reference design) -->
    <div class="bg-[#222222] text-white/90 text-xs border-b border-black/20 py-1.5">
        <div class="kba-container flex items-center justify-between">
            <!-- Left: Hotline & Free Delivery -->
            <div class="flex items-center gap-4 text-[11px] sm:text-xs">
                <a href="tel:{{ preg_replace('/[^+\d]/', '', \App\Models\Setting::get('store_phone', '18001323218')) }}" class="flex items-center gap-1.5 text-white/85 hover:text-white transition-colors">
                    <i class="bi bi-telephone text-[11px] text-white/70"></i>
                    <span>Hotline: <strong class="font-semibold text-white">{{ \App\Models\Setting::get('store_phone', '1800 - 132 3218') }}</strong></span>
                </a>
                <span class="text-white/20">|</span>
                <div class="flex items-center gap-1.5 text-white/85">
                    <i class="bi bi-truck text-xs text-white/70"></i>
                    <span>Free Delivery</span>
                </div>
            </div>

            <!-- Right: Currency -->
            <div class="flex items-center text-[11px] sm:text-xs">
                <!-- USD Currency with Orange Accent -->
                <div class="flex items-center gap-1 cursor-pointer hover:opacity-90 transition-opacity">
                    <span class="text-[#FF9800] font-bold">USD</span>
                    <i class="bi bi-chevron-down text-[9px] text-[#FF9800]"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Top Header Row (Highlighted with Logo Color #EA0202) -->
    <header style="background-color: #EA0202;" class="border-b border-[#C90204]">
        <div class="kba-container py-2 sm:py-2.5 lg:py-3 flex items-center justify-between gap-3 sm:gap-6">
            
            <!-- Logo & Brand -->
            <div class="flex items-center flex-shrink-0">
                <a wire:navigate href="{{ route('index') }}" class="flex items-center" aria-label="Maitighar home">
                    <img src="{{ asset('images/logo.png') }}" alt="Maitighar" class="h-14 sm:h-16 md:h-20 lg:h-24 w-auto object-contain">
                </a>
            </div>

            <!-- Central Search Bar with Categories Select and Red Button -->
            <form wire:submit.prevent="submitSearch" class="flex-1 max-w-2xl hidden md:flex items-center rounded-lg overflow-hidden bg-white shadow focus-within:ring-2 focus-within:ring-white/40 transition-all mx-4 lg:mx-8">
                <!-- Category Select Dropdown -->
                <div class="relative border-r border-slate-200 bg-slate-50/80 flex-shrink-0">
                    <select wire:model="selectedCategory" aria-label="Select Category" class="appearance-none bg-transparent pl-3.5 pr-8 py-2.5 text-xs font-semibold text-slate-700 border-none outline-none cursor-pointer focus:ring-0">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                </div>

                <!-- Text Input -->
                <input type="text" wire:model.defer="search" placeholder="Search Item..." class="w-full px-4 py-2 text-sm text-slate-800 placeholder-slate-400 border-none outline-none focus:ring-0">

                <!-- Red Search Button -->
                <button type="submit" aria-label="Search" class="bg-[#EA0202] hover:bg-[#C90204] text-white px-6 py-2.5 flex items-center justify-center transition-colors flex-shrink-0">
                    <i class="bi bi-search text-base"></i>
                </button>
            </form>

            <!-- Right Actions: Wishlist, Your Cart, Account -->
            <div class="flex items-center gap-5 sm:gap-7 flex-shrink-0">
                
                <!-- Wishlist -->
                <a wire:navigate href="{{ route('wishlist') }}" class="flex flex-col items-center justify-center text-white hover:text-white/80 transition-colors group">
                    <div class="relative">
                        <i class="bi bi-heart text-2xl group-hover:scale-105 transition-transform text-white"></i>
                        @if($wishlist_count > 0)
                            <span class="absolute -top-1 -right-2 bg-white text-[#EA0202] text-[9px] font-black rounded-full h-3.5 min-w-[14px] px-1 flex items-center justify-center shadow-sm">{{ $wishlist_count }}</span>
                        @endif
                    </div>
                    <span class="text-[11px] font-semibold text-white/95 mt-0.5 transition-colors">Wishlist</span>
                </a>

                <!-- Your Cart -->
                <a wire:navigate href="{{ route('cart-products') }}" class="flex flex-col items-center justify-center text-white hover:text-white/80 transition-colors group">
                    <div class="relative">
                        <i class="bi bi-cart3 text-2xl group-hover:scale-105 transition-transform text-white"></i>
                        <span class="absolute -top-1.5 -right-2.5 bg-white text-[#EA0202] text-[10px] font-black rounded-full h-4 min-w-[16px] px-1 flex items-center justify-center border-2 border-[#EA0202] shadow-sm">{{ $total_count }}</span>
                    </div>
                    <span class="text-[11px] font-semibold text-white/95 mt-0.5 transition-colors">Your Cart</span>
                </a>

                <!-- Account -->
                <div class="relative group" x-data="{ open: false, timeout: null }"
                     @mouseenter="clearTimeout(timeout); open = true"
                     @mouseleave="timeout = setTimeout(() => { open = false }, 250)"
                     @click.outside="open = false">
                    <button type="button" @click="open = !open" class="flex flex-col items-center justify-center text-white hover:text-white/80 transition-colors group focus:outline-none">
                        <i class="bi bi-person-circle text-2xl group-hover:scale-105 transition-transform text-white"></i>
                        <span class="text-[11px] font-semibold text-white/95 mt-0.5 transition-colors">Account</span>
                    </button>
                    
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 top-full pt-2 w-52 z-50">
                        <div class="rounded-xl bg-white border border-slate-200 shadow-xl py-2">
                            @auth
                                <div class="px-4 py-2 border-b border-slate-100 text-xs">
                                    <p class="text-slate-400 uppercase tracking-wider text-[10px] font-bold">Signed in as</p>
                                    <p class="font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                                </div>
                                <a wire:navigate href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 transition-colors">
                                    <i class="bi bi-grid-1x2 text-[#0C1E8C]"></i> Dashboard
                                </a>
                                <a wire:navigate href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 transition-colors">
                                    <i class="bi bi-person-gear text-slate-500"></i> Edit Profile
                                </a>
                                <a wire:navigate href="{{ route('my-orders') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 transition-colors">
                                    <i class="bi bi-box-seam text-slate-500"></i> My Orders
                                </a>
                                @if(method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                                    <a href="/stnapanel" class="flex items-center gap-2 px-4 py-2 text-xs text-[#0C1E8C] hover:bg-blue-50 font-bold border-t border-slate-100 transition-colors">
                                        <i class="bi bi-shield-lock"></i> Admin Panel
                                    </a>
                                @endif
                                <button type="button" wire:click="logout" class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-red-600 hover:bg-red-50 font-semibold border-t border-slate-100 transition-colors">
                                    <i class="bi bi-box-arrow-right"></i> Log out
                                </button>
                            @else
                                <a wire:navigate href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 transition-colors">
                                    <i class="bi bi-box-arrow-in-right text-[#EA0202]"></i> Log In
                                </a>
                                <a wire:navigate href="{{ route('register') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 transition-colors">
                                    <i class="bi bi-person-plus text-slate-500"></i> Create Account
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Mobile menu toggle button -->
                <button type="button" class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg border border-white/40 text-white hover:bg-white/10" data-kba-menu-open aria-label="Open mobile menu">
                    <i class="bi bi-list text-2xl text-white"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Search Bar (under top row on phones) -->
    <div style="background-color: #EA0202;" class="md:hidden py-2.5 border-b border-[#C90204]">
        <div class="kba-container">
            <form wire:submit.prevent="submitSearch" class="flex items-center rounded-lg overflow-hidden bg-white shadow-sm">
                <input type="text" wire:model.defer="search" placeholder="Search Item..." class="w-full px-3 py-2 text-xs text-slate-800 placeholder-slate-400 border-none outline-none focus:ring-0">
                <button type="submit" aria-label="Search" class="bg-[#EA0202] hover:bg-[#C90204] text-white px-4 py-2 flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-search text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Secondary Navigation Row (Home, Shop, About Us, Gallery, Contact Us) -->
    <div class="hidden md:block border-b border-slate-200 bg-white">
        <div class="kba-container flex items-center justify-between text-sm">
            
            <!-- Left Navigation Menu Links -->
            <nav class="flex items-center gap-7 lg:gap-8">
                <!-- Home Link with Active Red Underline -->
                <a wire:navigate href="{{ route('index') }}" class="py-3 font-bold transition-colors inline-flex items-center {{ request()->routeIs('index') ? 'text-[#EA0202] border-b-2 border-[#EA0202] -mb-[1px]' : 'text-slate-700 hover:text-[#EA0202]' }}">
                    Home
                </a>

                <!-- Shop Dropdown -->
                <div class="relative group" x-data="{ open: false, timeout: null }"
                     @mouseenter="clearTimeout(timeout); open = true"
                     @mouseleave="timeout = setTimeout(() => { open = false }, 250)"
                     @click.outside="open = false">
                    <button type="button" @click="open = !open" class="py-3 font-semibold text-slate-700 hover:text-[#EA0202] inline-flex items-center gap-1.5 transition-colors focus:outline-none {{ request()->routeIs('all-products', 'product-details', 'product-categories') ? 'text-[#EA0202]' : '' }}">
                        <span>Shop</span>
                        <i class="bi bi-chevron-down text-[10px] text-slate-400 group-hover:rotate-180 transition-transform"></i>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 top-full pt-1.5 w-52 z-50">
                        <div class="rounded-xl bg-white border border-slate-200 shadow-xl py-2">
                            <a wire:navigate href="{{ route('all-products') }}" class="block px-4 py-2 text-xs font-bold text-[#EA0202] hover:bg-red-50 border-b border-slate-100 transition-colors">All Products</a>
                            <a wire:navigate href="{{ route('product-categories') }}" class="block px-4 py-2 text-xs text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 font-semibold transition-colors">Categories</a>
                            <a wire:navigate href="{{ route('all-products', ['on_sale' => 1]) }}" class="block px-4 py-2 text-xs text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 font-semibold transition-colors">On Sale Offers</a>
                        </div>
                    </div>
                </div>


                <a wire:navigate href="{{ route('pages.show', 'about-us') }}" class="py-3 font-semibold transition-colors {{ request()->is('about-us') ? 'text-[#EA0202]' : 'text-slate-700 hover:text-[#EA0202]' }}">
                    About Us
                </a>

                <!-- Gallery Dropdown -->
                <div class="relative group" x-data="{ open: false, timeout: null }"
                     @mouseenter="clearTimeout(timeout); open = true"
                     @mouseleave="timeout = setTimeout(() => { open = false }, 250)"
                     @click.outside="open = false">
                    <button type="button" @click="open = !open" class="py-3 font-semibold text-slate-700 hover:text-[#EA0202] inline-flex items-center gap-1.5 transition-colors focus:outline-none {{ request()->routeIs('gallery.*', 'videos.*') ? 'text-[#EA0202]' : '' }}">
                        <span>Gallery</span>
                        <i class="bi bi-chevron-down text-[10px] text-slate-400 group-hover:rotate-180 transition-transform"></i>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 top-full pt-1.5 w-44 z-50">
                        <div class="rounded-xl bg-white border border-slate-200 shadow-xl py-2">
                            <a wire:navigate href="{{ route('gallery.index') }}" class="block px-4 py-2 text-xs text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 font-semibold transition-colors">Photos</a>
                            <a wire:navigate href="{{ route('videos.index') }}" class="block px-4 py-2 text-xs text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 font-semibold transition-colors">Videos</a>
                        </div>
                    </div>
                </div>

                <a wire:navigate href="{{ route('contact') }}" class="py-3 font-semibold transition-colors {{ request()->routeIs('contact') ? 'text-[#EA0202]' : 'text-slate-700 hover:text-[#EA0202]' }}">
                    Contact Us
                </a>
            </nav>

            <!-- Right Utility Links (Find Store, Help Center) -->
            <div class="flex items-center gap-5 text-xs text-slate-600 font-medium">
                <a wire:navigate href="{{ route('contact') }}" class="flex items-center gap-1.5 text-slate-700 hover:text-[#EA0202] transition-colors">
                    <i class="bi bi-geo-alt text-[#EA0202] text-sm"></i>
                    <span>Find Store</span>
                </a>
                <span class="text-slate-300">|</span>
                <a wire:navigate href="{{ route('contact') }}" class="flex items-center gap-1.5 text-slate-700 hover:text-[#EA0202] transition-colors">
                    <i class="bi bi-question-circle text-slate-500 text-sm"></i>
                    <span>Help Center</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Slide-out Menu Backdrop -->
    <div class="kba-mobile-backdrop" data-kba-menu-backdrop aria-hidden="true"></div>

    <!-- Mobile Slide-out Sidebar -->
    <aside class="kba-mobile-sidebar" id="kba-mobile-sidebar" data-kba-mobile-sidebar aria-hidden="true" aria-label="Mobile navigation" inert>
        <div class="kba-mobile-sidebar-head">
            <div class="kba-sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Maitighar" width="130" height="129">
                <span><strong>Maitighar</strong> <small>Shop All In One</small></span>
            </div>
            <button type="button" data-kba-menu-close aria-label="Close navigation menu"><i class="bi bi-x-lg"></i></button>
        </div>

        @auth
            <div class="kba-mobile-user-card">
                <span class="kba-mobile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span><small>Welcome back</small><strong>{{ auth()->user()->name }}</strong></span>
            </div>
        @else
            <div class="kba-mobile-guest-card">
                <span><small>Welcome to Maitighar</small><strong>Sign in to your account</strong></span>
                <a wire:navigate href="{{ route('login') }}" data-kba-menu-link>Sign In</a>
            </div>
        @endauth

        <nav class="kba-mobile-sidebar-nav">
            <span class="kba-mobile-nav-label">Explore</span>
            <a wire:navigate href="{{ route('index') }}" class="{{ request()->routeIs('index') ? 'is-active' : '' }}" data-kba-menu-link><i class="bi bi-house-door"></i><span>Home</span><i class="bi bi-chevron-right"></i></a>
            <a wire:navigate href="{{ route('all-products') }}" class="{{ request()->routeIs('all-products', 'product-details') ? 'is-active' : '' }}" data-kba-menu-link><i class="bi bi-grid"></i><span>Shop All Products</span><i class="bi bi-chevron-right"></i></a>
            <a wire:navigate href="{{ route('product-categories') }}" data-kba-menu-link><i class="bi bi-tag"></i><span>Categories</span><i class="bi bi-chevron-right"></i></a>
            <a wire:navigate href="{{ route('pages.show', 'about-us') }}" data-kba-menu-link><i class="bi bi-info-circle"></i><span>About Us</span><i class="bi bi-chevron-right"></i></a>
            <a wire:navigate href="{{ route('gallery.index') }}" data-kba-menu-link><i class="bi bi-images"></i><span>Photo Gallery</span><i class="bi bi-chevron-right"></i></a>
            <a wire:navigate href="{{ route('videos.index') }}" data-kba-menu-link><i class="bi bi-camera-video"></i><span>Video Gallery</span><i class="bi bi-chevron-right"></i></a>
            <a wire:navigate href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'is-active' : '' }}" data-kba-menu-link><i class="bi bi-chat-dots"></i><span>Contact Us</span><i class="bi bi-chevron-right"></i></a>

            <span class="kba-mobile-nav-label kba-account-label">My Account</span>
            <a wire:navigate href="{{ route('wishlist') }}" data-kba-menu-link><i class="bi bi-heart"></i><span>Wishlist</span><b>{{ $wishlist_count }}</b></a>
            <a wire:navigate href="{{ route('cart-products') }}" data-kba-menu-link><i class="bi bi-bag"></i><span>Shopping Bag</span><b>{{ $total_count }}</b></a>
            @auth
                <a wire:navigate href="{{ route('dashboard') }}" data-kba-menu-link><i class="bi bi-grid-1x2"></i><span>Dashboard</span><i class="bi bi-chevron-right"></i></a>
                <a wire:navigate href="{{ route('profile.edit') }}" data-kba-menu-link><i class="bi bi-person-gear"></i><span>Edit Profile</span><i class="bi bi-chevron-right"></i></a>
                <a wire:navigate href="{{ route('my-orders') }}" data-kba-menu-link><i class="bi bi-box-seam"></i><span>My Orders</span><i class="bi bi-chevron-right"></i></a>
                <button type="button" wire:click="logout" data-kba-menu-link><i class="bi bi-box-arrow-right"></i><span>Log Out</span><i class="bi bi-chevron-right"></i></button>
            @else
                <a wire:navigate href="{{ route('register') }}" data-kba-menu-link><i class="bi bi-person-plus"></i><span>Create Account</span><i class="bi bi-chevron-right"></i></a>
            @endauth
        </nav>

        <div class="kba-mobile-sidebar-contact">
            <i class="bi bi-telephone"></i>
            <span><small>Need help?</small><a href="tel:{{ preg_replace('/[^+\d]/', '', \App\Models\Setting::get('store_phone', '+61 432 685 546')) }}">{{ \App\Models\Setting::get('store_phone', '+61 432 685 546') }}</a></span>
        </div>
    </aside>

    <!-- Mobile Bottom App Nav Bar -->
    <nav class="kba-mobile-bottom-nav md:hidden" data-kba-mobile-bottom aria-label="Mobile navigation">
        <a wire:navigate href="{{ route('index') }}" class="{{ request()->routeIs('index') ? 'is-active' : '' }}">
            <span><i class="bi bi-house-door{{ request()->routeIs('index') ? '-fill' : '' }}"></i></span><small>Home</small>
        </a>
        <a wire:navigate href="{{ route('all-products') }}" class="{{ request()->routeIs('all-products', 'product-details') ? 'is-active' : '' }}">
            <span><i class="bi bi-grid"></i></span><small>Shop</small>
        </a>
        <a wire:navigate href="{{ route('wishlist') }}" class="{{ request()->routeIs('wishlist') ? 'is-active' : '' }}">
            <span><i class="bi bi-heart{{ request()->routeIs('wishlist') ? '-fill' : '' }}"></i><b>{{ $wishlist_count }}</b></span><small>Wishlist</small>
        </a>
        <a wire:navigate href="{{ route('cart-products') }}" class="{{ request()->routeIs('cart-products', 'checkout') ? 'is-active' : '' }}">
            <span><i class="bi bi-bag{{ request()->routeIs('cart-products', 'checkout') ? '-fill' : '' }}"></i><b>{{ $total_count }}</b></span><small>Cart</small>
        </a>
        @auth
            <a wire:navigate href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard', 'my-orders', 'my-order-details') ? 'is-active' : '' }}">
                <span><i class="bi bi-person{{ request()->routeIs('dashboard', 'my-orders', 'my-order-details') ? '-fill' : '' }}"></i></span><small>Account</small>
            </a>
        @else
            <a wire:navigate href="{{ route('login') }}" class="{{ request()->routeIs('login', 'register') ? 'is-active' : '' }}">
                <span><i class="bi bi-person{{ request()->routeIs('login', 'register') ? '-fill' : '' }}"></i></span><small>Login</small>
            </a>
        @endauth
    </nav>
</div>