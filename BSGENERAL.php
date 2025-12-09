<?php
$conexion = mysqli_connect("localhost", "root", "", "barbeel");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
