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

    public function excluir($IDVaga,$IDEscola){
        $QTVagas = Vaga::find($IDVaga)->QTVagas;
        Escola::find($IDEscola)->update([
            "QTVagas" => Escola::find($IDEscola)->QTVagas - $QTVagas
        ]);

        Vaga::find($IDVaga)->delete();

        return redirect()->back();

    }

    public function save(Request $request){
        try{
            $data = $request->all();
            Escola::find($request->IDEscola)->update([
                "QTVagas" => Escola::find($request->IDEscola)->QTVagas + $request->QTVagas
            ]);
            Vaga::create($data);
            $mensagem = "Vaga cadastrada com Sucesso!";
            $aid = '';
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
                    v.QTVagas,
                    v.IDEscola
                FROM vagas v
                INNER JOIN escolas e ON(v.IDEscola = e.id)
                WHERE e.IDOrg = $IDOrg
            SQL;
            $registros = DB::select($SQL);
        }else{
            $registros = Vaga::select('Faixa','INIMatricula','TERMatricula','QTVagas','id','IDEscola')->whereIn('IDEscola',EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional))->get();
        }
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Faixa;
                $item[] = $r->INIMatricula;
                $item[] = $r->TERMatricula;
                $item[] = $r->QTVagas;
                $item[] = "<a href='".route('Escolas/Vagas/Excluir',["IDVaga"=>$r->id,"IDEscola"=>$r->IDEscola])."' class='btn btn-danger btn-xs'>Excluir</a>";
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
