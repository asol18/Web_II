<?php
session_start();
require_once 'conexion.php';

$categorias = [
    "blusas", "abrigos", "conjuntos", "enaguas",
    "enterizos", "pantalones", "shorts", "vestidos",
    "zapatos", "chalecos"
];

// Obtener filtros desde GET
$nombre = trim($_GET['nombre'] ?? '');
$categoria = trim($_GET['categoria'] ?? '');
$precio_min = $_GET['precio_min'] ?? '';
$precio_max = $_GET['precio_max'] ?? '';

// Consulta base
$sql = "SELECT * FROM productos WHERE 1=1";

// Agregar filtros dinámicamente
if ($nombre !== '') {
    $sql .= " AND nombre LIKE :nombre";
}
if ($categoria !== '') {
    $sql .= " AND categoria = :categoria";
}
if ($precio_min !== '') {
    $sql .= " AND precio >= :precio_min";
}
if ($precio_max !== '') {
    $sql .= " AND precio <= :precio_max";
}

$stmt = $mysqli->prepare(str_replace(
    [':nombre', ':categoria', ':precio_min', ':precio_max'],
    ['?', '?', '?', '?'],
    $sql
));

$types = '';
$params = [];
if ($nombre !== '') {
    $types .= 's';
    $params[] = '%' . $nombre . '%';
}
if ($categoria !== '') {
    $types .= 's';
    $params[] = $categoria;
}
if ($precio_min !== '') {
    $types .= 'd';
    $params[] = $precio_min;
}
if ($precio_max !== '') {
    $types .= 'd';
    $params[] = $precio_max;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$productos = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Limón Dulce | Filtrar Productos </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles.css" />
    <style>
    .card-img-top {
        max-height: 180px;
        object-fit: cover;
    }

    .card {
        max-width: 300px;
        margin: 0 auto;
    }

    .card-body {
        font-size: 0.9rem;
    }

    @media (min-width: 768px) {
        .card {
            max-width: 100%;
        }
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

<div class="container my-5">
    <h2 class="mb-4 text-center">Filtrar Productos</h2>

    <!-- Formulario de filtros -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre del producto" value="<?= htmlspecialchars($nombre) ?>">
        </div>
        <div class="col-md-3">
            <select name="categoria" class="form-select">
              <option value="">Todas las categorías</option>
              <?php foreach ($categorias as $cat): ?>
                 <option value="<?= $cat ?>" <?= $categoria === $cat ? 'selected' : '' ?>>
                       <?= ucfirst($cat) ?>
                       </option>
                     <?php endforeach; ?>
                    </select>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="precio_min" class="form-control" placeholder="Precio mínimo" value="<?= htmlspecialchars($precio_min) ?>">
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="precio_max" class="form-control" placeholder="Precio máximo" value="<?= htmlspecialchars($precio_max) ?>">
        </div>
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel-fill"></i>
            </button>
        </div>
    </form>

    <!-- Lista de productos -->
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        <?php if (count($productos) > 0): ?>
            <?php foreach ($productos as $row): ?>
                <div class="col">
                    <div class="card" style="width: 18rem;">
                        <img src="img/<?= htmlspecialchars($row['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nombre']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['nombre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($row['descripcion']) ?></p>
                            <p class="card-text fw-bold text-success">₡<?= number_format($row['precio'], 2) ?></p>
                            <a href="carrito.php?action=add&id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-primary mt-auto">
                                Agregar al Carrito
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p>No se encontraron productos que coincidan con los filtros.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
