<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDVeiculo');
            $table->unsignedBigInteger('IDMotorista');
            $table->string('Descricao', 50);
            $table->json('RotaJSON');
            $table->float('Distancia');
            $table->timestamps();

            $table->foreign('IDVeiculo')->references('id')->on('veiculos');
            $table->foreign('IDMotorista')->references('id')->on('motoristas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rotas');
    }
}
