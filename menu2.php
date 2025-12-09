<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Procesamiento de acciones del carrito
include_once 'BSGENERAL.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = $_POST['producto_id'] ?? '';
    $cantidad = intval($_POST['cantidad'] ?? 0);

    // Lógica de agregar/eliminar/vaciar carrito
    if ($accion === 'agregar' && $cantidad > 0) {
        $_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + $cantidad;
        $mensaje = "Se agregaron $cantidad unidad(es) al carrito";
    }
    if ($accion === 'eliminar' && isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
        $mensaje = "Producto eliminado del carrito.";
    }
    if ($accion === 'vaciar') {
        $_SESSION['carrito'] = [];
        $mensaje = "Carrito vaciado correctamente.";
    }

    // Lógica de confirmación de compra
    if ($accion === 'confirmar_compra' && !empty($_SESSION['carrito'])) {
        $correo = $_POST['correo'] ?? '';
        $total = 0;
        $productos_ticket = "";
       
        // Buscar cliente por correo
        $sql_cliente = "SELECT id_clientes, nom_cliente FROM clientes WHERE ce_cliente = ? LIMIT 1";
        $stmt_cliente = $conexion->prepare($sql_cliente);
        $stmt_cliente->bind_param("s", $correo);
        $stmt_cliente->execute();
        $stmt_cliente->store_result();
        $stmt_cliente->bind_result($id_cliente, $nom_cliente);
        if ($stmt_cliente->num_rows > 0) {
            $stmt_cliente->fetch();
        } else {
            $nom_cliente = "Cliente no registrado";
            $id_cliente = null;
        }
        $stmt_cliente->close();

        // Calcular total y preparar productos para ticket
        foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
            $precio = obtenerPrecio($producto_id);
            $subtotal = $precio * $cantidad;
            $total += $subtotal;
            $productos_ticket .= "$producto_id x $cantidad = $subtotal\n";
        }

        // Guardar venta en la base de datos
        $fecha_venta = date('Y-m-d');
        if ($id_cliente) {
            $sql_venta = "INSERT INTO ventas (fecha_venta, id_cliente, total_venta) VALUES (?, ?, ?)";
            $stmt_venta = $conexion->prepare($sql_venta);
            $stmt_venta->bind_param("sii", $fecha_venta, $id_cliente, $total);
            $stmt_venta->execute();
            $id_venta = $stmt_venta->insert_id;
            $stmt_venta->close();

            // Guardar productos comprados en resgistro venta
            foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
                // Buscar id_pro
                $sql_pro = "SELECT id_pro FROM productos WHERE nom_pro = ? LIMIT 1";
                $stmt_pro = $conexion->prepare($sql_pro);
                $stmt_pro->bind_param("s", $producto_id);
                $stmt_pro->execute();
                $stmt_pro->store_result();
                $stmt_pro->bind_result($id_pro);
                if ($stmt_pro->num_rows > 0) {
                    $stmt_pro->fetch();
                    $total_producto = obtenerPrecio($producto_id) * $cantidad;
                    $sql_registro = "INSERT INTO `resgistro venta` (id_venta, id_pro, can_producto, total) VALUES (?, ?, ?, ?)";
                    $stmt_registro = $conexion->prepare($sql_registro);
                    $stmt_registro->bind_param("iiii", $id_venta, $id_pro, $cantidad, $total_producto);
                    $stmt_registro->execute();
                    $stmt_registro->close();
                }
                $stmt_pro->close();
            }
        }

        // Enviar ticket al correo
        $asunto = "Ticket de compra BAR-BEER";
        $dompdf = new Dompdf();
 $html ="
