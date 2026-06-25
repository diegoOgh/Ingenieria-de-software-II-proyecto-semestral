<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

// Verificación de seguridad: Sólo el encargado (rol 2) puede usar el cambio rápido
if (($_SESSION['rol_id'] ?? 0) != 2) {
    echo json_encode(['exito' => false, 'mensaje' => 'Permisos insuficientes']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id_conflicto = $input['id_conflicto'] ?? null;
$nuevo_estado = $input['estado'] ?? null;

if (!$id_conflicto || !$nuevo_estado) {
    echo json_encode(['exito' => false, 'mensaje' => 'Datos incompletos']);
    exit;
}

try {
    $conn = obtenerConexion();
    $sql = "UPDATE gestion_conflictos.casos_conflicto SET estado_caso = ? WHERE id_conflicto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevo_estado, $id_conflicto);
    $stmt->execute();

    if ($stmt->errno === 0) {
        echo json_encode(['exito' => true, 'mensaje' => 'Estado actualizado']);
    } else {
        echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error de servidor']);
}
?>