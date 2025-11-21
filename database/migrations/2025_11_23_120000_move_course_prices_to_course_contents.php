<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('course_contents', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('type');
            }

            if (!Schema::hasColumn('course_contents', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }
        });

        if (Schema::hasColumn('courses', 'price') || Schema::hasColumn('courses', 'discount_price')) {
            DB::table('courses')
                ->select('id', 'price', 'discount_price')
                ->orderBy('id')
                ->chunk(100, function ($courses) {
                    foreach ($courses as $course) {
                        if (is_null($course->price) && is_null($course->discount_price)) {
                            continue;
                        }

                        DB::table('course_contents')
                            ->where('course_id', $course->id)
                            ->update([
                                'price' => $course->price ?? 0,
                                'discount_price' => $course->discount_price,
                            ]);
                    }
                });
        }

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'discount_price')) {
                $table->dropColumn('discount_price');
            }

            if (Schema::hasColumn('courses', 'price')) {
                $table->dropColumn('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('status');
            }

            if (!Schema::hasColumn('courses', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }
        });

        DB::table('courses')
            ->select('id')
            ->orderBy('id')
            ->chunk(100, function ($courses) {
                foreach ($courses as $course) {
                    $aggregates = DB::table('course_contents')
                        ->selectRaw('MIN(price) as base_price, MIN(COALESCE(discount_price, price)) as sale_price')
                        ->where('course_id', $course->id)
                        ->first();

                    DB::table('courses')
                        ->where('id', $course->id)
                        ->update([
                            'price' => $aggregates?->base_price,
                            'discount_price' => $aggregates?->sale_price !== $aggregates?->base_price
                                ? $aggregates?->sale_price
                                : null,
                        ]);
                }
            });

        Schema::table('course_contents', function (Blueprint $table) {
            if (Schema::hasColumn('course_contents', 'discount_price')) {
                $table->dropColumn('discount_price');
            }

            if (Schema::hasColumn('course_contents', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};
