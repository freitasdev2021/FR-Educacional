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

    public const profSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "Apoio",
        "rota" => "Apoio/index"
    ]);
    public function index($IDProfessor=null){

        if($IDProfessor){
            $view = [
                "submodulos" => self::submodulos,
                'IDProfessor' => $IDProfessor
            ];
        }else{
            $view = array(
                "submodulos" => self::profSubmodulos,
                "IDProfessor" => Auth::user()->IDProfissional
            );
        }
        return view('Apoio.index',$view);
    }

    public function cadastro($IDProfessor,$id){
        $IDOrg = Auth::user()->id_org;
        $view = [
            "submodulos" => (Auth::user()->tipo == 6) ? self::profSubmodulos : self::submodulos,
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
            $view['Evolucao'] = json_decode($Registro->DSEvolucao);
            $view['Registro'] = $Registro;
        }

        return view('Apoio.cadastro',$view);
    }

    public function save(Request $request){
        try{
            if($request->id){
                // dd(Turno::find($request->id)->first());
                Apoio::find($request->id)->update($request->all());
                $aid = array(
                    'IDProfessor' => $request->IDProfessor,
                    'id' => $request->id
                );
                $rout = "Professores/Apoio/Edit";
            }else{
                Apoio::create($request->all());
                $aid = array(
                    'IDProfessor' => $request->IDProfessor,
                    'id' => 0
                );
                $rout = "Professores/Apoio/Novo/";
            }
            $mensagem = "Turno Salvo com Sucesso";
            $status = "success";
        }catch(\Throwable $th){
            $mensagem = "Erro ao Salvar o Turno ".$th;
            $status = 'error';
            $aid = array(
                'IDProfessor' => $request->IDProfessor,
                'id' => 0
            );
            $rout = "Professores/Apoio/Novo/";
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveEvolucao(Request $request){
        try{
           // dd(Turno::find($request->id)->first());
            $Apoio = Apoio::find($request->id);
            if(!empty($Apoio->DSEvolucao)){
                $Evolucao = json_decode($Apoio->DSEvolucao,true);
                array_push($Evolucao,array(
                    "Evolucao" => $request->Evolucao,
                    "Data" => date('d/m/Y')
                ));
                
                $Apoio->update([
                    "DSEvolucao" =>json_encode($Evolucao)
                ]);
            }else{
                $Apoio->update([
                    "DSEvolucao" => json_encode([array(
                        "Evolucao" => $request->Evolucao,
                        "Data" => date('d/m/Y')
                    )])
                ]);
            }
            $aid = array(
                'IDProfessor' => $request->IDProfessor,
                'id' => $request->id
            );
            $rout = "Professores/Apoio/Edit";
            $mensagem = "Turno Salvo com Sucesso";
            $status = "success";
        }catch(\Throwable $th){
            $mensagem = "Erro ao Salvar o Turno ".$th;
            $status = 'error';
            $aid = array(
                'IDProfessor' => $request->IDProfessor,
                'id' => 0
            );
            $rout = "Professores/Apoio/Edit";
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }


    public function getApoio($IDProfessor){
        $orgId = Auth::user()->id_org;
        if(Auth::user()->tipo == 5){
            $IDEscolas = implode(',',PedagogosController::getEscolasPedagogo(Auth::user()->IDProfissional));
            $SQL = <<<SQL
            SELECT 
                m.Nome as Aluno,
                ap.id as IDApoio,
                ap.DTInicio,
                ap.DTTermino
            FROM apoio ap 
            INNER JOIN alunos a ON(ap.IDAluno = a.id)
            INNER JOIN matriculas m ON(m.id = a.id)
            INNER JOIN turmas t ON(t.id = a.IDTurma)
            WHERE t.IDEscola IN($IDEscolas)
            SQL;
        }elseif(Auth::user()->tipo == 6){
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
        }elseif(Auth::user()->tipo == 4){
            $IDEscolas = implode(',',self::getEscolaDiretor(Auth::user()->id));
            $SQL = <<<SQL
            SELECT 
                m.Nome as Aluno,
                ap.id as IDApoio,
                ap.DTInicio,
                ap.DTTermino
            FROM apoio ap 
            INNER JOIN alunos a ON(ap.IDAluno = a.id)
            INNER JOIN matriculas m ON(m.id = a.id)
            INNER JOIN turmas t ON(t.id = a.IDTurma)
            WHERE t.IDEscola IN($IDEscolas)
            SQL;
        }

        $Professores = DB::select($SQL);

        if(count($Professores) > 0){
            foreach($Professores as $d){
                $item = [];
                $item[] = $d->Aluno;
                $item[] = $d->DTInicio;
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
