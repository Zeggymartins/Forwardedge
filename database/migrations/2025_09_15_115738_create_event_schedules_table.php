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
        Schema::create('event_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('session_title');
            $table->text('description')->nullable();
            $table->string('speaker_name')->nullable();
            $table->foreignId('speaker_id')->nullable()->constrained('event_speakers')->onDelete('set null');
            $table->string('location')->nullable(); // Room/Hall name
            $table->enum('session_type', ['keynote', 'session', 'workshop', 'break', 'lunch', 'networking'])->default('session');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'schedule_date', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_schedules');
    }
};
