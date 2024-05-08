<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanejamentoAnualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planejamento_anual', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDProfessor');
            $table->unsignedBigInteger('IDDisciplina');
            $table->unsignedBigInteger('IDTurma');
            $table->text('PLConteudos');
            $table->integer('Aprovado')->comment('0:Aprovado, 1:Reprovado');
            $table->timestamps();

            $table->foreign('IDProfessor')->references('id')->on('professores');
            $table->foreign('IDDisciplina')->references('id')->on('disciplinas');
            $table->foreign('IDTurma')->references('id')->on('turmas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('planejamento_anual');
    }
}
