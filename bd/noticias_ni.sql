CREATE DATABASE IF NOT EXISTS noticia_NI;
USE noticia_NI;

-- Tabla de roles simplificada
CREATE TABLE roles (
    id INT PRIMARY KEY, 
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO roles (id, nombre) VALUES 
(0, 'ADMIN'),
(1, 'USUARIO');

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Insertar usuarios iniciales
INSERT INTO usuarios (nombre, apellido, correo, contrasena, rol_id) VALUES 
('Admin', 'Principal', 'admin@noticias.com', '$2y$10$qYxSXf0fdN7U2jYRH5km.Oto7D.scGNkf7iV2Njx0w5OAjpJG/V6C', 0),
('Usuario', 'Normal' , 'usuario@noticias.com', '$2y$10$UQzAKggPNER4FaQwjmv2te2cIbYP2HfRENdS.cUKfNXnXnTBvKT5C', 1);

-- Tabla de categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    color VARCHAR(20) DEFAULT '#6c757d'
);

INSERT INTO categorias (nombre, color) VALUES 
('Política', '#dc3545'),
('Economía', '#28a745'),
('Deportes', '#007bff'),
('Tecnología', '#17a2b8'),
('Cultura', '#6f42c1'),
('Salud', '#e83e8c'),
('Medio Ambiente', '#20c997'),
('Educación', '#fd7e14');

-- Tabla de estados de noticia
CREATE TABLE estados_noticia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO estados_noticia (nombre) VALUES 
('Pendiente'),
('Programado'),
('Publicado');

-- Tabla principal de noticias con ENUM para tipo_noticia
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    resumen TEXT NOT NULL,
    contenido LONGTEXT NOT NULL,
    autor_id INT NOT NULL,
    categoria_id INT NOT NULL,
    tipo_noticia ENUM('nacional', 'internacional') NOT NULL,
    estado_id INT NOT NULL DEFAULT 1,
    imagen_portada VARCHAR(255) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_publicacion DATETIME NULL,
    fecha_programada DATETIME NULL,
    vistas INT DEFAULT 0,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (estado_id) REFERENCES estados_noticia(id),
    FULLTEXT INDEX (titulo, resumen, contenido)
);
CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    noticia_id INT NOT NULL,
    usuario_id INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    respuesta_id INT NULL,
    FOREIGN KEY (noticia_id) REFERENCES noticias(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (respuesta_id) REFERENCES comentarios(id)
);