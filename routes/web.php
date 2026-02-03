<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

    Route::get('/transacciones', function (Request $request) {
        try {
            $orderBy = (string) $request->query('orderBy', 'fecha');
            $orderDirection = strtolower((string) $request->query('orderDirection', 'desc'));
            $perPage = (int) $request->query('perPage', 15);

            $allowedOrderBy = [
                'fecha' => 't.fecha',
                'monto' => 't.monto',
                'tasa' => 't.tasa',
                'conversion' => 't.conversion',
                'id' => 't.id',
            ];

            $orderColumn = $allowedOrderBy[$orderBy] ?? 't.fecha';
            $orderDirection = in_array($orderDirection, ['asc', 'desc'], true) ? $orderDirection : 'desc';
            $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 15;

            $paginator = DB::table('transacciones as t')
                ->leftJoin('metodos_es as me', 'me.id', '=', 't.metodo_entrada_id')
                ->leftJoin('metodos_es as ms', 'ms.id', '=', 't.metodo_salida_id')
                ->whereNull('t.deleted_at')
                ->orderByRaw($orderColumn.' '.$orderDirection)
                ->select([
                    't.id',
                    't.tipo_transaccion_id',
                    't.monto',
                    't.metodo_entrada_id',
                    't.metodo_salida_id',
                    't.referencia_entrada',
                    't.referencia_salida',
                    't.fecha',
                    't.tasa',
                    't.conversion',
                    't.comprador_vendedor',
                    't.observacion',
                    't.user_id',
                    't.created_at',
                    't.updated_at',
                    't.deleted_at',
                    'me.descripcion as metodo_entrada__descripcion',
                    'ms.id as metodo_salida__id',
                    'ms.descripcion as metodo_salida__descripcion',
                    'ms.tipo_metodo_es_id as metodo_salida__tipo_metodo_es_id',
                    'ms.banco_id as metodo_salida__banco_id',
                    'ms.orden_saldos as metodo_salida__orden_saldos',
                ])
                ->selectRaw("t.tipo_transaccion_id as tipo_transaccion__id")
                ->selectRaw("case t.tipo_transaccion_id when 1 then 'Compra' when 2 then 'Venta' when 3 then 'Entrada' when 4 then 'Salida' when 5 then 'Cambio' else '---' end as tipo_transaccion__descripcion")
                ->paginate($perPage)
                ->appends($request->query());

            $payload = $paginator->toArray();
            $payload['data'] = array_map(function ($row) {
                $row = (array) $row;

                return [
                    'id' => $row['id'],
                    'tipo_transaccion_id' => (string) ($row['tipo_transaccion_id'] ?? ''),
                    'monto' => (string) ($row['monto'] ?? '0.00'),
                    'metodo_entrada_id' => $row['metodo_entrada_id'],
                    'metodo_salida_id' => $row['metodo_salida_id'],
                    'referencia_entrada' => $row['referencia_entrada'] ?? null,
                    'referencia_salida' => $row['referencia_salida'] ?? null,
                    'fecha' => $row['fecha'],
                    'tasa' => (string) ($row['tasa'] ?? null),
                    'conversion' => (string) ($row['conversion'] ?? null),
                    'comprador_vendedor' => $row['comprador_vendedor'] ?? null,
                    'observacion' => $row['observacion'] ?? null,
                    'user_id' => (string) ($row['user_id'] ?? ''),
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                    'deleted_at' => $row['deleted_at'],
                    'tipo_transaccion' => [
                        'id' => $row['tipo_transaccion__id'],
                        'descripcion' => $row['tipo_transaccion__descripcion'],
                    ],
                    'metodo_entrada' => [
                        'descripcion' => $row['metodo_entrada__descripcion'] ?? '---',
                    ],
                    'metodo_salida' => [
                        'id' => $row['metodo_salida__id'],
                        'descripcion' => $row['metodo_salida__descripcion'] ?? '---',
                        'tipo_metodo_es_id' => $row['metodo_salida__tipo_metodo_es_id'],
                        'banco_id' => $row['metodo_salida__banco_id'],
                        'orden_saldos' => $row['metodo_salida__orden_saldos'],
                    ],
                ];
            }, $payload['data']);

            return response()->json($payload);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    });
});
