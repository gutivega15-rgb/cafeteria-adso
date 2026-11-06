USE cafeteria_db;

ALTER TABLE pedidos
    ADD COLUMN mesa TINYINT UNSIGNED NULL AFTER cliente_nombre,
    ADD INDEX idx_pedidos_mesa (mesa);
