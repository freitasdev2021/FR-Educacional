<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aluno;
use App\Models\Matriculas;
use App\Http\Controllers\TransporteController;
use App\Models\Escola;
use App\Models\NEE;
use App\Models\Turma;
use App\Models\User;
use App\Models\Reclassificar;
use Illuminate\Support\Facades\Hash;
use App\Models\FeedbackTransferencia;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Renovacoes;
use App\Models\Remanejo;
use App\Models\Responsavel;
use App\Models\Anexo;
use App\Models\Espera;
use Illuminate\Support\Facades\Auth;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;
use App\Models\Nota;
use Carbon\Carbon;
use App\Models\Suspenso;
use App\Models\Situacao;
use App\Models\Transferencia;
use Storage;
use DateTime;

class AlunosController extends Controller
{
    public const submodulos = array([
        "nome" => "Alunos",
        "endereco" => "index",
        "rota" => "Alunos/index"
    ],[
        "nome" => 'Transferidos',
        "endereco" => "Transferidos",
        "rota" => "Alunos/Transferidos"
    ],[
        "nome" => "Lista de Espera",
        "endereco" => "Espera",
        "rota" => "Alunos/Espera"
    ],[
        'nome' => 'Remanejamentos/Reclassificações',
        'endereco' => 'Mudancas',
        'rota' => 'Alunos/Mudancas'
    ]);

    public const professoresModulos = array([
        "nome" => "Alunos",
        "endereco" => "index",
        "rota" => "Alunos/index"
    ]);

