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
            $table->timestampTz('judging_offers_date', $precision = 0)->useCurrent()->addMonths(2);
            $table->timestampTz('judging_offers_by_administrator_date', $precision = 0)->useCurrent()->addDays(63);
            $table->timestampTz('decision_committee_judgment_date', $precision = 0)->useCurrent()->addMonths(3);
            $table->timestampTz('administrator_decision_committee_judgment_date', $precision = 0)->useCurrent()->addDays(93);
            $table->timestampTz('announcing_result_date', $precision = 0)->useCurrent()->addDays(94);

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
