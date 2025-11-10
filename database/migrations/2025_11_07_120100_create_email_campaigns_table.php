<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subject');
            $table->string('subtitle')->nullable();
            $table->string('hero_image')->nullable();
            $table->text('intro')->nullable();
            $table->json('blocks')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_link')->nullable();
            $table->enum('status', ['draft', 'sending', 'completed', 'failed'])->default('draft');
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('total_count')->default(0);
            $table->text('last_error')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
