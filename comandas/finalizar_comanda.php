<?php
session_start();
include('../conexion.php');

// Validar datos del cliente
if (!isset($_POST['cliente_nombre'], $_POST['cliente_correo'])) {
    die("Error: Datos del cliente incompletos.");
}

$cliente_nombre = $_POST['cliente_nombre'];
$cliente_correo = $_POST['cliente_correo'];

// Validar que haya alimentos seleccionados
if (empty($_SESSION['seleccionados'])) {
    die("No hay alimentos seleccionados.");
}

// Datos de la sesión
$mesa_id = $_SESSION['mesa_id'] ?? 'No seleccionada';
$mesero_id = $_SESSION['mesero_id'] ?? null;
$mesero_nombre = $_SESSION['mesero_nombre'] ?? 'No seleccionado';
$fecha_registro = date('Y-m-d');
$hora_registro = date('H:i:s');

// Conexión a la base de datos
$conn = conectar();

// Insertar la comanda
$stmt = $conn->prepare("INSERT INTO comandas (mesa_id, cliente_nombre, cliente_correo, mesero_id, mesero_nombre, fecha_registro, hora_registro) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ississs", $mesa_id, $cliente_nombre, $cliente_correo, $mesero_id, $mesero_nombre, $fecha_registro, $hora_registro);
if (!$stmt->execute()) {
    die("Error al guardar la comanda: " . $stmt->error);
}
$comanda_id = $stmt->insert_id; // ID de la comanda recién creada
$stmt->close();

// Insertar los detalles de la comanda
$stmt = $conn->prepare("INSERT INTO comanda_detalles (id_comanda, alimento_id, alimento_nombre, cantidad) VALUES (?, ?, ?, ?)");
foreach ($_SESSION['seleccionados'] as $item) {
    $stmt->bind_param("iisi", $comanda_id, $item['id'], $item['nombre'], $item['cantidad']);
    if (!$stmt->execute()) {
        die("Error al guardar los detalles de la comanda: " . $stmt->error);
    }
}
$stmt->close();

// Vaciar el carrito
unset($_SESSION['seleccionados']);

$conn->close();

// Redirigir al resumen de la comanda
header("Location: resumen_comanda.php?id=$comanda_id");
exit();
?>
