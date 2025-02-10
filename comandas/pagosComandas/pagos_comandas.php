<?php
session_start();
include('../../conexion.php');

// Obtener el ID de la comanda seleccionada
$id_comanda = htmlspecialchars($_GET['id_comanda'] ?? '');
if (empty($id_comanda)) {
    die("Error: ID de comanda no proporcionado.");
}

$conn = conectar();
$stmt = $conn->prepare("
    SELECT 
        c.cliente_nombre, 
        c.cliente_correo, 
        SUM(d.cantidad * m.precio) AS total 
    FROM 
        comandas c
    JOIN 
        comanda_detalles d ON c.id_comanda = d.id_comanda
    JOIN 
        menu m ON d.alimento_id = m.id_alimento
    WHERE 
        c.id_comanda = ?
");
$stmt->bind_param("i", $id_comanda);
$stmt->execute();
$result = $stmt->get_result();
$comanda = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$comanda) {
    die("Error: Comanda no encontrada.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago de Comandas</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    // Mostrar campo adicional cuando el método de pago es "Efectivo"
    function toggleEfectivoFields() {
        const metodoPago = document.getElementById('metodo_pago').value;
        const efectivoFields = document.getElementById('efectivo-fields');
        if (metodoPago === 'efectivo') {
            efectivoFields.style.display = 'block';
        } else {
            efectivoFields.style.display = 'none';
        }

        // Mostrar campos de tarjeta si se selecciona 'tarjeta'
        const tarjetaFields = document.getElementById('tarjeta-fields');
        if (metodoPago === 'tarjeta') {
            tarjetaFields.style.display = 'block';
        } else {
            tarjetaFields.style.display = 'none';
        }
    }
    </script>
</head>
<body>
    <div class="container">
        <h1>Pago de Comandas</h1>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($comanda['cliente_nombre']); ?></p>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($comanda['cliente_correo']); ?></p>
        <p><strong>Total a Pagar:</strong> $<?php echo number_format($comanda['total'], 2); ?></p>

        <form action="procesar_pago.php" method="POST">
            <input type="hidden" name="id_comanda" value="<?php echo htmlspecialchars($id_comanda); ?>">
            <input type="hidden" name="total" value="<?php echo htmlspecialchars($comanda['total']); ?>">

            <label for="metodo_pago">Método de Pago:</label>
            <select id="metodo_pago" name="metodo_pago" onchange="toggleEfectivoFields()" required>
                <option value="">Seleccione un método</option>
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta</option>
            </select>

            <!-- Campos adicionales para efectivo -->
            <div id="efectivo-fields" style="display: none;">
                <label for="cantidad_recibida">Cantidad Recibida:</label>
                <input type="number" id="cantidad_recibida" name="cantidad_recibida" placeholder="Monto recibido en $" min="0" step="0.01">
            </div>

            <!-- Campos para pago con tarjeta -->
            <div id="tarjeta-fields" style="display: none;">
                <label for="numero_tarjeta">Número de Tarjeta:</label>
                <input type="text" id="numero_tarjeta" name="numero_tarjeta" placeholder="Número de tarjeta" required>

                <label for="clave_tarjeta">Clave:</label>
                <input type="password" id="clave_tarjeta" name="clave_tarjeta" placeholder="Clave" required>
            </div>

            <script>
            function togglePaymentFields() {
                const metodoPago = document.getElementById('metodo_pago').value;
                document.getElementById('tarjeta-fields').style.display = metodoPago === 'tarjeta' ? 'block' : 'none';
                document.getElementById('efectivo-fields').style.display = metodoPago === 'efectivo' ? 'block' : 'none';
            }
            </script>

            <label for="propina">Propina:</label>
            <input type="number" id="propina" name="propina" placeholder="Monto en $" min="0" step="0.01" required>

            <button type="submit" class="button">Confirmar Pago</button>
        </form>
    </div>
</body>
</html>
