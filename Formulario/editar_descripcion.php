<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se recibieron datos.']);
    exit;
}

$id_conflicto = (int)($input['id_conflicto'] ?? 0);
$descripcion  = trim($input['descripcion'] ?? '');

if ($id_conflicto <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de conflicto inválido.']);
    exit;
}

try {
    $conn = obtenerConexion();

    $stmt = $conn->prepare(
        "UPDATE gestion_conflictos.casos_conflicto 
         SET descripcion = ? 
         WHERE id_conflicto = ?"
    );
    $stmt->bind_param('si', $descripcion, $id_conflicto);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        echo json_encode(['exito' => false, 'mensaje' => 'No se encontró el conflicto o la descripción es igual.']);
    } else {
        echo json_encode(['exito' => true, 'mensaje' => 'Descripción actualizada correctamente.']);
    }

    $stmt->close();
    $conn->close();

} catch (RuntimeException $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error de sistema: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al actualizar: ' . $e->getMessage()]);
}
?>
