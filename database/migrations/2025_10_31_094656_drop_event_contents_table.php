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
        // If you want to be extra safe on some DBs, you can drop the FK first.
        // But dropIfExists is generally fine if the table exists.
        Schema::dropIfExists('event_contents');
    }

    /** Recreate the table exactly as before (rollback) */
    public function down(): void
    {
        Schema::create('event_contents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained()       // references 'id' on 'events'
                ->onDelete('cascade');

            $table->enum('type', [
                'heading',
                'paragraph',
                'list',
                'image',
                'feature',
            ]);

            $table->longText('content');
            $table->integer('position')->default(0);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['event_id', 'sort_order']);
        });
    }
};
