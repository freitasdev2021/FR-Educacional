<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlocacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alocacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDEscola');
            $table->unsignedBigInteger('IDProfissional');
            $table->time('INITurno');
            $table->time('TERTurno');
            $table->timestamps();

            $table->foreign('IDEscola')->references('id')->on('escolas');
            // Assumindo que IDProfissional se refere a um modelo "Profissional",
            // você pode ajustar a referência de chave estrangeira conforme necessário
            $table->foreign('IDProfissional')->references('id')->on('profissionais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alocacoes');
    }
}
