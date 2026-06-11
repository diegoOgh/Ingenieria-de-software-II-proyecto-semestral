
-- Creación de otros funcionarios
USE gestion_escolar;

INSERT INTO funcionarios (nombres, apellidos) 
VALUES ('María', 'Gómez');

INSERT INTO funcionarios (nombres, apellidos) 
VALUES ('Carlos', 'Sepúlveda');


-- Roles y usuarios para los funcionarios existentes
USE gestion_conflictos;

-- Insertamos los roles primero (ignore por si alguien ya los tiene creados)
INSERT IGNORE INTO rol (id_rol, nombre) VALUES (1, 'Funcionario General');
INSERT IGNORE INTO rol (id_rol, nombre) VALUES (2, 'Encargado de Convivencia');

-- insertamos las cuentas de usuario de funcionarios generales
INSERT INTO usuario (email, contrasena, id_rol, id_funcionario) 
VALUES 
('jperez@colegio.cl', '123456', 1, (SELECT id_funcionario FROM gestion_escolar.funcionarios WHERE nombres = 'Juan' AND apellidos = 'Pérez' LIMIT 1)),
('mgomez@colegio.cl', '123456', 1, (SELECT id_funcionario FROM gestion_escolar.funcionarios WHERE nombres = 'María' AND apellidos = 'Gómez' LIMIT 1)),
('psech@colegio.cl', '123456', 1, (SELECT id_funcionario FROM gestion_escolar.funcionarios WHERE nombres = 'Pepito' AND apellidos = 'Sech' LIMIT 1)),
('rjamon@colegio.cl', '123456', 1, (SELECT id_funcionario FROM gestion_escolar.funcionarios WHERE nombres = 'Ramón' AND apellidos = 'Jamón' LIMIT 1));

-- Insertamos al Encargado de Convivencia
INSERT INTO usuario (email, contrasena, id_rol, id_funcionario) 
VALUES 
('csepulveda@colegio.cl', '123456', 2, (SELECT id_funcionario FROM gestion_escolar.funcionarios WHERE nombres = 'Carlos' AND apellidos = 'Sepúlveda' LIMIT 1));