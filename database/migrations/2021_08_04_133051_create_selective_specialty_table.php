<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectiveSpecialtyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selective_specialty', function (Blueprint $table) {
            $table->unsignedBigInteger('tender_id')->unique();
            $table->foreign('tender_id')->references('tender_id')->on('tenders')->onDelete('cascade');
            $table->enum('specialty', ['medical', 'engineering-related','Raw materials','technical','technology-related','Other']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selective_specialty');
    }
}
