<?php

namespace Tests\Feature\Pages;

use App\Livewire\Pages\SavedRepliesPage;
use App\Models\Chat;
use App\Models\SavedReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SavedRepliesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_replies_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(SavedRepliesPage::class)
            ->assertOk();
    }

    public function test_saved_replies_page_requires_authentication(): void
    {
        $this->get(route('saved-replies'))
            ->assertRedirect(route('login'));
    }

    public function test_it_shows_user_saved_replies(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $chat = Chat::query()->create([
            'user_id' => $user->id,
            'title' => 'Test session',
            'mode' => 'quick',
        ]);

        SavedReply::query()->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'title' => 'Payment reminder reply',
            'reply_text' => 'Hi, I wanted to follow up on the outstanding payment.',
            'is_favorite' => false,
        ]);

        Livewire::test(SavedRepliesPage::class)
            ->assertSee('Payment reminder reply')
            ->assertSee('Hi, I wanted to follow up');
    }

    public function test_it_does_not_show_other_users_replies(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user);

        SavedReply::query()->create([
            'user_id' => $other->id,
            'title' => 'Secret reply',
            'reply_text' => 'This belongs to another user.',
            'is_favorite' => false,
        ]);

        Livewire::test(SavedRepliesPage::class)
            ->assertDontSee('Secret reply');
    }

    public function test_it_can_toggle_favorite(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $reply = SavedReply::query()->create([
            'user_id' => $user->id,
            'title' => 'Test reply',
            'reply_text' => 'Some reply text.',
            'is_favorite' => false,
        ]);

        Livewire::test(SavedRepliesPage::class)
            ->call('toggleFavorite', $reply->id);

        $this->assertTrue($reply->fresh()->is_favorite);
    }

    public function test_it_can_unfavorite_a_reply(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $reply = SavedReply::query()->create([
            'user_id' => $user->id,
            'title' => 'Favorite reply',
            'reply_text' => 'Some reply text.',
            'is_favorite' => true,
        ]);

        Livewire::test(SavedRepliesPage::class)
            ->call('toggleFavorite', $reply->id);

        $this->assertFalse($reply->fresh()->is_favorite);
    }

    public function test_it_can_delete_a_reply(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $reply = SavedReply::query()->create([
            'user_id' => $user->id,
            'title' => 'Delete me',
            'reply_text' => 'To be deleted.',
            'is_favorite' => false,
        ]);

        Livewire::test(SavedRepliesPage::class)
            ->call('confirmDelete', $reply->id)
            ->assertSet('confirmDeleteId', $reply->id)
            ->call('deleteReply', $reply->id)
            ->assertSet('confirmDeleteId', null);

        $this->assertNull(SavedReply::find($reply->id));
    }

    public function test_it_cannot_delete_another_users_reply(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user);

        $reply = SavedReply::query()->create([
            'user_id' => $other->id,
            'title' => 'Other users reply',
            'reply_text' => 'Protected.',
            'is_favorite' => false,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test(SavedRepliesPage::class)
            ->call('reuseReply', $reply->id);
    }

    public function test_reuse_reply_stores_in_session_and_redirects(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $reply = SavedReply::query()->create([
            'user_id' => $user->id,
            'title' => 'Reusable reply',
            'reply_text' => 'Hi, following up on the payment.',
            'is_favorite' => false,
        ]);

        Livewire::test(SavedRepliesPage::class)
            ->call('reuseReply', $reply->id)
            ->assertRedirect(route('dashboard'));

        $this->assertEquals('Hi, following up on the payment.', session('reuse_reply_text'));
    }
}
