<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualCommitteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_committees', function (Blueprint $table) {
            $table->bigIncrements('virtual_committee_id')->unique();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('company_id')->on('companies')->onDelete('cascade');
            $table->enum('type',['financial','technician','decision maker']);
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
        Schema::dropIfExists('virtual_committees');
    }
}
