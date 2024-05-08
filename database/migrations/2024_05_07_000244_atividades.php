<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atividades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDTurma');
            $table->unsignedBigInteger('IDDisciplina');
            $table->date('DTAvaliacao');
            $table->string('TPConteudo', 50);
            $table->string('DSAtividade', 250);
            $table->float('Pontuacao');
            $table->timestamps();

            $table->foreign('IDTurma')->references('id')->on('turmas');
            $table->foreign('IDDisciplina')->references('id')->on('disciplinas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('atividades');
    }
}
