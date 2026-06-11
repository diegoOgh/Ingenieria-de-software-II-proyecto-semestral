<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once 'conexion.php';

$q = trim($_GET['q'] ?? '');

//Se ejecuta cuando el frontend hace: fetch('buscar_funcionarios.php?q=todos')
if ($q === 'todos') {
    try {
        $conn = obtenerConexion();

        // módulo de conflictos para traer SOLO a quienes tienen cuenta activa Y 
        // cuyo rol es exactamente 1 (Funcionario General).
        $stmt = $conn->prepare(
            "SELECT f.id_funcionario, f.nombres, f.apellidos
             FROM gestion_escolar.funcionarios f
             INNER JOIN gestion_conflictos.usuario u ON u.id_funcionario = f.id_funcionario
             WHERE u.id_rol = 1
             ORDER BY f.apellidos, f.nombres"
        );
        $stmt->execute();
        $res = $stmt->get_result();
        
        $funcionarios = [];
        while ($fila = $res->fetch_assoc()) {
            $funcionarios[] = [
                'id'     => $fila['id_funcionario'],
                'nombre' => $fila['nombres'] . ' ' . $fila['apellidos'],
            ];
        }
        
        $stmt->close();
        $conn->close();
        
        // Enviamos la lista exitosa al frontend
        echo json_encode($funcionarios);
        
    } catch (Exception $e) {
       
        echo json_encode(['error_real' => 'Error en consulta (todos): ' . $e->getMessage()]);
    }
    exit;
}

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $conn = obtenerConexion();

    $stmt = $conn->prepare(
        "SELECT f.id_funcionario, f.nombres, f.apellidos
         FROM gestion_escolar.funcionarios f
         INNER JOIN gestion_conflictos.usuario u ON u.id_funcionario = f.id_funcionario
         WHERE u.id_rol = 1
           AND CONCAT(f.nombres, ' ', f.apellidos) LIKE ?
         ORDER BY f.apellidos, f.nombres
         LIMIT 10"
    );

    $like = '%' . $q . '%';
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $res = $stmt->get_result();

    $funcionarios = [];
    while ($fila = $res->fetch_assoc()) {
        $funcionarios[] = [
            'id'     => $fila['id_funcionario'],
            'nombre' => $fila['nombres'] . ' ' . $fila['apellidos'],
        ];
    }

    $stmt->close();
    $conn->close();

    echo json_encode($funcionarios);

} catch (Exception $e) {

    echo json_encode(['error_real' => 'Error en consulta (búsqueda): ' . $e->getMessage()]);
}
?>