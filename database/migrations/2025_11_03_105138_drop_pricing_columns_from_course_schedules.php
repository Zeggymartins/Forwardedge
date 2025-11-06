<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function dropTagIndex(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        try {
            if ($driver === 'sqlite') {
                DB::statement('DROP INDEX IF EXISTS "course_schedules_tag_index"');
            } elseif ($driver === 'mysql') {
                DB::statement('DROP INDEX `course_schedules_tag_index` ON `course_schedules`');
            } else {
                Schema::table('course_schedules', function (Blueprint $table) {
                    $table->dropIndex('course_schedules_tag_index');
                });
            }
        } catch (\Throwable $e) {
            // Index might already be gone; ignore.
        }
    }

    protected function createTagIndex(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        try {
            if ($driver === 'sqlite') {
                DB::statement('CREATE INDEX IF NOT EXISTS "course_schedules_tag_index" ON "course_schedules" ("tag")');
            } elseif ($driver === 'mysql') {
                DB::statement('CREATE INDEX `course_schedules_tag_index` ON `course_schedules` (`tag`)');
            } else {
                Schema::table('course_schedules', function (Blueprint $table) {
                    $table->index('tag', 'course_schedules_tag_index');
                });
            }
        } catch (\Throwable $e) {
            // Ignore if it already exists
        }
    }

    public function up(): void
    {
        // 1) Drop index on 'tag' if present (SQLite needs this BEFORE column drop)
        $this->dropTagIndex();

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
        $this->createTagIndex();
    }
};
