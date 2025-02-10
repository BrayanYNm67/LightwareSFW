<?php
session_start();
include('../conexion.php'); // Incluimos el archivo de conexión
$conn = conectar(); // Conexión a la base de datos

// Procesar selección de alimentos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['alimentos']) || empty($_POST['alimentos'])) {
        die("Por favor selecciona al menos un alimento.");
    }

    foreach ($_POST['alimentos'] as $id_alimento) {
        $cantidad = isset($_POST['cantidad'][$id_alimento]) ? intval($_POST['cantidad'][$id_alimento]) : 1;
        if ($cantidad <= 0) {
            die("La cantidad para el alimento $id_alimento no es válida.");
        }

        // Obtener el nombre del alimento desde la base de datos
        $stmt = $conn->prepare("SELECT nombre FROM menu WHERE id_alimento = ?");
        $stmt->bind_param("i", $id_alimento);
        $stmt->execute();
        $stmt->bind_result($nombre);
        $stmt->fetch();
        $stmt->close();

        if (!isset($_SESSION['seleccionados'])) {
            $_SESSION['seleccionados'] = [];
        }

        $_SESSION['seleccionados'][] = [
            'id' => $id_alimento,
            'nombre' => $nombre,
            'cantidad' => $cantidad
        ];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Desayunos</title>
</head>
<body>
    <div class="container">
        <h1>Menú de Desayunos</h1>
        <form action="./procesarSeleccion/procesar_seleccion.php" method="POST">
            <label for="alimentos">Selecciona tus alimentos y cantidades:</label>
            <div class="menu-list">
                <?php
                
                // Llamamos a la función conectar para obtener la conexión a la base de datos
                $conn = conectar();

                // Consulta para obtener los alimentos de la categoría "Desayuno"
                $sql = "SELECT id_alimento, nombre, precio FROM menu WHERE categoria = 'Desayuno'";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <div class='menu-item'>
                            <label>
                                <input type='checkbox' name='alimentos[]' value='" . $row['id_alimento'] . "'>
                                " . $row['nombre'] . " - $" . number_format($row['precio'], 2) . "
                            </label>
                            <input type='hidden' name='nombre[" . $row['id_alimento'] . "]' value='" . htmlspecialchars($row['nombre']) . "'>
                            <input type='number' name='cantidad[" . $row['id_alimento'] . "]' min='1' max='100' value='1'>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>No hay desayunos disponibles</p>";
                }
                $conn->close();

                ?>
            </div>
            
            <button type="submit">Seleccionar</button>
            <div class="back-button">
                <button type="button" onclick="window.location.href='../index.php'">Regresar al inicio</button>
            </div>
        </form>
    </div>
</body>
</html>
