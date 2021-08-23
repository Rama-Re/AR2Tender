<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_result', function (Blueprint $table) {
            $table->bigIncrements('tender_result_id')->unique();
            $table->unsignedBigInteger('committee_member_id');
            $table->foreign('committee_member_id')->references('committee_member_id')->on('committee_members')->onDelete('cascade');
            $table->unsignedBigInteger('submit_form_id');
            $table->foreign('submit_form_id')->references('submit_form_id')->on('submit_forms')->onDelete('cascade');
            $table->unsignedBigInteger('tender_id');
            $table->foreign('tender_id')->references('tender_id')->on('tenders')->onDelete('cascade');
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
        Schema::dropIfExists('tender_result');
    }
}
