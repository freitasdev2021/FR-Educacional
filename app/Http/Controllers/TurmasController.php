<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EscolasController;
use App\Http\Controllers\SecretariasController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Organizacao;
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
        WHERE o.id = $idorg AND t.id = $id AND STAluno = 0 GROUP BY a.id ORDER BY m.Nome ASC 
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

        array_push($Estagios,'Ano');
        $view = [
            'submodulos' => self::professoresSubmodulos,
            'id' => $IDTurma,
            'Disciplinas' => (Auth::user()->tipo == 6) ? ProfessoresController::getDisciplinasProfessor(Auth::user()->IDProfissional) : EscolasController::getListDisciplinasEscola(EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional)),
            'Estagios' => $Estagios,
            'Turma' => Turma::find($IDTurma)
        ];
        if($IDTurma){
            $view['submodulos'] = self::professoresCadastroSubmodulos;
        }
        return view('Escolas.desempenhoTurma',$view);
    }

    public static function getProgredidosTurma($IDTurma,$Ano){
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
                WHERE rec2.IDAluno = a.id 
                AND rec2.IDDisciplina = d.id 
                ) as RecBim,
                (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio = 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as RecAn,
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
                AND t.id = $IDTurma
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

    public function getDesempenho($IDTurma){
        $NOW = date('Y');
        if(isset($_GET['Disciplina']) && !empty($_GET['Disciplina']) && isset($_GET['Estagio']) && !empty($_GET['Estagio'])){
            $Disciplina = $_GET['Disciplina'];
            $Estagio = $_GET['Estagio'];

            if($Estagio == "Ano"){
                $andAno = "";
                $GroupBy = "GROUP BY 
                m.Nome, a.id, t.Periodo, d.NMDisciplina;";
                $select = <<<SQL
                    (SELECT COUNT(DISTINCT auFreq.Hash) AS total FROM aulas auFreq WHERE auFreq.TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = au.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(), '%Y')) as FreqDisc,
                    (SELECT COUNT(DISTINCT auFreq.Hash) AS total FROM aulas auFreq WHERE auFreq.TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDTurma = au.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(), '%Y')) - (SELECT COUNT(f2.id) 
                    FROM frequencia f2 
                    INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                    WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                    AND au2.IDDisciplina = d.id 
                    AND DATE_FORMAT(au2.DTAula, '%Y') = $NOW
                    ) as FrequenciaAno,
                    (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio = 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as RecAno,
                    (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as RecBim,
                    (SELECT SUM(rec2.PontuacaoPeriodo) FROM recuperacao rec2 WHERE rec2.Estagio != 'ANUAL' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as PontBim,
                    (SELECT SUM(n2.Nota) 
                    FROM notas n2 
                    INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                    INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                    WHERE au3.IDDisciplina = d.id 
                    AND n2.IDAluno = a.id 
                    AND DATE_FORMAT(au3.created_at, '%Y') = $NOW
                    ) as TotalAno,
                SQL;
            }else{
                $andAno = "AND au.Estagio = '".$Estagio."'";
                $GroupBy = "GROUP BY 
                m.Nome, a.id, t.Periodo, d.NMDisciplina, au.Estagio;";
                $select = <<<SQL
                -- Soma das Notas do Aluno para a Disciplina e Estágio específicos
                    (SELECT SUM(n2.Nota) 
                    FROM notas n2 
                    INNER JOIN atividades at2 ON n2.IDAtividade = at2.id 
                    INNER JOIN aulas au3 ON at2.IDAula = au3.id 
                    WHERE au3.IDDisciplina = d.id 
                    AND n2.IDAluno = a.id 
                    AND au3.Estagio = '$Estagio'
                    AND DATE_FORMAT(au3.created_at, '%Y') = $NOW
                    ) as Total,
                    (SELECT SUM(rec2.Nota) FROM recuperacao rec2 WHERE rec2.Estagio = '$Estagio' AND rec2.IDAluno = a.id AND rec2.IDDisciplina = d.id ) as RecBim,
                    -- Frequência (quantidade de presenças) do Aluno para a Disciplina e Estágio específicos
                    (SELECT COUNT(DISTINCT auFreq.Hash) AS total FROM aulas auFreq WHERE auFreq.TPConteudo = 0 AND auFreq.DTAula > au.DTAula AND auFreq.IDTurma = au.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(), '%Y') AND auFreq.Estagio = '$Estagio' ) as FreqDisc,
                    (SELECT COUNT(DISTINCT auFreq.Hash) AS total FROM aulas auFreq WHERE auFreq.TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDTurma = au.IDTurma AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(), '%Y')) - (SELECT COUNT(f2.id) 
                    FROM frequencia f2 
                    INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                    WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                    AND au2.IDDisciplina = d.id AND au2.Estagio = '$Estagio' 
                    AND DATE_FORMAT(au2.DTAula, '%Y') = $NOW
                    ) as Frequencia,
                SQL;
            }

            $SQL = <<<SQL
            SELECT 
                $select
                CASE WHEN (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = $Disciplina AND n2.IDAluno = a.id ) < t.MediaPeriodo THEN 'Reprovado' ELSE 'Aprovado' END as Resultado,
                m.Nome as Aluno,         -- Nome do Aluno
                a.id as IDAluno,         -- ID do Aluno
                t.Periodo,               -- Período do Aluno
                d.NMDisciplina as Disciplina, -- Nome da Disciplina
                au.Estagio,              -- Estágio (Bimestre)
                t.MediaPeriodo,
                t.TPAvaliacao,
                t.MINFrequencia
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
                $andAno             -- Filtro para estágio (4º Bimestre)
                AND DATE_FORMAT(au.DTAula, '%Y') = $NOW -- Filtro para o ano de 2024

            $GroupBy
            SQL;
            $resultados = DB::select($SQL);
        }else{
            $resultados = [];
        }
        
        if(count($resultados) > 0){
            foreach($resultados as $r){
                if($Estagio == "Ano"){
                    $Estagios = 200;
                }else{
                    switch($r->Periodo){
                        case 'Bimestral':
                            $Estagios = 200/4;
                        break;
                        case 'Trimestral':
                            $Estagios = 200/3;
                            $MediaTotal = $r->MediaPeriodo * 3;
                        break;
                        case 'Semestral':
                            $Estagios = 200/2;
                            $MediaTotal = $r->MediaPeriodo * 2;
                        break;
                        case 'Anual':
                            $Estagios = 200/1;
                            $MediaTotal = $r->MediaPeriodo;
                        break;
                    }
                }

                $MediaTotal = $r->MediaPeriodo * 4;

                $rota = route('Alunos/Recuperacao', ["IDAluno" => $r->IDAluno, "Estagio" => $r->Estagio]);

                if($Estagio == "Ano"){
                    $FrequenciaPorc = ($r->FrequenciaAno / $r->FreqDisc) * 100 ;
                    $Frequencia = number_format(100 - $FrequenciaPorc,1,'.',''). " %";
                    if($r->TPAvaliacao == "Nota"){
                        if($r->RecAno > 0){
                            $Total = $r->RecAno;
                        }else{
                            if($r->RecBim > 0){
                                $Total = ($r->RecBim - $r->TotalAno) + $r->PontBim;
                                //dd($Total);
                            }else{
                                $Total = $r->TotalAno;
                            }
                        }
                        
                        $Resultado = ($Total > $MediaTotal && $Frequencia >= $r->MINFrequencia) ? 'Aprovado' : 'Reprovado';
                    }else{
                        $Resultado = "Conceito Sob. Avaliação";
                    }
                }else{
                    if($r->Frequencia == 0 || $r->FreqDisc == 0){
                        $FrequenciaPorc = 0 ;
                    }else{
                        $FrequenciaPorc = ($r->Frequencia / $r->FreqDisc) * 100 ;
                    }
                    
                    $Frequencia = number_format(100 - $FrequenciaPorc,1,'.',''). " %";
                    
                    if($r->TPAvaliacao == "Nota"){
                        $Total = ($r->RecBim > 0) ? $r->RecBim : $r->Total;
                        $Resultado = ($Total > $r->MediaPeriodo && $Frequencia >= $r->MINFrequencia) ? 'Aprovado' : 'Reprovado';
                    }else{
                        $Resultado = "Conceito Sob. Avaliação, o Resultado será Inserido na Ficha";
                    }
                }

                $item = [];
                $item[] = $r->Aluno;
                $item[] = ($r->TPAvaliacao == "Nota") ? $Total : 'Conceito' ;
                $item[] = $Estagio;
                $item[] = $Frequencia; // Corrigido o uso de concatenação
                $item[] = $r->Disciplina;
                $item[] = $Resultado;
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

    public function getHeaderDisciplinasTurma($IDTurma){
        $disciplinas = [];
        $SQL = <<<SQL
            SELECT
                d.NMDisciplina as Disciplina,
                d.CargaHoraria
            FROM turnos tn
            INNER JOIN turmas t ON(tn.IDTurma = t.id)
            INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
            INNER JOIN escolas e ON(al.IDEscola = e.id)
            INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
            WHERE tn.IDTurma = $IDTurma GROUP BY d.id
        SQL;

        foreach(DB::select($SQL) as $s){
            array_push($disciplinas,$s);
        }
        return $disciplinas;
    }

    public function getAta($IDTurma){
        /////////////////////
        // Exemplo de dados da consulta SQL que serão usados para gerar o boletim 
        $ata = array();
        $Turma = Turma::find($IDTurma);
        $Escola = Escola::find($Turma->IDEscola);
        $Organizacao = Organizacao::find($Escola->IDOrg);
        // Obter dados dos alunos e suas notas
        foreach (self::getAlunosByTurma($IDTurma) as $a) {
            $SQL = <<<SQL
               SELECT 
                    d.NMDisciplina as Disciplina,
                    m.Nome,
                    a.id as IDAluno,
                    (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDDisciplina = d.id AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) as FreqDisc,
                    (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) 
                    FROM frequencia f2 
                    INNER JOIN aulas au2 ON au2.id = f2.IDAula 
                    WHERE au2.TPConteudo = 0 AND f2.IDAluno = a.id 
                    AND au2.IDDisciplina = d.id 
                    AND DATE_FORMAT(au2.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')
                    ) as FrequenciaAno,
                    (SELECT rec2.Nota FROM recuperacao rec2 WHERE rec2.Estagio = "ANUAL" AND rec2.IDAluno = $a AND rec2.IDDisciplina = d.id AND DATE_FORMAT(rec2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as RecAn,
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
                INNER JOIN turmas t ON(a.IDTurma = t.id)
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
                    if($boletim->RecAn > 0){
                        $ata[$boletim->Nome][$boletim->Disciplina] = $boletim->RecAn;
                    }else{
                        if($boletim->RecBim > 0){
                            $Nota = ($boletim->RecBim - $boletim->Nota) + $boletim->PontBim;
                            $ata[$boletim->Nome][$boletim->Disciplina] = $Nota;
                        }else{
                            $Nota = $boletim->Nota;
                            $ata[$boletim->Nome][$boletim->Disciplina] = $Nota;
                        }

                        $frequenciaAno = ($boletim->FrequenciaAno <=0) ? 0 : $boletim->FrequenciaAno;
                        
                        $porcFreq = 60;
                        // $ata[$boletim->Nome]["Frequência (%)"] = 100 - $porcFreq ;
                        // $ata[$boletim->Nome]["Faltas"] = $frequenciaAno;
                        // $ata[$boletim->Nome]['Carga Horária'] = date('H:i', strtotime($boletim->CargaHoraria));

                        if(AlunosController::getResultadoAno($boletim->IDAluno,date('Y')) == "Aprovado"){
                            $ata[$boletim->Nome]['Resultado'] = "A";
                        }else{
                            $ata[$boletim->Nome]['Resultado'] = "R";
                        }
                    }
                    
                }
            }
        }

        // Definir a fonte para as disciplinas (texto vertical)
        $disciplinas = self::getDisciplinasTurma($IDTurma);
        $headerDisciplinas = self::getHeaderDisciplinasTurma($IDTurma);
        $disciplinas[] = "Resultado";
        //dd($disciplinas,$headerDisciplinas);
        $CHDisciplinas = [];
        foreach($headerDisciplinas as $hd){
            array_push($CHDisciplinas,$hd->CargaHoraria);
        }
        // Configurar PDF
        $pdf = new Fpdf('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetMargins(5, 5, 5);

        self::criarCabecalho($pdf,$Escola->Nome,$Organizacao->Organizacao,'storage/organizacao_' . Auth::user()->id_org . '_escolas/escola_' . $Turma->IDEscola . '/' . $Escola->Foto,"ATA DE RESULTADOS FINAIS",[
            "Rua" => $Escola->Rua,
            "Numero" => $Escola->Numero,
            "Bairro" => $Escola->Bairro,
            "Cidade" => $Escola->Bairro,
            "UF" => $Escola->UF
        ]);

        // Linha para separar o cabeçalho do restante
        
        // Mensagem inicial
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(5, 50); // Ajusta a posição inicial para o texto
        $pdf->MultiCell(200, 5, self::utfConvert("Dia " . date('d/m/Y') . " terminou-se o processo de apuração das notas dos alunos do(a) $Turma->Serie, da(o) ENSINO FUNDAMENTAL DE 9 ANOS - SERIES INICIAIS, turma: $Turma->Serie - $Turma->NMTurma, turno: MATUTINO deste estabelecimento de ensino, com os seguintes resultados:"), 0, 'L');
        $pdf->Ln(12);
        // Disciplinas
        $colWidth = 12; // Largura ajustada
        $rowHeight = 4; // Altura das linhas ajustada
        $xPosInicial = 80;
        $yPos = 75;

        foreach ($disciplinas as $key => $disciplina) {
            $pdf->SetXY($xPosInicial, $yPos);
            $pdf->SetFont('Arial', 'B', 9);

            // Bordas e rotação
            $pdf->Rect($xPosInicial - 5, $yPos - 15, $colWidth, 35);
            $pdf->Rotate(90, $xPosInicial + 7, $yPos + 12);
            $pdf->Cell(50, $colWidth, self::utfConvert($disciplina), 0, 1, 'L');
            $pdf->Rotate(0);

            $result = array_filter($headerDisciplinas, function ($item) use ($disciplina) {
                return $item->Disciplina === $disciplina;
            });
            $pdf->SetXY($xPosInicial - 5, $yPos + 20);
            $pdf->SetFont('Arial', 'B', 7);
            if ($result) {
                $pdf->Cell($colWidth, 5, 'CH: ' . $CHDisciplinas[$key], 1, 0, 'C');
            } else {
                $pdf->Cell($colWidth, 5, 'Tot.: ' . array_sum($CHDisciplinas), 1, 'C');
            }

            $xPosInicial += $colWidth;
        }

        $pdf->SetY($yPos + 25);
        $pdf->SetFont('Arial', '', 8);

        foreach ($ata as $aluno => $notas) {
            $pdf->Cell(70, $rowHeight, self::utfConvert($aluno), 1);
            foreach ($disciplinas as $disciplina) {
                $nota = $notas[$disciplina] ?? '';
                $pdf->Cell($colWidth, $rowHeight, $nota, 1, 0, 'C');
            }
            $pdf->Ln();
        }

        $pdf->Ln();

        // Observações
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(275, 5, self::utfConvert("Observações: " . $Escola->ObsAta), 0, 'L');
        // Campos de assinatura no rodapé
        $pdf->SetY(-35); // Define a posição do rodapé (35 mm acima do fim da página)
        $pdf->SetFont('Arial', '', 10);
        $larguraTotal = 200; // Largura total disponível para os campos de assinatura
        $espacoEntreCampos = 20; // Espaço entre os campos
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
        $pdf->Cell($campoLargura, 5, self::utfConvert('Secretário(a)'), 0, 0, 'C');
        $pdf->Cell($espacoEntreCampos, 5, '', 0, 0); // Espaço
        $pdf->Cell($campoLargura, 5, self::utfConvert('Inspeção Escolar'), 0, 1, 'C');

        // Saída
        $pdf->Output('I', 'Ata_de_Resultados_Finais.pdf');
        exit;

    }

    public function getTurnos($IDTurma){

    }

    public function gerarBoletins($turmaId)
    {
        // Exemplo de dados da consulta SQL que serão usados para gerar o boletim
        $boletins = array();
        
        foreach(self::getAlunosByTurma($turmaId) as $a){
            $SQL = <<<SQL
            SELECT 
                d.NMDisciplina as Disciplina,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="1º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="1º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y')) as Faltas1B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="2º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="2º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas2B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="3º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="3º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas3B,
                (SELECT COUNT(auFreq.id) FROM aulas auFreq WHERE TPConteudo = 0 AND auFreq.DTAula > a.DTEntrada AND auFreq.IDTurma = t.id AND auFreq.IDDisciplina = d.id AND auFreq.Estagio="4º BIM" AND DATE_FORMAT(auFreq.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')) - (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE TPConteudo = 0 AND f2.IDAluno = $a AND au.id AND au2.IDDisciplina = d.id AND au2.Estagio="4º BIM" AND DATE_FORMAT(f2.created_at, '%Y') = DATE_FORMAT(NOW(),'%Y') ) as Faltas4B,
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
            INNER JOIN turmas t ON(a.IDTurma = t.id)
            INNER JOIN atividades at ON(at.IDAula = au.id)
            INNER JOIN notas n ON(at.id = n.IDAtividade)
            WHERE a.id = $a AND DATE_FORMAT(au.DTAula, '%Y') = DATE_FORMAT(NOW(),'%Y')
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
                $pdf->Cell(21, 7, $d['Faltas1B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota2B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas2B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Nota3B'], 1, 0, 'C');
                $pdf->Cell(21, 7, $d['Faltas3B'], 1, 0, 'C');
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
