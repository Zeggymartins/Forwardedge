<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_content_access_logs', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('message');
            $table->timestamp('last_accessed_at')->nullable()->after('expires_at');
            $table->integer('access_count')->default(0)->after('last_accessed_at');
            $table->string('ip_address')->nullable()->after('access_count');
            $table->text('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('course_content_access_logs', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'last_accessed_at', 'access_count', 'ip_address', 'user_agent']);
        });
    }
};
