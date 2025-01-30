<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organisers', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('short_name_1'); //  nama pendek pengantin lelaki / nama pendek pasangan 1
            $table->string('short_name_2')->nullable(); // nama pendek pengantin perempuan / nama pendek pasangan 2
            $table->string('short_name_3')->nullable(); // Nama pendek pengantin lelaki 2
            $table->string('short_name_4')->nullable(); // Nama pendek pengantin perempuan 2

            $table->string('full_name_1'); // nama penuh pengantin lelaki / nama penuh pasangan 1
            $table->string('full_name_2')->nullable(); // nama penuh pengantin perempuan / nama penuh pasangan 2
            $table->string('full_name_3')->nullable(); // Nama penuh pengantin lelaki 2
            $table->string('full_name_4')->nullable(); // Nama penuh pengantin perempuan 2

            $table->string('associated_name_1')->nullable(); // nama bapa pengantin lelaki / nama makayah pasangan 1
            $table->string('associated_name_1_1')->nullable(); // Nama ibu pengantin lelaki
            $table->string('associated_name_2')->nullable(); // nama bapa pengantin perempuan / nama makayah pasangan 2
            $table->string('associated_name_2_1')->nullable(); // Nama ibu pengantin perempuan

            $table->text('image_1')->nullable();
            $table->enum('organiser_type', ['single_couple', 'double_couple'])->default('single_couple');
            $table->foreignId('font_id')->nullable()->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('organisers');
    }
};
