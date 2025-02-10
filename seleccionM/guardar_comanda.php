<?php
session_start(); // Iniciar sesión

// Validar los datos enviados desde el formulario
if (isset($_POST['mesero_id']) && isset($_POST['mesa'])) {
    $mesero_id = $_POST['mesero_id'];
    $mesa_id = $_POST['mesa'];

    // Guardar selección en la sesión
    $_SESSION['mesero_id'] = $mesero_id;
    $_SESSION['mesa_id'] = $mesa_id;

    // Redirigir al menú principal
    header('Location: indexSeleccion.php');
    exit();
} else {
    die('Datos incompletos: asegúrate de seleccionar el mesero y la mesa.');
}
?>
