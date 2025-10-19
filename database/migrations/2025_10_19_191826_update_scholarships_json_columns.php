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
        Schema::table('scholarships', function (Blueprint $table) {
            // Add if missing; adjust if your table already has them
            if (!Schema::hasColumn('scholarships', 'program_includes')) $table->json('program_includes')->nullable()->after('about');
            if (!Schema::hasColumn('scholarships', 'who_can_apply'))    $table->json('who_can_apply')->nullable()->after('program_includes');
            if (!Schema::hasColumn('scholarships', 'how_to_apply'))     $table->json('how_to_apply')->nullable()->after('who_can_apply');

            if (!Schema::hasColumn('scholarships', 'image')) $table->string('image')->nullable()->after('subtext');
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
