<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_registrations') && Schema::hasColumn('event_registrations', 'ticket_id')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropConstrainedForeignId('ticket_id');
            });
        }

        Schema::dropIfExists('event_schedules');
        Schema::dropIfExists('event_speakers');
        Schema::dropIfExists('event_tickets');
        Schema::dropIfExists('event_sponsors');
        Schema::dropIfExists('event_contents');
    }

    public function down(): void
    {
        Schema::create('event_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->text('content')->nullable();
            $table->string('position')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['event_id', 'sort_order']);
        });

        Schema::create('event_speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('company')->nullable();
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_keynote')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['event_id', 'sort_order']);
        });

        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity_available')->nullable();
            $table->integer('quantity_sold')->default(0);
            $table->dateTime('sale_start')->nullable();
            $table->dateTime('sale_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('features')->nullable();
            $table->timestamps();
            $table->index(['event_id', 'is_active', 'sort_order']);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'ticket_id')) {
                $table->foreignId('ticket_id')->after('event_id')->constrained('event_tickets')->onDelete('cascade');
            }
        });

        Schema::create('event_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('speaker_id')->nullable()->constrained('event_speakers')->nullOnDelete();
            $table->date('schedule_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('title');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['event_id', 'schedule_date', 'sort_order']);
        });

        Schema::create('event_sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->string('tier')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['event_id', 'tier', 'sort_order']);
        });
    }
};
