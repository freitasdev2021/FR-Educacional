<?php

use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecretariasController;
use App\Http\Controllers\VagasController;
use App\Http\Controllers\AulasController;
use App\Http\Controllers\OcorrenciasController;
use App\Http\Controllers\PlanejamentosController;
use App\Http\Controllers\EscolasController;
use App\Http\Controllers\RecuperacaoController;
use App\Http\Controllers\TurmasController;
use App\Http\Controllers\ApoioController;
use App\Http\Controllers\DiretoresController;
use App\Http\Controllers\DiarioController;
use App\Http\Controllers\RelatoriosController;
use App\Http\Controllers\ProfessoresController;
use App\Http\Controllers\CardapioController;
use App\Http\Controllers\PedagogosController;
use App\Http\Controllers\ResponsaveisController;
use App\Http\Controllers\AlunosController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\AuxiliaresController;
use App\Http\Controllers\TransporteController;
use App\Http\Controllers\EnderecosController;
use App\Http\Controllers\SalasController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth','STAcesso'])->group(function () {
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
        //ACESSOS
        Route::get("Acessos/Bloquear/{IDUser}/{STAcesso}",[ProfessoresController::class,'bloquearAcesso'])->name("Acessos/Bloquear");  
        //ESCOLAS
        Route::get('/Escolas/list',[EscolasController::class,'getEscolas'])->name('Escolas/list');
        Route::get('/Escolas',[EscolasController::class,'index'])->name('Escolas/index');
        Route::get('/Escolas/Novo',[EscolasController::class,'cadastro'])->name('Escolas/Novo')->middleware('secretario');
        Route::get('/Escolas/Cadastro/{id}',[EscolasController::class,'cadastro'])->name('Escolas/Edit')->middleware('secretario');
        Route::post('/Escolas/Save',[EscolasController::class,'save'])->name('Escolas/Save')->middleware('secretario');
        //PROFESSORES
        Route::get('/Professores/list',[ProfessoresController::class,'getProfessores'])->name('Professores/list');
        Route::get('/Professores',[ProfessoresController::class,'index'])->name('Professores/index');
        Route::get('/Professores/Cadastro/{id}',[ProfessoresController::class,'cadastro'])->name('Professores/Edit');
        //TURNOS
        Route::get('Professores/Turnos/Remove/{IDTurno}',[ProfessoresController::class,'removeTurno'])->name('Professores/Turnos/Remove');
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
        Route::get('Auxiliares/list',[AuxiliaresController::class,'getAuxiliares'])->name('Auxiliares/list');
        Route::get('Auxiliares',[AuxiliaresController::class,'index'])->name('Auxiliares/index');
        Route::get('Auxiliares/Novo',[AuxiliaresController::class,'cadastro'])->name('Auxiliares/Novo');
        Route::get('Auxiliares/Cadastro/{id}',[AuxiliaresController::class,'cadastro'])->name('Auxiliares/Edit');
        Route::post('Auxiliares/Save',[AuxiliaresController::class,'save'])->name('Auxiliares/Save');
        //OCORRENCIAS
        Route::post("Ocorrencias/Responder",[OcorrenciasController::class,'responder'])->name("Ocorrencias/Responder");
        //
    });
    //CAMADA DE SEGURANÇA SECRETARIO E PROFESSOR
    Route::middleware('secretarioProfessor')->group(function(){
        Route::get('/Professores/DisciplinasProfessor/{IDTurma}',[ProfessoresController::class,'getDisciplinasTurmaProfessor'])->name('Professores/DisciplinasProfessor');
    });
    //CAMADA DE SEGURANÇA, TIME EDUCACIONAL COMPLETO
    Route::middleware('time')->group(function(){
        //ANEXOS
        Route::get('Alunos/Anexos/{IDAluno}',[AlunosController::class,'anexos'])->name("Alunos/Anexos");
        Route::post('Alunos/Anexos/Save',[AlunosController::class,'saveAnexo'])->name("Alunos/Anexos/Save");
        //DIARIO
        Route::get("Aulas/Diario",[DiarioController::class,'index'])->name("Aulas/Diario/index");
        Route::post("Aulas/Diario/Comentar",[DiarioController::class,'comentar'])->name("Aulas/Diario/Comentar");
        Route::get("Aulas/Diario/Exportar/{Professor}/{Estagio}/{Data}/{Comentario}",[DiarioController::class,'exportar'])->name('Aulas/Diario/Exportar');
        //GET BOLETIM
        Route::get("Turmas/Boletins/{id}",[TurmasController::class,'gerarBoletins'])->name("Turmas/Boletins");
        Route::get('/Turmas/Desempenho/{IDTurma}',[TurmasController::class,'desempenho'])->name('Turmas/Desempenho');
        Route::get('/Turmas/Desempenho/list/{IDTurma}',[TurmasController::class,'getDesempenho'])->name('Turmas/Desempenho/list');
        //MATRICULA
        Route::get('/Alunos/list',[AlunosController::class,'getAlunos'])->name('Alunos/list');
        Route::get('/Alunos',[AlunosController::class,'index'])->name('Alunos/index');
        Route::get('/Alunos/Transferidos',[AlunosController::class,'transferidos'])->name('Alunos/Transferidos');
        Route::get('/Alunos/Transferidos/Transferido/{id}',[AlunosController::class,'matriculaTransferidos'])->name('Alunos/Transferidos/Transferido');
        Route::get('/Alunos/Transferidos/list',[AlunosController::class,'getTransferidos'])->name('Alunos/Transferidos/list');
        Route::get('/Alunos/Novo',[AlunosController::class,'cadastro'])->name('Alunos/Novo');
        Route::get('/Alunos/Cadastro/{id}',[AlunosController::class,'cadastro'])->name('Alunos/Edit');
        Route::post('/Alunos/Save',[AlunosController::class,'save'])->name('Alunos/Save');
        Route::post('/Alunos/Renovar',[AlunosController::class,'renovar'])->name('Alunos/Renovar');
        Route::post('/Alunos/Transferidos/Matricular',[AlunosController::class,'matricularTransferido'])->name('Alunos/Transferidos/Matricular');
        //FICHA AVALIATIVA
        Route::post('Fichas/Save',[FichaController::class,'save'])->name('Fichas/Save');
        Route::get('Fichas',[FichaController::class,'index'])->name('Fichas/index');
        Route::get('Fichas/list',[FichaController::class,'getFichas'])->name('Fichas/list');
        Route::get('Fichas/Cadastro',[FichaController::class,'cadastro'])->name('Fichas/Novo');
        Route::get('Fichas/Cadastro/{id}',[FichaController::class,'cadastro'])->name('Fichas/Edit');
        Route::get('Fichas/Respostas/{id}',[FichaController::class,'respostas'])->name('Fichas/Respostas');
        Route::get('Fichas/getRespostas/{id}',[FichaController::class,'getRespostas'])->name('Fichas/getRespostas');
        Route::get('Fichas/Respostas/Export/{id}', [FichaController::class, 'exportRespostas'])->name('Fichas/Respostas/Export');
        Route::get('Fichas/Visualizar/{id}',[FichaController::class,'visualizar'])->name('Fichas/Visualizar');
        Route::post('Fichas/Responder',[FichaController::class,'responder'])->name('Fichas/Responder');
        //DADOS DO ALUNO
        Route::get("Alunos/Comprovantes/Matricula/{id}",[AlunosController::class,'getComprovanteMatricula'])->name("Alunos/Comprovante/Matricula");
        Route::get("Alunos/Comprovantes/Frequencia/{id}",[AlunosController::class,'getComprovanteFrequencia'])->name("Alunos/Comprovante/Frequencia");
        Route::get("Alunos/Comprovantes/Filiacao/{id}",[AlunosController::class,'getRelatorioMatricula'])->name("Alunos/Comprovante/Filiacao");
        Route::get("Alunos/Comprovantes/Conclusao/{id}",[AlunosController::class,'getComprovanteConclusao'])->name("Alunos/Comprovante/Conclusao");
        Route::get("Alunos/Comprovantes/Transferencia/{id}",[AlunosController::class,'getDeclaracaoTransferencia'])->name("Alunos/Comprovante/Transferencia");
        Route::get('/Alunos/Historico/{id}',[AlunosController::class,'historico'])->name('Alunos/Historico');
        Route::get('/Alunos/Boletim/{id}',[AlunosController::class,'boletim'])->name('Alunos/Boletim');
        Route::get('/Alunos/Suspenso/{id}',[AlunosController::class,'suspenso'])->name('Alunos/Suspenso');
        Route::post('/Alunos/Transferencias/Cancela',[AlunosController::class,'cancelaTransferencia'])->name('Alunos/Transferencias/Cancela');
        Route::post('/Alunos/Suspenso/Save',[AlunosController::class,'saveSuspenso'])->name('Alunos/Suspenso/Save');
        Route::post('/Alunos/Suspenso/Remove',[AlunosController::class,'removerSuspensao'])->name('Alunos/Suspenso/Remove');
        Route::get('/Alunos/Atividades/{id}',[AlunosController::class,'atividades'])->name('Alunos/Atividades');
        Route::get('/Alunos/Ficha/{id}',[AlunosController::class,'ficha'])->name('Alunos/Ficha');
        Route::get('/Alunos/Desempenho/list/{id}',[AlunosController::class,'getAtividadesAluno'])->name('Alunos/Desempenho/list');
        //TRANSFERENCIAS DO ALUNO
        Route::get('/Alunos/Transferencias/list/{id}',[AlunosController::class,'getTransferencias'])->name('Alunos/Transferencias/list');
        Route::get('/Alunos/Transferencias/{id}',[AlunosController::class,'transferencias'])->name('Alunos/Transferencias');
        Route::get('/Alunos/Transferencias/Cadastro/{IDAluno}',[AlunosController::class,'cadastroTransferencias'])->name('Alunos/Transferencias/Novo');
        Route::post('/Alunos/Transferencias/Save',[AlunosController::class,'saveTransferencias'])->name('Alunos/Transferencias/Save');
        //SITUAÇÃO DO ALUNO
        Route::get('/Alunos/Situacao/list/{id}',[AlunosController::class,'getSituacao'])->name('Alunos/Situacao/list');
        Route::get('/Alunos/Situacao/{id}',[AlunosController::class,'situacao'])->name('Alunos/Situacao');
        Route::get('/Alunos/Situacao/Cadastro/{IDAluno}',[AlunosController::class,'cadastroSituacao'])->name('Alunos/Situacao/Novo');
        Route::post('/Alunos/Situacao/Save',[AlunosController::class,'saveSituacao'])->name('Alunos/Situacao/Save');
        //PLANEJAMENTOS
        Route::get('/Planejamentos/list',[PlanejamentosController::class,'getPlanejamentos'])->name('Planejamentos/list');
        Route::get('/Planejamentos',[PlanejamentosController::class,'index'])->name('Planejamentos/index');
        Route::get('/Planejamentos/{id}/Componentes',[PlanejamentosController::class,'componentes'])->name('Planejamentos/Componentes');
        Route::get('/Planejamentos/Novo',[PlanejamentosController::class,'cadastro'])->name('Planejamentos/Novo');
        Route::get('/Planejamentos/Cadastro/{id}',[PlanejamentosController::class,'cadastro'])->name('Planejamentos/Cadastro');
        Route::get('/Planejamentos/getConteudo/{IDDisciplina}/{IDTurma}/{TPAula}',[PlanejamentosController::class,'getPlanejamentoByTurma'])->name('Planejamentos/getConteudo');
        Route::post('/Planejamentos/Save',[PlanejamentosController::class,'save'])->name('Planejamentos/Save');
        Route::post('/Planejamentos/Componentes/Save',[PlanejamentosController::class,'saveComponentes'])->name('Planejamentos/Componentes/Save');
        //RECUPERAÇÃO
        Route::get('Aulas/Recuperacao',[RecuperacaoController::class,'index'])->name("Aulas/Recuperacao/index");
        Route::get('Aulas/Recuperacao/Novo',[RecuperacaoController::class,'cadastro'])->name("Aulas/Recuperacao/Novo");
        Route::get('Aulas/Recuperacao/Edit/{id}',[RecuperacaoController::class,'cadastro'])->name("Aulas/Recuperacao/Edit");
        Route::get('Aulas/Recuperacao/list',[RecuperacaoController::class,'getRecuperacoes'])->name("Aulas/Recuperacao/list");
        Route::post('Aulas/Recuperacao/Save',[RecuperacaoController::class,'save'])->name("Aulas/Recuperacao/Save");
        //AULAS
        Route::get('/Aulas/Presenca/{IDAula}',[AulasController::class,'chamada'])->name('Aulas/Presenca');
        Route::get('/Aulas/Presenca/list/{IDAula}',[AulasController::class,'getAulaPresenca'])->name('Aulas/Presenca/list');
        Route::post('/Aulas/setPresenca',[AulasController::class,'setPresenca'])->name('Aulas/setPresenca')->middleware('professor');
        Route::get('/Aulas/list',[AulasController::class,'getAulas'])->name('Aulas/list');
        Route::get('/Aulas',[AulasController::class,'index'])->name('Aulas/index');
        Route::get('/Aulas/Novo',[AulasController::class,'cadastro'])->name('Aulas/Novo')->middleware('professor');
        Route::get('/Aulas/Cadastro/{id}',[AulasController::class,'cadastro'])->name('Aulas/Edit');
        Route::get('/Aulas/Chamada/{id}',[AulasController::class,'chamada'])->name('Aulas/Chamada');
        Route::post('/Aulas/Save',[AulasController::class,'save'])->name('Aulas/Save');
        Route::post('/Aulas/getAlunos',[AulasController::class,'getAulaAlunos'])->name('Aulas/getAlunos');
        //ATIVIDADES
        Route::get('/Aulas/Atividades/list',[AulasController::class,'getAtividades'])->name('Aulas/Atividades/list');
        Route::get('/Aulas/Atividades',[AulasController::class,'atividades'])->name('Aulas/Atividades/index');
        Route::get('/Aulas/Atividades/Novo',[AulasController::class,'cadastroAtividades'])->name('Aulas/Atividades/Novo')->middleware('professor');
        Route::get('/Aulas/Atividades/Cadastro/{id}',[AulasController::class,'cadastroAtividades'])->name('Aulas/Atividades/Edit');
        Route::get('/Aulas/Atividades/Exclusao/{id}',[AulasController::class,'excluirAtividade'])->name('Aulas/Atividades/Exclusao')->middleware('professor');
        Route::get('/Aulas/Atividades/Correcao/{id}',[AulasController::class,'correcaoAtividades'])->name('Aulas/Atividades/Correcao');
        Route::post('/Aulas/Atividades/Save',[AulasController::class,'saveAtividades'])->name('Aulas/Atividades/Save')->middleware('professor');
        Route::post('/Aulas/Atividades/setNota',[AulasController::class,'setNota'])->name('Aulas/Atividades/setNota')->middleware('professor');
        //OCORRENCIAS
        Route::get('Ocorrencias/list',[OcorrenciasController::class,'getOcorrencias'])->name('Ocorrencias/list');
        Route::get('Ocorrencias',[OcorrenciasController::class,'index'])->name('Ocorrencias/index');
        Route::get('Ocorrencias/Novo',[OcorrenciasController::class,'cadastro'])->name('Ocorrencias/Novo');
        Route::get('Ocorrencias/Cadastro/{id}',[OcorrenciasController::class,'cadastro'])->name('Ocorrencias/Edit');
        Route::post('Ocorrencias/Save',[OcorrenciasController::class,'save'])->name('Ocorrencias/Save');
        //DISCIPLINAS
        Route::get('/Escolas/Disciplinas/list',[EscolasController::class,'getDisciplinas'])->name('Escolas/Disciplinas/list');
        Route::get('/Escolas/Disciplinas',[EscolasController::class,'Disciplinas'])->name('Escolas/Disciplinas');
        Route::get('/Escolas/Disciplinas/Novo',[EscolasController::class,'cadastroDisciplinas'])->name('Escolas/Disciplinas/Novo');
        Route::get('/Escolas/Disciplinas/Edit/{id}',[EscolasController::class,'cadastroDisciplinas'])->name('Escolas/Disciplinas/Cadastro');
        Route::post('/Escolas/Disciplinas/Save',[EscolasController::class,'saveDisciplinas'])->name('Escolas/Disciplinas/Save');
        Route::get('/Escolas/Disciplinas/Get/{IDEscola}',[EscolasController::class,'getDisciplinasEscola'])->name('Escolas/Disciplinas/Get');
        Route::post('/Escolas/Anosletivos/Save',[EscolasController::class,'saveAnosLetivos'])->name('Escolas/Anosletivos/Save');
        //IMPORTAR ALUNOS
        Route::patch('Alunos/Importar/{IDTurma}',[AlunosController::class,'importarAlunos'])->name('Alunos/Importar');
        //TURMAS
        Route::get('/Escolas/Turmas/Novo',[EscolasController::class,'cadastroTurmas'])->name('Escolas/Turmas/Novo');
        Route::get('/Escolas/Turmas/Edit/{id}',[EscolasController::class,'cadastroTurmas'])->name('Escolas/Turmas/Cadastro');
        Route::post('/Escolas/Turmas/Save',[EscolasController::class,'saveTurmas'])->name('Escolas/Turmas/Save');
        //TURMAS
        Route::get("Turmas/Ata/{IDTurma}",[TurmasController::class,'getAta'])->name("Turmas/Ata");
        Route::get('Turmas/Alunos/Exportar/{IDTurma}',[TurmasController::class,'exportaAlunosTurma'])->name('Turmas/Alunos/Exportar');
        Route::get('/Escolas/Turmas/{IDDisciplina}/getTurmasDisciplina/{TPRetorno}',[EscolasController::class,'getTurmasDisciplinas']);
        Route::get('/Escolas/Turmas/list',[EscolasController::class,'getTurmas'])->name('Escolas/Turmas/list');
        Route::get('/Escolas/Turmas',[EscolasController::class,'Turmas'])->name('Escolas/Turmas');
        Route::get('Turmas',[TurmasController::class,'index'])->name('Turmas/index');
        //SALAS
        Route::get('Escolas/Salas',[SalasController::class,'index'])->name('Escolas/Salas');
        Route::get('Escolas/Salas/Cadastro',[SalasController::class,'cadastro'])->name('Escolas/Salas/Novo');
        Route::get('Escolas/Salas/Cadastro/{id}',[SalasController::class,'cadastro'])->name('Escolas/Salas/Edit');
        Route::get('Escolas/Salas/list',[SalasController::class,'getSalas'])->name('Escolas/Salas/list');
        Route::post('Escolas/Salas/Save',[SalasController::class,'save'])->name('Escolas/Salas/Save');
        //PAIS
        Route::get('Responsaveis',[ResponsaveisController::class,'index'])->name('Responsaveis/index');
        //PROFESSORES
        Route::get("Apoio",[ApoioController::class,'index'])->name("Apoio/index");
        Route::get("Professores/Apoio/list/{IDProfessor}",[ApoioController::class,'getApoio'])->name("Professores/Apoio/list");
        Route::get("Professores/Apoio/{IDProfessor}",[ApoioController::class,'index'])->name("Professores/Apoio");
        Route::get("Professores/Apoio/evolucao/{id}",[ApoioController::class,'getEvolucao'])->name("Professores/Apoio/evolucao");
        Route::get("Professores/{IDProfessor}/Apoio/Novo/{id}",[ApoioController::class,'cadastro'])->name("Professores/Apoio/Novo/");
        Route::get("Professores/{IDProfessor}/Apoio/Edit/{id}",[ApoioController::class,'cadastro'])->name("Professores/Apoio/Edit");
        Route::post("Professores/Apoio/Save",[ApoioController::class,'save'])->name("Professores/Apoio/Save");
        Route::post("Professores/Apoio/NovaEvolucao",[ApoioController::class,'saveEvolucao'])->name("Professores/Apoio/NovaEvolucao");
        //
        //RELATORIOS
        Route::get('Escolas/Relatorios',[EscolasController::class,'relatorios'])->name('Escolas/Relatorios');
        Route::get('Escolas/Relatorios/Imprimir/{Tipo}',[RelatoriosController::class,'imprimir'])->name('Escolas/Relatorios/Imprimir');
        Route::put('Escolas/Relatorios/Gerar/{Tipo}',[RelatoriosController::class,'Gerar'])->name('Escolas/Relatorios/Gerar');
        //VAGAS
        Route::get('Escolas/Vagas',[VagasController::class,'index'])->name('Escolas/Vagas');
        Route::get('Escolas/Vagas/Cadastro',[VagasController::class,'cadastro'])->name('Escolas/Vagas/Novo');
        Route::get('Escolas/Vagas/Cadastro/{IDVaga}',[VagasController::class,'cadastro'])->name('Escolas/Vagas/Edit');
        Route::get('Escolas/Vagas/list',[VagasController::class,'getVagas'])->name('Escolas/Vagas/list');
        Route::post('Escolas/Vagas/Save',[VagasController::class,'save'])->name('Escolas/Vagas/Save');
        //CALENDARIO
        Route::post('Calendario/Rematricula',[CalendarioController::class,'rematricula'])->name('Calendario/Rematricula');
        Route::get('/Calendario',[CalendarioController::class,'index'])->name('Calendario/index');
        Route::get('/Calendario/Eventos/list',[CalendarioController::class,'getEventos'])->name('Calendario/Eventos/list');
        Route::get('/Calendario/Eventos',[CalendarioController::class,'eventosIndex'])->name('Calendario/Eventos');
        Route::get('/Calendario/Eventos/Novo',[CalendarioController::class,'eventosCadastro'])->name('Calendario/Eventos/Novo');
        Route::get('/Calendario/Eventos/Cadastro/{id}',[CalendarioController::class,'eventosCadastro'])->name('Calendario/Eventos/Edit');
        Route::post('/Calendario/Eventos/Save',[CalendarioController::class,'saveEvento'])->name('Calendario/Eventos/Save');
        //
        Route::get('/Calendario/Alunos/Ferias/list',[CalendarioController::class,'getFeriasAlunos'])->name('Calendario/FeriasAlunos/list');
        Route::get('/Calendario/Alunos/Ferias',[CalendarioController::class,'feriasAlunosIndex'])->name('Calendario/FeriasAlunos');
        Route::get('/Calendario/Alunos/Ferias/Novo',[CalendarioController::class,'cadastroFeriasAlunos'])->name('Calendario/FeriasAlunos/Novo');
        Route::get('/Calendario/Alunos/Ferias/Cadastro/{id}',[CalendarioController::class,'cadastroFeriasAlunos'])->name('Calendario/FeriasAlunos/Edit');
        Route::post('/Calendario/Alunos/Ferias/Save',[CalendarioController::class,'saveFeriasAlunos'])->name('Calendario/Alunos/Ferias/Save');
        //
        Route::get('/Calendario/Alunos/Periodos/list',[CalendarioController::class,'getPeriodos'])->name('Calendario/Periodos/list');
        Route::get('/Calendario/Alunos/Periodos',[CalendarioController::class,'periodosIndex'])->name('Calendario/Periodos');
        Route::get('/Calendario/Alunos/Periodos/Novo',[CalendarioController::class,'cadastroPeriodos'])->name('Calendario/Periodos/Novo');
        Route::get('/Calendario/Alunos/Periodos/Cadastro/{id}',[CalendarioController::class,'cadastroPeriodos'])->name('Calendario/Periodos/Edit');
        Route::post('/Calendario/Alunos/Periodos/Save',[CalendarioController::class,'savePeriodos'])->name('Calendario/Periodos/Save');
        //
        Route::get('/Calendario/Profissionais/Ferias/list',[CalendarioController::class,'getFeriasProfissionais'])->name('Calendario/FeriasProfissionais/list');
        Route::get('/Calendario/Profissionais/Ferias',[CalendarioController::class,'feriasProfissionaisIndex'])->name('Calendario/FeriasProfissionais');
        Route::get('/Calendario/Profissionais/Ferias/Novo',[CalendarioController::class,'cadastroFeriasProfissionais'])->name('Calendario/FeriasProfissionais/Novo');
        Route::get('/Calendario/Profissionais/Ferias/Cadastro/{id}',[CalendarioController::class,'cadastroFeriasProfissionais'])->name('Calendario/FeriasProfissionais/Edit');
        Route::post('/Calendario/Profissionais/Ferias/Save',[CalendarioController::class,'saveFeriasProfissionais'])->name('Calendario/Profissionais/Ferias/Save');
        //
        Route::get('/Calendario/Sabados/list',[CalendarioController::class,'getSabados'])->name('Calendario/Sabados/list');
        Route::get('/Calendario/Sabados',[CalendarioController::class,'sabadosIndex'])->name('Calendario/Sabados');
        Route::get('/Calendario/Sabados/Novo',[CalendarioController::class,'cadastroSabados'])->name('Calendario/Sabados/Novo');
        Route::get('/Calendario/Sabados/Cadastro/{id}',[CalendarioController::class,'cadastroSabados'])->name('Calendario/Sabados/Edit');
        Route::post('/Calendario/Sabados/Save',[CalendarioController::class,'saveSabados'])->name('Calendario/Sabados/Save');
        //
        Route::get('/Calendario/Recuperacoes/list',[CalendarioController::class,'getRecuperacoes'])->name('Calendario/Recuperacoes/list');
        Route::get('/Calendario/Recuperacoes',[CalendarioController::class,'recuperacoesIndex'])->name('Calendario/Recuperacoes');
        Route::get('/Calendario/Recuperacoes/Novo',[CalendarioController::class,'cadastroRecuperacoes'])->name('Calendario/Recuperacoes/Novo');
        Route::get('/Calendario/Recuperacoes/Cadastro/{id}',[CalendarioController::class,'cadastroRecuperacoes'])->name('Calendario/Recuperacoes/Edit');
        Route::post('/Calendario/Recuperacoes/Save',[CalendarioController::class,'saveRecuperacoes'])->name('Calendario/Recuperacoes/Save');
        //
        Route::get('/Calendario/Feriados/list',[CalendarioController::class,'getFeriados'])->name('Calendario/Feriados/list');
        Route::get('/Calendario/Feriados',[CalendarioController::class,'FeriadosIndex'])->name('Calendario/Feriados');
        Route::get('/Calendario/Feriados/Novo',[CalendarioController::class,'cadastroFeriados'])->name('Calendario/Feriados/Novo');
        Route::get('/Calendario/Feriados/Cadastro/{id}',[CalendarioController::class,'cadastroFeriados'])->name('Calendario/Feriados/Edit');
        Route::post('/Calendario/Feriados/Save',[CalendarioController::class,'saveFeriados'])->name('Calendario/Feriados/Save');
        //
        Route::get('/Calendario/Planejamentos/list',[CalendarioController::class,'getPlanejamentos'])->name('Calendario/Planejamentos/list');
        Route::get('/Calendario/Planejamentos',[CalendarioController::class,'PlanejamentosIndex'])->name('Calendario/Planejamentos');
        Route::get('/Calendario/Planejamentos/Novo',[CalendarioController::class,'cadastroPlanejamentos'])->name('Calendario/Planejamentos/Novo');
        Route::get('/Calendario/Planejamentos/Cadastro/{id}',[CalendarioController::class,'cadastroPlanejamentos'])->name('Calendario/Planejamentos/Edit');
        Route::post('/Calendario/Planejamentos/Save',[CalendarioController::class,'savePlanejamentos'])->name('Calendario/Planejamentos/Save');
        //
        Route::get('/Calendario/Paralizacoes/list',[CalendarioController::class,'getParalizacoes'])->name('Calendario/Paralizacoes/list');
        Route::get('/Calendario/Paralizacoes',[CalendarioController::class,'paralizacoesIndex'])->name('Calendario/Paralizacoes');
        Route::get('/Calendario/Paralizacoes/Novo',[CalendarioController::class,'cadastroParalizacao'])->name('Calendario/Paralizacoes/Novo');
        Route::get('/Calendario/Paralizacoes/Cadastro/{id}',[CalendarioController::class,'cadastroParalizacao'])->name('Calendario/Paralizacoes/Edit');
        Route::post('/Calendario/Paralizacoes/Save',[CalendarioController::class,'saveParalizacao'])->name('Calendario/Paralizacoes/Save');
        //TRANSPORTE
        Route::get('/Transporte/list',[TransporteController::class,'getRotas'])->name('Transporte/list');
        Route::get('/Transporte',[TransporteController::class,'index'])->name('Transporte/index');
        Route::get('/Transporte/Novo',[TransporteController::class,'cadastro'])->name('Transporte/Novo');
        Route::get('/Transporte/Cadastro/{id}',[TransporteController::class,'cadastro'])->name('Transporte/Edit');
        Route::post('/Transporte/Save',[TransporteController::class,'save'])->name('Transporte/Save');
        //paradas
        Route::get('/Transporte/Paradas/list/{idrota}',[TransporteController::class,'getParadas'])->name('Transporte/Paradas/list');
        Route::get('/Transporte/{idrota}/Paradas',[TransporteController::class,'paradas'])->name('Transporte/Paradas');
        Route::get('/Transporte/{idrota}/Paradas/Novo',[TransporteController::class,'cadastroParadas'])->name('Transporte/Paradas/Novo');
        Route::get('/Transporte/{idrota}/Paradas/Cadastro/{id}',[TransporteController::class,'cadastroParadas'])->name('Transporte/Paradas/Edit');
        Route::post('/Transporte/Paradas/Save',[TransporteController::class,'saveParadas'])->name('Transporte/Paradas/Save');
        //rodagem
        Route::get('/Transporte/Rodagem/list/{idrota}',[TransporteController::class,'getRodagem'])->name('Transporte/Rodagem/list');
        Route::get('/Transporte/{idrota}/Rodagem',[TransporteController::class,'rodagem'])->name('Transporte/Rodagem');
        Route::get('/Transporte/{idrota}/Rodagem/Novo',[TransporteController::class,'cadastroRodagem'])->name('Transporte/Rodagem/Novo');
        Route::post('/Transporte/Rodagem/Save',[TransporteController::class,'saveRodagem'])->name('Transporte/Rodagem/Save');
        //veiculos
        Route::get('/Transporte/Veiculos/list',[TransporteController::class,'getVeiculos'])->name('Transporte/Veiculos/list');
        Route::get('/Transporte/Veiculos',[TransporteController::class,'veiculos'])->name('Transporte/Veiculos/index');
        Route::get('/Transporte/Veiculos/Novo',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Novo');
        Route::get('/Transporte/Veiculos/Cadastro/{id}',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Edit');
        Route::post('/Transporte/Veiculos/Save',[TransporteController::class,'saveVeiculos'])->name('Transporte/Veiculos/Save');
        //motoristas
        Route::get('/Transporte/Motoristas/list',[TransporteController::class,'getMotoristas'])->name('Transporte/Motoristas/list');
        Route::get('/Transporte/Motoristas',[TransporteController::class,'motoristas'])->name('Transporte/Motoristas/index');
        Route::get('/Transporte/Motoristas/Novo',[TransporteController::class,'cadastroMotoristas'])->name('Transporte/Motoristas/Novo');
        Route::get('/Transporte/Motoristas/Cadastro/{id}',[TransporteController::class,'cadastroMotoristas'])->name('Transporte/Motoristas/Edit');
        Route::post('/Transporte/Motoristas/Save',[TransporteController::class,'saveMotoristas'])->name('Transporte/Motoristas/Save');
        //terceirizadas
        Route::get('/Transporte/Terceirizadas/list',[TransporteController::class,'getTerceirizadas'])->name('Transporte/Terceirizadas/list');
        Route::get('/Transporte/Terceirizadas',[TransporteController::class,'terceirizadas'])->name('Transporte/Terceirizadas/index');
        Route::get('/Transporte/Terceirizadas/Novo',[TransporteController::class,'cadastroTerceirizadas'])->name('Transporte/Terceirizadas/Novo');
        Route::get('/Transporte/Terceirizadas/Cadastro/{id}',[TransporteController::class,'cadastroTerceirizadas'])->name('Transporte/Terceirizadas/Edit');
        Route::post('/Transporte/Terceirizadas/Save',[TransporteController::class,'saveTerceirizadas'])->name('Transporte/Terceirizadas/Save');
        //
        Route::get('/Transporte/Veiculos/list',[TransporteController::class,'getVeiculos'])->name('Transporte/Veiculos/list');
        Route::get('/Transporte/Veiculos',[TransporteController::class,'veiculos'])->name('Transporte/Veiculos/index');
        Route::get('/Transporte/Veiculos/Novo',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Novo');
        Route::get('/Transporte/Veiculos/Cadastro/{id}',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Edit');
        Route::post('/Transporte/Veiculos/Save',[TransporteController::class,'saveVeiculos'])->name('Transporte/Veiculos/Save');
        //MERENDA
        Route::get('/Merenda/list',[CardapioController::class,'getMerenda'])->name('Merenda/list');
        Route::get('/Merenda',[CardapioController::class,'index'])->name('Merenda/index');
        Route::get('/Merenda/Novo',[CardapioController::class,'cadastro'])->name('Merenda/Novo');
        Route::get('/Merenda/Cadastro/{id}',[CardapioController::class,'cadastro'])->name('Merenda/Edit');
        Route::post('/Merenda/Save',[CardapioController::class,'save'])->name('Merenda/Save');
        //ESTOQUE
        Route::get('/Merenda/Estoque/list',[CardapioController::class,'getEstoque'])->name('Merenda/Estoque/list');
        Route::get('/Merenda/Estoque',[CardapioController::class,'estoque'])->name('Merenda/Estoque/index');
        Route::get('/Merenda/Estoque/Novo',[CardapioController::class,'cadastroEstoque'])->name('Merenda/Estoque/Novo');
        Route::get('/Merenda/Estoque/Cadastro/{id}',[CardapioController::class,'cadastroEstoque'])->name('Merenda/Estoque/Edit');
        Route::post('/Merenda/Estoque/Save',[CardapioController::class,'saveEstoque'])->name('Merenda/Estoque/Save');
        //MOVIMENTACOES
        Route::get('/Merenda/Movimentacoes/list',[CardapioController::class,'getMovimentacoes'])->name('Merenda/Movimentacoes/list');
        Route::get('/Merenda/Movimentacoes',[CardapioController::class,'movimentacao'])->name('Merenda/Movimentacoes/index');
        Route::get('/Merenda/Movimentacoes/Novo',[CardapioController::class,'cadastroMovimentacoes'])->name('Merenda/Movimentacoes/Novo');
        Route::get('/Merenda/Movimentacoes/Cadastro/{id}',[CardapioController::class,'cadastroMovimentacoes'])->name('Merenda/Movimentacoes/Edit');
        Route::post('/Merenda/Movimentacoes/Save',[CardapioController::class,'saveMovimentacoes'])->name('Merenda/Movimentacoes/Save');
        //
    });
    //CAMADA DE SEGURANÇA DO SECRETARIO
    Route::middleware('secretario')->group(function(){
        //ENDERECOS
        Route::get('Enderecos',[EnderecosController::class,'index'])->name('Enderecos/index');
        Route::get('Enderecos/Cadastro',[EnderecosController::class,'cadastro'])->name('Enderecos/Novo');
        Route::get('Enderecos/list',[EnderecosController::class,'getEnderecos'])->name('Enderecos/list');
        Route::get('Enderecos/Cadastro/{id}',[EnderecosController::class,'cadastro'])->name('Enderecos/Edit');
        Route::post('Enderecos/Save',[EnderecosController::class,'save'])->name('Enderecos/Save');
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
