<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\DB;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/', function () {
    return view('home');
})->middleware('auth');

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/resumen', function () {
        $resumen = DB::selectOne('select entradas, salidas, balance from resumen_transacciones');

        return response()->json([
            'entradas' => $resumen?->entradas ?? '0.00',
            'salidas' => $resumen?->salidas ?? '0.00',
            'balance' => $resumen?->balance ?? '0.00',
        ]);
    });
});
