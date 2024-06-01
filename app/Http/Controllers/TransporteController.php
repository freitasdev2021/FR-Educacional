<?php

namespace App\Http\Controllers;

use App\Models\Motorista;
use App\Models\Rota;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Terceirizada;
use App\Models\Paradas;
use App\Models\Rodagem;

class TransporteController extends Controller
{
    //
    public const submodulos = array([
        "nome" => "Itinerários",
        "endereco" => "index",
        "rota" => "Transporte/index"
    ],[
        "nome" => "Veiculos",
        "endereco" => "Veiculos",
        "rota" => "Transporte/Veiculos/index"
    ],[
        "nome" => "Motoristas",
        "endereco" => "Motoristas",
        "rota" => "Transporte/Motoristas/index"
    ],[
        "nome" => "Terceirizadas",
        "endereco" => "Terceirizadas",
        "rota" => "Transporte/Terceirizadas/index"
    ]);
    //
    public const cadastroSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Transporte/Edit"
    ],[
        "nome" => "Paradas",
        "endereco" => "Paradas",
        "rota" => "Transporte/Paradas"
    ],[
        "nome" => "Rodagem",
        "endereco" => "Rodagem",
        "rota" => "Transporte/Rodagem"
    ]);
    //
    public function index(){
        return view('Transporte.index',[
            "submodulos" => self::submodulos
        ]);
    }
    //
    public function paradas($idrota){
        $view = [
            "IDRota" =>$idrota,
            'id' => '',
            'submodulos' => self::cadastroSubmodulos
        ];

        return view('Transporte.paradas',$view);
    }
    //
    public function rodagem($idrota){
        $view = [
            "IDRota" =>$idrota,
            'id' => '',
            'submodulos' => self::cadastroSubmodulos
        ];

        return view('Transporte.rodagem',$view);
    }
    //
    public function veiculos(){
        return view('Transporte.veiculos',[
            "submodulos" => self::submodulos
        ]);
    }
    //
    public function terceirizadas(){
        return view('Transporte.terceirizadas',[
            "submodulos" => self::submodulos
        ]);
    }
    //
    public function motoristas(){
        return view('Transporte.motoristas',[
            "submodulos" => self::submodulos
        ]);
    }
    //
    public function getRotas(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            m.Nome as Motorista,
            r.Descricao,
            r.Partida,
            r.Chegada,
            r.HoraPartida,
            r.HoraChegada,
            r.Turno,
            r.id as IDRota
        FROM rotas r 
        INNER JOIN motoristas m ON(m.id = r.IDMotorista)
        INNER JOIN organizacoes o ON(o.id = m.IDOrganizacao)
        WHERE o.id = $idorg
        ";

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Motorista;
                $item[] = $r->Descricao;
                $item[] = $r->Partida."(".$r->HoraPartida.")";
                $item[] = $r->Chegada."(".$r->HoraChegada.")";
                $item[] = "";
                $item[] = $r->Turno;
                $item[] = "<a href='".route('Transporte/Edit',$r->IDRota)."' class='btn btn-primary btn-xs'>Editar</a>";
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
    //
    public function getVeiculos(){
        $auth = Auth::user()->id_org;
        $SQL = "SELECT v.Nome,v.Marca,v.Placa,v.Cor,v.id as IDVeiculo FROM veiculos v INNER JOIN organizacoes o ON(o.id = v.IDOrganizacao) = o.id = $auth";
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Marca;
                $item[] = $r->Placa;
                $item[] = $r->Cor;
                $item[] = "<a href='".route('Transporte/Veiculos/Edit',$r->IDVeiculo)."' class='btn btn-primary btn-xs'>Editar</a>";
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
    //
    public function getMotoristas(){
        $auth = Auth::user()->id_org;
        $SQL = "SELECT 
            m.id AS IDMotorista,
            m.Nome AS Motorista,
            m.Admissao,
            m.TerminoContrato,
            m.CEP,
            m.Rua,
            m.UF,
            m.Cidade,
            m.Bairro,
            m.Numero
        FROM Motoristas m
        INNER JOIN organizacoes o ON m.IDOrganizacao = o.id
        WHERE o.id = $auth";
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Motorista;
                $item[] = Controller::data($r->Admissao,'d/m/Y');
                $item[] = Controller::data($r->TerminoContrato,'d/m/Y');
                $item[] = $r->Rua.", ".$r->Numero." ".$r->Bairro." ".$r->Cidade."/".$r->UF;
                $item[] = "<a href='".route('Transporte/Motoristas/Edit',$r->IDMotorista)."' class='btn btn-primary btn-xs'>Editar</a>";
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
    //
    public function getTerceirizadas(){
        $auth = Auth::user()->id_org;
        $SQL = "SELECT 
            t.id AS IDTerceirizada,
            t.TerminoContrato,
            t.CEP,
            t.Rua,
            t.UF,
            t.Cidade,
            t.Bairro,
            t.Numero,
            t.CNPJ,
            t.Nome
        FROM terceirizadas t
        INNER JOIN organizacoes o ON t.IDOrg = o.id
        WHERE o.id = $auth";
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->CNPJ;
                $item[] = Controller::data($r->TerminoContrato,'d/m/Y');
                $item[] = $r->Rua.", ".$r->Numero." ".$r->Bairro." ".$r->Cidade."/".$r->UF;
                $item[] = "<a href='".route('Transporte/Terceirizadas/Edit',$r->IDTerceirizada)."' class='btn btn-primary btn-xs'>Editar</a>";
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
    //CADASTROS
    public function cadastro($id=null){
        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'IDRota' => '',
            'Motoristas' => Motorista::where('IDOrganizacao',Auth::user()->id_org)->get()
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $SQL = "SELECT 
                m.id as IDMotorista,
                m.Nome as Motorista,
                r.Descricao,
                r.Partida,
                r.Chegada,
                r.Distancia,
                r.Turno,
                r.DiasJSON,
                r.HoraPartida,
                r.HoraChegada,
                r.id as IDRota
            FROM rotas r 
            INNER JOIN motoristas m ON(m.id = r.IDMotorista)
            INNER JOIN organizacoes o ON(o.id = m.IDOrganizacao)
            WHERE o.id = $idorg AND r.id = $id";
            $registro = DB::select($SQL)[0];
            $view['submodulos'][0]['nome'] = 'Cadastro';
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Transporte/Edit";
            //
            unset($view['submodulos'][1]);
            unset($view['submodulos'][2]);
            unset($view['submodulos'][3]);

            array_push($view['submodulos'],[
                "nome" => "Paradas",
                "endereco" => "Paradas",
                "rota" => "Transporte/Paradas"
            ]);

            array_push($view['submodulos'],[
                "nome" => "Rodagem",
                "endereco" => "Rodagem",
                "rota" => "Transporte/Rodagem"
            ]);
            $view['id'] = $id;
            $view['Registro'] = $registro;
            $view['Dias'] = json_decode($registro->DiasJSON,true);
        }

        return view('Transporte.cadastro',$view);
    }
    //CADASTRO MERENDA
    public function cadastroMotoristas($id=null){
        $view = [
            "submodulos" => self::submodulos,
            'id' => ''
        ];

        if($id){
            $auth = Auth::user()->id_org;
            $SQL = "SELECT 
                m.id AS IDMotorista,
                m.Nome AS Motorista,
                m.Admissao,
                m.TerminoContrato,
                m.CEP,
                m.Rua,
                m.Celular,
                m.Email,
                m.Nascimento,
                m.UF,
                m.Cidade,
                m.Bairro,
                m.Numero
            FROM motoristas m
            INNER JOIN organizacoes o ON m.IDOrganizacao = o.id
            WHERE o.id = $auth AND m.id = $id";

            $registro = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Transporte/Motoristas/Edit";
            $view['id'] = $id;
            $view['Registro'] = $registro[0];
        }

        return view('Transporte.cadastroMotoristas',$view);
    }
    //
    public function cadastroVeiculos($id=null){
        $view = [
            "submodulos" => self::submodulos,
            'id' => ''
        ];

        if($id){
            $auth = Auth::user()->id_org;
            $SQL = "SELECT v.Nome,v.Marca,v.Placa,v.KMAquisicao,v.Cor,v.id as IDVeiculo FROM veiculos v INNER JOIN organizacoes o ON(o.id = v.IDOrganizacao) = o.id = $auth AND v.id = $id";

            $registro = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Transporte/Veiculos/Edit";
            $view['id'] = $id;
            $view['Registro'] = $registro[0];
        }

        return view('Transporte.cadastroVeiculos',$view);
    }
    //
    public function cadastroTerceirizadas($id=null){
        $view = [
            "submodulos" => self::submodulos,
            'id' => ''
        ];

        if($id){
            $auth = Auth::user()->id_org;
            $SQL = "SELECT 
                t.id AS IDTerceirizada,
                t.TerminoContrato,
                t.CEP,
                t.Rua,
                t.UF,
                t.Cidade,
                t.Bairro,
                t.Telefone,
                t.Email,
                t.Numero,
                t.CNPJ,
                t.Nome
            FROM terceirizadas t
            INNER JOIN organizacoes o ON t.IDOrg = o.id
            WHERE o.id = $auth AND t.id = $id";

            $registro = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Transporte/Terceirizadas/Edit";
            $view['id'] = $id;
            $view['Registro'] = $registro[0];
        }

        return view('Transporte.cadastroTerceirizadas',$view);
    }
    //
    public function saveVeiculos(Request $request){
        try{
            $ter = $request->all();
            $ter['Placa'] = preg_replace('/\D/', '', $request->Placa);
            if($request->id){
                Veiculo::find($request->id)->update($ter);
                $aid = $request->id;
                $rout = "Transporte/Veiculos/Edit";
            }else{
                $ter['IDOrganizacao'] = Auth::user()->id_org;
                Veiculo::create($ter);
                $rout = "Transporte/Veiculos/Novo";
                $aid = "";
            }
            $mensagem = "Empresa Cadastrada com Sucesso";
            $status = 'success';
        }catch(\Throwable $th){
            $aid = '';
            $mensagem = $th->getMessage();
            $status = 'error'; 
            $rout = "Transporte/Veiculos/Novo";
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    //
    public function saveMotoristas(Request $request){
        try{
            $ter = $request->all();
            $ter['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $ter['Celular'] = preg_replace('/\D/', '', $request->Celular);
            if($request->id){
                Motorista::find($request->id)->update($ter);
                $aid = $request->id;
                $rout = "Transporte/Motoristas/Edit";
            }else{
                $ter['IDOrganizacao'] = Auth::user()->id_org;
                Motorista::create($ter);
                $rout = "Transporte/Motoristas/Novo";
                $aid = "";
            }
            $mensagem = "Motorista Cadastrado com Sucesso";
            $status = 'success';
        }catch(\Throwable $th){
            $aid = '';
            $mensagem = $th->getMessage();
            $status = 'error'; 
            $rout = "Transporte/Motoristas/Novo";
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    //
    public function save(Request $request){
        try{
            $ter = $request->all();
            $ter['CEP'] = preg_replace('/\D/', '', $request->CEP);
            $ter['Celular'] = preg_replace('/\D/', '', $request->Celular);
            $dias = [];
            foreach($request->Dia as $d){
                array_push($dias,$d);
            }
            $ter['DiasJSON'] = json_encode($dias,JSON_UNESCAPED_UNICODE);
            if($request->id){
                Rota::find($request->id)->update($ter);
                $aid = $request->id;
                $rout = "Transporte/Edit";
            }else{
                $ter['IDOrganizacao'] = Auth::user()->id_org;
                Rota::create($ter);
                $rout = "Transporte/Novo";
                $aid = "";
            }
            $mensagem = "Itinerário Cadastrado com Sucesso";
            $status = 'success';
        }catch(\Throwable $th){
            $aid = '';
            $mensagem = $th->getMessage();
            $status = 'error'; 
            $rout = "Transporte/Novo";
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    //
    public function saveParadas(Request $request){
        try{
            $ter = $request->all();
            $ter['IDRota'] = $request->IDRota;
            if($request->id){
                Paradas::find($request->id)->update($ter);
                $aid = array("id"=> $request->id,"idrota" => $request->IDRota);
                $rout = "Transporte/Paradas/Edit";
            }else{
                $ter['IDOrganizacao'] = Auth::user()->id_org;
                Paradas::create($ter);
                $rout = "Transporte/Paradas/Novo";
                $aid = $request->IDRota;
            }
            $mensagem = "Ponto de Parada Cadastrado com Sucesso";
            $status = 'success';
        }catch(\Throwable $th){
            $aid = '';
            $mensagem = $th->getMessage();
            $status = 'error'; 
            $rout = "Transporte/Novo";
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    //
    public function saveRodagem(Request $request){
        try{
            Rodagem::create($request->all());
            $KMRodados = $request->KMFinal - $request->KMInicial;
            $aid = $request->IDRota;
            $status = "success";
            $mensagem = "Rodagem Cadastrada com Sucesso! o Veículo Rodou ".$KMRodados." Quilometros";
            $rout = "Transporte/Rodagem/Novo";
        }catch(\Throwable $th){
            $aid = $request->IDRota;
            $mensagem = $th->getMessage();
            $rout = "Transporte/Rodagem/Novo";
            $status = "error";
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }
    //
    public function getRodagem($id){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            rod.KMInicial,rod.KMFinal,rod.id as IDRodagem, v.Nome as Veiculo
        FROM rodagem rod 
        INNER JOIN veiculos v ON(v.id = rod.IDVeiculo) 
        INNER JOIN organizacoes o ON(v.IDOrganizacao = o.id) 
        WHERE o.id = $idorg AND rod.IDRota = $id";
        
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Veiculo;
                $item[] = $r->KMInicial;
                $item[] = $r->KMInicial;
                $item[] = $r->KMFinal - $r->KMInicial;
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
    //
    public function getParadas($id){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT p.Nome,p.Hora,p.id as IDParada,r.id as IDRota FROM paradas p INNER JOIN rotas r ON(p.IDRota = r.id) INNER JOIN motoristas m ON(r.IDMotorista = m.id) WHERE IDRota = $id AND m.IDOrganizacao = $idorg ";
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Hora;
                $item[] = "<a href='".route('Transporte/Paradas/Edit',['idrota' => $id,'id' => $r->IDRota])."' class='btn btn-primary btn-xs'>Editar</a>";
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
    //
    public function cadastroParadas($idrota,$id=null){
        $view = [
            "submodulos" => self::cadastroSubmodulos,
            'id' => 0,
            'IDRota' => $idrota
        ];

        if($id){
            $idorg = Auth::user()->id_org;
            $SQL = "SELECT p.Nome,p.Hora,p.id as IDParada FROM paradas p INNER JOIN rotas r ON(p.IDRota = r.id) INNER JOIN motoristas m ON(r.IDMotorista = m.id) WHERE IDRota = $id AND m.IDOrganizacao = $idorg AND p.id = $id ";
            // $view['submodulos'][1]['rota'] = 'Transporte/Paradas/Edit';
            // $view['submodulos'][1]['endereco'] = 'Edit';
            $view['Registro'] = DB::select($SQL)[0];
            $view['id'] = $id;
        }

        return view("Transporte.cadastroParadas",$view);
    }
    //
    public function cadastroRodagem($idrota){
        return view('Transporte.cadastroRodagem',[
            'id' => 0,
            'IDRota' => $idrota,
            'submodulos' => self::cadastroSubmodulos,
            'Veiculos' => Veiculo::where('IDOrganizacao',Auth::user()->id_org)->get()
        ]);
    }
    // 
}
