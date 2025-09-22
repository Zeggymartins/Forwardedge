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
        Schema::create('blog_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id');
            $table->string('type'); // e.g. heading, paragraph, list, feature, image
            $table->longText('content')->nullable(); // actual text or data
            $table->json('extras')->nullable(); // for list items, image URLs, feature icons etc.
            $table->integer('order')->default(0); // position of the block in blog
            $table->timestamps();

            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_details');
    }
};
