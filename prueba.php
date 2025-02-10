<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';        // Servidor SMTP de Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'fnafbray67@gmail.com';
    $mail->Password = 'apvh zjxu mrof byvi';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Destinatarios
    $mail->setFrom('fnafbray67@gmail.com', 'Brayan');
    $mail->addAddress('vc0312ml@gmail.com', 'Victoria');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Correo de prueba';
    $mail->Body = 'Este es un correo de prueba enviado desde PHPMailer.';
    $mail->AltBody = 'Este es un correo de prueba enviado desde PHPMailer.';

    $mail->send();
    echo 'El correo se envió correctamente.';
} catch (Exception $e) {
    echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
}
