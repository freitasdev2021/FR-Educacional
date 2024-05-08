<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotoristasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motoristas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDOrganizacao');
            $table->string('Nome', 50);
            $table->date('Nascimento');
            $table->date('Admissao');
            $table->string('Email', 50);
            $table->string('Celular', 11);
            $table->date('TerminoContrato')->nullable();
            $table->string('CEP', 8);
            $table->string('Rua', 50);
            $table->string('UF', 2);
            $table->string('Cidade', 50);
            $table->string('Bairro', 500);
            $table->integer('Numero');
            $table->tinyInteger('Ativo')->default(1);
            $table->timestamps();

            $table->foreign('IDOrganizacao')->references('id')->on('organizacoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motoristas');
    }
}
