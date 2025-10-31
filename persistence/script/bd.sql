
CREATE DATABASE IF NOT EXISTS futbol_persistencia;
USE futbol_persistencia;

-- Tabla EQUIPOS
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    estadio VARCHAR(150) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla PARTIDOS
CREATE TABLE IF NOT EXISTS partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_local_id INT NOT NULL,
    equipo_visitante_id INT NOT NULL,
    resultado VARCHAR(3) NOT NULL,
    jornada INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_local_id) REFERENCES equipos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (equipo_visitante_id) REFERENCES equipos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY uk_partido_jornada (equipo_local_id, equipo_visitante_id, jornada),
    INDEX idx_jornada (jornada),
    INDEX idx_equipo_local (equipo_local_id),
    INDEX idx_equipo_visitante (equipo_visitante_id),
    CHECK (resultado IN ('1', 'X', '2')),
    CONSTRAINT chk_equipos_diferentes CHECK (equipo_local_id != equipo_visitante_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo
INSERT INTO equipos (nombre, estadio) VALUES
('Real Madrid', 'Santiago Bernabéu'),
('FC Barcelona', 'Camp Nou'),
('Atlético Madrid', 'Metropolitano'),
('Valencia CF', 'Mestalla'),
('Sevilla FC', 'Ramón Sánchez-Pizjuán');

INSERT INTO partidos (equipo_local_id, equipo_visitante_id, resultado, jornada) VALUES
(1, 2, '1', 1),
(3, 4, 'X', 1),
(5, 1, '2', 1),
(2, 3, '1', 2),
(4, 5, 'X', 2),
(1, 3, 'X', 3),
(2, 4, '1', 3);
futbol_persistencia