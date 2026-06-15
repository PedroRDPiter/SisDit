-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-06-2026 a las 22:12:00
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
-- Base de datos: `sistema`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calles`
--

CREATE TABLE `calles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catastro`
--

CREATE TABLE `catastro` (
  `id` int(11) NOT NULL,
  `cuenta_catastral` varchar(50) NOT NULL,
  `propietario` varchar(150) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `utm_x` decimal(12,2) DEFAULT NULL,
  `utm_y` decimal(12,2) DEFAULT NULL,
  `superficie` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigos_postales`
--

CREATE TABLE `codigos_postales` (
  `id` int(11) NOT NULL,
  `codigo_postal` varchar(5) NOT NULL,
  `tipo_asentamiento` varchar(100) NOT NULL,
  `asentamiento` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `codigos_postales`
--

INSERT INTO `codigos_postales` (`id`, `codigo_postal`, `tipo_asentamiento`, `asentamiento`) VALUES
(1, '20434', 'Colonia', '18 de Marzo'),
(2, '20435', 'Colonia', 'El Salitrillo'),
(3, '20435', 'Ranchería', 'La Misión'),
(4, '20435', 'Granja', 'Candelaria'),
(5, '20435', 'Ranchería', 'El Barzón'),
(6, '20435', 'Ranchería', 'Las Norias'),
(7, '20435', 'Granja', 'Lupita'),
(8, '20437', 'Colonia', 'Constitución'),
(9, '20437', 'Colonia', 'Héctor Hugo Olivares'),
(10, '20437', 'Colonia', 'Lindavista'),
(11, '20437', 'Ranchería', 'Canal Grande'),
(12, '20437', 'Colonia', 'Potrero San Isidro'),
(13, '20437', 'Colonia', 'Las Antenas'),
(14, '20437', 'Ejido', 'Pabellón de Hidalgo [Ejido Ex-Hacienda]'),
(15, '20437', 'Colonia', 'El Milagro'),
(16, '20437', 'Colonia', 'Pabellón de Hidalgo Centro'),
(17, '20437', 'Ejido', 'Estancia de Mosqueira'),
(18, '20437', 'Granja', 'El Rosario'),
(19, '20440', 'Ranchería', 'Morelos'),
(20, '20444', 'Ranchería', 'Las Camas'),
(21, '20444', 'Ranchería', 'Potrerillos'),
(22, '20444', 'Ranchería', 'Túnel de Potrerillo'),
(23, '20450', 'Ranchería', 'El Ajiladero'),
(24, '20450', 'Ejido', 'El Panal'),
(25, '20450', 'Ejido', 'Fresnillo'),
(26, '20450', 'Ranchería', 'La Boquilla'),
(27, '20450', 'Ranchería', 'Peña Blanca'),
(28, '20400', 'Colonia', 'Rincón de Romos Centro'),
(29, '20403', 'Fraccionamiento', 'Rincón Real'),
(30, '20403', 'Fraccionamiento', 'Norte'),
(31, '20403', 'Colonia', 'Santa Elena'),
(32, '20404', 'Fraccionamiento', 'Valle del Real'),
(33, '20404', 'Colonia', 'San Rafael I [Potrero]'),
(34, '20404', 'Fraccionamiento', 'José Luis Macias'),
(35, '20404', 'Fraccionamiento', 'La Estancia de Chora'),
(36, '20404', 'Fraccionamiento', 'Embajadores'),
(37, '20405', 'Barrio', 'El Chaveño'),
(38, '20405', 'Fraccionamiento', 'Villas del Camino Real'),
(39, '20405', 'Barrio', 'De Guadalupe'),
(40, '20405', 'Fraccionamiento', 'La Paz'),
(41, '20406', 'Fraccionamiento', 'Rinconada de las Piedras'),
(42, '20406', 'Fraccionamiento', 'Rinconada la Alameda'),
(43, '20406', 'Barrio', 'De Chora'),
(44, '20406', 'Colonia', 'Santa Cruz'),
(45, '20406', 'Fraccionamiento', 'Lázaro Cárdenas'),
(46, '20406', 'Fraccionamiento', 'Fraternidad'),
(47, '20406', 'Colonia', 'Cerro del Gato'),
(48, '20410', 'Colonia', 'Independencia'),
(49, '20410', 'Colonia', 'Magisterial'),
(50, '20410', 'Fraccionamiento', 'Magisterial II'),
(51, '20410', 'Colonia', 'Santa Anita'),
(52, '20410', 'Fraccionamiento', 'El Potrero'),
(53, '20410', 'Condominio', 'La Mezquitera'),
(54, '20414', 'Fraccionamiento', 'Villas de Jesús'),
(55, '20415', 'Fraccionamiento', 'Fundadores'),
(56, '20415', 'Colonia', 'San José'),
(57, '20416', 'Fraccionamiento', 'La Haciendita'),
(58, '20416', 'Ranchería', 'Presa de San Elías (José Muñoz)'),
(59, '20416', 'Colonia', 'Presidentes de México'),
(60, '20416', 'Fraccionamiento', 'Solidaridad'),
(61, '20417', 'Fraccionamiento', 'Miguel Hidalgo'),
(62, '20417', 'Fraccionamiento', 'Popular'),
(63, '20420', 'Colonia', 'Pablo Escaleras'),
(64, '20420', 'Ejido', 'El Saucillo'),
(65, '20420', 'Ejido', 'El Bajío'),
(66, '20420', 'Ranchería', 'Mar Negro'),
(67, '20420', 'Ranchería', 'Estación Rincón de Romos'),
(68, '20423', 'Colonia', 'San Judas Tadeo (Santa Fe)'),
(69, '20424', 'Ranchería', 'Puerta del Muerto (El 15)'),
(70, '20424', 'Ranchería', 'California'),
(71, '20424', 'Ranchería', 'Bajío del Yerbaníz'),
(72, '20424', 'Ranchería', 'Tanque Blanco'),
(73, '20424', 'Ranchería', 'El Tarasco I (Potrero)'),
(74, '20425', 'Ejido', 'San Jacinto'),
(75, '20426', 'Ejido', 'San Juan de la Natura'),
(76, '20426', 'Colonia', '16 de Septiembre'),
(77, '20427', 'Colonia', 'El Valle de las Delicias'),
(78, '20427', 'Colonia', 'Los Morales'),
(79, '20427', 'Ranchería', 'San Isidro el Labrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios_tramites`
--

CREATE TABLE `comentarios_tramites` (
  `id` int(11) NOT NULL,
  `tramite_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `es_interno` tinyint(1) DEFAULT 0,
  `leido` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios_tramites`
--

INSERT INTO `comentarios_tramites` (`id`, `tramite_id`, `usuario_id`, `comentario`, `es_interno`, `leido`, `created_at`) VALUES
(1, 35, 20, 'CONSTRUIDO', 0, 0, '2026-04-27 20:35:15'),
(2, 33, 20, 'SIN OBSERVACIONES', 0, 0, '2026-04-27 20:36:14'),
(3, 34, 20, 'SIN OBSERVACIONES', 0, 0, '2026-04-27 20:38:49'),
(4, 35, 20, 'CONSTRUIDO', 0, 0, '2026-04-28 18:43:20'),
(5, 35, 20, 'CONSTRUIDO', 0, 0, '2026-04-28 18:46:33'),
(6, 35, 20, 'CONSTRUIDO', 0, 0, '2026-04-28 18:48:31'),
(7, 35, 20, 'CONSTRUIDO', 0, 0, '2026-04-28 19:01:33'),
(8, 35, 20, 'CONSTRUIDO', 0, 0, '2026-04-28 19:33:17'),
(9, 38, 20, 'TIENE REQUERIMIENTO POR LA CONTRUCCION', 0, 0, '2026-04-29 17:43:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` enum('texto','numero','boolean','json') DEFAULT 'texto',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `updated_at`) VALUES
(1, 'max_file_size', '5242880', 'Tamaño máximo archivo en bytes (5MB)', 'numero', '2026-03-10 20:04:43'),
(2, 'dias_alerta_vencimiento', '3', 'Días antes de alertar vencimiento', 'numero', '2026-03-10 20:04:43'),
(3, 'municipio_nombre', 'Rincón de Romos', 'Nombre del municipio', 'texto', '2026-03-10 20:04:43'),
(4, 'director_nombre', 'LIC. URB. JESÚS BERNARDO DÍAZ DE LEÓN GUTIÉRREZ', 'Nombre del director', 'texto', '2026-04-24 15:10:26'),
(5, 'director_cargo', 'DIRECTOR DE PLANEACIÓN Y DESARROLLO URBANO', 'Cargo del director', 'texto', '2026-03-10 20:04:43'),
(6, 'whatsapp_numero', '4498077899', 'WhatsApp de contacto', 'texto', '2026-03-10 20:04:43'),
(7, 'email_contacto', 'dir.planeacionydu@gmail.com', 'Correo de contacto', 'texto', '2026-03-10 20:04:43'),
(8, 'constancia_reglamento_1', 'En inmuebles construidos deberán colocarse en el exterior, al frente de la construcción junto al acceso principal;', 'Reglamento I de la constancia de número oficial', 'texto', '2026-04-24 15:10:26'),
(9, 'constancia_reglamento_2', 'Los números oficiales en ningún caso deberán ser pintados sobre muros, bloques, columnas y/o en elementos de fácil destrucción;', 'Reglamento II de la constancia de número oficial', 'texto', '2026-04-24 15:10:26'),
(10, 'constancia_reglamento_3', 'Deberán además ser de tipo de fuente legible y permitir una fácil lectura a un mínimo de veinte metros;', 'Reglamento III de la constancia de número oficial', 'texto', '2026-04-24 15:10:26'),
(11, 'constancia_reglamento_4', 'Las placas de numeración deberán colocarse en una altura mínima de dos metros con cincuenta centímetros a partir del nivel de la banqueta.', 'Reglamento IV de la constancia de número oficial', 'texto', '2026-04-24 15:10:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `constancias_extra`
--

CREATE TABLE `constancias_extra` (
  `id` int(11) NOT NULL,
  `tramite_id` int(11) NOT NULL,
  `numero_constancia` varchar(50) DEFAULT NULL COMMENT 'Número oficial asignado a esta constancia adicional',
  `direccion` varchar(200) DEFAULT NULL,
  `colonia` varchar(100) DEFAULT NULL,
  `manzana` varchar(50) DEFAULT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `utc` varchar(100) DEFAULT NULL COMMENT 'Unidad de Tenencia de la Tierra / Colonia específica',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `constancias_extra`
--

INSERT INTO `constancias_extra` (`id`, `tramite_id`, `numero_constancia`, `direccion`, `colonia`, `manzana`, `lote`, `utc`, `created_at`) VALUES
(1, 33, '202-A', 'ANTONIO MUOZ ACOSTA 341 SOLIDARIDAD', 'SOLIDARIDAD', '12', '1', '', '2026-05-19 17:00:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `constancias_generadas`
--

CREATE TABLE `constancias_generadas` (
  `id` int(11) NOT NULL,
  `tramite_id` int(11) NOT NULL,
  `folio_salida_numero` int(11) NOT NULL,
  `folio_salida_anio` int(11) NOT NULL,
  `fecha_generacion` datetime DEFAULT current_timestamp(),
  `croquis_archivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `constancias_generadas`
--

INSERT INTO `constancias_generadas` (`id`, `tramite_id`, `folio_salida_numero`, `folio_salida_anio`, `fecha_generacion`, `croquis_archivo`) VALUES
(1, 33, 6, 2026, '2026-05-19 12:44:41', NULL),
(2, 33, 7, 2026, '2026-05-19 12:45:07', NULL),
(3, 33, 8, 2026, '2026-05-19 12:55:56', NULL),
(4, 33, 9, 2026, '2026-05-19 13:03:28', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `folios_salida_secuencia`
--

CREATE TABLE `folios_salida_secuencia` (
  `ano` int(11) NOT NULL,
  `siguiente_numero` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `folios_salida_secuencia`
--

INSERT INTO `folios_salida_secuencia` (`ano`, `siguiente_numero`) VALUES
(2026, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_tramites`
--

CREATE TABLE `historial_tramites` (
  `id` int(11) NOT NULL,
  `tramite_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` enum('Creado','Modificado','Enviado a revisión','Aprobado por Verificador','Aprobado','Rechazado','En corrección') NOT NULL,
  `estatus_anterior` varchar(50) DEFAULT NULL,
  `estatus_nuevo` varchar(50) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_tramites`
--

INSERT INTO `historial_tramites` (`id`, `tramite_id`, `usuario_id`, `accion`, `estatus_anterior`, `estatus_nuevo`, `comentario`, `fecha`) VALUES
(66, 24, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-14 16:32:24'),
(67, 24, 18, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-14 16:32:24'),
(68, 24, 11, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-14 16:56:27'),
(69, 24, 2, 'Modificado', 'Aprobado por Verificador', 'En revisión', '', '2026-04-15 17:22:36'),
(70, 24, 2, 'En corrección', 'En revisión', 'En corrección', '', '2026-04-15 17:22:47'),
(71, 24, 2, 'Aprobado por Verificador', 'En corrección', 'Aprobado por Verificador', '', '2026-04-15 17:22:58'),
(72, 24, 2, 'Rechazado', 'Aprobado por Verificador', 'Rechazado', '', '2026-04-15 17:23:13'),
(73, 24, 2, 'Aprobado por Verificador', 'Rechazado', 'Aprobado por Verificador', '', '2026-04-15 17:23:20'),
(74, 24, 18, 'Aprobado', 'Aprobado por Verificador', 'Aprobado', '', '2026-04-15 17:24:09'),
(93, 33, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-16 18:15:04'),
(94, 33, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-16 18:15:04'),
(95, 34, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-16 18:30:49'),
(96, 34, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-16 18:30:49'),
(97, 35, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-16 20:30:56'),
(98, 35, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-16 20:30:56'),
(99, 36, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-17 15:13:37'),
(100, 36, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-17 15:13:37'),
(101, 37, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-17 16:24:19'),
(102, 37, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-17 16:24:19'),
(103, 33, 11, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-20 17:14:30'),
(104, 33, 11, 'Modificado', 'Aprobado por Verificador', 'En revisión', '', '2026-04-20 17:16:00'),
(105, 38, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-20 18:25:00'),
(106, 38, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-20 18:25:00'),
(109, 40, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-20 20:57:26'),
(110, 40, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-20 20:57:26'),
(115, 43, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-21 16:32:25'),
(116, 43, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-21 16:32:25'),
(117, 40, 11, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-21 16:49:52'),
(118, 40, 11, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-04-21 16:50:02'),
(119, 44, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-21 20:16:41'),
(120, 44, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-21 20:16:41'),
(121, 45, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-21 20:45:21'),
(122, 45, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-21 20:45:21'),
(123, 46, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-22 18:05:01'),
(124, 46, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-22 18:05:01'),
(125, 47, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-23 16:05:59'),
(126, 47, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-23 16:05:59'),
(127, 48, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-23 18:35:25'),
(128, 48, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-23 18:35:25'),
(129, 49, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-23 18:49:12'),
(130, 49, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-23 18:49:12'),
(131, 40, 2, 'Modificado', 'Aprobado por Verificador', 'En revisión', '', '2026-04-23 19:12:25'),
(132, 33, 2, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-23 19:12:38'),
(133, 33, 2, 'Modificado', 'Aprobado por Verificador', 'En revisión', '', '2026-04-23 19:15:50'),
(138, 45, 2, 'Modificado', 'En revisión', 'En revisión', '', '2026-04-24 14:32:17'),
(139, 33, 2, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-24 14:44:55'),
(140, 33, 2, 'Modificado', 'Aprobado por Verificador', 'En revisión', '', '2026-04-24 15:02:35'),
(143, 53, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-27 15:37:45'),
(144, 53, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-27 15:37:45'),
(145, 54, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-27 16:11:45'),
(146, 54, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-27 16:11:45'),
(147, 55, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-27 19:15:02'),
(148, 55, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-27 19:15:02'),
(149, 56, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-27 19:51:25'),
(150, 56, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-27 19:51:25'),
(151, 35, 20, 'Modificado', 'En revisión', 'En revisión', 'CONSTRUIDO', '2026-04-27 20:35:15'),
(152, 33, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', 'SIN OBSERVACIONES', '2026-04-27 20:36:14'),
(153, 34, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', 'SIN OBSERVACIONES', '2026-04-27 20:38:49'),
(154, 57, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-27 20:59:57'),
(155, 57, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-27 20:59:57'),
(156, 58, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-28 17:28:35'),
(157, 58, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-28 17:28:35'),
(158, 35, 20, 'Modificado', 'En revisión', 'En revisión', 'CONSTRUIDO', '2026-04-28 18:43:20'),
(159, 35, 20, 'Modificado', 'En revisión', 'En revisión', 'CONSTRUIDO', '2026-04-28 18:46:33'),
(160, 35, 20, 'Modificado', 'En revisión', 'En revisión', 'CONSTRUIDO', '2026-04-28 18:48:31'),
(161, 35, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', 'CONSTRUIDO', '2026-04-28 19:01:33'),
(162, 35, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', 'CONSTRUIDO', '2026-04-28 19:33:17'),
(163, 40, 20, 'Modificado', 'En revisión', 'En revisión', '', '2026-04-29 17:15:10'),
(164, 40, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-29 17:15:34'),
(165, 40, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-04-29 17:19:52'),
(166, 38, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-29 17:41:59'),
(167, 38, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-04-29 17:42:35'),
(168, 38, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-04-29 17:42:59'),
(169, 38, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', 'TIENE REQUERIMIENTO POR LA CONTRUCCION', '2026-04-29 17:43:57'),
(170, 54, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-29 19:37:34'),
(171, 54, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-04-29 19:38:10'),
(172, 54, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-04-29 19:51:33'),
(173, 59, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-29 20:38:29'),
(174, 59, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-29 20:38:29'),
(175, 36, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-30 15:25:35'),
(176, 60, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-30 16:04:51'),
(177, 60, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-30 16:04:51'),
(178, 61, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-30 16:42:04'),
(179, 61, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-30 16:42:04'),
(180, 62, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-30 16:56:54'),
(181, 62, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-30 16:56:54'),
(182, 44, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-30 17:32:12'),
(183, 33, 18, 'Aprobado', 'Aprobado por Verificador', 'Aprobado', '', '2026-04-30 18:26:27'),
(184, 63, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-30 18:27:28'),
(185, 63, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-30 18:27:28'),
(188, 65, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-30 19:21:20'),
(189, 65, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-30 19:21:20'),
(190, 53, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-04-30 19:34:53'),
(191, 66, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-30 19:46:28'),
(192, 66, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-30 19:46:28'),
(193, 67, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-04-30 19:52:11'),
(194, 67, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-04-30 19:52:11'),
(195, 68, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-07 18:16:10'),
(196, 68, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-07 18:16:10'),
(197, 69, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-07 18:38:43'),
(198, 69, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-07 18:38:43'),
(199, 70, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-07 18:48:11'),
(200, 70, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-07 18:48:11'),
(201, 71, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-07 19:46:29'),
(202, 71, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-07 19:46:29'),
(203, 72, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 16:39:54'),
(204, 72, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 16:39:54'),
(205, 73, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 16:52:43'),
(206, 73, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 16:52:43'),
(207, 74, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 16:59:51'),
(208, 74, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 16:59:51'),
(209, 75, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 17:02:48'),
(210, 75, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 17:02:48'),
(211, 76, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 17:07:07'),
(212, 76, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 17:07:07'),
(213, 77, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 17:09:24'),
(214, 77, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 17:09:24'),
(215, 78, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 17:15:43'),
(216, 78, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 17:15:43'),
(217, 79, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 17:19:40'),
(218, 79, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 17:19:40'),
(219, 80, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 18:15:34'),
(220, 80, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 18:15:34'),
(223, 82, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 19:49:13'),
(224, 82, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 19:49:13'),
(225, 83, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 20:47:08'),
(226, 83, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 20:47:08'),
(227, 84, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-08 20:51:36'),
(228, 84, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-08 20:51:36'),
(229, 85, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 14:49:14'),
(230, 85, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 14:49:14'),
(231, 86, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 14:56:51'),
(232, 86, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 14:56:51'),
(233, 57, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-12 15:56:58'),
(234, 59, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-12 16:14:11'),
(235, 87, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 16:28:04'),
(236, 87, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 16:28:04'),
(237, 88, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 16:30:11'),
(238, 88, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 16:30:11'),
(239, 89, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 17:14:38'),
(240, 89, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 17:14:38'),
(241, 90, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 18:28:07'),
(242, 90, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 18:28:07'),
(243, 91, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 18:36:57'),
(244, 91, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 18:36:57'),
(245, 92, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 19:56:17'),
(246, 92, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 19:56:17'),
(247, 93, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 20:09:42'),
(248, 93, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 20:09:42'),
(249, 94, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 20:15:57'),
(250, 94, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 20:15:57'),
(251, 95, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 20:29:45'),
(252, 95, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 20:29:45'),
(253, 96, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 20:39:37'),
(254, 96, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 20:39:37'),
(255, 97, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-12 20:51:42'),
(256, 97, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-12 20:51:42'),
(257, 98, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-13 17:27:34'),
(258, 98, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-13 17:27:34'),
(259, 99, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-13 18:04:58'),
(260, 99, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-13 18:04:58'),
(261, 100, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-14 16:30:30'),
(262, 100, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-14 16:30:30'),
(263, 101, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-14 16:44:56'),
(264, 101, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-14 16:44:56'),
(265, 102, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-14 17:13:39'),
(266, 102, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-14 17:13:39'),
(267, 103, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-14 17:45:16'),
(268, 103, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-14 17:45:16'),
(269, 104, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-14 18:52:21'),
(270, 104, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-14 18:52:21'),
(271, 105, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-14 18:56:49'),
(272, 105, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-14 18:56:49'),
(273, 106, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-14 19:32:46'),
(274, 106, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-14 19:32:46'),
(275, 72, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-14 19:34:18'),
(276, 73, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-14 19:42:18'),
(277, 74, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-14 19:45:14'),
(278, 80, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-14 19:54:03'),
(279, 85, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-14 20:15:54'),
(280, 107, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-14 20:16:01'),
(281, 107, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-14 20:16:01'),
(282, 103, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-15 15:41:40'),
(283, 108, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-15 16:34:20'),
(284, 108, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-15 16:34:20'),
(285, 92, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-15 16:59:06'),
(286, 94, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-15 17:25:20'),
(287, 107, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-15 17:40:15'),
(288, 108, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-15 17:40:51'),
(289, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-15 17:41:05'),
(290, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-15 17:41:23'),
(291, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-15 17:42:01'),
(292, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-15 17:42:19'),
(293, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-15 17:42:36'),
(294, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-15 17:42:50'),
(295, 108, 20, 'Modificado', 'Aprobado por Verificador', 'En revisión', '', '2026-05-15 17:43:35'),
(296, 109, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-15 20:39:41'),
(297, 109, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-15 20:39:41'),
(298, 110, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-15 20:42:48'),
(299, 110, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-15 20:42:49'),
(300, 111, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-18 15:12:12'),
(301, 111, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-18 15:12:12'),
(302, 112, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-18 17:06:15'),
(303, 112, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-18 17:06:15'),
(304, 113, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-18 17:39:02'),
(305, 113, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-18 17:39:02'),
(306, 114, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-18 19:14:44'),
(307, 114, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-18 19:14:44'),
(308, 109, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-18 19:31:07'),
(309, 115, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-18 19:33:03'),
(310, 115, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-18 19:33:03'),
(311, 110, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-18 19:43:00'),
(312, 116, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-18 20:22:13'),
(313, 116, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-18 20:22:13'),
(314, 108, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-18 20:29:36'),
(315, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:30:13'),
(316, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:30:39'),
(317, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:32:08'),
(318, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:32:40'),
(319, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:33:06'),
(320, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:33:18'),
(321, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:33:46'),
(322, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:34:36'),
(323, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:44:06'),
(324, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:45:13'),
(325, 108, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-18 20:47:41'),
(326, 89, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-19 15:45:14'),
(327, 93, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-19 17:00:29'),
(328, 99, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-19 17:08:17'),
(329, 117, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-19 19:13:27'),
(330, 117, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-19 19:13:27'),
(331, 118, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-19 20:19:43'),
(332, 118, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-19 20:19:43'),
(333, 110, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-19 20:22:58'),
(334, 110, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-19 20:25:49'),
(335, 110, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-19 20:28:42'),
(336, 99, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-19 20:33:51'),
(337, 99, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-19 20:35:14'),
(338, 109, 20, 'Aprobado por Verificador', 'Aprobado por Verificador', 'Aprobado por Verificador', '', '2026-05-19 20:42:50'),
(343, 121, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-19 20:50:14'),
(344, 121, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-19 20:50:14'),
(351, 125, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-20 15:28:12'),
(352, 125, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-20 15:28:12'),
(353, 126, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-20 15:53:29'),
(354, 126, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-20 15:53:29'),
(363, 131, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-20 17:07:25'),
(364, 131, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-20 17:07:25'),
(365, 132, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-20 17:10:59'),
(366, 132, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-20 17:10:59'),
(371, 135, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-20 18:51:36'),
(372, 135, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-20 18:51:36'),
(375, 137, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-20 18:53:13'),
(376, 137, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-20 18:53:13'),
(396, 147, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-21 14:26:10'),
(397, 147, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-21 14:26:10'),
(404, 121, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-21 17:49:03'),
(405, 151, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-21 18:01:45'),
(406, 151, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-21 18:01:45'),
(417, 154, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 16:16:52'),
(418, 154, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 16:16:52'),
(419, 155, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 16:17:15'),
(420, 155, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 16:17:15'),
(421, 156, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 16:59:18'),
(422, 156, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 16:59:18'),
(423, 157, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 17:03:04'),
(424, 157, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 17:03:04'),
(425, 112, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-22 17:14:55'),
(426, 115, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-22 17:21:03'),
(427, 158, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 17:25:49'),
(428, 158, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 17:25:49'),
(429, 117, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-22 17:25:50'),
(430, 159, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 17:47:40'),
(431, 159, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 17:47:40'),
(432, 160, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 17:54:43'),
(433, 160, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 17:54:43'),
(434, 161, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 19:52:10'),
(435, 161, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 19:52:10'),
(436, 162, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 20:13:28'),
(437, 162, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 20:13:28'),
(438, 163, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 20:16:59'),
(439, 163, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 20:16:59'),
(440, 164, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 20:19:44'),
(441, 164, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 20:19:44'),
(442, 165, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-22 20:22:43'),
(443, 165, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-22 20:22:43'),
(459, 113, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-25 15:39:45'),
(460, 178, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-25 15:43:57'),
(461, 178, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-25 15:43:57'),
(464, 180, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-25 15:52:41'),
(465, 180, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-25 15:52:41'),
(466, 181, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-25 16:16:37'),
(467, 181, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-25 16:16:37'),
(468, 182, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-25 16:56:41'),
(469, 182, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-25 16:56:41'),
(470, 183, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-25 17:27:42'),
(471, 183, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-25 17:27:42'),
(472, 126, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-25 17:41:06'),
(473, 184, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-25 17:45:17'),
(474, 184, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-25 17:45:17'),
(475, 182, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-25 18:11:55'),
(476, 185, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-25 19:19:21'),
(477, 185, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-25 19:19:21'),
(480, 187, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-26 15:34:38'),
(481, 187, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-26 15:34:38'),
(482, 188, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-26 15:40:29'),
(483, 188, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-26 15:40:29'),
(484, 189, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-26 16:32:01'),
(485, 189, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-26 16:32:01'),
(486, 190, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-26 17:46:23'),
(487, 190, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-26 17:46:23'),
(492, 193, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-26 17:55:58'),
(493, 193, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-26 17:55:58'),
(504, 200, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-26 19:25:20'),
(505, 200, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-26 19:25:20'),
(506, 201, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-26 19:54:03'),
(507, 201, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-26 19:54:03'),
(508, 202, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-27 14:31:36'),
(509, 202, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-27 14:31:36'),
(510, 203, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-27 16:02:18'),
(511, 203, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-27 16:02:18'),
(512, 189, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-27 16:04:38'),
(564, 244, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-27 18:33:15'),
(565, 244, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-27 18:33:15'),
(566, 245, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-27 18:37:19'),
(567, 245, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-27 18:37:19'),
(569, 157, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-27 20:16:04'),
(570, 159, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-27 20:31:45'),
(571, 246, 12, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-28 16:27:05'),
(572, 246, 12, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-28 16:27:05'),
(573, 247, 12, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-28 16:27:05'),
(574, 248, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-28 16:57:01'),
(575, 248, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-28 16:57:01'),
(576, 249, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-28 17:21:38'),
(577, 249, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-28 17:21:38'),
(578, 250, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 14:57:15'),
(579, 250, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-29 14:57:15'),
(580, 251, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 14:57:15'),
(581, 252, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 14:57:15'),
(582, 253, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 14:57:15'),
(583, 254, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 16:27:35'),
(584, 254, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-29 16:27:35'),
(585, 255, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 17:39:42'),
(586, 255, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-29 17:39:42'),
(587, 256, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:02:47'),
(588, 256, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-29 18:02:47'),
(589, 257, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:05:32'),
(590, 257, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-29 18:05:32'),
(591, 258, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(592, 258, 18, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-05-29 18:16:35'),
(593, 259, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(594, 260, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(595, 261, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(596, 262, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(597, 263, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(598, 264, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(599, 265, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(600, 266, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(601, 267, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(602, 268, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(603, 269, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(604, 270, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(605, 271, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(606, 272, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(607, 273, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-05-29 18:16:35'),
(608, 273, 2, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 18:16:53'),
(609, 272, 2, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 18:18:49'),
(610, 181, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 18:33:48'),
(611, 184, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 18:42:24'),
(612, 187, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 19:00:58'),
(613, 271, 2, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 19:08:40'),
(614, 258, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 19:10:05'),
(615, 259, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 19:12:14'),
(616, 270, 2, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 19:24:52'),
(617, 200, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 19:43:06'),
(618, 183, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 19:46:59'),
(619, 161, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-05-29 20:45:36'),
(620, 274, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-01 17:00:40'),
(621, 274, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-01 17:00:40'),
(622, 275, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-01 17:40:23'),
(623, 275, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-01 17:40:23'),
(624, 276, 12, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-01 18:33:17'),
(625, 276, 12, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-01 18:33:17'),
(626, 277, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-01 19:08:24'),
(627, 277, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-01 19:08:24'),
(628, 278, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 14:32:12'),
(629, 278, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 14:32:12'),
(630, 279, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 14:57:16'),
(631, 279, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 14:57:16'),
(632, 203, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-02 15:46:01'),
(633, 280, 12, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 18:24:38'),
(634, 280, 12, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 18:24:38'),
(635, 281, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 18:42:31'),
(636, 281, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 18:42:31'),
(637, 282, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 18:50:27'),
(638, 282, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 18:50:27'),
(639, 283, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 18:54:02'),
(640, 283, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 18:54:02'),
(641, 255, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-02 19:06:34'),
(642, 284, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 19:09:41'),
(643, 284, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 19:09:41'),
(644, 285, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 19:09:41'),
(645, 286, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 19:12:29'),
(646, 286, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 19:12:29'),
(647, 287, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 19:12:29'),
(648, 245, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-02 19:28:07'),
(649, 288, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-02 19:43:39'),
(650, 288, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-02 19:43:39'),
(651, 275, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-03 15:17:57'),
(652, 289, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-03 16:32:25'),
(653, 289, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-03 16:32:25'),
(654, 254, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-03 18:20:29'),
(655, 290, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-03 18:40:08'),
(656, 290, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-03 18:40:08'),
(657, 249, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-03 19:21:17'),
(658, 250, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-03 19:46:40'),
(659, 291, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-03 20:00:21'),
(660, 291, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-03 20:00:21'),
(661, 292, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-03 20:04:50'),
(662, 292, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-03 20:04:50'),
(663, 293, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-03 20:11:26'),
(664, 293, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-03 20:11:26'),
(665, 294, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-04 15:57:25'),
(666, 294, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-04 15:57:25'),
(667, 282, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-04 18:54:07'),
(668, 295, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-04 19:20:03'),
(669, 295, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-04 19:20:03'),
(670, 295, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-04 19:49:10'),
(671, 296, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-05 14:28:44'),
(672, 296, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-05 14:28:44'),
(673, 297, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-05 16:14:21'),
(674, 297, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-05 16:14:21'),
(675, 298, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-05 17:03:05'),
(676, 298, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-05 17:03:05'),
(677, 299, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-05 19:36:57'),
(678, 299, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-05 19:36:57'),
(679, 300, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-05 20:13:13'),
(680, 300, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-05 20:13:13'),
(681, 301, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-05 20:23:26'),
(682, 301, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-05 20:23:26'),
(683, 302, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-05 20:42:25'),
(684, 302, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-05 20:42:25'),
(685, 277, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-05 20:43:15'),
(686, 280, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-05 20:52:18'),
(687, 303, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-08 17:09:37'),
(688, 303, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-08 17:09:37'),
(689, 281, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-08 17:47:22'),
(690, 283, 20, 'Modificado', 'En revisión', 'En revisión', '', '2026-06-08 17:57:40'),
(691, 283, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-08 17:57:55'),
(692, 290, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-08 18:00:33'),
(693, 291, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-08 18:08:06'),
(694, 292, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-08 18:10:03'),
(695, 293, 20, 'Aprobado por Verificador', 'En revisión', 'Aprobado por Verificador', '', '2026-06-08 18:16:22'),
(696, 304, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-08 18:28:06'),
(697, 304, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-08 18:28:06'),
(698, 305, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-08 18:50:30'),
(699, 305, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-08 18:50:30'),
(700, 306, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-08 20:25:22'),
(701, 306, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-08 20:25:22'),
(702, 184, 19, 'Aprobado', 'Aprobado por Verificador', 'Aprobado', '', '2026-06-08 20:28:20'),
(703, 307, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-09 16:35:12'),
(704, 307, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-09 16:35:12'),
(705, 73, 18, 'Aprobado', 'Aprobado por Verificador', 'Aprobado', '', '2026-06-09 18:51:10'),
(708, 309, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-09 19:39:44'),
(709, 309, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-09 19:39:44'),
(710, 310, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-09 20:27:20'),
(711, 310, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-09 20:27:20'),
(718, 314, 18, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-10 15:07:11'),
(719, 314, 18, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-10 15:07:11'),
(720, 281, 13, 'Aprobado', 'Aprobado por Verificador', 'Aprobado', '', '2026-06-10 15:29:53'),
(721, 315, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-10 16:21:41'),
(722, 315, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-10 16:21:41'),
(723, 316, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-10 16:22:15'),
(724, 316, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-10 16:22:15'),
(725, 317, 12, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-10 17:58:30'),
(726, 317, 12, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-10 17:58:30'),
(727, 318, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-10 20:59:26'),
(728, 318, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-10 20:59:26'),
(729, 319, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-11 15:36:35'),
(730, 319, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-11 15:36:35'),
(731, 320, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-11 17:53:29'),
(732, 320, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-11 17:53:29'),
(733, 321, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-12 15:29:04'),
(734, 321, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-12 15:29:04'),
(735, 322, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-12 15:41:14'),
(736, 322, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-12 15:41:14'),
(737, 323, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-12 17:04:12'),
(738, 323, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-12 17:04:12'),
(739, 324, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-12 17:29:01'),
(740, 324, 13, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-12 17:29:01'),
(741, 325, 13, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-12 17:29:01'),
(742, 326, 19, 'Creado', NULL, 'En revisión', 'Trámite creado en el sistema', '2026-06-15 16:11:22'),
(743, 326, 19, 'Creado', NULL, 'En revisión', 'Trámite creado', '2026-06-15 16:11:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_actividad`
--

CREATE TABLE `logs_actividad` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs_actividad`
--

INSERT INTO `logs_actividad` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `ip_address`, `user_agent`, `fecha`) VALUES
(1490, 18, 'Creó trámite', 'tramites', 197, 'Folio: 472/2026 (principal con cantidad 1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-26 18:17:33'),
(1491, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:19:20'),
(1492, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:21:53'),
(1493, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-26 19:23:50'),
(1494, 18, 'Creó trámite', 'tramites', 199, 'Folio: 472/2026 (principal con cantidad 1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-26 19:24:05'),
(1495, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:24:19'),
(1496, 13, 'Creó trámite', 'tramites', 200, 'Folio: 472/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:25:20'),
(1497, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:42:58'),
(1498, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:43:37'),
(1499, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:51:42'),
(1500, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:51:46'),
(1501, 19, 'Creó trámite', 'tramites', 201, 'Folio: 473/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 19:54:03'),
(1502, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-26 20:52:08'),
(1503, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 14:27:45'),
(1504, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 14:29:49'),
(1505, 19, 'Creó trámite', 'tramites', 202, 'Folio: 474/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 14:31:36'),
(1506, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 15:46:53'),
(1507, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 412/2026 | Archivo: .private/412_2026/croquis/croquis_6a1712304f51d_1779896880.jpg', '10.1.85.79', NULL, '2026-05-27 15:48:00'),
(1508, 20, 'Actualizo datos constancia', 'tramites', 103, 'Folio: 412/2026 | Datos constancia actualizados | Numero: 610', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 15:48:00'),
(1509, 20, 'Actualizo datos constancia', 'tramites', 103, 'Folio: 412/2026 | Datos constancia actualizados | Numero: 610-B', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 15:48:14'),
(1510, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 401/2026 | Archivo: .private/401_2026/croquis/croquis_6a1712df346a4_1779897055.jpg', '10.1.85.79', NULL, '2026-05-27 15:50:55'),
(1511, 20, 'Actualizo datos constancia', 'tramites', 92, 'Folio: 401/2026 | Datos constancia actualizados | Numero: 240-A', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 15:51:05'),
(1512, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 15:58:12'),
(1513, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 16:00:58'),
(1514, 13, 'Creó trámite', 'tramites', 203, 'Folio: 475/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 16:02:18'),
(1515, 20, 'Actualizó trámite', 'tramites', 189, 'Folio: 469/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:04:38'),
(1516, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 469/2026 | Archivo: .private/469_2026/croquis/croquis_6a171620702bd_1779897888.jpg', '10.1.85.79', NULL, '2026-05-27 16:04:48'),
(1517, 20, 'Actualizo datos constancia', 'tramites', 189, 'Folio: 469/2026 | Datos constancia actualizados | Numero: 116', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:05:21'),
(1518, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:06:25'),
(1519, 20, 'Actualizó trámite', 'tramites', 186, 'Folio: 466/2026 | En revisión → En revisión | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:07:55'),
(1520, 20, 'Actualizó trámite', 'tramites', 186, 'Folio: 466/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:08:15'),
(1521, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 16:13:27'),
(1522, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:14:17'),
(1523, 18, 'Creó trámite', 'tramites', 204, 'Folio: 476/2026 (principal con cantidad 16)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:18:27'),
(1524, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 16:20:35'),
(1525, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:20:57'),
(1526, 18, 'Logout', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:24:55'),
(1527, 2, 'Login exitoso', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:25:03'),
(1528, 20, 'Actualizó trámite', 'tramites', 204, 'Folio: 466/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:26:16'),
(1529, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 466/2026 | Archivo: .private/466_2026/croquis/croquis_6a171bcd9a9a3_1779899341.jpg', '10.1.85.79', NULL, '2026-05-27 16:29:01'),
(1530, 20, 'Actualizo datos constancia', 'tramites', 204, 'Folio: 466/2026 | Datos constancia actualizados | Numero: 103-G', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:32:29'),
(1531, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 466/2026 | Archivo: .private/466_2026/croquis/croquis_6a171d299d3db_1779899689.jpg', '10.1.85.79', NULL, '2026-05-27 16:34:49'),
(1532, 20, 'Actualizo datos constancia', 'tramites', 204, 'Folio: 466/2026 | Datos constancia actualizados | Numero: 103-F', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:35:01'),
(1533, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 16:35:24'),
(1534, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 16:38:08'),
(1535, 2, 'Logout', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:38:20'),
(1536, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:38:29'),
(1537, 18, 'Creó trámite', 'tramites', 205, 'Folio: 476/2026 (principal con cantidad 15)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:38:42'),
(1538, 18, 'Creó trámite', 'tramites', 206, 'Folio: 476/2026 (principal con cantidad 16)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:43:54'),
(1539, 20, 'Actualizo datos constancia', 'tramites', 115, 'Folio: 424/2026 | Datos constancia actualizados | Numero: 233', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 16:45:01'),
(1540, 18, 'Creó trámite', 'tramites', 207, 'Folio: 476/2026 (principal con cantidad 16)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 16:46:08'),
(1541, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 17:10:42'),
(1542, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 17:48:51'),
(1543, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 17:53:34'),
(1544, 18, 'Creó trámite', 'tramites', 208, 'Folio: 476/2026 (principal con cantidad 16)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 17:53:47'),
(1545, 18, 'Creó trámite', 'tramites', 209, 'Folio: 476/2026 (principal con cantidad 1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 18:02:02'),
(1546, 18, 'Creó trámite', 'tramites', 225, 'Folio: 477/2026 (principal con cantidad 1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 18:04:30'),
(1547, 18, 'Creó trámite', 'tramites', 228, 'Folio: 476/2026 (principal con cantidad 1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 18:06:21'),
(1548, 19, 'Creó trámite', 'tramites', 244, 'Folio: 477/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 18:33:15'),
(1549, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 18:35:45'),
(1550, 13, 'Creó trámite', 'tramites', 245, 'Folio: 478/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 18:37:19'),
(1551, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 19:18:27'),
(1552, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 19:24:48'),
(1553, 20, 'Actualizo datos constancia', 'tramites', 92, 'Folio: 401/2026 | Datos constancia actualizados | Numero: 240-A | Cantidad: 1', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 19:28:11'),
(1554, 20, 'Actualizo datos constancia', 'tramites', 92, 'Folio: 401/2026 | Datos constancia actualizados | Numero: 240-A | Cantidad: 1', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 19:34:29'),
(1555, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:38:12'),
(1556, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 401/2026 | Archivo: .private/401_2026/croquis/croquis_6a17489e5676f_1779910814.jpg', '10.1.85.79', NULL, '2026-05-27 19:40:14'),
(1557, 20, 'Actualizo datos constancia', 'tramites', 92, 'Folio: 401/2026 | Datos constancia actualizados | Numero: 240 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:40:15'),
(1558, 20, 'Actualizó trámite', 'tramites', 228, 'Folio: 476/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:45:35'),
(1559, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 19:47:33'),
(1560, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_6a174ad2e3f65_1779911378.jpg', '10.1.85.79', NULL, '2026-05-27 19:49:38'),
(1561, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-G | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:49:40'),
(1562, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_6a174b277d536_1779911463.jpg', '10.1.85.79', NULL, '2026-05-27 19:51:03'),
(1563, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-F | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:51:05'),
(1564, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_6a174b767d553_1779911542.jpg', '10.1.85.79', NULL, '2026-05-27 19:52:22'),
(1565, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-E | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:52:31'),
(1566, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_6a174bd4e56ed_1779911636.jpg', '10.1.85.79', NULL, '2026-05-27 19:53:56'),
(1567, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-D | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:53:57'),
(1568, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_6a174c2956ed2_1779911721.jpg', '10.1.85.79', NULL, '2026-05-27 19:55:21'),
(1569, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-C | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:55:25'),
(1570, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_6a174c8079a29_1779911808.jpg', '10.1.85.79', NULL, '2026-05-27 19:56:48'),
(1571, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:56:49'),
(1572, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_6a174cacf0949_1779911852.jpg', '10.1.85.79', NULL, '2026-05-27 19:57:32'),
(1573, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-C | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:57:34'),
(1574, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103- | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 19:58:20'),
(1575, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 20:01:43'),
(1576, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 20:01:58'),
(1577, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 20:10:57'),
(1578, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 20:11:02'),
(1579, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:15:34'),
(1580, 20, 'Actualizó trámite', 'tramites', 157, 'Folio: 450/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:16:04'),
(1581, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 450/2026 | Archivo: .private/450_2026/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-05-27 20:16:20'),
(1582, 20, 'Actualizo datos constancia', 'tramites', 157, 'Folio: 450/2026 | Datos constancia actualizados | Numero: 406 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:16:59'),
(1583, 20, 'Actualizo datos constancia', 'tramites', 157, 'Folio: 450/2026 | Datos constancia actualizados | Numero: 406 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:17:15'),
(1584, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: verificador@sistema.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:20:39'),
(1585, 2, 'Login exitoso', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:20:47'),
(1586, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:21:26'),
(1587, 20, 'Actualizo datos constancia', 'tramites', 157, 'Folio: 450/2026 | Datos constancia actualizados | Numero: 406 | Cantidad: 1', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 20:23:25'),
(1588, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-05-27 20:24:07'),
(1589, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:24:23'),
(1590, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 | Archivo: .private/476_2026/croquis/croquis_2.jpg', '10.1.85.79', NULL, '2026-05-27 20:26:03'),
(1591, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:26:09'),
(1592, 20, 'Actualizó trámite', 'tramites', 159, 'Folio: 452/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:31:45'),
(1593, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 452/2026 (ID: 159) | Archivo: .private/159/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-05-27 20:32:07'),
(1594, 20, 'Actualizo datos constancia', 'tramites', 159, 'Folio: 452/2026 | Datos constancia actualizados | Numero: 318 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:35:31'),
(1595, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:47:06'),
(1596, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 450/2026 (ID: 157) | Archivo: .private/157/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-05-27 20:47:23'),
(1597, 20, 'Actualizo datos constancia', 'tramites', 157, 'Folio: 450/2026 | Datos constancia actualizados | Numero: 406-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-27 20:47:30'),
(1598, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-27 20:53:52'),
(1599, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: jairo.lopez133@gmail.com', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 16:14:59'),
(1600, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: jairo.lopez133@gmail.com', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 16:15:22'),
(1601, 12, 'Login exitoso', 'usuarios', 12, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 16:15:43'),
(1602, 12, 'Creó trámite', 'tramites', 246, 'Folio: 479/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 16:27:05'),
(1603, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 16:55:10'),
(1604, 19, 'Creó trámite', 'tramites', 248, 'Folio: 480/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 16:57:01'),
(1605, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 16:57:28'),
(1606, 19, 'Creó trámite', 'tramites', 249, 'Folio: 481/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 17:21:38'),
(1607, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 19:38:26'),
(1608, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 21:06:16'),
(1609, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 14:53:43'),
(1610, 19, 'Creó trámite', 'tramites', 250, 'Folio: 482/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 14:57:15'),
(1611, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 15:07:46'),
(1612, 18, 'Logout', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 15:43:42'),
(1613, 2, 'Login exitoso', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 15:43:50'),
(1614, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 15:45:39'),
(1615, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 15:45:58'),
(1616, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 15:50:21'),
(1617, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 15:56:12'),
(1618, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 16:00:05'),
(1619, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 16:26:20'),
(1620, 13, 'Creó trámite', 'tramites', 254, 'Folio: 483/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 16:27:35'),
(1621, 2, 'Login exitoso', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 16:43:48'),
(1622, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 16:44:02'),
(1623, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:19:53'),
(1624, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 17:21:01'),
(1625, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-F | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:21:03'),
(1626, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:24:15'),
(1627, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '10.1.85.79', NULL, '2026-05-29 17:24:39'),
(1628, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:24:40'),
(1629, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '10.1.85.79', NULL, '2026-05-29 17:26:15'),
(1630, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:26:31'),
(1631, 2, 'Logout', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:29:15'),
(1632, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: ventanilla@vet.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:29:21'),
(1633, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: ventanilla@vet.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:29:28'),
(1634, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:29:37'),
(1635, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '10.1.85.79', NULL, '2026-05-29 17:31:10'),
(1636, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 17:31:28'),
(1637, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '10.1.85.79', NULL, '2026-05-29 17:32:29'),
(1638, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:32:35'),
(1639, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 17:33:20'),
(1640, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '10.1.85.79', NULL, '2026-05-29 17:33:31'),
(1641, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:33:33'),
(1642, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 17:34:14'),
(1643, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 17:34:17'),
(1644, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '10.1.85.79', NULL, '2026-05-29 17:36:58'),
(1645, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-D | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:36:59'),
(1646, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:38:00'),
(1647, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:38:44'),
(1648, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:39:01'),
(1649, 18, 'Logout', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:39:33'),
(1650, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:39:37'),
(1651, 2, 'Login exitoso', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:39:40'),
(1652, 13, 'Creó trámite', 'tramites', 255, 'Folio: 484/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 17:39:42'),
(1653, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:39:48'),
(1654, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:39:51'),
(1655, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:40:04'),
(1656, 20, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 17:40:16'),
(1657, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 17:42:15'),
(1658, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:42:16'),
(1659, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 17:45:05'),
(1660, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:45:07'),
(1661, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 17:46:24'),
(1662, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:46:26'),
(1663, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 17:47:07'),
(1664, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:47:09'),
(1665, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 17:48:14'),
(1666, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:48:17'),
(1667, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 17:48:57'),
(1668, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 17:48:58'),
(1669, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 18:01:13'),
(1670, 19, 'Creó trámite', 'tramites', 256, 'Folio: 485/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 18:02:47'),
(1671, 19, 'Creó trámite', 'tramites', 257, 'Folio: 486/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 18:05:32'),
(1672, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 18:07:31'),
(1673, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:07:33'),
(1674, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 18:10:24'),
(1675, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-N | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:10:26'),
(1676, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:11:48'),
(1677, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 18:11:58'),
(1678, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:12:03'),
(1679, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:12:48'),
(1680, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 18:13:25'),
(1681, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 18:13:34'),
(1682, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:13:35'),
(1683, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 476/2026 (ID: 228) | Archivo: .private/228/croquis/croquis_228_1.jpg', '::1', NULL, '2026-05-29 18:14:17'),
(1684, 2, 'Actualizo datos constancia', 'tramites', 228, 'Folio: 476/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:14:18'),
(1685, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 18:16:06'),
(1686, 18, 'Creó trámite', 'tramites', 258, 'Folio: 487/2026 (principal con cantidad 1)', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 18:16:35'),
(1687, 2, 'Actualizó trámite', 'tramites', 273, 'Folio: 487/2026 | En revisión → Aprobado por Verificador | Verificador: JUAN CARLOS VERIFICADOR GÓMEZ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:16:53'),
(1688, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_258_1.jpg', '::1', NULL, '2026-05-29 18:17:35'),
(1689, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_258_1.jpg', '::1', NULL, '2026-05-29 18:17:41'),
(1690, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:17:42'),
(1691, 2, 'Actualizó trámite', 'tramites', 272, 'Folio: 487/2026 | En revisión → Aprobado por Verificador | Verificador: JUAN CARLOS VERIFICADOR GÓMEZ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:18:49'),
(1692, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_258_1.jpg', '::1', NULL, '2026-05-29 18:19:26'),
(1693, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 18:19:29'),
(1694, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 18:33:14'),
(1695, 20, 'Actualizó trámite', 'tramites', 181, 'Folio: 461/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 18:33:48'),
(1696, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 461/2026 (ID: 181) | Archivo: .private/181/croquis/croquis_181_1.jpg', '10.1.85.79', NULL, '2026-05-29 18:34:01'),
(1697, 20, 'Actualizo datos constancia', 'tramites', 181, 'Folio: 461/2026 | Datos constancia actualizados | Numero: 326-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 18:34:58');
INSERT INTO `logs_actividad` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `ip_address`, `user_agent`, `fecha`) VALUES
(1698, 20, 'Actualizó trámite', 'tramites', 184, 'Folio: 464/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 18:42:24'),
(1699, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 464/2026 (ID: 184) | Archivo: .private/184/croquis/croquis_184_1.jpg', '10.1.85.79', NULL, '2026-05-29 18:42:38'),
(1700, 20, 'Actualizo datos constancia', 'tramites', 184, 'Folio: 464/2026 | Datos constancia actualizados | Numero: 208 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 18:43:02'),
(1701, 20, 'Actualizó trámite', 'tramites', 187, 'Folio: 467/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:00:58'),
(1702, 2, 'Login exitoso', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:01:02'),
(1703, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 467/2026 (ID: 187) | Archivo: .private/187/croquis/croquis_187.jpg', '10.1.85.79', NULL, '2026-05-29 19:01:09'),
(1704, 20, 'Actualizo datos constancia', 'tramites', 187, 'Folio: 467/2026 | Datos constancia actualizados | Numero: 131 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:01:29'),
(1705, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:01:30'),
(1706, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:01:41'),
(1707, 2, 'Actualizó trámite', 'tramites', 271, 'Folio: 487/2026 | En revisión → Aprobado por Verificador | Verificador: JUAN CARLOS VERIFICADOR GÓMEZ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:08:40'),
(1708, 20, 'Actualizó trámite', 'tramites', 258, 'Folio: 487/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:10:05'),
(1709, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '10.1.85.79', NULL, '2026-05-29 19:10:46'),
(1710, 20, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:10:47'),
(1711, 20, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:11:04'),
(1712, 20, 'Actualizó trámite', 'tramites', 259, 'Folio: 487/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:12:14'),
(1713, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '10.1.85.79', NULL, '2026-05-29 19:12:54'),
(1714, 20, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:12:57'),
(1715, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:15:21'),
(1716, 2, 'Actualizó trámite', 'tramites', 270, 'Folio: 487/2026 | En revisión → Aprobado por Verificador | Verificador: JUAN CARLOS VERIFICADOR GÓMEZ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:24:52'),
(1717, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:25:13'),
(1718, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:25:14'),
(1719, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:25:41'),
(1720, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:25:42'),
(1721, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:25:54'),
(1722, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:26:07'),
(1723, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:29:56'),
(1724, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:29:57'),
(1725, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:31:31'),
(1726, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:31:37'),
(1727, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:32:14'),
(1728, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:32:15'),
(1729, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:32:44'),
(1730, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:32:51'),
(1731, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 19:37:11'),
(1732, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 19:37:21'),
(1733, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:42:45'),
(1734, 20, 'Actualizó trámite', 'tramites', 200, 'Folio: 472/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:43:06'),
(1735, 20, 'Actualizo datos constancia', 'tramites', 200, 'Folio: 472/2026 | Datos constancia actualizados | Numero: 104 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:44:02'),
(1736, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 472/2026 (ID: 200) | Archivo: .private/200/croquis/croquis_200.jpg', '10.1.85.79', NULL, '2026-05-29 19:44:08'),
(1737, 20, 'Actualizo datos constancia', 'tramites', 200, 'Folio: 472/2026 | Datos constancia actualizados | Numero: 104 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:44:09'),
(1738, 20, 'Actualizó trámite', 'tramites', 183, 'Folio: 463/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 19:46:59'),
(1739, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 20:06:27'),
(1740, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 20:06:35'),
(1741, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 20:07:03'),
(1742, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 20:07:04'),
(1743, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: .private/258/croquis/croquis_251.jpg', '::1', NULL, '2026-05-29 20:10:36'),
(1744, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 20:10:48'),
(1745, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 20:11:02'),
(1746, 2, 'Logout', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 20:12:27'),
(1747, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-29 20:12:32'),
(1748, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 20:19:04'),
(1749, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 20:31:39'),
(1750, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 463/2026 (ID: 183) | Archivo: .private/183/croquis/croquis_183.jpg', '10.1.85.79', NULL, '2026-05-29 20:32:37'),
(1751, 20, 'Actualizo datos constancia', 'tramites', 183, 'Folio: 463/2026 | Datos constancia actualizados | Numero: 206-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 20:33:59'),
(1752, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 20:45:17'),
(1753, 20, 'Actualizó trámite', 'tramites', 161, 'Folio: 454/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 20:45:36'),
(1754, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 454/2026 (ID: 161) | Archivo: .private/161/croquis/croquis_161.jpg', '10.1.85.79', NULL, '2026-05-29 20:45:51'),
(1755, 20, 'Actualizo datos constancia', 'tramites', 161, 'Folio: 454/2026 | Datos constancia actualizados | Numero: 510 | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-29 20:46:32'),
(1756, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 14:55:05'),
(1757, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:11:48'),
(1758, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:13:33'),
(1759, 20, 'Actualizo datos constancia', 'tramites', 159, 'Folio: 452/2026 | Datos constancia actualizados | Numero: 318 | Cantidad: 1', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:14:06'),
(1760, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:14:40'),
(1761, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:14:43'),
(1762, 20, 'Actualizo datos constancia', 'tramites', 183, 'Folio: 463/2026 | Datos constancia actualizados | Numero: 206-A | Cantidad: 1', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:14:58'),
(1763, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: jairo.lopez133@gmail.com', '10.1.85.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 OPR/131.0.0.0', '2026-06-01 15:16:26'),
(1764, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:18:56'),
(1765, 20, 'Actualizo datos constancia', 'tramites', 117, 'Folio: 426/2026 | Datos constancia actualizados | Numero: 330 | Cantidad: 1', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:22:02'),
(1766, 20, 'Actualizo datos constancia', 'tramites', 159, 'Folio: 452/2026 | Datos constancia actualizados | Numero: 318 | Cantidad: 1', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:22:15'),
(1767, 20, 'Actualizo datos constancia', 'tramites', 113, 'Folio: 422/2026 | Datos constancia actualizados | Numero: 302-A | Cantidad: 1', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 15:29:57'),
(1768, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 16:55:21'),
(1769, 13, 'Creó trámite', 'tramites', 274, 'Folio: 488/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 17:00:40'),
(1770, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 17:27:05'),
(1771, 20, 'Actualizo datos constancia', 'tramites', 112, 'Folio: 421/2026 | Datos constancia actualizados | Numero: 110 | Cantidad: 1', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 17:36:16'),
(1772, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 17:39:10'),
(1773, 13, 'Creó trámite', 'tramites', 275, 'Folio: 489/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 17:40:23'),
(1774, 2, 'Login exitoso', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 17:54:53'),
(1775, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: croquis/258/croquis_258_1780336539.jpg', '::1', NULL, '2026-06-01 17:55:39'),
(1776, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 17:55:46'),
(1777, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 487/2026 (ID: 258) | Archivo: croquis/258/croquis_258_1780336608.jpg', '::1', NULL, '2026-06-01 17:56:48'),
(1778, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 17:56:50'),
(1779, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-A | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 18:00:38'),
(1780, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103 | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 18:00:45'),
(1781, 2, 'Actualizo datos constancia', 'tramites', 258, 'Folio: 487/2026 | Datos constancia actualizados | Numero: 103-B | Cantidad: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 18:01:39'),
(1782, 12, 'Login exitoso', 'usuarios', 12, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 18:30:05'),
(1783, 12, 'Creó trámite', 'tramites', 276, 'Folio: 490/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 18:33:17'),
(1784, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 19:07:20'),
(1785, 13, 'Creó trámite', 'tramites', 277, 'Folio: 491/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 19:08:24'),
(1786, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 19:33:46'),
(1787, 20, 'Actualizo datos constancia', 'tramites', 113, 'Folio: 422/2026 | Datos constancia actualizados | Numero: 302-A | Cantidad: 1', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 19:34:41'),
(1788, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 19:36:20'),
(1789, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para folio: 422/2026 (ID: 113) | Archivo: croquis/113/croquis_113_1780342691.jpg', '10.1.85.79', NULL, '2026-06-01 19:38:11'),
(1790, 20, 'Actualizo datos constancia', 'tramites', 113, 'Folio: 422/2026 | Datos constancia actualizados | Numero: 302-A | Cantidad: 1', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 19:38:12'),
(1791, 2, 'Login exitoso', 'usuarios', 2, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-01 20:49:35'),
(1792, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: ventanilla@vet.com', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 14:18:22'),
(1793, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 14:18:37'),
(1794, 18, 'Logout', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 14:29:16'),
(1795, 2, 'Login exitoso', 'usuarios', 2, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 14:29:22'),
(1796, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 14:30:30'),
(1797, 13, 'Creó trámite', 'tramites', 278, 'Folio: 492/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 14:32:12'),
(1798, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 271 | Archivo: .private/271/croquis/croquis_1.jpg', '10.1.85.9', NULL, '2026-06-02 14:50:46'),
(1799, 2, 'Actualizo datos constancia', 'tramites', 271, 'Folio entrada: 487/2026 | Subtramite id: 271 | Datos constancia | Numero: 103-A | Folio salida: 257/2026', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 14:50:52'),
(1800, 2, 'Actualizo datos constancia', 'tramites', 259, 'Folio entrada: 487/2026 | Subtramite id: 259 | Datos constancia | Numero: 103 | Folio salida: 258/2026', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 14:51:17'),
(1801, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 259 | Archivo: .private/259/croquis/croquis_1.jpg', '10.1.85.9', NULL, '2026-06-02 14:51:32'),
(1802, 2, 'Actualizo datos constancia', 'tramites', 259, 'Folio entrada: 487/2026 | Subtramite id: 259 | Datos constancia | Numero: 103 | Folio salida: 258/2026', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 14:51:34'),
(1803, 13, 'Creó trámite', 'tramites', 279, 'Folio: 493/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 14:57:16'),
(1804, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 15:33:51'),
(1805, 20, 'Actualizó trámite', 'tramites', 203, 'Folio: 475/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 15:46:01'),
(1806, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 203 | Archivo: .private/203/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-02 15:46:26'),
(1807, 20, 'Actualizo datos constancia', 'tramites', 203, 'Folio entrada: 475/2026 | Subtramite id: 203 | Datos constancia | Numero: 302 | Folio salida: 259/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 15:47:02'),
(1808, 2, 'Login exitoso', 'usuarios', 2, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 15:52:03'),
(1809, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 272 | Archivo: .private/272/croquis/croquis_1.jpg', '10.1.85.9', NULL, '2026-06-02 15:52:44'),
(1810, 2, 'Actualizo datos constancia', 'tramites', 272, 'Folio entrada: 487/2026 | Subtramite id: 272 | Datos constancia | Numero: 103-B | Folio salida: 260/2026', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 15:52:46'),
(1811, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: jairo.lopez133@gmail.com', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:22:19'),
(1812, 13, 'Logout', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:22:28'),
(1813, 12, 'Login exitoso', 'usuarios', 12, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:22:49'),
(1814, 12, 'Creó trámite', 'tramites', 280, 'Folio: 494/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:24:38'),
(1815, 12, 'Logout', 'usuarios', 12, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:25:31'),
(1816, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:29:54'),
(1817, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:38:41'),
(1818, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:38:43'),
(1819, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:39:36'),
(1820, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:39:39'),
(1821, 19, 'Creó trámite', 'tramites', 281, 'Folio: 495/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:42:31'),
(1822, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:47:42'),
(1823, 13, 'Creó trámite', 'tramites', 282, 'Folio: 496/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:50:27'),
(1824, 19, 'Creó trámite', 'tramites', 283, 'Folio: 497/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 18:54:02'),
(1825, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:03:43'),
(1826, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:03:45'),
(1827, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 19:05:26'),
(1828, 20, 'Actualizó trámite', 'tramites', 255, 'Folio: 484/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 19:06:34'),
(1829, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 255 | Archivo: .private/255/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-02 19:07:23'),
(1830, 20, 'Actualizo datos constancia', 'tramites', 255, 'Folio entrada: 484/2026 | Subtramite id: 255 | Datos constancia | Numero: 709 | Folio salida: 262/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 19:08:06'),
(1831, 13, 'Creó trámite', 'tramites', 284, 'Folio: 498/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:09:41'),
(1832, 13, 'Creó trámite', 'tramites', 286, 'Folio: 499/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:12:29'),
(1833, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 19:27:50'),
(1834, 20, 'Actualizó trámite', 'tramites', 245, 'Folio: 478/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 19:28:07'),
(1835, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 245 | Archivo: .private/245/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-02 19:28:17'),
(1836, 20, 'Actualizo datos constancia', 'tramites', 245, 'Folio entrada: 478/2026 | Subtramite id: 245 | Datos constancia | Numero: 701 | Folio salida: 263/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 19:29:20'),
(1837, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:39:36'),
(1838, 19, 'Creó trámite', 'tramites', 288, 'Folio: 500/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:43:39'),
(1839, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:50:17'),
(1840, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:50:21'),
(1841, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:52:12'),
(1842, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:52:16'),
(1843, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:52:24'),
(1844, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:52:27'),
(1845, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:53:35'),
(1846, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:53:37'),
(1847, 20, 'Actualizo datos constancia', 'tramites', 182, 'Folio entrada: 462/2026 | Subtramite id: 182 | Datos constancia | Numero: 916 | Folio salida: 264/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:56:22'),
(1848, 20, 'Actualizo datos constancia', 'tramites', 183, 'Folio entrada: 463/2026 | Subtramite id: 183 | Datos constancia | Numero: 206-A | Folio salida: 252/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 20:00:21'),
(1849, 20, 'Actualizo datos constancia', 'tramites', 183, 'Folio entrada: 463/2026 | Subtramite id: 183 | Datos constancia | Numero: 206-A | Folio salida: 252/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 20:02:40'),
(1850, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 20:13:58'),
(1851, 20, 'Actualizo datos constancia', 'tramites', 189, 'Folio entrada: 469/2026 | Subtramite id: 189 | Datos constancia | Numero: 116 | Folio salida: 266/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 20:16:21'),
(1852, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 20:20:40'),
(1853, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 183 | Archivo: .private/183/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-02 20:21:06'),
(1854, 20, 'Actualizo datos constancia', 'tramites', 183, 'Folio entrada: 463/2026 | Subtramite id: 183 | Datos constancia | Numero: 206-A | Folio salida: 252/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-02 20:21:09'),
(1855, 13, 'Logout', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 21:01:57'),
(1856, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 14:29:01'),
(1857, 20, 'Actualizo datos constancia', 'tramites', 203, 'Folio entrada: 475/2026 | Subtramite id: 203 | Datos constancia | Numero: 302 | Folio salida: 259/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 14:41:03'),
(1858, 20, 'Actualizo datos constancia', 'tramites', 203, 'Folio entrada: 475/2026 | Subtramite id: 203 | Datos constancia | Numero: 302 | Folio salida: 259/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 14:41:50'),
(1859, 20, 'Actualizo datos constancia', 'tramites', 255, 'Folio entrada: 484/2026 | Subtramite id: 255 | Datos constancia | Numero: 709 | Folio salida: 262/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 14:53:48'),
(1860, 20, 'Actualizo datos constancia', 'tramites', 245, 'Folio entrada: 478/2026 | Subtramite id: 245 | Datos constancia | Numero: 701 | Folio salida: 263/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 15:09:46'),
(1861, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 15:17:01'),
(1862, 20, 'Actualizó trámite', 'tramites', 275, 'Folio: 489/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 15:17:57'),
(1863, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 275 | Archivo: .private/275/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 15:18:30'),
(1864, 20, 'Actualizo datos constancia', 'tramites', 275, 'Folio entrada: 489/2026 | Subtramite id: 275 | Datos constancia | Numero: 104-E | Folio salida: 269/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 15:23:05'),
(1865, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 161 | Archivo: .private/161/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 16:00:43'),
(1866, 20, 'Actualizo datos constancia', 'tramites', 161, 'Folio entrada: 454/2026 | Subtramite id: 161 | Datos constancia | Numero: 510 | Folio salida: 265/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:00:45'),
(1867, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:00:51'),
(1868, 20, 'Actualizo datos constancia', 'tramites', 159, 'Folio entrada: 452/2026 | Subtramite id: 159 | Datos constancia | Numero: 318 | Folio salida: 249/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:01:16'),
(1869, 20, 'Actualizo datos constancia', 'tramites', 161, 'Folio entrada: 454/2026 | Subtramite id: 161 | Datos constancia | Numero: 302 | Folio salida: 265/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:01:50'),
(1870, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 161 | Archivo: .private/161/croquis/croquis_2.jpg', '10.1.85.79', NULL, '2026-06-03 16:07:20'),
(1871, 20, 'Actualizo datos constancia', 'tramites', 161, 'Folio entrada: 454/2026 | Subtramite id: 161 | Datos constancia | Numero: 510 | Folio salida: 265/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:07:34'),
(1872, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 181 | Archivo: .private/181/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 16:10:14'),
(1873, 20, 'Actualizo datos constancia', 'tramites', 181, 'Folio entrada: 461/2026 | Subtramite id: 181 | Datos constancia | Numero: 326-B | Folio salida: 270/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:10:24'),
(1874, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:10:47'),
(1875, 20, 'Actualizo datos constancia', 'tramites', 181, 'Folio entrada: 461/2026 | Subtramite id: 181 | Datos constancia | Numero: 326-B | Folio salida: 270/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:12:59'),
(1876, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 181 | Archivo: .private/181/croquis/croquis_2.jpg', '10.1.85.79', NULL, '2026-06-03 16:17:54'),
(1877, 20, 'Actualizo datos constancia', 'tramites', 181, 'Folio entrada: 461/2026 | Subtramite id: 181 | Datos constancia | Numero: 326-A | Folio salida: 270/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:17:55'),
(1878, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 161 | Archivo: .private/161/croquis/croquis_3.jpg', '10.1.85.79', NULL, '2026-06-03 16:19:38'),
(1879, 20, 'Actualizo datos constancia', 'tramites', 161, 'Folio entrada: 454/2026 | Subtramite id: 161 | Datos constancia | Numero: 510 | Folio salida: 265/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:19:43'),
(1880, 2, 'Login exitoso', 'usuarios', 2, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:23:23'),
(1881, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 184 | Archivo: .private/184/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 16:24:43'),
(1882, 20, 'Actualizo datos constancia', 'tramites', 184, 'Folio entrada: 464/2026 | Subtramite id: 184 | Datos constancia | Numero: 208 | Folio salida: 267/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:24:45'),
(1883, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 33 | Archivo: .private/33/croquis/croquis_1.png', '10.1.85.9', NULL, '2026-06-03 16:24:46'),
(1884, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 187 | Archivo: .private/187/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 16:27:26'),
(1885, 20, 'Actualizo datos constancia', 'tramites', 187, 'Folio entrada: 467/2026 | Subtramite id: 187 | Datos constancia | Numero: 131 | Folio salida: 268/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:27:28'),
(1886, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 200 | Archivo: .private/200/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 16:28:39'),
(1887, 20, 'Actualizo datos constancia', 'tramites', 200, 'Folio entrada: 472/2026 | Subtramite id: 200 | Datos constancia | Numero: 104 | Folio salida: 256/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 16:28:40'),
(1888, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:28:40'),
(1889, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:28:44'),
(1890, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 33 | Archivo: .private/33/croquis/croquis_2.png', '10.1.85.9', NULL, '2026-06-03 16:29:15'),
(1891, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 33 | Archivo: .private/33/croquis/croquis_3.png', '10.1.85.9', NULL, '2026-06-03 16:29:39'),
(1892, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:32:01'),
(1893, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:32:07'),
(1894, 19, 'Creó trámite', 'tramites', 289, 'Folio: 501/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:32:25'),
(1895, 20, 'Actualizo datos constancia', 'tramites', 117, 'Folio entrada: 426/2026 | Subtramite id: 117 | Datos constancia | Numero: 330 | Folio salida: 253/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:32:34'),
(1896, 20, 'Actualizo datos constancia', 'tramites', 117, 'Folio entrada: 426/2026 | Subtramite id: 117 | Datos constancia | Numero: 330 | Folio salida: 253/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:32:37'),
(1897, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 33 | Archivo: .private/33/croquis/croquis_4.png', '10.1.85.9', NULL, '2026-06-03 16:35:29'),
(1898, 2, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 33 | Archivo: .private/33/croquis/croquis_1.jpg', '10.1.85.9', NULL, '2026-06-03 16:35:48'),
(1899, 20, 'Actualizo datos constancia', 'tramites', 112, 'Folio entrada: 421/2026 | Subtramite id: 112 | Datos constancia | Numero: 110 | Folio salida: 255/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:39:17'),
(1900, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 16:59:26'),
(1901, 20, 'Actualizo datos constancia', 'tramites', 184, 'Folio entrada: 464/2026 | Subtramite id: 184 | Datos constancia | Numero: 208 | Folio salida: 267/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 17:00:35'),
(1902, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:05:16');
INSERT INTO `logs_actividad` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `ip_address`, `user_agent`, `fecha`) VALUES
(1903, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:05:31'),
(1904, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:06:18'),
(1905, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:06:21'),
(1906, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:07:46'),
(1907, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:07:50'),
(1908, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:09:51'),
(1909, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:09:55'),
(1910, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 18:20:00'),
(1911, 20, 'Actualizó trámite', 'tramites', 254, 'Folio: 483/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 18:20:29'),
(1912, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 254 | Archivo: .private/254/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 18:20:45'),
(1913, 20, 'Actualizo datos constancia', 'tramites', 254, 'Folio entrada: 483/2026 | Subtramite id: 254 | Datos constancia | Numero: 117 | Folio salida: 271/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 18:21:54'),
(1914, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:37:12'),
(1915, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:37:15'),
(1916, 19, 'Creó trámite', 'tramites', 290, 'Folio: 502/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:40:08'),
(1917, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:59:43'),
(1918, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 18:59:48'),
(1919, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 19:05:57'),
(1920, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 19:06:00'),
(1921, 20, 'Actualizo datos constancia', 'tramites', 245, 'Folio entrada: 478/2026 | Subtramite id: 245 | Datos constancia | Numero: 701 | Folio salida: 263/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 19:07:29'),
(1922, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 19:20:59'),
(1923, 20, 'Actualizó trámite', 'tramites', 249, 'Folio: 481/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 19:21:17'),
(1924, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 249 | Archivo: .private/249/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 19:21:34'),
(1925, 20, 'Actualizo datos constancia', 'tramites', 249, 'Folio entrada: 481/2026 | Subtramite id: 249 | Datos constancia | Numero: 105-A | Folio salida: 272/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 19:23:06'),
(1926, 20, 'Actualizó trámite', 'tramites', 250, 'Folio: 482/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 19:46:40'),
(1927, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 251 | Archivo: .private/251/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 19:47:12'),
(1928, 20, 'Actualizo datos constancia', 'tramites', 251, 'Folio entrada: 482/2026 | Subtramite id: 251 | Datos constancia | Numero: 203 | Folio salida: 273/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 19:47:47'),
(1929, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 253 | Archivo: .private/253/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-03 19:56:08'),
(1930, 20, 'Actualizo datos constancia', 'tramites', 253, 'Folio entrada: 482/2026 | Subtramite id: 253 | Datos constancia | Numero: 205 | Folio salida: 274/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-03 19:56:19'),
(1931, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 19:57:38'),
(1932, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 19:57:41'),
(1933, 19, 'Creó trámite', 'tramites', 291, 'Folio: 503/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 20:00:21'),
(1934, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 20:00:53'),
(1935, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 20:00:55'),
(1936, 13, 'Creó trámite', 'tramites', 292, 'Folio: 504/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 20:04:50'),
(1937, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 20:09:04'),
(1938, 13, 'Creó trámite', 'tramites', 293, 'Folio: 505/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 20:11:26'),
(1939, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 15:55:29'),
(1940, 19, 'Creó trámite', 'tramites', 294, 'Folio: 506/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 15:57:25'),
(1941, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 18:44:45'),
(1942, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 18:45:12'),
(1943, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 18:45:15'),
(1944, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 18:50:06'),
(1945, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-04 18:53:50'),
(1946, 20, 'Actualizó trámite', 'tramites', 282, 'Folio: 496/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-04 18:54:07'),
(1947, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 282 | Archivo: .private/282/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-04 18:54:23'),
(1948, 20, 'Actualizo datos constancia', 'tramites', 282, 'Folio entrada: 496/2026 | Subtramite id: 282 | Datos constancia | Numero: 210 | Folio salida: 275/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-04 18:55:58'),
(1949, 20, 'Actualizo datos constancia', 'tramites', 282, 'Folio entrada: 496/2026 | Subtramite id: 282 | Datos constancia | Numero: 210 | Folio salida: 275/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 19:01:13'),
(1950, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 19:18:52'),
(1951, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 19:18:56'),
(1952, 19, 'Creó trámite', 'tramites', 295, 'Folio: 507/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 19:20:03'),
(1953, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-04 19:48:58'),
(1954, 20, 'Actualizó trámite', 'tramites', 295, 'Folio: 507/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-04 19:49:10'),
(1955, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-04 19:50:47'),
(1956, 20, 'Actualizo datos constancia', 'tramites', 295, 'Folio entrada: 507/2026 | Subtramite id: 295 | Datos constancia | Numero: 306 | Folio salida: 276/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-04 19:52:32'),
(1957, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 20:01:50'),
(1958, 13, 'Logout', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 20:47:50'),
(1959, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 20:48:02'),
(1960, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 14:18:06'),
(1961, 13, 'Creó trámite', 'tramites', 296, 'Folio: 508/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 14:28:44'),
(1962, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 14:37:29'),
(1963, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 14:50:42'),
(1964, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 16:11:06'),
(1965, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 16:12:02'),
(1966, 13, 'Creó trámite', 'tramites', 297, 'Folio: 509/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 16:14:21'),
(1967, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 16:58:55'),
(1968, 19, 'Creó trámite', 'tramites', 298, 'Folio: 510/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 17:03:05'),
(1969, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-05 18:14:01'),
(1970, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 18:55:01'),
(1971, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 19:31:06'),
(1972, 13, 'Creó trámite', 'tramites', 299, 'Folio: 511/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 19:36:57'),
(1973, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 20:07:47'),
(1974, 13, 'Creó trámite', 'tramites', 300, 'Folio: 512/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 20:13:13'),
(1975, 19, 'Creó trámite', 'tramites', 301, 'Folio: 513/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 20:23:26'),
(1976, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 20:38:15'),
(1977, 13, 'Creó trámite', 'tramites', 302, 'Folio: 514/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 20:42:25'),
(1978, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-05 20:42:38'),
(1979, 20, 'Actualizó trámite', 'tramites', 277, 'Folio: 491/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-05 20:43:15'),
(1980, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 20:44:44'),
(1981, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 277 | Archivo: .private/277/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-05 20:45:02'),
(1982, 20, 'Actualizo datos constancia', 'tramites', 277, 'Folio entrada: 491/2026 | Subtramite id: 277 | Datos constancia | Numero: 513 | Folio salida: 277/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-05 20:45:02'),
(1983, 20, 'Actualizó trámite', 'tramites', 280, 'Folio: 494/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-05 20:52:18'),
(1984, 20, 'Actualizo datos constancia', 'tramites', 280, 'Folio entrada: 494/2026 | Subtramite id: 280 | Datos constancia | Numero: 209 | Folio salida: 278/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-05 20:54:09'),
(1985, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-05 20:59:01'),
(1986, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 14:22:36'),
(1987, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 14:50:19'),
(1988, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 15:01:46'),
(1989, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 15:02:24'),
(1990, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 15:02:27'),
(1991, 20, 'Actualizo datos constancia', 'tramites', 187, 'Folio entrada: 467/2026 | Subtramite id: 187 | Datos constancia | Numero: 131 | Folio salida: 268/2026', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 15:07:36'),
(1992, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 16:00:34'),
(1993, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 16:24:10'),
(1994, 20, 'Actualizo datos constancia', 'tramites', 161, 'Folio entrada: 454/2026 | Subtramite id: 161 | Datos constancia | Numero: 510 | Folio salida: 265/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 16:25:13'),
(1995, NULL, 'Intento de login - usuario no existe', 'usuarios', NULL, 'Email: dir.planeacionydu@gmial.com', '10.1.85.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:29:41'),
(1996, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: dir.planeacionydu@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:30:25'),
(1997, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: dir.planeacionydu@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:30:49'),
(1998, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: dir.planeacionydu@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:31:14'),
(1999, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: dir.planeacionydu@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:31:26'),
(2000, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: dir.planeacionydu@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:31:33'),
(2001, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: dir.planeacionydu@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:31:44'),
(2002, 15, 'Login exitoso', 'usuarios', 15, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:34:24'),
(2003, 15, 'Aprobo solicitud registro', 'solicitudes_registro', 25, 'Solicitud aprobada: DIR PLANEACIÓN Y DESARROLLO URBANO / Rol: Ventanilla', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:34:33'),
(2004, 15, 'Actualizó usuario', 'usuarios', 21, 'Usuario: DIR PLANEACIÓN Y DESARROLLO URBANO (Administrador)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:35:11'),
(2005, 15, 'Logout', 'usuarios', 15, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:35:15'),
(2006, 21, 'Login exitoso', 'usuarios', 21, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:35:23'),
(2007, 21, 'Logout', 'usuarios', 21, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 16:35:26'),
(2008, 21, 'Login exitoso', 'usuarios', 21, NULL, '10.1.85.228', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 16:44:48'),
(2009, 21, 'Logout', 'usuarios', 21, NULL, '10.1.85.228', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 16:49:54'),
(2010, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: jairo.lopez133@gmail.com', '10.1.85.228', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 16:50:22'),
(2011, 12, 'Login exitoso', 'usuarios', 12, NULL, '10.1.85.228', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 16:51:02'),
(2012, 12, 'Logout', 'usuarios', 12, NULL, '10.1.85.228', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 16:58:59'),
(2013, 4, 'Login exitoso', 'usuarios', 4, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 17:01:06'),
(2014, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 17:08:41'),
(2015, 19, 'Creó trámite', 'tramites', 303, 'Folio: 515/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 17:09:37'),
(2016, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-08 17:09:50'),
(2017, 12, 'Login exitoso', 'usuarios', 12, NULL, '10.1.85.228', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 17:10:28'),
(2018, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:23:10'),
(2019, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 280 | Archivo: .private/280/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-08 17:23:48'),
(2020, 20, 'Actualizo datos constancia', 'tramites', 280, 'Folio entrada: 494/2026 | Subtramite id: 280 | Datos constancia | Numero: 209 | Folio salida: 278/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:23:52'),
(2021, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 295 | Archivo: .private/295/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-08 17:40:42'),
(2022, 20, 'Actualizo datos constancia', 'tramites', 295, 'Folio entrada: 507/2026 | Subtramite id: 295 | Datos constancia | Numero: 306 | Folio salida: 276/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:40:44'),
(2023, 20, 'Actualizo datos constancia', 'tramites', 277, 'Folio entrada: 491/2026 | Subtramite id: 277 | Datos constancia | Numero: 513 | Folio salida: 277/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:41:29'),
(2024, 20, 'Actualizó trámite', 'tramites', 281, 'Folio: 495/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:47:22'),
(2025, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 281 | Archivo: .private/281/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-08 17:48:38'),
(2026, 20, 'Actualizo datos constancia', 'tramites', 281, 'Folio entrada: 495/2026 | Subtramite id: 281 | Datos constancia | Numero: 315 | Folio salida: 279/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:49:28'),
(2027, 20, 'Actualizó trámite', 'tramites', 283, 'Folio: 497/2026 | En revisión → En revisión | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:57:40'),
(2028, 20, 'Actualizó trámite', 'tramites', 283, 'Folio: 497/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:57:55'),
(2029, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 283 | Archivo: .private/283/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-08 17:58:07'),
(2030, 20, 'Actualizo datos constancia', 'tramites', 283, 'Folio entrada: 497/2026 | Subtramite id: 283 | Datos constancia | Numero: 202 | Folio salida: 280/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 17:58:43'),
(2031, 20, 'Actualizó trámite', 'tramites', 290, 'Folio: 502/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 18:00:33'),
(2032, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 290 | Archivo: .private/290/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-08 18:00:44'),
(2033, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 18:01:11'),
(2034, 20, 'Actualizo datos constancia', 'tramites', 290, 'Folio entrada: 502/2026 | Subtramite id: 290 | Datos constancia | Numero: 107 | Folio salida: 281/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 18:01:49'),
(2035, 20, 'Actualizó trámite', 'tramites', 291, 'Folio: 503/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 18:08:06'),
(2036, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 291 | Archivo: .private/291/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-08 18:08:16'),
(2037, 20, 'Actualizo datos constancia', 'tramites', 291, 'Folio entrada: 503/2026 | Subtramite id: 291 | Datos constancia | Numero: 911 | Folio salida: 282/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 18:09:19'),
(2038, 20, 'Actualizó trámite', 'tramites', 292, 'Folio: 504/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 18:10:03'),
(2039, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 292 | Archivo: .private/292/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-08 18:15:07'),
(2040, 20, 'Actualizo datos constancia', 'tramites', 292, 'Folio entrada: 504/2026 | Subtramite id: 292 | Datos constancia | Numero: 416 | Folio salida: 283/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 18:16:07'),
(2041, 20, 'Actualizó trámite', 'tramites', 293, 'Folio: 505/2026 | En revisión → Aprobado por Verificador | Verificador: ALFREDO DIAZ', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 18:16:22'),
(2042, 20, 'Actualizo datos constancia', 'tramites', 293, 'Folio entrada: 505/2026 | Subtramite id: 293 | Datos constancia | Numero: 416-A | Folio salida: 284/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-08 18:18:03'),
(2043, 4, 'Login exitoso', 'usuarios', 4, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 18:25:21'),
(2044, 19, 'Creó trámite', 'tramites', 304, 'Folio: 516/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 18:28:06'),
(2045, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 18:40:01'),
(2046, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 18:49:22'),
(2047, 13, 'Creó trámite', 'tramites', 305, 'Folio: 517/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 18:50:30'),
(2048, 4, 'Login exitoso', 'usuarios', 4, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 18:52:18'),
(2049, 2, 'Login exitoso', 'usuarios', 2, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-08 19:18:51'),
(2050, 2, 'Logout', 'usuarios', 2, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-08 19:20:14'),
(2051, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-08 19:20:19'),
(2052, 13, 'Logout', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 19:54:10'),
(2053, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 19:54:14'),
(2054, 20, 'Actualizo datos constancia', 'tramites', 200, 'Folio entrada: 472/2026 | Subtramite id: 200 | Datos constancia | Numero: 104 | Folio salida: 256/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:00:33'),
(2055, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:17:30'),
(2056, 4, 'Logout', 'usuarios', 4, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 20:20:47'),
(2057, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 20:20:52'),
(2058, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:22:08'),
(2059, 13, 'Creó trámite', 'tramites', 306, 'Folio: 518/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:25:22'),
(2060, 19, 'Actualizó trámite', 'tramites', 184, 'Folio: 464/2026 | Aprobado por Verificador → Aprobado | Verificador: VENTANILLA', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 20:28:20'),
(2061, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 20:28:28'),
(2062, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:36:52'),
(2063, 20, 'Actualizo datos constancia', 'tramites', 275, 'Folio entrada: 489/2026 | Subtramite id: 275 | Datos constancia | Numero: 104-E | Folio salida: 269/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:37:21'),
(2064, 4, 'Login exitoso', 'usuarios', 4, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 20:40:37'),
(2065, 4, 'Logout', 'usuarios', 4, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 20:41:16'),
(2066, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 20:41:39'),
(2067, 20, 'Actualizo datos constancia', 'tramites', 249, 'Folio entrada: 481/2026 | Subtramite id: 249 | Datos constancia | Numero: 105-A | Folio salida: 272/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:41:53'),
(2068, 18, 'Logout', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 20:52:58'),
(2069, 20, 'Actualizo datos constancia', 'tramites', 254, 'Folio entrada: 483/2026 | Subtramite id: 254 | Datos constancia | Numero: 117 | Folio salida: 271/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:53:06'),
(2070, 4, 'Login exitoso', 'usuarios', 4, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-06-08 20:53:08'),
(2071, 20, 'Actualizo datos constancia', 'tramites', 183, 'Folio entrada: 463/2026 | Subtramite id: 183 | Datos constancia | Numero: 206-A | Folio salida: 252/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 20:58:06'),
(2072, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 21:03:53'),
(2073, 4, 'Login exitoso', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 14:26:48'),
(2074, 18, 'Login exitoso', 'usuarios', 18, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-09 14:33:19'),
(2075, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:16:43'),
(2076, 15, 'Login exitoso', 'usuarios', 15, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:19:02'),
(2077, 15, 'Aprobo solicitud registro', 'solicitudes_registro', 26, 'Solicitud aprobada: ANA GARCIA / Rol: Usuario', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:19:14'),
(2078, 15, 'Logout', 'usuarios', 15, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:19:17'),
(2079, 22, 'Login exitoso', 'usuarios', 22, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:20:12'),
(2080, 22, 'Logout', 'usuarios', 22, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:29:19'),
(2081, 15, 'Login exitoso', 'usuarios', 15, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:29:36'),
(2082, 15, 'Logout', 'usuarios', 15, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:41:14'),
(2083, 22, 'Login exitoso', 'usuarios', 22, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:41:18'),
(2084, 22, 'Login exitoso', 'usuarios', 22, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:41:50'),
(2085, 20, 'Actualizo datos constancia', 'tramites', 277, 'Folio entrada: 491/2026 | Subtramite id: 277 | Datos constancia | Numero: 513 | Folio salida: 277/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:42:57'),
(2086, 22, 'Logout', 'usuarios', 22, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 15:55:07'),
(2087, 20, 'Actualizo datos constancia', 'tramites', 280, 'Folio entrada: 494/2026 | Subtramite id: 280 | Datos constancia | Numero: 209 | Folio salida: 278/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 16:24:32'),
(2088, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 16:24:42'),
(2089, 20, 'Actualizo datos constancia', 'tramites', 280, 'Folio entrada: 494/2026 | Subtramite id: 280 | Datos constancia | Numero: 209 | Folio salida: 278/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 16:25:37'),
(2090, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 16:33:46'),
(2091, 13, 'Creó trámite', 'tramites', 307, 'Folio: 519/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 16:35:12'),
(2092, 4, 'Login exitoso', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 16:35:36'),
(2093, 4, 'Login exitoso', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 16:36:05'),
(2094, 4, 'Login exitoso', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 16:37:50'),
(2095, 4, 'Logout', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 16:41:22'),
(2096, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 16:41:30'),
(2097, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 16:57:19'),
(2098, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 17:50:51'),
(2099, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 18:37:24'),
(2100, 20, 'Actualizo datos constancia', 'tramites', 290, 'Folio entrada: 502/2026 | Subtramite id: 290 | Datos constancia | Numero: 107 | Folio salida: 281/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 18:37:59'),
(2101, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 18:41:04'),
(2102, 18, 'Actualizó trámite', 'tramites', 73, 'Folio: 383/2026 | Aprobado por Verificador → Aprobado | Verificador: VENTANILLA', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 18:51:10'),
(2103, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 18:56:41'),
(2104, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 18:56:44'),
(2105, 20, 'Actualizo datos constancia', 'tramites', 251, 'Folio entrada: 482/2026 | Subtramite id: 251 | Datos constancia | Numero: 203 | Folio salida: 273/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 18:57:38'),
(2106, 20, 'Actualizo datos constancia', 'tramites', 251, 'Folio entrada: 482/2026 | Subtramite id: 251 | Datos constancia | Numero: 203 | Folio salida: 273/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 18:59:19'),
(2107, 18, 'Logout', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:09:29'),
(2108, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:09:35'),
(2109, 20, 'Actualizo datos constancia', 'tramites', 253, 'Folio entrada: 482/2026 | Subtramite id: 253 | Datos constancia | Numero: 205 | Folio salida: 274/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:09:54'),
(2110, 20, 'Actualizo datos constancia', 'tramites', 253, 'Folio entrada: 482/2026 | Subtramite id: 253 | Datos constancia | Numero: 205 | Folio salida: 274/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:11:32'),
(2111, 18, 'Logout', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:15:46'),
(2112, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: usuario@sistema.com', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:18:12'),
(2113, 4, 'Login exitoso', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:18:28'),
(2114, 4, 'Logout', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:19:34');
INSERT INTO `logs_actividad` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `ip_address`, `user_agent`, `fecha`) VALUES
(2115, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: ventanilla@vet.com', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:19:40'),
(2116, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:19:49'),
(2117, 20, 'Actualizo datos constancia', 'tramites', 203, 'Folio entrada: 475/2026 | Subtramite id: 203 | Datos constancia | Numero: 302 | Folio salida: 259/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:24:30'),
(2118, 18, 'Creó trámite', 'tramites', 308, 'Folio: 520/2026 (principal con cantidad 1)', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-09 19:30:04'),
(2119, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:37:45'),
(2120, 19, 'Creó trámite', 'tramites', 309, 'Folio: 520/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:39:44'),
(2121, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:49:14'),
(2122, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:49:18'),
(2123, 20, 'Logout', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:49:49'),
(2124, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 19:49:53'),
(2125, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 20:24:46'),
(2126, 19, 'Creó trámite', 'tramites', 310, 'Folio: 521/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 20:27:20'),
(2127, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-09 20:57:47'),
(2128, NULL, 'Intento de login fallido', 'usuarios', NULL, 'Email: ventanilla@vet.com', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 14:18:51'),
(2129, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 14:18:59'),
(2130, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 14:21:18'),
(2131, 18, 'Creó trámite', 'tramites', 311, 'Folio: 522/2026 (principal con cantidad 1)', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 14:41:38'),
(2132, 18, 'Creó trámite', 'tramites', 312, 'Folio: 522/2026 (principal con cantidad 1)', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 14:43:23'),
(2133, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 14:55:45'),
(2134, 18, 'Creó trámite', 'tramites', 313, 'Folio: 522/2026 (principal con cantidad 1)', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 15:06:18'),
(2135, 18, 'Creó trámite', 'tramites', 314, 'Folio: 522/2026 (principal con cantidad 1)', '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 15:07:11'),
(2136, 20, 'Actualizo datos constancia', 'tramites', 281, 'Folio entrada: 495/2026 | Subtramite id: 281 | Datos constancia | Numero: 315 | Folio salida: 279/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:25:10'),
(2137, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:28:26'),
(2138, 20, 'Actualizo datos constancia', 'tramites', 281, 'Folio entrada: 495/2026 | Subtramite id: 281 | Datos constancia | Numero: 315 | Folio salida: 279/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:28:42'),
(2139, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:28:51'),
(2140, 13, 'Actualizó trámite', 'tramites', 281, 'Folio: 495/2026 | Aprobado por Verificador → Aprobado | Verificador: VENTANILLA', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:29:53'),
(2141, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:31:17'),
(2142, 20, 'Actualizo datos constancia', 'tramites', 283, 'Folio entrada: 497/2026 | Subtramite id: 283 | Datos constancia | Numero: 202 | Folio salida: 280/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:33:43'),
(2143, 20, 'Actualizo datos constancia', 'tramites', 293, 'Folio entrada: 505/2026 | Subtramite id: 293 | Datos constancia | Numero: 416-A | Folio salida: 284/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:41:34'),
(2144, 20, 'Actualizo datos constancia', 'tramites', 292, 'Folio entrada: 504/2026 | Subtramite id: 292 | Datos constancia | Numero: 416 | Folio salida: 283/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:42:49'),
(2145, 20, 'Actualizo datos constancia', 'tramites', 290, 'Folio entrada: 502/2026 | Subtramite id: 290 | Datos constancia | Numero: 107 | Folio salida: 281/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:47:32'),
(2146, 4, 'Login exitoso', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 15:47:59'),
(2147, 20, 'Actualizo datos constancia', 'tramites', 291, 'Folio entrada: 503/2026 | Subtramite id: 291 | Datos constancia | Numero: 911 | Folio salida: 282/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:51:46'),
(2148, 4, 'Login exitoso', 'usuarios', 4, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 15:57:42'),
(2149, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 16:20:12'),
(2150, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 16:21:09'),
(2151, 19, 'Creó trámite', 'tramites', 315, 'Folio: 522/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 16:21:41'),
(2152, 13, 'Creó trámite', 'tramites', 316, 'Folio: 523/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 16:22:15'),
(2153, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 16:25:33'),
(2154, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 16:54:09'),
(2155, 12, 'Login exitoso', 'usuarios', 12, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 17:57:38'),
(2156, 12, 'Creó trámite', 'tramites', 317, 'Folio: 524/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 17:58:30'),
(2157, 12, 'Login exitoso', 'usuarios', 12, NULL, '10.1.85.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-10 19:10:32'),
(2158, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 20:57:56'),
(2159, 19, 'Creó trámite', 'tramites', 318, 'Folio: 525/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 20:59:26'),
(2160, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 15:34:55'),
(2161, 13, 'Creó trámite', 'tramites', 319, 'Folio: 526/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 15:36:35'),
(2162, 13, 'Logout', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 16:42:21'),
(2163, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 17:34:59'),
(2164, 13, 'Creó trámite', 'tramites', 320, 'Folio: 527/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 17:53:29'),
(2165, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:26:50'),
(2166, 13, 'Creó trámite', 'tramites', 321, 'Folio: 528/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:29:04'),
(2167, 13, 'Creó trámite', 'tramites', 322, 'Folio: 529/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:41:14'),
(2168, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 16:29:33'),
(2169, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:02:22'),
(2170, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-12 17:02:35'),
(2171, 13, 'Creó trámite', 'tramites', 323, 'Folio: 530/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:04:12'),
(2172, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-12 17:22:11'),
(2173, 20, 'Croquis constancia', 'tramites', NULL, 'Croquis cargado para subtramite ID: 293 | Archivo: .private/293/croquis/croquis_1.jpg', '10.1.85.79', NULL, '2026-06-12 17:22:45'),
(2174, 20, 'Actualizo datos constancia', 'tramites', 293, 'Folio entrada: 505/2026 | Subtramite id: 293 | Datos constancia | Numero: 416-A | Folio salida: 284/2026', '10.1.85.79', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-12 17:22:54'),
(2175, 13, 'Creó trámite', 'tramites', 324, 'Folio: 531/2026 (principal con cantidad 1)', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:29:01'),
(2176, 13, 'Logout', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:03:16'),
(2177, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 14:34:03'),
(2178, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 14:34:21'),
(2179, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 14:34:24'),
(2180, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-15 14:52:02'),
(2181, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 15:32:42'),
(2182, 20, 'Actualizo datos constancia', 'tramites', 295, 'Folio entrada: 507/2026 | Subtramite id: 295 | Datos constancia | Numero: 306 | Folio salida: 276/2026', '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 15:33:07'),
(2183, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 16:09:11'),
(2184, 19, 'Creó trámite', 'tramites', 326, 'Folio: 532/2026 (principal con cantidad 1)', '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 16:11:22'),
(2185, 19, 'Login exitoso', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 17:36:09'),
(2186, 19, 'Logout', 'usuarios', 19, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 17:36:32'),
(2187, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 17:36:35'),
(2188, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-15 18:35:03'),
(2189, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-15 19:39:48'),
(2190, 13, 'Login exitoso', 'usuarios', 13, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 19:57:05'),
(2191, 20, 'Login exitoso', 'usuarios', 20, NULL, '10.1.85.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 19:57:14'),
(2192, 18, 'Logout', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-15 20:01:16'),
(2193, 18, 'Login exitoso', 'usuarios', 18, NULL, '10.1.85.9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-15 20:01:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_registro`
--

CREATE TABLE `solicitudes_registro` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('Usuario','Ventanilla','Verificador') NOT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado') NOT NULL DEFAULT 'Pendiente',
  `motivo_rechazo` text DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `resuelto_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_registro`
--

INSERT INTO `solicitudes_registro` (`id`, `nombre`, `apellidos`, `correo`, `password_hash`, `telefono`, `rol`, `estado`, `motivo_rechazo`, `fecha_solicitud`, `fecha_resolucion`, `resuelto_por`) VALUES
(11, 'FRANCISCO', 'FLORES', 'fco.flores.1508@gmail.com', '$2y$10$7SUXXUMegirlEobidcWk0.VVm4h.R.Y1emADhlKR6jrA7GS1nlN7e', '4651088177', 'Ventanilla', 'Aprobado', NULL, '2026-04-08 15:27:14', '2026-04-08 15:27:34', 1),
(12, 'ALFREDO', 'DIAZ GARCIA', 'alfredodiaz130578@gmail.com', '$2y$10$0Rf4yClwhvQd97UITGBOAeMATnV884bHomJjnCa7JxVDZr72xRq1e', '4651000912', 'Verificador', 'Aprobado', NULL, '2026-04-08 15:34:13', '2026-04-08 15:34:40', 1),
(13, 'JAIRO DAMIAN', 'LOPEZ VALLE', 'jairo.lopez133@gmail.com', '$2y$10$ya6yXlTOD7h.JDG4on9WEeMsUtrjZrnbbCzPzUrnyot3Q7Ml9s86i', '4651004812', 'Ventanilla', 'Aprobado', NULL, '2026-04-08 16:04:16', '2026-04-08 16:04:32', 1),
(14, 'AZUL MARIA', 'CAMPOS PEREZ', 'camposazul246@gmail.com', '$2y$10$yJPtHohM7Dbqa3abYYanfef2L3fI12GLjN2MRim2DswQVDfluFqnO', '4651181090', 'Ventanilla', 'Aprobado', NULL, '2026-04-08 16:06:38', '2026-04-08 16:07:20', 1),
(15, 'HEIDI ALEXA', 'GARCIA DIAZ', 'heidigarciad4@gmail.con', '$2y$10$mh0zcw0BzVU5SD9wSbB0w.2jb/VUBfbjwxEimR64oFU6NSeD27V.m', '4651034454', 'Ventanilla', 'Aprobado', NULL, '2026-04-08 16:12:44', '2026-04-08 16:13:31', 1),
(16, 'PEDRO', 'RUIZ DIAZ', 'phrd.123@gmail.com', '$2y$10$vqPFIU6rtZAv3oG37bnHy.w.kg4TblAgzr1yKPDKlZtduoi8b/UHW', '4651021512', 'Ventanilla', 'Aprobado', NULL, '2026-04-08 18:03:30', '2026-04-08 18:04:16', 1),
(20, 'VENTANILLA', 'VENATANA', 'ventanilla@vet.com', '$2y$10$Y61.Y57Q.kqv/oATp8Jki.OKhQnToUQ7l/F.XZY1OpbqLcPoKgjyW', '1234567891', 'Ventanilla', 'Aprobado', NULL, '2026-04-13 14:21:17', '2026-04-13 14:21:32', 15),
(21, 'HEIDI ALEXA', 'GARCIA DIAZ', 'heidigarciad4@gmail.com', '$2y$10$S7lVAb9NW96KEKaF9nYYDOU.wJCrglNVjJvgp48q176p.TJOoRiL.', '4651034454', 'Ventanilla', 'Aprobado', NULL, '2026-04-16 19:05:13', '2026-04-16 19:05:32', 15),
(22, 'ALFREDO', 'DIAZ', 'alfredodiaz@gmail.com', '$2y$10$t4T78XyZwY5O.AKrB.ay8OBk8JTgmdOS.o1kmpkgQr18jrAKdfku6', '4651000912', 'Verificador', 'Aprobado', NULL, '2026-04-27 20:02:24', '2026-04-27 20:03:03', 15),
(25, 'DIR', 'PLANEACIÓN Y DESARROLLO URBANO', 'dir.planeacionydu@gmail.com', '$2y$10$v77MF5/9tLw1/6TW7ApshuDRtVA4AFlBFIgGjQzZLR.s6tmIHRz7i', '0000000000', 'Ventanilla', 'Aprobado', NULL, '2026-06-08 16:34:17', '2026-06-08 16:34:33', 15),
(26, 'ANA', 'GARCIA', 'anna.garcia0598@gmail.com', '$2y$10$k76I3JiAUOLlt3ew6A29K.hOEeZJOPXewXpPIhPWlzPj1giIeN5U.', '4651292134', 'Usuario', 'Aprobado', NULL, '2026-06-09 15:18:32', '2026-06-09 15:19:14', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_tramite`
--

CREATE TABLE `tipos_tramite` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `plantilla_pdf` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_tramite`
--

INSERT INTO `tipos_tramite` (`id`, `codigo`, `nombre`, `descripcion`, `plantilla_pdf`, `activo`, `created_at`) VALUES
(1, 'NUM_OFICIAL', 'Constancia de Número Oficial', 'Asignación de número oficial a inmuebles. 10 días hábiles.', 'numero_oficial.php', 1, '2026-03-10 20:04:43'),
(2, 'CMCU', 'Constancia de Compatibilidad Urbanística', 'Compatibilidad de uso de suelo. 10 días hábiles.', 'cmcu.php', 1, '2026-03-10 20:04:43'),
(3, 'FUSION', 'Fusión de Predios', 'Fusión de dos o más predios. 10 días hábiles.', 'fusion.php', 1, '2026-03-10 20:04:43'),
(4, 'SUBDIVISION', 'Subdivisión de Predio', 'Subdivisión de un predio. 10 días hábiles.', 'subdivision.php', 1, '2026-03-10 20:04:43'),
(5, 'INFORME_CU', 'Informe de Compatibilidad Urbanística', 'Informe de compatibilidad. 10 días hábiles.', NULL, 1, '2026-03-10 20:04:43'),
(6, 'TER_OBRA', 'Terminacion de Obra', 'Constancia de uso de suelo. 10 días hábiles.', NULL, 1, '2026-03-10 20:04:43'),
(7, 'LIC_CONST', 'Licencia de Construcción', 'Licencia para construcción/remodelación. 10 días hábiles.', NULL, 1, '2026-03-10 20:04:43'),
(8, 'ANUNCIOS', 'Anuncios Publicitarios', 'Tramitar anuncios publicitarios.', NULL, 1, '2026-03-10 20:04:43'),
(9, 'VOBO', 'Visto Bueno', 'Solicitud de Visto Bueno', 'vobo.php', 1, '2026-06-09 16:45:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites`
--

CREATE TABLE `tramites` (
  `id` int(11) NOT NULL,
  `folio_numero` int(11) NOT NULL,
  `folio_anio` int(11) NOT NULL,
  `tipo_tramite_id` int(11) NOT NULL,
  `propietario` varchar(150) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `numero` varchar(11) NOT NULL,
  `numero_asignado` varchar(50) DEFAULT NULL,
  `tipo_asignacion` varchar(50) DEFAULT NULL,
  `referencia_anterior` varchar(255) DEFAULT NULL,
  `entre_calle1` varchar(255) DEFAULT NULL,
  `entre_calle2` varchar(250) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `colonia` varchar(100) DEFAULT NULL,
  `manzana` varchar(50) DEFAULT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `calle` varchar(100) DEFAULT NULL,
  `lat` decimal(12,2) DEFAULT NULL COMMENT 'UTM X (Este)',
  `lng` decimal(12,2) DEFAULT NULL COMMENT 'UTM Y (Norte)',
  `fecha_ingreso` date NOT NULL,
  `fecha_entrega` date NOT NULL,
  `fecha_constancia` date DEFAULT NULL,
  `solicitante` varchar(150) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `cuenta_catastral` varchar(50) DEFAULT NULL COMMENT 'Solo números — asignada por trigger si viene vacía',
  `superficie` varchar(50) DEFAULT NULL,
  `ine_archivo` varchar(255) DEFAULT NULL,
  `oficio_vobo` varchar(255) NOT NULL,
  `titulo_archivo` varchar(255) DEFAULT NULL,
  `predial_archivo` varchar(255) DEFAULT NULL,
  `escrituras_archivo` varchar(255) DEFAULT NULL,
  `Resolucion` varchar(255) NOT NULL,
  `foto_predio_archivo` varchar(255) DEFAULT NULL,
  `formato_constancia` varchar(255) DEFAULT NULL,
  `carta_poder` varchar(255) DEFAULT NULL,
  `foto1_archivo` varchar(255) DEFAULT NULL,
  `foto2_archivo` varchar(255) DEFAULT NULL,
  `croquis_archivo` varchar(255) DEFAULT NULL COMMENT 'Imagen del croquis del predio para la constancia',
  `otros_archivos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`otros_archivos`)),
  `datos_especificos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_especificos`)),
  `comentario_sin_doc` text DEFAULT NULL,
  `estatus` enum('En revisión','En Revisión por Validador','Aprobado por Verificador','Aprobado','Rechazado','En corrección') NOT NULL DEFAULT 'En revisión',
  `observaciones` text DEFAULT NULL,
  `verificador_nombre` varchar(150) DEFAULT NULL,
  `aprobado_por` int(11) DEFAULT NULL,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `aprobado_director` tinyint(1) DEFAULT 0,
  `fecha_aprobacion_director` timestamp NULL DEFAULT NULL,
  `usuario_creador_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tiempo_ingreso` datetime DEFAULT current_timestamp(),
  `tiempo_salida` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `folio_salida_numero` int(11) DEFAULT NULL COMMENT 'Número secuencial de salida (independiente del folio de ingreso)',
  `folio_salida_anio` int(11) DEFAULT NULL COMMENT 'Año del folio de salida',
  `tramite_principal_id` int(11) DEFAULT NULL,
  `licencia_numero` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tramites`
--

INSERT INTO `tramites` (`id`, `folio_numero`, `folio_anio`, `tipo_tramite_id`, `propietario`, `direccion`, `numero`, `numero_asignado`, `tipo_asignacion`, `referencia_anterior`, `entre_calle1`, `entre_calle2`, `localidad`, `colonia`, `manzana`, `lote`, `cp`, `calle`, `lat`, `lng`, `fecha_ingreso`, `fecha_entrega`, `fecha_constancia`, `solicitante`, `telefono`, `correo`, `cuenta_catastral`, `superficie`, `ine_archivo`, `oficio_vobo`, `titulo_archivo`, `predial_archivo`, `escrituras_archivo`, `Resolucion`, `foto_predio_archivo`, `formato_constancia`, `carta_poder`, `foto1_archivo`, `foto2_archivo`, `croquis_archivo`, `otros_archivos`, `datos_especificos`, `comentario_sin_doc`, `estatus`, `observaciones`, `verificador_nombre`, `aprobado_por`, `fecha_aprobacion`, `aprobado_director`, `fecha_aprobacion_director`, `usuario_creador_id`, `created_at`, `tiempo_ingreso`, `tiempo_salida`, `updated_at`, `folio_salida_numero`, `folio_salida_anio`, `tramite_principal_id`, `licencia_numero`, `cantidad`) VALUES
(24, 993, 2025, 1, 'CELIA SOTO OJEDA', 'POMEX', '305', '106', 'ASIGNACION', '', 'PRIV ANASTACIO REYES', 'CALLEJON SOTO', 'RINCON DE ROMOS', 'LA ESTANCIA DE CHORA', '2', '12', '20437', NULL, 22.54, -108.34, '2026-04-14', '2026-04-28', '2026-04-14', 'CELIA SOTO OJEDA', '4651250796', NULL, '07001040051037000', '200', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, 'foto1_69de71bb34294_1776185787.jpg', NULL, '.private/993_2025/croquis/croquis_69eb9d1b06a2f_1777048859.jpg', NULL, NULL, NULL, 'Aprobado', '', 'VENTANILLA', 18, '2026-04-15 17:24:09', 0, NULL, 18, '2026-04-14 16:32:24', '2026-04-14 10:32:24', '2026-04-15 11:24:09', '2026-05-25 16:33:32', 1, 2026, NULL, NULL, 1),
(33, 349, 2026, 1, 'REMIGIO PUGA DE SANTIAGO', 'GRAFITO', '114', '114', 'ASIGNACION', '114', 'CALLE SIFON', 'VENUSTIANO CARRANZA', 'PABELLON DE HIDALGO', 'ZONA CENTRO', '2', '16', '20437', NULL, NULL, NULL, '2026-04-15', '2026-04-29', '2026-04-23', 'REMIGIO PUGA DE SANTIAGO', '4494374394', NULL, '70302010002000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/33/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado', '', 'VENTANILLA', 18, '2026-04-30 18:26:27', 0, NULL, 13, '2026-04-16 18:15:04', '2026-04-16 12:15:04', '2026-04-30 12:26:27', '2026-06-03 16:35:48', 234, 2026, NULL, NULL, 1),
(34, 350, 2026, 2, 'BRAYAN ROMO MONTALVO', 'ALVARO OBREGON SUB 14/07', '805', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-16', '2026-04-30', NULL, 'ALEJANDRO ROMO LUCIO', '4651157053', NULL, '07001010042030000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, 'foto1_69efc959d7e18_1777322329.jpeg', NULL, NULL, NULL, NULL, NULL, 'Aprobado por Verificador', 'SIN OBSERVACIONES', 'ALFREDO DIAZ', 20, '2026-04-27 20:38:49', 0, NULL, 13, '2026-04-16 18:30:49', '2026-04-16 12:30:49', '2026-04-27 14:38:49', '2026-05-29 20:16:40', NULL, NULL, NULL, NULL, 1),
(35, 351, 2026, 1, 'JUAN PEDRO DE LA CRUZ ROMO', 'CALLE ESMERALDA', 'S/N', '208', 'ASIGNACION', '', 'ZAFIRO', 'ORO', 'RINCON DE ROMOS', 'LA PLATA', '2', '1', '20404', NULL, NULL, NULL, '2026-04-16', '2026-04-30', '2026-04-28', 'MARIA DE LOS ANGELES DAVILA REYES', '4651284154', NULL, '70101117001000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/351_2026/croquis/croquis_69f10b908b9f7_1777404816.jpg', NULL, '{\"contrato_arrendamiento\":null,\"memoria_descriptiva\":null,\"poder_notariado\":null,\"acta_constitutiva\":null,\"solicitud_por_escrito\":null,\"licencia_de_construccion\":null,\"bitacora_de_obra\":null}', NULL, 'Aprobado', 'CONSTRUIDO', 'ALFREDO DIAZ', 20, '2026-04-28 19:33:17', 0, NULL, 13, '2026-04-16 20:30:56', '2026-04-16 14:30:56', '2026-04-28 13:33:17', '2026-05-29 20:16:49', 211, 2026, NULL, NULL, 1),
(36, 352, 2026, 2, 'BELEN MORENO GORDILLO', 'CALLE COAHUILA ', '#210 M-7L-2', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'MIGUEL HIDALGO', NULL, NULL, '20417', NULL, NULL, NULL, '2026-04-17', '2026-05-01', NULL, 'BELEN MORENO GORDILLO', '4651212314', NULL, '70103095023000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cmcu comercial', 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-04-30 15:25:35', 0, NULL, 19, '2026-04-17 15:13:37', '2026-04-17 09:13:37', '2026-04-30 09:25:35', '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(37, 353, 2026, 2, 'DANIELA YARETZI NAJERA DELGADO', 'ALDAMA SUB 036/2021', 'S/N', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'BARRIO DE CHORA', NULL, NULL, '20406', NULL, NULL, NULL, '2026-04-17', '2026-05-01', NULL, 'JOSE MANUEL DELGADO', '4651099754', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-17 16:24:19', '2026-04-17 10:24:19', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(38, 354, 2026, 1, 'MA ALICIA REYES SOTO', 'GRAFITO', '#101', '101', 'ASIGNACION', '', 'LA PEDRERA', 'AV DE LAS PIEDRAS', 'RINCON DE ROMOS', 'RINCONADA DE LAS PIEDRAS', '', '', '20406', NULL, NULL, NULL, '2026-04-20', '2026-05-04', '2026-04-29', 'MA ALICIA REYES SOTO', '4492326111', NULL, '70104109024000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/354_2026/croquis/croquis_69f243f5362eb_1777484789.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', 'TIENE REQUERIMIENTO POR LA CONTRUCCION', 'ALFREDO DIAZ', 20, '2026-04-29 17:43:57', 0, NULL, 13, '2026-04-20 18:25:00', '2026-04-20 12:25:00', '2026-04-29 11:43:57', '2026-05-25 16:33:32', 214, 2026, NULL, NULL, 1),
(40, 355, 2026, 1, 'GLOBO DE AGUA S DE RL DE C.V.', 'COLIMA', '#202', '202-A', 'ASIGNACION', '', 'CHIAPAS', 'CAMPECHE', 'RINCON DE ROMOS', 'MIGUEL HIDALGO', '6', '6', '20417', NULL, NULL, NULL, '2026-04-20', '2026-05-04', '2026-04-21', 'GLOBO DE AGUA S DE RL DE DC', '4499116452', NULL, '70103078034000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/355_2026/croquis/croquis_69f2421ae6e7d_1777484314.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-04-29 17:19:52', 0, NULL, 13, '2026-04-20 20:57:26', '2026-04-20 14:57:26', '2026-04-29 11:19:52', '2026-05-25 16:33:32', 212, 2026, NULL, NULL, 1),
(43, 356, 2026, 7, 'ERIKA MARGARITA DIAZ ACOSTA', 'MORELOS SUR', 'S/N', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'RINCON DE ROMOS', NULL, NULL, '20400', NULL, NULL, NULL, '2026-04-21', '2026-05-05', NULL, 'ERIKA MARGARITA DIAZ ACOSTA', '4651015525', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-21 16:32:25', '2026-04-21 10:32:25', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(44, 357, 2026, 1, 'KAREN MAIDELEN ROQUE ESQUIVEL', 'SIN NOMBRE           LAS ANTENAS', 'S/N', '104', 'ASIGNACION', '', 'PRIVADA SIN NOMBRE 1', 'CARR EST 54 RINCON - PABELLON DE HIDALGO', 'RINCON DE ROMOS', 'LAS ANTENAS', '', '', '20427', NULL, NULL, NULL, '2026-04-21', '2026-05-05', '2026-04-30', 'KAREN MAIDELEN ROQUE ESQUIVEL', '4651506512', NULL, '7130115021000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/357_2026/croquis/croquis_69f393e285cd9_1777570786.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-04-30 17:32:12', 0, NULL, 13, '2026-04-21 20:16:41', '2026-04-21 14:16:41', '2026-04-30 11:32:12', '2026-05-25 16:33:32', 215, 2026, NULL, NULL, 1),
(45, 358, 2026, 7, 'RICARDO HERNANDEZ MORENO', 'GENERAL LAZARO CARDENAS ', 'S/N', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'PUERTA DEL MUERTO', NULL, NULL, '20424', NULL, NULL, NULL, '2026-04-21', '2026-05-05', NULL, 'RICARDO HERNANDEZ MORENO', '4651176229', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', '', 'JUAN CARLOS VERIFICADOR GÓMEZ', 2, '2026-04-24 14:32:17', 0, NULL, 13, '2026-04-21 20:45:21', '2026-04-21 14:45:21', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(46, 359, 2026, 5, 'JOSE MANUEL CONTRERAS MENDEZ', 'LOTE 189 Z1 P1/3 EJIDO FRESNILLO', 'L-189', NULL, NULL, NULL, NULL, '', 'EJIDO FRESNILLO', 'EJIDO FRESNILLO', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-22', '2026-05-06', NULL, 'JOSE MANUEL CONTRERAS MENDEZ', '4651152535', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-22 18:05:01', '2026-04-22 12:05:01', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(47, 360, 2026, 2, 'JUAN POSADA GARCIA', 'CUAHUTEMOC', 'S/N', NULL, NULL, NULL, NULL, '', 'EL SAUCILLO', 'RINCON DE ROMOS', NULL, NULL, '20400', NULL, NULL, NULL, '2026-04-23', '2026-05-07', NULL, 'ROSA CECILIA MARTINEZ GUTIERREZ', '4651096503', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-23 16:05:59', '2026-04-23 10:05:59', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(48, 361, 2026, 7, 'REYNA ALEJANDRA PEREZ RAMIREZ', 'MORELOS             L-22, S/N,                GENERAL', 'S/N', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'VALLE DE LAS DELICIAS', NULL, NULL, '20427', NULL, NULL, NULL, '2026-04-23', '2026-05-07', NULL, 'REYNA ALEJANDRA PEREZ RAMIREZ', '4651042537', NULL, '70000009112000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-04-23 18:35:25', '2026-04-23 12:35:25', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(49, 362, 2026, 7, 'ROBERTO CASTORENA PEREZ', 'PROL. 5 DE MAYO VALLE DEL REAL', 'S/N', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SECTOR 1', NULL, NULL, '20400', NULL, NULL, NULL, '2026-04-23', '2026-05-07', NULL, 'JOSEFINA CASTORENA AGUILAR', '7142346453', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-23 18:49:12', '2026-04-23 12:49:12', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(53, 364, 2026, 1, 'FELIPE DE JESUS REYES VENEGAS', 'MOTOLINIA ORIENTE', '#835', '835', 'ASIGNACION', '', 'CIRCUITO OSCAR GALIE', 'COPALLI', 'RINCON DE ROMOS', 'EMBAJADORES', '', '', '20404', NULL, NULL, NULL, '2026-04-27', '2026-05-11', '2026-04-30', 'FELIPE DE JESUS REYES VENEGAS', '4651036981', NULL, '70101090012000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/364_2026/croquis/croquis_69f3af188d36c_1777577752.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-04-30 19:34:53', 0, NULL, 13, '2026-04-27 15:37:45', '2026-04-27 09:37:45', '2026-04-30 13:34:53', '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(54, 365, 2026, 1, 'JOSE ANTONIO ESPARZA RUELAS', 'SERGIO JIMENEZ MUÑOZ', 'S/N', '424', 'ASIGNACION', '', 'GILBERTO ROMO N', 'VICTOR CASTORENA', 'RINCON DE ROMOS', 'SOLIDARIDAD', '', '', '20416', NULL, NULL, NULL, '2026-04-27', '2026-05-11', '2026-04-29', 'JOSE ANTONIO ESPARZA RUELAS', '4493573451', NULL, '70103052027000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/365_2026/croquis/croquis_69f25e85eaf5d_1777491589.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-04-29 19:51:33', 0, NULL, 19, '2026-04-27 16:11:45', '2026-04-27 10:11:45', '2026-04-29 13:51:33', '2026-05-25 16:33:32', 213, 2026, NULL, NULL, 1),
(55, 366, 2026, 7, 'JULIO CESAR MORENO ROMO', 'AV. LAS PIEDRAS L-01 M-07', '#201', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'RINCONADA DE LAS PIEDRAS', NULL, NULL, '20400', NULL, NULL, NULL, '2026-04-27', '2026-05-11', NULL, 'JULIO CESAR MORENO ROMO', '4651128730', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-27 19:15:02', '2026-04-27 13:15:02', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(56, 367, 2026, 4, 'ARNULFO AVILA BRISEÑO', 'FRANCISCO I MADERO L-05 M-08', 'S/N', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'PUERTA DEL MUERTO', NULL, NULL, '20424', NULL, NULL, NULL, '2026-04-27', '2026-05-11', NULL, 'JOSE HUMBERTO RAMOS PADILLA', '4491894913', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-27 19:51:25', '2026-04-27 13:51:25', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(57, 368, 2026, 1, 'VICTOR MANUEL VENTURA ESPARZA', 'MEXICO', 'S/N', '605', 'ASIGNACION', '', 'VENUSTIANO CARRANZA Y EMILIANO ZAPATA', '', 'RINCON DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-04-27', '2026-05-11', '2026-05-12', 'VICTOR MANUEL VENTURA ESPARZA', '4492224653', NULL, '07001020015022000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/368_2026/croquis/croquis_6a034ddb0474e_1778601435.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-12 15:56:58', 0, NULL, 19, '2026-04-27 20:59:57', '2026-04-27 14:59:57', '2026-05-12 09:56:58', '2026-05-25 16:33:32', 216, 2026, NULL, NULL, 1),
(58, 369, 2026, 8, 'AUTOZONE DE MEXICO SOCIEDAD DE RESPONSABILIDAD LIMITADA DE CV', 'DR FRANCISCO GUEL JIMENEZ', 'S/N', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SANTA ANITA', NULL, NULL, '20410', NULL, 776247.15, 2460412.46, '2026-04-28', '2026-05-12', NULL, 'AUTOZONE DE MEXICO SOCIEDAD DE RESPONSABILIDAD LIMITADA DE CV', '4492905055', NULL, '07001030009007000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-04-28 17:28:35', '2026-04-28 11:28:35', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(59, 370, 2026, 1, 'J JESUS GARCIA GARCIA', 'MANUEL DE VELAZCO MARTINES', '', '311', 'ASIGNACION', '', 'SERGIO JIMENEZ MUNOZ', 'ISABEL ESPARZA RODRIGUEZ', 'RINCON DE ROMOS', 'SOLIDARIDAD', '', '', '20416', NULL, 775713.52, 2460076.13, '2026-04-29', '2026-05-13', '2026-05-20', 'J JESUS GARCIA GARCIA', '4651091099', NULL, '70103091027000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/370_2026/croquis/croquis_6a074020df0c5_1778860064.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-12 16:14:11', 0, NULL, 13, '2026-04-29 20:38:29', '2026-04-29 14:38:29', '2026-05-12 10:14:11', '2026-05-25 16:33:32', 240, 2026, NULL, NULL, 1),
(60, 371, 2026, 7, 'MA GUADALUPE VAZQUEZ RODRIGUEZ', 'CALLE AGUASCALIENTES #136 M-14 L-35 FRACCIONAMIENTO MIGUEL HIDALGO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'MIGUEL HIDALGO', NULL, NULL, '20417', NULL, 775577.48, 2460477.08, '2026-04-30', '2026-05-14', NULL, 'MA GUADALUPE VAZQUEZ RODRIGUEZ', '4651172809', NULL, '07001030032005000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-04-30 16:04:51', '2026-04-30 10:04:51', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(61, 372, 2026, 7, 'NORA CARDONA REYES', 'ALVARO OBREGON PONIENTE', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-04-30', '2026-05-14', NULL, 'NORA CARDONA REYES', '4491922898', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-30 16:42:04', '2026-04-30 10:42:04', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(62, 373, 2026, 7, 'CUDBERTO LUEVANO VAZQUEZ', 'MATEO GUERRERO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SAN JOSE', NULL, NULL, '20400', NULL, NULL, NULL, '2026-04-30', '2026-05-14', NULL, 'SAN JUANA BERENICE', '4651096919', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '[{\"tipo\":\"escritura\",\"label\":\"Escritura \\/ Título\",\"archivo\":\".private+373+2026+escritura\\/escritura_1781549758_caf644bf.pdf\",\"fecha\":\"2026-06-15 12:55:58\",\"usuario_id\":18}]', NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-30 16:56:54', '2026-04-30 10:56:54', NULL, '2026-06-15 18:55:58', NULL, NULL, NULL, NULL, 1),
(63, 374, 2026, 4, 'PATRICIO CHAVEZ TORRES', 'MORELOS', '', NULL, NULL, NULL, NULL, '', 'SAN JACINTO', NULL, NULL, NULL, '20425', NULL, NULL, NULL, '2026-04-30', '2026-05-14', NULL, 'PATRICIO CHAVEZ TORRES', '4651032559', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-30 18:27:28', '2026-04-30 12:27:28', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(65, 375, 2026, 7, 'FRANCISCO ALVARADO GUERRERO', 'AV. REVOLUCION', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'ESCALERAS', NULL, NULL, '20420', NULL, 776575.45, 2459701.33, '2026-04-30', '2026-05-14', NULL, 'FRANCISCO ALVARADO GUERRERO', '4651243302', NULL, '75801050001000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-04-30 19:21:20', '2026-04-30 13:21:20', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(66, 376, 2026, 7, 'A A TALLER DE ARQUITECTURA SA DE CV', 'PROFRA. SOLEDAD RAUDRY PEDROZA #211 L-8 M-4', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'RINCON REAL', NULL, NULL, '20403', NULL, NULL, NULL, '2026-04-30', '2026-05-14', NULL, 'A A TALLER DE ARQUITECTURA SA DE CV', '4651410197', NULL, '70104117008000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-04-30 19:46:28', '2026-04-30 13:46:28', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(67, 377, 2026, 7, 'A A TALLER DE ARQUITECTURA SA DE CV', 'PROFRA. SOLEDAD RAUDRY PEDROZA #209 L-9 M-4 RINCON REAL', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'RINCON REAL', NULL, NULL, '20403', NULL, NULL, NULL, '2026-04-30', '2026-05-14', NULL, 'A A TALLER DE ARQUITECTURA SA DE CV', '4651410197', NULL, '70104117009000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-04-30 19:52:11', '2026-04-30 13:52:11', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(68, 378, 2026, 2, 'JESUS ANTONIO FLORES BUSTOS', 'M1 L13 POR REGULARIZAR', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'LAS FLORES', NULL, NULL, '20420', NULL, NULL, NULL, '2026-05-07', '2026-05-21', NULL, 'JESUS ANTONIO FLORES BUSTOS', '4494155325', NULL, '701010136013000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-07 18:16:10', '2026-05-07 12:16:10', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(69, 379, 2026, 7, 'GREGORIO OVALLE HERRERA', 'AV. UNIVERSIDAD NO. 11 EL BAJIO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL BAJIO', NULL, NULL, '20420', NULL, 777563.14, 2462293.03, '2026-05-07', '2026-05-21', NULL, 'ARELY BETANIA IBARRA GALINDO', '4112682792', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-07 18:38:43', '2026-05-07 12:38:43', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(70, 380, 2026, 2, 'JULIO CESAR MORENO RAMIREZ', 'PLUTARCO ELIAS CALLES S/N RINCON DE ROMOS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SAN JOSE', NULL, NULL, '20415', NULL, 776121.31, 2459769.75, '2026-05-07', '2026-05-21', NULL, 'JULIO CESAR MORENO RAMIREZ', '4772022465', NULL, '07001030020019000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 2, NULL, 13, '2026-05-07 18:48:11', '2026-05-07 12:48:11', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(71, 381, 2026, 1, 'GUADALUPE ALEJANDRO ORTIZ ESTRADA', 'SIN NOMBRE ORIENTE S/N, LOTE 6 DE LA SUBDIVISION 030/2022', '', NULL, NULL, NULL, NULL, '', 'EL BAJIO', 'FRACCIONAMIENTO LUIS ELIODORO ROMERO ORTIZ', NULL, NULL, '20420', NULL, NULL, NULL, '2026-05-07', '2026-05-21', NULL, 'GUADALUPE ALEJANDRO ORTIZ ESTRADA', '4651015721', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-07 19:46:29', '2026-05-07 13:46:29', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(72, 382, 2026, 1, 'AA TALLER DE ARQUITECTURA SA DE CV', 'PROFRA SOLEDAD RAUDRY PEDROZA', '', '215', 'ASIGNACION', '', 'PROF MANUEL ZAPATA JUAREZ', 'CECILIA GUERRA DELGADO', 'RINCON DE ROMOS', 'RINCON REAL', '4', '6', NULL, NULL, NULL, NULL, '2026-05-08', '2026-05-22', '2026-05-14', 'AA TALLER DE ARQUITECTURA SA DE CV', '4651410197', NULL, '70104117006000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/382_2026/croquis/croquis_6a062498e5bb7_1778787480.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-14 19:34:18', 0, NULL, 19, '2026-05-08 16:39:54', '2026-05-08 10:39:54', '2026-05-14 13:34:18', '2026-05-25 16:33:32', 218, 2026, NULL, NULL, 1),
(73, 383, 2026, 1, 'AA TALLER DE ARQUITECTURA SA DE CV', 'PROFRA SOLEDAD RAUDRY PEDROZA', '', '213', 'ASIGNACION', '', 'PROF MANUEL ZAPATA JUAREZ', 'CECILIA GUERRA DELGADO', 'RINCON DE ROMOS', 'RINCON REAL', '4', '7', '20400', NULL, NULL, NULL, '2026-05-08', '2026-05-22', '2026-05-14', 'AA TALLER DE ARQUITECTURA SA DE CV', '4651410197', NULL, '70104117007000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/383_2026/croquis/croquis_6a0625aa28548_1778787754.jpg', NULL, NULL, NULL, 'Aprobado', '', 'VENTANILLA', 18, '2026-06-09 18:51:10', 0, NULL, 19, '2026-05-08 16:52:43', '2026-05-08 10:52:43', '2026-06-09 12:51:10', '2026-06-09 18:51:10', 217, 2026, NULL, NULL, 1),
(74, 384, 2026, 1, 'MARGARITA LUEVANO LAZARIN', 'PINO', '', '219', 'ASIGNACION', '', 'JACARANDA', 'OLMO', 'RINCON DE ROMOS', 'SANTA CRUZ', '10', '11', '20406', NULL, NULL, NULL, '2026-05-08', '2026-05-22', '2026-05-14', 'MARGARITA LUEVANO LAZARIN', '4651047521', NULL, '70104057020000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/384_2026/croquis/croquis_6a06265a26203_1778787930.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-14 19:45:14', 0, NULL, 19, '2026-05-08 16:59:51', '2026-05-08 10:59:51', '2026-05-14 13:45:14', '2026-05-25 16:33:32', 219, 2026, NULL, NULL, 1),
(75, 385, 2026, 3, 'LUIS SANTIAGO DIMAS RODRIGUEZ', 'INSURGENTES S/N L-04 EL PANAL', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL PANAL', NULL, NULL, '20420', NULL, NULL, NULL, '2026-05-08', '2026-05-22', NULL, 'LUIS SANTIAGO DIMAS RODRIGUEZ', '4651134996', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-08 17:02:48', '2026-05-08 11:02:48', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(76, 386, 2026, 4, 'SERGIO MACIAS PADILLA', 'PARCELA NO 656 Z13 P1/2 S/T 8494 RINCON DE ROMOS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-08', '2026-05-22', NULL, 'SERGIO MACIAS PADILLA', '4498543625', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-08 17:07:07', '2026-05-08 11:07:07', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(77, 387, 2026, 8, 'BBVA', 'MORELOS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-08', '2026-05-22', NULL, 'BBVA', '4493935186', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-08 17:09:24', '2026-05-08 11:09:24', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(78, 388, 2026, 7, 'MARTHA JANETH CONTRERAS PALACIOS', 'AV. RUTA DE LA PLATA, EL POTRERO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL POTRERO', NULL, NULL, '20410', NULL, NULL, NULL, '2026-05-08', '2026-05-22', NULL, 'MARTHA JANETH CONTRERAS PALACIOS', '4651122372', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-08 17:15:43', '2026-05-08 11:15:43', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(79, 389, 2026, 6, 'SECRETARIA DE OBRAS PUBLICAS', 'VARIAS CALLES', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-08', '2026-05-22', NULL, 'SECRETARIA DE OBRAS PUBLICAS', '4491411342', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-08 17:19:40', '2026-05-08 11:19:40', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(80, 390, 2026, 1, 'ENEDELIA MARQUEZ CASTAÑON', 'MORELOS NORTE', '', '418', 'ASIGNACION', '', 'H COLEGIO MILITAR', 'MOTOLINIA ORIENTE', 'RINCON DE ROMOS', 'CENTRO', '', '', '20400', NULL, 776256.72, 2461037.60, '2026-05-08', '2026-05-22', '2026-05-14', 'MARIA MERCEDES VARGAS RUELAS', '4651027120', NULL, '70104013058000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/390_2026/croquis/croquis_6a0628678a391_1778788455.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-14 19:54:03', 0, NULL, 13, '2026-05-08 18:15:34', '2026-05-08 12:15:34', '2026-05-14 13:54:03', '2026-05-25 16:33:32', 220, 2026, NULL, NULL, 1),
(82, 391, 2026, 7, 'MA DE LA LUZ RODRIGUEZ RAMOS', 'COYOTL', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'ESTANCIA DE CHORA', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-08', '2026-05-22', NULL, 'MA DEL CARMEN HERNANDEZ GUERRERO', '4651098743', NULL, '70101065017000', '90', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-08 19:49:13', '2026-05-08 13:49:13', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(83, 392, 2026, 7, 'JUVENTINO PINEDO CATAÑO', 'PANTEON MUNICIPAL M8 F8', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-08', '2026-05-22', NULL, 'JUVENTINO PINEDO CATAÑO', '4651031892', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-08 20:47:08', '2026-05-08 14:47:08', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(84, 393, 2026, 2, 'JULIO CESAR MORENO RAMÍREZ', 'PLUTARCO ELIAS CALLES', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SAN JOSE', NULL, NULL, '20415', NULL, NULL, NULL, '2026-05-08', '2026-05-22', NULL, 'JULIO CESAR MORENO ROMO', '4772022465', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-08 20:51:36', '2026-05-08 14:51:36', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(85, 394, 2026, 1, 'JUAN CALDERON FLORES', 'TEXCOCO', '', '103', 'ASIGNACION', '113', 'DOLORES HIDALGO NORTE', '16 DE SEPTIEMBRE NORTE', 'RINCON DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-05-12', '2026-05-26', '2026-05-14', 'MARIA GUADALUPE DE LA TORRE SUAREZ', '4495803282', NULL, '70101030021000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/394_2026/croquis/croquis_6a062d917db0f_1778789777.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-14 20:15:54', 0, NULL, 13, '2026-05-12 14:49:14', '2026-05-12 08:49:14', '2026-05-14 14:15:54', '2026-05-25 16:33:32', 228, 2026, NULL, NULL, 1),
(86, 395, 2026, 7, 'MA DE LOURDES ORTIZ DURON', 'MARIANO JIMENEZ ESQUINA OLIVARES SANTANA S/N PABELLON DE HIDALGO', '', NULL, NULL, NULL, NULL, '', 'PABELLON DE HIDALGO', 'CENTRO', NULL, NULL, '20437', NULL, NULL, NULL, '2026-05-12', '2026-05-26', NULL, 'MA DE LOURDES ORTIZ DURON', '4661091937', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-12 14:56:51', '2026-05-12 08:56:51', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(87, 396, 2026, 1, 'OBED ARECHIGA ARECHIGA', 'LUIS PASTEUR #103 CENTRO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, 776269.24, 2460679.91, '2026-05-12', '2026-05-26', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-12 16:28:04', '2026-05-12 10:28:04', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(88, 397, 2026, 1, 'OBED ARECHIGA ARECHIGA', 'DR. ALBERTO SABIN', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, 776269.24, 2460679.91, '2026-05-12', '2026-05-26', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-12 16:30:11', '2026-05-12 10:30:11', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(89, 398, 2026, 1, 'SOSIMO CASILLAS SOLEDAD', '20 DE NOVIEMBRE', '', '602', 'ASIGNACION', '', 'FRANCISCO I MADERO', 'SIN NOMBRE', 'RINCON DE ROMOS', 'COL 16 DE SEPTIEMBRE', '18', '1', NULL, NULL, NULL, NULL, '2026-05-12', '2026-05-26', '2026-05-19', 'SOSIMO CASILLAS SOLEDAD', '4651025987', NULL, '734010180010000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/398_2026/croquis/croquis_6a0c85afaf418_1779205551.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-19 15:45:14', 0, NULL, 13, '2026-05-12 17:14:38', '2026-05-12 11:14:38', '2026-05-19 09:45:14', '2026-05-25 16:33:32', 239, 2026, NULL, NULL, 1),
(90, 399, 2026, 4, 'ANTONIO CASTILLO SALAS', 'FRANCISCO VILLA', '', NULL, NULL, NULL, NULL, '', 'SAN JUAN DE LA NATURA', NULL, NULL, NULL, '20426', NULL, NULL, NULL, '2026-05-12', '2026-05-26', NULL, 'ANTONIO CASTILLO SALAS', '4651545369', NULL, '74801006010000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-12 18:28:07', '2026-05-12 12:28:07', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(91, 400, 2026, 2, 'MARIO ANTONIO SOTO VELASQUEZ', 'BENITO JUAREZ S/N 1, SAN JACINTO', '', NULL, NULL, NULL, NULL, '', 'SAN JACINTO', 'SAN JACINTO', NULL, NULL, '20425', NULL, NULL, NULL, '2026-05-12', '2026-05-26', NULL, 'MARIO ANTONIO SOTO VELASQUEZ', '4651072655', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-12 18:36:57', '2026-05-12 12:36:57', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(92, 401, 2026, 1, 'MARIA ARCELIA GONZALEZ GUERRERO', 'ING MIGUEL ANGEL BARBERENA VEGA', '', '240', 'ASIGNACION', '', 'PEDRO GUERRERO LUCIO', 'MANUEL DE VELASCO MARTINEZ', 'RINCON DE ROMOS', 'SOLIDARIDAD', '', '', '20416', NULL, 775547.43, 2459841.60, '2026-05-12', '2026-05-26', '2026-05-15', 'MARIA ARCELIA GONZALEZ GUERRERO', '4491871542', NULL, '07001030087002000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/401_2026/croquis/croquis_6a17489e5676f_1779910814.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-15 16:59:06', 0, NULL, 13, '2026-05-12 19:56:17', '2026-05-12 13:56:17', '2026-05-15 10:59:06', '2026-05-27 19:40:15', 246, 2026, NULL, NULL, 1),
(93, 402, 2026, 1, 'JUAN JOSE PEREZ GUILLEN', 'MIGUEL HIDALGO', '', '326', 'ASIGNACION', '', 'JUAN ESCUTIA', 'ZONA PARCELARIA', 'RINCON DE ROMOS', 'VALLE DE LAS DELICIAS', '', '', '20427', NULL, NULL, NULL, '2026-05-12', '2026-05-26', '2026-05-19', 'JUAN JOSE PEREZ GUILLEN', '4651042537', NULL, '70000000868000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/402_2026/croquis/croquis_6a0c973f5492e_1779210047.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-19 17:00:29', 0, NULL, 13, '2026-05-12 20:09:42', '2026-05-12 14:09:42', '2026-05-19 11:00:29', '2026-05-25 16:33:32', 238, 2026, NULL, NULL, 1),
(94, 403, 2026, 1, 'MA DEL CARMEN ROSALES DE LOERA', 'ALVARO OBREGON ORIENTE', '', '142', 'ASIGNACION', '', 'LUIS PASTEUR', 'MEXICO SUR', 'RINCON DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-05-12', '2026-05-26', '2026-05-20', 'MA DEL CARMEN ROSALES DE LOERA', '4651068478', NULL, '70104017010000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/403_2026/croquis/croquis_6a07570c46d0f_1778865932.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-15 17:25:20', 0, NULL, 19, '2026-05-12 20:15:57', '2026-05-12 14:15:57', '2026-05-15 11:25:20', '2026-05-25 16:33:32', 241, 2026, NULL, NULL, 1),
(95, 404, 2026, 2, 'OMAR ANDRES ROMO PADILLA', 'DOLORES HIDALGO S/N', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, 776295.66, 2460702.54, '2026-05-12', '2026-05-26', NULL, 'OMAR ANDRES ROMO PADILLA', '4652071937', NULL, '07001040017006000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-12 20:29:45', '2026-05-12 14:29:45', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(96, 405, 2026, 4, 'ARNULFO AVILA BRISEÑO', 'FRANCISCO I MADERO L-05 M-08 PUERTA DEL MUERTO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'PUERTA DEL MUERTO', NULL, NULL, '20424', NULL, NULL, NULL, '2026-05-12', '2026-05-26', NULL, 'ARNULFO AVILA BRISEÑO', '4491894913', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-12 20:39:37', '2026-05-12 14:39:37', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(97, 406, 2026, 7, 'FERNANDO RODRIGUEZ HERRERA', 'FRACC FRATERNIDAD L 5 M 19 S/N', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'FRATERNIDAD', NULL, NULL, '20410', NULL, NULL, NULL, '2026-05-12', '2026-05-26', NULL, 'FERNANDO RODRIGUEZ HERRERA', '4491518196', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-12 20:51:42', '2026-05-12 14:51:42', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(98, 407, 2026, 7, 'ISMAEL SANTILLAN RODRIGUEZ', 'CHIAPAS #239 M-6 L-26', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'MIGUEL HIDALGO', NULL, NULL, '20417', NULL, 775432.56, 2460270.63, '2026-05-13', '2026-05-27', NULL, 'ISMAEL SANTILLAN RODRIGUEZ', '9994467279', NULL, '07001030077017000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-13 17:27:34', '2026-05-13 11:27:34', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(99, 408, 2026, 1, 'PAMELA MACIAS HERNANDEZ', 'POTRERO', '', '215', 'ASIGNACION', '', 'CHAVENO', 'INSURGENTES', 'RINCON DE ROMOS', 'EL POTRERO', '', '', '20410', NULL, NULL, NULL, '2026-05-13', '2026-05-27', '2026-05-19', 'PAMELA MACIAS HERNANDEZ', '4621238878', NULL, '70102051036000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/408_2026/croquis/croquis_6a0c995c52dc5_1779210588.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-19 20:35:14', 0, NULL, 19, '2026-05-13 18:04:58', '2026-05-13 12:04:58', '2026-05-19 14:35:14', '2026-05-25 16:33:32', 235, 2026, NULL, NULL, 1),
(100, 409, 2026, 7, 'GERARDO ESPARZA HUERTA', 'OCTLI #104 L-28 M-02', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'ESTANCIA DE CHORA', NULL, NULL, '20404', NULL, NULL, NULL, '2026-05-14', '2026-05-28', NULL, 'GERARDO ESPARZA HUERTA', '4651051499', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-14 16:30:30', '2026-05-14 10:30:30', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(101, 410, 2026, 2, 'MUNICIPIO DE RINCON DE ROMOS', 'MIGUEL HIDALGO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-14', '2026-05-28', NULL, 'MUNICIPIO DE RINCON DE ROMOS', '0000000000', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-14 16:44:56', '2026-05-14 10:44:56', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(102, 411, 2026, 2, 'ELISA MACIAS PASILLAS', 'VALLE DELICIAS #108', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'VALLE DE LAS DELICIAS', NULL, NULL, '20427', NULL, NULL, NULL, '2026-05-14', '2026-05-28', NULL, 'ELISA MACIAS PASILLAS', '4651492313', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-14 17:13:39', '2026-05-14 11:13:39', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(103, 412, 2026, 1, 'JUAN PUENTES URRUTIA', 'MOTOLINIA PONIENTE', '', '610-B', 'ASIGNACION', '', 'CUAHUTEMOC', 'CHAPULTEPEC', 'RINCON DE ROMOS', 'BARRIO DE CHORA', '', '', '20406', NULL, NULL, NULL, '2026-05-14', '2026-05-28', '2026-05-20', 'JUAN PUENTES URRUTIA', '4651099754', NULL, '70104050003000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/412_2026/croquis/croquis_6a1712304f51d_1779896880.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-15 15:41:40', 0, NULL, 19, '2026-05-14 17:45:16', '2026-05-14 11:45:16', '2026-05-15 09:41:40', '2026-05-27 15:48:14', 242, 2026, NULL, NULL, 1),
(104, 413, 2026, 2, 'DIANA JAZMIN HERNANDEZ TAFOYA', 'PEDRO GUERRERO LUCIO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-05-14', '2026-05-28', NULL, 'FRANCISCO JAVIER VALLE MARMOLEJO', '3346086050', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-14 18:52:21', '2026-05-14 12:52:21', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(105, 414, 2026, 7, 'CARLOS TOVAR BUENROSTRO', 'ALVARO OBREGON #137  L-02 SUB-060/96', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-14', '2026-05-28', NULL, 'CARLOS TOVAR BUENROSTRO', '4497552637', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-14 18:56:49', '2026-05-14 12:56:49', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(106, 415, 2026, 2, 'PRODUCTORA AGRICOLA ALBARRAN', 'LAZARO CARDENAS S/N', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL BAJIO', NULL, NULL, '20420', NULL, NULL, NULL, '2026-05-14', '2026-05-28', NULL, 'PRODUCTORA AGRICOLA ALBARRAN', '4492875863', NULL, '70601025001000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-14 19:32:46', '2026-05-14 13:32:46', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(107, 416, 2026, 1, 'JUAN MANUEL LUEVANO ROMO', 'OLIVARES SANTANA', '', '109', 'ASIGNACION', '', '4 DE SEPTIEMBRE', 'AV REVOLUCION', 'RINCON DE ROMOS', 'ESCALERAS', '19', '11', '20420', NULL, NULL, NULL, '2026-05-14', '2026-05-28', '2026-05-20', 'JUAN MANUEL LUEVANO ROMO', '9187984604', NULL, '75801051011000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/416_2026/croquis/croquis_6a0f3bfcafe87_1779383292.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-15 17:40:15', 0, NULL, 13, '2026-05-14 20:16:01', '2026-05-14 14:16:01', '2026-05-15 11:40:15', '2026-05-25 16:33:32', 243, 2026, NULL, NULL, 1),
(108, 417, 2026, 1, 'JUANA MARIA RODRIGUEZ MARTINEZ', 'MIGUEL HIDALGO', '', '302', 'ASIGNACION', '', 'INSURGENTES', 'EDEN', 'RINCON DE ROMOS', 'CENTRO HISTORICO', '', '', '20400', NULL, NULL, NULL, '2026-05-15', '2026-05-29', '2026-05-18', 'JUANA MARIA RODRIGUEZ MARTINEZ', '4651077785', NULL, '70103005024000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/417_2026/croquis/croquis_6a0b7b7721df0_1779137399.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-18 20:47:41', 0, NULL, 13, '2026-05-15 16:34:20', '2026-05-15 10:34:20', '2026-05-18 14:47:41', '2026-05-25 16:33:32', 232, 2026, NULL, NULL, 1),
(109, 418, 2026, 1, 'HECTOR MANUEL ALVAREZ LARA', 'POTRERO DE LA CRUZ', '', '105', 'ASIGNACION', '', 'CHAVENO', 'INSURGENTES', 'RINCON DE ROMOS', 'EL POTRERO', '', '', '20410', NULL, NULL, NULL, '2026-05-15', '2026-05-29', '2026-05-19', 'HECTOR MANUEL ALVAREZ LARA', '4493894731', NULL, '70102045041000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/418_2026/croquis/croquis_6a0ccc702f3e7_1779223664.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-19 20:42:50', 0, NULL, 13, '2026-05-15 20:39:41', '2026-05-15 14:39:41', '2026-05-19 14:42:50', '2026-05-25 16:33:32', 236, 2026, NULL, NULL, 1),
(110, 419, 2026, 1, 'HECTOR MANUEL ALVAREZ LARA', 'VICTOR CASTORENA', '', '204', 'ASIGNACION', '', 'MANUEL DELGADO VALADEZ', 'SERGIO JIMENEZ MUNOZ', 'RINCON DE ROMOS', 'SOLIDARIDAD', '', '', '20416', NULL, NULL, NULL, '2026-05-15', '2026-05-29', '2026-05-18', 'HECTOR MANUEL ALVAREZ LARA', '4493894731', NULL, '70103075066000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/419_2026/croquis/croquis_6a0b6bd52d473_1779133397.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-19 20:28:42', 0, NULL, 13, '2026-05-15 20:42:48', '2026-05-15 14:42:48', '2026-05-19 14:28:42', '2026-05-25 16:33:32', 237, 2026, NULL, NULL, 1),
(111, 420, 2026, 7, 'PEDRO SANTILLAN SANTOS', 'PABELLON DE HIDALGO RINCON DE ROMOS', '', NULL, NULL, NULL, NULL, '', 'PABELLON DE HIDALGO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-18', '2026-06-01', NULL, 'PEDRO SANTILLAN SANTOS', '4659511211', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-18 15:12:12', '2026-05-18 09:12:12', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(112, 421, 2026, 1, 'MARTIN REVILLA RODRIGUEZ', 'MOTOLINIA PONIENTE', '', '110', 'ASIGNACION', '', 'MORELOS NORTE', 'MEXICO NORTE', 'RINCON DE ROMOS', 'CENTRO', '', '2', '20400', NULL, NULL, NULL, '2026-05-18', '2026-06-01', '2026-06-03', 'MARITN REVILLA RODRIGUEZ', '4651128965', NULL, '70104014004000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/421_2026/croquis/croquis_6a108f355c96f_1779470133.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-22 17:14:55', 0, NULL, 13, '2026-05-18 17:06:15', '2026-05-18 11:06:15', '2026-05-22 11:14:55', '2026-06-03 16:39:17', 255, 2026, NULL, NULL, 1),
(113, 422, 2026, 1, 'MARIA DEL ROSARIO MERCADO NIEVES', 'MIGUEL HIDALGO', '', '302-A', 'ASIGNACION', '', 'AV ZACATECAS', 'ALVARO OBREGON', 'SAN JACINTO', 'SAN JACINTO', '', '', '20425', NULL, NULL, NULL, '2026-05-18', '2026-06-01', '2026-06-01', 'MARIA DEL ROSARIO MERCADO NIEVES', '4651019734', NULL, '70201025014000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, 'croquis/113/croquis_113_1780342691.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-25 15:39:45', 0, NULL, 13, '2026-05-18 17:39:02', '2026-05-18 11:39:02', '2026-05-25 09:39:45', '2026-06-01 19:38:11', 254, 2026, NULL, NULL, 1),
(114, 423, 2026, 2, 'JORGE CASIMIRO ALLENDE', 'DR. FRANCISCO GUEL JIMENEZ', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-18', '2026-06-01', NULL, 'JORGE CASIMIRO ALLENDE', '4651294587', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-18 19:14:44', '2026-05-18 13:14:44', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(115, 424, 2026, 1, 'ALEJANDRO HERNANDEZ MARTINEZ', 'MATIAS MARIN VARGAS', '', '233', 'ASIGNACION', '', 'PEDRO GUERRERO LUCIO', 'MANUEL DE VELASCO MARTINEZ', 'RINCON DE ROMOS', 'SOLIDARIDAD', '', '', '20416', NULL, 775607.00, 2460032.14, '2026-05-18', '2026-06-01', '2026-05-27', 'ALICIA DE LARA MARQUEZ', '4651229213', NULL, '70103090044000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/424_2026/croquis/croquis_6a1090b8165f8_1779470520.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-22 17:21:03', 0, NULL, 13, '2026-05-18 19:33:03', '2026-05-18 13:33:03', '2026-05-22 11:21:03', '2026-05-27 16:45:01', 245, 2026, NULL, NULL, 1),
(116, 425, 2026, 4, 'JOSE EFREN JUAREZ PONCE', 'EL POLVO SUBD. 065/98', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'VALLE DE LAS DELICIAS', NULL, NULL, '20427', NULL, NULL, NULL, '2026-05-18', '2026-06-01', NULL, 'JOSE EFREN JUAREZ PONCE', '4651490626', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-18 20:22:13', '2026-05-18 14:22:13', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(117, 426, 2026, 1, 'BLANCA ESTHELA GARCIA HERRERA', 'CTO. GAETAN LAVERTU', '', '330', 'ASIGNACION', '', 'CIRCUITO GAETAN LAVERTU', 'CIRCUITO GAETAN LAVERTU', 'RINCON DE ROMOS', 'EMBAJADORES', '5', '12', '20404', NULL, 777116.02, 2461273.36, '2026-05-19', '2026-06-02', '2026-06-03', 'MARIA GARCIA HERRERA', '4651058206', NULL, '70101080012000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/426_2026/croquis/croquis_6a1091a905901_1779470761.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-22 17:25:50', 0, NULL, 19, '2026-05-19 19:13:27', '2026-05-19 13:13:27', '2026-05-22 11:25:50', '2026-06-03 18:07:31', 253, 2026, NULL, NULL, 1),
(118, 427, 2026, 2, 'SERGIO ANTONIO CASTRO SANDOVAL', 'DOLORES HIDALGO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-19', '2026-06-02', NULL, 'SERGIO ANTONIO CASTRO SANDOVAL', '4494348051', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-19 20:19:43', '2026-05-19 14:19:43', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(121, 428, 2026, 1, 'RODOLFO DANIEL Y CONDS VILLALPANDO HERNANDEZ', 'SIN NOMBRE', '', '409', 'ASIGNACION', '', 'ANTONIO MUNOZ ACOSTA', 'J REFUGIO JIMENEZ L', 'RINCON DE ROMOS', 'FUNDADORES', '', '', NULL, NULL, NULL, NULL, '2026-05-19', '2026-06-02', '2026-05-22', 'JULIO CESAR ALVARADO RAMOS', '4494684155', NULL, '70103170019000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/428_2026/croquis/croquis_6a0f459df4141_1779385757.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-21 17:49:03', 0, NULL, 19, '2026-05-19 20:50:14', '2026-05-19 14:50:14', '2026-05-21 11:49:03', '2026-06-02 19:04:13', 261, 2026, NULL, NULL, 1),
(125, 432, 2026, 8, 'CADENA COMERCIAL OXXO', 'MOTOLINIA 710 EL CHAVEÑO - NUEVA 201 PABELLON DE HIDALGO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL CHAVEÑO', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-20', '2026-06-03', NULL, 'OLGA PATRICIA', '4773931895', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-20 15:28:12', '2026-05-20 09:28:12', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(126, 433, 2026, 1, 'ROSA MARTHA MARIN SANTANA', 'ALDAMA Y MOTOLINIA', '', '101', 'ASIGNACION', '', 'HEROICO COLEGIO MILITAR', 'MOTOLINIA PONIENTE', 'RINCON DE ROMOS', 'BARRIO DE CHORA', '', '', '20406', NULL, 775795.67, 2460915.31, '2026-05-20', '2026-06-03', '2026-05-25', 'ROSA MARTHA MARIN SANTANA', '4651050828', NULL, '07001040082013000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/433_2026/croquis/croquis_6a1489f1667ec_1779730929.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-25 17:41:06', 0, NULL, 13, '2026-05-20 15:53:29', '2026-05-20 09:53:29', '2026-05-29 10:47:23', '2026-05-29 16:47:23', 250, 2026, NULL, NULL, 1);
INSERT INTO `tramites` (`id`, `folio_numero`, `folio_anio`, `tipo_tramite_id`, `propietario`, `direccion`, `numero`, `numero_asignado`, `tipo_asignacion`, `referencia_anterior`, `entre_calle1`, `entre_calle2`, `localidad`, `colonia`, `manzana`, `lote`, `cp`, `calle`, `lat`, `lng`, `fecha_ingreso`, `fecha_entrega`, `fecha_constancia`, `solicitante`, `telefono`, `correo`, `cuenta_catastral`, `superficie`, `ine_archivo`, `oficio_vobo`, `titulo_archivo`, `predial_archivo`, `escrituras_archivo`, `Resolucion`, `foto_predio_archivo`, `formato_constancia`, `carta_poder`, `foto1_archivo`, `foto2_archivo`, `croquis_archivo`, `otros_archivos`, `datos_especificos`, `comentario_sin_doc`, `estatus`, `observaciones`, `verificador_nombre`, `aprobado_por`, `fecha_aprobacion`, `aprobado_director`, `fecha_aprobacion_director`, `usuario_creador_id`, `created_at`, `tiempo_ingreso`, `tiempo_salida`, `updated_at`, `folio_salida_numero`, `folio_salida_anio`, `tramite_principal_id`, `licencia_numero`, `cantidad`) VALUES
(131, 438, 2026, 2, 'BRYAN JOSSUE GARDUÑO SORIA', 'OBREGON ORIENTE', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-20', '2026-06-03', NULL, 'BRYAN JOSSUE GARDUÑO SORIA', '4491067317', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-20 17:07:25', '2026-05-20 11:07:25', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(132, 439, 2026, 2, 'BRYAN JOSSUE GARDUÑO SORIA', 'ALVARO OBREGON', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-20', '2026-06-03', NULL, 'BRYAN JOSSUE GARDUÑO SORIA', '4491067317', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-20 17:10:59', '2026-05-20 11:10:59', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(135, 440, 2026, 7, 'GREGORIO SANCHEZ BELTRAN', 'CHIHUAHUA', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'MIGUEL HIDALGO', NULL, NULL, '20417', NULL, NULL, NULL, '2026-05-20', '2026-06-03', NULL, 'JOSEFINA SANCHEZ CASTRO', '4651133801', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-20 18:51:36', '2026-05-20 12:51:36', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(137, 442, 2026, 7, 'MARIA ELENA GARCIA LOERA', 'NIÑOS HEROES', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'MAR NEGRO', NULL, NULL, '20420', NULL, NULL, NULL, '2026-05-20', '2026-06-03', NULL, 'MARIA ELENA GARCIA LOERA', '4651264151', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-20 18:53:13', '2026-05-20 12:53:13', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(147, 444, 2026, 7, 'OBRAS PUBLICAS', 'VARIAS CALLES', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-21', '2026-06-04', NULL, 'OBRAS PUBLICAS', '4651234567', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-21 14:26:10', '2026-05-21 08:26:10', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(151, 445, 2026, 2, 'RAQUEL CONTRERAS ACEVEDO Y CONDS', 'PRIMO VERDAD', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-21', '2026-06-04', NULL, 'RAQUEL CONTRERAS ACEVEDO Y CONDS', '4659556109', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-21 18:01:45', '2026-05-21 12:01:45', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(154, 447, 2026, 2, 'RAFAEL GARCIA LUEVANO', 'MEXICO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'JOSE LUIS MACIAS', NULL, NULL, '20404', NULL, 776377.07, 2461057.47, '2026-05-22', '2026-06-05', NULL, 'RAFAEL GARCIA LUEVANO', '4651004587', NULL, '07001040013011000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-22 16:16:52', '2026-05-22 10:16:52', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(155, 448, 2026, 4, 'SAMUEL ROMO CASTORENA', 'ALVARO OBREGON', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL CHAVEÑO', NULL, NULL, NULL, NULL, 777121.99, 2460705.00, '2026-05-22', '2026-06-05', NULL, 'SAMUEL ROMO CASTORENA', '4651048134', NULL, '07001010041015000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'rev de subdivision', 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-22 16:17:15', '2026-05-22 10:17:15', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(156, 449, 2026, 1, 'MARGARITA LUEVANO LAZARIN', 'JACARANDA', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-22', '2026-06-05', NULL, 'MARGARITA LUEVANO LAZARIN', '4641047521', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-22 16:59:18', '2026-05-22 10:59:18', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(157, 450, 2026, 1, 'MARGARITA LUEVANO LAZARIN', 'JACARANDA', '', '406-A', 'ASIGNACION', '', 'ROBLE', 'PINO', 'RINCON DE ROMOS', 'SANTA CRUZ', '10', '11', '20406', NULL, NULL, NULL, '2026-05-22', '2026-06-05', '2026-05-27', 'MARGARITA LUEVANO LAZARIN', '4651047521', NULL, '70104057020000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/157/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-27 20:16:04', 0, NULL, 19, '2026-05-22 17:03:04', '2026-05-22 11:03:04', '2026-05-27 14:23:28', '2026-05-27 20:47:30', 248, 2026, NULL, NULL, 1),
(158, 451, 2026, 2, 'ARIANA CECILIA MACCIAS PEREZ', 'PILARES', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'VALLE DEL REAL', NULL, NULL, '20404', NULL, NULL, NULL, '2026-05-22', '2026-06-05', NULL, 'ARIANA CECILIA MACCIAS PEREZ', '0000000000', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-22 17:25:49', '2026-05-22 11:25:49', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(159, 452, 2026, 1, 'SANDRA BERTOSSI SILVA', 'PONCIANO ARRIAGA', '', '318', 'ASIGNACION', '', 'VALENTIN GOMEZ FARIAS', 'FRANCISCO I MADERO', 'PABELLON DE HIDALGO', 'CONSTITUCION', '3', '2', '20437', NULL, NULL, NULL, '2026-05-22', '2026-06-05', '2026-06-01', 'SANDRA BERTOSSI SILVA', '4651082526', NULL, '70301002002000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/159/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-27 20:31:45', 0, NULL, 19, '2026-05-22 17:47:40', '2026-05-22 11:47:40', '2026-05-27 14:35:35', '2026-06-01 15:14:06', 249, 2026, NULL, NULL, 1),
(160, 453, 2026, 5, 'JAIME MUÑOS PRIETO', 'VALLE DEL REAL', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'VALLE DEL REAL', NULL, NULL, '20404', NULL, NULL, NULL, '2026-05-22', '2026-06-05', NULL, 'JAIME MUÑOS PRIETO', '4651068478', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-22 17:54:43', '2026-05-22 11:54:43', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(161, 454, 2026, 1, 'SEVERO OCON ARGANDOÑA', 'INSURGENTES', '', '510', 'ASIGNACION', '', 'BENITO JUAREZ', 'FRANCISCO I MADERO', 'RINCON DE ROMOS', '16 DE SEPTIEMBRE', '15', '6', '20427', NULL, NULL, NULL, '2026-05-22', '2026-06-05', '2026-06-08', 'SEVERO OCON ARGANDOÑA', '4651025987', NULL, '73401015008000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/161/croquis/croquis_3.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-29 20:45:36', 0, NULL, 13, '2026-05-22 19:52:10', '2026-05-22 13:52:10', '2026-06-02 14:14:31', '2026-06-08 16:25:13', 265, 2026, NULL, NULL, 1),
(162, 455, 2026, 4, 'CAROLINA CERVANTES POZO', 'CALLE SIN NOMBRE', '', NULL, NULL, NULL, NULL, '', 'SAN JACINTO', 'SAN JACINTO', NULL, NULL, '20425', NULL, NULL, NULL, '2026-05-22', '2026-06-05', NULL, 'CAROLINA CERVANTES POZO', '4776482715', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-22 20:13:28', '2026-05-22 14:13:28', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(163, 456, 2026, 4, 'CAROLINA CERVANTES POZO', 'CALLE SIN NOMBRE', '', NULL, NULL, NULL, NULL, '', 'SAN JACINTO', 'SAN JACINTO', NULL, NULL, '20425', NULL, NULL, NULL, '2026-05-22', '2026-06-05', NULL, 'CAROLINA CERVANTES POZO', '4776482715', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-22 20:16:59', '2026-05-22 14:16:59', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(164, 457, 2026, 4, 'CAROLINA CERVANTES POZO', 'CALLE SIN NOMBRE', '', NULL, NULL, NULL, NULL, '', 'SAN JACINTO', 'SAN JACINTO', NULL, NULL, '20425', NULL, NULL, NULL, '2026-05-22', '2026-06-05', NULL, 'CAROLINA CERVANTES POZO', '4776482715', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-22 20:19:44', '2026-05-22 14:19:44', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(165, 458, 2026, 4, 'CAROLINA CERVANTES POZO', 'CALLE SIN NOMBRE', '', NULL, NULL, NULL, NULL, '', 'SAN JACINTO', 'SAN JACINTO', NULL, NULL, '20425', NULL, NULL, NULL, '2026-05-22', '2026-06-05', NULL, 'CAROLINA CERVANTES POZO', '4776482715', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-22 20:22:43', '2026-05-22 14:22:43', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(178, 459, 2026, 7, 'MA CONSUELO TRINIDAD MACIAS', 'PANTEON MUNICIPAL SECCION 11 TERRENO 170', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'PANTEON', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-25', '2026-06-08', NULL, 'MA CONSUELO TRINIDAD MACIAS', '4651181756', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-25 15:43:57', '2026-05-25 09:43:57', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(180, 460, 2026, 2, 'MA GUADALUPE ROMAN CUEVAS', 'DOLORES HIDALGO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-25', '2026-06-08', NULL, 'LUCIA CALZADA RODRIGUEZ', '4651621376', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-25 15:52:41', '2026-05-25 09:52:41', NULL, '2026-05-25 16:33:32', NULL, NULL, NULL, NULL, 1),
(181, 461, 2026, 1, 'ELISA MURILLO MENDEZ', 'DOLORES HIDALGO', '', '326-A', 'ASIGNACION', '', 'HEROICO COLEGIO MILITAR', 'MOTOLINIA ORIENTE', 'RINCON DE ROMOS', 'JOSE LUIS MACIAS', '1', '6', '20404', NULL, NULL, NULL, '2026-05-25', '2026-06-08', '2026-05-29', 'ELISA MURILLO MENDEZ', '4494805571', NULL, '70101031001000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/181/croquis/croquis_2.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-29 18:33:48', 0, NULL, 13, '2026-05-25 16:16:37', '2026-05-25 10:16:37', '2026-06-03 10:10:24', '2026-06-03 16:17:55', 270, 2026, NULL, NULL, 1),
(182, 462, 2026, 1, 'JULIAN ROMO CASTAÑEDA', 'ALVARO OBREGON', '', '916', 'ASIGNACION', '', 'CALLEJON ROMO', 'AV RUTA DE LA PLATA', 'RIINCON DE ROMOS', 'BARRIO DE GUADALUPE', '', '', '20400', NULL, 777179.37, 2460669.46, '2026-05-25', '2026-06-08', '2026-05-25', 'CECILIA ROMO GAYTAN', '4492245773', NULL, '70101047002000', '240', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/462_2026/croquis/croquis_6a1490f9c1ea1_1779732729.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-25 18:11:55', 0, NULL, 19, '2026-05-25 16:56:41', '2026-05-25 10:56:41', '2026-06-02 13:55:30', '2026-06-02 19:56:22', 264, 2026, NULL, NULL, 1),
(183, 463, 2026, 1, 'FLAVIO SANCHEZ LAGUNA', '20 DE NOVIEMBRE', '', '206-A', 'ASIGNACION', '', '5 DE MAYO', '24 DE FEBRERO', 'PABELLON DE HIDALGO', 'HECTOR HUGO OLIVARES', '7', '8', '20437', NULL, NULL, NULL, '2026-05-25', '2026-06-08', '2026-06-08', 'FLAVIO SANCHEZ LAGUNA', '4492139743', NULL, '70302045023000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/183/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-29 19:46:59', 0, NULL, 19, '2026-05-25 17:27:42', '2026-05-25 11:27:42', '2026-06-01 09:15:02', '2026-06-08 20:58:06', 252, 2026, NULL, NULL, 1),
(184, 464, 2026, 1, 'MA TERESA RODRIGUEZ FIERROS Y COND', 'MAZATL', '', '208', 'ASIGNACION', '', 'OCTLI', 'AV RUTA DE LA PLATA', 'RINCON DE ROMOS', 'ESTANCIA DE CHORA', '15', '9', '20404', NULL, NULL, NULL, '2026-05-25', '2026-06-08', '2026-05-29', 'MA TERESA RODRIGUEZ FIERROS', '4651637638', NULL, '70101062009000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/184/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado', '', 'VENTANILLA', 19, '2026-06-08 20:28:20', 0, NULL, 19, '2026-05-25 17:45:17', '2026-05-25 11:45:17', '2026-06-08 14:28:20', '2026-06-08 20:28:20', 267, 2026, NULL, NULL, 1),
(185, 465, 2026, 2, 'AA TALLER DE ARQUITECTURA SA DE CV', 'PROFA SOLEDAD RAUDRY PEDROZA M 3 L 7', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'RINCON REAL', NULL, NULL, '20403', NULL, NULL, NULL, '2026-05-25', '2026-06-08', NULL, 'AA TALLER DE ARQUITECTURA SA DE CV', '4651410197', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-25 19:19:21', '2026-05-25 13:19:21', NULL, '2026-05-25 19:19:21', NULL, NULL, NULL, NULL, 1),
(187, 467, 2026, 1, 'ALEJANDRO RIGOBERTO Y COND. ESCOBEDO SILVA.', 'CHICHIMECATL', '', '131', 'ASIGNACION', '', 'COPALLI', 'OCTLI', 'RINCON DE ROMOS', 'ESTANCIA DE CHORA', '08', '31', '20404', NULL, 777364.95, 2461024.03, '2026-05-26', '2026-06-09', '2026-05-29', 'BRENDA ANGELICA LUEVANO DE LA ROSA Y ALEJANDRO RIGOBERTO ESCOBEDO SILVA', '4651079686', NULL, '70101055031000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/187/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-29 19:00:58', 0, NULL, 19, '2026-05-26 15:34:38', '2026-05-26 09:34:38', '2026-06-03 08:31:15', '2026-06-15 14:32:51', 268, 2026, NULL, NULL, 1),
(188, 468, 2026, 7, 'ROCIO FABIOLA DIOSDADO SILVESTRE', 'VICTORIA', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, 776367.75, 2460768.08, '2026-05-26', '2026-06-09', NULL, 'ROCIO FABIOLA DIOSDADO SILVESTRE', '4651093493', NULL, '07001040015023000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-26 15:40:29', '2026-05-26 09:40:29', NULL, '2026-05-26 15:40:29', NULL, NULL, NULL, NULL, 1),
(189, 469, 2026, 1, 'MIGUEL MARMOLEJO MONTOYA', 'ANAHUAC', '', '116', 'ASIGNACION', '', 'INSURGENTES PONIENTE', 'EDEN', 'RINCON DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-05-26', '2026-06-09', '2026-05-27', 'MIGUEL MARMOLEJO MONTOYA', '4651262705', NULL, '70103007017000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/469_2026/croquis/croquis_6a171620702bd_1779897888.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-27 16:04:38', 0, NULL, 19, '2026-05-26 16:32:01', '2026-05-26 10:32:01', '2026-06-02 14:16:21', '2026-06-02 20:16:21', 266, 2026, NULL, NULL, 1),
(190, 470, 2026, 4, 'FRIDA MICHELLE IBARRA LOMELI', '24 DE OCTUBRE', '', NULL, NULL, NULL, NULL, '', 'PABELLON DE HIDALGO', NULL, NULL, NULL, '20437', NULL, NULL, NULL, '2026-05-26', '2026-06-09', NULL, 'FRIDA MICHELLE IBARRA LOMELI', '4495418392', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-05-26 17:46:23', '2026-05-26 11:46:23', NULL, '2026-05-26 17:46:23', NULL, NULL, NULL, NULL, 1),
(193, 471, 2026, 7, 'JUAN MANUEL PUENTES GONZALEZ', 'PLUTARCO ELIAS CALLES', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'FUNDADORES', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-26', '2026-06-09', NULL, 'JUAN MANUEL PUENTES GONZALEZ', '4492875863', NULL, '70103169017000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-26 17:55:58', '2026-05-26 11:55:58', NULL, '2026-05-26 17:55:58', NULL, NULL, NULL, NULL, 1),
(200, 472, 2026, 1, 'ANTONIO ROBLES MARTINEZ', 'GALEANA', '', '104', 'ASIGNACION', '', 'IGNACIO ALLENDE', 'IGNACIO ALDAMA', 'PABELLON DE HIDALGO', 'CENTRO', '', '', '20437', NULL, NULL, NULL, '2026-05-26', '2026-06-09', '2026-06-08', 'ANTONIO ROBLES MARTINEZ', '4651190684', NULL, '70302029002000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/200/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-29 19:43:06', 0, NULL, 13, '2026-05-26 19:25:20', '2026-05-26 13:25:20', '2026-06-01 14:49:59', '2026-06-08 20:00:33', 256, 2026, NULL, NULL, 1),
(201, 473, 2026, 2, 'MARIO MANUEL SANTANA OROZCO', 'RANCHO EL OLIVAR', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'REAL DEL COLOMO', NULL, NULL, '20416', NULL, NULL, NULL, '2026-05-26', '2026-06-09', NULL, 'MARIO MANUEL SANTANA OROZCO', '0000000000', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-26 19:54:03', '2026-05-26 13:54:03', NULL, '2026-05-26 19:54:03', NULL, NULL, NULL, NULL, 1),
(202, 474, 2026, 1, 'VICTORIA IVETTE REYES CORONADO', 'CALLE SIN NOMBRE', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'LAS PALMAS', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27', '2026-06-10', NULL, 'VICTORIA IVETTE REYES CORONADO', '4651136834', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-27 14:31:36', '2026-05-27 08:31:36', NULL, '2026-05-27 14:31:36', NULL, NULL, NULL, NULL, 1),
(203, 475, 2026, 1, 'HILARIO CASILLAS SOLEDAD', 'FRANCISCO I MADERO', '', '302', 'ASIGNACION', '', 'INSURGENTES', 'MONTE', 'RINCON DE ROMOS', '16 DE SEPTIEMBRE', '19', '1', '20427', NULL, NULL, NULL, '2026-05-27', '2026-06-10', '2026-06-09', 'GLORIA CASILLAS MARMOLEJO', '4651025987', NULL, '73401019001000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/203/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-02 15:46:01', 0, NULL, 13, '2026-05-27 16:02:18', '2026-05-27 10:02:18', '2026-06-02 09:47:02', '2026-06-09 19:24:30', 259, 2026, NULL, NULL, 1),
(244, 477, 2026, 1, 'JOSE FRANCISCO MARTINEZ SERRANO', 'LAS HORMIGAS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-27', '2026-06-10', NULL, 'JOSE FRANCISCO MARTINEZ SERRANO', '4491074565', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-27 18:33:15', '2026-05-27 12:33:15', NULL, '2026-05-27 18:33:15', NULL, NULL, NULL, NULL, 1),
(245, 478, 2026, 1, 'GOLD CORPORATION SA DE CV', 'CHAVENO', '', '701', 'ASIGNACION', '', 'POTRERO', 'POTRERO DEL VALLE', 'RINCON DE ROMOS', 'EL POTRERO', '10', 'L-1 SUBD 070-2018', NULL, NULL, NULL, NULL, '2026-05-27', '2026-06-10', '2026-06-02', 'GOLD CORPORATION SA DE CV', '4494898035', NULL, '70102048044000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/245/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-02 19:28:07', 0, NULL, 13, '2026-05-27 18:37:19', '2026-05-27 12:37:19', '2026-06-02 13:29:20', '2026-06-03 19:07:29', 263, 2026, NULL, NULL, 1),
(246, 479, 2026, 8, 'NUEVA WALMART DE MEXICO S DE RL DE CV', 'BOULERVARD AL BAJIO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'REAL EL COLOMO', NULL, NULL, '20400', NULL, 776599.05, 2461571.13, '2026-05-28', '2026-06-11', NULL, 'VIOLETA EUGENIA RENDON SUAREZ', '5638776572', NULL, '07000990010187000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 12, '2026-05-28 16:27:05', '2026-05-28 10:27:05', NULL, '2026-05-28 16:27:05', NULL, NULL, NULL, NULL, 1),
(247, 479, 2026, 8, 'NUEVA WALMART DE MEXICO S DE RL DE CV', 'BOULERVARD AL BAJIO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'REAL EL COLOMO', NULL, NULL, '20400', NULL, 776599.05, 2461571.13, '2026-05-28', '2026-06-11', NULL, 'VIOLETA EUGENIA RENDON SUAREZ', '5638776572', NULL, '07000990010187000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 12, '2026-05-28 16:27:05', '2026-05-28 10:27:05', NULL, '2026-05-28 16:27:05', NULL, NULL, 246, NULL, 1),
(248, 480, 2026, 7, 'MA LUISA MATA HERNANDEZ', 'AV. CONSTITUCION', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-05-28', '2026-06-11', NULL, 'CLAUDIA ANAHI MATA ESPARZA', '4493981376', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-28 16:57:01', '2026-05-28 10:57:01', NULL, '2026-05-28 16:57:01', NULL, NULL, NULL, NULL, 1),
(249, 481, 2026, 1, 'ROBERTO CASTORENA VALDEZ', 'ENRIQUE OLIVARES SANTANA', '', '105-A', 'ASIGNACION', '', 'OLIVARES SANTANA', '20 DE NOVIEMBRE', 'PABLO ESCALERAS', 'PABLO ESCALERAS', '12', '1', NULL, NULL, 774869.47, 2463360.05, '2026-05-28', '2026-06-11', '2026-06-08', 'ANA ISABEL CASTORENA PUENTES', '4651545805', NULL, '70000008146000', '4022', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/249/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-03 19:21:17', 0, NULL, 19, '2026-05-28 17:21:38', '2026-05-28 11:21:38', '2026-06-03 13:23:06', '2026-06-08 20:44:00', 272, 2026, NULL, NULL, 1),
(250, 482, 2026, 2, 'CADENA COMERCIAL OXXO SOCIEDAD ANONIMA DE CAPITAL VARIABLE', 'CHAPULTEPEC', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'BARRIO DE CHORA', NULL, NULL, '20406', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OLGA FABIOLA HERNANDEZ', '4773931895', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-03 19:46:40', 0, NULL, 19, '2026-05-29 14:57:15', '2026-05-29 08:57:15', NULL, '2026-06-03 19:46:40', NULL, NULL, NULL, NULL, 1),
(251, 482, 2026, 1, 'CADENA COMERCIAL OXXO SOCIEDAD ANONIMA DE CAPITAL VARIABLE', 'CHAPULTEPEC', '', '203', 'ASIGNACION', '', 'ALAMOS', 'SU MISMA CALLE', 'RINCON DE ROMOS', 'RINCONADA ALAMEDA', '3', '1', '20406', NULL, NULL, NULL, '2026-05-29', '2026-06-12', '2026-06-09', 'OLGA FABIOLA HERNANDEZ', '4773931895', NULL, '70104025119000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/251/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-03 19:46:40', 0, NULL, 19, '2026-05-29 14:57:15', '2026-05-29 08:57:15', '2026-06-03 13:47:47', '2026-06-09 18:59:19', 273, 2026, 250, NULL, 1),
(252, 482, 2026, 2, 'CADENA COMERCIAL OXXO SOCIEDAD ANONIMA DE CAPITAL VARIABLE', 'CHAPULTEPEC', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'BARRIO DE CHORA', NULL, NULL, '20406', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OLGA FABIOLA HERNANDEZ', '4773931895', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-03 19:46:40', 0, NULL, 19, '2026-05-29 14:57:15', '2026-05-29 08:57:15', NULL, '2026-06-03 19:46:40', NULL, NULL, 250, NULL, 1),
(253, 482, 2026, 1, 'CADENA COMERCIAL OXXO SOCIEDAD ANONIMA DE CAPITAL VARIABLE', 'CHAPULTEPEC', '', '205', 'ASIGNACION', '', 'ALAMOS', 'SU MISMA CALLE', 'RINCON DE ROMOS', 'RINCONADA ALAMEDA', '3', '2', '20406', NULL, NULL, NULL, '2026-05-29', '2026-06-12', '2026-06-09', 'OLGA FABIOLA HERNANDEZ', '4773931895', NULL, '70104025120000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/253/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-03 19:46:40', 0, NULL, 19, '2026-05-29 14:57:15', '2026-05-29 08:57:15', '2026-06-03 13:56:19', '2026-06-09 19:11:32', 274, 2026, 250, NULL, 1),
(254, 483, 2026, 1, 'MA LUISA MARTINEZ HERNANDEZ', 'AFECTO', '', '117', 'ASIGNACION', '', 'UNIDAD', 'LIMITE DEL FRACCIONAMIENTO', 'RINCON DE ROMOS', 'FRATERNIDAD', '1', '12', '20410', NULL, NULL, NULL, '2026-05-29', '2026-06-12', '2026-06-08', 'MA LUISA MARTINEZ HERNANDEZ', '4651044082', NULL, '70104122012000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/254/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-03 18:20:29', 0, NULL, 13, '2026-05-29 16:27:35', '2026-05-29 10:27:35', '2026-06-03 12:21:54', '2026-06-08 20:53:06', 271, 2026, NULL, NULL, 1),
(255, 484, 2026, 1, 'MA GUADALUPE LUCIO ESPARZA', 'PROL. LIBERTAD', '', '709', 'ASIGNACION', '', 'LA PEDRERA', 'NIOS HEROES', 'RINCON DE ROMOS', 'SANTA ELENA', '', '', '20403', NULL, NULL, NULL, '2026-05-29', '2026-06-12', '2026-06-02', 'MA GUADALUPE LUCIO ESPARZA', '4651097787', NULL, '70104006021000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/255/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-02 19:06:34', 0, NULL, 13, '2026-05-29 17:39:42', '2026-05-29 11:39:42', '2026-06-02 13:08:06', '2026-06-03 14:53:48', 262, 2026, NULL, NULL, 1),
(256, 485, 2026, 7, 'ROGELIO CUEVAS AGUILERA', 'GUADALUPE', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'JOSE LUIS MACIAS', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'JOSE LUIS DE LA ROSA', '4651288468', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-29 18:02:47', '2026-05-29 12:02:47', NULL, '2026-05-29 18:02:47', NULL, NULL, NULL, NULL, 1),
(257, 486, 2026, 7, 'JUAN LUEVANO RODRIGUEZ', 'LIBERTAD', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SANTA ELENA', NULL, NULL, '20403', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'JOSE LUIS DE LA ROSA', '4651288468', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-05-29 18:05:32', '2026-05-29 12:05:32', NULL, '2026-05-29 18:05:32', NULL, NULL, NULL, NULL, 1),
(258, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', '103-B', 'ASIGNACION', '', 'ALVARO OBREGON', 'DR ALBERTO SABIN', 'RINCÓN DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', '2026-06-01', 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', '', 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, 'croquis/258/croquis_258_1780336608.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-29 19:10:05', 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', '2026-05-29 12:17:44', '2026-06-08 19:54:59', 251, 2026, NULL, NULL, 1),
(259, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', '103', 'ASIGNACION', '', 'ALVARO OBREGON', 'DR ALBERTO SABIN', 'RINCÓN DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', '2026-06-02', 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', '', 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/259/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-05-29 19:12:14', 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', '2026-06-02 08:51:17', '2026-06-08 19:54:59', 258, 2026, 258, NULL, 15),
(260, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(261, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(262, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(263, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(264, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(265, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(266, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(267, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(268, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(269, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(270, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Aprobado por Verificador', '', 'JUAN CARLOS VERIFICADOR GÓMEZ', 2, '2026-05-29 19:24:52', 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(271, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', '103-A', 'ASIGNACION', '', 'ALVARO OBREGON', 'DR ALBERTO SABIN', 'RINCÓN DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', '2026-06-02', 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', '', 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/271/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'JUAN CARLOS VERIFICADOR GÓMEZ', 2, '2026-05-29 19:08:40', 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', '2026-06-02 08:50:52', '2026-06-08 19:54:59', 257, 2026, 258, NULL, 15),
(272, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', '103-B', 'ASIGNACION', '', 'ALVARO OBREGON', 'DR ALBERTO SABIN', 'RINCÓN DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', '2026-06-02', 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', '', 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/272/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'JUAN CARLOS VERIFICADOR GÓMEZ', 2, '2026-05-29 18:18:49', 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', '2026-06-02 09:52:46', '2026-06-08 19:54:59', 260, 2026, 258, NULL, 15),
(273, 487, 2026, 1, 'ELIMELEC ARECHIGA ARECHIGA Y CONDS', 'LUIS PASTEUR', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-05-29', '2026-06-12', NULL, 'OBED ARECHIGA ARECHIGA', '4495163270', NULL, '07001040017001000', NULL, 'uploads/487/2026/ine.pdf', '', NULL, 'uploads/487/2026/predial.pdf', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Aprobado por Verificador', '', 'JUAN CARLOS VERIFICADOR GÓMEZ', 2, '2026-05-29 18:16:53', 0, NULL, 18, '2026-05-29 18:16:35', '2026-05-29 12:16:35', NULL, '2026-06-08 19:54:59', NULL, NULL, 258, NULL, 15),
(274, 488, 2026, 2, 'ERIK IVAN PINEDA MACIAS', 'INSURGENTES', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL POTRERO', NULL, NULL, '20410', NULL, NULL, NULL, '2026-06-01', '2026-06-15', NULL, 'ERIK IVAN PINEDA MACIAS', '4651097518', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-01 17:00:40', '2026-06-01 11:00:40', NULL, '2026-06-01 17:00:40', NULL, NULL, NULL, NULL, 1),
(275, 489, 2026, 1, 'JESUS MENDEZ RODRIGUEZ', 'SAUZ', '', '104-E', 'ASIGNACION', '', 'MATIAS MARIN', 'CALLE POMEX', 'RINCON DE ROMOS', 'SANTA CRUZ', '17', '2', '20406', NULL, NULL, NULL, '2026-06-01', '2026-06-15', '2026-06-08', 'JUAN REFUGIO MENDEZ SALAS', '9313343874', NULL, '70104070002000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/275/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-03 15:17:57', 0, NULL, 13, '2026-06-01 17:40:23', '2026-06-01 11:40:23', '2026-06-03 09:23:05', '2026-06-08 20:37:21', 269, 2026, NULL, NULL, 1),
(276, 490, 2026, 1, 'FREDERICO ALVAREZ VELAZQUEZ', 'PRIMO VERDAD', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'BARRIO DE CHORA', NULL, NULL, '20406', NULL, NULL, NULL, '2026-06-01', '2026-06-15', NULL, 'FREDERICO ALVAREZ VELAZQUEZ', '4494412552', NULL, '70103002003000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 12, '2026-06-01 18:33:17', '2026-06-01 12:33:17', NULL, '2026-06-01 18:33:17', NULL, NULL, NULL, NULL, 1),
(277, 491, 2026, 1, 'MANUEL NERI GONZALEZ', '20 DE NOVIEMBRE', '', '513', 'ASIGNACION', '', 'FRANCISCO VILLA', 'FRANCISCO I MADERO', 'RINCON DE ROMOS', '16 DE SEPTIEMBRE', '11', '9', '20427', NULL, NULL, NULL, '2026-06-01', '2026-06-15', '2026-06-09', 'CINTHIA NERI DELGADO', '4651025987', NULL, '73401011009000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/277/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-05 20:43:15', 0, NULL, 13, '2026-06-01 19:08:24', '2026-06-01 13:08:24', '2026-06-05 14:45:02', '2026-06-09 15:42:57', 277, 2026, NULL, NULL, 1),
(278, 492, 2026, 7, 'MA DEL REFUGIO CAMACHO BENITEZ', 'VALENTIN GOMEZ FARIAS', '', NULL, NULL, NULL, NULL, '', 'PABELLON DE HIDALGO', 'CONSTITUCION', NULL, NULL, '20437', NULL, NULL, NULL, '2026-06-02', '2026-06-16', NULL, 'MA DEL REFUGIO CAMACHO BENITEZ', '4494155826', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-02 14:32:12', '2026-06-02 08:32:12', NULL, '2026-06-02 14:32:12', NULL, NULL, NULL, NULL, 1),
(279, 493, 2026, 7, 'GILBERTO SEGURA VILLALOBOS', 'MANUEL DELGADO VALADEZ ESQ. SERGIO JIMENEZ MUÑOZ', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-06-02', '2026-06-16', NULL, 'GILBERTO SEGURA VILLALOBOS', '5093051209', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-02 14:57:16', '2026-06-02 08:57:16', NULL, '2026-06-02 14:57:16', NULL, NULL, NULL, NULL, 1),
(280, 494, 2026, 1, 'SANTIAGO RODRIGUEZ PALACIOS', 'CHIAPAS', '', '209', 'ASIGNACION', '', 'CAMPECHE', 'COLIMA', 'RINCON DE ROMOS', 'MIGUEL HIDALGO', '6', '11', '20404', NULL, NULL, NULL, '2026-06-02', '2026-06-16', '2026-06-09', 'SANTIAGO RODRIGUEZ PALACIOS', '4494524258', NULL, '70103078039000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/280/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-05 20:52:18', 0, NULL, 12, '2026-06-02 18:24:38', '2026-06-02 12:24:38', '2026-06-05 14:54:09', '2026-06-09 16:25:37', 278, 2026, NULL, NULL, 1),
(281, 495, 2026, 1, 'MA DE JESUS  HERRERA ZAPATA', 'ENCARNACION ESPARZA QUEZADA', '', '315', 'ASIGNACION', '', 'VICTOR CASTORENA', 'PEDRO GUERRERO LUCIO', 'RINCON DE ROMOS', 'SOLIDARIDAD', '13', '14', '20416', NULL, NULL, NULL, '2026-06-02', '2026-06-16', '2026-06-10', 'MA DE JESUS  HERRERA ZAPATA', '4651027021', NULL, '70103023039000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/281/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado', '', 'VENTANILLA', 13, '2026-06-10 15:29:53', 0, NULL, 19, '2026-06-02 18:42:31', '2026-06-02 12:42:31', '2026-06-10 09:29:53', '2026-06-10 15:29:53', 279, 2026, NULL, NULL, 1),
(282, 496, 2026, 1, 'JOSE MANUEL HERRERA ZAMARRIPA', 'ENRIQUE OLIVARES SANTANA', '', '210', 'ASIGNACION', '', 'PRIVADA MARIANO JIMENEZ', 'PRIVADA INDEPENDENCIA DE MEXICO', 'PABELLON DE HIDALGO', 'CENTRO', '', '', '20437', NULL, NULL, NULL, '2026-06-02', '2026-06-16', '2026-06-04', 'MA GUADALUPE PASILLAS SERVIN', '4494241489', NULL, '70302025020000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/282/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-04 18:54:07', 0, NULL, 13, '2026-06-02 18:50:27', '2026-06-02 12:50:27', '2026-06-04 12:55:58', '2026-06-04 19:01:13', 275, 2026, NULL, NULL, 1),
(283, 497, 2026, 1, 'RIGOBERTO MARTINEZ PADILLA', 'PINO SUAREZ', '', '202', 'ASIGNACION', '', 'ALVARO OBREGON', 'FRANCISCO I MADERO', 'SAN JACINTO', 'FCO I MADERO', '', '', NULL, NULL, NULL, NULL, '2026-06-02', '2026-06-16', '2026-06-10', 'YOLANDA DIAZ', '4498071830', NULL, '70201017003000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/283/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-08 17:57:55', 0, NULL, 19, '2026-06-02 18:54:02', '2026-06-02 12:54:02', '2026-06-08 11:58:43', '2026-06-10 15:33:43', 280, 2026, NULL, NULL, 1),
(284, 498, 2026, 2, 'JESUS MORALES HERNANDEZ', 'GENERAL LAZARO CARDENAS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'BARRIO DE GUADALUPE', NULL, NULL, '20405', NULL, NULL, NULL, '2026-06-02', '2026-06-16', NULL, 'ESPERANZA MORALES', '4651015255', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-02 19:09:41', '2026-06-02 13:09:41', NULL, '2026-06-02 19:09:41', NULL, NULL, NULL, NULL, 1),
(285, 498, 2026, 4, 'JESUS MORALES HERNANDEZ', 'GENERAL LAZARO CARDENAS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'BARRIO DE GUADALUPE', NULL, NULL, '20405', NULL, NULL, NULL, '2026-06-02', '2026-06-16', NULL, 'ESPERANZA MORALES', '4651015255', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-02 19:09:41', '2026-06-02 13:09:41', NULL, '2026-06-02 19:09:41', NULL, NULL, 284, NULL, 1),
(286, 499, 2026, 2, 'JESUS MORALES HERNANDEZ', 'PREDIO 1 DE LA SUBD. 035/2019 DE LA PARCELA 157 Z3 P 1/6', '', NULL, NULL, NULL, NULL, '', 'SAN JACINTO', NULL, NULL, NULL, '20425', NULL, NULL, NULL, '2026-06-02', '2026-06-16', NULL, 'ESPERANZA MORALES', '4651015255', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-02 19:12:29', '2026-06-02 13:12:29', NULL, '2026-06-02 19:12:29', NULL, NULL, NULL, NULL, 1),
(287, 499, 2026, 4, 'JESUS MORALES HERNANDEZ', 'PREDIO 1 DE LA SUBD. 035/2019 DE LA PARCELA 157 Z3 P 1/6', '', NULL, NULL, NULL, NULL, '', 'SAN JACINTO', NULL, NULL, NULL, '20425', NULL, NULL, NULL, '2026-06-02', '2026-06-16', NULL, 'ESPERANZA MORALES', '4651015255', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-02 19:12:29', '2026-06-02 13:12:29', NULL, '2026-06-02 19:12:29', NULL, NULL, 286, NULL, 1),
(288, 500, 2026, 2, 'FLOR MARGARITA MUÑOZ TORRES', 'HEROICA VERACRUZ', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL CHAVEÑO', NULL, NULL, '20405', NULL, NULL, NULL, '2026-06-02', '2026-06-16', NULL, 'FLOR MARGARITA MUÑOZ TORRES', '4493527100', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-02 19:43:39', '2026-06-02 13:43:39', NULL, '2026-06-02 19:43:39', NULL, NULL, NULL, NULL, 1),
(289, 501, 2026, 7, 'A2 A2 TALLER DE ARQUITECTURA SA DE CV', 'PROFRA, SOLEDAD RAUDRY PEDROZA S/N L-7 M-4 RINCON REAL', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'RINCON DE ROMOS', NULL, NULL, '20400', NULL, NULL, NULL, '2026-06-03', '2026-06-17', NULL, 'A2 A2 TALLER DE ARQUITECTURA SA DE CV', '0000000000', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-03 16:32:25', '2026-06-03 10:32:25', NULL, '2026-06-03 16:32:25', NULL, NULL, NULL, NULL, 1),
(290, 502, 2026, 1, 'GOLD CORPORATION SA DE CV', 'PROL AV 20 DE NOVIEMBRE M18 L35', '', '107', 'ASIGNACION', '', 'XOCHITL', 'CHAVENO', 'RINCON DE ROMOS', 'EL POTRERO', '18', '35', NULL, NULL, NULL, NULL, '2026-06-03', '2026-06-17', '2026-06-10', 'FABRISSIO IGNCIO PEREZ', '4491551718', NULL, '70102056035000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/290/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-08 18:00:33', 0, NULL, 19, '2026-06-03 18:40:08', '2026-06-03 12:40:08', '2026-06-08 12:01:49', '2026-06-10 15:47:32', 281, 2026, NULL, NULL, 1),
(291, 503, 2026, 1, 'ALEJANDRO MUÑOZ ROMERO', 'CHAVENO', '', '911', 'ASIGNACION', '', 'POTRERO DEL LLANO', 'POTRERO DEL REY', 'RINCON DE ROMOS', 'EL POTRERO', '6', '1', '20410', NULL, NULL, NULL, '2026-06-03', '2026-06-17', '2026-06-10', 'ROMINA PADILLA SALAZAR', '4651542822', NULL, '70102044044000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/291/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-08 18:08:06', 0, NULL, 19, '2026-06-03 20:00:21', '2026-06-03 14:00:21', '2026-06-08 12:09:19', '2026-06-10 15:51:46', 282, 2026, NULL, NULL, 1),
(292, 504, 2026, 1, 'HECTOR LEONEL OVALLE GAMEZ', 'MORELOS', '', '416', 'ASIGNACION', '', 'HEROICO COLEGIO MILITAR', 'MOTOLINIA ORIENTE', 'RINCON DE ROMOS', 'CENTRO', '', '', '20400', NULL, 776251.62, 2461034.19, '2026-06-03', '2026-06-17', '2026-06-10', 'HECTOR LEONEL OVALLE GAMEZ', '4651090565', NULL, '70104013059000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/292/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-08 18:10:03', 0, NULL, 13, '2026-06-03 20:04:50', '2026-06-03 14:04:50', '2026-06-08 12:16:07', '2026-06-10 15:42:49', 283, 2026, NULL, NULL, 1),
(293, 505, 2026, 1, 'HECTOR LEONEL OVALLE GAMEZ', 'MORELOS NORTE', '', '416-A', 'ASIGNACION', '', 'HEROICO COLEGIO MILITAR', 'MOTOLINIA ORIENTE', 'RINCON DE ROMOS', 'CENTRO', '', '', '20400', NULL, NULL, NULL, '2026-06-03', '2026-06-17', '2026-06-10', 'HECTOR LEONEL OVALLE GAMEZ', '4651090565', NULL, '70104013059000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/293/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-08 18:16:22', 0, NULL, 13, '2026-06-03 20:11:26', '2026-06-03 14:11:26', '2026-06-08 12:18:03', '2026-06-12 17:22:45', 284, 2026, NULL, NULL, 1);
INSERT INTO `tramites` (`id`, `folio_numero`, `folio_anio`, `tipo_tramite_id`, `propietario`, `direccion`, `numero`, `numero_asignado`, `tipo_asignacion`, `referencia_anterior`, `entre_calle1`, `entre_calle2`, `localidad`, `colonia`, `manzana`, `lote`, `cp`, `calle`, `lat`, `lng`, `fecha_ingreso`, `fecha_entrega`, `fecha_constancia`, `solicitante`, `telefono`, `correo`, `cuenta_catastral`, `superficie`, `ine_archivo`, `oficio_vobo`, `titulo_archivo`, `predial_archivo`, `escrituras_archivo`, `Resolucion`, `foto_predio_archivo`, `formato_constancia`, `carta_poder`, `foto1_archivo`, `foto2_archivo`, `croquis_archivo`, `otros_archivos`, `datos_especificos`, `comentario_sin_doc`, `estatus`, `observaciones`, `verificador_nombre`, `aprobado_por`, `fecha_aprobacion`, `aprobado_director`, `fecha_aprobacion_director`, `usuario_creador_id`, `created_at`, `tiempo_ingreso`, `tiempo_salida`, `updated_at`, `folio_salida_numero`, `folio_salida_anio`, `tramite_principal_id`, `licencia_numero`, `cantidad`) VALUES
(294, 506, 2026, 4, 'FRIDA MICHELLE IBARRA LOMELI', '24 DE OCTUBRE', '', NULL, NULL, NULL, NULL, '', 'PABELLON DE HIDALGO', 'CENTRO', NULL, NULL, '20437', NULL, NULL, NULL, '2026-06-04', '2026-06-18', NULL, 'JUAN ANTONIO RAMIRES PUENTES', '4495418392', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-04 15:57:25', '2026-06-04 09:57:25', NULL, '2026-06-04 15:57:25', NULL, NULL, NULL, NULL, 1),
(295, 507, 2026, 1, 'SILVIA LUEVANO RODRIGUEZ', 'FELIPE CASTORENA', '', '306', 'ASIGNACION', '', 'LIMITE DE LA COMUNIDAD', 'LIMITE DE LA COMUNIDAD', 'FRESNILLO', 'EJIDO FRESNILLO', '', '', NULL, NULL, NULL, NULL, '2026-06-04', '2026-06-18', '2026-06-04', 'SILVIA LUEVANO RODRIGUEZ', '4651628457', NULL, '00000000000', '', NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, '.private/295/croquis/croquis_1.jpg', NULL, NULL, NULL, 'Aprobado por Verificador', '', 'ALFREDO DIAZ', 20, '2026-06-04 19:49:10', 0, NULL, 19, '2026-06-04 19:20:03', '2026-06-04 13:20:03', '2026-06-04 13:52:32', '2026-06-15 15:33:07', 276, 2026, NULL, NULL, 1),
(296, 508, 2026, 2, 'DURAGAS SA DE CV', 'AV. UNIVERSIDAD', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SANTA ANITA', NULL, NULL, '20410', NULL, NULL, NULL, '2026-06-05', '2026-06-19', NULL, 'DURAGAS SA DE CV', '4491500703', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-05 14:28:44', '2026-06-05 08:28:44', NULL, '2026-06-05 14:28:44', NULL, NULL, NULL, NULL, 1),
(297, 509, 2026, 7, 'CESAR ENRRIQUE RAMIREZ GARCIA', 'POTRERO DEL VALLE', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL POTRERO', NULL, NULL, '20410', NULL, NULL, NULL, '2026-06-05', '2026-06-19', NULL, 'MARIA GUADALUPE GUARDADO PIZAÑA', '4651223503', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-05 16:14:21', '2026-06-05 10:14:21', NULL, '2026-06-05 16:14:21', NULL, NULL, NULL, NULL, 1),
(298, 510, 2026, 1, 'JORGE LUIS DIAZ ALVARADO', 'POTRERO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL POTRERO', NULL, NULL, '20444', NULL, NULL, NULL, '2026-06-05', '2026-06-19', NULL, 'JORGE LUIS DIAZ ALVARADO', '4651243836', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-05 17:03:05', '2026-06-05 11:03:05', NULL, '2026-06-05 17:03:05', NULL, NULL, NULL, NULL, 1),
(299, 511, 2026, 4, 'RAUL SAMBRANO MUÑOZ', 'CALLE SIN NOMBRE L-8 M-24', '', NULL, NULL, NULL, NULL, '', 'SAN JUAN DE LA NATURA', 'SAN JUAN DE LA NATURA', NULL, NULL, '20426', NULL, NULL, NULL, '2026-06-05', '2026-06-19', NULL, 'OMAR EDUARDO DE LA TORRE GOMEZ', '4659557410', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-05 19:36:57', '2026-06-05 13:36:57', NULL, '2026-06-05 19:36:57', NULL, NULL, NULL, NULL, 1),
(300, 512, 2026, 1, 'PEDRO VENEGAS SOTO', 'NICOLAS BRAVO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'BARRIO DE CHORA', NULL, NULL, '20406', NULL, NULL, NULL, '2026-06-05', '2026-06-19', NULL, 'DAVID MACIAS', '4651091572', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-05 20:13:13', '2026-06-05 14:13:13', NULL, '2026-06-05 20:13:13', NULL, NULL, NULL, NULL, 1),
(301, 513, 2026, 4, 'RAQUEL RODRIGUEZ HERNANDEZ', 'PARCELA NO 101 Z01 P1/3', '', NULL, NULL, NULL, NULL, '', 'FRESNILLO', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-06-05', '2026-06-19', NULL, 'RAQUEL RODRIGUEZ HERNANDEZ', '0000000000', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-05 20:23:26', '2026-06-05 14:23:26', NULL, '2026-06-05 20:23:26', NULL, NULL, NULL, NULL, 1),
(302, 514, 2026, 1, 'RODOLFO DANIEL VILLALPANDO HERNANDEZ Y CONDS 1', 'PROL. PLUTARCO ELIAS CALLES M-2 L-21', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'FUNDADORES', NULL, NULL, '20400', NULL, NULL, NULL, '2026-06-05', '2026-06-19', NULL, 'NANCY', '4492875863', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-05 20:42:25', '2026-06-05 14:42:25', NULL, '2026-06-05 20:42:25', NULL, NULL, NULL, NULL, 1),
(303, 515, 2026, 7, 'SECRETARIA DE OBRAS PUBLICAS', '16 DE SEPTIEMBRE ,EL SAUCILLO', '', NULL, NULL, NULL, NULL, '', 'EL SAUCILLO', 'RINCON DE ROMOS', NULL, NULL, '20400', NULL, NULL, NULL, '2026-06-08', '2026-06-22', NULL, 'JOSE LUIS MAGDALENO RODRIGUEZ', '4491413096', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-08 17:09:37', '2026-06-08 11:09:37', NULL, '2026-06-08 17:09:37', NULL, NULL, NULL, NULL, 1),
(304, 516, 2026, 7, 'JUAN ANTONIO LOPEZ GAYTAN', 'MANUEL DE VELAZCO MARTINEZ', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-06-08', '2026-06-22', NULL, 'GIOVANA NIEVES LOPEZ', '4494337644', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-08 18:28:06', '2026-06-08 12:28:06', NULL, '2026-06-08 18:28:06', NULL, NULL, NULL, NULL, 1),
(305, 517, 2026, 7, 'IRMA AGUILERA ORTEGA', 'MANUEL DE VELASCO MARTINEZ', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-06-08', '2026-06-22', NULL, 'IRMA AGUILERA ORTEGA', '4651253147', NULL, '2026214748', NULL, 'uploads/517/2026/ine.pdf', '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-08 18:50:30', '2026-06-08 12:50:30', NULL, '2026-06-08 19:41:45', NULL, NULL, NULL, NULL, 1),
(306, 518, 2026, 1, 'NORMA ANGELICA RODRIGUEZ RAMIREZ', 'LAZARO CARDENAS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'ESTANCIA DE MOSQUEIRA', NULL, NULL, '20437', NULL, NULL, NULL, '2026-06-08', '2026-06-22', NULL, 'RIGOBERTO ARMENDARIZ GARAY', '4651043834', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-08 20:25:22', '2026-06-08 14:25:22', NULL, '2026-06-08 20:25:22', NULL, NULL, NULL, NULL, 1),
(307, 519, 2026, 7, 'RAFAEL DE LUNA HERNANDEZ', 'CARLOS RAMOS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'REAL DEL COLOMO', NULL, NULL, '20416', NULL, NULL, NULL, '2026-06-09', '2026-06-23', NULL, 'RAFAEL DE LUNA HERNANDEZ', '4494696788', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-09 16:35:12', '2026-06-09 10:35:12', NULL, '2026-06-09 16:35:12', NULL, NULL, NULL, NULL, 1),
(309, 520, 2026, 4, 'SAMUEL ROMO CASTAÑEDA', 'ALVARO OBREGON EL CHAVEÑO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL CHAVEÑO', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-09', '2026-06-23', NULL, 'SAMUEL ROMO CASTAÑEDA', '4651048134', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-09 19:39:44', '2026-06-09 13:39:44', NULL, '2026-06-09 19:39:44', NULL, NULL, NULL, NULL, 1),
(310, 521, 2026, 1, 'GILBERTO VILLALOBOS SEGURA', 'JIMENEZ MUÑOZ', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-06-09', '2026-06-23', NULL, 'GILBERTO VILLALOBOS SEGURA', '4495604374', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-09 20:27:20', '2026-06-09 14:27:20', NULL, '2026-06-10 15:05:55', NULL, NULL, NULL, NULL, 1),
(314, 33, 2026, 9, 'PRUEBA', 'PRUEBA', '', NULL, NULL, NULL, NULL, '', 'RINCÓN DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-06-10', '2026-06-24', NULL, 'PRUEBA', '0000000000', NULL, '07001040017001000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 18, '2026-06-10 15:07:11', '2026-06-10 09:07:11', NULL, '2026-06-10 15:07:22', NULL, NULL, NULL, NULL, 1),
(315, 522, 2026, 7, 'JOSE RAUL CORTES AYALA', 'PROLONGACION 5 DE MAYO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'VALLE DEL REAL', NULL, NULL, '20404', NULL, NULL, NULL, '2026-06-10', '2026-06-24', NULL, 'JOSE RAUL CORTES AYALA', '4491927542', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-10 16:21:41', '2026-06-10 10:21:41', NULL, '2026-06-10 16:21:41', NULL, NULL, NULL, NULL, 1),
(316, 523, 2026, 1, 'HUGO OMAR ZUÑIGA GARZA', 'MORELOS SUR', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'CENTRO', NULL, NULL, '20400', NULL, NULL, NULL, '2026-06-10', '2026-06-24', NULL, 'HUGO OMAR ZUÑIGA GARZA', '4651084559', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-10 16:22:15', '2026-06-10 10:22:15', NULL, '2026-06-10 16:22:15', NULL, NULL, NULL, NULL, 1),
(317, 524, 2026, 7, 'BERTHA ESTELA DE LA CRUZ PADILLA', 'PANTEON MUNICIPAL S8 T138 Y T140', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', NULL, NULL, NULL, '20400', NULL, NULL, NULL, '2026-06-10', '2026-06-24', NULL, 'BERTHA ESTELA DE LA CRUZ PADILLA', '4491671345', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 12, '2026-06-10 17:58:30', '2026-06-10 11:58:30', NULL, '2026-06-10 17:58:30', NULL, NULL, NULL, NULL, 1),
(318, 525, 2026, 1, 'ANTONIO ROMO RODRIGUEZ', 'AV CONSTITUCION', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-06-10', '2026-06-24', NULL, 'SARA DEL ROSARIO PEREZ LOPEZ', '4651011453', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-10 20:59:26', '2026-06-10 14:59:26', NULL, '2026-06-10 20:59:26', NULL, NULL, NULL, NULL, 1),
(319, 526, 2026, 7, 'JOSE ANTONIO PINEDO CATAÑO', 'MATIAS MARIN VARGAS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-06-11', '2026-06-25', NULL, 'JOSE ARMANDO PINEDA MACIAS', '4659555929', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-11 15:36:35', '2026-06-11 09:36:35', NULL, '2026-06-11 15:36:35', NULL, NULL, NULL, NULL, 1),
(320, 527, 2026, 2, 'LAURA ALICIA RODRIGUEZ PUENTES', 'ROSAURA ZAPATA', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SANTA ELENA', NULL, NULL, '20403', NULL, NULL, NULL, '2026-06-11', '2026-06-25', NULL, 'LAURA ALICIA RODRIGUEZ PUENTES', '4651351210', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-11 17:53:29', '2026-06-11 11:53:29', NULL, '2026-06-11 17:53:29', NULL, NULL, NULL, NULL, 1),
(321, 528, 2026, 1, 'ADELA IBARRA REYES', 'PREDIO RUSTICO', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'EL MILAGRO', NULL, NULL, '20436', NULL, NULL, NULL, '2026-06-12', '2026-06-26', NULL, 'DARIO PEREZ IBARRA', '4651129513', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-12 15:29:04', '2026-06-12 09:29:04', NULL, '2026-06-12 15:29:04', NULL, NULL, NULL, NULL, 1),
(322, 529, 2026, 2, 'JORGE ALREDO CORREA LUEVANO', 'EMILIANO ZAPATA', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SANTA ANITA', NULL, NULL, '20410', NULL, NULL, NULL, '2026-06-12', '2026-06-26', NULL, 'FATIMA RIVERA CALZADA', '4495794660', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-12 15:41:14', '2026-06-12 09:41:14', NULL, '2026-06-12 15:41:14', NULL, NULL, NULL, NULL, 1),
(323, 530, 2026, 7, 'ANA LILIA ESQUIVEL BASALDUA', 'J. ISABEL ESPARZA RODRIGUEZ', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SOLIDARIDAD', NULL, NULL, '20416', NULL, NULL, NULL, '2026-06-12', '2026-06-26', NULL, 'GUILLERMO ALVAREZ GONZALEZ', '4651134043', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-12 17:04:12', '2026-06-12 11:04:12', NULL, '2026-06-12 17:04:12', NULL, NULL, NULL, NULL, 1),
(324, 531, 2026, 7, 'JOSE LUIS QUIROZ ESPINO', 'MORELOS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SAN JOSE', NULL, NULL, '20415', NULL, 776371.37, 2459806.31, '2026-06-12', '2026-06-26', NULL, 'JOSE LUIS QUIROZ ESPINO', '4651031263', NULL, '07001030020018000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-12 17:29:01', '2026-06-12 11:29:01', NULL, '2026-06-12 17:29:01', NULL, NULL, NULL, NULL, 1),
(325, 531, 2026, 2, 'JOSE LUIS QUIROZ ESPINO', 'MORELOS', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', 'SAN JOSE', NULL, NULL, '20415', NULL, 776371.37, 2459806.31, '2026-06-12', '2026-06-26', NULL, 'JOSE LUIS QUIROZ ESPINO', '4651031263', NULL, '07001030020018000', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 13, '2026-06-12 17:29:01', '2026-06-12 11:29:01', NULL, '2026-06-12 17:29:01', NULL, NULL, 324, NULL, 1),
(326, 532, 2026, 4, 'ESPERANZA CALVILLO HERNANDEZ', 'ALVARO OBREGON ORIENTE', '', NULL, NULL, NULL, NULL, '', 'RINCON DE ROMOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-15', '2026-06-29', NULL, 'ESPERANZA CALVILLO HERNANDEZ', '4651217725', NULL, '2026214748', NULL, NULL, '', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En revisión', NULL, NULL, NULL, NULL, 0, NULL, 19, '2026-06-15 16:11:22', '2026-06-15 10:11:22', NULL, '2026-06-15 16:11:22', NULL, NULL, NULL, NULL, 1);

--
-- Disparadores `tramites`
--
DELIMITER $$
CREATE TRIGGER `trg_cuenta_catastral_auto` BEFORE INSERT ON `tramites` FOR EACH ROW BEGIN
  DECLARE siguiente INT DEFAULT 1;
  -- Si no se proporcionó cuenta catastral, asignar automáticamente
  IF (NEW.cuenta_catastral IS NULL OR TRIM(NEW.cuenta_catastral) = '') THEN
    SELECT COALESCE(MAX(CAST(cuenta_catastral AS UNSIGNED)), 0) + 1
      INTO siguiente
      FROM tramites
     WHERE cuenta_catastral REGEXP '^[0-9]+$';
    -- Formato: AAAANNNNNN  (año 4 dígitos + secuencial 6 dígitos) = 10 dígitos puros
    SET NEW.cuenta_catastral = CONCAT(YEAR(NOW()), LPAD(siguiente, 6, '0'));
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_historial_insert` AFTER INSERT ON `tramites` FOR EACH ROW BEGIN
  INSERT INTO historial_tramites
    (tramite_id, usuario_id, accion, estatus_nuevo, comentario)
  VALUES
    (NEW.id, NEW.usuario_creador_id, 'Creado', NEW.estatus, 'Trámite creado en el sistema');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites_adicionales`
--

CREATE TABLE `tramites_adicionales` (
  `id` int(11) NOT NULL,
  `tramite_principal_id` int(11) NOT NULL,
  `tipo_tramite_id` int(11) NOT NULL,
  `propietario` varchar(150) NOT NULL,
  `solicitante` varchar(150) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `folio_numero_adicional` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `estatus` enum('En revisión','Aprobado por Verificador','Aprobado','Rechazado','En corrección') NOT NULL DEFAULT 'En revisión',
  `verificador_nombre` varchar(150) DEFAULT NULL,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `aprobado_por` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `folio_salida_numero` int(11) DEFAULT NULL,
  `folio_salida_anio` int(11) DEFAULT NULL,
  `numero_asignado` varchar(50) DEFAULT NULL,
  `tipo_asignacion` varchar(50) DEFAULT 'ASIGNACION',
  `referencia_anterior` varchar(255) DEFAULT NULL,
  `entre_calle1` varchar(255) DEFAULT NULL,
  `entre_calle2` varchar(250) DEFAULT NULL,
  `cuenta_catastral` varchar(50) DEFAULT NULL,
  `manzana` varchar(50) DEFAULT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `fecha_constancia` date DEFAULT NULL,
  `foto1_archivo` varchar(255) DEFAULT NULL,
  `foto2_archivo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramites_folios_salida`
--

CREATE TABLE `tramites_folios_salida` (
  `id` int(11) NOT NULL,
  `tramite_id` int(11) NOT NULL,
  `folio_salida_numero` int(11) NOT NULL,
  `folio_salida_anio` int(11) NOT NULL,
  `posicion_en_grupo` int(11) NOT NULL DEFAULT 0,
  `numero_asignado` varchar(50) DEFAULT NULL,
  `tipo_asignacion` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('Usuario','Ventanilla','Verificador','Administrador') DEFAULT 'Usuario',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `token_recuperacion` varchar(100) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `correo`, `password`, `rol`, `activo`, `fecha_registro`, `ultimo_acceso`, `token_recuperacion`, `token_expira`) VALUES
(1, 'Admin', 'Sistema', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 0, '2026-03-10 20:04:43', '2026-04-09 14:44:00', NULL, NULL),
(2, 'Juan Carlos', 'Verificador Gómez', 'verificador@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Verificador', 1, '2026-03-10 20:04:43', '2026-06-08 19:18:51', NULL, NULL),
(3, 'María Elena', 'Secretaria López', 'secretaria@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 0, '2026-03-10 20:04:43', '2026-03-31 01:39:55', NULL, NULL),
(4, 'Pedro Antonio', 'Usuario Martínez', 'usuario@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario', 1, '2026-03-10 20:04:43', '2026-06-10 15:57:42', NULL, NULL),
(10, 'FRANCISCO', 'FLORES', 'fco.flores.1508@gmail.com', '$2y$10$7SUXXUMegirlEobidcWk0.VVm4h.R.Y1emADhlKR6jrA7GS1nlN7e', 'Administrador', 1, '2026-04-08 15:27:34', '2026-04-08 15:30:32', NULL, NULL),
(11, 'ALFREDO', 'DIAZ GARCIA', 'alfredodiaz130578@gmail.com', '$2y$10$xidy/X0NhZn2Xv277QZWXutyzSEdEmwF//TGKQ2hCmcp/xhUX3Ak.', 'Verificador', 1, '2026-04-08 15:34:40', '2026-04-21 16:44:20', NULL, NULL),
(12, 'JAIRO DAMIAN', 'LOPEZ VALLE', 'jairo.lopez133@gmail.com', '$2y$10$JoOYDWbqFF5KWiYIvpjWb.DqKiFnIlu3yaAAo7Z3iUQ2wUwuU8yTq', 'Ventanilla', 1, '2026-04-08 16:04:32', '2026-06-10 19:10:32', NULL, NULL),
(13, 'AZUL MARIA', 'CAMPOS PEREZ', 'camposazul246@gmail.com', '$2y$10$yJPtHohM7Dbqa3abYYanfef2L3fI12GLjN2MRim2DswQVDfluFqnO', 'Ventanilla', 1, '2026-04-08 16:07:20', '2026-06-15 19:57:05', NULL, NULL),
(14, 'HEIDI ALEXA', 'GARCIA DIAZ', 'heidigarciad4@gmail.con', '$2y$10$sV4SWzXrYD7FxpfOucmGo.LM3p2TeAi4nosVpJDKPcQwBLpZzqxcy', 'Ventanilla', 1, '2026-04-08 16:13:31', NULL, NULL, NULL),
(15, 'PEDRO', 'RUIZ DIAZ', 'phrd.123@gmail.com', '$2y$10$xSsVVyfno5cSy.093D20XOJ4Yivw19YJbj4dTiG4iHx96XpC/1hkK', 'Administrador', 1, '2026-04-08 18:04:16', '2026-06-09 15:29:36', '59e3da3cbd6284a91894a826132f5f03e4a65a1d53cc9e692f460e8f3370a6f619acdcbda3a17db8c958d0e4377c07cd9db4', '2026-04-08 13:54:34'),
(16, 'PRUEBA', 'VENTANILLA', 'correoventanilla@correo.com', '$2y$10$a.MBNnTJxWPV.ZiR0EgUpuh5exm6bWH7sdnn/IfaPxUpjzRjsZMAK', 'Ventanilla', 1, '2026-04-08 19:08:34', '2026-04-08 19:08:45', NULL, NULL),
(18, 'VENTANILLA', 'VENATANA', 'ventanilla@vet.com', '$2y$10$iM.Jm3DC.GZQgZfDWZ5BPuLRJIuAKysxExyl2p5PcGKWxJDw5efty', 'Ventanilla', 1, '2026-04-13 14:21:32', '2026-06-15 20:01:21', NULL, NULL),
(19, 'HEIDI ALEXA', 'GARCIA DIAZ', 'heidigarciad4@gmail.com', '$2y$10$S7lVAb9NW96KEKaF9nYYDOU.wJCrglNVjJvgp48q176p.TJOoRiL.', 'Ventanilla', 1, '2026-04-16 19:05:32', '2026-06-15 17:36:09', NULL, NULL),
(20, 'ALFREDO', 'DIAZ', 'alfredodiaz@gmail.com', '$2y$10$t4T78XyZwY5O.AKrB.ay8OBk8JTgmdOS.o1kmpkgQr18jrAKdfku6', 'Verificador', 1, '2026-04-27 20:03:03', '2026-06-15 19:57:14', NULL, NULL),
(21, 'DIR', 'PLANEACIÓN Y DESARROLLO URBANO', 'dir.planeacionydu@gmail.com', '$2y$10$tz8iEv/OwfwTykYfbh5w0.imSxMBDJ0wipBEc3URKiC/PiIv2WCKu', 'Administrador', 1, '2026-06-08 16:34:33', '2026-06-08 16:44:48', NULL, NULL),
(22, 'ANA', 'GARCIA', 'anna.garcia0598@gmail.com', '$2y$10$k76I3JiAUOLlt3ew6A29K.hOEeZJOPXewXpPIhPWlzPj1giIeN5U.', 'Usuario', 1, '2026-06-09 15:19:14', '2026-06-09 15:41:50', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_tramites`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_tramites` (
`id` int(11)
,`folio_numero` int(11)
,`folio_anio` int(11)
,`tipo_tramite_id` int(11)
,`propietario` varchar(150)
,`direccion` varchar(200)
,`localidad` varchar(100)
,`colonia` varchar(100)
,`cp` varchar(10)
,`lat` decimal(12,2)
,`lng` decimal(12,2)
,`fecha_ingreso` date
,`fecha_entrega` date
,`solicitante` varchar(150)
,`telefono` varchar(30)
,`correo` varchar(150)
,`cuenta_catastral` varchar(50)
,`superficie` varchar(50)
,`ine_archivo` varchar(255)
,`titulo_archivo` varchar(255)
,`predial_archivo` varchar(255)
,`escrituras_archivo` varchar(255)
,`foto_predio_archivo` varchar(255)
,`formato_constancia` varchar(255)
,`carta_poder` varchar(255)
,`foto1_archivo` varchar(255)
,`foto2_archivo` varchar(255)
,`otros_archivos` longtext
,`datos_especificos` longtext
,`comentario_sin_doc` text
,`estatus` enum('En revisión','En Revisión por Validador','Aprobado por Verificador','Aprobado','Rechazado','En corrección')
,`observaciones` text
,`verificador_nombre` varchar(150)
,`aprobado_por` int(11)
,`fecha_aprobacion` timestamp
,`aprobado_director` tinyint(1)
,`fecha_aprobacion_director` timestamp
,`usuario_creador_id` int(11)
,`created_at` timestamp
,`updated_at` timestamp
,`tipo_tramite_nombre` varchar(150)
,`tipo_tramite_codigo` varchar(20)
,`creador_nombre_completo` varchar(201)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_tramites`
--
DROP TABLE IF EXISTS `vista_tramites`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_tramites`  AS SELECT `t`.`id` AS `id`, `t`.`folio_numero` AS `folio_numero`, `t`.`folio_anio` AS `folio_anio`, `t`.`tipo_tramite_id` AS `tipo_tramite_id`, `t`.`propietario` AS `propietario`, `t`.`direccion` AS `direccion`, `t`.`localidad` AS `localidad`, `t`.`colonia` AS `colonia`, `t`.`cp` AS `cp`, `t`.`lat` AS `lat`, `t`.`lng` AS `lng`, `t`.`fecha_ingreso` AS `fecha_ingreso`, `t`.`fecha_entrega` AS `fecha_entrega`, `t`.`solicitante` AS `solicitante`, `t`.`telefono` AS `telefono`, `t`.`correo` AS `correo`, `t`.`cuenta_catastral` AS `cuenta_catastral`, `t`.`superficie` AS `superficie`, `t`.`ine_archivo` AS `ine_archivo`, `t`.`titulo_archivo` AS `titulo_archivo`, `t`.`predial_archivo` AS `predial_archivo`, `t`.`escrituras_archivo` AS `escrituras_archivo`, `t`.`foto_predio_archivo` AS `foto_predio_archivo`, `t`.`formato_constancia` AS `formato_constancia`, `t`.`carta_poder` AS `carta_poder`, `t`.`foto1_archivo` AS `foto1_archivo`, `t`.`foto2_archivo` AS `foto2_archivo`, `t`.`otros_archivos` AS `otros_archivos`, `t`.`datos_especificos` AS `datos_especificos`, `t`.`comentario_sin_doc` AS `comentario_sin_doc`, `t`.`estatus` AS `estatus`, `t`.`observaciones` AS `observaciones`, `t`.`verificador_nombre` AS `verificador_nombre`, `t`.`aprobado_por` AS `aprobado_por`, `t`.`fecha_aprobacion` AS `fecha_aprobacion`, `t`.`aprobado_director` AS `aprobado_director`, `t`.`fecha_aprobacion_director` AS `fecha_aprobacion_director`, `t`.`usuario_creador_id` AS `usuario_creador_id`, `t`.`created_at` AS `created_at`, `t`.`updated_at` AS `updated_at`, `tt`.`nombre` AS `tipo_tramite_nombre`, `tt`.`codigo` AS `tipo_tramite_codigo`, concat(`u`.`nombre`,' ',`u`.`apellidos`) AS `creador_nombre_completo` FROM ((`tramites` `t` left join `tipos_tramite` `tt` on(`t`.`tipo_tramite_id` = `tt`.`id`)) left join `usuarios` `u` on(`t`.`usuario_creador_id` = `u`.`id`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calles`
--
ALTER TABLE `calles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_nombre` (`nombre`);

--
-- Indices de la tabla `catastro`
--
ALTER TABLE `catastro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_catastral` (`cuenta_catastral`);

--
-- Indices de la tabla `codigos_postales`
--
ALTER TABLE `codigos_postales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cp` (`codigo_postal`),
  ADD KEY `idx_asentamiento` (`asentamiento`);

--
-- Indices de la tabla `comentarios_tramites`
--
ALTER TABLE `comentarios_tramites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tramite` (`tramite_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_leido` (`leido`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_clave` (`clave`);

--
-- Indices de la tabla `constancias_extra`
--
ALTER TABLE `constancias_extra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_constextra_tramite` (`tramite_id`);

--
-- Indices de la tabla `constancias_generadas`
--
ALTER TABLE `constancias_generadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tramite_id` (`tramite_id`),
  ADD KEY `idx_folio_salida` (`folio_salida_anio`,`folio_salida_numero`);

--
-- Indices de la tabla `folios_salida_secuencia`
--
ALTER TABLE `folios_salida_secuencia`
  ADD PRIMARY KEY (`ano`);

--
-- Indices de la tabla `historial_tramites`
--
ALTER TABLE `historial_tramites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tramite` (`tramite_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`);

--
-- Indices de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_accion` (`accion`);

--
-- Indices de la tabla `solicitudes_registro`
--
ALTER TABLE `solicitudes_registro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_correo` (`correo`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `tipos_tramite`
--
ALTER TABLE `tipos_tramite`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_codigo` (`codigo`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `tramites`
--
ALTER TABLE `tramites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estatus` (`estatus`),
  ADD KEY `idx_fecha_ingreso` (`fecha_ingreso`),
  ADD KEY `idx_propietario` (`propietario`),
  ADD KEY `idx_tipo_tramite` (`tipo_tramite_id`),
  ADD KEY `idx_cuenta_catastral` (`cuenta_catastral`),
  ADD KEY `fk_usuario_creador` (`usuario_creador_id`),
  ADD KEY `fk_aprobado_por` (`aprobado_por`),
  ADD KEY `idx_folio_salida` (`folio_salida_numero`,`folio_salida_anio`),
  ADD KEY `idx_tramite_principal` (`tramite_principal_id`),
  ADD KEY `idx_licencia_numero` (`licencia_numero`),
  ADD KEY `uk_folio` (`folio_numero`,`folio_anio`);

--
-- Indices de la tabla `tramites_adicionales`
--
ALTER TABLE `tramites_adicionales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tramite_principal` (`tramite_principal_id`),
  ADD KEY `idx_tipo_tramite` (`tipo_tramite_id`),
  ADD KEY `idx_folio_salida` (`folio_salida_numero`,`folio_salida_anio`),
  ADD KEY `fk_ta_aprobador` (`aprobado_por`);

--
-- Indices de la tabla `tramites_folios_salida`
--
ALTER TABLE `tramites_folios_salida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tramite` (`tramite_id`),
  ADD KEY `idx_folio_salida` (`folio_salida_numero`,`folio_salida_anio`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_correo` (`correo`),
  ADD KEY `idx_rol` (`rol`),
  ADD KEY `idx_activo` (`activo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calles`
--
ALTER TABLE `calles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `catastro`
--
ALTER TABLE `catastro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `codigos_postales`
--
ALTER TABLE `codigos_postales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de la tabla `comentarios_tramites`
--
ALTER TABLE `comentarios_tramites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `constancias_extra`
--
ALTER TABLE `constancias_extra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `constancias_generadas`
--
ALTER TABLE `constancias_generadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `historial_tramites`
--
ALTER TABLE `historial_tramites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=744;

--
-- AUTO_INCREMENT de la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2194;

--
-- AUTO_INCREMENT de la tabla `solicitudes_registro`
--
ALTER TABLE `solicitudes_registro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `tipos_tramite`
--
ALTER TABLE `tipos_tramite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tramites`
--
ALTER TABLE `tramites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;

--
-- AUTO_INCREMENT de la tabla `tramites_adicionales`
--
ALTER TABLE `tramites_adicionales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `tramites_folios_salida`
--
ALTER TABLE `tramites_folios_salida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios_tramites`
--
ALTER TABLE `comentarios_tramites`
  ADD CONSTRAINT `fk_co_tramite` FOREIGN KEY (`tramite_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_co_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `constancias_extra`
--
ALTER TABLE `constancias_extra`
  ADD CONSTRAINT `fk_constextra_tramite` FOREIGN KEY (`tramite_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `constancias_generadas`
--
ALTER TABLE `constancias_generadas`
  ADD CONSTRAINT `constancias_generadas_ibfk_1` FOREIGN KEY (`tramite_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_tramites`
--
ALTER TABLE `historial_tramites`
  ADD CONSTRAINT `fk_hi_tramite` FOREIGN KEY (`tramite_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hi_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD CONSTRAINT `fk_lo_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tramites`
--
ALTER TABLE `tramites`
  ADD CONSTRAINT `fk_tr_aprobador` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_tr_creador` FOREIGN KEY (`usuario_creador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_tr_principal` FOREIGN KEY (`tramite_principal_id`) REFERENCES `tramites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tr_tipo` FOREIGN KEY (`tipo_tramite_id`) REFERENCES `tipos_tramite` (`id`);

--
-- Filtros para la tabla `tramites_adicionales`
--
ALTER TABLE `tramites_adicionales`
  ADD CONSTRAINT `fk_ta_aprobador` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ta_principal` FOREIGN KEY (`tramite_principal_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ta_tipo` FOREIGN KEY (`tipo_tramite_id`) REFERENCES `tipos_tramite` (`id`);

--
-- Filtros para la tabla `tramites_folios_salida`
--
ALTER TABLE `tramites_folios_salida`
  ADD CONSTRAINT `fk_fs_tramite` FOREIGN KEY (`tramite_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
