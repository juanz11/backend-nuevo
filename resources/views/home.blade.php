<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1E4494">
    <title>Control de divisas</title>
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
        .table-responsive {
            position: relative;
            isolation: isolate;
            overflow: auto;
        }
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
        }
        .table tbody td {
            position: relative;
            z-index: 1;
        }
        .table tbody td .btn,
        .table tbody td i {
            position: relative;
            z-index: 1;
        }
        .table > :not(caption) > * > * {
            padding: .85rem .75rem;
        }
        .badge-soft {
            background: rgba(30, 68, 148, 0.10);
            color: #1E4494;
            border: 1px solid rgba(30, 68, 148, 0.18);
        }
        .badge-type {
            border: 1px solid rgba(15, 23, 42, 0.10);
        }
        .badge-type-entrada {
            background: rgba(34, 197, 94, 0.15);
            color: #166534;
            border-color: rgba(34, 197, 94, 0.25);
        }
        .badge-type-salida {
            background: rgba(239, 68, 68, 0.15);
            color: #7f1d1d;
            border-color: rgba(239, 68, 68, 0.25);
        }
        .badge-type-compra,
        .badge-type-venta {
            background: rgba(59, 130, 246, 0.15);
            color: #1e3a8a;
            border-color: rgba(59, 130, 246, 0.25);
        }
        .badge-type-cambio {
            background: rgba(168, 85, 247, 0.15);
            color: #581c87;
            border-color: rgba(168, 85, 247, 0.25);
        }
        .pagination .page-link {
            border-radius: .75rem;
        }
    </style>
