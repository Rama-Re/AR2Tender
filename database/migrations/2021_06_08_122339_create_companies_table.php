<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('company_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->string('director_name'); 
            $table->string('company_name');
            $table->string('image')->nullable();
            $table->string('image_path')->nullable();
            $table->string('username')->unique();
            $table->longText('about_us');
            $table->enum('status', ['TenderOffer', 'TendersManager'])->default('TendersManager');
            $table->enum('specialty', ['medical', 'engineering-related','Raw materials','technical','technology-related','Other']);
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
        Schema::dropIfExists('companies');
    }
}
