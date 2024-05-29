<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cardapio;
use App\Models\EstoqueMovimentacao;
use App\Models\Estoque;
use App\Models\Escola;

class CardapioController extends Controller
{

    public const submodulos = array([
        "nome" => "Cardápio",
        "endereco" => "index",
        "rota" => "Merenda/index"
    ],[
        "nome" => "Estoque",
        "endereco" => "Estoque",
        "rota" => "Merenda/Estoque/index"
    ],[
        "nome" => "Movimentações",
        "endereco" => "Movimentacoes",
        "rota" => "Merenda/Movimentacoes/index"
    ]);

    public function index(){
        return view('Cardapio.index',[
            "submodulos" => self::submodulos
        ]);
    }

    public function estoque(){
        return view('Cardapio.estoque',[
            "submodulos" => self::submodulos
        ]);
    }

    public function movimentacao(){
        return view('Cardapio.movimentacao',[
            "submodulos" => self::submodulos
        ]);
    }

    public function cadastro($id=null){
        $view = [
            "submodulos" => self::submodulos,
            "Escolas" => Escola::where("IDOrg",Auth::user()->id_org),
            'id' => ''
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $view['submodulos'][0]['rota'] = 'Merenda/Edit'; 
            $view['submodulos'][0]['endereco'] = 'Edit';
            $view['id'] = $id;
            $view['Registro'] = DB::select("SELECT c.id as IDMerenda,c.Dia,c.Turno,c.Descricao,e.Nome as Escola FROM cardapio c INNER JOIN escolas e INNER JOIN organizacoes o ON(o.id = e.IDOrg) WHERE o.id = $idorg AND c.id = $id")[0];
        }
        return view('Cardapio.cadastroMerenda',$view);
    }

    public function cadastroEstoque($id=null){
        $view = [
            "submodulos" => self::submodulos,
            "Escolas" => Escola::where("IDOrg",Auth::user()->id_org),
            'id' => ''
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $SQL = "SELECT 
                es.Quantidade,
                es.TPUnidade as Unidade,
                es.Vencimento,
                es.Item,
                es.created_at as Cadastro,
                es.id as IDEstoque 
            FROM 
                estoque es INNER JOIN escolas e ON(e.id = es.IDEscola)
                INNER JOIN organizacoes o ON (e.IDOrg = o.id)
                WHERE o.id = $idorg AND es.id = $id
            ";
            $view['submodulos'][0]['rota'] = 'Merenda/Estoque/Edit'; 
            $view['submodulos'][0]['endereco'] = 'Edit';
            $view['id'] = $id;
            $view['Registro'] = DB::select($SQL)[0];
        }
        return view('Cardapio.cadastroEstoque',$view);
    }

    public function cadastroMovimentacoes($id=null){
        $idorg = Auth::user()->id_org;
        $view = [
            "submodulos" => self::submodulos,
            "Escolas" => Escola::where("IDOrg",Auth::user()->id_org),
            'id' => '',
            'Estoque' => DB::select("SELECT es.id as IDEstoque,es.Item FROM estoque es INNER JOIN escolas e ON(es.IDEscola = e.id ) INNER JOIN organizacoes o ON(o.id = e.IDOrg) WHERE o.id = $idorg ")
        ];

        $idorg = Auth::user()->id_org;

        if($id){
            $SQL = "SELECT 
                es.Item,
                es.TPUnidade as Unidade,
                m.Quantidade,
                m.TPMovimentacao,
                m.created_at as Cadastro
            FROM estoque_movimentacao m 
                INNER JOIN estoque es ON(es.id = m.IDEstoque)
                INNER JOIN escolas e ON(e.id = es.IDEscola)
                INNER JOIN organizacoes o ON (e.IDOrg = o.id)
                WHERE o.id = $idorg AND m.id = $id
            ";
            $view['submodulos'][0]['rota'] = 'Merenda/Estoque/Edit'; 
            $view['submodulos'][0]['endereco'] = 'Edit';
            $view['id'] = $id;
            $view['Registro'] = DB::select($SQL);
        }
        return view('Cardapio.cadastroMovimentacao',$view);
    }

