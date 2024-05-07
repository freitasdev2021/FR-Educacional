<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEscolasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('escolas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDOrg');
            $table->foreign('IDOrg')->references('id')->on('organizacoes');
            $table->string('Nome', 50);
            $table->string('CEP', 8);
            $table->string('Rua', 50);
            $table->string('Bairro', 50);
            $table->string('Cidade', 50);
            $table->integer('Numero');
            $table->string('UF', 2);
            $table->string('Telefone', 11);
            $table->string('Email', 50);
            $table->integer('QTVagas');
            $table->integer('QTRepetencia');
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
        Schema::dropIfExists('escolas');
    }
}
