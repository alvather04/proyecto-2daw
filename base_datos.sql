-- Base de datos del proyecto
CREATE DATABASE IF NOT EXISTS proyecto;
USE proyecto;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS USUARIO (
    ID_usuario CHAR(9) PRIMARY KEY,
    nombre VARCHAR(100),
    correo VARCHAR(100) UNIQUE,
    contrasena VARCHAR(50),
    region VARCHAR(50)
);

-- Tabla de noticias
CREATE TABLE IF NOT EXISTS NOTICIAS (
    ID CHAR(4) PRIMARY KEY,
    fecha DATE NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    contenido VARCHAR(200)
);

-- Tabla de comunidades
CREATE TABLE IF NOT EXISTS COMUNIDADES (
    ID CHAR(3) PRIMARY KEY,
    roles VARCHAR(50),
    nombre VARCHAR(100)
);

-- Relaciones usuario-noticia
CREATE TABLE IF NOT EXISTS VEN (
    ID_noticias CHAR(4),
    ID_usuario CHAR(9),
    PRIMARY KEY (ID_noticias, ID_usuario),
    FOREIGN KEY (ID_noticias) REFERENCES NOTICIAS(ID),
    FOREIGN KEY (ID_usuario) REFERENCES USUARIO(ID_usuario)
);

-- Usuarios en comunidades
CREATE TABLE IF NOT EXISTS FORMAN (
    ID_usuario CHAR(9),
    ID_comunidades CHAR(3),
    PRIMARY KEY (ID_usuario, ID_comunidades),
    FOREIGN KEY (ID_usuario) REFERENCES USUARIO(ID_usuario),
    FOREIGN KEY (ID_comunidades) REFERENCES COMUNIDADES(ID)
);

-- Mensajes de comunidades
CREATE TABLE IF NOT EXISTS MENSAJES (
    ID_comunidades CHAR(3),
    fecha_publicacion DATE,
    contenido VARCHAR(300),
    PRIMARY KEY (ID_comunidades, fecha_publicacion),
    FOREIGN KEY (ID_comunidades) REFERENCES COMUNIDADES(ID)
);

-- Interacciones reflexivas entre usuarios
CREATE TABLE IF NOT EXISTS INTERACTUA (
    ID_usuario1 CHAR(9),
    ID_usuario2 CHAR(9),
    fecha_interaccion DATE,
    PRIMARY KEY (ID_usuario1, ID_usuario2),
    FOREIGN KEY (ID_usuario1) REFERENCES USUARIO(ID_usuario),
    FOREIGN KEY (ID_usuario2) REFERENCES USUARIO(ID_usuario)
);

-- ===============================
-- DATOS DE PRUEBA
-- ===============================

-- Inserts de usuarios
INSERT INTO USUARIO (ID_usuario, nombre, correo, contrasena, region) VALUES
('U00000001', 'Álvaro Martínez', 'alvaro@email.com', 'pass123', 'Andalucía'),
('U00000002', 'Lucía Fernández', 'lucia@email.com', 'lucia456', 'Cataluña'),
('U00000003', 'Carlos López', 'carlos@email.com', 'carlos789', 'Madrid');

-- Inserts de noticias
INSERT INTO NOTICIAS (ID, fecha, titulo, contenido) VALUES
('N001', '2025-12-01', 'Bienvenida al proyecto', 'Contenido de bienvenida al proyecto'),
('N002', '2025-12-02', 'Actualización de sistema', 'Se han añadido nuevas funcionalidades'),
('N003', '2025-12-03', 'Eventos próximos', 'Próximos eventos para la comunidad');

-- Inserts de comunidades
INSERT INTO COMUNIDADES (ID, roles, nombre) VALUES
('C01', 'Miembro', 'Fiddle my stick'),
('C02', 'Miembro', 'Diferencia agrícola'),
('C03', 'Miembro', 'SYBAU');

-- Relaciones usuario-noticia
INSERT INTO VEN (ID_noticias, ID_usuario) VALUES
('N001', 'U00000001'),
('N002', 'U00000002'),
('N003', 'U00000003');

-- Usuarios en comunidades
INSERT INTO FORMAN (ID_usuario, ID_comunidades) VALUES
('U00000001', 'C01'),
('U00000002', 'C02'),
('U00000003', 'C03');

-- Mensajes de comunidades
INSERT INTO MENSAJES (ID_comunidades, fecha_publicacion, contenido) VALUES
('C01', '2025-12-01', 'Primer mensaje de Fiddle my stick'),
('C02', '2025-12-02', 'Primer mensaje de Diferencia agrícola'),
('C03', '2025-12-03', 'Primer mensaje de SYBAU');

-- Interacciones entre usuarios
INSERT INTO INTERACTUA (ID_usuario1, ID_usuario2, fecha_interaccion) VALUES
('U00000001', 'U00000002', '2025-12-01'),
('U00000002', 'U00000003', '2025-12-02'),
('U00000003', 'U00000001', '2025-12-03');
