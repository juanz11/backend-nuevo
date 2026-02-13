<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaccionesController extends Controller
{
    private function normalizeDecimal($value)
    {
        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }

        $s = str_replace(' ', '', $s);

        $hasComma = strpos($s, ',') !== false;
        $hasDot = strpos($s, '.') !== false;

        if ($hasComma && $hasDot) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } elseif ($hasComma) {
            $s = str_replace(',', '.', $s);
        } elseif ($hasDot) {
            $dotCount = substr_count($s, '.');

            if ($dotCount > 1) {
                $lastPos = strrpos($s, '.');
                $decLen = $lastPos === false ? 0 : (strlen($s) - $lastPos - 1);

                if ($lastPos !== false && $decLen > 0 && $decLen <= 2) {
                    $intPart = str_replace('.', '', substr($s, 0, $lastPos));
                    $decPart = substr($s, $lastPos + 1);
                    $s = $intPart.'.'.$decPart;
                } else {
                    $s = str_replace('.', '', $s);
                }
            } else {
                $pos = strrpos($s, '.');
                $decLen = $pos === false ? 0 : (strlen($s) - $pos - 1);

                if ($pos !== false && $decLen === 3) {
                    $s = str_replace('.', '', $s);
                }
            }
        }

        return is_numeric($s) ? (float) $s : null;
    }

    private function validateAndBuildPayload(array $input, $userId)
    {
        $tipoTransaccionId = (int) ($input['tipo_transaccion_id'] ?? 0);
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

        $validator = Validator::make($input, $rules, [
            'required' => 'El campo :attribute es obligatorio.',
        ]);

        if ($validator->fails()) {
            return [null, $validator->errors()];
        }

        $monto = $this->normalizeDecimal($input['monto'] ?? null);
        $tasa = $this->normalizeDecimal($input['tasa'] ?? null);

        if ($monto === null || $tasa === null) {
            return [null, ['monto' => ['Monto inválido'], 'tasa' => ['Tasa inválida']]];
        }

        $metodoEntradaId = (int) ($input['metodo_entrada_id'] ?? 0);
        $metodoSalidaId = (int) ($input['metodo_salida_id'] ?? 0);

        $referenciaEntrada = trim((string) ($input['referencia_entrada'] ?? ''));
        $referenciaSalida = trim((string) ($input['referencia_salida'] ?? ''));
        $fecha = trim((string) ($input['fecha'] ?? ''));
        $compradorVendedor = trim((string) ($input['comprador_vendedor'] ?? ''));
        $observacion = trim((string) ($input['observacion'] ?? ''));

        $conversion = $monto * $tasa;

        $payload = [
            'user_id' => $userId,
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

        return [$payload, null];
    }

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

            $hasComma = strpos($s, ',') !== false;
            $hasDot = strpos($s, '.') !== false;

            if ($hasComma && $hasDot) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } elseif ($hasComma) {
                $s = str_replace(',', '.', $s);
            } elseif ($hasDot) {
                $dotCount = substr_count($s, '.');

                if ($dotCount > 1) {
                    $lastPos = strrpos($s, '.');
                    $decLen = $lastPos === false ? 0 : (strlen($s) - $lastPos - 1);

                    if ($lastPos !== false && $decLen > 0 && $decLen <= 2) {
                        $intPart = str_replace('.', '', substr($s, 0, $lastPos));
                        $decPart = substr($s, $lastPos + 1);
                        $s = $intPart.'.'.$decPart;
                    } else {
                        $s = str_replace('.', '', $s);
                    }
                } else {
                    $pos = strrpos($s, '.');
                    $decLen = $pos === false ? 0 : (strlen($s) - $pos - 1);

                    if ($pos !== false && $decLen === 3) {
                        $s = str_replace('.', '', $s);
                    }
                }
            }

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

    public function pdf(Request $request)
    {
        try {
            $rows = $this->applyOrdering($request, $this->buildQuery($request))->get();

            $fmt = function ($v) {
                if ($v === null || $v === '') {
                    return '';
                }
                if (is_numeric($v)) {
                    return number_format((float) $v, 2, ',', '.');
                }
                return (string) $v;
            };

            $mapped = array_map(function ($r) use ($fmt) {
                $r = (array) $r;
                $r['monto_fmt'] = $fmt($r['monto'] ?? '');
                $r['tasa_fmt'] = $fmt($r['tasa'] ?? '');
                $r['conversion_fmt'] = $fmt($r['conversion'] ?? '');
                return $r;
            }, $rows->all());

            $filters = [];
            $tipoTransaccionId = (int) $request->query('tipo_transaccion_id', 0);
            if ($tipoTransaccionId > 0) $filters[] = 'tipo=' . $tipoTransaccionId;
            $metodoSalidaId = (int) $request->query('metodo_salida_id', 0);
            if ($metodoSalidaId > 0) $filters[] = 'salida=' . $metodoSalidaId;
            $metodoEntradaId = (int) $request->query('metodo_entrada_id', 0);
            if ($metodoEntradaId > 0) $filters[] = 'entrada=' . $metodoEntradaId;
            $fecha = trim((string) $request->query('fecha', ''));
            if ($fecha !== '') $filters[] = 'fecha=' . $fecha;

            $generatedAt = now()->format('Y-m-d H:i:s');
            $filtersText = implode(' | ', $filters);

            return response()
                ->view('pdf.transacciones', [
                    'rows' => $mapped,
                    'generatedAt' => $generatedAt,
                    'filtersText' => $filtersText,
                ])
                ->header('Content-Type', 'text/html; charset=UTF-8');
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
            $user = $request->user();
            [$payload, $errors] = $this->validateAndBuildPayload($request->all(), $user?->id);
            if ($errors !== null) {
                return response()->json([
                    'message' => 'Validación fallida',
                    'errors' => $errors,
                ], 422);
            }

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

    public function storeBulk(Request $request)
    {
        try {
            $user = $request->user();
            $items = $request->input('items');

            if (!is_array($items) || count($items) === 0) {
                return response()->json([
                    'message' => 'Debe enviar items',
                ], 422);
            }

            if (count($items) > 200) {
                return response()->json([
                    'message' => 'Máximo 200 registros por carga',
                ], 422);
            }

            $ids = [];
            $rowErrors = [];

            DB::beginTransaction();

            foreach ($items as $idx => $input) {
                if (!is_array($input)) {
                    $rowErrors[$idx] = ['item' => ['Formato inválido']];
                    continue;
                }

                [$payload, $errors] = $this->validateAndBuildPayload($input, $user?->id);
                if ($errors !== null) {
                    $rowErrors[$idx] = $errors;
                    continue;
                }

                $ids[] = DB::table('transacciones')->insertGetId($payload);
            }

            if (count($rowErrors) > 0) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Validación fallida',
                    'errors' => $rowErrors,
                ], 422);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transacciones creadas correctamente',
                'ids' => $ids,
            ], 201);
        } catch (\Throwable $e) {
            try {
                DB::rollBack();
            } catch (\Throwable $e2) {
            }

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $id = (int) $id;
            if ($id <= 0) {
                return response()->json([
                    'message' => 'ID inválido',
                ], 422);
            }

            $affected = DB::table('transacciones')
                ->where('id', '=', $id)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);

            if ($affected === 0) {
                return response()->json([
                    'message' => 'Transacción no encontrada',
                ], 404);
            }

            return response()->json([
                'message' => 'Transacción eliminada',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
