<?php

namespace App\Livewire\Pages;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class SavedRepliesPage extends Component
{
    public ?int $confirmDeleteId = null;

    public function toggleFavorite(int $id): void
    {
        $reply = auth()->user()->savedReplies()->findOrFail($id);
        $reply->update(['is_favorite' => ! $reply->is_favorite]);
    }

    public function reuseReply(int $id): void
    {
        $reply = auth()->user()->savedReplies()->findOrFail($id);
        session()->put('reuse_reply_text', $reply->reply_text);
        $this->redirect(route('dashboard'));
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function deleteReply(int $id): void
    {
        auth()->user()->savedReplies()->where('id', $id)->delete();
        $this->confirmDeleteId = null;
    }

    public function render(): View
    {
        $user = auth()->user();

        return view('livewire.pages.saved-replies-page', [
            'favorites' => $user->savedReplies()
                ->where('is_favorite', true)
                ->latest()
                ->get(),
            'allReplies' => $user->savedReplies()
                ->latest()
                ->get(),
        ]);
    }
}
