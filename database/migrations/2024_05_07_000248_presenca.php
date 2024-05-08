<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresencasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presencas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDAula');
            $table->unsignedBigInteger('IDEscola');
            $table->unsignedBigInteger('IDTurma');
            $table->unsignedBigInteger('IDProfessor');
            $table->unsignedBigInteger('IDAluno');
            $table->tinyInteger('Status')->default(0);
            $table->timestamps();

            $table->foreign('IDAula')->references('id')->on('aulas');
            $table->foreign('IDEscola')->references('id')->on('escolas');
            $table->foreign('IDTurma')->references('id')->on('turmas');
            $table->foreign('IDProfessor')->references('id')->on('professores');
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
        Schema::dropIfExists('presencas');
    }
}
