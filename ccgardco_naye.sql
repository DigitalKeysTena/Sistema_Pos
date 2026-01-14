-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 13-01-2026 a las 21:34:41
-- Versión del servidor: 10.6.22-MariaDB-cll-lve
-- Versión de PHP: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ccgardco_naye`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apertura_caja`
--

CREATE TABLE `apertura_caja` (
  `Id_Apertura` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `Fecha_Apertura` datetime NOT NULL DEFAULT current_timestamp(),
  `Fecha_Cierre` datetime DEFAULT NULL,
  `Monto_Inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Estado` enum('ABIERTA','CERRADA') DEFAULT 'ABIERTA',
  `Observaciones` text DEFAULT NULL,
  `Fecha_Registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `apertura_caja`
--

INSERT INTO `apertura_caja` (`Id_Apertura`, `Id_Usuario`, `Fecha_Apertura`, `Fecha_Cierre`, `Monto_Inicial`, `Estado`, `Observaciones`, `Fecha_Registro`) VALUES
(6, 2, '2026-01-07 00:33:02', '2026-01-07 00:33:32', 5.00, 'CERRADA', 'SOBREANTE DE DIAN ANTERIOR', '2026-01-07 05:33:02'),
(7, 2, '2026-01-07 00:34:03', '2026-01-07 01:22:43', 0.00, 'CERRADA', '', '2026-01-07 05:34:03'),
(8, 2, '2026-01-08 22:55:00', '2026-01-08 23:22:11', 0.00, 'CERRADA', '', '2026-01-09 03:55:00'),
(9, 2, '2026-01-08 23:22:35', '2026-01-08 23:27:24', 100.00, 'CERRADA', 'FONDPS', '2026-01-09 04:22:35'),
(10, 2, '2026-01-09 00:01:02', '2026-01-09 00:40:35', 0.00, 'CERRADA', '', '2026-01-09 05:01:02'),
(11, 2, '2026-01-09 00:33:01', '2026-01-09 00:40:53', 0.00, 'CERRADA', '', '2026-01-09 05:33:01'),
(12, 2, '2026-01-09 00:34:07', '2026-01-09 00:41:08', 0.00, 'CERRADA', '', '2026-01-09 05:34:07'),
(13, 2, '2026-01-09 00:36:13', '2026-01-09 00:44:33', 0.00, 'CERRADA', '', '2026-01-09 05:36:13'),
(14, 2, '2026-01-09 00:44:54', '2026-01-09 00:45:11', 0.00, 'CERRADA', '', '2026-01-09 05:44:54'),
(15, 2, '2026-01-09 00:50:52', '2026-01-09 00:51:23', 100.00, 'CERRADA', NULL, '2026-01-09 05:50:52'),
(16, 2, '2026-01-09 00:53:44', '2026-01-09 01:01:50', 100.00, 'CERRADA', NULL, '2026-01-09 05:53:44'),
(17, 2, '2026-01-09 01:04:42', '2026-01-09 01:05:00', 0.00, 'CERRADA', '', '2026-01-09 06:04:42'),
(18, 2, '2026-01-09 01:06:04', '2026-01-09 01:06:30', 562.00, 'CERRADA', '', '2026-01-09 06:06:04'),
(19, 2, '2026-01-09 01:07:06', '2026-01-09 01:07:48', 21.50, 'CERRADA', '', '2026-01-09 06:07:06'),
(20, 2, '2026-01-09 01:08:02', '2026-01-09 01:08:35', 519.01, 'CERRADA', '', '2026-01-09 06:08:02'),
(21, 2, '2026-01-09 01:09:16', '2026-01-09 01:10:06', 0.00, 'CERRADA', '', '2026-01-09 06:09:16'),
(22, 2, '2026-01-09 13:56:47', NULL, 10.00, 'ABIERTA', '', '2026-01-09 18:56:47'),
(23, 2, '2026-01-11 21:20:43', '2026-01-11 21:43:15', 100.00, 'CERRADA', 'adicional', '2026-01-12 02:20:43'),
(24, 2, '2026-01-11 21:43:33', '2026-01-11 21:44:15', 0.00, 'CERRADA', '', '2026-01-12 02:43:33'),
(25, 2, '2026-01-11 21:44:32', '2026-01-11 21:59:15', 0.00, 'CERRADA', '', '2026-01-12 02:44:32'),
(26, 2, '2026-01-11 21:59:15', '2026-01-11 22:00:04', 50.00, 'CERRADA', 'Continuación automática del cierre anterior', '2026-01-12 02:59:15'),
(27, 2, '2026-01-11 22:00:22', '2026-01-11 22:01:26', 3.50, 'CERRADA', '', '2026-01-12 03:00:22'),
(28, 2, '2026-01-11 22:01:38', '2026-01-11 22:52:07', 0.00, 'CERRADA', '', '2026-01-12 03:01:38'),
(29, 2, '2026-01-11 22:52:36', '2026-01-11 23:15:17', 0.00, 'CERRADA', '', '2026-01-12 03:52:36'),
(30, 2, '2026-01-11 23:15:17', '2026-01-11 23:15:42', 3.00, 'CERRADA', 'Continuación automática del cierre anterior', '2026-01-12 04:15:17'),
(31, 2, '2026-01-11 23:21:49', '2026-01-11 23:22:17', 10.00, 'CERRADA', '', '2026-01-12 04:21:49'),
(32, 2, '2026-01-11 23:22:45', '2026-01-11 23:23:41', 0.00, 'CERRADA', '', '2026-01-12 04:22:45'),
(33, 2, '2026-01-11 23:23:41', '2026-01-11 23:24:29', 0.01, 'CERRADA', 'Continuación automática del cierre anterior', '2026-01-12 04:23:41'),
(34, 2, '2026-01-11 23:24:44', NULL, 0.00, 'ABIERTA', '', '2026-01-12 04:24:44'),
(35, 2, '2026-01-12 20:28:43', '2026-01-12 20:37:04', 10.00, 'CERRADA', '', '2026-01-13 01:28:43'),
(36, 2, '2026-01-12 20:37:04', '2026-01-12 22:59:02', 0.00, 'CERRADA', 'Continuación automática del cierre anterior', '2026-01-13 01:37:04'),
(37, 2, '2026-01-12 22:59:02', '2026-01-12 23:02:29', 20.00, 'CERRADA', 'Continuación automática del cierre anterior', '2026-01-13 03:59:02'),
(38, 2, '2026-01-12 23:02:29', '2026-01-12 23:18:51', 32.50, 'CERRADA', 'Continuación automática del cierre anterior', '2026-01-13 04:02:29'),
(39, 2, '2026-01-12 23:23:39', '2026-01-12 23:23:54', 0.00, 'CERRADA', '', '2026-01-13 04:23:39'),
(40, 2, '2026-01-12 23:24:05', '2026-01-12 23:24:15', 0.00, 'CERRADA', '', '2026-01-13 04:24:05'),
(41, 2, '2026-01-12 23:24:15', '2026-01-12 23:26:12', 0.00, 'CERRADA', 'Continuación automática del cierre anterior', '2026-01-13 04:24:15'),
(42, 2, '2026-01-12 23:29:01', '2026-01-12 23:50:54', 0.00, 'CERRADA', '', '2026-01-13 04:29:01'),
(43, 2, '2026-01-12 23:51:45', '2026-01-12 23:53:56', 0.00, 'CERRADA', '', '2026-01-13 04:51:45'),
(44, 2, '2026-01-12 23:53:56', '2026-01-12 23:58:49', 53.50, 'CERRADA', 'Continuación automática del cierre anterior', '2026-01-13 04:53:56'),
(45, 2, '2026-01-12 23:59:10', NULL, 10.00, 'ABIERTA', '', '2026-01-13 04:59:10'),
(46, 2, '2026-01-13 19:07:30', NULL, 0.00, 'ABIERTA', '', '2026-01-14 00:07:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blocked_entities`
--

CREATE TABLE `blocked_entities` (
  `id` int(11) NOT NULL,
  `entity` varchar(150) NOT NULL,
  `entity_type` enum('ip','user_agent') NOT NULL,
  `blocked_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `Id_Categoria` int(11) NOT NULL,
  `Tipo_Categoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`Id_Categoria`, `Tipo_Categoria`) VALUES
(1, 'Ropa'),
(2, 'Alimentos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierre_caja`
--

CREATE TABLE `cierre_caja` (
  `Id_Cierre` int(11) NOT NULL,
  `Id_Apertura` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `Fecha_Cierre` datetime NOT NULL,
  `Monto_Inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Total_Ventas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Numero_Ventas` int(11) NOT NULL DEFAULT 0,
  `Total_Efectivo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Total_Tarjetas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Total_Transferencias` decimal(10,2) DEFAULT 0.00,
  `Gastos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Retiros` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Total_Esperado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Total_Contado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Diferencia` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Tipo_Cierre` enum('DEPOSITO','CONTINUACION') DEFAULT 'DEPOSITO',
  `Observaciones` text DEFAULT NULL,
  `Estado` enum('CERRADA','REVISADO','APROBADO') DEFAULT 'CERRADA',
  `Fecha_Registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cierre_caja`
--

INSERT INTO `cierre_caja` (`Id_Cierre`, `Id_Apertura`, `Id_Usuario`, `Fecha_Cierre`, `Monto_Inicial`, `Total_Ventas`, `Numero_Ventas`, `Total_Efectivo`, `Total_Tarjetas`, `Total_Transferencias`, `Gastos`, `Retiros`, `Total_Esperado`, `Total_Contado`, `Diferencia`, `Tipo_Cierre`, `Observaciones`, `Estado`, `Fecha_Registro`) VALUES
(5, 6, 2, '2026-01-07 00:33:32', 5.00, 0.00, 0, 5.00, 0.00, 0.00, 0.00, 0.00, 5.00, 5.00, 0.00, 'DEPOSITO', '', 'CERRADA', '2026-01-07 05:33:32'),
(6, 7, 2, '2026-01-07 01:22:43', 0.00, 95.00, 4, 72.00, 0.00, 0.00, 0.00, 0.00, 72.00, 72.00, 0.00, 'DEPOSITO', '', 'CERRADA', '2026-01-07 06:22:43'),
(7, 8, 2, '2026-01-08 23:22:11', 0.00, 104.00, 3, 52.00, 0.00, 48.00, 1.50, 2.50, 52.00, 52.00, 0.00, 'DEPOSITO', '', 'CERRADA', '2026-01-09 04:22:11'),
(8, 9, 2, '2026-01-08 23:27:24', 100.00, 232.00, 5, 108.00, 0.00, 120.00, 1.50, 102.50, 108.00, 108.00, 0.00, 'DEPOSITO', '', 'CERRADA', '2026-01-09 04:27:24'),
(15, 10, 2, '2026-01-09 00:29:12', 0.00, 48.00, 2, 24.00, 0.00, 24.00, 100.00, 2.50, -78.50, 24.00, 24.00, 'DEPOSITO', 'Prueba', 'CERRADA', '2026-01-09 05:29:12'),
(16, 11, 2, '2026-01-09 00:33:40', 0.00, 48.00, 2, 24.00, 0.00, 24.00, 100.00, 2.50, -78.50, 24.00, 102.50, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:33:40'),
(17, 12, 2, '2026-01-09 00:35:05', 0.00, 48.00, 2, 78.50, 0.00, 24.00, 100.00, 2.50, -78.50, 78.50, 157.00, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:35:05'),
(18, 13, 2, '2026-01-09 00:36:29', 0.00, 48.00, 2, 100.00, 0.00, 24.00, 100.00, 2.50, -78.50, 100.00, 178.50, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:36:29'),
(19, 10, 2, '2026-01-09 00:40:35', 0.00, 48.00, 2, 0.01, 0.00, 24.00, 562.00, 2.50, -540.50, 0.01, 540.51, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:40:35'),
(20, 11, 2, '2026-01-09 00:40:53', 0.00, 48.00, 2, 50.00, 0.00, 24.00, 562.00, 2.50, -540.50, 50.00, 590.50, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:40:53'),
(21, 12, 2, '2026-01-09 00:41:08', 0.00, 48.00, 2, 0.01, 0.00, 24.00, 562.00, 2.50, -540.50, 0.01, 540.51, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:41:08'),
(22, 13, 2, '2026-01-09 00:44:33', 0.00, 48.00, 2, 0.01, 0.00, 24.00, 562.00, 2.50, -540.50, 0.01, 540.51, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:44:33'),
(23, 14, 2, '2026-01-09 00:45:11', 0.00, 48.00, 2, 0.01, 0.00, 24.00, 562.00, 2.50, -540.50, 0.01, 540.51, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:45:11'),
(24, 15, 2, '2026-01-09 00:51:23', 100.00, 48.00, 2, 20.00, 0.00, 24.00, 562.00, 2.50, -440.50, 20.00, 460.50, 'DEPOSITO', '', 'CERRADA', '2026-01-09 05:51:23'),
(25, 16, 2, '2026-01-09 01:01:50', 100.00, 48.00, 2, 24.00, 0.00, 24.00, 100.00, 2.50, -78.50, 24.00, 24.00, 'DEPOSITO', 'Debug paso 2', 'CERRADA', '2026-01-09 06:01:50'),
(26, 17, 2, '2026-01-09 01:05:00', 0.00, 48.00, 2, 100.00, 0.00, 24.00, 562.00, 2.50, -540.50, 100.00, 640.50, 'DEPOSITO', '', 'CERRADA', '2026-01-09 06:05:00'),
(27, 18, 2, '2026-01-09 01:06:30', 562.00, 48.00, 2, 0.01, 0.00, 24.00, 562.00, 2.50, 21.50, 0.01, -21.49, 'DEPOSITO', '', 'CERRADA', '2026-01-09 06:06:30'),
(28, 19, 2, '2026-01-09 01:07:48', 21.50, 48.00, 2, 0.01, 0.00, 24.00, 562.00, 2.50, -519.00, 0.01, 519.01, 'DEPOSITO', '', 'CERRADA', '2026-01-09 06:07:48'),
(29, 20, 2, '2026-01-09 01:08:35', 519.01, 48.00, 2, 1.00, 0.00, 24.00, 562.00, 2.50, -21.49, 1.00, 22.49, 'DEPOSITO', '', 'CERRADA', '2026-01-09 06:08:35'),
(30, 21, 2, '2026-01-09 01:10:06', 0.00, 48.00, 2, 540.50, 0.00, 24.00, 562.00, 2.50, -540.50, 540.50, 1081.00, 'DEPOSITO', '', 'CERRADA', '2026-01-09 06:10:06'),
(31, 23, 2, '2026-01-11 21:43:15', 100.00, 5.00, 1, 5.00, 0.00, 0.00, 0.00, 0.00, 105.00, 115.00, 10.00, 'DEPOSITO', '', 'CERRADA', '2026-01-12 02:43:15'),
(32, 24, 2, '2026-01-11 21:44:15', 0.00, 5.00, 1, 5.00, 0.00, 0.00, 0.00, 0.00, 5.00, 10.00, 5.00, 'DEPOSITO', '', 'CERRADA', '2026-01-12 02:44:15'),
(33, 25, 2, '2026-01-11 21:59:15', 0.00, 5.00, 1, 5.00, 0.00, 0.00, 1.50, 0.00, 3.50, 50.00, 46.50, 'CONTINUACION', '', 'CERRADA', '2026-01-12 02:59:15'),
(34, 26, 2, '2026-01-11 22:00:04', 50.00, 5.00, 1, 5.00, 0.00, 0.00, 1.50, 0.00, 53.50, 50.00, -3.50, 'DEPOSITO', '', 'CERRADA', '2026-01-12 03:00:04'),
(35, 27, 2, '2026-01-11 22:01:26', 3.50, 5.00, 1, 5.00, 0.00, 0.00, 1.50, 0.00, 7.00, 7.00, 0.00, 'DEPOSITO', '', 'CERRADA', '2026-01-12 03:01:26'),
(36, 28, 2, '2026-01-11 22:52:07', 0.00, 5.00, 1, 5.00, 0.00, 0.00, 9.00, 1.50, -5.50, 20.00, 25.50, 'DEPOSITO', '', 'CERRADA', '2026-01-12 03:52:07'),
(37, 29, 2, '2026-01-11 23:15:17', 0.00, 5.00, 1, 5.00, 0.00, 0.00, 10.50, 3.00, -8.50, 3.00, 11.50, 'CONTINUACION', '', 'CERRADA', '2026-01-12 04:15:17'),
(38, 30, 2, '2026-01-11 23:15:42', 3.00, 5.00, 1, 5.00, 0.00, 0.00, 10.50, 3.00, -5.50, 3.00, 8.50, 'DEPOSITO', '', 'CERRADA', '2026-01-12 04:15:42'),
(39, 31, 2, '2026-01-11 23:22:17', 10.00, 5.00, 1, 5.00, 0.00, 0.00, 10.50, 3.00, 1.50, 10.00, 8.50, 'DEPOSITO', '', 'CERRADA', '2026-01-12 04:22:17'),
(40, 32, 2, '2026-01-11 23:23:41', 0.00, 5.00, 1, 5.00, 0.00, 0.00, 12.00, 3.00, -10.00, 0.01, 10.01, 'CONTINUACION', '', 'CERRADA', '2026-01-12 04:23:41'),
(41, 33, 2, '2026-01-11 23:24:29', 0.01, 5.00, 1, 5.00, 0.00, 0.00, 12.00, 3.00, -9.99, 0.01, 10.00, 'DEPOSITO', '', 'CERRADA', '2026-01-12 04:24:29'),
(42, 35, 2, '2026-01-12 20:37:04', 10.00, 24.00, 1, 24.00, 0.00, 0.00, 3.00, 8.50, 22.50, 0.00, -22.50, 'CONTINUACION', '', 'CERRADA', '2026-01-13 01:37:04'),
(43, 36, 2, '2026-01-12 22:59:02', 0.00, 24.00, 1, 24.00, 0.00, 0.00, 3.00, 8.50, 12.50, 20.00, 7.50, 'CONTINUACION', '', 'CERRADA', '2026-01-13 03:59:02'),
(44, 37, 2, '2026-01-12 23:02:29', 20.00, 24.00, 1, 24.00, 0.00, 0.00, 3.00, 8.50, 32.50, 32.50, 0.00, 'CONTINUACION', '', 'CERRADA', '2026-01-13 04:02:29'),
(45, 38, 2, '2026-01-12 23:18:51', 32.50, 24.00, 1, 24.00, 0.00, 0.00, 3.00, 8.50, 45.00, 0.00, -45.00, 'DEPOSITO', '', 'CERRADA', '2026-01-13 04:18:51'),
(46, 39, 2, '2026-01-12 23:23:54', 0.00, 24.00, 1, 24.00, 0.00, 0.00, 3.00, 8.50, 12.50, 0.00, -12.50, 'DEPOSITO', '', 'CERRADA', '2026-01-13 04:23:54'),
(47, 40, 2, '2026-01-12 23:24:15', 0.00, 24.00, 1, 24.00, 0.00, 0.00, 3.00, 8.50, 12.50, 0.00, -12.50, 'CONTINUACION', '', 'CERRADA', '2026-01-13 04:24:15'),
(48, 41, 2, '2026-01-12 23:26:12', 0.00, 24.00, 1, 24.00, 0.00, 0.00, 3.00, 8.50, 12.50, 0.00, -12.50, 'DEPOSITO', '', 'CERRADA', '2026-01-13 04:26:12'),
(49, 42, 2, '2026-01-12 23:50:54', 0.00, 24.00, 1, 24.00, 0.00, 0.00, 3.00, 8.50, 12.50, 0.00, -12.50, 'DEPOSITO', '', 'CERRADA', '2026-01-13 04:50:54'),
(50, 43, 2, '2026-01-12 23:53:56', 0.00, 48.00, 2, 48.00, 0.00, 0.00, 4.50, 11.50, 32.00, 53.50, 21.50, 'CONTINUACION', '', 'CERRADA', '2026-01-13 04:53:56'),
(51, 44, 2, '2026-01-12 23:58:49', 53.50, 48.00, 2, 48.00, 0.00, 0.00, 4.50, 11.50, 85.50, 111.50, 26.00, 'DEPOSITO', '', 'CERRADA', '2026-01-13 04:58:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `Id_Clientes` int(11) NOT NULL,
  `Nombre_Cliente` varchar(100) NOT NULL,
  `Apellido_Cliente` varchar(100) NOT NULL,
  `Cedula_Cliente` int(15) NOT NULL,
  `Telefono_Cliente` int(10) NOT NULL,
  `Correo_Cliente` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`Id_Clientes`, `Nombre_Cliente`, `Apellido_Cliente`, `Cedula_Cliente`, `Telefono_Cliente`, `Correo_Cliente`) VALUES
(1, 'marimar', '', 1234567890, 1234322333, 'tester@gmail.com'),
(2, 'marimar12', '', 1234567890, 1234322333, 'teste12r@gmail.com'),
(3, 'pepe', '', 2147483647, 2147483647, 'ggogo@dfd.com'),
(4, 'juampa', '', 1234567890, 1234322333, 'testedsdsr@gmail.com'),
(5, 'jonathan', 'miguel cedeño', 1501090730, 968632274, 'digital.keys.tena.test@gmail.com'),
(6, '212', '', 2147483647, 1111111111, 'ewqe@dsad.com'),
(7, 'Jonthan', '', 1501090730, 968632274, 'digital.keys.tena@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descripcion_categoria`
--

CREATE TABLE `descripcion_categoria` (
  `Id_Descripcion_Categoria` int(11) NOT NULL,
  `Id_Categoria` int(11) NOT NULL,
  `Descrip_Categoria` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `descripcion_categoria`
--

INSERT INTO `descripcion_categoria` (`Id_Descripcion_Categoria`, `Id_Categoria`, `Descrip_Categoria`) VALUES
(1, 2, 'Bebidas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desglose_denominaciones`
--

CREATE TABLE `desglose_denominaciones` (
  `Id_Desglose` int(11) NOT NULL,
  `Id_Cierre` int(11) NOT NULL,
  `Denominacion` decimal(10,2) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Tipo` enum('BILLETE','MONEDA') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `Id_Detalle` int(11) NOT NULL,
  `Id_Venta_Detalle` int(11) NOT NULL,
  `Id_Inventario_Detalle` int(11) NOT NULL,
  `Cantidad` int(100) NOT NULL,
  `Precio_Unitario` decimal(20,0) NOT NULL,
  `SubTotal` decimal(20,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`Id_Detalle`, `Id_Venta_Detalle`, `Id_Inventario_Detalle`, `Cantidad`, `Precio_Unitario`, `SubTotal`) VALUES
(1, 1, 4, 1, 24, 24),
(2, 2, 18, 1, 23, 23),
(3, 3, 4, 1, 24, 24),
(4, 4, 4, 1, 24, 24),
(5, 5, 7, 1, 152, 152),
(6, 6, 4, 2, 24, 48),
(7, 7, 4, 15, 24, 358),
(8, 8, 4, 1, 24, 24),
(9, 9, 3, 3, 56, 168),
(10, 10, 9, 15, 75, 1132),
(11, 11, 3, 2, 56, 112),
(12, 12, 3, 3, 56, 168),
(13, 13, 3, 1, 56, 56),
(14, 14, 3, 1, 56, 56),
(15, 15, 18, 1, 23, 23),
(16, 16, 3, 1, 56, 56),
(17, 17, 3, 1, 56, 56),
(18, 18, 9, 1, 75, 75),
(19, 19, 4, 1, 24, 24),
(20, 20, 12, 12, 3, 38),
(21, 21, 18, 1, 23, 23),
(22, 21, 3, 1, 56, 56),
(23, 22, 21, 1, 36, 36),
(24, 23, 6, 1, 117, 117),
(25, 24, 3, 1, 56, 56),
(26, 25, 4, 6, 24, 143),
(27, 26, 4, 3, 24, 72),
(28, 27, 4, 2, 24, 48),
(29, 28, 1, 1, 5, 5),
(30, 29, 4, 2, 24, 48),
(31, 30, 4, 1, 24, 24),
(32, 31, 6, 1, 117, 117),
(36, 46, 58, 2, 2, 4),
(37, 47, 58, 1, 2, 2),
(38, 48, 4, 1, 24, 24),
(39, 49, 52, 1, 33, 33),
(40, 50, 4, 1, 24, 24),
(41, 51, 3, 1, 56, 56),
(42, 52, 4, 1, 24, 24),
(43, 53, 4, 1, 24, 24),
(44, 54, 4, 1, 24, 24),
(45, 55, 18, 1, 23, 23),
(46, 56, 4, 1, 24, 24),
(47, 57, 4, 1, 24, 24),
(48, 58, 3, 1, 56, 56),
(49, 59, 4, 3, 24, 72),
(50, 60, 3, 1, 56, 56),
(51, 61, 4, 1, 24, 24),
(52, 62, 4, 1, 24, 24),
(53, 63, 1, 1, 5, 5),
(54, 64, 4, 1, 24, 24),
(55, 65, 4, 1, 24, 24),
(56, 66, 4, 1, 24, 24),
(57, 67, 4, 1, 24, 24),
(58, 68, 4, 1, 24, 24),
(59, 69, 52, 1, 33, 33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diferencias_caja`
--

CREATE TABLE `diferencias_caja` (
  `Id_Diferencia` int(11) NOT NULL,
  `Id_Cierre` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `Fecha_Diferencia` datetime NOT NULL,
  `Tipo_Diferencia` enum('SOBRANTE','FALTANTE','EXACTO') NOT NULL,
  `Monto_Diferencia` decimal(10,2) NOT NULL,
  `Observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `diferencias_caja`
--

INSERT INTO `diferencias_caja` (`Id_Diferencia`, `Id_Cierre`, `Id_Usuario`, `Fecha_Diferencia`, `Tipo_Diferencia`, `Monto_Diferencia`, `Observaciones`) VALUES
(1, 15, 2, '2026-01-09 00:29:12', 'SOBRANTE', 24.00, 'Prueba'),
(2, 16, 2, '2026-01-09 00:33:40', 'SOBRANTE', 102.50, ''),
(3, 17, 2, '2026-01-09 00:35:05', 'SOBRANTE', 157.00, ''),
(4, 18, 2, '2026-01-09 00:36:29', 'SOBRANTE', 178.50, ''),
(5, 19, 2, '2026-01-09 00:40:35', 'SOBRANTE', 540.51, ''),
(6, 20, 2, '2026-01-09 00:40:53', 'SOBRANTE', 590.50, ''),
(7, 21, 2, '2026-01-09 00:41:08', 'SOBRANTE', 540.51, ''),
(8, 22, 2, '2026-01-09 00:44:33', 'SOBRANTE', 540.51, ''),
(9, 23, 2, '2026-01-09 00:45:11', 'SOBRANTE', 540.51, ''),
(10, 24, 2, '2026-01-09 00:51:23', 'SOBRANTE', 460.50, ''),
(11, 25, 2, '2026-01-09 01:01:50', 'SOBRANTE', 24.00, 'Debug paso 2'),
(12, 26, 2, '2026-01-09 01:05:00', 'SOBRANTE', 640.50, ''),
(13, 27, 2, '2026-01-09 01:06:30', 'FALTANTE', 21.49, ''),
(14, 28, 2, '2026-01-09 01:07:48', 'SOBRANTE', 519.01, ''),
(15, 29, 2, '2026-01-09 01:08:35', 'SOBRANTE', 22.49, ''),
(16, 30, 2, '2026-01-09 01:10:06', 'SOBRANTE', 1081.00, ''),
(17, 31, 2, '2026-01-11 21:43:15', 'SOBRANTE', 10.00, ''),
(18, 32, 2, '2026-01-11 21:44:15', 'SOBRANTE', 5.00, ''),
(19, 33, 2, '2026-01-11 21:59:15', 'SOBRANTE', 46.50, ''),
(20, 34, 2, '2026-01-11 22:00:04', 'FALTANTE', 3.50, ''),
(21, 36, 2, '2026-01-11 22:52:07', 'SOBRANTE', 25.50, ''),
(22, 37, 2, '2026-01-11 23:15:17', 'SOBRANTE', 11.50, ''),
(23, 38, 2, '2026-01-11 23:15:42', 'SOBRANTE', 8.50, ''),
(24, 39, 2, '2026-01-11 23:22:17', 'SOBRANTE', 8.50, ''),
(25, 40, 2, '2026-01-11 23:23:41', 'SOBRANTE', 10.01, ''),
(26, 41, 2, '2026-01-11 23:24:29', 'SOBRANTE', 10.00, ''),
(27, 42, 2, '2026-01-12 20:37:04', 'FALTANTE', 22.50, ''),
(28, 43, 2, '2026-01-12 22:59:02', 'SOBRANTE', 7.50, ''),
(29, 45, 2, '2026-01-12 23:18:51', 'FALTANTE', 45.00, ''),
(30, 46, 2, '2026-01-12 23:23:54', 'FALTANTE', 12.50, ''),
(31, 47, 2, '2026-01-12 23:24:15', 'FALTANTE', 12.50, ''),
(32, 48, 2, '2026-01-12 23:26:12', 'FALTANTE', 12.50, ''),
(33, 49, 2, '2026-01-12 23:50:54', 'FALTANTE', 12.50, ''),
(34, 50, 2, '2026-01-12 23:53:56', 'SOBRANTE', 21.50, ''),
(35, 51, 2, '2026-01-12 23:58:49', 'SOBRANTE', 26.00, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_caja`
--

CREATE TABLE `gastos_caja` (
  `Id_Gasto` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `Id_Apertura_Caja` int(11) DEFAULT NULL,
  `Fecha_Gasto` datetime NOT NULL DEFAULT current_timestamp(),
  `Concepto` varchar(200) NOT NULL,
  `Monto` decimal(10,2) NOT NULL,
  `Categoria` varchar(100) DEFAULT NULL,
  `Observaciones` text DEFAULT NULL,
  `Fecha_Registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos_caja`
--

INSERT INTO `gastos_caja` (`Id_Gasto`, `Id_Usuario`, `Id_Apertura_Caja`, `Fecha_Gasto`, `Concepto`, `Monto`, `Categoria`, `Observaciones`, `Fecha_Registro`) VALUES
(1, 2, NULL, '2026-01-06 22:18:46', 'funda de hielo', 2.50, 'Compras', '', '2026-01-07 03:18:46'),
(2, 2, NULL, '2026-01-06 22:19:40', 'chancho', 15.00, 'Compras', 'un costilla de chancho', '2026-01-07 03:19:40'),
(3, 2, NULL, '2026-01-06 23:31:42', 'compras', 5.00, 'General', '', '2026-01-07 04:31:42'),
(4, 2, 8, '2026-01-08 23:16:12', 'FUNDA DE HIELO', 1.50, 'Compras', 'SIN HIELO', '2026-01-09 04:16:12'),
(5, 2, NULL, '2026-01-08 23:28:56', '104', 104.00, 'Compras', '', '2026-01-09 04:28:56'),
(6, 2, NULL, '2026-01-08 23:29:34', '208', 208.00, 'Servicios', '', '2026-01-09 04:29:34'),
(8, 2, 10, '2026-01-09 00:19:46', 'TESTE', 100.00, 'Compras', '', '2026-01-09 05:19:46'),
(9, 2, 10, '2026-01-09 00:37:05', 'DSDS', 462.00, 'Compras', '', '2026-01-09 05:37:05'),
(10, 2, 25, '2026-01-11 21:58:17', 'conpra de hielo', 1.50, 'Compras', '', '2026-01-12 02:58:17'),
(11, 2, 28, '2026-01-11 22:50:13', 'funda de hielo', 1.50, 'Compras', '', '2026-01-12 03:50:13'),
(12, 2, 28, '2026-01-11 22:51:27', 'funda de hielo', 6.00, '', '', '2026-01-12 03:51:27'),
(13, 2, 29, '2026-01-11 22:53:08', 'funda de hielo', 1.50, 'Compras', '', '2026-01-12 03:53:08'),
(14, 2, 32, '2026-01-11 23:23:12', '1.50', 1.50, 'Compras', '', '2026-01-12 04:23:12'),
(15, 2, 35, '2026-01-12 20:29:51', 'compra de hielo', 1.50, 'Compras', '', '2026-01-13 01:29:51'),
(16, 2, 35, '2026-01-12 20:30:46', 'prestado para hielo', 1.50, 'Compras', '', '2026-01-13 01:30:46'),
(17, 2, 43, '2026-01-12 23:52:51', 'funda de hierlo', 1.50, 'Compras', '', '2026-01-13 04:52:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `Id_Inventario` int(11) NOT NULL,
  `Id_Inventario_Categoria` int(11) NOT NULL,
  `Id_Tipo_Categoria` int(11) DEFAULT NULL,
  `Nombre_Producto` varchar(50) NOT NULL,
  `Margen_Utilidad` int(10) NOT NULL,
  `Precio_Compra_Producto` double NOT NULL,
  `Precio_Venta_Producto` double NOT NULL,
  `Stock_Producto` int(11) NOT NULL,
  `Fecha_Entrada` date NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Codigo_Producto` varchar(100) NOT NULL,
  `Codigo_Barras` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`Id_Inventario`, `Id_Inventario_Categoria`, `Id_Tipo_Categoria`, `Nombre_Producto`, `Margen_Utilidad`, `Precio_Compra_Producto`, `Precio_Venta_Producto`, `Stock_Producto`, `Fecha_Entrada`, `Fecha_Caducidad`, `Codigo_Producto`, `Codigo_Barras`) VALUES
(1, 2, 1, 'cocal cola', 45, 2.5, 4.55, 19, '2025-12-11', '2025-12-01', 'PROD-20251211-0E857A', '2009313579751'),
(2, 2, 1, 'cocal', 65, 43, 122.86, 32, '2025-12-11', '2025-12-12', 'PROD-20251211-568814', '2007160551890'),
(3, 2, 1, 'coca cola1', 43, 32, 56.14, 1249, '2025-12-11', '2025-12-12', 'PROD-20251211-F2837E', '2006624749262'),
(4, 2, 1, 'coca cola', 12, 21, 23.86, 999975, '2025-12-11', '2025-12-11', 'PROD-20251211-55BEE7', '2005804341166'),
(5, 2, 1, 'dasda', 32, 32, 47.06, 112, '2025-12-11', '2025-12-25', 'PROD-20251211-011364', '2008587267647'),
(6, 2, 1, 'test', 54, 54, 117.39, 0, '2025-12-11', '2025-12-11', 'PROD-20251211-C899F7', '2008891242552'),
(7, 2, 1, 'test2', 56, 67, 152.27, 32, '2025-12-11', '2025-12-10', 'PROD-20251211-93AF34', '2005153402488'),
(8, 2, 1, 'Cafe', 25, 1.25, 1.67, 21, '2025-12-11', '2025-12-11', 'PROD-20251212-61981D', '2003201051794'),
(9, 2, 1, '222222', 43, 43, 75.44, 26, '2025-12-11', '2025-12-11', 'PROD-20251212-B6E479', '2002550200198'),
(10, 2, 1, 'Boble tea', 50, 2.5, 5, 12, '2025-12-11', '2026-01-31', 'PROD-20251212-D14A8B', '2004580274927'),
(11, 2, 1, 'tets', 43, 34, 59.65, 65, '2025-12-11', '2025-12-20', 'PROD-20251212-3451E0', '2003069309174'),
(12, 2, 1, 'Pony', 52, 1.5, 3.13, 0, '2025-12-15', '2025-12-15', 'PROD-20251215-D36D58', '2001824872741'),
(13, 2, 1, 'tester', 99, 9, 900, 12, '2025-12-15', '2025-12-15', 'PROD-20251216-C361F1', '2004857859178'),
(14, 2, 1, 'pruebas12', 54, 12, 26.09, 12, '2025-12-16', '2025-12-16', 'PROD-20251216-7CD456', '2002929616346'),
(15, 2, 1, 'diseñonuevo', 54, 12, 26.09, 28, '2025-12-16', '2025-12-16', 'PROD-20251216-3FF823', '2006714099444'),
(16, 2, 1, '123455', 2, 12, 12.24, 29, '2025-12-16', '2025-12-16', 'PROD-20251216-135AD8', '2005252164324'),
(17, 2, 1, 'Pepsi', 12, 1.5, 1.7, 24, '2025-12-16', '2027-12-16', 'PROD-20251217-7AFA2A', '2000281774438'),
(18, 2, 1, 'Coca 5gm', 13, 20, 22.99, 16, '2025-12-16', '2027-12-16', 'PROD-20251217-1E4322', '2002900441905'),
(19, 2, 1, 'kkdd', 1, 21, 21.21, 15, '2025-12-01', '2025-12-01', 'PROD-20251217-C0B238', '2007102815837'),
(20, 2, 1, 'Pruevas235', 54, 15, 32.61, 15, '2025-12-18', '2025-12-27', 'PROD-20251219-3BDECF', '2004138472010'),
(21, 2, 1, 'Testeo12', 58, 15, 35.71, 11, '2025-12-18', '2025-12-21', 'PROD-20251219-B66129', '2003020692888'),
(22, 2, 1, 'Hsjsjd', 15, 15, 17.65, 15, '2025-12-18', '2025-12-18', 'PROD-20251219-97AFFB', '2004515946417'),
(23, 2, 1, 'Ysysjd', 58, 25, 59.52, 21, '2025-12-18', '2025-12-26', 'PROD-20251219-C6E42D', '2004196035622'),
(24, 2, 1, 'Jdhsbd', 15, 15, 17.65, 18, '2025-12-18', '2025-12-28', 'PROD-20251219-F2EBB8', '2006992321893'),
(25, 2, 1, 'Hdhs', 51, 15, 30.61, 15, '2025-12-18', '2025-12-19', 'PROD-20251219-CA3A0B', '2009375907172'),
(26, 2, 1, '2222222s', 43, 21, 36.84, 32, '2025-12-20', '2025-12-20', 'PROD-20251221-470D61', '2006618843815'),
(27, 2, 1, 'Cswgt', 24, 54, 71.05, 54, '2025-12-20', '2025-12-20', 'PROD-20251221-6557D8', '2008058487116'),
(28, 2, 1, 'dsaxsaxs', 9, 21, 23.08, 12, '2025-12-20', '2025-12-20', 'PROD-20251221-4C6AC5', '2006911476536'),
(29, 2, 1, 'Hshs', 55, 25, 55.56, 15, '2025-12-20', '2025-12-20', 'PROD-20251221-165335', '2009361790450'),
(30, 2, 1, 'dsadsa', 32, 12, 17.65, 32, '2025-12-20', '2025-12-20', 'PROD-20251221-93B734', '2007781824861'),
(31, 2, 1, 'dsad', 21, 12, 15.19, 21, '2025-12-21', '2025-12-21', 'PROD-20251221-803C3D', '2000269292589'),
(32, 2, 1, 'Udjdjd', 25, 55, 73.33, 345, '2025-12-21', '2025-12-21', 'PROD-20251221-FDC4EA', '2001139085492'),
(33, 2, 1, 'Gjkn', 58, 55, 130.95, 58, '2025-12-21', '2025-12-21', 'PROD-20251221-A3C15B', '2003173296377'),
(34, 2, 1, 'nlkhv', 59, 58, 141.46, 58, '2025-12-21', '2025-12-21', 'PROD-20251221-73BC2D', '2006446714189'),
(35, 2, 1, 'Blbb', 55, 25, 55.56, 25, '2025-12-21', '2025-12-21', 'PROD-20251221-29FC89', '2000596467148'),
(36, 2, 1, 'hhdjd', 56, 89, 202.27, 668, '2025-12-21', '2025-12-21', 'PROD-20251221-21BFC5', '2002456069653'),
(37, 2, 1, 'jjk', 55, 55, 122.22, 35, '2025-12-21', '2025-12-21', 'PROD-20251221-8DB578', '2008520450235'),
(38, 2, 1, 'Uuu', 55, 25, 55.56, 36, '2025-12-21', '2025-12-21', 'PROD-20251221-FB56F4', '2006721842378'),
(39, 2, 1, 'hhvv', 55, 55, 122.22, 25, '2025-12-21', '2025-12-21', 'PROD-20251221-52C82D', '2007698051824'),
(40, 2, 1, 'ujdkkd', 55, 58, 128.89, 58, '2025-12-21', '2025-12-21', 'PROD-20251221-D385CB', '2008817523314'),
(41, 2, 1, 'hhg', 88, 80, 666.67, 69, '2025-12-21', '2025-12-21', 'PROD-20251221-A45C5C', '2005889333773'),
(42, 2, 1, 'Hdbxkkc', 58, 28, 66.67, 43, '2025-12-23', '2025-12-23', 'PROD-20251224-1F4082', '2002402385653'),
(43, 2, 1, 'Jsjsbbdl', 2, 58, 59.18, 25, '2025-12-23', '2025-12-23', 'PROD-20251224-0BFC0B', '2001964217396'),
(44, 2, 1, 'Tester15', 52, 15, 31.25, 25, '2026-01-02', '2026-01-31', 'PROD-20260102-E1D037', '2009958234138'),
(45, 2, 1, 'Twdtsbjk', 25, 25, 33.33, 25, '2026-01-02', '2026-01-31', 'PROD-20260103-C9E0F9', '2009391287418'),
(46, 2, 1, 'conejos', 26, 21, 28.38, 21, '2026-01-04', '2026-01-31', 'PROD-20260104-2588B3', '2002550200488'),
(47, 2, 1, 'ponyt', 32, 23, 33.82, 54, '2026-01-04', '2026-01-31', 'PROD-20260104-B43EB1', '8905915302794'),
(48, 2, 1, 'cocaina', 55, 12, 26.67, 100, '2026-01-04', '2026-01-31', 'PROD-20260104-CE9638', '8906619652604'),
(49, 2, 1, 'ettets', 34, 64, 96.97, 30, '2026-01-04', '2026-01-16', 'PROD-20260104-5FD043', '8905199798108'),
(50, 2, 1, 'Hsybfjfk', 28, 25, 34.72, 25, '2026-01-05', '2026-01-17', 'PROD-20260105-ED401D', '8906040715787'),
(51, 2, 1, 'testchinl', 12, 25, 28.41, 8, '2026-01-05', '2026-01-31', 'PROD-20260106-AD1CBC', '6920907818901'),
(52, 2, 1, 'aguatesyer', 25, 25, 33.33, 23, '2026-01-05', '2026-01-31', 'PROD-20260106-32CA75', '8850389111628'),
(53, 2, 1, 'tragobbb', 25, 155, 206.67, 25, '2026-01-05', '2026-01-31', 'PROD-20260106-5047C7', '8801100132569'),
(54, 2, 1, 'sobres', 55, 12, 26.67, 25, '2026-01-05', '2026-01-31', 'PROD-20260106-F16610', '6920548862011'),
(55, 2, 1, 'fruit fun', 25, 25, 33.33, 25, '2026-01-05', '2026-01-31', 'PROD-20260106-A524B9', '6901180911510'),
(56, 2, 1, 'fideos', 25, 25, 33.33, 25, '2026-01-05', '2026-01-31', 'PROD-20260106-6231D6', '6900873039005'),
(57, 2, 1, 'fideos3', 25, 25, 33.33, 36, '2026-01-05', '2026-01-31', 'PROD-20260106-413B62', '6900873097043'),
(58, 2, 1, 'amarrillo', 25, 1.58, 2.11, 22, '2026-01-05', '2026-01-31', 'PROD-20260106-484763', '6937962130183');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `Id_Login` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(150) NOT NULL,
  `Id_Login_Usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`Id_Login`, `Username`, `Password`, `Id_Login_Usuario`) VALUES
(2, 'admin', '$2y$10$M1AAXRX.kJds/Rx3H1w/OuZ7N3TRV2qashp9e9X52UkwjZPRckBl6', 1),
(3, 'jonathan', '$2y$10$ZWW504xy71t2pMCItVduNOGXjisyssojU9O3MbFr0rsV30QOXBxLS', 2),
(4, 'Naye12.', '1233213213', 3),
(8, 'kose', '$2y$10$ElepOj4hpwb8akMXQaN9V.LDBUCl8JqxS7lfLCnENK4EqlGJebYpq', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 1,
  `last_attempt` datetime NOT NULL DEFAULT current_timestamp(),
  `locked_until` datetime DEFAULT NULL,
  `user_agent` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `ip`, `attempts`, `last_attempt`, `locked_until`, `user_agent`) VALUES
(1, 'cxz', '51.15.24.226', 1, '2025-12-08 05:46:25', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(2, 's', '212.83.137.177', 1, '2025-12-08 05:51:42', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(3, 'dsds', '212.83.137.177', 1, '2025-12-08 05:51:51', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(4, '1', '212.83.137.177', 2, '2025-12-08 05:52:02', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(5, '11', '51.15.24.226', 2, '2025-12-08 05:56:55', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(6, '111', '51.15.24.226', 1, '2025-12-08 05:57:11', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(7, '11', '212.83.137.177', 5, '2025-12-08 06:18:27', '2025-12-08 06:19:27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(8, '111', '212.83.137.177', 1, '2025-12-08 06:08:41', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(9, 'admin', '51.158.254.162', 1, '2025-12-11 04:09:29', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(10, 'admin', '51.158.254.163', 5, '2025-12-11 04:21:07', '2025-12-10 23:22:07', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(12, 'admina', '181.199.54.166', 1, '2025-12-11 04:15:38', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(21, 'jonathan', '181.112.92.165', 1, '2025-12-24 18:50:45', NULL, 'Mozilla/5.0 (Linux; Android 15; Infinix X6850 Build/AP3A.240905.015.A2; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.34 Mobile Safari/537.36'),
(22, 'admin', '172.225.173.245', 1, '2026-01-02 15:35:42', NULL, 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1'),
(23, '12345', '148.227.107.82', 2, '2026-01-10 14:16:05', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36'),
(24, '\" or 1=1', '148.227.107.82', 1, '2026-01-10 14:15:32', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36'),
(25, '\' or 1=1', '148.227.107.82', 1, '2026-01-10 14:15:40', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts_log`
--

CREATE TABLE `login_attempts_log` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `user_agent` varchar(200) DEFAULT NULL,
  `attempt_time` datetime NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `Id_Movimiento` int(11) NOT NULL,
  `Id_Producto` int(11) NOT NULL,
  `Tipo_Movimiento` enum('INGRESO','SALIDA','AJUSTE') NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Stock_Anterior` int(11) NOT NULL,
  `Stock_Nuevo` int(11) NOT NULL,
  `Notas` text DEFAULT NULL,
  `Usuario_Id` int(11) DEFAULT NULL,
  `Fecha_Movimiento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_inventario`
--

INSERT INTO `movimientos_inventario` (`Id_Movimiento`, `Id_Producto`, `Tipo_Movimiento`, `Cantidad`, `Stock_Anterior`, `Stock_Nuevo`, `Notas`, `Usuario_Id`, `Fecha_Movimiento`) VALUES
(1, 1, 'INGRESO', 21, 0, 21, 'Producto creado: cocal cola (Código de barras: 2009313579751)', 1, '2025-12-11 05:44:06'),
(2, 2, 'INGRESO', 32, 0, 32, 'Producto creado: cocal (Código de barras: 2007160551890)', 1, '2025-12-11 05:46:57'),
(3, 3, 'INGRESO', 12, 0, 12, 'Producto creado: coca cola1 (Código de barras: 2006624749262)', 1, '2025-12-11 05:48:02'),
(4, 4, 'INGRESO', 21, 0, 21, 'Producto creado: coca cola (Código de barras: 2005804341166)', 1, '2025-12-11 05:50:14'),
(5, 5, 'INGRESO', 23, 0, 23, 'Producto creado: dasda (Código de barras: 2008587267647)', 1, '2025-12-11 06:16:57'),
(6, 5, 'INGRESO', 21, 23, 44, '21', 1, '2025-12-11 07:12:25'),
(7, 5, 'INGRESO', 56, 44, 100, '3', 1, '2025-12-11 07:13:03'),
(8, 6, 'INGRESO', 2, 0, 2, 'Producto creado: test (Código de barras: 2008891242552)', 1, '2025-12-11 07:14:10'),
(9, 7, 'INGRESO', 2, 0, 2, 'Producto creado: test2 (Código de barras: 2005153402488)', 1, '2025-12-11 07:19:19'),
(10, 7, 'INGRESO', 2, 2, 4, 'Hhs', 1, '2025-12-11 07:41:17'),
(11, 7, 'INGRESO', 5, 4, 9, '', 1, '2025-12-11 07:41:23'),
(12, 5, 'INGRESO', 12, 100, 112, 'Hshs', 1, '2025-12-11 07:41:47'),
(13, 8, 'INGRESO', 21, 0, 21, 'Producto creado: Cafe (Código de barras: 2003201051794)', 1, '2025-12-12 00:06:18'),
(14, 9, 'INGRESO', 43, 0, 43, 'Producto creado: 222222 (Código de barras: 2002550200198)', 1, '2025-12-12 03:01:14'),
(15, 10, 'INGRESO', 10, 0, 10, 'Producto creado: Boble tea (Código de barras: 2004580274927)', 1, '2025-12-12 03:30:02'),
(16, 10, 'INGRESO', 2, 10, 12, '', 1, '2025-12-12 03:30:30'),
(17, 11, 'INGRESO', 23, 0, 23, 'Producto creado: tets (Código de barras: 2003069309174)', 1, '2025-12-12 03:57:15'),
(18, 12, 'INGRESO', 12, 0, 12, 'Producto creado: Pony (Código de barras: 2001824872741)', 1, '2025-12-15 14:00:28'),
(19, 11, 'INGRESO', 21, 23, 44, 'tese', 1, '2025-12-16 04:26:52'),
(20, 11, 'INGRESO', 21, 44, 65, '', 1, '2025-12-16 04:27:02'),
(21, 13, 'INGRESO', 12, 0, 12, 'Producto creado: tester (Código de barras: 2004857859178)', 1, '2025-12-16 04:55:16'),
(22, 14, 'INGRESO', 12, 0, 12, 'Producto creado: pruebas12 (Código de barras: 2002929616346)', 1, '2025-12-16 05:12:13'),
(23, 6, 'INGRESO', 12, 2, 14, '', 1, '2025-12-16 05:24:17'),
(24, 7, 'INGRESO', 12, 9, 21, '', 1, '2025-12-16 05:24:51'),
(25, 6, 'INGRESO', 12, 14, 26, '', 1, '2025-12-16 05:25:07'),
(26, 7, 'INGRESO', 12, 21, 33, '21', 1, '2025-12-16 05:25:18'),
(27, 15, 'INGRESO', 1, 0, 1, 'Producto creado: diseñonuevo (Código de barras: 2006714099444)', 1, '2025-12-16 05:39:28'),
(28, 16, 'INGRESO', 12, 0, 12, 'Producto creado: 123455 (Código de barras: 2005252164324)', 1, '2025-12-16 05:40:40'),
(29, 16, 'INGRESO', 12, 12, 24, '21', 1, '2025-12-16 05:50:22'),
(30, 15, 'INGRESO', 2, 1, 3, '', 2, '2025-12-17 01:59:19'),
(31, 15, 'INGRESO', 4, 3, 7, '', 2, '2025-12-17 02:36:34'),
(32, 16, 'INGRESO', 3, 24, 27, '', 2, '2025-12-17 02:45:42'),
(33, 16, 'INGRESO', 2, 27, 29, '', 2, '2025-12-17 02:45:55'),
(34, 15, 'INGRESO', 21, 7, 28, '', 2, '2025-12-17 02:46:09'),
(35, 17, 'INGRESO', 12, 0, 12, 'Producto creado: Pepsi (Código de barras: 2000281774438)', 5, '2025-12-17 02:46:18'),
(36, 17, 'INGRESO', 12, 12, 24, '', 2, '2025-12-17 03:01:04'),
(37, 18, 'INGRESO', 20, 0, 20, 'Producto creado: Coca 5gm (Código de barras: 2002900441905)', 5, '2025-12-17 03:11:56'),
(38, 19, 'INGRESO', 1, 0, 1, 'Producto creado: kkdd (Código de barras: 2007102815837)', 2, '2025-12-17 03:49:38'),
(39, 19, 'INGRESO', 2, 1, 3, '', 2, '2025-12-17 04:55:37'),
(40, 19, 'INGRESO', 12, 3, 15, '', 5, '2025-12-17 18:07:57'),
(41, 20, 'INGRESO', 15, 0, 15, 'Producto creado: Pruevas235 (Código de barras: 2004138472010)', 1, '2025-12-19 03:28:41'),
(42, 21, 'INGRESO', 12, 0, 12, 'Producto creado: Testeo12 (Código de barras: 2003020692888)', 1, '2025-12-19 03:30:21'),
(43, 22, 'INGRESO', 15, 0, 15, 'Producto creado: Hsjsjd (Código de barras: 2004515946417)', 1, '2025-12-19 03:31:42'),
(44, 23, 'INGRESO', 21, 0, 21, 'Producto creado: Ysysjd (Código de barras: 2004196035622)', 1, '2025-12-19 03:34:11'),
(45, 24, 'INGRESO', 18, 0, 18, 'Producto creado: Jdhsbd (Código de barras: 2006992321893)', 1, '2025-12-19 03:37:55'),
(46, 25, 'INGRESO', 15, 0, 15, 'Producto creado: Hdhs (Código de barras: 2009375907172)', 1, '2025-12-19 03:39:07'),
(47, 26, 'INGRESO', 32, 0, 32, 'Producto creado: 2222222s (Código de barras: 2006618843815)', 1, '2025-12-21 04:37:53'),
(48, 27, 'INGRESO', 54, 0, 54, 'Producto creado: Cswgt (Código de barras: 2008058487116)', 1, '2025-12-21 04:40:15'),
(49, 28, 'INGRESO', 12, 0, 12, 'Producto creado: dsaxsaxs (Código de barras: 2006911476536)', 1, '2025-12-21 04:52:17'),
(50, 29, 'INGRESO', 15, 0, 15, 'Producto creado: Hshs (Código de barras: 2009361790450)', 1, '2025-12-21 04:52:57'),
(51, 30, 'INGRESO', 32, 0, 32, 'Producto creado: dsadsa (Código de barras: 2007781824861)', 1, '2025-12-21 04:53:50'),
(52, 31, 'INGRESO', 21, 0, 21, 'Producto creado: dsad (Código de barras: 2000269292589)', 1, '2025-12-21 05:02:46'),
(53, 32, 'INGRESO', 345, 0, 345, 'Producto creado: Udjdjd (Código de barras: 2001139085492)', 1, '2025-12-21 05:03:54'),
(54, 33, 'INGRESO', 58, 0, 58, 'Producto creado: Gjkn (Código de barras: 2003173296377)', 1, '2025-12-21 13:44:00'),
(55, 34, 'INGRESO', 58, 0, 58, 'Producto creado: nlkhv (Código de barras: 2006446714189)', 1, '2025-12-21 13:48:46'),
(56, 35, 'INGRESO', 25, 0, 25, 'Producto creado: Blbb (Código de barras: 2000596467148)', 1, '2025-12-21 13:56:42'),
(57, 36, 'INGRESO', 668, 0, 668, 'Producto creado: hhdjd (Código de barras: 2002456069653)', 1, '2025-12-21 13:58:16'),
(58, 37, 'INGRESO', 35, 0, 35, 'Producto creado: jjk (Código de barras: 2008520450235)', 1, '2025-12-21 14:07:41'),
(59, 38, 'INGRESO', 36, 0, 36, 'Producto creado: Uuu (Código de barras: 2006721842378)', 1, '2025-12-21 14:08:41'),
(60, 39, 'INGRESO', 25, 0, 25, 'Producto creado: hhvv (Código de barras: 2007698051824)', 1, '2025-12-21 14:11:18'),
(61, 40, 'INGRESO', 58, 0, 58, 'Producto creado: ujdkkd (Código de barras: 2008817523314)', 1, '2025-12-21 14:13:44'),
(62, 41, 'INGRESO', 69, 0, 69, 'Producto creado: hhg (Código de barras: 2005889333773)', 1, '2025-12-21 14:20:40'),
(63, 42, 'INGRESO', 28, 0, 28, 'Producto creado: Hdbxkkc (Código de barras: 2002402385653)', 1, '2025-12-24 04:04:06'),
(64, 42, 'INGRESO', 15, 28, 43, '', 1, '2025-12-24 04:05:30'),
(65, 6, 'INGRESO', 2, 0, 2, '', 1, '2025-12-24 04:05:41'),
(66, 4, 'INGRESO', 1000000, 0, 1000000, '', 1, '2025-12-24 04:05:51'),
(67, 4, 'INGRESO', 12, 1000000, 1000012, '', 1, '2025-12-24 04:06:01'),
(68, 3, 'INGRESO', 1254, 0, 1254, 'Fuera de emisiones', 1, '2025-12-24 04:06:23'),
(69, 43, 'INGRESO', 25, 0, 25, 'Producto creado: Jsjsbbdl (Código de barras: 2001964217396)', 1, '2025-12-24 04:12:06'),
(70, 44, 'INGRESO', 25, 0, 25, 'Producto creado: Tester15 (Código de barras: 2009958234138)', 1, '2026-01-02 08:49:05'),
(71, 45, 'INGRESO', 25, 0, 25, 'Producto creado: Twdtsbjk (Código de barras: 2009391287418)', 1, '2026-01-02 19:51:24'),
(72, 46, 'INGRESO', 21, 0, 21, 'Producto creado: conejos (Código de barras: 2002550200488)', 1, '2026-01-04 00:53:58'),
(73, 47, 'INGRESO', 54, 0, 54, 'Producto creado: ponyt (Código de barras: 8905915302794)', 1, '2026-01-04 01:34:52'),
(74, 48, 'INGRESO', 21, 0, 21, 'Producto creado: cocaina (Código de barras: 8906619652604)', 1, '2026-01-04 01:43:12'),
(75, 48, 'INGRESO', 43, 21, 64, '32', 1, '2026-01-04 02:11:06'),
(76, 48, 'INGRESO', 36, 64, 100, 'loten uvo', 1, '2026-01-04 02:11:30'),
(77, 49, 'INGRESO', 32, 0, 32, 'Producto creado: ettets (Código de barras: 8905199798108)', 1, '2026-01-04 02:12:16'),
(78, 50, 'INGRESO', 25, 0, 25, 'Producto creado: Hsybfjfk (Código de barras: 8906040715787)', 1, '2026-01-05 13:11:21'),
(79, 51, 'INGRESO', 8, 0, 8, 'Producto creado: testchinl (Código de barras: 6920907818901)', 1, '2026-01-05 19:22:38'),
(80, 52, 'INGRESO', 25, 0, 25, 'Producto creado: aguatesyer (Código de barras: 8850389111628)', 1, '2026-01-05 19:25:13'),
(81, 53, 'INGRESO', 25, 0, 25, 'Producto creado: tragobbb (Código de barras: 8801100132569)', 1, '2026-01-05 19:27:18'),
(82, 54, 'INGRESO', 25, 0, 25, 'Producto creado: sobres (Código de barras: 6920548862011)', 1, '2026-01-05 19:31:46'),
(83, 55, 'INGRESO', 25, 0, 25, 'Producto creado: fruit fun (Código de barras: 6901180911510)', 1, '2026-01-05 19:32:59'),
(84, 56, 'INGRESO', 25, 0, 25, 'Producto creado: fideos (Código de barras: 6900873039005)', 1, '2026-01-05 19:34:02'),
(85, 57, 'INGRESO', 36, 0, 36, 'Producto creado: fideos3 (Código de barras: 6900873097043)', 1, '2026-01-05 19:35:43'),
(86, 58, 'INGRESO', 25, 0, 25, 'Producto creado: amarrillo (Código de barras: 6937962130183)', 1, '2026-01-05 19:44:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset`
--

CREATE TABLE `password_reset` (
  `Id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(200) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_reset`
--

INSERT INTO `password_reset` (`Id`, `email`, `token`, `expires_at`) VALUES
(13, 'digital.keys.tena@gmail.com', '933606', '2025-12-16 21:00:55'),
(14, 'jamilex_cueva@hotmail.com', '177539', '2025-12-16 21:06:45'),
(15, 'jonathan20010623@gmail.com', '791890', '2025-12-16 21:40:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `Id_Proveedor` int(11) NOT NULL,
  `Nombre_Proveedor` varchar(100) NOT NULL,
  `Apellido_Proveedor` varchar(100) NOT NULL,
  `Correo_Proveedor` varchar(100) NOT NULL,
  `Telefono_Proveedor` int(10) NOT NULL,
  `Cedula_Proveedor` int(15) NOT NULL,
  `cuentas_Bancaria_Proveedor` text NOT NULL,
  `Id_Proveedor_Categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retiros_caja`
--

CREATE TABLE `retiros_caja` (
  `Id_Retiro` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `Id_Apertura_Caja` int(11) DEFAULT NULL,
  `Fecha_Retiro` datetime NOT NULL DEFAULT current_timestamp(),
  `Monto` decimal(10,2) NOT NULL,
  `Motivo` varchar(200) NOT NULL,
  `Autorizado_Por` int(11) DEFAULT NULL,
  `Observaciones` text DEFAULT NULL,
  `Fecha_Registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `retiros_caja`
--

INSERT INTO `retiros_caja` (`Id_Retiro`, `Id_Usuario`, `Id_Apertura_Caja`, `Fecha_Retiro`, `Monto`, `Motivo`, `Autorizado_Por`, `Observaciones`, `Fecha_Registro`) VALUES
(1, 2, NULL, '2026-01-06 22:20:14', 1.50, 'adelantos', NULL, 'adelanto', '2026-01-07 03:20:14'),
(2, 2, 8, '2026-01-08 23:17:04', 2.50, 'ALMUERSO', NULL, 'COMIDA', '2026-01-09 04:17:04'),
(3, 2, 9, '2026-01-08 23:25:42', 100.00, 'FONDOS', NULL, '', '2026-01-09 04:25:42'),
(4, 2, 10, '2026-01-09 00:20:00', 2.50, 'ALMUERSO', NULL, '', '2026-01-09 05:20:00'),
(5, 2, 28, '2026-01-11 22:02:08', 1.50, 'debolucion', NULL, '', '2026-01-12 03:02:08'),
(6, 2, 29, '2026-01-11 22:55:22', 1.50, 'almuerso', NULL, '', '2026-01-12 03:55:22'),
(7, 2, 35, '2026-01-12 20:30:20', 8.50, 'adelantos', NULL, '', '2026-01-13 01:30:20'),
(8, 2, 43, '2026-01-12 23:53:03', 3.00, 'almuerso', NULL, '', '2026-01-13 04:53:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `Id_Rol` int(11) NOT NULL,
  `Descripcion_Rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`Id_Rol`, `Descripcion_Rol`) VALUES
(1, 'Administrador'),
(2, 'Vendedor'),
(3, 'Inventario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `saldo_acumulado_vendedor`
--

CREATE TABLE `saldo_acumulado_vendedor` (
  `Id_Saldo` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `Saldo_Actual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Fecha_Actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `saldo_acumulado_vendedor`
--

INSERT INTO `saldo_acumulado_vendedor` (`Id_Saldo`, `Id_Usuario`, `Saldo_Actual`, `Fecha_Actualizacion`) VALUES
(1, 2, 5.00, '2026-01-11 21:44:15'),
(2, 2, 5.00, '2026-01-11 21:44:15'),
(3, 2, 5.00, '2026-01-11 21:44:15'),
(4, 2, 46.50, '2026-01-11 21:59:15'),
(5, 2, -3.50, '2026-01-11 22:00:04'),
(6, 2, 0.00, '2026-01-11 22:01:26'),
(7, 2, 25.50, '2026-01-11 22:52:07'),
(8, 2, 11.50, '2026-01-11 23:15:17'),
(9, 2, 8.50, '2026-01-11 23:15:42'),
(10, 2, 8.50, '2026-01-11 23:22:17'),
(11, 2, 10.01, '2026-01-11 23:23:41'),
(12, 2, 10.00, '2026-01-11 23:24:29'),
(13, 2, -22.50, '2026-01-12 20:37:04'),
(14, 2, 7.50, '2026-01-12 22:59:02'),
(15, 2, 0.00, '2026-01-12 23:02:29'),
(16, 2, -45.00, '2026-01-12 23:18:51'),
(17, 2, -12.50, '2026-01-12 23:23:54'),
(18, 2, -12.50, '2026-01-12 23:24:15'),
(19, 2, -12.50, '2026-01-12 23:26:12'),
(20, 2, -12.50, '2026-01-12 23:50:54'),
(21, 2, 21.50, '2026-01-12 23:53:56'),
(22, 2, 26.00, '2026-01-12 23:58:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `Id_Usuario` int(11) NOT NULL,
  `Nombre_Usuario` varchar(100) NOT NULL,
  `Correo_Usuario` varchar(150) NOT NULL,
  `Id_Usuario_Rol` int(11) NOT NULL,
  `Apellido_Usuario` varchar(100) DEFAULT NULL,
  `Telefono_Usuario` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`Id_Usuario`, `Nombre_Usuario`, `Correo_Usuario`, `Id_Usuario_Rol`, `Apellido_Usuario`, `Telefono_Usuario`) VALUES
(1, 'jonathan', 'digital.keys.tena@gmail.com', 3, 'cedeño', '0968632274'),
(2, 'Jonathan2', 'jonathan20010623@gmail.com', 2, 'cedeño', '0968632274'),
(3, 'Nayely', 'jamilex@hotmail.com', 3, 'cueva', '0959589711'),
(5, 'kose', 'kkose2687@gmail.com', 3, 'el mandarina', '0987676321');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `Id_Venta` int(11) NOT NULL,
  `Id_Cliente_Venta` int(11) DEFAULT NULL,
  `Fecha_Venta` datetime NOT NULL,
  `Total_Venta` decimal(20,0) NOT NULL,
  `Metodo_Pago` varchar(50) DEFAULT 'EFECTIVO',
  `Numero_Comprobante` varchar(50) DEFAULT NULL,
  `Id_Usuario_Vendedor` int(11) DEFAULT NULL,
  `Id_Apertura_Caja` int(11) DEFAULT NULL,
  `Subtotal` decimal(10,2) DEFAULT 0.00,
  `Descuento` decimal(10,2) DEFAULT 0.00,
  `Impuesto` decimal(10,2) DEFAULT 0.00,
  `Notas` text DEFAULT NULL,
  `Estado_Venta` enum('PENDIENTE','COMPLETADA','CANCELADA') DEFAULT 'COMPLETADA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`Id_Venta`, `Id_Cliente_Venta`, `Fecha_Venta`, `Total_Venta`, `Metodo_Pago`, `Numero_Comprobante`, `Id_Usuario_Vendedor`, `Id_Apertura_Caja`, `Subtotal`, `Descuento`, `Impuesto`, `Notas`, `Estado_Venta`) VALUES
(1, 4, '2025-12-18 00:00:00', 0, 'TARJETA', NULL, 2, NULL, 0.00, 0.00, 0.00, '', 'COMPLETADA'),
(2, 1, '2025-12-18 00:00:00', 0, 'TRANSFERENCIA', NULL, 2, NULL, 0.00, 0.00, 0.00, 'tets', 'COMPLETADA'),
(3, 4, '2025-12-18 00:00:00', 0, 'TARJETA', NULL, 2, NULL, 0.00, 0.03, 0.00, 'ttes', 'COMPLETADA'),
(4, 1, '2025-12-18 00:00:00', 27, 'EFECTIVO', NULL, 2, NULL, 23.86, 0.00, 3.58, '', 'COMPLETADA'),
(5, 4, '2025-12-18 00:00:00', 175, 'TARJETA', NULL, 2, NULL, 152.27, 0.00, 22.84, 'test2001', 'COMPLETADA'),
(6, 1, '2025-12-18 00:00:00', 38, 'TARJETA', NULL, 2, NULL, 47.72, 15.00, 4.91, 'dasd', 'COMPLETADA'),
(7, 5, '2025-12-18 00:00:00', 412, 'TARJETA', NULL, 2, NULL, 357.90, 0.00, 53.68, 'pro', 'COMPLETADA'),
(8, 5, '2025-12-18 00:00:00', 27, 'TARJETA', NULL, 2, NULL, 23.86, 0.00, 3.58, 'sad', 'COMPLETADA'),
(9, 5, '2025-12-18 00:00:00', 21, 'EFECTIVO', NULL, 2, NULL, 168.42, 150.00, 2.76, 'dsad', 'COMPLETADA'),
(10, 2, '2025-12-18 00:00:00', 1301, 'TARJETA', NULL, 2, NULL, 1131.60, 0.00, 169.74, '', 'COMPLETADA'),
(11, 5, '2025-12-18 00:00:00', 129, 'TRANSFERENCIA', NULL, 2, NULL, 112.28, 0.00, 16.84, '', 'COMPLETADA'),
(12, 5, '2025-12-19 00:00:00', 194, 'EFECTIVO', NULL, 2, NULL, 168.42, 0.00, 25.26, '', 'COMPLETADA'),
(13, 5, '2025-12-19 00:00:00', 65, 'TARJETA', NULL, 2, NULL, 56.14, 0.00, 8.42, 'Hgf', 'COMPLETADA'),
(14, 5, '2025-12-19 00:00:00', 59, 'TARJETA', NULL, 2, NULL, 56.14, 5.00, 7.67, 'tes', 'COMPLETADA'),
(15, 6, '2025-12-19 00:00:00', 21, 'EFECTIVO', NULL, 2, NULL, 22.99, 5.00, 2.70, 'tas', 'COMPLETADA'),
(16, 5, '2025-12-19 00:00:00', 59, 'EFECTIVO', NULL, 2, NULL, 56.14, 5.00, 7.67, 'Hshsj', 'COMPLETADA'),
(17, 6, '2025-12-24 00:00:00', 65, 'EFECTIVO', NULL, 2, NULL, 56.14, 0.00, 8.42, '', 'COMPLETADA'),
(18, 5, '2025-12-24 00:00:00', 87, 'TARJETA', NULL, 2, NULL, 75.44, 0.00, 11.32, 'Jjdjjd', 'COMPLETADA'),
(19, 5, '2025-12-27 00:00:00', 27, 'TARJETA', NULL, 2, NULL, 23.86, 0.00, 3.58, 'dsad', 'COMPLETADA'),
(20, 3, '2025-12-29 00:00:00', 41, 'EFECTIVO', NULL, 2, NULL, 37.56, 2.00, 5.33, 'tester', 'COMPLETADA'),
(21, 3, '2025-12-29 00:00:00', 91, 'EFECTIVO', NULL, 2, NULL, 79.13, 0.00, 11.87, '', 'COMPLETADA'),
(22, 6, '2025-12-29 00:00:00', 41, 'TARJETA', NULL, 2, NULL, 35.71, 0.00, 5.36, 'notese', 'COMPLETADA'),
(23, 5, '2025-12-28 00:00:00', 135, 'EFECTIVO', NULL, 2, NULL, 117.39, 0.00, 17.61, 'fecha 28', 'COMPLETADA'),
(24, 5, '2025-12-29 00:00:00', 65, 'TRANSFERENCIA', NULL, 2, NULL, 56.14, 0.00, 8.42, 'banco austo', 'COMPLETADA'),
(25, 5, '2025-12-29 00:00:00', 165, 'TRANSFERENCIA', 'tsus-8277662627', 2, NULL, 143.16, 0.06, 21.46, 'banco de pichincha', 'COMPLETADA'),
(26, 5, '2025-12-29 00:00:00', 82, 'EFECTIVO', NULL, 2, NULL, 71.58, 0.00, 10.74, 'Banco pichinca', 'COMPLETADA'),
(27, 4, '2025-12-29 00:00:00', 55, 'TRANSFERENCIA', 'Bbdond', 2, NULL, 47.72, 0.00, 7.16, 'Tester29', 'COMPLETADA'),
(28, 5, '2025-12-30 00:00:00', 5, 'TRANSFERENCIA', 'Jsjsjsjs s', 2, NULL, 4.55, 0.00, 0.68, '', 'COMPLETADA'),
(29, 7, '2026-01-02 00:00:00', 55, 'EFECTIVO', NULL, 2, NULL, 47.72, 0.00, 7.16, '', 'COMPLETADA'),
(30, 2, '2026-01-03 00:00:00', 24, 'TRANSFERENCIA', 'mdudidj', 2, NULL, 23.86, 0.00, 0.00, 'banco austro', 'COMPLETADA'),
(31, 6, '2026-01-03 00:09:00', 117, 'EFECTIVO', NULL, 2, NULL, 117.39, 0.00, 0.00, '', 'COMPLETADA'),
(34, NULL, '2026-01-04 23:30:08', 50, 'EFECTIVO', NULL, 1, NULL, 0.00, 0.00, 0.00, NULL, 'COMPLETADA'),
(35, NULL, '2026-01-04 23:30:08', 30, 'TARJETA', NULL, 1, NULL, 0.00, 0.00, 0.00, NULL, 'COMPLETADA'),
(36, NULL, '2026-01-04 23:30:08', 20, 'EFECTIVO', NULL, 1, NULL, 0.00, 0.00, 0.00, NULL, 'COMPLETADA'),
(43, NULL, '2026-01-05 10:00:00', 50, 'EFECTIVO', NULL, 2, NULL, 0.00, 0.00, 0.00, NULL, 'COMPLETADA'),
(44, NULL, '2026-01-05 11:00:00', 30, 'TARJETA', NULL, 2, NULL, 0.00, 0.00, 0.00, NULL, 'COMPLETADA'),
(45, NULL, '2026-01-05 12:00:00', 25, 'EFECTIVO', NULL, 2, NULL, 0.00, 0.00, 0.00, NULL, 'COMPLETADA'),
(46, 5, '2026-01-05 19:48:00', 4, 'TRANSFERENCIA', 'hdhsj', 2, NULL, 4.22, 0.00, 0.00, 'Jwjbd', 'COMPLETADA'),
(47, 7, '2026-01-05 19:49:00', 2, 'TRANSFERENCIA', 'hsjs', 2, NULL, 2.11, 0.00, 0.00, 'Jebs', 'COMPLETADA'),
(48, 5, '2026-01-06 22:16:00', 24, 'EFECTIVO', NULL, 2, NULL, 23.86, 0.00, 0.00, 'nada', 'COMPLETADA'),
(49, 3, '2026-01-06 22:16:00', 33, 'TRANSFERENCIA', 'tshhhfff', 2, NULL, 33.33, 0.00, 0.00, 'banco pichinca', 'COMPLETADA'),
(50, 5, '2026-01-06 23:34:00', 24, 'TRANSFERENCIA', 'EASDJDSA', 2, NULL, 23.86, 0.00, 0.00, 'TESTET', 'COMPLETADA'),
(51, 5, '2026-01-06 23:35:00', 56, 'EFECTIVO', NULL, 2, NULL, 56.14, 0.00, 0.00, 'TESTE', 'COMPLETADA'),
(52, 5, '2026-01-07 00:37:00', 24, 'EFECTIVO', NULL, 2, 7, 23.86, 0.00, 0.00, 'SE', 'COMPLETADA'),
(53, 5, '2026-01-07 00:55:00', 24, 'EFECTIVO', NULL, 2, 7, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(54, 7, '2026-01-07 00:56:00', 24, 'EFECTIVO', NULL, 2, 7, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(55, 5, '2026-01-07 01:16:00', 23, 'TRANSFERENCIA', 'TESTER', 2, 7, 22.99, 0.00, 0.00, '', 'COMPLETADA'),
(56, 5, '2026-01-08 22:55:00', 24, 'TRANSFERENCIA', 'COOSD', 2, 8, 23.86, 0.00, 0.00, 'TETSE', 'COMPLETADA'),
(57, 6, '2026-01-08 22:56:00', 24, 'TRANSFERENCIA', 'DSAD', 2, 8, 23.86, 0.00, 0.00, 'DSAD', 'COMPLETADA'),
(58, 7, '2026-01-08 23:15:00', 56, 'EFECTIVO', NULL, 2, 8, 56.14, 0.00, 0.00, '', 'COMPLETADA'),
(59, 5, '2026-01-08 23:23:00', 72, 'TRANSFERENCIA', 'JHJJJI', 2, 9, 71.58, 0.00, 0.00, '', 'COMPLETADA'),
(60, 5, '2026-01-08 23:23:00', 56, 'EFECTIVO', NULL, 2, 9, 56.14, 0.00, 0.00, '', 'COMPLETADA'),
(61, 5, '2026-01-09 00:20:00', 24, 'TRANSFERENCIA', 'DSAD', 2, 10, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(62, 7, '2026-01-09 00:21:00', 24, 'EFECTIVO', NULL, 2, 10, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(63, 5, '2026-01-11 21:21:00', 5, 'EFECTIVO', NULL, 2, 23, 4.55, 0.00, 0.00, '', 'COMPLETADA'),
(64, 5, '2026-01-11 23:27:00', 24, 'TRANSFERENCIA', 'dsad', 2, NULL, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(65, 5, '2026-01-11 23:27:00', 24, 'EFECTIVO', NULL, 2, NULL, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(66, 5, '2026-01-11 23:34:00', 24, 'EFECTIVO', NULL, 2, NULL, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(67, 5, '2026-01-12 20:29:00', 24, 'EFECTIVO', NULL, 2, NULL, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(68, 5, '2026-01-12 23:52:00', 24, 'EFECTIVO', NULL, 2, NULL, 23.86, 0.00, 0.00, '', 'COMPLETADA'),
(69, 1, '2026-01-13 20:30:00', 33, 'TRANSFERENCIA', '123456987', 2, NULL, 33.33, 0.00, 0.00, '', 'COMPLETADA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `apertura_caja`
--
ALTER TABLE `apertura_caja`
  ADD PRIMARY KEY (`Id_Apertura`),
  ADD KEY `idx_usuario_fecha` (`Id_Usuario`,`Fecha_Apertura`),
  ADD KEY `idx_estado` (`Estado`),
  ADD KEY `idx_fecha` (`Fecha_Apertura`);

--
-- Indices de la tabla `blocked_entities`
--
ALTER TABLE `blocked_entities`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`Id_Categoria`);

--
-- Indices de la tabla `cierre_caja`
--
ALTER TABLE `cierre_caja`
  ADD PRIMARY KEY (`Id_Cierre`),
  ADD KEY `idx_usuario` (`Id_Usuario`),
  ADD KEY `idx_fecha` (`Fecha_Cierre`),
  ADD KEY `idx_estado` (`Estado`),
  ADD KEY `idx_usuario_fecha` (`Id_Usuario`,`Fecha_Cierre`),
  ADD KEY `idx_apertura` (`Id_Apertura`),
  ADD KEY `idx_tipo_cierre` (`Tipo_Cierre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`Id_Clientes`);

--
-- Indices de la tabla `descripcion_categoria`
--
ALTER TABLE `descripcion_categoria`
  ADD PRIMARY KEY (`Id_Descripcion_Categoria`),
  ADD KEY `FK_Descripcion_Categoria` (`Id_Categoria`);

--
-- Indices de la tabla `desglose_denominaciones`
--
ALTER TABLE `desglose_denominaciones`
  ADD PRIMARY KEY (`Id_Desglose`),
  ADD KEY `idx_cierre` (`Id_Cierre`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`Id_Detalle`),
  ADD KEY `Detalles_Ventas` (`Id_Venta_Detalle`),
  ADD KEY `Dalletes_Inventario` (`Id_Inventario_Detalle`),
  ADD KEY `idx_venta_detalle` (`Id_Venta_Detalle`);

--
-- Indices de la tabla `diferencias_caja`
--
ALTER TABLE `diferencias_caja`
  ADD PRIMARY KEY (`Id_Diferencia`),
  ADD KEY `idx_cierre` (`Id_Cierre`),
  ADD KEY `idx_usuario` (`Id_Usuario`);

--
-- Indices de la tabla `gastos_caja`
--
ALTER TABLE `gastos_caja`
  ADD PRIMARY KEY (`Id_Gasto`),
  ADD KEY `idx_usuario_fecha` (`Id_Usuario`,`Fecha_Gasto`),
  ADD KEY `idx_apertura_caja` (`Id_Apertura_Caja`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`Id_Inventario`),
  ADD KEY `Inventario_Categorio` (`Id_Inventario_Categoria`),
  ADD KEY `idx_tipo_categoria` (`Id_Tipo_Categoria`),
  ADD KEY `idx_codigo_barras` (`Codigo_Barras`);

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`Id_Login`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `idx_login_user` (`Id_Login_Usuario`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_attempts_user` (`username`),
  ADD KEY `idx_login_attempts_ip` (`ip`);

--
-- Indices de la tabla `login_attempts_log`
--
ALTER TABLE `login_attempts_log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`Id_Movimiento`),
  ADD KEY `idx_movimientos_producto` (`Id_Producto`),
  ADD KEY `idx_movimientos_usuario` (`Usuario_Id`),
  ADD KEY `idx_movimientos_fecha` (`Fecha_Movimiento`),
  ADD KEY `idx_movimientos_tipo` (`Tipo_Movimiento`),
  ADD KEY `idx_movimientos_producto_fecha` (`Id_Producto`,`Fecha_Movimiento`);

--
-- Indices de la tabla `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `idx_password_email` (`email`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`Id_Proveedor`),
  ADD KEY `Categoria_Proveedor` (`Id_Proveedor_Categoria`);

--
-- Indices de la tabla `retiros_caja`
--
ALTER TABLE `retiros_caja`
  ADD PRIMARY KEY (`Id_Retiro`),
  ADD KEY `Autorizado_Por` (`Autorizado_Por`),
  ADD KEY `idx_usuario_fecha` (`Id_Usuario`,`Fecha_Retiro`),
  ADD KEY `idx_apertura_caja` (`Id_Apertura_Caja`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`Id_Rol`);

--
-- Indices de la tabla `saldo_acumulado_vendedor`
--
ALTER TABLE `saldo_acumulado_vendedor`
  ADD PRIMARY KEY (`Id_Saldo`),
  ADD KEY `idx_usuario` (`Id_Usuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`Id_Usuario`),
  ADD KEY `idx_usuario_rol` (`Id_Usuario_Rol`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`Id_Venta`),
  ADD KEY `Venta_Cliente` (`Id_Cliente_Venta`),
  ADD KEY `idx_venta_fecha` (`Fecha_Venta`),
  ADD KEY `idx_venta_vendedor` (`Id_Usuario_Vendedor`),
  ADD KEY `idx_venta_estado` (`Estado_Venta`),
  ADD KEY `idx_usuario_fecha` (`Id_Usuario_Vendedor`,`Fecha_Venta`),
  ADD KEY `idx_metodo_pago` (`Metodo_Pago`),
  ADD KEY `idx_estado` (`Estado_Venta`),
  ADD KEY `idx_apertura_caja` (`Id_Apertura_Caja`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `apertura_caja`
--
ALTER TABLE `apertura_caja`
  MODIFY `Id_Apertura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `blocked_entities`
--
ALTER TABLE `blocked_entities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `Id_Categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cierre_caja`
--
ALTER TABLE `cierre_caja`
  MODIFY `Id_Cierre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `Id_Clientes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `descripcion_categoria`
--
ALTER TABLE `descripcion_categoria`
  MODIFY `Id_Descripcion_Categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `desglose_denominaciones`
--
ALTER TABLE `desglose_denominaciones`
  MODIFY `Id_Desglose` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `Id_Detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `diferencias_caja`
--
ALTER TABLE `diferencias_caja`
  MODIFY `Id_Diferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `gastos_caja`
--
ALTER TABLE `gastos_caja`
  MODIFY `Id_Gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `Id_Inventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `Id_Login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `login_attempts_log`
--
ALTER TABLE `login_attempts_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `Id_Movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de la tabla `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `Id_Proveedor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `retiros_caja`
--
ALTER TABLE `retiros_caja`
  MODIFY `Id_Retiro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `Id_Rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `saldo_acumulado_vendedor`
--
ALTER TABLE `saldo_acumulado_vendedor`
  MODIFY `Id_Saldo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `Id_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `Id_Venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `apertura_caja`
--
ALTER TABLE `apertura_caja`
  ADD CONSTRAINT `apertura_caja_ibfk_1` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cierre_caja`
--
ALTER TABLE `cierre_caja`
  ADD CONSTRAINT `cierre_caja_ibfk_1` FOREIGN KEY (`Id_Apertura`) REFERENCES `apertura_caja` (`Id_Apertura`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cierre_caja_ibfk_2` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `descripcion_categoria`
--
ALTER TABLE `descripcion_categoria`
  ADD CONSTRAINT `FK_Descripcion_Categoria` FOREIGN KEY (`Id_Categoria`) REFERENCES `categoria` (`Id_Categoria`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `desglose_denominaciones`
--
ALTER TABLE `desglose_denominaciones`
  ADD CONSTRAINT `desglose_denominaciones_ibfk_1` FOREIGN KEY (`Id_Cierre`) REFERENCES `cierre_caja` (`Id_Cierre`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `Dalletes_Inventario` FOREIGN KEY (`Id_Inventario_Detalle`) REFERENCES `inventario` (`Id_Inventario`),
  ADD CONSTRAINT `Detalles_Ventas` FOREIGN KEY (`Id_Venta_Detalle`) REFERENCES `venta` (`Id_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `diferencias_caja`
--
ALTER TABLE `diferencias_caja`
  ADD CONSTRAINT `diferencias_caja_ibfk_1` FOREIGN KEY (`Id_Cierre`) REFERENCES `cierre_caja` (`Id_Cierre`),
  ADD CONSTRAINT `diferencias_caja_ibfk_2` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`);

--
-- Filtros para la tabla `gastos_caja`
--
ALTER TABLE `gastos_caja`
  ADD CONSTRAINT `gastos_caja_ibfk_1` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `Inventario_Categorio` FOREIGN KEY (`Id_Inventario_Categoria`) REFERENCES `categoria` (`Id_Categoria`),
  ADD CONSTRAINT `fk_inventario_descripcion_categoria` FOREIGN KEY (`Id_Tipo_Categoria`) REFERENCES `descripcion_categoria` (`Id_Descripcion_Categoria`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `fk_login_usuario` FOREIGN KEY (`Id_Login_Usuario`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `fk_movimientos_producto` FOREIGN KEY (`Id_Producto`) REFERENCES `inventario` (`Id_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimientos_usuario` FOREIGN KEY (`Usuario_Id`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `Categoria_Proveedor` FOREIGN KEY (`Id_Proveedor_Categoria`) REFERENCES `categoria` (`Id_Categoria`);

--
-- Filtros para la tabla `retiros_caja`
--
ALTER TABLE `retiros_caja`
  ADD CONSTRAINT `retiros_caja_ibfk_1` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `retiros_caja_ibfk_2` FOREIGN KEY (`Autorizado_Por`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `saldo_acumulado_vendedor`
--
ALTER TABLE `saldo_acumulado_vendedor`
  ADD CONSTRAINT `saldo_acumulado_vendedor_ibfk_1` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`Id_Usuario_Rol`) REFERENCES `rol` (`Id_Rol`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `Venta_Cliente` FOREIGN KEY (`Id_Cliente_Venta`) REFERENCES `clientes` (`Id_Clientes`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
