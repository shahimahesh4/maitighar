<div class="w-full max-w-[1150px] py-12 px-4 sm:px-6 lg:px-8 mx-auto text-left">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-xs text-gray-500 dark:text-slate-400 font-medium" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a wire:navigate href="/" class="inline-flex items-center hover:text-amber-600 dark:hover:text-amber-500">
                    <i class="bi bi-house-door-fill mr-1.5 text-sm"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right mx-1 text-gray-400 dark:text-slate-650 text-[10px]"></i>
                    <a wire:navigate href="/products" class="hover:text-amber-600 dark:hover:text-amber-500">Products</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="bi bi-chevron-right mx-1 text-gray-400 dark:text-slate-650 text-[10px]"></i>
                    <span class="text-gray-800 dark:text-white font-semibold">Payment Cancelled</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800/80 rounded-3xl p-6 sm:p-10 shadow-sm max-w-2xl mx-auto text-center space-y-6">
        
        <!-- Warning Icon -->
        <div class="w-20 h-20 bg-red-50 dark:bg-red-950/30 text-red-500 dark:text-red-400 rounded-full flex items-center justify-center mx-auto text-4xl shadow-sm border border-red-100 dark:border-red-900/50">
            ⚠
        </div>
        
        <div class="space-y-2">
            <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white tracking-tight">Payment Cancelled</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 max-w-sm mx-auto">Your payment was cancelled or could not be processed. No funds have been charged from your account.</p>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap justify-center gap-4 pt-4 border-t border-gray-100 dark:border-slate-800/80">
            <a wire:navigate href="/cart" class="py-2.5 px-5 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-slate-700 dark:text-gray-300 dark:hover:bg-slate-800 transition">
                Return to Cart
            </a>
            <a wire:navigate href="/products" class="py-2.5 px-5 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-amber-600 text-white hover:bg-amber-700 shadow-md shadow-amber-500/10 hover:shadow-amber-500/20 transition hover-lift">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
