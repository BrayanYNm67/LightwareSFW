-- Crear base de datos
CREATE DATABASE IF NOT EXISTS registro_comandas;
USE registro_comandas;

-- Tabla para las mesas
CREATE TABLE mesas (
    id_mesa INT AUTO_INCREMENT PRIMARY KEY,
    numero_mesa INT NOT NULL UNIQUE
);

-- Tabla para los meseros
CREATE TABLE meseros (
    id_mesero INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL
);

-- Tabla para los clientes
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE
);

-- Tabla para el menú
CREATE TABLE menu (
    id_alimento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria ENUM('Desayuno', 'Comida', 'Cena', 'Bebida') NOT NULL,
    precio DECIMAL(10,2) NOT NULL
);

-- Tabla para las comandas
CREATE TABLE comandas (
    id_comanda INT AUTO_INCREMENT PRIMARY KEY,
    id_mesa INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    id_cliente INT NOT NULL,
    metodo_pago ENUM('Efectivo', 'Tarjeta') NOT NULL,
    propina DECIMAL(10,2) DEFAULT 0.00,
    estado ENUM('Abierta', 'Cerrada') DEFAULT 'Abierta',
    FOREIGN KEY (id_mesa) REFERENCES mesas(id_mesa),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

-- Tabla para los detalles de la comanda
CREATE TABLE detalles_comanda (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_comanda INT NOT NULL,
    id_alimento INT NOT NULL,
    cantidad INT NOT NULL,
    FOREIGN KEY (id_comanda) REFERENCES comandas(id_comanda),
    FOREIGN KEY (id_alimento) REFERENCES menu(id_alimento)
);

-- Tabla para las propinas
CREATE TABLE propinas (
    id_propina INT AUTO_INCREMENT PRIMARY KEY,
    id_mesero INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_mesero) REFERENCES meseros(id_mesero)
);

-- Tabla para el logueo de usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('Administrador', 'Mesero') NOT NULL
);
