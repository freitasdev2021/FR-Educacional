<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auxiliar;
use App\Models\Escola;
use App\Http\Controllers\PedagogosController;
use App\Http\Controllers\SMTPController;
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

    public static function getEscolaAdm($IDUser){
        return Auxiliar::select('IDEscola')->where('IDUser',$IDUser)->first()->IDEscola;
    }


    public function getAuxiliares(){
        $IDOrg = Auth::user()->id_org;
        switch(Auth::user()->tipo){
            case 2:
                $IDEscolas = implode(',',SecretariasController::getEscolasRede(Auth::user()->id_org));
                $WHERE = "WHERE u.id_org=".Auth::user()->id_org;
            break;
            case 2.5:
                $IDEscolas = implode(',',SecretariasController::getEscolasRede(Auth::user()->id_org));
                $WHERE = "WHERE u.id_org=".Auth::user()->id_org;
            break;
            case 4:
                $IDEscolas = self::getEscolaDiretor(Auth::user()->id);
                $WHERE = "WHERE e.id IN('".$IDEscolas."')";
            break;
            case 4.5:
                $IDEscola = AuxiliaresController::getEscolaAdm(Auth::user()->id);
                $WHERE = 'WHERE e.id='.$IDEscola;
            break;
        }
        $Auxiliares = DB::select("SELECT 
                a.id as IDAuxiliar,
                e.Nome as Escola,
                a.Nome as Auxiliar,
                TPContrato
            FROM auxiliares a
            LEFT JOIN users u ON(u.IDProfissional = a.id) 
            LEFT JOIN escolas e ON(a.IDEscola = e.id) 
            LEFT JOIN organizacoes o ON(e.IDOrg = o.id) 
            WHERE e.IDOrg = $IDOrg GROUP BY a.id
            ");

        if(count($Auxiliares) > 0){
            foreach($Auxiliares as $d){
                $item = [];
                $item[] = $d->Auxiliar;
                $item[] = $d->Escola;
                $item[] = $d->TPContrato;
                $item[] = "<a href='".route('Auxiliares/Edit',$d->IDAuxiliar)."' class='btn btn-primary btn-xs'>Editar</a>";
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
            $Registro = Auxiliar::find($id);
            $STAcesso = User::select('STAcesso')->where('IDProfissional',$id)->where('tipo',4.5)->first();
            $view['id'] = $id;
            $view['STAcesso'] = $STAcesso->STAcesso;
            $view['Registro'] = $Registro;
        }

        return view('Auxiliares.cadastro',$view);
    }

    public function save(Request $request){
        try{
            $aid = '';
            $dir = $request->all();
            if($request->id){
                $Auxiliar = Auxiliar::find($request->id);
                $Auxiliar->update($dir);
                $rout = 'Auxiliares/Edit';
                $aid = $request->id;
                $userTipo = User::find($request->IDUser);
                $userTipo->update(array("tipo"=>$request->Tipo));
                if($request->credenciais){
                    $rnd = rand(100000,999999);
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::find($request->IDUser);
                    SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                    //dd($Usuario->toArray());
                    $Usuario->update([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => $request->Tipo,
                        'password' => Hash::make($rnd)
                    ]);
                    //dd("aqui");
                }else{
                    $mensagem = 'Salvamento Feito com Sucesso!';
                }
            }else{
                $rnd = rand(100000,999999);
                SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                $auxId = Auxiliar::create($dir);
                $usId = User::create([
                    'name' => $request->Nome,
                    'email' => $request->Email,
                    'tipo' => $dir['Tipo'],
                    'password' => Hash::make($rnd),
                    'IDProfissional' => $auxId->id,
                    'id_org' => Auth::user()->id_org
                ]);

                Auxiliar::find($auxId->id)->update(["IDUser"=>$usId->id]);
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
