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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('payable_id');
            $table->string('payable_type');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'successful', 'failed'])->default('pending');
            $table->string('method')->nullable();
            $table->string('reference')->unique();
            $table->string('currency')->default('NGN');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['payable_id', 'payable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
