<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/CocinaModel.php';

class CocinaController
{
    private const ESTADOS = ['Pendiente', 'En preparación', 'Entregado'];

    public function index(): void
    {
        $pedidos = (new CocinaModel())->obtenerPedidos();
        $csrfToken = $_SESSION['csrf_token'];
        $mensaje = $_SESSION['mensaje_cocina'] ?? null;
        $error = $_SESSION['error_cocina'] ?? null;
        unset($_SESSION['mensaje_cocina'], $_SESSION['error_cocina']);

        require __DIR__ . '/../views/cocina/index.php';
    }

    public function datos(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

        echo json_encode(
            ['pedidos' => (new CocinaModel())->obtenerPedidos()],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    public function actualizarEstado(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo 'Método no permitido.';
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            $_SESSION['error_cocina'] = 'La sesión venció. Recarga el tablero e inténtalo de nuevo.';
            header('Location: index.php?route=cocina/index');
            return;
        }

        $pedidoId = filter_var($_POST['pedido_id'] ?? null, FILTER_VALIDATE_INT);
        $estadoActual = $_POST['estado_actual'] ?? null;
        $estadoNuevo = $_POST['estado_nuevo'] ?? null;

        if (
            $pedidoId === false
            || $pedidoId === null
            || !is_string($estadoActual)
            || !in_array($estadoActual, self::ESTADOS, true)
            || !is_string($estadoNuevo)
            || !in_array($estadoNuevo, self::ESTADOS, true)
        ) {
            http_response_code(400);
            $_SESSION['error_cocina'] = 'Los datos enviados para actualizar el pedido no son válidos.';
        } else {
            try {
                $actualizado = (new CocinaModel())->avanzarEstado(
                    (int) $pedidoId,
                    $estadoActual,
                    $estadoNuevo
                );
                $_SESSION[$actualizado ? 'mensaje_cocina' : 'error_cocina'] = $actualizado
                    ? sprintf('El pedido #%d ahora está %s.', $pedidoId, $estadoNuevo)
                    : 'El pedido cambió en otra pantalla. El tablero se actualizará con su estado actual.';
            } catch (DomainException $exception) {
                http_response_code(400);
                $_SESSION['error_cocina'] = $exception->getMessage();
            }
        }

        header('Location: index.php?route=cocina/index');
    }
}
