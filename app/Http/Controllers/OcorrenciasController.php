<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Ocorrencia;
use App\Models\ROcorrencia;
use App\Http\Controllers\ProfessoresController;
use App\Models\User;
use Illuminate\Http\Request;

class OcorrenciasController extends Controller
{
    public const submodulos = array([
        "rota" => 'Ocorrencias/index',
        'endereco'=> 'Ocorrencias',
        'nome' => 'Ocorrências'
    ]);

    public function index(){
        return view('Ocorrencias.index',[
            "submodulos" => self::submodulos
        ]);
    }

    public function cadastro($id = null){
        $IDEscolas = implode(",",ProfessoresController::getEscolasProfessor(Auth::user()->IDProfissional));
        $IDOrg = Auth::user()->id_org;
        $SQL = "SELECT
        m.Nome as Alvo,
        a.id
        FROM alunos a
        INNER JOIN matriculas m ON(m.id = a.IDMatricula)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas e ON(e.id = t.IDEscola)
        WHERE e.IDOrg = $IDOrg AND e.id IN($IDEscolas)
        ";
        $view = [
            "submodulos"=>self::submodulos,
            'id' => '',
            'escolas' => Ocorrencia::all()->where('IDOrg',Auth::user()->id_org),
            'Alvos' => DB::select($SQL)
        ];

        if($id){
            $view['Registro'] = Ocorrencia::find($id);
            $view['id'] = $id;
            $view['Respostas'] = ROcorrencia::all()->where("IDOcorrencia",$id);
        }

        return view('Ocorrencias.cadastro',$view);
    }

    public function responder(Request $request){
        ROcorrencia::create($request->all());
        return redirect()->back();
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['IDEmissor'] = Auth::user()->id;
            $data['IDEscola'] = DB::select("SELECT IDEscola FROM turmas t INNER JOIN alunos a ON(a.IDTurma = t.id) WHERE a.id = $request->IDAlvo");
            if($request->id){
                Ocorrencia::find($request->id)->update($data);
                $mensagem = "Ocorrencia Editada com Sucesso!";
                $aid = $request->id;
            }else{
                Ocorrencia::create($data);
                $mensagem = "Ocorrencia cadastrada com Sucesso!";
                $aid = '';
            }
            $status = 'success';
            $rota = 'Ocorrencias/Novo';
        }catch(\Throwable $th){
            $rota = 'Ocorrencias/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getOcorrencias(){
        $IDOrg = Auth::user()->id_org;
        $registros = DB::select("SELECT 
            m.Nome as Alvo,
            o.id,
            o.DTOcorrencia,
            u.name as Emissor,
            e.Nome as Escola
            FROM ocorrencias o 
            INNER JOIN users u ON(o.IDEmissor = u.id) 
            INNER JOIN alunos a ON(a.id = o.IDAlvo)
            INNER JOIN matriculas m ON(m.id = a.IDMatricula)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN escolas e ON(e.id = t.IDEscola)
            WHERE e.IDOrg = $IDOrg
            ");
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Escola;
                $item[] = $r->Emissor;
                $item[] = $r->Alvo;
                $item[] = $r->DTOcorrencia;
                $item[] = "<a href='".route('Ocorrencias/Edit',$r->id)."' class='btn btn-primary btn-xs'>Abrir</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($registros)),
            "recordsFiltered" => intval(count($registros)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }
}
