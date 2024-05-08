<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosPlsemanalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios_plsemanal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDPlsemanal');
            $table->unsignedBigInteger('IDPedagogo');
            $table->string('Feedback', 500);
            $table->timestamps();

            $table->foreign('IDPlsemanal')->references('id')->on('planejamento_semanal');
            $table->foreign('IDPedagogo')->references('id')->on('pedagogos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comentarios_plsemanal');
    }
}
