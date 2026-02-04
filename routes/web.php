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

    Route::get('/metodos', function () {
        $metodos = DB::table('metodos_es')
            ->select(['id', 'descripcion'])
            ->orderBy('id')
            ->get();

        return response()->json($metodos);
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

            $query = DB::table('transacciones as t')
                ->leftJoin('metodos_es as me', 'me.id', '=', 't.metodo_entrada_id')
                ->leftJoin('metodos_es as ms', 'ms.id', '=', 't.metodo_salida_id')
                ->whereNull('t.deleted_at')
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
                ->selectRaw("case t.tipo_transaccion_id when 1 then 'Compra' when 2 then 'Venta' when 3 then 'Entrada' when 4 then 'Salida' when 5 then 'Cambio' else '---' end as tipo_transaccion__descripcion");

            $tipoTransaccionId = (int) $request->query('tipo_transaccion_id', 0);
            if ($tipoTransaccionId > 0) {
                $query->where('t.tipo_transaccion_id', '=', $tipoTransaccionId);
            }

            $metodoSalidaId = (int) $request->query('metodo_salida_id', 0);
            if ($metodoSalidaId > 0) {
                $query->where('t.metodo_salida_id', '=', $metodoSalidaId);
            }

            $metodoEntradaId = (int) $request->query('metodo_entrada_id', 0);
            if ($metodoEntradaId > 0) {
                $query->where('t.metodo_entrada_id', '=', $metodoEntradaId);
            }

            $referenciaSalida = trim((string) $request->query('referencia_salida', ''));
            if ($referenciaSalida !== '') {
                $query->where('t.referencia_salida', 'like', '%'.$referenciaSalida.'%');
            }

            $referenciaEntrada = trim((string) $request->query('referencia_entrada', ''));
            if ($referenciaEntrada !== '') {
                $query->where('t.referencia_entrada', 'like', '%'.$referenciaEntrada.'%');
            }

            $fecha = trim((string) $request->query('fecha', ''));
            if ($fecha !== '') {
                $query->whereDate('t.fecha', '=', $fecha);
            }

            $compradorVendedor = trim((string) $request->query('comprador_vendedor', ''));
            if ($compradorVendedor !== '') {
                $query->where('t.comprador_vendedor', 'like', '%'.$compradorVendedor.'%');
            }

            $observacion = trim((string) $request->query('observacion', ''));
            if ($observacion !== '') {
                $query->where('t.observacion', 'like', '%'.$observacion.'%');
            }

            $normalizeDecimal = function ($value) {
                $s = trim((string) $value);
                if ($s === '' || $s === '0' || $s === '0.00' || $s === '0,00') {
                    return null;
                }

                $s = str_replace(' ', '', $s);
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);

                return is_numeric($s) ? (float) $s : null;
            };

            $monto = $normalizeDecimal($request->query('monto'));
            if ($monto !== null) {
                $query->where('t.monto', '=', $monto);
            }

            $tasa = $normalizeDecimal($request->query('tasa'));
            if ($tasa !== null) {
                $query->where('t.tasa', '=', $tasa);
            }

            $conversion = $normalizeDecimal($request->query('conversion'));
            if ($conversion !== null) {
                $query->where('t.conversion', '=', $conversion);
            }

            $paginator = $query
                ->orderByRaw($orderColumn.' '.$orderDirection)
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
