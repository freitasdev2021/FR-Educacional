<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auxiliar;
use App\Models\Escola;
use App\Http\Controllers\PedagogosController;
use App\Http\Controllers\EscolasController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuxiliaresController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Auxiliares/index"
    ]);
    public function index(){
        return view('Auxiliares.index',[
            "submodulos" => self::submodulos
        ]);
    }


    public function getAuxiliares(){
        switch(Auth::user()->tipo){
            case 2:
                $IDEscolas = implode(',',SecretariasController::getEscolasRede(Auth::user()->IDProfissional));
                $WHERE = "WHERE e.id IN('".$IDEscolas."')";
            break;
            case 4:
                $IDEscolas = self::getEscolaDiretor(Auth::user()->id);
                $WHERE = "WHERE e.id IN('".$IDEscolas."')";
            break;
            case 5:
                $IDEscolas = implode(',',PedagogosController::getEscolasPedagogo(Auth::user()->IDProfissional));
                $WHERE = "WHERE e.id IN('".$IDEscolas."')";
            break;
        }
        $Auxiliares = DB::select("SELECT 
                a.id as IDDiretor,
                a.Nome as Escola,
                a.Nome as Auxiliar,
                a.Admissao,
                a.TerminoContrato,
                a.CEP,
                a.Rua,
                a.UF,
                a.Cidade,
                a.Bairro,
                a.Numero 
            FROM Auxiliares a 
            INNER JOIN escolas e ON(a.IDEscola = e.id) 
            INNER JOIN organizacoes o ON(e.IDOrg = o.id) 
            $WHERE
            ");
        if(count($Auxiliares) > 0){
            foreach($Auxiliares as $d){
                $item = [];
                $item[] = $d->Auxiliar;
                $item[] = Controller::data($d->Admissao,'d/m/Y');
                $item[] = Controller::data($d->TerminoContrato,'d/m/Y');
                $item[] = $d->Escola;
                $item[] = $d->Rua.", ".$d->Numero." ".$d->Bairro." ".$d->Cidade."/".$d->UF;
                $item[] = "<a href='".route('Auxiliares/Edit',$d->IDDiretor)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($Auxiliares)),
            "recordsFiltered" => intval(count($Auxiliares)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function cadastro($id=null){

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            "Escolas" => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ];

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Auxiliar::find($id);
        }

        return view('Auxiliares.cadastro',$view);
    }

    public function save(Request $request){
        try{
            $aid = '';
            $dir = $request->all();
            $dir['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $dir['Celular'] = preg_replace('/\D/', '', $request->Celular);
            if($request->id){
                $Auxiliar = Auxiliar::find($request->id);
                $Auxiliar->update($dir);
                $rout = 'Auxiliares/Edit';
                $aid = $request->id;
                $userTipo = User::find($request->IDUser);
                $userTipo->update(array("tipo"=>$request->Tipo));
                if($request->credenciais){
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::find($request->IDUser);
                    //dd($Usuario->toArray());
                    $Usuario->update([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => $request->Tipo,
                        'password' => Hash::make(rand(100000,999999))
                    ]);
                    //dd("aqui");
                }else{
                    $mensagem = 'Salvamento Feito com Sucesso!';
                }
            }else{
                $dirId = Auxiliar::create($dir);
                User::create([
                    'name' => $request->Nome,
                    'email' => $request->Email,
                    'tipo' => $dir['Tipo'],
                    'password' => Hash::make(rand(100000,999999)),
                    'IDProfissional' => $dirId->id,
                    'id_org' => Auth::user()->id_org
                ]);
                $rout = 'Auxiliares/Novo';
                $mensagem = 'Salvamento Feito com Sucesso! as Credenciais de Login foram Enviadas no Email Cadastrado';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $rout = 'Auxiliares/Novo';
            $status = 'error';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
}
