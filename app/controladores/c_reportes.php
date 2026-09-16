<?php
class reportes {

	public function _consultar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$tipo = $omodelo->link->real_escape_string($tipo);
		$arreglo = array();

		if ($tipo == 'productos') {
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
		} else if($tipo == 'ventas') {
			$fechaInicio = $omodelo->link->real_escape_string($fechaInicio);
			$fechaFin = $omodelo->link->real_escape_string($fechaFin);
			$usuario = $omodelo->link->real_escape_string($usuario);
			$caja = $omodelo->link->real_escape_string($caja);

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
		        $busqueda .= "CONCAT(
					ID_Venta, 
					DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), 
					Total, 
					Tipo_Pago, 
					cajas.Nombre, 
					usuarios.Nombre, 
					usuarios.Primer_Apellido, 
					usuarios.Segundo_Apellido
				) REGEXP '" . $separa[$i] . "'";
		        if ($i < (count($separa) - 1)) {
		          $busqueda .= ' AND ';
		        }
		      }
		    }

			$where = '';
			if ($usuario != '') {
				$where = "AND FK_Usuario = '$usuario'";
			}

			if ($caja != '') {
				$where .= "AND FK_Caja = '$caja'";
			}

		    $query = "SELECT 
		    	ID_Venta, 
		    	IFNULL(Total - IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas WHERE FK_Venta = ID_Venta), 0), 0) AS Ganancia, 
				IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas WHERE FK_Venta = ID_Venta), 0) AS Total_Costo, 
		    	IFNULL((SELECT SUM(Total) FROM ventas INNER JOIN usuarios ON FK_Usuario = ID_Usuario LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE ventas.Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda $where), 0) AS SumTotal, 
		    	IFNULL((SELECT SUM(IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas WHERE FK_Venta = ID_Venta), 0)) FROM ventas INNER JOIN usuarios ON FK_Usuario = ID_Usuario LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE ventas.Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda $where), 0) AS SumCosto, 
		    	(IFNULL((SELECT SUM(Total) FROM ventas INNER JOIN usuarios ON FK_Usuario = ID_Usuario LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE ventas.Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda $where), 0) - IFNULL((SELECT SUM(IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas WHERE FK_Venta = ID_Venta), 0)) FROM ventas INNER JOIN usuarios ON FK_Usuario = ID_Usuario LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE ventas.Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda $where), 0)) AS SumGanancia,
		    	ID_Venta AS Folio, 
		    	Total, 
		    	Tipo_Pago, 
		    	Fecha_Registro AS Fecha, 
		    	DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, 
		    	ventas.Estatus AS Estatus, 
		    	cajas.Nombre AS Caja, 
				CONCAT(usuarios.Nombre, ' ', usuarios.Primer_Apellido, ' ', usuarios.Segundo_Apellido) AS Usuario,
		    	(SELECT COUNT(*) FROM ventas INNER JOIN usuarios ON FK_Usuario = ID_Usuario LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE ventas.Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda $where) AS Num 

		    	FROM ventas INNER JOIN usuarios ON FK_Usuario = ID_Usuario LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE ventas.Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $busqueda $where ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
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
		            'Ganancia' => '<span class="dinero">' . $row[$i]['Ganancia'] . '</span>',
					'Usuario' => $row[$i]['Usuario']
		          );
		        }

		        $arreglo['totales'] = array('NumRows' => $row[0]['Num'], 'Costo' => '<span class="dinero">'.$row[0]['SumCosto'].'</span>', 'Total' => '<span class="dinero">'.$row[0]['SumTotal'].'</span>', 'Ganancia' => '<span class="dinero">'.$row[0]['SumGanancia'].'</span>');
		      }
		    }
		} else if($tipo == 'chartVentas') {
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
		} else if($tipo == 'totalGastos'){
			$fechaInicio = $omodelo->link->real_escape_string($fechaInicio);
			$fechaFin = $omodelo->link->real_escape_string($fechaFin);
			$usuario = $omodelo->link->real_escape_string($usuario);
			$caja = $omodelo->link->real_escape_string($caja);

			$whereUsu = '';
			if ($usuario != '') {
				$whereUsu = "AND FK_Usuario = '$usuario'";
			}

			$whereCaja = '';
			if ($caja != '') {
				$whereCaja = "AND FK_Caja = '$caja'";
			}

			$query = "SELECT 
				IFNULL((SELECT SUM(Monto) FROM gastos WHERE (DATE_FORMAT(Fecha_Gasto, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Gasto, '%Y-%m-%d') <= '$fechaFin') $whereUsu), 0) AS TotalGastos,
				IFNULL((SELECT SUM(Total) FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE Estatus = 0 AND (DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $whereUsu $whereCaja), 0) AS TotalVentas,
				IFNULL((SELECT SUM(Costo * Cantidad) FROM detalles_ventas INNER JOIN ventas ON FK_Venta = ID_Venta LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja WHERE ventas.Estatus = 0 AND (DATE_FORMAT(ventas.Fecha_Registro, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(ventas.Fecha_Registro, '%Y-%m-%d') <= '$fechaFin') $whereUsu), 0) AS TotalCostoProductos";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($omodelo->link);
			}else{
				if($numerofilas > 0){
					$totalGastos = $row[0]['TotalGastos'];
					$totalVentas = $row[0]['TotalVentas'];
					$totalCostoProductos = $row[0]['TotalCostoProductos'];
					$gananciaVentas = $totalVentas - $totalCostoProductos;
					$diferencia = $gananciaVentas - $totalGastos;

					$arreglo = array(
						'TotalGastos' => '<span class="dinero">'.$totalGastos.'</span>',
						'TotalVentas' => '<span class="dinero">'.$totalVentas.'</span>',
						'TotalCostoProductos' => '<span class="dinero">'.$totalCostoProductos.'</span>',
						'GananciaVentas' => '<span class="dinero">'.$gananciaVentas.'</span>',
						'Diferencia' => '<span class="dinero">'.$diferencia.'</span>'
					);
				}
			}
		} else if ($tipo == 'pagos') {
			$cliente = isset($cliente) ? $omodelo->link->real_escape_string($cliente) : '';
			$buscar = isset($buscar) ? trim($omodelo->link->real_escape_string($buscar)) : '';
			$limit = (isset($limit) && $limit !== '') ? $omodelo->link->real_escape_string($limit) : '25';
			$pagina = (isset($pagina) && $pagina !== '') ? $omodelo->link->real_escape_string($pagina) : '1';
			$ordenColumna = isset($ordenColumna) ? $omodelo->link->real_escape_string($ordenColumna) : 'Fecha';
			$orden = isset($orden) ? $omodelo->link->real_escape_string($orden) : 'asc';

			$sqlRestante = "(ventas.Total - (ventas.Pago - ventas.Cambio) - IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ventas.ID_Venta), 0))";
			$sqlPagado = "((ventas.Pago - ventas.Cambio) + IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ventas.ID_Venta), 0))";
			$wherePendiente = "ventas.Estatus != 2 AND $sqlRestante > 0.009";

			$filtroCliente = '';
			if ($cliente != '' && $cliente != '0') {
				$filtroCliente = " AND ventas.FK_Cliente = '$cliente'";
			}

			$busqueda = '';
			if (trim($buscar) != '') {
				$separa = explode(' ', trim($buscar));
				$busqueda = ' AND ';
				for ($i = 0; $i < count($separa); $i++) {
					$busqueda .= "CONCAT(
						ventas.ID_Venta,
						DATE_FORMAT(ventas.Fecha_Registro, '%d-%m-%Y %r'),
						ventas.Total,
						ventas.Tipo_Pago,
						IFNULL(IF(clientes.Tipo = 'Moral', clientes.Razon_Social, CONCAT(clientes.Nombre, ' ', clientes.Primer_Apellido, ' ', clientes.Segundo_Apellido)), 'Publico en General')
					) REGEXP '" . $separa[$i] . "'";
					if ($i < (count($separa) - 1)) {
						$busqueda .= ' AND ';
					}
				}
			}

			$mapOrden = array(
				'Fecha' => 'ventas.Fecha_Registro',
				'Folio' => 'ventas.ID_Venta',
				'Cliente' => 'Cliente',
				'Tipo_Pago' => 'ventas.Tipo_Pago',
				'Total' => 'ventas.Total',
				'Pagado' => 'Pagado',
				'Restante' => 'Restante',
				'Estatus' => 'ventas.Estatus'
			);
			$ordenSql = isset($mapOrden[$ordenColumna]) ? $mapOrden[$ordenColumna] : 'ventas.Fecha_Registro';
			$ordenDir = strtolower($orden) == 'desc' ? 'DESC' : 'ASC';

			$query = "SELECT
				ventas.ID_Venta,
				ventas.ID_Venta AS Folio,
				DATE_FORMAT(ventas.Fecha_Registro, '%d-%m-%Y %r') AS Fecha,
				ventas.Tipo_Pago,
				ventas.Total,
				$sqlPagado AS Pagado,
				$sqlRestante AS Restante,
				ventas.Estatus,
				IFNULL(IF(clientes.Tipo = 'Moral', clientes.Razon_Social, CONCAT(clientes.Nombre, ' ', clientes.Primer_Apellido, ' ', clientes.Segundo_Apellido)), 'Publico en General') AS Cliente,
				(SELECT COUNT(*) FROM ventas LEFT JOIN clientes ON ventas.FK_Cliente = clientes.ID_Cliente WHERE $wherePendiente $filtroCliente $busqueda) AS Num,
				IFNULL((SELECT SUM(ventas.Total) FROM ventas LEFT JOIN clientes ON ventas.FK_Cliente = clientes.ID_Cliente WHERE $wherePendiente $filtroCliente $busqueda), 0) AS SumTotal,
				IFNULL((SELECT SUM($sqlPagado) FROM ventas LEFT JOIN clientes ON ventas.FK_Cliente = clientes.ID_Cliente WHERE $wherePendiente $filtroCliente $busqueda), 0) AS SumPagado,
				IFNULL((SELECT SUM($sqlRestante) FROM ventas LEFT JOIN clientes ON ventas.FK_Cliente = clientes.ID_Cliente WHERE $wherePendiente $filtroCliente $busqueda), 0) AS SumRestante
				FROM ventas
				LEFT JOIN clientes ON ventas.FK_Cliente = clientes.ID_Cliente
				WHERE $wherePendiente $filtroCliente $busqueda
				ORDER BY $ordenSql $ordenDir
				LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == 'si') {
				echo 'Error: ' . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$estatus = $row[$i]['Estatus'] == '1'
							? "<span class='badge rounded-pill bg-warning'>Pendiente</span>"
							: "<span class='badge rounded-pill bg-success'>Completada</span>";

						$bDetalles = '<br><button class="btn btn-link btn-sm bDetalles" idVenta="' . $row[$i]['ID_Venta'] . '" title="Ver Detalles">Ver Detalles</button>';
						$bVerPagos = '<br><button class="btn btn-link btn-sm bVerPagosVen" attrID="' . $row[$i]['ID_Venta'] . '" title="Ver Pagos">Ver Pagos</button>';

						$arreglo['data'][$i] = array(
							'Fecha' => $row[$i]['Fecha'],
							'Folio' => $row[$i]['Folio'] . $bDetalles . $bVerPagos,
							'Cliente' => $row[$i]['Cliente'],
							'Tipo_Pago' => $row[$i]['Tipo_Pago'],
							'Total' => '<span class="dinero">' . $row[$i]['Total'] . '</span>',
							'Pagado' => '<span class="dinero">' . $row[$i]['Pagado'] . '</span>',
							'Restante' => '<span class="dinero">' . $row[$i]['Restante'] . '</span>',
							'Estatus' => $estatus
						);
					}

					$arreglo['totales'] = array(
						'NumRows' => $row[0]['Num'],
						'Total' => '<span class="dinero">' . $row[0]['SumTotal'] . '</span>',
						'Pagado' => '<span class="dinero">' . $row[0]['SumPagado'] . '</span>',
						'Restante' => '<span class="dinero">' . $row[0]['SumRestante'] . '</span>'
					);
				}
			}
		} else if ($tipo == 'resumenPagos') {
			$cliente = isset($cliente) ? $omodelo->link->real_escape_string($cliente) : '';
			$sqlRestante = "(ventas.Total - (ventas.Pago - ventas.Cambio) - IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ventas.ID_Venta), 0))";
			$sqlPagado = "((ventas.Pago - ventas.Cambio) + IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ventas.ID_Venta), 0))";
			$wherePendiente = "ventas.Estatus != 2 AND $sqlRestante > 0.009";
			$filtroCliente = '';
			if ($cliente != '' && $cliente != '0') {
				$filtroCliente = " AND ventas.FK_Cliente = '$cliente'";
			}

			$query = "SELECT
				COUNT(*) AS Tickets,
				IFNULL(SUM(ventas.Total), 0) AS Total,
				IFNULL(SUM($sqlPagado), 0) AS Pagado,
				IFNULL(SUM($sqlRestante), 0) AS Adeudo
				FROM ventas
				WHERE $wherePendiente $filtroCliente";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == 'si') {
				echo 'Error: ' . mysqli_error($omodelo->link);
			} else if ($numerofilas > 0) {
				$arreglo = array(
					'Tickets' => $row[0]['Tickets'],
					'Total' => '<span class="dinero">' . $row[0]['Total'] . '</span>',
					'Pagado' => '<span class="dinero">' . $row[0]['Pagado'] . '</span>',
					'Adeudo' => '<span class="dinero">' . $row[0]['Adeudo'] . '</span>'
				);
			}
		} else if ($tipo == 'adeudoCliente') {
			$cliente = isset($cliente) ? $omodelo->link->real_escape_string($cliente) : '';
			$sqlRestante = "(ventas.Total - (ventas.Pago - ventas.Cambio) - IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ventas.ID_Venta), 0))";

			$nombre = '';
			if ($cliente != '' && $cliente != '0') {
				$queryNom = "SELECT IF(Tipo = 'Moral', Razon_Social, CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido)) AS Nombre FROM clientes WHERE ID_Cliente = '$cliente' LIMIT 1";
				$rowNom = $omodelo->_consultar($queryNom);
				if ($omodelo->error != 'si' && $omodelo->numerofilas > 0) {
					$nombre = $rowNom[0]['Nombre'];
				}
			}

			$query = "SELECT IFNULL(SUM($sqlRestante), 0) AS Adeudo
				FROM ventas
				WHERE ventas.Estatus != 2
				AND $sqlRestante > 0.009
				AND ventas.FK_Cliente = '$cliente'";
			$row = $omodelo->_consultar($query);

			if ($row == 'si') {
				echo 'Error: ' . mysqli_error($omodelo->link);
			} else {
				$adeudo = ($omodelo->numerofilas > 0) ? $row[0]['Adeudo'] : 0;
				$arreglo = array('Adeudo' => $adeudo, 'Cliente' => $nombre);
			}
		} else if ($tipo == 'cajasAbiertas') {
			$query = "SELECT ID_Caja, Nombre FROM cajas WHERE Estado = 1 ORDER BY Nombre";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == 'si') {
				echo 'Error: ' . mysqli_error($omodelo->link);
			} else if ($numerofilas > 0) {
				for ($i = 0; $i < $numerofilas; $i++) {
					$arreglo[$i] = array(
						'ID_Caja' => $row[$i]['ID_Caja'],
						'Nombre' => $row[$i]['Nombre']
					);
				}
			}
		}

		echo json_encode($arreglo);
	}

	public function _insertar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s');
		$tipo = $omodelo->link->real_escape_string($tipo ?? '');

		$esAdmin = isset($_SESSION['user_punto_venta']['Tipo_Usuario']) && $_SESSION['user_punto_venta']['Tipo_Usuario'] == '1';
		$puedeReportes = $esAdmin || (isset($_SESSION['user_punto_venta']['Permisos']['Reportes'][1]) && $_SESSION['user_punto_venta']['Permisos']['Reportes'][1] == '1');
		if (!$puedeReportes) {
			echo 'Error permiso';
			return;
		}

		if ($tipo != 'abono') {
			echo 'Error tipo';
			return;
		}

		$cliente = $omodelo->link->real_escape_string(trim($cliente ?? ''));
		$monto = round(floatval($monto ?? 0), 2);
		$tipoPago = $omodelo->link->real_escape_string(trim($tipoPago ?? 'Efectivo'));
		if ($tipoPago == '') {
			$tipoPago = 'Efectivo';
		}

		if ($cliente == '' || $cliente == '0') {
			echo 'Error cliente';
			return;
		}
		if ($monto <= 0) {
			echo 'Error monto';
			return;
		}

		$sqlRestante = "(ventas.Total - (ventas.Pago - ventas.Cambio) - IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ventas.ID_Venta), 0))";
		$queryAdeudo = "SELECT IFNULL(SUM($sqlRestante), 0) AS Adeudo
			FROM ventas
			WHERE ventas.Estatus != 2
			AND $sqlRestante > 0.009
			AND ventas.FK_Cliente = '$cliente'";
		$rowAdeudo = $omodelo->_consultar($queryAdeudo);
		if ($omodelo->error == 'si') {
			return;
		}
		$adeudo = round(floatval($rowAdeudo[0]['Adeudo']), 2);
		if ($monto > $adeudo + 0.009) {
			echo 'Error excede';
			return;
		}

		$idUsuario = $omodelo->link->real_escape_string($_SESSION['user_punto_venta']['ID_Usuario']);
		$caja = isset($caja) ? $omodelo->link->real_escape_string(trim($caja)) : '';
		$idDetalleCaja = '0';
		if ($caja != '' && $caja != '0') {
			$queryCaja = "SELECT ID_Detalle_Caja FROM detalles_caja INNER JOIN cajas ON detalles_caja.FK_Caja = cajas.ID_Caja WHERE cajas.ID_Caja = '$caja' AND cajas.Estado = 1 ORDER BY ID_Detalle_Caja DESC LIMIT 1";
			$rowCaja = $omodelo->_consultar($queryCaja);
			if ($omodelo->error != 'si' && $omodelo->numerofilas > 0) {
				$idDetalleCaja = $omodelo->link->real_escape_string($rowCaja[0]['ID_Detalle_Caja']);
			}
		}

		$queryTickets = "SELECT ventas.ID_Venta, $sqlRestante AS Restante
			FROM ventas
			WHERE ventas.Estatus != 2
			AND ventas.FK_Cliente = '$cliente'
			AND $sqlRestante > 0.009
			ORDER BY ventas.Fecha_Registro ASC, ventas.ID_Venta ASC";
		$rowTickets = $omodelo->_consultar($queryTickets);
		if ($omodelo->error == 'si') {
			return;
		}
		if ($omodelo->numerofilas == 0) {
			echo 'Error adeudo';
			return;
		}

		$omodelo->link->begin_transaction();
		$restanteAbono = $monto;
		$tickets = $omodelo->numerofilas;
		$errorPago = '';

		for ($i = 0; $i < $tickets; $i++) {
			if ($restanteAbono <= 0.009) {
				break;
			}

			$idVenta = $omodelo->link->real_escape_string($rowTickets[$i]['ID_Venta']);
			$restanteTicket = round(floatval($rowTickets[$i]['Restante']), 2);
			$aplicar = round(min($restanteTicket, $restanteAbono), 2);
			if ($aplicar <= 0) {
				continue;
			}

			$concepto = 'Abono a cuenta';
			$detalles = 'Aplicado desde reporte de pagos';
			$query = "INSERT INTO ventas_pagos SET
				FK_Venta = '$idVenta',
				Concepto = '$concepto',
				Monto = '$aplicar',
				Tipo_Pago = '$tipoPago',
				Detalles = '$detalles',
				Archivo = '',
				FK_Detalle_Caja = '$idDetalleCaja',
				Fecha_Registro = '$fecha',
				FK_Usuario = '$idUsuario'";
			$error = $omodelo->_insertar($query);
			if ($error == 'si') {
				$errorPago = mysqli_error($omodelo->link);
				break;
			}

			$restanteAbono = round($restanteAbono - $aplicar, 2);
		}

		if ($errorPago != '') {
			$omodelo->link->rollback();
			echo 'Error 1: ' . $errorPago;
			return;
		}

		$omodelo->link->commit();
		echo 'Correcto';
		$omodelo->movimiento("ABONO CLIENTE $cliente MONTO $monto", $idUsuario);
	}
}
?>