CREATE DATABASE IF NOT EXISTS cafeteria_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE cafeteria_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    usuario VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'cocina', 'mesero', 'cliente') NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_usuarios_rol_activo (rol, activo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT NULL,
    categoria VARCHAR(60) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_productos_menu (activo, stock, categoria, nombre)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_nombre VARCHAR(120) NULL,
    tipo_servicio VARCHAR(50) NOT NULL DEFAULT 'Mostrador',
    mesa TINYINT UNSIGNED NULL,
    estado ENUM('Pendiente', 'En preparación', 'Entregado') NOT NULL DEFAULT 'Pendiente',
    total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pedidos_estado_creado (estado, creado_en),
    INDEX idx_pedidos_mesa (mesa)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedido_detalles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    producto_id INT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_pedido_detalles_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_pedido_detalles_producto
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_pedido_producto (pedido_id, producto_id)
) ENGINE=InnoDB;
