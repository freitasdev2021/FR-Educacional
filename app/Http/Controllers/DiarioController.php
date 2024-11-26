<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Comentario;
use App\Models\Professor;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Http\Controllers\AulasController;
use Illuminate\Http\Request;

class DiarioController extends Controller
{
    public const submodulos = AulasController::submodulos;

    public function index(){
        $Data = array();
        $Professores = array();
        $ProfComentarios = array();
        $Estagio = array();
        foreach(self::paraFiltros() as $d){
            array_push($Data,$d->DTAula);
            array_push($Professores,$d->Professor);
            array_push($Estagio,$d->Estagio);
            array_push($ProfComentarios,array(
                "id"=>$d->IDProfessor,
                "Nome" => $d->Professor
            ));
        }
        $IDProfessores = implode(",",ProfessoresController::getEscolaProfessores());
    
        return view("Aulas.diario",[
            "submodulos" => self::submodulos,
            "relatorios" => self::getRelatoriosProfessor(),
            "Data" => array_unique($Data),
            "Professores" => Professor::findMany($IDProfessores),
            "Estagio" => array_unique($Estagio),
            "ProfessoresComentario" => array_map("unserialize", array_unique(array_map("serialize", $ProfComentarios))),
            "ComentariosProfessor" => DB::select("SELECT c.id,c.created_at,p.Nome as Professor,c.Titulo,c.Comentario FROM comentarios c INNER JOIN professores p ON(c.IDProfessor = p.id) WHERE p.id IN($IDProfessores)"),
            "currentComentarios" => Comentario::all()->where('IDProfessor',Auth::user()->IDProfissional)
        ]);
    }

    public function comentar(Request $request){
        Comentario::create($request->all());
        return redirect()->back();
    }

