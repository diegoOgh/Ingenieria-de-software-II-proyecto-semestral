<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'conexion.php';

$curso = trim($_GET['curso'] ?? '');
$q     = trim($_GET['q'] ?? '');

if (empty($curso) || strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $conn = obtenerConexion();

    $stmt = $conn->prepare(
        "SELECT nro_matricula, nombre, apellidos, curso
         FROM gestion_escolar.alumnos
         WHERE curso = ?
           AND CONCAT(nombre, ' ', apellidos) LIKE ?
         ORDER BY apellidos, nombre
         LIMIT 10"
    );

    $like = '%' . $q . '%';
    $stmt->bind_param('ss', $curso, $like);
    $stmt->execute();
    $res = $stmt->get_result();

    $alumnos = [];
    while ($fila = $res->fetch_assoc()) {
        $alumnos[] = [
            'nro_matricula' => $fila['nro_matricula'],
            'nombre'        => $fila['nombre'] . ' ' . $fila['apellidos'],
            'curso'         => $fila['curso'],
        ];
    }

    $stmt->close();
    $conn->close();

    echo json_encode($alumnos);

} catch (Exception $e) {
    echo json_encode([]);
}
?>