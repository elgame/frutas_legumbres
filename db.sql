-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-04-2013 a las 15:23:18
-- Versión del servidor: 5.5.27
-- Versión de PHP: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `frutas_legumbres`
--
CREATE DATABASE `frutas_legumbres` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `frutas_legumbres`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos_bancos`
--

CREATE TABLE IF NOT EXISTS `bancos_bancos` (
  `id_banco` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(25) NOT NULL,
  `status` enum('ac','e') NOT NULL,
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos_cuentas`
--

CREATE TABLE IF NOT EXISTS `bancos_cuentas` (
  `id_cuenta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_banco` int(10) unsigned NOT NULL,
  `numero` varchar(20) NOT NULL,
  `alias` varchar(40) NOT NULL,
  `status` enum('ac','e') NOT NULL,
  PRIMARY KEY (`id_cuenta`),
  KEY `id_banco` (`id_banco`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos_movimientos`
--

CREATE TABLE IF NOT EXISTS `bancos_movimientos` (
  `id_movimiento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_banco` int(10) unsigned NOT NULL,
  `id_cuenta` int(10) unsigned NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `concepto` varchar(254) NOT NULL,
  `monto` double NOT NULL,
  `tipo` enum('d','r') NOT NULL DEFAULT 'd' COMMENT 'd:deposito, r:retiro',
  `metodo_pago` varchar(20) NOT NULL,
  PRIMARY KEY (`id_movimiento`),
  KEY `id_banco` (`id_banco`),
  KEY `id_cuenta` (`id_cuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos_movimientos_conceptos`
--

CREATE TABLE IF NOT EXISTS `bancos_movimientos_conceptos` (
  `id_movimiento` bigint(20) unsigned NOT NULL,
  `no_concepto` int(10) unsigned NOT NULL,
  `concepto` varchar(254) NOT NULL,
  `monto` double NOT NULL,
  PRIMARY KEY (`id_movimiento`,`no_concepto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_inventario`
--

CREATE TABLE IF NOT EXISTS `cajas_inventario` (
  `id_inventario` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_productor` bigint(20) unsigned NOT NULL,
  `id_variedad` int(10) unsigned NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `concepto` varchar(254) NOT NULL,
  `cantidad` double NOT NULL,
  `chofer` varchar(30) NOT NULL,
  `tipo` enum('s','en') NOT NULL DEFAULT 's' COMMENT 's:salida, en:entrada de cajas',
  PRIMARY KEY (`id_inventario`),
  KEY `id_productor` (`id_productor`),
  KEY `id_variedad` (`id_variedad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_recibidas`
--

CREATE TABLE IF NOT EXISTS `cajas_recibidas` (
  `id_caja` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_dueno` bigint(20) unsigned NOT NULL,
  `id_productor` bigint(20) unsigned NOT NULL,
  `id_variedad` int(10) unsigned NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `certificado_tarjeta` varchar(40) NOT NULL,
  `codigo_huerta` varchar(40) NOT NULL,
  `no_lote` int(10) unsigned NOT NULL,
  `cajas` int(10) unsigned NOT NULL,
  `cajas_rezaga` int(10) unsigned NOT NULL,
  `no_ticket` varchar(10) DEFAULT NULL,
  `kilos` int(10) unsigned NOT NULL DEFAULT '0',
  `precio` double NOT NULL,
  `es_organico` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:es organico, 0:no es organico',
  `unidad_transporte` varchar(60) NOT NULL DEFAULT '',
  `dueno_carga` varchar(60) NOT NULL DEFAULT '',
  `observaciones` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_caja`),
  KEY `id_dueno` (`id_dueno`),
  KEY `id_productor` (`id_productor`),
  KEY `id_variedad` (`id_variedad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_recibidas_abonos`
--

CREATE TABLE IF NOT EXISTS `cajas_recibidas_abonos` (
  `id_abono` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_productor` bigint(20) unsigned NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `concepto` varchar(254) NOT NULL,
  `monto` double NOT NULL,
  PRIMARY KEY (`id_abono`),
  KEY `id_productor` (`id_productor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_tratamiento`
--

CREATE TABLE IF NOT EXISTS `cajas_tratamiento` (
  `id_caja` bigint(20) unsigned NOT NULL,
  `id_tratamiento` int(10) unsigned NOT NULL,
  `cantidad` double NOT NULL,
  PRIMARY KEY (`id_caja`,`id_tratamiento`),
  KEY `id_tratamiento` (`id_tratamiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `ci_sessions`
--

INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('3ec41e9550331334d9dce9dc66d90824', '::1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31 AlexaToolba', 1367270534, 'a:6:{s:9:"user_data";s:0:"";s:2:"id";s:1:"1";s:7:"usuario";s:5:"admin";s:5:"email";s:17:"dasdasd@gmail.com";s:4:"tipo";s:5:"admin";s:7:"idunico";s:24:"l517e7e6ad634a6.30457411";}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `duenios_huertas`
--

CREATE TABLE IF NOT EXISTS `duenios_huertas` (
  `id_dueno` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(130) NOT NULL,
  `calle` varchar(60) NOT NULL,
  `no_exterior` varchar(7) NOT NULL,
  `no_interior` varchar(7) NOT NULL,
  `colonia` varchar(60) NOT NULL,
  `municipio` varchar(45) NOT NULL,
  `estado` varchar(45) NOT NULL,
  `cp` int(10) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `status` enum('ac','e') NOT NULL DEFAULT 'ac',
  PRIMARY KEY (`id_dueno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `privilegios`
--

CREATE TABLE IF NOT EXISTS `privilegios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `id_padre` int(10) unsigned NOT NULL,
  `mostrar_menu` tinyint(1) NOT NULL DEFAULT '1',
  `url_accion` varchar(100) NOT NULL,
  `url_icono` varchar(100) NOT NULL,
  `target_blank` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Volcado de datos para la tabla `privilegios`
--

INSERT INTO `privilegios` (`id`, `nombre`, `id_padre`, `mostrar_menu`, `url_accion`, `url_icono`, `target_blank`) VALUES
(1, 'Privilegios', 0, 1, 'privilegios/', 'lock', 0),
(2, 'Agregar', 1, 1, 'privilegios/agregar/', 'plus', 0),
(3, 'Eliminar', 1, 0, 'privilegios/eliminar/', 'remove', 0),
(4, 'Modificar', 1, 0, 'privilegios/modificar/', 'edit', 0),
(5, 'Usuarios', 0, 1, 'usuarios/', 'user', 0),
(6, 'Agregar usuario', 5, 1, 'usuarios/agregar/', 'plus', 0),
(7, 'Modificar', 5, 0, 'usuarios/modificar/', 'edit', 0),
(8, 'Eliminar', 5, 0, 'usuarios/eliminar/', 'remove', 0),
(9, 'Activar', 5, 0, 'usuarios/activar/', 'ok', 0),
(10, 'Productores', 0, 1, 'productores/', 'user', 0),
(11, 'Agregar', 10, 1, 'productores/agregar/', 'plus', 0),
(12, 'Modificar', 10, 0, 'productores/modificar/', 'edit', 0),
(13, 'Eliminar', 10, 0, 'productores/eliminar/', 'remove', 0),
(14, 'Activar', 10, 0, 'productores/activar/', 'ok', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productores`
--

CREATE TABLE IF NOT EXISTS `productores` (
  `id_productor` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_fiscal` varchar(130) NOT NULL,
  `calle` varchar(60) NOT NULL,
  `no_exterior` varchar(7) NOT NULL,
  `no_interior` varchar(7) NOT NULL,
  `colonia` varchar(60) NOT NULL,
  `municipio` varchar(45) NOT NULL,
  `estado` varchar(45) NOT NULL,
  `cp` int(10) NOT NULL,
  `rfc` varchar(13) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `email` varchar(80) NOT NULL,
  `logo` varchar(130) NOT NULL,
  `regimen_fiscal` varchar(200) NOT NULL DEFAULT '',
  `status` enum('ac','e') NOT NULL DEFAULT 'ac',
  `tipo` enum('r','f') NOT NULL DEFAULT 'r' COMMENT 'r:regular (venden fruta), f:ficticio (comprobar gastos)',
  PRIMARY KEY (`id_productor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `productores`
--

INSERT INTO `productores` (`id_productor`, `nombre_fiscal`, `calle`, `no_exterior`, `no_interior`, `colonia`, `municipio`, `estado`, `cp`, `rfc`, `telefono`, `celular`, `email`, `logo`, `regimen_fiscal`, `status`, `tipo`) VALUES
(1, 'Abrahan Jimenez Magaña', '', '', '', '', '', '', 0, '', '', '', '', '', '', 'ac', 'r');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productores_facturas`
--

CREATE TABLE IF NOT EXISTS `productores_facturas` (
  `id_factura` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_productor` bigint(20) unsigned NOT NULL,
  `serie` varchar(30) NOT NULL,
  `folio` bigint(20) NOT NULL,
  `no_aprobacion` bigint(20) NOT NULL,
  `ano_aprobacion` bigint(5) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `importe_iva` double NOT NULL,
  `retencion_iva` double NOT NULL,
  `descuento` double NOT NULL DEFAULT '0',
  `subtotal` double NOT NULL,
  `total` double NOT NULL,
  `total_letra` varchar(250) NOT NULL,
  `img_cbb` varchar(60) NOT NULL,
  `forma_pago` varchar(80) NOT NULL,
  `metodo_pago` varchar(40) NOT NULL,
  `metodo_pago_digitos` varchar(20) NOT NULL,
  `condicion_pago` enum('co','cr') NOT NULL DEFAULT 'co' COMMENT 'cr:credito o co:contado',
  `plazo_credito` int(11) NOT NULL DEFAULT '0',
  `nombre` varchar(240) NOT NULL,
  `rfc` varchar(13) NOT NULL,
  `domicilio` varchar(250) NOT NULL,
  `domicilio2` varchar(220) NOT NULL,
  `status` enum('p','pa','ca') NOT NULL DEFAULT 'pa' COMMENT 'p:pendiente, pa:pagada, ca:cancelada',
  PRIMARY KEY (`id_factura`),
  KEY `id_productor` (`id_productor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productores_facturas_productos`
--

CREATE TABLE IF NOT EXISTS `productores_facturas_productos` (
  `id_fac_prod` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_factura` bigint(20) unsigned NOT NULL,
  `descripcion` varchar(254) NOT NULL,
  `taza_iva` double NOT NULL,
  `cantidad` double NOT NULL,
  `precio_unitario` double NOT NULL,
  `importe` double NOT NULL,
  `importe_iva` double NOT NULL,
  `total` double NOT NULL,
  `descuento` float NOT NULL DEFAULT '0' COMMENT 'Es el % del descuento',
  `retencion` float NOT NULL DEFAULT '0' COMMENT 'Es el % de la retencion',
  `unidad` varchar(20) NOT NULL,
  PRIMARY KEY (`id_fac_prod`),
  KEY `id_factura` (`id_factura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productores_series_folios`
--

CREATE TABLE IF NOT EXISTS `productores_series_folios` (
  `id_serie_folio` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_productor` bigint(20) unsigned NOT NULL,
  `serie` varchar(30) NOT NULL,
  `no_aprobacion` bigint(20) unsigned NOT NULL,
  `folio_inicio` bigint(20) unsigned NOT NULL,
  `folio_fin` bigint(20) unsigned NOT NULL,
  `imagen` varchar(200) NOT NULL,
  `leyenda` varchar(70) NOT NULL,
  `leyenda1` text NOT NULL,
  `leyenda2` text NOT NULL,
  `ano_aprobacion` date NOT NULL,
  PRIMARY KEY (`id_serie_folio`),
  KEY `id_productor` (`id_productor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamientos`
--

CREATE TABLE IF NOT EXISTS `tratamientos` (
  `id_tratamiento` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(40) NOT NULL,
  `status` enum('ac','e') NOT NULL DEFAULT 'ac',
  PRIMARY KEY (`id_tratamiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(110) NOT NULL,
  `usuario` varchar(10) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(70) NOT NULL,
  `tipo` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:activo, 0:eliminado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `password`, `email`, `tipo`, `status`) VALUES
(1, 'admin', 'admin', '12345', 'dasdasd@gmail.com', 'admin', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_privilegios`
--

CREATE TABLE IF NOT EXISTS `usuarios_privilegios` (
  `usuario_id` bigint(20) unsigned NOT NULL,
  `privilegio_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`usuario_id`,`privilegio_id`),
  KEY `privilegio_id` (`privilegio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuarios_privilegios`
--

INSERT INTO `usuarios_privilegios` (`usuario_id`, `privilegio_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variedades`
--

CREATE TABLE IF NOT EXISTS `variedades` (
  `id_variedad` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(40) NOT NULL,
  `tipo_pago` enum('k','c') NOT NULL DEFAULT 'k' COMMENT 'k:kilos, c:cajas',
  `status` enum('ac','e') NOT NULL DEFAULT 'ac' COMMENT 'ac:activo, e:eliminado',
  PRIMARY KEY (`id_variedad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bancos_cuentas`
--
ALTER TABLE `bancos_cuentas`
  ADD CONSTRAINT `bancos_cuentas_ibfk_1` FOREIGN KEY (`id_banco`) REFERENCES `bancos_bancos` (`id_banco`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bancos_movimientos`
--
ALTER TABLE `bancos_movimientos`
  ADD CONSTRAINT `bancos_movimientos_ibfk_1` FOREIGN KEY (`id_banco`) REFERENCES `bancos_bancos` (`id_banco`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bancos_movimientos_ibfk_2` FOREIGN KEY (`id_cuenta`) REFERENCES `bancos_cuentas` (`id_cuenta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bancos_movimientos_conceptos`
--
ALTER TABLE `bancos_movimientos_conceptos`
  ADD CONSTRAINT `bancos_movimientos_conceptos_ibfk_1` FOREIGN KEY (`id_movimiento`) REFERENCES `bancos_movimientos` (`id_movimiento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cajas_inventario`
--
ALTER TABLE `cajas_inventario`
  ADD CONSTRAINT `cajas_inventario_ibfk_1` FOREIGN KEY (`id_productor`) REFERENCES `productores` (`id_productor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cajas_inventario_ibfk_2` FOREIGN KEY (`id_variedad`) REFERENCES `variedades` (`id_variedad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cajas_recibidas`
--
ALTER TABLE `cajas_recibidas`
  ADD CONSTRAINT `cajas_recibidas_ibfk_1` FOREIGN KEY (`id_dueno`) REFERENCES `duenios_huertas` (`id_dueno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cajas_recibidas_ibfk_2` FOREIGN KEY (`id_productor`) REFERENCES `productores` (`id_productor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cajas_recibidas_ibfk_3` FOREIGN KEY (`id_variedad`) REFERENCES `variedades` (`id_variedad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cajas_recibidas_abonos`
--
ALTER TABLE `cajas_recibidas_abonos`
  ADD CONSTRAINT `cajas_recibidas_abonos_ibfk_1` FOREIGN KEY (`id_productor`) REFERENCES `productores` (`id_productor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cajas_tratamiento`
--
ALTER TABLE `cajas_tratamiento`
  ADD CONSTRAINT `cajas_tratamiento_ibfk_1` FOREIGN KEY (`id_caja`) REFERENCES `cajas_recibidas` (`id_caja`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cajas_tratamiento_ibfk_2` FOREIGN KEY (`id_tratamiento`) REFERENCES `tratamientos` (`id_tratamiento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productores_facturas`
--
ALTER TABLE `productores_facturas`
  ADD CONSTRAINT `productores_facturas_ibfk_1` FOREIGN KEY (`id_productor`) REFERENCES `productores` (`id_productor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productores_facturas_productos`
--
ALTER TABLE `productores_facturas_productos`
  ADD CONSTRAINT `productores_facturas_productos_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `productores_facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productores_series_folios`
--
ALTER TABLE `productores_series_folios`
  ADD CONSTRAINT `productores_series_folios_ibfk_1` FOREIGN KEY (`id_productor`) REFERENCES `productores` (`id_productor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios_privilegios`
--
ALTER TABLE `usuarios_privilegios`
  ADD CONSTRAINT `usuarios_privilegios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_privilegios_ibfk_2` FOREIGN KEY (`privilegio_id`) REFERENCES `privilegios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
