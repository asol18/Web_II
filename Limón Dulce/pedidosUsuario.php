<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicioSesion.php");
    exit;
}

require_once 'conexion.php';

$userId = $_SESSION['usuario_id'];

$sql = "
SELECT
    p.id_pago,
    p.fecha_pago,
    p.monto,
    p.metodo_pago,
    c.producto_id,
    c.precio,
    c.cantidad,
    c.subtotal,
    prod.nombre,
    prod.imagen
FROM pago p
INNER JOIN carrito c ON p.id_pago = c.pago_id
INNER JOIN productos prod ON c.producto_id = prod.id
WHERE p.id_usuario = ?
ORDER BY p.fecha_pago DESC, p.id_pago, c.producto_id
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$pedidos = [];

while ($row = $result->fetch_assoc()) {
    $idPago = $row['id_pago'];
    if (!isset($pedidos[$idPago])) {
        $pedidos[$idPago] = [
            'fecha_pago' => $row['fecha_pago'],
            'monto' => $row['monto'],
            'metodo_pago' => $row['metodo_pago'],
            'productos' => []
        ];
    }
    $pedidos[$idPago]['productos'][] = [
        'producto_id' => $row['producto_id'],
        'precio' => $row['precio'],
        'cantidad' => $row['cantidad'],
        'subtotal' => $row['subtotal'],
        'nombre' => $row['nombre'],
        'imagen' => $row['imagen']
    ];
}

$stmt->close();
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Limón Dulce | Mis pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles.css" />
    <style>
        .pedido-card {
            margin-bottom: 2rem;
            border: 1px solid #ddd;
            border-radius: 0.4rem;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .pedido-header {
            background-color: #AADD22;
            color: white;
            padding: 1rem;
            border-radius: 0.4rem 0.4rem 0 0;
        }
        .producto-item {
            border-bottom: 1px solid #ddd;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .producto-item:last-child {
            border-bottom: none;
        }
        .producto-imagen {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .producto-detalle {
            flex-grow: 1;
        }
        .producto-cantidad,
        .producto-precio,
        .producto-subtotal {
            min-width: 80px;
            text-align: center;
        }
        .pedido-footer {
            padding: 1rem;
            font-weight: 600;
            text-align: right;
            background-color: #f7f7f7;
            border-radius: 0 0 0.4rem 0.4rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="img/Logo.png" alt="Limón Dulce Logo" width="100" class="d-inline-block align-text-top" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="perfil.php"><i class="bi bi-person"></i> Perfil</a></li>
                <li class="nav-item"><a class="nav-link" href="carrito.php"><i class="bi bi-cart"></i> Carrito</a></li>
                <li class="nav-item"><a class="nav-link" href="cerrarSesion.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4">Mis Pedidos</h2>

    <?php if (empty($pedidos)): ?>
        <p class="text-center">No tienes pedidos registrados.</p>
    <?php else: ?>
        <?php foreach ($pedidos as $idPago => $pedido): ?>
            <div class="pedido-card">
                <div class="pedido-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Pedido #<?php echo htmlspecialchars($idPago); ?></strong><br />
                        <small>Fecha: <?php echo htmlspecialchars($pedido['fecha_pago']); ?></small><br />
                        <small>Método de pago: <?php echo htmlspecialchars($pedido['metodo_pago']); ?></small>
                    </div>
                    <div style="font-size: 1.2rem;">
                        Total: $<?php echo number_format($pedido['monto'], 2); ?>
                    </div>
                </div>
                <div>
                    <?php foreach ($pedido['productos'] as $prod): ?>
                        <div class="producto-item">
                            <img class="producto-imagen" src="img/<?php echo htmlspecialchars($prod['imagen']); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>" />
                            <div class="producto-detalle">
                                <strong><?php echo htmlspecialchars($prod['nombre']); ?></strong><br />
                                <small>ID Producto: <?php echo htmlspecialchars($prod['producto_id']); ?></small>
                            </div>
                            <div class="producto-cantidad">
                                <strong>Cantidad</strong><br /><?php echo htmlspecialchars($prod['cantidad']); ?>
                            </div>
                            <div class="producto-precio">
                                <strong>Precio</strong><br />$<?php echo number_format($prod['precio'], 2); ?>
                            </div>
                            <div class="producto-subtotal">
                                <strong>Subtotal</strong><br />$<?php echo number_format($prod['subtotal'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer class="custom-footer" style="background-color: #AADD22; color: white;">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <img src="img/Logo.png" alt="Limón Dulce Logo" width="130px" />
                <h5>Limón Dulce</h5>
                <p>Vístete de frescura, brilla con estilo</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="contact-info">
                    <img src="img/deskPhone.svg" />
                </div>
                <div>
                    <h5>Contacto</h5>
                    <p>
                        Limón Dulce Ciudad Quesada <br />
                        Dirección: Plaza Huetar, Alajuela Ciudad Quesada<br />
                        Teléfono: +506 2461-1522 / 8529-6326<br />
                        Email: info@limondulcecq.com
                    </p>
                    <p>
                        Limón Dulce Florencia<br />
                        Dirección: Centro Comercial Florencia, Alajuela Ciudad Quesada <br />
                        Teléfono: +506 2461-1832 / 6216-8158<br />
                        Email: info@limondulce.com
                    </p>
                </div>

                <div>
                    <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-3" />
        <div class="row">
            <div class="col-12 text-center">
                <p>&copy; 2025 Limón Dulce. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
