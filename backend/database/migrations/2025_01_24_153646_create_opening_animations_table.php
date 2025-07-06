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
        Schema::create('opening_animations', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->text('shadow')->nullable();
            $table->string('doors_color')->nullable();
            $table->text('left_door');
            $table->text('left_door_open');
            $table->text('right_door');
            $table->text('right_door_open');
            $table->text('sealer_position')->nullable();
            $table->text('sealer_style')->nullable();
            $table->boolean('is_sealer_custom')->default(false);
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Define foreign key constraints explicitly
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animations');
    }
};
