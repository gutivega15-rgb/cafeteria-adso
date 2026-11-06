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
