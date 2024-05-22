<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professor;
use App\Models\Escola;
use App\Models\Alocacao;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfessoresController extends Controller
{
    public const submodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Professores/index"
    ]);
    public function index(){
        return view('Professores.index',[
            "submodulos" => self::submodulos
        ]);
    }


    public function getProfessores(){

        $orgId = Auth::user()->id_org;
        $SQL = <<<SQL
        SELECT 
            p.id AS IDProfessor,
            CONCAT('[', GROUP_CONCAT('"', e.Nome, '"' SEPARATOR ','), ']') AS Escolas,
            p.Nome AS Professor,
            p.Admissao,
            p.TerminoContrato,
            p.CEP,
            p.Rua,
            p.UF,
            p.Cidade,
            p.Bairro,
            p.Numero
        FROM Professores p
        INNER JOIN alocacoes a ON a.IDProfissional = p.id
        INNER JOIN escolas e ON e.id = a.IDEscola
        INNER JOIN organizacoes o ON e.IDOrg = o.id
        WHERE o.id = $orgId
        GROUP BY p.id, p.Nome, p.Admissao, p.TerminoContrato, p.CEP, p.Rua, p.UF, p.Cidade, p.Bairro, p.Numero;

        SQL;

        $Professores = DB::select($SQL);

        if(count($Professores) > 0){
            foreach($Professores as $d){
                $item = [];
                $item[] = $d->Professor;
                $item[] = Controller::data($d->Admissao,'d/m/Y');
                $item[] = Controller::data($d->TerminoContrato,'d/m/Y');
                $item[] = implode(",",json_decode($d->Escolas));
                $item[] = $d->Rua.", ".$d->Numero." ".$d->Bairro." ".$d->Cidade."/".$d->UF;
                $item[] = "<a href='".route('Professores/Edit',$d->IDProfessor)."' class='btn btn-primary btn-xs'>Editar</a>";
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

    public function cadastro($id=null){
        $orgId = Auth::user()->id_org;
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'EscolasRegistradas' => Controller::array_associative_unique(DB::select("SELECT e.id as IDEscola,e.Nome FROM escolas e INNER JOIN organizacoes o ON(e.IDOrg = o.id) WHERE o.id = $orgId ORDER BY e.Nome"))
        ];

        if($id){
            
            $sqlScola = <<<SQL
            SELECT 
                e.id AS IDEscola,
                e.Nome,
                a.INITurno,
                a.TERTurno,
                CASE WHEN (SELECT COUNT(IDEscola) FROM alocacoes WHERE IDProfissional = $id AND IDEscola = e.id AND TPProfissional = 'PROF') THEN 1 ELSE 0 END AS Alocado
            FROM escolas e 
            LEFT JOIN alocacoes a ON e.id = a.IDEscola
            LEFT JOIN professores p ON p.id = a.IDProfissional
            INNER JOIN organizacoes o ON e.IDOrg = o.id  
            WHERE o.id = $orgId
            AND p.id NOT IN (
                SELECT IDProfissional 
                FROM alocacoes 
                WHERE IDProfissional != $id
            ) GROUP BY e.Nome ORDER BY e.Nome
            SQL;

           
            $escolasUm = Controller::array_associative_unique(DB::select($sqlScola));
            $SQL = <<<SQL
            SELECT 
                p.*
            FROM Professores p
            LEFT JOIN alocacoes a ON(a.IDProfissional = p.id)
            LEFT JOIN escolas e ON(e.id = a.IDEscola)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            WHERE o.id = $orgId AND p.id = $id
            SQL;

            $Professor = DB::select($SQL);


            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Professores/Edit";
            $view['id'] = $id;
            $view['Registro'] = $Professor[0];
            $view['EscolasRegistradas'] = $escolasUm;
        }

        return view('Professores.cadastro',$view);

    }

    public function save(Request $request){
        try{
            $aid = '';
            $dir = $request->all();
            $dir['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $dir['Celular'] = preg_replace('/\D/', '', $request->Celular);
            if($request->id){
                $Professor = Professor::find($request->id);
                $Professor->update($dir);
                $rout = 'Professores/Edit';
                $aid = $request->id;
                if($request->credenciais){
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::where('IDProfissional',$request->id)->where('id_org',Auth::user()->id_org);
                    $Usuario->update([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => 6,
                        'password' => Hash::make(rand(100000,999999))
                    ]);
                }else{
                    $mensagem = 'Salvamento Feito com Sucesso!';
                }
                ////
                if($request->alocacoes){
                    $alocacoes = [];
                    $iniTurno = [];
                    $terTurno = [];
                    $escolas = $request->Escola;
                    $Alocacao = Alocacao::where('IDProfissional',$request->id)->where('TPProfissional','PROF');
                    $Alocacao->delete();

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
                            'TPProfissional' => 'PROF'
                        ];
                    }
                    
                    foreach($alocacoes as $al){
                        Alocacao::create($al);
                    }
                }
                ///
            }else{
                $alocacoes = [];

                $profId = Professor::create($dir);


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
                        'IDProfissional' => $profId->id,
                        "INITurno" => $iniTurno[$i],
                        "TERTurno" => $terTurno[$i],
                        'TPProfissional' => 'PROF'
                    ];
                }
                
                foreach($alocacoes as $al){
                    Alocacao::create($al);
                }

                User::create([
                    'name' => $request->Nome,
                    'email' => $request->Email,
                    'tipo' => 6,
                    'password' => Hash::make(rand(100000,999999)),
                    'IDProfissional' => $profId->id,
                    'id_org' => Auth::user()->id_org
                ]);
                $rout = 'Professores/Novo';
                $mensagem = 'Salvamento Feito com Sucesso! as Credenciais de Login foram Enviadas no Email Cadastrado';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar a Escola: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

}
