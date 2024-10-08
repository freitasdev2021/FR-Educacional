<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escola;
use App\Models\Diretor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DiretoresController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Diretores/index"
    ]);
    public function index(){
        return view('Diretores.index',[
            "submodulos" => self::submodulos
        ]);
    }


    public function getDiretores(){
        $diretores = DB::select("SELECT d.id as IDDiretor,e.Nome as Escola,d.Nome as Diretor,d.Admissao,d.TerminoContrato,d.CEP,d.Rua,d.UF,d.Cidade,d.Bairro,d.Numero FROM diretores d INNER JOIN escolas e ON(d.IDEscola = e.id) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = '".Auth::user()->id_org."' ");
        if(count($diretores) > 0){
            foreach($diretores as $d){
                $item = [];
                $item[] = $d->Diretor;
                $item[] = Controller::data($d->Admissao,'d/m/Y');
                $item[] = Controller::data($d->TerminoContrato,'d/m/Y');
                $item[] = $d->Escola;
                $item[] = $d->Rua.", ".$d->Numero." ".$d->Bairro." ".$d->Cidade."/".$d->UF;
                $item[] = "<a href='".route('Diretores/Edit',$d->IDDiretor)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($diretores)),
            "recordsFiltered" => intval(count($diretores)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function cadastro($id=null){

        $diretor = DB::select("SELECT e.Nome as Escola,d.*,e.id as IDEscola FROM diretores d INNER JOIN escolas e ON(d.IDEscola = e.id) INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = '".Auth::user()->id_org."' AND d.id = '$id' ");

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            "Escolas" => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Diretores/Edit";
            $view['id'] = $id;
            $view['Registro'] = $diretor[0];
        }

        return view('Diretores.cadastro',$view);

    }

    public function save(Request $request){
        try{
            $aid = '';
            $dir = $request->all();
            $dir['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $dir['Celular'] = preg_replace('/\D/', '', $request->Celular);
            if($request->id){
                $Diretor = Diretor::find($request->id);
                $Diretor->update($dir);
                $rout = 'Diretores/Edit';
                $aid = $request->id;
                if($request->credenciais){
                    $rnd = rand(100000,999999);
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::where('IDProfissional',$request->id)->where('id_org',Auth::user()->id_org);
                    $Usuario->update([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => 4,
                        'password' => Hash::make($rnd)
                    ]);
                }else{
                    $mensagem = 'Salvamento Feito com Sucesso!';
                }
            }else{
                $dirId = Diretor::create($dir);
                $rnd = rand(100000,999999);
                SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                User::create([
                    'name' => $request->Nome,
                    'email' => $request->Email,
                    'tipo' => 4,
                    'password' => Hash::make($rnd),
                    'IDProfissional' => $dirId->id,
                    'id_org' => Auth::user()->id_org
                ]);
                $rout = 'Diretores/Novo';
                $mensagem = 'Salvamento Feito com Sucesso! as Credenciais de Login foram Enviadas no Email Cadastrado';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Diretores/Novo';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

}
