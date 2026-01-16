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
        // Add USD pricing to course_contents
        Schema::table('course_contents', function (Blueprint $table) {
            $table->decimal('price_usd', 10, 2)->nullable()->after('discount_price');
            $table->decimal('discount_price_usd', 10, 2)->nullable()->after('price_usd');
        });

        // Add currency to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency', 3)->default('NGN')->after('total_price');
        });

        // Add currency to cart_items
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('currency', 3)->default('NGN')->after('price');
        });

        // Add currency to order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('currency', 3)->default('NGN')->after('price');
        });

        // Add currency to enrollments
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('currency', 3)->default('NGN')->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            $table->dropColumn(['price_usd', 'discount_price_usd']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
