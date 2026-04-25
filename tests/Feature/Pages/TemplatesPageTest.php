<?php

namespace Tests\Feature\Pages;

use App\Livewire\Pages\TemplatesPage;
use App\Models\Template;
use App\Models\User;
use Database\Seeders\TemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TemplatesPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TemplateSeeder::class);
    }

    public function test_templates_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(TemplatesPage::class)
            ->assertOk();
    }

    public function test_templates_page_requires_authentication(): void
    {
        $this->get(route('templates'))
            ->assertRedirect(route('login'));
    }

    public function test_it_shows_all_seeded_templates_by_default(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(TemplatesPage::class)
            ->assertSee('Polite Payment Reminder')
            ->assertSee('Fiverr Delivery Follow-Up')
            ->assertSee('Recruiter Availability Reply');
    }

    public function test_it_filters_templates_by_category(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(TemplatesPage::class)
            ->call('setCategory', 'Payments')
            ->assertSet('activeCategory', 'Payments')
            ->assertSee('Polite Payment Reminder')
            ->assertDontSee('Fiverr Delivery Follow-Up');
    }

    public function test_it_shows_all_templates_after_resetting_to_all(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(TemplatesPage::class)
            ->call('setCategory', 'Jobs')
            ->call('setCategory', 'all')
            ->assertSee('Polite Payment Reminder')
            ->assertSee('Recruiter Availability Reply');
    }

    public function test_use_template_stores_session_and_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $template = Template::where('slug', 'polite-payment-reminder')->first();

        Livewire::test(TemplatesPage::class)
            ->call('useTemplate', $template->id)
            ->assertRedirect(route('dashboard'));

        $sessionData = session('apply_template');

        $this->assertNotNull($sessionData);
        $this->assertEquals($template->content, $sessionData['content']);
        $this->assertEquals('Payment Reminder', $sessionData['use_case']);
        $this->assertEquals('Polite', $sessionData['tone']);
        $this->assertEquals('English Improvement', $sessionData['language']);
    }

    public function test_template_use_case_and_tone_are_workspace_compatible(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $validTones = ['Professional', 'Friendly', 'Polite', 'Confident', 'Strong', 'Soft', 'Direct', 'Respectful', 'Short', 'Detailed'];
        $validUseCases = [
            'Fiverr Client Reply', 'WhatsApp Business Reply', 'Email Reply', 'Job Recruiter Reply',
            'LinkedIn Reply', 'Proposal Reply', 'Complaint Reply', 'Apology Reply', 'Follow-up Reply',
            'Negotiation Reply', 'Project Update Reply', 'Payment Reminder', 'Delay Update',
            'Asking for Requirements', 'Asking for Access', 'Review Request', 'Support Response',
            'Friendly Daily Reply', 'Translation Improvement', 'General Reply',
        ];
        $validLanguages = [
            'English Improvement', 'Broken English to Professional English',
            'Roman Urdu to Professional English', 'Urdu to English', 'Hindi to English',
            'English to German', 'English to French', 'English to Arabic',
        ];

        Template::all()->each(function (Template $template) use ($validTones, $validUseCases, $validLanguages): void {
            if ($template->tone) {
                $this->assertContains($template->tone, $validTones, "Template [{$template->slug}] has invalid tone: {$template->tone}");
            }
            if ($template->use_case) {
                $this->assertContains($template->use_case, $validUseCases, "Template [{$template->slug}] has invalid use_case: {$template->use_case}");
            }
            if ($template->language) {
                $this->assertContains($template->language, $validLanguages, "Template [{$template->slug}] has invalid language: {$template->language}");
            }
        });
    }
}
