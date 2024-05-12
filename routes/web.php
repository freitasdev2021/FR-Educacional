<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecretariasController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    //
    //SECRETARÍAS
    Route::get('/Secretarias',[SecretariasController::class,'index'])->name('Secretarias/index');
    Route::get('/Secretarias/Relatorios',[SecretariasController::class,'relatorios'])->name('Secretarias/Relatorios');
    Route::get('/Secretarias/Novo',[SecretariasController::class,'cadastro'])->name('Secretarias/Novo');
    Route::get('/Secretarias/Cadastro/{id}',[SecretariasController::class,'cadastro'])->name('Secretarias/Edit');
    Route::get('/Secretarias/list',[SecretariasController::class,'getSecretarias'])->name('Secretarias/list');
    Route::post('/Secretarias/Save',[SecretariasController::class,'save'])->name('Secretarias/Save');
    //USUARIOS FORNECEDOR
    Route::get('/Fornecedor/Usuarios',[UsuariosController::class,'fornecedoresIndex'])->name('Usuarios/indexFornecedor');
    //PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //
});

require __DIR__.'/auth.php';
