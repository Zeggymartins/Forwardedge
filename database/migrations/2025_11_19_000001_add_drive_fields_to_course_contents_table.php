<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            $table->string('drive_folder_id')->nullable()->after('file_path');
            $table->text('drive_share_link')->nullable()->after('drive_folder_id');
            $table->boolean('auto_grant_access')->default(false)->after('drive_share_link');
        });
    }

    public function down(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            $table->dropColumn(['drive_folder_id', 'drive_share_link', 'auto_grant_access']);
        });
    }
};
