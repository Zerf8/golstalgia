CREATE TABLE IF NOT EXISTS notificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    tipo ENUM('propuesta', 'confirmacion', 'cancelacion') NOT NULL,
    mensaje VARCHAR(255) NOT NULL,
    partida_id INT UNSIGNED,
    leida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (partida_id) REFERENCES partidas(id) ON DELETE SET NULL
);
