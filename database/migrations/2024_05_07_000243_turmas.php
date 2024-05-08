<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurmasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('turmas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDEscola');
            $table->string('Serie', 20);
            $table->string('Nome', 30);
            $table->time('INITurma');
            $table->time('TERTurma');
            $table->string('Periodo', 10);
            $table->float('NotaPeriodo')->nullable();
            $table->float('MediaPeriodo')->nullable();
            $table->float('TotalAno')->nullable();
            $table->timestamps();

            $table->foreign('IDEscola')->references('id')->on('escolas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('turmas');
    }
}
