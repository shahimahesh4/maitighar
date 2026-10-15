<?php

namespace App\Livewire;

use App\Models\SupportTicket;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Support Tickets - Maitighar')]
class SupportTicketsPage extends Component
{
    use WithPagination;

    public string $subject = '';

    public string $message = '';

    public string $priority = 'medium';

    public ?int $selectedTicketId = null;

    public function createTicket(): void
    {
        $validated = $this->validate([
            'subject' => ['required', 'string', 'min:5', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ]);

        $ticket = SupportTicket::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'open',
        ]);

        $this->reset('subject', 'message');
        $this->priority = 'medium';
        $this->selectedTicketId = $ticket->id;
        $this->resetPage();
        session()->flash('ticket_success', 'Your support ticket has been submitted successfully.');
        $this->dispatch('support-ticket-created');
    }

    public function selectTicket(int $ticketId): void
    {
        $ticket = SupportTicket::query()
            ->where('user_id', auth()->id())
            ->findOrFail($ticketId);

        $this->selectedTicketId = $ticket->id;
        $this->dispatch('support-ticket-selected');
    }

    public function render(): View
    {
        $tickets = SupportTicket::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(8);

        $selectedTicket = $this->selectedTicketId
            ? SupportTicket::query()->where('user_id', auth()->id())->find($this->selectedTicketId)
            : null;

        return view('livewire.support-tickets-page', compact('tickets', 'selectedTicket'));
    }
}
