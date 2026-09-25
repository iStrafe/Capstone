<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Age (whole years), color and breed are optional in the admin form, so the columns allow NULL.
     */
    public function up(): void
    {
        Schema::table('cats', function (Blueprint $table) {
            $table->integer('age')->nullable()->change();
            $table->string('color')->nullable()->change();
            $table->string('breed')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('cats')->whereNull('age')->update(['age' => 0]);
        DB::table('cats')->whereNull('color')->update(['color' => '']);
        DB::table('cats')->whereNull('breed')->update(['breed' => '']);

        Schema::table('cats', function (Blueprint $table) {
            $table->integer('age')->nullable(false)->change();
            $table->string('color')->nullable(false)->change();
            $table->string('breed')->nullable(false)->change();
        });
    }
};
