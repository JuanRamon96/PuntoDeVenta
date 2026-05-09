<?php  
class ventas{
	
	public function _consultar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s');

		$buscar = preg_replace('([^A-Za-z0-9])', '', $omodelo->link->real_escape_string($buscar));

		$busqueda = '';
	    if (trim($buscar) != '') {
	      	$separa = explode(' ', trim($buscar));
	      	$busqueda = 'WHERE ';
	      	for ($i = 0; $i < count($separa); $i++) {
	        	$busqueda .= "CONCAT(Codigo, Descripcion, Precio) REGEXP '" . $separa[$i] . "'";
	        	if ($i < (count($separa) - 1)) {
	          		$busqueda .= ' AND ';
	        	}
	      	}
	    }

		$productos = '';
		$query = "SELECT ID_Producto, Codigo, Descripcion, Precio, Foto FROM productos $busqueda";
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($row == 'si') {
			echo "Error: " . mysqli_error($omodelo->link);
		}else{
			if($numerofilas > 0){
				for ($i=0; $i < $numerofilas; $i++) { 
					$foto = 'vistas/assets/images/fondo.jpg';

				    if ($row[$i]['Foto'] != '' && file_exists('../vistas/assets/images/productos/' . $row[$i]['Foto'])) {
				        $foto = '../vistas/assets/images/productos/'.$row[$i]['Foto'];
				    }

					$productos .= '<div attrID="'.$row[$i]['ID_Producto'].'" class="producto col-4" style="background-image: url('."'".$foto."'".');">
						<p class="tituloProd"><span><b>Cod. </b>'.$row[$i]['Codigo'].'</span><br><span>'.$row[$i]['Descripcion'].'</span></p>
						<b class="precioProd dinero">'.$row[$i]['Precio'].'</b>
					</div>';
				}
			}
		}

