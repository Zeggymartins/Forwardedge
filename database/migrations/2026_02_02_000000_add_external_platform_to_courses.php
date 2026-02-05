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
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('status');
            $table->string('external_platform_name')->nullable()->after('is_external');
            $table->text('external_course_url')->nullable()->after('external_platform_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'external_platform_name', 'external_course_url']);
        });
    }
};
