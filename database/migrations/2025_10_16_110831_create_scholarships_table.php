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
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('status')->default('draft'); // draft|published|archived

            // HERO
            $table->string('headline')->nullable();     // e.g. "1,000 Scholarships in Cybersecurity Foundations Training"
            $table->text('subtext')->nullable();        // short paragraph
            $table->string('image')->nullable();        // storage path (optional background)
            $table->string('text')->nullable();          // e.g. "Apply for Scholarship"
            $table->string('cta_url')->nullable();           // route or external link

            // BODY SECTIONS (JSON arrays/strings for flexibility)
            $table->longText('about')->nullable();           // rich/long paragraph
            $table->json('program_includes')->nullable();    // ["5 weeks...", "Hands-on labs", ...]
            $table->json('who_can_apply')->nullable();       // ["Beginners...", "Students...", ...]
            $table->json('how_to_apply')->nullable();        // ["Click Apply...", "Fill form...", ...]
            $table->longText('important_note')->nullable();  // a paragraph
            $table->string('closing_headline')->nullable();  // final nudge headline
            $table->string('closing_cta_text')->nullable();  // "Apply Now"
            $table->string('closing_cta_url')->nullable();

            // Optional campaign meta
            $table->date('opens_at')->nullable();
            $table->date('closes_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
