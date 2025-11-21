<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'course_content_id')) {
                $table->foreignId('course_content_id')
                    ->nullable()
                    ->after('course_id')
                    ->constrained('course_contents')
                    ->nullOnDelete();
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'course_content_id')) {
                $table->foreignId('course_content_id')
                    ->nullable()
                    ->after('course_id')
                    ->constrained('course_contents')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'course_content_id')) {
                $table->dropConstrainedForeignId('course_content_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'course_content_id')) {
                $table->dropConstrainedForeignId('course_content_id');
            }
        });
    }
};
