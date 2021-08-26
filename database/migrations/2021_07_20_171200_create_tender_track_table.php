<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CreateTenderTrackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_track', function (Blueprint $table) {
            $table->bigIncrements('tender_track_id')->unique();
            $table->unsignedBigInteger('tender_id');
            $table->foreign('tender_id')->references('tender_id')->on('tenders')->onDelete('cascade');
            $table->timestampTz('start_date', $precision = 0)->useCurrent();//Set TIMESTAMP columns to use CURRENT_TIMESTAMP as default value.
            $table->timestampTz('end_date', $precision = 0)->useCurrent()->addMonth();
            $table->timestampTz('judging_offers_date_end', $precision = 0)->useCurrent()->addMonths(2);
            $table->timestampTz('decision_committee_judgment_date_end', $precision = 0)->useCurrent()->addMonths(3);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_track');
    }
}
