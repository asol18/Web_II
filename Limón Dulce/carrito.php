<?php
session_start();

// Función para formatear el precio en Colón Costarricense (CRC)
// Esto asume que el valor de $price ya es un float o un número
function formatPriceCRC($price) {
    // number_format(número, decimales, separador_decimal, separador_miles)
    return '₡ ' . number_format($price, 2, ',', '.'); // Formato tico: coma para decimales, punto para miles
}

// Aunque tu función parse_price_to_float es buena,
// si los precios vienen directamente de la DB o ya se procesaron a float al agregar al carrito,
// es posible que no la necesites para CADA iteración aquí.
// Pero la dejaremos si hay alguna posibilidad de que el precio en la sesión no sea un float puro.
function parse_price_to_float_updated($s)
{
    if (!is_string($s) && !is_numeric($s)) return 0.0;
    $s = trim((string)$s);
    $lastDot = strrpos($s, '.');
    $lastComma = strrpos($s, ',');

    if ($lastDot !== false && $lastComma !== false) {
        if ($lastComma > $lastDot) {
            $s = str_replace('.', '', $s); // Remove thousands separator
            $s = str_replace(',', '.', $s); // Replace decimal comma with dot
        } else {
            $s = str_replace(',', '', $s); // Remove thousands comma
        }
    } else {
        if ($lastComma !== false) {
            // Check if it's a thousands separator or a decimal comma
            $parts = explode(',', $s);
            $lastPart = end($parts);
            if (strlen($lastPart) == 3 && count($parts) > 1) {
                // If the last part has 3 digits and there are multiple parts, assume comma is thousands separator
                $s = str_replace(',', '', $s);
            } else {
                // Otherwise, assume comma is decimal separator
                $s = str_replace(',', '.', $s);
            }
        } elseif ($lastDot !== false) {
            // Check if it's a thousands separator or a decimal dot
            $parts = explode('.', $s);
            $lastPart = end($parts);
            if (strlen($lastPart) == 3 && count($parts) > 1) {
                // If the last part has 3 digits and there are multiple parts, assume dot is thousands separator
                $s = str_replace('.', '', $s);
            }
        }
    }
    return floatval($s);
}


$carrito = $_SESSION['carrito'] ?? [];
$subtotal = 0.0;

// Recalcular el subtotal basado en los ítems del carrito
foreach ($carrito as $id => $item) {
    // Asegúrate de que $item['precio'] es un número antes de usarlo.
    // Usaremos tu función parse_price_to_float_updated por seguridad,
    // pero idealmente $item['precio'] ya debería ser un float.
    $precio = parse_price_to_float_updated($item['precio']);
    $cantidad = isset($item['cantidad']) ? intval($item['cantidad']) : 1;

    // Asegúrate de que la cantidad no sea menor que 1
    if ($cantidad < 1) {
        $cantidad = 1;
        // Opcional: podrías querer actualizar la sesión aquí si la cantidad se modificó
        $_SESSION['carrito'][$id]['cantidad'] = 1;
    }

    $subtotal += $precio * $cantidad;
}

// Impuesto del 13% para Costa Rica (IVA)
$impuesto = $subtotal * 0.13;
// Costo de envío (ejemplo, puede ser dinámico en el futuro)
$envio = 2000.00; // Costo de envío en CRC
$total = $subtotal + $impuesto + $envio;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limón Dulce | Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="img/Logo.png" alt="Limón Dulce Logo" width="100" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
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
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <h2 class="text-center mb-4 section-title">Tu Carrito de Compras</h2>

        <div class="row">
            <div class="col-lg-8">
                <?php if (empty($carrito)) : ?>
                    <div class="alert alert-info text-center" role="alert">
                        Tu carrito está vacío. ¡Empieza a agregar productos!
                    </div>
                <?php else : ?>
                    <?php foreach ($carrito as $id => $item) :
                        $precio_unitario = parse_price_to_float_updated($item['precio']);
                        $cantidad_item = isset($item['cantidad']) ? intval($item['cantidad']) : 1;
                        $subtotal_item = $precio_unitario * $cantidad_item;
                    ?>
                        <div class="card mb-3 shadow-sm cart-item-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-md-3">
                                    <img src="<?php echo htmlspecialchars($item['imagen_url'] ?? 'placeholder.jpg'); ?>" class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($item['nombre'] ?? 'Producto'); ?>">
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['nombre'] ?? 'Producto Desconocido'); ?></h5>
                                        <p class="card-text text-muted">
                                            <?php echo htmlspecialchars($item['descripcion'] ?? ''); ?>
                                            <?php echo !empty($item['talla']) ? 'Talla: ' . htmlspecialchars($item['talla']) : ''; ?>
                                            <?php echo !empty($item['color']) ? 'Color: ' . htmlspecialchars($item['color']) : ''; ?>
                                        </p>
                                        <div class="d-flex align-items-center mb-2">
                                            <label for="qty-<?php echo $id; ?>" class="form-label mb-0 me-2">Cantidad:</label>
                                            <form action="actualizar_carrito.php" method="post" class="d-flex align-items-center">
                                                <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
                                                <input type="number" name="cantidad" id="qty-<?php echo $id; ?>" class="form-control w-25" value="<?php echo $cantidad_item; ?>" min="1" onchange="this.form.submit()">
                                                <span class="ms-auto fw-bold"><?php echo formatPriceCRC($subtotal_item); ?></span>
                                            </form>
                                        </div>
                                        <form action="eliminar_del_carrito.php" method="post">
                                            <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white custom-card-header">
                        Resumen del Pedido
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Subtotal:
                                <span><?php echo formatPriceCRC($subtotal); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Envío:
                                <span><?php echo formatPriceCRC($envio); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Impuestos (13% IVA):
                                <span><?php echo formatPriceCRC($impuesto); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                Total:
                                <span><?php echo formatPriceCRC($total); ?></span>
                            </li>
                        </ul>
                        <div class="d-grid mt-4">
                            <?php if (!empty($carrito)) : ?>
                                <a href="checkout.php" class="btn btn-primary btn-lg">Proceder al Pago <i class="bi bi-arrow-right-circle"></i></a>
                            <?php else : ?>
                                <button class="btn btn-primary btn-lg" disabled>Proceder al Pago</button>
                            <?php endif; ?>
                        </div>
                        <div class="d-grid mt-2">
                            <a href="index.php" class="btn btn-outline-secondary">Continuar Comprando</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

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