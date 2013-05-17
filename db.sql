-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-05-2013 a las 20:00:09
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `bancos_cuentas`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `bancos_movimientos`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `cajas_inventario`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `cajas_recibidas`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `cajas_recibidas_abonos`
--


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `duenios_huertas`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `productores`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `productores_facturas`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `productores_facturas_productos`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcado de datos para la tabla `productores_series_folios`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `variedades`
--

INSERT INTO `variedades` (`id_variedad`, `nombre`, `tipo_pago`, `status`) VALUES
(1, 'Ataulfo', 'c', 'ac'),
(2, 'Haden', 'k', 'ac');

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
