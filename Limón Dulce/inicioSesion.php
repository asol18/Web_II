<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contraseña'];

    // Seleccionamos también el rol
    $stmt = $mysqli->prepare("SELECT id, nombre, contraseña, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($contrasena, $user['contraseña'])) {
            // Guardamos datos en sesión
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            $_SESSION['usuario_rol'] = $user['rol'];

            // Redirección según el rol
            if ($user['rol'] === 'admin') {
                header("Location: adminIndex.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "No se encontró una cuenta con ese correo electrónico.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limón Dulce | Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="text-center">
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

    <main class="form-signin d-flex align-items-center py-4">
        <div class="card login-card w-100">
            <div class="card-body p-4">
                <img class="mb-4" src="img/Logo.png" alt="Limón Dulce Logo" width="100">
                <h1 class="h3 mb-3 fw-normal section-title">Inicia Sesión</h1>

                <form action="" method="POST">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingInput" name="correo" placeholder="correo" required>
                        <label for="floatingInput">Correo electrónico</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingPassword" name="contraseña" placeholder="Contraseña" required>
                        <label for="floatingPassword">Contraseña</label>
                    </div>

                    <div class="checkbox mb-3">
                        <label>
                            <input type="checkbox" value="remember-me"> Recordarme
                        </label>
                    </div>
                    <button class="w-100 btn btn-lg btn-primary mt-auto" type="submit">Entrar</button>
                    <p class="mt-3 mb-0">¿No tienes una cuenta? <a href="registro.php" class="text-primary">Regístrate aquí</a></p>
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