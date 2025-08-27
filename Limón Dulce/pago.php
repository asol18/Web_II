<?php
session_start();
date_default_timezone_set('America/Costa_Rica');

// Verifica que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: inicioSesion.php');
    exit;
}

// Verifica que se haya enviado el total
if (!isset($_POST['total'])) {
    header('Location: carrito.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$total = floatval($_POST['total']);
$fecha_pago = date('Y-m-d H:i:s');

// Guardamos información del pago para usar en confirmarPago.php
$_SESSION['pago'] = [
    'id_usuario' => $id_usuario,
    'total' => $total,
    'fecha_pago' => $fecha_pago
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Limon Dulce | Pago </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .hidden { display: none; }
        body {
            background-color: #222;
            color: white;
            padding-top: 50px;
        }
    </style>
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

<div class="container my-5">
    <h2 class="mb-4 text-center">Resumen del Pago</h2>
    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Fecha:</strong> <?= $fecha_pago ?></li>
        <li class="list-group-item"><strong>Monto Total:</strong> ₡<?= number_format($total, 2, ',', '.') ?></li>
    </ul>

    <h4>Selecciona el método de pago:</h4>
    <form action="procesarPago.php" method="POST" id="payment-form">
        <input type="hidden" name="metodo_pago" id="metodo_pago" value="tarjeta">

        <div class="mb-3 d-flex gap-4">
            <label class="cursor-pointer">
                <input type="radio" name="payment_method_choice" value="tarjeta" checked class="form-check-input">
                <span class="ms-2">Tarjeta</span>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="payment_method_choice" value="sinpe" class="form-check-input">
                <span class="ms-2">SINPE Móvil</span>
            </label>
        </div>

        <!-- TARJETA -->
        <div id="tarjeta-form">
            <div class="mb-3">
                <label class="form-label">Últimos 4 dígitos</label>
                <input type="text" name="ultimos4" class="form-control" pattern="\d{4}" maxlength="4" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre del titular</label>
                <input type="text" name="nombre_titular" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de expiración (MM/AA)</label>
                <input type="text" name="fecha_expiracion" class="form-control" pattern="(0[1-9]|1[0-2])\/\d{2}" required>
            </div>
        </div>

        <!-- SINPE -->
        <div id="sinpe-form" class="hidden">
            <div class="mb-3">
                <label class="form-label">Número de celular SINPE</label>
                <input type="tel" name="celular_sinpe" class="form-control" pattern="\d{8}">
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre del remitente</label>
                <input type="text" name="nombre_remitente" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Referencia de SINPE</label>
                <input type="text" name="referencia_sinpe" class="form-control">
            </div>
        </div>

        <div class="d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-success w-100">Confirmar Pago</button>
            <a href="carrito.php" class="btn btn-secondary w-100">Volver al Carrito</a>
        </div>
    </form>
</div>

<script>
    const metodoPagoRadios = document.querySelectorAll('input[name="payment_method_choice"]');
    const tarjetaForm = document.getElementById('tarjeta-form');
    const sinpeForm = document.getElementById('sinpe-form');
    const metodoPagoHidden = document.getElementById('metodo_pago');

    metodoPagoRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const value = radio.value;
            metodoPagoHidden.value = value;

            if (value === 'tarjeta') {
                tarjetaForm.classList.remove('hidden');
                sinpeForm.classList.add('hidden');
                tarjetaForm.querySelectorAll('input').forEach(i => i.required = true);
                sinpeForm.querySelectorAll('input').forEach(i => i.required = false);
            } else {
                tarjetaForm.classList.add('hidden');
                sinpeForm.classList.remove('hidden');
                tarjetaForm.querySelectorAll('input').forEach(i => i.required = false);
                sinpeForm.querySelectorAll('input').forEach(i => i.required = true);
            }
        });
    });
</script>
<br>
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
