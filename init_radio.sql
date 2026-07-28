-- =============================================
-- BASE DE DATOS: ProyectoFinal
-- SISTEMA DE GESTION DE RADIO FM
-- =============================================

DROP DATABASE IF EXISTS ProyectoFinal;
CREATE DATABASE ProyectoFinal CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ProyectoFinal;

-- =============================================
-- TABLA: usuarios (acceso al sistema)
-- =============================================
CREATE TABLE usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    usuario VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    rol VARCHAR(20) DEFAULT 'operador',
    estado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_usuario (usuario)
) ENGINE=InnoDB;

-- =============================================
-- TABLA: discjockey (locutores / DJs)
-- =============================================
CREATE TABLE discjockey (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre_artistico VARCHAR(100) NOT NULL,
    nombre_real VARCHAR(100) DEFAULT NULL,
    cedula VARCHAR(20) NOT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    correo VARCHAR(100) DEFAULT NULL,
    genero_favorito VARCHAR(50) DEFAULT NULL,
    horario_programa VARCHAR(100) DEFAULT NULL,
    nombre_programa VARCHAR(100) DEFAULT NULL,
    estado TINYINT(1) DEFAULT 1,
    fecha_ingreso DATE DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_cedula (cedula)
) ENGINE=InnoDB;

-- =============================================
-- TABLA: grupo (bandas / artistas)
-- =============================================
CREATE TABLE grupo (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nombre_grupo VARCHAR(100) NOT NULL,
    pais_origen VARCHAR(50) DEFAULT NULL,
    anio_formacion YEAR DEFAULT NULL,
    genero_musical VARCHAR(50) DEFAULT NULL,
    integrantes INT(11) DEFAULT NULL,
    biografia TEXT DEFAULT NULL,
    estado_activo TINYINT(1) DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- =============================================
-- TABLA: disco (albumes)
-- =============================================
CREATE TABLE disco (
    id INT(11) NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    grupo_id INT(11) NOT NULL,
    anio_lanzamiento YEAR DEFAULT NULL,
    discografica VARCHAR(100) DEFAULT NULL,
    num_canciones INT(11) DEFAULT 0,
    caratula_url VARCHAR(500) DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY fk_disco_grupo (grupo_id),
    CONSTRAINT fk_disco_grupo FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- TABLA: cancion
-- =============================================
CREATE TABLE cancion (
    id INT(11) NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    grupo_id INT(11) NOT NULL,
    disco_id INT(11) DEFAULT NULL,
    duracion_segundos INT(11) DEFAULT NULL,
    genero VARCHAR(50) DEFAULT NULL,
    ano_lanzamiento YEAR DEFAULT NULL,
    es_sencillo TINYINT(1) DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY fk_cancion_grupo (grupo_id),
    KEY fk_cancion_disco (disco_id),
    CONSTRAINT fk_cancion_grupo FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cancion_disco FOREIGN KEY (disco_id) REFERENCES disco(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- TABLA: reproduccion (historial de emision)
-- =============================================
CREATE TABLE reproduccion (
    id INT(11) NOT NULL AUTO_INCREMENT,
    cancion_id INT(11) NOT NULL,
    discjockey_id INT(11) NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duracion_real INT(11) DEFAULT NULL,
    observaciones VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY fk_repro_cancion (cancion_id),
    KEY fk_repro_dj (discjockey_id),
    KEY idx_fecha (fecha_hora),
    CONSTRAINT fk_repro_cancion FOREIGN KEY (cancion_id) REFERENCES cancion(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_repro_dj FOREIGN KEY (discjockey_id) REFERENCES discjockey(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- DATOS DE PRUEBA
-- =============================================

-- Usuarios
INSERT INTO usuarios (usuario, password_hash, nombre_completo, rol, estado) VALUES
('admin', '123456', 'Administrador Radio', 'administrador', 1),
('dj_carlos', '123456', 'Carlos Metal', 'operador', 1),
('dj_luna', '123456', 'Luna Fuego', 'operador', 1);

-- Discjockeys
INSERT INTO discjockey (nombre_artistico, nombre_real, cedula, telefono, correo, genero_favorito, horario_programa, nombre_programa, estado, fecha_ingreso) VALUES
('DJ Trueno', 'Carlos Mendoza', '1712345678', '0991112233', 'trueno@radiofm.com', 'Heavy Metal', '20:00 - 23:00', 'Noche de Metal', 1, '2020-03-15'),
('Luna Fuego', 'Luna Espinoza', '1723456789', '0992223344', 'luna@radiofm.com', 'Rock Alternativo', '14:00 - 17:00', 'Fuego Alternativo', 1, '2021-06-01'),
('Rock Master', 'Andres Castillo', '1734567890', '0993334455', 'rockmaster@radiofm.com', 'Rock Clasico', '08:00 - 12:00', 'Clasicos del Rock', 1, '2019-01-10'),
('DJ Tormenta', 'Maria Reyes', '1745678901', '0994445566', 'tormenta@radiofm.com', 'Punk Rock', '23:00 - 02:00', 'Tormenta Punk', 1, '2022-09-20'),
('El Surco', 'Pedro Vargas', '1756789012', '0995556677', 'surco@radiofm.com', 'Rock Latino', '17:00 - 20:00', 'Rock del Sur', 1, '2023-02-14');

-- Grupos
INSERT INTO grupo (nombre_grupo, pais_origen, anio_formacion, genero_musical, integrantes, biografia, estado_activo) VALUES
('Metallica', 'Estados Unidos', 1981, 'Thrash Metal', 4, 'Banda iconica del thrash metal, formada en Los Angeles.', 1),
('Nirvana', 'Estados Unidos', 1987, 'Grunge', 3, 'Pioneros del grunge, liderados por Kurt Cobain.', 1),
('Queen', 'Reino Unido', 1970, 'Rock Clasico', 4, 'Leyenda del rock liderada por Freddie Mercury.', 1),
('Green Day', 'Estados Unidos', 1987, 'Punk Rock', 3, 'Banda punk rock que revoluciono el genero en los 90s.', 1),
('Soda Stereo', 'Argentina', 1982, 'Rock en Espanol', 3, 'La banda mas influyente del rock latinoamericano.', 1),
('Iron Maiden', 'Reino Unido', 1975, 'Heavy Metal', 6, 'Legendarios del NWOBHM con Eddie como mascota.', 1),
('Radiohead', 'Reino Unido', 1985, 'Rock Alternativo', 5, 'Innovadores del rock alternativo y experimentales.', 1),
('Guns N Roses', 'Estados Unidos', 1985, 'Hard Rock', 5, 'La banda mas salvaje de Los Angeles.', 1),
('Los Prisioneros', 'Chile', 1983, 'New Wave', 3, 'Pioneros del rock en espanol y nueva ola.', 1),
('Foo Fighters', 'Estados Unidos', 1994, 'Rock Alternativo', 5, 'Proyecto de Dave Grohl post-Nirvana.', 1);

-- Discos
INSERT INTO disco (titulo, grupo_id, anio_lanzamiento, discografica, num_canciones) VALUES
('Master of Puppets', 1, 1986, 'Electra Records', 8),
('Nevermind', 2, 1991, 'DGC Records', 12),
('A Night at the Opera', 3, 1975, 'EMI Records', 12),
('Dookie', 4, 1994, 'Reprise Records', 14),
('Cancion Animal', 5, 1990, 'Sony Music', 11),
('The Number of the Beast', 6, 1982, 'EMI Records', 8),
('OK Computer', 7, 1997, 'Parlophone', 12),
('Appetite for Destruction', 8, 1987, 'Geffen Records', 12),
('La Voz de los 80', 9, 1984, 'EMI Odeon', 11),
('The Colour and the Shape', 10, 1997, 'Capitol Records', 11),
('...And Justice for All', 1, 1988, 'Electra Records', 10),
('In Utero', 2, 1993, 'DGC Records', 12);

-- Canciones
INSERT INTO cancion (titulo, grupo_id, disco_id, duracion_segundos, genero, ano_lanzamiento, es_sencillo) VALUES
('Master of Puppets', 1, 1, 518, 'Thrash Metal', 1986, 1),
('Enter Sandman', 1, NULL, 331, 'Heavy Metal', 1991, 1),
('Nothing Else Matters', 1, NULL, 388, 'Ballad Rock', 1991, 1),
('Smells Like Teen Spirit', 2, 2, 301, 'Grunge', 1991, 1),
('Come as You Are', 2, 2, 219, 'Grunge', 1991, 1),
('In Bloom', 2, 2, 255, 'Grunge', 1991, 0),
('Bohemian Rhapsody', 3, 3, 354, 'Opera Rock', 1975, 1),
('We Will Rock You', 3, NULL, 122, 'Anthem Rock', 1977, 1),
('We Are the Champions', 3, NULL, 179, 'Anthem Rock', 1977, 1),
('Basket Case', 4, 4, 181, 'Punk Rock', 1994, 1),
('Longview', 4, 4, 203, 'Punk Rock', 1994, 1),
('When I Come Around', 4, 4, 178, 'Punk Rock', 1994, 1),
('De Musica Ligera', 5, 5, 233, 'Rock en Espanol', 1990, 1),
('Persiana Americana', 5, 5, 281, 'Rock en Espanol', 1986, 1),
('En la Ciudad de la Furia', 5, NULL, 276, 'Rock Oscuro', 1987, 1),
('The Number of the Beast', 6, 6, 281, 'Heavy Metal', 1982, 1),
('Run to the Hills', 6, 6, 228, 'Heavy Metal', 1982, 1),
('Hallowed Be Thy Name', 6, 6, 428, 'Heavy Metal', 1982, 0),
('Paranoid Android', 7, 7, 382, 'Art Rock', 1997, 1),
('Karma Police', 7, 7, 264, 'Rock Alternativo', 1997, 1),
('Creep', 7, NULL, 236, 'Rock Alternativo', 1992, 1),
('Sweet Child O Mine', 8, 8, 356, 'Hard Rock', 1987, 1),
('Welcome to the Jungle', 8, 8, 289, 'Hard Rock', 1987, 1),
('November Rain', 8, NULL, 537, 'Power Ballad', 1991, 1),
('La Voz de los 80', 9, 9, 237, 'New Wave', 1984, 1),
('Tren al Sur', 9, NULL, 263, 'New Wave', 1990, 1),
('Everlong', 10, 10, 250, 'Rock Alternativo', 1997, 1),
('My Hero', 10, 10, 258, 'Rock Alternativo', 1997, 0),
('Learn to Fly', 10, 10, 235, 'Rock Alternativo', 1999, 1);

-- Reproducciones de ejemplo
INSERT INTO reproduccion (cancion_id, discjockey_id, fecha_hora, duracion_real, observaciones) VALUES
(4, 1, '2026-07-20 20:15:00', 301, 'Apertura de programa'),
(1, 1, '2026-07-20 20:25:00', 518, 'Bloque metalero'),
(16, 1, '2026-07-20 20:35:00', 281, 'Clasico del NWOBHM'),
(7, 3, '2026-07-20 08:10:00', 354, 'Inicio de programa clasico'),
(22, 3, '2026-07-20 08:20:00', 356, 'Hard Rock matutino'),
(13, 2, '2026-07-20 14:10:00', 233, 'Rock en espanol'),
(19, 2, '2026-07-20 14:20:00', 382, 'Alternativo progresivo'),
(10, 4, '2026-07-20 23:10:00', 181, 'Sesion punk nocturna'),
(25, 5, '2026-07-20 17:15:00', 237, 'Rock latinoamericano'),
(8, 3, '2026-07-21 08:05:00', 122, 'Himno clasico'),
(21, 2, '2026-07-21 14:30:00', 236, 'Sesion alternativa'),
(3, 1, '2026-07-21 21:00:00', 388, 'Balada para cerrar bloque'),
(27, 2, '2026-07-21 14:45:00', 250, 'Rock alternativo 90s'),
(12, 4, '2026-07-21 23:20:00', 178, 'Punk rapido'),
(15, 5, '2026-07-21 17:30:00', 276, 'Rock oscuro en espanol');
