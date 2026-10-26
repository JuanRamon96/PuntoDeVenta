-- =====================================================================
-- SCRIPT DE MIGRACIÓN: MÓDULO DE LOTES Y CADUCIDADES
-- Aplica para todas las bases de datos activas de Punto de Venta
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. Base de datos: punto_venta_1
-- ---------------------------------------------------------------------
USE `punto_venta_1`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv1` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv1` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv1` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv1` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv1` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 2. Base de datos: punto_venta_7
-- ---------------------------------------------------------------------
USE `punto_venta_7`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv7` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv7` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv7` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv7` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv7` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 3. Base de datos: punto_venta_8
-- ---------------------------------------------------------------------
USE `punto_venta_8`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv8` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv8` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv8` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv8` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv8` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 4. Base de datos: punto_venta_9
-- ---------------------------------------------------------------------
USE `punto_venta_9`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv9` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv9` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv9` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv9` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv9` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 5. Base de datos: punto_venta_10
-- ---------------------------------------------------------------------
USE `punto_venta_10`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv10` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv10` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv10` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv10` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv10` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 6. Base de datos: punto_venta_12
-- ---------------------------------------------------------------------
USE `punto_venta_12`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv12` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv12` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv12` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv12` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv12` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 7. Base de datos: punto_venta_13
-- ---------------------------------------------------------------------
USE `punto_venta_13`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv13` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv13` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv13` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv13` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv13` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 8. Base de datos: punto_venta_22
-- ---------------------------------------------------------------------
USE `punto_venta_22`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv22` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv22` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv22` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv22` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv22` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 9. Base de datos: punto_venta_24
-- ---------------------------------------------------------------------
USE `punto_venta_24`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv24` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv24` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv24` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv24` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv24` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 10. Base de datos: punto_venta_25
-- ---------------------------------------------------------------------
USE `punto_venta_25`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv25` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv25` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv25` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv25` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv25` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 11. Base de datos: punto_venta_26
-- ---------------------------------------------------------------------
USE `punto_venta_26`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv26` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv26` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv26` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv26` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv26` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 12. Base de datos: punto_venta_27
-- ---------------------------------------------------------------------
USE `punto_venta_27`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv27` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv27` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv27` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv27` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv27` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 13. Base de datos: punto_venta_28
-- ---------------------------------------------------------------------
USE `punto_venta_28`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv28` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv28` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv28` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv28` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv28` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 14. Base de datos: punto_venta_29
-- ---------------------------------------------------------------------
USE `punto_venta_29`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv29` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv29` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv29` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv29` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv29` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 15. Base de datos: punto_venta_30
-- ---------------------------------------------------------------------
USE `punto_venta_30`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv30` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv30` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv30` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv30` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv30` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 16. Base de datos: punto_venta_31
-- ---------------------------------------------------------------------
USE `punto_venta_31`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv31` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv31` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv31` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv31` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv31` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 17. Base de datos: punto_venta_32
-- ---------------------------------------------------------------------
USE `punto_venta_32`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv32` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv32` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv32` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv32` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv32` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------
-- 18. Base de datos: punto_venta_33
-- ---------------------------------------------------------------------
USE `punto_venta_33`;

CREATE TABLE IF NOT EXISTS `inventario_lotes` (
  `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Inventario` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  PRIMARY KEY (`ID_Lote`),
  KEY `FK_Inventario` (`FK_Inventario`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `inventario_lotes_ibfk_1_pv33` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1_pv33` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2_pv33` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1_pv33` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2_pv33` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
