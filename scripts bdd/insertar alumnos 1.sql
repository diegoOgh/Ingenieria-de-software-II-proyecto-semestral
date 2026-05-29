-- Insertar datos maestros en gestion_escolar
INSERT INTO gestion_escolar.funcionarios (nombres, apellidos) VALUES ('Juan', 'Pérez');
INSERT INTO gestion_escolar.alumnos (nro_matricula, nombre, apellidos, curso) VALUES (202601, 'Diego', 'Anabalón', '3ro Medio A');
INSERT INTO gestion_escolar.alumnos (nro_matricula, nombre, apellidos, curso) VALUES (202602, 'Sofía', 'Morales', '4to Medio B');

-- Registrar un conflicto en gestion_conflictos a cargo del funcionario 1 (Juan Pérez)
INSERT INTO gestion_conflictos.casos_conflicto (descripcion, estado_caso, id_funcionario_cargo) VALUES ('Discusión ocurrida durante una actividad escolar.','en proceso', 1);

-- Asociar los dos alumnos al conflicto número 1
INSERT INTO gestion_conflictos.detalle_alumnos_conflicto (id_conflicto, nro_matricula_alumno) VALUES (1, 202601);
INSERT INTO gestion_conflictos.detalle_alumnos_conflicto (id_conflicto, nro_matricula_alumno) VALUES (1, 202602);
