<?php
require 'conexion.php';

$mes = isset($_GET['mes']) ? intval($_GET['mes']) : 0;
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : 0;

if ($mes < 1 || $mes > 12 || $anio < 2000) {
    die("Parámetros de fecha inválidos.");
}

$stmt = $mysqli->prepare("CALL sp_ventas_por_mes(?, ?)");
$stmt->bind_param("ii", $mes, $anio);
$stmt->execute();
$resultado = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=reporte_ventas_{$mes}_{$anio}.csv");

$output = fopen('php://output', 'w');

// Encabezados
fputcsv($output, ['ID Pago', 'Fecha Pago', 'Método', 'ID Producto', 'Producto', 'Cantidad', 'Subtotal', 'Impuesto', 'Envío', 'Total']);

// Datos
while ($row = $resultado->fetch_assoc()) {
    fputcsv($output, [
        $row['id_pago'],
        $row['fecha_pago'],
        $row['metodo_pago'],
        $row['producto_id'],
        $row['nombre_producto'],
        $row['cantidad'],
        number_format($row['subtotal'], 2),
        number_format($row['impuesto'], 2),
        number_format($row['envio'], 2),
        number_format($row['total'], 2)
    ]);
}

fclose($output);
$stmt->close();
$mysqli->close();
exit;
?>
