<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/PedidoModel.php';
require_once __DIR__ . '/../models/ProductoModel.php';

class PedidoController
{
    public function crear(): void
    {
        $modeloProducto = new ProductoModel();
        $productos = $modeloProducto->obtenerMenuActivo();
        $error = null;
        $cantidadesSeleccionadas = [];
        $nombreCliente = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cantidades = $_POST['productos'] ?? null;
            $token = $_POST['csrf_token'] ?? '';
            $nombre = $_POST['cliente_nombre'] ?? '';

            if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                $error = 'La sesión del formulario venció. Recarga la página e inténtalo de nuevo.';
            } elseif (!is_array($cantidades) || !is_string($nombre)) {
                $error = 'Los datos enviados no tienen un formato válido.';
            } else {
                $cantidadesSeleccionadas = $cantidades;
                $nombreCliente = $nombre;

                try {
                    $pedidoId = (new PedidoModel())->registrarPedido($cantidades, $nombre);
                    $_SESSION['mensaje_pedido'] = sprintf('Pedido #%d registrado correctamente.', $pedidoId);
                    header('Location: index.php?route=pedidos/crear');
                    exit;
                } catch (DomainException $exception) {
                    $error = $exception->getMessage();
                }
            }
        }

        $mensaje = $_SESSION['mensaje_pedido'] ?? null;
        unset($_SESSION['mensaje_pedido']);
        require __DIR__ . '/../views/pedidos/crear.php';
    }

    public function movil(): void
    {
        $mesaEntrada = $_SERVER['REQUEST_METHOD'] === 'POST'
            ? ($_POST['mesa'] ?? null)
            : ($_GET['mesa'] ?? null);
        $mesa = filter_var(
            $mesaEntrada,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 50]]
        );

        if ($mesa === false) {
            http_response_code(400);
            echo 'El enlace de la mesa no es válido.';
            return;
        }

        $productos = (new ProductoModel())->obtenerMenuActivo();
        $error = null;
        $mensaje = $_SESSION['mensaje_pedido_movil'] ?? null;
        unset($_SESSION['mensaje_pedido_movil']);
        $cantidadesSeleccionadas = [];
        $nombreCliente = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cantidades = $_POST['productos'] ?? null;
            $token = $_POST['csrf_token'] ?? '';
            $nombre = $_POST['cliente_nombre'] ?? '';

            if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                $error = 'La sesión del formulario venció. Recarga la página e inténtalo de nuevo.';
            } elseif (!is_array($cantidades) || !is_string($nombre)) {
                $error = 'Los datos enviados no tienen un formato válido.';
            } else {
                $cantidadesSeleccionadas = $cantidades;
                $nombreCliente = $nombre;

                try {
                    $pedidoId = (new PedidoModel())->registrarPedido(
                        $cantidades,
                        $nombre,
                        (int) $mesa
                    );
                    $_SESSION['mensaje_pedido_movil'] = sprintf(
                        '¡Pedido #%d enviado a cocina! Mesa %d.',
                        $pedidoId,
                        $mesa
                    );
                    header('Location: index.php?route=pedidos/movil&mesa=' . (int) $mesa);
                    exit;
                } catch (DomainException $exception) {
                    $error = $exception->getMessage();
                }
            }
        }

        require __DIR__ . '/../views/pedidos/movil.php';
    }
}
