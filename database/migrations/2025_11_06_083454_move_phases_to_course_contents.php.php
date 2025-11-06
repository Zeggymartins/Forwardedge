<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 0) Ensure the new FK exists
        if (!Schema::hasColumn('course_phases', 'course_content_id')) {
            Schema::table('course_phases', function (Blueprint $table) {
                $table->foreignId('course_content_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // 1) Backfill course_content_id from course_id (if course_id exists)
        $hasCourseId = Schema::hasColumn('course_phases', 'course_id');
        if ($hasCourseId) {
            $phases = DB::table('course_phases')->select('id', 'course_id')->get();
            foreach ($phases as $p) {
                if (!$p->course_id) continue;

                $content = DB::table('course_contents')
                    ->where('course_id', $p->course_id)
                    ->orderBy('order')
                    ->first();

                if (!$content) {
                    $contentId = DB::table('course_contents')->insertGetId([
                        'course_id'  => $p->course_id,
                        'title'      => 'Curriculum',
                        'file_path'  => null,
                        'type'       => 'text', // must pass CHECK(type)
                        'content'    => null,
                        'order'      => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $contentId = $content->id;
                }

                DB::table('course_phases')->where('id', $p->id)->update([
                    'course_content_id' => $contentId,
                ]);
            }
        }

        // 2) Drop any indexes that include course_id (SQLite requires this BEFORE dropping the column)
        if ($hasCourseId && DB::getDriverName() === 'sqlite') {
            // List all indexes on course_phases
            $indexes = DB::select('PRAGMA index_list("course_phases")');
            foreach ($indexes as $idx) {
                $idxName = $idx->name ?? null;
                if (!$idxName) continue;

                // Inspect columns in this index
                $cols = DB::select('PRAGMA index_info("' . $idxName . '")');
                $touchesCourseId = false;
                foreach ($cols as $c) {
                    // column name property is 'name' in index_info
                    if (isset($c->name) && $c->name === 'course_id') {
                        $touchesCourseId = true;
                        break;
                    }
                }

                if ($touchesCourseId) {
                    // Drop the index
                    DB::statement('DROP INDEX IF EXISTS "' . $idxName . '"');
                }
            }
        }

        // 3) Drop FK/column course_id if present (after its indexes are gone)
        if ($hasCourseId) {
            Schema::table('course_phases', function (Blueprint $table) {
                try {
                    $table->dropConstrainedForeignId('course_id');
                } catch (\Throwable $e) {
                }
            });

            if (Schema::hasColumn('course_phases', 'course_id')) {
                Schema::table('course_phases', function (Blueprint $table) {
                    $table->dropColumn('course_id');
                });
            }
        }

        // 4) Optionally enforce NOT NULL on non-SQLite (skip on SQLite)
        try {
            if (DB::getDriverName() !== 'sqlite') {
                Schema::table('course_phases', function (Blueprint $table) {
                    $table->foreignId('course_content_id')->nullable(false)->change();
                });
            }
        } catch (\Throwable $e) {
            // keep nullable on dev/sqlite
        }
    }

    public function down(): void
    {
        // Recreate course_id
        if (!Schema::hasColumn('course_phases', 'course_id')) {
            Schema::table('course_phases', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        // Backfill course_id from course_content_id
        if (Schema::hasColumn('course_phases', 'course_content_id')) {
            $rows = DB::table('course_phases')->select('id', 'course_content_id')->get();
            foreach ($rows as $r) {
                if (!$r->course_content_id) continue;
                $content = DB::table('course_contents')->where('id', $r->course_content_id)->first();
                if ($content) {
                    DB::table('course_phases')->where('id', $r->id)->update([
                        'course_id' => $content->course_id,
                    ]);
                }
            }

            // Drop course_content_id (after removing FK)
            Schema::table('course_phases', function (Blueprint $table) {
                try {
                    $table->dropConstrainedForeignId('course_content_id');
                } catch (\Throwable $e) {
                }
            });
            if (Schema::hasColumn('course_phases', 'course_content_id')) {
                Schema::table('course_phases', function (Blueprint $table) {
                    $table->dropColumn('course_content_id');
                });
            }
        }

        // Optionally set course_id NOT NULL on non-SQLite
        try {
            if (DB::getDriverName() !== 'sqlite') {
                Schema::table('course_phases', function (Blueprint $table) {
                    $table->foreignId('course_id')->nullable(false)->change();
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
