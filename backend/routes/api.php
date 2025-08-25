<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
|
| Qualquer usuário pode acessar estas rotas sem autenticação.
|
*/
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (necessitam token Sanctum)
|--------------------------------------------------------------------------
|
| Usuário precisa estar autenticado para acessar.
|
*/
Route::middleware('auth:sanctum')->group(function () {

    // CRUD de produtos
    Route::apiResource('products', ProductController::class);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
