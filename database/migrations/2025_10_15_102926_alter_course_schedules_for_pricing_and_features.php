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
        Schema::table('course_schedules', function (Blueprint $table) {
            $table->text('description')->nullable()->after('price');
            $table->string('title')->nullable()->after('course_id');
            $table->string('tag', 32)->default('paid')->index()->after('description'); // free|paid|special|...
            $table->unsignedInteger('price_usd')->nullable()->after('tag');
            $table->json('features')->nullable()->after('price_usd'); // [{heading, description?}, ...]
            // allow duplicates on dates – nothing to change structurally if no unique indexes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
