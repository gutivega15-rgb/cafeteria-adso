<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tablero de cocina | Cafetería</title>
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
            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text text-white-50">Cocina / Barista</span>
                <form method="post" action="index.php?route=auth/logout" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-sm btn-outline-light">Cerrar sesión</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="container-fluid container-xxl py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Pedidos de cocina</h1>
                <p class="text-secondary mb-0">
                    Actualización automática cada 10 segundos.
                    <span id="ultima-actualizacion" aria-live="polite"></span>
                </p>
            </div>
            <button type="button" class="btn btn-outline-primary" id="actualizar-ahora">
                Actualizar ahora
            </button>
        </div>

        <?php if ($mensaje !== null): ?>
            <div class="alert alert-success" role="status">
                <?= htmlspecialchars((string) $mensaje, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== null): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div id="error-actualizacion" class="alert alert-warning d-none" role="alert">
            No fue posible actualizar el tablero. Se volverá a intentar automáticamente.
        </div>

        <?php
        $columnas = [
            'Pendiente' => ['titulo' => 'Pendientes', 'color' => 'warning'],
            'En preparación' => ['titulo' => 'En preparación', 'color' => 'primary'],
            'Entregado' => ['titulo' => 'Entregados', 'color' => 'success'],
        ];
        ?>
        <div class="row g-3">
            <?php foreach ($columnas as $estado => $columna): ?>
                <section class="col-12 col-lg-4" aria-label="<?= htmlspecialchars($columna['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <h2 class="h5 mb-0"><?= htmlspecialchars($columna['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <span class="badge text-bg-<?= $columna['color'] ?>" data-count="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>">0</span>
                    </div>
                    <div class="d-grid gap-3" data-lane="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>">
                        <?php foreach ($pedidos as $pedido): ?>
                            <?php if ($pedido['estado'] !== $estado) { continue; } ?>
                            <article class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                        <h3 class="h5 mb-0">Pedido #<?= (int) $pedido['id'] ?></h3>
                                        <small class="text-secondary">
                                            <?= htmlspecialchars((string) $pedido['creado_en'], ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                    </div>
                                    <p class="mb-2">
                                        <strong>Cliente:</strong>
                                        <?= htmlspecialchars((string) ($pedido['cliente_nombre'] ?: 'Sin nombre'), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <?php if ($pedido['mesa'] !== null): ?>
                                        <p class="mb-2"><strong>Mesa:</strong> <?= (int) $pedido['mesa'] ?></p>
                                    <?php endif; ?>
                                    <ul class="list-group list-group-flush mb-3">
                                        <?php foreach ($pedido['detalles'] as $detalle): ?>
                                            <li class="list-group-item px-0">
                                                <span class="fw-semibold"><?= (int) $detalle['cantidad'] ?>×</span>
                                                <?= htmlspecialchars((string) $detalle['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <p class="fw-semibold mb-3">
                                        Total: $<?= number_format((float) $pedido['total'], 0, ',', '.') ?> COP
                                    </p>
                                    <?php if ($estado !== 'Entregado'): ?>
                                        <?php $siguienteEstado = $estado === 'Pendiente' ? 'En preparación' : 'Entregado'; ?>
                                        <form method="post" action="index.php?route=cocina/actualizar-estado">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="pedido_id" value="<?= (int) $pedido['id'] ?>">
                                            <input type="hidden" name="estado_actual" value="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="estado_nuevo" value="<?= htmlspecialchars($siguienteEstado, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="btn btn-<?= $columna['color'] ?> w-100">
                                                <?= $estado === 'Pendiente' ? 'Iniciar preparación' : 'Marcar como entregado' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                        <p class="text-secondary small mb-0 d-none" data-empty="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>">
                            No hay pedidos en este estado.
                        </p>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        (() => {
            const estados = ['Pendiente', 'En preparación', 'Entregado'];
            const token = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            const endpointDatos = 'index.php?route=cocina/datos';
            const intervalo = 10000;
            const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[character]);

            function tarjeta(pedido) {
                const detalles = pedido.detalles.map((detalle) =>
                    `<li class="list-group-item px-0"><span class="fw-semibold">${Number(detalle.cantidad)}×</span> ${escapeHtml(detalle.nombre)}</li>`
                ).join('');
                let accion = '';

                if (pedido.estado !== 'Entregado') {
                    const siguiente = pedido.estado === 'Pendiente' ? 'En preparación' : 'Entregado';
                    const color = pedido.estado === 'Pendiente' ? 'warning' : 'primary';
                    const texto = pedido.estado === 'Pendiente' ? 'Iniciar preparación' : 'Marcar como entregado';
                    accion = `<form method="post" action="index.php?route=cocina/actualizar-estado">
                        <input type="hidden" name="csrf_token" value="${escapeHtml(token)}">
                        <input type="hidden" name="pedido_id" value="${Number(pedido.id)}">
                        <input type="hidden" name="estado_actual" value="${escapeHtml(pedido.estado)}">
                        <input type="hidden" name="estado_nuevo" value="${escapeHtml(siguiente)}">
                        <button type="submit" class="btn btn-${color} w-100">${texto}</button>
                    </form>`;
                }

                return `<article class="card border-0 shadow-sm"><div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <h3 class="h5 mb-0">Pedido #${Number(pedido.id)}</h3>
                        <small class="text-secondary">${escapeHtml(pedido.creado_en)}</small>
                    </div>
                    <p class="mb-2"><strong>Cliente:</strong> ${escapeHtml(pedido.cliente_nombre || 'Sin nombre')}</p>
                    ${pedido.mesa === null ? '' : `<p class="mb-2"><strong>Mesa:</strong> ${Number(pedido.mesa)}</p>`}
                    <ul class="list-group list-group-flush mb-3">${detalles}</ul>
                    <p class="fw-semibold mb-3">Total: $${Number(pedido.total).toLocaleString('es-CO')} COP</p>
                    ${accion}
                </div></article>`;
            }

            async function actualizarTablero() {
                const alerta = document.getElementById('error-actualizacion');

                try {
                    const respuesta = await fetch(endpointDatos, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store'
                    });
                    if (!respuesta.ok) {
                        throw new Error('Respuesta HTTP ' + respuesta.status);
                    }

                    const datos = await respuesta.json();
                    estados.forEach((estado) => {
                        const pedidosEstado = datos.pedidos.filter((pedido) => pedido.estado === estado);
                        const carril = document.querySelector(`[data-lane="${estado}"]`);
                        const contador = document.querySelector(`[data-count="${estado}"]`);
                        const vacio = document.querySelector(`[data-empty="${estado}"]`);
                        carril.querySelectorAll('.card').forEach((card) => card.remove());
                        pedidosEstado.forEach((pedido) => carril.insertAdjacentHTML('beforeend', tarjeta(pedido)));
                        contador.textContent = String(pedidosEstado.length);
                        vacio.classList.toggle('d-none', pedidosEstado.length !== 0);
                    });

                    alerta.classList.add('d-none');
                    document.getElementById('ultima-actualizacion').textContent =
                        'Actualizado a las ' + new Date().toLocaleTimeString('es-CO');
                } catch (error) {
                    console.error('No fue posible actualizar el tablero de cocina.', error);
                    alerta.classList.remove('d-none');
                }
            }

            document.getElementById('actualizar-ahora').addEventListener('click', actualizarTablero);
            actualizarTablero();
            window.setInterval(actualizarTablero, intervalo);
        })();
    </script>
</body>
</html>
