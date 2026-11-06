<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/InventarioModel.php';

class InventarioController
{
    public function index(): void
    {
        $modelo = new InventarioModel();
        $error = null;
        $mensaje = $_SESSION['mensaje_inventario'] ?? null;
        unset($_SESSION['mensaje_inventario']);

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            $productoId = filter_var($_POST['producto_id'] ?? null, FILTER_VALIDATE_INT);
            $cantidad = filter_var($_POST['cantidad'] ?? null, FILTER_VALIDATE_INT);
            $accion = $_POST['accion'] ?? null;

            if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                $error = 'La sesión del formulario venció. Recarga la página e inténtalo de nuevo.';
            } elseif (
                $productoId === false
                || $productoId === null
                || !is_string($accion)
                || $cantidad === false
                || $cantidad === null
            ) {
                http_response_code(400);
                $error = 'Los datos enviados para actualizar el inventario no son válidos.';
            } else {
                try {
                    $nuevoStock = $modelo->ajustarStock((int) $productoId, $accion, (int) $cantidad);
                    $_SESSION['mensaje_inventario'] = sprintf(
                        'Inventario actualizado. Nuevo stock: %d unidades.',
                        $nuevoStock
                    );
                    header('Location: index.php?route=inventario/index');
                    exit;
                } catch (DomainException $exception) {
                    $error = $exception->getMessage();
                }
            }
        }

        $productos = $modelo->obtenerProductos();
        $csrfToken = $_SESSION['csrf_token'];
        require __DIR__ . '/../views/inventario/index.php';
    }
}
