<?php
class hacerCompra {

	public function _consultar(){
		$omodelo = new m_modelo();
		extract($_POST);

		if ($tipo == "consultarProductos") {
			$buscar =  $omodelo->link->real_escape_string($buscar);
			$limit =  $omodelo->link->real_escape_string($limit);
			$pagina =  $omodelo->link->real_escape_string($pagina);
			$ordenColumna =  $omodelo->link->real_escape_string($ordenColumna);
			$orden =  $omodelo->link->real_escape_string($orden);
			$arreglo = array();

			$busqueda = '';
			if(trim($buscar) != ''){
				$separa = explode(' ', trim($buscar));
				$busqueda = 'WHERE ';
				for ($i=0; $i < count($separa); $i++) { 
					$busqueda .= "CONCAT(DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), Codigo, Descripcion, Costo) REGEXP '".$separa[$i]."'";
					if($i < (count($separa)-1)){
						$busqueda .= ' AND ';
					}
				}
			}	  

			$query = "SELECT ID_Producto, Fecha_Registro AS Fecha, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, Codigo, Descripcion, Costo, Foto, IFNULL((SELECT SUM(Cantidad) FROM inventario WHERE FK_Producto = ID_Producto), 0) AS Existencia, (SELECT COUNT(*) FROM productos $busqueda) AS Num FROM productos $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET ".(($pagina * $limit) - $limit);
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error 1: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					for($i=0; $i<$numerofilas; $i++){
						$foto = '<a href="vistas/assets/images/producto-generico.png" data-fancybox="images">
				        	<div style="background-image: url(' . "'" . 'vistas/assets/images/producto-generico.png' . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
				        	</div>
				        </a>';

				        if ($row[$i]['Foto'] != '' && file_exists('vistas/assets/images/productos/' . $row[$i]['Foto'])) {
				        	$foto = '<a href="vistas/assets/images/productos/' . $row[$i]['Foto'] . '" data-fancybox="images">
				          		<div style="background-image: url(' . "'" . 'vistas/assets/images/productos/' . $row[$i]['Foto'] . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
				          		</div>
				          	</a>';
				        }

						$arreglo['data'][$i] = array(
							'ID' => $row[$i]['ID_Producto'],
							'Fecha' => $row[$i]['Fecha_Registro'],
							'Codigo' => $foto.$row[$i]['Codigo'],
							'Descripcion' => $row[$i]['Descripcion'],
							'Costo' => '<span class="dinero">'.$row[$i]['Costo'].'</span>'
						);
					}

					$arreglo['totales'] = array('NumRows' => $row[0]['Num']);	
				}
			}

