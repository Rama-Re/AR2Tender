<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectiveCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selective_countries', function (Blueprint $table) {
            $table->char('country_id',2);
            $table->unsignedBigInteger('tender_id');
            $table->foreign('country_id')->references('country_id')->on('countries')->onDelete('cascade');
            $table->foreign('tender_id')->references('tender_id')->on('tenders')->onDelete('cascade');
            $table->primary(['country_id','tender_id'],'selective_country_id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selective_countries');
    }
}
