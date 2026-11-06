<?php
declare(strict_types=1);

class PedidoAdminModel
{
    private PDO $conexion;

    public function __construct(?PDO $conexion = null)
    {
        $this->conexion = $conexion ?? require __DIR__ . '/../config/database.php';
    }

    /**
     * Devuelve los pedidos más recientes con sus líneas de detalle.
     *
     * @return array<int, array<string, mixed>>
     */
    public function obtenerRegistrados(int $limite = 200): array
    {
        $limite = max(1, min($limite, 500));
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
             LEFT JOIN pedido_detalles pd ON pd.pedido_id = p.id
             LEFT JOIN productos pr ON pr.id = pd.producto_id
             WHERE p.id IN (
                SELECT recientes.id
                FROM (
                    SELECT id
                    FROM pedidos
                    ORDER BY creado_en DESC, id DESC
                    LIMIT {$limite}
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

            if ($fila['producto_nombre'] !== null) {
                $pedidos[$pedidoId]['detalles'][] = [
                    'nombre' => $fila['producto_nombre'],
                    'cantidad' => (int) $fila['cantidad'],
                ];
            }
        }

        return array_values($pedidos);
    }
}
