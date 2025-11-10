<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_campaign_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->enum('status', ['pending', 'sending', 'sent', 'skipped', 'failed'])->default('pending');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['email_campaign_id', 'email']);
            $table->index(['email_campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_campaign_recipients');
    }
};
