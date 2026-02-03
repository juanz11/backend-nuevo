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
                <div>Resultados obtenidos: 0</div>
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
                    <tbody>
                        <tr>
                            <td>Salida</td>
                            <td class="text-end">0</td>
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                            <td>--/--/----</td>
                            <td class="text-end">0</td>
                            <td class="text-end">0</td>
                            <td>---</td>
                            <td><i class="fas fa-eye"></i></td>
                            <td><button type="button" class="btn"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="w-100 d-flex justify-content-center align-items-center" style="height: 50px;"></div>
            <div class="w-100 d-flex justify-content-center">
                <div class="text-center">
                    <ul class="pagination">
                        <li class="page-item disabled"><span class="page-link">Anterior</span></li>
                        <li class="page-item active"><span class="page-link">1</span></li>
                        <li class="page-item disabled"><span class="page-link">Siguiente</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        (async function () {
            try {
                const res = await fetch('/api/resumen', { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;

                const data = await res.json();
                const balance = Number(data.balance ?? 0);
                const formatted = new Intl.NumberFormat('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }).format(balance);

                const el = document.getElementById('balance');
                if (el) el.textContent = formatted;
            } catch (e) {
                //
            }
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
