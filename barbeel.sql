-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-12-2025 a las 03:54:19
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `barbeel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_clientes` int(5) NOT NULL,
  `nom_cliente` varchar(50) NOT NULL,
  `tel_cliente` varchar(12) NOT NULL,
  `ce_cliente` varchar(30) NOT NULL,
  `di_cliente` varchar(50) NOT NULL,
  `contraseña` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_clientes`, `nom_cliente`, `tel_cliente`, `ce_cliente`, `di_cliente`, `contraseña`) VALUES
(1, 'marianito garcias pelez sanchez', '961 564 8949', 'marianito69@gmail.com', 'las palmas#123', 'warana123'),
(2, 'joselito', '9617894568', 'padilla220208@gmail.com', 'mi casa', 'fjklsdjfklsd'),
(8, '', '', '', '', ''),
(9, '', '', '', '', ''),
(10, '', '', '', '', ''),
(11, '', '', '', '', ''),
(12, '', '', '', '', ''),
(13, '', '', '', '', ''),
(14, '', '', '', '', ''),
(15, '', '', '', '', ''),
(16, '', '', 'dasdasasdsad', '', ''),
(17, '', '', '', '', ''),
(18, '', '', '', '', ''),
(19, 'joselito', '9617894568', 'joselito12345543@gmail', 'mi casa', 'fofitoasmr'),
(20, 'joselito', '9617894568', 'joselito12345543@gmail.com', 'mi casa', '12312312');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_pro` int(10) NOT NULL,
  `nom_pro` varchar(50) NOT NULL,
  `pvp_pro` int(10) NOT NULL,
  `stock` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO productos (id_pro, nom_pro, pvp_pro, stock) VALUES
(1, 'Bacardi', 150.00, 50),
(2, 'Blend_tinto', 280.00, 50),
(3, 'Bud_light', 45.00, 50),
(4, 'Chardonday', 320.00, 50),
(5, 'Coca-cola', 25.00, 50),
(6, 'Crown', 180.00, 50),
(7, 'Coors_light', 45.00, 50),
(8, 'Fanta', 25.00, 50),
(9, 'Manzanita', 25.00, 50),
(10, 'Michelob_ultra', 50.00, 50),
(11, 'Miller_light', 45.00, 50),
(12, 'Moscato', 290.00, 50),
(13, 'Pepsi', 25.00, 50),
(14, 'pinot_gris', 310.00, 50),
(15, 'Royal_ron', 140.00, 50),
(16, 'Tinto_dulce', 270.00, 50),
(17, 'Tito_s_vodka', 160.00, 50),
(18, 'Burrito', 85.00, 50),
(19, 'Chilaquiles', 95.00, 50),
(20, 'Enchiladas', 90.00, 50),
(21, 'Guacamole', 65.00, 50),
(22, 'Hamburguesa', 110.00, 50),
(23, 'Hot_dog', 70.00, 50),
(24, 'Papas_fritas', 55.00, 50),
(25, 'Pico_de_gallo', 45.00, 50),
(26, 'Pizza', 180.00, 50),
(27, 'Tacos', 75.00, 50),
(28, 'Tlayudas', 110.00, 50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resgistro venta`
--

CREATE TABLE `resgistro venta` (
  `id_mov` int(11) NOT NULL,
  `id_venta` int(10) NOT NULL,
  `can_producto` int(10) NOT NULL,
  `total` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(10) NOT NULL,
  `fecha_venta` date NOT NULL,
  `id_cliente` int(5) NOT NULL,
  `total_venta` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `fecha_venta`, `id_cliente`, `total_venta`) VALUES
(1, '2025-11-27', 2, 175),
(2, '2025-11-27', 2, 175),
(3, '2025-12-05', 2, 75),
(4, '2025-12-05', 2, 355),
(5, '2025-12-05', 2, 150),
(6, '2025-12-05', 2, 175),
(7, '2025-12-05', 2, 255),
(8, '2025-12-05', 2, 255),
(9, '2025-12-05', 2, 355),
(10, '2025-12-05', 2, 355),
(11, '2025-12-05', 2, 355),
(12, '2025-12-05', 2, 175),
(13, '2025-12-05', 2, 175),
(14, '2025-12-08', 2, 95);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_clientes`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_pro`);

--
-- Indices de la tabla `resgistro venta`
--
ALTER TABLE `resgistro venta`
  ADD PRIMARY KEY (`id_mov`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_clientes` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_pro` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resgistro venta`
--
ALTER TABLE `resgistro venta`
  MODIFY `id_mov` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `resgistro venta`
--
ALTER TABLE `resgistro venta`
  ADD CONSTRAINT `resgistro venta_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_clientes`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
