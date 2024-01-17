<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LivroUsuarioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('livros')->group(function () {
    Route::get('/', [LivroController::class, 'index']);
    Route::post('/', [LivroController::class, 'create']);
    Route::get('/{id}', [LivroController::class, 'show']);
    Route::put('/{id}', [LivroController::class, 'update']);
    Route::delete('/{id}', [LivroController::class, 'delete']);
});

Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'create']);
    Route::get('/{id}', [UsuarioController::class, 'show']);
    Route::put('/{id}', [UsuarioController::class, 'update']);
    Route::delete('/{id}', [UsuarioController::class, 'delete']);
});

Route::prefix('livros-usuarios')->group(function () {
    Route::get('/', [LivroUsuarioController::class, 'index']);
    Route::get('/{livroId}/{usuarioId}', [LivroUsuarioController::class, 'show']);
    Route::post('/{livroId}/{usuarioId}', [LivroUsuarioController::class, 'alugarLivro']);
    Route::put('/{livroId}/{usuarioId}/devolver', [LivroUsuarioController::class, 'devolverLivro']);
    Route::put('/{livroId}/{usuarioId}', [LivroUsuarioController::class, 'update']);
    Route::delete('/{livroId}/{usuarioId}', [LivroUsuarioController::class, 'delete']);
    Route::get('/exportar', [LivroUsuarioController::class, 'exportarLivrosUsuarios']);
});