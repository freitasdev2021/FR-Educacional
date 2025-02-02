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
use App\Models\Organizacao;
use App\Models\Aluno;
use App\Models\Sintese;
use App\Models\Matricula;
use App\Models\Turma;
use App\Models\Conceito;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Models\Escola;
use App\Models\FIndividual;

class FichaController extends Controller
{
    public const submodulos = array([
        'nome' => 'Ficha Evolutiva',
        'rota' => 'Fichas/index',
        'endereco' => 'index'
    ],[
        'nome' => 'Sínteses de Aprendizagem',
        'rota' => 'Fichas/Sinteses',
        'endereco' => 'Sinteses'
    ],[
        'nome' => 'Ficha Coletiva',
        'rota' => 'Fichas/Avaliativa',
        'endereco' => 'Avaliativa'
    ],[
        'nome' => 'Ficha Individual',
        'rota' => 'Fichas/Individual',
        'endereco' => 'Individual'
    ]);

    public const cadastroSubmodulos = array([
        'nome' => 'Fichas',
        'rota' => 'Fichas/index',
        'endereco' => 'index'
    ]);

    public function index(){
        $AND = " ";

        if(isset($_GET['Etapa']) && !empty($_GET['Etapa'])){
            $AND .=" AND c.Etapa='".$_GET['Etapa']."'";
        }

        if(isset($_GET['Turma']) && !empty($_GET['Turma'])){
            $AND .=" AND c.IDTurma='".$_GET['Turma']."'";
        }

        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            "AND"=> $AND
        ];

        if(Auth::user()->tipo == 6){
            $IDTurmas = ProfessoresController::getIdTurmasProfessor(Auth::user()->id,'sds');
            $view['Turmas'] = Turma::findMany($IDTurmas);
        }else{
            $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
            $view['Turmas'] = Turma::where('IDEscola',$IDEscolas)->get();
        }

