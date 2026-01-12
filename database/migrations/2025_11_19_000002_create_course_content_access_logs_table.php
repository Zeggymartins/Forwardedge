<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_content_access_logs')) {
            return;
        }

        Schema::create('course_content_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_content_id')->constrained('course_contents')->cascadeOnDelete();
            $table->string('email');
            $table->string('provider')->default('google_drive');
            $table->string('status')->default('pending');
            $table->text('message')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->integer('access_count')->default(0);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_content_access_logs');
    }
};
