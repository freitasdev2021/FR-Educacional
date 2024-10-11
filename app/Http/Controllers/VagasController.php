<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EscolasController;
use App\Models\Vaga;
use App\Models\Escola;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class VagasController extends Controller
{
    public const submodulos = EscolasController::submodulos;

    public function index(){
        return view('Vagas.index',[
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
            $view['Registro'] = Vaga::find($id);
            $view['id'] = $id;
        }

        return view('Vagas.cadastro',$view);
    }

    public function save(Request $request){
        try{
            $data = $request->all();

            if($request->id){
                Escola::find($request->IDEscola)->update([
                    "QTVagas" => Escola::find($request->IDEscola)->QTVagas + $request->QTVagas
                ]);
                Vaga::find($request->id)->update($data);
                $mensagem = "Vaga Editada com Sucesso!";
                $aid = $request->id;
            }else{
                Escola::find($request->IDEscola)->update([
                    "QTVagas" => Escola::find($request->IDEscola)->QTVagas + $request->QTVagas
                ]);
                Vaga::create($data);
                $mensagem = "Vaga cadastrada com Sucesso!";
                $aid = '';
            }
            $status = 'success';
            $rota = 'Escolas/Vagas/Novo';
        }catch(\Throwable $th){
            $rota = 'Escolas/Vagas/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getVagas(){
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $IDOrg = Auth::user()->id_org;
            $SQL = <<<SQL
                SELECT 
                    v.id,
                    v.Faixa,
                    v.INIMatricula,
                    v.TERMatricula,
                    v.QTVagas
                FROM vagas v
                INNER JOIN escolas e ON(v.IDEscola = e.id)
                WHERE e.IDOrg = $IDOrg
            SQL;
            $registros = DB::select($SQL);
        }elseif(Auth::user()->tipo == 5){
            $registros = Vaga::select('Faixa','INIMatricula','TERMatricula','QTVagas','id')->where('IDEscola',PedagogosController::getEscolasPedagogo(Auth::user()->IDProfissional))->get();
        }elseif(Auth::user()->tipo == 6){
            $registros = Vaga::select('Faixa','INIMatricula','TERMatricula','QTVagas','id')->where('IDEscola',ProfessoresController::getEscolasProfessor(Auth::user()->IDProfissional))->get();
        }elseif(Auth::user()->tipo == 4){
            $registros = Vaga::select('Faixa','INIMatricula','TERMatricula','QTVagas','id')->where('IDEscola',self::getEscolaDiretor(Auth::user()->id))->get();
        }elseif(Auth::user()->tipo == 4.5){
            $registros = Vaga::select('Faixa','INIMatricula','TERMatricula','QTVagas','id')->where('IDEscola',AuxiliaresController::getEscolaAdm(Auth::user()->id))->get();
        }
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Faixa;
                $item[] = $r->INIMatricula;
                $item[] = $r->TERMatricula;
                $item[] = $r->QTVagas;
                $item[] = "<a href='".route('Escolas/Vagas/Edit',$r->id)."' class='btn btn-primary btn-xs'>Abrir</a>";
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
