-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-05-2013 a las 02:44:22
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `bancos_bancos`
--

INSERT INTO `bancos_bancos` (`id_banco`, `nombre`, `status`) VALUES
(1, 'Banorte', 'ac'),
(2, 'Banbajio', 'ac');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos_cuentas`
--

CREATE TABLE IF NOT EXISTS `bancos_cuentas` (
  `id_cuenta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_banco` int(10) unsigned NOT NULL,
  `numero` varchar(20) NOT NULL,
  `alias` varchar(40) NOT NULL,
  `status` enum('ac','e') NOT NULL DEFAULT 'ac',
  PRIMARY KEY (`id_cuenta`),
  KEY `id_banco` (`id_banco`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `bancos_cuentas`
--

INSERT INTO `bancos_cuentas` (`id_cuenta`, `id_banco`, `numero`, `alias`, `status`) VALUES
(1, 1, '0938666', 'Cuenta 1', 'ac'),
(2, 1, '0938829', 'Cuenta 2', 'ac'),
(3, 2, '122', 'Cuenta bajio', 'ac');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos_movimientos`
--

CREATE TABLE IF NOT EXISTS `bancos_movimientos` (
  `id_movimiento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_banco` int(10) unsigned NOT NULL,
  `id_cuenta` int(10) unsigned NOT NULL,
  `id_fac_productor` bigint(20) unsigned DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `concepto` varchar(254) NOT NULL,
  `monto` double NOT NULL,
  `tipo` enum('d','r') NOT NULL DEFAULT 'd' COMMENT 'd:deposito, r:retiro',
  `metodo_pago` varchar(20) NOT NULL,
  `anombre_de` varchar(100) DEFAULT NULL,
  `moneda` varchar(6) DEFAULT NULL,
  `abono_cuenta` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:no poner legenda; 1:pner legenda',
  PRIMARY KEY (`id_movimiento`),
  KEY `id_banco` (`id_banco`),
  KEY `id_cuenta` (`id_cuenta`),
  KEY `id_fac_productor` (`id_fac_productor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Volcado de datos para la tabla `bancos_movimientos`
--

INSERT INTO `bancos_movimientos` (`id_movimiento`, `id_banco`, `id_cuenta`, `id_fac_productor`, `fecha`, `concepto`, `monto`, `tipo`, `metodo_pago`, `anombre_de`, `moneda`, `abono_cuenta`) VALUES
(4, 1, 1, NULL, '2013-05-06 20:42:00', 'dsadd', 1464, 'r', 'efectivo', NULL, NULL, 0),
(5, 1, 1, NULL, '2013-05-07 15:36:00', 'metemos dinero', 4000, 'd', 'efectivo', NULL, NULL, 0),
(6, 1, 1, NULL, '2013-05-07 15:36:00', 'metemos dinero', 4000, 'd', 'efectivo', NULL, NULL, 0),
(7, 1, 1, NULL, '2013-05-07 15:46:00', 'Pago de la factura 5', 44, 'r', 'tarjeta', NULL, NULL, 0),
(8, 1, 1, NULL, '2013-05-08 00:38:00', 'Pago de la factura 7', 46, 'r', 'cheque', NULL, NULL, 0),
(10, 1, 1, NULL, '2013-05-08 00:45:00', 'dasd', 231, 'r', 'efectivo', NULL, NULL, 0),
(11, 1, 1, 12, '2013-05-08 01:00:00', 'Pago de la factura 8', 396, 'r', 'efectivo', NULL, NULL, 0),
(12, 1, 1, NULL, '2013-05-08 02:03:00', 'das', 500, 'r', 'cheque', 'asdas dasd asd', 'M.N.', 1),
(13, 1, 1, NULL, '2013-05-08 22:51:00', 'dasdasd', 500, 'r', 'cheque', 'Gamaliel Mendoza Solis', 'USD', 0),
(14, 1, 1, 13, '2013-05-08 22:57:00', 'Pago de la factura 9', 1056, 'r', 'cheque', 'Abrahan Jimenez Magaña', 'M.N.', 0),
(18, 1, 1, NULL, '2013-05-11 03:08:00', 'das', 32, 'r', 'cheque', 'Abrahan Jimenez Magaña', 'M.N.', 1),
(19, 1, 1, NULL, '2013-05-11 03:10:00', 'dasd', 43, 'r', 'cheque', 'Abrahan Jimenez Magaña', 'USD', 1),
(20, 2, 3, NULL, '2013-05-11 03:10:00', 'fsdfsdf', 3432, 'd', 'efectivo', NULL, NULL, 0),
(21, 2, 3, NULL, '2013-05-11 15:50:00', 'dasdas', 2322, 'd', 'efectivo', NULL, NULL, 0),
(22, 1, 2, NULL, '2013-05-11 15:51:00', 'asdasd', 34221, 'd', 'transferencia', NULL, NULL, 0),
(23, 1, 2, 14, '2013-05-11 15:52:00', 'Pago de la factura 10', 14042, 'r', 'efectivo', NULL, NULL, 0),
(24, 2, 3, NULL, '2013-05-15 13:38:00', 'dasdas', 21221, 'd', 'efectivo', NULL, NULL, 0),
(25, 2, 3, NULL, '2013-05-15 13:38:00', 'dddd', 2212, 'd', 'transferencia', NULL, NULL, 0),
(26, 2, 3, NULL, '2013-05-15 13:38:00', 'easdasd', 22121, 'd', 'efectivo', NULL, NULL, 0),
(30, 2, 3, NULL, '2013-05-15 14:11:00', 'das', 10000, 'd', 'efectivo', NULL, NULL, 0),
(38, 2, 3, NULL, '2013-05-15 17:30:00', 'dd', 3000, 'r', 'efectivo', NULL, NULL, 0),
(40, 2, 3, NULL, '2013-05-15 17:55:00', 'dasd', 46504, 'r', 'cheque', 'dasdasdasd', 'USD', 0),
(41, 2, 3, 15, '2013-05-15 18:12:00', 'Pago de la factura 1-1', 2370, 'r', 'cheque', 'AngularJSui', 'M.N.', 1);

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

--
-- Volcado de datos para la tabla `bancos_movimientos_conceptos`
--

INSERT INTO `bancos_movimientos_conceptos` (`id_movimiento`, `no_concepto`, `concepto`, `monto`) VALUES
(4, 1, 'fsdfsdf', 400),
(4, 2, 'fklklskdl', 500),
(4, 3, 'kkjkjlakjsd', 564),
(8, 1, 'pango 1', 40),
(8, 2, 'clando 2', 6),
(11, 1, 'das', 396),
(14, 1, 'dasdasdas', 400),
(14, 2, 'dsadasd', 656),
(23, 1, 'jkaskdhaks', 4000),
(23, 2, 'hkahskdjhaks', 5000),
(23, 3, 'lhk ahskd halksdha ksdhlkalks kas kdjhakjs hdkajs hdkjashdk', 5000),
(23, 4, 'ka hskd haksjdh akjsdha shdkaj shdkjasdh', 42),
(41, 1, 'jahskjdhk asd asd asd asd asd asda sd', 1000),
(41, 2, 'dkdkdk a sdas dasd as das dasd', 1000),
(41, 3, 'kjhkjsahdka hskjdha ksjdhka jshd kajshd kjashd kjahsdkjha sdkja hsdkjha ksjdhkj ashdkjashkjdhsad kjashd', 370);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `bancos_movimientos_depositos`
--
CREATE TABLE IF NOT EXISTS `bancos_movimientos_depositos` (
`id_movimiento` bigint(20) unsigned
,`id_banco` int(10) unsigned
,`id_cuenta` int(10) unsigned
,`id_fac_productor` bigint(20) unsigned
,`fecha` timestamp
,`concepto` varchar(254)
,`monto` double
,`tipo` enum('d','r')
,`metodo_pago` varchar(20)
,`anombre_de` varchar(100)
,`moneda` varchar(6)
,`abono_cuenta` tinyint(1)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `bancos_movimientos_retiros`
--
CREATE TABLE IF NOT EXISTS `bancos_movimientos_retiros` (
`id_movimiento` bigint(20) unsigned
,`id_banco` int(10) unsigned
,`id_cuenta` int(10) unsigned
,`id_fac_productor` bigint(20) unsigned
,`fecha` timestamp
,`concepto` varchar(254)
,`monto` double
,`tipo` enum('d','r')
,`metodo_pago` varchar(20)
,`anombre_de` varchar(100)
,`moneda` varchar(6)
,`abono_cuenta` tinyint(1)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_inventario`
--

CREATE TABLE IF NOT EXISTS `cajas_inventario` (
  `id_inventario` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_productor` bigint(20) unsigned NOT NULL,
  `id_variedad` int(10) unsigned NOT NULL,
  `id_caja` bigint(20) unsigned DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `concepto` varchar(254) NOT NULL,
  `cantidad` double NOT NULL,
  `chofer` varchar(30) NOT NULL,
  `tipo` enum('s','en') NOT NULL DEFAULT 's' COMMENT 's:salida, en:entrada de cajas',
  PRIMARY KEY (`id_inventario`),
  KEY `id_productor` (`id_productor`),
  KEY `id_variedad` (`id_variedad`),
  KEY `id_caja` (`id_caja`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Volcado de datos para la tabla `cajas_inventario`
--

INSERT INTO `cajas_inventario` (`id_inventario`, `id_productor`, `id_variedad`, `id_caja`, `fecha`, `concepto`, `cantidad`, `chofer`, `tipo`) VALUES
(1, 3, 1, NULL, '2013-05-05 05:00:00', 'Se llevan cajas para la huerta 22', 100, 'Ramiro Casas', 's'),
(2, 3, 2, NULL, '2013-05-05 05:00:00', 'Mas cajas se llevan', 80, 'Calando chofer', 's'),
(3, 3, 1, 1, '2013-05-05 05:00:00', 'Registro de entradas cajas', 100, '', 'en'),
(4, 3, 1, NULL, '2013-05-08 05:00:00', 'ddasdas', 70, 'da sdasdasd', 'en'),
(5, 2, 1, NULL, '2013-04-01 06:00:00', 'Calando de new lo de agregar cajas', 75, 'chofer de pepe', 's'),
(7, 2, 2, NULL, '2013-05-08 05:00:00', 'ddasdasd', 50, 'dasdasd', 's'),
(8, 2, 1, 3, '2013-05-07 05:00:00', 'Registro de entradas cajas', 75, '', 'en'),
(9, 2, 2, 4, '2013-05-08 05:00:00', 'Registro de entradas cajas', 50, '', 'en'),
(10, 2, 2, 5, '2013-05-15 05:00:00', 'Registro de entradas cajas', 212, '', 'en');

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
  `kilos_rezaga` double NOT NULL DEFAULT '0',
  `total_pagar_kc` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_caja`),
  KEY `id_dueno` (`id_dueno`),
  KEY `id_productor` (`id_productor`),
  KEY `id_variedad` (`id_variedad`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `cajas_recibidas`
--

INSERT INTO `cajas_recibidas` (`id_caja`, `id_dueno`, `id_productor`, `id_variedad`, `fecha`, `certificado_tarjeta`, `codigo_huerta`, `no_lote`, `cajas`, `cajas_rezaga`, `no_ticket`, `kilos`, `precio`, `es_organico`, `unidad_transporte`, `dueno_carga`, `observaciones`, `kilos_rezaga`, `total_pagar_kc`) VALUES
(1, 3, 3, 1, '2013-05-05 05:00:00', '', 'LTW-22-123', 1, 100, 0, '', 0, 130, 0, 'Mercedes', 'Gama', 'Calando entradas de cajas', 0, 100),
(3, 3, 2, 1, '2013-05-07 05:00:00', '2312', '21233', 4, 75, 5, '', 0, 120, 1, 'asdasdasad', 'd asd asdasdasd', 'a das da sda sdasdas', 0, 70),
(4, 2, 2, 2, '2013-05-08 05:00:00', '3442', '4323', 5, 50, 2, '1', 9000, 4.5, 0, 'fsf sdf sdf', 'fsdf sdf sdf sdf', 'sdf sdf sdfsdf sdfsd f', 360, 8640),
(5, 2, 2, 2, '2013-05-15 05:00:00', 'ss', 'ds', 2, 212, 10, '2', 2000, 4, 1, 'das', 'Dasdkajs dkjals dkjasdkl', '', 94, 1906);

--
-- Disparadores `cajas_recibidas`
--
DROP TRIGGER IF EXISTS `agrega_entrada_inventario`;
DELIMITER //
CREATE TRIGGER `agrega_entrada_inventario` AFTER INSERT ON `cajas_recibidas`
 FOR EACH ROW BEGIN
    INSERT INTO cajas_inventario (id_productor, id_variedad, id_caja, fecha, concepto, cantidad, chofer, tipo)
    VALUES (NEW.id_productor, NEW.id_variedad, NEW.id_caja, NEW.fecha, 'Registro de entradas cajas', NEW.cajas, '', 'en');
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `delete_entrada_inventario`;
DELIMITER //
CREATE TRIGGER `delete_entrada_inventario` BEFORE DELETE ON `cajas_recibidas`
 FOR EACH ROW BEGIN

    DELETE FROM cajas_inventario WHERE id_caja = OLD.id_caja;

  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `update_entrada_inventario`;
DELIMITER //
CREATE TRIGGER `update_entrada_inventario` AFTER UPDATE ON `cajas_recibidas`
 FOR EACH ROW BEGIN

    UPDATE cajas_inventario
    SET id_productor = NEW.id_productor, id_variedad = NEW.id_variedad, fecha = NEW.fecha, cantidad = NEW.cajas
    WHERE id_caja = OLD.id_caja;

  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_recibidas_abonos`
--

CREATE TABLE IF NOT EXISTS `cajas_recibidas_abonos` (
  `id_abono` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_caja` bigint(20) unsigned NOT NULL,
  `id_productor` bigint(20) unsigned NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `concepto` varchar(254) NOT NULL,
  `monto` double NOT NULL,
  PRIMARY KEY (`id_abono`),
  KEY `id_productor` (`id_productor`),
  KEY `id_caja` (`id_caja`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Volcado de datos para la tabla `cajas_recibidas_abonos`
--

INSERT INTO `cajas_recibidas_abonos` (`id_abono`, `id_caja`, `id_productor`, `fecha`, `concepto`, `monto`) VALUES
(11, 3, 2, '2013-05-15 17:30:00', 'dd', 3000),
(15, 4, 2, '2013-05-15 17:55:00', 'dasd', 38880),
(16, 5, 2, '2013-05-15 17:55:00', 'dasd', 7624);

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

--
-- Volcado de datos para la tabla `cajas_tratamiento`
--

INSERT INTO `cajas_tratamiento` (`id_caja`, `id_tratamiento`, `cantidad`) VALUES
(1, 1, 30),
(4, 1, 33),
(4, 2, 32),
(5, 1, 100),
(5, 2, 100),
(5, 3, 12);

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
('9ee49b283ec501a9fb35c3dc785769cc', '::1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31 AlexaToolba', 1369182631, 'a:6:{s:9:"user_data";s:0:"";s:2:"id";s:1:"1";s:7:"usuario";s:5:"admin";s:5:"email";s:17:"dasdasd@gmail.com";s:4:"tipo";s:5:"admin";s:7:"idunico";s:24:"l519c0cb297e488.96705867";}');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `duenios_huertas`
--

INSERT INTO `duenios_huertas` (`id_dueno`, `nombre`, `calle`, `no_exterior`, `no_interior`, `colonia`, `municipio`, `estado`, `cp`, `telefono`, `celular`, `status`) VALUES
(1, 'Rodolfo Carretes Ferrera', 'Del medallin', '29', '', 'Los alcoholes', 'Sads', 'Colima', 6300, '', '', 'ac'),
(2, 'Jorge Mejía Villa nueva', 'Capilla sixtina', '', '', '', '', '', 0, '', '', 'ac'),
(3, 'Carlos Sepeda Perez', 'Guatemala', '490', '', 'Juan Jose Rios', 'TECOMAN', 'Colima', 28984, '313 32 48510', '3123123123', 'ac');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomina_salarios_minimos`
--

CREATE TABLE IF NOT EXISTS `nomina_salarios_minimos` (
  `id_salario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zona_a` float unsigned NOT NULL,
  `zona_b` float unsigned NOT NULL,
  `zona_c` float unsigned NOT NULL,
  PRIMARY KEY (`id_salario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `nomina_salarios_minimos`
--

INSERT INTO `nomina_salarios_minimos` (`id_salario`, `zona_a`, `zona_b`, `zona_c`) VALUES
(1, 64.76, 64.76, 61.38);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

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
(14, 'Activar', 10, 0, 'productores/activar/', 'ok', 0),
(15, 'Variedades', 0, 1, 'variedades/', 'leaf', 0),
(16, 'Agregar', 15, 1, 'variedades/agregar/', 'plus', 0),
(17, 'Modificar', 15, 0, 'variedades/modificar/', 'edit', 0),
(18, 'Eliminar', 15, 0, 'variedades/eliminar/', 'remove', 0),
(19, 'Activar', 15, 0, 'variedades/activar/', 'ok', 0),
(20, 'Facturacion', 10, 1, 'productoresfac/', 'file', 0),
(21, 'Series y folios', 20, 1, 'productoresfac/series_folios/', 'list-alt', 0),
(22, 'Agregar', 21, 1, 'productoresfac/agregar_serie_folio/', 'plus', 0),
(23, 'Modificar', 21, 0, 'productoresfac/modificar_serie_folio/', 'edit', 0),
(24, 'Agregar', 20, 1, 'productoresfac/agregar/', 'plus', 0),
(25, 'Detalles facturas', 20, 0, 'productoresfac/detalles_facturas/', 'file', 0),
(26, 'Cancelar', 20, 0, 'productoresfac/cancelar/', 'remove', 0),
(27, 'Imprimir', 20, 0, 'productoresfac/imprimir/', 'print', 0),
(28, 'Dueños Huertas', 0, 1, 'duenios_huertas/', 'user', 0),
(29, 'Agregar', 28, 1, 'duenios_huertas/agregar/', 'plus', 0),
(30, 'Modificar', 28, 0, 'duenios_huertas/modificar/', 'edit', 0),
(31, 'Eliminar', 28, 0, 'duenios_huertas/eliminar/', 'remove', 0),
(32, 'Activar', 28, 0, 'duenios_huertas/activar/', 'ok', 0),
(33, 'Cajas', 0, 1, 'cajas/', 'inbox', 0),
(34, 'Agregar Movimiento', 33, 1, 'cajas/agregar/', 'plus', 0),
(35, 'Entradas', 33, 1, 'cajas/entradas/', 'share-alt', 0),
(36, 'Agregar Entrada', 35, 1, 'cajas/agregar_entrada/', 'plus', 0),
(37, 'Modificar', 33, 0, 'cajas/modificar_entrada/', 'edit', 0),
(38, 'Eliminar', 33, 0, 'cajas/eliminar_entrada/', 'remove', 0),
(39, 'Banco', 0, 1, 'banco/', 'hdd', 0),
(40, 'Agregar operación', 39, 1, 'banco/agregar_operacion/', 'plus-sign', 0),
(41, 'Cuentas por pagar', 33, 1, 'cajas/cuentas_pagar/', 'list-alt', 0),
(42, 'Cuentas', 39, 1, 'banco/cuentas/', 'inbox', 0),
(43, 'Agregar', 42, 1, 'banco/agregar_cuenta/', 'plus', 0),
(44, 'Modificar', 42, 0, 'banco/modificar_cuenta/', 'edit', 0),
(45, 'Eliminar', 42, 0, 'banco/eliminar_cuenta/', 'remove', 0),
(46, 'Activar', 42, 0, 'banco/activar_cuenta/', 'ok', 0),
(47, 'Estado de cuenta', 39, 0, 'banco/estado_cuenta/', 'hdd', 0),
(48, 'Eliminar', 39, 0, 'banco/eliminar_operacion/', 'remove', 0),
(49, 'Reporte Relacion Cajas Recibidas', 33, 1, 'cajas_reportes/rcr/', 'book', 0),
(50, 'Reporte Relacion de Lavado por Lotes', 33, 1, 'cajas_reportes/rll/', 'book', 0),
(51, 'Eliminar', 41, 0, 'abonos/eliminar/', 'remove', 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `productores`
--

INSERT INTO `productores` (`id_productor`, `nombre_fiscal`, `calle`, `no_exterior`, `no_interior`, `colonia`, `municipio`, `estado`, `cp`, `rfc`, `telefono`, `celular`, `email`, `logo`, `regimen_fiscal`, `status`, `tipo`) VALUES
(1, 'Abrahan Jimenez Magaña', 'AV. 20 DE NOVIEMBRE', '2', '', 'Juan Jose Rios', 'Jalapa', 'Veraster', 0, 'AVT920312NQ3', '313 32 48510', '', '', 'application/images/productor/logos/4ba0b3b8ee91fda9af44030a42dd516b.jpg', 'Régimen Intermedio', 'ac', 'f'),
(2, 'ABARROTERA VALDEZ DE TECOMAN S.A. DE C.V.', 'AV. 20 DE NOVIEMBRE', 'S/N', 'AD', 'Juan Jose Rios', 'Michoacán', 'MICHOACAN', 91202, 'XAXX010101000', '313 32 48510', '3123123123', 'contacto@angularjsui.org', 'application/images/productor/logos/621a6495dddfd58218dee02c1eea3a54.jpg', 'Régimen Intermedio', 'ac', 'r'),
(3, 'ROBERTO NEVAREZ DOMINGUEZ', 'PISTA AEREA', 'S/N', '', 'PISTA AEREA', 'RANCHITO', 'MICHOACAN', 60800, 'NEDR091003FVS', '', '', '', '', '', 'ac', 'r'),
(4, 'CARLOS BATISTA ALONSO', '', '', '', '', '', '', 0, '', '', '', '', '', '', 'ac', 'r'),
(5, 'AngularJSui', 'AV. DE LAGO', 'S/N', 'AD', 'Juan Jose Rios', 'TECOMAN', 'CA', 91202, 'FSD993321DES', '313 32 48510', '', 'contacto@angularjsui.org', '', 'Régimen Intermedio', 'ac', 'f');

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
  `productor_domicilio` varchar(240) NOT NULL,
  `productor_ciudad` varchar(220) NOT NULL,
  `nombre` varchar(240) NOT NULL,
  `rfc` varchar(13) NOT NULL,
  `domicilio` varchar(250) NOT NULL,
  `domicilio2` varchar(220) NOT NULL,
  `status` enum('p','pa','ca') NOT NULL DEFAULT 'pa' COMMENT 'p:pendiente, pa:pagada, ca:cancelada',
  PRIMARY KEY (`id_factura`),
  KEY `id_productor` (`id_productor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Volcado de datos para la tabla `productores_facturas`
--

INSERT INTO `productores_facturas` (`id_factura`, `id_productor`, `serie`, `folio`, `no_aprobacion`, `ano_aprobacion`, `fecha`, `importe_iva`, `retencion_iva`, `descuento`, `subtotal`, `total`, `total_letra`, `img_cbb`, `forma_pago`, `metodo_pago`, `metodo_pago_digitos`, `condicion_pago`, `plazo_credito`, `productor_domicilio`, `productor_ciudad`, `nombre`, `rfc`, `domicilio`, `domicilio2`, `status`) VALUES
(2, 2, '', 1, 12345, 2013, '2013-05-03 05:00:00', 0, 0, 0, 8308.73, 8308.73, 'OCHO MIL TRESCIENTOS OCHO PESOS 73/100 M.N.', '8774248f9e8d9a4e2afb277704a1ca15.png', 'Pago en una sola exhibición', 'efectivo', 'No identificado', 'co', 0, ' (AD), Col. ', ', MICHOACAN, C.P. 91202', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(3, 2, '', 2, 12345, 2013, '2013-05-03 05:00:00', 0, 0, 0, 800000, 800000, 'OCHOCIENTOS  MIL PESOS 00/100 M.N.', '8774248f9e8d9a4e2afb277704a1ca15.png', 'Pago en una sola exhibición', 'transferencia', 'No identificado', 'co', 0, ' (AD), Col. ', ', MICHOACAN, C.P. 91202', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'ca'),
(4, 1, '', 1, 123456, 2013, '2013-05-03 05:00:00', 0, 0, 0, 8356, 8356, 'OCHO MIL TRESCIENTOS CINCUENTA Y SEIS PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'efectivo', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(5, 1, '', 2, 123456, 2013, '2013-05-05 05:00:00', 0, 0, 0, 264, 264, 'DOSCIENTOS SESENTA Y CUATRO PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'efectivo', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(6, 1, '', 3, 123456, 2013, '2013-05-06 05:00:00', 0, 0, 0, 672, 672, 'SEISCIENTOS SETENTA Y DOS PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'efectivo', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(8, 1, '', 4, 123456, 2013, '2013-05-06 05:00:00', 0, 0, 0, 1464, 1464, 'MIL CUATROCIENTOS SESENTA Y CUATRO PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'efectivo', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(9, 1, '', 5, 123456, 2013, '2013-05-07 05:00:00', 0, 0, 0, 44, 44, 'CUARENTA Y CUATRO PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'tarjeta', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(10, 1, '', 6, 123456, 2013, '2013-05-07 05:00:00', 0, 0, 0, 704, 704, 'SETECIENTOS CUATRO PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'efectivo', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(11, 1, '', 7, 123456, 2013, '2013-05-07 05:00:00', 0, 0, 0, 46, 46, 'CUARENTA Y SEIS PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'cheque', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(12, 1, '', 8, 123456, 2013, '2013-05-07 05:00:00', 0, 0, 0, 396, 396, 'TRESCIENTOS NOVENTA Y SEIS PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'efectivo', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(13, 1, '', 9, 123456, 2013, '2013-05-08 05:00:00', 0, 0, 0, 1056, 1056, 'MIL CINCUENTA Y SEIS PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'cheque', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(14, 1, '', 10, 123456, 2013, '2013-05-11 05:00:00', 0, 0, 0, 14042, 14042, 'CATORCE MIL CUARENTA Y DOS PESOS 00/100 M.N.', '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Pago en una sola exhibición', 'efectivo', 'No identificado', 'co', 0, 'AV. 20 DE NOVIEMBRE 2, Col. Juan Jose Rios', 'Jalapa, Veraster, C.P. 0', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa'),
(15, 5, 'F', 1, 312312, 2013, '2013-05-15 05:00:00', 0, 0, 0, 2370, 2370, 'DOS MIL TRESCIENTOS SETENTA PESOS 00/100 M.N.', 'dfae9752f6001fc437104b108ec96d95.png', 'Pago en una sola exhibición', 'cheque', 'No identificado', 'co', 0, 'AV. DE LAGO S/N(AD), Col. Juan Jose Rios', 'TECOMAN, CA, C.P. 91202', 'Frutas y Legumbres de la costa sur de jalisco', 'FLC090210ED3', 'Barra de navidad #53 Col. Pelistermen', 'Barra, Jalisco. CP 31932', 'pa');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Volcado de datos para la tabla `productores_facturas_productos`
--

INSERT INTO `productores_facturas_productos` (`id_fac_prod`, `id_factura`, `descripcion`, `taza_iva`, `cantidad`, `precio_unitario`, `importe`, `importe_iva`, `total`, `descuento`, `retencion`, `unidad`) VALUES
(1, 2, 'Mango ataulfo', 0, 212, 34, 7208, 0, 7208, 0, 0, 'Kilos'),
(2, 2, 'Calando los de mas parametros ver si soporta varios renglones en las facturas por lo que le meto mas caracteres a esta caja de texto en el perstente', 0, 83.2, 13.23, 1100.73, 0, 1100.73, 0, 0, 'Cajas'),
(3, 3, 'dasd as dasd asd asd', 0, 1000, 800, 800000, 0, 800000, 0, 0, 'Unidad'),
(4, 4, 'dasd asd asda sd', 0, 322, 23, 7406, 0, 7406, 0, 0, 'Unidad'),
(5, 4, 'da sd asd asd', 0, 22, 23, 506, 0, 506, 0, 0, 'Unidad'),
(6, 4, 'dasdasd', 0, 12, 3, 36, 0, 36, 0, 0, 'Unidad'),
(7, 4, 'fsd fsdf sdf sdf sd fsdfsd f', 0, 12, 34, 408, 0, 408, 0, 0, 'Unidad'),
(8, 5, 'asdasd', 0, 12, 22, 264, 0, 264, 0, 0, 'Unidad'),
(9, 6, 'da sda sd asdasdas', 0, 32, 21, 672, 0, 672, 0, 0, 'Unidad'),
(11, 8, 'dasdas', 0, 122, 12, 1464, 0, 1464, 0, 0, 'Unidad'),
(12, 9, 'asd', 0, 22, 2, 44, 0, 44, 0, 0, 'Unidad'),
(13, 10, 'das', 0, 32, 22, 704, 0, 704, 0, 0, 'Unidad'),
(14, 11, 'dasd', 0, 2, 23, 46, 0, 46, 0, 0, 'Unidad'),
(15, 12, 'dasdas dasd', 0, 33, 12, 396, 0, 396, 0, 0, 'Unidad'),
(16, 13, 'fsdfsdf sdf sdfsdf', 0, 32, 33, 1056, 0, 1056, 0, 0, 'Unidad'),
(17, 14, 'asdasd', 0, 22, 23, 506, 0, 506, 0, 0, 'Unidad'),
(18, 14, 'dasdddasdasd asd asd asd asd', 0, 32, 423, 13536, 0, 13536, 0, 0, 'Unidad'),
(19, 15, 'asdasd', 0, 321, 2, 642, 0, 642, 0, 0, 'Unidad'),
(20, 15, 'dasdasdasd', 0, 32, 22, 704, 0, 704, 0, 0, 'Unidad'),
(21, 15, 'dasdasd', 0, 32, 32, 1024, 0, 1024, 0, 0, 'Unidad');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `productores_series_folios`
--

INSERT INTO `productores_series_folios` (`id_serie_folio`, `id_productor`, `serie`, `no_aprobacion`, `folio_inicio`, `folio_fin`, `imagen`, `leyenda`, `leyenda1`, `leyenda2`, `ano_aprobacion`) VALUES
(1, 2, '', 12345, 1, 10, '8774248f9e8d9a4e2afb277704a1ca15.png', 'Factura', 'La reproducción apócrifa de este comprobante constituye un delito en los términos de las disposiciones fiscales.', 'Esté comprobante tendrá una vigencia de dos años contados a partir de la fecha de aprobación de la asignación de folios, la cual es', '2013-05-01'),
(2, 1, '', 123456, 1, 100, '6a5d1984c5fad83df8fe5255ebe75a2a.png', 'Factura', 'La reproducción apócrifa de este comprobante constituye un delito en los términos de las disposiciones fiscales.', 'Esté comprobante tendrá una vigencia de dos años contados a partir de la fecha de aprobación de la asignación de folios, la cual es', '2013-05-01'),
(3, 5, 'F', 312312, 1, 20, 'dfae9752f6001fc437104b108ec96d95.png', 'Factura', 'La reproducción apócrifa de este comprobante constituye un delito en los términos de las disposiciones fiscales.', 'Esté comprobante tendrá una vigencia de dos años contados a partir de la fecha de aprobación de la asignación de folios, la cual es', '2013-05-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamientos`
--

CREATE TABLE IF NOT EXISTS `tratamientos` (
  `id_tratamiento` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(40) NOT NULL,
  `status` enum('ac','e') NOT NULL DEFAULT 'ac',
  PRIMARY KEY (`id_tratamiento`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `tratamientos`
--

INSERT INTO `tratamientos` (`id_tratamiento`, `nombre`, `status`) VALUES
(1, '75', 'ac'),
(2, '90', 'ac'),
(3, '110', 'ac');

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
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(1, 46),
(1, 47),
(1, 48),
(1, 49),
(1, 50),
(1, 51);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `variedades`
--

INSERT INTO `variedades` (`id_variedad`, `nombre`, `tipo_pago`, `status`) VALUES
(1, 'Ataulfo', 'c', 'ac'),
(2, 'Haden', 'k', 'ac'),
(3, '0', '', 'e');

-- --------------------------------------------------------

--
-- Estructura para la vista `bancos_movimientos_depositos`
--
DROP TABLE IF EXISTS `bancos_movimientos_depositos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bancos_movimientos_depositos` AS select `bancos_movimientos`.`id_movimiento` AS `id_movimiento`,`bancos_movimientos`.`id_banco` AS `id_banco`,`bancos_movimientos`.`id_cuenta` AS `id_cuenta`,`bancos_movimientos`.`id_fac_productor` AS `id_fac_productor`,`bancos_movimientos`.`fecha` AS `fecha`,`bancos_movimientos`.`concepto` AS `concepto`,`bancos_movimientos`.`monto` AS `monto`,`bancos_movimientos`.`tipo` AS `tipo`,`bancos_movimientos`.`metodo_pago` AS `metodo_pago`,`bancos_movimientos`.`anombre_de` AS `anombre_de`,`bancos_movimientos`.`moneda` AS `moneda`,`bancos_movimientos`.`abono_cuenta` AS `abono_cuenta` from `bancos_movimientos` where (`bancos_movimientos`.`tipo` = 'd');

-- --------------------------------------------------------

--
-- Estructura para la vista `bancos_movimientos_retiros`
--
DROP TABLE IF EXISTS `bancos_movimientos_retiros`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bancos_movimientos_retiros` AS select `bancos_movimientos`.`id_movimiento` AS `id_movimiento`,`bancos_movimientos`.`id_banco` AS `id_banco`,`bancos_movimientos`.`id_cuenta` AS `id_cuenta`,`bancos_movimientos`.`id_fac_productor` AS `id_fac_productor`,`bancos_movimientos`.`fecha` AS `fecha`,`bancos_movimientos`.`concepto` AS `concepto`,`bancos_movimientos`.`monto` AS `monto`,`bancos_movimientos`.`tipo` AS `tipo`,`bancos_movimientos`.`metodo_pago` AS `metodo_pago`,`bancos_movimientos`.`anombre_de` AS `anombre_de`,`bancos_movimientos`.`moneda` AS `moneda`,`bancos_movimientos`.`abono_cuenta` AS `abono_cuenta` from `bancos_movimientos` where (`bancos_movimientos`.`tipo` = 'r');

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
  ADD CONSTRAINT `bancos_movimientos_ibfk_2` FOREIGN KEY (`id_cuenta`) REFERENCES `bancos_cuentas` (`id_cuenta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bancos_movimientos_ibfk_3` FOREIGN KEY (`id_fac_productor`) REFERENCES `productores_facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `cajas_inventario_ibfk_2` FOREIGN KEY (`id_variedad`) REFERENCES `variedades` (`id_variedad`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cajas_inventario_ibfk_3` FOREIGN KEY (`id_caja`) REFERENCES `cajas_recibidas` (`id_caja`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `cajas_recibidas_abonos_ibfk_1` FOREIGN KEY (`id_productor`) REFERENCES `productores` (`id_productor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cajas_recibidas_abonos_ibfk_2` FOREIGN KEY (`id_caja`) REFERENCES `cajas_recibidas` (`id_caja`) ON DELETE CASCADE ON UPDATE CASCADE;

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
