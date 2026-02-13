<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaccionesController extends Controller
{
    private function buildQuery(Request $request)
    {
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

        return $query;
    }

    private function applyOrdering(Request $request, $query)
    {
        $orderBy = (string) $request->query('orderBy', 'fecha');
        $orderDirection = strtolower((string) $request->query('orderDirection', 'desc'));

        $allowedOrderBy = [
            'fecha' => 't.fecha',
            'monto' => 't.monto',
            'tasa' => 't.tasa',
            'conversion' => 't.conversion',
            'id' => 't.id',
        ];

        $orderColumn = $allowedOrderBy[$orderBy] ?? 't.fecha';
        $orderDirection = in_array($orderDirection, ['asc', 'desc'], true) ? $orderDirection : 'desc';

        return $query->orderByRaw($orderColumn.' '.$orderDirection);
    }

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->query('perPage', 15);
            $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 15;

            $query = $this->applyOrdering($request, $this->buildQuery($request));

            $paginator = $query
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
    }

    public function excel(Request $request)
    {
        try {
            $rows = $this->applyOrdering($request, $this->buildQuery($request))->get();

            $escape = function ($v) {
                return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            };

            $fmt = function ($v) {
                if ($v === null || $v === '') return '';
                if (is_numeric($v)) {
                    return number_format((float) $v, 2, ',', '.');
                }
                return (string) $v;
            };

            $headers = [
                'Tipo',
                'Monto USD',
                'Método salida',
                'Ref salida',
                'Método entrada',
                'Ref entrada',
                'Fecha',
                'Tasa (Bs)',
                'Conversión (Bs)',
                'Comp./Vend.',
                'Observación',
            ];

            $html = '<html><head><meta charset="utf-8">';
            $html .= '<style>';
            $html .= 'table{border-collapse:collapse;font-family:Calibri, Arial, sans-serif;font-size:11pt;}';
            $html .= 'th,td{border:1px solid #D9D9D9;padding:4px 6px;vertical-align:middle;}';
            $html .= 'th{background:#1F4E79;color:#FFFFFF;font-weight:bold;text-align:center;white-space:nowrap;}';
            $html .= 'td.num{text-align:right;mso-number-format:"0,00";}';
            $html .= 'td.txt{text-align:left;}';
            $html .= 'td.date{text-align:center;white-space:nowrap;}';
            $html .= '</style></head><body>';
            $html .= '<table>';
            $html .= '<tr>' . implode('', array_map(fn ($h) => '<th>'.$escape($h).'</th>', $headers)) . '</tr>';

            foreach ($rows as $r) {
                $r = (array) $r;
                $html .= '<tr>';
                $html .= '<td class="txt">'.$escape($r['tipo_transaccion__descripcion'] ?? '---').'</td>';
                $html .= '<td class="num">'.$escape($fmt($r['monto'] ?? '')).'</td>';
                $html .= '<td class="txt">'.$escape($r['metodo_salida__descripcion'] ?? '---').'</td>';
                $html .= '<td class="txt">'.$escape($r['referencia_salida'] ?? '').'</td>';
                $html .= '<td class="txt">'.$escape($r['metodo_entrada__descripcion'] ?? '---').'</td>';
                $html .= '<td class="txt">'.$escape($r['referencia_entrada'] ?? '').'</td>';
                $html .= '<td class="date">'.$escape($r['fecha'] ?? '').'</td>';
                $html .= '<td class="num">'.$escape($fmt($r['tasa'] ?? '')).'</td>';
                $html .= '<td class="num">'.$escape($fmt($r['conversion'] ?? '')).'</td>';
                $html .= '<td class="txt">'.$escape($r['comprador_vendedor'] ?? '').'</td>';
                $html .= '<td class="txt">'.$escape($r['observacion'] ?? '').'</td>';
                $html .= '</tr>';
            }

            $html .= '</table></body></html>';

            $fileName = 'transacciones_' . now()->format('Y-m-d_H-i-s') . '.xls';

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $normalizeDecimal = function ($value) {
                $s = trim((string) $value);
                if ($s === '') {
                    return null;
                }

                $s = str_replace(' ', '', $s);
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);

                return is_numeric($s) ? (float) $s : null;
            };

            $tipoTransaccionId = (int) $request->input('tipo_transaccion_id');
            $monto = $normalizeDecimal($request->input('monto'));
            $tasa = $normalizeDecimal($request->input('tasa'));

            $metodoEntradaId = (int) $request->input('metodo_entrada_id');
            $metodoSalidaId = (int) $request->input('metodo_salida_id');

            $referenciaEntrada = trim((string) $request->input('referencia_entrada', ''));
            $referenciaSalida = trim((string) $request->input('referencia_salida', ''));
            $fecha = trim((string) $request->input('fecha', ''));
            $compradorVendedor = trim((string) $request->input('comprador_vendedor', ''));
            $observacion = trim((string) $request->input('observacion', ''));

            $isEntrada = $tipoTransaccionId === 3;
            $isSalida = $tipoTransaccionId === 4;

            $rules = [
                'tipo_transaccion_id' => ['required', 'integer', 'min:1'],
                'monto' => ['required'],
                'tasa' => ['required'],
                'fecha' => ['required', 'date'],
            ];

            if (!$isSalida) {
                $rules['metodo_entrada_id'] = ['required', 'integer', 'min:1'];
                $rules['referencia_entrada'] = ['required', 'string'];
            }

            if (!$isEntrada) {
                $rules['metodo_salida_id'] = ['required', 'integer', 'min:1'];
                $rules['referencia_salida'] = ['required', 'string'];
            }

            $validator = Validator::make($request->all(), $rules, [
                'required' => 'El campo :attribute es obligatorio.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validación fallida',
                    'errors' => $validator->errors(),
                ], 422);
            }

            if ($monto === null || $tasa === null) {
                return response()->json([
                    'message' => 'Monto y tasa deben ser numéricos',
                ], 422);
            }

            $conversion = $monto * $tasa;
            $user = $request->user();

            $payload = [
                'user_id' => $user?->id,
                'tipo_transaccion_id' => $tipoTransaccionId,
                'monto' => $monto,
                'metodo_entrada_id' => $isSalida ? null : ($metodoEntradaId > 0 ? $metodoEntradaId : null),
                'metodo_salida_id' => $isEntrada ? null : ($metodoSalidaId > 0 ? $metodoSalidaId : null),
                'referencia_entrada' => $isSalida ? null : ($referenciaEntrada !== '' ? $referenciaEntrada : null),
                'referencia_salida' => $isEntrada ? null : ($referenciaSalida !== '' ? $referenciaSalida : null),
                'fecha' => $fecha,
                'tasa' => $tasa,
                'conversion' => $conversion,
                'comprador_vendedor' => $compradorVendedor !== '' ? $compradorVendedor : null,
                'observacion' => $observacion !== '' ? $observacion : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $id = DB::table('transacciones')->insertGetId($payload);

            return response()->json([
                'message' => 'Transacción creada correctamente',
                'id' => $id,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
