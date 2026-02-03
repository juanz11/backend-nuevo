<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1E4494">
    <title>Control de divisas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
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

    <div class="container">
        <div class="text-center p-5 text-muted">
            <h1>Divisas Totales: <span id="balance">0,00</span> USD</h1>
        </div>

        <div>
            <form class="bg-white border p-4" method="GET" action="/">
                <div class="row">
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
                    <fieldset class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="/" class="btn btn-secondary">Limpiar filtro</a>
                        <button type="button" class="btn btn-success">Excel<i class="fas fa-download ms-1"></i></button>
                        <button type="button" class="btn btn-success">PDF<i class="fas fa-download ms-1"></i></button>
                    </fieldset>
                </div>
            </form>
        </div>

        <div class="mt-4" id="result_table">
            <div class="w-100 border border-dark bg-dark rounded-top text-white p-2 d-flex justify-content-end">
                <div>Resultados obtenidos: <span id="results_count">0</span></div>
            </div>

            <div class="table-responsive">
                <table class="text-center table table-bordered">
                    <thead class="bg-dark text-white border border-dark">
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

            <div class="w-100 d-flex justify-content-center align-items-center" style="height: 50px;"></div>
            <div class="w-100 d-flex justify-content-center">
                <div class="text-center">
                    <ul class="pagination" id="pagination"></ul>
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

                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                    const tbody = document.getElementById('transacciones_tbody');
                    const countEl = document.getElementById('results_count');
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
                                    <td>${escapeHtml(tipo)}</td>
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
                                    <td><i class="fas fa-eye"></i></td>
                                    <td><button type="button" class="btn" disabled><i class="fas fa-trash"></i></button></td>
                                </tr>
                            `;
                        }).join('');
                        }
                    }
                    if (countEl) countEl.textContent = String(payload.total ?? data.length);

                    renderPagination(payload, { ...state, page: Number(payload.current_page ?? page) });
                }

                const state = { page: 1, orderBy: 'fecha', orderDirection: 'desc', perPage: 15 };
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
