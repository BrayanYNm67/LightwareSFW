<?php
include('../conexion.php'); // Incluir el archivo de conexión

$conn = conectar(); // Llamar a la función para conectar a la base de datos

// Obtener los meseros
$query = "SELECT id_mesa, estado, espacio, descripcion FROM mesas";
$result = $conn->query($query);

$mesas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mesas[] = $row;
    }
}

// Enviar los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($mesas);

$conn->close(); // Cerrar la conexión
?>
