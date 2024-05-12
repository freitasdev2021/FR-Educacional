<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaltasJustificadasProfissionalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faltas_justificadas_profissional', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDPessoa');
            $table->string('Justificativa', 250);
            $table->date('DTFalta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faltas_justificadas_profissional');
    }
}
