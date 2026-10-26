-- =========================================================
-- Script de Migración para Módulo de Lotes y Caducidades
-- =========================================================

-- 1. Tabla de Lotes del Inventario
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
  CONSTRAINT `inventario_lotes_ibfk_1` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 2. Tabla Relacional de Lotes por Detalle de Venta
CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Venta` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalles_ventas_lotes_ibfk_1` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalles_ventas_lotes_ibfk_2` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 3. Tabla Relacional de Lotes por Detalle de Compra
CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
  `FK_Detalle_Compra` int(11) NOT NULL,
  `FK_Lote` int(11) NOT NULL,
  `Cantidad` double NOT NULL,
  PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  KEY `FK_Lote` (`FK_Lote`),
  CONSTRAINT `detalle_compras_lotes_ibfk_1` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_lotes_ibfk_2` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
