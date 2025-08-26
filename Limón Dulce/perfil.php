<?php
session_start();

// 1. Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicioSesion.php");
    exit;
}

require_once 'conexion.php'; // Asegúrate de tener un archivo de conexión a la base de datos

$userId = $_SESSION['usuario_id'];

// 4. Realizar una consulta para obtener los datos del usuario
$sql = "SELECT nombre, correo, telefono, direccion FROM usuarios WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$userData = null;
if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
} else {
    // Si no se encuentra el usuario, redirigir o mostrar un error
    session_destroy();
    header("Location: inicioSesion.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limón Dulce | Mi Perfil</title>
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
                        <a class="nav-link" href="cerrarSesion.php">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <h2 class="text-center mb-4 section-title">Mi Perfil</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="list-group profile-nav-tabs" id="list-tab" role="tablist">
                    <a class="list-group-item list-group-item-action active" id="list-profile-list" data-bs-toggle="list" href="#list-profile" role="tab" aria-controls="list-profile">
                        <i class="bi bi-person-circle"></i> Datos Personales
                    </a>
                    <a class="list-group-item list-group-item-action" id="list-orders-list" data-bs-toggle="list" href="#list-orders" role="tab" aria-controls="list-orders">
                        <i class="bi bi-box-seam"></i> Mis Pedidos
                    </a>
                    <a class="list-group-item list-group-item-action text-danger" href="cerrarSesion.php">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                        <h4 class="mb-3">Datos Personales</h4>
                        <form action="actualizar_perfil.php" method="POST">
                            <div class="mb-3">
                                <label for="userName" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="userName" name="userName" value="<?php echo htmlspecialchars($userData['nombre'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="userEmail" name="userEmail" value="<?php echo htmlspecialchars($userData['correo'] ?? ''); ?>" required disabled>
                            </div>
                            <div class="mb-3">
                                <label for="userPhone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="userPhone" name="userPhone" value="<?php echo htmlspecialchars($userData['telefono'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="userAddress" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="userAddress" name="userAddress" value="<?php echo htmlspecialchars($userData['direccion'] ?? ''); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="list-orders" role="tabpanel" aria-labelledby="list-orders-list">
                        <h4 class="mb-3">Historial de Pedidos</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark custom-table-header">
                                    <tr>
                                        <th scope="col">ID Pedido</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // 5. Cargar los pedidos del usuario
                                    $sqlPedidos = "SELECT id, fecha_pedido, total, estado FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC";
                                    $stmtPedidos = $mysqli->prepare($sqlPedidos);
                                    $stmtPedidos->bind_param("i", $userId);
                                    $stmtPedidos->execute();
                                    $resultPedidos = $stmtPedidos->get_result();

                                    if ($resultPedidos->num_rows > 0) {
                                        while ($pedido = $resultPedidos->fetch_assoc()) {
                                            $estadoClass = ($pedido['estado'] == 'Completado') ? 'bg-success' : 'bg-warning text-dark';
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($pedido['id']) . '</td>';
                                            echo '<td>' . htmlspecialchars($pedido['fecha_pedido']) . '</td>';
                                            echo '<td>$' . htmlspecialchars(number_format($pedido['total'], 2)) . '</td>';
                                            echo '<td><span class="badge ' . $estadoClass . '">' . htmlspecialchars($pedido['estado']) . '</span></td>';
                                            echo '<td><a href="ver_pedido.php?id=' . htmlspecialchars($pedido['id']) . '" class="btn btn-sm btn-outline-info">Ver Detalles</a></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">No tienes pedidos registrados.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
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

<?php
// Cerrar la conexión a la base de datos al final del script
$stmt->close();
$mysqli->close();
?>