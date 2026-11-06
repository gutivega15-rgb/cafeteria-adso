<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/UsuarioModel.php';

class UsuarioController
{
    public function index(): void
    {
        $modelo = new UsuarioModel();
        $error = null;
        $mensaje = $_SESSION['mensaje_usuarios'] ?? null;
        unset($_SESSION['mensaje_usuarios']);

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $email = $_POST['email'] ?? '';
            $contrasena = $_POST['password'] ?? '';
            $rol = $_POST['rol'] ?? '';

            if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                $error = 'La sesión del formulario venció. Recarga la página e inténtalo de nuevo.';
            } elseif (
                !is_string($nombre)
                || !is_string($usuario)
                || !is_string($email)
                || !is_string($contrasena)
                || !is_string($rol)
            ) {
                http_response_code(400);
                $error = 'Los datos enviados no tienen un formato válido.';
            } else {
                try {
                    $nuevoId = $modelo->crear($nombre, $usuario, $email, $contrasena, $rol);
                    $_SESSION['mensaje_usuarios'] = sprintf('La cuenta "%s" se creó correctamente (ID %d).', trim($usuario), $nuevoId);
                    header('Location: index.php?route=admin/usuarios');
                    exit;
                } catch (DomainException $exception) {
                    $error = $exception->getMessage();
                } catch (PDOException $exception) {
                    if ($exception->getCode() !== '23000') {
                        throw $exception;
                    }

                    http_response_code(409);
                    $error = 'Ese nombre de usuario o correo ya está registrado.';
                }
            }
        }

        $usuarios = $modelo->obtenerTodos();
        $csrfToken = $_SESSION['csrf_token'];
        require __DIR__ . '/../views/usuarios/index.php';
    }
}
