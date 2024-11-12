<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BibliotecaController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota"=> "Biblioteca/index"
    ],[
        "nome" => "Emprestimos",
        "endereco" => "Emprestimos",
        "rota" => "Biblioteca/Emprestimos"
    ],[
        "nome" => "Leitores",
        "endereco" => "Leitores",
        "rota" => "Biblioteca/Leitores"
    ]);
}
