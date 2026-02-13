<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ResumenController;
use App\Http\Controllers\Api\MetodosController;
use App\Http\Controllers\Api\TransaccionesController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/', function () {
    return view('home');
})->middleware('auth');

Route::get('/carga', function () {
    return view('carga');
})->middleware('auth');

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/resumen', [ResumenController::class, 'show']);
    Route::get('/metodos', [MetodosController::class, 'index']);
    Route::get('/transacciones', [TransaccionesController::class, 'index']);
    Route::post('/transacciones', [TransaccionesController::class, 'store']);
});
