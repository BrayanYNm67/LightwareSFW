<?php
include('../conexion.php');
$conn = conectar();
if ($conn) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "Error al conectar.";
}
?>
