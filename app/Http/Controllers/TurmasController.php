<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EscolasController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Turma;
use App\Models\Nota;
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
        return EscolasController::turmas() ;
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
            'Disciplinas' => self::getFichaProfessor(Auth::user()->id,'Disciplinas'),
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
                m.Nome as Aluno,
                a.id as IDAluno,
                t.Periodo,
                (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = $Disciplina AND n2.IDAluno = a.id ) as Total,
                au.Estagio,
                CASE WHEN (SELECT SUM(n2.Nota) FROM notas n2 INNER JOIN atividades at2 ON(n2.IDAtividade = at2.id) INNER JOIN aulas au3 ON(at2.IDAula = au3.id) WHERE au3.IDDisciplina = $Disciplina AND n2.IDAluno = a.id ) < t.MediaPeriodo THEN 'Reprovado' ELSE 'Aprovado' END as Resultado,
                (SELECT COUNT(f2.id) FROM frequencia f2 INNER JOIN aulas au2 ON(au2.id = f2.IDAula) WHERE f2.IDAluno = a.id AND au2.IDDisciplina = d.id ) as Frequencia,
                d.NMDisciplina as Disciplina
            FROM alunos a
            INNER JOIN matriculas m ON(m.id = a.IDMatricula)
            INNER JOIN turmas t ON(t.id = a.IDTurma)
            INNER JOIN notas n ON(n.IDAluno = a.id)
            INNER JOIN aulas au ON(t.id = au.IDTurma)
            INNER JOIN disciplinas d ON (d.id = au.IDDisciplina)
            WHERE a.IDTurma = $IDTurma AND au.IDDisciplina = $Disciplina AND au.Estagio = '$Estagio' AND DATE_FORMAT(au.created_at,'%Y') = $NOW GROUP BY m.Nome,au.Estagio
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

                $item = [];
                $item[] = $r->Aluno;
                $item[] = $r->Total;
                $item[] = $r->Estagio;
                $item[] = ($r->Frequencia/$Estagios)*100.." %";
                $item[] = $r->Disciplina;
                $item[] = ($r->Resultado == 'Reprovado') ? "<button onclick=\"recuperacao('" . route('Alunos/Recuperacao', ['IDAluno' => $r->IDAluno, 'Estagio' => $r->Estagio]) . "')\" class='btn btn-danger btn-xs'>Recuperação</button>" : 'Aprovado';
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
}
