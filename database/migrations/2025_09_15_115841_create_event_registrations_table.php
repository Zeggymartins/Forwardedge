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
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained('event_tickets')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->text('special_requirements')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->string('registration_code')->unique();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->datetime('registered_at');
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index('email');
            $table->index('registration_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
