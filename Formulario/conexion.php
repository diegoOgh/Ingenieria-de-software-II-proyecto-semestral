<?php
// Cambiar valores dependiendo del WAMP
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');  // En WAMP por defecto no hay contraseña

function obtenerConexion(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, 'gestion_escolar', DB_PORT);

    if ($conn->connect_error) {
        // TEST 3
        throw new RuntimeException('Error al conectar con la base de datos: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
?>
