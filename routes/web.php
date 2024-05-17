<?php

use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecretariasController;
use App\Http\Controllers\SecretariosController;
use App\Http\Controllers\EscolasController;
use App\Http\Controllers\DiretoresController;
use App\Http\Controllers\ProfessoresController;
use App\Http\Controllers\CardapioController;
use App\Http\Controllers\PedagogosController;
use App\Http\Controllers\ResponsaveisController;
use App\Http\Controllers\AlunosController;
use App\Http\Controllers\AuxiliaresController;
use App\Http\Controllers\ApoioController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    //
    Route::middleware('fornecedor')->group(function () {
        //SECRETARÍAS
        Route::get('/Secretarias',[SecretariasController::class,'index'])->name('Secretarias/index');
        Route::get('/Secretarias/Relatorios',[SecretariasController::class,'relatorios'])->name('Secretarias/Relatorios');
        Route::get('/Secretarias/Novo',[SecretariasController::class,'cadastro'])->name('Secretarias/Novo');
        Route::get('/Secretarias/Cadastro/{id}',[SecretariasController::class,'cadastro'])->name('Secretarias/Edit');
        Route::get('/Secretarias/list',[SecretariasController::class,'getSecretarias'])->name('Secretarias/list');
        Route::post('/Secretarias/Save',[SecretariasController::class,'save'])->name('Secretarias/Save');
        //ADMINISTRADORES DA SECRETARIA
        Route::get('/Secretarias/Administradores/list',[SecretariasController::class,'getSecretariasAdministradores'])->name('Secretarias/Administradores/list');
        Route::get('/Secretarias/Administradores/Novo',[SecretariasController::class,'cadastroAdministradores'])->name('Secretarias/Administradores/Novo');
        Route::get('/Secretarias/Administradores/Cadastro/{id}',[SecretariasController::class,'cadastroAdministradores'])->name('Secretarias/Administradores/Edit');
        Route::get('/Secretarias/Administradores',[SecretariasController::class,'administradores'])->name('Secretarias/Administradores');
        Route::post('/Secretarias/Administradores/Save',[SecretariasController::class,'saveAdm'])->name('Secretarias/Administradores/Save');
        //USUARIOS FORNECEDOR
        Route::get('/Fornecedor/Usuarios',[UsuariosController::class,'fornecedoresIndex'])->name('Usuarios/indexFornecedor');
    });
    //
    //Route::middleware(['secretario','diretor','coordenador','pedagogo'])->group(function () {
        //ESCOLAS
        Route::get('/Escolas/list',[EscolasController::class,'getEscolas'])->name('Escolas/list')->middleware('secretario');
        Route::get('/Escolas',[EscolasController::class,'index'])->name('Escolas/index')->middleware('secretario');
        Route::get('/Escolas/Novo',[EscolasController::class,'cadastro'])->name('Escolas/Novo')->middleware('secretario');
        Route::get('/Escolas/Cadastro/{id}',[EscolasController::class,'cadastro'])->name('Escolas/Edit')->middleware('secretario');
        Route::post('/Escolas/Save',[EscolasController::class,'save'])->name('Escolas/Save')->middleware('secretario');
        //Anos letivos
        Route::get('/Escolas/Anosletivos/list',[EscolasController::class,'getAnosLetivos'])->name('Escolas/Anosletivos/list')->middleware('secretario');
        Route::get('/Escolas/Anosletivos',[EscolasController::class,'anosLetivos'])->name('Escolas/Anosletivos')->middleware(['secretario','coordenador']);
        Route::get('/Escolas/Anosletivos/Novo',[EscolasController::class,'cadastroAnosLetivos'])->name('Escolas/Anosletivos/Novo')->middleware('secretario');
        Route::get('/Escolas/Anosletivos/Edit/{id}',[EscolasController::class,'cadastroAnosLetivos'])->name('Escolas/Anosletivos/Cadastro')->middleware('secretario');
        Route::post('/Escolas/Anosletivos/Save',[EscolasController::class,'saveAnosLetivos'])->name('Escolas/Anosletivos/Save')->middleware('secretario');
        //Disciplinas
        Route::get('/Escolas/Disciplinas/list',[EscolasController::class,'getDisciplinas'])->name('Escolas/Disciplinas/list')->middleware('secretario');
        Route::get('/Escolas/Disciplinas',[EscolasController::class,'Disciplinas'])->name('Escolas/Disciplinas')->middleware(['secretario','diretor','coordenador']);
        Route::get('/Escolas/Disciplinas/Novo',[EscolasController::class,'cadastroDisciplinas'])->name('Escolas/Disciplinas/Novo')->middleware(['secretario']);
        Route::get('/Escolas/Disciplinas/Edit/{id}',[EscolasController::class,'cadastroDisciplinas'])->name('Escolas/Disciplinas/Cadastro');
        Route::post('/Escolas/Disciplinas/Save',[EscolasController::class,'saveDisciplinas'])->name('Escolas/Disciplinas/Save')->middleware(['secretario']);
        //Turmas
        Route::get('/Escolas/Turmas',[EscolasController::class,'Turmas'])->name('Escolas/Turmas');
        Route::get('/Escolas/Turmas/Novo',[EscolasController::class,'cadastroTurmas'])->name('Escolas/Turmas/Novo')->middleware(['secretario','diretor']);
        Route::get('/Escolas/Turmas/Edit/{id}',[EscolasController::class,'cadastroTurmas'])->name('Escolas/Turmas/Cadastro');
        Route::post('/Escolas/Turmas/Save',[EscolasController::class,'saveTurmas'])->name('Escolas/Turmas/Save')->middleware(['secretario','diretor','coordenador']);
        //ALUNOS
        Route::get('/Alunos',[AlunosController::class,'index'])->name('Alunos/index')->middleware(['diretor','coordenador','secretario']);
        Route::get('/Alunos/Desempenho/{id}',[AlunosController::class,'desempenhoIndex'])->name('Alunos/Desempenho/index')->middleware(['coordenador','pedagogo']); //desempenho do aluno em especifico
        Route::get('/Alunos/Suspensao/{id}',[AlunosController::class,'suspensoesIndex'])->name('Alunos/Suspensao/index'); // suspensões de um aluno em especifico
        Route::get('/Alunos/Falta/{id}',[AlunosController::class,'faltasIndex'])->name('Alunos/Falta/index'); //faltas de um aluno em especifico
        Route::get('/Alunos/Situacao/{id}',[AlunosController::class,'situacaoIndex'])->name('Alunos/Situacao/index'); //situação de um aluno em especifico (frequente,evadido,desistente)
        Route::get('/Alunos/Renovacao/{id}',[AlunosController::class,'renovacaoIndex'])->name('Alunos/Renovacao/index'); //renovação de um aluno em especifico
        Route::get('/Alunos/Renovacoes',[AlunosController::class,'renovacaoIndex'])->name('Alunos/Renovacoes/index'); //renovação de um aluno em especifico
        Route::get('/Alunos/Suspensoes/',[AlunosController::class,'suspensoesIndex'])->name('Alunos/Suspensoes/index'); // suspensões em geral
        Route::get('/Alunos/Faltas/',[AlunosController::class,'faltasIndex'])->name('Alunos/Faltas/index'); //faltas em geral
        Route::get('/Alunos/Desistentes',[AlunosController::class,'desistentesIndex'])->name('Alunos/Desistentes/index');
        Route::get('/Alunos/Recuperacao',[AlunosController::class,'recuperacoesIndex'])->name('Alunos/Recuperacao/index');
        Route::get('/Alunos/Reprovados',[AlunosController::class,'reprovadosIndex'])->name('Alunos/Reprovados/index');
        Route::get('/Alunos/Evadidos',[AlunosController::class,'evadidosIndex'])->name('Alunos/Evadidos/index');
        //Matriculas
        Route::get('/Alunos/Matriculas/Novo',[AlunosController::class,'cadastroMatricula'])->name('Alunos/Matriculas/Novo')->middleware(['coordenador','diretor']);
        Route::get('/Alunos/Matriculas/Cadastro/{id}',[AlunosController::class,'cadastroMatricula'])->name('Alunos/Matriculas/Edit')->middleware(['coordenador','diretor','pedagogo']);
        Route::post('/Alunos/Matriculas/Save',[AlunosController::class,'saveMatricula'])->name('Alunos/Matriculas/Save')->middleware(['coordenador','diretor']);
        //SECRETARIOS
        Route::get('/Coordenadores',[SecretariosController::class,'index'])->name('Coordenadores/index')->middleware('secretario');
        Route::get('/Coordenadores/Novo',[SecretariosController::class,'cadastro'])->name('Coordenadores/Novo')->middleware('secretario');
        Route::get('/Coordenadores/Cadastro/{id}',[SecretariosController::class,'cadastro'])->name('Coordenadores/Edit')->middleware('secretario');
        Route::post('/Coordenadores/Save',[SecretariosController::class,'save'])->name('Coordenadores/Save')->middleware('secretario');
        //DIRETORES
        Route::get('/Diretores',[DiretoresController::class,'index'])->name('Diretores/index')->middleware('secretario');
        Route::get('/Diretores/Novo',[DiretoresController::class,'cadastro'])->name('Diretores/Novo')->middleware('secretario');
        Route::get('/Diretores/Cadastro/{id}',[DiretoresController::class,'cadastro'])->name('Diretores/Edit')->middleware('secretario');
        Route::post('/Diretoress/Save',[DiretoresController::class,'save'])->name('Diretores/Save')->middleware('secretario');
        //PROFESSORES
        Route::get('/Professores',[ProfessoresController::class,'index'])->name('Professores/index')->middleware(['coordenador','diretor','secretario']);
        Route::get('/Professores/Novo',[ProfessoresController::class,'cadastro'])->name('Professores/Novo')->middleware('secretario');
        Route::get('/Professores/Cadastro/{id}',[ProfessoresController::class,'cadastro'])->name('Professores/Edit')->middleware(['coordenador','diretor','secretario']);
        Route::post('/Professores/Save',[ProfessoresController::class,'save'])->name('Professores/Save')->middleware('secretario');

        Route::get('/Acompanhamento',[ProfessoresController::class,'acompanhamento'])->name('Acompanhamento/index');
        Route::get('/Professores/Calendario/{id}',[ProfessoresController::class,'calendario'])->name('Professores/Calendario')->middleware(['diretor','secretario']);
        Route::get('/Professores/Diario/{id}',[ProfessoresController::class,'diario'])->name('Professores/Diario');
        Route::get('/Professores/Ocorrencias/{id}',[ProfessoresController::class,'ocorrencias'])->name('Professores/Ocorrencia');
        Route::post('/Professores/Planejamento/{id}',[ProfessoresController::class,'planejamento'])->name('Professores/Planejamento');
        Route::post('/Professores/Planejamentos',[ProfessoresController::class,'planejamentos'])->name('Professores/Planejamentos')->middleware(['coordenador','diretor','secretario']);
        Route::post('/Professores/Turnos/{id}',[ProfessoresController::class,'turnos'])->name('Professores/Turnos')->middleware(['coordenador','diretor','secretario']);
        //PEDAGOGOS
        Route::get('/Pedagogos',[PedagogosController::class,'index'])->name('Pedagogos/index')->middleware('secretario');
        Route::get('/Pedagogos/Novo',[PedagogosController::class,'cadastro'])->name('Pedagogos/Novo')->middleware('secretario');
        Route::get('/Pedagogos/Cadastro/{id}',[PedagogosController::class,'cadastro'])->name('Pedagogos/Edit')->middleware('secretario');
        Route::post('/Pedagogos/Save',[PedagogosController::class,'save'])->name('Pedagogos/Save')->middleware('secretario');
        //PAIS
        Route::get('/Responsaveis',[ResponsaveisController::class,'index'])->name('Responsaveis/index')->middleware(['coordenador','diretor']);
        Route::get('/Responsaveis/Novo',[ResponsaveisController::class,'cadastro'])->name('Responsaveis/Novo')->middleware(['coordenador','diretor']);
        Route::get('/Responsaveis/Cadastro/{id}',[ResponsaveisController::class,'cadastro'])->name('Responsaveis/Edit')->middleware(['coordenador','diretor']);
        Route::post('/Responsaveis/Save',[ResponsaveisController::class,'save'])->name('Responsaveis/Save')->middleware(['coordenador','diretor']);
        //ALUNOS
        Route::get('/Coordenadores',[SecretariosController::class,'index'])->name('Coordenadores/index')->middleware('secretario');
        Route::get('/Coordenadores/Novo',[SecretariosController::class,'cadastro'])->name('Coordenadores/Novo')->middleware('secretario');
        Route::get('/Coordenadores/Cadastro/{id}',[SecretariosController::class,'cadastro'])->name('Coordenadores/Edit')->middleware('secretario');
        Route::post('/Coordenadores/Save',[SecretariosController::class,'save'])->name('Coordenadores/Save');
        //AUXILIARES
        Route::get('/Auxiliares',[AuxiliaresController::class,'index'])->name('Auxiliares/index')->middleware(['secretario','diretor']);
        Route::get('/Auxiliares/Novo',[AuxiliaresController::class,'cadastro'])->name('Auxiliares/Novo')->middleware(['secretario','diretor']);
        Route::get('/Auxiliares/Cadastro/{id}',[AuxiliaresController::class,'cadastro'])->name('Auxiliares/Edit')->middleware(['secretario','diretor']);
        Route::post('/Auxiliares/Save',[AuxiliaresController::class,'save'])->name('Auxiliares/Save')->middleware(['secretario','diretor']);
        //APOIO
        Route::get('/Apoio',[ApoioController::class,'index'])->name('Apoio/index')->middleware(['secretario','diretor']);
        Route::get('/Apoio/Novo',[ApoioController::class,'cadastro'])->name('Apoio/Novo')->middleware(['secretario','diretor']);
        Route::get('/Apoio/Cadastro/{id}',[ApoioController::class,'cadastro'])->name('Apoio/Edit')->middleware(['secretario','diretor']);
        Route::post('/Apoio/Save',[ApoioController::class,'save'])->name('Apoio/Save')->middleware(['secretario','diretor']);
        //CALENDARIO
        Route::get('/Calendario',[CalendarioController::class,'index'])->name('Calendario/index')->middleware('secretario');
        Route::get('/Calendario/Novo',[CalendarioController::class,'cadastro'])->name('Calendario/Novo')->middleware('secretario');
        Route::get('/Calendario/Cadastro/{id}',[CalendarioController::class,'cadastro'])->name('Calendario/Edit')->middleware('secretario');
        Route::post('/Calendario/Save',[CalendarioController::class,'save'])->name('Calendario/Save')->middleware('secretario');
        //
        Route::get('/Calendario/Alunos/Ferias',[CalendarioController::class,'feriasAlunosIndex'])->name('Calendario/Alunos/Ferias/index')->middleware('secretario');
        Route::get('/Calendario/Alunos/Ferias/Novo',[CalendarioController::class,'feriasAlunosCadastro'])->name('Ferias/Alunos/Novo')->middleware('secretario');
        Route::get('/Calendario/Alunos/Ferias/Cadastro/{id}',[CalendarioController::class,'feriasAlunosCadastro'])->name('Calendario/Alunos/Ferias/Edit')->middleware('secretario');
        Route::post('/Calendario/Alunos/Ferias/Save',[CalendarioController::class,'feriasAlunosSave'])->name('Calendario/Alunos/Ferias/Save')->middleware('secretario');
        //
        Route::get('/Calendario/Profissionais/Ferias',[CalendarioController::class,'feriasProfissionaisIndex'])->name('Calendario/Profissionais/Ferias/index')->middleware('secretario');
        Route::get('/Calendario/Profissionais/Ferias/Novo',[CalendarioController::class,'feriasProfissionaisCadastro'])->name('Ferias/Profissionais/Novo')->middleware('secretario');
        Route::get('/Calendario/Profissionais/Ferias/Cadastro/{id}',[CalendarioController::class,'feriasProfissionaisCadastro'])->name('Calendario/Profissionais/Ferias/Edit')->middleware('secretario');
        Route::post('/Calendario/Profissionais/Ferias/Save',[CalendarioController::class,'feriasProfissionaisSave'])->name('Calendario/Profissionais/Ferias/Save')->middleware('secretario');
        //
        Route::get('/Calendario/Reunioes',[CalendarioController::class,'reunioesIndex'])->name('Calendario/Reunioes/index')->middleware(['diretor','secretario']);
        Route::get('/Calendario/Reunioes/Novo',[CalendarioController::class,'reunioesCadastro'])->name('Ferias/Alunos/Novo')->middleware('secretario');
        Route::get('/Calendario/Reunioes/Cadastro/{id}',[CalendarioController::class,'reunioesCadastro'])->name('Calendario/Reunioes/Edit')->middleware(['diretor','secretario']);
        Route::post('/Calendario/Reunioes/Save',[CalendarioController::class,'reunioesSave'])->name('Calendario/Reunioes/Save')->middleware('secretario');
        //
        Route::get('/Cardapio',[CardapioController::class,'index'])->name('Cardapio/index')->middleware(['diretor','secretario']);
        Route::get('/Cardapio/Novo',[CardapioController::class,'cadastro'])->name('Cardapio/Novo')->middleware('diretor');
        Route::get('/Cardapio/Cadastro/{id}',[CardapioController::class,'cadastro'])->name('Cardapio/Edit')->middleware(['diretor','secretario']);
        Route::post('/Cardapio/Save',[CardapioController::class,'save'])->name('Cardapio/Save')->middleware(['diretor','secretario']);
    //});
    //
    
    //PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //
});

require __DIR__.'/auth.php';