</head>
<body>
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
        <div class="row g-3 align-items-stretch mb-3 mb-lg-4">
            <div class="col-12 col-lg-8">
                <div class="card card-soft rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <div class="text-uppercase small text-muted">Balance</div>
                                <div class="display-6 fw-semibold mb-0">USD <span id="balance">0,00</span></div>
                            </div>
                            <div class="d-none d-md-flex align-items-center justify-content-center rounded-4" style="width:56px;height:56px;background:rgba(30,68,148,.10);color:#1E4494;">
                                <i class="fas fa-sack-dollar fa-lg"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-muted">Divisas Totales</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card card-soft rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-semibold">Sesión</div>
                            <span class="badge badge-soft rounded-pill">Activa</span>
                        </div>
                        <div class="mt-3 text-muted small">{{ auth()->user()->email }}</div>
                        <div class="mt-3">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-dark w-100">
                                    <i class="fas fa-power-off me-1"></i>Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <form class="card card-soft rounded-4" method="GET" action="/">
                <div class="card-body p-4">
                <div class="row g-3">
                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Tipo de transacción</label>
                        <select name="tipo_transaccion_id" class="form-select">
                            <option value="0">Todos</option>
                            <option value="1">Compra</option>
                            <option value="2">Venta</option>
                            <option value="3">Entrada</option>
                            <option value="4">Salida</option>
                            <option value="5">Cambio</option>
                        </select>
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label" for="fltMonto">Monto USD</label>
                        <input id="fltMonto" name="monto" class="text-end form-control" value="0,00">
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Método de pago SNC</label>
                        <select name="metodo_salida_id" class="form-select">
                            <option value="0">Todos</option>
                        </select>
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Referencia salida</label>
                        <input name="referencia_salida" type="text" class="form-control" value="">
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Método entrada</label>
                        <select name="metodo_entrada_id" class="form-select">
                            <option value="0">Todos</option>
                        </select>
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Referencia entrada</label>
                        <input name="referencia_entrada" type="text" class="form-control" value="">
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Fecha</label>
                        <input name="fecha" type="date" class="form-control" value="">
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Tasa</label>
                        <input name="tasa" class="text-end form-control" value="0,00">
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Conversión en Bs</label>
                        <input name="conversion" class="text-end form-control" value="0,00">
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Comprador/Vendedor</label>
                        <input name="comprador_vendedor" type="text" class="form-control" value="">
                    </div>

                    <div class="mb-3 col-lg-2 col-12">
                        <label class="form-label">Observación</label>
                        <input name="observacion" type="text" class="form-control" value="">
                    </div>
                </div>

                <div>
                    <fieldset class="d-flex flex-wrap gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Filtrar</button>
                        <a href="/" class="btn btn-outline-secondary px-4">Limpiar</a>
                        <button type="button" class="btn btn-outline-success px-4">Excel<i class="fas fa-download ms-1"></i></button>
                        <button type="button" class="btn btn-outline-success px-4">PDF<i class="fas fa-download ms-1"></i></button>
                    </fieldset>
                </div>
                </div>
            </form>
        </div>

        <div class="mt-4" id="result_table">
            <div class="card card-soft rounded-4">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="fw-semibold">Transacciones</div>
                        <div class="text-muted">Resultados: <span id="results_count">0</span></div>
                    </div>

            <div class="table-responsive" style="max-height: 65vh;">
                <table class="text-center table table-hover table-striped align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Tipo</th>
                            <th>Monto USD <i class="fas fa-sort text-white"></i></th>
                            <th>Método de pago SNC</th>
                            <th>Ref de salida</th>
                            <th>Método entrada</th>
                            <th>Ref de entrada</th>
                            <th>Fecha <i class="fas fa-sort-down text-white"></i></th>
                            <th>Tasa (Bs) <i class="fas fa-sort text-white"></i></th>
                            <th>Conversión (Bs) <i class="fas fa-sort text-white"></i></th>
                            <th>Comp./Vend.</th>
                            <th>Observación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="transacciones_tbody"></tbody>
                </table>
            </div>

                    <div class="pt-3 d-flex justify-content-center">
                        <ul class="pagination mb-0" id="pagination"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (async function () {
            try {
                const fmt2 = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const fmtDate = new Intl.DateTimeFormat('es-VE');

                async function loadResumen() {
                    const res = await fetch('/api/resumen', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;

                    const contentType = res.headers.get('content-type') ?? '';
                    if (!contentType.includes('application/json')) return;

                    const data = await res.json();
                    const balance = Number(data.balance ?? 0);
                    const el = document.getElementById('balance');
                    if (el) el.textContent = fmt2.format(balance);
                }

                function getFiltersFromUrl() {
                    const params = new URLSearchParams(window.location.search);

                    return {
                        tipo_transaccion_id: params.get('tipo_transaccion_id') ?? '0',
                        monto: params.get('monto') ?? '',
                        metodo_salida_id: params.get('metodo_salida_id') ?? '0',
                        referencia_salida: params.get('referencia_salida') ?? '',
                        metodo_entrada_id: params.get('metodo_entrada_id') ?? '0',
                        referencia_entrada: params.get('referencia_entrada') ?? '',
                        fecha: params.get('fecha') ?? '',
                        tasa: params.get('tasa') ?? '',
                        conversion: params.get('conversion') ?? '',
                        comprador_vendedor: params.get('comprador_vendedor') ?? '',
                        observacion: params.get('observacion') ?? '',
                    };
                }

                function applyFiltersToForm(filters) {
                    const setValue = (name, value) => {
                        const el = document.querySelector(`[name="${name}"]`);
                        if (!el) return;
                        el.value = value;
                    };

                    setValue('tipo_transaccion_id', filters.tipo_transaccion_id);
                    setValue('monto', filters.monto);
                    setValue('metodo_salida_id', filters.metodo_salida_id);
                    setValue('referencia_salida', filters.referencia_salida);
                    setValue('metodo_entrada_id', filters.metodo_entrada_id);
                    setValue('referencia_entrada', filters.referencia_entrada);
                    setValue('fecha', filters.fecha);
                    setValue('tasa', filters.tasa);
                    setValue('conversion', filters.conversion);
                    setValue('comprador_vendedor', filters.comprador_vendedor);
                    setValue('observacion', filters.observacion);
                }

                async function loadMetodos(filters) {
                    const res = await fetch('/api/metodos', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;

                    const contentType = res.headers.get('content-type') ?? '';
                    if (!contentType.includes('application/json')) return;

                    const metodos = await res.json();
                    const opts = Array.isArray(metodos) ? metodos : [];

                    const buildOptions = (selectedValue) => {
                        const base = '<option value="0">Todos</option>';
                        const rows = opts.map((m) => {
                            const id = String(m.id ?? '');
                            const desc = escapeHtml(m.descripcion ?? '---');
                            const selected = id === String(selectedValue) ? ' selected' : '';
                            return `<option value="${escapeHtml(id)}"${selected}>${desc}</option>`;
                        }).join('');
                        return base + rows;
                    };

                    const selSalida = document.querySelector('[name="metodo_salida_id"]');
                    if (selSalida) {
                        selSalida.innerHTML = buildOptions(filters.metodo_salida_id);
                    }

                    const selEntrada = document.querySelector('[name="metodo_entrada_id"]');
                    if (selEntrada) {
                        selEntrada.innerHTML = buildOptions(filters.metodo_entrada_id);
                    }
                }

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function renderPagination(payload, state) {
                    const ul = document.getElementById('pagination');
                    if (!ul) return;

                    const current = Number(payload.current_page ?? state.page);
                    const last = Number(payload.last_page ?? current);
                    const prevDisabled = current <= 1;
                    const nextDisabled = current >= last;

                    ul.innerHTML = '';

                    const makeItem = (label, disabled, onClick, active) => {
                        const li = document.createElement('li');
                        li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
                        const a = document.createElement('a');
                        a.className = 'page-link';
                        a.href = '#';
                        a.textContent = label;
                        if (!disabled) {
                            a.addEventListener('click', (e) => {
                                e.preventDefault();
                                onClick();
                            });
                        }
                        li.appendChild(a);
                        return li;
                    };

                    ul.appendChild(makeItem('Anterior', prevDisabled, () => loadTransacciones(current - 1, state), false));
                    ul.appendChild(makeItem(String(current), true, () => {}, true));
                    ul.appendChild(makeItem('Siguiente', nextDisabled, () => loadTransacciones(current + 1, state), false));
                }

                async function loadTransacciones(page, state) {
                    const url = new URL('/api/transacciones', window.location.origin);
                    url.searchParams.set('page', String(page));
                    url.searchParams.set('orderBy', state.orderBy);
                    url.searchParams.set('orderDirection', state.orderDirection);
                    url.searchParams.set('perPage', String(state.perPage));

                    const filters = state.filters ?? {};
                    Object.entries(filters).forEach(([k, v]) => {
                        if (v === null || v === undefined) return;
                        const s = String(v);
                        if (s === '') return;
                        if ((k === 'tipo_transaccion_id' || k === 'metodo_salida_id' || k === 'metodo_entrada_id') && s === '0') return;
                        url.searchParams.set(k, s);
                    });
                    const tbody = document.getElementById('transacciones_tbody');
                    const countEl = document.getElementById('results_count');
                    if (tbody) {
                        tbody.innerHTML = `<tr><td colspan="12" class="text-muted">Cargando...</td></tr>`;
                    }

                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) {
                        let details = '';
                        try {
                            const contentType = res.headers.get('content-type') ?? '';
                            if (contentType.includes('application/json')) {
                                const err = await res.json();
                                if (err && typeof err.message === 'string' && err.message.length > 0) {
                                    details = `: ${err.message}`;
                                }
                            }
                        } catch (e) {
                        }
                        if (tbody) {
                            tbody.innerHTML = `<tr><td colspan="12">Error cargando transacciones (${escapeHtml(res.status)})${escapeHtml(details)}</td></tr>`;
                        }
                        if (countEl) countEl.textContent = '0';
                        renderPagination({ current_page: 1, last_page: 1 }, state);
                        return;
                    }

                    const contentType = res.headers.get('content-type') ?? '';
                    if (!contentType.includes('application/json')) {
                        if (tbody) {
                            tbody.innerHTML = `<tr><td colspan="12">Error cargando transacciones (respuesta no JSON)</td></tr>`;
                        }
                        if (countEl) countEl.textContent = '0';
                        renderPagination({ current_page: 1, last_page: 1 }, state);
                        return;
                    }

                    const payload = await res.json();
                    const data = Array.isArray(payload.data) ? payload.data : [];
                    if (tbody) {
                        if (data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="12">Sin resultados</td></tr>`;
                        } else {
                        tbody.innerHTML = data.map((t) => {
                            const tipo = t?.tipo_transaccion?.descripcion ?? '---';
                            const tipoSlug = String(tipo).toLowerCase();
                            const tipoClass =
                                tipoSlug === 'entrada' ? 'badge-type-entrada' :
                                tipoSlug === 'salida' ? 'badge-type-salida' :
                                tipoSlug === 'compra' ? 'badge-type-compra' :
                                tipoSlug === 'venta' ? 'badge-type-venta' :
                                tipoSlug === 'cambio' ? 'badge-type-cambio' :
                                'badge-soft';
                            const monto = fmt2.format(Number(t?.monto ?? 0));
                            const metodoSalida = t?.metodo_salida?.descripcion ?? '---';
                            const refSalida = t?.referencia_salida ?? '---';
                            const metodoEntrada = t?.metodo_entrada?.descripcion ?? '---';
                            const refEntrada = t?.referencia_entrada ?? '---';
                            const fecha = t?.fecha ? fmtDate.format(new Date(t.fecha)) : '--/--/----';
                            const tasa = fmt2.format(Number(t?.tasa ?? 0));
                            const conversion = fmt2.format(Number(t?.conversion ?? 0));
                            const cv = t?.comprador_vendedor ?? '---';
                            const obs = t?.observacion ?? '---';

                            return `
                                <tr>
                                    <td><span class="badge rounded-pill badge-type ${escapeHtml(tipoClass)}">${escapeHtml(tipo)}</span></td>
                                    <td class="text-end">${escapeHtml(monto)}</td>
                                    <td>${escapeHtml(metodoSalida)}</td>
                                    <td>${escapeHtml(refSalida)}</td>
                                    <td>${escapeHtml(metodoEntrada)}</td>
                                    <td>${escapeHtml(refEntrada)}</td>
                                    <td>${escapeHtml(fecha)}</td>
                                    <td class="text-end">${escapeHtml(tasa)}</td>
                                    <td class="text-end">${escapeHtml(conversion)}</td>
                                    <td>${escapeHtml(cv)}</td>
                                    <td>${escapeHtml(obs)}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" disabled title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                        }
                    }
                    if (countEl) countEl.textContent = String(payload.total ?? data.length);

                    renderPagination(payload, { ...state, page: Number(payload.current_page ?? page) });
                }

                const filters = getFiltersFromUrl();
                applyFiltersToForm(filters);
                await loadMetodos(filters);

                const state = { page: 1, orderBy: 'fecha', orderDirection: 'desc', perPage: 15, filters };
                await loadResumen();
                await loadTransacciones(state.page, state);
            } catch (e) {
                const tbody = document.getElementById('transacciones_tbody');
                const countEl = document.getElementById('results_count');
                if (tbody) tbody.innerHTML = `<tr><td colspan="12">Error cargando transacciones</td></tr>`;
                if (countEl) countEl.textContent = '0';
            }
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
