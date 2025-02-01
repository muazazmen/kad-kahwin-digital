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
        Schema::create('guest_settings', function (Blueprint $table) {
            $table->id()->primary();
            $table->integer('overall_attend_limit')->default(0);
            $table->integer('attend_limit_per_guest')->default(1);
            $table->boolean('is_kids')->default(false);
            $table->boolean('is_timeslot')->default(false);
            $table->json('timeslots')->nullable(); // Store timeslots as JSON
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_settings');
    }
};
