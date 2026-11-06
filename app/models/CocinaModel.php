<?php
declare(strict_types=1);

class CocinaModel
{
    private const TRANSICIONES = [
        'Pendiente' => 'En preparación',
        'En preparación' => 'Entregado',
    ];

    private PDO $conexion;

    public function __construct(?PDO $conexion = null)
    {
        $this->conexion = $conexion ?? require __DIR__ . '/../config/database.php';
    }

    /**
     * Lista los últimos 50 pedidos y agrupa sus productos.
     *
     * @return array<int, array<string, mixed>>
     */
    public function obtenerPedidos(): array
    {
        $consulta = $this->conexion->query(
            "SELECT
                p.id AS pedido_id,
                p.cliente_nombre,
                p.mesa,
                p.estado,
                p.total,
                p.creado_en,
                pd.cantidad,
                pr.nombre AS producto_nombre
             FROM pedidos p
             INNER JOIN pedido_detalles pd ON pd.pedido_id = p.id
             INNER JOIN productos pr ON pr.id = pd.producto_id
             WHERE p.id IN (
                SELECT recientes.id
                FROM (
                    SELECT id
                    FROM pedidos
                    ORDER BY creado_en DESC, id DESC
                    LIMIT 50
                ) AS recientes
             )
             ORDER BY p.creado_en DESC, p.id DESC, pr.nombre ASC"
        );

        $pedidos = [];
        foreach ($consulta->fetchAll() as $fila) {
            $pedidoId = (int) $fila['pedido_id'];

            if (!isset($pedidos[$pedidoId])) {
                $pedidos[$pedidoId] = [
                    'id' => $pedidoId,
                    'cliente_nombre' => $fila['cliente_nombre'],
                    'mesa' => $fila['mesa'],
                    'estado' => $fila['estado'],
                    'total' => $fila['total'],
                    'creado_en' => $fila['creado_en'],
                    'detalles' => [],
                ];
            }

            $pedidos[$pedidoId]['detalles'][] = [
                'nombre' => $fila['producto_nombre'],
                'cantidad' => (int) $fila['cantidad'],
            ];
        }

        return array_values($pedidos);
    }

    /**
     * Cambia un pedido solamente si todavía conserva el estado esperado.
     */
    public function avanzarEstado(int $pedidoId, string $estadoActual, string $estadoNuevo): bool
    {
        if ($pedidoId <= 0 || (self::TRANSICIONES[$estadoActual] ?? null) !== $estadoNuevo) {
            throw new DomainException('La transición de estado solicitada no es válida.');
        }

        $sentencia = $this->conexion->prepare(
            'UPDATE pedidos
             SET estado = :estado_nuevo
             WHERE id = :id AND estado = :estado_actual'
        );
        $sentencia->execute([
            'estado_nuevo' => $estadoNuevo,
            'id' => $pedidoId,
            'estado_actual' => $estadoActual,
        ]);

        return $sentencia->rowCount() === 1;
    }
}
