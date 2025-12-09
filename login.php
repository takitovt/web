<?php
session_start();
include 'BSGENERAL.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT id_clientes, nom_cliente, contraseña FROM clientes WHERE ce_cliente = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $pass_bd);
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        // comparar contraseña (proyecto actual tiene contraseñas en texto plano)
        if ($password === $pass_bd) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header('Location: menu2.php');
            exit;
        } else {
            $error = 'Contraseña incorrecta.';
        }
    } else {
        $error = 'No existe ninguna cuenta con ese correo.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Iniciar sesión</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: 'Poppins',sans-serif;
            }
        body{
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            width: 100%;
            padding: 0 10px;
            position:relative;
        }
        body::before{
            content:'';
            position:absolute;
            height:100%;
            width:100%;
            background: url('ft/f_head.jpg') no-repeat center center / cover;
            filter: blur(5px);
            z-index:-1;
        }
        .wrapper{
            background:rgba(255,255,255,0.95);
            padding:40px;
            border-radius:12px;
            box-shadow:0 8px 32px rgba(0,0,0,0.2), 0 2px 8px rgba(0,0,0,0.15);
            width:100%;
            max-width:400px;
            text-align:center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(9px);
            -webkit-backdrop-filter: blur(9px);
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        h2{
            font-size:1.8rem;
            margin-bottom:30px;
            color:#333;
            font-weight:700;
        }
        .error-message{
            background-color:#fee;
            border-left:4px solid #d32f2f;
            color:#d32f2f;
            padding:12px 15px;
            border-radius:4px;
            margin-bottom:20px;
            font-size:0.9rem;
            animation: shake 0.3s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .input-field{
            position:relative;
            margin:20px 0;
            border-bottom:2px solid #ddd;
            transition:all 0.3s ease;
        }
        .input-field:focus-within{
            border-bottom-color:#667eea;
        }
        .input-field label{
            position:absolute;
            top:50%;
            left:0;
            transform:translateY(-50%);
            color:#999;
            font-size:16px;
            pointer-events:none;
            transition:0.3s ease;
        }
        .input-field input{
            width:100%;
            height:40px;
            background:transparent;
            border:none;
            font-size:16px;
            color:#333;
            outline:none;
        }
        .input-field input::placeholder{
            color: transparent;
        }
        .input-field input:focus + label,
        .input-field input:valid + label{
            top:-5px;
            font-size:0.8rem;
            color:#667eea;
            font-weight:500;
            transform:translateY(-120%);
        }
        .input-field input:focus{
            color:#333;
        }
        button{
            width:100%;
            padding:12px 20px;
            border:none;
            background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color:#fff;
            font-size:1rem;
            font-weight:600;
            border-radius:6px;
            cursor:pointer;
            border: 2px solid transparent;
            transition:all 0.3s ease;
            margin-top:10px;
            box-shadow:0 4px 15px rgba(102, 126, 234, 0.4);
        }
        button:hover{
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            box-shadow:0 6px 20px rgba(102, 126, 234, 0.6);
            transform:translateY(-2px);
        }
        button:active{
            transform:translateY(0);
        }
        .register{
            margin-top:25px;
            padding-top:20px;
            border-top:1px solid #eee;
        }
        .register p{
            color:#666; 
            font-size:0.95rem;
        }
        .register a{
            color:#667eea;
            text-decoration:none;
            font-weight:600;
            transition:color 0.3s ease;
        }
        .register a:hover{
            color:#764ba2;
            text-decoration:underline;
        }
        @media (max-width: 480px) {
            .wrapper {
                padding: 30px 20px;
            }
            h2 {
                font-size: 1.5rem;
            }
            .input-field {
                margin: 18px 0;
            }
        }

    </style>
</head>
<body>

    <div class="wrapper">
    <form method="post" action="login.php">
         <h2>Iniciar sesión</h2>
         <?php if ($error): ?>
         <div class="error-message">
             <strong>Error:</strong> <?=htmlspecialchars($error)?>
         </div>
         <?php endif; ?>
         <div class="input-field">
             <input type="email" name="email" required>
             <label>Correo</label>
         </div>
         <div class="input-field">
             <input type="password" name="password" required>
             <label>Contraseña</label>
         </div>
         <button type="submit">Entrar</button>
             <div class="back-button-container">
        <button class="back-button" type="button" onclick="history.back()">
            Volver
        </button>
    </div>
         <div class="register">
             <p>¿No tienes cuenta? <a href="form_usuario.php">Regístrate aquí</a></p>
         </div>
    </form>
    </div>
</body>
</html>
