<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenovacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renovacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDAluno');
            $table->foreign('IDAluno')->references('id')->on('alunos');
            $table->integer('Aprovado')->comment('0:Reprovada, 1:Aprovada');
            $table->year('ANO');
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
        Schema::dropIfExists('renovacoes');
    }
}
