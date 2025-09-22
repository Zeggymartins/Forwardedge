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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('location');
            $table->string('venue')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->string('timezone')->default('UTC');
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->enum('type', ['conference', 'workshop', 'webinar', 'seminar', 'training'])->default('conference');
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('max_attendees')->nullable();
            $table->integer('current_attendees')->default(0);
            $table->string('organizer_name')->nullable();
            $table->string('organizer_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->json('social_links')->nullable(); // {facebook, twitter, instagram, linkedin}
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['status', 'start_date']);
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
