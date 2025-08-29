<?php
session_start();
$rastreo = $_SESSION['rastreo'] ?? '';
unset($_SESSION['rastreo']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra exitosa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-5 text-center">
        <img src="img/Logo.png" alt="Logo" class="logo" style="width: 120px; height: auto; display: block; margin: 0 auto">
        <h1 class="text-success">✅ ¡Pago registrado con éxito!</h1>
        <p>Gracias por completar tu pago. Tu número de rastreo es: <strong><?php echo htmlspecialchars($rastreo); ?></strong>
        </br>Puedes regresar al inicio para seguir navegando.</p>
        <a href="index.php" class="btn btn-success mt-3">Ir al inicio</a>
    </div>
</body>
</html>
