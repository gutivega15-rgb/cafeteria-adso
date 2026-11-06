<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/ProductoModel.php';

class ProductoController
{
    public function index(): void
    {
        $modelo = new ProductoModel();
        $productos = $modelo->obtenerMenuActivo();

        require __DIR__ . '/../views/productos/index.php';
    }
}
