<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Drop index on 'tag' if present (SQLite needs this BEFORE column drop)
        // Default Laravel index name format: {table}_{column}_index
        DB::statement('DROP INDEX IF EXISTS "course_schedules_tag_index"');

        // 2) Drop columns one-by-one (safer for SQLite)
        $cols = ['price', 'description', 'title', 'tag', 'price_usd', 'features'];

        foreach ($cols as $col) {
            if (Schema::hasColumn('course_schedules', $col)) {
                Schema::table('course_schedules', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }

    public function down(): void
    {
        // Recreate the columns we removed (with same types & order hints)
        Schema::table('course_schedules', function (Blueprint $table) {
            // If these columns might already exist (e.g., partial rollbacks), guard with hasColumn
            if (!Schema::hasColumn('course_schedules', 'price')) {
                $table->unsignedInteger('price')->nullable()->after('course_id');
            }
        });

        Schema::table('course_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('course_schedules', 'description')) {
                $table->text('description')->nullable()->after('price');
            }
            if (!Schema::hasColumn('course_schedules', 'title')) {
                $table->string('title')->nullable()->after('course_id');
            }
            if (!Schema::hasColumn('course_schedules', 'tag')) {
                $table->string('tag', 32)->default('paid')->after('description');
            }
            if (!Schema::hasColumn('course_schedules', 'price_usd')) {
                $table->unsignedInteger('price_usd')->nullable()->after('tag');
            }
            if (!Schema::hasColumn('course_schedules', 'features')) {
                $table->json('features')->nullable()->after('price_usd');
            }
        });

        // Recreate the index on tag (after the column exists)
        DB::statement('CREATE INDEX IF NOT EXISTS "course_schedules_tag_index" ON "course_schedules" ("tag")');
    }
};
