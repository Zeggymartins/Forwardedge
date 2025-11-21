<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_content_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_content_id')->constrained('course_contents')->cascadeOnDelete();
            $table->string('email');
            $table->string('provider')->default('google_drive');
            $table->string('status')->default('pending');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_content_access_logs');
    }
};
