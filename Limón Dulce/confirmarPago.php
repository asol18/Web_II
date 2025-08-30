<?php
session_start();
$rastreo = $_SESSION['rastreo'] ?? '';
unset($_SESSION['rastreo']);

require 'conexion.php';

$detalles = [];
$total = 0;
$impuesto = 0;
$envio = 0;
$hayDatos = false;

if ($rastreo !== '') {
    // Fetch product details and cart totals via INNER JOIN
    $stmt = $mysqli->prepare("
        SELECT 
            p.nombre AS producto,
            dc.cantidad,
            dc.subtotal,
            c.impuesto,
            c.envio,
            c.total
        FROM carrito c
        INNER JOIN detalle_carrito dc ON dc.carrito_id = c.id
        INNER JOIN productos p ON p.id = dc.producto_id
        WHERE c.rastreo = ?
    ");
    $stmt->bind_param("s", $rastreo);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $detalles[] = $row;
    }

    $hayDatos = count($detalles) > 0;

    if ($hayDatos) {
        // Use values from the first row (same for the whole carrito)
        $impuesto = $detalles[0]['impuesto'];
        $envio = $detalles[0]['envio'];
        $total = $detalles[0]['total'];
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra exitosa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-5 text-center" style="max-width: 600px;">
        <img src="img/Logo.png" alt="Logo" class="logo" style="width: 120px; height: auto; display: block; margin: 0 auto">
        <h1 class="text-success">✅ ¡Pago registrado con éxito!</h1>
        <p>Gracias por completar tu pago. Tu número de rastreo es: <strong><?php echo htmlspecialchars($rastreo); ?></strong><br>
        Puedes regresar al inicio para seguir navegando.</p>

        <?php if ($hayDatos): ?>
            <h4 class="mt-4">Resumen de tu carrito:</h4>
            <table class="table table-bordered mt-3 text-start">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['producto']); ?></td>
                            <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                            <td>₡<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Impuesto:</td>
                        <td>₡<?php echo number_format($impuesto, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Envío:</td>
                        <td>₡<?php echo number_format($envio, 2); ?></td>
                    </tr>
                    <tr class="table-success">
                        <td colspan="2" class="text-end fw-bold">Total:</td>
                        <td>₡<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning mt-4">No se encontró información de la compra.</div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-success mt-3">Ir al inicio</a>
    </div>
</body>
</html>
