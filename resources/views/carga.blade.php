<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1E4494">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cargar - Control de divisas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background:
                linear-gradient(180deg, rgba(246, 248, 251, 0.92) 0%, rgba(246, 248, 251, 0.92) 100%),
                url('/WhatsApp%20Image%202026-02-04%20at%203.03.10%20PM%20(002).jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .app-navbar {
            backdrop-filter: blur(10px);
        }
        .card-soft {
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }
        .badge-soft {
            background: rgba(30, 68, 148, 0.10);
            color: #1E4494;
            border: 1px solid rgba(30, 68, 148, 0.18);
        }
    </style>
</head>
<body>
<noscript>Debe activar JavaScript para ejecutar esta aplicación.</noscript>

<nav class="navbar navbar-expand-lg bg-white border-bottom app-navbar">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="https://control.sncpharma.com/img/snclogo.svg" width="30" height="30" class="d-inline-block align-top" alt="logo-snc">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#basic-navbar-nav" aria-controls="basic-navbar-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="basic-navbar-nav">
            <div class="me-auto navbar-nav">
                <a class="nav-link" href="/"><i class="fas fa-home me-1"></i><span>Inicio</span></a>
                <a class="nav-link" href="/carga"><i class="fas fa-upload me-1"></i><span>Cargar</span></a>
                <a class="nav-link" href="/reporte"><i class="fas fa-file-excel me-1"></i><span>Generar reporte</span></a>
                <a class="nav-link" href="/saldos"><i class="fas fa-balance-scale me-1"></i><span>Saldos</span></a>
                <a class="nav-link" href="/eliminadas"><i class="fas fa-trash"></i><span>Transacciones Eliminadas</span></a>
            </div>

            <div class="ms-auto navbar-nav align-items-lg-center">
                <span class="nav-link text-muted">{{ auth()->user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link" style="text-decoration:none;">
                        <i class="fas fa-power-off me-1"></i>Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="container py-4 py-lg-5">
    <div class="text-center mb-4">
        <h1 class="fw-semibold">Cargar Compra/Venta de Divisas</h1>
        <div class="text-muted">Registra una transacción</div>
    </div>

    <div class="card card-soft rounded-4">
        <div class="card-body p-4 p-lg-4">
            <form id="form_carga" class="mt-1" autocomplete="off">
                <fieldset>
                    <div class="row g-3">
                        <div class="col-lg-4 col-12">
                            <label class="form-label">Tipo de Transacción</label>
                            <select name="tipo_transaccion_id" class="form-select">
                                <option value="1">Compra</option>
                                <option value="2">Venta</option>
                                <option value="3">Entrada</option>
                                <option value="4">Salida</option>
                                <option value="5">Cambio</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Monto USD</label>
                            <input name="monto" class="text-end form-control" value="0,00">
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Método de entrada</label>
                            <select name="metodo_entrada_id" class="form-select">
                                <option value="0">Cargando...</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Referencia de entrada</label>
                            <input name="referencia_entrada" class="form-control">
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Método de pago SNC</label>
                            <select name="metodo_salida_id" class="form-select">
                                <option value="0">Cargando...</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Referencia de salida</label>
                            <input name="referencia_salida" class="form-control">
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Fecha</label>
                            <input name="fecha" type="date" class="form-control">
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Tasa de cambio</label>
                            <input name="tasa" class="text-end form-control" value="0,00">
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Conversión en Bs</label>
                            <input id="conversion" disabled class="text-end form-control" value="0,00">
                        </div>

                        <div class="col-lg-4 col-12">
                            <label class="form-label">Comprador/Vendedor</label>
                            <input name="comprador_vendedor" class="form-control">
                        </div>

                        <div class="col-lg-8 col-12">
                            <label class="form-label" for="observacion">Observación</label>
                            <textarea rows="3" maxlength="100" name="observacion" id="observacion" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mt-4">
                        <button type="button" class="btn btn-outline-primary px-4" id="btn_add">Agregar a lista</button>
                        <button type="button" class="btn btn-primary px-4" id="btn_save_all">Guardar todos</button>
                        <span class="text-muted" id="save_status"></span>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div class="card card-soft rounded-4 mt-4">
        <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div class="fw-semibold">Registros en cola</div>
                <div class="text-muted">Total: <span id="queue_count">0</span></div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end">Tasa</th>
                            <th class="text-end">Conversión</th>
                            <th>Fecha</th>
                            <th>Mét. Entrada</th>
                            <th>Ref. Entrada</th>
                            <th>Mét. Salida</th>
                            <th>Ref. Salida</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="queue_tbody">
                        <tr><td colspan="11" class="text-muted">Sin registros</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    (async function () {
        const fmt2 = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

        const queue = [];
        const metodosMap = new Map();

        function tipoText(id) {
            const v = Number(id);
            if (v === 1) return 'Compra';
            if (v === 2) return 'Venta';
            if (v === 3) return 'Entrada';
            if (v === 4) return 'Salida';
            if (v === 5) return 'Cambio';
            return '---';
        }

        function renderQueue() {
            const tbody = document.getElementById('queue_tbody');
            const count = document.getElementById('queue_count');
            if (count) count.textContent = String(queue.length);
            if (!tbody) return;

            if (queue.length === 0) {
                tbody.innerHTML = '<tr><td colspan="11" class="text-muted">Sin registros</td></tr>';
                return;
            }

            tbody.innerHTML = queue.map((it, idx) => {
                const conv = Number(it.monto) * Number(it.tasa);
                const me = metodosMap.get(String(it.metodo_entrada_id ?? '')) ?? '---';
                const ms = metodosMap.get(String(it.metodo_salida_id ?? '')) ?? '---';
                return `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${tipoText(it.tipo_transaccion_id)}</td>
                        <td class="text-end">${fmt2.format(Number(it.monto ?? 0))}</td>
                        <td class="text-end">${fmt2.format(Number(it.tasa ?? 0))}</td>
                        <td class="text-end">${fmt2.format(conv)}</td>
                        <td>${String(it.fecha ?? '')}</td>
                        <td>${me}</td>
                        <td>${String(it.referencia_entrada ?? '')}</td>
                        <td>${ms}</td>
                        <td>${String(it.referencia_salida ?? '')}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-idx="${idx}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.querySelectorAll('button[data-idx]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const i = Number(btn.getAttribute('data-idx'));
                    if (!Number.isFinite(i)) return;
                    queue.splice(i, 1);
                    renderQueue();
                });
            });
        }

        function parseDecimal(value) {
            let s = String(value ?? '').trim();
            if (!s) return 0;
            s = s.replace(/\s+/g, '');
            s = s.replace(/\./g, '');
            s = s.replace(/,/g, '.');
            const n = Number(s);
            return Number.isFinite(n) ? n : 0;
        }

        function formatDecimalForApi(n) {
            const num = Number(n);
            if (!Number.isFinite(num)) return '0.00';
            return num.toFixed(2);
        }

        function updateConversion() {
            const montoEl = document.querySelector('[name="monto"]');
            const tasaEl = document.querySelector('[name="tasa"]');
            const convEl = document.getElementById('conversion');
            if (!montoEl || !tasaEl || !convEl) return;

            const monto = parseDecimal(montoEl.value);
            const tasa = parseDecimal(tasaEl.value);
            const conv = monto * tasa;
            convEl.value = fmt2.format(conv);
        }

        async function loadMetodos() {
            const res = await fetch('/api/metodos', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const contentType = res.headers.get('content-type') ?? '';
            if (!contentType.includes('application/json')) return;

            const metodos = await res.json();
            const opts = Array.isArray(metodos) ? metodos : [];

            metodosMap.clear();
            opts.forEach((m) => {
                const id = String(m.id ?? '');
                const desc = String(m.descripcion ?? '---');
                metodosMap.set(id, desc);
            });

            const html = opts.map((m) => {
                const id = String(m.id ?? '');
                const desc = String(m.descripcion ?? '---');
                return `<option value="${id}">${desc}</option>`;
            }).join('');

            const selEntrada = document.querySelector('[name="metodo_entrada_id"]');
            if (selEntrada) selEntrada.innerHTML = html;

            const selSalida = document.querySelector('[name="metodo_salida_id"]');
            if (selSalida) selSalida.innerHTML = html;
        }

        await loadMetodos();
        updateConversion();

        const montoEl = document.querySelector('[name="monto"]');
        const tasaEl = document.querySelector('[name="tasa"]');
        if (montoEl) montoEl.addEventListener('input', updateConversion);
        if (tasaEl) tasaEl.addEventListener('input', updateConversion);

        const form = document.getElementById('form_carga');
        const btnAdd = document.getElementById('btn_add');
        const btnSaveAll = document.getElementById('btn_save_all');

        function readCurrentPayload() {
            if (!form) return null;
            const formData = new FormData(form);
            const monto = parseDecimal(formData.get('monto'));
            const tasa = parseDecimal(formData.get('tasa'));

            return {
                tipo_transaccion_id: Number(formData.get('tipo_transaccion_id') ?? 0),
                monto: formatDecimalForApi(monto),
                metodo_entrada_id: Number(formData.get('metodo_entrada_id') ?? 0),
                metodo_salida_id: Number(formData.get('metodo_salida_id') ?? 0),
                referencia_entrada: String(formData.get('referencia_entrada') ?? ''),
                referencia_salida: String(formData.get('referencia_salida') ?? ''),
                fecha: String(formData.get('fecha') ?? ''),
                tasa: formatDecimalForApi(tasa),
                comprador_vendedor: String(formData.get('comprador_vendedor') ?? ''),
                observacion: String(formData.get('observacion') ?? ''),
            };
        }

        function validatePayload(payload) {
            const errs = [];
            const tipo = Number(payload?.tipo_transaccion_id ?? 0);
            const isEntrada = tipo === 3;
            const isSalida = tipo === 4;

            if (!tipo) errs.push('Debe seleccionar tipo de transacción');
            if (!payload?.fecha) errs.push('El campo fecha es obligatorio');
            if (payload?.monto === null || payload?.monto === undefined || payload?.monto === '') errs.push('El campo monto es obligatorio');
            if (payload?.tasa === null || payload?.tasa === undefined || payload?.tasa === '') errs.push('El campo tasa es obligatorio');

            if (!isSalida) {
                if (!Number(payload?.metodo_entrada_id ?? 0)) errs.push('El método de entrada es obligatorio');
                if (!String(payload?.referencia_entrada ?? '').trim()) errs.push('La referencia de entrada es obligatoria');
            }

            if (!isEntrada) {
                if (!Number(payload?.metodo_salida_id ?? 0)) errs.push('El método de pago SNC es obligatorio');
                if (!String(payload?.referencia_salida ?? '').trim()) errs.push('La referencia de salida es obligatoria');
            }

            return errs;
        }

        function flattenErrors(errors) {
            if (!errors) return [];
            if (Array.isArray(errors)) return errors.map(String);
            if (typeof errors !== 'object') return [String(errors)];

            const out = [];
            Object.entries(errors).forEach(([k, v]) => {
                if (Array.isArray(v)) {
                    v.forEach((msg) => out.push(`${k}: ${msg}`));
                    return;
                }
                if (v && typeof v === 'object') {
                    Object.entries(v).forEach(([k2, v2]) => {
                        if (Array.isArray(v2)) {
                            v2.forEach((msg) => out.push(`#${Number(k) + 1} ${k2}: ${msg}`));
                        } else {
                            out.push(`#${Number(k) + 1} ${k2}: ${String(v2)}`);
                        }
                    });
                    return;
                }
                out.push(`${k}: ${String(v)}`);
            });

            return out;
        }

        if (btnAdd) {
            btnAdd.addEventListener('click', () => {
                const status = document.getElementById('save_status');
                const payload = readCurrentPayload();
                if (!payload) return;

                const errs = validatePayload(payload);
                if (errs.length > 0) {
                    if (status) status.textContent = `Error: ${errs[0]}`;
                    return;
                }

                queue.push(payload);
                renderQueue();
                if (status) status.textContent = 'Agregado a la lista';
                if (form) form.reset();
                updateConversion();
            });
        }

        if (btnSaveAll) {
            btnSaveAll.addEventListener('click', async () => {
                const status = document.getElementById('save_status');
                if (queue.length === 0) {
                    if (status) status.textContent = 'No hay registros en cola';
                    return;
                }

                if (status) status.textContent = 'Guardando lista...';

                try {
                    const res = await fetch('/api/transacciones/bulk', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ items: queue }),
                    });

                    const contentType = res.headers.get('content-type') ?? '';
                    const isJson = contentType.includes('application/json');
                    const data = isJson ? await res.json() : null;

                    if (!res.ok) {
                        let msg = `Error guardando (${res.status})`;
                        if (data?.message) msg += `: ${data.message}`;
                        const details = flattenErrors(data?.errors);
                        if (details.length > 0) msg += ` | ${details[0]}`;
                        if (status) status.textContent = msg;
                        return;
                    }

                    queue.splice(0, queue.length);
                    renderQueue();
                    if (status) status.textContent = data?.message ?? 'Guardado';
                    if (form) form.reset();
                    updateConversion();
                } catch (err) {
                    if (status) status.textContent = 'Error guardando';
                }
            });
        }

        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
            });
        }

        renderQueue();
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
