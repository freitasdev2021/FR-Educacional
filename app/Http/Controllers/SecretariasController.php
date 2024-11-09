<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacao;
use App\Models\User;
use App\Http\Requests\secretariasRequest;
use App\Http\Requests\AdministradoresRequest;
use App\Http\Controllers\SMTPController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Hash;
class SecretariasController extends Controller
{

    public const submodulos = array([
        "nome" => "Cadastros",
        "endereco" => "index",
        "rota" => "Secretarias/index"
    ],[
        "nome" => "Administradores",
        "endereco" => "Administradores",
        "rota" => "Secretarias/Administradores"
    ],[
        "nome" => "Relatórios",
        "endereco" => "Relatorios",
        "rota" => "Secretarias/Relatorios"
    ]);

    public function cadastro($id=null){

        $view = [
            "submodulos" => self::submodulos,
            'id' => ''
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Secretarias/Edit";
            $view['id'] = $id;
            $view['Registro'] = Organizacao::all()->where('id',$id)->first();
            $view['end'] = json_decode($view['Registro']->Endereco);
        }

        return view('Secretarias.createSecretaria',$view);

    }

    public static function getInstituicao($ID){
        return Organizacao::find($ID);
    }

    public static function getEscolasRede($IDOrg){
        $IDEscolas = array();
        foreach(DB::select("SELECT id FROM escolas e WHERE e.IDOrg = $IDOrg ") as $e){
            array_push($IDEscolas,$e->id);
        }
        return $IDEscolas;
    }

    public function getLivroPonto(){
        // Obtenha os dados dos filtros do request
        $anoLetivo = "2024";
        $mes = "Novembro";
        $tipoFuncionario = "Professor";
        $incluiAssinaturasExtras = "Sabado"; // sábado, domingo, feriado, etc.
        $tamanhoFonteCorpo = 10; // valor padrão
        $tamanhoFonteObs = 9; // valor padrão

        // Obtenha dados dos docentes/funcionários para o relatório conforme os filtros
        $funcionarios = array([
            "nome" => "Freitas",
            "matricula" => "10174200",
            "regime" => "Integral",
            "turnos" => "Manhã",
            "periodos" => "Todos",
            "turmas" => "Todas",
            "hora_atividade" => "17:20" 
        ]);

        // Inicie o FPDF
        $pdf = new Fpdf();
        $pdf->AddPage();

        // Cabeçalho do relatório
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode("Livro Ponto - $mes/$anoLetivo"), 0, 1, 'C');
        $pdf->Ln(5);

        // Configurações do corpo do texto
        $pdf->SetFont('Arial', '', $tamanhoFonteCorpo);

        foreach ($funcionarios as $funcionario) {
            $pdf->Cell(0, 10, utf8_decode("Nome do Docente: {$funcionario['nome']}"), 0, 1);
            $pdf->Cell(0, 10, utf8_decode("Matrícula: {$funcionario['matricula']}"), 0, 1);
            $pdf->Cell(0, 10, utf8_decode("Regime de Contratação: {$funcionario['regime']}"), 0, 1);
            $pdf->Cell(0, 10, utf8_decode("Turnos de Aula: {$funcionario['turnos']}"), 0, 1);
            $pdf->Cell(0, 10, utf8_decode("Períodos de Aula: {$funcionario['periodos']}"), 0, 1);
            $pdf->Cell(0, 10, utf8_decode("Turmas: {$funcionario['turmas']}"), 0, 1);
            $pdf->Cell(0, 10, utf8_decode("Hora Atividade: {$funcionario['hora_atividade']}"), 0, 1);

            // Campos de assinatura
            $pdf->Cell(0, 10, "Assinatura do Docente: ____________________", 0, 1);
            $pdf->Cell(0, 10, "Assinatura do Diretor: ____________________", 0, 1);
            $pdf->Ln(10); // Espaço entre os registros de funcionários
        }

        // Rodapé com observações
        $pdf->SetFont('Arial', '', $tamanhoFonteObs);
        $pdf->Cell(0, 10, utf8_decode("Observações: "), 0, 1);

