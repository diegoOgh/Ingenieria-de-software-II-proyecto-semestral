<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

if (!isset($_SESSION['rol_id']) || !isset($_SESSION['funcionario_id'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado.']);
    exit;
}

$rol_id = $_SESSION['rol_id'];
$funcionario_id = $_SESSION['funcionario_id'];

try {
    $conn = obtenerConexion();

    $sql = "SELECT 
                COALESCE(SUM(CASE WHEN estado_caso = 'abierto' THEN 1 ELSE 0 END), 0) AS abiertos,
                COALESCE(SUM(CASE WHEN estado_caso = 'en proceso' THEN 1 ELSE 0 END), 0) AS en_proceso,
                COALESCE(SUM(CASE WHEN estado_caso = 'cerrado' 
                             AND MONTH(fecha_registro) = MONTH(CURRENT_DATE()) 
                             AND YEAR(fecha_registro) = YEAR(CURRENT_DATE()) 
                             THEN 1 ELSE 0 END), 0) AS cerrados_mes
            FROM gestion_conflictos.casos_conflicto";

    if ($rol_id == 1) {
        $sql .= " WHERE id_funcionario_cargo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $funcionario_id);
    } else {
        $stmt = $conn->prepare($sql);
    }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $lista_conflictos = [];
    if ($rol_id == 2) {
        $sql_lista = "SELECT id_conflicto, descripcion, estado_caso, DATE_FORMAT(fecha_registro, '%d %b') as fecha 
                      FROM gestion_conflictos.casos_conflicto 
                      ORDER BY fecha_registro DESC
                      LIMIT 3";
        $result = $conn->query($sql_lista);
        while ($caso = $result->fetch_assoc()) {
            $lista_conflictos[] = $caso;
        }
    }

    echo json_encode([
        'exito' => true,
        'datos' => $row,
        'conflictos' => $lista_conflictos 
    ]);

    $conn->close();

} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
}
?>