<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransporteController extends Controller
{
    //
    public const submodulos = array([
        "nome" => "Rotas",
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
    public function index(){
        return view('Transporte.index',[
            "submodulos" => self::submodulos
        ]);
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
            .r.Chegada,
            r.HoraPartida,
            r.HoraChegada,
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
                $item[] = "<a href='".route('Transporte/Cadastro',$r->IDRota)."' class='btn btn-primary btn-xs'>Editar</a>";
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
        $SQL = "SELECT v.Nome,v.Marca,v.Placa,v.Cor FROM veiculos v INNER JOIN organizacoes o ON(o.id = v.IDOrganizacao) = o.id = $auth";
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Nome;
                $item[] = $r->Marca;
                $item[] = $r->Placa;
                $item[] = $r->Cor;
                $item[] = "<a href='".route('Transporte/Veiculos/Cadastro',$r->IDVeiculo)."' class='btn btn-primary btn-xs'>Editar</a>";
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
            'id' => ''
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $SQL = "SELECT 
                m.Nome as Motorista,
                r.Descricao,
                r.Partida,
                .r.Chegada,
                r.HoraPartida,
                r.HoraChegada,
                r.RotaJSON,
                r.id as IDRota
            FROM rotas r 
            INNER JOIN motoristas m ON(m.id = r.IDMotorista)
            INNER JOIN organizacoes o ON(o.id = m.IDOrganizacao)
            WHERE o.id = $idorg AND m.id = $id";
            $registro = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Transportes/Cadastro";
            $view['id'] = $id;
            $view['Registro'] = $registro[0];
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
                p.id AS IDProfessor,
                m.Nome AS Motorista,
                p.Admissao,
                p.TerminoContrato,
                p.CEP,
                p.Rua,
                p.UF,
                p.Cidade,
                p.Bairro,
                p.Numero
            FROM Motoristas m
            INNER JOIN organizacoes o ON m.IDOrganizacao = o.id
            WHERE o.id = $auth AND m.id = $id";

            $registro = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Transportes/Motorista/Cadastro";
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
            $SQL = "SELECT v.Nome,v.Marca,v.Placa,v.Cor FROM veiculos v INNER JOIN organizacoes o ON(o.id = v.IDOrganizacao) = o.id = $auth AND v.id = $id";

            $registro = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Transportes/Veiculos/Cadastro";
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
                t.Numero,
                t.CNPJ,
                t.Nome
            FROM terceirizadas t
            INNER JOIN organizacoes o ON t.IDOrg = o.id
            WHERE o.id = $auth AND t.id = $id";

            $registro = DB::select($SQL);
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Transportes/Terceirizadas/Cadastro";
            $view['id'] = $id;
            $view['Registro'] = $registro[0];
        }

        return view('Transporte.cadastroTerceirizadas',$view);
    }
    //
}
