<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDProfessor');
            $table->unsignedBigInteger('IDDisciplina');
            $table->unsignedBigInteger('IDTurma');
            $table->time('INITur');
            $table->time('TERTur');
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
        Schema::dropIfExists('turnos');
    }
}
