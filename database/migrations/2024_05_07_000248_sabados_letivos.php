<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSabadosLetivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sabados_letivos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDEscola');
            $table->date('Data');
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
        Schema::dropIfExists('sabados_letivos');
    }
}
