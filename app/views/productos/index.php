<?php
$usuarioActual = $_SESSION['usuario'] ?? null;
$rolActual = is_array($usuarioActual) ? ($usuarioActual['rol'] ?? '') : '';
$urlPedido = match ($rolActual) {
    'administrador', 'mesero', 'cliente' => 'index.php?route=pedidos/crear',
    'cocina' => 'index.php?route=cocina/index',
    default => 'index.php?route=pedidos/crear',
};
$etiquetaAccion = match ($rolActual) {
    'administrador', 'mesero', 'cliente' => 'Hacer pedido',
    'cocina' => 'Tablero de cocina',
    default => 'Hacer pedido',
};
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fdfbf7">
    <meta name="description" content="Café artesanal, bebidas preparadas al momento y panadería fresca en Cafetería ADSO.">
    <title>Brewed &amp; Co. | Café preparado con cariño</title>
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
            --coffee-soft: #6d5549;
            --gold: #bd8751;
            --line: #e9e1d7;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 5rem;
        }

        body {
            color: var(--coffee);
            background: var(--cream);
            font-family: Arial, Helvetica, sans-serif;
        }

        .site-navbar {
            background: rgba(253, 251, 247, .96);
            border-bottom: 1px solid rgba(43, 30, 25, .08);
        }

        .brand-mark {
            color: var(--coffee);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -.04em;
        }

        .brand-mark:hover {
            color: var(--gold);
        }

        .nav-link {
            color: var(--coffee-soft);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .14em;
        }

        .nav-link:hover,
        .nav-link:focus {
            color: var(--gold);
        }

        .btn-coffee {
            border: 1px solid var(--coffee);
            border-radius: 0;
            background: var(--coffee);
            color: #fff;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .12em;
            padding: .9rem 1.25rem;
            text-transform: uppercase;
        }

        .btn-coffee:hover,
        .btn-coffee:focus {
            border-color: var(--gold);
            background: var(--gold);
            color: #fff;
        }

        .btn-outline-coffee {
            border: 1px solid rgba(43, 30, 25, .35);
            border-radius: 0;
            color: var(--coffee);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .12em;
            padding: .9rem 1.25rem;
            text-transform: uppercase;
        }

        .btn-outline-coffee:hover,
        .btn-outline-coffee:focus {
            border-color: var(--coffee);
            background: var(--coffee);
            color: #fff;
        }

        .hero-copy {
            display: flex;
            min-height: 570px;
            align-items: center;
            padding: 5rem max(1.5rem, calc((100vw - 1320px) / 2));
        }

        .eyebrow {
            color: var(--gold);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .2em;
            text-transform: uppercase;
        }

        .hero-title {
            max-width: 620px;
            margin: 1rem 0 1.25rem;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(3rem, 5.7vw, 5.7rem);
            font-weight: 400;
            letter-spacing: -.065em;
            line-height: .99;
        }

        .hero-description {
            max-width: 470px;
            color: var(--coffee-soft);
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .hero-visual {
            position: relative;
            min-height: 440px;
            overflow: hidden;
            background: #84674f;
        }

        .hero-visual::after {
            position: absolute;
            z-index: 1;
            background: linear-gradient(180deg, rgba(28, 19, 15, .16), transparent 38%, rgba(28, 19, 15, .3));
            content: "";
            inset: 0;
            pointer-events: none;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            min-height: 440px;
            object-fit: cover;
            object-position: center;
            filter: saturate(.82) sepia(.12);
        }

        .origin-tag,
        .special-card {
            position: absolute;
            z-index: 2;
            background: rgba(253, 251, 247, .94);
            box-shadow: 0 12px 36px rgba(34, 22, 16, .15);
        }

        .origin-tag {
            top: 2rem;
            left: 2rem;
            padding: .85rem 1rem;
        }

        .origin-tag small,
        .special-card small {
            display: block;
            color: var(--coffee-soft);
            font-size: .63rem;
            font-weight: 700;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .origin-tag strong {
            display: block;
            margin-top: .2rem;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1rem;
            font-weight: 400;
        }

        .special-card {
            right: 2rem;
            bottom: 2rem;
            width: min(320px, calc(100% - 4rem));
            padding: 1.25rem 1.4rem;
        }

        .special-card h2 {
            margin: .45rem 0 0;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1.45rem;
        }

        .section-space {
            padding-top: 6rem;
            padding-bottom: 6rem;
        }

        .section-title {
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(2.2rem, 4vw, 3.5rem);
            font-weight: 400;
            letter-spacing: -.045em;
        }

        .section-copy {
            color: var(--coffee-soft);
            line-height: 1.8;
        }

        .menu-card {
            height: 100%;
            border: 1px solid var(--line);
            background: #fff;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem rgba(43, 30, 25, .08);
        }

        .menu-category {
            color: var(--gold);
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .story-panel {
            background: #f2ece3;
        }

        .gallery-tile {
            min-height: 240px;
            background-position: center;
            background-size: cover;
        }

        .gallery-one {
            background-image: linear-gradient(rgba(43, 30, 25, .08), rgba(43, 30, 25, .08)), url("https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=1000&q=85");
        }

        .gallery-two {
            background-image: linear-gradient(rgba(43, 30, 25, .08), rgba(43, 30, 25, .08)), url("https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1000&q=85");
        }

        .gallery-three {
            background-image: linear-gradient(rgba(43, 30, 25, .08), rgba(43, 30, 25, .08)), url("https://images.unsplash.com/photo-1445116572660-236099ec97a0?auto=format&fit=crop&w=1000&q=85");
        }

        .contact-section,
        .site-footer {
            background: var(--coffee);
            color: var(--cream);
        }

        .contact-section .section-copy,
        .site-footer a {
            color: rgba(253, 251, 247, .72);
        }

        .contact-section .btn-outline-coffee {
            border-color: rgba(253, 251, 247, .55);
            color: var(--cream);
        }

        .contact-section .btn-outline-coffee:hover {
            border-color: var(--cream);
            background: var(--cream);
            color: var(--coffee);
        }

        @media (min-width: 992px) {
            .hero-copy {
                min-height: min(720px, calc(100vh - 76px));
            }

            .hero-visual,
            .hero-image {
                min-height: min(720px, calc(100vh - 76px));
            }
        }

        @media (max-width: 991.98px) {
            .hero-copy {
                min-height: auto;
                padding: 5rem max(1.5rem, calc((100vw - 720px) / 2));
            }

            .hero-visual,
            .hero-image {
                min-height: 500px;
            }
        }

        @media (max-width: 575.98px) {
            .hero-copy {
                padding-top: 4rem;
                padding-bottom: 4rem;
            }

            .hero-visual,
            .hero-image {
                min-height: 390px;
            }

            .origin-tag {
                top: 1rem;
                left: 1rem;
            }

            .special-card {
                right: 1rem;
                bottom: 1rem;
                width: calc(100% - 2rem);
            }

            .section-space {
                padding-top: 4rem;
                padding-bottom: 4rem;
            }

            .gallery-tile {
                min-height: 200px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <header class="site-navbar sticky-top">
        <nav class="navbar navbar-expand-lg container-xl py-3" aria-label="Main navigation">
            <a class="navbar-brand brand-mark" href="index.php">Brewed <span class="text-warning">&amp;</span> Co.</a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavigation"
                aria-controls="mainNavigation"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-3 py-3 py-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#home">INICIO</a></li>
                    <li class="nav-item"><a class="nav-link" href="#menu">MENÚ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">NOSOTROS</a></li>
                    <li class="nav-item"><a class="nav-link" href="#gallery">GALERÍA</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">CONTACTO</a></li>
                </ul>
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2">
                    <?php if (is_array($usuarioActual)): ?>
                        <span class="small text-secondary text-center">
                            <?= htmlspecialchars((string) ($usuarioActual['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if ($rolActual === 'administrador'): ?>
                            <a class="btn btn-outline-coffee" href="index.php?route=admin/pedidos">Pedidos registrados</a>
                            <a class="btn btn-outline-coffee" href="index.php?route=inventario/index">Inventario</a>
                            <a class="btn btn-outline-coffee" href="index.php?route=admin/usuarios">Usuarios</a>
                        <?php endif; ?>
                        <form method="post" action="index.php?route=auth/logout" class="m-0">
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            >
                            <button type="submit" class="btn btn-outline-coffee w-100">Cerrar sesión</button>
                        </form>
                    <?php else: ?>
                        <a class="btn btn-outline-coffee" href="index.php?route=auth/login">Iniciar sesión</a>
                        <a class="btn btn-outline-coffee" href="index.php?route=auth/registro">Crear cuenta</a>
                    <?php endif; ?>
                    <a class="btn btn-coffee" href="<?= htmlspecialchars($urlPedido, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($etiquetaAccion, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section id="home" class="container-fluid px-0">
            <div class="row g-0">
                <div class="col-lg-6 order-1">
                    <div class="hero-copy">
                        <div>
                            <p class="eyebrow mb-0">Café de origen · Momentos para disfrutar</p>
                            <h1 class="hero-title">Cada taza cuenta una historia</h1>
                            <p class="hero-description mb-4">
                                Café seleccionado con dedicación, bebidas preparadas a mano y un espacio acogedor
                                para hacer una pausa. Descubre tu próxima taza favorita, recién preparada para ti.
                            </p>
                            <div class="d-flex flex-wrap gap-3">
                                <a class="btn btn-coffee" href="#menu">Explorar el menú</a>
                                <a class="btn btn-outline-coffee" href="#about">Nuestra historia</a>
                            </div>
                            <p class="small text-secondary mt-4 mb-0">Tostado local. Preparado con cariño.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-2">
                    <div class="hero-visual">
                        <img
                            class="hero-image"
                            src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&amp;fit=crop&amp;w=1600&amp;q=90"
                            alt="Café recién preparado servido en una mesa cálida"
                            fetchpriority="high"
                        >
                        <div class="origin-tag">
                            <small>Origen único</small>
                            <strong>Ethiopia · Yirgacheffe</strong>
                        </div>
                        <div class="special-card">
                            <small>Especial del día</small>
                            <h2>Honey Oat Latte</h2>
                            <p class="small text-secondary mb-0 mt-1">Espresso, leche de avena y un toque de miel.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="menu" class="section-space">
            <div class="container">
                <div class="row align-items-end mb-4 mb-lg-5">
                    <div class="col-lg-8">
                        <p class="eyebrow">Hecho al momento, todos los días</p>
                        <h2 class="section-title mb-2">Un sabor para cada momento.</h2>
                        <p class="section-copy mb-0">Descubre nuestra selección, preparada al momento en la cafetería.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a class="btn btn-outline-coffee" href="#menu">
                            Ver el menú
                        </a>
                    </div>
                </div>

                <?php if ($productos === []): ?>
                    <div class="alert alert-light border" role="status">
                        Our menu is being prepared. Please check back soon.
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4">
                        <?php foreach ($productos as $producto): ?>
                            <?php
                            $nombre = htmlspecialchars((string) $producto['nombre'], ENT_QUOTES, 'UTF-8');
                            $descripcion = htmlspecialchars((string) ($producto['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $categoria = htmlspecialchars((string) $producto['categoria'], ENT_QUOTES, 'UTF-8');
                            ?>
                            <div class="col">
                                <article class="menu-card p-4">
                                    <p class="menu-category mb-2"><?= $categoria ?></p>
                                    <h3 class="h5 mb-2"><?= $nombre ?></h3>
                                    <p class="section-copy small mb-4">
                                        <?= $descripcion !== '' ? $descripcion : 'Prepared with care by our team.' ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="fw-bold">
                                            $<?= number_format((float) $producto['precio'], 0, ',', '.') ?> COP
                                        </span>
                                        <small class="text-secondary"><?= (int) $producto['stock'] ?> disponibles</small>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section id="about" class="story-panel section-space">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8">
                        <p class="eyebrow">Más que un café por la mañana</p>
                        <h2 class="section-title">Un lugar de barrio donde todos son bienvenidos.</h2>
                        <p class="section-copy mt-3 mb-4">
                            Creemos que una buena taza puede hacer especial cualquier día. Por eso elegimos
                            ingredientes de calidad, preparamos cada bebida al instante y recibimos a cada persona como en casa.
                        </p>
                        <a class="btn btn-outline-coffee" href="<?= htmlspecialchars($urlPedido, ENT_QUOTES, 'UTF-8') ?>">
                            Te esperamos
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="gallery" class="section-space">
            <div class="container">
                <div class="text-center mb-4 mb-lg-5">
                    <p class="eyebrow">Un vistazo a nuestros favoritos</p>
                    <h2 class="section-title mb-0">Para disfrutar sin afán.</h2>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-md-4"><div class="gallery-tile gallery-one" role="img" aria-label="Barista preparando café"></div></div>
                    <div class="col-12 col-md-4"><div class="gallery-tile gallery-two" role="img" aria-label="Café servido en una taza"></div></div>
                    <div class="col-12 col-md-4"><div class="gallery-tile gallery-three" role="img" aria-label="Interior acogedor de una cafetería"></div></div>
                </div>
            </div>
        </section>

        <section id="contact" class="contact-section section-space">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <p class="eyebrow">Tu próxima taza favorita te espera</p>
                        <h2 class="section-title mb-2">Pasa y quédate un rato.</h2>
                        <p class="section-copy mb-0">¿Tienes preguntas o una solicitud especial? Nos encantará atenderte.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a class="btn btn-outline-coffee" href="index.php?route=auth/login">Iniciar sesión</a>
                        <a class="btn btn-coffee ms-sm-2 mt-2 mt-sm-0" href="<?= htmlspecialchars($urlPedido, ENT_QUOTES, 'UTF-8') ?>">
                            Hacer pedido
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer py-4">
        <div class="container d-flex flex-column flex-sm-row justify-content-between gap-2">
            <span class="brand-mark text-white">Brewed &amp; Co.</span>
            <small>&copy; <?= date('Y') ?> Cafetería ADSO. Hecho con cariño.</small>
        </div>
    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
    ></script>
</body>
</html>
