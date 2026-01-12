<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_content_access_logs')) {
            return;
        }

        Schema::table('course_content_access_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('course_content_access_logs', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('message');
            }
            if (!Schema::hasColumn('course_content_access_logs', 'last_accessed_at')) {
                $table->timestamp('last_accessed_at')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('course_content_access_logs', 'access_count')) {
                $table->integer('access_count')->default(0)->after('last_accessed_at');
            }
            if (!Schema::hasColumn('course_content_access_logs', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('access_count');
            }
            if (!Schema::hasColumn('course_content_access_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('course_content_access_logs')) {
            return;
        }

        $columns = array_filter([
            Schema::hasColumn('course_content_access_logs', 'expires_at') ? 'expires_at' : null,
            Schema::hasColumn('course_content_access_logs', 'last_accessed_at') ? 'last_accessed_at' : null,
            Schema::hasColumn('course_content_access_logs', 'access_count') ? 'access_count' : null,
            Schema::hasColumn('course_content_access_logs', 'ip_address') ? 'ip_address' : null,
            Schema::hasColumn('course_content_access_logs', 'user_agent') ? 'user_agent' : null,
        ]);

        if ($columns) {
            Schema::table('course_content_access_logs', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
