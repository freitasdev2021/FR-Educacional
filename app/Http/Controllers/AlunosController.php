<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aluno;
use App\Models\Matriculas;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\Renovacoes;
use App\Models\Responsavel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Storage;

class AlunosController extends Controller
{
    public const submodulos = array([
        "nome" => "Alunos",
        "endereco" => "index",
        "rota" => "Alunos/index"
    ]);

    public function index(){
        return view('Alunos.index',[
            'submodulos' => self::submodulos,
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function cadastro($id=null){
        $idorg = Auth::user()->id_iorg;
        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            'Turmas' => Turma::where('IDEscola',self::getEscolaDiretor(Auth::user()->id))->get()
        ];

        if($id){
            $SQL = "SELECT 
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
                t.Serie as Serie,
                m.Nascimento as Nascimento,
                r.Vencimento as Vencimento
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            WHERE o.id = $idorg AND a.id = $id  
            ";

            $view['submodulos']['endereco'] = "Edit";
            $view['submodulos']['rota'] = 'Alunos/Cadastro';
            $view['Registro'] = DB::select($SQL);
        }

        return view('Alunos.cadastro',$view);
    }

    public function save(Request $request){
        try{
            if(!$request->id){

                $CResidencia = $request->file('CResidencia')->getClientOriginalName();
                $request->file('CResidencia')->storeAs('aluno_'.$request->Email,$CResidencia,'public');

                $RGPaisAnexo = $request->file('RGPaisAnexo')->getClientOriginalName();
                $request->file('RGPaisAnexo')->storeAs('aluno_'.$request->Email,$RGPaisAnexo,'public');

                $AnexoRG = $request->file('AnexoRG')->getClientOriginalName();
                $request->file('AnexoRG')->storeAs('aluno_'.$request->Email,$AnexoRG,'public');

                $Historico = $request->file('Historico')->getClientOriginalName();
                $request->file('Historico')->storeAs('aluno_'.$request->Email,$Historico,'public');

                $Foto = $request->file('Foto')->getClientOriginalName();
                $request->file('Foto')->storeAs('aluno_'.$request->Email,$Foto,'public');

                $matricula = array(
                    'AnexoRG' => $AnexoRG,
                    'CResidencia' => $CResidencia,
                    'Historico' => $Historico,
                    'Nome' => $request->Nome,
                    'CPF' => preg_replace('/\D/', '', $request->CPF),
                    'RG' => preg_replace('/\D/', '', $request->RG),
                    'CEP' => preg_replace('/\D/', '', $request->CEP),
                    'Rua' => $request->Rua,
                    'Email' => $request->Email,
                    'Celular' => preg_replace('/\D/', '', $request->Celular),
                    'UF' => $request->UF,
                    'Cidade' => $request->Cidade,
                    'BolsaFamilia' => $request->BolsaFamilia,
                    'Alergia' => $request->Alergia,
                    'Transporte' => $request->Transporte,
                    'NEE' => $request->NEE,
                    'AMedico' => $request->AMedico,
                    'APsicologico' => $request->APsicologico,
                    'Aprovado' => 1,
                    'Foto' => $Foto,
                    'Nascimento' => $request->Nascimento
                );

                //dd($request->file('AnexoRG')->getClientOriginalName());
    
                $createMatricula = Matriculas::create($matricula);

                $aluno = array(
                    'IDMatricula' => $createMatricula->id,
                    'STAluno' => 0,
                    'IDTurma' => $request->IDTurma
                );

                $createAluno = Aluno::create($aluno);

                $renovacao = array(
                    'IDAluno' => $createAluno->id,
                    'Aprovado' => 1,
                    'Vencimento' => $request->Vencimento,
                    'ANO' => 2024
                );

                Renovacoes::create($renovacao);

                $responsavel = array(
                    'IDAluno' => $createAluno->id,
                    'RGPaisAnexo' => $request->RGPaisAnexo,
                    'RGPais' => preg_replace('/\D/', '', $request->RGPais),
                    'NMResponsavel' => $request->NMResponsavel,
                    'EmailResponsavel' => $request->EmailResponsavel,
                    'CLResponsavel' => preg_replace('/\D/', '', $request->CLResponsavel),
                    'CPFResponsavel' => preg_replace('/\D/', '', $request->CPFResponsavel)
                );

                Responsavel::create($responsavel);
                $aid = '';
                $rout = 'Alunos/Novo';
            }else{
                $aid = $request->id;
                $rout = 'Alunos/Edit';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso!';
            $rout = 'Alunos/Novo';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Novo';
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getAlunos(){
        $idorg = Auth::user()->id_org;

        if(Auth::user()->tipo == 4){
            $IDEscola = self::getEscolaDiretor(Auth::user()->id);
            $AND = ' AND e.id='.$IDEscola;
            //dd($AND);
        }else{
            $AND = '';
        }

        $SQL = "SELECT
            a.id as IDAluno, 
            m.Nome as Nome,
            t.Nome as Turma,
            e.Nome as Escola,
            t.Serie as Serie,
            m.Nascimento as Nascimento,
            r.Vencimento as Vencimento,
            a.STAluno,
            m.Foto,
            m.Email
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN renovacoes r ON(r.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        WHERE o.id = $idorg $AND    
        ";

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                switch($r->STAluno){
                    case "0":
                        $Situacao = 'Frequente';
                    break;
                    case "1":
                        $Situacao = "Evadido";
                    break;
                    case "2":
                        $Situacao = "Desistente";
                    break;
                    case "3":
                        $Situacao = "Desligado";
                    break;
                    case "4":
                        $Situacao = "Egresso";
                    break;
                    case "5":
                        $Situacao = "Transferido Para Outra Rede";
                    break;
                }

                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Turma;
                (Auth::user()->tipo == 2) ? $item[] = $r->Escola : '';
                $item[] = $r->Serie;
                $item[] = Controller::data($r->Nascimento,'d/m/Y');
                $item[] = Controller::data($r->Vencimento,'d/m/Y');
                $item[] = $Situacao;
                $item[] = "
                <a href='".route('Alunos/Edit',$r->IDAluno)."' class='btn btn-primary btn-xs'>Visualizar</a>
                ";
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
