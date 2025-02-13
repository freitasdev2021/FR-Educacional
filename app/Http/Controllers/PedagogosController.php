<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedagogo;
use App\Models\Alocacao;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PedagogosController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Pedagogos/index"
    ]);
    public function index(){
        return view('Pedagogos.index',[
            "submodulos" => self::submodulos
        ]);
    }


    public function getPedagogos(){

        $AND = " AND a.IDEscola IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        
        $orgId = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT 
            p.id AS IDPedagogo,
            CONCAT('[', GROUP_CONCAT('"', e.Nome, '"' SEPARATOR ','), ']') AS Escolas,
            p.Nome AS Pedagogo,
            p.Email,
            TPContrato
        FROM pedagogos p
        INNER JOIN alocacoes a ON a.IDProfissional = p.id
        INNER JOIN escolas e ON e.id = a.IDEscola
        INNER JOIN organizacoes o ON e.IDOrg = o.id
        WHERE o.id = $orgId $AND AND a.TPProfissional = "PEDA"
        GROUP BY p.id, p.Nome;
        SQL;

        $Pedagogos = DB::select($SQL);

        if(count($Pedagogos) > 0){
            foreach($Pedagogos as $d){
                $item = [];
                $item[] = $d->Pedagogo;
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = implode(",",json_decode($d->Escolas)) : '';
                $item[] = $d->Email;
                $item[] = $d->TPContrato;
                $item[] = "<a href='".route('Pedagogos/Edit',$d->IDPedagogo)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($Pedagogos)),
            "recordsFiltered" => intval(count($Pedagogos)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function cadastro($id=null){
        $orgId = Auth::user()->id_org;
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'EscolasRegistradas' => Controller::array_associative_unique(DB::select("SELECT e.id as IDEscola,e.Nome FROM escolas e INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $orgId ORDER BY e.Nome"))
        ];

        if($id){
            
            $orgId = Auth::user()->id_org;
            $sqlScola = <<<SQL
            SELECT 
                e.id AS IDEscola,
                e.Nome,
                a.INITurno,
                a.TERTurno,
                CASE 
                    WHEN (SELECT COUNT(IDEscola) 
                        FROM alocacoes a2 
                        WHERE a2.IDProfissional = $id 
                            AND a2.IDEscola = e.id 
                            AND a2.TPProfissional = 'PEDA') > 0 THEN 1 
                    ELSE 0 
                END AS Alocado
            FROM escolas e 
            LEFT JOIN alocacoes a ON e.id = a.IDEscola AND a.IDProfissional = $id AND a.TPProfissional = 'PEDA'
            INNER JOIN organizacoes o ON e.IDOrg = o.id
            WHERE o.id = $orgId
            GROUP BY e.id, e.Nome, a.INITurno, a.TERTurno
            ORDER BY e.Nome;
            SQL;

           
            $escolasUm = Controller::array_associative_unique(DB::select($sqlScola));

            $Pedagogo = Pedagogo::find($id);
            $STAcesso = User::select('STAcesso')->where('IDProfissional',$id)->where('tipo',5)->first();

            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Pedagogos/Edit";
            $view['id'] = $id;
            $view['Registro'] = $Pedagogo;
            $view['STAcesso'] = $STAcesso->STAcesso;
            $view['EscolasRegistradas'] = $escolasUm;
        }

        return view('Pedagogos.cadastro',$view);

    }

    public static function getEscolasPedagogo($IDPedagogo){
        $SQL = "SELECT e.id as IDEscola FROM escolas e INNER JOIN alocacoes a ON(a.IDEscola = e.id) INNER JOIN pedagogos p  ON(p.id = a.IDProfissional) WHERE p.id = $IDPedagogo AND a.TPProfissional = 'PEDA' ";
        $IDEscolas = [];
        foreach(DB::select($SQL) as $e){
            array_push($IDEscolas,$e->IDEscola);
        }
        return $IDEscolas;
    }

    public function save(Request $request){
        //dd("AQUI");
        try{
            $aid = '';
            $dir = $request->all();
            if($request->id){
                $Pedagogo = Pedagogo::find($request->id);
                $Pedagogo->update($dir);
                $rout = 'Pedagogos/Edit';
                $aid = $request->id;
                if($request->credenciais){
                    $rnd = rand(100000,999999);
                    SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::find($request->IDUser);
                    $Usuario->update([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => 5,
                        'password' => Hash::make($rnd)
                    ]);
                }else{
                    $mensagem = 'Salvamento Feito com Sucesso!';
                }
                ////
                if($request->alocacoes){

                    $Alocacao = Alocacao::where('IDProfissional',$request->id)->where('TPProfissional','PEDA');
                    $Alocacao->delete();

                    $alocacoes = [];
                    $iniTurno = [];
                    $terTurno = [];
                    $escolas = $request->Escola;

                    foreach($request->INITur as $it){
                        if(!is_null($it)){
                            array_push($iniTurno,$it);
                        }
                    }
    
                    foreach($request->TERTur as $tt){
                        if(!is_null($tt)){
                            array_push($terTurno,$tt);
                        }
                    }
    
                    for($i=0; $i<count($escolas);$i++){
                        $alocacoes[] = [
                            'IDEscola' => $escolas[$i],
                            "INITurno" => $iniTurno[$i],
                            "TERTurno" => $terTurno[$i],
                            "IDProfissional" => $request->id,
                            'TPProfissional' => 'PEDA'
                        ];
                    }

                    //dd($alocacoes);
                    
                    foreach($alocacoes as $al){
                        Alocacao::create($al);
                    }
                }
                ///
            }else{
                $alocacoes = [];

                $rnd = rand(100000,999999);
                SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));

                $pedaId = Pedagogo::create($dir);


                function filterNull($var){
                    return !is_null($var);
                }

                $iniTurno = [];
                $terTurno = [];
                $escolas = $request->Escola;
                
                foreach($request->INITur as $it){
                    if(!is_null($it)){
                        array_push($iniTurno,$it);
                    }
                }

                foreach($request->TERTur as $tt){
                    if(!is_null($tt)){
                        array_push($terTurno,$tt);
                    }
                }

                for($i=0; $i<count($escolas);$i++){
                    $alocacoes[] = [
                        'IDEscola' => $escolas[$i],
                        'IDProfissional' => $pedaId->id,
                        "INITurno" => $iniTurno[$i],
                        "TERTurno" => $terTurno[$i],
                        'TPProfissional' => 'PEDA'
                    ];
                }
                
                foreach($alocacoes as $al){
                    Alocacao::create($al);
                }

                $usId = User::create([
                    'name' => $request->Nome,
                    'email' => $request->Email,
                    'tipo' => 5,
                    'password' => Hash::make($rnd),
                    'IDProfissional' => $pedaId->id,
                    'id_org' => Auth::user()->id_org
                ]);

                Pedagogo::find($pedaId->id)->update(["IDUser"=>$usId->id]);
                $rout = 'Pedagogos/Novo';
                $mensagem = 'Salvamento Feito com Sucesso! as Credenciais de Login foram Enviadas no Email Cadastrado';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Pedagogos/Novo';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
}
