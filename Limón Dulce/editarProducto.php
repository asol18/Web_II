<?php
session_start();
require_once "conexion.php";

// Validar que solo el admin pueda entrar
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$mensaje = "";
$id = $_GET['id'] ?? null;

// Verificar que el ID sea válido
if (!$id) {
    header("Location: adminIndex.php");
    exit;
}

// Obtener datos actuales del producto
$stmt = $mysqli->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();
$stmt->close();

if (!$producto) {
    header("Location: adminIndex.php");
    exit;
}

// Procesar edición
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $categoria = trim($_POST["categoria"]);
    $precio = floatval($_POST["precio"]);
    $stock = intval($_POST["stock"]);
    $imagen = trim($_POST["imagen"]);

    if (!empty($nombre) && !empty($categoria) && $precio > 0 && $stock >= 0 && !empty($imagen)) {
        $stmt = $mysqli->prepare("UPDATE productos SET nombre=?, categoria=?, precio=?, stock=?, imagen=? WHERE id=?");
        $stmt->bind_param("ssdssi", $nombre, $categoria, $precio, $stock, $imagen, $id);

        if ($stmt->execute()) {
            $mensaje = "✅ Producto actualizado correctamente.";
            // Refrescar datos del producto
            $producto = [
                "id" => $id,
                "nombre" => $nombre,
                "categoria" => $categoria,
                "precio" => $precio,
                "stock" => $stock,
                "imagen" => $imagen
            ];
        } else {
            $mensaje = "❌ Error al actualizar el producto: " . $mysqli->error;
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
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Editar Producto</h2>
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Nombre del producto</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Categoría</label>
            <input type="text" name="categoria" class="form-control" value="<?php echo htmlspecialchars($producto['categoria']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Precio (₡)</label>
            <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">URL de la imagen</label>
            <input type="text" name="imagen" class="form-control" value="<?php echo htmlspecialchars($producto['imagen']); ?>" required>
        </div>
        <button type="submit" class="btn btn-warning">Actualizar Producto</button>
        <a href="adminIndex.php" class="btn btn-secondary">Volver</a>
    </form>
</div>

</body>
</html>
