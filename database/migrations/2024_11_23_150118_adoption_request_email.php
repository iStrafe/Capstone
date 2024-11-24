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
         Schema::table('adoption_request', function (Blueprint $table) {
            $table->string('email')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('adoption_requests', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
