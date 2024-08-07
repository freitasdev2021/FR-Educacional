<?php

use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecretariasController;
use App\Http\Controllers\SecretariosController;
use App\Http\Controllers\AulasController;
use App\Http\Controllers\OcorrenciasController;
use App\Http\Controllers\PlanejamentosController;
use App\Http\Controllers\EscolasController;
use App\Http\Controllers\TurmasController;
use App\Http\Controllers\DiretoresController;
use App\Http\Controllers\ProfessoresController;
use App\Http\Controllers\CardapioController;
use App\Http\Controllers\PedagogosController;
use App\Http\Controllers\ResponsaveisController;
use App\Http\Controllers\AlunosController;
use App\Http\Controllers\AuxiliaresController;
use App\Http\Controllers\TransporteController;
use App\Http\Controllers\ApoioController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/',[DashboardController::class,'index'])->name('dashboard'); //DASHBOARD COMUM A TODOS
    //CAMADA DE SEGURANÇA FORNECEDOR DO SISTEMA
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
    //CAMADA DE SEGURANÇA SECRETARIO E DIRETOR
    Route::middleware('secretarioDiretor')->group(function () {
        //ESCOLAS
        Route::get('/Escolas/list',[EscolasController::class,'getEscolas'])->name('Escolas/list');
        Route::get('/Escolas',[EscolasController::class,'index'])->name('Escolas/index');
        Route::get('/Escolas/Novo',[EscolasController::class,'cadastro'])->name('Escolas/Novo')->middleware('secretario');
        Route::get('/Escolas/Cadastro/{id}',[EscolasController::class,'cadastro'])->name('Escolas/Edit')->middleware('secretario');
        Route::post('/Escolas/Save',[EscolasController::class,'save'])->name('Escolas/Save')->middleware('secretario');
        //DISCIPLINAS
        Route::get('/Escolas/Disciplinas/list',[EscolasController::class,'getDisciplinas'])->name('Escolas/Disciplinas/list');
        Route::get('/Escolas/Disciplinas',[EscolasController::class,'Disciplinas'])->name('Escolas/Disciplinas');
        Route::get('/Escolas/Disciplinas/Novo',[EscolasController::class,'cadastroDisciplinas'])->name('Escolas/Disciplinas/Novo')->middleware(['secretario']);
        Route::get('/Escolas/Disciplinas/Edit/{id}',[EscolasController::class,'cadastroDisciplinas'])->name('Escolas/Disciplinas/Cadastro')->middleware(['secretario']);
        Route::post('/Escolas/Disciplinas/Save',[EscolasController::class,'saveDisciplinas'])->name('Escolas/Disciplinas/Save')->middleware(['secretario']);
        Route::get('/Escolas/Disciplinas/Get/{IDEscola}',[EscolasController::class,'getDisciplinasEscola'])->name('Escolas/Disciplinas/Get')->middleware(['secretario']);
        Route::post('/Escolas/Anosletivos/Save',[EscolasController::class,'saveAnosLetivos'])->name('Escolas/Anosletivos/Save');
        //TURMAS
        Route::get('/Escolas/Turmas/Novo',[EscolasController::class,'cadastroTurmas'])->name('Escolas/Turmas/Novo');
        Route::get('/Escolas/Turmas/Edit/{id}',[EscolasController::class,'cadastroTurmas'])->name('Escolas/Turmas/Cadastro');
        Route::post('/Escolas/Turmas/Save',[EscolasController::class,'saveTurmas'])->name('Escolas/Turmas/Save');
        //PROFESSORES
        Route::get('/Professores/list',[ProfessoresController::class,'getProfessores'])->name('Professores/list');
        Route::get('/Professores',[ProfessoresController::class,'index'])->name('Professores/index');
        Route::get('/Professores/Cadastro/{id}',[ProfessoresController::class,'cadastro'])->name('Professores/Edit');
        //TURNOS
        Route::get('/Professores/Turnos/list/{idprofessor}',[ProfessoresController::class,'getTurnosProfessor'])->name('Professores/Turnos/list');
        Route::get('/Professores/Turnos/{idprofessor}',[ProfessoresController::class,'Turnos'])->name('Professores/Turnos');
        Route::get('/Professores/{idprofessor}/Turnos/Novo',[ProfessoresController::class,'cadastroTurnoProfessor'])->name('Professores/Turnos/Novo');
        Route::get('/Professores/{idprofessor}/Turnos/Cadastro/{id}',[ProfessoresController::class,'cadastroTurnoProfessor'])->name('Professores/Turnos/Edit');
        Route::post('/Professores/Turnos/Save',[ProfessoresController::class,'saveTurno'])->name('Professores/Turnos/Save');
        //PEDAGOGOS
        Route::get('/Pedagogos/list',[PedagogosController::class,'getPedagogos'])->name('Pedagogos/list');
        Route::get('/Pedagogos',[PedagogosController::class,'index'])->name('Pedagogos/index');
        Route::get('/Pedagogos/Cadastro/{id}',[PedagogosController::class,'cadastro'])->name('Pedagogos/Edit');
        //AUXILIARES
        Route::get('/Auxiliares/list',[AuxiliaresController::class,'getAuxiliares'])->name('Auxiliares/list');
        Route::get('/Auxiliares',[AuxiliaresController::class,'index'])->name('Auxiliares/index');
        Route::get('/Auxiliares/Novo',[AuxiliaresController::class,'cadastro'])->name('Auxiliares/Novo');
        Route::get('/Auxiliares/Cadastro/{id}',[AuxiliaresController::class,'cadastro'])->name('Auxiliares/Edit');
        Route::post('/Auxiliares/Save',[AuxiliaresController::class,'save'])->name('Auxiliares/Save');
    });
    //CAMADA DE SEGURANÇA SECRETARIO E PROFESSOR
    Route::middleware('secretarioProfessor')->group(function(){
        Route::get('/Professores/DisciplinasProfessor/{IDTurma}',[ProfessoresController::class,'getDisciplinasTurmaProfessor'])->name('Professores/DisciplinasProfessor');
        Route::get('/Turmas/Desempenho/{IDTurma}',[TurmasController::class,'desempenho'])->name('Turmas/Desempenho');
        Route::get('/Turmas/Desempenho/list/{IDTurma}',[TurmasController::class,'getDesempenho'])->name('Turmas/Desempenho/list');
    });
    //CAMADA DE SEGURANÇA, TIME EDUCACIONAL COMPLETO
    Route::middleware('time')->group(function(){
        //MATRICULA
        Route::get('/Alunos/list',[AlunosController::class,'getAlunos'])->name('Alunos/list');
        Route::get('/Alunos',[AlunosController::class,'index'])->name('Alunos/index');
        Route::get('/Alunos/Transferidos',[AlunosController::class,'transferidos'])->name('Alunos/Transferidos')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Transferidos/Transferido/{id}',[AlunosController::class,'matriculaTransferidos'])->name('Alunos/Transferidos/Transferido');
        Route::get('/Alunos/Transferidos/list',[AlunosController::class,'getTransferidos'])->name('Alunos/Transferidos/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Novo',[AlunosController::class,'cadastro'])->name('Alunos/Novo')->middleware('auxiliar');
        Route::get('/Alunos/Cadastro/{id}',[AlunosController::class,'cadastro'])->name('Alunos/Edit');
        Route::post('/Alunos/Save',[AlunosController::class,'save'])->name('Alunos/Save')->middleware(['auxiliar']);
        Route::post('/Alunos/Renovar',[AlunosController::class,'renovar'])->name('Alunos/Renovar')->middleware(['auxiliar']);
        Route::post('/Alunos/Transferidos/Matricular',[AlunosController::class,'matricularTransferido'])->name('Alunos/Transferidos/Matricular')->middleware('auxiliar');
        //DADOS DO ALUNO
        Route::get('/Alunos/Historico/{id}',[AlunosController::class,'historico'])->name('Alunos/Historico')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Boletim/{id}',[AlunosController::class,'boletim'])->name('Alunos/Boletim')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Suspenso/{id}',[AlunosController::class,'suspenso'])->name('Alunos/Suspenso')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Alunos/Transferencias/Cancela',[AlunosController::class,'cancelaTransferencia'])->name('Alunos/Transferencias/Cancela')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Alunos/Suspenso/Save',[AlunosController::class,'saveSuspenso'])->name('Alunos/Suspenso/Save')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Alunos/Suspenso/Remove',[AlunosController::class,'removerSuspensao'])->name('Alunos/Suspenso/Remove')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Atividades/{id}',[AlunosController::class,'atividades'])->name('Alunos/Atividades');
        Route::get('/Alunos/Ficha/{id}',[AlunosController::class,'ficha'])->name('Alunos/Ficha');
        Route::get('/Alunos/Desempenho/list/{id}',[AlunosController::class,'getAtividadesAluno'])->name('Alunos/Desempenho/list');
        //TRANSFERENCIAS DO ALUNO
        Route::get('/Alunos/Transferencias/list/{id}',[AlunosController::class,'getTransferencias'])->name('Alunos/Transferencias/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Transferencias/{id}',[AlunosController::class,'transferencias'])->name('Alunos/Transferencias')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Transferencias/Cadastro/{IDAluno}',[AlunosController::class,'cadastroTransferencias'])->name('Alunos/Transferencias/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Alunos/Transferencias/Save',[AlunosController::class,'saveTransferencias'])->name('Alunos/Transferencias/Save')->middleware(['diretor','secretario','auxiliar']);
        //SITUAÇÃO DO ALUNO
        Route::get('/Alunos/Situacao/list/{id}',[AlunosController::class,'getSituacao'])->name('Alunos/Situacao/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Situacao/{id}',[AlunosController::class,'situacao'])->name('Alunos/Situacao')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Alunos/Situacao/Cadastro/{IDAluno}',[AlunosController::class,'cadastroSituacao'])->name('Alunos/Situacao/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Alunos/Situacao/Save',[AlunosController::class,'saveSituacao'])->name('Alunos/Situacao/Save')->middleware(['diretor','secretario','auxiliar']);
        //PLANEJAMENTOS
        Route::get('/Planejamentos/list',[PlanejamentosController::class,'getPlanejamentos'])->name('Planejamentos/list')->middleware('professor');
        Route::get('/Planejamentos',[PlanejamentosController::class,'index'])->name('Planejamentos/index')->middleware('professor');
        Route::get('/Planejamentos/{id}/Componentes',[PlanejamentosController::class,'componentes'])->name('Planejamentos/Componentes')->middleware('professor');
        Route::get('/Planejamentos/Novo',[PlanejamentosController::class,'cadastro'])->name('Planejamentos/Novo')->middleware('professor');
        Route::get('/Planejamentos/Cadastro/{id}',[PlanejamentosController::class,'cadastro'])->name('Planejamentos/Cadastro')->middleware('professor');
        Route::get('/Planejamentos/getConteudo/{IDDisciplina}',[PlanejamentosController::class,'getPlanejamentoByTurma'])->name('Planejamentos/getConteudo')->middleware('professor');
        Route::post('/Planejamentos/Save',[PlanejamentosController::class,'save'])->name('Planejamentos/Save')->middleware('professor');
        Route::post('/Planejamentos/Componentes/Save',[PlanejamentosController::class,'saveComponentes'])->name('Planejamentos/Componentes/Save')->middleware('professor');
        //AULAS
        Route::get('/Aulas/Presenca/{IDAula}',[AulasController::class,'chamada'])->name('Aulas/Presenca')->middleware('professor');
        Route::get('/Aulas/Presenca/list/{IDAula}',[AulasController::class,'getAulaPresenca'])->name('Aulas/Presenca/list')->middleware('professor');
        Route::post('/Aulas/setPresenca',[AulasController::class,'setPresenca'])->name('Aulas/setPresenca')->middleware('professor');
        Route::get('/Aulas/list',[AulasController::class,'getAulas'])->name('Aulas/list')->middleware('professor');
        Route::get('/Aulas',[AulasController::class,'index'])->name('Aulas/index')->middleware('professor');
        Route::get('/Aulas/Novo',[AulasController::class,'cadastro'])->name('Aulas/Novo')->middleware('professor');
        Route::get('/Aulas/Cadastro/{id}',[AulasController::class,'cadastro'])->name('Aulas/Edit')->middleware('professor');
        Route::get('/Aulas/Chamada/{id}',[AulasController::class,'chamada'])->name('Aulas/Chamada')->middleware('professor');
        Route::post('/Aulas/Save',[AulasController::class,'save'])->name('Aulas/Save');
        Route::post('/Aulas/getAlunos',[AulasController::class,'getAulaAlunos'])->name('Aulas/getAlunos')->middleware('professor');
        //ATIVIDADES
        Route::get('/Aulas/Atividades/list',[AulasController::class,'getAtividades'])->name('Aulas/Atividades/list')->middleware('professor');
        Route::get('/Aulas/Atividades',[AulasController::class,'atividades'])->name('Aulas/Atividades/index');
        Route::get('/Aulas/Atividades/Novo',[AulasController::class,'cadastroAtividades'])->name('Aulas/Atividades/Novo')->middleware('professor');
        Route::get('/Aulas/Atividades/Cadastro/{id}',[AulasController::class,'cadastroAtividades'])->name('Aulas/Atividades/Edit')->middleware('professor');
        Route::get('/Aulas/Atividades/Correcao/{id}',[AulasController::class,'correcaoAtividades'])->name('Aulas/Atividades/Correcao')->middleware('professor');
        Route::post('/Aulas/Atividades/Save',[AulasController::class,'saveAtividades'])->name('Aulas/Atividades/Save')->middleware('professor');
        Route::post('/Aulas/Atividades/setNota',[AulasController::class,'setNota'])->name('Aulas/Atividades/setNota')->middleware('professor');
        //OCORRENCIAS
        Route::get('/Ocorrencias/list',[OcorrenciasController::class,'getOcorrencias'])->name('Ocorrencias/list')->middleware('professor');
        Route::get('/Ocorrencias',[OcorrenciasController::class,'index'])->name('Ocorrencias/index')->middleware('professor');
        Route::get('/Ocorrencias/Novo',[OcorrenciasController::class,'cadastro'])->name('Ocorrencias/Novo')->middleware('professor');
        Route::get('/Ocorrencias/Cadastro/{id}',[OcorrenciasController::class,'cadastro'])->name('Ocorrencias/Edit')->middleware('professor');
        Route::post('/Ocorrencias/Save',[OcorrenciasController::class,'save'])->name('Ocorrencias/Save')->middleware('professor');
        //TURMAS
        Route::get('/Escolas/Turmas/{IDDisciplina}/getTurmasDisciplina/{TPRetorno}',[EscolasController::class,'getTurmasDisciplinas']);
        Route::get('/Escolas/Turmas/list',[EscolasController::class,'getTurmas'])->name('Escolas/Turmas/list');
        Route::get('/Escolas/Turmas',[EscolasController::class,'Turmas'])->name('Escolas/Turmas');
        Route::get('Turmas',[TurmasController::class,'index'])->name('Turmas/index');
        //PAIS
        Route::get('/Responsaveis',[ResponsaveisController::class,'index'])->name('Responsaveis/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Responsaveis/Novo',[ResponsaveisController::class,'cadastro'])->name('Responsaveis/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Responsaveis/Cadastro/{id}',[ResponsaveisController::class,'cadastro'])->name('Responsaveis/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Responsaveis/Save',[ResponsaveisController::class,'save'])->name('Responsaveis/Save')->middleware(['diretor','secretario','auxiliar']);
        //CALENDARIO
        Route::get('/Calendario',[CalendarioController::class,'index'])->name('Calendario/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Eventos/list',[CalendarioController::class,'getEventos'])->name('Calendario/Eventos/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Eventos',[CalendarioController::class,'eventosIndex'])->name('Calendario/Eventos')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Eventos/Novo',[CalendarioController::class,'eventosCadastro'])->name('Calendario/Eventos/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Eventos/Cadastro/{id}',[CalendarioController::class,'eventosCadastro'])->name('Calendario/Eventos/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Calendario/Eventos/Save',[CalendarioController::class,'saveEvento'])->name('Calendario/Eventos/Save')->middleware(['diretor','secretario','auxiliar']);
        //
        Route::get('/Calendario/Alunos/Ferias/list',[CalendarioController::class,'getFeriasAlunos'])->name('Calendario/FeriasAlunos/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Alunos/Ferias',[CalendarioController::class,'feriasAlunosIndex'])->name('Calendario/FeriasAlunos')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Alunos/Ferias/Novo',[CalendarioController::class,'cadastroFeriasAlunos'])->name('Calendario/FeriasAlunos/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Alunos/Ferias/Cadastro/{id}',[CalendarioController::class,'cadastroFeriasAlunos'])->name('Calendario/FeriasAlunos/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Calendario/Alunos/Ferias/Save',[CalendarioController::class,'saveFeriasAlunos'])->name('Calendario/Alunos/Ferias/Save')->middleware(['diretor','secretario','auxiliar']);
        //
        Route::get('/Calendario/Profissionais/Ferias/list',[CalendarioController::class,'getFeriasProfissionais'])->name('Calendario/FeriasProfissionais/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Profissionais/Ferias',[CalendarioController::class,'feriasProfissionaisIndex'])->name('Calendario/FeriasProfissionais')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Profissionais/Ferias/Novo',[CalendarioController::class,'cadastroFeriasProfissionais'])->name('Calendario/FeriasProfissionais/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Profissionais/Ferias/Cadastro/{id}',[CalendarioController::class,'cadastroFeriasProfissionais'])->name('Calendario/FeriasProfissionais/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Calendario/Profissionais/Ferias/Save',[CalendarioController::class,'saveFeriasProfissionais'])->name('Calendario/Profissionais/Ferias/Save')->middleware(['diretor','secretario','auxiliar']);
        //
        Route::get('/Calendario/Sabados/list',[CalendarioController::class,'getSabados'])->name('Calendario/Sabados/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Sabados',[CalendarioController::class,'sabadosIndex'])->name('Calendario/Sabados')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Sabados/Novo',[CalendarioController::class,'cadastroSabados'])->name('Calendario/Sabados/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Sabados/Cadastro/{id}',[CalendarioController::class,'cadastroSabados'])->name('Calendario/Sabados/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Calendario/Sabados/Save',[CalendarioController::class,'saveSabados'])->name('Calendario/Sabados/Save')->middleware(['diretor','secretario','auxiliar']);
        //
        Route::get('/Calendario/Paralizacoes/list',[CalendarioController::class,'getParalizacoes'])->name('Calendario/Paralizacoes/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Paralizacoes',[CalendarioController::class,'paralizacoesIndex'])->name('Calendario/Paralizacoes')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Paralizacoes/Novo',[CalendarioController::class,'cadastroParalizacao'])->name('Calendario/Paralizacoes/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Calendario/Paralizacoes/Cadastro/{id}',[CalendarioController::class,'cadastroParalizacao'])->name('Calendario/Paralizacoes/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Calendario/Paralizacoes/Save',[CalendarioController::class,'saveParalizacao'])->name('Calendario/Paralizacoes/Save')->middleware(['diretor','secretario','auxiliar']);
        //TRANSPORTE
        Route::get('/Transporte/list',[TransporteController::class,'getRotas'])->name('Transporte/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte',[TransporteController::class,'index'])->name('Transporte/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Novo',[TransporteController::class,'cadastro'])->name('Transporte/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Cadastro/{id}',[TransporteController::class,'cadastro'])->name('Transporte/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Transporte/Save',[TransporteController::class,'save'])->name('Transporte/Save')->middleware(['diretor','secretario','auxiliar']);
        //paradas
        Route::get('/Transporte/Paradas/list/{idrota}',[TransporteController::class,'getParadas'])->name('Transporte/Paradas/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/{idrota}/Paradas',[TransporteController::class,'paradas'])->name('Transporte/Paradas')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/{idrota}/Paradas/Novo',[TransporteController::class,'cadastroParadas'])->name('Transporte/Paradas/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/{idrota}/Paradas/Cadastro/{id}',[TransporteController::class,'cadastroParadas'])->name('Transporte/Paradas/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Transporte/Paradas/Save',[TransporteController::class,'saveParadas'])->name('Transporte/Paradas/Save')->middleware(['diretor','secretario','auxiliar']);
        //rodagem
        Route::get('/Transporte/Rodagem/list/{idrota}',[TransporteController::class,'getRodagem'])->name('Transporte/Rodagem/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/{idrota}/Rodagem',[TransporteController::class,'rodagem'])->name('Transporte/Rodagem')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/{idrota}/Rodagem/Novo',[TransporteController::class,'cadastroRodagem'])->name('Transporte/Rodagem/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Transporte/Rodagem/Save',[TransporteController::class,'saveRodagem'])->name('Transporte/Rodagem/Save')->middleware(['diretor','secretario','auxiliar']);
        //veiculos
        Route::get('/Transporte/Veiculos/list',[TransporteController::class,'getVeiculos'])->name('Transporte/Veiculos/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Veiculos',[TransporteController::class,'veiculos'])->name('Transporte/Veiculos/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Veiculos/Novo',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Veiculos/Cadastro/{id}',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Transporte/Veiculos/Save',[TransporteController::class,'saveVeiculos'])->name('Transporte/Veiculos/Save')->middleware(['diretor','secretario','auxiliar']);
        //motoristas
        Route::get('/Transporte/Motoristas/list',[TransporteController::class,'getMotoristas'])->name('Transporte/Motoristas/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Motoristas',[TransporteController::class,'motoristas'])->name('Transporte/Motoristas/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Motoristas/Novo',[TransporteController::class,'cadastroMotoristas'])->name('Transporte/Motoristas/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Motoristas/Cadastro/{id}',[TransporteController::class,'cadastroMotoristas'])->name('Transporte/Motoristas/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Transporte/Motoristas/Save',[TransporteController::class,'saveMotoristas'])->name('Transporte/Motoristas/Save')->middleware(['diretor','secretario','auxiliar']);
        //terceirizadas
        Route::get('/Transporte/Terceirizadas/list',[TransporteController::class,'getTerceirizadas'])->name('Transporte/Terceirizadas/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Terceirizadas',[TransporteController::class,'terceirizadas'])->name('Transporte/Terceirizadas/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Terceirizadas/Novo',[TransporteController::class,'cadastroTerceirizadas'])->name('Transporte/Terceirizadas/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Terceirizadas/Cadastro/{id}',[TransporteController::class,'cadastroTerceirizadas'])->name('Transporte/Terceirizadas/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Transporte/Terceirizadas/Save',[TransporteController::class,'saveTerceirizadas'])->name('Transporte/Terceirizadas/Save')->middleware(['diretor','secretario','auxiliar']);
        //
        Route::get('/Transporte/Veiculos/list',[TransporteController::class,'getVeiculos'])->name('Transporte/Veiculos/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Veiculos',[TransporteController::class,'veiculos'])->name('Transporte/Veiculos/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Veiculos/Novo',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Transporte/Veiculos/Cadastro/{id}',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Transporte/Veiculos/Save',[TransporteController::class,'saveVeiculos'])->name('Transporte/Veiculos/Save')->middleware(['diretor','secretario','auxiliar']);
        //MERENDA
        Route::get('/Merenda/list',[CardapioController::class,'getMerenda'])->name('Merenda/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda',[CardapioController::class,'index'])->name('Merenda/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda/Novo',[CardapioController::class,'cadastro'])->name('Merenda/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda/Cadastro/{id}',[CardapioController::class,'cadastro'])->name('Merenda/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Merenda/Save',[CardapioController::class,'save'])->name('Merenda/Save')->middleware(['diretor','secretario','auxiliar']);
        //ESTOQUE
        Route::get('/Merenda/Estoque/list',[CardapioController::class,'getEstoque'])->name('Merenda/Estoque/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda/Estoque',[CardapioController::class,'estoque'])->name('Merenda/Estoque/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda/Estoque/Novo',[CardapioController::class,'cadastroEstoque'])->name('Merenda/Estoque/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda/Estoque/Cadastro/{id}',[CardapioController::class,'cadastroEstoque'])->name('Merenda/Estoque/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Merenda/Estoque/Save',[CardapioController::class,'saveEstoque'])->name('Merenda/Estoque/Save')->middleware(['diretor','secretario','auxiliar']);
        //MOVIMENTACOES
        Route::get('/Merenda/Movimentacoes/list',[CardapioController::class,'getMovimentacoes'])->name('Merenda/Movimentacoes/list')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda/Movimentacoes',[CardapioController::class,'movimentacao'])->name('Merenda/Movimentacoes/index')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda/Movimentacoes/Novo',[CardapioController::class,'cadastroMovimentacoes'])->name('Merenda/Movimentacoes/Novo')->middleware(['diretor','secretario','auxiliar']);
        Route::get('/Merenda/Movimentacoes/Cadastro/{id}',[CardapioController::class,'cadastroMovimentacoes'])->name('Merenda/Movimentacoes/Edit')->middleware(['diretor','secretario','auxiliar']);
        Route::post('/Merenda/Movimentacoes/Save',[CardapioController::class,'saveMovimentacoes'])->name('Merenda/Movimentacoes/Save')->middleware(['diretor','secretario','auxiliar']);
        //
    });
    //CAMADA DE SEGURANÇA DO SECRETARIO
    Route::middleware('secretario')->group(function(){
        //DIRETORES
        Route::get('/Diretores/list',[DiretoresController::class,'getDiretores'])->name('Diretores/list');
        Route::get('/Diretores',[DiretoresController::class,'index'])->name('Diretores/index');
        Route::get('/Diretores/Novo',[DiretoresController::class,'cadastro'])->name('Diretores/Novo');
        Route::get('/Diretores/Cadastro/{id}',[DiretoresController::class,'cadastro'])->name('Diretores/Edit');
        Route::post('/Diretoress/Save',[DiretoresController::class,'save'])->name('Diretores/Save');
        //PROFESSORES
        Route::get('/Professores/Novo',[ProfessoresController::class,'cadastro'])->name('Professores/Novo');
        Route::post('/Professores/Save',[ProfessoresController::class,'save'])->name('Professores/Save');
        //PEDAGOGOS
        Route::get('/Pedagogos/Novo',[PedagogosController::class,'cadastro'])->name('Pedagogos/Novo');
        Route::post('/Pedagogos/Save',[PedagogosController::class,'save'])->name('Pedagogos/Save');
    });
    //CAMADA DE SEGURANÇA DO PROFESSOR
    Route::middleware('professor')->group(function(){
        Route::get('/Alunos/Recuperacao/{IDAluno}/{Estagio}',[AlunosController::class,'recuperacao'])->name('Alunos/Recuperacao');
    });
    //PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //
});

require __DIR__.'/auth.php';
