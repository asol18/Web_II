<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION["usuario_nombre"];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limón Dulce | Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-secondary custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="img/Logo.png" alt="Limón Dulce Logo" width="130px" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
        <h2 class="text-center mb-4 section-title">Explora Nuestras Categorías de Ropa</h2>
        <p class="lead text-center mb-5">Encuentra el estilo perfecto para cada ocasión.</p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Blusas.png" class="card-img-top" alt="Blusas">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Diseños ligeros y modernos que realzan tu estilo día a día.</p>
                        <a href="catalogo.php" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Abrigos.png" class="card-img-top" alt="Abrigos">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Protección y estilo en una sola prenda para días fríos.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Conjuntos.png" class="card-img-top" alt="Conjuntos">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Combinaciones listas para lucir impecable sin esfuerzo.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Enaguas.png" class="card-img-top" alt="Enaguas">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Faldas versátiles que combinan frescura y feminidad para cualquier ocasión.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Enterizos.png" class="card-img-top" alt="Enterizos">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Piezas prácticas que unen comodidad y tendencia.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Pantalones.png" class="card-img-top" alt="Pantalones">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Desde clásicos hasta modernos, el aliado perfecto para cualquier look.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Shorts.png" class="card-img-top" alt="Shorts">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Cómodos y frescos, ideales para días soleados y llenos de energía.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Vestidos.png" class="card-img-top" alt="Vestidos">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Prendas únicas que destacan tu elegancia y frescura en cada momento.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Zapatos.png" class="card-img-top" alt="Zapatos">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">Calzado que completa tu outfit con estilo y comodidad.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm category-card">
                    <img src="img/Chalecos.png" class="card-img-top" alt="Chalecos">
                    <div class="card-body text-center d-flex flex-column">
                        <p class="card-text">La capa extra que aporta personalidad y versatilidad a tu look.</p>
                        <a href="#" class="btn btn-primary mt-auto">Ver Más</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="container-fluid info-section">
        <div class="row text-start">
            <div class="col-md-3">
                <div class="info-box">
                    <img src="img/WhatsApp.svg">
                    <div>
                        <h5>Escríbenos</h5>
                        <p>
                            Limón Dulce Boutique <br>
                            Limón Dulce Ciudad Quesada: <span class="highlight">8526-6326</span><br>
                            Limón Dulce Florencia: <span class="highlight">6216-8158</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <img src="img/tarjeta.svg">
                    <div>
                        <h5>¡Pagos Aceptados!</h5>
                        <p>
                            Sinpe móvil al 6216-8158 / 8529-6326, a nombre de Ana María Arias y Allyson Sequeira.<br>
                            Aceptamos Mastercard, Visa y CREDIX.<br>
                            Tasa 0% interés con Credomatic a 3 meses.<br>
                            Aparta a 45 días con el 30% del total.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <img src="img/carrito.svg">
                    <div>
                        <h5>Comprá con total tranquilidad</h5>
                        <p>
                            Plataforma de pago en línea brindada por el Banco Nacional.
                            Protección de datos y respaldo de compra.
                            Altos estándares de seguridad para comercios virtuales.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <img src="img/envío.svg">
                    <div>
                        <h5>¡Envíos seguros a todo el país!</h5>
                        <p>
                            Envíos por encomienda en la zona de San Carlos y Correos de Costa Rica para el resto del país.<br>
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