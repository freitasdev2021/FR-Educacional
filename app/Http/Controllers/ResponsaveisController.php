<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponsaveisController extends Controller
{

    public const submodulos = array([
        "nome" => "Relatório",
        "endereco" => "index",
        "rota" => "Responsaveis/index"
    ]);

    public function index(){
        return view('Responsaveis.index',[
            "submodulos" => self::submodulos
        ]);
    }
}
