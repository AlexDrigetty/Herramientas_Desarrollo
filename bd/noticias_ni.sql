-- Crear la base de datos
CREATE DATABASE noticias_ni;
USE noticias_ni;

-- Tabla de roles (nueva tabla)
CREATE TABLE roles (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

-- Insertar roles básicos
INSERT INTO roles (nombre, descripcion) VALUES 
('administrador', 'Acceso completo al sistema'),
('editor', 'Puede publicar y editar noticias'),
('lector', 'Solo puede leer noticias');

-- Tabla de usuarios modificada
CREATE TABLE usuarios (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL DEFAULT 3, -- Valor por defecto: lector
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla de noticias
CREATE TABLE noticias (
    id INT IDENTITY(1,1) PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    autor_id INT NOT NULL,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    categoria VARCHAR(100) NOT NULL,
    estado VARCHAR(20) DEFAULT 'borrador', -- Nuevo campo para workflow
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de permisos (opcional para sistema más complejo)
CREATE TABLE permisos (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

-- Tabla intermedia roles_permisos (opcional)
CREATE TABLE roles_permisos (
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    PRIMARY KEY (rol_id, permiso_id),
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE
);

INSERT INTO usuarios (nombre, apellido, correo, contrasena, rol_id) VALUES
('Juan', 'Pérez', 'juan.perez@example.com', 'contrasena_segura_1', 1), -- rol_id 1 es 'administrador'
('María', 'González', 'maria.gonzalez@example.com', 'mi_clave_secreta', 2), -- rol_id 2 es 'editor'
('Pedro', 'Ramírez', 'pedro.ramirez@example.com', 'pass12345', 3); -- rol_id 3 es 'lector'