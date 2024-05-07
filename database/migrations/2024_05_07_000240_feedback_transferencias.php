<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTransferenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback_transferencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDAluno');
            $table->foreign('IDAluno')->references('id')->on('alunos');
            $table->string('Feedback', 250);
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
        Schema::dropIfExists('feedback_transferencias');
    }
}
