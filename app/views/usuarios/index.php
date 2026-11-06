<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fdfbf7">
    <title>Usuarios | Cafetería ADSO</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <style>
        :root { --cream: #fdfbf7; --coffee: #2b1e19; --gold: #bd8751; }
        body { min-height: 100vh; color: var(--coffee); background: var(--cream); }
        .admin-navbar { background: var(--coffee); }
        .brand-name, h1, h2 { font-family: Georgia, "Times New Roman", serif; }
        .brand-name { font-size: 1.25rem; font-weight: 700; }
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 .2rem rgba(189, 135, 81, .18);
        }
        .btn-coffee { border-color: var(--coffee); background: var(--coffee); color: #fff; }
        .btn-coffee:hover { border-color: var(--gold); background: var(--gold); color: #fff; }
    </style>
</head>
<body>
    <header class="admin-navbar text-white">
        <nav class="navbar navbar-dark container py-3" aria-label="Navegación principal">
            <a class="navbar-brand brand-name" href="index.php?route=productos/index">Brewed &amp; Co.</a>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a class="nav-link text-white" href="index.php?route=inventario/index">Inventario</a>
                <a class="nav-link text-white" href="index.php?route=admin/pedidos">Pedidos registrados</a>
                <a class="nav-link text-white" href="index.php?route=pedidos/crear">Pedidos</a>
                <form method="post" action="index.php?route=auth/logout" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-sm btn-outline-light">Cerrar sesión</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="container py-4 py-lg-5">
        <div class="mb-4">
            <p class="small text-uppercase fw-bold mb-2" style="color: var(--gold); letter-spacing: .15em;">Administración</p>
            <h1 class="display-6 fw-semibold mb-2">Usuarios y equipo</h1>
            <p class="text-secondary mb-0">Crea cuentas para administración, meseros y cocina.</p>
        </div>

        <?php if ($mensaje !== null): ?>
            <div class="alert alert-success" role="status"><?= htmlspecialchars((string) $mensaje, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($error !== null): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="row g-4 align-items-start">
            <section class="col-12 col-lg-5" aria-labelledby="crear-usuario-titulo">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 id="crear-usuario-titulo" class="h4 mb-3">Agregar persona</h2>
                        <form method="post" action="index.php?route=admin/usuarios">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="120" required>
                            </div>
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="usuario"
                                    name="usuario"
                                    minlength="3"
                                    maxlength="80"
                                    pattern="[a-zA-Z0-9._\-]{3,80}"
                                    autocomplete="off"
                                    required
                                >
                                <div class="form-text">Letras, números, punto, guion y guion bajo.</div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="190" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña inicial</label>
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
                                <div class="form-text">Usa al menos 12 caracteres. La contraseña no se muestra después de crear la cuenta.</div>
                            </div>
                            <div class="mb-4">
                                <label for="rol" class="form-label">Rol</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="">Selecciona un rol</option>
                                    <option value="administrador">Administrador</option>
                                    <option value="mesero">Mesero</option>
                                    <option value="cocina">Cocina / Barista</option>
                                    <option value="cliente">Cliente</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-coffee w-100">Crear cuenta</button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="col-12 col-lg-7" aria-labelledby="usuarios-registrados-titulo">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 id="usuarios-registrados-titulo" class="h4 mb-0">Cuentas registradas</h2>
                            <span class="badge text-bg-dark"><?= count($usuarios) ?></span>
                        </div>
                        <?php if ($usuarios === []): ?>
                            <p class="text-secondary mb-0">No hay usuarios registrados.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <caption class="visually-hidden">Cuentas de usuario de la cafetería</caption>
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Persona</th>
                                            <th scope="col">Usuario / correo</th>
                                            <th scope="col">Rol</th>
                                            <th scope="col">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <?php
                                            $etiquetasRol = [
                                                'administrador' => 'Administrador',
                                                'mesero' => 'Mesero',
                                                'cocina' => 'Cocina',
                                                'cliente' => 'Cliente',
                                            ];
                                            ?>
                                            <tr>
                                                <td class="fw-semibold"><?= htmlspecialchars((string) $usuario['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <div><?= htmlspecialchars((string) $usuario['usuario'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <small class="text-secondary"><?= htmlspecialchars((string) $usuario['email'], ENT_QUOTES, 'UTF-8') ?></small>
                                                </td>
                                                <td><?= htmlspecialchars($etiquetasRol[$usuario['rol']] ?? $usuario['rol'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <?php if ((int) $usuario['activo'] === 1): ?>
                                                        <span class="badge text-bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge text-bg-secondary">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
