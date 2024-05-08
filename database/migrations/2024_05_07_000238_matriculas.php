<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatriculasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->text('AnexoRG')->nullable();
            $table->unsignedBigInteger('IDEscola');
            $table->text('CResidencia')->nullable();
            $table->text('Historico')->nullable();
            $table->string('Nome', 50);
            $table->string('CPF', 11);
            $table->string('RG', 9);
            $table->string('CEP', 8);
            $table->string('Rua', 50);
            $table->string('Email', 50);
            $table->string('Celular', 11);
            $table->string('UF', 2);
            $table->string('Cidade', 50);
            $table->integer('AnoLetivo');
            $table->integer('BolsaFamilia');
            $table->integer('Alergia');
            $table->integer('Transporte');
            $table->integer('NEE');
            $table->integer('AMedico');
            $table->integer('APsicologico');
            $table->integer('Aprovado')->default(2); // 0: Reprovado, 1: Aprovado, 2: Pendente
            $table->timestamps();

            $table->foreign('IDEscola')->references('id')->on('escolas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matriculas');
    }
}
