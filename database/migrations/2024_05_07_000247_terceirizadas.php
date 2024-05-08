<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerceirizadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terceirizadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDOrg');
            $table->string('Nome', 50);
            $table->string('CEP', 8);
            $table->string('Rua', 50);
            $table->string('Bairro', 50);
            $table->string('Cidade', 50);
            $table->integer('Numero');
            $table->string('UF', 2);
            $table->string('Telefone', 11);
            $table->string('Email', 50);
            $table->string('CNPJ', 14);
            $table->string('Ramo', 30);
            $table->timestamps();

            $table->foreign('IDOrg')->references('id')->on('organizacoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terceirizadas');
    }
}
