-- =====================================================================
-- SCRIPT DE MIGRACIÓN: TABLA detalles_orden_lotes
-- Aplica para todas las bases de datos de clientes
-- =====================================================================

-- Base de datos: punto_venta (Base plantilla)
USE `punto_venta`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_1
USE `punto_venta_1`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv1` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_7
USE `punto_venta_7`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv7` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_8
USE `punto_venta_8`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv8` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_9
USE `punto_venta_9`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv9` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_10
USE `punto_venta_10`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv10` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_12
USE `punto_venta_12`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv12` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_13
USE `punto_venta_13`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv13` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_22
USE `punto_venta_22`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv22` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_24
USE `punto_venta_24`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv24` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_25
USE `punto_venta_25`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv25` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_26
USE `punto_venta_26`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv26` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_27
USE `punto_venta_27`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv27` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_28
USE `punto_venta_28`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv28` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_29
USE `punto_venta_29`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv29` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_30
USE `punto_venta_30`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv30` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_31
USE `punto_venta_31`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv31` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_32
USE `punto_venta_32`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv32` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Base de datos: punto_venta_33
USE `punto_venta_33`;
CREATE TABLE IF NOT EXISTS `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Orden` int(11) NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  KEY `Fecha_Caducidad` (`Fecha_Caducidad`),
  CONSTRAINT `detalles_orden_lotes_ibfk_1_pv33` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
