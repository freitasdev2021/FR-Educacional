<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVeiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDOrganizacao');
            $table->string('Nome', 50);
            $table->string('Marca', 50);
            $table->string('Placa', 7);
            $table->string('Cor', 10);
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
        Schema::dropIfExists('veiculos');
    }
}
