<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AulasController extends Controller
{

    const submodulos = array([
        'nome' => 'Aulas',
        'rota' => 'Aulas/index',
        'endereco' => 'index'
    ],[
        'nome' => 'Atividades e Avaliações',
        'rota' => 'Aulas/Atividades/index',
        'endereco' => 'Atividades'
    ]);
    //
    const cadastroSubmodulos = array([
        'nome' => 'Aulas',
        'rota' => 'Aulas/Edit',
        'endereco' => 'Edit'
    ],[
        'nome' => 'Lista de Chamada',
        'endereco' => 'Chamada',
        'rota' => 'Aulas/Chamada'
    ]);
    //LISTAGEM PRINCIPAL
    public function index(){
        return view('Aulas.index',[
            'submodulos' => self::submodulos
        ]);
    }
    //ATIVIDADES
    public function atividades(){
        return view('Aulas.atividades',[
            'submodulos' => self::submodulos
        ]);
    }
    //CADASTRO DE AULAS
    public function cadastro($id=null){
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
        ];

        if($id){
            $view['id'] = $id;
            $view['submodulos'] = self::cadastroSubmodulos;
        }

        return view('Aulas.cadastro',$view);
    }
    //CADASTRO DE ATIVIDADES
    
    public function cadastroAtividades($id=null){
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
        ];

        if($id){
            $view['id'] = $id;
        }

        return view('Aulas.cadastroAtividades',$view);
    }
}
