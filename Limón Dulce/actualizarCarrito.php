<?php
session_start();

header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
    exit;
}

$action = $_POST['action'];
$id_producto = $_POST['id_producto'] ?? null;

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

function formatPriceCRC($price) {
    return '₡ ' . number_format($price, 2, ',', '.');
}

function parse_price_to_float_updated($s) {
    if (!is_string($s) && !is_numeric($s)) return 0.0;
    $s = trim((string)$s);
    $lastDot = strrpos($s, '.');
    $lastComma = strrpos($s, ',');

    if ($lastDot !== false && $lastComma !== false) {
        if ($lastComma > $lastDot) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } else {
            $s = str_replace(',', '', $s);
        }
    } else {
        if ($lastComma !== false) {
            $parts = explode(',', $s);
            $lastPart = end($parts);
            if (strlen($lastPart) == 3 && count($parts) > 1) {
                $s = str_replace(',', '', $s);
            } else {
                $s = str_replace(',', '.', $s);
            }
        } elseif ($lastDot !== false) {
            $parts = explode('.', $s);
            $lastPart = end($parts);
            if (strlen($lastPart) == 3 && count($parts) > 1) {
                $s = str_replace('.', '', $s);
            }
        }
    }
    return floatval($s);
}

switch ($action) {
    case 'actualizar':
        $cantidad = intval($_POST['cantidad'] ?? 1);
        if ($cantidad < 1) $cantidad = 1;

        if ($id_producto !== null && isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]['cantidad'] = $cantidad;
        }

        break;

    case 'eliminar':
        if ($id_producto !== null && isset($_SESSION['carrito'][$id_producto])) {
            unset($_SESSION['carrito'][$id_producto]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción inválida']);
        exit;
}

// Recalcular totales
$carrito = $_SESSION['carrito'];
$subtotal = 0.0;

foreach ($carrito as $item) {
    $precio = parse_price_to_float_updated($item['precio']);
    $cantidad = max(1, intval($item['cantidad'] ?? 1));
    $subtotal += $precio * $cantidad;
}

$impuesto = $subtotal * 0.13;
$envio = 2000.00;
$total = $subtotal + $impuesto + $envio;

// Para el subtotal de un item actualizado:
$subtotal_item = 0;
if ($id_producto !== null && isset($carrito[$id_producto])) {
    $precio_item = parse_price_to_float_updated($carrito[$id_producto]['precio']);
    $cantidad_item = intval($carrito[$id_producto]['cantidad']);
    $subtotal_item = $precio_item * $cantidad_item;
}

echo json_encode([
    'success' => true,
    'subtotal' => formatPriceCRC($subtotal),
    'impuesto' => formatPriceCRC($impuesto),
    'envio' => formatPriceCRC($envio),
    'total' => formatPriceCRC($total),
    'total_raw' => number_format($total, 2, '.', ''),
    'subtotal_item' => formatPriceCRC($subtotal_item)
]);
