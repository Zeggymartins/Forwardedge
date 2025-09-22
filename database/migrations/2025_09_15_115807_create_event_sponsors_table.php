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
        Schema::create('event_sponsors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('logo')->nullable();
                $table->string('website')->nullable();
                $table->text('description')->nullable();
                $table->enum('tier', ['platinum', 'gold', 'silver', 'bronze', 'partner'])->default('bronze');
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['event_id', 'tier', 'sort_order']);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sponsors');
    }
};
