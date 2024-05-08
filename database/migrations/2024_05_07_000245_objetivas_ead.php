<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjetivasEadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objetivas_ead', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDAtividade');
            $table->string('Enunciado', 50);
            $table->text('Opcoes');
            $table->char('Correta', 1);
            $table->char('Resposta', 1)->nullable();
            $table->string('Feedback', 250)->nullable();
            $table->float('Total');
            $table->timestamps();

            $table->foreign('IDAtividade')->references('id')->on('atividades_ead');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objetivas_ead');
    }
}
