<?php
// Conexión a la base de datos
function conectar() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'lightware_db';

    $mysqli = new mysqli($host, $user, $password, $database);

    if ($mysqli->connect_errno) {
        die("Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
    }

    return $mysqli;
}

?>
