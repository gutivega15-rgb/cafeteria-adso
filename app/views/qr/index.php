<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Códigos QR por mesa | Cafetería</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .qr-card { break-inside: avoid; box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="bg-light">
    <header class="bg-dark text-white no-print">
        <nav class="navbar navbar-dark container py-3" aria-label="Navegación principal">
            <a class="navbar-brand fw-semibold" href="index.php">Cafetería</a>
            <a class="nav-link text-white" href="index.php?route=productos/index">Ver catálogo</a>
        </nav>
    </header>

    <main class="container py-4 py-md-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold">QR para ordenar por mesa</h1>
                <p class="text-secondary">Cada código abre el menú móvil con la mesa correspondiente.</p>
            </div>
            <button type="button" class="btn btn-primary no-print" onclick="window.print()">
                Imprimir códigos
            </button>
        </div>

        <?php if ($error !== null): ?>
            <div class="alert alert-warning no-print" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-info no-print" role="note">
            Para escanear desde un celular, configura <code>APP_BASE_URL</code> con la dirección de tu PC en la red Wi-Fi,
            por ejemplo <code>http://192.168.1.20/cafeteria/index.php</code>. No uses <code>localhost</code> en el QR
            si el celular es otro dispositivo.
        </div>

        <form class="row g-2 align-items-end mb-4 no-print" method="get" action="index.php">
            <input type="hidden" name="route" value="qr/index">
            <div class="col-auto">
                <label for="cantidad" class="form-label">Cantidad de mesas</label>
                <input
                    type="number"
                    class="form-control"
                    id="cantidad"
                    name="cantidad"
                    min="1"
                    max="50"
                    value="<?= (int) $cantidadMesas ?>"
                >
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Generar</button>
            </div>
        </form>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3">
            <?php foreach ($codigos as $codigo): ?>
                <div class="col">
                    <article class="card qr-card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5">Mesa <?= (int) $codigo['mesa'] ?></h2>
                            <div
                                class="qr-code d-flex justify-content-center my-3"
                                data-url="<?= htmlspecialchars($codigo['url'], ENT_QUOTES, 'UTF-8') ?>"
                                aria-label="Código QR de la mesa <?= (int) $codigo['mesa'] ?>"
                            ></div>
                            <a
                                class="small text-break"
                                href="<?= htmlspecialchars($codigo['url'], ENT_QUOTES, 'UTF-8') ?>"
                            >Abrir menú de mesa <?= (int) $codigo['mesa'] ?></a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" referrerpolicy="no-referrer"></script>
    <script>
        document.querySelectorAll('.qr-code').forEach((contenedor) => {
            if (typeof QRCode === 'undefined') {
                contenedor.textContent = 'No se pudo cargar el generador QR. Usa el enlace de abajo.';
                return;
            }

            new QRCode(contenedor, {
                text: contenedor.dataset.url,
                width: 180,
                height: 180,
                correctLevel: QRCode.CorrectLevel.M
            });
        });
    </script>
</body>
</html>
