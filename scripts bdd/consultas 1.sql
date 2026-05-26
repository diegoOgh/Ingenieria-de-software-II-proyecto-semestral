SELECT 
    c.id_conflicto AS 'ID Caso',
    c.estado_caso AS 'Estado',
    CONCAT(f.nombres, ' ', f.apellidos) AS 'Funcionario a Cargo',
    CONCAT(a.nombre, ' ', a.apellidos) AS 'Alumno Involucrado',
    a.curso AS 'Curso Alumno'
FROM gestion_conflictos.casos_conflicto c
INNER JOIN gestion_escolar.funcionarios f 
    ON c.id_funcionario_cargo = f.id_funcionario
INNER JOIN gestion_conflictos.detalle_alumnos_conflicto d 
    ON c.id_conflicto = d.id_conflicto
INNER JOIN gestion_escolar.alumnos a 
    ON d.nro_matricula_alumno = a.nro_matricula;