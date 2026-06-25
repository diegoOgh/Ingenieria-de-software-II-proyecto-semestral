<?php
// 1. Forzar almacenamiento en búfer para evitar que cualquier Warning previo rompa el JSON
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Desactivar visualización de errores directos en el output
error_reporting(0); 
ini_set('display_errors', 0);

$id_conflicto = (int)($_GET['id_conflicto'] ?? 0);
$imagenes = [];

if ($id_conflicto > 0) {
    // Definición exacta de tu ruta: uploads/evidencias/conflicto_x
    $directorio = "uploads/evidencias/conflicto_{$id_conflicto}/";

    if (is_dir($directorio)) {
        // Buscamos cualquier archivo que empiece con "evidencia"
        $archivos = glob($directorio . "evidencia*.*");
        
        if ($archivos !== false) {
            foreach ($archivos as $archivo) {
                $imagenes[] = [
                    'nombre' => basename($archivo),
                    'ruta' => $directorio . basename($archivo)
                ];
            }
        }
    }
}

// 2. Limpiar el búfer por si PHP metió ruido de fondo de manera silenciosa
ob_clean();

// 3. Retornar la respuesta JSON limpia
echo json_encode($imagenes);
exit;
?>