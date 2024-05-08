<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDissertativasEadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dissertativas_ead', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDAtividade');
            $table->string('Enunciado', 50);
            $table->text('Resposta')->nullable();
            $table->string('Feedback', 250)->nullable();
            $table->tinyInteger('Resultado')->nullable();
            $table->float('Total');
            $table->float('Nota')->nullable();
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
        Schema::dropIfExists('dissertativas_ead');
    }
}
