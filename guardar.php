<?php
session_start();
include("BSGENERAL.php");

$nombre = $_POST['nom_cliente'];
$correo = $_POST['ce_cliente'];
$password = $_POST['contraseña'];
$telefono = $_POST['tel_cliente'];
$direccion = $_POST['di_cliente'];

// Usamos prepared statement para mayor seguridad
$stmt = $conexion->prepare("INSERT INTO clientes (nom_cliente, ce_cliente, contraseña, tel_cliente, di_cliente) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nombre, $correo, $password, $telefono, $direccion);

if ($stmt->execute()) {
    $id_insertado = $stmt->insert_id;

    // Iniciar sesión automáticamente después del registro
    $_SESSION['user_id'] = $id_insertado;
    $_SESSION['user_name'] = $nombre;
    $_SESSION['user_email'] = $correo;

    // Redirigir al menú o perfil
    header('Location: menu2.php');
    exit;
} else {
    echo "Error: " . $conexion->error;
}

$stmt->close();
$conexion->close();
?>