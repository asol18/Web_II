<?php
session_start();
require 'conexion.php';
date_default_timezone_set('America/Costa_Rica');

// Redirigir si no hay sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicioSesion.php?msg=login_required");
    exit;
}

$idUsuario = $_SESSION['usuario_id'];
$carrito = $_SESSION['carrito'] ?? [];
$datosPago = $_SESSION['pago'] ?? null;
$metodoPago = $_POST['metodo_pago'] ?? '';

// Validaciones básicas
if (!$datosPago || empty($carrito)) {
    die("Error: Información de pago o carrito no disponible.");
}

if (!in_array($metodoPago, ['tarjeta', 'sinpe'])) {
    die("Error: Método de pago inválido.");
}

// Función para obtener siguiente numero de rastreo
function obtenerSiguienteRastreo($mysqli): int {
    $result = $mysqli->query("SELECT MAX(rastreo) AS ultimo FROM carrito");
    $row = $result->fetch_assoc();
    return ($row['ultimo'] ?? 0) + 1;
}

// Calcular totales
function calcularTotales(array $carrito): array {
    $subtotal = 0.0;
    foreach ($carrito as $item) {
        $subtotal += floatval($item['precio']) * intval($item['cantidad'] ?? 1);
    }
    $impuesto = $subtotal * 0.13;
    $envio = 2000.00;
    return [
        'subtotal' => $subtotal,
        'impuesto' => $impuesto,
        'envio' => $envio,
        'total' => $subtotal + $impuesto + $envio
    ];
}

// Insertar pago
function insertarPago($mysqli, $idUsuario, $total, $fecha, $metodo): int {
    $stmt = $mysqli->prepare("INSERT INTO pago (id_usuario, monto, fecha_pago, metodo_pago) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $idUsuario, $total, $fecha, $metodo);
    $stmt->execute();
    $id = $mysqli->insert_id;
    $stmt->close();
    return $id;
}

// Insertar detalles de tarjeta o sinpe
function insertarDetallesMetodo($mysqli, $id_pago, $metodo) {
    if ($metodo === 'tarjeta') {
        $stmt = $mysqli->prepare("INSERT INTO pago_tarjeta (id_pago, ultimos4, nombre_titular, fecha_expiracion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_pago, $_POST['ultimos4'], $_POST['nombre_titular'], $_POST['fecha_expiracion']);
    } elseif ($metodo === 'sinpe') {
        $stmt = $mysqli->prepare("INSERT INTO pago_sinpe (id_pago, celular_sinpe, nombre_remitente, referencia_sinpe) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_pago, $_POST['celular_sinpe'], $_POST['nombre_remitente'], $_POST['referencia_sinpe']);
    } else {
        throw new Exception("Método de pago desconocido.");
    }

    $stmt->execute();
    $stmt->close();
}

// Insertar carrito
function insertarCarrito($mysqli, $idUsuario, $id_pago, $totales, $rastreo): int {
    $stmt = $mysqli->prepare("INSERT INTO carrito (usuario_id, pago_id, subtotal, impuesto, envio, total, rastreo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiddddi", $idUsuario, $id_pago, $totales['subtotal'], $totales['impuesto'], $totales['envio'], $totales['total'], $rastreo);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

// Insertar productos en detalle_carrito
function insertarProductos($mysqli, $id_carrito, array $carrito) {
    $stmt = $mysqli->prepare("INSERT INTO detalle_carrito (carrito_id, producto_id, precio, cantidad, subtotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($carrito as $item) {
        $id = intval($item['id']);
        $precio = floatval($item['precio']);
        $cantidad = intval($item['cantidad']);
        $subtotal = $precio * $cantidad;
        $stmt->bind_param("iiddi", $id_carrito, $id, $precio, $cantidad, $subtotal);
        $stmt->execute();
    }
    $stmt->close();
}

// Ejecutar proceso con transacción
try {
    $mysqli->begin_transaction();

    $rastreo = obtenerSiguienteRastreo($mysqli);
    $totales = calcularTotales($carrito);
    $id_pago = insertarPago($mysqli, $idUsuario, $totales['total'], $datosPago['fecha_pago'], $metodoPago);
    insertarDetallesMetodo($mysqli, $id_pago, $metodoPago);
    $id_carrito = insertarCarrito($mysqli, $idUsuario, $id_pago, $totales, $rastreo);
    insertarProductos($mysqli, $id_carrito, $carrito);

    $mysqli->commit();

    unset($_SESSION['pago'], $_SESSION['carrito']);
    $_SESSION['rastreo'] = $rastreo;
    header("Location: confirmarPago.php?success=1");
    exit;
} catch (Exception $e) {
    $mysqli->rollback();
    die("Error al procesar el pago: " . $e->getMessage());
}
