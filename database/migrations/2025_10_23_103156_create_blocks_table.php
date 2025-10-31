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
        Schema::create('blocks', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('page_id')->nullable()->constrained()->cascadeOnDelete();
                    $table->foreignId('parent_id')->nullable()->constrained('blocks')->nullOnDelete();
                    $table->string('type');          // e.g. hero, program_overview, curriculum, pricing_table, testimonials, faq, closing_cta...
                    $table->string('variant')->nullable();
                    $table->json('data')->nullable(); // block props/content
                    $table->integer('order')->default(0);
                    $table->boolean('is_published')->default(true);
                    $table->string('visibility')->nullable(); // public, logged_in, role:admin
                    $table->timestamps();

                    $table->index(['page_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
