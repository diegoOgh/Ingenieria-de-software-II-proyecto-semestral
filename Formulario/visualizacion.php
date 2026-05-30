<?php
require_once 'conexion.php';

$conn = obtenerConexion();

$sql = "
    SELECT
        cc.id_conflicto,
        cc.fecha_registro,
        cc.descripcion,
        CONCAT(f.nombres, ' ', f.apellidos) AS funcionario,

        GROUP_CONCAT(
            CONCAT(a.nombre, ' ', a.apellidos)
            SEPARATOR ', '
        ) AS alumnos

    FROM gestion_conflictos.casos_conflicto cc

    JOIN gestion_escolar.funcionarios f
        ON cc.id_funcionario_cargo = f.id_funcionario

    LEFT JOIN gestion_conflictos.detalle_alumnos_conflicto dac
        ON cc.id_conflicto = dac.id_conflicto

    LEFT JOIN gestion_escolar.alumnos a
        ON dac.nro_matricula_alumno = a.nro_matricula

    GROUP BY
        cc.id_conflicto,
        cc.fecha_registro,
        cc.descripcion,
        funcionario

    ORDER BY cc.id_conflicto DESC
";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conflictos Registrados</title>
</head>
<body>

<h1>Conflictos Registrados</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Funcionario</th>
        <th>Alumnos</th>
        <th>Fecha</th>
        <th>Descripción</th>
    </tr>

    <?php while($fila = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?= $fila['id_conflicto'] ?></td>
        <td><?= htmlspecialchars($fila['funcionario']) ?></td>
        <td><?= htmlspecialchars($fila['alumnos']) ?></td>
        <td><?= $fila['fecha_registro'] ?></td>
        <td><?= htmlspecialchars($fila['descripcion']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<br>

<a href="index.html">
    <button>Volver</button>
</a>

</body>
</html>

<?php
$conn->close();
?>