    public const cadastroSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "Edit",
        "rota" => "Alunos/Edit"
    ],[
        'nome' =>'Ficha',
        'endereco' => 'Ficha',
        'rota' => 'Alunos/Ficha'
    ],[
        'nome' => 'Atividades',
        'endereco' => 'Atividades',
        'rota' => 'Alunos/Atividades'
    ],[
        'nome' => 'Boletim',
        'endereco' => 'Boletim',
        'rota' => 'Alunos/Boletim'
    ],[
        'nome' => 'Histórico',
        'endereco' => 'Historico',
        'rota' => 'Alunos/Historico'
    ],[
        'nome' => 'Transferencias',
        'endereco' => 'Transferencias',
        'rota' => 'Alunos/Transferencias'
    ],[
        'nome' => 'Situação',
        'endereco' => 'Situacao',
        'rota' => 'Alunos/Situacao'
    ],[
        'nome' => 'Afastamento',
        'endereco' => 'Suspenso',
        'rota' => 'Alunos/Suspenso'
    ],[
        'nome' => 'Anexos',
        'endereco' => 'Anexos',
        'rota' => 'Alunos/Anexos'
    ],[
        'nome' => 'NEE',
        'endereco' => 'NEE',
        'rota' => 'Alunos/NEE'
    ]);

    public const professoresSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "Edit",
        "rota" => "Alunos/Edit"
    ],[
        'nome' => 'Atividades Desenvolvidas',
        'endereco' => 'Atividades',
        'rota' => 'Alunos/Atividades'
    ],[
        'nome' => 'Boletim',
        'endereco' => 'Boletim',
        'rota' => 'Alunos/Boletim'
    ],[
        'nome' => 'Histórico',
        'endereco' => 'Historico',
        'rota' => 'Alunos/Historico'
    ]);

    public function index(){

        if(self::getDados()['tipo'] == 6){
            $modulos = self::professoresModulos;
        }else{
            $modulos = self::submodulos;
        }

        return view('Alunos.index',[
            'submodulos' => $modulos,
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function mudancas(){
        if(self::getDados()['tipo'] == 6){
            $modulos = self::professoresModulos;
        }else{
            $modulos = self::submodulos;
        }

        $query = [];
        $IDEscola = '';
        if(isset($_GET['IDTurma']) && !empty($_GET['IDTurma'])){
            $IDTurma = $_GET['IDTurma'];
            $IDEscola = Turma::find($IDTurma)->IDEscola;
            $SQL = <<<SQL
                SELECT 
                    m.Nome as Aluno,
                    a.id as IDAluno,
                    a.IDTurma,
                    a.DTEntrada,
                    rec.created_at as UReclassificacao
                FROM alunos a 
                INNER JOIN matriculas m ON(m.id = a.IDMatricula)
                LEFT JOIN reclassificacoes rec ON(rec.IDAluno = a.id)
                WHERE a.IDTurma = $IDTurma
            SQL;

            $query = DB::select($SQL);
        }
        

        return view('Alunos.mudancas',[
            'submodulos' => $modulos,
            'IDEscola'=> $IDEscola,
            'Alunos' => $query,
            'Turmas' => Turma::join('escolas', 'turmas.IDEscola', '=', 'escolas.id')
            ->select('turmas.id as IDTurma','turmas.Serie','turmas.Nome as Turma', 'escolas.Nome as Escola')->whereIn('turmas.IDEscola',EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional))
            ->get(),
        ]);
    }

    public function anexos($IDAluno){
        return view('Alunos.anexos',[
            'submodulos' => self::cadastroSubmodulos,
            'IDAluno' => $IDAluno,
            "CDPasta" => DB::select("SELECT CDPasta FROM matriculas m INNER JOIN alunos a ON(m.id = a.IDMatricula) WHERE a.id = $IDAluno")[0]->CDPasta,
            "Anexos" => DB::select("SELECT Anexo,DSAnexo,CDPasta FROM anexos_aluno aa INNER JOIN alunos a ON(aa.IDAluno = a.id) INNER JOIN matriculas m ON(a.IDMatricula = m.id) WHERE a.id =$IDAluno")
        ]);
    }

    public function saveAnexo(Request $request){
        $data = $request->all();
        $Anexo = $request->file('Anexo')->getClientOriginalName();
        $request->file('Anexo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Anexo,'public');
        $data['Anexo'] = $Anexo;
        Anexo::create($data);
        return redirect()->back();
    }

    public function suspenso($id){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            a.id as IDAluno, 
            s.Justificativa,
            s.INISuspensao,
            s.TERSuspensao
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        LEFT JOIN suspensos s ON(s.IDInativo = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        INNER JOIN responsavel re ON(re.IDAluno = a.id)
        WHERE o.id = $idorg AND a.id = $id";

        return view('Alunos.suspensao',[
            'submodulos' => self::cadastroSubmodulos,
            'Registro' => DB::select($SQL)[0],
            'id'=> $id
        ]);
    }

    public function situacao($id){
        return view('Alunos.situacao',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id
        ]);
    }

    public function cadastroSituacao($id){
        return view('Alunos.cadastroSituacao',[
            'submodulos'=>self::cadastroSubmodulos,
            'id' => $id
        ]);
    }

    public function saveSuspenso(Request $request){
        try{
            $suspenso = DB::select("SELECT id FROM suspensos WHERE IDInativo = $request->IDInativo AND INISuspensao < NOW() AND TERSuspensao > NOW()");
            $sAluno = $request->all();
            $sAluno['INISuspensao'] = date('Y-m-d');
            if($suspenso){
                Suspenso::where('IDInativo',$request->IDInativo)->update([
                    'INISuspensao' => $request->INISuspensao,
                    'TERSuspensao' => $request->TERSuspensao,
                    'Justificativa' => $request->Justificativa
                ]);
            }else{
                Suspenso::create([
                    'INISuspensao' => $request->INISuspensao,
                    'TERSuspensao' => $request->TERSuspensao,
                    'Justificativa' => $request->Justificativa,
                    'IDInativo' => $request->IDInativo
                ]);       
            }
            $rout = 'Alunos/Suspenso';
            $aid = $request->IDInativo;
            $status = 'success';
            $mensagem = 'Suspensão Realizada';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Suspenso';
            $aid = $request->IDInativo;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function removerSuspensao(Request $request){
        try{
            DB::delete("DELETE FROM suspensos WHERE IDInativo = $request->IDInativo");
        }catch(\Throwable $th){

        }finally{
            return redirect()->route('Alunos/Suspenso',$request->IDInativo)->with('success','Suspensão Removida! o Aluno Poderá Voltar as Aulas');
        }
    }

    public function reclassificar(Request $request){
        try{
            $IDTurmaOrigem = Aluno::find($request->IDAluno)->IDTurma;
            $IDEscola = Turma::find($IDTurmaOrigem)->IDEscola;
            if($request->IDTurma != $IDTurmaOrigem){
                Aluno::find($request->IDAluno)->update([
                    "IDTurma" => $request->IDTurma
                ]);
                Reclassificar::create([
                    "IDTurmaAntiga" => $IDTurmaOrigem,
                    "IDTurmaNova" => $request->IDTurma,
                    "IDAluno" => $request->IDAluno,
                    "IDEscola" => $IDEscola
                ]);
                Remanejo::where('IDAluno',$request->IDAluno)->delete();
            }
        }catch(\Throwable $th){

        }finally{
            return redirect()->route('Alunos/Edit',$request->IDAluno)->with('Reclassificado','Aluno Reclassificado');
        }
    }

    public function remanejar(Request $request){
        try{
            $IDTurmaOrigem = Aluno::find($request->IDAluno)->IDTurma;
            $IDEscola = Turma::find($IDTurmaOrigem)->IDEscola;
            if($request->IDTurma != $IDTurmaOrigem){
                Aluno::find($request->IDAluno)->update([
                    "IDTurma" => $request->IDTurma
                ]);
                Remanejo::create([
                    "IDTurmaOrigem" => $IDTurmaOrigem,
                    "IDTurmaDestino" => $request->IDTurma,
                    "IDAluno" => $request->IDAluno,
                    "IDEscola" => $IDEscola
                ]);
            }
        }catch(\Thwrowable $th){

        }finally{
            return redirect()->route('Alunos/Edit',$request->IDAluno)->with('Remanejado','Aluno Remanejado');
        }
    }

    public function remanejarMassa(Request $request,$IDTurmaOrigem,$IDEscola){
        $Alunos = [];
        foreach($request->IDAluno as $key => $al){
            $Alunos[$al] = $request->IDTurmaDestino[$key];
        }
        //dd($Alunos);
        foreach($Alunos as $aluno => $turmaDestino){
            if($turmaDestino != $IDTurmaOrigem){
                Aluno::find($aluno)->update([
                    "IDTurma" => $turmaDestino
                ]);
                Reclassificar::create([
                    "IDTurmaAntiga" => $IDTurmaOrigem,
                    "IDTurmaNova" => $turmaDestino,
                    "IDAluno" => $aluno,
                    "IDEscola" => $IDEscola
                ]);
                Remanejo::where('IDAluno',$aluno)->delete();
            }
        }

        return redirect()->back()->with('success','Alunos movidos');
    }

    public function ficha($id){
        $idorg = Auth::user()->id_org;
        
        $Ficha = self::getAluno($id);
        return view('Alunos.ficha',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'Ficha' => $Ficha,
            'IDOrg' => Auth::user()->id_org,
            'Turmas' => Turma::where('IDEscola',$Ficha->IDTurma)->get()
        ]);
    }

    public function renovacoes($id){
        return view('Alunos.renovacoes',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'IDEscola' => self::getEscolaDiretor(Auth::user()->id)
        ]);
    }

    public static function getComprovanteEscolaridade($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Aluno = self::getAluno($IDAluno); 
        $Escola = Escola::find($Aluno->IDEscola);
        // Definir margens
        $pdf->SetMargins(20, 20, 20); // Margem de 20 em todos os lados

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Aluno->IDEscola . '/' . $Aluno->FotoEscola), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar
        $nomeEscola = "Nome da Escola"; // Defina o nome da escola
        $pdf->Cell(0, 10, $Aluno->Escola, 0, 1, 'C'); // Nome da escola centralizado
        // Espaço após a logo
        $pdf->Ln(40);

        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("DECLARAÇÃO DE ESCOLARIDADE"), 0, 1, 'C'); // Título centralizado
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo da declaração
        $pdf->SetFont('Arial', '', 12);

        // Nome da escola e do aluno (exemplo de variáveis $nomeEscola e $nomeAluno)
        if (Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->gte(Carbon::createFromTimeString('17:00:00'))) {
            $Turno = "Turno Integral";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('14:00:00'))){
            $Turno = "no Turno Manhã";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('13:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('17:00:00'))){
            $Turno = "no Turno Tarde";
        }else{
            $Turno = "";
        }
        //        
        $Ano = date('Y');
        // Inserir o texto da declaração
        $Nascimento = date('d/m/Y',strtotime($Aluno->Nascimento));
        $Pais = json_decode($Aluno->PaisJSON);
        $declaracao = "Declaramos, para os devidos fins, que o(a) aluno(a) $Aluno->Nome Nascido no dia $Nascimento cuja filiação é $Pais->Pai e $Pais->Mae está regularmente matriculado(a) no $Aluno->Serie - $Aluno->Turma da escola $Aluno->Escola, " .
                    "no ano letivo de $Ano $Turno, conforme os registros escolares.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($declaracao, 'ISO-8859-1', 'UTF-8')); // Quebra de linha automática

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        $pdf->Cell(0, 10, self::utfConvert("Emitidor por: ".Auth::user()->name.$Escola->Cidade.", ".$Escola->UF." - ".date('d/m/Y H:i:s')), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('I', 'Declaracao_Matricula.pdf');
        exit;

    }

    public static function getComprovanteVaga($IDAluno){
        $Aluno = self::getAluno($IDAluno); 
        $Escola = Escola::find($Aluno->IDEscola);
        // Definir margens
        $pdf = new FPDF();
        $pdf->AddPage('P'); // Documento em modo retrato
        $pdf->SetMargins(20, 20, 20); // Margem de 20 em todos os lados

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Aluno->IDEscola . '/' . $Aluno->FotoEscola), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        $pdf->SetFont('Arial', 'B', 16);
        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar
        $nomeEscola = "Nome da Escola"; // Defina o nome da escola
        $pdf->Cell(0, 10, $Aluno->Escola, 0, 1, 'C'); // Nome da escola centralizado
        // Espaço após a logo
        $pdf->Ln(25);
        // Cabeçalho do Relatório
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Atestado de Vaga', 0, 1, 'C');
        $pdf->Ln(10);
    
        // Informações do Aluno e Turma
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Turma:', 0, 0);
        $pdf->Cell(0, 10, self::utfConvert($Aluno->Serie." ".$Aluno->Turma), 0, 1);
        
        $pdf->Cell(40, 10, 'Nome do Aluno:', 0, 0);
        $pdf->Cell(0, 10, self::utfConvert($Aluno->Nome), 0, 1);
        $pdf->Ln(10);
        //
        $documentos = [
            'Certidão de Nascimento ou RG',
            'CPF do aluno',
            'Comprovante de Residência atualizado',
            'Histórico Escolar ou Declaração de Transferência',
            'Carteira de Vacinação',
            'Duas fotos 3x4',
            'CPF e RG do responsável',
            'Comprovante de Trabalho dos pais ou responsáveis (se necessário)',
            'Cartão do SUS',
            'Comprovante de Bolsa Família (se aplicável)'
        ];
        // Relação de Documentos
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, self::utfConvert('Documentos Necessários:'), 0, 1);
        $pdf->SetFont('Arial', '', 12);
        foreach ($documentos as $doc) {
            $pdf->Cell(10, 10, '-', 0, 0); // Marcador de lista
            $pdf->Cell(0, 10, self::utfConvert($doc), 0, 1);
        }
        $pdf->Ln(10);
    
        // Assinatura e Data
        $pdf->Cell(0, 10, '__________________________________________', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Assinatura do Emissor', 0, 1, 'C');
        $pdf->Ln(5);
    
        // Data e Hora de Emissão
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Emitido por ' . Auth::user()->name . ' em ' . date('d/m/Y H:i'), 0, 1, 'C');
    
        // Exibir o PDF
        $pdf->Output('I', 'Atestado_de_Vaga.pdf');
        exit;

    }

    public static function getComprovanteMatricula($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Aluno = self::getAluno($IDAluno); 
        $Escola = Escola::find($Aluno->IDEscola);
        // Definir margens
        $pdf->SetMargins(20, 20, 20); // Margem de 20 em todos os lados

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Aluno->IDEscola . '/' . $Aluno->FotoEscola), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar
        $nomeEscola = "Nome da Escola"; // Defina o nome da escola
        $pdf->Cell(0, 10, $Aluno->Escola, 0, 1, 'C'); // Nome da escola centralizado
        // Espaço após a logo
        $pdf->Ln(40);

        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("DECLARAÇÃO DE MATRÍCULA"), 0, 1, 'C'); // Título centralizado
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo da declaração
        $pdf->SetFont('Arial', '', 12);

        // Nome da escola e do aluno (exemplo de variáveis $nomeEscola e $nomeAluno)
        if (Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->gte(Carbon::createFromTimeString('17:00:00'))) {
            $Turno = "Turno Integral";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('14:00:00'))){
            $Turno = "no Turno Manhã";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('13:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('17:00:00'))){
            $Turno = "no Turno Tarde";
        }else{
            $Turno = "";
        }
        //        
        $Ano = date('Y');
        // Inserir o texto da declaração
        $Nascimento = date('d/m/Y',strtotime($Aluno->Nascimento));
        $Pais = json_decode($Aluno->PaisJSON);
        $declaracao = "Declaramos, para os devidos fins, que o(a) aluno(a) $Aluno->Nome Nascido no dia $Nascimento cuja filiação é $Pais->Pai e $Pais->Mae está regularmente matriculado(a) no $Aluno->Serie - $Aluno->Turma da escola $Aluno->Escola, " .
                    "no ano letivo de $Ano $Turno, conforme os registros escolares.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($declaracao, 'ISO-8859-1', 'UTF-8')); // Quebra de linha automática

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        $pdf->Cell(0, 10, self::utfConvert("Emitidor por: ".Auth::user()->name.$Escola->Cidade.", ".$Escola->UF." - ".date('d/m/Y H:i:s')), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('I', 'Declaracao_Matricula.pdf');
        exit;

    }

    public static function getFrequenciaEscolarAluno($IDAluno){
        $Ano = date('Y');
        $SQL = <<<SQL
            SELECT 
            (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDTurma = a.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
            FROM frequencia f2 
            INNER JOIN aulas au2 ON au2.id = f2.IDAula 
            WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
            AND DATE_FORMAT(au2.DTAula, '%Y') = $Ano
            ) as FrequenciaAno
        FROM 
            alunos a
        INNER JOIN 
            matriculas m ON m.id = a.IDMatricula   -- Relaciona alunos com suas matrículas
        INNER JOIN 
            turmas t ON t.id = a.IDTurma           -- Relaciona alunos com suas turmas
        INNER JOIN 
            aulas au ON t.id = au.IDTurma          -- Relaciona turmas com aulas
        INNER JOIN 
            disciplinas d ON d.id = au.IDDisciplina -- Relaciona aulas com disciplinas
        INNER JOIN 
            notas n ON n.IDAluno = a.id            -- Relaciona notas com alunos
        WHERE 
            DATE_FORMAT(au.DTAula, '%Y') = $Ano AND a.id = $IDAluno
        GROUP BY a.id
        SQL;

        if(isset(DB::Select($SQL)[0])){
            return DB::Select($SQL)[0]->FrequenciaAno;
        }else{
            return 0;
        }
    }

    public static function getPreMatricula($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Aluno = self::getAluno($IDAluno); 
        $Escola = Escola::find($Aluno->IDEscola);
        $Pais = json_decode($Aluno->PaisJSON);
        $lineHeight = 6;
        $ne = "Não";
        $uTransporte = "Não";
        $aImagem = "Não";
        if($Aluno->NEE == 1){
            $ne = "Sim";
        }

        if($Aluno->Transporte == 1){
            $uTransporte = "Sim";
        }

        if($Aluno->DireitoImagem == 1){
            $aImagem = "Sim";
        }

        $telefonePai = "";
        $telefoneMae = "";

        if(isset($Pais->TelefonePai)){
            $telefonePai = $Pais->TelefonePai;
        }

        $Nascimento = self::utfConvert("Não Informado.");

        if($Aluno->Nascimento){
            $Nascimento = date('d/m/Y', strtotime($Aluno->Nascimento));
        }


        if(isset($Pais->TelefoneMae)){
            $telefoneMae = $Pais->TelefoneMae;
        }

        $NEEs = implode(" ,",NEE::pluck('DSNecessidade')->where('IDAluno',$IDAluno)->toArray());
        // Definir margens
        $pdf->SetMargins(5, 5, 5); // Margem de 20 em todos os lados

        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar

        // Definir fonte e título
        self::criarCabecalho($pdf,$Aluno->Escola,$Aluno->Organizacao,'storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Aluno->IDEscola . '/' . $Aluno->FotoEscola,"FICHA DE MATRÍCULA");
        //AQUI VAI O CONTEUDO
        // DADOS DA ESCOLA
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, $lineHeight, self::utfConvert('IDENTIFICAÇÃO DA ESCOLA'), 0, 1);
        $pdf->SetFont('Arial', '', 9);

        $pdf->MultiCell(100, $lineHeight, self::utfConvert('Unidade de Ensino: ' . $Aluno->Escola), 0, 0);
        $pdf->Cell(0, $lineHeight, "ID INEP/CENSO: ".$Escola->IDCenso, 0, 1);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Endereço: ' . $Escola->Rua . ', ' . $Escola->Numero . ' ' . $Escola->Bairro . ' ' . $Escola->Cidade . ' - ' . $Escola->UF), 0, 1);
        $pdf->Cell(100, $lineHeight, 'Telefone: ' . $Aluno->TelefoneEscola, 0, 0);
        $pdf->Cell(0, $lineHeight, 'E-Mail: ' . $Aluno->EmailEscola, 0, 1);
        $pdf->Ln(5);

        // DADOS DO ALUNO
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, $lineHeight, self::utfConvert('IDENTIFICAÇÃO DO ALUNO'), 0, 1);
        $pdf->SetFont('Arial', '', 9);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Nome completo: ' . $Aluno->Nome), 0, 0);
        $pdf->Cell(0, $lineHeight, 'ID CENSO: ' . $Aluno->INEP, 0, 1);

        $pdf->Cell(100, $lineHeight, 'Data de nascimento: ' . $Nascimento, 0, 0);
        $pdf->Cell(0, $lineHeight, 'Sexo: ' . $Aluno->Sexo, 0, 1);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Certidão de nascimento: '.$Aluno->CNascimento), 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('Cor/Raça: '.$Aluno->Cor), 0, 1);

        $pdf->Cell(100, $lineHeight, 'CPF: ' . $Aluno->CPF, 0, 0);
        $pdf->Cell(0, $lineHeight, 'RG: ' . $Aluno->RG, 0, 1);

        $pdf->Cell(100, $lineHeight, 'Nacionalidade: Brasileiro', 0, 0);
        $pdf->Cell(0, $lineHeight, 'NIS: ' . $Aluno->NIS, 0, 1);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Endereço: ' . $Aluno->Rua . ', ' . $Aluno->Numero . ' ' . $Aluno->Bairro . ' ' . $Aluno->Cidade . ' - ' . $Aluno->UF), 0, 1);
        $pdf->Ln(5);

        // FILIAÇÃO
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, $lineHeight, self::utfConvert('FILIAÇÃO'), 0, 1);
        $pdf->SetFont('Arial', '', 9);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Pai: ' . $Pais->Pai), 0, 0);
        $pdf->Cell(0, $lineHeight, 'Telefone: ' . $telefonePai  , 0, 1);

        $pdf->Cell(100, $lineHeight, 'CPF: ' . $Pais->CPFPai, 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('Profissão: ' . $Pais->ProfissaoPai), 0, 1);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Mãe: ' . $Pais->Mae), 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('Telefone: ' . $telefoneMae), 0, 1);

        $pdf->Cell(100, $lineHeight, 'CPF: ' . $Pais->CPFMae, 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('Profissão: ' . $Pais->ProfissaoMae), 0, 1);
        $pdf->Ln(5);

        // INFORMAÇÕES ACADÊMICAS
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, $lineHeight, self::utfConvert('INFORMAÇÕES ACADÊMICAS'), 0, 1);
        $pdf->SetFont('Arial', '', 9);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Turma: ' . $Aluno->Turma), 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('Série: ' . $Aluno->Serie), 0, 1);
        $pdf->Ln(5);

        // NECESSIDADES ESPECIAIS
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, $lineHeight, self::utfConvert('NECESSIDADES ESPECIAIS'), 0, 1);
        $pdf->SetFont('Arial', '', 9);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Possui Necessidades Especiais: ' . $ne), 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('Deficiência: ' . $NEEs), 0, 1);
        //$pdf->Ln(5);
        //AUTORIZAÇÕES
        $pdf->Cell(100, $lineHeight, self::utfConvert('Autorizo uso da imagem de meu (minha) filho(a) para fins de propaganda e / ou divulgação da entidade na qual ele(a) está matriculado(a): ' . $aImagem), 0, 0);
        $pdf->Ln(5);
        $pdf->Cell(100, $lineHeight, self::utfConvert('Utiliza transporte escolar: ' . $uTransporte), 0, 0);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, $lineHeight, self::utfConvert('SAÚDE/OBSERVAÇÕES/DETALHES'), 0, 1);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(100, $lineHeight, self::utfConvert($Aluno->Observacoes), 0, 0);
        $pdf->Ln(15);
        //AQUI TERMINA O CONTEÚDO
        //$pdf->Cell(0, 10, self::utfConvert("Emitidor por: ".Auth::user()->name.$Escola->Cidade.", ".$Escola->UF." - ".date('d/m/Y H:i:s')), 0, 1, 'C'); // Texto de assinatura
        // Primeira linha de assinaturas
        $pdf->Cell(90, 10, '__________________________________', 0, 0, 'C'); // Assinatura 1
        $pdf->Cell(90, 10, '__________________________________', 0, 1, 'C'); // Assinatura 2

        // Texto explicativo da primeira linha
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(90, 5, self::utfConvert('Responsavel Legal'), 0, 0, 'C'); // Texto 1
        $pdf->Cell(90, 5, self::utfConvert('Diretor(a)'), 0, 1, 'C'); // Texto 2

        $pdf->Ln(); // Espaço entre a primeira e a segunda linha

        // Segunda linha de assinaturas
        $pdf->Cell(90, 10, '__________________________________', 0, 0, 'C'); // Assinatura 3
        $pdf->Cell(90, 10, '__________________________________', 0, 1, 'C'); // Assinatura 4

        // Texto explicativo da segunda linha
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(90, 5, self::utfConvert('Servidor(a)'), 0, 0, 'C'); // Texto 3
        $pdf->Cell(90, 5, self::utfConvert('Secretário(a)'), 0, 1, 'C'); // Texto 4
        // Saída do PDF
        $pdf->Output('I', 'Declaracao_Frequencia.pdf');
        exit;

    }

    public static function getComprovanteFrequencia($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Aluno = self::getAluno($IDAluno); 
        $Escola = Escola::find($Aluno->IDEscola);
        $FrequenciaEscolar = self::getFrequenciaEscolarAluno($Aluno->IDAluno);
        $PorcentagemFrequencia = ($FrequenciaEscolar/200) * 100 ."%";
        // Definir margens
        $pdf->SetMargins(20, 20, 20); // Margem de 20 em todos os lados

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Aluno->IDEscola . '/' . $Aluno->FotoEscola), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar
        $nomeEscola = "Nome da Escola"; // Defina o nome da escola
        $pdf->Cell(0, 10, self::utfConvert($Aluno->Escola), 0, 1, 'C'); // Nome da escola centralizado
        // Espaço após a logo
        $pdf->Ln(40);

        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("DECLARAÇÃO DE FREQUÊNCIA"), 0, 1, 'C'); // Título centralizado
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo da declaração
        $pdf->SetFont('Arial', '', 12);

        // Nome da escola e do aluno (exemplo de variáveis $nomeEscola e $nomeAluno)
        if (Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->gte(Carbon::createFromTimeString('17:00:00'))) {
            $Turno = "Turno Integral";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('14:00:00'))){
            $Turno = "no Turno Manhã";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('13:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('17:00:00'))){
            $Turno = "no Turno Tarde";
        }else{
            $Turno = "";
        }
        $Ano = date('Y');
        // Inserir o texto da declaração
        if (Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->gte(Carbon::createFromTimeString('17:00:00'))) {
            $Turno = "Turno Integral";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('14:00:00'))){
            $Turno = "no Turno Manhã";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('13:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('17:00:00'))){
            $Turno = "no Turno Tarde";
        }else{
            $Turno = "";
        }

        $Nascimento = date('d/m/Y',strtotime($Aluno->Nascimento));
        $Pais = json_decode($Aluno->PaisJSON);
        $declaracao = "Declaramos, para os devidos fins, que o(a) aluno(a) $Aluno->Nome Nascido no dia $Nascimento em $Aluno->Naturalidade cuja filiação é $Pais->Pai e $Pais->Mae está frequente no $Aluno->Serie - $Aluno->Turma da escola $Aluno->Escola, " .
                    "no ano letivo de $Ano $Turno, conforme os registros escolares.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($declaracao, 'ISO-8859-1', 'UTF-8')); // Quebra de linha automática
        $pdf->Ln();
        $pdf->Cell(0,10,self::utfConvert("Frequência Anual Atual: ".$PorcentagemFrequencia." (".date('d/m/Y').")"),0,1);
        $pdf->Cell(0,10,self::utfConvert("Frequência Mínima Anual: ".$Aluno->MINFrequencia."%"),0,1);
        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        $pdf->Cell(0, 10, self::utfConvert("Emitidor por: ".Auth::user()->name.$Escola->Cidade.", ".$Escola->UF." - ".date('d/m/Y H:i:s')), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('I', 'Declaracao_Frequencia.pdf');
        exit;

    }

    public static function getIniciante($IDAluno){
        $Aluno = self::getAluno($IDAluno);
        //dd($Aluno);
        $MatriculaEsseAno = Matriculas::where('id',$IDAluno)->whereYear('created_at',date('Y'))->exists();
        $TransferidoEsseAno = Transferencia::where('IDAluno',$IDAluno)->where('IDEscolaDestino',$Aluno->IDEscola)->whereYear('created_at',date('Y'))->exists();
        
        if($MatriculaEsseAno && !$TransferidoEsseAno){
            return "Sim";
        }elseif($TransferidoEsseAno){
            return "Sim";
        }else{
            return "Não";
        }
    }

    public static function getResultadoAno($IDAluno,$Ano){
        $SQL = <<<SQL
            SELECT 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND DATE_FORMAT(au3.created_at, '%Y') = $Ano
                ) as Total,
                (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as PontBim,
                (SELECT SUM(rec2.Nota) 
                FROM recuperacao rec2 
                WHERE rec2.Estagio !="ANUAL" AND rec2.IDAluno = a.id 
                AND rec2.IDDisciplina = d.id 
                ) as RecBim,
                (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio = 'ANUAL' AND rec2.IDAluno = $IDAluno AND rec2.IDDisciplina = d.id ) as RecAn,
                -- Frequência (quantidade de presenças) do Aluno para a Disciplina e Estágio específicos
                (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                WHERE f2.IDAluno = a.id 
                AND au2.IDDisciplina = d.id 
                AND DATE_FORMAT(au2.DTAula, '%Y') = $Ano
                ) as Frequencia,
                t.MediaPeriodo,
                -- Caso em que é verificado se a nota é inferior à média
                CASE WHEN 
                    (SELECT SUM(n2.Nota) 
                    FROM notas n2 
                    INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                    INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                    WHERE n2.IDAluno = a.id 
                    AND DATE_FORMAT(n2.created_at, '%Y') = $Ano
                    ) < t.MediaPeriodo THEN 'Reprovado' 
                ELSE 'Aprovado' END as Resultado,

                m.Nome as Aluno,         -- Nome do Aluno
                a.id as IDAluno,         -- ID do Aluno
                d.NMDisciplina as Disciplina, -- Nome da Disciplina
                t.MediaPeriodo,
                t.TPAvaliacao,
                t.MINFrequencia,
                t.Serie,
                t.QTRepetencia
            FROM 
                alunos a
            INNER JOIN 
                matriculas m ON m.id = a.IDMatricula   -- Relaciona alunos com suas matrículas
            INNER JOIN 
                turmas t ON t.id = a.IDTurma           -- Relaciona alunos com suas turmas
            INNER JOIN 
                aulas au ON t.id = au.IDTurma          -- Relaciona turmas com aulas
            INNER JOIN 
                disciplinas d ON d.id = au.IDDisciplina -- Relaciona aulas com disciplinas
            INNER JOIN 
                notas n ON n.IDAluno = a.id            -- Relaciona notas com alunos
            WHERE 
                                    -- Filtro para turma específica
                DATE_FORMAT(au.DTAula, '%Y') = $Ano -- Filtro para o ano de 2024
                AND a.id = $IDAluno
            GROUP BY 
                a.id, d.id, m.Nome, t.MediaPeriodo, t.TPAvaliacao, t.MINFrequencia -- Corrigido o GROUP BY
        SQL;

        $Desempenho = DB::select($SQL);
        $Dependencias = array();
        $QTReprovacoes = array();
        if($Desempenho){
            foreach($Desempenho as $d){

                if($d->RecAn > 0){
                    $Total = $d->RecAn;
                }else{
                    if($d->RecBim > 0){
                        $Total = ($d->RecBim - $d->Total) + $d->PontBim;
                        //dd($Total);
                    }else{
                        $Total = $d->Total;
                    }
                }

                if($Total < $d->MediaPeriodo*4){
                    array_push($QTReprovacoes,$d->QTRepetencia);
                    array_push($Dependencias,array(
                        "Disciplina" => $d->Disciplina,
                        "Total" => ($d->RecAn) ? $d->RecAn : $d->Total,
                        "Media" => $d->MediaPeriodo*4
                    ));
                }
            }

            $repetencias = array_unique($QTReprovacoes)[0];

           
            $naoPassou = array_filter($Dependencias,function($i){
                if($i['Total'] < $i['Media'] ){
                    return $i;
                }
            });
            $dados = "Sim";
        }else{
            $dados = "Não";
        }

        if($dados == "Sim"){
            if(count($naoPassou) >= $repetencias){
                $situacao = "Reprovado";
            }else{
                $situacao = "Aprovado";
            }
        }
    

        if($dados == "Sim"){
            return $situacao;
        }else{
            return "Sem dados";
        }
        
    }

    public static function alunoVeio($IDAluno,$Hash){
        $SQL = "SELECT au.id FROM aulas au LEFT JOIN frequencia fr ON(fr.HashAula = au.Hash) WHERE au.Hash = '$Hash' AND fr.IDAluno = $IDAluno";

        return count(DB::select($SQL));
    }

    public function getRelatorioMatricula($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Escola = DB::select("SELECT 
            e.id,
            e.Cidade,
            e.UF, 
            e.Foto,
            m.Nome as Aluno,
            e.Foto,
            e.Nome as Escola,
            m.Numero as Numero,
            m.Bairro ,
            r.NMResponsavel as Responsavel,
            m.Rua
        FROM escolas e 
        INNER JOIN turmas t ON(t.IDEscola = e.id) 
        INNER JOIN alunos a ON(t.id = a.IDTurma ) 
        INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
        INNER JOIN responsavel r ON(r.IDAluno = a.id)
        WHERE a.id = $IDAluno")[0];
        // Definir margens
        $pdf->SetMargins(20, 20, 20); // Margem de 20 em todos os lados

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho

        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(50, 15); // Ajuste conforme necessário para centralizar
        $pdf->Cell(0, 10, $Escola->Escola, 0, 1, 'C'); // Nome da escola centralizado

        // Espaço após o título
        $pdf->Ln(20);

        // Título do relatório
        $pdf->Cell(0, 10, self::utfConvert('Relatório de Matrícula'), 0, 1, 'C');
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo do relatório
        $pdf->SetFont('Arial', '', 12);

        // Inserir as informações do relatório
        $pdf->Cell(0, 10, self::utfConvert("Nome Completo: $Escola->Aluno"), 0, 1);
        $pdf->Ln(5); // Espaço entre linhas

        $pdf->MultiCell(0, 10, mb_convert_encoding("Filiação:\n$Escola->Responsavel", 'ISO-8859-1', 'UTF-8'));
        $pdf->Ln(5); // Espaço entre linhas
        $endereco = $Escola->Rua.", ".$Escola->Numero." ".$Escola->Bairro." ".$Escola->Cidade.", ".$Escola->UF;
        $pdf->MultiCell(0, 10, mb_convert_encoding("Endereço:\n$endereco", 'ISO-8859-1', 'UTF-8'));

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        //$pdf->Cell(0, 10, self::utfConvert("Assinatura do Responsável"), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('D', 'Relatorio_Matricula.pdf');
        exit;
    }

    public function getComprovanteConclusao($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Escola = DB::select("SELECT e.id,e.Cidade,e.UF, e.Foto,m.Nome as Aluno,e.Foto,e.Nome as Escola,t.Serie 
        FROM escolas e 
        INNER JOIN turmas t ON(t.IDEscola = e.id) 
        INNER JOIN alunos a ON(t.id = a.IDTurma ) 
        INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
        WHERE a.id = $IDAluno")[0];
        // Definir margens
        $pdf->SetMargins(20, 20, 20); // Margem de 20 em todos os lados

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar
        $nomeEscola = "Nome da Escola"; // Defina o nome da escola
        $pdf->Cell(0, 10, $Escola->Escola, 0, 1, 'C'); // Nome da escola centralizado
        // Espaço após a logo
        $pdf->Ln(40);

        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("DECLARAÇÃO DE CONCLUSÃO"), 0, 1, 'C'); // Título centralizado
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo da declaração
        $pdf->SetFont('Arial', '', 12);

        // Nome da escola e do aluno (exemplo de variáveis $nomeEscola e $nomeAluno)
        $nomeEscola = "Nome da Escola";
        $nomeAluno = "Nome do Aluno";
        $Ano = date('Y');
        // Inserir o texto da declaração
        $declaracao = "Certificamos que o(a) aluno(a) $Escola->Aluno êxito o $Escola->Serie pela $Escola->Escola, " .
              "no ano letivo de $Ano, estando apto(a) a seguir com seus estudos ou carreira conforme desejado.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($declaracao, 'ISO-8859-1', 'UTF-8')); // Quebra de linha automática

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        $pdf->Cell(0, 10, self::utfConvert($Escola->Cidade.", ".$Escola->UF." - ".date('d/m/Y H:i:s')), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('D', 'Declaracao_Conclusao.pdf');
        exit;
    }

    public function getAtestadoComparecimento($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Aluno = self::getAluno($IDAluno);
        $Escola = Escola::find($Aluno->IDEscola);
        // Definir margens
        $pdf->SetMargins(20, 20, 20); // Margem de 20 em todos os lados

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar
        $pdf->Cell(0, 10, $Escola->Nome, 0, 1, 'C'); // Nome da escola centralizado

        // Espaço após o título
        $pdf->Ln(20);

        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Atestado de Comparecimento"), 0, 1, 'C'); // Título centralizado
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo da declaração
        $pdf->SetFont('Arial', '', 12);

        // Nome da escola e do aluno (exemplo de variáveis $nomeEscola e $nomeAluno)
        $nomeEscola = "Nome da Escola";
        $nomeAluno = "Nome do Aluno";
        $Ano = date('Y');
        if (Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->gte(Carbon::createFromTimeString('17:00:00'))) {
            $Turno = "Turno Integral";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('14:00:00'))){
            $Turno = "no Turno Manhã";
        }elseif(Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('13:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('17:00:00'))){
            $Turno = "no Turno Tarde";
        }else{
            $Turno = "";
        }
        // Inserir o texto da declaração
        $declaracao = "Atestamos que $Aluno->NMResponsavel Responsavel pelo(a) Aluno(a) $Aluno->Nome, matriculado(a) na turma $Aluno->Turma $Aluno->Serie do turno $Turno, esteve presente na escola na data e horário especificados.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($declaracao, 'ISO-8859-1', 'UTF-8')); // Quebra de linha automática

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        $pdf->Cell(0, 10, self::utfConvert($Escola->Cidade.", ".$Escola->UF." - ".date('d/m/Y H:i:s')), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('D', 'Atestado_Comparecimento.pdf');
        exit;
    }

    public function termoResponsabilidade($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Pega informações do aluno e da escola
        $Aluno = self::getAluno($IDAluno);
        $Escola = Escola::find($Aluno->IDEscola);

        // Definir margens
        $pdf->SetMargins(20, 20, 20);

        // Inserir a logo da escola
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30);

        // Nome da escola
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY(20, 15);
        $pdf->Cell(0, 10, $Escola->Nome, 0, 1, 'C');

        // Espaço após o título
        $pdf->Ln(20);

        // Título do termo
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Termo de Ciência e Responsabilidade Escolar"), 0, 1, 'C');
        $pdf->Ln(10);

        // Corpo do termo
        $pdf->SetFont('Arial', '', 12);

        // Definir turno baseado no horário da turma
        if (Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->gte(Carbon::createFromTimeString('17:00:00'))) {
            $Turno = "Turno Integral";
        } elseif (Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('07:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('14:00:00'))) {
            $Turno = "no Turno Manhã";
        } elseif (Carbon::parse($Aluno->INITurma)->gt(Carbon::createFromTimeString('13:00:00')) && Carbon::parse($Aluno->INITurma)->lt(Carbon::createFromTimeString('17:00:00'))) {
            $Turno = "no Turno Tarde";
        }else{
            $Turno = "";
        }

        // Texto do termo
        $termo = "Eu, $Aluno->NMResponsavel, responsável pelo(a) aluno(a) $Aluno->Nome, matriculado(a) na turma $Aluno->Turma, série $Aluno->Serie, $Turno, declaro estar ciente das normas e responsabilidades escolares estabelecidas pela escola $Escola->Nome, e comprometo-me a cumpri-las em prol do desenvolvimento educacional e disciplinar do aluno.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($termo, 'ISO-8859-1', 'UTF-8'));

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Linha de assinatura
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C');
        $pdf->Cell(0, 10, "Assinatura do Responsável", 0, 1, 'C');

        // Nome e data do emissor
        $pdf->Ln(10);
        $pdf->Cell(0, 10, "Emitido por: " . Auth::user()->name, 0, 1, 'L');
        $pdf->Cell(0, 10, self::utfConvert($Escola->Cidade . ", " . $Escola->UF . " - " . date('d/m/Y H:i:s')), 0, 1, 'L');

        // Saída do PDF
        $pdf->Output('D', 'Termo_Ciencia_Responsabilidade.pdf');
        exit;

    }

    public function getDeclaracaoTransferencia($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página

        // Definir margens
        $pdf->SetMargins(20, 20, 20);
        $Escola = DB::select("SELECT e.id,e.Cidade,e.UF, e.Foto,m.Nome as Aluno,e.Foto,e.Nome as Escola,t.Serie 
        FROM escolas e 
        INNER JOIN turmas t ON(t.IDEscola = e.id) 
        INNER JOIN alunos a ON(t.id = a.IDTurma ) 
        INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
        WHERE a.id = $IDAluno")[0];
        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar
        $pdf->Cell(0, 10, $Escola->Escola, 0, 1, 'C'); // Nome da escola centralizado

        // Espaço após o título
        $pdf->Ln(20);

        // Título da declaração
        $pdf->Cell(0, 10, self::utfConvert('Declaração de Transferência Escolar'), 0, 1, 'C');
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo da declaração
        $pdf->SetFont('Arial', '', 12);

        // Informações do aluno (exemplo de variáveis)
        $dataTransferencia = date('d/m/Y'); // Data atual

        // Texto da declaração de transferência
        $declaracao = "Declaramos para os devidos fins que o(a) aluno(a) $Escola->Aluno está sendo transferido(a) " .
                      "para outra instituição de ensino a partir da data de $dataTransferencia.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($declaracao, 'ISO-8859-1', 'UTF-8')); // Quebra de linha automática

        // Espaço antes das observações
        $pdf->Ln(10);

        // Observações
        $observacoes = "Informamos que o histórico escolar do aluno será emitido dentro de 30 dias a partir da data desta declaração.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($observacoes, 'ISO-8859-1', 'UTF-8'));

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        $pdf->Cell(0, 10, self::utfConvert("Assinatura do Responsável"), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('D', 'Declaracao_Transferencia.pdf');
        exit;
    }

    public static function getAluno($id){
        $alunos = array();
        $SQL = "SELECT 
                a.id as IDAluno, 
                m.id as IDMatricula,
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
                m.TPSangue,
                m.DireitoImagem,
                m.CNascimento,
                o.Organizacao,
                e.id as IDEscola,
                e.Foto as FotoEscola,
                e.Telefone as TelefoneEscola,
                e.Email as EmailEscola,
                t.Serie as Serie,
                m.Nascimento as Nascimento,
                a.STAluno,
                m.Foto,
                re.Escolaridade,
                re.Profissao,
                m.Email,
                m.RG,
                m.CPF,
                re.NMResponsavel,
                re.RGPais,
                re.CPFResponsavel,
                re.EmailResponsavel,
                m.CEP,
                m.Rua,
                m.Bairro,
                m.UF,
                m.Numero,
                m.Cidade,
                re.CLResponsavel,
                a.IDTurma,
                m.Numero,
                m.Celular,
                m.NEE,
                m.Alergia,
                m.Transporte,
                m.BolsaFamilia,
                m.AMedico,
                m.APsicologico,
                m.CDPasta,
                cal.INIRematricula,
                cal.TERRematricula,
                r.ANO,
                m.PaisJSON,
                m.Quilombola,
                m.CNascimento,
                m.CNH,
                m.SUS,
                m.Passaporte,
                m.Observacoes,
                e.id as IDEscola,
                m.Naturalidade,
                m.NIS,
                m.INEP,
                m.IDRota,
                m.Cor,
                m.Sexo,
                t.INITurma,
                t.TERTurma,
                t.MINFrequencia,
                t.MediaPeriodo,
                t.TPAvaliacao,
                a.DTEntrada,
                m.Expedidor
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN responsavel re ON(re.IDAluno = a.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            WHERE a.id = $id  
            ";
        return DB::select($SQL)[0];
    }

    public function gerarHistoricoEscolar($id,Request $request)
    {
        //dd($request->all());
        // Obtém todos os anos em que o aluno tem registros
        $anos = DB::table('aulas')
        ->join('frequencia', 'aulas.id', '=', 'frequencia.IDAula')
        ->join('alunos', 'alunos.id', '=', 'frequencia.IDAluno')
        ->where('alunos.id', $id)
        ->select(DB::raw('DISTINCT YEAR(aulas.DTAula) as ano'))
        ->orderBy('ano')
        ->pluck('ano')
        ->toArray();

        $Aluno = self::getAluno($id);

        //dd($Aluno);

        $selectYears = '';
        $selectCargas = "";
        foreach ($anos as $ano) {
            $selectYears .= "
            (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = {$ano} ) as RecBim_{$ano},
            (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio = 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = {$ano} ) as RecAn_{$ano},
            (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = {$ano} ) as PontRec_{$ano},
            MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND DATE_FORMAT(au3.DTAula, '%Y') = {$ano} ) END) as Total_{$ano}, 
            MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN 
                    (SELECT COUNT(f2.id) 
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                     AND au2.IDDisciplina = d.id
                     AND DATE_FORMAT(au2.DTAula, '%Y') = {$ano}) 
                END) as Frequencia_{$ano},
                MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN 
                    (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria)) 
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                     AND au2.IDDisciplina = d.id
                     AND DATE_FORMAT(au2.DTAula, '%Y') = {$ano}) 
                END) as CargaDisciplina_{$ano},
                MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN 
                    (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria))
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                     AND DATE_FORMAT(au2.DTAula, '%Y') = {$ano}) 
                END) as CargaTotal_{$ano},
            ";

            $selectCargas .="
            MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN 
            (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria))
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                AND DATE_FORMAT(au2.DTAula, '%Y') = {$ano}) 
            END) as CargaTotal_{$ano},
            ";
        }

        // Executa a consulta
        $historico = DB::select("
            SELECT 
                d.NMDisciplina as Disciplina,
                {$selectYears}
                COUNT(d.id)
            FROM 
                disciplinas d
            INNER JOIN 
                aulas au ON(d.id = au.IDDisciplina)
            INNER JOIN 
                frequencia f ON(au.id = f.IDAula)
            INNER JOIN 
                alunos a ON(a.id = f.IDAluno)
            WHERE 
                a.id = :aluno_id
            GROUP BY 
                d.id;
        ", ['aluno_id' => $id]);

        $cargas = DB::select("
            SELECT 
                {$selectCargas}
                COUNT(au.id)
            FROM 
                aulas au
            INNER JOIN 
                frequencia f ON(au.id = f.IDAula)
            INNER JOIN 
                alunos a ON(a.id = f.IDAluno)
            INNER JOIN 
                atividades at ON(at.IDAula = au.id)
            INNER JOIN 
                notas n ON(at.id = n.IDAtividade)
            WHERE 
                a.id = :aluno_id
        ", ['aluno_id' => $id]);

        // Configura o FPDF
        $pdf = new Fpdf();
        $pdf->AddPage('L');
        $Escola = Escola::find($Aluno->IDEscola);
        $pdf->SetMargins(20, 20, 20); // Margem de 20 em todos os lados

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(20, 15); // Ajuste o valor X conforme necessário para centralizar
        $pdf->Cell(0, 10, self::utfConvert($Escola->Nome), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, self::utfConvert($Escola->Rua.", ".$Escola->Numero." ".$Escola->Bairro." - ".$Escola->Cidade."/".$Escola->UF), 0, 1, 'C');
        // Espaço após a logo
        $pdf->Ln(5);
        if($request->CMCertificado){
            $Titulo = "CERTIFICADO DE CONCLUSÃO";
        }else{
            $Titulo = "HISTÓRICO ESCOLAR";
        }
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert($Titulo), 0, 1, 'C'); // Título centralizado
        if($request->SGVia){
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0,10,"Segunda Via",0,1,'C');
        }
        $pdf->Ln(10); // Espaço após o título
        //MENSAGEM
        if($request->CMCertificado){
            $pdf->SetFont('Arial', 'B', 14);
            $mensagemCertificado = "Certificamos que ".self::utfConvert($Aluno->Nome).", portador do CPF ".self::utfConvert($Aluno->CPF).", natural de ".self::utfConvert($Aluno->Naturalidade).", nascido(a) em ".date('d/m/Y',strtotime($Aluno->Nascimento)).", concluiu com êxito mais uma etapa de ensino.";
            $pdf->MultiCell(0, 4, self::utfConvert($mensagemCertificado), 0, 'C'); // Centralizado e com quebra de linha automática
            $pdf->Ln(10);
        }
        //DADOS DO ALUNO
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(80, 5, "Nome: ".self::utfConvert($Aluno->Nome), 0, 0);
        $pdf->Cell(50, 5, "INEP: ".self::utfConvert($Aluno->INEP), 0, 0);
        $pdf->Cell(50, 5, "CPF: ".self::utfConvert($Aluno->CPF), 0, 1); // Quebra de linha para o próximo conjunto

        // Naturalidade e Nascimento
        $pdf->Cell(80, 5, "Naturalidade: ".self::utfConvert($Aluno->Naturalidade), 0, 0);
        $pdf->Cell(50, 5, "Nascimento: ".date('d/m/Y',strtotime($Aluno->Nascimento)), 0, 0);
        $pdf->Cell(50, 5, self::utfConvert("Observação: ".$request->OBSIndividual), 0, 0);
        $pdf->Ln();
        $pdf->Cell(50, 5, self::utfConvert("Observação Geral: ".$Escola->OBSGeralHistorico), 0, 0);
        $Filiacao = json_decode($Aluno->PaisJSON);
        $pdf->Ln();
        $pdf->Cell(50, 5, "Pai: ".self::utfConvert($Filiacao->Pai), 0, 1);
        $pdf->Cell(50, 5,self::utfConvert("Mãe: ".$Filiacao->Mae), 0, 1);
               
        //
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 8);
        // Cabeçalho da tabela
        $pdf->Cell(40, 5, 'Disciplinas', 1, 0, 'C');
        foreach ($anos as $ano) {
            $pdf->Cell(40, 5, "{$ano}", 1, 0, 'C');
        }
        $pdf->Ln();

        // Dados das disciplinas e notas por ano
        foreach ($historico as $disciplina) {
            $pdf->Cell(40, 5, self::utfConvert($disciplina->Disciplina), 1);
            foreach ($anos as $ano) {
                $notaKey = "Total_{$ano}";
                $carga = "CargaDisciplina_{$ano}";
                $nota = isset($disciplina->$notaKey) ? $disciplina->$notaKey : '-';
                $pdf->Cell(20, 5, $nota, 1, 0, 'C');
                $pdf->Cell(20, 5, $disciplina->$carga, 1, 0, 'C');
            }
            $pdf->Ln();
        }

        $pdf->Ln();
        foreach($cargas as $c){
            foreach($anos as $ano){
                $carga = "CargaTotal_{$ano}";
                $pdf->Cell(20, 5, "{$ano}", 1, 0, 'C');
                $pdf->Cell(20, 5, $c->$carga, 1, 0, 'C');
            }
        }

        $pdf->Ln(30);
        // Assinaturas
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(90, 10, "Assinatura do Diretor:", 0, 0, 'C');
        $pdf->Cell(90, 10, self::utfConvert("Assinatura do Secretário Escolar:"), 0, 1, 'C');

        // Desenha linhas para as assinaturas
        $pdf->Cell(90, 10, "_________________________", 0, 0, 'C');
        $pdf->Cell(90, 10, "_________________________", 0, 1, 'C');

        // Saída do PDF
        $pdf->Output("I",'Historico_'.rand(1,100).".pdf");
        exit;
    }

    public function historico($id){
        // Primeiro, obtenha todos os anos em que o aluno tem registros
        $anos = DB::table('aulas')
            ->join('frequencia', 'aulas.id', '=', 'frequencia.IDAula')
            ->join('alunos', 'alunos.id', '=', 'frequencia.IDAluno')
            ->where('alunos.id', $id)
            ->select(DB::raw('DISTINCT YEAR(aulas.DTAula) as ano'))
            ->orderBy('ano')
            ->pluck('ano')
            ->toArray();

            //dd($anos);

        // Crie a parte dinâmica da consulta para as colunas de anos
        $selectCargas = "";
        $selectYears = '';
        foreach ($anos as $ano) {
            $selectYears .= "
            (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = {$ano} ) as RecBim_{$ano},
            (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio = 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = {$ano} ) as RecAn_{$ano},
            (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = {$ano} ) as PontRec_{$ano},
            MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND DATE_FORMAT(au3.DTAula, '%Y') = {$ano} ) END) as Total_{$ano}, 
            MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN 
                    (SELECT COUNT(f2.id) 
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                     AND au2.IDDisciplina = d.id
                     AND DATE_FORMAT(au2.DTAula, '%Y') = {$ano}) 
                END) as Frequencia_{$ano},
                MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN 
                    (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria)) 
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                     AND au2.IDDisciplina = d.id
                     AND DATE_FORMAT(au2.DTAula, '%Y') = {$ano}) 
                END) as CargaDisciplina_{$ano},
                MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN 
                    (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria))
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                     AND DATE_FORMAT(au2.DTAula, '%Y') = {$ano}) 
                END) as CargaTotal_{$ano},
            ";

            $selectCargas .="
            MAX(CASE WHEN DATE_FORMAT(au.DTAula, '%Y') = {$ano} THEN 
            (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria))
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                AND DATE_FORMAT(au2.DTAula, '%Y') = {$ano}) 
            END) as CargaTotal_{$ano},
            ";
        }
        

        if(!empty($selectYears)){
            $historico = DB::select("
                SELECT 
                    d.NMDisciplina as Disciplina,
                    {$selectYears}
                    COUNT(d.id)
                FROM 
                    disciplinas d
                INNER JOIN 
                    aulas au ON(d.id = au.IDDisciplina)
                INNER JOIN 
                    frequencia f ON(au.id = f.IDAula)
                INNER JOIN 
                    alunos a ON(a.id = f.IDAluno)
                INNER JOIN 
                    turmas t ON(a.IDTurma = t.id)
                INNER JOIN 
                    atividades at ON(at.IDAula = au.id)
                INNER JOIN 
                    notas n ON(at.id = n.IDAtividade)
                WHERE 
                    a.id = :aluno_id
                GROUP BY 
                    d.id;
            ", ['aluno_id' => $id]);
        }else{
            $historico = [];
        }

        if(!empty($selectCargas)){
            $cargas = DB::select("
                SELECT 
                    {$selectCargas}
                    COUNT(au.id)
                FROM 
                    aulas au
                INNER JOIN 
                    frequencia f ON(au.id = f.IDAula)
                INNER JOIN 
                    alunos a ON(a.id = f.IDAluno)
                INNER JOIN 
                    atividades at ON(at.IDAula = au.id)
                INNER JOIN 
                    notas n ON(at.id = n.IDAtividade)
                WHERE 
                    a.id = :aluno_id
            ", ['aluno_id' => $id]);
        }else{
            $cargas = [];
        }
        // Consulta SQL dinâmica

        
        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }

        return view('Alunos.historico',[
            'submodulos' => $submodulos,
            'id' => $id,
            'historico' => $historico,
            'cargas' => $cargas,
            'anos' => $anos
        ]);
    }

    public function boletim($id){

        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }
        $Turma = Turma::find(Aluno::find($id)->IDTurma);
        switch($Turma->Periodo){
            case 'Bimestral':
                $SQL = <<<SQL
                    SELECT 
                        d.NMDisciplina as Disciplina,
                        t.MINFrequencia,
                        (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.IDTurma = t.id AND auFreq.Estagio="1º BIM" AND auFreq.DTAula > a.DTEntrada AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Faltas1B,
                        (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.IDTurma = t.id AND auFreq.Estagio="2º BIM" AND auFreq.DTAula > a.DTEntrada AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas2B,
                        (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.IDTurma = t.id AND auFreq.Estagio="3º BIM" AND auFreq.DTAula > a.DTEntrada AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas3B,
                        (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.IDTurma = t.id AND auFreq.Estagio="4º BIM" AND auFreq.DTAula > a.DTEntrada AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="4º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas4B,
                        (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.IDTurma = t.id AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as FrequenciaAno,
                        CASE WHEN 
                            (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                        THEN
                            (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                        ELSE 
                            (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='1º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                        END as Nota1B,
                        CASE WHEN 
                            (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                        THEN
                            (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                        ELSE 
                            (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='2º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                        END as Nota2B,
                        CASE WHEN 
                            (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                        THEN
                            (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                        ELSE 
                            (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='3º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                        END as Nota3B,
                        CASE WHEN 
                            (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                        THEN
                            (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                        ELSE 
                            (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='4º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                        END as Nota4B
                    FROM disciplinas d
                    INNER JOIN aulas au ON(d.id = au.IDDisciplina)
                    INNER JOIN frequencia f ON(au.id = f.IDAula)
                    INNER JOIN alunos a ON(a.id = f.IDAluno)
                    INNER JOIN turmas t ON(a.IDTurma = t.id)
                    INNER JOIN atividades at ON(at.IDAula = au.id)
                    INNER JOIN notas n ON(at.id = n.IDAtividade)
                    WHERE a.id = $id
                    GROUP BY d.id 
                SQL;
            break;
            case 'Trimestral':
                $SQL = <<<SQL
                    SELECT 
                        d.NMDisciplina as Disciplina,
                        (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º TRI" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id) as Rec1B,
                        (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º TRI" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id) as Rec2B,
                        (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º TRI" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id) as Rec3B,
                        (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='1º TRI' ) as Nota1B,
                        200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º TRI" ) as Faltas1B,
                        (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='2º TRI' ) as Nota2B,
                        200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º TRI" ) as Faltas2B,
                        (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='3º TRI' ) as Nota3B,
                        200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º TRI" ) as Faltas3B
                    FROM disciplinas d
                    INNER JOIN aulas au ON(d.id = au.IDDisciplina)
                    INNER JOIN frequencia f ON(au.id = f.IDAula)
                    INNER JOIN alunos a ON(a.id = f.IDAluno)
                    INNER JOIN turmas t ON(a.IDTurma = t.id)
                    INNER JOIN atividades at ON(at.IDAula = au.id)
                    INNER JOIN notas n ON(at.id = n.IDAtividade)
                    WHERE a.id = $id
                    GROUP BY d.id 
                SQL;
            break;
            case 'Semestral':
                $SQL = <<<SQL
                    SELECT 
                        d.NMDisciplina as Disciplina,
                        (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º SEM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id) as Rec1B,
                        (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º SEM" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id) as Rec2B,
                        (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='1º SEM' ) as Nota1B,
                        200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º SEM" ) as Faltas1B,
                        (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='2º SEM' ) as Nota2B,
                        200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º SEM" ) as Faltas2B
                    FROM disciplinas d
                    INNER JOIN aulas au ON(d.id = au.IDDisciplina)
                    INNER JOIN frequencia f ON(au.id = f.IDAula)
                    INNER JOIN alunos a ON(a.id = f.IDAluno)
                    INNER JOIN turmas t ON(a.IDTurma = t.id)
                    INNER JOIN atividades at ON(at.IDAula = au.id)
                    INNER JOIN notas n ON(at.id = n.IDAtividade)
                    WHERE a.id = $id
                    GROUP BY d.id 
                SQL;
            break;
            case 'Anual':
                $SQL = <<<SQL
                    SELECT 
                        d.NMDisciplina as Disciplina,
                        (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º PER" AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id) as Rec1B,
                        (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='1º PER' ) as Nota1B,
                        200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º PER" ) as Faltas1B
                    FROM disciplinas d
                    INNER JOIN aulas au ON(d.id = au.IDDisciplina)
                    INNER JOIN frequencia f ON(au.id = f.IDAula)
                    INNER JOIN alunos a ON(a.id = f.IDAluno)
                    INNER JOIN turmas t ON(a.IDTurma = t.id)
                    INNER JOIN atividades at ON(at.IDAula = au.id)
                    INNER JOIN notas n ON(at.id = n.IDAtividade)
                    WHERE a.id = $id
                    GROUP BY d.id 
                SQL;
            break;
        }

        //$Boletim = DB::select($SQL);

        return view('Alunos.boletim',[
            'submodulos' => $submodulos,
            'id' => $id,
            "Boletim" => DB::select($SQL),
            "Periodo" => $Turma->Periodo,
            "MediaPeriodo" => $Turma->MediaPeriodo,
            "MINFrequencia" => $Turma->MINFrequencia
        ]);
    }

    public function transferencias($id){

        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }

        return view('Alunos.transferencias',[
            'submodulos' => $submodulos,
            'id' => $id
        ]);
    }

    public function faltasJustificadas($id){
        return view('Alunos.faltasJustificadas',[
            'submodulos' => self::cadastroSubmodulos,
            'id' => $id,
            'IDEscola' => self::getEscolaDiretor(Auth::user()->id)
        ]);
    }

    public function atividades($id){

        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }

        $IDTurma = Aluno::find($id)->IDTurma;

        $Turma = Turma::find($IDTurma);
        switch($Turma->Periodo){
            case 'Bimestral':
                $Estagios = array("1º BIM","2º BIM","3º BIM","4º BIM");
            break;
            case 'Trimestral':
                $Estagios = array("1º TRI","2º TRI","3º TRI");
            break;
            case 'Semestral':
                $Estagios = array("1º SEM","2º SEM");
            break;
            case 'Anual':
                $Estagios = array("1º PER");
            break;
        }

        $IDOrg = Auth::user()->id_org;

        $Disciplinas = json_encode(DB::select("SELECT 
            d.NMDisciplina as Disciplina,
            d.id as IDDisciplina
        FROM disciplinas d 
        INNER JOIN alocacoes_disciplinas ad ON(d.id = ad.IDDisciplina)
        INNER JOIN escolas e ON(e.id = ad.IDEscola)
        WHERE e.IDOrg = $IDOrg GROUP BY d.id"));
        return view('Alunos.atividades',[
            'submodulos' => $submodulos,
            'id' => $id,
            'Disciplinas' => (Auth::user()->tipo == 6) ? ProfessoresController::getNomeDisiciplinasProfessor(Auth::user()->id) : json_decode($Disciplinas,true),
            "Estagios" => $Estagios
        ]);
    }

    public function getAtividadesAluno($IDAluno){
        $AND = "";
        $Ano = date('Y');
        $registros = [];
        if(isset($_GET['Disciplina'])){
            $AND = " AND d.id=".$_GET['Disciplina']." AND a.Estagio='".$_GET['Estagio']."'";
            $AND .= " AND DATE_FORMAT(a.DTAula, '%Y') = $Ano"; 
            $idorg = Auth::user()->id_org;
            $SQL = "
                SELECT 
                    at.TPConteudo, 
                    a.Estagio,
                    t.TPAvaliacao,
                    d.NMDisciplina as Disciplina,
                    n.Conceito,
                    a.DTAula,
                    MAX(CASE WHEN att.IDAluno = n.IDAluno THEN n.Nota ELSE 0 END) as Nota -- Pega a maior nota para evitar duplicações
                FROM 
                    atividades at
                INNER JOIN 
                    aulas a ON(a.id = at.IDAula)
                INNER JOIN 
                    turmas t ON(t.id = a.IDTurma)
                INNER JOIN 
                    disciplinas d ON(d.id = a.IDDisciplina)
                INNER JOIN 
                    atividades_atribuicoes att ON(at.id = att.IDAtividade)
                LEFT JOIN 
                    notas n ON(att.IDAluno = n.IDAluno AND att.IDAtividade = n.IDAtividade) -- Junta notas sem duplicar
                WHERE 
                    n.IDAluno = $IDAluno $AND
                GROUP BY 
                    at.TPConteudo, 
                    a.Estagio,
                    at.created_at,
                    d.NMDisciplina
            ";            

            $registros = DB::select($SQL);
        }
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Disciplina;
                $item[] = $r->TPConteudo;
                $item[] = ($r->TPAvaliacao == "Nota") ? $r->Nota : $r->Conceito;
                $item[] = $r->Estagio;
                $item[] = date('d/m/Y', strtotime($r->DTAula));
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

    public function matriculaTransferidos($id){

        $idorg = Auth::user()->id_org;
        $IDEscola = self::getEscolaDiretor(Auth::user()->id);
        $SQL = "SELECT
            a.id as IDAluno, 
            tr.id as IDTransferencia,
            eDestino.Nome as EscolaDestino,
            eOrigem.Nome as EscolaOrigem,
            tr.Justificativa,
            m.Foto,
            m.CDPasta,
            tr.created_at as DTTransferencia
        FROM transferencias tr
        INNER JOIN alunos a ON(a.id = tr.IDAluno)
        INNER JOIN matriculas m ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas eDestino ON(tr.IDEscolaDestino = eDestino.id)
        INNER JOIN escolas eOrigem ON(tr.IDEscolaOrigem = eOrigem.id)
        INNER JOIN organizacoes o ON(eOrigem.IDOrg = o.id)
        WHERE o.id = $idorg AND eDestino.id = $IDEscola AND tr.id = $id    
        ";

        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            'Turmas' => Turma::where('IDEscola',self::getEscolaDiretor(Auth::user()->id))->get(),
            'Registro' => DB::select($SQL)[0]
        ];

        return view('Alunos.matriculaTransferidos',$view);
    }

    public function transferidos(){
        return view('Alunos.transferidos',[
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function matricularTransferido(Request $request){
        try{
            if($request->IDTurma != 0){
                $IDEscola = self::getEscolaDiretor(Auth::user()->id);
                $Aluno = Aluno::find($request->IDAluno);
                $Transferencia = Transferencia::find($request->IDTransferencia);
                Remanejo::create([
                    "IDTurmaOrigem" => $Aluno->IDTurma,
                    "IDTurmaDestino" => $request->IDTurma,
                    "IDAluno" => $request->IDAluno,
                    "IDEscola" => $IDEscola,
                    "created_at" => $Transferencia->DTTransferencia
                ]);

                $Aluno->update(['IDTurma'=>$request->IDTurma]);
                $Transferencia->update(['Aprovado'=> 1]);
                DB::update("UPDATE escolas SET QTVagas = QTVagas-1 WHERE id = $IDEscola");
                $mensagem = 'Transferência Realizada com Sucesso!';
            }else{
                Transferencia::find($request->IDTransferencia)->update(['Aprovado'=> 2]);
                $mensagem = 'Transferência Reprovada';
            }

            FeedbackTransferencia::create(['IDTransferencia'=>$request->IDTransferencia,'Feedback'=>$request->Feedback]);
            $rout = 'Alunos/Transferidos';
            $aid = '';
            $status = 'success';
        }catch(\Throwable $th){
            $rout = 'Alunos/Transferidos/Transferido';
            $aid = $request->IDTransferencia;
            $status = 'error';
            $mensagem = 'Erro ao Transferir '.$th->getMessage();
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getTransferidos(){
        $idorg = Auth::user()->id_org;
        $AND = " AND eDestino.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $SQL = "SELECT
            tr.id as IDTransferencia,
            a.id as IDAluno, 
            eDestino.Nome as EscolaDestino,
            eOrigem.Nome as EscolaOrigem,
            tr.Justificativa,
            tr.DTTransferencia
        FROM transferencias tr
        INNER JOIN alunos a ON(a.id = tr.IDAluno)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas eDestino ON(tr.IDEscolaDestino = eDestino.id)
        INNER JOIN escolas eOrigem ON(tr.IDEscolaOrigem = eOrigem.id)
        INNER JOIN organizacoes o ON(eOrigem.IDOrg = o.id)
        WHERE o.id = $idorg $AND AND tr.Aprovado = 0   
        ";

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->EscolaOrigem;
                $item[] = Controller::data($r->DTTransferencia,'d/m/Y');
                $item[] = $r->Justificativa;
                $item[] = "<a href=".route('Alunos/Transferidos/Transferido',$r->IDTransferencia)." class='btn btn-fr btn-xs'>Aprovar/Reprovar</a>";
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

    public function cadastro($id=null){
        $idorg = Auth::user()->id_org;
        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresModulos;
        }else{
            $submodulos = self::submodulos;
        }

        $view = [
            'submodulos' => $submodulos,
            'Turmas' => Turma::join('escolas', 'turmas.IDEscola', '=', 'escolas.id')
            ->select('turmas.id as IDTurma','turmas.Serie','turmas.Nome as Turma', 'escolas.Nome as Escola')->whereIn('turmas.IDEscola',EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional))
            ->get(),
            'id' => '',
            "Rotas" => TransporteController::SQLRotas()
        ];
        
        if($id){
            $ReclassificacoesSQL = "SELECT 
                td.Serie as Destino,
                tor.Serie as Origem 
            FROM 
                reclassificacoes rc 
            INNER JOIN 
                alunos al ON(al.id = rc.IDAluno)
            INNER JOIN 
                turmas as tor ON(tor.id = rc.IDTurmaAntiga)
            INNER JOIN
                turmas as td ON(td.id = rc.IDTurmaNova)
            WHERE al.id = $id
            ";

            $RemanejamentosSQL = "SELECT 
                td.Serie as Destino,
                tor.Serie as Origem,
                tor.Nome as OrigemNome,
                td.Nome as DestinoNome 
            FROM 
                remanejados rm 
            INNER JOIN 
                alunos al ON(al.id = rm.IDAluno)
            INNER JOIN 
                turmas as tor ON(tor.id = rm.IDTurmaOrigem)
            INNER JOIN
                turmas as td ON(td.id = rm.IDTurmaDestino)
            WHERE al.id = $id
            ";
            $Registro = self::getAluno($id);
            $Vencimento = Carbon::parse($Registro->INIRematricula);
            $Hoje = Carbon::parse(date('Y-m-d'));
            if(self::getDados()['tipo'] == 6){
                $view['submodulos'] = self::professoresSubmodulos;
            }else{
                $view['submodulos'] = self::cadastroSubmodulos;
            }
   
            $view['id'] = $id;
            $view['IDOrg'] = Auth::user()->id_org;
            $view['Registro'] = $Registro;
            $view['Pais'] = json_decode($Registro->PaisJSON);
            $view['Vencimento'] = $Vencimento;
            $view['Hoje'] = $Hoje;
            $view['Remanejamentos'] = DB::select($RemanejamentosSQL);
            $view['Reclassificacoes'] = DB::select($ReclassificacoesSQL);
        }

        return view('Alunos.cadastro',$view);
    }

    public function recuperacao($IDAluno,$Estagio){
        $SQL = "SELECT atv.id as IDAtividade 
        FROM atividades atv 
        INNER JOIN aulas a ON (a.id = atv.IDAula)
        LEFT JOIN notas n ON(n.IDAtividade = atv.id) 
        WHERE a.Estagio LIKE '$Estagio%' AND n.IDAluno = $IDAluno";
        $IDAtividades = DB::select($SQL);
        $IDAtividadesArray = array_map(function($item) {
            return $item->IDAtividade;
        }, $IDAtividades);
        //dd($IDAtividadesArray);
        //dd(Nota::whereIn('IDAtividade',$IDAtividadesArray)->where('IDAluno',$IDAluno)->get()->toArray());
        Nota::whereIn('IDAtividade',$IDAtividadesArray)->where('IDAluno',$IDAluno)->delete();
        return redirect()->back();
    }

    public function importarAlunos(Request $request, $IDTurma){
        if($request->file('Alunos')){
            $arquivo = $request->file("Alunos");
            // Lê o conteúdo do arquivo
            $conteudo = file_get_contents($arquivo->getRealPath());

            // Decodifica o JSON para um array associativo
            $sheetData = json_decode($conteudo, true);
            // Processa os dados da planilha
            //dd($sheetData);
            foreach ($sheetData as $key => $row) {
                // Verifica se a linha tem os dados necessários
                if (!empty($row['Nome'])) {
                    // Criar a matrícula              
                    $matricula = Matriculas::create([
                        "Nome" => $row['Nome'],
                        "Sexo" => $row['Sexo'],
                        "Nascimento" => (DateTime::createFromFormat('d/m/Y', $row['Nascimento'])) ? DateTime::createFromFormat('d/m/Y', $row['Nascimento'])->format('Y-m-d') : null,
                        "CPF" => preg_replace('/\D/', '', $row['CPF']),
                        "Observacoes" => $row['Observacoes'],
                        "Cor" => $row['Cor'],
                        "CDPasta" => rand(0, 99999999999), // Adiciona o campo CDPasta
                        "PaisJSON" => json_encode($row['PaisJSON'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        "Observacoes"=> $row['Observacoes'],
                        "UF"=> $row['UF'],
                        "Cidade" => $row['Cidade'],
                        "Bairro"=> $row['Bairro'],
                        "Numero"=> is_numeric($row['Numero']) ? $row['Numero'] : '',
                        "CEP" => is_numeric($row['CEP']) ? $row['CEP'] : '',
                        "Celular" => preg_replace('/\D/', '', $row['Celular']),
                        "NEE" => !is_null($row['NEE']) ? 1 : 0
                        //"Nascimento" (strtotime($row['Nascimento']) !== false) ? DateTime::createFromFormat('d/m/Y', $row['Nascimento'])->format('Y-m-d') : null
                    ]);
            
                    // Verifica se a matrícula foi criada
                    if ($matricula) {
                        // Criar aluno
                        $aluno = Aluno::create([
                            "IDMatricula" => $matricula->id,
                            "STAluno" => 0,
                            "IDTurma" => $IDTurma,
                            "Nascimento" => (DateTime::createFromFormat('d/m/Y', $row['DTEntrada'])) ? DateTime::createFromFormat('d/m/Y', $row['DTEntrada'])->format('Y-m-d') : null,
                        ]);
            
                        // Verifica se o aluno foi criado
                        if ($aluno) {
                            // Criar responsável
                            Responsavel::create([
                                "IDAluno" => $aluno->id,
                                "CLResponsavel" => preg_replace('/\D/', '', $row['CLResponsavel']),
                                "NMResponsavel" => !is_null($row['NMResponsavel']) ? 'Não informado' : '',
                                "CPFResponsavel" => preg_replace('/\D/', '', $row['CPFResponsavel'])
                            ]);
            
                            // Criar renovações
                            Renovacoes::create([
                                "IDAluno" => $aluno->id,
                                "Aprovado" => 1,
                                "ANO" => date('Y')+1
                            ]);
                        }
                    }
                }
            }            

            return redirect()->back();
        }
    }

    public static function getAlunoByUser($IDAluno){
        return DB::select("SELECT id FROM alunos WHERE IDUser = $IDAluno")[0]->id;
    }

    public static function getDadosBoletim($IDAluno){
            $SQL = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                d.id as IDDisciplina,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="1º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = $IDAluno AND au2.IDDisciplina = d.id AND au2.Estagio = "1º BIM" 
                AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y')) as Faltas1B,
                AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id
            (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="2º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
            FROM frequencia f2 
            INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
            WHERE au2.TPConteudo = 0 AND f2.IDAluno = $IDAluno AND au2.IDDisciplina = d.id AND au2.Estagio = "2º BIM" 
            AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y')) as Faltas2B,
            
            (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="3º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = $IDAluno AND au2.IDDisciplina = d.id AND au2.Estagio = "3º BIM" 
                AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y')) as Faltas3B,
            
            (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="4º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = $IDAluno AND au2.IDDisciplina = d.id AND au2.Estagio = "4º BIM" 
                AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y')) as Faltas4B,
            
            -- 1º Bimestre
            CASE WHEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "1º BIM" 
                AND rec2.IDAluno = $IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y')) > 0
            THEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "1º BIM" 
                AND rec2.IDAluno = $IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y'))
            ELSE 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) 
                INNER JOIN aulas au3 ON(at2.IDAula = au3.id) 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '1º BIM' 
                AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y'))
            END as Nota1B,

            -- 2º Bimestre
            CASE WHEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "2º BIM" 
                AND rec2.IDAluno = $IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y')) > 0
            THEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "2º BIM" 
                AND rec2.IDAluno = $IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y'))
            ELSE 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) 
                INNER JOIN aulas au3 ON(at2.IDAula = au3.id) 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '2º BIM' 
                AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y'))
            END as Nota2B,

            -- 3º Bimestre
            CASE WHEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "3º BIM" 
                AND rec2.IDAluno = $IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y')) > 0
            THEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "3º BIM" 
                AND rec2.IDAluno = $IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y'))
            ELSE 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) 
                INNER JOIN aulas au3 ON(at2.IDAula = au3.id) 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '3º BIM' 
                AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y'))
            END as Nota3B,

            -- 4º Bimestre
            CASE WHEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "4º BIM" 
                AND rec2.IDAluno = $IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y')) > 0
            THEN 
                (SELECT rec2.Nota 
                FROM recuperacao rec2 
                WHERE rec2.Estagio = "4º BIM" 
                AND rec2.IDAluno = $IDAluno 
                AND rec2.IDDisciplina = d.id 
                AND rec2.created_at = DATE_FORMAT(NOW(), '%Y'))
            ELSE 
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) 
                INNER JOIN aulas au3 ON(at2.IDAula = au3.id) 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '4º BIM' 
                AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(), '%Y'))
            END as Nota4B
            FROM disciplinas d
            INNER JOIN aulas au ON(d.id = au.IDDisciplina)
            INNER JOIN frequencia f ON(au.id = f.IDAula)
            INNER JOIN alunos a ON(a.id = f.IDAluno)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN atividades at ON(at.IDAula = au.id)
            INNER JOIN notas n ON(at.id = n.IDAtividade)
            WHERE a.id = $IDAluno
            GROUP BY d.id

            SQL;
            
        // echo $SQL;
        // dd("Aqui");
        $queryBoletim = DB::select($SQL);

        return $queryBoletim;
    }

    public function getDesempenhoGeral($IDAluno){
        $IDOrg = Auth::user()->id_org;
        $IDAluno = self::getAlunoByUser(Auth::user()->id);
        $DadosBoletim = self::getDadosBoletim($IDAluno);
        
        //ARMAZENAMENTO DAS NOTAS DO ALUNO
        $boletins = array();
        // Verificar se o aluno tem notas lançadas
        if (!empty(self::getDadosBoletim($IDAluno))) {
            
            $boletins = array(
                "DadosAluno" => self::getAluno($IDAluno),
                "Disciplinas" => []
            );
            // Adicionar ao boletim somente se há dados
            foreach ($DadosBoletim as $boletim) {
                $boletins['Disciplinas'][] = array(
                    "Disciplina" => $boletim->Disciplina,
                    "IDDisciplina" => $boletim->IDDisciplina,
                    "IDAluno" => $IDAluno,
                    "Nota1B"=> $boletim->Nota1B,
                    "Nota2B"=> $boletim->Nota2B,
                    "Nota3B"=> $boletim->Nota3B,
                    "Nota4B"=> $boletim->Nota4B,
                    "Faltas1B"=> $boletim->Faltas1B,
                    "Faltas2B"=> $boletim->Faltas2B,
                    "Faltas3B"=> $boletim->Faltas3B,
                    "Faltas4B"=> $boletim->Faltas4B,
                );
            }
        }

        //dd($boletins);

        if(count($boletins) == 0){
            
            $dadosDesempenho = array(
                "Boletim" => 0,
                "Frequencia" => 0, 
                "PFrequencia" => 0
            );
        }else{
            $Frequencia = array_reduce($boletins['Disciplinas'], function ($total, $disciplina) {
                return $total + $disciplina['Faltas1B'] + $disciplina['Faltas2B'] + $disciplina['Faltas3B'] + $disciplina['Faltas4B'];
            }, 0);
            $dadosDesempenho = array(
                "Boletim" => $boletins,
                "Frequencia" => $Frequencia, 
                "PFrequencia" => ($Frequencia/200) * 100
            );
        }

        return $dadosDesempenho;
        
    }

    public static function getTotalDisciplinaAno($IDAluno,$IDDisciplina){
        $SQL = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                m.Nome,
                a.id as IDAluno,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.IDDisciplina = d.id AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                AND au2.IDDisciplina = d.id 
                AND DATE_FORMAT(au2.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')
                ) as FrequenciaAno,
                (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "ANUAL" AND rec2.IDAluno = $IDAluno AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as RecAn,
                (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Nota,
                (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as PontBim,
                (SELECT SUM(rec2.Nota) 
                FROM recuperacao rec2 
                WHERE rec2.Estagio !="ANUAL" AND rec2.IDAluno = a.id 
                AND rec2.IDDisciplina = d.id 
                ) as RecBim,
                (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria))
                    FROM frequencia f2 
                    INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                    WHERE f2.IDAluno = a.id 
                    AND DATE_FORMAT(au2.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) as CargaHoraria 
            FROM disciplinas d
            INNER JOIN aulas au ON(d.id = au.IDDisciplina)
            INNER JOIN frequencia f ON(au.id = f.IDAula)
            INNER JOIN alunos a ON(a.id = f.IDAluno)
            INNER JOIN matriculas m ON(m.id = a.IDMatricula)
            INNER JOIN atividades at ON(at.IDAula = au.id)
            INNER JOIN notas n ON(at.id = n.IDAtividade)
            WHERE a.id = $IDAluno AND d.id = $IDDisciplina AND DATE_FORMAT(f.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')
            GROUP BY m.Nome, m.id, d.id
        SQL;
    
        $queryBoletim = DB::select($SQL);

        if(count($queryBoletim) == 0){
           $Total = 0; 
        }else{
            if($queryBoletim[0]->RecAn > 0){
                $Total = $queryBoletim[0]->RecAno;
            }else{
                $Total = $queryBoletim[0]->Nota;
            }
        }

        return $Total;
    }

    public function matriculas(){
        $IDAluno = self::getAlunoByUser(Auth::user()->id);
        $SQLMatriculas = "SELECT
            m.Nome as Aluno,
            eDestino.Nome as Escola,
            tr.created_at as DTMatricula
        FROM transferencias tr
        INNER JOIN alunos a ON(a.id = tr.IDAluno)
        INNER JOIN matriculas m ON(m.id = a.IDMatricula)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas eDestino ON(tr.IDEscolaDestino = eDestino.id)
        WHERE a.id = $IDAluno AND tr.Aprovado = 1  
        ";

        //dd($IDAluno);
        $Matricula = self::getAluno($IDAluno);
        $Matriculas = DB::select($SQLMatriculas);

        return view('Alunos.matricula',[
            "submodulos" => array([
                "nome" => "Matrículas",
                "endereco" => "index",
                "rota" => "Matriculas/index"
            ]),
            "Matricula" => $Matricula,
            "Matriculas" => $Matriculas
        ]);
    }

    
    public function desempenho(){
        
        $IDAluno = self::getAlunoByUser(Auth::user()->id);
        $Conceitos = FichaController::getFichaAluno($IDAluno);
        $Boletim = self::getDesempenhoGeral($IDAluno);
        //dd($Boletim['Boletim']);
        return view('Alunos.desempenho',[
            "submodulos" => array([
                "nome" => "Desempenho",
                "endereco" => "index",
                "rota" => "Desempenho/index"
            ]),
            "Boletim" => $Boletim,
            "TPAvaliacao" => "Nota",
            "conceitos" => $Conceitos
        ]);
    }

    public static function getAlunosTurma($IDTurma){
        $SQL = "SELECT
            a.id as IDAluno, 
            m.Nome as Nome,
            t.Nome as Turma,
            e.Nome as Escola,
            t.Serie as Serie,
            m.Nascimento as Nascimento,
            a.STAluno,
            m.Foto,
            m.INEP,
            m.Email,
            ats.created_at as DTSituacao,
            m.CPF,
            resp.NMResponsavel,
            r.ANO,
            m.NEE,
            m.Sexo,
            m.created_at,
            resp.CLResponsavel,
            MAX(tr.Aprovado) as Aprovado,
            cal.INIRematricula,
            cal.TERRematricula,
            cal.INIAno,
            cal.TERAno,
            r.ANO,
            re.NMRestricao,
            CASE WHEN cv.IDAluno IS NOT NULL THEN 'checked' ELSE '' END as Marcado
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN renovacoes r ON(r.IDAluno = a.id)
        LEFT JOIN alteracoes_situacao ats ON(ats.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
        INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
        LEFT JOIN restricoes_alimentares re ON(re.IDAluno = a.id)
        LEFT JOIN cardapio_vinculo cv ON(cv.IDAluno = a.id)
        WHERE t.id = $IDTurma GROUP BY a.id ORDER BY m.Nome ASC 
        ";
        return DB::select($SQL);
    }

    public static function getAlunosTurmas(){
        $idorg = Auth::user()->id_org;
        $WHERE = "WHERE ";
        if(in_array(Auth::user()->tipo,[2,2.5])){
            $WHERE .= "e.IDOrg=".Auth::user()->id_org;;
        }else{
            $WHERE .="e.id = ".self::getEscolaDiretor(Auth::user()->id);
        }

        $SQL = "SELECT t.id as IDTurma,t.Nome as Turma,t.Serie,e.Nome as Escola FROM turmas t INNER JOIN escolas e ON(e.id = t.IDEscola) $WHERE";
        $AlunosTurmas = [];
        $Turmas = DB::select($SQL);

        foreach($Turmas as $t){
            $AlunosTurmas[$t->Serie." - ".$t->Turma] = self::getAlunosTurma($t->IDTurma);
        }

        return $AlunosTurmas;
    }

    public function saveEspera(Request $request){
        try{
            $data = $request->all();
            if(Auth::user()->tipo == 4){
                $data['IDEscola'] = self::getEscolaDiretor(Auth::user()->id);
            }else{
                $data['IDEscola'] = $request->IDEscola;
            }

            if($request->id){
                Espera::find($request->id)->update($data);
                $rota = 'Alunos/Espera/Edit';
                $aid = $request->id;
            }else{
                Espera::create($data);
                $aid = '';
                $rota = 'Alunos/Espera/Novo';
            }
            $mensagem = "Salvamento Realizado com Sucesso!";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Alunos/Espera/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function espera(){
        return view('Alunos.espera',[
            "submodulos" => self::submodulos
        ]);
    }

    public function getEspera(){
        $idorg = Auth::user()->id_org;
        $AND = " WHERE e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $SQL = <<<SQL
            SELECT 
                le.id,
                le.Aluno,
                e.Nome as Escola,
                le.Observacoes,
                le.Contato
            FROM lista_espera le
            INNER JOIN escolas e ON(e.id = le.IDEscola)
            $AND
        SQL;

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Escola;
                $item[] = $r->Aluno;
                $item[] = $r->Contato;
                $item[] = $r->Observacoes;
                $item[] = "<a href=".route('Alunos/Espera/Edit',$r->id)." class='btn btn-fr btn-xs'>Editar</a>&nbsp; <a href=".route('Alunos/Espera/Delete',$r->id)." class='btn btn-danger btn-xs'>Excluir</a>";
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

    public function deleteEspera($id){
        Espera::find($id)->delete();
        return redirect()->back();
    }

    public function cadastroEspera($id=null){
        $view = [
            "submodulos" => self::submodulos,
            "id" => ""
        ];

        $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
        $view['Escolas'] = Escola::findMany($IDEscolas);

        if($id){
            $view['id'] = $id;
            $view['Registro'] = Espera::find($id);
        }

        return view('Alunos.cadastroEspera',$view);
    }

    public function save(Request $request){
        try{
            $CDPasta = rand(0,99999999999);
            //dd($request->file('RGPaisAnexo')->getClientOriginalName());
            //
            if(!$request->IDMatricula || !$request->IDAluno){

    
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    $request->file('Foto')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$Foto,'public');
                }else{
                    $Foto = '';
                }

                if($request->credenciaisLogin){
                    $rnd = rand(100000,999999);
                    SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                    $UserAluno = User::create([
                        'name' => $request->Nome,
                        'email' => $request->Email,
                        'tipo' => 7,
                        'password' => Hash::make($rnd)
                    ]);
                }

                $Pais = array(
                    "Mae" => $request->Mae,
                    "Pai" => $request->Pai,
                    "ProfissaoMae" => $request->ProfissaoMae,
                    "ProfissaoPai" => $request->ProfissaoPai,
                    "RGMae" => $request->RGMae,
                    "RGPai" => $request->RGPai,
                    "CPFMae" => $request->CPFMae,
                    "CPFPai" => $request->CPFPai,
                    "EscolaridadeMae" => $request->EscolaridadeMae,
                    "EscolaridadePai" => $request->EscolaridadePai,
                    "EmailMae" => $request->EmailMae,
                    "EmailPai" => $request->EmailPai,
                    "NascimentoMae" => $request->NascimentoMae,
                    "NascimentoPai" => $request->NascimentoPai,
                    "TelefonePai" => $request->TelefonePai,
                    "TelefoneMae" => $request->TelefoneMae
                );
    
                $matricula = array(
                    "SUS" => $request->SUS,
                    "Passaporte" => $request->Passaporte,
                    "CNH" => $request->CNH,
                    'Nome' => $request->Nome,
                    'CPF' => preg_replace('/\D/', '', $request->CPF),
                    'RG' => preg_replace('/\D/', '', $request->RG),
                    'CEP' => preg_replace('/\D/', '', $request->CEP),
                    'Rua' => $request->Rua,
                    'Email' => $request->Email,
                    'Celular' => preg_replace('/\D/', '', $request->Celular),
                    'UF' => $request->UF,
                    'Cidade' => $request->Cidade,
                    'BolsaFamilia' => $request->BolsaFamilia,
                    'Alergia' => $request->Alergia,
                    'Transporte' => $request->Transporte,
                    'NEE' => $request->NEE,
                    'AMedico' => $request->AMedico,
                    'APsicologico' => $request->APsicologico,
                    'Aprovado' => 1,
                    'Foto' => $Foto,
                    'Bairro' => $request->Bairro,
                    'Numero' => $request->Numero,
                    'Nascimento' => $request->Nascimento,
                    'CDPasta' => $CDPasta,
                    "EFisica" => $request->EFisica,
                    "EReligioso" => $request->EReligioso,
                    "DireitoImagem" => $request->DireitoImagem,
                    "Quilombola" => $request->Quilombola,
                    "Cor" => $request->Cor,
                    "Sexo" => $request->Sexo,
                    "INEP" => $request->INEP,
                    "NIS" => $request->NIS,
                    "Naturalidade" => $request->Naturalidade,
                    "IDRota" => $request->IDRota,
                    "TPSangue" => $request->TPSangue,
                    "Expedidor" => $request->Expedidor,
                    "CNascimento"=>$request->CNascimento
                );

                $matricula['PaisJSON'] = json_encode($Pais);

                $createMatricula = Matriculas::create($matricula);

                $aluno = array(
                    'IDMatricula' => $createMatricula->id,
                    'STAluno' => 0,
                    'IDTurma' => $request->IDTurma,
                    'DTEntrada' => $request->DTEntrada
                );

                if($request->credenciaisLogin){
                    $aluno['IDUser'] = $UserAluno->id;
                }

                $createAluno = Aluno::create($aluno);

                $renovacao = array(
                    'IDAluno' => $createAluno->id,
                    'Aprovado' => 1,
                    'Vencimento' => $request->Vencimento,
                    'ANO' => date('Y')
                );

                Renovacoes::create($renovacao);

                $responsavel = array(
                    'IDAluno' => $createAluno->id,
                    'RGPais' => preg_replace('/\D/', '', $request->RGPais),
                    'NMResponsavel' => $request->NMResponsavel,
                    'EmailResponsavel' => $request->EmailResponsavel,
                    'CLResponsavel' => preg_replace('/\D/', '', $request->CLResponsavel),
                    'CPFResponsavel' => preg_replace('/\D/', '', $request->CPFResponsavel),
                    'Profissao' => $request->Profissao,
                    'Escolaridade' => $request->Escolaridade
                );

                Responsavel::create($responsavel);
                $IDEscola = Turma::find($request->IDTurma)->IDEscola;
                DB::update("UPDATE escolas SET QTVagas = QTVagas-1 WHERE id = $IDEscola");
                $aid = '';
                $rout = 'Alunos/Novo';
            }else{
                
    
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldFoto);
                    $request->file('Foto')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Foto,'public');
                }else{
                    $Foto = '';
                }

                $aluno = array(
                    'STAluno' => 0,
                    'IDTurma' => $request->IDTurma,
                    'DTEntrada' => $request->DTEntrada
                );

                if($request->credenciaisLogin){
                    $rnd = rand(100000,999999);
                    SMTPController::send($request->Email,"FR Educacional",'Mail.senha',array("Senha"=>$rnd,"Email"=>$request->Email));
                    $mensagem = 'Salvamento Feito com Sucesso! as Novas Credenciais de Login foram Enviadas no Email Cadastrado';
                    $Usuario = User::find($request->IDUser);
                    if($Usuario){
                        $Usuario->update([
                            'name' => $request->Nome,
                            'email' => $request->Email,
                            'tipo' => 7,
                            'password' => Hash::make($rnd)
                        ]);
                    }else{
                        $UserAluno = User::create([
                            'name' => $request->Nome,
                            'email' => $request->Email,
                            'tipo' => 7,
                            'password' => Hash::make($rnd)
                        ]);

                        $aluno['IDUser'] = $UserAluno->id;
                    }
                    
                }

                $Pais = array(
                    "Mae" => $request->Mae,
                    "Pai" => $request->Pai,
                    "ProfissaoMae" => $request->ProfissaoMae,
                    "ProfissaoPai" => $request->ProfissaoPai,
                    "RGMae" => $request->RGMae,
                    "RGPai" => $request->RGPai,
                    "CPFMae" => $request->CPFMae,
                    "CPFPai" => $request->CPFPai,
                    "EscolaridadeMae" => $request->EscolaridadeMae,
                    "EscolaridadePai" => $request->EscolaridadePai,
                    "EscolaridadePai" => $request->EscolaridadePai,
                    "EmailMae" => $request->EmailMae,
                    "EmailPai" => $request->EmailPai,
                    "NascimentoMae" => $request->NascimentoMae,
                    "NascimentoPai" => $request->NascimentoPai,
                    "TelefonePai" => $request->TelefonePai,
                    "TelefoneMae" => $request->TelefoneMae
                );
    
                $matricula = array(
                    "SUS" => $request->SUS,
                    "Passaporte" => $request->Passaporte,
                    "CNH" => $request->CNH,
                    'Nome' => $request->Nome,
                    'CPF' => preg_replace('/\D/', '', $request->CPF),
                    'RG' => preg_replace('/\D/', '', $request->RG),
                    'CEP' => preg_replace('/\D/', '', $request->CEP),
                    'Rua' => $request->Rua,
                    'Email' => $request->Email,
                    'Celular' => preg_replace('/\D/', '', $request->Celular),
                    'UF' => $request->UF,
                    'Cidade' => $request->Cidade,
                    'BolsaFamilia' => $request->BolsaFamilia,
                    'Alergia' => $request->Alergia,
                    'Transporte' => $request->Transporte,
                    'NEE' => $request->NEE,
                    'AMedico' => $request->AMedico,
                    'APsicologico' => $request->APsicologico,
                    'Aprovado' => 1,
                    'Foto' => $Foto,
                    'Bairro' => $request->Bairro,
                    'Numero' => $request->Numero,
                    'Nascimento' => $request->Nascimento,
                    "Autorizacao" => $request->Autorizacao,
                    "Quilombola" => $request->Quilombola,
                    "EFisica" => $request->EFisica,
                    "EReligioso" => $request->EReligioso,
                    "DireitoImagem" => $request->DireitoImagem,
                    "Cor" => $request->Cor,
                    "Sexo" => $request->Sexo,
                    "INEP" => $request->INEP,
                    "NIS" => $request->NIS,
                    "Naturalidade" => $request->Naturalidade,
                    "IDRota" => $request->IDRota,
                    "TPSangue" => $request->TPSangue,
                    "Expedidor" => $request->Expedidor,
                    "CNascimento"=>$request->CNascimento
                );

                //dd($matricula);

                $matricula['PaisJSON'] = json_encode($Pais);

                if(empty($Foto)){
                    unset($matricula['Foto']);
                }

                if(empty($request->RG)){
                    unset($matricula['RG']);
                }

                if(empty($request->CPF)){
                    unset($matricula['CPF']);
                }

                if(is_null($request->Nome)){
                    unset($matricula['Nome']);    
                }

                if(is_null($request->Nascimento)){
                    unset($matricula['Nascimento']);    
                }

                //dd($matricula);

                Matriculas::find($request->IDMatricula)->update($matricula);

                $IDTurmaOrigem = Aluno::find($request->IDAluno)->IDTurma;
                $IDEscola = Turma::find($IDTurmaOrigem)->IDEscola;

                //dd($IDEscola);

                Aluno::find($request->IDAluno)->update($aluno);

                $responsavel = array(
                    'RGPais' => preg_replace('/\D/', '', $request->RGPais),
                    'NMResponsavel' => $request->NMResponsavel,
                    'EmailResponsavel' => $request->EmailResponsavel,
                    'CLResponsavel' => preg_replace('/\D/', '', $request->CLResponsavel),
                    'CPFResponsavel' => preg_replace('/\D/', '', $request->CPFResponsavel),
                    'Profissao' => $request->Profissao,
                    'Escolaridade' => $request->Escolaridade
                );

                Responsavel::where('IDAluno',$request->IDAluno)->update($responsavel);

                $aid = $request->IDAluno;
                $rout = 'Alunos/Edit';
            }
            $status = 'success';
            $mensagem = 'Salvamento Feito com Sucesso!';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Novo';
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function renovar(Request $request){
        try{
            DB::update("UPDATE renovacoes SET ANO = ANO + 1 WHERE IDAluno = '$request->IDAluno'");
            $rout = 'Alunos/Edit';
            $aid = $request->IDAluno;
            $status = 'success';
            $mensagem = 'Matrícula Renovada com Sucesso!';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Novo';
            $aid = '';
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public static function getCDPastaAluno($IDAluno){
        return DB::select("SELECT CDPasta FROM matriculas m INNER JOIN alunos a ON(a.IDMatricula = m.id) WHERE a.id = $IDAluno")[0]->CDPasta;
    }

    public function necessidades($id){
        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }

        return view('Alunos.necessidades',[
            "submodulos"=> $submodulos,
            "id" => $id
        ]);
    }

    public function cadastroNecessidade($IDAluno,$id=null){
        if(self::getDados()['tipo'] == 6){
            $submodulos = self::professoresSubmodulos;
        }else{
            $submodulos = self::cadastroSubmodulos;
        }

        $view = array(
            "submodulos" => $submodulos,
            "IDAluno" => $IDAluno,
            'id' => '',
            "CDPasta" => self::getCDPastaAluno($IDAluno)
        );

        if($id){
            $view['id'] = $id;
            $view['Registro'] = NEE::find($id);
        }

        return view('Alunos.cadastroNecessidade',$view);
    }

    public function saveNecessidade(Request $request){
        try{
            $data = $request->all();
            if($request->id){
                if($request->file('Laudo')){
                    $Laudo = $request->file('Laudo')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldLaudo);
                    $request->file('Laudo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Laudo,'public');
                }else{
                    $Laudo = '';
                }
                $data['Laudo'] = $Laudo;
                NEE::find($request->id)->update($data);
                $mensagem = "Laudo Editado com Sucesso!";
                $aid = array("id"=>$request->id,"IDAluno"=>$request->IDAluno);
                $rota = 'Alunos/NEE/Edit';
            }else{
                // dd($data);
                if($request->file('Laudo')){
                    $Laudo = $request->file('Laudo')->getClientOriginalName();
                    $request->file('Laudo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Laudo,'public');
                }else{
                    $Laudo = '';
                }
                $data['Laudo'] = $Laudo;
                NEE::create($data);
                $mensagem = "Laudo cadastrado com Sucesso!";
                $aid = $request->IDAluno;
                $rota = 'Alunos/NEE/Novo';
            }
            $status = 'success';
            
        }catch(\Throwable $th){
            $rota = 'Alunos/NEE/Novo';
            $mensagem = 'Erro '.$th;
            $aid = $request->IDAluno;
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function getNecessidades($IDAluno){
        $registros = DB::select("SELECT n.id,n.IDAluno,n.DSNecessidade,n.CID,n.DTLaudo,n.Laudo FROM necessidades_aluno n INNER JOIN alunos a ON(a.id = n.IDAluno) WHERE a.id = $IDAluno");
        $IDOrg = Auth::user()->id_org;
        $CDPasta = self::getCDPastaAluno($IDAluno);
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->DSNecessidade;
                $item[] = $r->CID;
                $item[] = $r->DTLaudo;
                $downloadUrl = url("storage/organizacao_{$IDOrg}_alunos/aluno_{$CDPasta}/{$r->Laudo}");
                $item[] = "<a href='".route('Alunos/NEE/Edit',array('id'=>$r->id,'IDAluno'=>$r->IDAluno))."' class='btn btn-primary btn-xs'>Abrir</a> <a href='{$downloadUrl}' download class='btn btn-success btn-xs'>Download Laudo</a>";
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

    public function saveSituacao(Request $request){
        try{
            if($request->STAluno > 0){
                DB::update('UPDATE escolas SET QTVagas = QTVagas + 1');
            }else{
                DB::update('UPDATE escolas SET QTVagas = QTVagas - 1');
            }

            Situacao::create([
                'Justificativa' => $request->Justificativa,
                'IDAluno' => $request->IDAluno,
                'STAluno' => $request->STAluno,
                'DTSituacao' => $request->DTSituacao
            ]);

            if($request->STAluno !=0){
                Aluno::where('id',$request->IDAluno)->update(['STAluno'=> $request->STAluno,'DTSaida'=>$request->DTSituacao]);
            }else{
                Aluno::where('id',$request->IDAluno)->update(['STAluno'=> $request->STAluno,'DTSaida'=>NULL]);
            }

            $status = 'success';
            $mensagem = "Situação Atualizada com Sucesso!";
            $rout = 'Alunos/Situacao/Novo';
            $aid = $request->IDAluno;
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Situacao/Novo';
            $aid = $request->IDAluno;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function cancelaTransferencia(Request $request){
        try{
            Transferencia::find($request->IDTransferencia)->delete();
            Aluno::where('id',$request->IDAluno)->update(['STAluno'=> 0,'DTSaida' => null]);
            $status = 'success';
            $mensagem = "Transferência Cancelada com Sucesso!";
            $rout = 'Alunos/Transferencias';
            $aid = $request->IDAluno;
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Transferencias';
            $aid = $request->IDAluno;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function saveTransferencias(Request $request){
        try{

            if($request->IDEscolaDestino == 0){
                //dd("Teste");
                $IDMatricula = Aluno::find($request->IDAluno)->IDMatricula;
                Matriculas::find($IDMatricula)->update([
                    "STAluno" => 5
                ]);

                Escola::find($request->IDEscolaOrigem)->update([
                    "QTVagas" => Escola::find($request->IDEscolaOrigem)->QTVagas - 1
                ]);

                Aluno::where('id',$request->IDAluno)->update(['STAluno'=> 5,'DTSaida'=>$request->DTTransferencia]);

                Situacao::create([
                    'Justificativa' => $request->Justificativa,
                    'IDAluno' => $request->IDAluno,
                    'STAluno' => 5,
                    'DTSituacao' => $request->DTTransferencia
                ]);
            }
            
            if($request->IDEscolaOrigem !=0){
                Transferencia::create($request->all());
                Escola::find($request->IDEscolaOrigem)->update([
                    "QTVagas" => Escola::find($request->IDEscolaOrigem)->QTVagas - 1
                ]);
            }else{
                Escola::find($request->IDEscolaOrigem)->update([
                    "QTVagas" => Escola::find($request->IDEscolaOrigem)->QTVagas - 1
                ]);
                $trs = $request->all();
                $trs['Aprovado'] = 3;
                Transferencia::create($trs);
            }
            $status = 'success';
            $mensagem = "Transferência Feita com Sucesso!";
            $rout = 'Alunos/Transferencias';
            $aid = $request->IDAluno;
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = $th->getMessage();
            $rout = 'Alunos/Transferencias';
            $aid = $request->IDAluno;
        }finally{
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getTransferencias($IDAluno){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT
            a.id as IDAluno, 
            tr.id as IDTransferencia,
            eOrigem.Nome as EscolaOrigem,
            tr.Justificativa,
            tr.Aprovado,
            CASE WHEN tr.IDEscolaDestino = 0 THEN 'Escola Fora da Rede' ELSE eDestino.Nome END as Destino,
            t.Nome as Turma,
            tr.DTTransferencia,
            CASE WHEN ft.Feedback IS NOT NULL THEN ft.Feedback ELSE '' END as Feedback
        FROM transferencias tr
        INNER JOIN alunos a ON(a.id = tr.IDAluno)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN escolas eOrigem ON(tr.IDEscolaOrigem = eOrigem.id)
        LEFT JOIN escolas eDestino ON(tr.IDEscolaDestino = eDestino.id)
        LEFT JOIN feedback_transferencias as ft ON(ft.IDTransferencia = tr.id)
        INNER JOIN organizacoes o ON(eOrigem.IDOrg = o.id)
        WHERE o.id = $idorg AND a.id = $IDAluno   
        ";

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                switch($r->Aprovado){
                    case '0':
                        $st = "<strong class='text-warning'>Pendente</strong>";
                    break;
                    case '1':
                        $st = "<strong class='text-success'>Aprovado</strong>";
                    break;
                    case '2':
                        $st = "<strong class='text-danger'>Reprovado (".$r->Feedback.")</strong>";
                    break;
                    case '3':
                        $st = "<strong class='text-secondary'>Transferido Para Fora da Rede</strong>";
                    break;
                }
                $opcoes = "<form id='formCancelaTransferencia' style='display:none' method='POST' action=".route('Alunos/Transferencias/Cancela').">
                    <input type='hidden' name='_token' value=".csrf_token().">
                    <input type='hidden' name='IDAluno' value='$r->IDAluno'>
                    <input type='hidden' name='IDTransferencia' value='$r->IDTransferencia'>
                </form>
                ";

                if($r->Aprovado != 1){
                    $opcoes .="<button class='btn btn-xs btn-danger' onclick='cancelarTransferencia($r->IDTransferencia)'>Cancelar Transferencia</button>";
                }

                $item = [];
                $item[] = $r->EscolaOrigem;
                $item[] = $r->Destino;
                $item[] = Controller::data($r->DTTransferencia,'d/m/Y');
                $item[] = $r->Justificativa;
                $item[] = $st;
                $item[] = $opcoes;
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

    function cadastroTransferencias($IDAluno){
        return view('Alunos.cadastroTransferencia',[
            'submodulos'=>self::cadastroSubmodulos,
            'id' => $IDAluno,
            'IDEscola' => self::getEscolaDiretor(Auth::user()->id),
            'Escolas' => Escola::where('IDOrg',Auth::user()->id_org)->get()
        ]);
    }

    public function getAlunos(){
        $idorg = Auth::user()->id_org;
        if(Auth::user()->tipo == 6){
            $AND = " AND a.id IN(".implode(",",ProfessoresController::getIdAlunosProfessor(Auth::user()->IDProfissional)).")";
        }else{
            $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";
        }
        

        if(isset($_GET['Status']) && !empty($_GET['Status'])){
            $AND .= " AND a.STAluno=".$_GET['Status'];
        }

        if(isset($_GET['Escola']) && !empty($_GET['Escola'])){
            $AND .= " AND e.id=".$_GET['Escola'];
        }

        $SQL = "SELECT
            a.id as IDAluno, 
            m.Nome as Nome,
            t.Nome as Turma,
            e.Nome as Escola,
            t.Serie as Serie,
            m.Nascimento as Nascimento,
            a.STAluno,
            m.Foto,
            m.Email,
            r.ANO,
            MAX(tr.Aprovado) as Aprovado,
            cal.INIRematricula,
            cal.TERRematricula,
            cal.INIAno,
            cal.TERAno
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        LEFT JOIN transferencias tr ON(tr.IDAluno = a.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN renovacoes r ON(r.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
        WHERE o.id = $idorg $AND GROUP BY a.id 
        ";
        
        //dd($SQL);
        
        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                switch($r->STAluno){
                    case "0":
                        $Situacao = 'Frequente';
                    break;
                    case "1":
                        $Situacao = "Evadido";
                    break;
                    case "2":
                        $Situacao = "Desistente";
                    break;
                    case "3":
                        $Situacao = "Desligado";
                    break;
                    case "4":
                        $Situacao = "Egresso";
                    break;
                    case "5":
                        $Situacao = "Transferido Para Outra Rede";
                    break;
                }

                if($r->Aprovado == 3){
                    $transferido = "<strong class='text-danger'>(Aluno Transferido Para Outra Rede)</strong>";
                }else{
                    $transferido = '';
                }

                $Vencimento = Carbon::parse($r->INIRematricula);
                $Hoje = Carbon::parse(date('Y-m-d'));
                $INIRematricula = Carbon::parse($r->INIRematricula);
                $item = [];
                $item[] = $r->Nome." ".$transferido;
                $item[] = $r->Turma;
                (in_array(Auth::user()->tipo,[2,2.5])) ? $item[] = $r->Escola : '';
                $item[] = $r->Serie;
                $item[] = ($r->Nascimento) ? Controller::data($r->Nascimento,'d/m/Y') : 'Não Informado';
                $item[] = $Vencimento->lt($Hoje) && $r->ANO <= date('Y') ? "<strong class='text-danger'>PENDENTE RENOVAÇÃO</strong>" : "<strong class='text-success'>RENOVADA</strong>";
                $item[] = "<strong>".$Situacao."</strong>";
                $item[] = " <a href='".route('Alunos/Edit',$r->IDAluno)."' class='btn btn-primary btn-xs'>Visualizar</a>";
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

    public function getSituacao($id){
        $idorg = Auth::user()->id_org;

        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

        $SQL = "SELECT
            a.id as IDAluno, 
            at.STAluno,
            a.STAluno as alunoST,
            at.Justificativa,
            at.DTSituacao
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(t.id = a.IDTurma)
        INNER JOIN alteracoes_situacao at ON(at.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        WHERE o.id = $idorg AND a.id = $id $AND    
        ";

        $registros = DB::select($SQL);
        if(count($registros) > 0){
            foreach($registros as $r){
                switch($r->STAluno){
                    case "0":
                        $Situacao = 'Frequente';
                    break;
                    case "1":
                        $Situacao = "Evadido";
                    break;
                    case "2":
                        $Situacao = "Desistente";
                    break;
                    case "3":
                        $Situacao = "Desligado";
                    break;
                    case "4":
                        $Situacao = "Egresso";
                    break;
                    case "5":
                        $Situacao = "Transferido Para Outra Rede";
                    break;
                }

                $item = [];
                $item[] = $Situacao;
                $item[] = Controller::data($r->DTSituacao,'d/m/Y');
                $item[] = $r->Justificativa;
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