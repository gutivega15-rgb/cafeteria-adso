<?php
declare(strict_types=1);

class ProductoModel
{
    private PDO $conexion;

    public function __construct(?PDO $conexion = null)
    {
        $this->conexion = $conexion ?? require __DIR__ . '/../config/database.php';
    }

    /**
     * Obtiene los productos activos que tienen existencias.
     *
     * @return array<int, array<string, mixed>>
     */
    public function obtenerMenuActivo(): array
    {
        $consulta = '
            SELECT id, nombre, descripcion, categoria, precio, stock
            FROM productos
            WHERE activo = 1 AND stock > 0
            ORDER BY categoria ASC, nombre ASC
        ';

        $sentencia = $this->conexion->query($consulta);

        return $sentencia->fetchAll();
    }
}
