<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController
{
    public function login(): void
    {
        if (Auth::estaAutenticado()) {
            $this->redirigirPorRol((string) $_SESSION['usuario']['rol']);
        }

        $error = null;
        $mensaje = $_SESSION['mensaje_auth'] ?? null;
        unset($_SESSION['mensaje_auth']);
        $usuario = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            $usuarioEntrada = $_POST['usuario'] ?? '';
            $contrasena = $_POST['password'] ?? $_POST['contrasena'] ?? '';

            if (
                !is_string($token)
                || !hash_equals($_SESSION['csrf_token'], $token)
            ) {
                http_response_code(403);
                $error = 'La sesión del formulario venció. Recarga la página e inténtalo de nuevo.';
            } elseif (
                !is_string($usuarioEntrada)
                || !is_string($contrasena)
                || trim($usuarioEntrada) === ''
                || $contrasena === ''
                || strlen($usuarioEntrada) > 80
                || strlen($contrasena) > 4096
            ) {
                $error = 'Ingresa un usuario o correo y una contraseña válidos.';
            } else {
                $usuario = trim($usuarioEntrada);
                $datosUsuario = (new UsuarioModel())->autenticar($usuario, $contrasena);

                if ($datosUsuario === null) {
                    $error = 'Usuario o contraseña incorrectos.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['usuario'] = $datosUsuario;
                    $_SESSION['rol'] = $datosUsuario['rol'];
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $this->redirigirPorRol($datosUsuario['rol']);
                }
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function registrar(): void
    {
        if (Auth::estaAutenticado()) {
            $this->redirigirPorRol((string) $_SESSION['usuario']['rol']);
        }

        $error = null;
        $nombre = '';
        $usuario = '';
        $email = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            $nombreEntrada = $_POST['nombre'] ?? '';
            $usuarioEntrada = $_POST['usuario'] ?? '';
            $emailEntrada = $_POST['email'] ?? '';
            $contrasena = $_POST['password'] ?? '';

            if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                $error = 'La sesión del formulario venció. Recarga la página e inténtalo de nuevo.';
            } elseif (
                !is_string($nombreEntrada)
                || !is_string($usuarioEntrada)
                || !is_string($emailEntrada)
                || !is_string($contrasena)
            ) {
                http_response_code(400);
                $error = 'Los datos enviados no tienen un formato válido.';
            } else {
                $nombre = $nombreEntrada;
                $usuario = $usuarioEntrada;
                $email = $emailEntrada;

                try {
                    (new UsuarioModel())->crear(
                        $nombreEntrada,
                        $usuarioEntrada,
                        $emailEntrada,
                        $contrasena,
                        'cliente'
                    );
                    $_SESSION['mensaje_auth'] = 'Tu cuenta fue creada. Ya puedes iniciar sesión.';
                    header('Location: index.php?route=auth/login');
                    exit;
                } catch (DomainException $exception) {
                    $error = $exception->getMessage();
                } catch (PDOException $exception) {
                    if ($exception->getCode() !== '23000') {
                        throw $exception;
                    }

                    http_response_code(409);
                    $error = 'Ese usuario o correo ya está registrado.';
                }
            }
        }

        $csrfToken = $_SESSION['csrf_token'];
        require __DIR__ . '/../views/auth/registro.php';
    }

    public function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo 'Método no permitido.';
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo 'La solicitud de cierre de sesión no es válida. Recarga la página e inténtalo de nuevo.';
            return;
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }
        session_destroy();

        header('Location: index.php?route=auth/login');
    }

    private function redirigirPorRol(string $rol): void
    {
        $rutasPorRol = [
            'administrador' => 'inventario/index',
            'cocina' => 'cocina/index',
            'mesero' => 'pedidos/crear',
            'cliente' => 'productos/index',
        ];

        header('Location: index.php?route=' . ($rutasPorRol[$rol] ?? 'productos/index'));
        exit;
    }
}
