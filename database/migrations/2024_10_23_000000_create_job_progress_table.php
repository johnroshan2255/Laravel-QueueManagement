<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobProgressTable extends Migration
{
    public function up()
    {
        Schema::create('job_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->index();
            $table->string('job_name')->index();
            $table->integer('progress');
            $table->string('status'); // In Progress, Completed, Failed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_progress');
    }
}