<html>
<head>
  <style>
    body { font-family: DejaVu Sans, sans-serif; }
    .ticket { border: 1px solid #000; padding: 20px; width: 400px; }
    h2 { text-align: center; }
    pre { font-size: 14px; }
  </style>
</head>
<body>
  <div class='ticket'>
    <h2>$asunto</h2>
    <p><strong>Cliente:</strong> $nom_cliente</p>
    <pre>$productos_ticket</pre>
    <p><strong>Total:</strong> $$total</p>
    <p><strong>Fecha:</strong> $fecha_venta</p>
    <hr>
    <p style='text-align:center;'>¡Gracias por tu compra!</p>
  </div>
</body>
</html>
";;

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // tamaño y orientación
$dompdf->render();

// Mostrar en navegador listo para imprimir
file_put_contents("ticket.pdf", $dompdf->output());

        // Vaciar carrito y mostrar confirmación
        $_SESSION['carrito'] = [];
        $mensaje = "¡Compra confirmada! Ticket enviado a $correo";
    }
}


// Función para obtener el precio basado en el nombre del producto
function obtenerPrecio($nombre) {
    // Mapas de precios (valores originales)
    $preciosBebidas = [
        'BACARDI' => 150,
        'BLEND TINTO' => 280,
        'Bud light' => 45,
        'CHARDONNAY' => 320,
        'COCA-COLA' => 25,
        'Coors light' => 45,
        'CROWN' => 180,
        'FANTA' => 25,
        'MANZANITA' => 25,
        'MICHELOB ULTRA' => 50,
        'MILLER LIGHT' => 45,
        'MOSCATO' => 290,
        'PEPSI' => 25,
        'PINOT GRIS' => 310,
        'ROYAL RON' => 140,
        'TINTO DULCE' => 270,
        'TITO_S VODKA' => 160
    ];

    // Precios para alimentos
    $preciosAlimentos = [
        'BURRITO' => 85,
        'CHILAQUILES' => 95,
        'ENCHILADAS' => 90,
        'GUACAMOLE' => 65,
        'HAMBURGUESA' => 110,
        'Hot dog' => 70,
        'PAPAS FRITAS' => 55,
        'PICO DE GALLO' => 45,
        'PIZZA' => 180,
        'TACOS' => 75,
        'TLAYUDAS' => 100
    ];

    // Normaliza un nombre para comparar: quita extensiones/guiones bajos, deja mayúsculas y espacios simples
    $normalizar = function($s) {
        $s = preg_replace('/\.(png|jpg|jpeg|gif)$/i', '', $s); // quitar extensión
        $s = str_replace(['_', '-'], ' ', $s);
        $s = preg_replace('/\s+/', ' ', $s); // colapsar espacios
        $s = trim($s);
        $s = mb_strtoupper($s);
        return $s;
    };

    $nombreNorm = $normalizar($nombre);

    // Crear mapas normalizados una vez por llamada
    $mapB = [];
    foreach ($preciosBebidas as $k => $v) {
        $mapB[$normalizar($k)] = $v;
    }
    $mapA = [];
    foreach ($preciosAlimentos as $k => $v) {
        $mapA[$normalizar($k)] = $v;
    }

    if (isset($mapB[$nombreNorm])) return $mapB[$nombreNorm];
    if (isset($mapA[$nombreNorm])) return $mapA[$nombreNorm];

    return 0;
}

// Función para obtener los productos de una carpeta
function obtenerProductos($carpeta) {
    $productos = [];
    $archivos = glob("$carpeta/*.*");
    foreach ($archivos as $archivo) {
        $nombre = basename($archivo);
        $nombreSinExtension = pathinfo($nombre, PATHINFO_FILENAME);
        // Limpiamos el nombre: quitamos guiones bajos y los reemplazamos por espacios
        $nombreLimpio = str_replace('_', ' ', $nombreSinExtension);
        // Eliminamos cualquier guion al final del nombre
        $nombreLimpio = rtrim($nombreLimpio, '_');
        $productos[] = [
            'nombre' => $nombreLimpio,
            'imagen' => $archivo,
            'precio' => obtenerPrecio($nombreSinExtension)
        ];
    }
    return $productos;
}

