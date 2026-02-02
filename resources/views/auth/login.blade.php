<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1E4494">
    <title>Control de divisas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="w-100 min-vh-100" style="background-image: url('https://control.sncpharma.com/img/login-bg.jpg'); background-size: cover; background-position: center;">
        <div class="w-100 d-flex justify-content-center pt-5 px-3">
            <div class="card p-4 bg-light" style="width: 100%; max-width: 420px;">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="text-center mb-3">
                        <img src="https://control.sncpharma.com/img/snclogo.svg" alt="logo-snc" width="150" height="150">
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label" for="email">Correo</label>
                        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña</label>
                        <input id="password" name="password" type="password" class="form-control" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-2 text-white">
            Copyright © SNC Pharma 2026
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
