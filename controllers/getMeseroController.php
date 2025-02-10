<?php
include('../conexion.php'); // Incluir el archivo de conexión

$conn = conectar(); // Llamar a la función para conectar a la base de datos

// Obtener los meseros
$query = "SELECT id_mesero, nombre FROM meseros";
$result = $conn->query($query);

$meseros = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $meseros[] = $row;
    }
}

// Enviar los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($meseros);

$conn->close(); // Cerrar la conexión
?>
