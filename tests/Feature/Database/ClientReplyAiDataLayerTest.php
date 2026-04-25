<?php

namespace Tests\Feature\Database;

use Database\Seeders\TemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClientReplyAiDataLayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_tables_and_columns_exist(): void
    {
        $this->assertTrue(Schema::hasTable('chats'));
        $this->assertTrue(Schema::hasTable('chat_messages'));
        $this->assertTrue(Schema::hasTable('saved_replies'));
        $this->assertTrue(Schema::hasTable('templates'));
        $this->assertTrue(Schema::hasTable('usage_limits'));
        $this->assertTrue(Schema::hasTable('user_profiles'));
        $this->assertTrue(Schema::hasTable('subscriptions'));

        $this->assertTrue(Schema::hasColumns('users', [
            'plan',
            'timezone',
        ]));
    }

    public function test_template_seeder_creates_system_templates(): void
    {
        $this->seed(TemplateSeeder::class);

        $this->assertDatabaseCount('templates', 8);
        $this->assertDatabaseHas('templates', [
            'slug' => 'polite-payment-reminder',
            'is_system' => true,
        ]);
        $this->assertDatabaseHas('templates', [
            'slug' => 'roman-urdu-to-english-upgrade',
            'category' => 'Language',
        ]);
    }
}
