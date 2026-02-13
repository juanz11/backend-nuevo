<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transacciones</title>
    <style>
        @page { size: legal landscape; margin: 12mm; }
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #111827; }
        .meta { display:flex; justify-content:space-between; align-items:flex-end; gap:12px; margin-bottom: 10px; }
        .title { font-size: 16pt; font-weight: 700; }
        .subtitle { font-size: 10pt; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #D9D9D9; padding: 4px 6px; vertical-align: middle; }
        th { background: #1F4E79; color: #ffffff; font-weight: 700; text-align: center; white-space: nowrap; }
        td.num { text-align: right; }
        td.txt { text-align: left; }
        td.date { text-align: center; white-space: nowrap; }
        .small { font-size: 9.5pt; }
        .footer { margin-top: 10px; font-size: 9.5pt; color: #6b7280; }
        @media print {
            .no-print { display:none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:10px;">
        <button onclick="window.print()">Imprimir / Guardar como PDF</button>
    </div>

    <div class="meta">
        <div>
            <div class="title">Reporte de Transacciones</div>
            <div class="subtitle">Generado: {{ $generatedAt }}</div>
        </div>
        <div class="subtitle">
            @if($filtersText)
                Filtros: {{ $filtersText }}
            @endif
        </div>
    </div>

    <table class="small">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Monto USD</th>
                <th>Método salida</th>
                <th>Ref salida</th>
                <th>Método entrada</th>
                <th>Ref entrada</th>
                <th>Fecha</th>
                <th>Tasa (Bs)</th>
                <th>Conversión (Bs)</th>
                <th>Comp./Vend.</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
                <tr>
                    <td class="txt">{{ $r['tipo_transaccion__descripcion'] ?? '---' }}</td>
                    <td class="num">{{ $r['monto_fmt'] }}</td>
                    <td class="txt">{{ $r['metodo_salida__descripcion'] ?? '---' }}</td>
                    <td class="txt">{{ $r['referencia_salida'] ?? '' }}</td>
                    <td class="txt">{{ $r['metodo_entrada__descripcion'] ?? '---' }}</td>
                    <td class="txt">{{ $r['referencia_entrada'] ?? '' }}</td>
                    <td class="date">{{ $r['fecha'] ?? '' }}</td>
                    <td class="num">{{ $r['tasa_fmt'] }}</td>
                    <td class="num">{{ $r['conversion_fmt'] }}</td>
                    <td class="txt">{{ $r['comprador_vendedor'] ?? '' }}</td>
                    <td class="txt">{{ $r['observacion'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total filas: {{ count($rows) }}
    </div>

    <script>
        (function(){
            try {
                const params = new URLSearchParams(window.location.search);
                if (params.get('print') === '1') {
                    window.print();
                }
            } catch (e) {}
        })();
    </script>
</body>
</html>
