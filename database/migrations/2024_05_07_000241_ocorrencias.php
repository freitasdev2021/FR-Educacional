<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcorrenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDAluno');
            $table->unsignedBigInteger('IDProfessor');
            $table->dateTime('DTOcorrencia');
            $table->string('DSOcorrido', 500);
            $table->timestamps();

            $table->foreign('IDAluno')->references('id')->on('alunos');
            $table->foreign('IDProfessor')->references('id')->on('professores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ocorrencias');
    }
}
