<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/PedidoAdminModel.php';

class PedidoAdminController
{
    public function index(): void
    {
        $pedidos = (new PedidoAdminModel())->obtenerRegistrados();
        require __DIR__ . '/../views/pedidos/admin.php';
    }
}
