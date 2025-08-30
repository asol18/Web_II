<?php

session_start();
require_once 'conexion.php';
require_once 'productosIndex.php'; //Productos categorizados para el view del index


$categoriaSeleccionada = strtolower(trim($_GET['categoria'] ?? ''));
$busqueda = strtolower(trim($_GET['busqueda'] ?? ''));


$productosFiltrados = array_filter($productos, function ($producto) use ($categoriaSeleccionada, $busqueda) {
    $coincideCategoria = !$categoriaSeleccionada || strtolower($producto['categoria']) === $categoriaSeleccionada;
    $coincideBusqueda = !$busqueda || stripos($producto['nombre'], $busqueda) !== false || stripos($producto['descripcion'], $busqueda) !== false;
    return $coincideCategoria && $coincideBusqueda;
});
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Limón Dulce | Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles.css" />
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
        <h2 class="text-center mb-4 section-title">Explora Nuestras Categorías de Ropa</h2>
        <p class="lead text-center mb-5">Encuentra el estilo perfecto para cada ocasión.</p>

        <div class="row mb-4">
            <div class="col-md-12">
<div class="row mb-4">
    <div class="col-md-12 text-end">
        <a href="filtrosProductos.php" class="btn btn-outline-primary">
            <i class="bi bi-funnel-fill"></i> Filtrar Productos
        </a>
    </div>
</div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if (count($productosFiltrados) > 0): ?>
                <?php foreach ($productosFiltrados as $producto): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm category-card">
                            <img src="<?= htmlspecialchars($producto['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']) ?>" />
                            <div class="card-body text-center d-flex flex-column">
                                <p class="card-text"><?= htmlspecialchars($producto['descripcion']) ?></p>
                                <a href="catalogo.php?categoria=<?= urlencode($producto['categoria']) ?>" class="btn btn-primary mt-auto">Ver Más</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No se encontraron productos para esta categoría o búsqueda.</p>
            <?php endif; ?>
        </div>
    </main>

    <div class="container-fluid info-section">
        <div class="row text-start">
            <div class="col-md-3">
                <div class="info-box">
                    <img src="img/WhatsApp.svg" alt="WhatsApp" />
                    <div>
                        <h5>Escríbenos</h5>
                        <p>
                            Limón Dulce Boutique <br />
                            Limón Dulce Ciudad Quesada: <span class="highlight">8526-6326</span><br />
                            Limón Dulce Florencia: <span class="highlight">6216-8158</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <img src="img/tarjeta.svg" alt="Pagos Aceptados" />
                    <div>
                        <h5>¡Pagos Aceptados!</h5>
                        <p>
                            Sinpe móvil al 6216-8158 / 8529-6326, a nombre de Ana María Arias y Allyson Sequeira.<br />
                            Aceptamos Mastercard, Visa y CREDIX.<br />
                            Tasa 0% interés con Credomatic a 3 meses.<br />
                            Aparta a 45 días con el 30% del total.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <img src="img/carrito.svg" alt="Compra Segura" />
                    <div>
                        <h5>Comprá con total tranquilidad</h5>
                        <p>
                            Plataforma de pago en línea brindada por el Banco Nacional.<br />
                            Protección de datos y respaldo de compra.<br />
                            Altos estándares de seguridad para comercios virtuales.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <img src="img/envío.svg" alt="Envíos Seguros" />
                    <div>
                        <h5>¡Envíos seguros a todo el país!</h5>
                        <p>
                            Envíos por encomienda en la zona de San Carlos y Correos de Costa Rica para el resto del país.<br />
                            Costo de envío: ₡2,500 Correos de Costa Rica, ₡1,500 Encomienda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
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
                        <img src="img/deskPhone.svg" alt="Teléfono" />
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