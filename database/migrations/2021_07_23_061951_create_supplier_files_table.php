<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_files', function (Blueprint $table) {
            $table->bigIncrements('supplier_file_id')->unique();
            $table->unsignedBigInteger('submit_form_id');
            $table->foreign('submit_form_id')->references('company_id','tender_id')->on('submit_forms')->onDelete('cascade');
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
        Schema::dropIfExists('supplier_files');
    }
}