    public function getMerenda(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT c.id as IDMerenda,c.Dia,c.Turno,c.Descricao,e.Nome as Escola FROM cardapio c INNER JOIN escolas e INNER JOIN organizacoes o ON(o.id = e.IDOrg) WHERE o.id = $idorg AND e.id = 2";

        $merendas = DB::select($SQL);
        if(count($merendas) > 0){
            foreach($merendas as $m){
                $item = [];
                $item[] = $m->Escola;
                $item[] = $m->Descricao;
                $item[] = Controller::data($m->Dia,'d/m/Y');
                $item[] = $m->Turno;
                $item[] = "<a href='".route('Merenda/Edit',$m->IDMerenda)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($merendas)),
            "recordsFiltered" => intval(count($merendas)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function save(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Merenda Salva com Sucesso';
            if($request->id){
                Cardapio::find($request->id)->update($request->all());
                $aid = $request->id;
                $rout = 'Merenda/Edit';
            }else{
                Cardapio::create($request->all());
                $rout = 'Merenda/Novo';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Merenda/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveEstoque(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Estoque Salvo com Sucesso';
            if($request->id){
                Estoque::find($request->id)->update($request->all());
                $aid = $request->id;
                $rout = 'Merenda/Estoque/Edit';
            }else{
                Estoque::create($request->all());
                $rout = 'Merenda/Estoque/Novo';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Merenda/Estoque/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveMovimentacoes(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Movimentação Salva com Sucesso';
            EstoqueMovimentacao::create($request->all());
            if($request->TPMovimentacao == "1"){
                DB::update("UPDATE estoque e SET e.Quantidade = e.Quantidade + $request->Quantidade WHERE id = $request->IDEstoque");
            }else{
                DB::update("UPDATE estoque e SET e.Quantidade = e.Quantidade - $request->Quantidade WHERE id = $request->IDEstoque");
            }
            $rout = 'Merenda/Novo';
            $aid = '';
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Merenda/Movimentacoes/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }


    public function getEstoque(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            es.Quantidade,
            es.TPUnidade as Unidade,
            es.Vencimento,
            es.Item,
            es.created_at as Cadastro,
            es.id as IDEstoque 
        FROM 
            estoque es INNER JOIN escolas e ON(e.id = es.IDEscola)
            INNER JOIN organizacoes o ON (e.IDOrg = o.id)
            WHERE o.id = $idorg
        ";

        $estoque = DB::select($SQL);
        if(count($estoque) > 0){
            foreach($estoque as $e){
                $item = [];
                $item[] = $e->Item;
                $item[] = $e->Quantidade;
                $item[] = $e->Unidade;
                $item[] = Controller::data($e->Cadastro,'d/m/Y');
                $item[] = Controller::data($e->Vencimento,'d/m/Y');
                $item[] = "<a href='".route('Merenda/Estoque/Edit',$e->IDEstoque)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($estoque)),
            "recordsFiltered" => intval(count($estoque)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function getMovimentacoes(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            es.Item,
            es.TPUnidade as Unidade,
            m.Quantidade,
            m.TPMovimentacao,
            m.created_at as Cadastro
        FROM estoque_movimentacao m 
            INNER JOIN estoque es ON(es.id = m.IDEstoque)
            INNER JOIN escolas e ON(e.id = es.IDEscola)
            INNER JOIN organizacoes o ON (e.IDOrg = o.id)
            WHERE o.id = $idorg
        ";

        $estoque = DB::select($SQL);
        if(count($estoque) > 0){
            foreach($estoque as $e){
                $item = [];
                $item[] = $e->Item;
                $item[] = Controller::data($e->Cadastro,'d/m/Y');
                $item[] = $e->Quantidade;
                $item[] = ($e->TPMovimentacao == '0') ? 'Saida' : 'Entrada';
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($estoque)),
            "recordsFiltered" => intval(count($estoque)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }
}
