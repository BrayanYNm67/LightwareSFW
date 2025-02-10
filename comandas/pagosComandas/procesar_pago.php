<?php
include('../../conexion.php');

$id_comanda = htmlspecialchars($_POST['id_comanda'] ?? '');
$total = htmlspecialchars($_POST['total'] ?? 0);
$metodo_pago = htmlspecialchars($_POST['metodo_pago'] ?? '');
$cantidad_recibida = htmlspecialchars($_POST['cantidad_recibida'] ?? null);
$propina = htmlspecialchars($_POST['propina'] ?? 0);

if (empty($id_comanda) || empty($metodo_pago)) {
    die("Error: Datos incompletos.");
}

// Conectar a la base de datos
$conn = conectar();

$stmt = $conn->prepare("
    INSERT INTO pagos (id_comanda, total, metodo_pago, cantidad_recibida, propina, fecha_pago)
    VALUES (?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param(
    "idssd",
    $id_comanda,
    $total,
    $metodo_pago,
    $cantidad_recibida,
    $propina
);

if ($stmt->execute()) {
    echo "Pago registrado exitosamente.";
    header("Location: ./pago_exitoso.php?id_comanda=$id_comanda");
} else {
    echo "Error al registrar el pago: " . $stmt->error;
}


// Si el método de pago es con tarjeta
if ($metodo_pago === 'tarjeta') {
    $numero_tarjeta = htmlspecialchars($_POST['numero_tarjeta'] ?? '');
    $clave_tarjeta = htmlspecialchars($_POST['clave_tarjeta'] ?? '');

    // Verificar si la tarjeta existe y la clave es correcta
    $stmt = $conn->prepare("SELECT * FROM tarjeta WHERE numero_tarjeta = ? AND clave_tarjeta = ?");
    $stmt->bind_param("ss", $numero_tarjeta, $clave_tarjeta);
    $stmt->execute();
    $tarjeta = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$tarjeta) {
        die("Error: Tarjeta no encontrada o datos incorrectos.");
    }

    // Verificar si el saldo es suficiente
    if ($tarjeta['saldo'] < $total) {
        die("Error: Saldo insuficiente en la tarjeta.");
    }

    // Descontar el saldo de la tarjeta
    $nuevo_saldo = $tarjeta['saldo'] - $total;
    $stmt = $conn->prepare("UPDATE tarjeta SET saldo = ? WHERE id_tarjeta = ?");
    $stmt->bind_param("di", $nuevo_saldo, $tarjeta['id_tarjeta']);
    if (!$stmt->execute()) {
        die("Error: No se pudo actualizar el saldo de la tarjeta.");
    }
    $stmt->close();
}


// Redirigir al usuario
    header("Location: ./pago_exitoso.php?id_comanda=$id_comanda");

$stmt->close();
$conn->close();
?>
