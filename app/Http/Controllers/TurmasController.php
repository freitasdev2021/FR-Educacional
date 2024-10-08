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

    public function gerarBoletins($turmaId)
    {
        // Exemplo de dados da consulta SQL que serão usados para gerar o boletim
        $boletins = array();
        
        foreach(self::getAlunosByTurma($turmaId) as $a){
            $SQL = <<<SQL
            SELECT 
                    d.NMDisciplina as Disciplina,
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "1º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id) as Rec1B,
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "2º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id) as Rec2B,
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "3º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id) as Rec3B,
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "4º BIM" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id) as Rec4B,
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='1º BIM' ) as Nota1B,
                    200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º BIM" ) as Faltas1B,
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='2º BIM' ) as Nota2B,
                    200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º BIM" ) as Faltas2B,
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='3º BIM' ) as Nota3B,
                    200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º BIM" ) as Faltas3B,
                    (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = d.id AND n2.IDAluno = a.id AND au3.Estagio='4º BIM' ) as Nota4B,
                    200 - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id AND au2.Estagio="4º BIM" ) as Faltas4B
                FROM disciplinas d
                INNER JOIN aulas au ON(d.id = au.IDDisciplina)
                INNER JOIN frequencia f ON(au.id = f.IDAula)
                INNER JOIN alunos a ON(a.id = f.IDAluno)
                INNER JOIN atividades at ON(at.IDAula = au.id)
                INNER JOIN notas n ON(at.id = n.IDAtividade)
                WHERE a.id = $a
                GROUP BY d.id 
            SQL;
            $queryBoletim = DB::select($SQL)[0];
            $queryAluno = DB::select("SELECT m.Nome as Aluno,t.Nome as Turma,e.Nome as Escola FROM alunos a INNER JOIN matriculas m ON(m.id = a.IDMatricula) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN escolas e ON(t.IDEscola = e.id) WHERE a.id = $a")[0];
            array_push($boletins,array("Disciplina" => $queryBoletim->Disciplina,
                "DadosAluno" => $queryAluno,
                "Rec1B"=> $queryBoletim->Rec1B,
                "Rec2B"=> $queryBoletim->Rec2B,
                "Rec3B"=> $queryBoletim->Rec3B,
                "Rec4B"=> $queryBoletim->Rec4B,
                "Nota1B"=> $queryBoletim->Nota1B,
                "Nota2B"=> $queryBoletim->Nota2B,
                "Nota3B"=> $queryBoletim->Nota3B,
                "Nota4B"=> $queryBoletim->Nota4B,
                "Faltas1B"=> $queryBoletim->Faltas1B,
                "Faltas2B"=> $queryBoletim->Faltas2B,
                "Faltas3B"=> $queryBoletim->Faltas3B,
                "Faltas4B"=> $queryBoletim->Faltas4B,
            ));
        }
        //dd($boletins);
        //RESGATAR DADOS DA ORGANIZACAO
        $Organizacao = SecretariasController::getInstituicao(Auth::user()->id_org);
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
            $pdf->SetFont('Arial', 'B', 12);
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
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(30, 7, mb_convert_encoding($row['Disciplina'],'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(21, 7, $row['Nota1B'], 1, 0, 'C');
            $pdf->Cell(21, 7, $row['Faltas1B']/4, 1, 0, 'C');
            $pdf->Cell(21, 7, $row['Nota2B'], 1, 0, 'C');
            $pdf->Cell(21, 7, $row['Faltas2B']/4, 1, 0, 'C');
            $pdf->Cell(21, 7, $row['Nota3B'], 1, 0, 'C');
            $pdf->Cell(21, 7, $row['Faltas3B']/4, 1, 0, 'C');
            $pdf->Cell(21, 7, $row['Nota4B'], 1, 0, 'C');
            $pdf->Cell(21, 7, $row['Faltas4B']/4, 1, 1, 'C');
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


    public function getAlunosByTurma($IDTurma){
        $arrId = array();
        foreach(Aluno::select("id")->where("IDTurma",$IDTurma)->get() as $a){
            array_push($arrId,$a->id);
        }
        return $arrId;
    }

}
