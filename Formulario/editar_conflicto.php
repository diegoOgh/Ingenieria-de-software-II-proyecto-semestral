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
$estado       = trim($input['estado'] ?? '');

$estados_validos = ['abierto', 'en proceso', 'cerrado'];

if ($id_conflicto <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de conflicto inválido.']);
    exit;
}

if (!in_array($estado, $estados_validos, true)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Estado inválido.']);
    exit;
}

try {
    $conn = obtenerConexion();

    $stmt = $conn->prepare(
        "UPDATE gestion_conflictos.casos_conflicto 
         SET descripcion = ?, estado_caso = ?
         WHERE id_conflicto = ?"
    );
    $stmt->bind_param('ssi', $descripcion, $estado, $id_conflicto);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo json_encode(['exito' => true, 'mensaje' => 'Conflicto actualizado correctamente.']);

} catch (RuntimeException $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error de sistema: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al actualizar: ' . $e->getMessage()]);
}
?>
