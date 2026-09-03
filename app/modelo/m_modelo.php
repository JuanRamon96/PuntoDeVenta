<?php
date_default_timezone_set('America/Mexico_City');
include "config/conexion.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/vendor/autoload.php';

class m_modelo extends conexion
{
	public $link;
	public $numerofilas;
	public $error;

	public function __construct()
	{
		$this->link = conexion::__construct();
	}

	public function _insertar($query)
	{
		$result = $this->link->query($query);
		$this->numerofilas = $this->link->affected_rows;
		if (!$result) {
			$error = 'si';
		} else {
			$error = 'no';
		}

		return $error;
	}

	public function _consultar($query)
	{
		$result = $this->link->query($query);
		$this->numerofilas = $result->num_rows;
		if (!$result) {
			$this->error = 'si';
			echo "Se produjo un error en el modelo: " . mysqli_error($this->link) . " SQL: " . $query;
		} else {
			$this->error = 'no';
			while ($resultado[] = $result->fetch_array());
		}

		return $resultado;
	}

	public function _email($destino, $asunto, $mensaje)
	{
		$mail = new PHPMailer(true);

		try {
			$mail->SMTPOptions = array(
				'ssl' => array(
					'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
					'verify_peer' => false,
					'verify_peer_name' => false
				)
			);

			// Configuración SMTP
			$mail->isSMTP();
			$mail->Host = '216.246.113.191';
			$mail->SMTPAuth = true;
			$mail->Username = 'ventastool@bigtool.mx';
			$mail->Password = 'Ventas_2026';
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port = 587;
			$mail->CharSet = 'UTF-8'; // Añadir charset

			// Remitentes y destinatarios
			$mail->setFrom('ventastool@bigtool.mx', 'Contacto VentasTool');
			$mail->addAddress($destino);

			// Contenido del email
			$mail->isHTML(true);
			$mail->Subject = $asunto;
			$mail->Body = $mensaje;
			$mail->AltBody = strip_tags($mensaje);

			$mail->send();

			//echo "Mensaje enviado";
		} catch (Exception $e) {
			//echo "Error al enviar correo: " . $e->getMessage();
		}
	}

	public function _edad($fechaVencimiento, $fechaActual)
	{
		$d1 = new DateTime($fechaActual);
		$d2 = new DateTime($fechaVencimiento);

		// Si la fecha de vencimiento ya pasó, invertimos para no tener negativos raros
		$vencido = $d2 < $d1;

		$interval = $d1->diff($d2); // DateInterval

		$anios = $interval->y;
		$meses = $interval->m;
		$dias  = $interval->d;

		// String legible
		$partes = [];
		if ($anios > 0) $partes[] = $anios . ' año' . ($anios != 1 ? 's' : '');
		if ($meses > 0) $partes[] = $meses . ' mes' . ($meses != 1 ? 'es' : '');
		if ($dias > 0 || empty($partes)) $partes[] = $dias . ' día' . ($dias != 1 ? 's' : '');

		$legible = implode(', ', $partes);
		if ($vencido) {
			$legible = 'Vencido hace ' . $legible;
		} else {
			$legible = $legible . ' restantes';
		}

		// String tipo "años~meses~dias"
		$codificado = $anios . '~' . $meses . '~' . $dias;

		return [$legible, $codificado];
	}

	public function movimiento($sql, $id)
	{
		$fecha = date("Y-m-d h:m:s");
		/*$ip = $_SERVER['REMOTE_ADDR']; // Esto contendrá la ip de la solicitud.
		
		$dataArray = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));

		// obteniendo el 'user_agent':
		if ( isset( $_SERVER ) ) {
		    $user_agent = $_SERVER['HTTP_USER_AGENT'];
		} else {
		    global $HTTP_SERVER_VARS;
		    if ( isset( $HTTP_SERVER_VARS ) ) {
		        $user_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
		    } else {
		        global $HTTP_USER_AGENT;
		        $user_agent = $HTTP_USER_AGENT;
		    }
		}
		  
		$os_array =  array(
		    '/windows nt 10/i'      =>  'Windows 10',
		    '/windows nt 6.3/i'     =>  'Windows 8.1',
		    '/windows nt 6.2/i'     =>  'Windows 8',
		    '/windows nt 6.1/i'     =>  'Windows 7',
		    '/windows nt 6.0/i'     =>  'Windows Vista',
		    '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
		    '/windows nt 5.1/i'     =>  'Windows XP',
		    '/windows xp/i'         =>  'Windows XP',
		    '/windows nt 5.0/i'     =>  'Windows 2000',
		    '/windows me/i'         =>  'Windows ME',
		    '/win98/i'              =>  'Windows 98',
		    '/win95/i'              =>  'Windows 95',
		    '/win16/i'              =>  'Windows 3.11',
		    '/macintosh|mac os x/i' =>  'Mac OS X',
		    '/mac_powerpc/i'        =>  'Mac OS 9',
		    '/linux/i'              =>  'Linux',
		    '/browseruntu/i'             =>  'browseruntu',
		    '/iphone/i'             =>  'iPhone',
		    '/ipod/i'               =>  'iPod',
		    '/ipad/i'               =>  'iPad',
		    '/android/i'            =>  'Android',
		    '/blackberry/i'         =>  'BlackBerry',
		    '/webos/i'              =>  'Mobile'
		);
		    
		$os_platform = "Unknown OS Platform";
		foreach ($os_array as $regex => $value) { 
		    if (preg_match($regex, $user_agent)) {
		        $os_platform = $value;
		    }
		}
		
		$browser_array = array(
		    '/msie/i'       =>  'Internet Explorer',
		    '/firefox/i'    =>  'Firefox',
		    '/safari/i'     =>  'Safari',
		    '/chrome/i'     =>  'Chrome',
		    '/edge/i'       =>  'Edge',
		    '/opera/i'      =>  'Opera',
		    '/netscape/i'   =>  'Netscape',
		    '/maxthon/i'    =>  'Maxthon',
		    '/konqueror/i'  =>  'Konqueror',
		    '/mobile/i'     =>  'Handheld Browser'
		);
		$browser = "Unknown Browser";
		foreach ($browser_array as $regex => $value) { 
		    if (preg_match($regex, $user_agent)) {
		        $browser = $value;
		    }
		}

		// finally get the correct version number
		$known = array('Version', $browser, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			 
		if (!preg_match_all($pattern, $user_agent, $matches)) {
			   // we have no matching number just continue
		}
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($user_agent,"Version") < strripos($user_agent,$browser)){
				   $version= $matches['version'][0];
			}else{
				   $version= $matches['version'][1];
			}
		}else {
			$version= $matches['version'][0];
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		$user_browser = $browser." ".$version;	*/
		$sql = $this->link->real_escape_string($sql);

		//$query="INSERT INTO movimientos VALUES(null, '$sql', '$dataArray->geoplugin_request', '$dataArray->geoplugin_countryName', '$dataArray->geoplugin_regionName', '$user_browser', '$os_platform', '$fecha', '$_SERVER[HTTP_USER_AGENT]', '$id')";
		$query = "INSERT INTO movimientos SET Descripcion = '$sql', Fecha = '$fecha', FK_Usuario = '$id'";
		$error = $this->_insertar($query);

		if ($error == 'si') {
			echo "Error Movimientos: " . mysqli_error($this->link);
		}
	}

