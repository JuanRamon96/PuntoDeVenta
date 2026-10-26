-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 15-09-2026 a las 11:01:54
-- Versión del servidor: 8.0.38
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `punto_venta_13`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `ID_Caja` int NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Detalles` text NOT NULL,
  `Estado` tinyint(1) NOT NULL COMMENT 'Cerrada(0) Abierta(1)',
  `FK_Usuario` int NOT NULL,
  `FK_Sucursal` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`ID_Caja`, `Nombre`, `Detalles`, `Estado`, `FK_Usuario`, `FK_Sucursal`) VALUES
(1, 'Caja 01', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificaciones`
--

CREATE TABLE `clasificaciones` (
  `ID_Clasificacion` int NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Descripcion` text NOT NULL,
  `Foto` text NOT NULL,
  `Fecha_Registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `ID_Cliente` int NOT NULL,
  `FK_Sucursal` int NOT NULL,
  `Tipo` varchar(60) NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Primer_Apellido` tinytext NOT NULL,
  `Segundo_Apellido` tinytext NOT NULL,
  `Sexo` varchar(60) NOT NULL,
  `RFC` tinytext NOT NULL,
  `Regimen_CFDI` tinytext NOT NULL,
  `Razon_Social` tinytext NOT NULL,
  `Calle` tinytext NOT NULL,
  `No_Exterior` varchar(30) NOT NULL,
  `No_Interior` varchar(30) NOT NULL,
  `Colonia` tinytext NOT NULL,
  `CP` varchar(30) NOT NULL,
  `Ciudad` tinytext NOT NULL,
  `Estado` tinytext NOT NULL,
  `Pais` tinytext NOT NULL,
  `Detalles` text NOT NULL,
  `Latitud` tinytext NOT NULL,
  `Longitud` tinytext NOT NULL,
  `Telefono` tinytext NOT NULL,
  `Segundo_Telefono` tinytext NOT NULL,
  `Email` tinytext NOT NULL,
  `Contacto` tinytext NOT NULL,
  `Puesto_Contacto` tinytext NOT NULL,
  `Telefono_Contacto` tinytext NOT NULL,
  `Email_Contacto` tinytext NOT NULL,
  `Fecha_Registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `ID_Compra` int NOT NULL,
  `FK_Usuario` int NOT NULL,
  `FK_Proveedor` int NOT NULL,
  `FK_Orden` int NOT NULL,
  `Tipo_Compra` varchar(50) NOT NULL,
  `Estatus` varchar(50) NOT NULL,
  `Descuento` double NOT NULL,
  `Total` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `Fecha_Credito` datetime NOT NULL,
  `FK_Sucursal` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `compras`
--
DELIMITER $$
CREATE TRIGGER `completar_orden` AFTER INSERT ON `compras` FOR EACH ROW BEGIN
			IF New.FK_Orden != 0 THEN
				UPDATE ordenes_compra SET Estatus = 'Completada' WHERE ID_Orden_Compra = New.FK_Orden;
			END IF;
		END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_pagos`
--

CREATE TABLE `compras_pagos` (
  `ID_Pago` int NOT NULL,
  `FK_Compra` int NOT NULL,
  `Concepto` text NOT NULL,
  `Monto` double NOT NULL,
  `Tipo_Pago` varchar(100) NOT NULL,
  `Detalles` text NOT NULL,
  `FK_Usuario` int NOT NULL,
  `FK_Detalle_Caja` int NOT NULL,
  `Archivo` text NOT NULL,
  `Fecha_Registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `compras_pagos`
--
DELIMITER $$
CREATE TRIGGER `actualizar_compra` AFTER INSERT ON `compras_pagos` FOR EACH ROW BEGIN
			IF (SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = New.FK_Compra) >= (SELECT Total FROM compras WHERE ID_Compra = New.FK_Compra) THEN
				UPDATE compras SET Estatus = 'Completada' WHERE ID_Compra = New.FK_Compra;
			END IF;
		END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `estatus_compra` AFTER DELETE ON `compras_pagos` FOR EACH ROW BEGIN
			IF IFNULL((SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = Old.FK_Compra), 0) < (SELECT Total FROM compras WHERE ID_Compra = Old.FK_Compra) THEN
				UPDATE compras SET Estatus = 'Pendiente' WHERE ID_Compra = Old.FK_Compra;
			END IF;
		END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `ID_Configuracion` int NOT NULL,
  `Nombre` varchar(60) NOT NULL,
  `Domicilio` tinytext NOT NULL,
  `Telefono` varchar(60) NOT NULL,
  `Foto` text NOT NULL,
  `Porcentaje_Suma` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`ID_Configuracion`, `Nombre`, `Domicilio`, `Telefono`, `Foto`, `Porcentaje_Suma`) VALUES
