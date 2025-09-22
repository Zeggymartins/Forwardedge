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
            $table->decimal('price', 10, 2)->default(0)->after('status');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->boolean('is_featured')->default(false)->after('discount_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Course', function (Blueprint $table) {
            //
        });
    }
};
