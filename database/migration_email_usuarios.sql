USE cafeteria_db;

ALTER TABLE usuarios
    ADD COLUMN email VARCHAR(190) NULL AFTER usuario;

UPDATE usuarios
SET email = CONCAT(usuario, '@cafeteria.local')
WHERE email IS NULL;

ALTER TABLE usuarios
    MODIFY COLUMN email VARCHAR(190) NOT NULL,
    ADD UNIQUE KEY uq_usuarios_email (email);
