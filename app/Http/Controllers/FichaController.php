<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProfessoresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Ficha;
use App\Models\Resposta;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Models\Escola;

class FichaController extends Controller
{
    public const submodulos = array([
        'nome' => 'Formulários',
        'rota' => 'Fichas/index',
        'endereco' => 'index'
    ]);

    public const cadastroSubmodulos = array([
        'nome' => 'Formulários',
        'rota' => 'Fichas/index',
        'endereco' => 'index'
    ],[
        'nome' => 'Respostas',
        'rota' => 'Fichas/Respostas',
        'endereco' => 'Respostas'
    ]);
    public function index(){
        return view('Fichas.index',[
            'submodulos' => self::submodulos,
            'id' => ''
        ]);
    }

    public function cadastro($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos,
            'Escolas' => Escola::all()->whereIn('id',EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional))
        );

        if($id){
            $rsp = Ficha::find($id);
            $view['id'] = $id;
            $view['Registro'] = Ficha::find($id);
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['Formulario'] = json_decode($rsp);
        }

        return view('Fichas.cadastro', $view);
    }

    public function respostas($id){
        $registros = DB::select("SELECT r.Respostas as respostas, m.Nome as nome 
        FROM respostas_ficha r 
        INNER JOIN ficha_avaliativa f ON f.id = r.IDFicha 
        INNER JOIN alunos a ON r.IDAluno = a.id 
        INNER JOIN matriculas m ON m.id = a.IDMatricula 
        WHERE f.id = :id", ['id' => $id]);

        $IDAluno = Resposta::where('IDFicha', $id)->first()->IDAluno;
        $escola = DB::select("SELECT e.id, e.Nome as nome, e.Foto as foto 
            FROM escolas e 
            INNER JOIN turmas t ON t.IDEscola = e.id 
            INNER JOIN alunos a ON t.id = a.IDTurma 
            WHERE a.id = :IDAluno", ['IDAluno' => $IDAluno])[0];

        return view('Fichas.respostas',array(
            "submodulos" => self::cadastroSubmodulos,
            "registros" => $registros,
            "escola" => $escola,
            "id" => $id
        ));
    }

    public function gerarFichaIndividual($id){
        $Aluno = AlunosController::getAluno($id);
        $SQL = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Faltas1B,
                50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas2B,
                50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas3B,
                50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = $id AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="4º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas4B,
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
        $queryBoletim = DB::select($SQL);
        $Filiacao = json_decode($Aluno->PaisJSON);
        //
        $pdf = new FPDF(); //Cria o PDF
        $pdf->AddPage(); // Adiciona a página

        // Definir margens
        $pdf->SetMargins(5, 5, 5);
        //CABECALHO DO BOLETIM
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Aluno->IDEscola . '/' . $Aluno->FotoEscola), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);
        $Escola = Escola::find($Aluno->IDEscola);
        // Posição do nome da escola após a logo
        $pdf->SetXY(30, 15); // Ajuste o valor X conforme necessário para centralizar
        $pdf->Cell(0, 10, self::utfConvert($Aluno->Escola), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, self::utfConvert($Escola->Rua.", ".$Escola->Numero." ".$Escola->Bairro." - ".$Escola->Cidade."/".$Escola->UF), 0, 1, 'C');
        $pdf->Ln(20);
        //CABECALHO DO BOLETIM
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, "Ficha Individual", 0, 1, 'C'); // Nome da escola centralizado
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Ln(3);
        $pdf->Cell(66, 7, mb_convert_encoding("Escola: " . $Aluno->Escola, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(66, 7, mb_convert_encoding("Turma: " . $Aluno->Turma, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(66, 7, mb_convert_encoding("Ano Letivo: ". date('Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();
        //
        $pdf->Cell(66, 7, mb_convert_encoding("INEP: " . $Aluno->INEP, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(66, 7, mb_convert_encoding("Data de Nascimento: " . $Aluno->Nascimento, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(66, 7, mb_convert_encoding("Cor/Raça: ". $Aluno->Cor, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(66, 7, mb_convert_encoding("NIS: " . $Aluno->NIS, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(66, 7, mb_convert_encoding("Sexo: " . $Aluno->Sexo, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(66, 7, mb_convert_encoding("Estado Civil: Solteiro", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(198, 7, mb_convert_encoding("Aluno: " . $Aluno->Nome , 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(198, 7, mb_convert_encoding("Celular: " . $Aluno->Celular , 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(198, 7, mb_convert_encoding("Endereço: " . $Aluno->Rua.", ".$Aluno->Numero." ".$Aluno->Bairro." - ".$Aluno->Cidade." ".$Aluno->UF , 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(198, 7, mb_convert_encoding("Filiação: " . $Filiacao->Pai." e ".$Filiacao->Mae , 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();
        //ETAPAS
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 7, 'Etapas', 1, 0, 'C');
        $pdf->Cell(42, 7, mb_convert_encoding("1º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(42, 7, mb_convert_encoding("2º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(42, 7, mb_convert_encoding("3º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(42, 7, mb_convert_encoding("4º BIM", 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();
        //NOTAS E FALTAS
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 7, 'DISCIPLINAS', 1, 0, 'C');
        $pdf->Cell(21, 7, 'Nota', 1, 0, 'C');
        $pdf->Cell(21, 7, 'Falta', 1, 0, 'C');
        $pdf->Cell(21, 7, 'Nota', 1, 0, 'C');
        $pdf->Cell(21, 7, 'Falta', 1, 0, 'C');
        $pdf->Cell(21, 7, 'Nota', 1, 0, 'C');
        $pdf->Cell(21, 7, 'Falta', 1, 0, 'C');
        $pdf->Cell(21, 7, 'Nota', 1, 0, 'C');
        $pdf->Cell(21, 7, 'Falta', 1, 1, 'C');
        //CORPO DO BOLETIM
        foreach($queryBoletim as $d){
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(30, 7, mb_convert_encoding($d->Disciplina,'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(21, 7, $d->Nota1B, 1, 0, 'C');
            $pdf->Cell(21, 7, $d->Faltas1B, 1, 0, 'C');
            $pdf->Cell(21, 7, $d->Nota2B, 1, 0, 'C');
            $pdf->Cell(21, 7, $d->Faltas2B, 1, 0, 'C');
            $pdf->Cell(21, 7, $d->Nota3B, 1, 0, 'C');
            $pdf->Cell(21, 7, $d->Faltas3B, 1, 0, 'C');
            $pdf->Cell(21, 7, $d->Nota4B, 1, 0, 'C');
            $pdf->Cell(21, 7, $d->Faltas4B, 1, 1, 'C');
        }
        //RODAPÉ
        $pdf->Ln(1);
        $pdf->Cell(0, 10, 'Assinatura do Diretor(a): _______________________', 0, 1, 'L');
        $pdf->Cell(0, 10, self::utfConvert('Assinatura do Secretário(a): ____________________________'), 0, 1, 'L');
        $pdf->Cell(0, 10, self::utfConvert('Emissão: '.date('d/m/Y H:i',strtotime(date('Y-m-d H:i')))), 0, 1, 'L');
        $pdf->Ln(1);
        //IMPRESSÃO
        $pdf->Output('I', 'boletim.pdf');
        exit;
        //
    }

    public function exportRespostas($id)
    {
        // Consulta os registros
        $registros = DB::select("
        SELECT r.Respostas, r.id, m.Nome 
        FROM respostas_ficha r 
        INNER JOIN ficha_avaliativa f ON (f.id = r.IDFicha) 
        INNER JOIN alunos a ON (r.IDAluno = a.id)
        INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
        WHERE f.id = :id", ['id' => $id]);

        // Inicializa a planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define o cabeçalho
        $sheet->setCellValue('A1', 'Nome');
        $colIndex = 'B';

        if (count($registros) > 0) {
            $primeiroRegistro = json_decode($registros[0]->Respostas, true);
            foreach ($primeiroRegistro as $r) {
                $sheet->setCellValue($colIndex . '1', $r['Conteudo']);
                $colIndex++;
            }
        }

        // Preenche os dados
        $row = 2; // Começa na segunda linha (a primeira é o cabeçalho)
        foreach ($registros as $registro) {
            $item = [];
            $item[] = $registro->Nome;
            $respostas = json_decode($registro->Respostas, true);
            foreach ($respostas as $resposta) {
                $item[] = $resposta['Resposta'];
            }

            $colIndex = 'A';
            foreach ($item as $valor) {
                $sheet->setCellValue($colIndex . $row, $valor);
                $colIndex++;
            }
            $row++;
        }

        // Gera o arquivo Excel
        $fileName = 'relatorio_' . date('Y-m-d_H-i-s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Cria a resposta para download
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportarRespostasPDF($id)
    {
        // Consulta os dados dos registros
        $registros = DB::select("
            SELECT r.Respostas, r.id, m.Nome 
            FROM respostas_ficha r 
            INNER JOIN ficha_avaliativa f ON (f.id = r.IDFicha) 
            INNER JOIN alunos a ON (r.IDAluno = a.id)
            INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
            WHERE f.id = :id", ['id' => $id]);
    
        // Inicializa o PDF
        $pdf = new Fpdf();
        $pdf->SetMargins(20, 20, 20); // Margens de 20 em todos os lados
    
        // Obtém informações da escola
        $IDAluno = Resposta::where('IDFicha', $id)->first()->IDAluno;
        $Escola = DB::select("
            SELECT e.id, e.Cidade, e.UF, e.Foto, m.Nome as Aluno, e.Foto, e.Nome as Escola 
            FROM escolas e 
            INNER JOIN turmas t ON(t.IDEscola = e.id) 
            INNER JOIN alunos a ON(t.id = a.IDTurma ) 
            INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
            WHERE a.id = :IDAluno", ['IDAluno' => $IDAluno])[0];
    
        // Cabeçalho com a logo e nome da escola
        $pdf->AddPage();
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY(50, 15);
        $pdf->Cell(0, 10, self::utfConvert($Escola->Escola), 0, 1, 'C');
        $pdf->Ln(30);
    
        //$boletinsPorPagina = 0;
    
        foreach ($registros as $registro) {
            // Verifica se precisa de uma nova página a cada 2 boletins
            // if ($boletinsPorPagina % 2 == 0 && $boletinsPorPagina > 0) {
            //     $pdf->AddPage();
            // }
    
            // Nome do Aluno
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 10, 'Aluno: ' . self::utfConvert($registro->Nome), 1, 1, 'L');
            $pdf->Ln(5);
    
            // Cabeçalho da tabela
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(150, 8, 'Conteudo', 1, 0, 'C');  // Ajuste de largura para o conteúdo
            $pdf->Cell(20, 8, 'Resposta', 1, 1, 'C');   // Ajuste de largura para a resposta
    
            // Adiciona as respostas com alinhamento dinâmico
            $pdf->SetFont('Arial', '', 9);
            $respostas = json_decode($registro->Respostas, true);
    
            foreach ($respostas as $resposta) {
                $conteudo = self::utfConvert($resposta['Conteudo']);
                $respostaTexto = self::utfConvert($resposta['Resposta']);
                
                // Limite de caracteres por linha para a coluna de Conteúdo
                $lineWidthContent = 90; // Ajuste esse valor conforme necessário para a largura da célula
    
                // Quebra o conteúdo em várias linhas com `wordwrap`
                $wrappedContent = wordwrap($conteudo, $lineWidthContent, PHP_EOL);
    
                // Divide as linhas do conteúdo e calcula a altura total
                $lines = explode(PHP_EOL, $wrappedContent);
                $cellHeight = 8 * count($lines);
    
                // Verifica se a altura atual + altura da célula ultrapassa o limite da página
                if ($pdf->GetY() + $cellHeight > $pdf->GetPageHeight() - 20) {
                    $pdf->AddPage(); // Adiciona uma nova página se não houver espaço suficiente
                    $boletinsPorPagina = 0; // Reinicia a contagem de boletins na nova página
    
                    // Redesenha o cabeçalho e nome do aluno
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(0, 10, 'Aluno: ' . self::utfConvert($registro->Nome), 1, 1, 'L');
                    $pdf->Ln(5);
                    $pdf->Cell(150, 8, 'Conteudo', 1, 0, 'C');
                    $pdf->Cell(20, 8, 'Resposta', 1, 1, 'C');
                    $pdf->SetFont('Arial', '', 9);
                }
    
                // Exibe cada linha do conteúdo
                $yBefore = $pdf->GetY();
                foreach ($lines as $line) {
                    $pdf->Cell(150, 8, $line, 0, 2); // Imprime cada linha na mesma célula
                }
                // Borda ao redor de toda a célula de conteúdo
                $pdf->Rect(20, $yBefore, 150, $cellHeight);
    
                // Alinha a coluna "Resposta" ao lado do conteúdo
                $pdf->SetXY(170, $yBefore);
                $pdf->Cell(20, $cellHeight, $respostaTexto, 1, 1, 'C');
                
                // Define a posição Y para a próxima linha de conteúdo
                $pdf->SetY($yBefore + $cellHeight);
            }
    
            $pdf->Ln(10); // Espaço entre boletins
            
        }
    
        // Saída do PDF
        $pdf->Output();
        exit;
    }
    


    public function getRespostas($id){
        $registros = DB::select("
            SELECT r.Respostas, r.id, m.Nome 
            FROM respostas_ficha r 
            INNER JOIN ficha_avaliativa f ON (f.id = r.IDFicha) 
            INNER JOIN alunos a ON (r.IDAluno = a.id)
            INNER JOIN matriculas m ON(m.id = a.IDMatricula) 
            WHERE f.id = :id", ['id' => $id]);

            $itensJSON = [];

            if (count($registros) > 0) {
                foreach ($registros as $registro) {
                    $item = [];
                    // Adiciona o nome do usuário
                    $item[] = $registro->Nome;
                    
                    // Decodifica as respostas JSON para um array associativo
                    $respostas = json_decode($registro->Respostas, true);
                    
                    // Adiciona cada resposta ao array de itens
                    foreach ($respostas as $resposta) {
                        $item[] = $resposta['Resposta'];
                    }
                    
                    // Adiciona o item ao array de itens JSON
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

    public function visualizar($id){
        return view('Fichas.formulario',array(
           "Ficha" => json_decode(Ficha::find($id)->Formulario),
           'id' => $id,
           "Alunos" => EscolasController::getNomeAlunosEscola(EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)),
           'submodulos'=> self::submodulos
        ));
    }

    public function responder(Request $request){
        try{
            $respostas = $request->all();
            $Form = json_decode(Ficha::find($respostas['IDFicha'])->Formulario,true);
            
            $respondidas = [];
            unset($respostas['_token']);
            unset($respostas['IDFicha']);
            foreach($respostas as $rKey =>$rVal){
                $Form[$rKey]['Resposta'] = $rVal;
            }
            
            foreach($Form as $f){
                if(!empty($f['Conteudo'])){
                    array_push($respondidas,$f);
                }
            }
            
            $Rsp = array_map(function($ar){
                if(!is_null($ar)){
                    return $ar;
                }
            },$respondidas);
            
            Resposta::create(array(
                "Respostas" => json_encode($Rsp),
                "IDFicha" => $request->IDFicha,
                "IDAluno" => $request->IDAluno
            ));
            $rota = 'Fichas/Visualizar';
            $mensagem = "Ficha Respondida!";
            $aid = $request->IDFicha;
            $status = 'success';
        }catch(\Throwable $th){
            $mensagem = 'Erro '. $th->getMessage();
            $aid = $request->IDFicha;
            $rota = 'Fichas/Visualizar';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function save(Request $request){
        try{
            $data = $request->all();
            $arrayFormParsed = [];
            
            $arrayForm = array_map(function($a){
                if(!empty($a['Conteudo'])){
                    return $a;
                }
            },json_decode($data['Formulario'],true));
            
            foreach($arrayForm as $af){
                if(!is_null($af)){
                    array_push($arrayFormParsed,$af);
                }
            }
            
            $data['Ficha'] = json_encode($arrayFormParsed);
            
            if(!$request->id){
                //MailController::send($request->email,'Confirmação - Organizador','Mail.cadastroorganizador',array('Senha'=> $RandPW,'Email'=> $request->email));
                Ficha::create($data);
            }else{
                Ficha::find($request->id)->update($data);
            }
            $situacao['mensagem'] = "Salvo";
            $situacao['status'] = 'success';
        }catch(\Throwable $th){
            $situacao['mensagem'] = 'Erro '. $th->getMessage();
            $situacao['status'] = 'success';
        }finally{
            return json_encode($situacao);
        }
    }

    public function getFichas(){
        $IDEscolas = implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));

        $registros = DB::select("SELECT f.Titulo,e.Nome as Escola,f.id as IDFicha FROM ficha_avaliativa f INNER JOIN escolas e ON(e.id = f.IDEscola) AND e.id IN($IDEscolas) ");
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Titulo;
                $item[] = $r->Escola;
                $item[] = "
                <a class='btn btn-danger btn-xs' href=".route('Fichas/Respostas/Export/PDF',$r->IDFicha).">Exportar (PDF)</a>&nbsp
                <a class='btn btn-secondary btn-xs' href=".route('Fichas/Respostas',$r->IDFicha).">Respostas</a>&nbsp
                <a class='btn btn-success btn-xs' href=".route('Fichas/Edit',$r->IDFicha).">Editar Ficha</a>&nbsp
                <a class='btn btn-primary btn-xs' href=".route('Fichas/Visualizar',$r->IDFicha).">Avaliar Aluno</a>&nbsp
                ";
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
