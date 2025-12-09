<?php
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

// Aquí defines el contenido del ticket en HTML
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
$dompdf->stream("ticket.pdf", ["Attachment" => false]);
?>