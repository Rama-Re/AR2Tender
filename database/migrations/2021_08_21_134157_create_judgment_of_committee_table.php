<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJudgmentOfCommitteeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judgment_of_committee', function (Blueprint $table) {
            $table->bigIncrements('committee_judgment_id')->unique();
            $table->unsignedBigInteger('committee_member_id');
            $table->foreign('committee_member_id')->references('committee_member_id')->on('committee_members')->onDelete('cascade');
            $table->unsignedBigInteger('submit_form_id');
            $table->foreign('submit_form_id')->references('submit_form_id')->on('submit_forms')->onDelete('cascade');
            $table->longText('judgment');
            $table->integer('vote');
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
        Schema::dropIfExists('judgment_of_committee');
    }
}
