<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear pedido | Cafetería</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
</head>
<body class="bg-light">
    <header class="bg-dark text-white">
        <nav class="navbar navbar-dark container py-3" aria-label="Navegación principal">
            <a class="navbar-brand fw-semibold" href="index.php">Cafetería</a>
            <a class="nav-link text-white" href="index.php?route=productos/index">Ver menú</a>
        </nav>
    </header>

    <main class="container py-5">
        <div class="mb-4">
            <h1 class="display-6 fw-bold">Crear pedido</h1>
            <p class="text-secondary mb-0">Indica las cantidades de los productos que deseas agregar.</p>
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
            <div class="alert alert-info" role="status">
                No hay productos disponibles para agregar a un pedido.
            </div>
        <?php else: ?>
            <form method="post" action="index.php?route=pedidos/crear">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <label for="cliente_nombre" class="form-label">Nombre del cliente (opcional)</label>
                        <input
                            type="text"
                            class="form-control"
                            id="cliente_nombre"
                            name="cliente_nombre"
                            maxlength="120"
                            value="<?= htmlspecialchars($nombreCliente, ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                    <?php foreach ($productos as $producto): ?>
                        <?php
                        $productoId = (int) $producto['id'];
                        $nombre = htmlspecialchars((string) $producto['nombre'], ENT_QUOTES, 'UTF-8');
                        $categoria = htmlspecialchars((string) $producto['categoria'], ENT_QUOTES, 'UTF-8');
                        $descripcion = htmlspecialchars((string) ($producto['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
                        $stock = (int) $producto['stock'];
                        $valorCantidad = $cantidadesSeleccionadas[$productoId] ?? 0;
                        ?>
                        <div class="col">
                            <article class="card h-100 border-0 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between gap-2">
                                        <h2 class="h5"><?= $nombre ?></h2>
                                        <span class="badge text-bg-secondary align-self-start"><?= $categoria ?></span>
                                    </div>
                                    <?php if ($descripcion !== ''): ?>
                                        <p class="text-secondary small"><?= $descripcion ?></p>
                                    <?php endif; ?>
                                    <p class="fw-semibold mb-3">
                                        $<?= number_format((float) $producto['precio'], 0, ',', '.') ?> COP
                                        <span class="text-secondary fw-normal">· <?= $stock ?> disponibles</span>
                                    </p>
                                    <label for="producto_<?= $productoId ?>" class="form-label mt-auto">
                                        Cantidad
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="producto_<?= $productoId ?>"
                                        name="productos[<?= $productoId ?>]"
                                        min="0"
                                        max="<?= $stock ?>"
                                        step="1"
                                        value="<?= htmlspecialchars((string) $valorCantidad, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Registrar pedido</button>
                </div>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
