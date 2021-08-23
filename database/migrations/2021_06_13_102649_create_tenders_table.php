<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
class CreateTendersTable extends Migration
{
    
    public function up()
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->bigIncrements('tender_id')->unique();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('company_id')->on('companies')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->boolean('active'); // make it to public or save as draft
            $table->enum('type', ['open', 'selective'])->default('Open');
            $table->enum('selective',['companies','specialty','countries'])->nullable();
            $table->enum('category', ['medical', 'engineering-related','Raw materials','technical','technology-related','Other'])->default('Other');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenders');
    }
}
