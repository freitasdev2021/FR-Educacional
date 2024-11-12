<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cardapio;
use App\Models\EstoqueMovimentacao;
use App\Http\Controllers\EscolasController;
use App\Http\Controllers\AlunosController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Aluno;
use App\Models\Estoque;
use App\Models\Alimento;
use App\Models\Nutricionista;
use App\Models\IMC;
use App\Models\Restricao;
use App\Models\ContratoMerenda;
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
    ],[
        "nome" => "Nutricionistas",
        "endereco" => "Nutricionistas",
        "rota" => "Merenda/Nutricionistas/index"
    ],
    [
        "nome" => "Alimentos",
        "endereco" => "Alimentos",
        "rota" => "Merenda/Alimentos/index"
    ],[
        "nome" => "Contratos",
        "endereco" => "Contratos",
        "rota" => "Merenda/Contratos/index"
    ],[
        "nome" => "Restrições Alimentares",
        "endereco" => "Restricoes",
        "rota" => "Merenda/Restricoes/index"
    ],[
        "nome" => "Evolução Corporal",
        "endereco" => "IMC",
        "rota" => "Merenda/IMC/index"
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

    public function nutricionistas(){
        return view('Cardapio.nutricionistas',[
            "submodulos" => self::submodulos
        ]);
    }

    public function alimentos(){
        return view('Cardapio.alimentos',[
            "submodulos" => self::submodulos
        ]);
    }

    public function contratos(){
        return view('Cardapio.contratos',[
            "submodulos" => self::submodulos
        ]);
    }

    public function restricoes(){
        return view('Cardapio.restricoes',[
            "submodulos" => self::submodulos
        ]);
    }

    public function IMC(){
        return view('Cardapio.imc',[
            "submodulos" => self::submodulos
        ]);
    }

    public function cadastroAlimentos($id=null){
        $view = array(
            "submodulos" => self::submodulos,
            "Escolas" => Escola::where('IDOrg',Auth::user()->id_org)->get()
        );

        if($id){
            $Alimento = Alimento::find($id);
            $view['Registro'] = $Alimento;
            $view['Nutrientes'] = json_decode($Alimento->Nutrientes);
            $view['id'] = $id;
        }

        return view('Cardapio.cadastroAlimento',$view);
    }

    public function cadastroContratos($id=null){
        $Contrato = ContratoMerenda::find($id);

        $view = array(
            "submodulos" => self::submodulos
        );

        if($id){
            $view['Registro'] = $Contrato;
            $view['id'] = $id;
            $view['AF'] = json_decode($Contrato->AF);
            $view['Empenho'] = json_decode($Contrato->Empenho);
        }

        return view('Cardapio.cadastroContrato',$view);
    }

    public function saveEmpenho(Request $request){
        $Contrato = ContratoMerenda::find($request->IDContrato);
        if(!is_null($Contrato->Empenho)){
            $Empenho = json_decode($Contrato->Empenho,true);

            array_push($Empenho,array(
                "Ordem" => $request->Ordem,
                "Valor" => $request->Valor)
            );

            $Contrato->update([
                "Empenho" => json_encode($Empenho)
            ]);
        }else{
            $Contrato->update([
                "Empenho" => json_encode(array([
                    "Ordem" => $request->Ordem,
                    "Valor" => $request->Valor
                ]))
            ]);
        }

        return redirect()->back();
    }

    public function saveAF(Request $request){
        $Contrato = ContratoMerenda::find($request->IDContrato);
        //dd($Contrato);
        if(!is_null($Contrato->AF)){
            $AF = json_decode($Contrato->AF,true);

            array_push($AF,array(
                "Autorizacao" => $request->Autorizacao,
                "Local" => $request->Local)
            );

            $Contrato->update([
                "AF" => json_encode($AF)
            ]);
        }else{
            $Contrato->update([
                "AF" => json_encode(array([
                    "Autorizacao" => $request->Autorizacao,
                    "Local" => $request->Local
                ]))
            ]);
        }
        

        return redirect()->back();
    }

    public function cadastroRestricoes($id=null){
        $IDAlunos = implode(",",EscolasController::getAlunosEscola(EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)));
        $view = array(
            "submodulos" => self::submodulos,
            "Alunos" => DB::select("SELECT m.Nome,a.id FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) WHERE a.id IN($IDAlunos)")
        );

        if($id){
            $view['Registro'] = Restricao::find($id);
            $view['id'] = $id;
        }

        return view('Cardapio.cadastroRestricao',$view);
    }

    public function cadastroNutricionistas($id=null){
        $view = array(
            "submodulos" => self::submodulos
        );

        if($id){
            $view['Registro'] = Nutricionista::find($id);
            $view['id'] = $id;
        }

        return view('Cardapio.cadastroNutricionista',$view);
    }

    public function cadastroImc($id=null){
        $IDAlunos = implode(",",EscolasController::getAlunosEscola(EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)));
        $view = array(
            "submodulos" => self::submodulos,
            "Alunos" => DB::select("SELECT m.Nome,a.id FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) WHERE a.id IN($IDAlunos)")
        );

        if($id){
            $view['Registro'] = IMC::find($id);
            $view['id'] = $id;
        }

        return view('Cardapio.cadastroImc',$view);
    }

    public function getRestricaoAluno($IDAluno){

    }

    public function cadastro($id=null){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            a.id AS IDAlimento,
            MAX(a.Nome) AS Nome,
            MAX(a.MDPreparo) AS MDPreparo,
            MAX(a.Nutrientes) AS Nutrientes,
            MAX(CASE WHEN cva.IDCardapio IS NOT NULL THEN 'checked' ELSE '' END) as AlimentoMarcado
        FROM alimentos a 
        INNER JOIN escolas e ON e.id = a.IDEscola
        INNER JOIN cardapio c ON e.id = c.IDEscola
        LEFT JOIN cardapio_vinculo_alimento cva ON c.id = cva.IDCardapio
        WHERE e.IDOrg = $idorg
        GROUP BY a.id;
        ";
        //dd(AlunosController::getAlunosTurmas());
        $view = [
            "submodulos" => self::submodulos,
            "Escolas" => Escola::where("IDOrg",Auth::user()->id_org),
            'id' => '',
            "Turmas" => AlunosController::getAlunosTurmas(),
            "Alimentos" => DB::select($SQL)
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

    public function saveNutricionistas(Request $request){
        try{
            if($request->id){
                $Nutricionista = Nutricionista::find($request->id);
                if($request->credenciais){
                    $rnd = rand(100000,999999);
                    SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::find($request->IDUser);
                    $Usuario->update([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => 3,
                        'password' => Hash::make($rnd)
                    ]);
                }

                $Nutricionista->update($request->all());
                $rout = 'Merenda/Nutricionistas/Edit';
                $aid = $request->id;
            }else{
                $rout = 'Merenda/Nutricionistas/Novo';
                $aid = "";
                $rnd = rand(100000,999999);
                SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                $mensagem = 'Salvamento Feito com Sucesso! as Credenciais de Login foram Enviadas no Email Cadastrado';
                $Usuario = User::create([
                    'name' => $request->Nome,
                    'email' => $request->Email,
                    'tipo' => 3,
                    'password' => Hash::make($rnd),
                    'id_org' => Auth::user()->id_org
                ]);

                $Data = $request->all();
                $Data['IDUser'] = $Usuario->id;
                Nutricionista::create($Data); 
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso!';
        }catch(\Thwrowable $th){
            $aid = "";
            $status = 'error';
            $mensagem = "Erro ao Salvar a Escola: ".$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
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
        $SQL = "SELECT c.id as IDMerenda,c.Tipo,c.Dia,c.Turno,c.Descricao,e.Nome as Escola FROM cardapio c INNER JOIN escolas e INNER JOIN organizacoes o ON(o.id = e.IDOrg) WHERE o.id = $idorg";

        $merendas = DB::select($SQL);
        if(count($merendas) > 0){
            foreach($merendas as $m){
                $item = [];
                $item[] = $m->Escola;
                $item[] = $m->Descricao;
                $item[] = Controller::data($m->Dia,'d/m/Y');
                $item[] = $m->Turno;
                $item[] = $m->Tipo;
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

    public function getNutricionistas(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT n.id as IDNutricionista,n.Nome,n.CRN,n.Email,n.Celular,n.TPContrato FROM nutricionistas n INNER JOIN users u ON(u.id = n.IDUser) WHERE u.id_org = $idorg";

        $merendas = DB::select($SQL);
        if(count($merendas) > 0){
            foreach($merendas as $m){
                $item = [];
                $item[] = $m->Nome;
                $item[] = $m->CRN;
                $item[] = $m->Celular;
                $item[] = $m->Email;
                $item[] = "<a href='".route('Merenda/Nutricionistas/Edit',$m->IDNutricionista)."' class='btn btn-primary btn-xs'>Editar</a>";
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

    public static function getAlimentos(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            a.id as IDAlimento,
            a.Nome,
            a.MDPreparo,
            a.Nutrientes
        FROM alimentos a 
        INNER JOIN escolas e ON(e.id = a.IDEscola) 
        
        WHERE e.IDOrg = $idorg";
       
        $merendas = DB::select($SQL);
        if(count($merendas) > 0){
            foreach($merendas as $m){
                $item = [];
                $ULNutrientes = "<ul>"; 
                
                foreach(json_decode($m->Nutrientes) as $Key => $nt){
                    $ULNutrientes .="<li>".$Key.": ".$nt."</li>";
                }
                $ULNutrientes.= "</ul>";
                $item[] = $m->Nome;
                $item[] = $m->MDPreparo;
                $item[] = $ULNutrientes;
                $item[] = "<a href='".route('Merenda/Alimentos/Edit',$m->IDAlimento)."' class='btn btn-primary btn-xs'>Editar</a>";
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

    public function getContratos(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT NMEmpresa,Vigencia,VLContrato,NProcesso,c.id as IDContrato FROM contratos_merenda c WHERE c.IDOrg = $idorg";
       
        $merendas = DB::select($SQL);
        if(count($merendas) > 0){
            foreach($merendas as $m){
                $item = [];
                $item[] = $m->NMEmpresa;
                $item[] = $m->Vigencia;
                $item[] = $m->VLContrato;
                $item[] = $m->NProcesso;
                $item[] = "<a href='".route('Merenda/Contratos/Edit',$m->IDContrato)."' class='btn btn-primary btn-xs'>Editar</a>";
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

    public function getRestricoes(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT ra.Substituto,ra.NMRestricao,m.Nome as Aluno,ra.id as IDRestricao
        FROM restricoes_alimentares ra 
        INNER JOIN alunos a ON(a.id = ra.IDAluno)
        INNER JOIN matriculas m ON(m.id = a.IDMatricula)
        INNER JOIN turmas t ON(t.id = a.IDTurma)
        INNER JOIN escolas e ON(t.IDEscola = e.id) 
        WHERE e.IDOrg = $idorg";
       
        $merendas = DB::select($SQL);
        if(count($merendas) > 0){
            foreach($merendas as $m){
                $item = [];
                
                $item[] = $m->Aluno;
                $item[] = $m->NMRestricao;
                $item[] = $m->Substituto;
                $item[] = "<a href='".route('Merenda/Restricoes/Edit',$m->IDRestricao)."' class='btn btn-primary btn-xs'>Editar</a>";
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
                DB::table('cardapio_vinculo_alimento')->where('IDCardapio', $request->id)->delete();
                DB::table('cardapio_vinculo')->where('IDCardapio',  $request->id)->delete();
                foreach($request->IDAlimento as $al){
                    DB::table('cardapio_vinculo_alimento')->insert([
                        'IDCardapio' => $request->id,
                        'IDAlimento' => $al
                    ]);
                }

                foreach($request->IDAluno as $aluno){
                    DB::table('cardapio_vinculo')->insert([
                        'IDCardapio' => $request->id,
                        'IDAluno' => $aluno
                    ]);
                }
                $aid = $request->id;
                $rout = 'Merenda/Edit';
            }else{
                $Cardapio = Cardapio::create($request->all());
                foreach($request->IDAlimento as $al){
                    DB::table('cardapio_vinculo_alimento')->insert([
                        'IDCardapio' => $Cardapio->id,
                        'IDAlimento' => $al
                    ]);
                }

                foreach($request->IDAluno as $aluno){
                    DB::table('cardapio_vinculo')->insert([
                        'IDCardapio' => $Cardapio->id,
                        'IDAluno' => $aluno
                    ]);
                }

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

    public function saveImc(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Salvo com Sucesso';
            $Data = $request->all();
            $Data['IMC'] = round($imc = floatval($request->Peso) / (floatval($request->Altura) * floatval($request->Altura)),2);
            if($request->id){
                IMC::find($request->id)->update($Data);
                $aid = $request->id;
                $rout = 'Merenda/IMC/Edit';
            }else{
                IMC::create($Data);
                $rout = 'Merenda/IMC/Novo';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Merenda/IMC/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveRestricoes(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Merenda Salva com Sucesso';
            if($request->id){
                Restricao::find($request->id)->update($request->all());
                $aid = $request->id;
                $rout = 'Merenda/Restricoes/Edit';
            }else{
                Restricao::create($request->all());
                $rout = 'Merenda/Restricoes/Novo';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Merenda/Restricoes/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveContratos(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Merenda Salva com Sucesso';
            $Data = $request->all();
            $Data['IDOrg'] = Auth::user()->id_org;
            if($request->id){
                ContratoMerenda::find($request->id)->update($Data);
                $aid = $request->id;
                $rout = 'Merenda/Contratos/Edit';
            }else{
                ContratoMerenda::create($Data);
                $rout = 'Merenda/Contratos/Novo';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Merenda/Contratos/Novo';
            $mensagem = 'Houve um Erro: '.$th;
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveAlimentos(Request $request){
        try{
            $status = 'success';
            $mensagem = 'Merenda Salva com Sucesso';
            $Data = $request->all();
            $Nutrientes = $request->only([
                'Zinco', 'Ferro', 'Calcio', 'Magnesio', 'Sodio', 'Potassio', 
                'Fosforo', 'VitaminaA', 'VitaminaC', 'VitaminaD', 'VitaminaE', 
                'VitaminaK', 'Proteina', 'Carboidrato', 'Gordura'
            ]);
            
            $Data['Nutrientes'] = json_encode($Nutrientes);

            if($request->id){
                Alimento::find($request->id)->update($Data);
                $aid = $request->id;
                $rout = 'Merenda/Alimentos/Edit';
            }else{
                Alimento::create($Data);
                $rout = 'Merenda/Alimentos/Novo';
                $aid = '';
            }
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Merenda/Alimentos/Novo';
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
            $data = $request->all();
            $data['IDEscola'] = self::getEscolaDiretor(Auth::user()->id);
            if($request->id){
                Estoque::find($request->id)->update($data);
                $aid = $request->id;
                $rout = 'Merenda/Estoque/Edit';
            }else{
                Estoque::create($data);
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

    public function getIMC(){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT ia.id as IDImc,ia.IMC,Altura,Peso,m.Nome as Aluno
        FROM imc_alunos ia 
        INNER JOIN alunos a ON(a.id = ia.IDAluno)
        INNER JOIN matriculas m ON(m.id = a.IDMatricula)
        INNER JOIN turmas t ON(t.id = a.IDTurma)
        INNER JOIN escolas e ON(t.IDEscola = e.id) 
        WHERE e.IDOrg = $idorg";

        $estoque = DB::select($SQL);
        if(count($estoque) > 0){
            foreach($estoque as $e){
                $item = [];
                $item[] = $e->Aluno;
                $item[] = $e->Altura;
                $item[] = $e->Peso;
                $item[] = $e->IMC;
                $item[] = "<a href='".route('Merenda/IMC/Edit',$e->IDImc)."' class='btn btn-primary btn-xs'>Editar</a>";
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
