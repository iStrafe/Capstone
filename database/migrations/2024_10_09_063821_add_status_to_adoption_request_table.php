<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAdoptionRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('adoption_request', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('valid_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adoption_request', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('valid_id');
        });
    }
}