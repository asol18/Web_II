-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2025 at 03:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `limon_dulce`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ventas_por_mes` (IN `mes` INT, IN `anio` INT)   BEGIN
    SELECT
        p.id_pago,
        p.fecha_pago,
        p.metodo_pago,
        dc.producto_id,
        pr.nombre AS nombre_producto,
        dc.cantidad,
        dc.subtotal,
        IF(dc.id = min_dc.min_id, c.impuesto, 0) AS impuesto,
        IF(dc.id = min_dc.min_id, c.envio, 0) AS envio,
        IF(dc.id = min_dc.min_id, c.total, 0) AS total
    FROM pago p
    JOIN carrito c ON c.pago_id = p.id_pago
    JOIN detalle_carrito dc ON dc.carrito_id = c.id
    JOIN productos pr ON pr.id = dc.producto_id
    JOIN (
        SELECT carrito_id, MIN(id) AS min_id
        FROM detalle_carrito
        GROUP BY carrito_id
    ) AS min_dc ON min_dc.carrito_id = dc.carrito_id
    WHERE MONTH(p.fecha_pago) = mes
      AND YEAR(p.fecha_pago) = anio
    ORDER BY p.fecha_pago;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `pago_id` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `impuesto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rastreo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carrito`
--

INSERT INTO `carrito` (`id`, `usuario_id`, `pago_id`, `subtotal`, `impuesto`, `envio`, `total`, `rastreo`) VALUES
(1, 4, 1, 76800.00, 9984.00, 2000.00, 88784.00, 1),
(2, 3, 2, 14000.00, 1820.00, 2000.00, 17820.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `detalle_carrito`
--

CREATE TABLE `detalle_carrito` (
  `id` int(11) NOT NULL,
  `carrito_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detalle_carrito`
--

INSERT INTO `detalle_carrito` (`id`, `carrito_id`, `producto_id`, `precio`, `cantidad`, `subtotal`) VALUES
(1, 1, 6, 20800.00, 2, 41600.00),
(2, 1, 21, 35200.00, 1, 35200.00),
(3, 2, 1, 14000.00, 1, 14000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `monto` float NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pago`
--

INSERT INTO `pago` (`id_pago`, `id_usuario`, `monto`, `fecha_pago`, `metodo_pago`) VALUES
(1, 4, 88784, '2025-08-29', 'tarjeta'),
(2, 3, 17820, '2025-08-29', 'sinpe');

-- --------------------------------------------------------

--
-- Table structure for table `pago_sinpe`
--

