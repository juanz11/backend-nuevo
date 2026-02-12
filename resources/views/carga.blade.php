<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1E4494">
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
                        <button type="submit" class="btn btn-primary px-4">Guardar</button>
                        <span class="text-muted" id="save_status"></span>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<script>
    (async function () {
        const fmt2 = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function parseDecimal(value) {
            let s = String(value ?? '').trim();
            if (!s) return 0;
            s = s.replace(/\s+/g, '');
            s = s.replace(/\./g, '');
            s = s.replace(/,/g, '.');
            const n = Number(s);
            return Number.isFinite(n) ? n : 0;
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
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const status = document.getElementById('save_status');
                if (status) status.textContent = 'Pendiente implementar guardado';
            });
        }
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
