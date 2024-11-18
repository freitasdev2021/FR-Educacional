<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CI;
use App\Models\User;
use App\Models\CIMensagem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CIController extends Controller
{

    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "CI/index"
    ]);

    public const destinatarioSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "Destinatario",
        "rota" => "CI/Destinatario"
    ]);

    public const cadastroSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "CI/index"
    ],[
        "nome" => "Mensagens",
        "endereco" => "Mensagens",
        "rota" => "CI/Mensagens"
    ]);

    public function index(){
        return view('comunicacao_interna.index',[
            "submodulos" => self::submodulos
        ]);
    }

    public function destinatarioIndex(){
        $IDUser = Auth::user()->id;

        $SQL = <<<SQL
        SELECT 
            ci.Assunto,
            ci.Mensagem,
            CONCAT(
                '[',
                GROUP_CONCAT(
                    DISTINCT
                    '{'
                    ,'"Mensagem":"', m.Mensagem, '"'
                    ,'}'
                    SEPARATOR ','
                ),
                ']'
            ) AS Mensagens
        FROM comunicacao_interna ci
        INNER JOIN ci_mensagens m ON ci.id = m.IDComunicacao
        WHERE m.IDUser = $IDUser
        GROUP BY m.IDComunicacao;
        SQL;

        if(CIMensagem::where('IDUser',$IDUser)->whereNull('Visualizacao')){
            CIMensagem::where('IDUser',$IDUser)->update([
                "Visualizacao" => date('Y-m-d H:i:s')
            ]); 
        }
        
        return view('comunicacao_interna.destinatarioIndex',[
            "submodulos" => self::destinatarioSubmodulos,
            "Comunicacoes" => DB::select($SQL)
        ]);
    }

    public function getCi(){
        $IDOrg = Auth::user()->id_org;

        $SQL = <<<SQL
            SELECT 
                Assunto,
                id
            FROM comunicacao_interna ci
            WHERE ci.IDOrg = $IDOrg
        SQL;

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Assunto;
                $item[] = "<a href=".route('CI/Edit',$r->id)." class='btn btn-fr btn-xs'>Abrir</a>&nbsp<a href=".route('CI/Delete',$r->id)." class='btn btn-fr btn-xs'>Encerrar</a>";
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

    public function getMensagens($IDCi){
        $IDOrg = Auth::user()->id_org;

        $SQL = <<<SQL
            SELECT 
                cm.Mensagem,
                cm.Visualizacao,
                cm.id
            FROM ci_mensagens cm
            WHERE cm.IDComunicacao = $IDCi
        SQL;

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Mensagem;
                $item[] = $r->Visualizacao;
                $item[] = "";
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

    public function cadastro($id=null){
        $view = [
            "submodulos" => self::submodulos,
            "id" => ""
        ];

        if($id){
            $view['Registro'] = CI::find($id);
            $view['id'] = $id;
            $view['submodulos'] = self::cadastroSubmodulos;
        }

        return view('comunicacao_interna.cadastro',$view);
    }

    public function cadastroMensagens($IDCi){
        $view = [
            "submodulos" => self::cadastroSubmodulos,
            "id" => $IDCi,
            "Destinatarios" => User::where("id_org",Auth::user()->id_org)->whereIn('tipo',[2,2.5,3,4,4.5,5,5.5,6,6.5])->get()
        ];

        return view('comunicacao_interna.cadastroMensagem',$view);
    }

    public function mensagens($id){
        return view("comunicacao_interna.mensagens",[
            "submodulos" => self::cadastroSubmodulos,
            "id" => $id
        ]);
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['IDOrg'] = Auth::user()->id_org;
            if($request->id){
                CI::find($request->id)->update($data);
                $aid = $request->id;
                $rota = 'CI/Edit';
            }else{
                CI::create($data);
                $aid = '';
                $rota = 'CI/Novo';
            }
            $mensagem = "Salvamento feito com Sucesso";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'CI/Novo';
            $mensagem = 'Erro '.$th->getMessage();
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function saveMensagens($IDCi,Request $request){
        try{
            foreach($request->Destinatario as $d){
                CIMensagem::create([
                    "Mensagem" => $request->Mensagem,
                    "IDUser" => $d,
                    "IDComunicacao" => $IDCi
                ]);
            }
            $aid = $IDCi;
            $rota = 'CI/Mensagens/Novo';
            $mensagem = "Mensagens Enviadas";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'CI/Novo';
            $mensagem = 'Erro '.$th->getMessage();
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }
}
