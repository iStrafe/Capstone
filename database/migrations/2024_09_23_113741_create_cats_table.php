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
        Schema::create('cats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('telephone_number');
            $table->string('mobile_number');
            $table->string('cat_name');
            $table->string('cat_image')->nullable();
            $table->integer('age');
            $table->string('color');
            $table->string('breed');
            $table->enum('sex', ['Male', 'Female']);
            $table->date('date_of_adoption');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cats');
    }
};
