<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <link rel="stylesheet" href="Styles.css">
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: 'Poppins',sans-serif;
        }
        body { background-color: #f6f7fb; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            padding: 0 10px;
            position: relative;}
            body::before{
            content:'';
            position:absolute;
            height:100%;
            width:100%;
            background: url('ft/f_head.jpg') no-repeat center center / cover;
            filter: blur(5px);
            z-index:-1;
            }
        .form-container {
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
        h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #333;
        }
        .form-sub {
            font-size: 1rem;
            margin-bottom: 25px;
            color: #666;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }
        .form-actions {
            margin-top: 25px;
        }
        .btn-primary {
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
        .btn-primary:hover {
             background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            box-shadow:0 6px 20px rgba(102, 126, 234, 0.6);
            transform:translateY(-2px);
        }
        .btn-primary:active {
            transform:translateY(0);
        }
        .password-wrap {
            position: relative;
        }
        .toggle-pass {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #007BFF;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        

         
    </style>

</head>

<body>
    <main class="form-container">
        <section class="form-card">
            <h2>Crear cuenta</h2>
            <p class="form-sub">Regístrate para acceder a nuestros servicios</p>

            <form action="guardar.php" method="POST" class="register-form">
                
                <div class="form-group">
                    <label for="nom_cliente">Nombre</label>
                    <input id="nom_cliente" type="text" name="nom_cliente" required placeholder="Tu nombre completo">
                </div>

                <div class="form-group">
                    <label for="ce_cliente">Correo</label>
                    <input id="ce_cliente" type="email" name="ce_cliente" required placeholder="ejemplo@dominio.com">
                </div>

                <div class="form-group">
                    <label for="contraseña">Contraseña</label>
                    <div class="password-wrap">
                        <input id="contraseña" type="password" name="contraseña" required placeholder="Mínimo 6 caracteres">
                        <button type="button" class="toggle-pass" aria-label="Mostrar contraseña">Mostrar</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="di_cliente">Dirección</label> 
                    <input id="di_cliente" type="text" name="di_cliente" required placeholder="Calle, número, ciudad">
                </div>

                <div class="form-group">
                    <label for="tel_cliente">Teléfono</label>
                    <input id="tel_cliente" type="tel" name="tel_cliente" required placeholder="(555) 555-5555" >
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Registrar</button>
                </div>
                     <div class="back-button-container">
        <button class="btn-primary" type="button" onclick="history.back()">
            Volver
        </button>
    </div>
            </form>
              <div class="login-link">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </section>
    </main>

    <script>
        //placeholder="(555)_555-5555" placeholder="Calle,_número,_ciudad" placeholder="Mínimo_6_caracteres" placeholder="ejemplo@dominio.com" placeholder="Tu_nombre_completo"
        // Toggle simple de visibilidad de contraseña
        document.addEventListener('click', function(e){
            if(e.target && e.target.classList.contains('toggle-pass')){
                const wrap = e.target.closest('.password-wrap');
                const input = wrap.querySelector('input[type="password"], input[type="text"]');
                 const btn = e.target;
                
                if(input.type === 'password'){
                    input.type = 'text';
                    btn.textContent = 'Ocultar';
                } else {
                    input.type = 'password';
                    btn.textContent = 'Mostrar';
                }
            }
        });
            const emailInput = document.getElementById('ce_cliente');
        if(emailInput){
            emailInput.addEventListener('blur', function(){
                const email = this.value.trim();
                if(email && !email.includes('@') || !email.includes('.') || email.indexOf('com') === -1){
                    this.setCustomValidity('Por favor ingresa un correo válido');
                } else {
                    this.setCustomValidity('');
                }
            });
        }

        // Validación de teléfono (solo números)
        const telInput = document.getElementById('tel_cliente');
        if(telInput){
            telInput.addEventListener('input', function(){
                this.value = this.value.replace(/[^0-9\-\+\(\)]/g, '');
            });
        }
    </script>
</body>
</html>