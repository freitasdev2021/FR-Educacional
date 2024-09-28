<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EnderecosController extends Controller
{
    public const submodulos = array([
        "rota" => 'Enderecos/index',
        'endereco'=> 'Enderecos',
        'nome' => 'Endereços'
    ]);

    public function index(){
        return view('Enderecos.index',[
            "submodulos" => self::submodulos
        ]);
    }

    public function cadastro($id = null){
        $view = [
            "submodulos"=>self::submodulos,
            'id' => '',
            'escolas' => Endereco::all()->where('IDOrg',Auth::user()->id_org)
        ];

        if($id){
            $view['Registro'] = Endereco::find($id);
            $view['id'] = $id;
        }

        return view('Enderecos.cadastro',$view);
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $data['IDOrg'] = Auth::user()->id_org;
            if($request->id){
                Endereco::find($request->id)->update($data);
                $mensagem = "Endereço Editado com Sucesso!";
                $aid = $request->id;
            }else{
                Endereco::create($data);
                $mensagem = "Endereco cadastrado com Sucesso!";
                $aid = '';
            }
            $status = 'success';
            $rota = 'Enderecos/Novo';
        }catch(\Throwable $th){
            $rota = 'Enderecos/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getEnderecos(){
        $registros = Endereco::select('NMEndereco','EnderecoJSON','id')->where('IDOrg',Auth::user()->id_org)->get();
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->NMEndereco;
                $item[] = $r->EnderecoJSON;
                $item[] = "<a href='".route('Enderecos/Edit',$r->id)."' class='btn btn-primary btn-xs'>Abrir</a>";
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
