<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstoqueMovimentacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estoque_movimentacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('IDEstoque');
            $table->integer('TPMovimentacao'); // 0: Entrada, 1: Saída
            $table->timestamps();

            $table->foreign('IDEstoque')->references('id')->on('estoque');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estoque_movimentacao');
    }
}
