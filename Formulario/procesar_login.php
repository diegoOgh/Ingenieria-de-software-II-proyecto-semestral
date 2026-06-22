<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once 'conexion.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se recibieron datos.']);
    exit;
}

$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['exito' => false, 'mensaje' => 'El correo y la contraseña son obligatorios.']);
    exit;
}

try {
    $conn = obtenerConexion();
    
    $stmt = $conn->prepare(
        "SELECT u.id_usuario, u.contrasena, u.id_rol, f.id_funcionario, f.nombres, f.apellidos 
         FROM gestion_conflictos.usuario u
         JOIN gestion_escolar.funcionarios f ON u.id_funcionario = f.id_funcionario
         WHERE u.email = ?"
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        if ($password === $fila['contrasena']) {
            
            $_SESSION['usuario_id'] = $fila['id_usuario'];
            $_SESSION['funcionario_id'] = $fila['id_funcionario'];
            $_SESSION['rol_id'] = $fila['id_rol'];
            $_SESSION['nombre_usuario'] = $fila['nombres'] . ' ' . $fila['apellidos'];

            echo json_encode([
                'exito' => true, 
                'mensaje' => 'Login exitoso.',
                'rol' => $fila['id_rol'], 
                'nombre' => $fila['nombres'] . ' ' . $fila['apellidos']
            ]);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'Contraseña incorrecta.']);
        }
    } else {
        echo json_encode(['exito' => false, 'mensaje' => 'El usuario no existe en el sistema.']);
    }

    $stmt->close();
    $conn->close();

} catch (RuntimeException $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error de conexión: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al procesar: ' . $e->getMessage()]);
}
?>