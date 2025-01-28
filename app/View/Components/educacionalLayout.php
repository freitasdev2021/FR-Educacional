<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class educacionalLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public const modulos = array(
        0 => [ // Fornecedor
            [
                'nome' => 'Secretarías',
                'icon' => 'bx bx-buildings',
                'rota' => 'Secretarias/index',
                'endereco' => 'Secretarias'
            ],
            [
                'nome' => 'Usuários',
                'icon' => 'bx bx-user',
                'rota' => 'Usuarios/indexFornecedor',
                'endereco' => 'Usuarios'
            ]
        ],
        2 => [ // Secretário
            [
                'nome' => 'Alunos',
                'icon' => 'bx bxs-group',
                'rota' => 'Alunos/index',
                'endereco' => 'Alunos'
            ],
            [
                'nome' => 'Recrutamento',
                'icon' => 'bx bxs-notepad',
                'rota' => 'Recrutamento/index',
                'endereco' => 'Recrutamento'
            ],
            [
                'nome' => 'Diretores',
                'icon' => 'bx bxs-briefcase-alt',
                'rota' => 'Diretores/index',
                'endereco' => 'Diretores'
            ],
            [
                'nome' => 'Escolas',
                'icon' => 'bx bxs-school',
                'rota' => 'Escolas/index',
                'endereco' => 'Escolas'
            ],
            [
                'nome' => 'Professores',
                'icon' => 'bx bxs-book-reader',
                'rota' => 'Professores/index',
                'endereco' => 'Professores'
            ],
            [
                'nome' => 'Pedagogos',
                'icon' => 'bx bx-library',
                'rota' => 'Pedagogos/index',
                'endereco' => 'Pedagogos'
            ],
            [
                'nome' => 'Biblioteca',
                'icon' => 'bx bx-book',
                'rota' => 'Biblioteca/index',
                'endereco' => 'Biblioteca'
            ],
            [
                'nome' => 'Funcionários',
                'icon' => 'bx bxs-user-detail',
                'rota' => 'Auxiliares/index',
                'endereco' => 'Auxiliares'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Merenda',
                'icon' => 'bx bx-fork',
                'rota' => 'Merenda/index',
                'endereco' => 'Merenda'
            ],
            [
                'nome' => 'Transporte',
                'icon' => 'bx bx-bus-school',
                'rota' => 'Transporte/index',
                'endereco' => 'Transporte'
            ]
        ],
        2.5 => [ // Secretário (Permissão 2.5)
            [
                'nome' => 'Recrutamento',
                'icon' => 'bx bxs-notepad',
                'rota' => 'Recrutamento/index',
                'endereco' => 'Recrutamento'
            ],
            [
                'nome' => 'Alunos',
                'icon' => 'bx bxs-group',
                'rota' => 'Alunos/index',
                'endereco' => 'Alunos'
            ],
            [
                'nome' => 'Diretores',
                'icon' => 'bx bxs-briefcase-alt',
                'rota' => 'Diretores/index',
                'endereco' => 'Diretores'
            ],
            [
                'nome' => 'Escolas',
                'icon' => 'bx bxs-school',
                'rota' => 'Escolas/index',
                'endereco' => 'Escolas'
            ],
            [
                'nome' => 'Professores',
                'icon' => 'bx bxs-book-reader',
                'rota' => 'Professores/index',
                'endereco' => 'Professores'
            ],
            [
                'nome' => 'Pedagogos',
                'icon' => 'bx bx-library',
                'rota' => 'Pedagogos/index',
                'endereco' => 'Pedagogos'
            ],
            [
                'nome' => 'Biblioteca',
                'icon' => 'bx bx-book',
                'rota' => 'Biblioteca/index',
                'endereco' => 'Biblioteca'
            ],
            [
                'nome' => 'Funcionários',
                'icon' => 'bx bxs-user-detail',
                'rota' => 'Auxiliares/index',
                'endereco' => 'Auxiliares'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Merenda',
                'icon' => 'bx bx-fork',
                'rota' => 'Merenda/index',
                'endereco' => 'Merenda'
            ],
            [
                'nome' => 'Transporte',
                'icon' => 'bx bx-bus-school',
                'rota' => 'Transporte/index',
                'endereco' => 'Transporte'
            ]
        ],
        3 => [ // Nutricionista
            // Adicionar módulos específicos para Nutricionista
        ],
        4 => [ // Diretor
            [
                'nome' => 'Lançamentos',
                'icon' => 'bx bxs-book',
                'rota' => 'Aulas/index',
                'endereco' => 'Lançamentos'
            ],
            [
                'nome' => 'Pedagogos',
                'icon' => 'bx bx-library',
                'rota' => 'Pedagogos/index',
                'endereco' => 'Pedagogos'
            ],
            [
                'nome' => 'Ocorrências',
                'icon' => 'bx bx-highlight',
                'rota' => 'Ocorrencias/index',
                'endereco' => 'Ocorrencias'
            ],
            [
                'nome' => 'Fichas',
                'icon' => 'bx bx-spreadsheet',
                'rota' => 'Fichas/index',
                'endereco' => 'Fichas'
            ],
            [
                'nome' => 'Alunos',
                'icon' => 'bx bxs-group',
                'rota' => 'Alunos/index',
                'endereco' => 'Alunos'
            ],
            [
                'nome' => 'Biblioteca',
                'icon' => 'bx bx-book',
                'rota' => 'Biblioteca/index',
                'endereco' => 'Biblioteca'
            ],
            [
                'nome' => 'Funcionários',
                'icon' => 'bx bxs-user-detail',
                'rota' => 'Auxiliares/index',
                'endereco' => 'Auxiliares'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Endereços',
                'icon' => 'bx bx-street-view',
                'rota' => 'Enderecos/index',
                'endereco' => 'Enderecos'
            ],
            [
                'nome' => 'Merenda',
                'icon' => 'bx bx-fork',
                'rota' => 'Merenda/index',
                'endereco' => 'Merenda'
            ],
            [
                'nome' => 'Transporte',
                'icon' => 'bx bx-bus-school',
                'rota' => 'Transporte/index',
                'endereco' => 'Transporte'
            ]
        ],
        4.5 => [ // Diretor (Permissão 4.5)
            [
                'nome' => 'Lançamentos',
                'icon' => 'bx bxs-book',
                'rota' => 'Aulas/index',
                'endereco' => 'Lançamentos'
            ],
            [
                'nome' => 'Pedagogos',
                'icon' => 'bx bx-library',
                'rota' => 'Pedagogos/index',
                'endereco' => 'Pedagogos'
            ],
            [
                'nome' => 'Ocorrências',
                'icon' => 'bx bx-highlight',
                'rota' => 'Ocorrencias/index',
                'endereco' => 'Ocorrencias'
            ],
            [
                'nome' => 'Fichas',
                'icon' => 'bx bx-spreadsheet',
                'rota' => 'Fichas/index',
                'endereco' => 'Fichas'
            ],
            [
                'nome' => 'Alunos',
                'icon' => 'bx bxs-group',
                'rota' => 'Alunos/index',
                'endereco' => 'Alunos'
            ],
            [
                'nome' => 'Biblioteca',
                'icon' => 'bx bx-book',
                'rota' => 'Biblioteca/index',
                'endereco' => 'Biblioteca'
            ],
            [
                'nome' => 'Funcionários',
                'icon' => 'bx bxs-user-detail',
                'rota' => 'Auxiliares/index',
                'endereco' => 'Auxiliares'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Merenda',
                'icon' => 'bx bx-fork',
                'rota' => 'Merenda/index',
                'endereco' => 'Merenda'
            ],
            [
                'nome' => 'Transporte',
                'icon' => 'bx bx-bus-school',
                'rota' => 'Transporte/index',
                'endereco' => 'Transporte'
            ]
        ],
        5 => [ // Supervisor
            [
                'nome' => 'Alunos',
                'icon' => 'bx bxs-group',
                'rota' => 'Alunos/index',
                'endereco' => 'Alunos'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Turmas',
                'icon' => 'bx bxs-graduation',
                'rota' => 'Turmas/index',
                'endereco' => 'Turmas'
            ],
            [
                'nome' => 'Lançamentos',
                'icon' => 'bx bxs-book',
                'rota' => 'Aulas/index',
                'endereco' => 'Lançamentos'
            ],
            [
                'nome' => 'Planejamentos',
                'icon' => 'bx bx-list-ol',
                'rota' => 'Planejamentos/index',
                'endereco' => 'Planejamentos'
            ],
            [
                'nome' => 'Ocorrências',
                'icon' => 'bx bx-highlight',
                'rota' => 'Ocorrencias/index',
                'endereco' => 'Ocorrencias'
            ],
            [
                'nome' => 'Fichas',
                'icon' => 'bx bx-spreadsheet',
                'rota' => 'Fichas/index',
                'endereco' => 'Fichas'
            ],
            [
                'nome' => 'EAD',
                'icon' => 'bx bx-desktop',
                'rota' => 'EAD/index',
                'endereco' => 'EAD'
            ],
            [
                'nome' => 'Apoio',
                'icon' => 'bx bxs-universal-access',
                'rota' => 'Apoio/index',
                'endereco' => 'Apoio'
            ]
        ],
        5.5 => [ // Supervisor (Permissão 5.5)
            [
                'nome' => 'Alunos',
                'icon' => 'bx bxs-group',
                'rota' => 'Alunos/index',
                'endereco' => 'Alunos'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Turmas',
                'icon' => 'bx bxs-graduation',
                'rota' => 'Turmas/index',
                'endereco' => 'Turmas'
            ],
            [
                'nome' => 'Lançamentos',
                'icon' => 'bx bxs-book',
                'rota' => 'Aulas/index',
                'endereco' => 'Lançamentos'
            ],
            [
                'nome' => 'Planejamentos',
                'icon' => 'bx bx-list-ol',
                'rota' => 'Planejamentos/index',
                'endereco' => 'Planejamentos'
            ],
            [
                'nome' => 'Ocorrências',
                'icon' => 'bx bx-highlight',
                'rota' => 'Ocorrencias/index',
                'endereco' => 'Ocorrencias'
            ],
            [
                'nome' => 'Fichas',
                'icon' => 'bx bx-spreadsheet',
                'rota' => 'Fichas/index',
                'endereco' => 'Fichas'
            ],
            [
                'nome' => 'EAD',
                'icon' => 'bx bx-desktop',
                'rota' => 'EAD/index',
                'endereco' => 'EAD'
            ],
            [
                'nome' => 'Apoio',
                'icon' => 'bx bxs-universal-access',
                'rota' => 'Apoio/index',
                'endereco' => 'Apoio'
            ]
        ],
        6 => [ // Professor
            [
                'nome' => 'Alunos',
                'icon' => 'bx bxs-group',
                'rota' => 'Alunos/index',
                'endereco' => 'Alunos'
            ],
            [
                'nome' => 'Apoio',
                'icon' => 'bx bxs-universal-accessp',
                'rota' => 'Apoio/index',
                'endereco' => 'Apoio'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Turmas',
                'icon' => 'bx bxs-graduation',
                'rota' => 'Turmas/index',
                'endereco' => 'Turmas'
            ],
            [
                'nome' => 'Lançamentos',
                'icon' => 'bx bxs-book',
                'rota' => 'Aulas/index',
                'endereco' => 'Lançamentos'
            ],
            [
                'nome' => 'Planejamentos',
                'icon' => 'bx bx-list-ol',
                'rota' => 'Planejamentos/index',
                'endereco' => 'Planejamentos'
            ],
            [
                'nome' => 'Ocorrências',
                'icon' => 'bx bx-highlight',
                'rota' => 'Ocorrencias/index',
                'endereco' => 'Ocorrencias'
            ],
            [
                'nome' => 'Fichas',
                'icon' => 'bx bx-spreadsheet',
                'rota' => 'Fichas/index',
                'endereco' => 'Fichas'
            ],
            [
                'nome' => 'EAD',
                'icon' => 'bx bx-desktop',
                'rota' => 'EAD/index',
                'endereco' => 'EAD'
            ]
        ],
        6.5 => [ // Professor (Permissão 6.5)
            [
                'nome' => 'Alunos',
                'icon' => 'bx bxs-group',
                'rota' => 'Alunos/index',
                'endereco' => 'Alunos'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Turmas',
                'icon' => 'bx bxs-graduation',
                'rota' => 'Turmas/index',
                'endereco' => 'Turmas'
            ],
            [
                'nome' => 'Lançamentos',
                'icon' => 'bx bxs-book',
                'rota' => 'Aulas/index',
                'endereco' => 'Lançamentos'
            ],
            [
                'nome' => 'Planejamentos',
                'icon' => 'bx bx-list-ol',
                'rota' => 'Planejamentos/index',
                'endereco' => 'Planejamentos'
            ],
            [
                'nome' => 'Ocorrências',
                'icon' => 'bx bx-highlight',
                'rota' => 'Ocorrencias/index',
                'endereco' => 'Ocorrencias'
            ],
            [
                'nome' => 'Fichas',
                'icon' => 'bx bx-spreadsheet',
                'rota' => 'Fichas/index',
                'endereco' => 'Fichas'
            ],
            [
                'nome' => 'EAD',
                'icon' => 'bx bx-desktop',
                'rota' => 'EAD/index',
                'endereco' => 'EAD'
            ]
        ],
        7 => [ // Aluno
            [
                'nome' => 'Matrículas',
                'icon' => 'bx bxs-group',
                'rota' => 'Matriculas/index',
                'endereco' => 'Matriculas'
            ],
            [
                'nome' => 'Ocorrências',
                'icon' => 'bx bx-highlight',
                'rota' => 'OcorrenciasAluno/index',
                'endereco' => 'OcorrenciasAluno'
            ],
            [
                'nome' => 'Calendário',
                'icon' => 'bx bx-calendar',
                'rota' => 'Calendario/index',
                'endereco' => 'Calendario'
            ],
            [
                'nome' => 'Lançamentos',
                'icon' => 'bx bxs-book',
                'rota' => 'Aulas/indexAluno',
                'endereco' => 'Lançamentos'
            ]
        ],
        8 => [ // Candidato
            [
                'nome' => 'Cadastro',
                'icon' => 'bx bx-user-plus',
                'rota' => 'Cadastro/index',
                'endereco' => 'Cadastro'
            ],
            [
                'nome' => 'Cursos',
                'icon' => 'bx bx-book',
                'rota' => 'Cursos/index',
                'endereco' => 'Cursos'
            ]
        ]
    );    

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $Tipo = Auth::user()->tipo;
        return view('components.Educacional-layout',[
            "modulos" => self::modulos[$Tipo]
            
        ]);
    }
}
