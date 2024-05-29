<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransporteController extends Controller
{
    //
    public const submodulos = array([
        "nome" => "Rotas",
        "endereco" => "index",
        "rota" => "Rotas/index"
    ],[
        "nome" => "Veiculos",
        "endereco" => "Veiculos",
        "rota" => "Veiculos/index"
    ],[
        "nome" => "Motoristas",
        "endereco" => "Motoristas",
        "rota" => "Motoristas/index"
    ],[
        "nome" => "Terceirizadas",
        "endereco" => "Terceirizadas",
        "rota" => "Terceirizadas/index"
    ]);
    //
    public function index(){
        return view('Transporte.index',[
            "submodulos" => self::submodulos
        ]);
    }
    //
}
