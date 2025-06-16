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
        Schema::create('events', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('title');
            $table->text('introduction');
            $table->text('greeting');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['draft', 'confirmed', 'completed', 'cancelled'])->default('draft');
            $table->string('hashtag');
            $table->text('prayer')->nullable();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organiser_id')->constrained()->cascadeOnDelete();
            $table->foreignId('music_id')->constrained()->cascadeOnDelete()->nullable();
            $table->foreignId('frame_id')->constrained()->cascadeOnDelete()->nullable();
            $table->string('youtube_music_url')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Define foreign key constraints explicitly
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
