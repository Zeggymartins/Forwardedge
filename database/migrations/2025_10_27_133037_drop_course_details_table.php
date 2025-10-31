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
        Schema::dropIfExists('course_details'); // goodbye, marketing table
    }
    public function down(): void
    {
        Schema::create('course_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['heading', 'paragraph', 'image', 'features', 'list']);
            $table->longText('content')->nullable(); // For text, json, etc.
            $table->string('image')->nullable();     // For image paths
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
};
