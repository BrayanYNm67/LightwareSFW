<?php
include('../controllers/getEsperaController.php');
$conn = conectar();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Comandas</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Estado de Comandas</h1>
        <table>
            <thead>
                <tr>
                    <th>ID Comanda</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Tiempo de Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $estado_color = $row['estado'] === 'listo' ? 'green' : 'orange';
                        echo "<tr style='color: {$estado_color};'>
                                <td>{$row['id_comanda']}</td>
                                <td>{$row['cliente_nombre']}</td>
                                <td>{$row['estado']}</td>
                                <td>{$row['tiempo_registro']}</td>";
                        
                        // Mostrar botón solo para comandas "listo"
                        if ($row['estado'] === 'listo') {
                            echo "<td>
                                    <button 
                                        onclick=\"window.location.href='pagosComandas/pagos_comandas.php?id_comanda={$row['id_comanda']}'\" 
                                        class='button-a'>
                                        Continuar al Pago
                                    </button>
                                  </td>";
                        } else {
                            echo "<td>-</td>";
                        }
                        
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay comandas registradas</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
