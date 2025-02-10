<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
include('../../conexion.php');

// Obtener el ID de la comanda desde el formulario
$id_comanda = htmlspecialchars($_POST['id_comanda'] ?? '');
if (empty($id_comanda)) {
    die("Error: ID de comanda no proporcionado.");
}

// Conectar a la base de datos
$conn = conectar();

// Consultar detalles del pago, cliente y resumen de alimentos
$stmt = $conn->prepare("
    SELECT 
        p.total,
        p.metodo_pago,
        p.propina,
        p.fecha_pago,
        c.cliente_nombre,
        c.cliente_correo,
        GROUP_CONCAT(CONCAT(dc.alimento_nombre, ' (', dc.cantidad, ')') SEPARATOR ', ') AS alimentos
    FROM 
        pagos p
    JOIN 
        comandas c ON p.id_comanda = c.id_comanda
    JOIN 
        comanda_detalles dc ON c.id_comanda = dc.id_comanda
    WHERE 
        p.id_comanda = ?
    GROUP BY 
        p.id_comanda
");
$stmt->bind_param("i", $id_comanda);
$stmt->execute();
$detalle_comanda = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$detalle_comanda) {
    die("Error: Detalles de la comanda no encontrados.");
}

// Configurar PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'fnafbray67@gmail.com'; // Reemplaza con tu correo
    $mail->Password = 'apvh zjxu mrof byvi';   // Reemplaza con tu contraseña
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Destinatarios
    $mail->setFrom('fnafbray67@gmail.com', 'Restaurante WAOS');
    $mail->addAddress($detalle_comanda['cliente_correo'], $detalle_comanda['cliente_nombre']);

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Resumen de tu pago y consumo';
    $mail->Body = "
        <h1>Gracias por tu pago</h1>
        <p><strong>Cliente:</strong> {$detalle_comanda['cliente_nombre']}</p>
        <p><strong>Total Pagado:</strong> $".number_format($detalle_comanda['total'], 2)."</p>
        <p><strong>Método de Pago:</strong> ".ucfirst(htmlspecialchars($detalle_comanda['metodo_pago']))."</p>
        <p><strong>Propina:</strong> $".number_format($detalle_comanda['propina'], 2)."</p>
        <p><strong>Fecha del Pago:</strong> {$detalle_comanda['fecha_pago']}</p>
        <p><strong>Resumen de Alimentos:</strong> {$detalle_comanda['alimentos']}</p>
        <p>¡Gracias por tu visita!</p>
    ";
    $mail->AltBody = "Gracias por tu pago. Cliente: {$detalle_comanda['cliente_nombre']}, Total: $".number_format($detalle_comanda['total'], 2);

    $mail->send();
    echo "El ticket se envió correctamente al correo de {$detalle_comanda['cliente_nombre']}.";
    
    // Botón para redirigir a index.php
        echo '<br><br><form action="../../../index.php" method="get">
                <button type="submit">Volver al Inicio</button>
            </form>';


} catch (Exception $e) {
    echo "No se pudo enviar el ticket. Error: {$mail->ErrorInfo}";
}
?>
