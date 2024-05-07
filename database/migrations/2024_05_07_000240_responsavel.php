<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsavelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responsavels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDAluno');
            $table->foreign('IDAluno')->references('id')->on('alunos');
            $table->text('RGPaisAnexo');
            $table->string('RGPais', 9);
            $table->string('NMResponsavel', 50);
            $table->string('EmailResponsavel', 50);
            $table->string('CLResponsavel', 11);
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
        Schema::dropIfExists('responsavels');
    }
}
