<?php
session_start();
include('../conexion.php');

// Verificar si el mesero ha cambiado
if (isset($_POST['nuevo_mesero_id'])) {
    $nuevo_mesero_id = $_POST['nuevo_mesero_id'];
    if (!isset($_SESSION['mesero_id']) || $_SESSION['mesero_id'] != $nuevo_mesero_id) {
        // Cerrar sesión actual y comenzar una nueva
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['mesero_id'] = $nuevo_mesero_id;
    }
}

// Recuperar mesero_id y mesa_id de la sesión
$mesero_id = isset($_SESSION['mesero_id']) ? $_SESSION['mesero_id'] : null;
$mesa_id = isset($_SESSION['mesa_id']) ? $_SESSION['mesa_id'] : 'No seleccionada';

$mesero_nombre = 'No seleccionado'; 

if ($mesero_id) {
    $conn = conectar(); // Función para conectar a la base de datos
    $stmt = $conn->prepare("SELECT nombre FROM meseros WHERE id_mesero = ?");
    $stmt->bind_param("i", $mesero_id);
    $stmt->execute();
    $stmt->bind_result($nombre);
    if ($stmt->fetch()) {
        $mesero_nombre = $nombre;
    }
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menú de Categorías</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h1>NUESTRO MENÚ</h1>
        <h2>Seleccione una Categoría</h2>

        <p><strong>Mesero Seleccionado:</strong> <?php echo htmlspecialchars($mesero_nombre); ?></p>
        <p><strong>Mesa Seleccionada:</strong> <?php echo htmlspecialchars($mesa_id); ?></p><br>
        
        <!-- Categorías -->
        <a href="../categorias/desayuno.php">
            <div class="button">
                <img src="../img/desayuno.png" alt="Desayuno">
                <br>
                DESAYUNO
            </div>
        </a>
        
        <a href="../categorias/comida.php">
            <div class="button">
                <img src="../img/almuerzo.png" alt="Comida">
                <br>
                COMIDA
            </div>
        </a>

        <a href="../categorias/cena.php">
            <div class="button">
                <img src="../img/cena.jpg" alt="Cena">
                <br>
                CENA
            </div>
        </a>
        
        <a href="../categorias/bebidas.php">
            <div class="button">
                <img src="../img/bebida.png" alt="Bebidas">
                <br>
                BEBIDAS
            </div>
        </a>
        
        <a href="../logout.php">
            <div class="button">
                <img src="../img/regresar.png" alt="Salir" id="salirButton">
                <br>
                SALIR
            </div>   
        </a>
    </div>

    <!-- Icono del carrito -->
    <div class="cart-icon" onclick="showPaymentPopup()">
        🛒 <span id="cart-count"><?php echo count($_SESSION['seleccionados'] ?? []); ?></span>
    </div>

    <!-- Pop-up para tipo de pago -->
    <div id="payment-popup1" class="modal-container" style="display: none;" onclick="closePaymentPopup(event)">
        <div class="payment-form">
            <h3>Seleccione el Tipo de Pago</h3>
            <button onclick="handlePayment('global')">Pago Global</button>
            <button onclick="handlePayment('individual')">Pago Individual</button>
        </div>
    </div>

    <!-- Pop-up para finalizar comanda -->
    <div id="cart-popup" class="cart-popup" style="display: none;">
        <h3>Alimentos Seleccionados</h3>
        <ul>
            <?php
            if (!empty($_SESSION['seleccionados'])) {
                foreach ($_SESSION['seleccionados'] as $index => $item) {
                    echo "
                    <li style='display: flex; align-items: center;'>
                        <form action='../categorias/procesarSeleccion/procesar_seleccion.php' method='POST' style='margin-right: 10px;'>
                            <input type='hidden' name='accion' value='eliminar'>
                            <input type='hidden' name='index' value='{$index}'>
                            <button type='submit' class='delete-btn'>&times;</button>
                        </form>
                        <span>{$item['nombre']} x {$item['cantidad']}</span>
                    </li>";
                }
            } else {
                echo "<li>No hay alimentos seleccionados</li>";
            }
            ?>
        </ul>
        <button onclick="showClientForm()">Finalizar Comanda</button>
    </div>

    <!-- Formulario modal para datos del cliente -->
    <div id="modal-container" class="modal-container" style="display: none;" onclick="closeClientForm(event)">
        <div class="client-form">
            <h3>Datos del Cliente</h3>
            <form action="../comandas/finalizar_comanda.php" method="POST">
                <label for="cliente_nombre">Nombre del Cliente:</label>
                <input type="text" id="cliente_nombre" name="cliente_nombre" required>
                <label for="cliente_correo">Correo Electrónico:</label>
                <input type="email" id="cliente_correo" name="cliente_correo" required>
                <input type="hidden" id="tipo_pago" name="tipo_pago">
                <button type="submit">Confirmar Comanda</button>
            </form>
        </div>
    </div>

    <!-- Formulario modal para datos del cliente en pago global -->
    <div id="global-modal-container" class="modal-container" style="display: none;" onclick="closeGlobalClientForm(event)">
        <div class="client-form">
            <h3>Datos del Cliente (Pago Global)</h3>
            <form action="../comandas/finalizar_comanda.php" method="POST">
                <label for="cliente_nombre">Nombre del Cliente:</label>
                <input type="text" id="cliente_nombre" name="cliente_nombre" required>
                <label for="cliente_correo">Correo Electrónico:</label>
                <input type="email" id="cliente_correo" name="cliente_correo" required>
                <button type="submit">Confirmar Pago Global</button>
            </form>
        </div>
    </div>

    <script>
    function showPaymentPopup() {
        document.getElementById('payment-popup1').style.display = 'flex';
    }

    function closePaymentPopup(event) {
        const popup = document.getElementById('payment-popup1');
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    }

    function handlePayment(type) {
        document.getElementById('payment-popup1').style.display = 'none';
        if (type === 'individual') {
            document.getElementById('modal-container').style.display = 'flex';
        } else if (type === 'global') {
            document.getElementById('global-modal-container').style.display = 'flex';
        }
    }

    function closeClientForm(event) {
        const modalContainer = document.getElementById('modal-container');
        if (event.target === modalContainer) {
            modalContainer.style.display = 'none';
        }
    }

    function closeGlobalClientForm(event) {
        const modalContainer = document.getElementById('global-modal-container');
        if (event.target === modalContainer) {
            modalContainer.style.display = 'none';
        }
    }
    </script>
</body>
</html>