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
            $table->string('approval_date')->nullable()->after('date_of_adoption');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adoption_request', function (Blueprint $table) {
            $table->dropColumn('approval_date');
        });
    }
};
