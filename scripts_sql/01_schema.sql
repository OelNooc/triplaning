CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    contraseña VARCHAR(255)
);

CREATE TABLE destinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    pais VARCHAR(100)
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)  
);

CREATE TABLE atracciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    id_destino INT,
    id_categoria INT,
    horario_apertura TIME,
    horario_cierre TIME,
    tiempo INT, 
    costo DECIMAL(10,2),
    FOREIGN KEY (id_destino) REFERENCES destinos(id),
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

CREATE TABLE itinerarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_destino INT,
    fecha_inicio DATE,
    fecha_termino DATE,
    presupuesto decimal(10,2),  
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_destino) REFERENCES destinos(id)
);

CREATE TABLE itinerario_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_itinerario INT,
    id_atraccion INT,
    fecha DATE,
    hora TIME,
    FOREIGN KEY (id_itinerario) REFERENCES itinerarios(id),
    FOREIGN KEY (id_atraccion) REFERENCES atracciones(id)
);