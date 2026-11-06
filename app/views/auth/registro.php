<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fdfbf7">
    <title>Crear cuenta | Cafetería ADSO</title>
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
                radial-gradient(ellipse at 85% 12%, rgba(189, 135, 81, .14), transparent 32rem),
                var(--cream);
        }

        .register-card {
            max-width: 520px;
            border: 1px solid rgba(43, 30, 25, .08);
            border-radius: 1rem;
            box-shadow: 0 1.5rem 5rem rgba(43, 30, 25, .12);
        }

        .register-brand {
            color: var(--coffee);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .register-kicker {
            color: var(--gold);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .form-control {
            min-height: 3rem;
            border-color: #e5ddd3;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 .2rem rgba(189, 135, 81, .18);
        }

        .btn-coffee {
            min-height: 3rem;
            border: 0;
            background: var(--coffee);
            color: #fff;
        }

        .btn-coffee:hover,
        .btn-coffee:focus {
            background: #50382d;
            color: #fff;
        }
    </style>
</head>
<body>
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <section class="card register-card w-100">
            <div class="card-body p-4 p-md-5">
                <a class="register-brand text-decoration-none" href="index.php?route=productos/index">
                    Brewed &amp; Co.
                </a>
                <p class="register-kicker mt-4 mb-2">Únete a nosotros</p>
                <h1 class="h2 fw-bold mb-2">Crear cuenta</h1>
                <p class="text-secondary mb-4">Regístrate para iniciar sesión y hacer tus pedidos.</p>

                <?php if ($error !== null): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?route=auth/registro">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre completo</label>
                        <input
                            type="text"
                            class="form-control"
                            id="nombre"
                            name="nombre"
                            maxlength="120"
                            autocomplete="name"
                            value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
                            required
                            autofocus
                        >
                    </div>

                    <div class="mb-3">
                        <label for="usuario" class="form-label">Nombre de usuario</label>
                        <input
                            type="text"
                            class="form-control"
                            id="usuario"
                            name="usuario"
                            minlength="3"
                            maxlength="80"
                            pattern="[a-zA-Z0-9._\-]{3,80}"
                            autocomplete="username"
                            value="<?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >
                        <div class="form-text">Usa letras, números, punto, guion o guion bajo.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            maxlength="190"
                            autocomplete="email"
                            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            minlength="12"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >
                        <div class="form-text">Debe tener entre 12 y 72 caracteres.</div>
                    </div>

                    <button type="submit" class="btn btn-coffee btn-lg w-100">Crear cuenta de cliente</button>
                </form>

                <p class="small text-center mt-4 mb-0">
                    ¿Ya tienes cuenta?
                    <a href="index.php?route=auth/login" class="link-dark fw-semibold">Inicia sesión</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>