		echo $productos;
	}

	public function _insertar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s');

		$metodoPago = $omodelo->link->real_escape_string($metodoPago);
		$pago = $omodelo->link->real_escape_string($pago);
		$detalles = $omodelo->link->real_escape_string($detalles);
		$total = $omodelo->link->real_escape_string($total);
		$cambio = $omodelo->link->real_escape_string($cambio);

		$query = "INSERT INTO ventas SET Total = '$total', Tipo_Pago = '$metodoPago', Pago = '$pago', Cambio = '$cambio', Detalles = '$detalles', Fecha_Registro = '$fecha', Estatus = 1, FK_Usuario = '".$_SESSION['user_tablet_stazione']['ID_Usuario']."'";
		$error = $omodelo->_insertar($query);

		if ($error == 'si') {
			echo "Error 1: " . mysqli_error($omodelo->link);
		}else{
			$id = mysqli_insert_id($omodelo->link);

			$detallesVentas = json_decode($detallesVentas, true);
			foreach ($detallesVentas as $detalle) {
				$detalle['id'] = $omodelo->link->real_escape_string($detalle['id']);
				$detalle['nombre'] = $omodelo->link->real_escape_string($detalle['nombre']);
				$detalle['precio'] = $omodelo->link->real_escape_string($detalle['precio']);
				$detalle['cantidad'] = $omodelo->link->real_escape_string($detalle['cantidad']);
				$detalle['total'] = $omodelo->link->real_escape_string($detalle['total']);

				$query1 = "INSERT INTO detalles_ventas SET FK_Venta = '$id', FK_Producto = '$detalle[id]', Descripcion = '$detalle[nombre]', Precio = '$detalle[precio]', Cantidad = '$detalle[cantidad]', Total = '$detalle[total]'";
				$error1 = $omodelo->_insertar($query1);

				if ($error1 == 'si') {
					echo "Error 2: " . mysqli_error($omodelo->link);
				}
			}

			echo "Correcto~$id";

			$omodelo->movimiento($query, $_SESSION['user_tablet_stazione']['ID_Usuario']);
		}
	}

	public function _modificar() {
	    $omodelo = new m_modelo();
	    extract($_POST);
	    $fecha = date('Y-m-d H:i:s');
	    $tipo = $omodelo->link->real_escape_string($tipo);

	    if($tipo == "cancelar"){ 
		    $idVenta = $omodelo->link->real_escape_string($idVenta);
		    $regresarInventario = $omodelo->link->real_escape_string($regresarInventario);

		    $query = "UPDATE ventas SET Estatus = 2, Fecha_Cancelacion = '$fecha', Regrezo_Inventario = '$regresarInventario' WHERE ID_Venta = '$idVenta'";
		    $error = $omodelo->_insertar($query);

		    if ($error == 'si') {
		      	echo 'Error 1: ' . mysqli_error($omodelo->link);
		    }else{
			    if ($regresarInventario == '1') {
			        $query1 = "SELECT FK_Producto, Cantidad FROM detalles_ventas WHERE FK_Venta = '$idVenta'";
			        $row = $omodelo->_consultar($query1);
			        $numerofilas = $omodelo->numerofilas;

			        if($row == 'si'){
			          	echo 'Error 2: ' . mysqli_error($omodelo->link);
			        }else{
			          	if($numerofilas > 0){
			            	for ($i=0; $i < $numerofilas; $i++) { 
			              		$query2 = "UPDATE inventario SET Cantidad = Cantidad + " . $row[$i]['Cantidad'] . " WHERE FK_Producto = '".$row[$i]['FK_Producto']."'";
			              		$error2 = $omodelo->_insertar($query2);

			              		if ($error2 == 'si') {
			                		echo 'Error 3: ' . mysqli_error($omodelo->link);
			              		}
			            	}
			          	}
			        }
			    }

		      	echo 'Correcto';

		      	$omodelo->movimiento($query, $_SESSION['user_app_stazione']['ID_Usuario']);
		    }
		}else if($tipo == 'agregar'){
	      	$id = $omodelo->link->real_escape_string($id);
	     	$codigo = $omodelo->link->real_escape_string($codigo);
	      	$cantidad = $omodelo->link->real_escape_string($cantidad);
	      	$precio = $omodelo->link->real_escape_string($precio);
	      	$descuento = $omodelo->link->real_escape_string($descuento);

      		$query = "SELECT ID_Producto, Descripcion, Precio, IFNULL((SELECT COUNT(*) FROM detalles_ventas INNER JOIN productos ON FK_Producto = ID_Producto WHERE FK_Venta = '$id' AND Codigo = '$codigo'), '0') AS Encontrado FROM productos WHERE Codigo = '$codigo'";
      		$row = $omodelo->_consultar($query);
      		$numerofilas = $omodelo->numerofilas;

      		if($row == 'si'){
        		echo "Error 1: " . mysqli_error($omodelo->link);
      		}else{
        		$total = ($precio * $cantidad) - $descuento;
        		if ($numerofilas > 0) {
          			$total = ($row[0]['Precio'] * $cantidad) - $descuento;
        		}

        		$query1 = "UPDATE ventas SET Total = Total + $total, Cambio = Pago - Total WHERE ID_Venta = '$id'";
        		$error = $omodelo->_insertar($query1);

        		if($error == 'si'){
          			echo "Error 2: " . mysqli_error($omodelo->link);
        		}else{  
          			if($numerofilas > 0){
            			if($row[0]['Encontrado'] == 0){
              				$query2 = "INSERT INTO detalles_ventas SET FK_Venta = '$id', Cantidad = '$cantidad', Descuento = '$descuento', Total = '$total', Descripcion = '".$row[0]['Descripcion']."', Precio = '".$row[0]['Precio']."', FK_Producto = '".$row[0]['ID_Producto']."'";
            			}else{
              				$query2 = "UPDATE detalles_ventas SET Cantidad = Cantidad + '$cantidad', Descuento = '$descuento', Precio = '".$row[0]['Precio']."', Total = (Cantidad * Precio) - Descuento, Descripcion = '".$row[0]['Descripcion']."' WHERE FK_Venta = '$id' AND FK_Producto = '".$row[0]['ID_Producto']."'";
            			}
            			$error1 = $omodelo->_insertar($query2);

            			if($error1 == 'si'){
              				echo "Error 3: " . mysqli_error($omodelo->link);
            			}else{
              				echo "Correcto";

              				$omodelo->movimiento($query2, $_SESSION['user_app_stazione']['ID_Usuario']);
            			}
          			}else{
            			$query1 = "INSERT INTO detalles_ventas SET FK_Venta = '$id', Descripcion = '$codigo', Cantidad = '$cantidad', Precio = '$precio', Descuento = '$descuento', Total = '$total'";
            			$error = $omodelo->_insertar($query1);

            			if($error == 'si'){
              				echo "Error 2: " . mysqli_error($omodelo->link);
            			}else{
              				echo "Correcto";

              				$omodelo->movimiento($query1, $_SESSION['user_app_stazione']['ID_Usuario']);
            			}
          			}
        		}
      		}
    	}
	}

	public function _detalles(){
	    $omodelo = new m_modelo();
	    extract($_POST);
	    $arreglo = array();
	    $tipo = $omodelo->link->real_escape_string($tipo);

    	if ($tipo == 'ventas') {
	      	$buscar = trim($omodelo->link->real_escape_string($buscar));
	     	$limit = $omodelo->link->real_escape_string($limit);
	      	$pagina = $omodelo->link->real_escape_string($pagina);
	      	$ordenColumna = $omodelo->link->real_escape_string($ordenColumna);
	      	$orden = $omodelo->link->real_escape_string($orden);

      		$busqueda = '';
      		if (trim($buscar) != '') {
        		$separa = explode(' ', trim($buscar));
        		$busqueda = 'AND ';
        		for ($i = 0; $i < count($separa); $i++) {
          			$busqueda .= "CONCAT(LPAD(ID_Venta, 6, '0'), DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), Total, Pago, Cambio, Tipo_Pago, Detalles) REGEXP '" . $separa[$i] . "'";
          			if ($i < (count($separa) - 1)) {
            			$busqueda .= ' AND ';
          			}
        		}
      		}

      		$query = "SELECT ID_Venta, LPAD(ID_Venta, 6, '0') AS Folio, Total, Pago, Cambio, Tipo_Pago, Fecha_Registro AS Fecha, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, Estatus, Detalles, (SELECT COUNT(*) FROM ventas WHERE Estatus = 1 $busqueda) AS Num FROM ventas WHERE Estatus = 1 $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      		$row = $omodelo->_consultar($query);
      		$numerofilas = $omodelo->numerofilas;

      		if ($row == 'si') {
        		echo 'Error 1: ' . mysqli_error($omodelo->link);
      		}else{
        		if ($numerofilas > 0){
          			for ($i = 0; $i < $numerofilas; $i++) {
            			$detalles = '<p>'.$row[$i]['Detalles'].'</p>
            			<table class="table" width="100%">
              				<thead>
				                <tr>
				                 	<th>Cod.</th>
				                  	<th>Producto</th>
				                  	<th>Cantidad</th>
				                  	<th>Precio</th>
				                  	<th>Descuento</th>
				                  	<th>Total</th>
				                  	<th>Acciones</th>
				                </tr>
              				</thead>
              				<tbody>';
            			$query1 = "SELECT ID_Detalle_Venta, IFNULL(Codigo, '') AS Codigo, detalles_ventas.Descripcion AS Descripcion, detalles_ventas.Precio AS Precio, Cantidad, Descuento, Total FROM detalles_ventas LEFT JOIN productos ON FK_Producto = ID_Producto WHERE FK_Venta = '".$row[$i]['ID_Venta']."'";
            			$row1 = $omodelo->_consultar($query1);
            			$numerofilas1 = $omodelo->numerofilas;

            			if ($row1 == 'si') {
              				echo 'Error 2: ' . mysqli_error($omodelo->link);
            			}else{
              				if ($numerofilas1 > 0){
                				for ($x = 0; $x < $numerofilas1; $x++) {
                  					$detalles .= '<tr>
                    					<td>'.$row1[$x]['Codigo'].'</td>
                    					<td>'.$row1[$x]['Descripcion'].'</td>
                    					<td><span class="cantidad">'.$row1[$x]['Cantidad'].'</span></td>
                    					<td><span class="dinero">'.$row1[$x]['Precio'].'</span></td>
                    					<td><span class="dinero">'.$row1[$x]['Descuento'].'</span></td>
                    					<td><span class="dinero">'.$row1[$x]['Total'].'</span></td>
                    					<td><button type="button" class="btn btn-sm btn-danger bQuitarProductoMas" attrID="'.$row1[$x]['ID_Detalle_Venta'].'" title="Quitar producto"><i class="fas fa-trash"></i></button></td>
                  					</tr>';
                				}
              				}
            			}

            			$detalles .= '<tr>
				                  	<td colspan="2"><input style="border-radius: 5px;" type="text" class="form-control form-control-sm" id="codigoProdMas" placeholder="Código/Prod. Comun"></td>
				                  	<td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="cantidadProdMas" placeholder="Cantidad" value="1"></td>
				                  	<td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="precioProdMas" placeholder="Precio" value="0"></td>
				                  	<td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="descuentoProdMas" placeholder="Descuento" value="0"></td>
				                  	<td></td>
				                  	<td><button type="button" class="btn btn-sm btn-success bAgregarProductoMas" attrID="'.$row[$i]['ID_Venta'].'" title="Agregar producto"><i class="fas fa-plus"></i></button></td>
		                		</tr>
	              			</tbody>
            			</table>';

            			$estatus = "<span class='badge rounded-pill bg-success'>Completada</span>";

			            if($row[$i]['Estatus'] == 1){ 
			              $estatus = "<span class='badge rounded-pill bg-warning'>Pendiente</span>";
			            }elseif($row[$i]['Estatus'] == 2){
			              $estatus = "<span class='badge rounded-pill bg-danger'>Cancelada</span>";
			            }

            			$arreglo['data'][$i] = array(
              				'ID' => $row[$i]['ID_Venta'],
              				'Fecha' => $row[$i]['Fecha_Registro'],
              				'Folio' => $row[$i]['Folio'],
              				'Tipo' => $row[$i]['Tipo_Pago'],
              				'Total' => '<span class="dinero">' . $row[$i]['Total'] . '</span>',
              				'Pago' => '<span class="dinero">' . $row[$i]['Pago'] . '</span>',
              				'Cambio' => '<span class="dinero">' . $row[$i]['Cambio'] . '</span>',
              				'Estatus' => $estatus,
              				'Detalles' => $detalles,
              				'Acciones' => '<button type="button" class="btn btn-sm btn-warning bCancelarVentaPen" attrID="'.$row[$i]['ID_Venta'].'" title="Cancelar venta"><i class="fas fa-ban"></i></button>'
            			);
         			}

          			$arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        		}
      		}
      
      		echo json_encode($arreglo);
    	}
  	}

  	public function _eliminar() {
	    $omodelo = new m_modelo();
	    extract($_POST);
	    $tipo = $omodelo->link->real_escape_string($tipo);
	    $fecha = date('Y-m-d H:i:s');

	    if($tipo == 'quitar'){
	      	$id = $omodelo->link->real_escape_string($id);
	      
	      	$query = "UPDATE ventas SET Total = Total - (SELECT Total FROM detalles_ventas WHERE ID_Detalle_Venta = '$id'), Cambio = Pago - Total WHERE ID_Venta = (SELECT FK_Venta FROM detalles_ventas WHERE ID_Detalle_Venta = '$id')";
	      	$error = $omodelo->_insertar($query);

	      	if ($error == 'si') {
	        	echo "Error 1: " . mysqli_error($omodelo->link);
	      	}else{
		        $query1 = "DELETE FROM detalles_ventas WHERE ID_Detalle_Venta = '$id'";
		        $error1 = $omodelo->_insertar($query1);

		        if ($error1 == 'si') {
		          	echo "Error 2: " . mysqli_error($omodelo->link);
		        }else{
		          	echo 'Correcto';

		          	$omodelo->movimiento($query1, $_SESSION['user_app_stazione']['ID_Usuario']);
		        }
	      	}
	    }
  	}
}
?>