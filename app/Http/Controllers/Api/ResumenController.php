<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ResumenController extends Controller
{
    public function show()
    {
        $resumen = DB::selectOne('select entradas, salidas, balance from resumen_transacciones');

        return response()->json([
            'entradas' => $resumen?->entradas ?? '0.00',
            'salidas' => $resumen?->salidas ?? '0.00',
            'balance' => $resumen?->balance ?? '0.00',
        ]);
    }
}
