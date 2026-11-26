<div class="space-y-5">
    <div class="rounded-xl border border-primary-100 bg-primary-50 p-4">
        <p class="text-sm font-semibold text-gray-500">Customer email</p>
        <a href="mailto:{{ $record->email }}" class="mt-1 inline-flex items-center gap-2 text-base font-bold text-primary-700 hover:underline">
            <x-filament::icon icon="heroicon-m-envelope" class="h-5 w-5" />
            {{ $record->email }}
        </a>
    </div>

    <dl class="grid gap-4 sm:grid-cols-2">
        <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</dt><dd class="mt-1 font-semibold text-gray-950">{{ $record->full_name }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Phone</dt><dd class="mt-1 font-semibold text-gray-950">{{ $record->phone }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Product</dt><dd class="mt-1 font-semibold text-gray-950">{{ $record->product->name }}</dd></div>
        <div><dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Quantity</dt><dd class="mt-1 font-semibold text-gray-950">{{ $record->quantity }}</dd></div>
    </dl>

    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Customer message</p>
        <p class="mt-2 whitespace-pre-line rounded-xl bg-gray-50 p-4 text-sm leading-6 text-gray-700">{{ $record->message ?: 'No additional message was provided.' }}</p>
    </div>
</div>
