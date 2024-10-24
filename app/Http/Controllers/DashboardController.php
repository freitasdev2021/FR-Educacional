<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PedagogosController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class DashboardController extends Controller
{

    public function index(){
       
        $IDOrg = Auth::user()->id_org;
        return view('dashboard',[
            'ficha' => self::getFichaProfessor(Auth::user()->id,'Horarios'),
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
        ]);
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
                CONCAT(
                    '[',
                    GROUP_CONCAT(
                        '{'
                        ,'"Inicio":"', tn.INITur, '"'
                        ,',"Termino":"', tn.TERTur, '"'
                        ,',"Turma":"', t.Nome, '"'
                        ,',"Disciplina":"', d.NMDisciplina, '"'
                        ,',"Escola":"', e.Nome, '"'
                        ,'}'
                        SEPARATOR ','
                    ),
                    ']'
                ) as Horarios,
                tn.DiaSemana as Dia
            FROM turnos tn
            INNER JOIN turmas t ON(tn.IDTurma = t.id)
            INNER JOIN alocacoes al ON(t.IDEscola = al.IDEscola)
            INNER JOIN escolas e ON(al.IDEscola = e.id)
            INNER JOIN professores p ON(p.id = tn.IDProfessor)
            INNER JOIN users us ON(us.IDProfissional = p.id)
            INNER JOIN disciplinas d ON(d.id = tn.IDDisciplina)
            WHERE us.id = $IDProf 
            GROUP BY DiaSemana
        SQL;

        DB::statement("SET SESSION group_concat_max_len = 1000000");

        $turnos = DB::select($SQL);

        foreach($turnos as $t){
            // Adiciona os dias e os horários no array
            array_push($dias, $t->Dia);
            array_push($horarios, json_decode($t->Horarios));
        }

        return array(
            "Dias" => $dias,
            "Horarios" => $horarios
        );

    }
}
