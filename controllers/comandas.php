<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $id_mesa = $_POST['id_mesa'];         // ID de la mesa seleccionada
    $id_cliente = $_POST['id_cliente'];   // ID del cliente registrado
    $metodo_pago = $_POST['metodo_pago']; // 'Efectivo' o 'Tarjeta'
    $propina = isset($_POST['propina']) ? $_POST['propina'] : 0.00; // Propina opcional

    // Fecha y hora actuales del sistema
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    try {
        // Preparar la consulta para insertar en la tabla `comandas`
        $stmt = $conn->prepare(
            "INSERT INTO comandas (id_mesa, fecha, hora, id_cliente, metodo_pago, propina, estado) 
             VALUES (?, ?, ?, ?, ?, ?, 'Abierta')"
        );

        // Ejecutar consulta con los parámetros
        $stmt->execute([$id_mesa, $fecha, $hora, $id_cliente, $metodo_pago, $propina]);

        echo json_encode(["success" => true, "message" => "Comanda registrada con éxito."]);
    } catch (PDOException $e) {
        // Manejo de errores en caso de fallo
        echo json_encode(["success" => false, "message" => "Error al registrar la comanda.", "error" => $e->getMessage()]);
    }
}
?>
