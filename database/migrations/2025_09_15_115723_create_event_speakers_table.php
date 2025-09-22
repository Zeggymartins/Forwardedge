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
        Schema::create('event_speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('company')->nullable();
            $table->text('bio')->nullable();
            $table->string('image')->nullable();
            $table->string('email')->nullable();
            $table->json('social_links')->nullable(); // {twitter, linkedin, website}
            $table->boolean('is_keynote')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_speakers');
    }
};
