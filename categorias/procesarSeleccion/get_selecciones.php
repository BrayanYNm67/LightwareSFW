<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['seleccionados'])) {
    echo json_encode($_SESSION['seleccionados']);
} else {
    echo json_encode([]);
}
?>
