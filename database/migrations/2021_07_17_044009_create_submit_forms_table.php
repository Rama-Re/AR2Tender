<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmitFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submit_forms', function (Blueprint $table) {
            $table->bigIncrements('submit_form_id')->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('tender_id');
            $table->foreign('company_id')->references('company_id')->on('companies')->onDelete('cascade');
            $table->foreign('tender_id')->references('tender_id')->on('tenders')->onDelete('cascade');

            //$table->unsignedBigInteger('price');
            $table->string('price')->default('0');//make it as string to add (SP) or ($) or ...
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
        Schema::dropIfExists('submit_forms');
    }
}
