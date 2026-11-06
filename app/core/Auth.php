<?php
declare(strict_types=1);

class Auth
{
    /**
     * Redirige a la pantalla de acceso o bloquea roles sin permiso.
     *
     * @param array<int, string> $rolesPermitidos
     */
    public static function exigirRol(array $rolesPermitidos): void
    {
        $usuario = $_SESSION['usuario'] ?? null;
        $rol = $_SESSION['rol'] ?? (is_array($usuario) ? ($usuario['rol'] ?? null) : null);

        if (!is_array($usuario) || !is_string($rol)) {
            header('Location: index.php?route=auth/login');
            exit;
        }

        if (!in_array($rol, $rolesPermitidos, true)) {
            http_response_code(403);
            echo 'No tienes permisos para acceder a esta sección.';
            exit;
        }
    }

    public static function estaAutenticado(): bool
    {
        return isset($_SESSION['usuario']['id'])
            && (isset($_SESSION['rol']) || isset($_SESSION['usuario']['rol']));
    }
}
