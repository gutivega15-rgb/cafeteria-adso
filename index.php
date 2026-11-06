<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
    'path' => '/',
]);
session_start();

require_once __DIR__ . '/app/core/Auth.php';
require_once __DIR__ . '/app/controllers/ProductoController.php';
require_once __DIR__ . '/app/controllers/PedidoController.php';
require_once __DIR__ . '/app/controllers/CocinaController.php';
require_once __DIR__ . '/app/controllers/QrController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/InventarioController.php';
require_once __DIR__ . '/app/controllers/UsuarioController.php';
require_once __DIR__ . '/app/controllers/PedidoAdminController.php';

$rutas = [
    'productos/index' => [ProductoController::class, 'index'],
    'pedidos/crear' => [PedidoController::class, 'crear'],
    'pedidos/movil' => [PedidoController::class, 'movil'],
    'qr/index' => [QrController::class, 'index'],
    'cocina/index' => [CocinaController::class, 'index'],
    'cocina/datos' => [CocinaController::class, 'datos'],
    'cocina/actualizar-estado' => [CocinaController::class, 'actualizarEstado'],
    'auth/login' => [AuthController::class, 'login'],
    'auth/registro' => [AuthController::class, 'registrar'],
    'auth/logout' => [AuthController::class, 'logout'],
    'inventario/index' => [InventarioController::class, 'index'],
    'admin/usuarios' => [UsuarioController::class, 'index'],
    'admin/pedidos' => [PedidoAdminController::class, 'index'],
];

$rolesPorRuta = [
    'pedidos/crear' => ['administrador', 'mesero', 'cliente'],
    'qr/index' => ['administrador', 'mesero'],
    'cocina/index' => ['cocina'],
    'cocina/datos' => ['cocina'],
    'cocina/actualizar-estado' => ['cocina'],
];

$rolesPorPrefijo = [
    'admin/' => ['administrador'],
    'inventario/' => ['administrador'],
    'reportes/' => ['administrador'],
];

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$ruta = $_GET['route'] ?? 'productos/index';

if (!is_string($ruta) || !isset($rutas[$ruta])) {
    http_response_code(404);
    echo 'La página solicitada no existe.';
    exit;
}

try {
    $rolesPermitidos = $rolesPorRuta[$ruta] ?? null;
    if ($rolesPermitidos === null) {
        foreach ($rolesPorPrefijo as $prefijo => $roles) {
            if (str_starts_with($ruta, $prefijo)) {
                $rolesPermitidos = $roles;
                break;
            }
        }
    }

    if ($rolesPermitidos !== null) {
        Auth::exigirRol($rolesPermitidos);
    }

    [$controlador, $accion] = $rutas[$ruta];
    (new $controlador())->$accion();
} catch (PDOException $error) {
    error_log('Error de base de datos en Cafetería: ' . $error->getMessage());
    http_response_code(500);
    echo 'No fue posible completar la operación. Verifica la conexión y la estructura de la base de datos.';
}
