<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fdfbf7">
    <title>Iniciar sesión | Cafetería ADSO</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <style>
        :root {
            --cream: #fdfbf7;
            --coffee: #2b1e19;
            --coffee-muted: #755f52;
            --gold: #bd8751;
        }

        body {
            min-height: 100vh;
            color: var(--coffee);
            background:
                radial-gradient(ellipse at 12% 12%, rgba(189, 135, 81, .12), transparent 34rem),
                var(--cream);
        }

        .login-card {
            max-width: 460px;
            border: 1px solid rgba(43, 30, 25, .08);
            border-radius: 1rem;
            box-shadow: 0 1.5rem 5rem rgba(43, 30, 25, .12);
        }

        .login-brand {
            color: var(--coffee);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .login-kicker {
            color: var(--gold);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .form-control {
            min-height: 3.2rem;
            border-color: #e5ddd3;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 .2rem rgba(189, 135, 81, .18);
        }

        .btn-coffee {
            min-height: 3.2rem;
            border: 0;
            background: var(--coffee);
            color: #fff;
        }

        .btn-coffee:hover,
        .btn-coffee:focus {
            background: #50382d;
            color: #fff;
        }

        .text-coffee-muted {
            color: var(--coffee-muted);
        }
    </style>
</head>
<body>
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <section class="card login-card w-100">
            <div class="card-body p-4 p-md-5">
                <a class="login-brand text-decoration-none" href="index.php?route=productos/index">
                    Brewed &amp; Co.
                </a>
                <p class="login-kicker mt-4 mb-2">Bienvenido de nuevo</p>
                <h1 class="h2 fw-bold mb-2">Iniciar sesión</h1>
                <p class="text-coffee-muted mb-4">Accede con tu usuario o correo de Cafetería ADSO.</p>

                <?php if ($mensaje !== null): ?>
                    <div class="alert alert-success" role="status">
                        <?= htmlspecialchars((string) $mensaje, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if ($error !== null): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?route=auth/login">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario o correo</label>
                        <input
                            type="text"
                            class="form-control form-control-lg"
                            id="usuario"
                            name="usuario"
                            maxlength="80"
                            autocomplete="username"
                            value="<?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?>"
                            required
                            autofocus
                        >
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input
                            type="password"
                            class="form-control form-control-lg"
                            id="password"
                            name="password"
                            maxlength="4096"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-coffee btn-lg w-100">Entrar</button>
                </form>
                <p class="small text-coffee-muted text-center mt-4 mb-0">
                    ¿Aún no tienes cuenta?
                    <a href="index.php?route=auth/registro" class="link-dark fw-semibold">Regístrate</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>
