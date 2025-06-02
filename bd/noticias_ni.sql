CREATE DATABASE IF NOT EXISTS noticias_ni;
USE noticias_ni;

-- Tabla de roles
CREATE TABLE roles (
    id INT PRIMARY KEY, 
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

INSERT INTO roles (id, nombre, descripcion) VALUES 
(0, 'ADMIN', 'Administrador del sistema'),
(1, 'USER', 'Identidad del cliente');

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL DEFAULT 1,
    activo BIT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_login DATETIME,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Insertar usuarios con contraseñas encriptadas
INSERT INTO usuarios (nombre, apellido, correo, contrasena, rol_id) VALUES 
('Admin', 'Principal', 'admin@noticias.com', '$2y$10$qYxSXf0fdN7U2jYRH5km.Oto7D.scGNkf7iV2Njx0w5OAjpJG/V6C', 0);

INSERT INTO usuarios (nombre, apellido, correo, contrasena) VALUES 
('Usuario', 'Normal', 'usuario@noticias.com', '$2y$10$UQzAKggPNER4FaQwjmv2te2cIbYP2HfRENdS.cUKfNXnXnTBvKT5C');

-- Resto de tablas...
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    autor_id INT NOT NULL,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    categoria_id INT,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);