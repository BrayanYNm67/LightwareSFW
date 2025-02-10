<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si no existe la sesión, inicializar
    if (!isset($_SESSION['seleccionados'])) {
        $_SESSION['seleccionados'] = [];
    }

    // Detectar acción
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($accion === 'eliminar') {
        // Eliminar un alimento basado en su índice
        $index = isset($_POST['index']) ? intval($_POST['index']) : -1;

        if ($index >= 0 && $index < count($_SESSION['seleccionados'])) {
            array_splice($_SESSION['seleccionados'], $index, 1); // Elimina el elemento
        }
    } else {
        // Lógica para agregar alimentos (ya implementada)
        if (!empty($_POST['alimentos'])) {
            foreach ($_POST['alimentos'] as $id_alimento) {
                $cantidad = isset($_POST['cantidad'][$id_alimento]) ? intval($_POST['cantidad'][$id_alimento]) : 1;
                $nombre = isset($_POST['nombre'][$id_alimento]) ? $_POST['nombre'][$id_alimento] : 'Alimento desconocido';

                $encontrado = false;
                foreach ($_SESSION['seleccionados'] as &$seleccion) {
                    if ($seleccion['id'] == $id_alimento) {
                        $seleccion['cantidad'] += $cantidad;
                        $encontrado = true;
                        break;
                    }
                }

                if (!$encontrado) {
                    $_SESSION['seleccionados'][] = [
                        'id' => $id_alimento,
                        'nombre' => $nombre,
                        'cantidad' => $cantidad
                    ];
                }
            }
        }
    }

    // Redirigir de vuelta a la página principal
    header('Location: ../../seleccionM/indexSeleccion.php');
    exit;
}
?>
