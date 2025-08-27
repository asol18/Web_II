<?php
session_start();
require 'conexion.php';
date_default_timezone_set('America/Costa_Rica');

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicioSesion.php?msg=login_required");
    exit;
}

$idUsuario = $_SESSION['usuario_id'];

// Validar que hay datos de pago
if (!isset($_SESSION['pago'])) {
    die("Error: No se encontró la información del pago.");
}

$datosPago = $_SESSION['pago'];
$total = floatval($datosPago['total']);
$fechaPago = $datosPago['fecha_pago'];
$metodoPago = $_POST['metodo_pago'] ?? '';

if (!in_array($metodoPago, ['tarjeta', 'sinpe'])) {
    die("Error: Método de pago inválido.");
}

$carrito = $_SESSION['carrito'] ?? [];

if (empty($carrito)) {
    die("Error: El carrito está vacío.");
}

// Calcular subtotales e impuestos de nuevo por seguridad
$subtotal = 0.0;
foreach ($carrito as $item) {
    $precio = floatval($item['precio']);
    $cantidad = intval($item['cantidad'] ?? 1);
    $subtotal += $precio * $cantidad;
}
$impuesto = $subtotal * 0.13;
$envio = 2000.00;
$totalCalculado = $subtotal + $impuesto + $envio;
$mysqli->begin_transaction();

try {
    // 1. Insertar en tabla `pago`
    $stmt_pago = $mysqli->prepare("INSERT INTO pago (id_usuario, monto, fecha_pago, metodo_pago) VALUES (?, ?, ?, ?)");
    $stmt_pago->bind_param("idss", $idUsuario, $totalCalculado, $fechaPago, $metodoPago);
    $stmt_pago->execute();
    $id_pago = $mysqli->insert_id;
    $stmt_pago->close();

    if (!$id_pago) {
        throw new Exception("Error al crear el registro de pago.");
    }

    // 2. Detalles del método de pago
    if ($metodoPago === 'tarjeta') {
        $ultimos4 = $_POST['ultimos4'] ?? '';
        $nombreTitular = $_POST['nombre_titular'] ?? '';
        $fechaExp = $_POST['fecha_expiracion'] ?? '';

        if (!$ultimos4 || !$nombreTitular || !$fechaExp) {
            throw new Exception("Faltan datos de la tarjeta.");
        }

        $stmtTarjeta = $mysqli->prepare("INSERT INTO pago_tarjeta (id_pago, ultimos4, nombre_titular, fecha_expiracion)
                                         VALUES (?, ?, ?, ?)");
        $stmtTarjeta->bind_param("isss", $id_pago, $ultimos4, $nombreTitular, $fechaExp);
        $stmtTarjeta->execute();
        $stmtTarjeta->close();
    } elseif ($metodoPago === 'sinpe') {
        $celularSinpe = $_POST['celular_sinpe'] ?? '';
        $nombreRemitente = $_POST['nombre_remitente'] ?? '';
        $referencia = $_POST['referencia_sinpe'] ?? '';

        if (!$celularSinpe || !$nombreRemitente || !$referencia) {
            throw new Exception("Faltan datos de SINPE.");
        }

        $stmtSinpe = $mysqli->prepare("INSERT INTO pago_sinpe (id_pago, celular_sinpe, nombre_remitente, referencia_sinpe)
                                       VALUES (?, ?, ?, ?)");
        $stmtSinpe->bind_param("isss", $id_pago, $celularSinpe, $nombreRemitente, $referencia);
        $stmtSinpe->execute();
        $stmtSinpe->close();
    }

    // 3. Insertar productos del carrito en tabla `carrito`
    // Modificado para incluir el id_pago
    foreach ($carrito as $producto) {
        $producto_id = intval($producto['id']);
        $precio = floatval($producto['precio']);
        $cantidad = intval($producto['cantidad'] ?? 1);
        $subtotalProd = $precio * $cantidad;
        $impuestoProd = $subtotalProd * 0.13;
        $envioProd = 2000.00; // Puedes cambiar esto si hay lógica por producto
        $totalProd = $subtotalProd + $impuestoProd + $envioProd;

        $stmtCarrito = $mysqli->prepare("INSERT INTO carrito (usuario_id, producto_id, precio, cantidad, subtotal, impuesto, envio, total, id_pago)
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtCarrito->bind_param("iididdddi", $idUsuario, $producto_id, $precio, $cantidad, $subtotalProd, $impuestoProd, $envioProd, $totalProd, $id_pago);
        $stmtCarrito->execute();
        $stmtCarrito->close();
    }

    // 4. Confirmar la transacción
    $mysqli->commit();

    // 5. Limpiar datos de sesión
    unset($_SESSION['pago']);
    unset($_SESSION['carrito']);

    // 6. Redirigir
    header("Location: confirmarPago.php?success=1");
    exit;
} catch (Exception $e) {
    $mysqli->rollback();
    die("Ocurrió un error al procesar el pago: " . $e->getMessage());
}
