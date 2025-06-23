-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-06-2025 a las 15:05:07
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `base_ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `cedula`, `nombre`, `telefono`) VALUES
(1, '31438776', 'Marco Camacho', '04165227711'),
(2, '26959512', 'Yulia Viera', '04145021471');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `nombre_producto` varchar(30) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal_dolares` float NOT NULL,
  `subtotal_bolivares` float NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `nombre_producto`, `cantidad`, `subtotal_dolares`, `subtotal_bolivares`, `venta_id`, `producto_id`) VALUES
(1, 'Harina deli', 2, 6, 583.86, 11, 0),
(2, 'Caraotas la coste', 1, 1.5, 145.97, 12, 0),
(3, 'Arroz Mari', 1, 1.5, 145.97, 13, 0),
(6, 'Arroz Mari', 4, 6, 632.7, 16, 0),
(7, 'Arroz Mari', 3, 4.5, 474.53, 17, 0),
(8, 'Arroz Mari', 6, 9, 949.05, 18, 0),
(9, 'Arroz Mari', 4, 6, 632.7, 19, 0),
(10, 'Arroz Mari', 1, 1.5, 0, 25, 2),
(11, 'Arroz Mari', 1, 1.5, 0, 26, 2),
(12, 'Caraotas la coste', 1, 1.5, 0, 27, 9),
(13, 'Caraotas la coste', 1, 1.5, 0, 28, 9),
(14, 'Caraotas la coste', 1, 1.5, 0, 29, 9),
(15, 'Harina deli', 1, 3, 0, 30, 1),
(16, 'Cafe', 1, 2.5, 0, 31, 10),
(17, 'Cafe', 1, 2.5, 263.63, 33, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio_en_dolares` double NOT NULL,
  `cantidad` int(30) NOT NULL,
  `precio_en_bolivares` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio_en_dolares`, `cantidad`, `precio_en_bolivares`) VALUES
(1, 'Harina deli', 3, 2, 0),
(2, 'Arroz Mari', 1.5, 2, 0),
(6, 'Champu Liso 250ml', 10.5, 1, 0),
(9, 'Caraotas la coste', 1.5, 5, 0),
(10, 'Cafe', 2.5, 9, 0),
(11, 'Cejas y pestañasssssss', 10, 12, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_cambio`
--

CREATE TABLE `tipo_cambio` (
  `id` int(10) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `tasa_de_cambio` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_cambio`
--

INSERT INTO `tipo_cambio` (`id`, `fecha`, `tasa_de_cambio`) VALUES
(4, '2024-11-26 13:48:50', 105.45);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `correo` varchar(20) NOT NULL,
  `clave` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `correo`, `clave`) VALUES
(1, 'camaco81@gmail.com', 'Electro123'),
(3, 'master@gmail.com', 'Anime123456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha_venta` date NOT NULL,
  `total_dolares` float NOT NULL,
  `total_bolivares` float NOT NULL,
  `cliente_nombre` varchar(50) NOT NULL,
  `cliente_cedula` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha_venta`, `total_dolares`, `total_bolivares`, `cliente_nombre`, `cliente_cedula`) VALUES
(9, '2025-06-03', 3, 291.93, 'Consumidor Final', 'V-00000000'),
(10, '2025-06-03', 3, 291.93, 'Consumidor Final', 'V-00000000'),
(11, '2025-06-03', 6, 583.86, 'Consumidor Final', 'V-00000000'),
(12, '2025-06-03', 1.5, 145.97, 'Sol Contreras', 'V-30099742'),
(13, '2025-06-03', 1.5, 145.97, 'Marco Camacho', '31438776'),
(15, '2025-06-21', 10, 105, 'Yulia VIera', '26959512'),
(16, '2025-06-23', 6, 632.7, 'Consumidor Final', 'V-00000000'),
(17, '2025-06-23', 4.5, 474.53, 'Consumidor Final', 'V-00000000'),
(18, '2025-06-23', 9, 949.05, 'Consumidor Final', 'V-00000000'),
(19, '2025-06-23', 6, 632.7, 'Consumidor Final', 'V-00000000'),
(23, '2025-06-23', 1.5, 158.18, 'Marco Camacho', '31438776'),
(24, '2025-06-23', 1.5, 158.18, 'Marco Camacho', '31438776'),
(25, '2025-06-23', 1.5, 158.18, 'Marco Camacho', '31438776'),
(26, '2025-06-23', 1.5, 158.18, 'Consumidor Final', 'V-00000000'),
(27, '2025-06-23', 1.5, 158.18, 'Consumidor Final', 'V-00000000'),
(28, '2025-06-23', 1.5, 158.18, 'Consumidor Final', 'V-00000000'),
(29, '2025-06-23', 1.5, 158.18, 'Consumidor Final', 'V-00000000'),
(30, '2025-06-23', 3, 316.35, 'Marco Camacho', '31438776'),
(31, '2025-06-23', 2.5, 263.63, 'Marco Camacho', '31438776'),
(33, '2025-06-23', 2.5, 263.63, 'Consumidor Final', '31438776');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `venta_id` (`venta_id`),
  ADD UNIQUE KEY `venta_id_2` (`venta_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `fk_detalleventa_ventas` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