        // Envie o PDF para o navegador
        $pdf->Output('I', "Livro_Ponto_{$mes}_{$anoLetivo}.pdf");
        exit;
    }

    public static function getAlunosRede($IDOrg){
        $IDAlunos = array();
        $SQL = "SELECT
            a.id as IDAluno 
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            WHERE o.id = $IDOrg GROUP BY a.id    
        ";

        foreach(DB::select($SQL) as $a){
            array_push($IDAlunos,$a);
        }

        return $IDAlunos;
    }

    public function cadastroAdministradores($id=null){

        $view = [
            "submodulos" => self::submodulos,
            'id' => '',
            'Organizacoes' => Organizacao::all()
        ];

        if($id){
            $view['submodulos'][0]['endereco'] = "Edit";
            $view['submodulos'][0]['rota'] = "Secretarias/Edit";
            $view['id'] = $id;
            $view['Registro'] = User::all()->where('id',$id)->first();
            $view['end'] = json_decode($view['Registro']->Endereco);
        }

        return view('Secretarias.createAdministrador',$view);

    }

    public function save(Request $request){
        try{
            $org = $request->all();
            $org['Endereco'] = json_encode(array(
                "Rua" => $request->Rua,
                "Cidade" => $request->Cidade,
                "Bairro" => $request->Bairro,
                "UF" => $request->UF,
                "Numero" => $request->Numero,
                "CEP" => $request->CEP
            ));
            $aid = '';
            if($request->id){
                $Organizacao = Organizacao::find($request->id);
                $Organizacao->update($org);
                $rout = 'Secretarias/Edit';
                $aid = $request->id;
            }else{
                Organizacao::create($org);
                $rout = 'Secretarias/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = "Erro ao Salvar a Secretaría: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    
    public function saveAdm(AdministradoresRequest $request){
        try{
            $adm = $request->all();
            $adm['tipo'] = 2;
            $aid = '';
            if($request->id){
                $Organizacao = User::find($request->id);
                $Organizacao->update($adm);
                $rout = 'Secretarias/Administradores/Edit';
                $aid = $request->id;
            }else{
                $rnd = rand(100000,999999);
                $adm['password'] = Hash::make($rnd);
                SMTPController::send($adm['email'],"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$adm['email']));
                //dd("Enviou");
                User::create($adm);
                $rout = 'Secretarias/Administradores/Novo';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $rout = 'Secretarias/Administradores/Novo';
            $mensagem = "Erro ao Salvar o Administrador: ".$th;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function index(){

        return view('Secretarias.index',[
            "submodulos" => self::submodulos,
            'id'
        ]);
    }

    public function getSecretarias(){
        if(Organizacao::count() > 0){
            foreach(Organizacao::all() as $o){
                $endJSON = json_decode($o->Endereco,true);
                $item = [];
                $item[] = $o->Organizacao;
                $item[] = $o->Email;
                $item[] = $endJSON['Rua']." ".$endJSON['Numero']." ".$endJSON['Bairro']." - ".$endJSON['Cidade']."/".$endJSON['UF'];
                $item[] = $o->UF;
                $item[] = $o->Cidade;
                $item[] = "<a href='".route('Secretarias/Edit',$o->id)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(Organizacao::count()),
            "recordsFiltered" => intval(Organizacao::count()),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function getSecretariasAdministradores(){
        if(User::all()->where('tipo',2)->count() > 0){
            foreach(User::all()->where('tipo',2) as $u){
                $item = [];
                $item[] = $u->name;
                $item[] = $u->email;
                $item[] = "<a href='".route('Secretarias/Administradores/Edit',$u->id)."' class='btn btn-primary btn-xs'>Editar</a>";
                $itensJSON[] = $item;
            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(User::all()->where('tipo',2)->count()),
            "recordsFiltered" => intval(User::all()->where('tipo',2)->count()),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function administradores(){
        return view('Secretarias.administradores',[
            "submodulos" => self::submodulos,
            'id' => ''
        ]);
    }


    public function relatorios(){

        return view('Secretarias.relatorios',[
            "submodulos" => self::submodulos,
            'id' => ''
        ]);
    }

}