CREATE TABLE `pago_sinpe` (
  `id_pago_sinpe` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `celular_sinpe` int(8) NOT NULL,
  `nombre_remitente` varchar(100) NOT NULL,
  `referencia_sinpe` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pago_sinpe`
--

INSERT INTO `pago_sinpe` (`id_pago_sinpe`, `id_pago`, `celular_sinpe`, `nombre_remitente`, `referencia_sinpe`) VALUES
(1, 2, 85296326, 'Allyson Sequeira', 'CR78960');

-- --------------------------------------------------------

--
-- Table structure for table `pago_tarjeta`
--

CREATE TABLE `pago_tarjeta` (
  `id_pago_tarjeta` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `ultimos4` varchar(4) NOT NULL,
  `nombre_titular` varchar(100) NOT NULL,
  `fecha_expiracion` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pago_tarjeta`
--

INSERT INTO `pago_tarjeta` (`id_pago_tarjeta`, `id_pago`, `ultimos4`, `nombre_titular`, `fecha_expiracion`) VALUES
(1, 1, '7845', 'Ana Arias', '05/29');

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `categoria`, `imagen`, `stock`, `creado_en`) VALUES
(1, 'Blusa Floral', 'Blusa floral\r\nModelo utiliza talla S\r\nTela 100% rayon\r\nTallas disponibles S M L', 14000.00, 'blusas', 'Blusa Floral.jpg', 6, '2025-08-07 19:32:54'),
(3, 'Blusa amarilla de rayas', 'Blusa amarilla de rayas\r\nModelo utiliza S\r\nTela 88% viscose 15% nylon \r\nTallas en S M L', 12000.00, 'blusas', 'Blusa amarilla de rayas.png', 10, '2025-08-25 17:42:30'),
(4, 'Jacket negra con zipper', 'Jacket negra con zipper    \r\nModelo utiliza S\r\nTela 95% polyester 5% spandex\r\nTallas en S M L', 15200.00, 'abrigos', 'Jacket negra con zipper.png', 5, '2025-08-25 17:42:30'),
(5, 'Suéter deportiva menta', 'Suéter deportiva menta\r\nModelo utiliza S\r\nTela 92% polyester 8% spandex\r\nTallas en S M L', 15200.00, 'abrigos', 'Suéter deportiva menta.png', 3, '2025-08-25 17:42:30'),
(6, 'Set short con camisera flores naranja', 'Set short con camisera flores naranja\r\nModelo utiliza S\r\nTela 100% polyester \r\nTallas en S M L', 20800.00, 'conjuntos', 'Set short con camisera flores naranja.png', 10, '2025-08-25 17:42:30'),
(7, 'Set chaleco con palazzo ivory', 'Set chaleco con palazzo ivory\r\nModelo utiliza S\r\nTela 100% polyester \r\nTallas en S M L ', 44000.00, 'conjuntos', 'Set chaleco con palazzo ivory.png', 5, '2025-08-25 17:42:30'),
(8, 'Short enagua de mezclilla', 'Short enagua de mezclilla\r\nModelo utiliza S\r\nTela 72% algodón 24% polyester 3% rayón 1% spandex\r\nTallas en S M L', 16000.00, 'enaguas', 'Short enagua de mezclilla.png', 7, '2025-08-25 17:42:30'),
(9, 'Falda floreada midi', 'Falda floreada midi\r\nModelo utiliza S\r\nTela 100% polyester \r\nTallas en S M L', 19200.00, 'enaguas', 'Falda floreada midi.png', 15, '2025-08-25 17:42:30'),
(10, 'Jumpsuit blanco cropped', 'Jumpsuit blanco cropped\r\nModelo utiliza S\r\nTela 100% polyester \r\nTallas en S M LL', 28000.00, 'enterizos', 'Jumpsuit blanco cropped.png', 10, '2025-08-25 17:42:30'),
(11, 'Jumpsuit negro', 'Jumpsuit negro\r\nTela 95% poliester 5% poliester\r\nModelo usa talla S\r\nTallas en S M L ', 19200.00, 'enterizos', 'Jumpsuit negro.png', 9, '2025-08-25 17:42:30'),
(12, 'Pantalón negro cargo', 'Pantalón negro cargo\r\nModelo utiliza S\r\nTela 95% polyester 5% spandex\r\nTallas en S M L', 17600.00, 'pantalones', 'Pantalón negro cargo.png', 6, '2025-08-25 17:57:44'),
(13, 'Pantalón de pinzas beige', 'Pantalón de pinzas beige\r\nModelo utiliza S\r\nTela 70% polyester 24% rayon 6% spandex\r\nTallas en S M L', 19200.00, 'pantalones', 'Pantalón de pinzas beige.png', 7, '2025-08-25 17:57:44'),
(14, 'Short de mezclilla ivory', 'Short de mezclilla ivory\r\nModelo utiliza S\r\nTela 98% algodón 2% spandex\r\nTallas en S M L', 16000.00, 'shorts', 'Short de mezclilla ivory.png', 6, '2025-08-25 17:57:44'),
(15, 'Short de mezclilla con brillo', 'Short de mezclilla con brillo\r\nModelo usa talla M\r\nTela 100% poliester \r\nTallas en S M L', 19200.00, 'shorts', 'Short de mezclilla con brillo.png', 7, '2025-08-25 17:57:44'),
(16, 'Sandalias transparentes blanca', 'Sandalias transparentes blanca\r\nTallas en, 35, 36, 37, 38, 39\r\nAltura tacón 9 cm', 19200.00, 'zapatos', 'Sandalias transparentes blanca.png', 20, '2025-08-25 17:57:44'),
(17, 'Tennis rosadas', 'Tennis rosadas\r\nTallas en, 35, 36, 37, 38', 19600.00, 'zapatos', 'Tennis rosadas.png', 19, '2025-08-25 17:57:44'),
(18, 'Chaleco rosado', 'Chaleco rosado\r\nModelo utiliza S\r\nTela 72% algodón 1% spandex 24% polyester 3% rayon\r\nTallas en S M L', 17600.00, 'chalecos', 'Chaleco rosado.png', 8, '2025-08-25 17:57:44'),
(19, 'Chaleco moca', 'Chaleco moca\r\nModelo utiliza S \r\nTela 97% polyester 3% spandex\r\nTallas en S M L', 17600.00, 'chalecos', 'Chaleco moca.png', 4, '2025-08-25 17:57:44'),
(20, 'Vestido de fiesta negro', 'Vestido de fiesta negro\r\nModelo utiliza S\r\nTela 100% polyester \r\nTallas en S M L', 44000.00, 'vestidos', 'Vestido de fiesta negro.png', 10, '2025-08-25 18:02:00'),
(21, 'Vestido de lunares midi', 'Vestido de lunares midi\r\nModelo utiliza S\r\nTela 100% polyester\r\nTallas en S M L ', 35200.00, 'vestidos', 'Vestido de lunares midi.png', 7, '2025-08-25 18:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('usuario','admin') DEFAULT 'usuario',
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `telefono`, `direccion`, `contraseña`, `rol`, `creado_en`) VALUES
(1, 'Admin', 'admin@limon.com', '0000', 'Backend', '$2y$10$oXo5FzuBLx3wFTYCONPgEumdeMC2NC0gChVSoVFZ71FWshg4fonCm', 'admin', '2025-08-07 00:57:33'),
(3, 'Allyson', 'allysequi18@gmail.com', '85296326', 'Gamonales, Ciudad Quesada San Carlos', '$2y$10$6wingZ3vYOgv55K4A4wADuvB9EvlPH7UUQ1MtL9/3Ju.uEr2LrGOO', 'usuario', '2025-08-07 19:44:58'),
(4, 'Ana Maria', 'anamariacampos20@hotmail.com', '62168158', 'Barrio Lourdes', '$2y$10$sEv5gy577Sc2QQUYsV0TjO6/USX.fDUULr5pOWw0.GcrmpZc5B8e.', 'usuario', '2025-08-29 03:20:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `id_pago` (`pago_id`);

--
-- Indexes for table `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `fk_detalleCarrito_carrito` (`carrito_id`);

--
-- Indexes for table `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `fk_pago_usuario` (`id_usuario`);

--
-- Indexes for table `pago_sinpe`
--
ALTER TABLE `pago_sinpe`
  ADD PRIMARY KEY (`id_pago_sinpe`),
  ADD KEY `fk_sinpe_pago` (`id_pago`);

--
-- Indexes for table `pago_tarjeta`
--
ALTER TABLE `pago_tarjeta`
  ADD PRIMARY KEY (`id_pago_tarjeta`),
  ADD KEY `fk_tarjeta_pago` (`id_pago`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pago_sinpe`
--
ALTER TABLE `pago_sinpe`
  MODIFY `id_pago_sinpe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pago_tarjeta`
--
ALTER TABLE `pago_tarjeta`
  MODIFY `id_pago_tarjeta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_carrito_pago` FOREIGN KEY (`pago_id`) REFERENCES `pago` (`id_pago`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_carrito_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Constraints for table `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  ADD CONSTRAINT `detalle_carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `fk_detalleCarrito_carrito` FOREIGN KEY (`carrito_id`) REFERENCES `carrito` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
