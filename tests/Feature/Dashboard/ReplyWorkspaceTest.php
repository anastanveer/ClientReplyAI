<?php

namespace Tests\Feature\Dashboard;

use App\Livewire\Dashboard\ReplyWorkspace;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\SavedReply;
use App\Models\Template;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\Replies\DTOs\GeneratedReplyData;
use App\Services\Replies\ReplyGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class ReplyWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_validates_the_composer_input(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ReplyWorkspace::class)
            ->set('composer', '')
            ->call('generateReply')
            ->assertHasErrors(['composer' => 'required']);
    }

    public function test_it_sets_the_generated_reply_on_success(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'timezone' => 'UTC',
        ]);
        $this->actingAs($user);

        $chat = $user->chats()->create([
            'title' => 'Payment reminder',
            'mode' => 'quick',
            'last_message_at' => now(),
        ]);

        $userMessage = ChatMessage::query()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'role' => 'user',
            'message_type' => 'raw_input',
            'input_text' => 'Please ask client for payment',
            'meta' => [],
        ]);

        $assistantMessage = ChatMessage::query()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'role' => 'assistant',
            'message_type' => 'generated_reply',
            'output_text' => 'Hi, just following up on the pending payment.',
            'meta' => [],
        ]);

        $service = Mockery::mock(ReplyGenerationService::class);
        $service->shouldReceive('generate')
            ->once()
            ->andReturn(new GeneratedReplyData(
                chat: $chat,
                userMessage: $userMessage,
                assistantMessage: $assistantMessage,
                bestReply: 'Hi, just following up on the pending payment.',
                riskNote: null,
                coachNote: null,
                nextStep: null,
                variants: [],
                provider: 'gemini',
                model: 'gemini-test-model',
                finishReason: 'STOP',
                usage: ['total_tokens' => 40],
                usedFallback: false,
            ));

        $this->app->instance(ReplyGenerationService::class, $service);

        Livewire::test(ReplyWorkspace::class)
            ->set('composer', 'Please ask client for payment')
            ->call('generateReply')
            ->assertSet('bestReply', 'Hi, just following up on the pending payment.')
            ->assertSet('providerStatus', 'Generated with Gemini.')
            ->assertSet('currentChatId', $chat->id)
            ->assertSet('errorMessage', null);
    }

    public function test_it_saves_the_reply_to_saved_replies(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'timezone' => 'UTC',
        ]);
        $this->actingAs($user);

        $chat = $user->chats()->create([
            'title' => 'Payment reminder',
            'mode' => 'quick',
            'last_message_at' => now(),
        ]);

        $userMessage = ChatMessage::query()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'role' => 'user',
            'message_type' => 'raw_input',
            'input_text' => 'Please ask client for payment',
            'meta' => [],
        ]);

        $assistantMessage = ChatMessage::query()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'role' => 'assistant',
            'message_type' => 'generated_reply',
            'output_text' => 'Hi, just following up on the pending payment.',
            'meta' => [],
        ]);

        $service = Mockery::mock(ReplyGenerationService::class);
        $service->shouldReceive('generate')
            ->once()
            ->andReturn(new GeneratedReplyData(
                chat: $chat,
                userMessage: $userMessage,
                assistantMessage: $assistantMessage,
                bestReply: 'Hi, just following up on the pending payment.',
                riskNote: null,
                coachNote: null,
                nextStep: null,
                variants: [],
                provider: 'gemini',
                model: 'gemini-test-model',
                finishReason: 'STOP',
                usage: ['total_tokens' => 40],
                usedFallback: false,
            ));

        $this->app->instance(ReplyGenerationService::class, $service);

        Livewire::test(ReplyWorkspace::class)
            ->set('composer', 'Please ask client for payment')
            ->call('generateReply')
            ->assertSet('savedReplyId', null)
            ->call('saveReply')
            ->assertSet('savedReplyId', fn ($value) => $value !== null);

        $this->assertDatabaseHas('saved_replies', [
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'reply_text' => 'Hi, just following up on the pending payment.',
        ]);
    }

    public function test_save_reply_is_idempotent(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'timezone' => 'UTC',
        ]);
        $this->actingAs($user);

        $chat = $user->chats()->create([
            'title' => 'Test',
            'mode' => 'quick',
            'last_message_at' => now(),
        ]);

        $userMessage = ChatMessage::query()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'role' => 'user',
            'message_type' => 'raw_input',
            'input_text' => 'Test input',
            'meta' => [],
        ]);

        $assistantMessage = ChatMessage::query()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'role' => 'assistant',
            'message_type' => 'generated_reply',
            'output_text' => 'Test reply.',
            'meta' => [],
        ]);

        $service = Mockery::mock(ReplyGenerationService::class);
        $service->shouldReceive('generate')
            ->once()
            ->andReturn(new GeneratedReplyData(
                chat: $chat,
                userMessage: $userMessage,
                assistantMessage: $assistantMessage,
                bestReply: 'Test reply.',
                riskNote: null,
                coachNote: null,
                nextStep: null,
                variants: [],
                provider: 'gemini',
                model: 'gemini-test-model',
                finishReason: 'STOP',
                usage: ['total_tokens' => 20],
                usedFallback: false,
            ));

        $this->app->instance(ReplyGenerationService::class, $service);

        Livewire::test(ReplyWorkspace::class)
            ->set('composer', 'Test input')
            ->call('generateReply')
            ->call('saveReply')
            ->call('saveReply');

        $this->assertCount(1, SavedReply::where('user_id', $user->id)->get());
    }

    public function test_mount_fills_composer_from_session_reuse(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        session()->put('reuse_reply_text', 'Pre-filled reply text from session.');

        Livewire::test(ReplyWorkspace::class)
            ->assertSet('composer', 'Pre-filled reply text from session.');

        $this->assertNull(session('reuse_reply_text'));
    }

    public function test_apply_template_fills_workspace_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $template = Template::query()->create([
            'name' => 'Test Template',
            'slug' => 'test-template',
            'category' => 'Test',
            'use_case' => 'Payment Reminder',
            'tone' => 'Polite',
            'language' => 'English Improvement',
            'content' => 'Hi, please process the payment today.',
            'is_system' => true,
        ]);

        Livewire::test(ReplyWorkspace::class)
            ->call('applyTemplate', $template->id)
            ->assertSet('composer', 'Hi, please process the payment today.')
            ->assertSet('tone', 'Polite')
            ->assertSet('useCase', 'Payment Reminder')
            ->assertSet('language', 'English Improvement');
    }

    public function test_apply_template_ignores_unknown_id(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ReplyWorkspace::class)
            ->call('applyTemplate', 99999)
            ->assertSet('composer', fn ($value) => $value !== '');
    }

    public function test_mount_applies_template_from_session(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        session()->put('apply_template', [
            'content' => 'Template content pre-filled.',
            'use_case' => 'Complaint Reply',
            'tone' => 'Soft',
            'language' => 'English Improvement',
        ]);

        Livewire::test(ReplyWorkspace::class)
            ->assertSet('composer', 'Template content pre-filled.')
            ->assertSet('useCase', 'Complaint Reply')
            ->assertSet('tone', 'Soft');

        $this->assertNull(session('apply_template'));
    }

    public function test_mount_loads_profile_defaults_when_no_session_override(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserProfile::query()->create([
            'user_id' => $user->id,
            'preferred_tone' => 'Friendly',
            'default_use_case' => 'WhatsApp Business Reply',
            'default_language' => 'Roman Urdu to Professional English',
        ]);

        Livewire::test(ReplyWorkspace::class)
            ->assertSet('tone', 'Friendly')
            ->assertSet('useCase', 'WhatsApp Business Reply')
            ->assertSet('language', 'Roman Urdu to Professional English');
    }

    public function test_template_session_overrides_profile_defaults(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserProfile::query()->create([
            'user_id' => $user->id,
            'preferred_tone' => 'Friendly',
            'default_use_case' => 'WhatsApp Business Reply',
        ]);

        session()->put('apply_template', [
            'content' => 'Template overrides profile.',
            'use_case' => 'Payment Reminder',
            'tone' => 'Polite',
            'language' => 'English Improvement',
        ]);

        Livewire::test(ReplyWorkspace::class)
            ->assertSet('tone', 'Polite')
            ->assertSet('useCase', 'Payment Reminder');
    }

    public function test_mount_uses_hardcoded_defaults_when_profile_has_no_preferences(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserProfile::query()->create([
            'user_id' => $user->id,
            'profession' => 'Developer',
        ]);

        Livewire::test(ReplyWorkspace::class)
            ->assertSet('tone', 'Professional')
            ->assertSet('useCase', 'Payment Reminder')
            ->assertSet('language', 'English Improvement');
    }
}
