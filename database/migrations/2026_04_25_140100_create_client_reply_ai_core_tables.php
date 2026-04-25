<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('mode')->default('quick');
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('role');
            $table->string('message_type');
            $table->longText('input_text')->nullable();
            $table->longText('output_text')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['chat_id', 'created_at']);
            $table->index(['role', 'message_type']);
        });

        Schema::create('saved_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chat_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('source_message_id')->nullable()->constrained('chat_messages')->nullOnDelete();
            $table->string('title')->nullable();
            $table->longText('reply_text');
            $table->boolean('is_favorite')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_favorite']);
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('use_case')->nullable();
            $table->string('tone')->nullable();
            $table->string('language')->nullable();
            $table->text('prompt_hint')->nullable();
            $table->longText('content');
            $table->boolean('is_system')->default(true);
            $table->timestamps();

            $table->index(['category', 'use_case']);
        });

        Schema::create('usage_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('usage_date');
            $table->unsignedInteger('replies_generated')->default(0);
            $table->unsignedInteger('saved_replies_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'usage_date']);
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('profession')->nullable();
            $table->string('preferred_tone')->nullable();
            $table->string('default_use_case')->nullable();
            $table->string('default_language')->nullable();
            $table->text('signature')->nullable();
            $table->json('writing_preferences')->nullable();
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->nullable();
            $table->string('provider_subscription_id')->nullable();
            $table->string('status')->default('inactive');
            $table->string('plan_name')->default('free');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_subscription_id']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('usage_limits');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('saved_replies');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chats');
    }
};
