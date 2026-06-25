<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$id_conflicto = (int)($_POST['id_conflicto'] ?? 0);

if ($id_conflicto <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de conflicto inválido.']);
    exit;
}

if (!isset($_FILES['evidencia']) || $_FILES['evidencia']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['exito' => false, 'mensaje' => 'Archivo no válido.']);
    exit;
}

$archivo = $_FILES['evidencia'];

// Validar que sea imagen
if (getimagesize($archivo['tmp_name']) === false) {
    echo json_encode(['exito' => false, 'mensaje' => 'El archivo no es una imagen válida.']);
    exit;
}

// Crear una carpeta única para este conflicto
$directorio_destino = "uploads/evidencias/conflicto_{$id_conflicto}/";
if (!is_dir($directorio_destino)) {
    mkdir($directorio_destino, 0755, true);
}

// Contar cuántas imágenes ya existen para calcular el número "X"
$archivos_existentes = glob($directorio_destino . "evidencia*.*");
$numero_siguiente = count($archivos_existentes) + 1;

// Obtener extensión original (.png, .jpg, etc.)
$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);

// Nombre final solicitado: evidenciaX.extensión
$nombre_final = "evidencia{$numero_siguiente}.{$extension}";
$ruta_completa = $directorio_destino . $nombre_final;

if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
    echo json_encode(['exito' => true, 'mensaje' => 'Evidencia guardada como ' . $nombre_final]);
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar localmente.']);
}
?>