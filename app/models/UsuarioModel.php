<?php
declare(strict_types=1);

class UsuarioModel
{
    private const ROLES_VALIDOS = ['administrador', 'mesero', 'cocina', 'cliente'];

    private PDO $conexion;

    public function __construct(?PDO $conexion = null)
    {
        $this->conexion = $conexion ?? require __DIR__ . '/../config/database.php';
    }

    /**
     * Comprueba credenciales y devuelve solo los datos de sesión necesarios.
     *
     * @return array{id: int, nombre: string, rol: string}|null
     */
    public function autenticar(string $usuario, string $contrasena): ?array
    {
        $consulta = $this->conexion->prepare(
            'SELECT id, nombre, usuario, email, password_hash, rol
             FROM usuarios
             WHERE (usuario = :usuario OR email = :email) AND activo = 1
             LIMIT 1'
        );
        $consulta->execute([
            'usuario' => $usuario,
            'email' => $usuario,
        ]);
        $registro = $consulta->fetch();

        if ($registro === false || !password_verify($contrasena, $registro['password_hash'])) {
            return null;
        }

        if (password_needs_rehash($registro['password_hash'], PASSWORD_DEFAULT)) {
            $nuevoHash = password_hash($contrasena, PASSWORD_DEFAULT);
            $actualizarHash = $this->conexion->prepare(
                'UPDATE usuarios SET password_hash = :password_hash WHERE id = :id'
            );
            $actualizarHash->execute([
                'password_hash' => $nuevoHash,
                'id' => $registro['id'],
            ]);
        }

        return [
            'id' => (int) $registro['id'],
            'nombre' => $registro['nombre'],
            'rol' => $registro['rol'],
        ];
    }

    /**
     * @return array<int, array{id: int, nombre: string, usuario: string, email: string, rol: string, activo: int}>
     */
    public function obtenerTodos(): array
    {
        $consulta = $this->conexion->query(
            'SELECT id, nombre, usuario, email, rol, activo
             FROM usuarios
             ORDER BY nombre ASC, id ASC'
        );

        return $consulta->fetchAll();
    }

    public function crear(
        string $nombre,
        string $usuario,
        string $email,
        string $contrasena,
        string $rol
    ): int {
        $nombre = trim($nombre);
        $usuario = trim($usuario);
        $email = trim($email);

        if ($nombre === '' || mb_strlen($nombre) > 120) {
            throw new DomainException('El nombre es obligatorio y no debe superar 120 caracteres.');
        }

        if (!preg_match('/\A[a-zA-Z0-9._-]{3,80}\z/', $usuario)) {
            throw new DomainException('El usuario debe tener entre 3 y 80 caracteres: letras, números, punto, guion o guion bajo.');
        }

        if (strlen($email) > 190 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException('Ingresa un correo electrónico válido.');
        }

        if (strlen($contrasena) < 12 || strlen($contrasena) > 72) {
            throw new DomainException('La contraseña debe tener entre 12 y 72 caracteres.');
        }

        if (!in_array($rol, self::ROLES_VALIDOS, true)) {
            throw new DomainException('Selecciona un rol válido.');
        }

        $insertar = $this->conexion->prepare(
            'INSERT INTO usuarios (nombre, usuario, email, password_hash, rol, activo)
             VALUES (:nombre, :usuario, :email, :password_hash, :rol, 1)'
        );
        $insertar->execute([
            'nombre' => $nombre,
            'usuario' => $usuario,
            'email' => $email,
            'password_hash' => password_hash($contrasena, PASSWORD_DEFAULT),
            'rol' => $rol,
        ]);

        return (int) $this->conexion->lastInsertId();
    }
}
