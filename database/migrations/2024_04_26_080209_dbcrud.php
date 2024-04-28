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
        Schema::create('catinfo',function(Blueprint $table){
            $table->integer('id')->unique();
            $table->string('name');
            $table->string('gender');
            $table->string('breed');
            $table->string('eye_color');
            $table->string('fur_color');
            $table->longText('description');
            $table->integer('status');
        });
    } 
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
