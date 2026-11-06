<?php
declare(strict_types=1);

class InventarioModel
{
    private PDO $conexion;

    public function __construct(?PDO $conexion = null)
    {
        $this->conexion = $conexion ?? require __DIR__ . '/../config/database.php';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function obtenerProductos(): array
    {
        $consulta = $this->conexion->query(
            'SELECT id, nombre, categoria, precio, stock
             FROM productos
             ORDER BY categoria ASC, nombre ASC'
        );

        return $consulta->fetchAll();
    }

    public function ajustarStock(int $productoId, string $accion, int $cantidad): int
    {
        if ($productoId <= 0 || !in_array($accion, ['sumar', 'restar'], true)) {
            throw new DomainException('El producto o la operación seleccionada no es válida.');
        }

        if ($cantidad < 1 || $cantidad > 1000000) {
            throw new DomainException('La cantidad debe estar entre 1 y 1.000.000.');
        }

        $this->conexion->beginTransaction();

        try {
            $buscarProducto = $this->conexion->prepare(
                'SELECT stock FROM productos WHERE id = :id FOR UPDATE'
            );
            $buscarProducto->execute(['id' => $productoId]);
            $stockActual = $buscarProducto->fetchColumn();

            if ($stockActual === false) {
                throw new DomainException('El producto ya no existe.');
            }

            $nuevoStock = (int) $stockActual + ($accion === 'sumar' ? $cantidad : -$cantidad);
            if ($nuevoStock < 0) {
                throw new DomainException('No puedes dejar el inventario con stock negativo.');
            }
            if ($nuevoStock > 2147483647) {
                throw new DomainException('La cantidad supera el máximo permitido por la base de datos.');
            }

            $actualizar = $this->conexion->prepare(
                'UPDATE productos SET stock = :stock WHERE id = :id'
            );
            $actualizar->execute([
                'stock' => $nuevoStock,
                'id' => $productoId,
            ]);

            $this->conexion->commit();

            return $nuevoStock;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }
}
