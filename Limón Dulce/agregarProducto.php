<?php
session_start();
require_once "conexion.php";

// Validar que solo el admin pueda entrar
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: adminIndex.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $categoria = trim($_POST["categoria"]);
    $precio = floatval($_POST["precio"]);
    $stock = intval($_POST["stock"]);
    $imagen = trim($_POST["imagen"]); // URL de la imagen (ej: img/producto1.jpg)

    if (!empty($nombre) && !empty($categoria) && $precio > 0 && $stock >= 0 && !empty($imagen)) {
        $stmt = $mysqli->prepare("INSERT INTO productos (nombre, categoria, precio, stock, imagen, creado_en) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssdss", $nombre, $categoria, $precio, $stock, $imagen);

        if ($stmt->execute()) {
            $mensaje = "✅ Producto agregado correctamente.";
        } else {
            $mensaje = "❌ Error al agregar el producto: " . $mysqli->error;
        }
        $stmt->close();
    } else {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Agregar Nuevo Producto</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Nombre del producto</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Categoría</label>
            <input type="text" name="categoria" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Precio (₡)</label>
            <input type="number" name="precio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">URL de la imagen</label>
            <input type="text" name="imagen" class="form-control" placeholder="Ejemplo: img/camisa1.jpg" required>
        </div>

        <button type="submit" class="btn btn-success">Agregar Producto</button>
        <a href="adminIndex.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>
