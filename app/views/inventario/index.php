<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fdfbf7">
    <title>Inventario | Cafetería ADSO</title>
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
            background: var(--cream);
        }

        .inventory-navbar {
            background: var(--coffee);
        }

        .inventory-title,
        .brand-name {
            font-family: Georgia, "Times New Roman", serif;
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .table > :not(caption) > * > * {
            padding: 1rem .85rem;
            vertical-align: middle;
        }

        .stock-low {
            color: #842029;
            background: #f8d7da;
        }

        .stock-ok {
            color: #146c43;
            background: #d1e7dd;
        }

        .stock-form {
            min-width: 230px;
        }

        .btn-coffee {
            border-color: var(--coffee);
            background: var(--coffee);
            color: #fff;
        }

        .btn-coffee:hover {
            border-color: var(--gold);
            background: var(--gold);
            color: #fff;
        }
    </style>
</head>
<body>
    <header class="inventory-navbar text-white">
        <nav class="navbar navbar-dark container py-3" aria-label="Navegación principal">
            <a class="navbar-brand brand-name" href="index.php?route=productos/index">Brewed &amp; Co.</a>
            <div class="d-flex align-items-center gap-3">
                <a class="nav-link text-white" href="index.php?route=admin/usuarios">Usuarios</a>
                <a class="nav-link text-white" href="index.php?route=admin/pedidos">Pedidos registrados</a>
                <a class="nav-link text-white" href="index.php?route=pedidos/crear">Crear pedido</a>
                <a class="nav-link text-white" href="index.php?route=productos/index">Ver catálogo</a>
                <form method="post" action="index.php?route=auth/logout" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-sm btn-outline-light">Cerrar sesión</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="container py-4 py-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <p class="small text-uppercase fw-bold mb-2" style="color: var(--gold); letter-spacing: .15em;">
                    Administración
                </p>
                <h1 class="inventory-title display-6 fw-semibold mb-2">Control de inventario</h1>
                <p class="text-secondary mb-0">Consulta existencias y realiza ajustes rápidos de stock.</p>
            </div>
            <span class="badge rounded-pill text-bg-dark"><?= count($productos) ?> productos</span>
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

        <section class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php if ($productos === []): ?>
                    <div class="p-4">
                        <div class="alert alert-info mb-0" role="status">
                            Aún no hay productos registrados en el inventario.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <caption class="visually-hidden">Productos y existencias del inventario</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Categoría</th>
                                    <th scope="col" class="text-nowrap">Precio</th>
                                    <th scope="col">Stock actual</th>
                                    <th scope="col">Ajustar existencias</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <?php
                                    $productoId = (int) $producto['id'];
                                    $stock = (int) $producto['stock'];
                                    ?>
                                    <tr>
                                        <th scope="row"><?= $productoId ?></th>
                                        <td class="fw-semibold">
                                            <?= htmlspecialchars((string) $producto['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td><?= htmlspecialchars((string) $producto['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-nowrap">
                                            $<?= number_format((float) $producto['precio'], 0, ',', '.') ?> COP
                                        </td>
                                        <td>
                                            <?php if ($stock <= 5): ?>
                                                <span class="badge stock-low">Stock bajo: <?= $stock ?></span>
                                            <?php else: ?>
                                                <span class="badge stock-ok"><?= $stock ?> disponibles</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form
                                                method="post"
                                                action="index.php?route=inventario/index"
                                                class="stock-form d-flex flex-wrap gap-2"
                                            >
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="producto_id" value="<?= $productoId ?>">
                                                <label class="visually-hidden" for="cantidad_<?= $productoId ?>">
                                                    Cantidad para el producto <?= htmlspecialchars((string) $producto['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                                </label>
                                                <input
                                                    type="number"
                                                    class="form-control form-control-sm"
                                                    id="cantidad_<?= $productoId ?>"
                                                    name="cantidad"
                                                    min="1"
                                                    max="1000000"
                                                    value="1"
                                                    required
                                                >
                                                <button type="submit" name="accion" value="sumar" class="btn btn-sm btn-outline-success">
                                                    + Añadir
                                                </button>
                                                <button
                                                    type="submit"
                                                    name="accion"
                                                    value="restar"
                                                    class="btn btn-sm btn-outline-danger"
                                                    <?= $stock === 0 ? 'disabled' : '' ?>
                                                >
                                                    − Retirar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
