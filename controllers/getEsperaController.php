<?php
include('../conexion.php');
$conn = conectar();

// Actualizar estado de comandas que han superado el tiempo de espera
$sql_update = "
    UPDATE comandas
    SET estado = 'listo'
    WHERE estado = 'pendiente' AND TIMESTAMPDIFF(SECOND, tiempo_registro, NOW()) >= 60";
$conn->query($sql_update);

// Obtener comandas pendientes y listas
$sql = "
    SELECT id_comanda, cliente_nombre, estado, tiempo_registro 
    FROM comandas
    ORDER BY FIELD(estado, 'pendiente', 'listo'), tiempo_registro ASC";
$result = $conn->query($sql);

?>