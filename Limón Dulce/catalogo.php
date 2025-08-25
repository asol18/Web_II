<?php
require_once 'conexion.php'; 
// OBTENER LA CATEGORÍA DE LA URL
// Usa $_GET para obtener el parámetro 'categoria'
$categoria = $_GET['categoria'] ?? null;

// Si no se especifica una categoría, se redirige o se muestra un mensaje de error
if (!$categoria) {
    header("Location: index.php"); // Redirige a la página principal
    exit;
}

//  CONSULTA A LA BASE DE DATOS USANDO CONSULTAS PREPARADAS
// Esto previene inyecciones de SQL
$sql = "SELECT id, nombre, descripcion, imagen, precio FROM productos WHERE categoria = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $categoria); // 's' indica que el parámetro es una cadena de texto
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - <?php echo htmlspecialchars(ucfirst($categoria)); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Catálogo de <?php echo htmlspecialchars(ucfirst($categoria)); ?></h1>
        <div class="row">
            <?php
            // 4. MOSTRAR LOS PRODUCTOS DINÁMICAMENTE
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="img/<?php echo htmlspecialchars($row['imagen']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($row['descripcion']); ?>
                                </p>
                                <p class="card-text fw-bold">$<?php echo htmlspecialchars(number_format($row['precio'], 2)); ?></p>
                                <a href="carrito.php?action=add&id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-primary">Agregar al Carrito</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="text-center">No se encontraron productos en esta categoría.</p>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php

$stmt->close();
$mysqli->close();
?>