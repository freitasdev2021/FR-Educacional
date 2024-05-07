<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJustificativaAlteracoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('justificativa_alteracoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDAluno');
            $table->foreign('IDAluno')->references('id')->on('alunos');
            $table->string('Justificativa', 250);
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
        Schema::dropIfExists('justificativa_alteracoes');
    }
}
