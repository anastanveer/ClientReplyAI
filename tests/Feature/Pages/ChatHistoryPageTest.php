<?php

namespace Tests\Feature\Pages;

use App\Livewire\Pages\ChatHistoryPage;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatHistoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_history_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ChatHistoryPage::class)
            ->assertOk();
    }

    public function test_chat_history_page_requires_authentication(): void
    {
        $this->get(route('chat-history'))
            ->assertRedirect(route('login'));
    }

    public function test_it_shows_user_chats(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Chat::query()->create([
            'user_id' => $user->id,
            'title' => 'Follow-up on pending payment',
            'mode' => 'quick',
            'last_message_at' => now(),
        ]);

        Livewire::test(ChatHistoryPage::class)
            ->assertSee('Follow-up on pending payment');
    }

    public function test_it_does_not_show_other_users_chats(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user);

        Chat::query()->create([
            'user_id' => $other->id,
            'title' => 'Private session',
            'mode' => 'quick',
            'last_message_at' => now(),
        ]);

        Livewire::test(ChatHistoryPage::class)
            ->assertDontSee('Private session');
    }

    public function test_it_shows_empty_state_when_no_chats(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ChatHistoryPage::class)
            ->assertSee('No chat history yet');
    }

    public function test_it_groups_chats_by_date(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Chat::query()->create([
            'user_id' => $user->id,
            'title' => 'Todays session',
            'mode' => 'quick',
            'last_message_at' => now(),
        ]);

        Chat::query()->create([
            'user_id' => $user->id,
            'title' => 'Old session',
            'mode' => 'advanced',
            'last_message_at' => now()->subDays(30),
        ]);

        Livewire::test(ChatHistoryPage::class)
            ->assertSee('Today')
            ->assertSee('Older');
    }
}
