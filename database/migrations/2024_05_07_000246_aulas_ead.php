<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAulasEadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aulas_ead', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDTurma');
            $table->string('DescricaoAula', 250);
            $table->timestamps();

            $table->foreign('IDTurma')->references('id')->on('turmas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aulas_ead');
    }
}
