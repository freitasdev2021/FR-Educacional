<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeriasProfissionaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ferias_profissionais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDEscola');
            $table->unsignedBigInteger('IDProfissional');
            $table->date('DTInicio');
            $table->date('DTTermino');
            $table->timestamps();

            $table->foreign('IDFerias')->references('id')->on('ferias');
            $table->foreign('IDEscola')->references('id')->on('escolas');
            $table->foreign('IDProfissional')->references('id')->on('profissionais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ferias_profissionais');
    }
}
