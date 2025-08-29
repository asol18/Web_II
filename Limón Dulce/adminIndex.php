<?php
session_start();
require_once 'conexion.php';
// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}
// Obtener productos de la base de datos, incluyendo la columna de imagen
$resultado = $mysqli->query("SELECT id, imagen, nombre, categoria, precio, stock FROM productos ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inicio | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .producto-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
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

    <div class="container my-5">
        <h2 class="mb-4">Gestión de Productos</h2>
        <div class="mb-3 text-end">
            <a href="agregarProducto.php" class="btn btn-primary">+ Agregar Producto</a>
        </div>
        <?php if ($resultado->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['id']); ?></td>
                                <td>
                                    <?php if (!empty($fila['imagen'])): ?>
                                        <!-- Aquí está la corrección: se usa una ruta absoluta para que la imagen se cargue correctamente -->
                                        <img src="img/<?php echo htmlspecialchars($fila['imagen']); ?>" class="producto-img" alt="<?php echo htmlspecialchars($fila['nombre']); ?>" />
                                    <?php else: ?>
                                        <span class="text-muted">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                                <td>₡<?php echo number_format($fila['precio'], 3); ?></td>
                                <td><?php echo htmlspecialchars($fila['stock']); ?></td>
                                <td>
                                    <a href="editarProducto.php?id=<?php echo htmlspecialchars($fila['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="eliminarProducto.php?id=<?php echo htmlspecialchars($fila['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No hay productos registrados.</div>
        <?php endif; ?>
    </div>
    
<div class="mb-4">
    <h4>Generar Reporte de Ventas</h4>
    <form action="reporteIngresosMes.php" method="get" class="row g-3">
        <div class="col-md-3">
            <label for="mes" class="form-label">Mes (1-12)</label>
            <input type="number" name="mes" id="mes" min="1" max="12" required class="form-control">
        </div>
        <div class="col-md-3">
            <label for="anio" class="form-label">Año</label>
            <input type="number" name="anio" id="anio" min="2000" max="2100" required class="form-control">
        </div>
        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-success">Descargar CSV</button>
        </div>
    </form>
</div>

</body>

</html>