<?php
$host = "localhost";
$usuario = "root";
$contraseña = ""; // o tu contraseña de MySQL
$bd = "limon_dulce";

$conexion = new mysqli($host, $usuario, $contraseña, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
