<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
public function up(): void
    {
        Schema::dropIfExists('scholarships');
    }

    public function down(): void
    {
        // Optional: re-create table if rolled back
        Schema::create('scholarships', function ($table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('status')->default('draft');

            $table->string('headline')->nullable();
            $table->text('subtext')->nullable();
            $table->string('image')->nullable();
            $table->string('text')->nullable();
            $table->string('cta_url')->nullable();

            $table->longText('about')->nullable();
            $table->json('program_includes')->nullable();
            $table->json('who_can_apply')->nullable();
            $table->json('how_to_apply')->nullable();
            $table->longText('important_note')->nullable();
            $table->string('closing_headline')->nullable();
            $table->string('closing_cta_text')->nullable();
            $table->string('closing_cta_url')->nullable();

            $table->date('opens_at')->nullable();
            $table->date('closes_at')->nullable();
            $table->timestamps();
        });
    }
};