	public function _crear($numero, $correo, $contrasena)
	{	
		set_time_limit(900);
		
		$this->link->query("CREATE DATABASE punto_venta_$numero");

		$this->link->query("USE punto_venta_$numero");

		$this->link->query("CREATE TABLE `cajas` (
			`ID_Caja` int(11) NOT NULL,
			`Nombre` tinytext NOT NULL,
			`Detalles` text NOT NULL,
			`Estado` tinyint(1) NOT NULL COMMENT 'Cerrada(0) Abierta(1)',
			`FK_Usuario` int(11) NOT NULL,
			`FK_Sucursal` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("INSERT INTO `cajas` (`ID_Caja`, `Nombre`, `Detalles`, `Estado`, `FK_Usuario`, `FK_Sucursal`) VALUES (1, 'Caja 01', '', 0, 0, 0);");

		$this->link->query("CREATE TABLE `clasificaciones` (
			`ID_Clasificacion` int(11) NOT NULL,
			`Nombre` tinytext NOT NULL,
			`Descripcion` text NOT NULL,
			`Foto` text NOT NULL,
			`Fecha_Registro` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `clientes` (
			`ID_Cliente` int(11) NOT NULL,
			`FK_Sucursal` int(11) NOT NULL,
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
		");

		$this->link->query("CREATE TABLE `compras` (
			`ID_Compra` int(11) NOT NULL,
			`FK_Usuario` int(11) NOT NULL,
			`FK_Proveedor` int(11) NOT NULL,
			`FK_Orden` int(11) NOT NULL,
			`Tipo_Compra` varchar(50) NOT NULL,
			`Estatus` varchar(50) NOT NULL,
			`Descuento` double NOT NULL,
			`Total` double NOT NULL,
			`Fecha_Registro` datetime NOT NULL,
			`Fecha_Credito` datetime NOT NULL,
			`FK_Sucursal` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TRIGGER `completar_orden` AFTER INSERT ON `compras` FOR EACH ROW BEGIN
			IF New.FK_Orden != 0 THEN
				UPDATE ordenes_compra SET Estatus = 'Completada' WHERE ID_Orden_Compra = New.FK_Orden;
			END IF;
		END;");

		$this->link->query("CREATE TABLE `compras_pagos` (
			`ID_Pago` int(11) NOT NULL,
			`FK_Compra` int(11) NOT NULL,
			`Concepto` text NOT NULL,
			`Monto` double NOT NULL,
			`Tipo_Pago` varchar(100) NOT NULL,
			`Detalles` text NOT NULL,
			`FK_Usuario` int(11) NOT NULL,
			`FK_Detalle_Caja` int(11) NOT NULL,
			`Archivo` text NOT NULL,
			`Fecha_Registro` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TRIGGER `actualizar_compra` AFTER INSERT ON `compras_pagos` FOR EACH ROW BEGIN
			IF (SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = New.FK_Compra) >= (SELECT Total FROM compras WHERE ID_Compra = New.FK_Compra) THEN
				UPDATE compras SET Estatus = 'Completada' WHERE ID_Compra = New.FK_Compra;
			END IF;
		END;");

		$this->link->query("CREATE TRIGGER `estatus_compra` AFTER DELETE ON `compras_pagos` FOR EACH ROW BEGIN
			IF IFNULL((SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = Old.FK_Compra), 0) < (SELECT Total FROM compras WHERE ID_Compra = Old.FK_Compra) THEN
				UPDATE compras SET Estatus = 'Pendiente' WHERE ID_Compra = Old.FK_Compra;
			END IF;
		END;");

		$this->link->query("CREATE TABLE `configuracion` (
			`ID_Configuracion` int(11) NOT NULL,
			`Nombre` varchar(60) NOT NULL,
			`Domicilio` tinytext NOT NULL,
			`Telefono` varchar(60) NOT NULL,
			`Foto` text NOT NULL,
			`Porcentaje_Suma` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("INSERT INTO `configuracion` (`ID_Configuracion`, `Nombre`, `Domicilio`, `Telefono`, `Foto`, `Porcentaje_Suma`) VALUES (1, 'Punto de Venta', '', '', '', 0);");

		$this->link->query("CREATE TABLE `configuracion_facturacion` (
			`ID_Configuracion` int(11) NOT NULL,
			`RFC` varchar(60) NOT NULL,
			`Nombre` tinytext NOT NULL,
			`Regimen` tinytext NOT NULL,
			`Domicilio` text NOT NULL,
			`CP` varchar(30) NOT NULL,
			`Certificado` text NOT NULL,
			`Key_Cer` text NOT NULL,
			`Contrasena` tinytext NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("INSERT INTO `configuracion_facturacion` (`ID_Configuracion`, `RFC`, `Nombre`, `Regimen`, `Domicilio`, `CP`, `Certificado`, `Key_Cer`, `Contrasena`) VALUES (1, '', '', '', '', '', '', '', '');");

		$this->link->query("CREATE TABLE `detalles_caja` (
			`ID_Detalle_Caja` int(11) NOT NULL,
			`FK_Caja` int(11) NOT NULL,
			`Fecha_Abrir` datetime NOT NULL,
			`Monto_Abrir` double NOT NULL,
			`FK_Usuario_Abrir` int(11) NOT NULL,
			`Fecha_Cierre` datetime NOT NULL,
			`Monto_Cierre` double NOT NULL,
			`FK_Usuario_Cierre` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `detalles_facturas` (
			`ID_Detalle_Factura` int(11) NOT NULL,
			`FK_Factura` int(11) NOT NULL,
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
		");

		$this->link->query("CREATE TABLE `detalles_inventario` (
			`ID_Detalle_Inventario` int(11) NOT NULL,
			`FK_Inventario` int(11) NOT NULL,
			`FK_Sucursal` int(11) NOT NULL,
			`Cantidad` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `detalles_orden` (
			`ID_Detalle_Orden` int(11) NOT NULL,
			`FK_Orden` int(11) NOT NULL,
			`FK_Producto` int(11) NOT NULL,
			`Descripcion` tinytext NOT NULL,
			`Costo` double NOT NULL,
			`Cantidad` double NOT NULL,
			`Subtotal` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `detalles_ventas` (
			`ID_Detalle_Venta` int(11) NOT NULL,
			`FK_Venta` int(11) NOT NULL,
			`FK_Producto` int(11) NOT NULL,
			`Descripcion` tinytext NOT NULL,
			`Precio` double NOT NULL,
			`Costo` double NOT NULL,
			`Cantidad` double NOT NULL,
			`Descuento` double NOT NULL,
			`Total` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;	
		");

		$this->link->query("CREATE TRIGGER `devolverInve` BEFORE DELETE ON `detalles_ventas` FOR EACH ROW BEGIN
			DECLARE v_Cantidad_Devuelta DECIMAL(10,2);

			SELECT IFNULL(SUM(Cantidad), 0) INTO v_Cantidad_Devuelta
			FROM devoluciones
			WHERE FK_Detalle_Venta = OLD.ID_Detalle_Venta;

			UPDATE inventario 
			SET Cantidad = Cantidad + (OLD.Cantidad - v_Cantidad_Devuelta) 
			WHERE FK_Producto = OLD.FK_Producto;
				
			/*UPDATE detalles_inventario SET Cantidad = Cantidad + Old.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = Old.FK_Producto) AND FK_Sucursal = (SELECT FK_Sucursal FROM cajas WHERE ID_Caja = (SELECT FK_Caja FROM detalles_caja WHERE ID_Detalle_Caja = (SELECT FK_Detalles_Caja FROM ventas WHERE ID_Venta = Old.FK_Venta)));*/
		END;");

		$this->link->query("CREATE TRIGGER `rebajaInven` AFTER INSERT ON `detalles_ventas` FOR EACH ROW BEGIN 
			UPDATE inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Producto = New.FK_Producto; 
					
			UPDATE detalles_inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = New.FK_Producto) AND FK_Sucursal = (SELECT FK_Sucursal FROM cajas WHERE ID_Caja = (SELECT FK_Caja FROM detalles_caja WHERE ID_Detalle_Caja = (SELECT FK_Detalles_Caja FROM ventas WHERE ID_Venta = New.FK_Venta)));
		END;");

		$this->link->query("CREATE TABLE `detalle_compras` (
			`ID_Detalle_Compra` int(11) NOT NULL,
			`FK_Compra` int(11) NOT NULL,
			`FK_Producto` int(11) NOT NULL,
			`Descripcion` tinytext NOT NULL,
			`Costo` double NOT NULL,
			`Cantidad` double NOT NULL,
			`Subtotal` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TRIGGER `actualizar_inventario` AFTER INSERT ON `detalle_compras` FOR EACH ROW BEGIN 
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
		END;");

		$this->link->query("CREATE TABLE `devoluciones` (
			`ID_Devolucion` int(11) NOT NULL,
			`FK_Detalle_Venta` int(11) NOT NULL,
			`Cantidad` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TRIGGER trg_devoluciones_after_insert
			AFTER INSERT ON devoluciones
			FOR EACH ROW
			BEGIN
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
			END;
		");

		$this->link->query("CREATE TRIGGER trg_devoluciones_after_delete
			AFTER DELETE ON devoluciones
			FOR EACH ROW
			BEGIN
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
			END;
		");

		$this->link->query("CREATE TABLE `direcciones_cliente` (
			`ID_Direccion` int(11) NOT NULL,
			`FK_Cliente` int(11) NOT NULL,
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
		");

		$this->link->query("CREATE TABLE `docs_relacionados` (
			`ID_Documento` int(11) NOT NULL,
			`FK_Factura` int(11) NOT NULL,
			`UUID` text NOT NULL,
			`Parcialidad` int(11) NOT NULL,
			`Saldo_Anterior` varchar(11) NOT NULL,
			`Importe_Pagado` varchar(11) NOT NULL,
			`Saldo_Insoluto` varchar(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `facturas` (
			`ID_Factura` int(11) NOT NULL,
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
			`FK_Venta` int(11) NOT NULL,
			`FK_Cliente` int(11) NOT NULL,
			`Email` tinytext NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `gastos` (
			`ID_Gasto` int(11) NOT NULL,
			`Monto` double NOT NULL,
			`Descripcion` text NOT NULL,
			`Comprobante` text NOT NULL,
			`Fecha_Gasto` date NOT NULL,
			`Fecha_Registro` datetime NOT NULL,
			`FK_Usuario` int(11) NOT NULL,
			`FK_Sucursal` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `historial_caja` (
			`ID_Historial` int(11) NOT NULL,
			`FK_Detalle_Caja` int(11) NOT NULL,
			`FK_Usuario` int(11) NOT NULL,
			`Fecha_Uso` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `impuestos` (
			`ID_Impuesto` int(11) NOT NULL,
			`Nombre` tinytext NOT NULL,
			`Porcentaje` double NOT NULL,
			`Clave_CFDI` varchar(60) NOT NULL,
			`Tipo_Factor` varchar(60) NOT NULL,
			`Clase` varchar(60) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("INSERT INTO `impuestos` (`ID_Impuesto`, `Nombre`, `Porcentaje`, `Clave_CFDI`, `Tipo_Factor`, `Clase`) VALUES
			(1, 'IVA', 16, '002', 'Tasa', 'Trasladado'),
			(2, 'ISR', 2, '001', 'Tasa', 'Retenido'),
			(3, 'IEPS', 10, '003', 'Cuota', 'Trasladado');
		");

		$this->link->query("CREATE TABLE `impuestos_docs` (
			`ID_Impuesto_Docs` int(11) NOT NULL,
			`FK_Detalle_Docs` int(11) NOT NULL,
			`Nombre` varchar(60) NOT NULL,
			`Clave` varchar(30) NOT NULL,
			`Valor` varchar(11) NOT NULL,
			`Clase` varchar(30) NOT NULL,
			`Factor` varchar(30) NOT NULL,
			`Importe` varchar(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `impuestos_factura` (
			`ID_Impuesto_Factura` int(11) NOT NULL,
			`FK_Detalle_Factura` int(11) NOT NULL,
			`Nombre` varchar(60) NOT NULL,
			`Clave` varchar(30) NOT NULL,
			`Valor` varchar(11) NOT NULL,
			`Clase` varchar(30) NOT NULL,
			`Factor` varchar(30) NOT NULL,
			`Importe` varchar(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `impuestos_productos` (
			`ID_Impuesto_Producto` int(11) NOT NULL,
			`FK_Producto` int(11) NOT NULL,
			`FK_Impuesto` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `impuestos_ventas` (
			`ID_Impuesto_Venta` int(11) NOT NULL,
			`FK_Detalle_Venta` int(11) NOT NULL,
			`Nombre` tinytext NOT NULL,
			`Porcentaje` double NOT NULL,
			`Clave_CFDI` tinytext NOT NULL,
			`Tipo_Factor` tinytext NOT NULL,
			`Clase` tinytext NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `inventario` (
			`ID_Inventario` int(11) NOT NULL,
			`FK_Producto` int(11) NOT NULL,
			`Cantidad` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `merma` (
			`ID_Merma` int(11) NOT NULL,
			`FK_Producto` int(11) NOT NULL,
			`Descripcion` text NOT NULL,
			`Cantidad` double NOT NULL,
			`Costo` double NOT NULL,
			`Total` double NOT NULL,
			`Fecha_Merma` date NOT NULL,
			`Fecha_Registro` datetime NOT NULL,
			`Foto` text NOT NULL,
			`FK_Sucursal` int(11) NOT NULL,
			`FK_Usuario` int(11) NOT NULL,
			`Afecto_Inventario` varchar(30) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TRIGGER `devolver_inve_de_merma` AFTER DELETE ON `merma` FOR EACH ROW BEGIN
			IF OLD.Afecto_Inventario = 'Si' THEN
				UPDATE inventario SET Cantidad = Cantidad + OLD.Cantidad WHERE FK_Producto = OLD.FK_Producto;
					
				UPDATE detalles_inventario SET Cantidad = Cantidad + OLD.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = OLD.FK_Producto) AND FK_Sucursal = OLD.FK_sucursal;
			END IF;
		END;");

		$this->link->query("CREATE TRIGGER `recarcular_inve_up_merma` AFTER UPDATE ON `merma` FOR EACH ROW BEGIN
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
		END;");

		$this->link->query("CREATE TRIGGER `restar_inve_in_merma` AFTER INSERT ON `merma` FOR EACH ROW BEGIN
			IF New.Afecto_Inventario = 'Si' THEN
				UPDATE inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Producto = New.FK_Producto;
					
				UPDATE detalles_inventario SET Cantidad = Cantidad - New.Cantidad WHERE FK_Inventario = (SELECT ID_Inventario FROM inventario WHERE FK_Producto = New.FK_Producto) AND FK_Sucursal = New.FK_sucursal;
			END IF;
		END;");

		$this->link->query("CREATE TABLE `movimientos` (
			`ID_Movimiento` int(11) NOT NULL,
			`Descripcion` longtext NOT NULL,
			`IP` varchar(30) NOT NULL,
			`Pais` varchar(60) NOT NULL,
			`Estado` varchar(60) NOT NULL,
			`Navegador` varchar(100) NOT NULL,
			`SO` varchar(60) NOT NULL,
			`Fecha` datetime NOT NULL,
			`Access` longtext NOT NULL,
			`FK_Usuario` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `movimientos_caja` (
			`ID_Movimiento` int(11) NOT NULL,
			`Tipo` varchar(60) NOT NULL,
			`Monto` double NOT NULL,
			`Descripcion` text NOT NULL,
			`Fecha_Registro` datetime NOT NULL,
			`FK_Detalle_Caja` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `ordenes_compra` (
			`ID_Orden_Compra` int(11) NOT NULL,
			`FK_Usuario` int(11) NOT NULL,
			`FK_Proveedor` int(11) NOT NULL,
			`Descuento` double NOT NULL,
			`Total` double NOT NULL,
			`Estatus` varchar(50) NOT NULL,
			`Fecha_Registro` datetime NOT NULL,
			`FK_Sucursal` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `productos` (
			`ID_Producto` int(11) NOT NULL,
			`Codigo` varchar(60) NOT NULL,
			`Descripcion` tinytext NOT NULL,
			`Clase` varchar(30) NOT NULL COMMENT 'Pieza o Granel',
			`Costo` double NOT NULL COMMENT 'General',
			`Costo_Promedio` double NOT NULL,
			`Precio` double NOT NULL COMMENT 'General',
			`Precio_Mayoreo` double NOT NULL COMMENT 'General',
			`Stock_Minimo` double NOT NULL,
			`Stock_Maximo` double NOT NULL,
			`FK_Clasificacion` int(11) NOT NULL,
			`Foto` tinytext NOT NULL,
			`Fecha_Registro` datetime NOT NULL,
			`Clave_ProdServ_CFDI` tinytext NOT NULL,
			`Descripcion_Clave_CDFI` tinytext NOT NULL,
			`Clave_Unidad_CFDI` tinytext NOT NULL,
			`Nombre_Unidad_CFDI` tinytext NOT NULL,
			`Simbolo_CFDI` tinytext NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TRIGGER `agregar_inventario` AFTER INSERT ON `productos` FOR EACH ROW BEGIN
			INSERT INTO inventario SET FK_Producto = New.ID_Producto, Cantidad = 0;
		END;");

		$this->link->query("CREATE TABLE `proveedores` (
			`ID_Proveedor` int(11) NOT NULL,
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
		");

		$this->link->query("INSERT INTO `proveedores` (`ID_Proveedor`, `Razon_Social`, `RFC`, `Credito`, `Calle`, `No_Exterior`, `No_Interior`, `Colonia`, `CP`, `Ciudad`, `Estado`, `Pais`, `Contacto`, `Puesto`, `Email_Contacto`, `Telefono_Contacto`, `Telefono`, `Segundo_Telefono`, `Email`, `Clabe`, `Banco`, `Titular`, `Fecha_Registro`) VALUES
			(1, 'PROVEEDOR GENERAL', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NOW());
		");

		$this->link->query("CREATE TABLE `sucursales` (
			`ID_Sucursal` int(11) NOT NULL,
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
		");

		$this->link->query("CREATE TABLE `traslados` (
			`ID_Traslado` int(11) NOT NULL,
			`FK_Sucursal_Origen` int(11) NOT NULL,
			`FK_Sucursal_Destino` int(11) NOT NULL,
			`Estatus` varchar(30) NOT NULL,
			`Fecha_Traslado` date NOT NULL,
			`Fecha_Registro` date NOT NULL,
			`FK_Usuario` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TABLE `usuarios` (
			`ID_Usuario` int(11) NOT NULL,
			`Nombre` tinytext NOT NULL,
			`Primer_Apellido` tinytext NOT NULL,
			`Segundo_Apellido` tinytext NOT NULL,
			`Puesto` tinytext NOT NULL,
			`Correo` varchar(300) NOT NULL,
			`Contrasena` text NOT NULL,
			`Tipo_Usuario` tinyint(1) NOT NULL,
			`Permisos` text NOT NULL,
			`Estatus` tinyint(1) NOT NULL,
			`Intentos` int(11) NOT NULL,
			`Ultimo_Intento` datetime NOT NULL,
			`Tiempo_Inicio` datetime NOT NULL,
			`Tiempo_Final` datetime NOT NULL,
			`Foto` text NOT NULL,
			`Temporal` tinyint(1) NOT NULL,
			`Activo` tinyint(1) NOT NULL,
			`Tipo_Login` int(2) NOT NULL,
			`Conectado` tinyint(1) NOT NULL,
			`Fecha_Alta` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("INSERT INTO `usuarios` (`ID_Usuario`, `Nombre`, `Primer_Apellido`, `Segundo_Apellido`, `Puesto`, `Correo`, `Contrasena`, `Tipo_Usuario`, `Permisos`, `Estatus`, `Intentos`, `Ultimo_Intento`, `Tiempo_Inicio`, `Tiempo_Final`, `Foto`, `Temporal`, `Activo`, `Tipo_Login`, `Conectado`, `Fecha_Alta`) VALUES
			(1, 'Admin', 'Admin', '', '', '$correo', '$contrasena', 1, '', 0, 0, '', '', '', '', 0, 1, 1, 0, NOW());
		");

		$this->link->query("CREATE TABLE `ventas` (
			`ID_Venta` int(11) NOT NULL,
			`FK_Usuario` int(11) NOT NULL COMMENT 'Usuario que hizo la venta',
			`FK_Cliente` int(11) NOT NULL,
			`FK_Detalles_Caja` int(11) NOT NULL,
			`Descuento` double NOT NULL,
			`Total` double NOT NULL,
			`Tipo_Pago` varchar(60) NOT NULL,
			`Pago` double NOT NULL,
			`Cambio` double NOT NULL,
			`Detalles` text NOT NULL,
			`Fecha_Registro` datetime NOT NULL,
			`Estatus` int(11) NOT NULL,
			`Fecha_Cancelacion` datetime NOT NULL,
			`Regrezo_Inventario` tinyint(1) NOT NULL,
			`Turno` varchar(11) NOT NULL,
			`FK_Direccion` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TRIGGER `before_delete_venta` BEFORE DELETE ON `ventas` FOR EACH ROW BEGIN
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
		END;");

		$this->link->query("CREATE TABLE `ventas_pagos` (
			`ID_Pago` int(11) NOT NULL,
			`FK_Venta` int(11) NOT NULL,
			`Concepto` text NOT NULL,
			`Monto` double NOT NULL,
			`Tipo_Pago` varchar(100) NOT NULL,
			`Detalles` text NOT NULL,
			`FK_Usuario` int(11) NOT NULL,
			`FK_Detalle_Caja` int(11) NOT NULL,
			`Archivo` text NOT NULL,
			`Fecha_Registro` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");

		$this->link->query("CREATE TRIGGER `actualizar_venta` AFTER INSERT ON `ventas_pagos` FOR EACH ROW BEGIN
			IF (SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = New.FK_Venta) >= (SELECT (Total - Pago + Cambio) FROM ventas WHERE ID_Venta = New.FK_Venta) THEN
				UPDATE ventas SET Estatus = 0 WHERE ID_Venta = New.FK_Venta;
			END IF;
		END;");

		$this->link->query("CREATE TRIGGER `estatus_venta` AFTER DELETE ON `ventas_pagos` FOR EACH ROW BEGIN
			IF IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = Old.FK_Venta), 0) < (SELECT (Total - Pago + Cambio) FROM ventas WHERE ID_Venta = Old.FK_Venta) THEN
				UPDATE ventas SET Estatus = 1 WHERE ID_Venta = Old.FK_Venta;
			END IF;
		END;");

		$this->link->query("ALTER TABLE `cajas` ADD PRIMARY KEY (`ID_Caja`);");

		$this->link->query("ALTER TABLE `clasificaciones` ADD PRIMARY KEY (`ID_Clasificacion`);");

		$this->link->query("ALTER TABLE `clientes` ADD PRIMARY KEY (`ID_Cliente`);");

		$this->link->query("ALTER TABLE `compras`
			ADD PRIMARY KEY (`ID_Compra`),
			ADD KEY `compras_ibfk_1` (`FK_Proveedor`),
			ADD KEY `compras_ibfk_2` (`FK_Usuario`);
		");

		$this->link->query("ALTER TABLE `compras_pagos`
			ADD PRIMARY KEY (`ID_Pago`),
			ADD KEY `FK_Compra` (`FK_Compra`),
			ADD KEY `FK_Usuario` (`FK_Usuario`);
		");

		$this->link->query("ALTER TABLE `configuracion` ADD PRIMARY KEY (`ID_Configuracion`);");

		$this->link->query("ALTER TABLE `configuracion_facturacion` ADD PRIMARY KEY (`ID_Configuracion`);");

		$this->link->query("ALTER TABLE `detalles_caja`
			ADD PRIMARY KEY (`ID_Detalle_Caja`),
			ADD KEY `FK_Caja` (`FK_Caja`);
		");

		$this->link->query("ALTER TABLE `detalles_facturas`
			ADD PRIMARY KEY (`ID_Detalle_Factura`),
			ADD KEY `FK_Factura` (`FK_Factura`);
		");

		$this->link->query("ALTER TABLE `detalles_inventario`
			ADD PRIMARY KEY (`ID_Detalle_Inventario`),
			ADD KEY `FK_Inventario` (`FK_Inventario`);
		");

		$this->link->query("ALTER TABLE `detalles_orden`
			ADD PRIMARY KEY (`ID_Detalle_Orden`),
			ADD KEY `FK_Orden` (`FK_Orden`);
		");

		$this->link->query("ALTER TABLE `detalles_ventas`
			ADD PRIMARY KEY (`ID_Detalle_Venta`),
			ADD KEY `FK_Venta` (`FK_Venta`);
		");

		$this->link->query("ALTER TABLE `detalle_compras`
			ADD PRIMARY KEY (`ID_Detalle_Compra`),
			ADD KEY `FK_Compra` (`FK_Compra`);
		");

		$this->link->query("ALTER TABLE `devoluciones`
			ADD PRIMARY KEY (`ID_Devolucion`),
			ADD KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`);
		");

		$this->link->query("ALTER TABLE `direcciones_cliente`
			ADD PRIMARY KEY (`ID_Direccion`),
			ADD KEY `FK_Cliente` (`FK_Cliente`);
		");

		$this->link->query("ALTER TABLE `docs_relacionados`
			ADD PRIMARY KEY (`ID_Documento`),
			ADD KEY `FK_Factura` (`FK_Factura`);
		");

		$this->link->query("ALTER TABLE `facturas` ADD PRIMARY KEY (`ID_Factura`);");

		$this->link->query("ALTER TABLE `gastos` ADD PRIMARY KEY (`ID_Gasto`);");

		$this->link->query("ALTER TABLE `historial_caja`
			ADD PRIMARY KEY (`ID_Historial`),
			ADD KEY `FK_Detalle_Caja` (`FK_Detalle_Caja`),
			ADD KEY `FK_Usuario` (`FK_Usuario`);
		");

		$this->link->query("ALTER TABLE `impuestos` ADD PRIMARY KEY (`ID_Impuesto`);");

		$this->link->query("ALTER TABLE `impuestos_docs`
			ADD PRIMARY KEY (`ID_Impuesto_Docs`),
			ADD KEY `FK_Detalle_Docs` (`FK_Detalle_Docs`);
		");

		$this->link->query("ALTER TABLE `impuestos_factura`
			ADD PRIMARY KEY (`ID_Impuesto_Factura`),
			ADD KEY `FK_Detalle_Factura` (`FK_Detalle_Factura`);
		");

		$this->link->query("ALTER TABLE `impuestos_productos`
			ADD PRIMARY KEY (`ID_Impuesto_Producto`),
			ADD KEY `FK_Producto` (`FK_Producto`),
			ADD KEY `FK_Impuesto` (`FK_Impuesto`);
		");

		$this->link->query("ALTER TABLE `impuestos_ventas`
			ADD PRIMARY KEY (`ID_Impuesto_Venta`),
			ADD KEY `FK_Detalle_Producto` (`FK_Detalle_Venta`);
		");

		$this->link->query("ALTER TABLE `inventario`
			ADD PRIMARY KEY (`ID_Inventario`),
			ADD KEY `FK_Producto` (`FK_Producto`);
		");

		$this->link->query("ALTER TABLE `merma`
			ADD PRIMARY KEY (`ID_Merma`),
			ADD KEY `FK_Producto` (`FK_Producto`);
		");

		$this->link->query("ALTER TABLE `movimientos` ADD PRIMARY KEY (`ID_Movimiento`);");

		$this->link->query("ALTER TABLE `movimientos_caja`
			ADD PRIMARY KEY (`ID_Movimiento`),
			ADD KEY `FK_Detalle_Caja` (`FK_Detalle_Caja`);
		");

		$this->link->query("ALTER TABLE `ordenes_compra`
			ADD PRIMARY KEY (`ID_Orden_Compra`),
			ADD KEY `ordenes_compra_ibfk_1` (`FK_Usuario`),
			ADD KEY `ordenes_compra_ibfk_2` (`FK_Proveedor`);
		");

		$this->link->query("ALTER TABLE `productos`
			ADD PRIMARY KEY (`ID_Producto`),
			ADD UNIQUE KEY `Codigo` (`Codigo`);
		");

		$this->link->query("ALTER TABLE `proveedores` ADD PRIMARY KEY (`ID_Proveedor`);");

		$this->link->query("ALTER TABLE `sucursales` ADD PRIMARY KEY (`ID_Sucursal`);");

		$this->link->query("ALTER TABLE `traslados` ADD PRIMARY KEY (`ID_Traslado`);");

		$this->link->query("ALTER TABLE `usuarios`
			ADD PRIMARY KEY (`ID_Usuario`),
			ADD UNIQUE KEY `Correo` (`Correo`);
		");

		$this->link->query("ALTER TABLE `ventas`
			ADD PRIMARY KEY (`ID_Venta`),
			ADD KEY `FK_Usuario` (`FK_Usuario`);
		");

		$this->link->query("ALTER TABLE `ventas_pagos`
			ADD PRIMARY KEY (`ID_Pago`),
			ADD KEY `FK_Venta` (`FK_Venta`);
		");

		$this->link->query("ALTER TABLE `cajas` MODIFY `ID_Caja` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `clasificaciones` MODIFY `ID_Clasificacion` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `clientes` MODIFY `ID_Cliente` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `compras` MODIFY `ID_Compra` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `compras_pagos` MODIFY `ID_Pago` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `configuracion` MODIFY `ID_Configuracion` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `configuracion_facturacion` MODIFY `ID_Configuracion` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `detalles_caja` MODIFY `ID_Detalle_Caja` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `detalles_facturas` MODIFY `ID_Detalle_Factura` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `detalles_inventario` MODIFY `ID_Detalle_Inventario` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `detalles_orden` MODIFY `ID_Detalle_Orden` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `detalles_ventas` MODIFY `ID_Detalle_Venta` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `detalle_compras` MODIFY `ID_Detalle_Compra` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `devoluciones` MODIFY `ID_Devolucion` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `direcciones_cliente` MODIFY `ID_Direccion` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `docs_relacionados` MODIFY `ID_Documento` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `facturas` MODIFY `ID_Factura` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `gastos` MODIFY `ID_Gasto` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `historial_caja` MODIFY `ID_Historial` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `impuestos` MODIFY `ID_Impuesto` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `impuestos_docs` MODIFY `ID_Impuesto_Docs` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `impuestos_factura` MODIFY `ID_Impuesto_Factura` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `impuestos_productos` MODIFY `ID_Impuesto_Producto` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `impuestos_ventas` MODIFY `ID_Impuesto_Venta` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `inventario` MODIFY `ID_Inventario` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `merma` MODIFY `ID_Merma` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `movimientos` MODIFY `ID_Movimiento` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `movimientos_caja` MODIFY `ID_Movimiento` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `ordenes_compra` MODIFY `ID_Orden_Compra` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `productos` MODIFY `ID_Producto` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `proveedores` MODIFY `ID_Proveedor` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `sucursales` MODIFY `ID_Sucursal` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `traslados` MODIFY `ID_Traslado` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `usuarios` MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `ventas` MODIFY `ID_Venta` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `ventas_pagos` MODIFY `ID_Pago` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `compras`
			ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`FK_Proveedor`) REFERENCES `proveedores` (`ID_Proveedor`) ON DELETE CASCADE ON UPDATE CASCADE,
			ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
		");

		$this->link->query("ALTER TABLE `compras_pagos`
			ADD CONSTRAINT `compras_pagos_ibfk_1` FOREIGN KEY (`FK_Compra`) REFERENCES `compras` (`ID_Compra`) ON DELETE CASCADE ON UPDATE CASCADE,
			ADD CONSTRAINT `compras_pagos_ibfk_2` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
		");

		$this->link->query("ALTER TABLE `detalles_caja` ADD CONSTRAINT `detalles_caja_ibfk_1` FOREIGN KEY (`FK_Caja`) REFERENCES `cajas` (`ID_Caja`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `detalles_facturas` ADD CONSTRAINT `detalles_facturas_ibfk_1` FOREIGN KEY (`FK_Factura`) REFERENCES `facturas` (`ID_Factura`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `detalles_inventario` ADD CONSTRAINT `detalles_inventario_ibfk_1` FOREIGN KEY (`FK_Inventario`) REFERENCES `inventario` (`ID_Inventario`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `detalles_orden` ADD CONSTRAINT `detalles_orden_ibfk_1` FOREIGN KEY (`FK_Orden`) REFERENCES `ordenes_compra` (`ID_Orden_Compra`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `detalles_ventas` ADD CONSTRAINT `detalles_ventas_ibfk_1` FOREIGN KEY (`FK_Venta`) REFERENCES `ventas` (`ID_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `detalle_compras` ADD CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`FK_Compra`) REFERENCES `compras` (`ID_Compra`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `devoluciones` ADD CONSTRAINT `devoluciones_ibfk_1` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `direcciones_cliente` ADD CONSTRAINT `direcciones_cliente_ibfk_1` FOREIGN KEY (`FK_Cliente`) REFERENCES `clientes` (`ID_Cliente`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `docs_relacionados` ADD CONSTRAINT `docs_relacionados_ibfk_1` FOREIGN KEY (`FK_Factura`) REFERENCES `facturas` (`ID_Factura`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `historial_caja`
			ADD CONSTRAINT `historial_caja_ibfk_1` FOREIGN KEY (`FK_Detalle_Caja`) REFERENCES `detalles_caja` (`ID_Detalle_Caja`) ON DELETE CASCADE ON UPDATE CASCADE,
			ADD CONSTRAINT `historial_caja_ibfk_2` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
		");

		$this->link->query("ALTER TABLE `impuestos_docs` ADD CONSTRAINT `impuestos_docs_ibfk_1` FOREIGN KEY (`FK_Detalle_Docs`) REFERENCES `docs_relacionados` (`ID_Documento`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `impuestos_factura` ADD CONSTRAINT `impuestos_factura_ibfk_1` FOREIGN KEY (`FK_Detalle_Factura`) REFERENCES `detalles_facturas` (`ID_Detalle_Factura`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `impuestos_productos`
			ADD CONSTRAINT `impuestos_productos_ibfk_1` FOREIGN KEY (`FK_Producto`) REFERENCES `productos` (`ID_Producto`) ON DELETE CASCADE ON UPDATE CASCADE,
			ADD CONSTRAINT `impuestos_productos_ibfk_2` FOREIGN KEY (`FK_Impuesto`) REFERENCES `impuestos` (`ID_Impuesto`) ON DELETE CASCADE ON UPDATE CASCADE;
		");

		$this->link->query("ALTER TABLE `impuestos_ventas` ADD CONSTRAINT `impuestos_ventas_ibfk_1` FOREIGN KEY (`FK_Detalle_Venta`) REFERENCES `detalles_ventas` (`ID_Detalle_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `inventario` ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`FK_Producto`) REFERENCES `productos` (`ID_Producto`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `merma` ADD CONSTRAINT `merma_ibfk_1` FOREIGN KEY (`FK_Producto`) REFERENCES `productos` (`ID_Producto`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `movimientos_caja` ADD CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`FK_Detalle_Caja`) REFERENCES `detalles_caja` (`ID_Detalle_Caja`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `ordenes_compra`
			ADD CONSTRAINT `ordenes_compra_ibfk_1` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
			ADD CONSTRAINT `ordenes_compra_ibfk_2` FOREIGN KEY (`FK_Proveedor`) REFERENCES `proveedores` (`ID_Proveedor`) ON DELETE CASCADE ON UPDATE CASCADE;
		");

		$this->link->query("ALTER TABLE `ventas` ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`);");

		$this->link->query("ALTER TABLE `ventas_pagos` ADD CONSTRAINT `ventas_pagos_ibfk_1` FOREIGN KEY (`FK_Venta`) REFERENCES `ventas` (`ID_Venta`) ON DELETE CASCADE ON UPDATE CASCADE;");
	}
}