        return view('Fichas.index',$view);
    }

    public function deleteSintese($id){
        Sintese::find($id)->delete();
        return redirect()->back();
    }

    public function avaliativa(){
        $AND = " ";
        $IDOrg = Auth::user()->id_org;
        if(isset($_GET['IDTurma']) && !empty($_GET['IDTurma'])){
            $AND .=" AND al.IDTurma='".$_GET['IDTurma']."'";
        }

        $SQLFichas = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                CONCAT(
                '[',
                GROUP_CONCAT(
                    DISTINCT
                    '{'
                    ,'"Referencia":"', sa.Referencia, '"'
                    ,',"Sintese":"', sa.Sintese, '"'
                    ,'}'
                    SEPARATOR ','
                    ),
                ']'
                    ) AS Sinteses
            FROM sintese_aprendizagem as sa 
            INNER JOIN disciplinas d ON(d.id = sa.IDDisciplina)
            INNER JOIN alocacoes_disciplinas ad ON(ad.IDDisciplina = d.id)
            INNER JOIN escolas es ON(es.id = ad.IDEscola)
            WHERE es.IDOrg = $IDOrg GROUP BY d.id
        SQL;

        $SQLAlunos = <<<SQL
            SELECT 
                m.Nome as Aluno
            FROM matriculas m
            INNER JOIN alunos al ON(al.IDMatricula = m.id)
            WHERE STAluno = 0 $AND ORDER BY m.Nome
        SQL;

        if((isset($_GET['IDTurma']) && !empty($_GET['IDTurma']))){
            $Alunos = DB::select($SQLAlunos);
        }else{
            $Alunos = [];
        }

        

        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            'Fichas' => DB::select($SQLFichas),
            'Alunos' => $Alunos
        ];

        if(Auth::user()->tipo == 6){
            $IDTurmas = ProfessoresController::getIdTurmasProfessor(Auth::user()->id,'sds');
            $view['Turmas'] = Turma::findMany($IDTurmas);
        }else{
            $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
            $view['Turmas'] = Turma::where('IDEscola',$IDEscolas)->get();
        }

        return view('Fichas.Avaliar.index',$view);
    }

    public function saveAvaliativa(Request $request){
        $Sintese = json_decode($request->EnviarSintese,true);
        //dd($Sintese);
        $Fichas = json_decode($request->Fichas,true);
        //dd($Fichas);
        $IDTurma = $request->IDTurma;
        $Escola = EscolasController::getEscolaTurma($IDTurma);
        $lineHeight = 4;
        $lineHeightH = 4;
        $fontHeader = 10;
        $fontBody = 5;
        //PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetMargins(3, 3, 3); 
        self::criarCabecalho($pdf,$Escola->Nome,$Escola->Organizacao,'storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto,"FICHA AVALIATIVA INFANTIL",[
            "Rua" => $Escola->Rua,
            "Numero" => $Escola->Numero,
            "Bairro" => $Escola->Bairro,
            "Cidade" => $Escola->Bairro,
            "UF" => $Escola->UF
        ],
        $Escola->Email,
        $Escola->Telefone);
        //CORPO DO DOCUMENTO
        $pdf->SetFont('Arial', 'B', $fontHeader);
        //LEGENDA DAS FICHAS

        $xInicial = 10; // Posição inicial no eixo X
        $yInicial = $pdf->GetY(); // Posição inicial no eixo Y
        $containerLargura = 50; // Largura de cada container
        $espacamento = 10; // Espaço entre os containers
        $alturaMaximaContainer = 45; // Altura máxima antes de quebrar para a próxima linha

        foreach ($Fichas as $keyfic => $fic) {
            // Verifica se o próximo container ultrapassa a largura da página
            if ($xInicial + $containerLargura > $pdf->GetPageWidth() - 10) {
                $xInicial = 10; // Volta para o início
                $yInicial += $alturaMaximaContainer; // Avança para a próxima linha
            }

            // Define a posição inicial do container
            $pdf->SetXY($xInicial, $yInicial);

            // Título do container (Disciplina)
            $pdf->SetFont('Arial', 'B', $fontBody);
            $pdf->MultiCell($containerLargura, $lineHeightH, self::utfConvert(1+$keyfic.". ".$fic['Disciplina']), 0, '');

            // Listinha de texto com as sínteses
            $pdf->SetFont('Arial', '', $fontBody);
            foreach (json_decode($fic['Sinteses']) as $si) {
                $pdf->SetX($xInicial); // Mantém o alinhamento no container
                $texto = "- " . self::utfConvert($si->Referencia) . ": " . self::utfConvert($si->Sintese);
                $pdf->MultiCell($containerLargura, $lineHeightH, $texto, 0, 'L');
            }

            // Ajusta a posição para o próximo container
            $xInicial += $containerLargura + $espacamento;
        }

        $pdf->Ln();        
        //FIM DA LEGENDA DAS FICHAS
        $pdf->Cell(50, $lineHeight, self::utfConvert('Aluno'), 1);
        foreach($Fichas as $keyF => $f){
            $countSinteses = count(json_decode($f['Sinteses'],true));
            $pdf->Cell(7*$countSinteses, $lineHeight, $keyF+1, 1);
        }
        $pdf->Ln();
        //CORPO DA TABELA
        $pdf->SetFont('Arial', '', $fontBody);
        foreach($Sintese as $s){
            $pdf->Cell(50, $lineHeight, self::utfConvert($s['Aluno']), 1);
            //dd($s['Disciplinas']);
            foreach($s['Disciplinas'] as $disc){
                foreach($disc as $dis){
                    foreach($dis as $keyD => $d){
                        $pdf->Cell(7, $lineHeight, $keyD." - ".$d, 1);
                    }
                }
            }
            $pdf->Ln();
        }
        //FIM DO CORPO DA TABELA
        $pdf->Ln();
        $pdf->Cell(0, $lineHeight, self::utfConvert('LEGENDA: S - SIM, N - NÃO, AV - AS VEZES'), 0, 1);
        //FIM DO CORPO DO DOCUMENTO
        $pdf->Output('I',"Ficha Infantil FR Educacional".'.pdf');
        exit;
    }

    public function sinteses(){
        $AND = " ";

        if(isset($_GET['IDDisciplina']) && !empty($_GET['IDDisciplina'])){
            $AND .=" AND d.id='".$_GET['IDDisciplina']."'";
        }

        $view = [
            'submodulos' => self::submodulos,
            'id' => '',
            "AND"=> $AND
        ];

        if(Auth::user()->tipo == 6){
            $view['Disciplinas'] = EscolasController::getDisciplinasProfessor(Auth::user()->id);
        }else{
            $view['Disciplinas'] = EscolasController::getDisciplinasEscola();
        }

        return view('Fichas.Sinteses.index',$view);
    }

    public function getSelectAlunosFicha(Request $request){
        $IDTurma = $request->IDTurma;
        $SQL = <<<SQL
            SELECT 
                m.Nome AS Aluno,
                m.id AS IDAluno
            FROM alunos a
            INNER JOIN matriculas m ON m.id = a.IDMatricula
            INNER JOIN turmas t ON a.IDTurma = t.id
            WHERE t.id = '$IDTurma'
            GROUP BY m.Nome, m.id
        SQL;

        $alunos = DB::select($SQL);

        ob_start();
        foreach($alunos as $a){
        ?>
            <tr>
                <td><?=$a->Aluno?></td>
                <td>
                    <input type="hidden" value="<?=$a->IDAluno?>" name="Aluno[]">
                    <input type="text" name="Conceito[]">
                </td>
            </tr>
        <?php
        }
        return ob_get_clean();
    }

    public function cadastro($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos,
            'Escolas' => Escola::findMany(EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional))
        );

        if(Auth::user()->tipo == 6){
            $IDTurmas = ProfessoresController::getIdTurmasProfessor(Auth::user()->id,'sds');
            $view['Turmas'] = Turma::findMany($IDTurmas);
        }else{
            $IDEscolas = EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional);
            $view['Turmas'] = Turma::where('IDEscola',$IDEscolas)->get();
        }
        

        if($id){
            $Conceito = Conceito::find($id);
            $view['id'] = $id;
            $view['Registro'] = $Conceito;
            $view['submodulos'] = self::cadastroSubmodulos;
            $view['Conceitos'] = json_decode($Conceito->ConceitosJSON);
        }

        return view('Fichas.cadastro', $view);
    }

    public function cadastroSinteses($id = null){
        $view = array(
            'id' => '',
            'submodulos' => self::submodulos
        );

        if(Auth::user()->tipo == 6){
            $view['Disciplinas'] = EscolasController::getDisciplinasProfessor(Auth::user()->id);
        }else{
            $view['Disciplinas'] = EscolasController::getDisciplinasEscola();
        }
        

        if($id){
            $Conceito = Conceito::find($id);
            $view['id'] = $id;
            $view['Registro'] = Sintese::find($id);
        }

        return view('Fichas.Sinteses.cadastro', $view);
    }

    public static function getFichaAluno($IDAluno){
        $SQL = "SELECT r.Respostas as respostas, m.Nome as nome 
        FROM respostas_ficha r 
        INNER JOIN ficha_avaliativa f ON f.id = r.IDFicha 
        INNER JOIN alunos a ON r.IDAluno = a.id 
        INNER JOIN matriculas m ON m.id = a.IDMatricula 
        WHERE a.id=".$IDAluno;

        $registros = DB::select($SQL);

        return $registros;
    }

    public function gerarFichaIndividual(Request $request,$IDAluno){
        $FIndividual = json_decode($request->Ficha,true);
        //dd($FIndividual);
        $Aluno = AlunosController::getAluno($IDAluno);
        $Escola = Escola::find($Aluno->IDEscola);
        $Organizacao = Organizacao::find($Escola->IDOrg);
        if(FIndividual::where('IDAluno',$IDAluno)->exists()){
            FIndividual::where(["IDAluno"=> $IDAluno])->update([
                "Avaliacao" => $request->Ficha
            ]);
        }else{
            FIndividual::create([
                "Avaliacao"=> $request->Ficha,
                "IDAluno"=>$IDAluno
            ]);
        }

        $lineHeight = 6;
        $Ano = date('Y');
        $pdf = new FPDF();
        $pdf->AddPage(); 
        //
        self::criarCabecalho($pdf,$Escola->Nome,$Organizacao->Organizacao,'storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Aluno->IDEscola . '/' . $Escola->Foto,"FICHA DE ACOMPANHAMENTO E AVALIAÇÃO DA APRENDIZAGEM",[
            "Rua" => $Escola->Rua,
            "Numero" => $Escola->Numero,
            "Bairro" => $Escola->Bairro,
            "Cidade" => $Escola->Bairro,
            "UF" => $Escola->UF
        ],
        $Escola->Email,
        $Escola->Telefone);
        $pdf->SetFont('Arial', '', 8);
        //
        $pdf->Cell(65, $lineHeight, self::utfConvert('Nome: ' . $Aluno->Nome), 0, 0);
        $pdf->Cell(65, $lineHeight, self::utfConvert('Data de Nascimento: ' . date('d/m/Y',strtotime($Aluno->Nascimento))), 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('INEP: ' . $Aluno->INEP), 0, 1);

        $pdf->Cell(65, $lineHeight, self::utfConvert('Turma e Série: '.$Aluno->Serie.' '. $Aluno->Turma), 0, 0);
        $pdf->Cell(65, $lineHeight, self::utfConvert('Turno: ' . $Aluno->Turno), 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('Período Letivo: ' . $Ano), 0, 1);

        $pdf->Cell(100, $lineHeight, self::utfConvert('Situação do Aluno: Aprovado'), 0, 0);
        $pdf->Cell(0, $lineHeight, self::utfConvert('Legenda: S: SIM, N: NÃO, AV: as Vezes'), 0, 1);
        //
        //dd($FIndividual);
        foreach ($FIndividual as $key => $fi) {
            // Título da seção
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, $lineHeight, self::utfConvert($key), 0, 1);
            $pdf->Ln(2); // Pequeno espaçamento
        
            // Indicadores e Bimestres
            $pdf->SetFont('Arial', '', 9);
            foreach ($fi as $keyFi => $f) {
                // Exibe o texto do indicador
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->MultiCell(0, $lineHeight, self::utfConvert($keyFi)); // Ocupa toda a linha
                $pdf->Ln(1); // Pequeno espaçamento entre o texto e os bimestres
        
                // Exibe os bimestres lado a lado
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(15, $lineHeight, '1BIM:', 0, 0);
                $pdf->Cell(20, $lineHeight, self::utfConvert($f['1 BIM']), 0, 0);
                $pdf->Cell(15, $lineHeight, '2BIM:', 0, 0);
                $pdf->Cell(20, $lineHeight, self::utfConvert($f['2 BIM']), 0, 0);
                $pdf->Cell(15, $lineHeight, '3BIM:', 0, 0);
                $pdf->Cell(20, $lineHeight, self::utfConvert($f['3 BIM']), 0, 0);
                $pdf->Cell(15, $lineHeight, '4BIM:', 0, 0);
                $pdf->Cell(20, $lineHeight, self::utfConvert($f['4 BIM']), 0, 1); // Nova linha após os bimestres
        
                $pdf->Ln(3); // Espaçamento entre os indicadores
            }
        
            $pdf->Ln(4); // Espaço entre seções
        }            
        ////////
        //CAMPOS DE ASSINATURA
        $pdf->Ln();
        $pdf->MultiCell(160, $lineHeight, self::utfConvert('Parecer Descritivo: '.$request->Parecer), 0, 0);
        $pdf->Cell(100, $lineHeight, self::utfConvert('Observaçoes: '.$request->Observacoes), 0, 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, self::utfConvert($Escola->Cidade." - ".$Escola->UF.", ".date('d/m/Y')), 0, 1, 'C');
        $pdf->Ln(10);
        $larguraTotal = 160; // Largura total disponível para os campos de assinatura
        $espacoEntreCampos = 30; // Espaço entre os campos
        $campoLargura = ($larguraTotal - (2 * $espacoEntreCampos)) / 3; // Calcula a largura de cada campo

        // Campo de assinatura 1
        $pdf->SetX((210 - $larguraTotal) / 2); // Centraliza os campos horizontalmente
        $pdf->Cell($campoLargura, 10, '', 'T', 0, 'C'); // Linha para assinatura
        $pdf->Cell($espacoEntreCampos, 10, '', 0, 0); // Espaço entre os campos

        // Campo de assinatura 2
        $pdf->Cell($campoLargura, 10, '', 'T', 0, 'C'); // Linha para assinatura
        $pdf->Cell($espacoEntreCampos, 10, '', 0, 0); // Espaço entre os campos

        // Campo de assinatura 3
        $pdf->Cell($campoLargura, 5, '', 'T', 1, 'C'); // Linha para assinatura

        // Nome dos responsáveis abaixo das linhas de assinatura
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX((210 - $larguraTotal) / 2); // Centraliza os textos horizontalmente
        $pdf->Cell($campoLargura, 5, self::utfConvert('Diretor(a)'), 0, 0, 'C');
        $pdf->Cell($espacoEntreCampos, 5, '', 0, 0); // Espaço
        $pdf->Cell($campoLargura, 5, self::utfConvert('Coordenador(a)'), 0, 0, 'C');
        $pdf->Cell($espacoEntreCampos, 5, '', 0, 0); // Espaço
        $pdf->Cell($campoLargura, 5, self::utfConvert('Professor(a)'), 0, 1, 'C');

        $pdf->Output('I', 'Ficha Individual.pdf');
        exit;
    }

    public function individual(){
        if(isset($_GET['IDAluno']) && isset($_GET['IDDisciplina']) && isset($_GET['Etapa'])){
            $Ficha = json_decode(FIndividual::select('Avaliacao')->where('IDAluno',$_GET['IDAluno'])->where('IDDisciplina',$_GET['IDDisciplina'])->where('Etapa',$_GET['Etapa'])->first(),true);
        }else{
            $Ficha = [];
        }
        
        $view = [
            "Alunos" => EscolasController::getNomeAlunosEscola(EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)),
            "submodulos" => self::submodulos,
            'id' => '',
            'Ficha' => $Ficha
        ];

        if(Auth::user()->tipo == 6){
            $view['Disciplinas'] = EscolasController::getDisciplinasProfessor(Auth::user()->id);
        }else{
            $view['Disciplinas'] = EscolasController::getDisciplinasEscola();
        }

        return view('Fichas.Individual.index',$view);
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

    public function exportarRespostasPDF($IDTurma,$Etapa){
        $Fichas = Conceito::select('ConceitosJSON','NMConceito')->where('IDTurma',$IDTurma)->where('Etapa',$Etapa)->get();
        $Turma = Turma::find($IDTurma);
        $Escola = Escola::find($Turma->IDEscola);
        $pdf = new FPDF(); //Cria o PDF
        $pdf->AddPage(); // Adiciona a página
        //CABECALHO DO BOLETIM
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Turma->IDEscola . '/' . $Escola->Foto), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);
        // Posição do nome da escola após a logo
        $pdf->SetXY(30, 15); // Ajuste o valor X conforme necessário para centralizar
        $pdf->Cell(0, 10, self::utfConvert($Escola->Nome), 0, 1, 'C'); // Nome da escola centralizado
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, self::utfConvert($Escola->Rua.", ".$Escola->Numero." ".$Escola->Bairro." - ".$Escola->Cidade."/".$Escola->UF), 0, 1, 'C');
        $pdf->Ln(20);
        //CABECALHO DO BOLETIM
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, "Ficha Individual", 0, 1, 'C'); // Nome da escola centralizado
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Ln(3);
        //
        foreach($Fichas->toArray() as $ta){
             //
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(190, 7, $ta['NMConceito'], 1, 0, 'C');
            $pdf->Ln();
            $pdf->Cell(100, 7, 'Aluno', 1, 0, 'C');
            $pdf->Cell(90, 7, 'Conceito', 1, 0, 'C');
            $pdf->SetFont('Arial', '', 8);
            //
            foreach(json_decode($ta['ConceitosJSON']) as $c){
                $pdf->Ln();
                $pdf->Cell(100, 7, self::utfConvert($c->Aluno), 1, 0, 'C');
                $pdf->Cell(90, 7, self::utfConvert($c->Conceito), 1, 0, 'C');
            }
            $pdf->Ln(15);
            //
            // echo "<pre>";
            // print_r($ta['NMConceito']);
            // print_r(json_decode($ta['ConceitosJSON']));
            // echo "</pre>";
        }
        //IMPRESSÃO
        $pdf->Output('I', 'ficha.pdf');
        exit;
        //
    }

    public function visualizar($id){
        $IDAlunos = implode(",",EscolasController::getAlunosEscola(EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)));
        return view('Fichas.formulario',array(
           "Ficha" => json_decode(Ficha::find($id)->Formulario),
           'id' => $id,
           "Alunos" => (Auth::user()->tipo == 6) ? ProfessoresController::getAlunosProfessor(Auth::user()->IDProfissional) : DB::select("SELECT m.Nome as Aluno,a.id, t.Nome as Turma,t.Serie,e.Nome as Escola FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN escolas e ON(e.id = t.IDEscola) WHERE a.id IN($IDAlunos)") ,
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

    public function saveSinteses(Request $request){
        try{
            $data = $request->all();

            if($request->id){
                Sintese::find($request->id)->update($data);
                $rota = 'Fichas/Sinteses/Edit';
                $aid = $request->id;
            }else{
                Sintese::create($data);
                $aid = '';
                $rota = 'Fichas/Sinteses/Novo';
            }
            $mensagem = "Salvamento Realizado com Sucesso!";
            $status = 'success';
        }catch(\Throwable $th){
            $rota = 'Fichas/Sinteses/Novo';
            $mensagem = 'Erro '.$th;
            $aid = '';
            $status = 'error';
        }finally{
            return redirect()->route($rota,$aid)->with($status,$mensagem);
        }
    }

    public function save(Request $request){
        try{
            $Conceitos = [];
            $Aluno = [];
            foreach($request->Conceito as $ct){
                array_push($Conceitos,$ct);
            }
            //
            foreach($request->Aluno as $al){
                array_push($Aluno,$al);
            }
            //
            for($i=0;$i<count($Conceitos);$i++){
                $IDAluno = $Aluno[$i];
                $Al = DB::select("SELECT m.Nome FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) WHERE a.id = $IDAluno")[0];
                $Conceituados[] = [
                    "IDAluno" => $IDAluno,
                    "Aluno" => $Al->Nome,
                    "Conceito" => $Conceitos[$i],
                ];
            }

            $JSONConceitos = json_encode($Conceituados);
            if($request->id){
                Conceito::find($request->id)->update([  
                    "ConceitosJSON"=> $JSONConceitos,
                    "IDTurma"=> $request->IDTurma,
                    "Etapa" => $request->Etapa,
                    "NMConceito"=>$request->NMConceito
                ]);

                $rout = "Fichas/Edit";
                $aid = $request->id;
            }else{
                Conceito::create([  
                    "ConceitosJSON"=> $JSONConceitos,
                    "IDTurma"=> $request->IDTurma,
                    "Etapa" => $request->Etapa,
                    "NMConceito"=>$request->NMConceito
                ]);

                $rout = 'Fichas/Novo';
                $aid = "";
            }
            
            $status = 'success';
            $mensagem = 'Conceito Lançado com Sucesso';
        }catch(\Throwable $th){
            $status = 'error';
            $mensagem = 'Erro ao lançar Conceitos:'. $th->getMessage();
            $aid = '';
            $rout = "Fichas/index";
        }finally{
            //dd($Conceituados);
            return redirect()->route($rout,$aid)->with($status,$mensagem);
        }
    }

    public function getFichas($AND){
        if(Auth::user()->tipo == 6){
            $ID = implode(",",ProfessoresController::getIdTurmasProfessor(Auth::user()->id,'sds'));
        }else{
            $ID = implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));
        }

        //dd($AND);

        $registros = DB::select("SELECT c.id as IDFicha,c.NMConceito,c.Etapa,t.Nome as Turma,t.Serie FROM conceitos c INNER JOIN turmas t ON(c.IDTurma = t.id) WHERE c.IDTurma IN($ID) $AND ");
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->NMConceito;
                $item[] = $r->Turma ." - ".$r->Serie;
                $item[] = $r->Etapa;
                $item[] = "
                <a class='btn btn-success btn-xs' href=".route('Fichas/Edit',$r->IDFicha).">Abrir</a>&nbsp
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

    public function getSinteses($AND){
        $IDOrg = Auth::user()->id_org;
        if(Auth::user()->tipo == 6){
            $ID = implode(",",ProfessoresController::getIdTurmasProfessor(Auth::user()->id,'sds'));
        }else{
            $ID = implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));
        }

        $SQL = <<<SQL
            SELECT 
                sa.Referencia,
                d.NMDisciplina as Disciplina,
                sa.id,
                sa.Sintese 
            FROM sintese_aprendizagem as sa 
            INNER JOIN disciplinas d ON(d.id = sa.IDDisciplina)
            INNER JOIN alocacoes_disciplinas ad ON(ad.IDDisciplina = d.id)
            INNER JOIN escolas es ON(es.id = ad.IDEscola)
            WHERE es.IDOrg = $IDOrg $AND GROUP BY sa.id
        SQL;

        $registros = DB::select($SQL);
        
        if(count($registros) > 0){
            foreach($registros as $r){
                $item = [];
                $item[] = $r->Referencia;
                $item[] = $r->Sintese;
                $item[] = $r->Disciplina;
                $item[] = "<div class='row'>
                <a class='btn btn-success btn-xs' href=".route('Fichas/Sinteses/Edit',$r->id).">Editar</a>&nbsp
                <a class='btn btn-danger btn-xs' href=".route('Fichas/Sinteses/Delete',$r->id).">Excluir</a>
                </div>
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
