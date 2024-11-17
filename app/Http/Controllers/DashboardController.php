<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PedagogosController;
use App\Http\Controllers\AlunosController;
use App\Http\Controllers\CalendarioController;
use App\Models\ProcessoSeletivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Candidato;
class DashboardController extends Controller
{

    public function index(){
        //dd(CalendarioController::calendarioLetivo());
        $IDOrg = Auth::user()->id_org;
        $view = [
            'ficha' => self::horariosProfessor(Auth::user()->id),
            'horariosAluno' => (Auth::user()->tipo == 7) ? self::getHorariosAluno(AlunosController::getAlunoByUser(Auth::user()->id)) : [],
            'Matriculas' => self::alunos(''),
            'Alunos' => self::alunos(' AND m.Aprovado=1'),
            'Desistentes' => self::alunos(' AND a.STAluno=2'),
            'Evadidos' => self::alunos(' AND a.STAluno=1'),
            'Transferidos' => DB::select("SELECT COUNT(t.id) as Quantidade FROM transferencias t INNER JOIN escolas e ON(t.IDEscolaOrigem = e.id) OR (t.IDEscolaDestino = e.id) WHERE e.IDOrg = $IDOrg AND t.Aprovado = 1 ")[0],
            'Alergia' => self::alunos(' AND m.Alergia > 0'),
            'Transporte' => self::alunos(' AND m.Transporte > 0'),
            'NEE' => self::alunos(' AND m.NEE > 0'),
            'BolsaFamilia' => self::alunos(' AND m.BolsaFamilia > 0'),
            'AMedico' => self::alunos(' AND m.AMedico > 0'),
            'APsicologico' => self::alunos(' AND m.APsicologico > 0'),
            'Usuarios' => DB::select("SELECT COUNT(u.id) as Usuarios FROM users u WHERE id_org=$IDOrg")[0]
        ];

        if(Auth::user()->tipo == 8){
            $IDUser = Auth::user()->id;
            $Candidatura = Candidato::where('IDUser',$IDUser)->first();
            $IDCandidato = $Candidatura->id;
            $view['Processos'] = DB::select("SELECT ps.id,ps.Nome,ps.Descricao FROM processos_seletivos ps LEFT JOIN inscricoes i ON(ps.id = i.IDProcesso) WHERE i.IDCandidato IS NULL");
        }

        return view('dashboard',$view);
    }

    public static function alunos($WHERE){
        $IDOrg = Auth::user()->id_org;
        if(Auth::user()->tipo == 4){
            $WHERE .= ' AND e.id='.self::getEscolaDiretor(Auth::user()->id);
        }
        return DB::select("SELECT COUNT(m.id) as Quantidade FROM matriculas m INNER JOIN alunos a ON(m.id = a.IDMatricula) INNER JOIN turmas t ON(t.id = a.IDTurma) INNER JOIN escolas e ON(e.id = t.IDEscola) WHERE e.IDOrg = $IDOrg $WHERE ")[0];
    }

    public function horariosProfessor($IDProf){
        $dias = [];
        $horarios = [];
        $SQL = <<<SQL
           SELECT 
            t.Serie AS Serie,
            t.Nome as Turma,
            CONCAT(
                '[',
                GROUP_CONCAT(
                    DISTINCT
                    '{'
                    ,'"Inicio":"', tn.INITur, '"'
                    ,',"Termino":"', tn.TERTur, '"'
                    ,',"Disciplina":"', d.NMDisciplina, '"'
                    ,',"Escola":"', e.Nome, '"'
                    ,',"Dia":"', tn.DiaSemana, '"'
                    ,'}'
                    SEPARATOR ','
                ),
                ']'
            ) AS Horarios
        FROM turnos tn
        INNER JOIN turmas t ON tn.IDTurma = t.id
        INNER JOIN alocacoes al ON t.IDEscola = al.IDEscola
        INNER JOIN escolas e ON al.IDEscola = e.id
        INNER JOIN professores p ON p.id = tn.IDProfessor
        INNER JOIN users us ON us.IDProfissional = p.id
        INNER JOIN disciplinas d ON d.id = tn.IDDisciplina
        WHERE us.id = $IDProf AND al.TPProfissional = 'PROF' 
        GROUP BY t.id, t.Serie;
        SQL;

        DB::statement("SET SESSION group_concat_max_len = 1000000");

        $turnos = DB::select($SQL);

        return $turnos;

    }

    public static function getHorariosAluno($IDAluno){
        $IDTurma = AlunosController::getAluno($IDAluno)->IDTurma;
        $SQL = <<<SQL
        SELECT 
            t.Serie AS Serie,
            t.Nome as Turma,
            CONCAT(
                '[',
                GROUP_CONCAT(
                    DISTINCT
                    '{'
                    ,'"Inicio":"', tn.INITur, '"'
                    ,',"Termino":"', tn.TERTur, '"'
                    ,',"Disciplina":"', d.NMDisciplina, '"'
                    ,',"Dia":"', tn.DiaSemana, '"'
                    ,'}'
                    SEPARATOR ','
                ),
                ']'
            ) AS Horarios
        FROM turnos tn
        INNER JOIN turmas t ON tn.IDTurma = t.id
        INNER JOIN alocacoes al ON t.IDEscola = al.IDEscola
        INNER JOIN escolas e ON al.IDEscola = e.id
        INNER JOIN professores p ON p.id = tn.IDProfessor
        INNER JOIN users us ON us.IDProfissional = p.id
        INNER JOIN disciplinas d ON d.id = tn.IDDisciplina
        WHERE t.id = $IDTurma AND al.TPProfissional = 'PROF' 
        GROUP BY t.id, t.Serie;
        SQL;

        return DB::select($SQL);
    }
}
