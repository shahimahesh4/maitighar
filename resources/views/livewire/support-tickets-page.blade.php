<div class="min-h-screen bg-slate-50 py-4 sm:py-6">
    <div class="mx-auto w-[calc(100%-32px)] max-w-[1320px]">
        <nav class="mb-5 flex items-center gap-2 text-xs text-slate-500" aria-label="Breadcrumb">
            <a wire:navigate href="{{ route('dashboard') }}" class="font-semibold hover:text-[#EC0205]">Dashboard</a>
            <i class="bi bi-chevron-right text-[9px]"></i><span class="font-bold text-slate-800">Support tickets</span>
        </nav>

        <header class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#091b38] via-[#0C1E8C] to-[#071355] px-6 py-8 text-white shadow-xl shadow-slate-900/10 sm:px-9">
            <div class="relative flex items-center gap-4"><span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white/10 text-xl text-red-400"><i class="bi bi-headset"></i></span><div><p class="text-[10px] font-bold uppercase tracking-[.2em] text-red-400">Customer help desk</p><h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Support tickets</h1><p class="mt-2 text-sm text-white/70">Ask for help and follow the team’s response from your dashboard.</p></div></div>
        </header>

        @if(session('ticket_success'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700" role="status"><i class="bi bi-check-circle-fill mr-2"></i>{{ session('ticket_success') }}</div>
        @endif

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[390px_minmax(0,1fr)]">
            <section class="scroll-mt-24 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" id="new-ticket">
                <h2 class="text-lg font-extrabold text-slate-900">Create a new ticket</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">Describe the issue clearly so our team can assist you faster.</p>
                <form wire:submit="createTicket" class="mt-5 space-y-4">
                    <div><label for="ticket-subject" class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Subject</label><input id="ticket-subject" wire:model="subject" type="text" maxlength="160" placeholder="What do you need help with?" class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100">@error('subject')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label for="ticket-priority" class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Priority</label><select id="ticket-priority" wire:model="priority" class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>@error('priority')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label for="ticket-message" class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Message</label><textarea id="ticket-message" wire:model="message" rows="6" maxlength="5000" placeholder="Include order number or other helpful details…" class="w-full rounded-xl border border-slate-300 bg-white p-4 text-sm leading-6 outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100"></textarea>@error('message')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="createTicket" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#EC0205] to-[#C90204] hover:from-[#C90204] hover:to-[#A80203] px-5 text-sm font-extrabold text-white disabled:opacity-60"><span wire:loading.remove wire:target="createTicket">Submit ticket <i class="bi bi-send"></i></span><span wire:loading wire:target="createTicket">Submitting…</span></button>
                </form>
            </section>

            <div class="min-w-0 space-y-6">
                @if($selectedTicket)
                    @php($statusTone = match($selectedTicket->status) {'resolved' => 'bg-emerald-100 text-emerald-700', 'in_progress' => 'bg-amber-100 text-amber-800', 'closed' => 'bg-slate-100 text-slate-600', default => 'bg-blue-100 text-blue-700'})
                    <section id="ticket-detail" class="scroll-mt-24 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-[10px] font-bold uppercase tracking-[.18em] text-[#0C1E8C]">Ticket #{{ str_pad((string) $selectedTicket->id, 6, '0', STR_PAD_LEFT) }}</p><h2 class="mt-2 text-xl font-extrabold text-slate-900">{{ $selectedTicket->subject }}</h2></div><span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wide {{ $statusTone }}">{{ Str::headline($selectedTicket->status) }}</span></div>
                        <div class="mt-5 rounded-2xl bg-slate-50 p-4"><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Your message</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $selectedTicket->message }}</p></div>
                        <div class="mt-4 rounded-2xl border border-slate-200 p-4"><p class="text-[10px] font-bold uppercase tracking-wider text-[#0C1E8C]">Support response</p>@if($selectedTicket->admin_reply)<p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $selectedTicket->admin_reply }}</p>@else<p class="mt-2 text-sm text-slate-500">Our team has not replied yet. Please check again later.</p>@endif</div>
                    </section>
                @endif

                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-5 sm:px-6"><h2 class="text-lg font-extrabold text-slate-900">My tickets</h2><p class="mt-1 text-xs text-slate-500">Select a ticket to see its full message and response.</p></div>
                    @forelse($tickets as $ticket)
                        @php($tone = match($ticket->status) {'resolved' => 'bg-emerald-100 text-emerald-700', 'in_progress' => 'bg-amber-100 text-amber-800', 'closed' => 'bg-slate-100 text-slate-600', default => 'bg-blue-100 text-blue-700'})
                        <button type="button" wire:click="selectTicket({{ $ticket->id }})" class="flex w-full items-center gap-4 border-b border-slate-100 px-5 py-4 text-left transition last:border-0 hover:bg-slate-50 sm:px-6">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-red-50 text-[#EC0205]"><i class="bi bi-chat-left-text"></i></span><span class="min-w-0 flex-1"><strong class="block truncate text-sm text-slate-900">{{ $ticket->subject }}</strong><small class="mt-1 block text-xs text-slate-500">#{{ str_pad((string) $ticket->id, 6, '0', STR_PAD_LEFT) }} · {{ $ticket->created_at->format('d M Y') }} · {{ ucfirst($ticket->priority) }}</small></span><span class="hidden rounded-full px-3 py-1 text-[9px] font-bold uppercase sm:inline-flex {{ $tone }}">{{ Str::headline($ticket->status) }}</span><i class="bi bi-chevron-right text-slate-300"></i>
                        </button>
                    @empty
                        <div class="px-6 py-12 text-center"><i class="bi bi-chat-square-text text-3xl text-[#0C1E8C]"></i><h3 class="mt-3 font-bold text-slate-800">No support tickets yet</h3><p class="mt-1 text-sm text-slate-500">Your submitted tickets will appear here.</p></div>
                    @endforelse
                    @if($tickets->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $tickets->links() }}</div>@endif
                </section>
            </div>
        </div>
    </div>
</div>

@script
<script>
    const focusTicket = () => window.setTimeout(() => document.getElementById('ticket-detail')?.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
    $wire.on('support-ticket-created', focusTicket);
    $wire.on('support-ticket-selected', focusTicket);
</script>
@endscript
