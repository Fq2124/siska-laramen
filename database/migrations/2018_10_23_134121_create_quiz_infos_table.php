<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quiztype_id')->unsigned();
            $table->foreign('quiztype_id')->references('id')->on('quiz_types');
            $table->integer('total_question');
            $table->string('question_ids');
            $table->string('unique_code', 6);
            $table->integer('time_limit');
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
        Schema::dropIfExists('quiz_infos');
    }
}
