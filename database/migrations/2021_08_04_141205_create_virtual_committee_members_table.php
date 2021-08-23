<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualCommitteeMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_committee_members', function (Blueprint $table) {
            $table->bigIncrements('virtual_committee_member_id')->unique();
            $table->unsignedBigInteger('virtual_committee_id');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('virtual_committee_id')->references('virtual_committee_id')->on('virtual_committees')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->primary(['virtual_committee_id','employee_id'],'virtual_committee_member_id')->unique();
            $table->enum('task',['administrator','viewer','discussant']);
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
        Schema::dropIfExists('virtual_committee_members');
    }
}
