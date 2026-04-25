<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\WritingPreferencesForm;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WritingPreferencesFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_requires_authentication(): void
    {
        $this->get(route('settings'))
            ->assertRedirect(route('login'));
    }

    public function test_settings_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(WritingPreferencesForm::class)
            ->assertOk();
    }

    public function test_form_loads_empty_when_no_profile_exists(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(WritingPreferencesForm::class)
            ->assertSet('profession', '')
            ->assertSet('preferredTone', '')
            ->assertSet('defaultUseCase', '')
            ->assertSet('defaultLanguage', '')
            ->assertSet('signature', '')
            ->assertSet('extraInstruction', '');
    }

    public function test_form_loads_existing_profile_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserProfile::query()->create([
            'user_id' => $user->id,
            'profession' => 'Freelance Developer',
            'preferred_tone' => 'Professional',
            'default_use_case' => 'Payment Reminder',
            'default_language' => 'English Improvement',
            'signature' => 'Best regards, Dev',
            'writing_preferences' => ['instruction' => 'Keep replies short.'],
        ]);

        Livewire::test(WritingPreferencesForm::class)
            ->assertSet('profession', 'Freelance Developer')
            ->assertSet('preferredTone', 'Professional')
            ->assertSet('defaultUseCase', 'Payment Reminder')
            ->assertSet('defaultLanguage', 'English Improvement')
            ->assertSet('signature', 'Best regards, Dev')
            ->assertSet('extraInstruction', 'Keep replies short.');
    }

    public function test_save_creates_new_profile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(WritingPreferencesForm::class)
            ->set('profession', 'Account Manager')
            ->set('preferredTone', 'Friendly')
            ->set('defaultUseCase', 'Email Reply')
            ->set('defaultLanguage', 'English Improvement')
            ->call('save')
            ->assertSet('saved', true);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'profession' => 'Account Manager',
            'preferred_tone' => 'Friendly',
            'default_use_case' => 'Email Reply',
        ]);
    }

    public function test_save_updates_existing_profile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        UserProfile::query()->create([
            'user_id' => $user->id,
            'profession' => 'Old Profession',
            'preferred_tone' => 'Polite',
        ]);

        Livewire::test(WritingPreferencesForm::class)
            ->set('profession', 'Updated Profession')
            ->set('preferredTone', 'Confident')
            ->call('save')
            ->assertSet('saved', true);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'profession' => 'Updated Profession',
            'preferred_tone' => 'Confident',
        ]);

        $this->assertCount(1, UserProfile::where('user_id', $user->id)->get());
    }

    public function test_save_stores_extra_instruction_in_writing_preferences_json(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(WritingPreferencesForm::class)
            ->set('extraInstruction', 'Never use formal salutations.')
            ->call('save');

        $profile = UserProfile::where('user_id', $user->id)->first();

        $this->assertNotNull($profile);
        $this->assertEquals(['instruction' => 'Never use formal salutations.'], $profile->writing_preferences);
    }

    public function test_save_stores_null_writing_preferences_when_instruction_is_empty(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(WritingPreferencesForm::class)
            ->set('extraInstruction', '')
            ->call('save');

        $profile = UserProfile::where('user_id', $user->id)->first();

        $this->assertNull($profile->writing_preferences);
    }

    public function test_empty_selects_store_null_not_empty_string(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(WritingPreferencesForm::class)
            ->set('preferredTone', '')
            ->set('defaultUseCase', '')
            ->set('defaultLanguage', '')
            ->call('save');

        $profile = UserProfile::where('user_id', $user->id)->first();

        $this->assertNull($profile->preferred_tone);
        $this->assertNull($profile->default_use_case);
        $this->assertNull($profile->default_language);
    }

    public function test_profession_max_length_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(WritingPreferencesForm::class)
            ->set('profession', str_repeat('a', 101))
            ->call('save')
            ->assertHasErrors(['profession']);
    }

    public function test_signature_max_length_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(WritingPreferencesForm::class)
            ->set('signature', str_repeat('x', 501))
            ->call('save')
            ->assertHasErrors(['signature']);
    }
}
