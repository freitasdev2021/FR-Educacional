<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacao;
use App\Http\Requests\secretariasRequest;
class SecretariasController extends Controller
{

    public const submodulos = array([
        "nome" => "Cadastros",
        "endereco" => "index",
        "rota" => "Secretarias/index"
    ],[
        "nome" => "Relatórios",
        "endereco" => "Relatorios",
        "rota" => "Secretarias/Relatorios"
    ]);

    public function cadastro($id=null){

        $view = [
            "submodulos" => array([
                "nome" => "Cadastro",
                "endereco" => "Novo",
                "rota" => "Secretarias/Novo"
            ]),
            'id' => ''
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Secretarias/Edit";
            $view['id'] = $id;
            $view['Registro'] = Organizacao::all()->where('id',$id)->first();
            $view['end'] = json_decode($view['Registro']->Endereco);
        }

        return view('Secretarias.createSecretaria',$view);

    }

    public function save(SecretariasRequest $request){
        try{
            $org = $request->all();
            $org['Endereco'] = json_encode(array(
                "Rua" => $request->Rua,
                "Cidade" => $request->Cidade,
                "Bairro" => $request->Bairro,
                "UF" => $request->UF,
                "Numero" => $request->Numero,
                "CEP" => $request->CEP
            ));
            $aid = '';
            if($request->id){
                $Organizacao = Organizacao::find($request->id);
                $Organizacao->update($org);
                $rout = 'Secretarias/Edit';
                $aid = $request->id;
            }else{
                Organizacao::create($org);
                $rout = 'Secretarias/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar a Secretaría: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function index(){

        return view('Secretarias.index',[
            "submodulos" => self::submodulos
        ]);
    }

    public function getSecretarias(){
        if(Organizacao::count() > 0){
            foreach(Organizacao::all() as $o){
                $endJSON = json_decode($o->Endereco,true);
                $item = [];
                $item[] = $o->Organizacao;
                $item[] = $o->Email;
                $item[] = $endJSON['Rua']." ".$endJSON['Numero']." ".$endJSON['Bairro']." - ".$endJSON['Cidade']."/".$endJSON['UF'];
                $item[] = $o->UF;
                $item[] = $o->Cidade;
                $item[] = "<a href='".route('Secretarias/Edit',$o->id)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(Organizacao::count()),
            "recordsFiltered" => intval(Organizacao::count()),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }


    public function relatorios(){

        return view('Secretarias.relatorios',[
            "submodulos" => self::submodulos
        ]);
    }

}