			echo json_encode($arreglo);
		}else if($tipo == "consultarProveedores"){
			$buscar =  $omodelo->link->real_escape_string($buscar);
			$limit =  $omodelo->link->real_escape_string($limit);
			$pagina =  $omodelo->link->real_escape_string($pagina);
			$ordenColumna =  $omodelo->link->real_escape_string($ordenColumna);
			$orden =  $omodelo->link->real_escape_string($orden);
			$arreglo = array();

			$busqueda = '';
			if(trim($buscar) != ''){
				$separa = explode(' ', trim($buscar));
				$busqueda = 'AND ';
				for ($i=0; $i < count($separa); $i++) { 
					$busqueda .= "CONCAT(DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), Razon_Social, RFC, Credito, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Contacto, Puesto, Email_Contacto, Telefono_Contacto, Telefono, Segundo_Telefono, Email, Clabe, Banco, Titular) REGEXP '".$separa[$i]."'";
					if($i < (count($separa)-1)){
						$busqueda .= ' AND ';
					}
				}
			}

			$query = "SELECT ID_Proveedor, Razon_Social AS Nombre, RFC, Credito, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Contacto, Puesto, Email_Contacto, Telefono_Contacto, Telefono, Segundo_Telefono, Email, Clabe, Banco, Titular, Fecha_Registro AS Fecha, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, IFNULL((SELECT SUM(Total) - IFNULL((SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = ID_Compra), 0) FROM compras WHERE FK_Proveedor = ID_Proveedor AND Tipo_Compra != 'Contado'), 0) AS Adeudo, (SELECT COUNT(*) FROM proveedores $busqueda WHERE ID_Proveedor != 1) AS Num FROM proveedores WHERE ID_Proveedor != 1 $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET ".(($pagina * $limit) - $limit);
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					for($i=0; $i<$numerofilas; $i++){
						$domicilio = '<b>'.$row[$i]['Calle'].' #'.$row[$i]['No_Exterior'].'</b> ';
				        $contacto = 'Tel: <b>'.$row[$i]['Telefono'].'</b><br>';

				        if ($row[$i]['No_Interior'] != "") {
				        	$domicilio .= 'No. Int. <b>'.$row[$i]['No_Interior'].'</b> ';
				        }

				        if ($row[$i]['Colonia'] != "") {
				            $domicilio .= 'Col. <b>'.$row[$i]['Colonia'].'</b> ';
				        }

				        $domicilio .= 'C.P. <b>'.$row[$i]['CP'].'</b>, <b>'.$row[$i]['Ciudad'].'</b> <b>'.$row[$i]['Estado'].'</b>, <b>'.$row[$i]['Pais'].'</b>';
				          

				        if ($row[$i]['Segundo_Telefono'] != "") {
				            $contacto .= 'Tel: <b>'.$row[$i]['Segundo_Telefono'].'</b><br>';
				        }

				        if ($row[$i]['Email'] != "") {
				            $contacto .= 'Email: <b>'.$row[$i]['Email'].'</b><br>';
				        }

				        if ($row[$i]['Contacto'] != '') {
				            $contacto .= 'Contacto: <b>'.$row[$i]['Contacto'].'</b>';
				        }

				        if ($row[$i]['Email_Contacto'] != '') {
				            $contacto .= 'Email contacto: <b>'.$row[$i]['Email_Contacto'].'</b>';
				        }

				        if ($row[$i]['Telefono_Contacto'] != '') {
				            $contacto .= 'Tel contacto: <b>'.$row[$i]['Telefono_Contacto'].'</b>';
				        }

				        $cuenta = '';
				        if ($row[$i]['Clabe'] != '') {
				            $cuenta .= 'CLABE Interbancaria: <b>'.$row[$i]['Clabe'].'</b>';
				        }

				        if ($row[$i]['Clabe'] != '') {
				            $cuenta .= 'Banco: <b>'.$row[$i]['Banco'].'</b>';
				        }

				        if ($row[$i]['Titular'] != '') {
				            $cuenta .= 'Titular: <b>'.$row[$i]['Titular'].'</b>';
				        }

				        $nombre = '<span>'.$row[$i]['Nombre'].'</span>';
				        if ($row[$i]['RFC'] != '') {
				            $nombre .= '<br>RFC: <b>'.$row[$i]['RFC'].'</b>';
				        }

				        if ($row[$i]['Credito'] != '') {
				            $nombre .= '<br>Crédito: <b>'.$row[$i]['Credito'].'</b><br>Adeudo: <b>'.$row[$i]['Adeudo'].'</b><br>Restante: <b class="dinero">'.($row[$i]['Credito'] - $row[$i]['Adeudo']).'</b>';
				        }

				        $arreglo['data'][$i] = array(
				            'ID' => $row[$i]['ID_Proveedor'],
				            'Fecha' => $row[$i]['Fecha_Registro'],
				            'Nombre' => $nombre,
				            'Domicilio' => $domicilio,
				            'Contacto' => $contacto,
				            'Cuenta' => $cuenta
				        );
					}

					$arreglo['totales'] = array('NumRows' => $row[0]['Num']);	
				}
			}

			echo json_encode($arreglo);
		}else if($tipo == "consultarProductoCodigo"){
			$codigo =  $omodelo->link->real_escape_string($codigo);
			$arreglo = null;	

			$query = "SELECT ID_Producto, Codigo, Descripcion, Costo FROM productos WHERE Codigo = '$codigo'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					$arreglo = $row[0];
				}
			}

			echo json_encode($arreglo);
		}else if($tipo == "consultarOrdenes"){
			$buscar = $omodelo->link->real_escape_string($buscar);
			$limit = $omodelo->link->real_escape_string($limit);
			$pagina = $omodelo->link->real_escape_string($pagina);
			$ordenColumna = $omodelo->link->real_escape_string($ordenColumna);
			$orden = $omodelo->link->real_escape_string($orden);
			$arreglo = array();

			$busqueda = '';
			if(trim($buscar) != ''){
				$separa = explode(' ', trim($buscar));
				$busqueda = 'WHERE ';
				for ($i=0; $i < count($separa); $i++) { 
					$busqueda .= "CONCAT(DATE_FORMAT(ordenes_compra.Fecha_Registro, '%d-%m-%Y %r'), ID_Orden_Compra, Razon_Social, proveedores.Telefono, Total, Estatus, IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = FK_Sucursal), 'NA')) REGEXP '".$separa[$i]."'";
					if($i < (count($separa)-1)){
						$busqueda .= ' AND ';
					}
				}
			}

			$query = "SELECT ID_Orden_Compra, ID_Orden_Compra AS Folio, FK_Sucursal, IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = FK_Sucursal), 'NA') AS Sucursal, FK_Usuario, (SELECT usuarios.Nombre FROM usuarios WHERE ID_Usuario = FK_Usuario) AS NombreUsuario, FK_Proveedor, Razon_Social AS Proveedor, proveedores.Telefono AS Telefono, Total, Estatus, DATE_FORMAT(ordenes_compra.Fecha_Registro, '%d-%m-%Y %r') AS Datos, (SELECT COUNT(*) FROM ordenes_compra INNER JOIN proveedores ON FK_Proveedor = ID_Proveedor $busqueda) AS Num FROM ordenes_compra INNER JOIN proveedores ON FK_Proveedor = ID_Proveedor $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET ".(($pagina * $limit) - $limit);
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					$SumarCompras = 0;
					for($i=0; $i<$numerofilas; $i++){
						$estatus = ''; $botonCargar = '';

						if ($row[$i]['Estatus'] == "Pendiente") {
							$estatus = '<span class="badge rounded-pill bg-warning">Pendiente</span>';

							$botonCargar = '<button class="btn btn-primary btn-sm bCargarOrden" attrID="'.$row[$i]['ID_Orden_Compra'].'" title="Seleccionar"><i class="fas fa-square-check"></i></button>';	
						}else if($row[$i]['Estatus'] == "Completada"){
							$estatus = '<span class="badge rounded-pill bg-success">Completada</span>';
							$botonCargar = '';
						}

						$botonEliminar = '<button class="btn btn-danger btn-sm bEliminarOrden" attrID="'.$row[$i]['ID_Orden_Compra'].'"><i class="fas fa-trash" title="Eliminar Orden"></i></button>';	

						$arreglo['data'][$i] = array(
							'ID' => $row[$i]['ID_Orden_Compra'],
							'Datos' => 'Fecha: <b>'.$row[$i]['Datos'].'<br></b>Folio: <b>'.$row[$i]['Folio'].'</b><br>Usuario: <b>'.$row[$i]['NombreUsuario'].'</b>',
							'Proveedor' => 'Nombre: <b>'.$row[$i]['Proveedor'].'</b><br>Teléfono: <b>'.$row[$i]['Telefono'].'</b>',
							'Total' => 'Total: <b class="dinero" style="font-size: 15px;">'.$row[$i]['Total'].'</b>',
							'Detalles' => $estatus.'<br><button class="btn btn-link btn-sm bVerProductosOrden" attrid="'.$row[$i]['ID_Orden_Compra'].'">Ver productos</button>',
							'Sucursal' => $row[$i]['Sucursal'],
							'Acciones' => $botonCargar.' '.$botonEliminar.' <button class="btn btn-success btn-sm bImprimirTicketOrden" attrID="'.$row[$i]['ID_Orden_Compra'].'" title="Imprimir Orden"><i class="fas fa-print"></i></button>',
						);
					}

					$arreglo['totales'] = array('NumRows' => $row[0]['Num']);	
				}
			}

			echo json_encode($arreglo);	
		}
	}	

	public function _insertar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s'); 

		$idProveedor = $omodelo->link->real_escape_string($idProveedor);
		$sucursal = $omodelo->link->real_escape_string($sucursal);
		$tipoCompra = $omodelo->link->real_escape_string($tipoCompra);
		$descuento = $omodelo->link->real_escape_string($descuento);
		$total = $omodelo->link->real_escape_string($total);
		$fechaCredito = $omodelo->link->real_escape_string($fechaCredito);
		$importe = $omodelo->link->real_escape_string($importePagadoCobrar);
		$concepto = $omodelo->link->real_escape_string($conceptoPagoCobrar);
		$tipoPago = $omodelo->link->real_escape_string($tipoPagoCobrar);
		$detalles = $omodelo->link->real_escape_string($detallesPagoCobrar);
		$orden = $omodelo->link->real_escape_string($orden);

		$estatus = 'Pendiente';
		if($tipoCompra == 'Contado') {
			$estatus = 'Completada';
		}

		$query = "INSERT INTO compras SET FK_Proveedor = '$idProveedor', FK_Orden = '$orden', Total = '$total', Fecha_Credito = '$fechaCredito', Tipo_Compra = '$tipoCompra', Estatus = '$estatus', Descuento = '$descuento', Fecha_Registro = '$fecha', FK_Sucursal = '$sucursal', FK_Usuario = '".$_SESSION['user_punto_venta']['ID_Usuario']."'";
		$error = $omodelo->_insertar($query);

		if ($error == "si") {
			echo "Error 1: ".mysqli_error($omodelo->link);
		}else{
			$id = mysqli_insert_id($omodelo->link);

			$productos = json_decode($productos, true);

			foreach ($productos as $producto) {
				$producto['id'] = $omodelo->link->real_escape_string($producto['id']);
				$producto['descripcion'] = $omodelo->link->real_escape_string($producto['descripcion']);
				$producto['costo'] = $omodelo->link->real_escape_string($producto['costo']);
				$producto['cantidad'] = $omodelo->link->real_escape_string($producto['cantidad']);
				$producto['subtotal'] = $omodelo->link->real_escape_string($producto['subtotal']);
				
				$query3 = "INSERT INTO detalle_compras SET FK_Compra = '$id', FK_Producto = '$producto[id]', Descripcion = '$producto[descripcion]', Costo = '$producto[costo]', Cantidad = '$producto[cantidad]', Subtotal = '$producto[subtotal]'";
				$error3 = $omodelo->_insertar($query3);
				
				if ($error3 == "si") {
					echo "Error detalles compra: ".mysqli_error($omodelo->link);
				}
			}

			if($importe > 0){
				$query1 = "INSERT INTO compras_pagos SET FK_Compra = '$id', Concepto = '$concepto', Monto = '$importe', Tipo_Pago = '$tipoPago', Detalles = '$detalles', Fecha_Registro = '$fecha', FK_Usuario = '".$_SESSION['user_punto_venta']['ID_Usuario']."'";
				$error1 = $omodelo->_insertar($query1);
				$status = 1;

				if ($error1 == "si") {
					echo "Error 2: ".mysqli_error($omodelo->link);
				}else{
					$idPago = mysqli_insert_id($omodelo->link);

					if ($_FILES['comprobantePagoCobrar']['size'] > 0 && $_FILES['comprobantePagoCobrar']['error'] == 0) {
						$file = $_FILES["comprobantePagoCobrar"];
						$nombreDoc = $file["name"];
						$tipo = $file["type"];
						$ruta_provisional = $file["tmp_name"];
						$size = $file["size"];
						$carpeta = "vistas/assets/files/pagos/";

						if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'application/pdf' && $tipo != ''){
							echo "Error 2 Formato";
						}else if ($size > (1024*1024*10)){
							echo "Error 3 Peso";
						}else{
							$status = 0;
							$ruta = $carpeta;
						}
					}
								
					if($status == 0){
						$query2 = "UPDATE compras_pagos SET Archivo = '".$idPago.'_'.$nombreDoc."' WHERE ID_Pago = '$idPago'";
						$error2 = $omodelo->_insertar($query2);	

						if ($error2 == "si") {
							echo "Error 4: ".mysqli_error($omodelo->link); 
						}else{
							move_uploaded_file($ruta_provisional,  $ruta.$idPago.'_'.$nombreDoc);
						}
					}
				}
			}

			echo 'Correcto~'.$id;

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	public function _detalles(){
		$omodelo = new m_modelo();
		extract($_POST);
		$tipo =  $omodelo->link->real_escape_string($tipo);

		if($tipo == 'consultarProductosOrden'){
			$id =  $omodelo->link->real_escape_string($id);
			$tabla = '';

			$query = "SELECT ID_Detalle_Orden, FK_Producto, Codigo, detalles_orden.Descripcion AS Descripcion, detalles_orden.Costo AS Costo, Cantidad, Subtotal FROM detalles_orden LEFT JOIN productos ON FK_Producto = ID_Producto WHERE FK_Orden = '$id' ORDER BY Descripcion";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					for ($i=0; $i < $numerofilas; $i++) { 
						$tabla .= '<tr>
							<td>'.$row[$i]['Codigo'].'</td>
							<td>'.$row[$i]['Descripcion'].'</td>
							<td><span class="dinero">'.$row[$i]['Costo'].'</span></td>
							<td><span class="cantidad">'.$row[$i]['Cantidad'].'</span></td>
							<td><span class="dinero">'.$row[$i]['Subtotal'].'</span></td>
						</tr>';
					}
				}
			}

			echo $tabla;
		}else if($tipo == "consultarOrden"){
			$id = $omodelo->link->real_escape_string($id); 
			$array = null;

			$query = "SELECT ID_Orden_Compra, FK_Proveedor AS Proveedor, FK_Sucursal, Razon_Social, RFC, Credito, Descuento, Total, Estatus, DATE_FORMAT(ordenes_compra.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, IFNULL((SELECT SUM(Total) - IFNULL((SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = ID_Compra), 0) FROM compras WHERE FK_Proveedor = Proveedor AND Tipo_Compra != 'Contado'), 0) AS Adeudo FROM ordenes_compra INNER JOIN proveedores ON FK_Proveedor = ID_Proveedor WHERE ID_Orden_Compra = '$id'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					$productos = array();
					$query1 = "SELECT ID_Detalle_Orden, FK_Producto, Codigo, detalles_orden.Descripcion AS Descripcion, Codigo, detalles_orden.Costo AS Costo, Cantidad, Subtotal FROM detalles_orden LEFT JOIN productos ON FK_Producto = ID_Producto WHERE FK_Orden = '$id' ORDER BY Descripcion";
					$row1 = $omodelo->_consultar($query1);
					$numerofilas1 = $omodelo->numerofilas;

					if($row1 == 'si'){
						echo "Error: ".mysqli_error($omodelo->link);
					}else{
						if($numerofilas1 > 0){
							for ($i=0; $i < $numerofilas1; $i++) { 
								$productos[$i] = array('ID_Detalle_Orden' => $row1[$i]['ID_Detalle_Orden'], 'FK_Producto' => $row1[$i]['FK_Producto'], 'Codigo' => $row1[$i]['Codigo'], 'Descripcion' => $row1[$i]['Descripcion'], 'Costo' => $row1[$i]['Costo'], 'Cantidad' => $row1[$i]['Cantidad'], 'Subtotal' => $row1[$i]['Subtotal']);
							}
						}
					}

					$array = array(
						'ID_Orden_Compra' => $row[0]['ID_Orden_Compra'], 
						'FK_Proveedor' => $row[0]['Proveedor'],
						'FK_Sucursal' => $row[0]['FK_Sucursal'], 
						'Razon_Social' => $row[0]['Razon_Social'], 
						'RFC' => $row[0]['RFC'], 
						'Credito' => $row[0]['Credito'], 
						'Adeudo' => $row[0]['Adeudo'], 
						'Restante' => ($row[0]['Credito'] - $row[0]['Adeudo']), 
						'Descuento' => $row[0]['Descuento'], 
						'Total' => $row[0]['Total'], 
						'Estatus' => $row[0]['Estatus'], 
						'Fecha_Registro' => $row[0]['Fecha_Registro'], 
						'Productos' => $productos
					);
				}
			}

			echo json_encode($array);
		}
	}

	public function _modificar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s'); 
		$tipo =  $omodelo->link->real_escape_string($tipo);

		if($tipo == 'insertarOrden'){
			$sucursal = $omodelo->link->real_escape_string($sucursal);
			$idProveedor = $omodelo->link->real_escape_string($idProveedor);
			$total = $omodelo->link->real_escape_string($total);
			$descuento = $omodelo->link->real_escape_string($descuento);
			
			$query = "INSERT INTO ordenes_compra SET FK_Proveedor= '$idProveedor', Total= '$total', Estatus= 'Pendiente', Fecha_Registro = '$fecha', Descuento = '$descuento', FK_Sucursal = '$sucursal', FK_Usuario = '".$_SESSION['user_punto_venta']['ID_Usuario']."'";
			$error = $omodelo->_insertar($query);

			if ($error == "si") {
				echo "Error 1: ".mysqli_error($omodelo->link);
			}else{
				$id = mysqli_insert_id($omodelo->link);

				$productos = json_decode($productos, true);

				foreach ($productos as $producto) {
					$producto['id'] = $omodelo->link->real_escape_string($producto['id']);
					$producto['descripcion'] = $omodelo->link->real_escape_string($producto['descripcion']);
					$producto['costo'] = $omodelo->link->real_escape_string($producto['costo']);
					$producto['cantidad'] = $omodelo->link->real_escape_string($producto['cantidad']);
					$producto['subtotal'] = $omodelo->link->real_escape_string($producto['subtotal']);
					
					$query3 = "INSERT INTO detalles_orden SET FK_Orden = '$id', FK_Producto = '$producto[id]', Descripcion = '$producto[descripcion]', Costo = '$producto[costo]', Cantidad = '$producto[cantidad]', Subtotal = '$producto[subtotal]'";
					$error3 = $omodelo->_insertar($query3);

					if ($error3 == "si") {
						echo "Error detalles: ".mysqli_error($omodelo->link);
					}
				}

				echo 'Correcto~'.$id;

				$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
			}
		}else if($tipo == 'modificarOrden'){
			$id = $omodelo->link->real_escape_string($id);
			$sucursal = $omodelo->link->real_escape_string($sucursal);
			$idProveedor = $omodelo->link->real_escape_string($idProveedor);
			$total = $omodelo->link->real_escape_string($total);
			$descuento = $omodelo->link->real_escape_string($descuento);
			
			$query = "UPDATE ordenes_compra SET FK_Proveedor = '$idProveedor', Total = '$total', Descuento = '$descuento', FK_Sucursal = '$sucursal' WHERE ID_Orden_Compra = '$id'";
			$error = $omodelo->_insertar($query);

			if ($error == "si") {
				echo "Error 1: ".mysqli_error($omodelo->link);
			}else{
				$query1 = "DELETE FROM detalles_orden WHERE FK_Orden = '$id'";
				$error1 = $omodelo->_insertar($query1);

				if ($error1 == "si") {
					echo "Error 2: ".mysqli_error($omodelo->link);
				}else{
					$productos = json_decode($productos, true);

					foreach ($productos as $producto) {
						$producto['id'] = $omodelo->link->real_escape_string($producto['id']);
						$producto['descripcion'] = $omodelo->link->real_escape_string($producto['descripcion']);
						$producto['costo'] = $omodelo->link->real_escape_string($producto['costo']);
						$producto['cantidad'] = $omodelo->link->real_escape_string($producto['cantidad']);
						$producto['subtotal'] = $omodelo->link->real_escape_string($producto['subtotal']);
						
						$query3 = "INSERT INTO detalles_orden SET FK_Orden = '$id', FK_Producto = '$producto[id]', Descripcion = '$producto[descripcion]', Costo = '$producto[costo]', Cantidad = '$producto[cantidad]', Subtotal = '$producto[subtotal]'";
						$error3 = $omodelo->_insertar($query3);

						if ($error3 == "si") {
							echo "Error detalles: ".mysqli_error($omodelo->link);
						}
					}
				}

				echo "Correcto~".$id;

				$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
			}
		}
	}

	public function _eliminar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$tipo =  $omodelo->link->real_escape_string($tipo);

		if($tipo == 'ordenCompra'){
			$id = $omodelo->link->real_escape_string($id);
			
			$query = "DELETE FROM ordenes_compra WHERE ID_Orden_Compra = '$id'";
			$error = $omodelo->_insertar($query);

			if ($error == "si") {
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				echo "Correcto";

				$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
			}
		}
	}
}
?>
