<?php
include('../../conexion.php');

// Obtener el ID de la comanda desde la URL
$id_comanda = htmlspecialchars($_GET['id_comanda'] ?? '');
if (empty($id_comanda)) {
    die("Error: ID de comanda no proporcionado.");
}

// Conectar a la base de datos
$conn = conectar();

// Consultar los detalles del pago y la comanda
$stmt = $conn->prepare("
    SELECT 
        p.id_comanda,
        p.total,
        p.metodo_pago,
        p.cantidad_recibida,
        p.propina,
        p.fecha_pago,
        c.cliente_nombre,
        c.cliente_correo
    FROM 
        pagos p
    JOIN 
        comandas c ON p.id_comanda = c.id_comanda
    WHERE 
        p.id_comanda = ?
");
$stmt->bind_param("i", $id_comanda);
$stmt->execute();
$result = $stmt->get_result();
$detalle_pago = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$detalle_pago) {
    die("Error: Detalles del pago no encontrados.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Pago Registrado Exitosamente</h1>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($detalle_pago['cliente_nombre']); ?></p>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($detalle_pago['cliente_correo']); ?></p>
        <p><strong>Total Pagado:</strong> $<?php echo number_format($detalle_pago['total'], 2); ?></p>
        <p><strong>Método de Pago:</strong> <?php echo ucfirst(htmlspecialchars($detalle_pago['metodo_pago'])); ?></p>
        <?php if ($detalle_pago['metodo_pago'] === 'efectivo'): ?>
            <p><strong>Cantidad Recibida:</strong> $<?php echo number_format($detalle_pago['cantidad_recibida'], 2); ?></p>
            <p><strong>Cambio:</strong> $<?php echo number_format($detalle_pago['cantidad_recibida'] - $detalle_pago['total'], 2); ?></p>
        <?php endif; ?>
        <p><strong>Propina:</strong> $<?php echo number_format($detalle_pago['propina'], 2); ?></p>
        <p><strong>Fecha del Pago:</strong> <?php echo htmlspecialchars($detalle_pago['fecha_pago']); ?></p>

        <form action="enviar_ticket.php" method="POST">
            <input type="hidden" name="id_comanda" value="<?php echo htmlspecialchars($id_comanda); ?>">
            <button type="submit" class="button">Enviar Ticket al Correo</button>
        </form>

        <a href="../../../index.php" class="button">Regresar a Comandas</a>
    </div>
</body>
</html>
