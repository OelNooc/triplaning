-- Insertar categorías 
INSERT INTO categorias (id, nombre) VALUES
(1, 'Cultura'),
(2, 'Naturaleza'),
(3, 'Aventura'),
(4, 'Compras'),
(5, 'Entretenimiento');

-- Insertar usuarios
INSERT INTO usuarios (nombre, email, contraseña) VALUES
('Ana Torres', 'ana@example.com', '$2y$10$ejemploHashSeguro123456'),
('Carlos Ruiz', 'carlos@example.com', '$2y$10$ejemploHashSeguro654321');

-- Insertar destinos (5 países con 5 ciudades cada uno)
INSERT INTO destinos (nombre, pais) VALUES
-- Chile
('La Serena', 'Chile'),
('Santiago', 'Chile'),
('Concepcion', 'Chile'),
('Puerto Montt', 'Chile'),
('Valparaiso', 'Chile'),

-- Perú
('Lima', 'Perú'),
('Arequipa', 'Perú'),
('Cusco', 'Perú'),
('Trujillo', 'Perú'),
('Iquitos', 'Perú'),

-- Argentina
('Buenos Aires', 'Argentina'),
('Córdoba', 'Argentina'),
('Mendoza', 'Argentina'),
('Rosario', 'Argentina'),
('Bariloche', 'Argentina'),

-- Brasil
('Río de Janeiro', 'Brasil'),
('São Paulo', 'Brasil'),
('Brasilia', 'Brasil'),
('Salvador', 'Brasil'),
('Foz do Iguaçu', 'Brasil'),

-- México
('Ciudad de México', 'México'),
('Guadalajara', 'México'),
('Monterrey', 'México'),
('Cancún', 'México'),
('Tulum', 'México');

-- Insertar atracciones (2 por ciudad, con estructura exacta de tu tabla)
INSERT INTO atracciones (nombre, id_destino, id_categoria, horario_apertura, horario_cierre, tiempo, costo) VALUES
-- Chile
('Museo Arqueológico', 1, 1, '09:00:00', '18:00:00', 120, 10.00),
('Faro Monumental', 1, 2, '08:00:00', '20:00:00', 60, 0.00),
('Cerro San Cristóbal', 2, 3, '06:00:00', '19:00:00', 180, 5.00),
('Mercado Central', 2, 4, '09:00:00', '18:00:00', 120, 0.00),
('Parque Ecuador', 3, 2, '08:00:00', '20:00:00', 90, 0.00),
('Galeria de la Historia', 3, 1, '10:00:00', '18:00:00', 60, 3.50),
('Muelle Angelmó', 4, 4, '09:00:00', '19:00:00', 90, 0.00),
('Parque Alerce Andino', 4, 2, '08:30:00', '18:30:00', 180, 8.00),
('Ascensor Artillería', 5, 1, '10:00:00', '18:00:00', 60, 2.00),
('Playa Ancha', 5, 2, '06:00:00', '20:00:00', 120, 0.00),

-- Perú (ejemplo)
('Plaza Mayor de Lima', 6, 1, '09:00:00', '18:00:00', 90, 0.00),
('Circuito Mágico del Agua', 6, 5, '17:00:00', '23:00:00', 120, 15.00),
('Monasterio Santa Catalina', 7, 1, '09:00:00', '17:00:00', 120, 10.00),
('Mirador Yanahuara', 7, 2, '08:00:00', '18:00:00', 60, 0.00),

-- Argentina (ejemplo)
('Casa Rosada', 11, 1, '10:00:00', '18:00:00', 90, 0.00),
('La Boca', 11, 5, '08:00:00', '20:00:00', 120, 0.00),
('Parque Sarmiento', 12, 2, '08:00:00', '19:00:00', 120, 0.00),
('Manzana Jesuítica', 12, 1, '09:00:00', '17:00:00', 90, 3.00),

-- Brasil (ejemplo)
('Pan de Azúcar', 16, 3, '08:00:00', '19:00:00', 150, 25.00),
('Playa Ipanema', 16, 2, '06:00:00', '20:00:00', 180, 0.00),
('Museo de Arte', 17, 1, '10:00:00', '18:00:00', 120, 10.00),
('Parque Ibirapuera', 17, 2, '05:00:00', '00:00:00', 180, 0.00),

-- México (ejemplo)
('Zócalo Capitalino', 21, 1, '08:00:00', '22:00:00', 90, 0.00),
('Castillo Chapultepec', 21, 1, '09:00:00', '17:00:00', 120, 5.00),
('Teatro Degollado', 22, 1, '10:00:00', '18:00:00', 60, 5.00),
('Zoológico Guadalajara', 22, 5, '09:30:00', '18:30:00', 150, 8.00);

-- Insertar itinerarios (2 por usuario)
INSERT INTO itinerarios (id_usuario, id_destino, fecha_inicio, fecha_termino, presupuesto) VALUES
(1, 2, '2023-12-01', '2023-12-07', 500.0),   -- Ana en Santiago
(1, 6, '2024-01-15', '2024-01-22', 1000.0),    -- Ana en Lima
(2, 16, '2023-11-10', '2023-11-17', 1000.0),   -- Carlos en Río
(2, 21, '2024-02-05', '2024-02-12', 500.0);  -- Carlos en CDMX

-- Insertar detalles de itinerarios (2 atracciones por itinerario)
INSERT INTO itinerario_detalle (id_itinerario, id_atraccion, fecha, hora) VALUES
-- Itinerario 1 (Ana en Santiago)
(1, 3, '2023-12-02', '10:00:00'),  -- Cerro San Cristóbal
(1, 4, '2023-12-03', '12:00:00'),  -- Mercado Central

-- Itinerario 2 (Ana en Lima)
(2, 11, '2024-01-16', '09:30:00'), -- Plaza Mayor
(2, 12, '2024-01-17', '18:00:00'), -- Circuito Mágico

-- Itinerario 3 (Carlos en Río)
(3, 19, '2023-11-11', '08:30:00'), -- Pan de Azúcar
(3, 20, '2023-11-12', '15:00:00'), -- Playa Ipanema

-- Itinerario 4 (Carlos en CDMX)
(4, 25, '2024-02-06', '09:00:00'), -- Zócalo
(4, 26, '2024-02-07', '11:00:00'); -- Castillo Chapultepec