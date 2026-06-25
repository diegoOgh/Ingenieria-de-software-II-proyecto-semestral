<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

$estado = $_GET['estado'] ?? '';
$id_funcionario = $_SESSION['funcionario_id'] ?? 0;
$rol_id = $_SESSION['rol_id'] ?? 0;

if (!$id_funcionario || empty($estado)) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit;
}

try {
    $conn = obtenerConexion();
    
    // El Encargado ve los de todos. El Funcionario general ve solo los suyos.
    if ($rol_id == 2) {
        $sql = "SELECT id_conflicto, descripcion, fecha_registro as fecha, estado_caso
                FROM gestion_conflictos.casos_conflicto
                WHERE estado_caso = ? ORDER BY fecha_registro DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $estado);
    } else {
        $sql = "SELECT id_conflicto, descripcion, fecha_registro as fecha, estado_caso
                FROM gestion_conflictos.casos_conflicto
                WHERE id_funcionario_cargo = ? AND estado_caso = ? ORDER BY fecha_registro DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_funcionario, $estado);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    $conflictos = [];
    while($row = $res->fetch_assoc()) {
        $conflictos[] = $row;
    }

    echo json_encode(['exito' => true, 'conflictos' => $conflictos]);
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error de servidor']);
}
?>