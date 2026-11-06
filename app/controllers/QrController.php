<?php
declare(strict_types=1);

class QrController
{
    public function index(): void
    {
        $error = null;
        $cantidadMesas = filter_var(
            $_GET['cantidad'] ?? '10',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 50]]
        );

        if ($cantidadMesas === false) {
            $cantidadMesas = 10;
            $error = 'La cantidad de mesas debe estar entre 1 y 50. Se muestran 10 mesas.';
        }

        $baseUrl = getenv('APP_BASE_URL') ?: $this->urlActual();
        $partesUrl = parse_url($baseUrl);
        if (
            filter_var($baseUrl, FILTER_VALIDATE_URL) === false
            || !is_array($partesUrl)
            || !in_array($partesUrl['scheme'] ?? '', ['http', 'https'], true)
            || empty($partesUrl['host'])
            || !str_ends_with($partesUrl['path'] ?? '', '/index.php')
            || isset($partesUrl['user'])
            || isset($partesUrl['pass'])
            || isset($partesUrl['query'])
            || isset($partesUrl['fragment'])
        ) {
            http_response_code(500);
            $error = 'Configura APP_BASE_URL con una dirección http(s) válida que termine en index.php.';
            $baseUrl = 'http://localhost/cafeteria/index.php';
        }

        $codigos = [];
        for ($mesa = 1; $mesa <= $cantidadMesas; $mesa++) {
            $codigos[] = [
                'mesa' => $mesa,
                'url' => $baseUrl . '?' . http_build_query([
                    'route' => 'pedidos/movil',
                    'mesa' => $mesa,
                ]),
            ];
        }

        require __DIR__ . '/../views/qr/index.php';
    }

    private function urlActual(): string
    {
        $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $protocolo = $https ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
        if (isset($_SERVER['SERVER_PORT']) && !in_array((int) $_SERVER['SERVER_PORT'], [80, 443], true)) {
            $host .= ':' . (int) $_SERVER['SERVER_PORT'];
        }
        $script = $_SERVER['SCRIPT_NAME'] ?? '/cafeteria/index.php';

        return $protocolo . '://' . $host . $script;
    }
}
