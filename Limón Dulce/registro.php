<?php
session_start();
require_once 'conexion.php'; 

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Obtener y sanear los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $contraseña = $_POST['contraseña'] ?? '';

    // 2. Validar que los campos no estén vacíos
    if (empty($nombre) || empty($correo) || empty($telefono) || empty($direccion) || empty($contraseña)) {
        $error_message = "Por favor, completa todos los campos.";
    } elseif (strlen($contraseña) < 6) {
        $error_message = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Verifica si el correo ya existe en la base de datos
        $sql_check_email = "SELECT id FROM usuarios WHERE correo = ?";
        $stmt_check_email = $mysqli->prepare($sql_check_email);
        $stmt_check_email->bind_param("s", $correo);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();
        
        if ($result_check_email->num_rows > 0) {
            $error_message = "Este correo electrónico ya está registrado.";
        } else {
            // Hashear la contraseña para almacenarla de forma segura
            $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT);
            
            // Inserta el nuevo usuario en la base de datos
            $sql_insert = "INSERT INTO usuarios (nombre, correo, telefono, direccion, contraseña) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $mysqli->prepare($sql_insert);
            $stmt_insert->bind_param("sssss", $nombre, $correo, $telefono, $direccion, $hashed_password);

            if ($stmt_insert->execute()) {
                $success_message = "¡Registro exitoso! Ahora puedes iniciar sesión.";
                
                // Iniciar sesión automáticamente al usuario
                $_SESSION['usuario_id'] = $mysqli->insert_id;
                $_SESSION['usuario_nombre'] = $nombre;
                
                // Redirige al usuario
                header("Location: perfil.php");
                exit;
            } else {
                $error_message = "Hubo un error al registrar al usuario. Por favor, inténtalo de nuevo.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limón Dulce | Registrarse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="text-center">
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

    <main class="form-signin d-flex align-items-center py-4">
        <div class="card login-card w-100">
            <div class="card-body p-4">
                <img class="mb-4" src="img/Logo.png" alt="Limón Dulce Logo" width="100">
                <h1 class="h3 mb-3 fw-normal section-title">Crea tu Cuenta</h1>

                <form action="" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingName" name="nombre" placeholder="nombre" required>
                        <label for="floatingName">Nombre completo</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingEmail" name="correo" placeholder="correo" required>
                        <label for="floatingEmail">Correo electrónico</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingPhone" name="telefono" placeholder="telefono" required>
                        <label for="floatingPhone">Número de teléfono</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingAddress" name="direccion" placeholder="direccion" required>
                        <label for="floatingAddress">Dirección</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingPassword" name="contraseña" placeholder="Contraseña" required>
                        <label for="floatingPassword">Contraseña</label>
                    </div>
                    <button class="w-100 btn btn-lg btn-primary mt-auto" type="submit">Registrarse</button>
                    <p class="mt-3 mb-0">¿Ya tienes una cuenta? <a href="inicioSesion.php" class="text-primary">Inicia Sesión</a></p>
                </form>
            </div>
        </div>
    </main>

    <footer class="mt-auto text-center py-3" style="background-color:  #AADD22; color: white;">
        <p>&copy; 2025 Limón Dulce. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>