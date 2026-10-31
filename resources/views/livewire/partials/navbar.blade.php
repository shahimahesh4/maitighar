<div class="kba-store-header w-full bg-white font-sans shadow-sm" data-kba-store-header>
    <!-- Top Header Bar (Dark Charcoal matching reference design) -->
    <div class="kba-store-utility bg-[#222222] text-white/90 border-b border-black/20 py-1 sm:py-1.5">
        <div class="kba-store-utility__inner w-full px-4 sm:px-6 lg:px-8 xl:px-12 flex items-center justify-between">
            <!-- Left: Hotline & Free Delivery & Quick Links -->
            <div class="flex items-center gap-2.5 sm:gap-3.5 text-[11px] sm:text-[12px] leading-tight">
                <a href="tel:{{ preg_replace('/[^+\d]/', '', \App\Models\Setting::get('store_phone', '+61432685546')) }}" class="flex items-center gap-1.5 text-white/85 hover:text-white transition-colors">
                    <i class="bi bi-telephone text-[11px] sm:text-[12px] text-white/70"></i>
                    <span><span class="hidden sm:inline">Hotline: </span><strong class="font-semibold text-white">{{ \App\Models\Setting::get('store_phone', '+61 432 685 546') }}</strong></span>
                </a>
                <span class="text-white/20 hidden sm:inline text-[11px]">|</span>
                <div class="hidden sm:flex items-center gap-1.5 text-white/85">
                    <i class="bi bi-truck text-[12px] sm:text-[13px] text-white/70"></i>
                    <span>Free Delivery</span>
                </div>
                <span class="text-white/20 hidden lg:inline text-[11px]">|</span>
                <a wire:navigate href="{{ route('contact') }}" class="hidden lg:flex items-center gap-1.5 text-white/85 hover:text-white transition-colors">
                    <i class="bi bi-geo-alt text-[11px] sm:text-[12px] text-white/70"></i>
                    <span>Find Store</span>
                </a>
                <span class="text-white/20 hidden lg:inline text-[11px]">|</span>
                <a wire:navigate href="{{ route('support-tickets') }}" class="hidden lg:flex items-center gap-1.5 text-white/85 hover:text-white transition-colors">
                    <i class="bi bi-headset text-[11px] sm:text-[12px] text-white/70"></i>
                    <span>Help Desk</span>
                </a>
            </div>

            <!-- Right: Social Media Icons & Currency -->
            <div class="flex items-center gap-2.5 sm:gap-3.5 text-[11px] sm:text-[12px] leading-tight">
                <!-- Social Media Links -->
                <div class="flex items-center gap-2.5 sm:gap-3 text-white/75">
                    @if($fbUrl = \App\Models\Setting::get('facebook_url', 'https://www.facebook.com/maitigharaustralia'))
                        <a href="{{ $fbUrl }}" target="_blank" rel="noopener" class="text-white/75 hover:text-white transition-colors inline-flex items-center justify-center" aria-label="Facebook" title="Facebook">
                            <i class="bi bi-facebook text-[16px] sm:text-[17px]"></i>
                        </a>
                    @endif
                    @if($instaUrl = \App\Models\Setting::get('instagram_url', 'https://www.instagram.com/maitighar'))
                        <a href="{{ $instaUrl }}" target="_blank" rel="noopener" class="text-white/75 hover:text-white transition-colors inline-flex items-center justify-center" aria-label="Instagram" title="Instagram">
                            <i class="bi bi-instagram text-[16px] sm:text-[17px]"></i>
                        </a>
                    @endif
                    @if($tiktokUrl = \App\Models\Setting::get('tiktok_url', 'https://www.tiktok.com'))
                        <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener" class="text-white/75 hover:text-white transition-colors inline-flex items-center justify-center" aria-label="TikTok" title="TikTok">
                            <i class="bi bi-tiktok text-[16px] sm:text-[17px]"></i>
                        </a>
                    @endif
                    @if($ytUrl = \App\Models\Setting::get('youtube_url', 'https://www.youtube.com'))
                        <a href="{{ $ytUrl }}" target="_blank" rel="noopener" class="text-white/75 hover:text-white transition-colors inline-flex items-center justify-center" aria-label="YouTube" title="YouTube">
                            <i class="bi bi-youtube text-[16px] sm:text-[17px]"></i>
                        </a>
                    @endif
                </div>

                <span class="text-white/20 text-[11px]">|</span>

                <!-- Currency Switcher with Orange Accent (Defaults to AUD) -->
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-1 cursor-pointer hover:opacity-90 transition-opacity text-[#FF9800] font-bold text-[11px] sm:text-[12px] focus:outline-none" aria-label="Select currency" title="Select currency">
                        <span>{{ $currentCurrency }}</span>
                        <i class="bi bi-chevron-down text-[9px] text-[#FF9800]"></i>
                    </button>
                    <div x-show="open" x-cloak class="absolute right-0 top-full mt-2 w-32 bg-white text-slate-800 rounded-xl shadow-xl border border-slate-200 py-1.5 z-50 text-xs">
                        <button type="button" wire:click="setCurrency('AUD')" @click="open = false" class="w-full text-left px-3.5 py-1.5 hover:bg-slate-50 flex items-center justify-between {{ $currentCurrency === 'AUD' ? 'font-bold text-[#EA0202]' : 'text-slate-700' }}">
                            <span>AUD ($)</span>
                            @if($currentCurrency === 'AUD')<i class="bi bi-check text-base text-[#EA0202]"></i>@endif
                        </button>
                        <button type="button" wire:click="setCurrency('USD')" @click="open = false" class="w-full text-left px-3.5 py-1.5 hover:bg-slate-50 flex items-center justify-between {{ $currentCurrency === 'USD' ? 'font-bold text-[#EA0202]' : 'text-slate-700' }}">
                            <span>USD ($)</span>
                            @if($currentCurrency === 'USD')<i class="bi bi-check text-base text-[#EA0202]"></i>@endif
                        </button>
                        <button type="button" wire:click="setCurrency('NPR')" @click="open = false" class="w-full text-left px-3.5 py-1.5 hover:bg-slate-50 flex items-center justify-between {{ $currentCurrency === 'NPR' ? 'font-bold text-[#EA0202]' : 'text-slate-700' }}">
                            <span>NPR (Rs.)</span>
                            @if($currentCurrency === 'NPR')<i class="bi bi-check text-base text-[#EA0202]"></i>@endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Top Header Row (Highlighted with Logo Color #EA0202) -->
    <header class="kba-store-mainbar border-b border-[#C90204]">
        <div class="kba-store-mainbar__inner w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-1 sm:py-1.5 lg:py-2 flex items-center justify-between gap-3 sm:gap-6">
            
            <!-- Logo & Brand -->
            <div class="kba-store-logo flex items-center flex-shrink-0">
                <a wire:navigate href="{{ route('index') }}" class="flex items-center" aria-label="Maitighar home">
                    <img src="{{ asset('images/logo.webp') }}" alt="Maitighar" class="h-10 sm:h-11 md:h-12 lg:h-14 xl:h-16 w-auto object-contain transition-all hover:scale-105 duration-200">
                </a>
            </div>

            <!-- Central Navigation Menu (Moved into header) -->
            <nav class="kba-store-nav hidden md:flex items-center gap-1 lg:gap-2.5 xl:gap-4 flex-1 justify-center mx-2 lg:mx-6 text-xs sm:text-sm">
                <!-- Home Link -->
                <a wire:navigate href="{{ route('index') }}" class="px-3 py-1 rounded-lg font-bold transition-all {{ request()->routeIs('index') ? 'bg-white text-[#EC0205] shadow-sm' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                    Home
                </a>

                <a wire:navigate href="{{ route('all-products') }}" class="px-3 py-1 rounded-lg font-semibold transition-all {{ request()->routeIs('all-products', 'product-details', 'product-categories') ? 'bg-white text-[#EC0205] font-bold' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                    Products
                </a>

                <!-- About Us -->
                <a wire:navigate href="{{ route('pages.show', 'about-us') }}" class="px-3 py-1 rounded-lg font-semibold transition-all {{ request()->is('about-us') ? 'bg-white text-[#EC0205] font-bold' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                    About Us
                </a>

                <!-- Gallery Dropdown -->
                <div class="relative group" x-data="{ open: false, timeout: null }" :class="{ 'is-open': open }"
                     @mouseenter="clearTimeout(timeout); open = true"
                     @mouseleave="timeout = setTimeout(() => { open = false }, 250)"
                     @click.outside="open = false">
                    <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu" class="px-3 py-1 rounded-lg font-semibold inline-flex items-center gap-1 transition-all focus:outline-none {{ request()->routeIs('gallery.*', 'videos.*') ? 'bg-white text-[#EC0205] font-bold' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                        <span>Gallery</span>
                        <i class="bi bi-chevron-down kba-store-dropdown-chevron text-[9px] text-white/80 transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 top-full pt-1.5 w-44 z-50">
                        <div class="rounded-xl bg-white border border-slate-200 shadow-xl py-2 text-slate-800">
                            <a wire:navigate href="{{ route('gallery.index') }}" class="block px-4 py-2 text-xs text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 font-semibold transition-colors">Photos</a>
                            <a wire:navigate href="{{ route('videos.index') }}" class="block px-4 py-2 text-xs text-slate-700 hover:text-[#EA0202] hover:bg-slate-50 font-semibold transition-colors">Videos</a>
                        </div>
                    </div>
                </div>

                <!-- Contact Us -->
                <a wire:navigate href="{{ route('contact') }}" class="px-3 py-1 rounded-lg font-semibold transition-all {{ request()->routeIs('contact') ? 'bg-white text-[#EC0205] font-bold' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                    Contact Us
                </a>
            </nav>

            <!-- Right Actions: Wishlist, Your Cart, Account -->
            <div class="kba-store-actions flex items-center gap-4 sm:gap-6 flex-shrink-0">
                
                <!-- Wishlist -->
                <a wire:navigate href="{{ route('wishlist') }}" class="flex flex-col items-center justify-center text-white hover:text-white/80 transition-colors group">
                    <div class="relative">
                        <i class="bi bi-heart text-lg sm:text-xl group-hover:scale-105 transition-transform text-white"></i>
                        @if($wishlist_count > 0)
                            <span class="absolute -top-1 -right-2 bg-white text-[#EA0202] text-[8px] font-black rounded-full h-3 min-w-[12px] px-0.5 flex items-center justify-center shadow-sm">{{ $wishlist_count }}</span>
                        @endif
                    </div>
                    <span class="text-[10px] font-semibold text-white/95 mt-0.5 transition-colors">Wishlist</span>
                </a>

                <!-- Your Cart -->
                <a wire:navigate href="{{ route('cart-products') }}" class="flex flex-col items-center justify-center text-white hover:text-white/80 transition-colors group">
                    <div class="relative">
                        <i class="bi bi-cart3 text-lg sm:text-xl group-hover:scale-105 transition-transform text-white"></i>
                        <span class="absolute -top-1.5 -right-2.5 bg-white text-[#EA0202] text-[9px] font-black rounded-full h-3.5 min-w-[14px] px-0.5 flex items-center justify-center border-2 border-[#EA0202] shadow-sm">{{ $total_count }}</span>
                    </div>
                    <span class="text-[10px] font-semibold text-white/95 mt-0.5 transition-colors">Your Cart</span>
                </a>

                <!-- Account -->
                <div class="relative group" x-data="{ open: false, timeout: null }"
                     @mouseenter="clearTimeout(timeout); open = true"
                     @mouseleave="timeout = setTimeout(() => { open = false }, 250)"
                     @click.outside="open = false">
                    <button type="button" @click="open = !open" class="flex flex-col items-center justify-center text-white hover:text-white/80 transition-colors group focus:outline-none">
                        <i class="bi bi-person-circle text-lg sm:text-xl group-hover:scale-105 transition-transform text-white"></i>
                        <span class="text-[10px] font-semibold text-white/95 mt-0.5 transition-colors">Account</span>
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
                <button type="button" class="md:hidden flex items-center justify-center w-8 h-8 rounded-lg border border-white/40 text-white hover:bg-white/10" data-kba-menu-open aria-label="Open mobile menu">
                    <i class="bi bi-list text-xl text-white"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Slide-out Menu Backdrop -->
    <div class="kba-mobile-backdrop" data-kba-menu-backdrop aria-hidden="true"></div>

    <!-- Mobile Slide-out Sidebar -->
    <aside class="kba-mobile-sidebar" id="kba-mobile-sidebar" data-kba-mobile-sidebar aria-hidden="true" aria-label="Mobile navigation" inert>
        <div class="kba-mobile-sidebar-head">
            <div class="kba-sidebar-brand">
                <img src="{{ asset('images/logo.webp') }}" alt="Maitighar" width="130" height="129">
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
