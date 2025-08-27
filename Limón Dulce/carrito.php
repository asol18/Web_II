<?php
session_start();
require_once 'conexion.php';

// --- FUNCIONES AUXILIARES ---
function formatPriceCRC($price) {
    return '₡ ' . number_format($price, 2, ',', '.');
}

function parse_price_to_float_updated($s) {
    if (!is_string($s) && !is_numeric($s)) return 0.0;
    $s = trim((string)$s);
    $lastDot = strrpos($s, '.');
    $lastComma = strrpos($s, ',');

    if ($lastDot !== false && $lastComma !== false) {
        if ($lastComma > $lastDot) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } else {
            $s = str_replace(',', '', $s);
        }
    } else {
        if ($lastComma !== false) {
            $parts = explode(',', $s);
            $lastPart = end($parts);
            if (strlen($lastPart) == 3 && count($parts) > 1) {
                $s = str_replace(',', '', $s);
            } else {
                $s = str_replace(',', '.', $s);
            }
        } elseif ($lastDot !== false) {
            $parts = explode('.', $s);
            $lastPart = end($parts);
            if (strlen($lastPart) == 3 && count($parts) > 1) {
                $s = str_replace('.', '', $s);
            }
        }
    }
    return floatval($s);
}

// Inicializar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto]['cantidad']++;
    } else {
        $sql = "SELECT id, nombre, precio, imagen FROM productos WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $producto = $result->fetch_assoc();
            $_SESSION['carrito'][$id_producto] = [
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'imagen' => $producto['imagen'],
                'cantidad' => 1
            ];
        }
    }
    header('Location: carrito.php');
    exit;
}

// Vaciar carrito
if (isset($_GET['action']) && $_GET['action'] == 'vaciar') {
    $_SESSION['carrito'] = [];
    header('Location: carrito.php');
    exit;
}

// --- CÁLCULO DE TOTALES ---
$carrito = $_SESSION['carrito'] ?? [];
$subtotal = 0.0;

foreach ($carrito as $item) {
    $precio = parse_price_to_float_updated($item['precio']);
    $cantidad = max(1, intval($item['cantidad'] ?? 1));
    $subtotal += $precio * $cantidad;
}

$impuesto = $subtotal * 0.13;
$envio = 2000.00;
$total = $subtotal + $impuesto + $envio;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-secondary custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="img/Logo.png" alt="Limón Dulce Logo" width="130px" class="d-inline-block align-text-top" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['usuario_nombre'])): ?>
                    <li class="nav-item">
                        <span class="nav-link">¡Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">
                            <i class="bi bi-person"></i> Perfil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            <i class="bi bi-cart"></i> Carrito
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger me-2" href="cerrarSesion.php">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-success me-2" href="inicioSesion.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">
                            <i class="bi bi-person"></i> Perfil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            <i class="bi bi-cart"></i> Carrito
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container my-5">
    <h2 class="text-center mb-4">Tu Carrito</h2>
    <div class="row">
        <div class="col-lg-8">
            <?php if (empty($carrito)) : ?>
                <div class="alert alert-info text-center">Tu carrito está vacío.</div>
            <?php else : ?>
                <?php foreach ($carrito as $id => $item): 
                    $precio = parse_price_to_float_updated($item['precio']);
                    $cantidad = intval($item['cantidad']);
                    $subtotal_item = $precio * $cantidad;
                ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-3">
                                <img src="img/<?php echo htmlspecialchars($item['imagen']); ?>" class="img-fluid">
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['nombre']); ?></h5>
                                    <form class="d-flex align-items-center actualizar-cantidad-form" data-id="<?php echo $id; ?>">
                                        <input type="number" class="form-control w-25 cantidad-input" value="<?php echo $cantidad; ?>" min="1">
                                        <span class="ms-auto fw-bold subtotal-item" id="subtotal-item-<?php echo $id; ?>"><?php echo formatPriceCRC($subtotal_item); ?></span>
                                    </form>
                                    <button class="btn btn-danger btn-sm eliminar-producto mt-2" data-id="<?php echo $id; ?>"><i class="bi bi-trash"></i> Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Resumen</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">Subtotal: <span id="subtotal"><?php echo formatPriceCRC($subtotal); ?></span></li>
                        <li class="list-group-item d-flex justify-content-between">Envío: <span id="envio"><?php echo formatPriceCRC($envio); ?></span></li>
                        <li class="list-group-item d-flex justify-content-between">Impuestos: <span id="impuesto"><?php echo formatPriceCRC($impuesto); ?></span></li>
                        <li class="list-group-item d-flex justify-content-between fw-bold">Total: <span id="total"><?php echo formatPriceCRC($total); ?></span></li>
                    </ul>
                    <form action="pago.php" method="post" class="d-grid mt-3">
                        <input type="hidden" name="total" id="total-input" value="<?php echo number_format($total, 2, '.', ''); ?>">
                        <button type="submit" class="btn btn-primary btn-lg">Proceder al Pago</button>
                    </form>
                    <a href="index.php" class="btn btn-outline-secondary mt-2 w-100">Seguir Comprando</a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.querySelectorAll('.cantidad-input').forEach(input => {
    input.addEventListener('change', () => {
        const form = input.closest('.actualizar-cantidad-form');
        const id = form.getAttribute('data-id');
        const cantidad = input.value;

        fetch('actualizarCarrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'actualizar',
                id_producto: id,
                cantidad: cantidad
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`subtotal-item-${id}`).textContent = data.subtotal_item;
                document.getElementById('subtotal').textContent = data.subtotal;
                document.getElementById('impuesto').textContent = data.impuesto;
                document.getElementById('total').textContent = data.total;
                document.getElementById('total-input').value = data.total_raw;
            }
        });
    });
});

document.querySelectorAll('.eliminar-producto').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        if (confirm('¿Eliminar este producto del carrito?')) {
            fetch('actualizarCarrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'eliminar',
                    id_producto: id
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
});
</script>
<footer class="custom-footer" style="background-color: #AADD22; color: white;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <img src="img/Logo.png" alt="Limón Dulce Logo" width="130px">
                    <h5>Limón Dulce</h5>
                    <p>Vístete de frescura, brilla con estilo</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="contact-info">
                        <img src="img/deskPhone.svg">
                    </div>
                    <div>
                        <h5>Contacto</h5>
                        <p>
                            Limón Dulce Ciudad Quesada <br>
                            Dirección: Plaza Huetar, Alajuela Ciudad Quesada<br>
                            Teléfono: +506 2461-1522 / 8529-6326<br>
                            Email: info@limondulcecq.com
                        </p>
                        <p>
                            Limón Dulce Florencia<br>
                            Dirección: Centro Comercial Florencia, Alajuela Ciudad Quesada <br>
                            Teléfono: +506 2461-1832 / 6216-8158<br>
                            Email: info@limondulce.com
                        </p>
                    </div>

                    <div>
                        <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-3">
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
