<?php
class compras {

	public function _consultar(){
		$omodelo = new m_modelo();
		extract($_POST);

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
				$busqueda .= "CONCAT(
					DATE_FORMAT(compras.Fecha_Registro, '%d-%m-%Y %r'), 
					ID_Compra, 
					Razon_Social, 
					RFC, 
					proveedores.Email, 
					proveedores.Telefono, 
					Total, 
					Estatus, 
					Tipo_Compra, 
					IFNULL((SELECT Nombre FROM sucursales WHERE FK_Sucursal = ID_Sucursal), 'NA')
				) REGEXP '".$separa[$i]."'";
				if($i < (count($separa)-1)){
					$busqueda .= ' AND ';
				}
			}
		}

		$query = "SELECT 
			ID_Compra, 
			ID_Compra AS Datos, 
			FK_Sucursal, 
			IFNULL((SELECT Nombre FROM sucursales WHERE FK_Sucursal = ID_Sucursal), 'NA') AS Sucursal, 
			FK_Usuario, 
			(SELECT usuarios.Nombre FROM usuarios WHERE ID_Usuario = FK_Usuario) AS NombreUsuario, 
			FK_Proveedor, 
			RFC, 
			proveedores.Email AS Email, 
			proveedores.Telefono AS Telefono, 
			Razon_Social, 
			Tipo_Compra, 
			Total, 
			Estatus, 
			compras.Fecha_Registro AS Fecha, 
			DATE_FORMAT(compras.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, 
			IFNULL((SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = ID_Compra), 0) AS Pagado, 
			(SELECT COUNT(*) FROM compras INNER JOIN proveedores ON FK_Proveedor = ID_Proveedor $busqueda) AS Num 
		FROM compras INNER JOIN proveedores ON FK_Proveedor = ID_Proveedor $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET ".(($pagina * $limit) - $limit);
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if($row == 'si'){
			echo "Error: ".mysqli_error($omodelo->link);
		}else{
			if($numerofilas > 0){
				$sumaCompras = 0;

				for($i=0; $i<$numerofilas; $i++){
					$motivocancelada = ''; $botonCancelar = ''; $botonPagos = ''; $verPagos = '';

					$estatus = "";
					if ($row[$i]['Estatus'] == "Completada") {
						$estatus = '<span class="badge rounded-pill bg-success">Completada</span>';
						
						if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Compras'][4] == '1') {
							$botonCancelar = ' <button class="btn btn-warning btn-sm bCancelarCompra" attrID="'.$row[$i]['ID_Compra'].'" folio="'.$row[$i]['Datos'].'" title="Cancelar"><i style="color: white;" class="fas fa-circle-xmark"></i></button> ';
						}
						
						$sumaCompras += $row[$i]['Total'];
					}else if($row[$i]['Estatus'] == "Cancelada"){
						$estatus = '<span class="badge rounded-pill bg-danger">Cancelada</span>';
						//$motivocancelada="Fecha de cancelación: <b>".$row[$i]['Fecha_Cancelada']."</b><br>Motivo: ".$row[$i]['Motivo_Cancelada']; 
					}else if ($row[$i]['Estatus'] == "Pendiente") {
						$estatus = '<span class="badge rounded-pill bg-warning">Pendiente</span>';

						if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Compras'][4] == '1') {
							$botonCancelar = ' <button class="btn btn-warning btn-sm bCancelarCompra" attrID="'.$row[$i]['ID_Compra'].'" folio="'.$row[$i]['Datos'].'" title="Cancelar"><i style="color: white;" class="fas fa-circle-xmark"></i></button> ';
						}

						$sumaCompras += $row[$i]['Total'];
					}

					$verPagos = '<br><button class="btn btn-link btn-sm bVerHistorialPagos" attrID="'.$row[$i]['ID_Compra'].'" folio="'.$row[$i]['Datos'].'">Ver pagos</button>';
					if (($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Compras'][5] == '1')) { //&& $row[$i]['Tipo_Compra'] == 'Crédito') {
						$botonPagos = '<button class="btn btn-primary btn-sm bPagoCom" attrID="'.$row[$i]['ID_Compra'].'" folio="'.$row[$i]['Datos'].'"><i class="fa-solid fa-sack-dollar" title="Realizar Pago"></i></button>';
					}

					$botonEliminar = "";
					if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Compras'][3] == '1') {
						$botonEliminar = '<button class="btn btn-danger btn-sm bEliminarCompra" attrID="'.$row[$i]['ID_Compra'].'" folio="'.$row[$i]['Datos'].'"><i class="fas fa-trash" title="Eliminar Venta"></i></button>';
					}

					$favor = 0;
					$restante = $row[$i]['Total'] - $row[$i]['Pagado'];
					if($restante < 0){
						$favor = $restante * -1;
						$restante = 0;
					}

					$arreglo['data'][$i] = array(
						'ID' => $row[$i]['ID_Compra'],
						'Fecha' => $row[$i]['Fecha_Registro'],
						'Datos' => '</b>Folio: <b>'.$row[$i]['Datos'].'</b><br>Tipo: <b>'.$row[$i]['Tipo_Compra'].'</b><br>Usuario: <b>'.$row[$i]['NombreUsuario'].'</b>',
						'Proveedor' => 'Razón social: <b>'.$row[$i]['Razon_Social'].'</b><br>RFC: <b>'.$row[$i]['RFC'].'</b><br>Teléfono: <b>'.$row[$i]['Telefono'].'</b><br>Email: <b>'.$row[$i]['Email'].'</b>',
						'Total' => '<span style="font-size: 18px;">Total: <b class="dinero">'.$row[$i]['Total'].'</b></span><br> Pago: <b class="dinero">'.$row[$i]['Pagado'].'</b><br>Restante: <b class="dinero">'.$restante.'</b><br>A favor: <b class="dinero">'.$favor.'</b>',
						'Detalles' => $estatus.'<br>'.$motivocancelada.'<br><button class="btn btn-link btn-sm bVerProductosCompra" attrID="'.$row[$i]['ID_Compra'].'" folio="'.$row[$i]['Datos'].'">Ver productos</button>'.$verPagos,
						'Sucursal' => $row[$i]['Sucursal'],
						'Acciones' => $botonEliminar.' '.$botonCancelar.' '.$botonPagos.' <button class="btn btn-success btn-sm bImprimirTicketCompra" attrID="'.$row[$i]['ID_Compra'].'" folio="'.$row[$i]['Datos'].'" title="Imprimir Ticket"><i class="fas fa-print"></i></button>',
					);
				}

				$arreglo['totales'] = array(
					'NumRows' => $row[0]['Num'],
					'Total' => '<b class="dinero">'.$sumaCompras.'</b>'
				);	
			}
		}

		echo json_encode($arreglo);
	}

	public function _insertar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s'); 

		$id =  $omodelo->link->real_escape_string($id);
		$importePago=  $omodelo->link->real_escape_string($importePagoCompra);
		$conceptoPago =  $omodelo->link->real_escape_string($conceptoPago);
		$tipoDePago =  $omodelo->link->real_escape_string($tipoDePago);
		$detallesPago =  $omodelo->link->real_escape_string($detallesPago);
		$cajaPago =  $omodelo->link->real_escape_string($cajaPago);
		
		$usuario = $_SESSION['user_punto_venta']['ID_Usuario'];

		$query = "INSERT INTO compras_pagos SET FK_Compra = '$id', FK_Detalle_Caja = IFNULL((SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$cajaPago' ORDER BY ID_Detalle_Caja DESC LIMIT 1), 0), Monto = '$importePago', Concepto = '$conceptoPago', Tipo_Pago = '$tipoDePago', Fecha_Registro = '$fecha', FK_Usuario = '$usuario', Detalles = '$detallesPago'";
		$error = $omodelo->_insertar($query);

		if ($error == "si") {
			echo "Error 1: ".mysqli_error($omodelo->link);
		}else{
			$idPago = mysqli_insert_id($omodelo->link);

			$status = 1;
			$ruta = "vistas/assets/files/pagos/";
			if ($_FILES['comprobantePago']['size'] > 0 && $_FILES['comprobantePago']['error'] == 0) {
				$file = $_FILES["comprobantePago"];
				$nombreDoc = $file["name"];
				$tipo = $file["type"];
				$ruta_provisional = $file["tmp_name"];
				$size = $file["size"];

				if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'application/pdf' && $tipo != ''){
					echo "Error 2 Formato";
				}else if ($size > (1024*1024*10)){
					echo "Error 3 Peso";
				}else{
					$status = 0;
				}
			}
			
			if($status == 0){
				$bd = $_SESSION['user_punto_bd'];
				$query2 = "UPDATE compras_pagos SET Archivo = '" . $bd . '_' . $idPago . '_' . $nombreDoc . "' WHERE ID_Pago = '$idPago'";
				$error3 = $omodelo->_insertar($query2);	

				if ($error3 == "si") {
					echo "Error 4: ".mysqli_error($omodelo->link); 
				}else{
					move_uploaded_file($ruta_provisional,  $ruta . $bd . '_' . $idPago . '_' . $nombreDoc);
				}
			}

			echo 'Correcto';

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}
	
	public function _modificar(){
		$omodelo = new m_modelo();
		extract($_POST); 
		$fecha = date('Y-m-d H:i:s'); 
		$id = $omodelo->link->real_escape_string($id);
		
		$query = "UPDATE compras SET Estatus = 'Cancelada' WHERE ID_Compra = '$id'";
		$error = $omodelo->_insertar($query);
		
		if ($error == "si") {
			echo "Error: ".mysqli_error($omodelo->link);
		}else{
			echo "Correcto";

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}	
	}

	public function _eliminar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$id = $omodelo->link->real_escape_string($id);

		$query = "SELECT Archivo FROM compras_pagos WHERE FK_Compra = '$id' AND Archivo != ''";
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($row == "si") {
			echo "Error 1: ".mysqli_error($omodelo->link);
		}else{
			if($numerofilas > 0){
				for ($i=0; $i < $numerofilas; $i++) { 
					if ($row[$i]["Archivo"] != "" && file_exists("vistas/assets/files/pagos/".$row[$i]["Archivo"]."")) {
						unlink("vistas/assets/files/pagos/".$row[$i]["Archivo"]."");
					}
				}
			}

			$query1 = "DELETE FROM compras WHERE ID_Compra = '$id'";
			$error = $omodelo->_insertar($query1);

			if ($error == "si") {
				echo "Error 2: ".mysqli_error($omodelo->link);
			}else{
				echo "Correcto";

				$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
			}	
		}
	}

	public function _detalles(){
		$omodelo = new m_modelo();
		extract($_POST);
		$tipo = $omodelo->link->real_escape_string($tipo);
		
		if($tipo == 'productos'){
			$id = $omodelo->link->real_escape_string($id);

			$query = "SELECT ID_Detalle_Compra, FK_Compra, Codigo, detalle_compras.Descripcion AS Descripcion, Foto, detalle_compras.Costo AS Costo, Cantidad, Subtotal FROM detalle_compras LEFT JOIN productos ON FK_Producto = ID_Producto WHERE FK_Compra = '$id'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;
			$tabla = "";

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					for($i=0; $i<$numerofilas; $i++){
						$imagen = '<a href="vistas/assets/images/producto-generico.png" data-fancybox="images">
										<div style="background-image: url('."'".'vistas/assets/images/producto-generico.png'."'".'); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
										</div>
									</a>';
						if ($row[$i]["Foto"] != "" && file_exists("vistas/assets/images/productos/".$row[$i]["Foto"])) {
							$imagen = '<a href="vistas/assets/images/productos/'.$row[$i]["Foto"].'" data-fancybox="images">
								<div style="background-image: url('."'".'vistas/assets/images/productos/'.$row[$i]["Foto"]."'".'); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
								</div>
							</a>';
						}

						$tabla .= '<tr>
							<td>'.$imagen.$row[$i]["Codigo"].'</td>
							<td>'.$row[$i]["Descripcion"].'</b></td>
							<td><span class="dinero">'.$row[$i]["Costo"].'</span></td>
							<td><span class="cantidad">'.$row[$i]["Cantidad"].'</span></td>
							<td><span class="dinero">'.$row[$i]["Subtotal"].'</span></td>
						</tr>';
					}
				}
			}

			echo $tabla;
		}else if($tipo == 'pago'){
			$id = $omodelo->link->real_escape_string($id);

			$query = "SELECT FK_Proveedor, Razon_Social, Total, IFNULL((SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = '$id'), 0) AS TotalPagos FROM compras INNER JOIN proveedores ON ID_Proveedor = FK_Proveedor WHERE ID_Compra = '$id'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					echo json_encode($row[0]);
				}
			}
		}else if($tipo == 'historialPagos'){
			$id = $omodelo->link->real_escape_string($id);

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
					$busqueda .= "CONCAT(DATE_FORMAT(compras_pagos.Fecha_Registro, '%d-%m-%Y %r'), Concepto, Monto, Tipo_Pago, Detalles, cajas.Nombre, usuarios.Nombre, Primer_Apellido, Segundo_Apellido) REGEXP '".$separa[$i]."'";
					if($i < (count($separa)-1)){
						$busqueda .= ' AND ';
					}
				}
			}
			
			$query = "SELECT ID_Pago, FK_Compra, Concepto, Monto, Tipo_Pago, compras_pagos.Fecha_Registro AS Fecha, DATE_FORMAT(compras_pagos.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, compras_pagos.Detalles AS Detalles, Archivo, cajas.Nombre AS Caja, CONCAT(usuarios.Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) AS Usuario, (SELECT COUNT(*) FROM compras_pagos LEFT JOIN detalles_caja ON FK_Detalle_Caja = ID_Detalle_Caja LEFT JOIN cajas ON FK_Caja = ID_Caja LEFT JOIN usuarios ON compras_pagos.FK_Usuario = ID_Usuario WHERE FK_Compra = '$id' $busqueda) AS Num FROM compras_pagos LEFT JOIN detalles_caja ON FK_Detalle_Caja = ID_Detalle_Caja LEFT JOIN cajas ON FK_Caja = ID_Caja LEFT JOIN usuarios ON compras_pagos.FK_Usuario = ID_Usuario WHERE FK_Compra = '$id' $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET ".(($pagina * $limit) - $limit);
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					for($i=0; $i<$numerofilas; $i++){
						$imagen = '';
						if ($row[$i]["Archivo"] != "" && file_exists("vistas/assets/files/pagos/".$row[$i]["Archivo"])) {
							$imagen = '<a href="vistas/assets/files/pagos/'.$row[$i]["Archivo"].'" data-fancybox="iframe">
								<i class="fas fa-file" style="font-size: 25px;"></i>
							</a>';	
						}

						$detalles = '';
						if($row[$i]['Caja'] != ''){
			             	$detalles .= '<br><span class="text-muted">Caja:</span> '.$row[$i]['Caja'];
			            }

			            if(trim($row[$i]['Usuario']) != ''){
			              	$detalles .= '<br><span class="text-muted">Usuario:</span> '.$row[$i]['Usuario'];
			            }
						
						$arreglo['data'][$i] = array(
							'ID' => $row[$i]['ID_Pago'],
							'Fecha' => $row[$i]["Fecha_Registro"],
							'Concepto' => $row[$i]["Concepto"],
							'TipoPago' => $row[$i]["Tipo_Pago"],
							'Monto' => '<span class="dinero">'.$row[$i]["Monto"].'</span>',
							'Detalles' => $row[$i]["Detalles"].$detalles,
							'Comprobante' => $imagen,
							'Accion' => '<button class="btn btn-danger btn-sm bEliminarPago" attrID="'.$row[$i]['ID_Pago'].'" idCompra="'.$row[$i]['FK_Compra'].'" archivo="'.$row[$i]["Archivo"].'"><i class="fas fa-trash"></i></button>'
						);
					}

					$arreglo['totales'] = array('NumRows' => $row[0]['Num']);
				}
			}

			echo json_encode($arreglo);
		}else if($tipo == 'eliminarPago'){
			$id = $omodelo->link->real_escape_string($id);	
			$archivo = $omodelo->link->real_escape_string($archivo);

			$query = "DELETE FROM compras_pagos WHERE ID_Pago = '$id'";
			$error = $omodelo->_insertar($query);

			if ($error == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($archivo != "" && file_exists("vistas/assets/files/pagos/".$archivo)) {
					unlink("vistas/assets/files/pagos/".$archivo);
				}

				echo "Correcto";
				$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
			}
		}else if($tipo == 'inventario'){
			$id = $omodelo->link->real_escape_string($id);

			$query = "SELECT ID_Detalle_Compra, FK_Producto, Costo, Cantidad, Subtotal, FK_Sucursal FROM detalle_compras INNER JOIN compras ON FK_Compra = ID_Compra WHERE FK_Compra = '$id'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error 1: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					for($i=0; $i<$numerofilas; $i++){
						$query1 = "UPDATE inventario SET Cantidad = Cantidad - ".$row[$i]['Cantidad']." WHERE FK_Producto = '".$row[$i]['FK_Producto']."'";
						$error = $omodelo->_insertar($query1);

						if ($error == "si") {
							echo "Error 2: ".mysqli_error($omodelo->link);
						}	

						if($row[$i]['FK_Sucursal'] != '' && $row[$i]['FK_Sucursal'] != 0){
							$query1 = "UPDATE detalles_inventario SET Cantidad = Cantidad - ".$row[$i]['Cantidad']." WHERE FK_Inventario = IFNULL((SELECT ID_Inventario FROM inventario WHERE FK_Producto = '".$row[$i]['FK_Producto']."'), 0) AND FK_Sucursal = '".$row[$i]['FK_Sucursal']."'";
							$error = $omodelo->_insertar($query1);

							if ($error == "si") {
								echo "Error 3: ".mysqli_error($omodelo->link);
							}
						}
					}
				}

				echo "Correcto";
			}
		}
	}
}
?>
