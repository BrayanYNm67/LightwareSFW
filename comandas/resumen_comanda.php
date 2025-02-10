<?php
session_start();
include('../conexion.php');

// Validar ID de comanda
if (!isset($_GET['id'])) {
    die("Error: Comanda no encontrada.");
}

$conn = conectar();
$comanda_id = $_GET['id'];

// Consultar comanda principal
$stmt = $conn->prepare("SELECT * FROM comandas WHERE id_comanda = ?");
$stmt->bind_param("i", $comanda_id);
$stmt->execute();
$comanda = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$comanda) {
    die("Comanda no encontrada.");
}

// Consultar detalles de la comanda principal
$stmt = $conn->prepare("SELECT * FROM comanda_detalles WHERE id_comanda = ?");
$stmt->bind_param("i", $comanda_id);
$stmt->execute();
$detalles = $stmt->get_result();
$stmt->close();

// Agrupar comandas individuales en una sola sesión
if (!isset($_SESSION['comandas_agrupadas'])) {
    $_SESSION['comandas_agrupadas'] = [];
}
$_SESSION['comandas_agrupadas'][$comanda_id] = [
    'cliente_nombre' => $comanda['cliente_nombre'],
    'cliente_correo' => $comanda['cliente_correo'],
    'detalles' => []
];

while ($detalle = $detalles->fetch_assoc()) {
    $_SESSION['comandas_agrupadas'][$comanda_id]['detalles'][] = $detalle;
}
$conn->close();

$pago_tipo = $_SESSION['pago_tipo'] ?? 'individual'; // Asume pago individual si no está definido
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Comanda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        h1 {
            color: #4CAF50;
        }

        .comanda {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
        }

        .buttons {
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .fixed-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 50px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            font-size: 16px;
            cursor: pointer;
        }

        .fixed-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Resumen de Comandas</h1>

    <?php foreach ($_SESSION['comandas_agrupadas'] as $id => $comanda): ?>
        <div class="comanda">
            <h2>Comanda ID: <?php echo htmlspecialchars($id); ?></h2>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($comanda['cliente_nombre']); ?></p>
            <p><strong>Correo:</strong> <?php echo htmlspecialchars($comanda['cliente_correo']); ?></p>

            <h3>Alimentos</h3>
            <ul>
                <?php foreach ($comanda['detalles'] as $detalle): ?>
                    <li><?php echo htmlspecialchars($detalle['alimento_nombre']) . " x " . htmlspecialchars($detalle['cantidad']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>

    <div class="buttons">
        <?php switch ($pago_tipo):
            case 'global': ?>
                <button onclick="window.location.href='editar_comanda.php?id=<?php echo $comanda_id; ?>'" class="btn">Editar Comanda</button>
                <?php break;
            case 'individual': ?>
                <button onclick="window.location.href='../seleccionM/indexSeleccion.php'" class="btn">Agregar Otra Comanda</button>
                <?php break;
        endswitch; ?>
        <button onclick="window.location.href='espera_comandas.php'" class="btn">Enviar a Cocina</button>
    </div>

    <button onclick="window.location.href='../seleccionM/indexSeleccion.php'" class="fixed-btn">Regresar</button>
</body>
</html>
