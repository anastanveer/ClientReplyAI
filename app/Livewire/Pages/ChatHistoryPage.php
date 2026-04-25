<?php

namespace App\Livewire\Pages;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ChatHistoryPage extends Component
{
    public function render(): View
    {
        return view('livewire.pages.chat-history-page', [
            'groupedChats' => $this->groupedChats(),
        ]);
    }

    protected function groupedChats(): Collection
    {
        return auth()->user()
            ->chats()
            ->latest('last_message_at')
            ->take(50)
            ->get()
            ->groupBy(fn ($chat) => $this->groupLabel($chat->last_message_at));
    }

    protected function groupLabel(?Carbon $dt): string
    {
        if ($dt === null) {
            return 'Older';
        }

        if ($dt->isToday()) {
            return 'Today';
        }

        if ($dt->isYesterday()) {
            return 'Yesterday';
        }

        if ($dt->greaterThanOrEqualTo(now()->subDays(7))) {
            return 'Previous 7 Days';
        }

        return 'Older';
    }
}