    public function getRelatoriosProfessor(){
        $IDEscolas = implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));

        $WHERE = "WHERE ";
        if(!isset($_GET['Professor'])){
            $WHERE .= " t.IDEscola IN($IDEscolas)";
        }

        if(isset($_GET['Professor'])){

            if(isset($_GET['Professor']) && !empty($_GET['Professor'])){
                $WHERE .= "p.id=".$_GET['Professor'];
            }

            if(isset($_GET['Estagio']) && !empty($_GET['Estagio'])){
                $estagio = $_GET['Estagio'];
                $WHERE .= " AND au.Estagio = '$estagio'";
            }

            if(isset($_GET['Data']) && !empty($_GET['Data'])){
                $Data = $_GET['Data'];
                $WHERE .= " AND au.DTAula = '$Data'";
            }
        }

        $SQL = <<<SQL
            SELECT 
                p.Nome as Professor,
                p.id as IDProfessor,
                t.Nome as Turma,
                t.Serie as Serie,
                au.DSAula as Aula,
                au.created_at,
                au.Estagio,
                au.DTAula,
                (
                SELECT
                    CONCAT(
                        '[',
                        GROUP_CONCAT(
                            CONCAT(
                                '{',
                                '"Conteudo":"', atv.TPConteudo, '"',
                                '}'
                            )
                            SEPARATOR ','
                        ),
                        ']'
                    )
                FROM 
                    atividades atv
                INNER JOIN 
                    aulas au2 ON au2.id = atv.IDAula
                WHERE 
                    atv.IDAula = au.id
            ) AS conteudoLecionado
            FROM 
                aulas au 
            INNER JOIN professores p ON(p.id = au.IDProfessor)
            INNER JOIN turmas t ON(t.id = au.IDTurma)
            $WHERE
        SQL;
        //dd($SQL);
        return DB::select($SQL);

    }


    public function paraFiltros(){
        $IDEscolas = implode(",",EscolasController::getIdEscolas(Auth::user()->tipo,Auth::user()->id,Auth::user()->id_org,Auth::user()->IDProfissional));

        $SQL = <<<SQL
            SELECT 
                p.Nome as Professor,
                p.id as IDProfessor,
                t.Nome as Turma,
                t.Serie as Serie,
                au.DSAula as Aula,
                au.created_at,
                au.Estagio,
                au.DTAula,
                (
                    SELECT
                        CONCAT(
                            '[',
                            GROUP_CONCAT(
                                '{'
                                ,',"Conteudo":"', atv.TPConteudo, '"'
                                ,'}'
                                SEPARATOR ','
                            ),
                            ']'
                        )
                    FROM 
                        atividades atv
                    INNER JOIN 
                        aulas au2 ON(au2.id = atv.IDAula) 
                    WHERE 
                        atv.IDAula = au.id
                ) AS conteudoLecionado
            FROM 
                aulas au 
            INNER JOIN professores p ON(p.id = au.IDProfessor)
            INNER JOIN turmas t ON(t.id = au.IDTurma)
            WHERE t.IDEscola IN($IDEscolas)
        SQL;

        return DB::select($SQL);
    }

    public function exportar($Professor,$Estagio,$Data,$Comentario){
        $DataComentario = Comentario::find($Comentario);
        $DataProfessor = Professor::find($Professor);

        $SQL = <<<SQL
            SELECT 
                p.Nome as Professor,
                p.id as IDProfessor,
                t.Nome as Turma,
                t.Serie as Serie,
                au.DSAula as Aula,
                au.created_at,
                au.Estagio,
                (
                    SELECT
                        CONCAT(
                            '[',
                            GROUP_CONCAT(
                                '{'
                                ,',"Conteudo":"', atv.TPConteudo, '"'
                                ,'}'
                                SEPARATOR ','
                            ),
                            ']'
                        )
                    FROM 
                        atividades atv
                    INNER JOIN 
                        aulas au2 ON(au2.id = atv.IDAula) 
                    WHERE 
                        atv.IDAula = au.id
                ) AS conteudoLecionado
            FROM 
                aulas au 
            INNER JOIN professores p ON(p.id = au.IDProfessor)
            INNER JOIN turmas t ON(t.id = au.IDTurma)
            WHERE p.id = $Professor AND au.Estagio = '$Estagio' AND au.created_at = '$Data' 
        SQL;

        $Diario = DB::select($SQL);

        $Conteudo = array(
            "Professor" => $DataProfessor->Nome,
            "Comentario" => array(
                "Titulo" => $DataComentario->Titulo,
                "Conteudo" => $DataComentario->Comentario
            ),
            "Diario" => $Diario
        );
        // Criar o PDF com FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Adiciona uma página

        // Definir margens
        $pdf->SetMargins(10, 10, 10);

        // Definir cabeçalho do relatório
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, self::utfConvert("Diário do Professor - ") . $Conteudo['Professor'], 0, 1, 'C');
        $pdf->Ln(10); // Espaço após o título

        // Definir fonte para o corpo do relatório
        $pdf->SetFont('Arial', '', 12);

        // Loop através das aulas no diário
        foreach ($Conteudo['Diario'] as $aula) {
            // Informações da aula
            $pdf->Cell(0, 10, 'Turma: ' . $aula->Turma, 0, 1);
            $pdf->Cell(0, 10, self::utfConvert("Série: ") . self::utfConvert($aula->Serie), 0, 1);
            $pdf->Cell(0, 10, 'Aula: ' . self::utfConvert($aula->Aula), 0, 1);
            $pdf->Cell(0, 10, 'Data: ' . self::data($aula->created_at,'d/m/Y'), 0, 1);
            $pdf->Cell(0, 10, self::utfConvert("Estágio: ") . self::utfConvert($aula->Estagio), 0, 1);
            $pdf->Ln(5); // Espaço após as informações da aula

            // Conteúdo lecionado
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, self::utfConvert("Conteúdo Lecionado: "), 0, 1);
            $pdf->SetFont('Arial', '', 12);

            // Decodificar o JSON do conteúdo lecionado
            $conteudos = json_decode($aula->conteudoLecionado, true);

            if (!empty($conteudos)) {
                foreach ($conteudos as $conteudo) {
                    // Atividade
                    $pdf->Cell(0, 10, '- Atividade: ' . self::utfConvert($conteudo['Atividade']), 0, 1);

                    // Conteúdo com quebra de linha usando MultiCell
                    $pdf->MultiCell(0, 10, self::utfConvert("Conteúdo: ") . self::utfConvert($conteudo['Conteudo']), 0, 'L');
                    $pdf->Ln(2); // Espaço entre os conteúdos
                }
            } else {
                $pdf->Cell(0, 10, 'Nenhum conteúdo registrado.', 0, 1);
            }

            // Separar cada aula com um espaço maior
            $pdf->Ln(10);
        }

        // Testar um bloco longo de texto
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, $Conteudo['Comentario']['Conteudo'], 0, 'L');
        $pdf->Ln(10);

        // Assinatura do Professor
        $pdf->Cell(0, 10, 'Assinatura do Professor(a): _______________________', 0, 1, 'L');

        // Gera o PDF para saída
        $pdf->Output('I','Relatorio.pdf');
        exit;

    }

}
