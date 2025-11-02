-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-11-2025 a las 00:21:22
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
-- Base de datos: `barberia_ortega`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquileres`
--

CREATE TABLE `alquileres` (
  `id_alquiler` int(11) NOT NULL,
  `id_estacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `contrato_pdf` varchar(255) DEFAULT NULL,
  `estado` enum('vigente','vencido','cancelado') DEFAULT 'vigente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alquileres`
--

INSERT INTO `alquileres` (`id_alquiler`, `id_estacion`, `id_usuario`, `fecha_inicio`, `fecha_fin`, `monto`, `contrato_pdf`, `estado`) VALUES
(1, 1, 8, '2025-11-02', '2025-12-02', 850.00, NULL, 'vigente'),
(2, 2, 3, '2025-11-02', '2025-12-02', 850.00, NULL, 'vigente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id_evento` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id_evento`, `id_usuario`, `accion`, `fecha`) VALUES
(1, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-25 17:22:13'),
(2, 1, 'Cierre de sesión - IP: ::1', '2025-10-25 17:51:13'),
(3, 1, 'Intento de inicio de sesión fallido - IP: ::1', '2025-10-25 17:51:20'),
(4, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-25 17:51:32'),
(5, 1, 'Cierre de sesión - IP: ::1', '2025-10-25 18:23:04'),
(6, 1, 'Intento de inicio de sesión fallido - IP: ::1', '2025-10-25 18:23:06'),
(7, 1, 'Intento de inicio de sesión fallido - IP: ::1', '2025-10-25 18:23:10'),
(8, 1, 'Intento de inicio de sesión fallido - IP: ::1', '2025-10-25 18:24:19'),
(9, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-25 18:24:27'),
(10, 1, 'Cierre de sesión - IP: ::1', '2025-10-25 18:25:18'),
(11, 1, 'Intento de inicio de sesión fallido - IP: ::1', '2025-10-25 18:26:55'),
(12, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-25 18:27:04'),
(13, 1, 'Creó cliente #1: luis campos', '2025-10-26 02:10:59'),
(14, 1, 'Creó usuario #5: lucas (Rol: barbero)', '2025-10-26 11:16:31'),
(15, 1, 'Cambió estado del usuario #5 a \'inactivo\'', '2025-10-26 11:17:17'),
(16, 1, 'Cambió estado del usuario #4 a \'inactivo\'', '2025-10-26 11:17:21'),
(17, 1, 'Cambió estado del usuario #2 a \'inactivo\'', '2025-10-26 11:17:24'),
(18, 1, 'Cambió estado del usuario #3 a \'inactivo\'', '2025-10-26 11:17:25'),
(19, 1, 'Cambió estado del usuario #3 a \'activo\'', '2025-10-26 11:38:44'),
(20, 1, 'Cambió estado del usuario #2 a \'activo\'', '2025-10-26 11:38:46'),
(21, 1, 'Cambió estado del usuario #4 a \'activo\'', '2025-10-26 11:38:47'),
(22, 1, 'Cambió estado del usuario #5 a \'activo\'', '2025-10-26 11:38:58'),
(23, 1, 'Cambió estado del usuario #4 a \'inactivo\'', '2025-10-26 11:39:00'),
(24, 1, 'Cambió estado del usuario #5 a \'inactivo\'', '2025-10-26 11:39:02'),
(25, 1, 'Cambió estado del usuario #3 a \'inactivo\'', '2025-10-26 11:39:05'),
(26, 1, 'Creó usuario #6: diego (Rol: barbero)', '2025-10-26 11:42:10'),
(27, 1, 'Cambió estado del usuario #5 a \'activo\'', '2025-10-26 11:42:43'),
(28, 1, 'Cambió estado del usuario #4 a \'activo\'', '2025-10-26 11:42:45'),
(29, 1, 'Cambió estado del usuario #3 a \'activo\'', '2025-10-26 11:42:49'),
(30, 1, 'Creó cliente #2: marcos lopez', '2025-10-26 11:52:41'),
(31, 1, 'Cambió estado del usuario #5 a \'inactivo\'', '2025-10-26 23:00:48'),
(32, 1, 'Cambió estado del usuario #4 a \'inactivo\'', '2025-10-26 23:00:56'),
(33, 1, 'Cambió estado del usuario #5 a \'activo\'', '2025-10-26 23:01:01'),
(34, 1, 'Cambió estado del usuario #4 a \'activo\'', '2025-10-26 23:01:04'),
(35, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-28 10:43:19'),
(36, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 00:02:00'),
(37, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:02:10'),
(38, 1, 'Creó usuario #7: santos (Rol: barbero)', '2025-10-29 00:04:58'),
(39, 1, 'Creó usuario #8: santosbarber (Rol: barbero)', '2025-10-29 00:07:02'),
(40, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 00:07:44'),
(41, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:08:01'),
(42, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 00:14:43'),
(43, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:14:55'),
(44, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 00:15:22'),
(45, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:15:27'),
(46, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 00:15:34'),
(47, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:15:37'),
(48, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 00:15:45'),
(49, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:15:48'),
(50, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 00:19:26'),
(51, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:21:25'),
(52, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 00:21:27'),
(53, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:44:08'),
(54, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 00:44:13'),
(55, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:44:19'),
(56, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 00:44:28'),
(57, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:44:32'),
(58, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 00:49:36'),
(59, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 00:49:58'),
(60, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 00:50:12'),
(61, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 01:06:26'),
(62, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 01:19:17'),
(63, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 01:19:21'),
(64, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 01:20:04'),
(65, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 01:20:42'),
(66, 1, 'Cierre de sesión - IP: ::1', '2025-10-29 01:20:59'),
(67, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 01:21:44'),
(68, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 01:27:35'),
(69, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 01:29:28'),
(70, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 01:39:28'),
(71, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 01:49:53'),
(72, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 01:50:40'),
(73, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 04:13:51'),
(74, 8, 'Cierre de sesión - IP: ::1', '2025-10-29 04:13:56'),
(75, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-29 04:13:58'),
(76, 1, 'Creó servicio #1: tinte', '2025-10-29 20:42:24'),
(77, 1, 'Editó servicio #1: tinte', '2025-10-29 20:43:09'),
(78, 1, 'Creó reserva #4 para cliente #1', '2025-10-29 20:44:42'),
(79, 1, 'Creó cliente #3: jorge marcos perez', '2025-10-29 20:45:53'),
(80, 1, 'Cierre de sesión - IP: ::1', '2025-10-30 00:47:40'),
(81, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-30 00:48:00'),
(82, 1, 'Creó reserva #6 para cliente #1', '2025-10-30 00:51:15'),
(83, 1, 'Creó servicio #2: degradaso desde o', '2025-10-30 01:15:32'),
(84, 1, 'Creó servicio #3: corte cadete', '2025-10-30 01:16:03'),
(85, 1, 'Creó servicio #4: Corte + Barba', '2025-10-30 01:16:56'),
(86, 1, 'Cierre de sesión - IP: ::1', '2025-10-30 01:20:17'),
(87, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-30 01:20:23'),
(88, 8, 'Creó reserva #7 para cliente #3', '2025-10-30 01:21:27'),
(89, 8, 'Creó reserva #8 para cliente #2', '2025-10-30 01:22:35'),
(90, 8, 'Creó reserva #9 para cliente #3', '2025-10-30 01:23:44'),
(91, 8, 'Editó reserva #4', '2025-10-30 01:26:22'),
(92, 8, 'Creó cliente #4: miguel limachi mendoza', '2025-10-30 01:27:37'),
(93, 8, 'Creó reserva #11 para cliente #4', '2025-10-30 01:29:45'),
(94, 8, 'Cierre de sesión - IP: ::1', '2025-10-30 01:30:04'),
(95, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-30 01:30:08'),
(96, 8, 'Creó servicio #5: corte pong', '2025-10-30 01:31:19'),
(97, 8, 'Creó cliente #5: blady cabrera camacho', '2025-10-30 01:32:52'),
(98, 8, 'Creó reserva #12 para cliente #5', '2025-10-30 01:33:41'),
(99, 8, 'Cierre de sesión - IP: ::1', '2025-10-30 01:34:17'),
(100, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-30 01:34:22'),
(101, 1, 'Creó reserva #13 para cliente #4', '2025-10-30 01:35:42'),
(102, 1, 'Cierre de sesión - IP: ::1', '2025-10-31 01:08:53'),
(103, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-10-31 01:09:02'),
(104, 8, 'Cierre de sesión - IP: ::1', '2025-10-31 01:09:06'),
(105, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-10-31 01:09:18'),
(106, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-11-02 17:25:52'),
(107, 8, 'Cierre de sesión - IP: ::1', '2025-11-02 17:25:59'),
(108, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-11-02 17:26:02'),
(109, 1, 'Creó reserva #14 para cliente #4', '2025-11-02 17:27:51'),
(110, 1, 'Editó reserva #14', '2025-11-02 17:37:01'),
(111, 1, 'Creó estación #1: mesa 1', '2025-11-02 17:38:24'),
(112, 1, 'Creó alquiler #1 para estación #1', '2025-11-02 17:38:40'),
(113, 1, 'Editó estación #1: mesa 1', '2025-11-02 17:40:01'),
(114, 1, 'Editó estación #1: mesa 1', '2025-11-02 17:40:30'),
(115, 1, 'Asignó turno de apertura #1 para 2025-11-21 al usuario #8', '2025-11-02 17:41:17'),
(116, 1, 'Marcó como cumplido el turno #1', '2025-11-02 17:44:24'),
(117, 1, 'Creó estación #2: mesa 2', '2025-11-02 17:45:03'),
(118, 1, 'Creó alquiler #2 para estación #2', '2025-11-02 17:55:00'),
(119, 1, 'Intento de inicio de sesión fallido - IP: ::1', '2025-11-02 17:56:44'),
(120, 1, 'Intento de inicio de sesión fallido - IP: ::1', '2025-11-02 17:56:54'),
(121, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-11-02 17:57:08'),
(122, NULL, 'Registró pago de alquiler - Contrato #1 - Estación: mesa 1 - Monto: Bs 850,00 - Concepto: qweq', '2025-11-02 18:01:57'),
(123, NULL, 'Registró pago de alquiler - Contrato #2 - Estación: mesa 2 - Monto: Bs 850,00 - Concepto: a tiempo', '2025-11-02 18:06:43'),
(124, 1, 'Creó reserva #15 para cliente #3', '2025-11-02 18:07:40'),
(125, NULL, 'Marcó como pagada la reserva #15 - Cliente: jorge marcos perez - Monto: Bs 10,00', '2025-11-02 18:08:23'),
(126, NULL, 'Marcó como pagada la reserva #9 - Cliente: jorge marcos perez - Monto: Bs 30,00', '2025-11-02 18:09:17'),
(127, NULL, 'Registró pago de servicio - Reserva #7 - Cliente: jorge marcos perez - Monto: Bs 20,00', '2025-11-02 18:27:13'),
(128, NULL, 'Marcó como pagada la reserva #6 - Cliente: luis campos - Monto: Bs 20,00', '2025-11-02 18:32:10'),
(129, 1, 'Asignó turno de apertura #2 para 2025-11-03 al usuario #8', '2025-11-02 18:40:28'),
(130, 1, 'Asignó turno de apertura #3 para 2025-11-04 al usuario #7', '2025-11-02 18:40:51'),
(131, 1, 'Asignó turno de apertura #4 para 2025-11-05 al usuario #4', '2025-11-02 18:41:33'),
(132, 1, 'Cierre de sesión - IP: ::1', '2025-11-02 19:16:49'),
(133, 8, 'Inicio de sesión exitoso - IP: ::1', '2025-11-02 19:17:21'),
(134, 8, 'Cierre de sesión - IP: ::1', '2025-11-02 19:17:52'),
(135, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-11-02 19:17:57'),
(136, NULL, 'Registró pago de servicio - Reserva #11 - Cliente: miguel limachi mendoza - Monto: Bs 12,00', '2025-11-02 19:19:23'),
(137, NULL, 'Eliminó pago de alquiler #2 - Contrato #2 - Estación: mesa 2', '2025-11-02 19:22:39'),
(138, NULL, 'Registró pago de alquiler - Contrato #2 - Estación: mesa 2 - Monto: Bs 850,00', '2025-11-02 19:22:55'),
(139, 1, 'Creó reserva #16 para cliente #1', '2025-11-02 19:30:05'),
(140, 1, 'Creó reserva #17 para cliente #5', '2025-11-02 19:33:46'),
(141, 1, 'Creó reserva #18 para cliente #3', '2025-11-02 19:38:51'),
(142, 1, 'Creó reserva #19 para cliente #5', '2025-11-02 19:43:00'),
(143, 1, 'Creó reserva #20 para cliente #5', '2025-11-02 19:47:00'),
(144, 1, 'Creó reserva #21 para cliente #3', '2025-11-02 20:01:13'),
(145, NULL, 'Registró pago de servicio - Reserva #20 - Cliente: blady cabrera camacho - Monto: Bs 31,00', '2025-11-02 20:05:15'),
(146, NULL, 'Registró pago de servicio - Reserva #16 - Cliente: luis campos - Monto: Bs 12,00', '2025-11-02 20:05:35'),
(147, NULL, 'Registró pago de servicio - Reserva #18 - Cliente: jorge marcos perez - Monto: Bs 12,00', '2025-11-02 20:06:10'),
(148, NULL, 'Registró pago de servicio - Reserva #17 - Cliente: blady cabrera camacho - Monto: Bs 10,00', '2025-11-02 20:07:16'),
(149, NULL, 'Registró pago de servicio - Reserva #13 - Cliente: miguel limachi mendoza - Monto: Bs 12,00', '2025-11-02 20:08:20'),
(150, 1, 'Creó reserva #22 para cliente #5', '2025-11-02 20:09:44'),
(151, NULL, 'Registró pago de servicio - Reserva #12 - Cliente: blady cabrera camacho - Monto: Bs 10,00', '2025-11-02 20:11:21'),
(152, NULL, 'Registró pago de servicio - Reserva #22 - Cliente: blady cabrera camacho - Monto: Bs 30,00', '2025-11-02 20:13:44'),
(153, NULL, 'Registró pago de servicio - Reserva #21 - Cliente: jorge marcos perez - Monto: Bs 23,00', '2025-11-02 20:16:38'),
(154, NULL, 'Registró pago de servicio - Reserva #8 - Cliente: marcos lopez - Monto: Bs 20,01', '2025-11-02 20:20:29'),
(155, 1, 'Creó cliente #6: Daniel merlo salcedo', '2025-11-02 20:23:25'),
(156, 1, 'Creó reserva #23 para cliente #6', '2025-11-02 20:24:15'),
(157, 1, 'Cierre de sesión - IP: ::1', '2025-11-02 20:28:40'),
(158, 1, 'Inicio de sesión exitoso - IP: ::1', '2025-11-02 20:28:42'),
(159, 1, 'Creó reserva #24 para cliente #6', '2025-11-02 20:31:44'),
(160, 1, 'Cambió estado del usuario #8 a \'inactivo\'', '2025-11-02 20:36:21'),
(161, 1, 'Cambió estado del usuario #8 a \'activo\'', '2025-11-02 20:36:22'),
(162, 1, 'Asignó turno de apertura #5 para 2025-11-02 al usuario #6', '2025-11-02 20:36:49'),
(163, 1, 'Editó turno #5', '2025-11-02 20:37:02'),
(164, 1, 'Marcó como cumplido el turno #5', '2025-11-02 20:37:56'),
(165, 1, 'Creó reserva #42 para cliente #5', '2025-11-02 20:59:49'),
(166, 1, 'Creó reserva #53 para cliente #5', '2025-11-02 21:04:39'),
(167, 1, 'Creó reserva #56 para cliente #5', '2025-11-02 21:29:48'),
(168, 1, 'Creó reserva #57 para cliente #5', '2025-11-02 22:04:10'),
(169, 1, 'Creó reserva #58 para cliente #2', '2025-11-02 22:14:42'),
(170, 1, 'Editó reserva #15', '2025-11-02 22:15:14'),
(171, 1, 'Editó reserva #14', '2025-11-02 22:21:34'),
(172, 1, 'Editó reserva #14', '2025-11-02 22:26:22'),
(173, 1, 'Editó reserva #14', '2025-11-02 22:45:22'),
(174, 1, 'Editó reserva #15', '2025-11-02 22:45:34'),
(175, 1, 'Cambió estado del usuario #4 a \'inactivo\'', '2025-11-02 22:49:25'),
(176, 1, 'Cambió estado del usuario #7 a \'inactivo\'', '2025-11-02 22:49:29'),
(177, 1, 'Cambió estado del usuario #8 a \'inactivo\'', '2025-11-02 22:49:55'),
(178, 1, 'Cambió estado del usuario #6 a \'inactivo\'', '2025-11-02 22:51:43'),
(179, 1, 'Cambió estado del usuario #6 a \'inactivo\'', '2025-11-02 22:51:45'),
(180, 1, 'Cambió estado del usuario #5 a \'inactivo\'', '2025-11-02 22:51:52'),
(181, 1, 'Cambió estado del usuario #2 a \'inactivo\'', '2025-11-02 22:51:56'),
(182, 1, 'Cambió estado del usuario #5 a \'inactivo\'', '2025-11-02 22:52:04'),
(183, 1, 'Cambió estado del usuario #6 a \'activo\'', '2025-11-02 22:52:07'),
(184, 1, 'Cambió estado del usuario #8 a \'activo\'', '2025-11-02 22:53:20'),
(185, 1, 'Cambió estado del usuario #7 a \'activo\'', '2025-11-02 22:53:38'),
(186, 1, 'Cambió estado del usuario #6 a \'inactivo\'', '2025-11-02 22:54:49'),
(187, 1, 'Cambió estado del usuario #8 a \'inactivo\'', '2025-11-02 22:58:12'),
(188, 1, 'Cambió estado del usuario #7 a \'inactivo\'', '2025-11-02 22:58:14'),
(189, 1, 'Cambió estado del usuario #3 a \'inactivo\'', '2025-11-02 22:58:16'),
(190, 1, 'Cambió estado del usuario #8 a \'activo\'', '2025-11-02 22:58:18'),
(191, 1, 'Cambió estado del usuario #7 a \'activo\'', '2025-11-02 22:58:21'),
(192, 1, 'Cambió estado del usuario #6 a \'activo\'', '2025-11-02 22:58:23'),
(193, NULL, 'Marcó como pagada la reserva #24 - Cliente: Daniel merlo salcedo - Monto: Bs 30,00', '2025-11-02 23:12:20'),
(194, NULL, 'Registró pago de servicio - Reserva #42 - Cliente: blady cabrera camacho - Monto: Bs 10,00', '2025-11-02 23:16:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `telefono`, `correo`, `notas`, `fecha_registro`) VALUES
(1, 'luis campos', '60553964', 'juan@gmail.com', 'frecuente', '2025-10-26 02:10:59'),
(2, 'marcos lopez', '76543333', 'marcos@gmail.com', 'corte de gradado', '2025-10-26 11:52:41'),
(3, 'jorge marcos perez', '78965432', 'jorge@gmail.com', '', '2025-10-29 20:45:53'),
(4, 'miguel limachi mendoza', '76543214', 'miguel@gmail.com', 'otros', '2025-10-30 01:27:37'),
(5, 'blady cabrera camacho', '65432134', 'blady@gmail.com', 'ninguna', '2025-10-30 01:32:51'),
(6, 'Daniel merlo salcedo', '65432345', 'daniel@gamil.com', 'sasdada', '2025-11-02 20:23:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id_config` int(11) NOT NULL,
  `parametro` varchar(100) DEFAULT NULL,
  `valor` text DEFAULT NULL,
  `actualizado` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estaciones`
--

CREATE TABLE `estaciones` (
  `id_estacion` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estaciones`
--

INSERT INTO `estaciones` (`id_estacion`, `nombre`, `descripcion`, `disponible`) VALUES
(1, 'mesa 1', '', 0),
(2, 'mesa 2', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `enviado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_reserva` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo` enum('efectivo','tarjeta','qr','otro') DEFAULT 'efectivo',
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_reserva`, `monto`, `metodo`, `id_usuario`, `fecha_pago`) VALUES
(1, 15, 10.00, 'efectivo', NULL, '2025-11-02 18:08:23'),
(2, 9, 30.00, 'efectivo', NULL, '2025-11-02 18:09:17'),
(3, 7, 20.00, 'efectivo', NULL, '2025-11-02 18:27:13'),
(4, 6, 20.00, 'efectivo', NULL, '2025-11-02 18:32:10'),
(5, 11, 12.00, 'efectivo', NULL, '2025-11-02 19:19:23'),
(6, 20, 31.00, 'efectivo', NULL, '2025-11-02 20:05:15'),
(7, 16, 12.00, 'efectivo', NULL, '2025-11-02 20:05:35'),
(8, 18, 12.00, 'efectivo', NULL, '2025-11-02 20:06:10'),
(9, 17, 10.00, 'efectivo', NULL, '2025-11-02 20:07:16'),
(10, 13, 12.00, 'efectivo', NULL, '2025-11-02 20:08:20'),
(11, 12, 10.00, 'efectivo', NULL, '2025-11-02 20:11:21'),
(12, 22, 30.00, 'efectivo', NULL, '2025-11-02 20:13:44'),
(13, 21, 23.00, 'efectivo', NULL, '2025-11-02 20:16:38'),
(14, 8, 20.01, 'efectivo', NULL, '2025-11-02 20:20:29'),
(15, 24, 30.00, 'efectivo', NULL, '2025-11-02 23:12:20'),
(16, 42, 10.00, 'efectivo', NULL, '2025-11-02 23:16:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_alquiler`
--

CREATE TABLE `pagos_alquiler` (
  `id_pago_alquiler` int(11) NOT NULL,
  `id_alquiler` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `metodo` enum('efectivo','tarjeta','qr','otro') DEFAULT 'efectivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos_alquiler`
--

INSERT INTO `pagos_alquiler` (`id_pago_alquiler`, `id_alquiler`, `monto`, `fecha_pago`, `metodo`) VALUES
(1, 1, 850.00, '2025-11-02 18:01:57', 'efectivo'),
(3, 2, 850.00, '2025-11-02 19:22:55', 'efectivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada','finalizada') DEFAULT 'pendiente',
  `pagado` tinyint(1) DEFAULT 0,
  `notas` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_cliente`, `id_usuario`, `id_servicio`, `fecha_inicio`, `fecha_fin`, `estado`, `pagado`, `notas`, `fecha_registro`) VALUES
(4, 1, 7, 1, '2025-10-29 18:48:00', '2025-10-29 00:00:00', 'finalizada', 1, '', '2025-10-29 20:44:42'),
(6, 1, 6, 1, '2025-10-29 21:51:00', '2025-10-29 00:00:00', 'confirmada', 1, '', '2025-10-30 00:51:15'),
(7, 3, 5, 2, '2025-10-30 10:00:00', '2025-10-30 00:00:00', 'confirmada', 1, '', '2025-10-30 01:21:27'),
(8, 2, 6, 2, '2025-10-30 21:24:00', '2025-10-30 00:00:00', 'finalizada', 1, 'pago efectivo', '2025-10-30 01:22:35'),
(9, 3, 6, 4, '2025-10-30 10:26:00', '2025-10-30 00:00:00', 'confirmada', 1, '', '2025-10-30 01:23:44'),
(11, 4, 8, 3, '2025-10-29 09:30:00', '2025-10-29 00:00:00', 'confirmada', 1, 'ning', '2025-10-30 01:29:45'),
(12, 5, 3, 5, '2025-10-29 21:33:00', '2025-10-29 00:00:00', 'finalizada', 1, '', '2025-10-30 01:33:41'),
(13, 4, 6, 4, '2025-10-29 13:35:00', '2025-10-29 00:00:00', 'finalizada', 1, '', '2025-10-30 01:35:42'),
(14, 4, 8, 1, '2025-11-02 14:27:00', '2025-11-02 00:00:00', 'cancelada', 1, 'dadas', '2025-11-02 17:27:51'),
(15, 3, 5, 5, '2025-11-02 14:11:00', '2025-11-02 00:00:00', 'pendiente', 1, 'asd', '2025-11-02 18:07:40'),
(16, 1, 3, 3, '2025-11-02 17:29:00', '2025-11-02 00:00:00', 'confirmada', 1, '', '2025-11-02 19:30:05'),
(17, 5, 6, 3, '2025-11-02 15:34:00', '2025-11-02 00:00:00', 'finalizada', 1, '', '2025-11-02 19:33:45'),
(18, 3, 6, 3, '2025-11-02 16:38:00', '2025-11-02 00:00:00', 'confirmada', 1, 'asa', '2025-11-02 19:38:51'),
(19, 5, 6, 3, '2025-11-02 15:46:00', '2025-11-02 00:00:00', 'confirmada', 1, '', '2025-11-02 19:43:00'),
(20, 5, 6, 4, '2025-11-02 17:46:00', '2025-11-02 00:00:00', 'confirmada', 1, 'asds', '2025-11-02 19:47:00'),
(21, 3, 6, 3, '2025-11-02 16:00:00', '2025-11-02 00:00:00', 'finalizada', 1, 'ff', '2025-11-02 20:01:13'),
(22, 5, 6, 4, '2025-11-03 17:09:00', '2025-11-03 00:00:00', 'confirmada', 1, '', '2025-11-02 20:09:44'),
(23, 6, 5, 4, '2025-11-14 17:23:00', '2025-11-14 00:00:00', 'pendiente', 0, 'dasd', '2025-11-02 20:24:15'),
(24, 6, 6, 4, '2025-12-18 16:35:00', '2025-12-18 00:00:00', 'confirmada', 1, '', '2025-11-02 20:31:44'),
(42, 5, 5, 3, '2025-11-02 16:00:00', '2025-11-02 00:00:00', 'confirmada', 1, 'asdasd', '2025-11-02 20:59:49'),
(53, 5, 6, 5, '2025-11-02 19:04:00', '2025-11-02 00:00:00', 'confirmada', 0, '', '2025-11-02 21:04:39'),
(56, 5, 6, 4, '2025-11-02 18:15:00', '2025-11-02 18:45:00', 'confirmada', 0, '', '2025-11-02 21:29:48'),
(57, 5, 5, 3, '2025-11-02 18:00:00', '2025-11-02 18:05:00', 'confirmada', 0, '', '2025-11-02 22:04:10'),
(58, 2, 8, 3, '2025-11-02 18:15:00', '2025-11-02 18:20:00', 'confirmada', 0, '', '2025-11-02 22:14:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sanciones`
--

CREATE TABLE `sanciones` (
  `id_sancion` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT 0.00,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `duracion_minutos` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre`, `duracion_minutos`, `precio`, `fecha_registro`) VALUES
(1, 'tinte', 30, 20.00, '2025-10-29 20:42:23'),
(2, 'degradaso desde o', 15, 20.00, '2025-10-30 01:15:32'),
(3, 'corte cadete', 5, 10.00, '2025-10-30 01:16:03'),
(4, 'Corte + Barba', 30, 30.00, '2025-10-30 01:16:55'),
(5, 'corte pong', 20, 10.00, '2025-10-30 01:31:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id_turno` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('apertura','cierre','limpieza') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `cumplido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id_turno`, `id_usuario`, `fecha`, `tipo`, `observaciones`, `cumplido`) VALUES
(1, 8, '2025-11-21', 'apertura', '', 1),
(2, 8, '2025-11-03', 'apertura', '', 0),
(3, 7, '2025-11-04', 'apertura', '', 0),
(4, 4, '2025-11-05', 'apertura', '', 0),
(5, 6, '2025-11-02', 'apertura', 'hghh', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `rol` enum('administrador','barbero','apoyo') DEFAULT 'barbero',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `usuario`, `correo`, `telefono`, `contrasena_hash`, `rol`, `estado`, `fecha_registro`) VALUES
(1, 'Administrador', 'admin', 'admin@barberiaortega.com', '70000000', '$2y$10$yNRT0FVZ5J3sKGDMskQj.Ozme5I7H2TdlSXmOvUu5E/LsBIBCHbiy', 'administrador', 'activo', '2025-10-25 17:21:56'),
(2, 'Carlos Ortega', 'admin_ortega', 'admin@barberiaortega.com', '70000001', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'administrador', 'inactivo', '2025-10-26 02:50:46'),
(3, 'Luis Gómez', 'luis_barber', 'luis@barberiaortega.com', '70000002', 'dd5b9f55350bf6b19a25746eb6534fd3022406e8dc297e96951ebd2c35e3a545', 'barbero', 'inactivo', '2025-10-26 02:50:46'),
(4, 'María López', 'maria_apoyo', 'maria@barberiaortega.com', '70000003', 'd20cc73dd289e6940552b0250e9136167f4c8af14a8582ea627cb23a2c6c13a0', 'apoyo', 'inactivo', '2025-10-26 02:50:46'),
(5, 'fredy lucas', 'lucas', 'freddy@gmail.com', '60523345', '$2y$10$3V9TEfqbzPo7Gt6mCQmCAegoR6tphOg3Thtj6yXqYJ5LA40Bczakq', 'barbero', 'inactivo', '2025-10-26 11:16:31'),
(6, 'diego mendoza', 'diego', 'diego@gmail.com', '60523345', '$2y$10$LcQsF6IGaZnprz3q3J2OJ.ZZJYIzk0TQ2C5ax128hgbVgod5lBw42', 'barbero', 'activo', '2025-10-26 11:42:10'),
(7, 'santos huanca', 'santos', '', '60645539', '$2y$10$0F1K0KkXhgoXLJ/Rxv.V4umDgbiXALzb58xM75vFUmRPxXch7Qase', 'barbero', 'activo', '2025-10-29 00:04:58'),
(8, 'santos huanca limachi', 'santosbarber', 'santos@gmail.com', '60643469', '$2y$10$rH7sS1nClNhcFkXKB5jr/u4ixz8HGEhfE1YB0E6.gg5wpCoAgbWqa', 'barbero', 'activo', '2025-10-29 00:07:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alquileres`
--
ALTER TABLE `alquileres`
  ADD PRIMARY KEY (`id_alquiler`),
  ADD KEY `id_estacion` (`id_estacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `parametro` (`parametro`);

--
-- Indices de la tabla `estaciones`
--
ALTER TABLE `estaciones`
  ADD PRIMARY KEY (`id_estacion`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `enviado_por` (`enviado_por`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_reserva` (`id_reserva`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pagos_alquiler`
--
ALTER TABLE `pagos_alquiler`
  ADD PRIMARY KEY (`id_pago_alquiler`),
  ADD KEY `id_alquiler` (`id_alquiler`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `sanciones`
--
ALTER TABLE `sanciones`
  ADD PRIMARY KEY (`id_sancion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id_turno`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alquileres`
--
ALTER TABLE `alquileres`
  MODIFY `id_alquiler` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estaciones`
--
ALTER TABLE `estaciones`
  MODIFY `id_estacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `pagos_alquiler`
--
ALTER TABLE `pagos_alquiler`
  MODIFY `id_pago_alquiler` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `sanciones`
--
ALTER TABLE `sanciones`
  MODIFY `id_sancion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id_turno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alquileres`
--
ALTER TABLE `alquileres`
  ADD CONSTRAINT `alquileres_ibfk_1` FOREIGN KEY (`id_estacion`) REFERENCES `estaciones` (`id_estacion`),
  ADD CONSTRAINT `alquileres_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`enviado_por`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id_reserva`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pagos_alquiler`
--
ALTER TABLE `pagos_alquiler`
  ADD CONSTRAINT `pagos_alquiler_ibfk_1` FOREIGN KEY (`id_alquiler`) REFERENCES `alquileres` (`id_alquiler`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`);

--
-- Filtros para la tabla `sanciones`
--
ALTER TABLE `sanciones`
  ADD CONSTRAINT `sanciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD CONSTRAINT `turnos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
