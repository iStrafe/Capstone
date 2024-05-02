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
         Schema::create('reportcat',function(Blueprint $table){
            $table->integer('id')->unique()->autoIncrement();
            $table->string('name');
            $table->string('gender');
            $table->string('breed');
            $table->string('eye_color');
            $table->string('fur_color');
            $table->timestamp('last_seen_date');
            $table->longText('last_seen_location');
            $table->string('contact_email');
            $table->binary('catimage');
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
