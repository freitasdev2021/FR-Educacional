<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EscolasController;
use App\Http\Controllers\PedagogosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Sala;
use App\Models\Turma;
use App\Models\Escola;

class SalasController extends Controller
{
    public const submodulos = EscolasController::submodulos;

    public function index(){
        return view("Salas.index",[
            "submodulos" => self::submodulos
        ]);
    }

    public function cadastro($id = null){
        $view = [
            "submodulos"=>self::submodulos,
            'id' => '',
            'escolas' => Escola::all()->where('IDOrg',Auth::user()->id_org)
        ];

        if($id){
            $view['Registros'] = Sala::find($id);
            $view['Turmas'] = Turma::select('Nome','Serie','INITurma','TERTurma')->where('IDSala',$id)->get();
            $view['id'] = $id;
        }

        return view('Salas.cadastro',$view);
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            if(Auth::user()->tipo == 4){
                $data['IDEscola'] = self::getEscolaDiretor(Auth::user()->id);
            }

            if($request->id){
                Sala::find($request->id)->update($data);
                $mensagem = "Sala Editada com Sucesso!";
                $rota = 'Escolas/Salas/Edit';
                $aid = $request->id;
            }else{
                Sala::create($data);
                $mensagem = "Sala cadastrada com Sucesso!";
                $aid = '';
                $rota = 'Escolas/Salas/Novo';
            }
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Escolas/Salas/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getSalas(){
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $IDOrg = Auth::user()->id_org;
            $SQL = <<<SQL
                SELECT 
                    s.NMSala,s.TMSala,s.id,s.TPSala
                FROM salas s
                INNER JOIN escolas e ON(s.IDEscola = e.id)
                WHERE e.IDOrg = $IDOrg
            SQL;
            $registros = DB::select($SQL);
        }else{
            $registros = Sala::select('NMSala','TMSala','id','TPSala')->whereIn('IDEscola',EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional))->get();
        }
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->TPSala;
                $item[] = $r->NMSala;
                $item[] = $r->TMSala . " M2";
                $item[] = "<a href='".route('Escolas/Salas/Edit',$r->id)."' class='btn btn-primary btn-xs'>Abrir</a>";
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
