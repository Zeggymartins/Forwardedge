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
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Early Bird, Regular, VIP, etc.
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity_available')->nullable();
            $table->integer('quantity_sold')->default(0);
            $table->datetime('sale_start')->nullable();
            $table->datetime('sale_end')->nullable();
            $table->json('features')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
    }
};
