<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosPlanualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios_planual', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDPlanual');
            $table->unsignedBigInteger('IDPedagogo');
            $table->string('Feedback', 500);
            $table->timestamps();

            $table->foreign('IDPlanual')->references('id')->on('planejamento_anual');
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
        Schema::dropIfExists('comentarios_planual');
    }
}
