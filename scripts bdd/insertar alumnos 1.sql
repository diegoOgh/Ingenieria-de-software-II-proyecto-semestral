-- Insertar datos maestros en gestion_escolar
INSERT INTO gestion_escolar.funcionarios (nombres, apellidos) VALUES ('Juan', 'Pérez');
INSERT INTO gestion_escolar.alumnos (nro_matricula, nombre, apellidos, curso) VALUES (202601, 'Diego', 'Anabalón', '3ro Medio A');
INSERT INTO gestion_escolar.alumnos (nro_matricula, nombre, apellidos, curso) VALUES (202602, 'Sofía', 'Morales', '4to Medio B');

-- Registrar un conflicto en gestion_conflictos a cargo del funcionario 1 (Juan Pérez)
INSERT INTO gestion_conflictos.casos_conflicto (descripcion, estado_caso, id_funcionario_cargo) VALUES ('Discusión ocurrida durante una actividad escolar.','en proceso', 1);

-- Asociar los dos alumnos al conflicto número 1
INSERT INTO gestion_conflictos.detalle_alumnos_conflicto (id_conflicto, nro_matricula_alumno) VALUES (1, 202601);
INSERT INTO gestion_conflictos.detalle_alumnos_conflicto (id_conflicto, nro_matricula_alumno) VALUES (1, 202602);

-- Insertar datos maestros en gestion_escolar
INSERT INTO gestion_escolar.funcionarios (nombres, apellidos) VALUES ('Pepito', 'Sech');
INSERT INTO gestion_escolar.funcionarios (nombres, apellidos) VALUES ('Ramón', 'Jamón');
INSERT INTO gestion_escolar.alumnos (nro_matricula, nombre, apellidos, curso) VALUES (202603, 'Giuseppe', 'Peazo', '1ro Medio A');
INSERT INTO gestion_escolar.alumnos (nro_matricula, nombre, apellidos, curso) VALUES (202604, 'Ana', 'Carrascal', '2do Medio A');
INSERT INTO gestion_escolar.alumnos (nro_matricula, nombre, apellidos, curso) VALUES (202605, 'Fernando', 'Zlatanpedri', '8vo Básico A');


USE gestion_escolar;

-- POBLACIÓN DE TABLA ALUMNOS (1° Básico a 4° Medio)

INSERT INTO alumnos (nro_matricula, nombre, apellidos, curso) VALUES
-- 1° Básico A
(20260101, 'Sofía', 'González', '1 Básico A'),
(20260102, 'Agustín', 'Muñoz', '1 Básico A'),
(20260103, 'Isidora', 'Rojas', '1 Básico A'),
(20260104, 'Mateo', 'Díaz', '1 Básico A'),
(20260105, 'Martina', 'Pérez', '1 Básico A'),
(20260106, 'Benjamín', 'Soto', '1 Básico A'),
(20260107, 'Emilia', 'Contreras', '1 Básico A'),
(20260108, 'Vicente', 'Silva', '1 Básico A'),
(20260109, 'Antonia', 'Martínez', '1 Básico A'),
(20260110, 'Martín', 'Sepúlveda', '1 Básico A'),

-- 2° Básico A
(20260201, 'Florencia', 'Morales', '2 Básico A'),
(20260202, 'Joaquín', 'Fuentes', '2 Básico A'),
(20260203, 'Maite', 'Castro', '2 Básico A'),
(20260204, 'Tomás', 'Herrera', '2 Básico A'),
(20260205, 'Catalina', 'Araya', '2 Básico A'),
(20260206, 'Diego', 'Gómez', '2 Básico A'),
(20260207, 'Javiera', 'Tapia', '2 Básico A'),
(20260208, 'Matías', 'Espinoza', '2 Básico A'),
(20260209, 'Valentina', 'Torres', '2 Básico A'),
(20260210, 'Nicolás', 'Ramírez', '2 Básico A'),

-- 3° Básico A
(20260301, 'Camila', 'Flores', '3 Básico A'),
(20260302, 'Sebastián', 'Valdés', '3 Básico A'),
(20260303, 'Constanza', 'Castillo', '3 Básico A'),
(20260304, 'Felipe', 'Gutiérrez', '3 Básico A'),
(20260305, 'Antonella', 'Pizarro', '3 Básico A'),
(20260306, 'Lucas', 'Orellana', '3 Básico A'),
(20260307, 'Josefa', 'Salazar', '3 Básico A'),
(20260308, 'Alonso', 'Valenzuela', '3 Básico A'),
(20260309, 'Amanda', 'Figueroa', '3 Básico A'),
(20260310, 'Francisco', 'Pinto', '3 Básico A'),

-- 4° Básico A
(20260401, 'Mía', 'Miranda', '4 Básico A'),
(20260402, 'Bastián', 'Parra', '4 Básico A'),
(20260403, 'Renata', 'Cárdenas', '4 Básico A'),
(20260404, 'Ignacio', 'Salinas', '4 Básico A'),
(20260405, 'Victoria', 'Godoy', '4 Básico A'),
(20260406, 'Emilio', 'Escobar', '4 Básico A'),
(20260407, 'Julieta', 'Abarca', '4 Básico A'),
(20260408, 'Simón', 'Carvajal', '4 Básico A'),
(20260409, 'Josefina', 'Pino', '4 Básico A'),
(20260410, 'Gabriel', 'Alarcón', '4 Básico A'),

