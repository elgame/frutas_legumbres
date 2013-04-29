-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-04-2013 a las 23:03:06
-- Versión del servidor: 5.5.27
-- Versión de PHP: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `punto_venta`
--
CREATE DATABASE `punto_venta` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `punto_venta`;

DELIMITER $$
--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `valida_ultimo_nodo`(`id_fam` BIGINT) RETURNS tinyint(4)
    DETERMINISTIC
    COMMENT 'Valida si un id de familia es ultimo nodo o no'
BEGIN
DECLARE vconta BIGINT;

	SELECT COUNT(*) INTO vconta FROM productos_familias WHERE id_padre = id_fam AND status = 1;
	IF vconta = 0 THEN
		RETURN 1;
	END IF;
RETURN 0;
END$$

DELIMITER ;

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
('be496c80d00794f8e69f909d65dd09e8', '::1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31 AlexaToolba', 1365388949, 'a:6:{s:9:"user_data";s:0:"";s:2:"id";s:1:"1";s:7:"usuario";s:5:"admin";s:5:"email";s:17:"dasdasd@gmail.com";s:4:"tipo";s:5:"admin";s:7:"idunico";s:24:"l51621e8eba9163.05045807";}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_fiscal` varchar(120) NOT NULL,
  `rfc` varchar(13) NOT NULL,
  `calle` varchar(80) NOT NULL,
  `no_exterior` varchar(8) NOT NULL,
  `no_interior` varchar(8) NOT NULL,
  `colonia` varchar(80) NOT NULL,
  `municipio` varchar(60) NOT NULL,
  `estado` varchar(60) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `desceunto` double NOT NULL,
  `email` varchar(70) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `enviar_factura` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:enviar, 0:no enviar',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:activo, 0:eliminado',
  PRIMARY KEY (`id`)
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

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
(10, 'Productos Base', 0, 1, 'productos/', 'book', 0),
(11, 'Agregar', 10, 1, 'productos/agregar/', 'plus', 0),
(12, 'Modificar', 10, 0, 'productos/modificar/', 'edit', 0),
(13, 'Eliminar', 10, 0, 'productos/eliminar/', 'remove', 0),
(14, 'Activar', 10, 0, 'productos/activar/', 'ok', 0),
(15, 'Agregar Inventario', 10, 0, 'productos/agregar_inventario/', 'circle-arrow-up', 0),
(16, 'Familias', 0, 1, 'familias/', 'th-large', 0),
(17, 'Agregar', 16, 1, 'familias/agregar/', 'plus', 0),
(18, 'Modificar', 16, 0, 'familias/modificar/', 'edit', 0),
(19, 'Eliminar', 16, 0, 'familias/eliminar/', 'remove', 0),
(20, 'Activar', 16, 0, 'familias/activar/', 'ok', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_base`
--

CREATE TABLE IF NOT EXISTS `productos_base` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `stock_min` double NOT NULL DEFAULT '0',
  `precio_compra` double NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:activo, 0:eliminado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Volcado de datos para la tabla `productos_base`
--

INSERT INTO `productos_base` (`id`, `nombre`, `stock_min`, `precio_compra`, `status`) VALUES
(1, 'Leche', 20, 5, 1),
(2, 'Cucharas', 20, 0.2, 1),
(3, 'Popote', 10, 0.04, 1),
(4, 'Vaso', 10, 0.5, 1),
(5, 'Tapa', 10, 0.08, 1),
(6, 'Azucar', 50, 1, 1),
(7, 'Chocolate', 20, 0.5, 1),
(8, 'Pan molido', 4, 5, 1),
(9, 'Pasas', 15, 2, 1),
(10, 'Cacahuate', 30, 0.5, 1),
(11, 'Pepino', 20, 0.5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_base_entradas`
--

CREATE TABLE IF NOT EXISTS `productos_base_entradas` (
  `base_id` bigint(20) unsigned NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cantidad` double NOT NULL,
  `precio_compra` double NOT NULL,
  `importe` double NOT NULL,
  PRIMARY KEY (`base_id`,`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `productos_base_entradas`
--

INSERT INTO `productos_base_entradas` (`base_id`, `fecha`, `cantidad`, `precio_compra`, `importe`) VALUES
(1, '2013-04-03 03:24:03', 20, 3, 60),
(1, '2013-04-03 03:35:57', -10, 0, 0),
(2, '2013-04-03 03:52:14', 20, 0.2, 4);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `productos_base_entradas_exist`
--
CREATE TABLE IF NOT EXISTS `productos_base_entradas_exist` (
`base_id` bigint(20) unsigned
,`entradas` double
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `productos_base_existencias`
--
CREATE TABLE IF NOT EXISTS `productos_base_existencias` (
`id` bigint(20) unsigned
,`entradas` double
,`salidas` double
,`existencia` double
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_base_familia`
--

CREATE TABLE IF NOT EXISTS `productos_base_familia` (
  `familia_id` bigint(20) unsigned NOT NULL,
  `base_id` bigint(20) unsigned NOT NULL,
  `cantidad` double NOT NULL,
  PRIMARY KEY (`familia_id`,`base_id`),
  KEY `base_id` (`base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `productos_base_familia`
--

INSERT INTO `productos_base_familia` (`familia_id`, `base_id`, `cantidad`) VALUES
(7, 3, 1),
(7, 4, 1),
(7, 5, 1),
(7, 6, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_base_salidas`
--

CREATE TABLE IF NOT EXISTS `productos_base_salidas` (
  `ticket_id` bigint(20) unsigned NOT NULL,
  `familia_id` bigint(20) unsigned NOT NULL,
  `base_id` bigint(20) unsigned NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cantidad` double NOT NULL,
  `precio_compra` double NOT NULL,
  `importe` double NOT NULL,
  PRIMARY KEY (`ticket_id`,`familia_id`,`base_id`),
  KEY `familia_id` (`familia_id`),
  KEY `base_id` (`base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `productos_base_salidas_exist`
--
CREATE TABLE IF NOT EXISTS `productos_base_salidas_exist` (
`base_id` bigint(20) unsigned
,`salidas` double
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_familias`
--

CREATE TABLE IF NOT EXISTS `productos_familias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_padre` bigint(20) DEFAULT NULL,
  `nombre` varchar(80) NOT NULL,
  `precio_venta` double NOT NULL DEFAULT '0',
  `imagen` varchar(100) DEFAULT NULL,
  `color1` varchar(7) DEFAULT NULL,
  `color2` varchar(7) DEFAULT NULL,
  `ultimo_nodo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:ultimo, 0:no es ultimo',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:activo, 0:eliminado',
  PRIMARY KEY (`id`),
  KEY `id_padre` (`id_padre`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `productos_familias`
--

INSERT INTO `productos_familias` (`id`, `id_padre`, `nombre`, `precio_venta`, `imagen`, `color1`, `color2`, `ultimo_nodo`, `status`) VALUES
(1, NULL, 'Padre', 0, NULL, NULL, NULL, 0, 1),
(5, 1, 'Cafe capuchino', 25, 'af493bb94c89f885a7ac491d2b38fb09.jpg', '#fffcb8', '#fffd12', 1, 1),
(6, 1, 'Bebidas', 0, '', '#7efa29', '#7efa4e', 0, 1),
(7, 6, 'Cafe negro', 20, '6e53f8ff0a65a18a5b43a4b9f4142dbb.jpg', '#79ff29', '#79ff83', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `cliente_id` bigint(20) unsigned DEFAULT NULL,
  `folio` bigint(20) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` double NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:vendido, 0:cancelado',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `cliente_id` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets_detalle`
--

CREATE TABLE IF NOT EXISTS `tickets_detalle` (
  `ticket_id` bigint(20) unsigned NOT NULL,
  `familia_id` bigint(20) unsigned NOT NULL,
  `cantidad` double NOT NULL,
  `precio_venta` double NOT NULL,
  `importe` double NOT NULL,
  PRIMARY KEY (`ticket_id`,`familia_id`),
  KEY `familia_id` (`familia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `password`, `email`, `tipo`, `status`) VALUES
(1, 'admin', 'admin', '12345', 'dasdasd@gmail.com', 'admin', 1),
(2, 'asd', 'dd', '12345', '', 'admin', 0);

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
(2, 1),
(1, 2),
(2, 2),
(1, 3),
(2, 3),
(1, 4),
(2, 4),
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
(1, 20);

-- --------------------------------------------------------

--
-- Estructura para la vista `productos_base_entradas_exist`
--
DROP TABLE IF EXISTS `productos_base_entradas_exist`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `productos_base_entradas_exist` AS select `productos_base_entradas`.`base_id` AS `base_id`,sum(`productos_base_entradas`.`cantidad`) AS `entradas` from `productos_base_entradas` group by `productos_base_entradas`.`base_id`;

-- --------------------------------------------------------

--
-- Estructura para la vista `productos_base_existencias`
--
DROP TABLE IF EXISTS `productos_base_existencias`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `productos_base_existencias` AS select `pb`.`id` AS `id`,ifnull(sum(`e`.`entradas`),0) AS `entradas`,ifnull(sum(`s`.`salidas`),0) AS `salidas`,(ifnull(sum(`e`.`entradas`),0) - ifnull(sum(`s`.`salidas`),0)) AS `existencia` from ((`productos_base` `pb` left join `productos_base_entradas_exist` `e` on((`e`.`base_id` = `pb`.`id`))) left join `productos_base_salidas_exist` `s` on((`s`.`base_id` = `pb`.`id`))) group by `pb`.`id`;

-- --------------------------------------------------------

--
-- Estructura para la vista `productos_base_salidas_exist`
--
DROP TABLE IF EXISTS `productos_base_salidas_exist`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `productos_base_salidas_exist` AS select `productos_base_salidas`.`base_id` AS `base_id`,sum(`productos_base_salidas`.`cantidad`) AS `salidas` from `productos_base_salidas` group by `productos_base_salidas`.`base_id`;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `productos_base_entradas`
--
ALTER TABLE `productos_base_entradas`
  ADD CONSTRAINT `productos_base_entradas_ibfk_1` FOREIGN KEY (`base_id`) REFERENCES `productos_base` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_base_familia`
--
ALTER TABLE `productos_base_familia`
  ADD CONSTRAINT `productos_base_familia_ibfk_1` FOREIGN KEY (`base_id`) REFERENCES `productos_base` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_base_familia_ibfk_2` FOREIGN KEY (`familia_id`) REFERENCES `productos_familias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_base_salidas`
--
ALTER TABLE `productos_base_salidas`
  ADD CONSTRAINT `productos_base_salidas_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_base_salidas_ibfk_2` FOREIGN KEY (`familia_id`) REFERENCES `productos_familias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_base_salidas_ibfk_3` FOREIGN KEY (`base_id`) REFERENCES `productos_base` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tickets_detalle`
--
ALTER TABLE `tickets_detalle`
  ADD CONSTRAINT `tickets_detalle_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tickets_detalle_ibfk_2` FOREIGN KEY (`familia_id`) REFERENCES `productos_familias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios_privilegios`
--
ALTER TABLE `usuarios_privilegios`
  ADD CONSTRAINT `usuarios_privilegios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_privilegios_ibfk_2` FOREIGN KEY (`privilegio_id`) REFERENCES `privilegios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
