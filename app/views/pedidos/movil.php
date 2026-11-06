<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ordena desde tu mesa | Cafetería</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
</head>
<body class="bg-light">
    <header class="bg-dark text-white">
        <div class="container py-3 d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-semibold text-white text-decoration-none" href="index.php">Cafetería</a>
            <span class="badge text-bg-light">Mesa <?= (int) $mesa ?></span>
        </div>
    </header>

    <main class="container py-4 py-md-5" style="max-width: 900px;">
        <div class="mb-4">
            <h1 class="h2 fw-bold">Ordena desde tu mesa</h1>
            <p class="text-secondary mb-0">Elige tus productos y enviaremos el pedido a cocina.</p>
        </div>

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

        <?php if ($productos === []): ?>
            <div class="alert alert-info" role="status">No hay productos disponibles en este momento.</div>
        <?php else: ?>
            <form method="post" action="index.php?route=pedidos/movil&amp;mesa=<?= (int) $mesa ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="mesa" value="<?= (int) $mesa ?>">

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label for="cliente_nombre" class="form-label">Tu nombre (opcional)</label>
                        <input
                            type="text"
                            class="form-control"
                            id="cliente_nombre"
                            name="cliente_nombre"
                            maxlength="120"
                            autocomplete="name"
                            value="<?= htmlspecialchars($nombreCliente, ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <?php foreach ($productos as $producto): ?>
                        <?php
                        $productoId = (int) $producto['id'];
                        $nombre = htmlspecialchars((string) $producto['nombre'], ENT_QUOTES, 'UTF-8');
                        $descripcion = htmlspecialchars((string) ($producto['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $categoria = htmlspecialchars((string) $producto['categoria'], ENT_QUOTES, 'UTF-8');
                        $stock = (int) $producto['stock'];
                        $cantidad = $cantidadesSeleccionadas[$productoId] ?? 0;
                        ?>
                        <article class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <h2 class="h5 mb-1"><?= $nombre ?></h2>
                                        <span class="badge text-bg-secondary"><?= $categoria ?></span>
                                        <?php if ($descripcion !== ''): ?>
                                            <p class="text-secondary small mt-2 mb-0"><?= $descripcion ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-nowrap">
                                            $<?= number_format((float) $producto['precio'], 0, ',', '.') ?>
                                        </div>
                                        <small class="text-secondary">COP</small>
                                    </div>
                                </div>
                                <div class="row align-items-center mt-3">
                                    <label for="producto_<?= $productoId ?>" class="col form-label mb-0">
                                        Cantidad <span class="text-secondary">(<?= $stock ?> disponibles)</span>
                                    </label>
                                    <div class="col-4 col-sm-3">
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="producto_<?= $productoId ?>"
                                            name="productos[<?= $productoId ?>]"
                                            min="0"
                                            max="<?= $stock ?>"
                                            step="1"
                                            value="<?= htmlspecialchars((string) $cantidad, ENT_QUOTES, 'UTF-8') ?>"
                                            inputmode="numeric"
                                        >
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="sticky-bottom bg-light py-3 mt-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Enviar pedido a cocina
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
