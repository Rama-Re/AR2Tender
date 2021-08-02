<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_files', function (Blueprint $table) {
            $table->bigIncrements('tender_file_id')->unique();
            $table->unsignedBigInteger('tender_id');
            $table->foreign('tender_id')->references('tender_id')->on('tenders')->onDelete('cascade');
            $table->string('name');
            $table->unsignedBigInteger('size');
            $table->string('path');
            $table->enum('type',['financial requirement','technician requirement','other']);
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
        Schema::dropIfExists('tender_files');
    }
}
