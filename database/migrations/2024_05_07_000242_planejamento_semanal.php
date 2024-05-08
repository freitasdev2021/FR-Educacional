<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanejamentoSemanalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planejamento_semanal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDPlaanual');
            $table->text('PLConteudos');
            $table->date('INISemana');
            $table->date('TERSemana');
            $table->integer('Aprovado')->comment('0:Aprovado, 1:Reprovado');
            $table->timestamps();

            $table->foreign('IDPlaanual')->references('id')->on('planejamento_anual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('planejamento_semanal');
    }
}
