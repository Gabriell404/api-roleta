<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\HistoricoContempladosController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\PremioController;
use App\Http\Controllers\PromotorController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Rota estabelecimento
    Route::post('/estabelecimentos', [EstabelecimentoController::class, 'create']);
    Route::delete('/estabelecimentos/{id}', [EstabelecimentoController::class, 'delete']);
    Route::patch('/estabelecimentos/{id}', [EstabelecimentoController::class, 'update']);
    Route::put('/estabelecimentos-status/{id}', [EstabelecimentoController::class, 'updateStatus']);

    // Rotas prêmios      
    Route::post('/premios', [PremioController::class, 'create']);
    Route::delete('/premios/{id}', [PremioController::class, 'delete']);
    Route::put('/premios-status/{id}', [PremioController::class, 'updateStatus']);
    Route::patch('/premios/{id}', [PremioController::class, 'update']);

    // Rotas participante
    Route::delete('/participantes/{id}', [ParticipanteController::class, 'delete']);
    Route::patch('/participantes/{id}', [ParticipanteController::class, 'update']);

    // Rota logout 
    Route::post('/logout', [UsuarioController::class, 'logout']);
});

// Rota estabelecimento
Route::get('/estabelecimentos', [EstabelecimentoController::class, 'index']);

// Rotas do usuario 
Route::post('/user-create', [UsuarioController::class, 'create']);
Route::post('/login', [UsuarioController::class, 'login']);

// Rotas prêmios
Route::get('/premios', [PremioController::class, 'index']);
Route::get('/premios-roleta', [PremioController::class, 'getPremiosRoleta']);

// Rotas promotores
Route::post('/promotores', [PromotorController::class, 'create']);
Route::get('/promotores', [PromotorController::class, 'index']);
Route::delete('/promotores/{id}', [PromotorController::class, 'delete']);
Route::patch('/promotores/{id}', [PromotorController::class, 'update']);

// Rotas participantes
Route::get('/participantes/{cpf}', [ParticipanteController::class, 'find']);
Route::get('/participantes', [ParticipanteController::class, 'index']);
Route::post('/participantes', [ParticipanteController::class, 'create']);

// Rota historico contemplados 
Route::get('/historico-contemplados', [HistoricoContempladosController::class, 'index']);
