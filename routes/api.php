<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\HistoricoContempladosController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\PremioController;
use App\Http\Controllers\PromotorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Rota estabelecimento
Route::post('/estabelecimentos', [EstabelecimentoController::class, 'create']);
Route::get('/estabelecimentos', [EstabelecimentoController::class, 'index']);
Route::delete('/estabelecimentos/{id}', [EstabelecimentoController::class, 'delete']);
Route::patch('/estabelecimentos/{id}', [EstabelecimentoController::class, 'update']);

// Rotas prêmios
Route::post('/premios', [PremioController::class, 'create']);
Route::get('/premios', [PremioController::class, 'index']);
Route::get('/premios-roleta', [PremioController::class, 'getPremiosRoleta']);
Route::patch('/premios/{id}', [PremioController::class, 'update']);
Route::put('/premios-status/{id}', [PremioController::class, 'udapteStatus']);
Route::delete('/premios/{id}', [PremioController::class, 'delete']);

// Rotas promotores
Route::post('/promotores', [PromotorController::class, 'create']);
Route::get('/promotores', [PromotorController::class, 'index']);
Route::delete('/promotores/{id}', [PromotorController::class, 'delete']);
Route::patch('/promotores/{id}', [PromotorController::class, 'update']);

// Rotas participantes
Route::post('/participantes', [ParticipanteController::class, 'create']);
Route::get('/participantes/{cpf}', [ParticipanteController::class, 'find']);
Route::get('/participantes', [ParticipanteController::class, 'index']);
Route::delete('/participantes/{id}', [ParticipanteController::class, 'delete']);
Route::patch('/participantes/{id}', [ParticipanteController::class, 'update']);

// Rotas historico contemplados 
Route::get('/historico-contemplados',[HistoricoContempladosController::class, 'index']);