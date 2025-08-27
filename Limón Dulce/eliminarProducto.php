<?php
session_start();
require_once "conexion.php";

// Validar que solo el admin pueda entrar
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $mysqli->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: adminIndex.php?mensaje=Producto eliminado correctamente");
    } else {
        header("Location: adminIndex.php?mensaje=Error al eliminar el producto");
    }

    $stmt->close();
} else {
    header("Location: adminIndex.php?mensaje=ID no válido");
}
exit;
?>
