<?php
session_start();
include 'BSGENERAL.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT id_clientes, nom_cliente, ce_cliente, tel_cliente, di_cliente FROM clientes WHERE id_clientes = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $nombre, $correo, $telefono, $direccion);
if ($stmt->num_rows > 0) {
    $stmt->fetch();
} else {
    // Si por alguna razón no existe, cerrar sesión
    session_destroy();
    header('Location: login.php');
    exit;
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mi perfil</title>
    <style>
        .profile { max-width:600px; margin: 20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,.1);} 
        .profile h2 { margin-bottom: 10px; }
        .profile p { margin: 6px 0; }
    </style>
</head>
<body>
    <div class="profile">
        <h2>Mi perfil</h2>
        <p><strong>Nombre:</strong> <?=htmlspecialchars($nombre)?></p>
        <p><strong>Correo:</strong> <?=htmlspecialchars($correo)?></p>
        <p><strong>Teléfono:</strong> <?=htmlspecialchars($telefono)?></p>
        <p><strong>Dirección:</strong> <?=htmlspecialchars($direccion)?></p>

        <p><a href="menu2.php">Volver al menú</a> | <a href="logout.php">Cerrar sesión</a></p>
    </div>
</body>
</html>
