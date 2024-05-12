<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuariosController extends Controller
{


    public const submodulos = array([
        "nome" => "Cadastros",
        "endereco" => "indexFornecedor",
        "rota" => "Usuarios/indexFornecedor"
    ]);

    public function fornecedoresIndex(){
        return view('Usuarios.indexFornecedor',[
            "submodulos" => self::submodulos
        ]);
    }
}
