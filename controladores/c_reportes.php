<?php
class reportes {

	public function _consultar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$tipo = $omodelo->link->real_escape_string($tipo);
		$arreglo = array();

		if($tipo == 'productos'){
			$fechaInicio = $omodelo->link->real_escape_string($fechaInicio);
			$fechaFin = $omodelo->link->real_escape_string($fechaFin);

			$query = "SELECT ID_Detalle_Venta, Foto, detalles_ventas.Descripcion AS Producto, SUM(Cantidad) AS Cantidad FROM detalles_ventas INNER JOIN ventas ON FK_Venta = ID_Venta LEFT JOIN productos ON FK_Producto = ID_Producto WHERE Estatus = 0 AND (DATE_FORMAT(ventas.Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(ventas.Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') GROUP BY detalles_ventas.Descripcion ORDER BY Cantidad DESC LIMIT 10";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					for($i=0; $i<$numerofilas; $i++){
						$foto = 'vistas/assets/images/producto-generico.png';

						if($row[$i]['Foto'] != "" && file_exists("vistas/assets/images/productos/".$row[$i]['Foto'])){
							$foto = 'vistas/assets/images/productos/'.$row[$i]['Foto'];	
						}

						$arreglo[$i] = array('Foto' => $foto, 'Producto' => $row[$i]['Producto'], 'Cantidad' => $row[$i]['Cantidad']);	
					}
				}
			}
		}else if($tipo == 'ventas'){
			$fechaInicio = $omodelo->link->real_escape_string($fechaInicio);
			$fechaFin = $omodelo->link->real_escape_string($fechaFin);

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
		        $busqueda .= "CONCAT(ID_Venta, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), Total, Tipo_Pago, cajas.Nombre) REGEXP '" . $separa[$i] . "'";
		        if ($i < (count($separa) - 1)) {
		          $busqueda .= ' AND ';
		        }
		      }
		    }

		    $query = "SELECT 
		    	ID_Venta, 
		    	IFNULL(Total - IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas WHERE FK_Venta = ID_Venta), 0), 0) AS Ganancia, IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas WHERE FK_Venta = ID_Venta), 0) AS Total_Costo, 
		    	IFNULL((SELECT SUM(Total) FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda), 0) AS SumTotal, 
		    	IFNULL((SELECT SUM(IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas WHERE FK_Venta = ID_Venta), 0)) FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda), 0) AS SumCosto, 
		    	(IFNULL((SELECT SUM(Total) FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda), 0) - IFNULL((SELECT SUM(IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas WHERE FK_Venta = ID_Venta), 0)) FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda), 0)) AS SumGanancia,
		    	ID_Venta AS Folio, 
		    	Total, 
		    	Tipo_Pago, 
		    	Fecha_Registro AS Fecha, 
		    	DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, 
		    	Estatus, 
		    	cajas.Nombre AS Caja, 
		    	(SELECT COUNT(*) FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda) AS Num 

		    	FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
		    $row = $omodelo->_consultar($query);
		    $numerofilas = $omodelo->numerofilas;

		    if ($row == 'si') {
		      echo 'Error: ' . mysqli_error($omodelo->link);
		    }else{
		      if ($numerofilas > 0){
		        for ($i = 0; $i < $numerofilas; $i++) {
		          $arreglo['data'][$i] = array(
		            'ID' => $row[$i]['ID_Venta'],
		            'Fecha' => $row[$i]['Fecha_Registro'],
		            'Caja' => $row[$i]['Caja'],
		            'Folio' => $row[$i]['Folio'],
		            'Tipo_Pago' => $row[$i]['Tipo_Pago'],
		            'Total' => '<span class="dinero">' . $row[$i]['Total'] . '</span>',
		            'Total_Costo' => '<span class="dinero">' . $row[$i]['Total_Costo'] . '</span>',
		            'Ganancia' => '<span class="dinero">' . $row[$i]['Ganancia'] . '</span>'
		          );
		        }

		        $arreglo['totales'] = array('NumRows' => $row[0]['Num'], 'Costo' => '<span class="dinero">'.$row[0]['SumCosto'].'</span>', 'Total' => '<span class="dinero">'.$row[0]['SumTotal'].'</span>', 'Ganancia' => '<span class="dinero">'.$row[0]['SumGanancia'].'</span>');
		      }
		    }
		}else if($tipo == 'chartVentas'){
			$fechaInicio = $omodelo->link->real_escape_string($fechaInicio);
			$fechaFin = $omodelo->link->real_escape_string($fechaFin);

			$query = "SELECT COUNT(*) AS Cantidad, DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') AS Fecha FROM ventas WHERE Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') GROUP BY Fecha";
		    $row = $omodelo->_consultar($query);
		    $numerofilas = $omodelo->numerofilas;

		    if ($row == 'si') {
		      echo 'Error: ' . mysqli_error($omodelo->link);
		    }else{
			    if ($numerofilas > 0){
			    	for ($i=0; $i < $numerofilas; $i++) { 
			    		$arreglo[$i] = array('Fecha' => $row[$i]['Fecha'], 'Cantidad' => $row[$i]['Cantidad']);
			    	}
			    }
		  	}
		}

		echo json_encode($arreglo);
	}
}
?>