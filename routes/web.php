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
use App\Http\Controllers\TransporteController;
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
        Route::get('/Escolas/Turmas/list',[EscolasController::class,'getTurmas'])->name('Escolas/Turmas/list')->middleware('secretario');
        Route::get('/Escolas/Turmas',[EscolasController::class,'Turmas'])->name('Escolas/Turmas');
        Route::get('/Escolas/Turmas/Novo',[EscolasController::class,'cadastroTurmas'])->name('Escolas/Turmas/Novo')->middleware(['secretario','diretor']);
        Route::get('/Escolas/Turmas/Edit/{id}',[EscolasController::class,'cadastroTurmas'])->name('Escolas/Turmas/Cadastro');
        Route::post('/Escolas/Turmas/Save',[EscolasController::class,'saveTurmas'])->name('Escolas/Turmas/Save')->middleware(['secretario','diretor','coordenador']);
        //ALUNOS
        Route::get('/Alunos/list',[AlunosController::class,'getAlunos'])->name('Alunos/list')->middleware('secretario');
        Route::get('/Alunos',[AlunosController::class,'index'])->name('Alunos/index')->middleware(['diretor','coordenador','secretario']);
        Route::get('/Alunos/Novo',[AlunosController::class,'cadastro'])->name('Alunos/Novo')->middleware(['diretor','coordenador','secretario']);
        Route::post('/Alunos/Save',[AlunosController::class,'save'])->name('Alunos/Save')->middleware(['secretario']);
        //DIRETORES
        Route::get('/Diretores/list',[DiretoresController::class,'getDiretores'])->name('Diretores/list')->middleware('secretario');
        Route::get('/Diretores',[DiretoresController::class,'index'])->name('Diretores/index')->middleware('secretario');
        Route::get('/Diretores/Novo',[DiretoresController::class,'cadastro'])->name('Diretores/Novo')->middleware('secretario');
        Route::get('/Diretores/Cadastro/{id}',[DiretoresController::class,'cadastro'])->name('Diretores/Edit')->middleware('secretario');
        Route::post('/Diretoress/Save',[DiretoresController::class,'save'])->name('Diretores/Save')->middleware('secretario');
        //PROFESSORES
        Route::get('/Professores/list',[ProfessoresController::class,'getProfessores'])->name('Professores/list')->middleware('secretario');
        Route::get('/Professores',[ProfessoresController::class,'index'])->name('Professores/index')->middleware(['coordenador','diretor','secretario']);
        Route::get('/Professores/Novo',[ProfessoresController::class,'cadastro'])->name('Professores/Novo')->middleware('secretario');
        Route::get('/Professores/Cadastro/{id}',[ProfessoresController::class,'cadastro'])->name('Professores/Edit')->middleware(['coordenador','diretor','secretario']);
        Route::post('/Professores/Save',[ProfessoresController::class,'save'])->name('Professores/Save')->middleware('secretario');
        //turnos
        Route::get('/Professores/Turnos/list/{idprofessor}',[ProfessoresController::class,'getTurnosProfessor'])->name('Professores/Turnos/list')->middleware('secretario');
        Route::get('/Professores/Turnos/{idprofessor}',[ProfessoresController::class,'Turnos'])->name('Professores/Turnos')->middleware(['coordenador','diretor','secretario']);
        Route::get('/Professores/{idprofessor}/Turnos/Novo',[ProfessoresController::class,'cadastroTurnoProfessor'])->name('Professores/Turnos/Novo')->middleware('secretario');
        Route::get('/Professores/{idprofessor}/Turnos/Cadastro/{id}',[ProfessoresController::class,'cadastroTurnoProfessor'])->name('Professores/Turnos/Edit')->middleware(['coordenador','diretor','secretario']);
        Route::post('/Professores/Turnos/Save',[ProfessoresController::class,'saveTurno'])->name('Professores/Turnos/Save')->middleware('secretario');
        //
        //PEDAGOGOS
        Route::get('/Pedagogos/list',[PedagogosController::class,'getPedagogos'])->name('Pedagogos/list')->middleware('secretario');
        Route::get('/Pedagogos',[PedagogosController::class,'index'])->name('Pedagogos/index')->middleware('secretario');
        Route::get('/Pedagogos/Novo',[PedagogosController::class,'cadastro'])->name('Pedagogos/Novo')->middleware('secretario');
        Route::get('/Pedagogos/Cadastro/{id}',[PedagogosController::class,'cadastro'])->name('Pedagogos/Edit')->middleware('secretario');
        Route::post('/Pedagogos/Save',[PedagogosController::class,'save'])->name('Pedagogos/Save')->middleware('secretario');
        //PAIS
        Route::get('/Responsaveis',[ResponsaveisController::class,'index'])->name('Responsaveis/index')->middleware(['coordenador','diretor']);
        Route::get('/Responsaveis/Novo',[ResponsaveisController::class,'cadastro'])->name('Responsaveis/Novo')->middleware(['coordenador','diretor']);
        Route::get('/Responsaveis/Cadastro/{id}',[ResponsaveisController::class,'cadastro'])->name('Responsaveis/Edit')->middleware(['coordenador','diretor']);
        Route::post('/Responsaveis/Save',[ResponsaveisController::class,'save'])->name('Responsaveis/Save')->middleware(['coordenador','diretor']);
        //AUXILIARES
        Route::get('/Auxiliares/list',[AuxiliaresController::class,'getAuxiliares'])->name('Auxiliares/list')->middleware(['secretario','diretor']);
        Route::get('/Auxiliares',[AuxiliaresController::class,'index'])->name('Auxiliares/index')->middleware(['secretario','diretor']);
        Route::get('/Auxiliares/Novo',[AuxiliaresController::class,'cadastro'])->name('Auxiliares/Novo')->middleware(['secretario','diretor']);
        Route::get('/Auxiliares/Cadastro/{id}',[AuxiliaresController::class,'cadastro'])->name('Auxiliares/Edit')->middleware(['secretario','diretor']);
        Route::post('/Auxiliares/Save',[AuxiliaresController::class,'save'])->name('Auxiliares/Save')->middleware(['secretario','diretor']);
        //CALENDARIO
        Route::get('/Calendario',[CalendarioController::class,'index'])->name('Calendario/index')->middleware('secretario');
        Route::get('/Calendario/Eventos/list',[CalendarioController::class,'getEventos'])->name('Calendario/Eventos/list')->middleware('secretario');
        Route::get('/Calendario/Eventos',[CalendarioController::class,'eventosIndex'])->name('Calendario/Eventos')->middleware('secretario');
        Route::get('/Calendario/Eventos/Novo',[CalendarioController::class,'eventosCadastro'])->name('Calendario/Eventos/Novo')->middleware('secretario');
        Route::get('/Calendario/Eventos/Cadastro/{id}',[CalendarioController::class,'eventosCadastro'])->name('Calendario/Eventos/Edit')->middleware('secretario');
        Route::post('/Calendario/Eventos/Save',[CalendarioController::class,'saveEvento'])->name('Calendario/Eventos/Save')->middleware('secretario');
        //
        Route::get('/Calendario/Alunos/Ferias/list',[CalendarioController::class,'getFeriasAlunos'])->name('Calendario/FeriasAlunos/list')->middleware('secretario');
        Route::get('/Calendario/Alunos/Ferias',[CalendarioController::class,'feriasAlunosIndex'])->name('Calendario/FeriasAlunos')->middleware('secretario');
        Route::get('/Calendario/Alunos/Ferias/Novo',[CalendarioController::class,'cadastroFeriasAlunos'])->name('Calendario/FeriasAlunos/Novo')->middleware('secretario');
        Route::get('/Calendario/Alunos/Ferias/Cadastro/{id}',[CalendarioController::class,'cadastroFeriasAlunos'])->name('Calendario/FeriasAlunos/Edit')->middleware('secretario');
        Route::post('/Calendario/Alunos/Ferias/Save',[CalendarioController::class,'saveFeriasAlunos'])->name('Calendario/Alunos/Ferias/Save')->middleware('secretario');
        //
        Route::get('/Calendario/Profissionais/Ferias/list',[CalendarioController::class,'getFeriasProfissionais'])->name('Calendario/FeriasProfissionais/list')->middleware('secretario');
        Route::get('/Calendario/Profissionais/Ferias',[CalendarioController::class,'feriasProfissionaisIndex'])->name('Calendario/FeriasProfissionais')->middleware('secretario');
        Route::get('/Calendario/Profissionais/Ferias/Novo',[CalendarioController::class,'cadastroFeriasProfissionais'])->name('Calendario/FeriasProfissionais/Novo')->middleware('secretario');
        Route::get('/Calendario/Profissionais/Ferias/Cadastro/{id}',[CalendarioController::class,'cadastroFeriasProfissionais'])->name('Calendario/FeriasProfissionais/Edit')->middleware('secretario');
        Route::post('/Calendario/Profissionais/Ferias/Save',[CalendarioController::class,'saveFeriasProfissionais'])->name('Calendario/Profissionais/Ferias/Save')->middleware('secretario');
        //
        Route::get('/Calendario/Sabados/list',[CalendarioController::class,'getSabados'])->name('Calendario/Sabados/list')->middleware('secretario');
        Route::get('/Calendario/Sabados',[CalendarioController::class,'sabadosIndex'])->name('Calendario/Sabados')->middleware(['diretor','secretario']);
        Route::get('/Calendario/Sabados/Novo',[CalendarioController::class,'cadastroSabados'])->name('Calendario/Sabados/Novo')->middleware('secretario');
        Route::get('/Calendario/Sabados/Cadastro/{id}',[CalendarioController::class,'cadastroSabados'])->name('Calendario/Sabados/Edit')->middleware(['diretor','secretario']);
        Route::post('/Calendario/Sabados/Save',[CalendarioController::class,'saveSabados'])->name('Calendario/Sabados/Save')->middleware('secretario');
        //
        Route::get('/Calendario/Paralizacoes/list',[CalendarioController::class,'getParalizacoes'])->name('Calendario/Paralizacoes/list')->middleware('secretario');
        Route::get('/Calendario/Paralizacoes',[CalendarioController::class,'paralizacoesIndex'])->name('Calendario/Paralizacoes')->middleware(['diretor','secretario']);
        Route::get('/Calendario/Paralizacoes/Novo',[CalendarioController::class,'cadastroParalizacao'])->name('Calendario/Paralizacoes/Novo')->middleware('secretario');
        Route::get('/Calendario/Paralizacoes/Cadastro/{id}',[CalendarioController::class,'cadastroParalizacao'])->name('Calendario/Paralizacoes/Edit')->middleware(['diretor','secretario']);
        Route::post('/Calendario/Paralizacoes/Save',[CalendarioController::class,'saveParalizacao'])->name('Calendario/Paralizacoes/Save')->middleware('secretario');
        //TRANSPORTE
        Route::get('/Transporte/list',[TransporteController::class,'getRotas'])->name('Transporte/list')->middleware(['diretor','secretario']);
        Route::get('/Transporte',[TransporteController::class,'index'])->name('Transporte/index')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Novo',[TransporteController::class,'cadastro'])->name('Transporte/Novo')->middleware('diretor');
        Route::get('/Transporte/Cadastro/{id}',[TransporteController::class,'cadastro'])->name('Transporte/Edit')->middleware(['diretor','secretario']);
        Route::post('/Transporte/Save',[TransporteController::class,'save'])->name('Transporte/Save')->middleware(['diretor','secretario']);
        //paradas
        Route::get('/Transporte/Paradas/list/{idrota}',[TransporteController::class,'getParadas'])->name('Transporte/Paradas/list')->middleware(['diretor','secretario']);
        Route::get('/Transporte/{idrota}/Paradas',[TransporteController::class,'paradas'])->name('Transporte/Paradas')->middleware(['diretor','secretario']);
        Route::get('/Transporte/{idrota}/Paradas/Novo',[TransporteController::class,'cadastroParadas'])->name('Transporte/Paradas/Novo')->middleware('diretor');
        Route::get('/Transporte/{idrota}/Paradas/Cadastro/{id}',[TransporteController::class,'cadastroParadas'])->name('Transporte/Paradas/Edit')->middleware(['diretor','secretario']);
        Route::post('/Transporte/Paradas/Save',[TransporteController::class,'saveParadas'])->name('Transporte/Paradas/Save')->middleware(['diretor','secretario']);
        //rodagem
        Route::get('/Transporte/Rodagem/list/{idrota}',[TransporteController::class,'getRodagem'])->name('Transporte/Rodagem/list')->middleware(['diretor','secretario']);
        Route::get('/Transporte/{idrota}/Rodagem',[TransporteController::class,'rodagem'])->name('Transporte/Rodagem')->middleware(['diretor','secretario']);
        Route::get('/Transporte/{idrota}/Rodagem/Novo',[TransporteController::class,'cadastroRodagem'])->name('Transporte/Rodagem/Novo')->middleware('diretor');
        Route::post('/Transporte/Rodagem/Save',[TransporteController::class,'saveRodagem'])->name('Transporte/Rodagem/Save')->middleware(['diretor','secretario']);
        //veiculos
        Route::get('/Transporte/Veiculos/list',[TransporteController::class,'getVeiculos'])->name('Transporte/Veiculos/list')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Veiculos',[TransporteController::class,'veiculos'])->name('Transporte/Veiculos/index')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Veiculos/Novo',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Novo')->middleware('diretor');
        Route::get('/Transporte/Veiculos/Cadastro/{id}',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Edit')->middleware(['diretor','secretario']);
        Route::post('/Transporte/Veiculos/Save',[TransporteController::class,'saveVeiculos'])->name('Transporte/Veiculos/Save')->middleware(['diretor','secretario']);
        //motoristas
        Route::get('/Transporte/Motoristas/list',[TransporteController::class,'getMotoristas'])->name('Transporte/Motoristas/list')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Motoristas',[TransporteController::class,'motoristas'])->name('Transporte/Motoristas/index')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Motoristas/Novo',[TransporteController::class,'cadastroMotoristas'])->name('Transporte/Motoristas/Novo')->middleware('diretor');
        Route::get('/Transporte/Motoristas/Cadastro/{id}',[TransporteController::class,'cadastroMotoristas'])->name('Transporte/Motoristas/Edit')->middleware(['diretor','secretario']);
        Route::post('/Transporte/Motoristas/Save',[TransporteController::class,'saveMotoristas'])->name('Transporte/Motoristas/Save')->middleware(['diretor','secretario']);
        //terceirizadas
        Route::get('/Transporte/Terceirizadas/list',[TransporteController::class,'getTerceirizadas'])->name('Transporte/Terceirizadas/list')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Terceirizadas',[TransporteController::class,'terceirizadas'])->name('Transporte/Terceirizadas/index')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Terceirizadas/Novo',[TransporteController::class,'cadastroTerceirizadas'])->name('Transporte/Terceirizadas/Novo')->middleware('diretor');
        Route::get('/Transporte/Terceirizadas/Cadastro/{id}',[TransporteController::class,'cadastroTerceirizadas'])->name('Transporte/Terceirizadas/Edit')->middleware(['diretor','secretario']);
        Route::post('/Transporte/Terceirizadas/Save',[TransporteController::class,'saveTerceirizadas'])->name('Transporte/Terceirizadas/Save')->middleware(['diretor','secretario']);
        //
        Route::get('/Transporte/Veiculos/list',[TransporteController::class,'getVeiculos'])->name('Transporte/Veiculos/list')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Veiculos',[TransporteController::class,'veiculos'])->name('Transporte/Veiculos/index')->middleware(['diretor','secretario']);
        Route::get('/Transporte/Veiculos/Novo',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Novo')->middleware('diretor');
        Route::get('/Transporte/Veiculos/Cadastro/{id}',[TransporteController::class,'cadastroVeiculos'])->name('Transporte/Veiculos/Edit')->middleware(['diretor','secretario']);
        Route::post('/Transporte/Veiculos/Save',[TransporteController::class,'saveVeiculos'])->name('Transporte/Veiculos/Save')->middleware(['diretor','secretario']);
        //MERENDA
        Route::get('/Merenda/list',[CardapioController::class,'getMerenda'])->name('Merenda/list')->middleware(['diretor','secretario']);
        Route::get('/Merenda',[CardapioController::class,'index'])->name('Merenda/index')->middleware(['diretor','secretario']);
        Route::get('/Merenda/Novo',[CardapioController::class,'cadastro'])->name('Merenda/Novo')->middleware('diretor');
        Route::get('/Merenda/Cadastro/{id}',[CardapioController::class,'cadastro'])->name('Merenda/Edit')->middleware(['diretor','secretario']);
        Route::post('/Merenda/Save',[CardapioController::class,'save'])->name('Merenda/Save')->middleware(['diretor','secretario']);
        //estoque
        Route::get('/Merenda/Estoque/list',[CardapioController::class,'getEstoque'])->name('Merenda/Estoque/list')->middleware(['diretor','secretario']);
        Route::get('/Merenda/Estoque',[CardapioController::class,'estoque'])->name('Merenda/Estoque/index')->middleware(['diretor','secretario']);
        Route::get('/Merenda/Estoque/Novo',[CardapioController::class,'cadastroEstoque'])->name('Merenda/Estoque/Novo')->middleware('diretor');
        Route::get('/Merenda/Estoque/Cadastro/{id}',[CardapioController::class,'cadastroEstoque'])->name('Merenda/Estoque/Edit')->middleware(['diretor','secretario']);
        Route::post('/Merenda/Estoque/Save',[CardapioController::class,'saveEstoque'])->name('Merenda/Estoque/Save')->middleware(['diretor','secretario']);
        //movimentacoes
        Route::get('/Merenda/Movimentacoes/list',[CardapioController::class,'getMovimentacoes'])->name('Merenda/Movimentacoes/list')->middleware(['diretor','secretario']);
        Route::get('/Merenda/Movimentacoes',[CardapioController::class,'movimentacao'])->name('Merenda/Movimentacoes/index')->middleware(['diretor','secretario']);
        Route::get('/Merenda/Movimentacoes/Novo',[CardapioController::class,'cadastroMovimentacoes'])->name('Merenda/Movimentacoes/Novo')->middleware('diretor');
        Route::get('/Merenda/Movimentacoes/Cadastro/{id}',[CardapioController::class,'cadastroMovimentacoes'])->name('Merenda/Movimentacoes/Edit')->middleware(['diretor','secretario']);
        Route::post('/Merenda/Movimentacoes/Save',[CardapioController::class,'saveMovimentacoes'])->name('Merenda/Movimentacoes/Save')->middleware(['diretor','secretario']);
        //
    //});
    //
    
    //PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //
});

require __DIR__.'/auth.php';
