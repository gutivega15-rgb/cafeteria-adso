<?php
declare(strict_types=1);

class PedidoModel
{
    private const ESTADOS_VALIDOS = [
        'Pendiente',
        'En preparación',
        'Entregado',
    ];

    private PDO $conexion;

    public function __construct(?PDO $conexion = null)
    {
        $this->conexion = $conexion ?? require __DIR__ . '/../config/database.php';
    }

    /**
     * Registra el pedido y sus detalles como una sola operación.
     *
     * @param array<int|string, int|string> $productos
     */
    public function registrarPedido(
        array $productos,
        ?string $nombreCliente = null,
        ?int $mesa = null
    ): int
    {
        $productos = $this->normalizarProductos($productos);
        $nombreCliente = $nombreCliente !== null ? trim($nombreCliente) : '';

        if (mb_strlen($nombreCliente) > 120) {
            throw new DomainException('El nombre del cliente no puede superar 120 caracteres.');
        }

        if ($mesa !== null && ($mesa < 1 || $mesa > 50)) {
            throw new DomainException('El número de mesa debe estar entre 1 y 50.');
        }

        $this->conexion->beginTransaction();

        try {
            $buscarProducto = $this->conexion->prepare(
                'SELECT id, nombre, precio, stock
                 FROM productos
                 WHERE id = :id AND activo = 1
                 FOR UPDATE'
            );
            $detalles = [];
            $totalCentavos = 0;

            foreach ($productos as $productoId => $cantidad) {
                $buscarProducto->execute(['id' => $productoId]);
                $producto = $buscarProducto->fetch();

                if ($producto === false) {
                    throw new DomainException('Uno de los productos seleccionados ya no está disponible.');
                }

                if ((int) $producto['stock'] < $cantidad) {
                    throw new DomainException(
                        sprintf('No hay existencias suficientes de "%s".', $producto['nombre'])
                    );
                }

                $precioUnitarioCentavos = $this->aCentavos((string) $producto['precio']);
                if ($precioUnitarioCentavos < 0) {
                    throw new DomainException('No se puede incluir un producto con precio negativo.');
                }

                $subtotalCentavos = $precioUnitarioCentavos * $cantidad;
                $totalCentavos += $subtotalCentavos;
                $detalles[] = [
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $this->formatearPrecio($precioUnitarioCentavos),
                    'subtotal' => $this->formatearPrecio($subtotalCentavos),
                ];
            }

            $insertarPedido = $this->conexion->prepare(
                "INSERT INTO pedidos (cliente_nombre, tipo_servicio, mesa, estado, total)
                 VALUES (:cliente_nombre, :tipo_servicio, :mesa, 'Pendiente', :total)"
            );
            $insertarPedido->execute([
                'cliente_nombre' => $nombreCliente !== '' ? $nombreCliente : null,
                'tipo_servicio' => $mesa !== null ? 'Mesa' : 'Mostrador',
                'mesa' => $mesa,
                'total' => $this->formatearPrecio($totalCentavos),
            ]);
            $pedidoId = (int) $this->conexion->lastInsertId();

            $insertarDetalle = $this->conexion->prepare(
                'INSERT INTO pedido_detalles
                    (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                 VALUES
                    (:pedido_id, :producto_id, :cantidad, :precio_unitario, :subtotal)'
            );
            $actualizarStock = $this->conexion->prepare(
                'UPDATE productos
                 SET stock = stock - :cantidad_actualizar
                 WHERE id = :id AND activo = 1 AND stock >= :cantidad_disponible'
            );

            foreach ($detalles as $detalle) {
                $insertarDetalle->execute([
                    'pedido_id' => $pedidoId,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal'],
                ]);
                $actualizarStock->execute([
                    'cantidad_actualizar' => $detalle['cantidad'],
                    'cantidad_disponible' => $detalle['cantidad'],
                    'id' => $detalle['producto_id'],
                ]);

                if ($actualizarStock->rowCount() !== 1) {
                    throw new DomainException('No fue posible reservar el inventario seleccionado.');
                }
            }

            $this->conexion->commit();

            return $pedidoId;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    public function actualizarEstado(int $pedidoId, string $estado): bool
    {
        if ($pedidoId <= 0) {
            throw new InvalidArgumentException('El identificador del pedido no es válido.');
        }

        if (!in_array($estado, self::ESTADOS_VALIDOS, true)) {
            throw new InvalidArgumentException('El estado indicado no es válido.');
        }

        $sentencia = $this->conexion->prepare(
            'UPDATE pedidos SET estado = :estado WHERE id = :id'
        );
        $sentencia->execute([
            'estado' => $estado,
            'id' => $pedidoId,
        ]);

        if ($sentencia->rowCount() > 0) {
            return true;
        }

        $verificarPedido = $this->conexion->prepare('SELECT 1 FROM pedidos WHERE id = :id');
        $verificarPedido->execute(['id' => $pedidoId]);

        return $verificarPedido->fetchColumn() !== false;
    }

    /**
     * @param array<int|string, int|string> $productos
     * @return array<int, int>
     */
    private function normalizarProductos(array $productos): array
    {
        $normalizados = [];

        foreach ($productos as $id => $cantidad) {
            if (!is_int($id) && !is_string($id)) {
                throw new DomainException('La selección contiene un producto no válido.');
            }

            if (filter_var($id, FILTER_VALIDATE_INT) === false || (int) $id <= 0) {
                throw new DomainException('La selección contiene un producto no válido.');
            }

            if (!is_int($cantidad) && !is_string($cantidad)) {
                throw new DomainException('Las cantidades deben ser números enteros.');
            }

            if (filter_var($cantidad, FILTER_VALIDATE_INT) === false) {
                throw new DomainException('Las cantidades deben ser números enteros.');
            }

            $cantidad = (int) $cantidad;
            if ($cantidad < 0) {
                throw new DomainException('Las cantidades no pueden ser negativas.');
            }

            if ($cantidad > 0) {
                $normalizados[(int) $id] = $cantidad;
            }
        }

        if ($normalizados === []) {
            throw new DomainException('Selecciona al menos un producto para el pedido.');
        }

        ksort($normalizados, SORT_NUMERIC);

        return $normalizados;
    }

    private function aCentavos(string $precio): int
    {
        [$unidades, $fraccion] = array_pad(explode('.', $precio, 2), 2, '0');
        $fraccion = str_pad(substr($fraccion, 0, 2), 2, '0');

        return ((int) $unidades * 100) + (int) $fraccion;
    }

    private function formatearPrecio(int $centavos): string
    {
        return sprintf('%d.%02d', intdiv($centavos, 100), $centavos % 100);
    }
}
