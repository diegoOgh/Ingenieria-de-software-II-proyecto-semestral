<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarNotificacionEncargado($id_conflicto, $fecha, $descripcion) {
    // Eliminamos la consulta a la BD temporalmente 
    $listaAlumnos = "<li>(Listado pendiente de configuración)</li>";

    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'notificaciones.software2@gmail.com'; 
        $mail->Password   = 'ucbdvzyvaecxyrhq'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Remitente y Destinatario (es el mismo ya que es de prueba)
        $mail->setFrom('notificaciones.software2@gmail.com', 'Sistema de Convivencia');
        $mail->addAddress('notificaciones.software2@gmail.com', 'Encargado de Convivencia');

        // Contenido del correo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'PRUEBA DE SISTEMA: Caso #' . $id_conflicto;
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif;'>
                <h2 style='color: #2c3e50;'>Prueba de funcionamiento</h2>
                <p>El sistema está enviando correos correctamente.</p>
                <hr>
                <p><strong>ID del Caso:</strong> #{$id_conflicto}</p>
                <p><strong>Descripción:</strong> {$descripcion}</p>
            </div>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Si falla, aquí veremos el error real en tu archivo de logs
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
?>