-- 5° Básico A
(20260501, 'Pascuala', 'Cornejo', '5 Básico A'),
(20260502, 'Facundo', 'Becerra', '5 Básico A'),
(20260503, 'Ignacia', 'Lagos', '5 Básico A'),
(20260504, 'Maximiliano', 'Saavedra', '5 Básico A'),
(20260505, 'Amalia', 'Ponce', '5 Básico A'),
(20260506, 'Pedro', 'Rivas', '5 Básico A'),
(20260507, 'Elena', 'Cortes', '5 Básico A'),
(20260508, 'Damián', 'Guzmán', '5 Básico A'),
(20260509, 'Rafaela', 'Vera', '5 Básico A'),
(20260510, 'Bruno', 'Toledo', '5 Básico A'),

-- 6° Básico A
(20260601, 'Colomba', 'Bustamante', '6 Básico A'),
(20260602, 'Clemente', 'Fierro', '6 Básico A'),
(20260603, 'Laura', 'Pavez', '6 Básico A'),
(20260604, 'Hugo', 'Toro', '6 Básico A'),
(20260605, 'Fernanda', 'Retamal', '6 Básico A'),
(20260606, 'Julián', 'Moya', '6 Básico A'),
(20260607, 'Bárbara', 'Arce', '6 Básico A'),
(20260608, 'Camilo', 'Osorio', '6 Básico A'),
(20260609, 'Magdalena', 'Garrido', '6 Básico A'),
(20260610, 'Gonzalo', 'Uribe', '6 Básico A'),

-- 7° Básico A
(20260701, 'Laura', 'Leiva', '7 Básico A'),
(20260702, 'Santiago', 'Riquelme', '7 Básico A'),
(20260703, 'Samantha', 'Inostroza', '7 Básico A'),
(20260704, 'Daniel', 'Villalobos', '7 Básico A'),
(20260705, 'Ana', 'Carrillo', '7 Básico A'),
(20260706, 'Esteban', 'San Martín', '7 Básico A'),
(20260707, 'Carolina', 'Cerda', '7 Básico A'),
(20260708, 'Tomás', 'Bustos', '7 Básico A'),
(20260709, 'Pía', 'Roa', '7 Básico A'),
(20260710, 'Franco', 'Zapata', '7 Básico A'),

-- 8° Básico A
(20260801, 'Dominga', 'Poblete', '8 Básico A'),
(20260802, 'Renato', 'Lira', '8 Básico A'),
(20260803, 'Francisca', 'Lillo', '8 Básico A'),
(20260804, 'Ian', 'Márquez', '8 Básico A'),
(20260805, 'Sofía', 'Salgado', '8 Básico A'),
(20260806, 'Alexander', 'Cifuentes', '8 Básico A'),
(20260807, 'Amparo', 'Carrasco', '8 Básico A'),
(20260808, 'Franco', 'Varela', '8 Básico A'),
(20260809, 'Paz', 'Aranda', '8 Básico A'),
(20260810, 'Andrés', 'Neira', '8 Básico A'),

-- 1° Medio A
(20260901, 'Macarena', 'Núñez', '1 Medio A'),
(20260902, 'Juan', 'Becerra', '1 Medio A'),
(20260903, 'Rocío', 'Medina', '1 Medio A'),
(20260904, 'Javier', 'Navarro', '1 Medio A'),
(20260905, 'Luciana', 'Vergara', '1 Medio A'),
(20260906, 'Héctor', 'Yáñez', '1 Medio A'),
(20260907, 'Kiara', 'Vargas', '1 Medio A'),
(20260908, 'Arturo', 'Pino', '1 Medio A'),
(20260909, 'Antonella', 'Reyes', '1 Medio A'),
(20260910, 'Rodrigo', 'Ramos', '1 Medio A'),

-- 2° Medio A
(20261001, 'Catalina', 'Ruiz', '2 Medio A'),
(20261002, 'Guillermo', 'Saavedra', '2 Medio A'),
(20261003, 'Isabel', 'Castro', '2 Medio A'),
(20261004, 'Carlos', 'Morales', '2 Medio A'),
(20261005, 'Aylin', 'Ortiz', '2 Medio A'),
(20261006, 'Víctor', 'Rojas', '2 Medio A'),
(20261007, 'Monserrat', 'Silva', '2 Medio A'),
(20261008, 'Eduardo', 'Muñoz', '2 Medio A'),
(20261009, 'Génesis', 'González', '2 Medio A'),
(20261010, 'Ricardo', 'Díaz', '2 Medio A'),

-- 3° Medio A
(20261101, 'Constanza', 'Pérez', '3 Medio A'),
(20261102, 'Luis', 'Soto', '3 Medio A'),
(20261103, 'Javiera', 'Contreras', '3 Medio A'),
(20261104, 'Humberto', 'Martínez', '3 Medio A'),
(20261105, 'Millaray', 'Sepúlveda', '3 Medio A'),
(20261106, 'Pablo', 'Fuentes', '3 Medio A'),
(20261107, 'Camila', 'Herrera', '3 Medio A'),
(20261108, 'Marcelo', 'Araya', '3 Medio A'),
(20261109, 'Valentina', 'Gómez', '3 Medio A'),
(20261110, 'Claudio', 'Tapia', '3 Medio A'),

-- 4° Medio A
(20261201, 'Allison', 'Espinoza', '4 Medio A'),
(20261202, 'Miguel', 'Torres', '4 Medio A'),
(20261203, 'Rayen', 'Ramírez', '4 Medio A'),
(20261204, 'Ángel', 'Flores', '4 Medio A'),
(20261205, 'Belén', 'Valdés', '4 Medio A'),
(20261206, 'David', 'Castillo', '4 Medio A'),
(20261207, 'Escarlet', 'Gutiérrez', '4 Medio A'),
(20261208, 'Kevin', 'Pizarro', '4 Medio A'),
(20261209, 'Nayareth', 'Orellana', '4 Medio A'),
(20261210, 'Brayan', 'Salazar', '4 Medio A');