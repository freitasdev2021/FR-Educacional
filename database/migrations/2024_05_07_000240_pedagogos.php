<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedagogosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedagogos', function (Blueprint $table) {
            $table->id();
            $table->integer('IDEscola');
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
            $table->string('Bairro', 50);
            $table->integer('Numero');
            $table->integer('Ativo')->default(1)->comment('0:Inativo, 1:Ativo');
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
        Schema::dropIfExists('pedagogos');
    }
}
