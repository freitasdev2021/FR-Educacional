<?php

use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecretariasController;
use App\Http\Controllers\VagasController;
use App\Http\Controllers\EADController;
use App\Http\Controllers\AulasController;
use App\Http\Controllers\OcorrenciasController;
use App\Http\Controllers\PlanejamentosController;
use App\Http\Controllers\EscolasController;
use App\Http\Controllers\RecuperacaoController;
use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\TurmasController;
use App\Http\Controllers\ApoioController;
use App\Http\Controllers\DiretoresController;
use App\Http\Controllers\DiarioController;
use App\Http\Controllers\RelatoriosController;
use App\Http\Controllers\ProfessoresController;
use App\Http\Controllers\RecrutamentoController;
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
use App\Http\Controllers\CIController;
use App\Http\Controllers\AvaliacoesController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('Recrutamento/Registro/{Orgao}/{ID}',[RecrutamentoController::class,'registro'])->name('Recrutamento/Registro');
Route::patch("Recrutamento/Registrar/{IDOrg}",[RecrutamentoController::class,'registrar'])->name('Recrutamento/Registrar');

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
        //Route::get('/Professores/DisciplinasProfessor/{IDTurma}/{IDProfessor?}',[ProfessoresController::class,'getDisciplinasTurmaProfessor'])->name('Professores/DisciplinasProfessor');
    });
    //CAMADA DE SEGURANÇA, TIME EDUCACIONAL COMPLETO
    Route::middleware('time')->group(function(){
        //AULAS
        Route::get('Professores/Turmas/{IDTurma}',[EscolasController::class,'getProfessoresTurmaHTML'])->name('Professores/Turmas');
        Route::get('/Professores/DisciplinasProfessor/{IDTurma}/{IDProfessor?}',[ProfessoresController::class,'getDisciplinasTurmaProfessor'])->name('Professores/DisciplinasProfessor');
        Route::get('/Professores/SelectDisciplinasProfessor/{IDTurma}/{IDProfessor?}',[ProfessoresController::class,'getSelectDisciplinasTurmaProfessor'])->name('Professores/SelectDisciplinasProfessor');
        Route::get('/Aulas/Presenca/Todos/{IDAula}',[AulasController::class,'presencaTodos'])->name('Aulas/Presenca/Todos');
        Route::get('/Aulas/Presenca/{Hash}',[AulasController::class,'chamada'])->name('Aulas/Presenca');
        Route::get('/Avaliacoes/Nota/{Hash}',[AulasController::class,'chamada'])->name('Avaliacoes/Notas');
        Route::get('/Aulas/Presenca/list/{Hash}',[AulasController::class,'getAulaPresenca'])->name('Aulas/Presenca/list');
        Route::get('/Avaliacoes/Nota/list/{Hash}',[AulasController::class,'getAvaliacaoNota'])->name('Avaliacoes/Nota/list');
        Route::get('/Aulas/ListaAlunos/{IDTurma}/{DTAula}',[AulasController::class,'getHtmlAlunosChamada'])->name('Aulas/ListaAlunos');
        Route::get('/Aulas/ListaAlunosAvaliacao/{IDTurma}/{DTAula}',[AulasController::class,'getHtmlAlunosAvaliacao'])->name('Aulas/ListaAlunosAvaliacao');
        Route::post('/Aulas/setPresenca',[AulasController::class,'setPresenca'])->name('Aulas/setPresenca');
        Route::get('/Aulas/list',[AulasController::class,'getAulas'])->name('Aulas/list');
        Route::get('/Aulas',[AulasController::class,'index'])->name('Aulas/index');
        Route::get('/Aulas/Novo',[AulasController::class,'cadastro'])->name('Aulas/Novo');
        Route::get('/Aulas/Cadastro/{id}',[AulasController::class,'cadastro'])->name('Aulas/Edit');
        Route::get('Aulas/Delete/{Hash}',[AulasController::class,'deleteAula'])->name('Aulas/Delete');
        Route::get('/Aulas/Chamada/{id}',[AulasController::class,'chamada'])->name('Aulas/Chamada');
        Route::post('/Aulas/Save',[AulasController::class,'save'])->name('Aulas/Save');
        Route::post('/Aulas/getAlunos',[AulasController::class,'getAulaAlunos'])->name('Aulas/getAlunos');
        Route::post('/Turmas/AlunosHtml',[FichaController::class,'getSelectAlunosFicha'])->name('Turmas/AlunosHtml');
        Route::patch('Aulas/Alterar/{Hash}',[AulasController::class,'alterarAula'])->name('Aulas/Alterar');
        //AVALICAÇÕES
        Route::get('Aulas/Avaliacoes',[AvaliacoesController::class,'index'])->name("Aulas/Avaliacoes/index");
        Route::get('Aulas/Avaliacoes/Novo',[AvaliacoesController::class,'cadastro'])->name("Aulas/Avaliacoes/Novo");
        Route::get('Aulas/Avaliacoes/Edit/{id}',[AvaliacoesController::class,'cadastro'])->name("Aulas/Avaliacoes/Edit");
        Route::get('Aulas/Avaliacoes/list',[AvaliacoesController::class,'getAvaliacoes'])->name("Aulas/Avaliacoes/list");
        Route::post('Aulas/Avaliacoes/Save',[AvaliacoesController::class,'save'])->name("Aulas/Avaliacoes/Save");
        //ATIVIDADES
        Route::get('/Aulas/Atividades/list',[AulasController::class,'getAtividades'])->name('Aulas/Atividades/list');
        Route::get('/Aulas/Atividades',[AulasController::class,'atividades'])->name('Aulas/Atividades/index');
        Route::get('/Aulas/Atividades/Novo',[AulasController::class,'cadastroAtividades'])->name('Aulas/Atividades/Novo');
        Route::get('/Aulas/Atividades/Cadastro/{id}',[AulasController::class,'cadastroAtividades'])->name('Aulas/Atividades/Edit');
        Route::get('/Aulas/Atividades/Exclusao/{id}',[AulasController::class,'excluirAtividade'])->name('Aulas/Atividades/Exclusao');
        Route::get('/Aulas/Atividades/Correcao/{id}',[AulasController::class,'correcaoAtividades'])->name('Aulas/Atividades/Correcao');
        Route::post('/Aulas/Atividades/Save',[AulasController::class,'saveAtividades'])->name('Aulas/Atividades/Save');
        Route::post('/Aulas/Atividades/setNota',[AulasController::class,'setNota'])->name('Aulas/Atividades/setNota');
        Route::get('Secretaria/LivroPonto',[SecretariasController::class,'getLivroPonto'])->name('Secretaria/LivroPonto');
        //FICHA INDIVIDUAL
        Route::get('Alunos/Relatorio/Teste',[RelatoriosController::class,'getAlunosTurmasEditavel'])->name('Alunos/Relatorio/Teste');
        Route::get('Turmas/Aprovacao/Teste/{IDTurma}/{Ano}',[TurmasController::class,'getProgredidosTurma'])->name('Turmas/Aprovacao/Teste');
        Route::get('Alunos/FichaIndividual/{id}',[FichaController::class,'gerarFichaIndividual'])->name('Alunos/FichaIndividual');
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
        //LISTA DE ESPERA
        Route::get('Alunos/Espera',[AlunosController::class,'espera'])->name('Alunos/Espera');
        Route::get('Alunos/Espera/list',[AlunosController::class,'getEspera'])->name('Alunos/Espera/list');
        Route::get('Alunos/Espera/Cadastro',[AlunosController::class,'cadastroEspera'])->name('Alunos/Espera/Novo');
        Route::get('Alunos/Espera/Cadastro/{id}',[AlunosController::class,'cadastroEspera'])->name('Alunos/Espera/Edit');
        Route::get('Alunos/Espera/Delete/{id}',[AlunosController::class,'deleteEspera'])->name('Alunos/Espera/Delete');
        Route::get('Alunos/Espera/Designacao/{IDAluno}',[AlunosController::class,'designacaoEspera'])->name('Alunos/Espera/Designacao');
        Route::post('Alunos/Espera/Save',[AlunosController::class,'saveEspera'])->name('Alunos/Espera/Save');
        //MATRICULA
        Route::get('/Alunos/list',[AlunosController::class,'getAlunos'])->name('Alunos/list');
        Route::get('/Alunos',[AlunosController::class,'index'])->name('Alunos/index');
        Route::get('/Alunos/Transferidos',[AlunosController::class,'transferidos'])->name('Alunos/Transferidos');
        Route::get('/Alunos/Transferidos/Transferido/{id}',[AlunosController::class,'matriculaTransferidos'])->name('Alunos/Transferidos/Transferido');
        Route::get('/Alunos/Transferidos/list',[AlunosController::class,'getTransferidos'])->name('Alunos/Transferidos/list');
        Route::get('/Alunos/Novo',[AlunosController::class,'cadastro'])->name('Alunos/Novo');
        Route::get('/Alunos/Cadastro/{id}',[AlunosController::class,'cadastro'])->name('Alunos/Edit');
        Route::post('/Alunos/Save',[AlunosController::class,'save'])->name('Alunos/Save');
        Route::post('Alunos/Reclassificar',[AlunosController::class,'reclassificar'])->name('Alunos/Reclassificar');
        Route::post('Alunos/Remanejar',[AlunosController::class,'remanejar'])->name('Alunos/Remanejar');
        Route::post('/Alunos/Renovar',[AlunosController::class,'renovar'])->name('Alunos/Renovar');
        Route::post('/Alunos/Transferidos/Matricular',[AlunosController::class,'matricularTransferido'])->name('Alunos/Transferidos/Matricular');
        //FICHA AVALIATIVA
        Route::post('Fichas/Save',[FichaController::class,'save'])->name('Fichas/Save');
        Route::get('Fichas',[FichaController::class,'index'])->name('Fichas/index');
        Route::get('Fichas/list/{AND}',[FichaController::class,'getFichas'])->name('Fichas/list');
        Route::get('Fichas/Cadastro',[FichaController::class,'cadastro'])->name('Fichas/Novo');
        Route::get('Fichas/Cadastro/{id}',[FichaController::class,'cadastro'])->name('Fichas/Edit');
        Route::get('Fichas/Respostas/{id}',[FichaController::class,'respostas'])->name('Fichas/Respostas');
        Route::get('Fichas/Respostas/Export/{id}', [FichaController::class, 'exportRespostas'])->name('Fichas/Respostas/Export');
        Route::get('Fichas/Imprimir/{IDTurma}/{Etapa}', [FichaController::class, 'exportarRespostasPDF'])->name('Fichas/Imprimir');
        Route::get('Fichas/Visualizar/{id}',[FichaController::class,'visualizar'])->name('Fichas/Visualizar');
        Route::post('Fichas/Responder',[FichaController::class,'responder'])->name('Fichas/Responder');
        ////EAD
        //view
        Route::get('EAD',[EADController::class,'index'])->name('EAD/index');
        Route::get('EAD/Instituicoes',[EADController::class,'instituicoes'])->name('EAD/Instituicoes');
        Route::get('EAD/Cursos',[EADController::class,'cursos'])->name('EAD/Cursos');
        Route::get('EAD/Etapas/{IDCurso}',[EADController::class,'etapas'])->name('EAD/Etapas');
        Route::get('EAD/Aulas/{IDCurso}',[EADController::class,'aulas'])->name('EAD/Aulas');
        //save
        Route::post('EAD/Save',[EADController::class,'save'])->name("EAD/Save");
        Route::post('EAD/Instituicoes/Save',[EADController::class,'saveInstituicoes'])->name('EAD/Instituicoes/Save');
        Route::post('EAD/Aulas/Save',[EADController::class,'saveAula'])->name('EAD/Aulas/Save');
        Route::post('EAD/Cursos/Save',[EADController::class,'saveCurso'])->name('EAD/Cursos/Save');
        Route::post('EAD/Etapas/Save',[EADController::class,'saveEtapa'])->name('EAD/Etapas/Save');
        //cadastro
        Route::get('EAD/Instituicoes/Cadastro',[EADController::class,'cadastroInstituicoes'])->name('EAD/Instituicoes/Novo');
        Route::get('EAD/Instituicoes/Cadastro/{id}',[EADController::class,'cadastroInstituicoes'])->name('EAD/Instituicoes/Edit');
        //
        Route::get('EAD/Aulas/Cadastro/{IDCurso}',[EADController::class,'cadastroAulas'])->name('EAD/Aulas/Novo');
        Route::get('EAD/Aulas/Cadastro/{id}/{IDCurso}',[EADController::class,'cadastroAulas'])->name('EAD/Aulas/Edit');
        //
        Route::get('EAD/Cursos/Cadastro',[EADController::class,'cadastroCursos'])->name('EAD/Cursos/Novo');
        Route::get('EAD/Cursos/Cadastro/{id}',[EADController::class,'cadastroCursos'])->name('EAD/Cursos/Edit');
        //
        Route::get('EAD/Etapas/Cadastro/{IDCurso}',[EADController::class,'cadastroEtapas'])->name('EAD/Etapas/Novo');
        Route::get('EAD/Etapas/Cadastro/{id}/{IDCurso}',[EADController::class,'cadastroEtapas'])->name('EAD/Etapas/Edit');
        //list
        Route::get('Instituicoes/list',[EADController::class,'getInstituicoes'])->name('Instituicoes/list');
        Route::get('Cursos/list',[EADController::class,'getCursos'])->name('EAD/Cursos/list');
        Route::get('Etapas/list/{IDEtapa}',[EADController::class,'getEtapas'])->name('Etapas/list');
        Route::get('Aulas/list/{IDEtapa}',[EADController::class,'getAulas'])->name('EAD/Aulas/list');
        ////LAUDOS
        Route::get('Alunos/NEE/list/{IDAluno}',[AlunosController::class,'getNecessidades'])->name('Alunos/NEE/list');
        Route::get('Alunos/NEE/{IDAluno}',[AlunosController::class,'necessidades'])->name('Alunos/NEE');
        Route::get('Alunos/NEE/Cadastro/{IDAluno}',[AlunosController::class,'cadastroNecessidade'])->name('Alunos/NEE/Novo');
        Route::get('Alunos/NEE/Cadastro/{IDAluno}/{id}',[AlunosController::class,'cadastroNecessidade'])->name('Alunos/NEE/Edit');
        Route::post('Alunos/NEE/Save',[AlunosController::class,'saveNecessidade'])->name('Alunos/NEE/Save');
        //
        Route::get("Alunos/Comprovantes/Matricula/{id}",[AlunosController::class,'getComprovanteMatricula'])->name("Alunos/Comprovante/Matricula");
        Route::get("Alunos/Comprovantes/Escolaridade/{id}",[AlunosController::class,'getComprovanteEscolaridade'])->name("Alunos/Comprovante/Escolaridade");
        Route::get("Alunos/Comprovantes/Vaga/{id}",[AlunosController::class,'getComprovanteVaga'])->name("Alunos/Comprovante/Vaga");
        Route::get("Alunos/Comprovantes/Prematricula/{id}",[AlunosController::class,'getPreMatricula'])->name("Alunos/Comprovante/Prematricula");
        Route::get("Alunos/Comprovantes/Frequencia/{id}",[AlunosController::class,'getComprovanteFrequencia'])->name("Alunos/Comprovante/Frequencia");
        Route::get("Alunos/Comprovantes/Filiacao/{id}",[AlunosController::class,'getRelatorioMatricula'])->name("Alunos/Comprovante/Filiacao");
        Route::get("Alunos/Comprovantes/Conclusao/{id}",[AlunosController::class,'getComprovanteConclusao'])->name("Alunos/Comprovante/Conclusao");
        Route::get("Alunos/Comprovantes/Transferencia/{id}",[AlunosController::class,'getDeclaracaoTransferencia'])->name("Alunos/Comprovante/Transferencia");
        Route::get("Alunos/Comprovantes/Comparecimento/{id}",[AlunosController::class,'getAtestadoComparecimento'])->name("Alunos/Comprovante/Comparecimento");
        Route::get("Alunos/Comprovantes/Responsabilidade/{id}",[AlunosController::class,'termoResponsabilidade'])->name("Alunos/Comprovante/Responsabilidade");
        Route::get('/Alunos/Historico/{id}',[AlunosController::class,'historico'])->name('Alunos/Historico');
        Route::get('/Alunos/Boletim/{id}',[AlunosController::class,'boletim'])->name('Alunos/Boletim');
        Route::patch('/Alunos/GerarHistorico/{id}',[AlunosController::class,'gerarHistoricoEscolar'])->name('Alunos/GerarHistorico');
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
        //PLANEJAMENTO AEE
        Route::get('Planejamentos/AEE/list',[PlanejamentosController::class,'getPlanejamentosAee'])->name('Planejamentos/AEE/list');
        Route::get('Planejamentos/AEE',[PlanejamentosController::class,'aee'])->name('Planejamentos/AEE');
        Route::get('Planejamentos/AEE/Componentes/{id}',[PlanejamentosController::class,'componentesAee'])->name('Planejamentos/AEE/Componentes');
        Route::get('Planejamentos/AEE/Novo',[PlanejamentosController::class,'cadastroAee'])->name('Planejamentos/AEE/Novo');
        Route::get('Planejamentos/AEE/Cadastro/{id}',[PlanejamentosController::class,'cadastroAee'])->name('Planejamentos/AEE/Cadastro');
        Route::post('Planejamentos/AEE/Save',[PlanejamentosController::class,'saveAee'])->name('Planejamentos/AEE/Save');
        Route::post('/Planejamentos/AEE/Componentes/Save',[PlanejamentosController::class,'saveComponentesAee'])->name('Planejamentos/AEE/Componentes/Save');
        //PLANEJAMENTO ESCOLAR
        Route::get('/Planejamentos/list',[PlanejamentosController::class,'getPlanejamentos'])->name('Planejamentos/list');
        Route::get('/Planejamentos',[PlanejamentosController::class,'index'])->name('Planejamentos/index');
        Route::get('Planejamentos/Metas',[PlanejamentosController::class,'metas'])->name('Planejamentos/Metas');
        Route::get('/Planejamentos/{id}/Componentes',[PlanejamentosController::class,'componentes'])->name('Planejamentos/Componentes');
        Route::get('/Planejamentos/Novo',[PlanejamentosController::class,'cadastro'])->name('Planejamentos/Novo');
        Route::get('/Planejamentos/Cadastro/{id}',[PlanejamentosController::class,'cadastro'])->name('Planejamentos/Cadastro');
        Route::get('/Planejamentos/getConteudo/{IDDisciplina}/{IDTurma}/{TPAula}',[PlanejamentosController::class,'getPlanejamentoByTurma'])->name('Planejamentos/getConteudo');
        Route::post('/Planejamentos/Save',[PlanejamentosController::class,'save'])->name('Planejamentos/Save');
        Route::post('/Planejamentos/Componentes/Save',[PlanejamentosController::class,'saveComponentes'])->name('Planejamentos/Componentes/Save');
        Route::patch("Planejamentos/Metas/Save/{IDEscola}",[PlanejamentosController::class,'saveMeta'])->name('Planejamentos/Metas/Save');
        Route::patch("Planejamentos/Objetivos/Save/{IDEscola}",[PlanejamentosController::class,'saveObjetivo'])->name('Planejamentos/Objetivos/Save');
        //RECUPERAÇÃO
        Route::get('Aulas/Recuperacao',[RecuperacaoController::class,'index'])->name("Aulas/Recuperacao/index");
        Route::get('Aulas/Recuperacao/Novo',[RecuperacaoController::class,'cadastro'])->name("Aulas/Recuperacao/Novo");
        Route::get('Aulas/Recuperacao/Edit/{id}',[RecuperacaoController::class,'cadastro'])->name("Aulas/Recuperacao/Edit");
        Route::get('Aulas/Recuperacao/list',[RecuperacaoController::class,'getRecuperacoes'])->name("Aulas/Recuperacao/list");
        Route::post('Aulas/Recuperacao/Save',[RecuperacaoController::class,'save'])->name("Aulas/Recuperacao/Save");
        //OCORRENCIAS
        Route::get('Ocorrencias/list/{IDAluno?}',[OcorrenciasController::class,'getOcorrencias'])->name('Ocorrencias/list');
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
        Route::get("Professores/Imprimir",[ProfessoresController::class,'Imprimir'])->name("Professores/Imprimir");
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
        Route::get('Escolas/Relatorios/ImprimirDireto/{Tipo}',[RelatoriosController::class,'imprimirDireto'])->name('Escolas/Relatorios/ImprimirDireto');
        Route::put('Escolas/Relatorios/Gerar/{Tipo}',[RelatoriosController::class,'Gerar'])->name('Escolas/Relatorios/Gerar');
        //VAGAS
        Route::get('Escolas/Vagas',[VagasController::class,'index'])->name('Escolas/Vagas');
        Route::get('Escolas/Vagas/Cadastro',[VagasController::class,'cadastro'])->name('Escolas/Vagas/Novo');
        Route::get('Escolas/Vagas/Cadastro/{IDVaga}',[VagasController::class,'cadastro'])->name('Escolas/Vagas/Edit');
        Route::get('Escolas/Vagas/Excluir/{IDVaga}/{IDEscola}',[VagasController::class,'excluir'])->name('Escolas/Vagas/Excluir');
        Route::get('Escolas/Vagas/list',[VagasController::class,'getVagas'])->name('Escolas/Vagas/list');
        Route::post('Escolas/Vagas/Save',[VagasController::class,'save'])->name('Escolas/Vagas/Save');
        //CALENDARIO
        Route::get('Calendario/Dias',[CalendarioController::class,'diasLetivos'])->name('Calendario/Dias');
        Route::get('Calendario/Gerar',[CalendarioController::class,'gerarCalendario'])->name('Calendario/Gerar');
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
        //ALIMENTOS
        Route::get('/Merenda/Alimentos/list',[CardapioController::class,'getAlimentos'])->name('Merenda/Alimentos/list');
        Route::get('/Merenda/Alimentos',[CardapioController::class,'alimentos'])->name('Merenda/Alimentos/index');
        Route::get('/Merenda/Alimentos/Novo',[CardapioController::class,'cadastroAlimentos'])->name('Merenda/Alimentos/Novo');
        Route::get('/Merenda/Alimentos/Cadastro/{id}',[CardapioController::class,'cadastroAlimentos'])->name('Merenda/Alimentos/Edit');
        Route::post('/Merenda/Alimentos/Save',[CardapioController::class,'saveAlimentos'])->name('Merenda/Alimentos/Save');
        //NUTRICIONISTAS
        Route::get('/Merenda/Nutricionistas/list',[CardapioController::class,'getNutricionistas'])->name('Merenda/Nutricionistas/list');
        Route::get('/Merenda/Nutricionistas',[CardapioController::class,'nutricionistas'])->name('Merenda/Nutricionistas/index');
        Route::get('/Merenda/Nutricionistas/Novo',[CardapioController::class,'cadastroNutricionistas'])->name('Merenda/Nutricionistas/Novo');
        Route::get('/Merenda/Nutricionistas/Cadastro/{id}',[CardapioController::class,'cadastroNutricionistas'])->name('Merenda/Nutricionistas/Edit');
        Route::post('/Merenda/Nutricionistas/Save',[CardapioController::class,'saveNutricionistas'])->name('Merenda/Nutricionistas/Save');
        //CONTRATOS
        Route::get('/Merenda/Contratos/list',[CardapioController::class,'getContratos'])->name('Merenda/Contratos/list');
        Route::get('/Merenda/Contratos',[CardapioController::class,'contratos'])->name('Merenda/Contratos/index');
        Route::get('/Merenda/Contratos/Novo',[CardapioController::class,'cadastroContratos'])->name('Merenda/Contratos/Novo');
        Route::get('/Merenda/Contratos/Cadastro/{id}',[CardapioController::class,'cadastroContratos'])->name('Merenda/Contratos/Edit');
        Route::post('/Merenda/Contratos/Save',[CardapioController::class,'saveContratos'])->name('Merenda/Contratos/Save');
        //RESTRICOES
        Route::get('/Merenda/Restricoes/list',[CardapioController::class,'getRestricoes'])->name('Merenda/Restricoes/list');
        Route::get('/Merenda/Restricoes',[CardapioController::class,'Restricoes'])->name('Merenda/Restricoes/index');
        Route::get('/Merenda/Restricoes/Novo',[CardapioController::class,'cadastroRestricoes'])->name('Merenda/Restricoes/Novo');
        Route::get('/Merenda/Restricoes/Cadastro/{id}',[CardapioController::class,'cadastroRestricoes'])->name('Merenda/Restricoes/Edit');
        Route::post('/Merenda/Restricoes/Save',[CardapioController::class,'saveRestricoes'])->name('Merenda/Restricoes/Save');
        //IMC
        Route::get('/Merenda/IMC/list',[CardapioController::class,'getIMC'])->name('Merenda/IMC/list');
        Route::get('/Merenda/IMC',[CardapioController::class,'IMC'])->name('Merenda/IMC/index');
        Route::get('/Merenda/IMC/Novo',[CardapioController::class,'cadastroIMC'])->name('Merenda/IMC/Novo');
        Route::get('/Merenda/IMC/Cadastro/{id}',[CardapioController::class,'cadastroIMC'])->name('Merenda/IMC/Edit');
        Route::post('/Merenda/IMC/Save',[CardapioController::class,'saveIMC'])->name('Merenda/IMC/Save');
        //EMPENHO
        Route::post('Merenda/Empenho/Save',[CardapioController::class,'saveEmpenho'])->name('Merenda/Empenho/Save');
        //AF
        Route::post('Merenda/AF/Save',[CardapioController::class,'saveAF'])->name('Merenda/AF/Save');
        //BIBLIOTECA
        Route::get('Biblioteca/list',[BibliotecaController::class,'getBibliotecas'])->name('Biblioteca/list');
        Route::get('Biblioteca',[BibliotecaController::class,'index'])->name('Biblioteca/index');
        Route::get('Biblioteca/Novo',[BibliotecaController::class,'cadastro'])->name('Biblioteca/Novo');
        Route::get('Biblioteca/Cadastro/{id}',[BibliotecaController::class,'cadastro'])->name('Biblioteca/Edit');
        Route::post('Biblioteca/Save',[BibliotecaController::class,'save'])->name('Biblioteca/Save');
        //Leitores
        Route::get('Biblioteca/Leitores/list',[BibliotecaController::class,'getLeitores'])->name('Biblioteca/Leitores/list');
        Route::get('Biblioteca/Leitores',[BibliotecaController::class,'leitores'])->name('Biblioteca/Leitores/index');
        Route::get('Biblioteca/Leitores/Novo',[BibliotecaController::class,'cadastroLeitores'])->name('Biblioteca/Leitores/Novo');
        Route::get('Biblioteca/Leitores/Cadastro/{id}',[BibliotecaController::class,'cadastroLeitores'])->name('Biblioteca/Leitores/Edit');
        Route::post('Biblioteca/Leitores/Save',[BibliotecaController::class,'saveLeitores'])->name('Biblioteca/Leitores/Save');
        //Emprestimos
        Route::get('Biblioteca/Emprestimos/list',[BibliotecaController::class,'getEmprestimos'])->name('Biblioteca/Emprestimos/list');
        Route::get('Biblioteca/Emprestimos',[BibliotecaController::class,'emprestimos'])->name('Biblioteca/Emprestimos/index');
        Route::get('Biblioteca/Emprestimos/Novo',[BibliotecaController::class,'cadastroEmprestimos'])->name('Biblioteca/Emprestimos/Novo');
        Route::get('Biblioteca/Emprestimos/Cadastro/{id}',[BibliotecaController::class,'cadastroEmprestimos'])->name('Biblioteca/Emprestimos/Edit');
        Route::post('Biblioteca/Emprestimos/Save',[BibliotecaController::class,'saveEmprestimos'])->name('Biblioteca/Emprestimos/Save');
        //Gerar Codigo de Barras
        Route::get('Biblioteca/GerarEtiquetas',[BibliotecaController::class,'gerarEtiquetas'])->name('Biblioteca/GerarEtiquetas');
        Route::get('Biblioteca/DevolverLivro/{IDEmprestimo}',[BibliotecaController::class,'devolverLivro'])->name('Biblioteca/DevolverLivro');
        //Destinatario
        Route::get('CI/Destinatario',[CIController::class,'destinatarioIndex'])->name('CI/Destinatario');
        //Respostas
        Route::patch('CI/Resposta/{IDUser}/{IDComunicacao}',[CIController::class,'responder'])->name('CI/Resposta');
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
        Route::get('Professores/Contratos',[ProfessoresController::class,'contratos'])->name('Professores/Contratos');
        Route::get('Professores/Contratos/list',[ProfessoresController::class,'getContratos'])->name('Professores/Contratos/list');
        Route::get('Professores/Contratos/Cadastro',[ProfessoresController::class,'cadastroContratos'])->name('Professores/Contratos/Novo');
        Route::get('Professores/Contratos/Cadastro/{id}',[ProfessoresController::class,'cadastroContratos'])->name('Professores/Contratos/Edit');
        Route::get('Professores/Contratos/Cancelar/{id}',[ProfessoresController::class,'cancelarContrato'])->name('Professores/Contratos/Cancelar');
        Route::post('Professores/Contratos/Save',[ProfessoresController::class,'saveContratos'])->name('Professores/Contratos/Save');
        Route::patch('Professores/Aditivos/Save/{IDContrato}',[ProfessoresController::class,'saveAditivos'])->name('Professores/Aditivos/Save');
        //PEDAGOGOS
        Route::get('/Pedagogos/Novo',[PedagogosController::class,'cadastro'])->name('Pedagogos/Novo');
        Route::post('/Pedagogos/Save',[PedagogosController::class,'save'])->name('Pedagogos/Save');
        //PROCESSO SELETIVO
        Route::get('Recrutamento/list',[RecrutamentoController::class,'getRecrutamento'])->name('Recrutamento/list');
        Route::get('Recrutamento',[RecrutamentoController::class,'index'])->name('Recrutamento/index');
        Route::get('Recrutamento/Cadastro',[RecrutamentoController::class,'cadastro'])->name('Recrutamento/Novo');
        Route::get('Recrutamento/Cadastro/{id}',[RecrutamentoController::class,'cadastro'])->name('Recrutamento/Edit');
        Route::post('Recrutamentos/Save',[RecrutamentoController::class,'save'])->name('Recrutamento/Save');
        //INSCRITOS
        Route::get('Recrutamento/Inscritos',[RecrutamentoController::class,'inscritos'])->name('Recrutamento/Inscritos/index');
        Route::get('Recrutamento/Inscritos/list',[RecrutamentoController::class,'getInscritos'])->name('Recrutamento/Inscritos/list');
        //COMUNICAÇÃO INTERNA
        Route::get('CI',[CIController::class,'index'])->name('CI/index');
        Route::get('CI/Cadastro',[CIController::class,'cadastro'])->name('CI/Novo');
        Route::get('CI/Cadastro/{id}',[CIController::class,'cadastro'])->name('CI/Edit');
        Route::get('CI/Encerrar/{id}',[CIController::class,'encerrar'])->name('CI/Encerrar');
        Route::get('CI/Mensagens/Cadastro/{IDCi}',[CIController::class,'cadastroMensagens'])->name('CI/Mensagens/Novo');
        Route::get('CI/Mensagens/Cadastro/{IDCi}/{id}',[CIController::class,'cadastroMensagens'])->name('CI/Mensagens/Edit');
        Route::get('CI/list',[CIController::class,'getCi'])->name('CI/list');
        Route::get('CI/Mensagens/{IDCi}',[CIController::class,'mensagens'])->name('CI/Mensagens');
        Route::get('CI/Mensagens/list/{IDCi}',[CIController::class,'getMensagens'])->name('CI/Mensagens/list');
        Route::post('CI/Save',[CIController::class,'save'])->name('CI/Save');
        Route::patch('CI/Mensagens/Save/{IDCi}',[CIController::class,'saveMensagens'])->name('CI/Mensagens/Save');
        //
    });
    //CAMADA DE PROTEÇÃO DO CANDIDATO
    Route::middleware('candidato')->group(function(){
        //CANDIDATURA
        Route::get('Candidatura/Inscrever/{id}',[RecrutamentoController::class,'inscrever'])->name('Candidatura/Inscrever');
        Route::get('Candidatura',[RecrutamentoController::class,'candidatura'])->name('Candidatura/index');
        Route::post('Candidatura/Save',[RecrutamentoController::class,'saveCandidatura'])->name('Candidatura/Save');
        Route::post('Candidatura/Anexo/Save',[RecrutamentoController::class,'saveAnexo'])->name('Candidatura/Anexo/Save');
        Route::post('Candidatura/Curso/Save',[RecrutamentoController::class,'saveCurso'])->name('Candidatura/Curso/Save');
        //
    });
    //CAMADA DE SEGURANÇA DO PORTAL DO ALUNO
    Route::middleware('aluno')->group(function(){
        //OCORRENCIAS
        Route::get('Aluno/Ocorrencias/list/{IDAluno}',[OcorrenciasController::class,'getOcorrencias'])->name('Aluno/Ocorrencias/list');
        Route::get('Aluno/Ocorrencias',[OcorrenciasController::class,'index'])->name('OcorrenciasAluno/index');
        Route::get('Aluno/Ocorrencia/{id}',[OcorrenciasController::class,'cadastro'])->name('Aluno/Ocorrencia');
        //CALENDARIO
        Route::get('Aluno/Calendario',[CalendarioController::class,'index'])->name('Aluno/Calendario/index');
        //Desempenho
        Route::get('Aluno/Desempenho',[AlunosController::class,'desempenho'])->name('Desempenho/index');
        //Matrículas
        Route::get('Aluno/Matriculas',[AlunosController::class,'matriculas'])->name('Matriculas/index');
        //EAD
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
