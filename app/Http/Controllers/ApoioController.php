<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apoio;
use App\Models\Escola;
use App\Models\User;
use App\Http\Controllers\ProfessoresController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApoioController extends Controller
{
    public const submodulos = ProfessoresController::cadastroSubmodulos;
    public function index($IDProfessor){
        return view('Apoio.index',[
            "submodulos" => self::submodulos,
            'IDProfessor' => $IDProfessor
        ]);
    }

    public function cadastro($IDProfessor,$id){
        $IDOrg = Auth::user()->id_org;
        $view = [
            "submodulos" => self::submodulos,
            "IDProfessor" => $IDProfessor,
            "Alunos" => DB::select("SELECT 
                    a.id,
                    m.Nome
                FROM matriculas m
                INNER JOIN alunos a ON(a.IDMatricula = m.id)
                INNER JOIN turmas t ON(t.id = a.IDTurma)
                INNER JOIN escolas e ON(e.id = t.IDEscola)
                WHERE e.IDOrg = $IDOrg
            ")
        ];

        if($id){
            $Registro = Apoio::find($id);
            $view['id'] = $id;
            $view['Evolucao'] = json_decode($Registro['DSEvolucao']);
            $view['Registro'] = $Registro;
        }

        return view('Apoio.cadastro',$view);
    }

    public function getApoio($IDProfessor){
        $orgId = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT 
            m.Nome as Aluno,
            ap.id as IDApoio,
            ap.DTInicio,
            ap.DTTermino
        FROM apoio ap 
        INNER JOIN alunos a ON(ap.IDAluno = a.id)
        INNER JOIN matriculas m ON(m.id = a.id)
        WHERE ap.IDProfessor = $IDProfessor
        SQL;

        $Professores = DB::select($SQL);

        if(count($Professores) > 0){
            foreach($Professores as $d){
                $item = [];
                $item[] = $d->Aluno;
                $item[] = $d->DTIicio;
                $item[] = $d->DTTermino;
                $item[] = "<a href='".route('Professores/Apoio/Edit',["id" =>$d->IDApoio,"IDProfessor"=>$IDProfessor])."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($Professores)),
            "recordsFiltered" => intval(count($Professores)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }
}
