<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aluno;
use App\Models\Matriculas;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\FeedbackTransferencia;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Renovacoes;
use App\Models\Remanejo;
use App\Models\Responsavel;
use App\Models\Anexo;
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

    public function ficha($id){
        $idorg = Auth::user()->id_org;
        $SQL = "SELECT 
            a.id as IDAluno, 
            m.id as IDMatricula,
            m.Nome as Nome,
            t.Nome as Turma,
            e.Nome as Escola,
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
            m.AnexoRG,
            re.RGPaisAnexo,
            m.CResidencia,
            m.Historico,
            re.RGPaisAnexo,
            cal.INIRematricula,
            cal.TERRematricula,
            r.ANO,
            m.PaisJSON,
            m.Quilombola
        FROM matriculas m
        INNER JOIN alunos a ON(a.IDMatricula = m.id)
        INNER JOIN turmas t ON(a.IDTurma = t.id)
        INNER JOIN renovacoes r ON(r.IDAluno = a.id)
        INNER JOIN escolas e ON(t.IDEscola = e.id)
        INNER JOIN organizacoes o ON(e.IDOrg = o.id)
        INNER JOIN responsavel re ON(re.IDAluno = a.id)
        INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
        WHERE o.id = $idorg AND a.id = $id  
        ";
        $Ficha = DB::select($SQL)[0];
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

    public static function getComprovanteMatricula($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Escola = DB::select("SELECT e.id,e.Cidade,e.UF, e.Foto,m.Nome as Aluno,e.Foto,e.Nome as Escola 
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
        $pdf->Cell(0, 10, self::utfConvert("DECLARAÇÃO DE MATRÍCULA"), 0, 1, 'C'); // Título centralizado
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo da declaração
        $pdf->SetFont('Arial', '', 12);

        // Nome da escola e do aluno (exemplo de variáveis $nomeEscola e $nomeAluno)
        $nomeEscola = "Nome da Escola";
        $nomeAluno = "Nome do Aluno";
        $Ano = date('Y');
        // Inserir o texto da declaração
        $declaracao = "Declaramos, para os devidos fins, que o(a) aluno(a) $Escola->Aluno está regularmente matriculado(a) na $Escola->Escola, " .
                    "no ano letivo de $Ano, conforme os registros escolares.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($declaracao, 'ISO-8859-1', 'UTF-8')); // Quebra de linha automática

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        $pdf->Cell(0, 10, self::utfConvert($Escola->Cidade.", ".$Escola->UF." - ".date('d/m/Y H:i:s')), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('D', 'Declaracao_Matricula.pdf');
        exit;

    }

    public static function getComprovanteFrequencia($IDAluno){
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página
        $Escola = DB::select("SELECT e.id,e.Cidade,e.UF, e.Foto,m.Nome as Aluno,e.Foto,e.Nome as Escola 
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
        $pdf->Cell(0, 10, self::utfConvert("DECLARAÇÃO DE FREQUÊNCIA"), 0, 1, 'C'); // Título centralizado
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo da declaração
        $pdf->SetFont('Arial', '', 12);

        // Nome da escola e do aluno (exemplo de variáveis $nomeEscola e $nomeAluno)
        $nomeEscola = "Nome da Escola";
        $nomeAluno = "Nome do Aluno";
        $Ano = date('Y');
        // Inserir o texto da declaração
        $declaracao = "Certificamos que o(a) aluno(a) $Escola->Aluno frequentou regularmente as aulas na $Escola->Escola, " .
              "durante o ano letivo de $Ano, conforme os registros de frequência escolar.";
        $pdf->MultiCell(0, 10, mb_convert_encoding($declaracao, 'ISO-8859-1', 'UTF-8')); // Quebra de linha automática

        // Espaço antes da assinatura
        $pdf->Ln(20);

        // Assinatura (ajuste o tamanho conforme necessário)
        $pdf->Cell(0, 10, "________________________________", 0, 1, 'C'); // Linha de assinatura
        $pdf->Cell(0, 10, self::utfConvert($Escola->Cidade.", ".$Escola->UF." - ".date('d/m/Y H:i:s')), 0, 1, 'C'); // Texto de assinatura

        // Saída do PDF
        $pdf->Output('D', 'Declaracao_Frequencia.pdf');
        exit;

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

    public function historico($id){
        // Primeiro, obtenha todos os anos em que o aluno tem registros
        $anos = DB::table('aulas')
            ->join('frequencia', 'aulas.id', '=', 'frequencia.IDAula')
            ->join('alunos', 'alunos.id', '=', 'frequencia.IDAluno')
            ->where('alunos.id', $id)
            ->select(DB::raw('DISTINCT YEAR(aulas.created_at) as ano'))
            ->orderBy('ano')
            ->pluck('ano')
            ->toArray();

            //dd($anos);

        // Crie a parte dinâmica da consulta para as colunas de anos
        $selectCargas = "";
        $selectYears = '';
        foreach ($anos as $ano) {
            $selectYears .= "
            (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id ) as RecBim_{$ano},
            (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio = 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id ) as RecAn_{$ano},
            (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = $id AND rec2.IDDisciplina = d.id ) as PontRec_{$ano},
            MAX(CASE WHEN DATE_FORMAT(au.created_at, '%Y') = {$ano} THEN (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id ) END) as Total_{$ano}, 
            MAX(CASE WHEN DATE_FORMAT(au.created_at, '%Y') = {$ano} THEN 
                    (SELECT COUNT(f2.id) 
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE f2.IDAluno = a.id 
                     AND au2.IDDisciplina = d.id
                     AND DATE_FORMAT(au2.created_at, '%Y') = {$ano}) 
                END) as Frequencia_{$ano},
                MAX(CASE WHEN DATE_FORMAT(au.created_at, '%Y') = {$ano} THEN 
                    (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria)) 
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE f2.IDAluno = a.id 
                     AND au2.IDDisciplina = d.id
                     AND DATE_FORMAT(au2.created_at, '%Y') = {$ano}) 
                END) as CargaDisciplina_{$ano},
                MAX(CASE WHEN DATE_FORMAT(au.created_at, '%Y') = {$ano} THEN 
                    (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria))
                     FROM frequencia f2 
                     INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                     WHERE f2.IDAluno = a.id 
                     AND DATE_FORMAT(au2.created_at, '%Y') = {$ano}) 
                END) as CargaTotal_{$ano},
            ";

            $selectCargas .="
            MAX(CASE WHEN DATE_FORMAT(au.created_at, '%Y') = {$ano} THEN 
            (SELECT SEC_TO_TIME(SUM(f2.CargaHoraria))
                FROM frequencia f2 
                INNER JOIN aulas au2 ON(au2.id = f2.IDAula) 
                WHERE f2.IDAluno = a.id 
                AND DATE_FORMAT(au2.created_at, '%Y') = {$ano}) 
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
                        50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.id = au.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Faltas1B,
                        50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.id = au.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas2B,
                        50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.id = au.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas3B,
                        50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.id = au.id AND au2.IDDisciplina = d.id AND au2.Estagio="4º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas4B,
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
            "MediaPeriodo" => $Turma->MediaPeriodo
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
        INNER JOIN escolas e ON(e.id = ad.IDDisciplina)
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
        $registros = [];
        if(isset($_GET['Disciplina'])){
            $AND = " AND d.id=".$_GET['Disciplina']." AND a.Estagio='".$_GET['Estagio']."'";
            $idorg = Auth::user()->id_org;
            $SQL = "
                SELECT 
                    at.TPConteudo, 
                    a.Estagio,
                    at.created_at as Data,
                    d.NMDisciplina as Disciplina,
                    MAX(CASE WHEN att.IDAluno = n.IDAluno THEN n.Nota ELSE 0 END) as Nota -- Pega a maior nota para evitar duplicações
                FROM 
                    atividades at
                INNER JOIN 
                    aulas a ON(a.id = at.IDAula)
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
                $item[] = $r->Nota;
                $item[] = $r->Estagio;
                $item[] = self::data($r->Data,'d/m/Y');
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
                Aluno::find($request->IDAluno)->update(['IDTurma'=>$request->IDTurma]);
                Transferencia::find($request->IDTransferencia)->update(['Aprovado'=> 1]);
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
            tr.created_at as DTTransferencia
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
            'id' => '',
            'Turmas' => Turma::join('escolas', 'turmas.IDEscola', '=', 'escolas.id')
            ->select('turmas.id as IDTurma','turmas.Serie','turmas.Nome as Turma', 'escolas.Nome as Escola')->whereIn('turmas.IDEscola',EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional))
            ->get()
        ];

        if($id){
            $SQL = "SELECT 
                a.id as IDAluno, 
                m.id as IDMatricula,
                m.Nome as Nome,
                t.Nome as Turma,
                e.Nome as Escola,
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
                m.AnexoRG,
                re.RGPaisAnexo,
                m.CResidencia,
                m.Historico,
                re.RGPaisAnexo,
                cal.INIRematricula,
                cal.TERRematricula,
                r.ANO,
                m.PaisJSON,
                m.Quilombola
            FROM matriculas m
            INNER JOIN alunos a ON(a.IDMatricula = m.id)
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN renovacoes r ON(r.IDAluno = a.id)
            INNER JOIN escolas e ON(t.IDEscola = e.id)
            INNER JOIN organizacoes o ON(e.IDOrg = o.id)
            INNER JOIN responsavel re ON(re.IDAluno = a.id)
            INNER JOIN calendario cal ON(cal.IDOrg = e.IDOrg)
            WHERE o.id = $idorg AND a.id = $id  
            ";

            $Registro = DB::select($SQL)[0];
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
                        "Cor" => "pardo",
                        "CDPasta" => rand(0, 99999999999) // Adiciona o campo CDPasta
                    ]);
            
                    // Verifica se a matrícula foi criada
                    if ($matricula) {
                        // Criar aluno
                        $aluno = Aluno::create([
                            "IDMatricula" => $matricula->id,
                            "STAluno" => 0,
                            "IDTurma" => $IDTurma
                        ]);
            
                        // Verifica se o aluno foi criado
                        if ($aluno) {
                            // Criar responsável
                            Responsavel::create([
                                "IDAluno" => $aluno->id,
                                "CLResponsavel" => preg_replace('/\D/', '', $row['CLResponsavel']),
                                "NMResponsavel" => $row['NMResponsavel'],
                            ]);
            
                            // Criar renovações
                            Renovacoes::create([
                                "IDAluno" => $aluno->id,
                                "Aprovado" => 1,
                                "ANO" => date('Y')
                            ]);
                        }
                    }
                }
            }            

            return redirect()->back();
        }
    }

    public function save(Request $request){
        try{
            $CDPasta = rand(0,99999999999);
            //dd($request->file('RGPaisAnexo')->getClientOriginalName());
            //
            if(!$request->IDMatricula || !$request->IDAluno){

                if($request->file('CResidencia')){
                    $CResidencia = $request->file('CResidencia')->getClientOriginalName();
                    $request->file('CResidencia')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$CResidencia,'public');
                }else{
                    $CResidencia = '';
                }
    
                if($request->file('RGPaisAnexo')){
                    $RGPaisAnexo = $request->file('RGPaisAnexo')->getClientOriginalName();
                    $request->file('RGPaisAnexo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$RGPaisAnexo,'public');
                }else{
                    $RGPaisAnexo = '';
                }
    
                if($request->file('AnexoRG')){
                    $AnexoRG = $request->file('AnexoRG')->getClientOriginalName();
                    $request->file('AnexoRG')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$AnexoRG,'public');
                }else{
                    $AnexoRG = '';
                }
    
                if($request->file('Historico')){
                    $Historico = $request->file('Historico')->getClientOriginalName();
                    $request->file('Historico')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$Historico,'public');
                }else{
                    $Historico = '';
                }
    
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    $request->file('Foto')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$CDPasta,$Foto,'public');
                }else{
                    $Foto = '';
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
                    "EscolaridadePai" => $request->EscolaridadePai
                );
    
                $matricula = array(
                    'AnexoRG' => $AnexoRG,
                    'CResidencia' => $CResidencia,
                    'Historico' => $Historico,
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
                    "Sexo" => $request->Sexo
                );

                $matricula['PaisJSON'] = json_encode($Pais);

                $createMatricula = Matriculas::create($matricula);

                $aluno = array(
                    'IDMatricula' => $createMatricula->id,
                    'STAluno' => 0,
                    'IDTurma' => $request->IDTurma
                );

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
                    'RGPaisAnexo' => $RGPaisAnexo,
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
                
                if($request->file('CResidencia')){
                    $CResidencia = $request->file('CResidencia')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldCResidencia);
                    $request->file('CResidencia')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$CResidencia,'public');
                }else{
                    $CResidencia = '';
                }
    
                if($request->file('RGPaisAnexo')){
                    $RGPaisAnexo = $request->file('RGPaisAnexo')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldRGPaisAnexo);
                    $request->file('RGPaisAnexo')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$RGPaisAnexo,'public');
                }else{
                    $RGPaisAnexo = '';
                }

                //dd($RGPaisAnexo);
    
                if($request->file('AnexoRG')){
                    $AnexoRG = $request->file('AnexoRG')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldAnexoRG);
                    $request->file('AnexoRG')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$AnexoRG,'public');
                }else{
                    $AnexoRG = '';
                }
    
                if($request->file('Historico')){
                    $Historico = $request->file('Historico')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldHistorico);
                    $request->file('Historico')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Historico,'public');
                }else{
                    $Historico = '';
                }
    
                if($request->file('Foto')){
                    $Foto = $request->file('Foto')->getClientOriginalName();
                    Storage::disk('public')->delete('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'. $request->CDPasta . '/' . $request->oldFoto);
                    $request->file('Foto')->storeAs('organizacao_'.Auth::user()->id_org.'_alunos/aluno_'.$request->CDPasta,$Foto,'public');
                }else{
                    $Foto = '';
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
                    "EscolaridadePai" => $request->EscolaridadePai
                );
    
                $matricula = array(
                    'AnexoRG' => $AnexoRG,
                    'CResidencia' => $CResidencia,
                    'Historico' => $Historico,
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
                    "Sexo" => $request->Sexo
                );

                $matricula['PaisJSON'] = json_encode($Pais);

                if(empty($Historico)){
                    unset($matricula['Historico']);
                }

                if(empty($AnexoRG)){
                    unset($matricula['AnexoRG']);
                }

                if(empty($CResidencia)){
                    unset($matricula['CResidencia']);
                }

                if(empty($Foto)){
                    unset($matricula['Foto']);
                }

                if(empty($request->RG)){
                    unset($matricula['RG']);
                }

                if(empty($request->CPF)){
                    unset($matricula['CPF']);
                }

                if(empty($RGPaisAnexo)){
                    unset($matricula['RGPaisAnexo']);
                }

                if(is_null($request->Nome)){
                    unset($matricula['Nome']);    
                }

                if(is_null($request->Nascimento)){
                    unset($matricula['Nascimento']);    
                }

                //dd($matricula);

                Matriculas::find($request->IDMatricula)->update($matricula);

                $aluno = array(
                    'STAluno' => 0,
                    'IDTurma' => $request->IDTurma
                );

                $IDTurmaOrigem = Aluno::find($request->IDAluno)->IDTurma;
                $IDEscola = Turma::find($IDTurmaOrigem)->IDEscola;

                //dd($IDEscola);

                if($request->IDTurma != $IDTurmaOrigem){
                    Remanejo::create([
                        "IDTurmaOrigem" => $IDTurmaOrigem,
                        "IDTurmaDestino" => $request->IDTurma,
                        "IDAluno" => $request->IDAluno,
                        "IDEscola" => $IDEscola
                    ]);
                }

                Aluno::find($request->IDAluno)->update($aluno);

                $responsavel = array(
                    'RGPaisAnexo' => $RGPaisAnexo,
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
                'STAluno' => $request->STAluno
            ]);

            Aluno::where('id',$request->IDAluno)->update(['STAluno'=> $request->STAluno]);

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
            if($request->IDEscolaOrigem !=0){
                Transferencia::create($request->all());
            }else{
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
            tr.created_at as DTTransferencia,
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

        $AND = " AND e.id IN(".implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)).")";

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
                $item[] = Controller::data($r->Nascimento,'d/m/Y');
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
            at.created_at
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
                $item[] = Controller::data($r->created_at,'d/m/Y');
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