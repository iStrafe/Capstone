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
        Schema::create('adoption_request', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('home_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('name_of_cat');
            $table->string('breed')->nullable();
            $table->integer('approximate_age')->nullable();
            $table->enum('sex', ['male', 'female']);
            $table->string('color');
            $table->date('date_of_adoption');
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('adoption_request', function (Blueprint $table) {
            //
        });
    }
};
