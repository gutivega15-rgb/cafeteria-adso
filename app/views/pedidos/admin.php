<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fdfbf7">
    <title>Pedidos registrados | Cafetería ADSO</title>
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
        .status-pending { color: #664d03; background: #fff3cd; }
        .status-preparing { color: #084298; background: #cfe2ff; }
        .status-done { color: #0f5132; background: #d1e7dd; }
    </style>
</head>
<body>
    <header class="admin-navbar text-white">
        <nav class="navbar navbar-dark container py-3" aria-label="Navegación principal">
            <a class="navbar-brand brand-name" href="index.php?route=productos/index">Brewed &amp; Co.</a>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a class="nav-link text-white" href="index.php?route=inventario/index">Inventario</a>
                <a class="nav-link text-white" href="index.php?route=admin/usuarios">Usuarios</a>
                <a class="nav-link text-white" href="index.php?route=pedidos/crear">Crear pedido</a>
                <form method="post" action="index.php?route=auth/logout" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-sm btn-outline-light">Cerrar sesión</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="container py-4 py-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <p class="small text-uppercase fw-bold mb-2" style="color: var(--gold); letter-spacing: .15em;">Administración</p>
                <h1 class="display-6 fw-semibold mb-2">Pedidos registrados</h1>
                <p class="text-secondary mb-0">Consulta el estado, los productos y el valor de cada pedido reciente.</p>
            </div>
            <span class="badge rounded-pill text-bg-dark"><?= count($pedidos) ?> pedidos recientes</span>
        </div>

        <?php if ($pedidos === []): ?>
            <div class="alert alert-info" role="status">Todavía no hay pedidos registrados.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-lg-2 g-3">
                <?php foreach ($pedidos as $pedido): ?>
                    <?php
                    $claseEstado = match ($pedido['estado']) {
                        'Pendiente' => 'status-pending',
                        'En preparación' => 'status-preparing',
                        'Entregado' => 'status-done',
                        default => 'text-bg-secondary',
                    };
                    ?>
                    <div class="col">
                        <article class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <h2 class="h5 mb-1">Pedido #<?= (int) $pedido['id'] ?></h2>
                                        <small class="text-secondary">
                                            <?= htmlspecialchars((string) $pedido['creado_en'], ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                    </div>
                                    <span class="badge <?= $claseEstado ?>">
                                        <?= htmlspecialchars((string) $pedido['estado'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                                <p class="mb-2">
                                    <strong>Cliente:</strong>
                                    <?= htmlspecialchars((string) ($pedido['cliente_nombre'] ?: 'Sin nombre'), ENT_QUOTES, 'UTF-8') ?>
                                    <?php if ($pedido['mesa'] !== null): ?>
                                        <span class="ms-2"><strong>Mesa:</strong> <?= (int) $pedido['mesa'] ?></span>
                                    <?php endif; ?>
                                </p>
                                <ul class="list-group list-group-flush mb-3">
                                    <?php if ($pedido['detalles'] === []): ?>
                                        <li class="list-group-item px-0 text-secondary">El pedido no tiene detalles asociados.</li>
                                    <?php else: ?>
                                        <?php foreach ($pedido['detalles'] as $detalle): ?>
                                            <li class="list-group-item px-0">
                                                <strong><?= (int) $detalle['cantidad'] ?>×</strong>
                                                <?= htmlspecialchars((string) $detalle['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                                <div class="d-flex justify-content-between fw-semibold">
                                    <span>Total</span>
                                    <span>$<?= number_format((float) $pedido['total'], 0, ',', '.') ?> COP</span>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
