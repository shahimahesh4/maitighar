<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->id }} - Maitighar</title>
    <!-- Tailwind CSS (CDN for printing style compatibility) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white;
                color: black;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border: 1px solid #e2e8f0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased p-4 sm:p-8">

    <div class="max-w-4xl mx-auto bg-white border border-slate-200 rounded-3xl shadow-sm p-6 sm:p-10 relative print-border">
        <!-- Floating Action Button -->
        <div class="absolute top-6 right-6 no-print flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-[#EC0205] hover:bg-[#C90204] text-white font-semibold text-xs rounded-xl shadow-md transition duration-250 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-12-6h13.44m-15 3h16.56a1.5 1.5 0 001.5-1.5V6.72a1.5 1.5 0 00-1.5-1.5H3.72A1.5 1.5 0 002.22 6.72v6.6a1.5 1.5 0 001.5 1.5z"/>
                </svg>
                Print Invoice
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition duration-250">
                Close
            </button>
        </div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Maitighar" class="h-12 w-12 rounded-xl object-contain shadow-sm">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-[#EC0205]">Maitighar</h1>
                    <p class="text-xs text-slate-400 mt-1">Nepalese Heritage with Love</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ \App\Models\Setting::get('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144') }}</p>
                </div>
            </div>
            <div class="text-left sm:text-right">
                <h2 class="text-lg font-bold text-slate-800">INVOICE</h2>
                <p class="text-xs text-slate-400 mt-1">Order #{{ $order->id }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Date: {{ $order->created_at->format('F d, Y') }}</p>
            </div>
        </div>

        <!-- Client & Order Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-8 border-b border-slate-100 text-left">
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Billing & Shipping Address</h3>
                @if ($order->address)
                    <p class="font-bold text-slate-800 text-sm">{{ $order->address->first_name }} {{ $order->address->last_name }}</p>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        {{ $order->address->street_address }},<br>
                        {{ $order->address->city }}, {{ $order->address->state }} - {{ $order->address->zip_code }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1.5">Phone: {{ $order->address->phone }}</p>
                @else
                    <p class="font-bold text-slate-800 text-sm">{{ $order->user->name }}</p>
                    <p class="text-xs text-slate-500 mt-1">Email: {{ $order->user->email }}</p>
                @endif
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Payment & Delivery</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Payment Method:</span>
                        <span class="font-semibold text-slate-700 uppercase">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Payment Status:</span>
                        <span class="font-semibold rounded-full px-2 py-0.5 {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} uppercase text-[10px]">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Shipping Method:</span>
                        <span class="font-semibold text-slate-700 uppercase">{{ str_replace('_', ' ', $order->shipping_method ?? 'standard') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Currency:</span>
                        <span class="font-bold text-slate-700 uppercase">{{ strtoupper($order->currency ?: 'AUD') }} (Australian Dollars)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Order Status:</span>
                        <span class="font-semibold text-slate-700 uppercase">{{ $order->status }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="py-8">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase font-bold">
                        <th class="py-3 font-semibold">Product Description</th>
                        <th class="py-3 text-center font-semibold">SKU</th>
                        <th class="py-3 text-right font-semibold">Unit Price</th>
                        <th class="py-3 text-center font-semibold">Qty</th>
                        <th class="py-3 text-right font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="py-4">
                                <span class="font-bold text-slate-800 text-sm block">{{ $item->product->name ?? 'Product' }}</span>
                                @if($item->options_text)
                                    <span class="text-xs text-slate-500 block mt-0.5 font-medium">{{ $item->options_text }}</span>
                                @endif
                                <span class="text-[10px] text-slate-400 mt-0.5 block">{{ $item->product->brand->name ?? 'Brand' }}</span>
                            </td>
                            <td class="py-4 text-center font-mono text-[11px] text-slate-500">{{ $item->variation_sku ?: ($item->product->sku ?? '-') }}</td>
                            <td class="py-4 text-right">{{ \App\Support\Money::format($item->unit_amount) }}</td>
                            <td class="py-4 text-center font-semibold text-slate-800">{{ $item->quantity }}</td>
                            <td class="py-4 text-right font-bold text-slate-800">{{ \App\Support\Money::format($item->total_amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-slate-150">
            <div>
                @if ($order->notes)
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Order Notes</h4>
                    <p class="text-xs text-slate-500 italic leading-relaxed">{{ $order->notes }}</p>
                @endif
            </div>
            <div class="space-y-3 text-xs text-left sm:text-right">
                @php
                    $subtotal = $order->items->sum('total_amount');
                @endphp
                <div class="flex justify-between sm:justify-end sm:gap-12">
                    <span class="text-slate-400">Subtotal:</span>
                    <span class="font-medium text-slate-800 w-24 block text-right">{{ \App\Support\Money::format($subtotal) }}</span>
                </div>
                
                @if ($order->discount_amount > 0)
                    <div class="flex justify-between sm:justify-end sm:gap-12">
                        <span class="text-slate-400">Discount ({{ $order->coupon_code }}):</span>
                        <span class="font-semibold text-[#EC0205] w-24 block text-right">-{{ \App\Support\Money::format($order->discount_amount) }}</span>
                    </div>
                @endif

                <div class="flex justify-between sm:justify-end sm:gap-12">
                    <span class="text-slate-400">Shipping:</span>
                    <span class="font-semibold text-emerald-600 w-24 block text-right">
                        {{ \App\Support\Money::format($order->shipping_amount) }}
                    </span>
                </div>

                <div class="flex justify-between sm:justify-end sm:gap-12 pt-3 border-t border-slate-100 items-center">
                    <span class="text-sm font-bold text-slate-800">Total Due (AUD):</span>
                    <span class="text-lg font-black text-[#EC0205] w-36 block text-right">
                        {{ \App\Support\Money::format($order->grand_total, $order->currency) }} {{ strtoupper($order->currency ?: 'AUD') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-6 border-t border-slate-100 text-center text-[10px] text-slate-400">
            <p>Thank you for shopping at Maitighar!</p>
            <p class="mt-1">For support or queries, contact us at {{ \App\Models\Setting::get('store_email', 'maitigharaustralia@gmail.com') }} | {{ \App\Models\Setting::get('store_phone', '+61 432 685 546') }}</p>
        </div>
    </div>

</body>
</html>
