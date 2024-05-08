<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeriasAlunosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ferias_alunos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDEscola');
            $table->unsignedBigInteger('IDAluno');
            $table->date('DTInicio');
            $table->date('DTTermino');
            $table->timestamps();
            $table->foreign('IDEscola')->references('id')->on('escolas');
            $table->foreign('IDAluno')->references('id')->on('alunos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ferias_alunos');
    }
}
