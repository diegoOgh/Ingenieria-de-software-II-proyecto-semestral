<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';
require_once 'enviar_correo.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se recibieron datos.']);
    exit;
}

$alumnos_ids    = $input['alumnos'] ?? [];
$fecha          = trim($input['fecha'] ?? '');
$funcionario_id = (int)($input['funcionario_id'] ?? 0);
$descripcion    = trim($input['descripcion'] ?? '');

$errores = [];

if ($funcionario_id <= 0) {
    $errores[] = 'El funcionario es obligatorio.';
}

if (empty($alumnos_ids) || !is_array($alumnos_ids)) {
    $errores[] = 'Debes seleccionar al menos un alumno.';
}
if (empty($fecha)) {
    $errores[] = 'La fecha es obligatoria.';
} else {
    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$fechaObj) {
        $errores[] = 'Formato de fecha inválido.';
    } elseif ($fecha > date('Y-m-d')) {
        $errores[] = 'La fecha no puede ser posterior a la fecha actual.';
    }
}

if (!empty($errores)) {
    echo json_encode(['exito' => false, 'mensaje' => implode(' | ', $errores)]);
    exit;
}

// TEST 1 y TEST 3
try {
    $conn = obtenerConexion();
    $conn->begin_transaction();

    // insertar el funcionario por nombre
    $id_funcionario = $funcionario_id;

    $stmt3 = $conn->prepare(
        "INSERT INTO gestion_conflictos.casos_conflicto
        (
            fecha_registro,
            descripcion,
            estado_caso,
            id_funcionario_cargo
        )
        VALUES (?, ?, 'abierto', ?)"
    );

    $stmt3->bind_param(
        'ssi',
        $fecha,
        $descripcion,
        $id_funcionario
    );

    $stmt3->execute();
    $id_conflicto = $conn->insert_id;
    $stmt3->close();

    // Procesar alumnos si viene mas de uno separado por comas
    foreach ($alumnos_ids as $nro_matricula) {
        $nro_matricula = (int) $nro_matricula;
        if ($nro_matricula <= 0) continue;

        $stmt6 = $conn->prepare(
            "INSERT INTO gestion_conflictos.detalle_alumnos_conflicto
            (id_conflicto, nro_matricula_alumno) VALUES (?, ?)"
        );
        $stmt6->bind_param('ii', $id_conflicto, $nro_matricula);
        $stmt6->execute();
        $stmt6->close();
    }


    $conn->commit();
    enviarNotificacionEncargado($id_conflicto, $fecha, $descripcion);
    $conn->close();

    // TEST 1: Respuesta de éxito
    echo json_encode([
        'exito'       => true,
        'mensaje'     => 'Conflicto registrado exitosamente.',
        'id_conflicto' => $id_conflicto
    ]);

} catch (RuntimeException $e) {
    // TEST 3: Error de conexión a la BDD
    echo json_encode(['exito' => false, 'mensaje' => 'Error de sistema: ' . $e->getMessage()]);
} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar: ' . $e->getMessage()]);
}
?>