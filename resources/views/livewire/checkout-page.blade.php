<div class="min-h-screen bg-slate-50 py-4 sm:py-6">
    <div class="mx-auto w-[calc(100%-32px)] max-w-[1320px]">
        <nav class="mb-5 flex items-center gap-2 text-xs text-slate-500 sm:mb-6" aria-label="Breadcrumb">
            <a wire:navigate href="{{ route('index') }}" class="inline-flex items-center gap-1.5 font-semibold hover:text-[#EC0205]"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a><i class="bi bi-chevron-right text-[9px]"></i>
            <a wire:navigate href="{{ route('cart-products') }}" class="font-semibold hover:text-[#EC0205]">Cart</a><i class="bi bi-chevron-right text-[9px]"></i>
            <span class="font-bold text-slate-800">Checkout</span>
        </nav>

        <header class="relative mb-7 overflow-hidden rounded-[28px] bg-gradient-to-br from-[#091b38] via-[#0C1E8C] to-[#071355] px-5 py-7 text-white shadow-[0_20px_50px_rgba(12,30,140,.18)] sm:px-8 sm:py-8 lg:px-10">
            <span class="pointer-events-none absolute -right-16 -top-28 h-72 w-72 rounded-full border border-white/10"></span>
            <span class="pointer-events-none absolute -bottom-32 right-28 h-64 w-64 rounded-full border border-white/10"></span>

            <div class="relative grid gap-7 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                <div class="flex items-start gap-4">
                    <span class="hidden h-14 w-14 shrink-0 place-items-center rounded-2xl border border-white/15 bg-white/10 text-xl text-red-400 shadow-inner sm:grid"><i class="bi bi-shield-lock" aria-hidden="true"></i></span>
                    <div>
                        <p class="mb-2 flex items-center gap-2 text-[10px] font-extrabold uppercase tracking-[.22em] text-red-400"><i class="bi bi-lock-fill sm:hidden" aria-hidden="true"></i> Secure checkout</p>
                        <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl lg:text-[34px]">Complete your order</h1>
                        <p class="mt-2 max-w-xl text-xs leading-5 text-white/65 sm:text-sm">Confirm your delivery details, select a trusted payment method, and review everything before placing your order.</p>
                    </div>
                </div>

                <ol class="grid grid-cols-[auto_1fr_auto_1fr_auto] items-center gap-2 rounded-2xl border border-white/10 bg-black/10 px-3 py-3 backdrop-blur-sm sm:min-w-[390px] sm:px-5" aria-label="Checkout progress">
                    <li class="flex items-center gap-2 text-[#bde1c5]">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-emerald-400/15 text-xs ring-1 ring-emerald-300/30"><i class="bi bi-check-lg" aria-hidden="true"></i></span>
                        <span class="hidden text-[9px] font-bold uppercase tracking-wider sm:block">Cart</span>
                    </li>
                    <span class="h-px bg-white/20" aria-hidden="true"></span>
                    <li class="flex items-center justify-center gap-2 text-white" aria-current="step">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#EC0205] text-xs font-extrabold text-white shadow-[0_0_0_4px_rgba(236,2,5,.2)]">2</span>
                        <span class="hidden text-[9px] font-bold uppercase tracking-wider sm:block">Checkout</span>
                    </li>
                    <span class="h-px bg-white/20" aria-hidden="true"></span>
                    <li class="flex items-center justify-end gap-2 text-white/50">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-white/15 bg-white/5 text-xs font-bold">3</span>
                        <span class="hidden text-[9px] font-bold uppercase tracking-wider sm:block">Complete</span>
                    </li>
                </ol>
            </div>

            <div class="relative mt-6 grid grid-cols-3 divide-x divide-white/10 border-t border-white/10 pt-4 text-center sm:flex sm:justify-start sm:gap-0 sm:text-left">
                @foreach ([['bi-shield-check', 'Protected data'], ['bi-truck', 'Delivery options'], ['bi-headset', 'Customer support']] as [$icon, $label])
                    <span class="flex items-center justify-center gap-2 px-2 text-[9px] font-bold uppercase tracking-wide text-white/60 first:pl-0 sm:justify-start sm:px-5 sm:first:pl-0"><i class="bi {{ $icon }} text-sm text-red-400" aria-hidden="true"></i>{{ $label }}</span>
                @endforeach
            </div>
        </header>

        @if($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-xs text-red-800" role="alert"><i class="bi bi-exclamation-circle-fill mt-0.5"></i><div><strong class="block text-sm">Please check your information</strong><span class="mt-1 block">{{ $errors->first() }}</span></div></div>
        @endif
        @if(session('warning'))<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-xs font-semibold text-amber-800"><i class="bi bi-exclamation-triangle-fill mr-2"></i>{{ session('warning') }}</div>@endif

            <section id="order-otp-panel" aria-labelledby="order-otp-title" @class(['mx-auto max-w-xl scroll-mt-28 rounded-[28px] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/10 sm:p-9', 'hidden' => ! $awaitingOrderOtp])>
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-50 text-xl text-[#0C1E8C]"><i class="bi bi-envelope-check"></i></div>
                <div class="mt-5 text-center">
                    <p class="text-[10px] font-extrabold uppercase tracking-[.2em] text-[#0C1E8C]">Order confirmation</p>
                    <h2 id="order-otp-title" class="mt-2 text-2xl font-extrabold text-slate-900">Enter your email OTP</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Confirm this checkout before we create your order and begin payment. The code expires in 10 minutes.</p>
                </div>

                @if(session('order_otp_status'))<div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center text-xs font-semibold text-emerald-700">{{ session('order_otp_status') }}</div>@endif

                <form wire:submit.prevent="verifyOrderOtp" class="mt-6 space-y-4">
                    <div>
                        <label for="order-otp" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Six-digit confirmation code</label>
                        <input id="order-otp" wire:model="orderOtp" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000" autofocus required class="h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-center text-xl font-bold tracking-[.35em] outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100">
                        @error('orderOtp')<p class="mt-2 text-center text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="verifyOrderOtp" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#EC0205] to-[#C90204] px-5 text-xs font-extrabold text-white disabled:opacity-60">
                        <span wire:loading.remove wire:target="verifyOrderOtp">Confirm order and continue <i class="bi bi-arrow-right"></i></span>
                        <span wire:loading wire:target="verifyOrderOtp"><i class="bi bi-arrow-repeat kba-spin"></i> Confirming…</span>
                    </button>
                    <div class="flex items-center justify-between gap-3 text-xs">
                        <button type="button" wire:click="resendOrderOtp" wire:loading.attr="disabled" wire:target="resendOrderOtp" class="font-bold text-[#0C1E8C] hover:underline">Send a new code</button>
                        <button type="button" wire:click="$set('awaitingOrderOtp', false)" class="font-semibold text-slate-500 hover:text-slate-800">Back to checkout</button>
                    </div>
                </form>
            </section>
        <form wire:submit="placeOrder" enctype="multipart/form-data" @class(['grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_360px]', 'hidden' => $awaitingOrderOtp]) data-kba-recaptcha-form>
            <input type="hidden" id="recaptchaToken" wire:model="recaptchaToken">
            <div class="space-y-6">
                <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-4 border-b border-slate-100 px-5 py-5 sm:px-7"><span class="grid h-10 w-10 place-items-center rounded-2xl bg-blue-50 text-[#0C1E8C]"><i class="bi bi-geo-alt"></i></span><div><h2 class="text-base font-extrabold text-slate-900">Delivery information</h2><p class="mt-1 text-[11px] text-slate-500">Where should we deliver your order?</p></div></div>
                    <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-7">
                        @foreach([
                            ['first_name','First name','text','Your first name','person'],
                            ['last_name','Last name','text','Your last name','person'],
                            ['phone','Phone number','tel','+977 9812345678','telephone'],
                        ] as [$field,$label,$type,$placeholder,$icon])
                            <div class="{{ $field === 'phone' ? 'sm:col-span-2' : '' }}" data-checkout-field>
                                <label for="checkout-{{ $field }}" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $label }} <b class="text-red-500">*</b></label>
                                <div class="relative"><i class="bi bi-{{ $icon }} absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="checkout-{{ $field }}" wire:model="{{ $field }}" type="{{ $type }}" placeholder="{{ $placeholder }}" class="h-12 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-slate-800 outline-none transition focus:border-[#0C1E8C] focus:bg-white focus:ring-4 focus:ring-blue-100" required></div>
                                @error($field)<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                        <div class="sm:col-span-2" data-checkout-field><label for="checkout-street" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Street address <b class="text-red-500">*</b></label><div class="relative"><i class="bi bi-house absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="checkout-street" wire:model="street_address" type="text" placeholder="Street, area, ward or landmark" class="h-12 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm outline-none focus:border-[#0C1E8C] focus:bg-white focus:ring-4 focus:ring-blue-100" required></div>@error('street_address')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        @foreach([['city','City','Bhairahawa'],['state','Province / State','Lumbini Province'],['zip_code','Postal code','32900']] as [$field,$label,$placeholder])
                            <div data-checkout-field><label for="checkout-{{ $field }}" class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $label }} <b class="text-red-500">*</b></label><input id="checkout-{{ $field }}" wire:model="{{ $field }}" type="text" placeholder="{{ $placeholder }}" class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm outline-none focus:border-[#0C1E8C] focus:bg-white focus:ring-4 focus:ring-blue-100" required>@error($field)<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        @endforeach
                    </div>
                </section>

                <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-4 border-b border-slate-100 px-5 py-5 sm:px-7"><span class="grid h-10 w-10 place-items-center rounded-2xl bg-blue-50 text-[#0C1E8C]"><i class="bi bi-truck"></i></span><div><h2 class="text-base font-extrabold text-slate-900">Delivery option</h2><p class="mt-1 text-[11px] text-slate-500">Choose the rate for your delivery area.</p></div></div>
                    <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-7">
                        @forelse($shipping_rates as $rate)
                            <label for="shipping-{{ $rate['id'] }}" @class(['relative cursor-pointer rounded-2xl border p-4 transition','border-[#0C1E8C] bg-blue-50/50 ring-2 ring-blue-100'=>(int)$shipping_rate_id===(int)$rate['id'],'border-slate-200 bg-white hover:border-[#0C1E8C]/40'=>(int)$shipping_rate_id!==(int)$rate['id']])>
                                <div class="flex items-start gap-3">
                                    <input id="shipping-{{ $rate['id'] }}" type="radio" wire:model.live="shipping_rate_id" value="{{ $rate['id'] }}" class="mt-1 h-4 w-4 text-[#0C1E8C] focus:ring-[#0C1E8C]">
                                    <span class="min-w-0 flex-1"><strong class="block text-xs text-slate-900">{{ $rate['shipping_method'] }}</strong><small class="mt-1 block text-[10px] leading-4 text-slate-500">{{ $rate['zone_name'] }} · {{ $rate['min_delivery_days'] }}–{{ $rate['max_delivery_days'] }} business days</small></span>
                                    <strong class="text-xs font-bold text-[#EC0205]">{{ \App\Support\Money::format($rate['cost']) }}</strong>
                                </div>
                            </label>
                        @empty
                            <div class="sm:col-span-2 rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-semibold text-red-700">No delivery options are currently available. Please contact the store.</div>
                        @endforelse
                        @error('shipping_rate_id')<p class="text-xs font-semibold text-red-600 sm:col-span-2">{{ $message }}</p>@enderror
                    </div>
                </section>

                <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-4 border-b border-slate-100 px-5 py-5 sm:px-7"><span class="grid h-10 w-10 place-items-center rounded-2xl bg-blue-50 text-[#0C1E8C]"><i class="bi bi-wallet2"></i></span><div><h2 class="text-base font-extrabold text-slate-900">Choose payment method</h2><p class="mt-1 text-[11px] text-slate-500">Secure and convenient payment options.</p></div></div>
                    <div class="space-y-5 p-5 sm:p-7">
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($active_gateways as $gateway)
                                <label for="pay-{{ $gateway['code'] }}" @class(['relative cursor-pointer rounded-2xl border p-4 transition','border-[#0C1E8C] bg-blue-50/50 ring-2 ring-blue-100'=>$payment_method===$gateway['code'],'border-slate-200 bg-white hover:border-[#0C1E8C]/40'=>$payment_method!==$gateway['code']])>
                                    <div class="flex items-center gap-3"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-lg text-[#0C1E8C] shadow-sm"><i class="bi {{ match($gateway['code']) {'cod'=>'bi-cash-coin','bank_transfer'=>'bi-bank',default=>'bi-credit-card'} }}"></i></span><div class="min-w-0 flex-1"><strong class="block truncate text-xs text-slate-900">{{ $gateway['name'] }}</strong><small class="mt-1 block text-[10px] leading-4 text-slate-500">{{ match($gateway['code']) {'cod'=>'Pay when your order arrives.','bank_transfer'=>'Transfer to our bank account and upload receipt.',default=>'Secure payment.'} }}</small></div><input id="pay-{{ $gateway['code'] }}" type="radio" wire:model.live="payment_method" value="{{ $gateway['code'] }}" class="h-4 w-4 text-[#0C1E8C] focus:ring-[#0C1E8C]"></div>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_method')<p class="text-xs font-semibold text-red-600">{{ $message }}</p>@enderror

                        @if($payment_method === 'bank_transfer')
                            <div wire:key="manual-bank-transfer" class="space-y-5 rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-4 sm:p-5">
                                <div><span class="text-[10px] font-bold uppercase tracking-[.16em] text-[#0C1E8C]">Pay before placing order</span><p class="mt-2 text-xs leading-5 text-slate-600">{{ $paymentSettings['instructions'] ?? 'Transfer the exact order total to our bank account and upload your receipt.' }}</p></div>
                                <dl class="grid gap-3 rounded-2xl border border-blue-100 bg-white p-4 text-xs sm:grid-cols-3">
                                    @foreach([['Bank',$paymentSettings['bank_name']??'Configure in admin'],['Account name',$paymentSettings['account_name']??'Configure in admin'],['Account number',$paymentSettings['account_number']??'Configure in admin']] as [$label,$value])<div><dt class="text-[10px] uppercase tracking-wider text-slate-400">{{ $label }}</dt><dd class="mt-1 break-all font-bold text-slate-900">{{ $value }}</dd></div>@endforeach
                                </dl>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div data-checkout-field><label for="payment_reference" class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Transaction reference <b class="text-red-500">*</b></label><input id="payment_reference" wire:model="payment_reference" type="text" placeholder="Bank transaction ID / Reference" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-xs outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100" required>@error('payment_reference')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                                    <div data-checkout-field><label for="payment_voucher" class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Payment receipt <b class="text-red-500">*</b></label><div class="rounded-xl border border-dashed border-blue-300 bg-white p-3"><input id="payment_voucher" wire:model="payment_voucher" type="file" accept="image/jpeg,image/png,image/webp" class="w-full text-[11px] text-slate-500 file:mr-2 file:rounded-lg file:border-0 file:bg-[#0C1E8C] file:px-3 file:py-2 file:text-[10px] file:font-bold file:text-white" required><span wire:loading wire:target="payment_voucher" class="mt-2 block text-[10px] text-[#0C1E8C]">Uploading receipt…</span>@if($payment_voucher)<img src="{{ $payment_voucher->temporaryUrl() }}" alt="Receipt preview" class="mt-3 max-h-36 rounded-lg border object-contain">@endif</div>@error('payment_voucher')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-6">
                <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                    <div class="bg-gradient-to-br from-[#091b38] to-[#0C1E8C] px-6 py-5 text-white"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-red-400">Order review</p><div class="mt-2 flex items-center justify-between"><h2 class="text-lg font-extrabold">Your order</h2><span class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-bold">{{ $cartQuantity }} items</span></div></div>
                    <ul class="max-h-64 divide-y divide-slate-100 overflow-y-auto px-5">
                        @foreach($checkoutItems as $item)
                            <li class="flex items-center gap-3 py-4">
                                <span class="relative grid h-12 w-12 shrink-0 place-items-center rounded-xl border bg-slate-50 p-1">
                                    <img src="{{ $item['image_url'] }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}'" alt="{{ $item['name'] }}" class="h-full w-full object-contain">
                                    <b class="absolute -right-1.5 -top-1.5 grid h-5 min-w-5 place-items-center rounded-full bg-[#EC0205] px-1 text-[9px] text-white">{{ $item['quantity'] }}</b>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <strong class="line-clamp-2 text-xs leading-4 text-slate-800">{{ $item['name'] }}</strong>
                                    @if(!empty($item['options_text']))
                                        <span class="block text-[10px] text-slate-500 font-medium">{{ $item['options_text'] }}</span>
                                    @endif
                                    <small class="mt-0.5 block text-[10px] text-slate-400">{{ \App\Support\Money::format($item['unit_amount']) }} each</small>
                                </span>
                                <strong class="text-xs text-slate-800">{{ \App\Support\Money::format($item['total_amount']) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                    <div class="border-t border-slate-100 p-5">
                        <label for="coupon" class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Promo code</label>
                        <div class="flex gap-2"><input id="coupon" wire:model.defer="coupon_code" type="text" placeholder="Enter code" class="h-10 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 text-xs uppercase outline-none focus:border-[#0C1E8C]"><button type="button" wire:click="applyCoupon" wire:loading.attr="disabled" wire:target="applyCoupon" class="rounded-xl bg-[#0C1E8C] hover:bg-[#091b38] px-4 text-[11px] font-bold text-white">Apply</button></div>
                        @if(session('coupon_success'))<p class="mt-2 text-[11px] font-semibold text-emerald-700">{{ session('coupon_success') }}</p>@endif
                        @if(session('coupon_error'))<p class="mt-2 text-[11px] font-semibold text-red-600">{{ session('coupon_error') }}</p>@endif
                        <dl class="mt-5 space-y-3 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Subtotal</dt>
                                <dd class="font-bold text-slate-900">{{ \App\Support\Money::format($subtotal) }}</dd>
                            </div>
                            @if($discount_amount > 0)
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Discount ({{ $applied_coupon }})</dt>
                                    <dd class="font-bold text-emerald-700">−{{ \App\Support\Money::format($discount_amount) }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500">
                                    Shipping
                                    @if($shipping_method)
                                        <small class="mt-0.5 block text-[9px]">{{ $shipping_method }}</small>
                                    @endif
                                </dt>
                                <dd class="font-bold text-emerald-700">
                                    {{ $shipping_rate_id ? \App\Support\Money::format($shipping_amount) : 'Select delivery' }}
                                </dd>
                            </div>
                        </dl>
                        <div class="my-4 border-t border-dashed border-slate-200"></div>
                        <div class="flex items-end justify-between"><span class="text-sm font-extrabold text-slate-900">Total</span><strong class="text-xl font-extrabold text-[#EC0205]">{{ \App\Support\Money::format($grand_total) }}</strong></div>
                        <p class="mt-1.5 text-right text-[11px] font-semibold text-slate-500">Your card will be charged in Australian dollars (AUD).</p>
                        <button type="submit" wire:loading.attr="disabled" wire:target="placeOrder,payment_voucher" class="mt-5 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#EC0205] to-[#C90204] px-5 text-xs font-extrabold text-white shadow-lg shadow-amber-900/15 transition hover:-translate-y-0.5 disabled:cursor-wait disabled:opacity-60"><span wire:loading.remove wire:target="placeOrder">Pay {{ \App\Support\Money::format($grand_total) }} AUD <i class="bi bi-arrow-right ml-1"></i></span><span wire:loading wire:target="placeOrder"><i class="bi bi-arrow-repeat kba-spin"></i>Processing order…</span></button>
                        <p class="mt-3 text-center text-[10px] leading-4 text-slate-400"><i class="bi bi-shield-lock mr-1 text-emerald-600"></i>Your information is encrypted and protected.</p>
                    </div>
                </section>
                <div class="grid grid-cols-3 gap-2 rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm">@foreach([['bi-shield-check','Secure'],['bi-truck','Delivery'],['bi-headset','Support']] as [$icon,$label])<div><i class="bi {{ $icon }} text-base text-[#0C1E8C]"></i><span class="mt-1 block text-[9px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</span></div>@endforeach</div>
            </aside>
        </form>
    </div>

    @script
    <script>
        $wire.on('checkout-validation-failed', () => {
            window.setTimeout(() => {
                const error = document.querySelector('[data-checkout-field] .text-red-600');
                if (! error) return;
                const field = error.closest('[data-checkout-field]');
                const control = field?.querySelector('input, select, textarea');
                field?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                window.setTimeout(() => control?.focus({ preventScroll: true }), 350);
            }, 75);
        });

        $wire.on('order-otp-ready', () => {
            window.setTimeout(() => {
                const panel = document.getElementById('order-otp-panel');
                const input = document.getElementById('order-otp');
                if (! panel || ! input) return;

                panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                window.setTimeout(() => input.focus({ preventScroll: true }), 450);
            }, 100);
        });
    </script>
    @endscript
</div>