$bebidas = obtenerProductos('productos');
$alimentos = obtenerProductos('alimentos');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Bar-Beer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Estilos para el carrito */
        .carrito-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .cantidad-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 5px;
        }
        
        .carrito-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .carrito-table th,
        .carrito-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .btn-agregar {
            background-color: #ff4501;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }
        
        .btn-eliminar {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .mensaje {
            background-color: #f8f9fa;
            border-left: 4px solid #ff4501;
            padding: 10px;
            margin: 10px 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .menu-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            text-align: center;
            padding: 40px 0;
            background: #2c3e50;
            color: white;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-family: 'Pirata One', cursive;
        }

        .section-title {
            font-size: 2em;
            color: #2c3e50;
            text-align: center;
            margin: 30px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid #ff4501;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            padding: 20px;
        }

        .menu-item {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-bottom: 2px solid #ff4501;
        }

        .item-details {
            padding: 15px;
            text-align: center;
        }

        .item-name {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .item-price {
            font-size: 1.3em;
            color: #ff4501;
            font-weight: bold;
        }

        .back-button {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 10px 20px;
            background-color: #ff4501;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #e63f00;
        }

        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
                padding: 10px;
            }

            .menu-item img {
                height: 150px;
            }

            .section-title {
                font-size: 1.5em;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para manejar todos los formularios de agregar al carrito
            document.querySelectorAll('.agregar-carrito-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevenir el envío tradicional del formulario
                    
                    // Crear objeto FormData con los datos del formulario
                    const formData = new FormData(form);
                    
                    // Enviar la petición AJAX
                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(() => {
                        // Recargar solo la sección del carrito
                        fetch(window.location.href)
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                
                                // Actualizar la sección del carrito
                                const carritoActual = document.querySelector('.carrito-section');
                                const carritoNuevo = doc.querySelector('.carrito-section');
                                if (carritoActual && carritoNuevo) {
                                    carritoActual.innerHTML = carritoNuevo.innerHTML;
                                }
                                
                                // Mostrar mensaje de éxito
                                const mensaje = document.createElement('div');
                                mensaje.className = 'mensaje';
                                mensaje.textContent = 'Producto agregado al carrito';
                                carritoActual.insertBefore(mensaje, carritoActual.firstChild);
                                
                                // Desvanecer el mensaje después de 3 segundos
                                setTimeout(() => {
                                    mensaje.style.transition = 'opacity 0.5s';
                                    mensaje.style.opacity = '0';
                                    setTimeout(() => mensaje.remove(), 500);
                                }, 3000);
                            });
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
            
            // Manejar los botones de eliminar y vaciar carrito
            document.addEventListener('click', function(e) {
                if (e.target.matches('.btn-eliminar') || e.target.matches('.vaciar-carrito')) {
                    e.preventDefault();
                    const form = e.target.closest('form');
                    
                    fetch(window.location.href, {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(response => response.text())
                    .then(() => {
                        // Actualizar solo la sección del carrito
                        fetch(window.location.href)
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const carritoActual = document.querySelector('.carrito-section');
                                const carritoNuevo = doc.querySelector('.carrito-section');
                                if (carritoActual && carritoNuevo) {
                                    carritoActual.innerHTML = carritoNuevo.innerHTML;
                                }
                            });
                    });
                }
            });
        });
    </script>
</head>
<body>
<script>
  // Guardar scroll antes de salir
  window.onbeforeunload = function() {
    localStorage.setItem("scrollPos", window.scrollY);
  };

  // Restaurar scroll al cargar
  window.onload = function() {
    var scrollPos = localStorage.getItem("scrollPos");
    if (scrollPos) window.scrollTo(0, scrollPos);
  };
</script>
    <header style="display:flex; align-items:center; justify-content:space-between; padding: 40px 20px;">
        <div>
            <h1 style="margin:0">BAR-BEER MENÚ</h1>
            <p style="margin:4px 0 0">Disfruta de nuestra selección de bebidas y alimentos</p>
        </div>
        <div style="text-align:right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="color:#fff;">Hola, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></div>
                <div style="margin-top:6px;"><a href="profile.php" style="color:#fff; text-decoration:underline; margin-right:10px;">Perfil</a><a href="logout.php" style="color:#fff; text-decoration:underline;">Cerrar sesión</a></div>
            <?php else: ?>
                <div><a href="login.php" style="color:#fff; text-decoration:underline; margin-right:10px;">Iniciar sesión</a><a href="form_usuario.php" style="color:#fff; text-decoration:underline;">Registro</a></div>
            <?php endif; ?>
        </div>
    </header>

    <div class="menu-container">
        <!-- Sección de Bebidas -->
        <h2 class="section-title">Bebidas</h2>
        <div class="menu-grid">
            <?php foreach ($bebidas as $bebida): ?>
            <div class="menu-item">
                <img src="<?php echo htmlspecialchars($bebida['imagen']); ?>" alt="<?php echo htmlspecialchars($bebida['nombre']); ?>">
                <div class="item-details">
                    <div class="item-name"><?php echo htmlspecialchars($bebida['nombre']); ?></div>
                    <div class="item-price">$<?php echo number_format($bebida['precio'], 2); ?></div>
                    <form method="post" class="agregar-carrito-form">
                        <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($bebida['nombre']); ?>">
                        <input type="hidden" name="accion" value="agregar">
                        <input type="number" name="cantidad" value="1" min="1" class="cantidad-input">
                        <button type="submit" class="btn-agregar">Agregar al Carrito</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Sección de Alimentos -->
        <h2 class="section-title">Alimentos</h2>
        <div class="menu-grid">
            <?php foreach ($alimentos as $alimento): ?>
            <div class="menu-item">
                <img src="<?php echo htmlspecialchars($alimento['imagen']); ?>" alt="<?php echo htmlspecialchars($alimento['nombre']); ?>">
                <div class="item-details">
                    <div class="item-name"><?php echo htmlspecialchars($alimento['nombre']); ?></div>
                    <div class="item-price">$<?php echo number_format($alimento['precio'], 2); ?></div>
                    <form method="post" class="agregar-carrito-form">
                        <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($alimento['nombre']); ?>">
                        <input type="hidden" name="accion" value="agregar">
                        <input type="number" name="cantidad" value="1" min="1" class="cantidad-input">
                        <button type="submit" class="btn-agregar">Agregar al Carrito</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Sección del Carrito -->
        <div class="carrito-section">
            <h2 class="section-title">Carrito de Compras</h2>
            <?php if (isset($mensaje)): ?>
                <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <?php if (empty($_SESSION['carrito'])): ?>
                <p>El carrito está vacío.</p>
            <?php else: ?>
                <table class="carrito-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['carrito'] as $producto_id => $cantidad):
                            $precio = obtenerPrecio($producto_id);
                            $subtotal = $precio * $cantidad;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto_id); ?></td>
                            <td><?php echo $cantidad; ?></td>
                            <td>$<?php echo number_format($precio, 2); ?></td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto_id); ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <button type="submit" class="btn-eliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total:</th>
                            <td colspan="2">$<?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <form method="post" style="margin-top: 15px;">
                    <input type="hidden" name="accion" value="vaciar">
                    <button type="submit" class="btn-eliminar">Vaciar Carrito</button>
                </form>

                <!-- Formulario de confirmación de compra -->
                <form method="post" style="margin-top: 25px; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h3>Confirmar compra</h3>
                    <label for="correo">Correo electrónico para el ticket:</label>
                    <input type="email" name="correo" id="correo" required style="margin: 10px 0; padding: 5px; width: 250px;">
                    <input type="hidden" name="accion" value="confirmar_compra">
                    <button type="submit" style="background-color: #27ae60; color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer;">Confirmar y pagar</button>
                </form>


            <?php endif; ?>
            <a href="ticket.pdf" target="_blank">Ver Ticket en PDF</a>
        </div>

        <a href="index.html" class="back-button">Regresar</a>
    </div>
    <footer>
        <p>© 2025 Unidad de Informática BAR-BEER — Proyecto académico desarrollado bajo la visión del Lic. José Roberto Méndez.</p>
    </footer>
</body>
</html>

