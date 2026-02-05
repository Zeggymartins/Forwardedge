<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            $table->string('delivery_mode', 20)->default('local')->after('type');
        });

        // Backfill: existing rows with a drive_folder_id are "drive" mode
        \DB::table('course_contents')
            ->whereNotNull('drive_folder_id')
            ->where('drive_folder_id', '!=', '')
            ->update(['delivery_mode' => 'drive']);
    }

    public function down(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            $table->dropColumn('delivery_mode');
        });
    }
};
