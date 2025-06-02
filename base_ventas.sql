-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 03:13 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `base_ventas`
--

-- --------------------------------------------------------

--
-- Table structure for table `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `nombre_producto` varchar(30) COLLATE utf32_unicode_ci NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal_dolares` float NOT NULL,
  `subtotal_bolivares` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Dumping data for table `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `nombre_producto`, `cantidad`, `subtotal_dolares`, `subtotal_bolivares`) VALUES
(1, 'Arroz Mari', 2, 3, 235.5),
(2, 'Caraotas la coste', 2, 6, 471),
(3, 'Arroz Mari', 4, 6, 471),
(4, 'Harina deli', 4, 9.6, 753.6),
(5, 'Harina deli', 3, 7.2, 677.232),
(7, 'Arroz Mari', 1, 1.5, 141.09),
(8, 'Arroz Mari', 2, 3, 282.18),
(9, 'Champu Liso 250ml', 4, 42, 3950.52),
(10, 'Arroz Mari', 4, 6, 564.36),
(11, 'Caraotas la coste', 3, 4.5, 437.89),
(12, 'Champu Liso 250ml', 1, 10.5, 1021.75),
(13, 'Arroz Mari', 2, 3, 291.93),
(14, 'Harina deli', 5, 15, 1459.65),
(15, 'Champu Liso 250ml', 1, 10.5, 1021.75),
(16, 'Harina deli', 2, 6, 583.86),
(17, 'Arroz Mari', 2, 3, 291.93),
(18, 'Harina deli', 2, 6, 583.86),
(19, 'Harina deli', 2, 6, 583.86),
(20, 'Arroz Mari', 4, 6, 583.86),
(21, 'Arroz Mari', 1, 1.5, 145.97),
(22, 'Cafe', 1, 2.5, 243.28),
(23, 'Harina deli', 2, 6, 583.86),
(24, 'Arroz Mari', 1, 1.5, 145.97),
(25, 'Arroz Mari', 2, 3, 291.93),
(26, 'Arroz Mari', 2, 3, 291.93),
(27, 'Harina deli', 1, 3, 291.93);

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int(10) NOT NULL,
  `nombre` varchar(50) COLLATE utf32_unicode_ci NOT NULL,
  `precio_en_dolares` double NOT NULL,
  `cantidad` int(30) NOT NULL,
  `precio_en_bolivares` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio_en_dolares`, `cantidad`, `precio_en_bolivares`) VALUES
(1, 'Harina deli', 3, 4, 0),
(2, 'Arroz Mari', 1.5, 3, 0),
(6, 'Champu Liso 250ml', 10.5, 1, 0),
(9, 'Caraotas la coste', 1.5, 10, 0),
(10, 'Cafe', 2.5, 11, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tipo_cambio`
--

CREATE TABLE `tipo_cambio` (
  `id` int(10) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tasa_de_cambio` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Dumping data for table `tipo_cambio`
--

INSERT INTO `tipo_cambio` (`id`, `fecha`, `tasa_de_cambio`) VALUES
(4, '2024-11-26 13:48:50', 97.31);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `correo` varchar(20) COLLATE utf32_unicode_ci NOT NULL,
  `clave` varchar(20) COLLATE utf32_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `correo`, `clave`) VALUES
(1, 'camaco81@gmail.com', 'Electro123'),
(3, 'master@gmail.com', 'Anime123456');

-- --------------------------------------------------------

--
-- Table structure for table `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha_venta` date NOT NULL,
  `total_dolares` float NOT NULL,
  `total_bolivares` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Dumping data for table `ventas`
--

INSERT INTO `ventas` (`id`, `fecha_venta`, `total_dolares`, `total_bolivares`) VALUES
(1, '2025-04-21', 12.6, 989.1),
(2, '2025-05-17', 11.7, 1100.5),
(3, '2025-05-17', 6.1, 573.77),
(4, '2025-05-17', 28.2, 2652.49),
(5, '2025-05-19', 10.2, 959.41),
(7, '2025-05-19', 3, 282.18),
(8, '2025-05-19', 3, 282.18),
(11, '2025-05-19', 42, 3950.52),
(13, '2025-05-29', 6, 564.36),
(14, '2025-05-31', 33, 3211.22),
(15, '2025-05-31', 0, 0),
(16, '2025-05-31', 0, 0),
(18, '2025-06-02', 10.5, 1021.75),
(21, '2025-06-02', 6, 583.86),
(22, '2025-06-02', 9, 875.79),
(23, '2025-06-02', 12, 1167.72),
(24, '2025-06-02', 4, 389.25),
(25, '2025-06-02', 7.5, 729.83),
(26, '2025-06-02', 3, 291.93),
(27, '2025-06-02', 6, 583.86);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
