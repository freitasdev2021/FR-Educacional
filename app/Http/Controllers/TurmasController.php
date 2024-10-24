<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EscolasController;
use App\Http\Controllers\SecretariasController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Escola;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;

class TurmasController extends Controller
{

    public const professoresSubmodulos = array([
        "nome" => "Cadastro",
        "endereco" => "index",
        "rota" => "Turmas/index"
    ]);

    public const professoresCadastroSubmodulos = array([
        "nome" => 'Desempenho',
        "endereco" => "Desempenho",
        "rota" => "Turmas/Desempenho"
    ]);

    public function index(){
        $view = [
            "submodulos" => self::professoresSubmodulos,
            'id' => ''
        ];
        if(Auth::user()->tipo == 4){
            $view['Registro'] = Escola::where('id',self::getEscolaDiretor(Auth::user()->id))->first();
        }
        return view('Escolas.turmas',$view);
    }

    public function exportaAlunosTurma($id){
        $idorg = Auth::user()->id_org;
        $Alunos = "SELECT
            a.id as IDAluno, 
            m.Nome as Nome,
            t.Nome as Turma,
            e.Nome as Escola,
            t.Serie as Serie,
            m.Nascimento as Nascimento,
            a.STAluno,
            m.Foto,
            m.Email,
            m.CPF,
            resp.NMResponsavel,
            r.ANO,
            resp.CLResponsavel,
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
        INNER JOIN responsavel resp ON(a.id = resp.IDAluno)
        WHERE o.id = $idorg AND t.id = $id GROUP BY a.id ORDER BY m.Nome ASC 
        ";
        $Turma = Turma::find($id);
        $Escola = Escola::find($Turma->IDEscola);
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Definir margens
        $pdf->SetMargins(20, 20, 20); // Margens esquerda, superior e direita
        $pdf->SetAutoPageBreak(true, 25); // Margem inferior

        // Inserir a logo da escola (ajuste o caminho e dimensões da imagem conforme necessário)
        $pdf->Image(public_path('storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Escola->id . '/' . $Escola->Foto), 10, 10, 30); // Caminho da logo, posição X, Y e tamanho
        // Definir fonte e título
        $pdf->SetFont('Arial', 'B', 16);

        // Posição do nome da escola após a logo
        $pdf->SetXY(30, 15); // Ajuste o valor X conforme necessário para centralizar
        $pdf->Cell(0, 10, self::utfConvert($Escola->Nome), 0, 1, 'C'); // Nome da escola centralizado
        
        // Definir cabeçalho do relatório
        $pdf->SetFont('Arial', 'B', 16);
        
        // Largura total da tabela (soma das larguras das colunas)
        $larguraTotalTabela = 161; 
        
        // Centralizar o cabeçalho com base na largura da tabela
        $pdf->SetX((220 - $larguraTotalTabela) / 2); // 210 é a largura da página A4 em mm
        $pdf->Cell($larguraTotalTabela, 10, self::utfConvert("Turma ".$Turma->Serie)." ".$Turma->Nome, 0, 1, 'C');
        $pdf->Ln(10); // Espaço após o título
        
        // Definir cabeçalhos da tabela
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(10, 10, self::utfConvert('N°'), 1);
        $pdf->Cell(100, 10, 'Nome', 1);
        $pdf->Cell(30, 10, 'Nascimento', 1);
        $pdf->Cell(30, 10, 'CPF', 1);
        $pdf->Ln(); // Pular linha após cabeçalho
        
        // Definir fonte para o corpo do relatório
        $pdf->SetFont('Arial', '', 12);
        
        // Loop através dos alunos para exibir os dados em células
        foreach (DB::select($Alunos) as $k=> $a) {
            $pdf->Cell(10,10,$k+1,1);
            $pdf->Cell(100, 10, self::utfConvert($a->Nome), 1);
            $pdf->Cell(30, 10, date('d/m/Y', strtotime($a->Nascimento)), 1);
            $pdf->Cell(30, 10, $a->CPF, 1);
            $pdf->Ln(); // Pular linha após cada aluno
        }

        // Gera o PDF para saída
        $pdf->Output('I',$Turma->Nome.'.pdf');
        exit;

    }

    public function desempenho($IDTurma){
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
        $view = [
            'submodulos' => self::professoresSubmodulos,
            'id' => $IDTurma,
            'Disciplinas' => ProfessoresController::getDisciplinasProfessor(Auth::user()->IDProfissional),
            'Estagios' => $Estagios
        ];
        if($IDTurma){
            $view['submodulos'] = self::professoresCadastroSubmodulos;
        }
        return view('Escolas.desempenhoTurma',$view);
    }

    public function getDesempenho($IDTurma){
        $NOW = date('Y');
        if(isset($_GET['Disciplina']) && !empty($_GET['Disciplina']) && isset($_GET['Estagio']) && !empty($_GET['Estagio'])){
            $Disciplina = $_GET['Disciplina'];
            $Estagio = $_GET['Estagio'];
            $SQL = <<<SQL
            SELECT 
                m.Nome as Aluno,         -- Nome do Aluno
                a.id as IDAluno,         -- ID do Aluno
                t.Periodo,               -- Período do Aluno
                d.NMDisciplina as Disciplina, -- Nome da Disciplina
                au.Estagio,              -- Estágio (Bimestre)
                
                -- Soma das Notas do Aluno para a Disciplina e Estágio específicos
                (SELECT SUM(n2.Nota) 
                FROM notas n2 
                INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                WHERE au3.IDDisciplina = d.id 
                AND n2.IDAluno = a.id 
                AND au3.Estagio = '$Estagio'
                ) as Total,

                -- Frequência (quantidade de presenças) do Aluno para a Disciplina e Estágio específicos
                (SELECT COUNT(f2.id) 
                FROM frequencia f2 
                INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                WHERE f2.IDAluno = a.id 
                AND au2.IDDisciplina = d.id 
                AND au2.Estagio = '$Estagio'
                ) as Frequencia,
                CASE WHEN (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = $Disciplina AND n2.IDAluno = a.id ) < t.MediaPeriodo THEN 'Reprovado' ELSE 'Aprovado' END as Resultado
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
                a.IDTurma = $IDTurma                          -- Filtro para turma específica
                AND au.IDDisciplina = $Disciplina                -- Filtro para disciplina específica
                AND au.Estagio = '$Estagio'              -- Filtro para estágio (4º Bimestre)
                AND DATE_FORMAT(au.created_at, '%Y') = $NOW -- Filtro para o ano de 2024

            GROUP BY 
                m.Nome, a.id, t.Periodo, d.NMDisciplina, au.Estagio;
            SQL;
            $resultados = DB::select($SQL);
        }else{
            $resultados = [];
        }
        
        if(count($resultados) > 0){
            foreach($resultados as $r){

                switch($r->Periodo){
                    case 'Bimestral':
                        $Estagios = 200/4;
                    break;
                    case 'Trimestral':
                        $Estagios = 200/3;
                    break;
                    case 'Semestral':
                        $Estagios = 200/2;
                    break;
                    case 'Anual':
                        $Estagios = 200/1;
                    break;
                }

                $rota = route('Alunos/Recuperacao', ["IDAluno" => $r->IDAluno, "Estagio" => $r->Estagio]);

                $item = [];
                $item[] = $r->Aluno;
                $item[] = $r->Total;
                $item[] = $r->Estagio;
                $item[] = ($r->Frequencia / $Estagios) * 100 . " %"; // Corrigido o uso de concatenação
                $item[] = $r->Disciplina;
                $item[] = ($r->Resultado == 'Reprovado') 
                    ? "<strong class='text-danger'>Recuperação</strong> <a href='{$rota}'>Zerar Conceito</a>" 
                    : "<strong class='text-success'>Aprovado</strong>";
                $itensJSON[] = $item;

            }
        }else{
            $itensJSON = [];
        }
        
        $resultados = [
            "recordsTotal" => intval(count($resultados)),
            "recordsFiltered" => intval(count($resultados)),
            "data" => $itensJSON 
        ];
        
        echo json_encode($resultados);
    }

    public function getDisciplinasTurma($IDTurma){
        $disciplinas = [];
        $SQL = <<<SQL
            SELECT
                d.NMDisciplina as Disciplina
            FROM turnos tn
            INNER JOIN turmas t ON(tn.IDTurma = t.id)
            INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
            INNER JOIN escolas e ON(al.IDEscola = e.id)
            INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
            WHERE tn.IDTurma = $IDTurma GROUP BY d.id
        SQL;

        foreach(DB::select($SQL) as $s){
            array_push($disciplinas,$s->Disciplina);
        }
        return $disciplinas;
    }

    public function getAta($IDTurma){
        /////////////////////
        // Exemplo de dados da consulta SQL que serão usados para gerar o boletim 
        
        $ata = array();

        // Obter dados dos alunos e suas notas
        foreach (self::getAlunosByTurma($IDTurma) as $a) {
            $SQL = <<<SQL
               SELECT 
                    d.NMDisciplina as Disciplina,
                    m.Nome,
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Rec,
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Nota
                FROM disciplinas d
                INNER JOIN aulas au ON(d.id = au.IDDisciplina)
                INNER JOIN frequencia f ON(au.id = f.IDAula)
                INNER JOIN alunos a ON(a.id = f.IDAluno)
                INNER JOIN matriculas m ON(m.id = a.IDMatricula)
                INNER JOIN atividades at ON(at.IDAula = au.id)
                INNER JOIN notas n ON(at.id = n.IDAtividade)
                WHERE a.id = $a AND DATE_FORMAT(f.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')
                GROUP BY m.Nome, m.id, d.id
            SQL;
        
            $queryBoletim = DB::select($SQL);
        
            // Verificar se o aluno tem notas lançadas
            if (!empty($queryBoletim)) {
                foreach ($queryBoletim as $boletim) {
                    // Se o aluno já existe no array $ata, apenas adiciona as notas da nova disciplina
                    if (!isset($ata[$boletim->Nome])) {
                        $ata[$boletim->Nome] = array(); // Inicializa o array para o aluno
                    }
                    // Adiciona a nota da disciplina no array do aluno
                    $ata[$boletim->Nome][$boletim->Disciplina] = $boletim->Nota;
                }
            }
        }
        
        // Criar o PDF com FPDF
        $pdf = new Fpdf('P', 'mm', 'A4');
        $pdf->AddPage();
        
        // Definir margens
        $pdf->SetMargins(5, 5, 5);
        
        // Definir a fonte para o título
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'ATA DE RESULTADOS FINAIS', 0, 1, 'C');
        
        // Espaço após o título
        $pdf->Ln(10);
        
        // Informações adicionais (número de Ata, data, etc.)
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'A presenca de distribuicao das notas finais e nota global dos alunos do Curso...', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Definir a fonte para as disciplinas (texto vertical)
        $disciplinas = self::getDisciplinasTurma($IDTurma);
        $colWidth = 8; // Ajuste para a largura das colunas
        $rowHeight = 7; // Altura das linhas
        
        // Definir a altura inicial das colunas
        $xPosInicial = 90;
        $yPos = 85; // Posição Y para as disciplinas
        $pdf->SetY($yPos); // Posição Y inicial
        
        // Imprimir as disciplinas verticalmente com bordas
        foreach ($disciplinas as $disciplina) {
            $pdf->SetXY($xPosInicial, $yPos); // Definir a posição X e Y para cada coluna
            $pdf->SetFont('Arial', 'B', 10);
        
            // Desenhar a borda da célula
            $pdf->Rect($xPosInicial - 5, $yPos - 30, $colWidth, 50); // Tamanho da célula vertical
        
            // Rotacionar o texto para ficar vertical
            $pdf->Rotate(90, $xPosInicial + 7, $yPos + 12); // Girar 90 graus
        
            // Imprimir o nome da disciplina com borda preta
            $pdf->Cell(45, $colWidth, mb_convert_encoding($disciplina, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        
            // Voltar à rotação normal
            $pdf->Rotate(0);
        
            // Mover para a próxima coluna
            $xPosInicial += $colWidth;
        }
        
        // Voltar para a rotação normal do texto e definir nova posição Y para a tabela
        $pdf->SetY($yPos + 20);
        
        // Criar linhas da tabela (para alunos)
        $pdf->SetFont('Arial', '', 12);
        
        // Para cada aluno e suas notas
        foreach ($ata as $aluno => $notas) {
            // Imprimir uma célula para o nome do aluno
            $pdf->Cell(80, $rowHeight, mb_convert_encoding($aluno, 'ISO-8859-1', 'UTF-8'), 1);
        
            // Para cada disciplina, imprime a nota, ou espaço vazio se o aluno não tiver nota para a disciplina
            foreach ($disciplinas as $disciplina) {
                $nota = isset($notas[$disciplina]) ? $notas[$disciplina] : ''; // Se a nota existir, imprime, senão imprime vazio
                $pdf->Cell($colWidth, $rowHeight, $nota, 1);
            }
        
            // Pular para a próxima linha
            $pdf->Ln();
        }
        
        // Saída do PDF
        $pdf->Output('I', 'Ata_de_Resultados_Finais.pdf');
        exit;
        
    }

    public function gerarBoletins($turmaId)
    {
        // Exemplo de dados da consulta SQL que serão usados para gerar o boletim
        $boletins = array();
        
        foreach(self::getAlunosByTurma($turmaId) as $a){
            $SQL = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.id = au.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Faltas1B,
                50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.id = au.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas2B,
                50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.id = au.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas3B,
                50 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.id = au.id AND au2.IDDisciplina = d.id AND au2.Estagio="4º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas4B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='1º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota1B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='2º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota2B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='3º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota3B,
                CASE WHEN 
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y')) > 0
                THEN
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND rec2.created_at = DATE_FORMAT(NOW(),'%Y'))
                ELSE 
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='4º BIM' AND DATE_FORMAT(n2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') )
                END as Nota4B
            FROM disciplinas d
            INNER JOIN aulas au ON(d.id = au.IDDisciplina)
            INNER JOIN frequencia f ON(au.id = f.IDAula)
            INNER JOIN alunos a ON(a.id = f.IDAluno)
            INNER JOIN atividades at ON(at.IDAula = au.id)
            INNER JOIN notas n ON(at.id = n.IDAtividade)
            WHERE a.id = $a
            GROUP BY d.id 
        SQL;
        $queryBoletim = DB::select($SQL);
        
        // Verificar se o aluno tem notas lançadas
        if (!empty($queryBoletim)) {
            $queryAluno = DB::select("SELECT m.Nome as Aluno,t.Nome as Turma,e.Nome as Escola FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN escolas e ON(t.IDEscola = e.id) WHERE a.id = $a")[0];
            $boletins[$a] = array(
                "DadosAluno" => $queryAluno,
                "Disciplinas" => []
            );
            // Adicionar ao boletim somente se há dados
            foreach ($queryBoletim as $boletim) {
                $boletins[$a]['Disciplinas'][] = array(
                    "Disciplina" => $boletim->Disciplina,
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
                
        }
        //dd($boletins);
        //RESGATAR DADOS DA ORGANIZACAO
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona a página antes do loop

        // Variável de controle para contar os boletins
        $boletimCount = 0;

        // Definir margens
        $pdf->SetMargins(5, 5, 5); // Define margens: esquerda, superior e direita

        // Loop através dos boletins
        foreach ($boletins as $row) {
            // Adiciona uma nova página a cada dois boletins
            if ($boletimCount % 2 == 0 && $boletimCount > 0) {
                $pdf->AddPage();
            }

            //CABECALHO DO BOLETIM
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Ln(3);
            $pdf->Cell(66, 7, mb_convert_encoding("Escola: " . $row['DadosAluno']->Escola, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(66, 7, mb_convert_encoding("Turma: " . $row['DadosAluno']->Turma, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(66, 7, mb_convert_encoding("Ano Letivo: ". date('Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Ln();
            $pdf->Cell(198, 7, mb_convert_encoding("Aluno: " . $row['DadosAluno']->Aluno , 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
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
            //DADOS DO BOLETIM
            foreach($row['Disciplinas'] as $d){
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(30, 7, mb_convert_encoding($d['Disciplina'],'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota1B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas1B']/4, 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota2B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas2B']/4, 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota3B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas3B']/4, 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota4B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas4B'], 1, 1, 'C');
            }
            //RODAPÉ
            $pdf->Ln(1);
            $pdf->Cell(0, 10, 'Assinatura do Professor(a): _______________________', 0, 1, 'L');
            $pdf->Cell(0, 10, self::utfConvert('Assinatura da Família: ____________________________'), 0, 1, 'L');
            $pdf->Ln(1);
            //CONTADOR DE BOLETINS
            $boletimCount++;
        }

        // Retorna o PDF
        $pdf->Output('I', 'boletim.pdf');
        exit;
    }


    public static function getAlunosByTurma($IDTurma){
        $arrId = array();
        foreach(Aluno::select("id")->where("IDTurma",$IDTurma)->where('STAluno',0)->get() as $a){
            array_push($arrId,$a->id);
        }
        return $arrId;
    }

}