(1, 'Punto de Venta', '', '', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_facturacion`
--

CREATE TABLE `configuracion_facturacion` (
  `ID_Configuracion` int NOT NULL,
  `RFC` varchar(60) NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Regimen` tinytext NOT NULL,
  `Domicilio` text NOT NULL,
  `CP` varchar(30) NOT NULL,
  `Certificado` text NOT NULL,
  `Key_Cer` text NOT NULL,
  `Contrasena` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `configuracion_facturacion`
--

INSERT INTO `configuracion_facturacion` (`ID_Configuracion`, `RFC`, `Nombre`, `Regimen`, `Domicilio`, `CP`, `Certificado`, `Key_Cer`, `Contrasena`) VALUES
(1, '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_caja`
--

CREATE TABLE `detalles_caja` (
  `ID_Detalle_Caja` int NOT NULL,
  `FK_Caja` int NOT NULL,
  `Fecha_Abrir` datetime NOT NULL,
  `Monto_Abrir` double NOT NULL,
  `FK_Usuario_Abrir` int NOT NULL,
  `Fecha_Cierre` datetime NOT NULL,
  `Monto_Cierre` double NOT NULL,
  `FK_Usuario_Cierre` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_facturas`
--

CREATE TABLE `detalles_facturas` (
  `ID_Detalle_Factura` int NOT NULL,
  `FK_Factura` int NOT NULL,
  `Producto` tinytext NOT NULL,
  `Codigo_Producto` tinytext NOT NULL,
  `Codigo_Unidad` tinytext NOT NULL,
  `Precio_Unitario` varchar(11) NOT NULL,
  `Cantidad` varchar(11) NOT NULL,
  `Subtotal` varchar(11) NOT NULL,
  `Descuento` varchar(11) NOT NULL,
  `Total` varchar(11) NOT NULL,
  `Impuesto` varchar(11) NOT NULL,
  `Importe` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_inventario`
--

CREATE TABLE `detalles_inventario` (
  `ID_Detalle_Inventario` int NOT NULL,
  `FK_Inventario` int NOT NULL,
  `FK_Sucursal` int NOT NULL,
  `Cantidad` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_orden`
--

CREATE TABLE `detalles_orden` (
  `ID_Detalle_Orden` int NOT NULL,
  `FK_Orden` int NOT NULL,
  `FK_Producto` int NOT NULL,
  `Descripcion` tinytext NOT NULL,
  `Costo` double NOT NULL,
  `Cantidad` double NOT NULL,
  `Subtotal` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_orden_lotes`
--

CREATE TABLE `detalles_orden_lotes` (
  `ID_Detalle_Orden_Lote` int NOT NULL,
  `FK_Detalle_Orden` int NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_ventas`
--

CREATE TABLE `detalles_ventas` (
  `ID_Detalle_Venta` int NOT NULL,
  `FK_Venta` int NOT NULL,
  `FK_Producto` int NOT NULL,
  `Descripcion` tinytext NOT NULL,
  `Precio` double NOT NULL,
  `Costo` double NOT NULL,
  `Cantidad` double NOT NULL,
  `Descuento` double NOT NULL,
  `Total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `detalles_ventas`
--
DELIMITER $$
CREATE TRIGGER `devolverInve` BEFORE DELETE ON `detalles_ventas` FOR EACH ROW BEGIN
			DECLARE v_Cantidad_Devuelta DECIMAL(10,2);

			SELECT IFNULL(SUM(Cantidad), 0) INTO v_Cantidad_Devuelta
			FROM devoluciones
			WHERE FK_Detalle_Venta = OLD.ID_Detalle_Venta;

			UPDATE inventario 
			SET Cantidad = Cantidad + (OLD.Cantidad - v_Cantidad_Devuelta) 
			WHERE FK_Producto = OLD.FK_Producto;
				
			/*UPDATE detalles_inventario SET Cantidad = Cantidad + Old.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = Old.FK_Producto) AND FK_Sucursal = (SELECT FK_Sucursal FROM cajas WHERE ID_Caja = (SELECT FK_Caja FROM detalles_caja WHERE ID_Detalle_Caja = (SELECT FK_Detalles_Caja FROM ventas WHERE ID_Venta = Old.FK_Venta)));*/
		END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `rebajaInven` AFTER INSERT ON `detalles_ventas` FOR EACH ROW BEGIN 
			UPDATE inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Producto = New.FK_Producto; 
					
			UPDATE detalles_inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = New.FK_Producto) AND FK_Sucursal = (SELECT FK_Sucursal FROM cajas WHERE ID_Caja = (SELECT FK_Caja FROM detalles_caja WHERE ID_Detalle_Caja = (SELECT FK_Detalles_Caja FROM ventas WHERE ID_Venta = New.FK_Venta)));
		END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_ventas_lotes`
--

CREATE TABLE `detalles_ventas_lotes` (
  `ID_Detalle_Venta_Lote` int NOT NULL,
  `FK_Detalle_Venta` int NOT NULL,
  `FK_Lote` int NOT NULL,
  `Cantidad` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE `detalle_compras` (
  `ID_Detalle_Compra` int NOT NULL,
  `FK_Compra` int NOT NULL,
  `FK_Producto` int NOT NULL,
  `Descripcion` tinytext NOT NULL,
  `Costo` double NOT NULL,
  `Cantidad` double NOT NULL,
  `Subtotal` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `detalle_compras`
--
DELIMITER $$
CREATE TRIGGER `actualizar_inventario` AFTER INSERT ON `detalle_compras` FOR EACH ROW BEGIN 
			UPDATE inventario SET Cantidad = Cantidad + New.Cantidad WHERE FK_Producto = New.FK_Producto;
				
			UPDATE productos SET Costo = New.Costo WHERE ID_Producto = New.FK_Producto;
				
			IF (SELECT Costo_Promedio FROM productos WHERE ID_Producto = New.FK_Producto) > 0 THEN
			
				UPDATE productos SET Costo_Promedio = ((Costo_Promedio * IFNULL((SELECT Cantidad FROM inventario WHERE FK_Producto = New.FK_Producto), 0)) + (New.Costo * New.Cantidad)) / (IFNULL((SELECT Cantidad FROM inventario WHERE FK_Producto = New.FK_Producto), 0) + New.Cantidad) WHERE ID_Producto = New.FK_Producto;
					
			ELSE
				
				UPDATE productos SET Costo_Promedio = New.Costo WHERE ID_Producto = New.FK_Producto;
					
			END IF;
				
			IF (SELECT FK_Sucursal FROM compras WHERE ID_Compra = NEW.FK_Compra) <> 0 THEN
				
				UPDATE detalles_inventario
				SET Cantidad = Cantidad + NEW.Cantidad
				WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = New.FK_Producto)
				AND FK_Sucursal = (SELECT FK_Sucursal FROM compras WHERE ID_Compra = NEW.FK_Compra);
					
			END IF;
		END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras_lotes`
--

CREATE TABLE `detalle_compras_lotes` (
  `ID_Detalle_Compra_Lote` int NOT NULL,
  `FK_Detalle_Compra` int NOT NULL,
  `FK_Lote` int NOT NULL,
  `Cantidad` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones`
--

CREATE TABLE `devoluciones` (
  `ID_Devolucion` int NOT NULL,
  `FK_Detalle_Venta` int NOT NULL,
  `Cantidad` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `devoluciones`
--
DELIMITER $$
CREATE TRIGGER `trg_devoluciones_after_delete` AFTER DELETE ON `devoluciones` FOR EACH ROW BEGIN
				DECLARE v_FK_Producto INT;
				DECLARE v_FK_Venta INT;
				DECLARE v_Cantidad_Original DECIMAL(10,2);
				DECLARE v_Total_Detalle DECIMAL(10,2);
				DECLARE v_Monto_Unitario DECIMAL(10,2);
				DECLARE v_Monto_Devuelto DECIMAL(10,2);

				-- Obtener datos del detalle de venta original
				SELECT FK_Producto, FK_Venta, Cantidad, Total
				INTO v_FK_Producto, v_FK_Venta, v_Cantidad_Original, v_Total_Detalle
				FROM detalles_ventas
				WHERE ID_Detalle_Venta = OLD.FK_Detalle_Venta;

				IF v_Cantidad_Original > 0 THEN
					SET v_Monto_Unitario = v_Total_Detalle / v_Cantidad_Original;
					SET v_Monto_Devuelto = v_Monto_Unitario * OLD.Cantidad;

					-- Restar del inventario lo que se había regresado
					UPDATE inventario
					SET Cantidad = Cantidad - OLD.Cantidad
					WHERE FK_Producto = v_FK_Producto;

					-- Regresar el monto a la venta (subir Total, bajar Cambio)
					UPDATE ventas
					SET Total = Total + v_Monto_Devuelto,
						Cambio = Cambio - v_Monto_Devuelto
					WHERE ID_Venta = v_FK_Venta;
				END IF;
			END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_devoluciones_after_insert` AFTER INSERT ON `devoluciones` FOR EACH ROW BEGIN
				DECLARE v_FK_Producto INT;
				DECLARE v_FK_Venta INT;
				DECLARE v_Cantidad_Original DECIMAL(10,2);
				DECLARE v_Total_Detalle DECIMAL(10,2);
				DECLARE v_Monto_Unitario DECIMAL(10,2);
				DECLARE v_Monto_Devuelto DECIMAL(10,2);

				-- Obtener datos del detalle de venta original
				SELECT FK_Producto, FK_Venta, Cantidad, Total
				INTO v_FK_Producto, v_FK_Venta, v_Cantidad_Original, v_Total_Detalle
				FROM detalles_ventas
				WHERE ID_Detalle_Venta = NEW.FK_Detalle_Venta;

				IF v_Cantidad_Original > 0 THEN
					SET v_Monto_Unitario = v_Total_Detalle / v_Cantidad_Original;
					SET v_Monto_Devuelto = v_Monto_Unitario * NEW.Cantidad;

					-- Aumentar inventario
					UPDATE inventario
					SET Cantidad = Cantidad + NEW.Cantidad
					WHERE FK_Producto = v_FK_Producto;

					-- Restar el monto devuelto del total de la venta
					-- y sumarlo al cambio para que no quede saldo a favor
					UPDATE ventas
					SET Total = Total - v_Monto_Devuelto,
						Cambio = Cambio + v_Monto_Devuelto
					WHERE ID_Venta = v_FK_Venta;
				END IF;
			END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones_cliente`
--

CREATE TABLE `direcciones_cliente` (
  `ID_Direccion` int NOT NULL,
  `FK_Cliente` int NOT NULL,
  `Calle` tinytext NOT NULL,
  `No_Exterior` varchar(11) NOT NULL,
  `No_Interior` varchar(11) NOT NULL,
  `CP` varchar(11) NOT NULL,
  `Colonia` tinytext NOT NULL,
  `Ciudad` tinytext NOT NULL,
  `Estado` tinytext NOT NULL,
  `Pais` tinytext NOT NULL,
  `Detalles` text NOT NULL,
  `Latitud` tinytext NOT NULL,
  `Longitud` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docs_relacionados`
--

CREATE TABLE `docs_relacionados` (
  `ID_Documento` int NOT NULL,
  `FK_Factura` int NOT NULL,
  `UUID` text NOT NULL,
  `Parcialidad` int NOT NULL,
  `Saldo_Anterior` varchar(11) NOT NULL,
  `Importe_Pagado` varchar(11) NOT NULL,
  `Saldo_Insoluto` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `ID_Factura` int NOT NULL,
  `Global` tinyint(1) NOT NULL,
  `Folio_Fiscal` text NOT NULL,
  `Nombre_Emisor` tinytext NOT NULL,
  `RFC_Emisor` tinytext NOT NULL,
  `CP_Emisor` varchar(30) NOT NULL,
  `Regimen_Emisor` varchar(60) NOT NULL,
  `General` tinyint(1) NOT NULL,
  `Nombre_Receptor` tinytext NOT NULL,
  `RFC_Receptor` tinytext NOT NULL,
  `CP_Receptor` varchar(30) NOT NULL,
  `Regimen_Receptor` tinytext NOT NULL,
  `Version_CFDI` varchar(30) NOT NULL,
  `Metodo_Pago` tinytext NOT NULL,
  `Forma_Pago` tinytext NOT NULL,
  `Uso_CFDI` tinytext NOT NULL,
  `Tipo_Comprobante` varchar(60) NOT NULL,
  `Moneda` varchar(30) NOT NULL,
  `Periodicidad` varchar(60) NOT NULL,
  `Mes` varchar(60) NOT NULL,
  `Ano` varchar(30) NOT NULL,
  `Subtotal` double NOT NULL,
  `Descuento` double NOT NULL,
  `Impuesto` varchar(11) NOT NULL,
  `Total` varchar(11) NOT NULL,
  `Estatus` varchar(30) NOT NULL,
  `Fecha_Emision` datetime NOT NULL,
  `Fecha_Timbrado` datetime NOT NULL,
  `PDF` text NOT NULL,
  `XML` text NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `FK_Venta` int NOT NULL,
  `FK_Cliente` int NOT NULL,
  `Email` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `ID_Gasto` int NOT NULL,
  `Monto` double NOT NULL,
  `Descripcion` text NOT NULL,
  `Comprobante` text NOT NULL,
  `Fecha_Gasto` date NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `FK_Usuario` int NOT NULL,
  `FK_Sucursal` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_caja`
--

CREATE TABLE `historial_caja` (
  `ID_Historial` int NOT NULL,
  `FK_Detalle_Caja` int NOT NULL,
  `FK_Usuario` int NOT NULL,
  `Fecha_Uso` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos`
--

CREATE TABLE `impuestos` (
  `ID_Impuesto` int NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Porcentaje` double NOT NULL,
  `Clave_CFDI` varchar(60) NOT NULL,
  `Tipo_Factor` varchar(60) NOT NULL,
  `Clase` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `impuestos`
--

INSERT INTO `impuestos` (`ID_Impuesto`, `Nombre`, `Porcentaje`, `Clave_CFDI`, `Tipo_Factor`, `Clase`) VALUES
(1, 'IVA', 16, '002', 'Tasa', 'Trasladado'),
(2, 'ISR', 2, '001', 'Tasa', 'Retenido'),
(3, 'IEPS', 10, '003', 'Cuota', 'Trasladado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos_docs`
--

CREATE TABLE `impuestos_docs` (
  `ID_Impuesto_Docs` int NOT NULL,
  `FK_Detalle_Docs` int NOT NULL,
  `Nombre` varchar(60) NOT NULL,
  `Clave` varchar(30) NOT NULL,
  `Valor` varchar(11) NOT NULL,
  `Clase` varchar(30) NOT NULL,
  `Factor` varchar(30) NOT NULL,
  `Importe` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos_factura`
--

CREATE TABLE `impuestos_factura` (
  `ID_Impuesto_Factura` int NOT NULL,
  `FK_Detalle_Factura` int NOT NULL,
  `Nombre` varchar(60) NOT NULL,
  `Clave` varchar(30) NOT NULL,
  `Valor` varchar(11) NOT NULL,
  `Clase` varchar(30) NOT NULL,
  `Factor` varchar(30) NOT NULL,
  `Importe` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos_productos`
--

CREATE TABLE `impuestos_productos` (
  `ID_Impuesto_Producto` int NOT NULL,
  `FK_Producto` int NOT NULL,
  `FK_Impuesto` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos_ventas`
--

CREATE TABLE `impuestos_ventas` (
  `ID_Impuesto_Venta` int NOT NULL,
  `FK_Detalle_Venta` int NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Porcentaje` double NOT NULL,
  `Clave_CFDI` tinytext NOT NULL,
  `Tipo_Factor` tinytext NOT NULL,
  `Clase` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `ID_Inventario` int NOT NULL,
  `FK_Producto` int NOT NULL,
  `Cantidad` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_lotes`
--

CREATE TABLE `inventario_lotes` (
  `ID_Lote` int NOT NULL,
  `FK_Inventario` int NOT NULL,
  `Nombre_Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad` double NOT NULL,
  `Fecha_Registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `merma`
--

CREATE TABLE `merma` (
  `ID_Merma` int NOT NULL,
  `FK_Producto` int NOT NULL,
  `Descripcion` text NOT NULL,
  `Cantidad` double NOT NULL,
  `Costo` double NOT NULL,
  `Total` double NOT NULL,
  `Fecha_Merma` date NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `Foto` text NOT NULL,
  `FK_Sucursal` int NOT NULL,
  `FK_Usuario` int NOT NULL,
  `Afecto_Inventario` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `merma`
--
DELIMITER $$
CREATE TRIGGER `devolver_inve_de_merma` AFTER DELETE ON `merma` FOR EACH ROW BEGIN
			IF OLD.Afecto_Inventario = 'Si' THEN
				UPDATE inventario SET Cantidad = Cantidad + OLD.Cantidad WHERE FK_Producto = OLD.FK_Producto;
					
				UPDATE detalles_inventario SET Cantidad = Cantidad + OLD.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = OLD.FK_Producto) AND FK_Sucursal = OLD.FK_sucursal;
			END IF;
		END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `recarcular_inve_up_merma` AFTER UPDATE ON `merma` FOR EACH ROW BEGIN
			IF New.Afecto_Inventario = 'Si' THEN
				IF OLD.Afecto_Inventario = 'Si' THEN
					
					UPDATE inventario SET Cantidad = Cantidad + OLD.Cantidad - New.Cantidad WHERE FK_Producto = New.FK_Producto;
						
				ELSE
					
					UPDATE inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Producto = New.FK_Producto;
						
				END IF;
					
				IF OLD.Afecto_Inventario = 'Si' THEN
					
					UPDATE detalles_inventario SET Cantidad = Cantidad + OLD.Cantidad - New.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = New.FK_Producto) AND FK_Sucursal = New.FK_sucursal;
						
				ELSE
					
					UPDATE detalles_inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = New.FK_Producto) AND FK_Sucursal = New.FK_sucursal;
						
				END IF;
			END IF;
		END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restar_inve_in_merma` AFTER INSERT ON `merma` FOR EACH ROW BEGIN
			IF New.Afecto_Inventario = 'Si' THEN
				UPDATE inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Producto = New.FK_Producto;
					
				UPDATE detalles_inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = New.FK_Producto) AND FK_Sucursal = New.FK_sucursal;
			END IF;
		END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `ID_Movimiento` int NOT NULL,
  `Descripcion` longtext NOT NULL,
  `IP` varchar(30) NOT NULL,
  `Pais` varchar(60) NOT NULL,
  `Estado` varchar(60) NOT NULL,
  `Navegador` varchar(100) NOT NULL,
  `SO` varchar(60) NOT NULL,
  `Fecha` datetime NOT NULL,
  `Access` longtext NOT NULL,
  `FK_Usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`ID_Movimiento`, `Descripcion`, `IP`, `Pais`, `Estado`, `Navegador`, `SO`, `Fecha`, `Access`, `FK_Usuario`) VALUES
(1, 'LOGIN almarazjorgeluis@gmail.com', '', '', '', '', '', '2026-09-15 09:09:53', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `ID_Movimiento` int NOT NULL,
  `Tipo` varchar(60) NOT NULL,
  `Monto` double NOT NULL,
  `Descripcion` text NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `FK_Detalle_Caja` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_compra`
--

CREATE TABLE `ordenes_compra` (
  `ID_Orden_Compra` int NOT NULL,
  `FK_Usuario` int NOT NULL,
  `FK_Proveedor` int NOT NULL,
  `Descuento` double NOT NULL,
  `Total` double NOT NULL,
  `Estatus` varchar(50) NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `FK_Sucursal` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `ID_Producto` int NOT NULL,
  `Codigo` varchar(60) NOT NULL,
  `Descripcion` tinytext NOT NULL,
  `Clase` varchar(30) NOT NULL COMMENT 'Pieza o Granel',
  `Costo` double NOT NULL COMMENT 'General',
  `Costo_Promedio` double NOT NULL,
  `Precio` double NOT NULL COMMENT 'General',
  `Precio_Mayoreo` double NOT NULL COMMENT 'General',
  `Stock_Minimo` double NOT NULL,
  `Stock_Maximo` double NOT NULL,
  `FK_Clasificacion` int NOT NULL,
  `Foto` tinytext NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `Clave_ProdServ_CFDI` tinytext NOT NULL,
  `Descripcion_Clave_CDFI` tinytext NOT NULL,
  `Clave_Unidad_CFDI` tinytext NOT NULL,
  `Nombre_Unidad_CFDI` tinytext NOT NULL,
  `Simbolo_CFDI` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `productos`
--
DELIMITER $$
CREATE TRIGGER `agregar_inventario` AFTER INSERT ON `productos` FOR EACH ROW BEGIN
			INSERT INTO inventario SET FK_Producto = New.ID_Producto, Cantidad = 0;
		END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `ID_Proveedor` int NOT NULL,
  `Razon_Social` tinytext NOT NULL,
  `RFC` tinytext NOT NULL,
  `Credito` double NOT NULL,
  `Calle` tinytext NOT NULL,
  `No_Exterior` varchar(30) NOT NULL,
  `No_Interior` varchar(30) NOT NULL,
  `Colonia` tinytext NOT NULL,
  `CP` varchar(30) NOT NULL,
  `Ciudad` tinytext NOT NULL,
  `Estado` tinytext NOT NULL,
  `Pais` tinytext NOT NULL,
  `Contacto` tinytext NOT NULL,
  `Puesto` tinytext NOT NULL,
  `Email_Contacto` tinytext NOT NULL,
  `Telefono_Contacto` tinytext NOT NULL,
  `Telefono` tinytext NOT NULL,
  `Segundo_Telefono` tinytext NOT NULL,
  `Email` tinytext NOT NULL,
  `Clabe` tinytext NOT NULL,
  `Banco` tinytext NOT NULL,
  `Titular` tinytext NOT NULL,
  `Fecha_Registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`ID_Proveedor`, `Razon_Social`, `RFC`, `Credito`, `Calle`, `No_Exterior`, `No_Interior`, `Colonia`, `CP`, `Ciudad`, `Estado`, `Pais`, `Contacto`, `Puesto`, `Email_Contacto`, `Telefono_Contacto`, `Telefono`, `Segundo_Telefono`, `Email`, `Clabe`, `Banco`, `Titular`, `Fecha_Registro`) VALUES
(1, 'PROVEEDOR GENERAL', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-09-15 09:55:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `ID_Sucursal` int NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Calle` text NOT NULL,
  `No_Exterior` varchar(11) NOT NULL,
  `No_Interior` varchar(11) NOT NULL,
  `Colonia` tinytext NOT NULL,
  `CP` varchar(11) NOT NULL,
  `Ciudad` tinytext NOT NULL,
  `Estado` tinytext NOT NULL,
  `Pais` tinytext NOT NULL,
  `Email` text NOT NULL,
  `Telefono` tinytext NOT NULL,
  `Segundo_Telefono` tinytext NOT NULL,
  `Latitud` varchar(60) NOT NULL,
  `Longitud` varchar(60) NOT NULL,
  `Fecha_Registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traslados`
--

CREATE TABLE `traslados` (
  `ID_Traslado` int NOT NULL,
  `FK_Sucursal_Origen` int NOT NULL,
  `FK_Sucursal_Destino` int NOT NULL,
  `Estatus` varchar(30) NOT NULL,
  `Fecha_Traslado` date NOT NULL,
  `Fecha_Registro` date NOT NULL,
  `FK_Usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_Usuario` int NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Primer_Apellido` tinytext NOT NULL,
  `Segundo_Apellido` tinytext NOT NULL,
  `Puesto` tinytext NOT NULL,
  `Correo` varchar(300) NOT NULL,
  `Contrasena` text NOT NULL,
  `Tipo_Usuario` tinyint(1) NOT NULL,
  `Permisos` text NOT NULL,
  `Estatus` tinyint(1) NOT NULL,
  `Intentos` int NOT NULL,
  `Ultimo_Intento` datetime NOT NULL,
  `Tiempo_Inicio` datetime NOT NULL,
  `Tiempo_Final` datetime NOT NULL,
  `Foto` text NOT NULL,
  `Temporal` tinyint(1) NOT NULL,
  `Activo` tinyint(1) NOT NULL,
  `Tipo_Login` int NOT NULL,
  `Conectado` tinyint(1) NOT NULL,
  `Fecha_Alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_Usuario`, `Nombre`, `Primer_Apellido`, `Segundo_Apellido`, `Puesto`, `Correo`, `Contrasena`, `Tipo_Usuario`, `Permisos`, `Estatus`, `Intentos`, `Ultimo_Intento`, `Tiempo_Inicio`, `Tiempo_Final`, `Foto`, `Temporal`, `Activo`, `Tipo_Login`, `Conectado`, `Fecha_Alta`) VALUES
(1, 'Admin', 'Admin', '', '', 'almarazjorgeluis@gmail.com', '$2y$12$4LFtt7p5ATSWqjv.u7LAOevxi4NtQMJBU5Q2j1GuafeIQpHXzXUBO', 1, '', 0, 0, '2026-09-15 09:55:52', '2026-09-15 09:55:52', '0000-00-00 00:00:00', '', 0, 1, 1, 0, '2026-09-15 09:55:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `ID_Venta` int NOT NULL,
  `FK_Usuario` int NOT NULL COMMENT 'Usuario que hizo la venta',
  `FK_Cliente` int NOT NULL,
  `FK_Detalles_Caja` int NOT NULL,
  `Descuento` double NOT NULL,
  `Total` double NOT NULL,
  `Tipo_Pago` varchar(60) NOT NULL,
  `Pago` double NOT NULL,
  `Cambio` double NOT NULL,
  `Detalles` text NOT NULL,
  `Fecha_Registro` datetime NOT NULL,
  `Estatus` int NOT NULL,
  `Fecha_Cancelacion` datetime NOT NULL,
  `Regrezo_Inventario` tinyint(1) NOT NULL,
  `Turno` varchar(11) NOT NULL,
  `FK_Direccion` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `ventas`
--
DELIMITER $$
CREATE TRIGGER `before_delete_venta` BEFORE DELETE ON `ventas` FOR EACH ROW BEGIN
			-- Restaurar el inventario antes de que se eliminen los detalles
			UPDATE inventario i
			JOIN detalles_ventas vd ON i.FK_Producto = vd.FK_Producto
			LEFT JOIN (
				SELECT FK_Detalle_Venta, SUM(Cantidad) AS Total_Devuelto
				FROM devoluciones
				GROUP BY FK_Detalle_Venta
			) d ON vd.ID_Detalle_Venta = d.FK_Detalle_Venta
			SET i.Cantidad = i.Cantidad + (vd.Cantidad - IFNULL(d.Total_Devuelto, 0))
			WHERE vd.FK_Venta = OLD.ID_Venta;
		END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_pagos`
--

CREATE TABLE `ventas_pagos` (
  `ID_Pago` int NOT NULL,
  `FK_Venta` int NOT NULL,
  `Concepto` text NOT NULL,
  `Monto` double NOT NULL,
  `Tipo_Pago` varchar(100) NOT NULL,
  `Detalles` text NOT NULL,
  `FK_Usuario` int NOT NULL,
  `FK_Detalle_Caja` int NOT NULL,
  `Archivo` text NOT NULL,
  `Fecha_Registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `ventas_pagos`
--
DELIMITER $$
CREATE TRIGGER `actualizar_venta` AFTER INSERT ON `ventas_pagos` FOR EACH ROW BEGIN
			IF (SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = New.FK_Venta) >= (SELECT (Total - Pago + Cambio) FROM ventas WHERE ID_Venta = New.FK_Venta) THEN
				UPDATE ventas SET Estatus = 0 WHERE ID_Venta = New.FK_Venta;
			END IF;
		END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `estatus_venta` AFTER DELETE ON `ventas_pagos` FOR EACH ROW BEGIN
			IF IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = Old.FK_Venta), 0) < (SELECT (Total - Pago + Cambio) FROM ventas WHERE ID_Venta = Old.FK_Venta) THEN
				UPDATE ventas SET Estatus = 1 WHERE ID_Venta = Old.FK_Venta;
			END IF;
		END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`ID_Caja`);

--
-- Indices de la tabla `clasificaciones`
--
ALTER TABLE `clasificaciones`
  ADD PRIMARY KEY (`ID_Clasificacion`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`ID_Cliente`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`ID_Compra`),
  ADD KEY `compras_ibfk_1` (`FK_Proveedor`),
  ADD KEY `compras_ibfk_2` (`FK_Usuario`);

--
-- Indices de la tabla `compras_pagos`
--
ALTER TABLE `compras_pagos`
  ADD PRIMARY KEY (`ID_Pago`),
  ADD KEY `FK_Compra` (`FK_Compra`),
  ADD KEY `FK_Usuario` (`FK_Usuario`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`ID_Configuracion`);

--
-- Indices de la tabla `configuracion_facturacion`
--
ALTER TABLE `configuracion_facturacion`
  ADD PRIMARY KEY (`ID_Configuracion`);

--
-- Indices de la tabla `detalles_caja`
--
ALTER TABLE `detalles_caja`
  ADD PRIMARY KEY (`ID_Detalle_Caja`),
  ADD KEY `FK_Caja` (`FK_Caja`);

--
-- Indices de la tabla `detalles_facturas`
--
ALTER TABLE `detalles_facturas`
  ADD PRIMARY KEY (`ID_Detalle_Factura`),
  ADD KEY `FK_Factura` (`FK_Factura`);

--
-- Indices de la tabla `detalles_inventario`
--
ALTER TABLE `detalles_inventario`
  ADD PRIMARY KEY (`ID_Detalle_Inventario`),
  ADD KEY `FK_Inventario` (`FK_Inventario`);

--
-- Indices de la tabla `detalles_orden`
--
ALTER TABLE `detalles_orden`
  ADD PRIMARY KEY (`ID_Detalle_Orden`),
  ADD KEY `FK_Orden` (`FK_Orden`);

--
-- Indices de la tabla `detalles_orden_lotes`
--
ALTER TABLE `detalles_orden_lotes`
  ADD PRIMARY KEY (`ID_Detalle_Orden_Lote`),
  ADD KEY `FK_Detalle_Orden` (`FK_Detalle_Orden`),
  ADD KEY `Fecha_Caducidad` (`Fecha_Caducidad`);

--
-- Indices de la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  ADD PRIMARY KEY (`ID_Detalle_Venta`),
  ADD KEY `FK_Venta` (`FK_Venta`);

--
-- Indices de la tabla `detalles_ventas_lotes`
--
ALTER TABLE `detalles_ventas_lotes`
  ADD PRIMARY KEY (`ID_Detalle_Venta_Lote`),
  ADD KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
  ADD KEY `FK_Lote` (`FK_Lote`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`ID_Detalle_Compra`),
  ADD KEY `FK_Compra` (`FK_Compra`);

--
-- Indices de la tabla `detalle_compras_lotes`
--
ALTER TABLE `detalle_compras_lotes`
  ADD PRIMARY KEY (`ID_Detalle_Compra_Lote`),
  ADD KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
  ADD KEY `FK_Lote` (`FK_Lote`);

--
-- Indices de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD PRIMARY KEY (`ID_Devolucion`),
  ADD KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`);

--
-- Indices de la tabla `direcciones_cliente`
--
ALTER TABLE `direcciones_cliente`
  ADD PRIMARY KEY (`ID_Direccion`),
  ADD KEY `FK_Cliente` (`FK_Cliente`);

--
-- Indices de la tabla `docs_relacionados`
--
ALTER TABLE `docs_relacionados`
  ADD PRIMARY KEY (`ID_Documento`),
  ADD KEY `FK_Factura` (`FK_Factura`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`ID_Factura`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`ID_Gasto`);

--
-- Indices de la tabla `historial_caja`
--
ALTER TABLE `historial_caja`
  ADD PRIMARY KEY (`ID_Historial`),
  ADD KEY `FK_Detalle_Caja` (`FK_Detalle_Caja`),
  ADD KEY `FK_Usuario` (`FK_Usuario`);

--
-- Indices de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  ADD PRIMARY KEY (`ID_Impuesto`);

--
-- Indices de la tabla `impuestos_docs`
--
ALTER TABLE `impuestos_docs`
  ADD PRIMARY KEY (`ID_Impuesto_Docs`),
  ADD KEY `FK_Detalle_Docs` (`FK_Detalle_Docs`);

--
-- Indices de la tabla `impuestos_factura`
--
ALTER TABLE `impuestos_factura`
  ADD PRIMARY KEY (`ID_Impuesto_Factura`),
  ADD KEY `FK_Detalle_Factura` (`FK_Detalle_Factura`);

--
-- Indices de la tabla `impuestos_productos`
--
ALTER TABLE `impuestos_productos`
  ADD PRIMARY KEY (`ID_Impuesto_Producto`),
  ADD KEY `FK_Producto` (`FK_Producto`),
  ADD KEY `FK_Impuesto` (`FK_Impuesto`);

--
-- Indices de la tabla `impuestos_ventas`
--
ALTER TABLE `impuestos_ventas`
  ADD PRIMARY KEY (`ID_Impuesto_Venta`),
  ADD KEY `FK_Detalle_Producto` (`FK_Detalle_Venta`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`ID_Inventario`),
  ADD KEY `FK_Producto` (`FK_Producto`);

--
-- Indices de la tabla `inventario_lotes`
--
ALTER TABLE `inventario_lotes`
  ADD PRIMARY KEY (`ID_Lote`),
  ADD KEY `FK_Inventario` (`FK_Inventario`),
  ADD KEY `Fecha_Caducidad` (`Fecha_Caducidad`);

--
-- Indices de la tabla `merma`
--
ALTER TABLE `merma`
  ADD PRIMARY KEY (`ID_Merma`),
  ADD KEY `FK_Producto` (`FK_Producto`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`ID_Movimiento`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD PRIMARY KEY (`ID_Movimiento`),
  ADD KEY `FK_Detalle_Caja` (`FK_Detalle_Caja`);

--
-- Indices de la tabla `ordenes_compra`
--
ALTER TABLE `ordenes_compra`
  ADD PRIMARY KEY (`ID_Orden_Compra`),
  ADD KEY `ordenes_compra_ibfk_1` (`FK_Usuario`),
  ADD KEY `ordenes_compra_ibfk_2` (`FK_Proveedor`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`ID_Producto`),
  ADD UNIQUE KEY `Codigo` (`Codigo`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`ID_Proveedor`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`ID_Sucursal`);

--
-- Indices de la tabla `traslados`
--
ALTER TABLE `traslados`
  ADD PRIMARY KEY (`ID_Traslado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Correo` (`Correo`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`ID_Venta`),
  ADD KEY `FK_Usuario` (`FK_Usuario`);

--
-- Indices de la tabla `ventas_pagos`
--
ALTER TABLE `ventas_pagos`
  ADD PRIMARY KEY (`ID_Pago`),
  ADD KEY `FK_Venta` (`FK_Venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `ID_Caja` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clasificaciones`
--
ALTER TABLE `clasificaciones`
  MODIFY `ID_Clasificacion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `ID_Cliente` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `ID_Compra` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras_pagos`
--
ALTER TABLE `compras_pagos`
  MODIFY `ID_Pago` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `ID_Configuracion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `configuracion_facturacion`
--
ALTER TABLE `configuracion_facturacion`
  MODIFY `ID_Configuracion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalles_caja`
--
ALTER TABLE `detalles_caja`
  MODIFY `ID_Detalle_Caja` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_facturas`
--
ALTER TABLE `detalles_facturas`
  MODIFY `ID_Detalle_Factura` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_inventario`
--
ALTER TABLE `detalles_inventario`
  MODIFY `ID_Detalle_Inventario` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_orden`
--
ALTER TABLE `detalles_orden`
  MODIFY `ID_Detalle_Orden` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_orden_lotes`
--
ALTER TABLE `detalles_orden_lotes`
  MODIFY `ID_Detalle_Orden_Lote` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  MODIFY `ID_Detalle_Venta` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalles_ventas_lotes`
--
ALTER TABLE `detalles_ventas_lotes`
  MODIFY `ID_Detalle_Venta_Lote` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `ID_Detalle_Compra` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compras_lotes`
--
ALTER TABLE `detalle_compras_lotes`
  MODIFY `ID_Detalle_Compra_Lote` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  MODIFY `ID_Devolucion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direcciones_cliente`
--
ALTER TABLE `direcciones_cliente`
  MODIFY `ID_Direccion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `docs_relacionados`
--
ALTER TABLE `docs_relacionados`
  MODIFY `ID_Documento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `ID_Factura` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `ID_Gasto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_caja`
--
ALTER TABLE `historial_caja`
  MODIFY `ID_Historial` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `impuestos`
--
ALTER TABLE `impuestos`
  MODIFY `ID_Impuesto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `impuestos_docs`
--
ALTER TABLE `impuestos_docs`
  MODIFY `ID_Impuesto_Docs` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `impuestos_factura`
--
ALTER TABLE `impuestos_factura`
  MODIFY `ID_Impuesto_Factura` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `impuestos_productos`
--
ALTER TABLE `impuestos_productos`
  MODIFY `ID_Impuesto_Producto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `impuestos_ventas`
--
ALTER TABLE `impuestos_ventas`
  MODIFY `ID_Impuesto_Venta` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `ID_Inventario` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_lotes`
--
ALTER TABLE `inventario_lotes`
  MODIFY `ID_Lote` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `merma`
--
ALTER TABLE `merma`
  MODIFY `ID_Merma` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `ID_Movimiento` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  MODIFY `ID_Movimiento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes_compra`
--
ALTER TABLE `ordenes_compra`
  MODIFY `ID_Orden_Compra` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `ID_Producto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `ID_Proveedor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `ID_Sucursal` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `traslados`
--
ALTER TABLE `traslados`
  MODIFY `ID_Traslado` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_Usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `ID_Venta` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas_pagos`
--
ALTER TABLE `ventas_pagos`
  MODIFY `ID_Pago` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`FK_Proveedor`) REFERENCES `proveedores` (`ID_Proveedor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras_pagos`
--
ALTER TABLE `compras_pagos`
  ADD CONSTRAINT `compras_pagos_ibfk_1` FOREIGN KEY (`FK_Compra`) REFERENCES `compras` (`ID_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `compras_pagos_ibfk_2` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_caja`
--
ALTER TABLE `detalles_caja`
  ADD CONSTRAINT `detalles_caja_ibfk_1` FOREIGN KEY (`FK_Caja`) REFERENCES `cajas` (`ID_Caja`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_facturas`
--
ALTER TABLE `detalles_facturas`
  ADD CONSTRAINT `detalles_facturas_ibfk_1` FOREIGN KEY (`FK_Factura`) REFERENCES `facturas` (`ID_Factura`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_inventario`
--
ALTER TABLE `detalles_inventario`
  ADD CONSTRAINT `detalles_inventario_ibfk_1` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_orden`
--
ALTER TABLE `detalles_orden`
  ADD CONSTRAINT `detalles_orden_ibfk_1` FOREIGN KEY (`FK_Orden`) REFERENCES `ordenes_compra` (`ID_Orden_Compra`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_orden_lotes`
--
ALTER TABLE `detalles_orden_lotes`
  ADD CONSTRAINT `detalles_orden_lotes_ibfk_1` FOREIGN KEY (`FK_Detalle_Orden`) REFERENCES `detalles_orden` (`ID_Detalle_Orden`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_ventas`
--
ALTER TABLE `detalles_ventas`
  ADD CONSTRAINT `detalles_ventas_ibfk_1` FOREIGN KEY (`FK_Venta`) REFERENCES `ventas` (`ID_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalles_ventas_lotes`
--
ALTER TABLE `detalles_ventas_lotes`
  ADD CONSTRAINT `detalles_ventas_lotes_ibfk_1` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalles_ventas_lotes_ibfk_2` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`FK_Compra`) REFERENCES `compras` (`ID_Compra`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_compras_lotes`
--
ALTER TABLE `detalle_compras_lotes`
  ADD CONSTRAINT `detalle_compras_lotes_ibfk_1` FOREIGN KEY (`FK_Detalle_Compra`) REFERENCES `detalle_compras` (`ID_Detalle_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_compras_lotes_ibfk_2` FOREIGN KEY (`FK_Lote`) REFERENCES `inventario_lotes` (`ID_Lote`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD CONSTRAINT `devoluciones_ibfk_1` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direcciones_cliente`
--
ALTER TABLE `direcciones_cliente`
  ADD CONSTRAINT `direcciones_cliente_ibfk_1` FOREIGN KEY (`FK_Cliente`) REFERENCES `clientes` (`ID_Cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `docs_relacionados`
--
ALTER TABLE `docs_relacionados`
  ADD CONSTRAINT `docs_relacionados_ibfk_1` FOREIGN KEY (`FK_Factura`) REFERENCES `facturas` (`ID_Factura`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_caja`
--
ALTER TABLE `historial_caja`
  ADD CONSTRAINT `historial_caja_ibfk_1` FOREIGN KEY (`FK_Detalle_Caja`) REFERENCES `detalles_caja` (`ID_Detalle_Caja`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historial_caja_ibfk_2` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `impuestos_docs`
--
ALTER TABLE `impuestos_docs`
  ADD CONSTRAINT `impuestos_docs_ibfk_1` FOREIGN KEY (`FK_Detalle_Docs`) REFERENCES `docs_relacionados` (`ID_Documento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `impuestos_factura`
--
ALTER TABLE `impuestos_factura`
  ADD CONSTRAINT `impuestos_factura_ibfk_1` FOREIGN KEY (`FK_Detalle_Factura`) REFERENCES `detalles_facturas` (`ID_Detalle_Factura`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `impuestos_productos`
--
ALTER TABLE `impuestos_productos`
  ADD CONSTRAINT `impuestos_productos_ibfk_1` FOREIGN KEY (`FK_Producto`) REFERENCES `productos` (`ID_Producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `impuestos_productos_ibfk_2` FOREIGN KEY (`FK_Impuesto`) REFERENCES `impuestos` (`ID_Impuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `impuestos_ventas`
--
ALTER TABLE `impuestos_ventas`
  ADD CONSTRAINT `impuestos_ventas_ibfk_1` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`FK_Producto`) REFERENCES `productos` (`ID_Producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `inventario_lotes`
--
ALTER TABLE `inventario_lotes`
  ADD CONSTRAINT `inventario_lotes_ibfk_1` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `merma`
--
ALTER TABLE `merma`
  ADD CONSTRAINT `merma_ibfk_1` FOREIGN KEY (`FK_Producto`) REFERENCES `productos` (`ID_Producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`FK_Detalle_Caja`) REFERENCES `detalles_caja` (`ID_Detalle_Caja`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ordenes_compra`
--
ALTER TABLE `ordenes_compra`
  ADD CONSTRAINT `ordenes_compra_ibfk_1` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ordenes_compra_ibfk_2` FOREIGN KEY (`FK_Proveedor`) REFERENCES `proveedores` (`ID_Proveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `ventas_pagos`
--
ALTER TABLE `ventas_pagos`
  ADD CONSTRAINT `ventas_pagos_ibfk_1` FOREIGN KEY (`FK_Venta`) REFERENCES `ventas` (`ID_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
