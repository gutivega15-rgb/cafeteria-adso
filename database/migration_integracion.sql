USE cafeteria_db;

-- Migración de una sola ejecución para las tablas existentes.
-- Alinea la estructura existente con los modelos y conserva los registros.
ALTER TABLE productos
    ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1 AFTER stock;

ALTER TABLE pedidos
    MODIFY COLUMN cliente_nombre VARCHAR(120) NULL,
    MODIFY COLUMN tipo_servicio VARCHAR(50) NOT NULL DEFAULT 'Mostrador',
    MODIFY COLUMN mesa TINYINT UNSIGNED NULL;

ALTER TABLE pedido_detalles
    ADD COLUMN subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00 AFTER precio_unitario;

-- En la base actual, password ya contiene hashes bcrypt.
ALTER TABLE usuarios
    CHANGE COLUMN password password_hash VARCHAR(255) NOT NULL,
    MODIFY COLUMN nombre VARCHAR(120) NOT NULL,
    MODIFY COLUMN usuario VARCHAR(80) NOT NULL,
    MODIFY COLUMN rol ENUM('administrador', 'cocina', 'mesero', 'cliente') NOT NULL DEFAULT 'mesero';

ALTER TABLE usuarios
    ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1 AFTER rol;
