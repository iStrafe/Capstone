<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admins can say why a cat was archived; the public cat page shows it.
     */
    public function up(): void
    {
        Schema::table('cats', function (Blueprint $table) {
            $table->string('archive_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cats', function (Blueprint $table) {
            $table->dropColumn('archive_reason');
        });
    }
};
