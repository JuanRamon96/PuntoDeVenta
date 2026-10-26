<?php
class promociones
{

	public function _consultar()
	{
		$omodelo = new m_modelo();
		extract($_POST);

		$buscar = $omodelo->link->real_escape_string($buscar);
		$limit = $omodelo->link->real_escape_string($limit);
		$pagina = $omodelo->link->real_escape_string($pagina);
		$ordenColumna = $omodelo->link->real_escape_string($ordenColumna);
		$orden = $omodelo->link->real_escape_string($orden);
		$arreglo = array();

		$mapaOrden = array(
			'Nombre' => 'promociones.Nombre',
			'Tipo' => 'promociones.Tipo',
			'Cantidad' => 'promociones.Cantidad',
			'Beneficio' => 'promociones.Tipo_Beneficio',
			'Estatus' => 'promociones.Estado'
		);
		$ordenColumna = isset($mapaOrden[$ordenColumna]) ? $mapaOrden[$ordenColumna] : 'promociones.ID_Promocion';

		$busqueda = '';
		if (trim($buscar) != '') {
			$separa = explode(' ', trim($buscar));
			$busqueda = 'WHERE ';
			for ($i = 0; $i < count($separa); $i++) {
				$busqueda .= "CONCAT(promociones.Nombre) REGEXP '" . $separa[$i] . "'";
				if ($i < (count($separa) - 1)) {
					$busqueda .= ' AND ';
				}
			}
		}

		$query = "SELECT 
			promociones.ID_Promocion, 
			promociones.Nombre, 
			promociones.Tipo, 
			promociones.FK_Producto, 
			promociones.FK_Clasificacion, 
			promociones.Cantidad, 
			promociones.Modo_Cantidad, 
			promociones.Tipo_Beneficio, 
			promociones.Precio_Fijo, 
			promociones.Tipo_Descuento, 
			promociones.Descuento, 
			promociones.Estado, 
			IFNULL(productos.Codigo, '') AS CodigoProducto, 
			IFNULL(productos.Descripcion, '') AS DescripcionProducto, 
			IFNULL(clasificaciones.Nombre, '') AS NombreClasificacion, 
			(SELECT COUNT(*) FROM promociones $busqueda) AS Num 
		FROM promociones 
		LEFT JOIN productos ON productos.ID_Producto = promociones.FK_Producto 
		LEFT JOIN clasificaciones ON clasificaciones.ID_Clasificacion = promociones.FK_Clasificacion 
		$busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($row == 'si') {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			if ($numerofilas > 0) {
				for ($i = 0; $i < $numerofilas; $i++) {
					if ($row[$i]['Tipo'] == 'Producto') {
						$aplica = '<b>Producto:</b> ' . $row[$i]['CodigoProducto'] . ' - ' . $row[$i]['DescripcionProducto'];
					} else {
						$aplica = '<b>Clasificación:</b> ' . $row[$i]['NombreClasificacion'];
					}

					if ($row[$i]['Tipo_Beneficio'] == 'Precio_Fijo') {
						$beneficio = 'Precio fijo: <span class="dinero">' . $row[$i]['Precio_Fijo'] . '</span>';
					} else {
						if ($row[$i]['Tipo_Descuento'] == 'Porcentaje') {
							$beneficio = 'Descuento: <span class="cantidad">' . $row[$i]['Descuento'] . '</span>%';
						} else {
							$beneficio = 'Descuento: <span class="dinero">' . $row[$i]['Descuento'] . '</span> por pieza';
						}
					}

					$disabledEstado = '';
					if (!($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Promociones'][3] == '1')) {
						$disabledEstado = 'disabled';
					}

					$checked = $row[$i]['Estado'] == '1' ? 'checked' : '';
					$estatus = '<div class="form-check form-switch d-flex justify-content-center">
						<input class="form-check-input bEstadoPromocion" type="checkbox" role="switch" attrID="' . $row[$i]['ID_Promocion'] . '" ' . $checked . ' ' . $disabledEstado . '>
					</div>';

					$bModificar = '';
					$bEliminar = '';
					if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Promociones'][3] == '1') {
						$bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarPromocion" title="Modificar" attrID="' . $row[$i]['ID_Promocion'] . '"><i class="fas fa-pencil"></i></button>';
					}
					if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Promociones'][4] == '1') {
						$bEliminar = '<button type="button" class="btn btn-sm btn-danger bEliminarPromocion" title="Eliminar" attrID="' . $row[$i]['ID_Promocion'] . '"><i class="fas fa-trash"></i></button>';
					}

					$modoCantidad = $row[$i]['Modo_Cantidad'] == 'Paquete' ? 'Paquete de' : 'Mínimo';

					$arreglo['data'][$i] = array(
						'ID' => $row[$i]['ID_Promocion'],
						'Nombre' => $row[$i]['Nombre'],
						'Tipo' => $aplica,
						'Cantidad' => $modoCantidad . ' <span class="cantidad">' . $row[$i]['Cantidad'] . '</span>',
						'Beneficio' => $beneficio,
						'Estatus' => $estatus,
						'Acciones' => $bModificar . ' ' . $bEliminar
					);
				}

				$arreglo['totales'] = array('NumRows' => $row[0]['Num']);
			}
		}

		echo json_encode($arreglo);
	}

	public function _insertar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date("Y-m-d H:i:s");

		$nombrePromocion = $omodelo->link->real_escape_string(trim($nombrePromocion));
		$tipoPromocion = $omodelo->link->real_escape_string(trim($tipoPromocion));
		$fkProducto = $omodelo->link->real_escape_string(trim(isset($fkProducto) ? $fkProducto : '0'));
		$fkClasificacion = $omodelo->link->real_escape_string(trim(isset($fkClasificacion) ? $fkClasificacion : '0'));
		$cantidadPromocion = $omodelo->link->real_escape_string(trim(isset($cantidadPromocion) ? $cantidadPromocion : '0'));
		$modoCantidadPromocion = $omodelo->link->real_escape_string(trim(isset($modoCantidadPromocion) ? $modoCantidadPromocion : 'Paquete'));
		$tipoBeneficioPromocion = $omodelo->link->real_escape_string(trim($tipoBeneficioPromocion));
		$precioFijoPromocion = $omodelo->link->real_escape_string(trim(isset($precioFijoPromocion) ? $precioFijoPromocion : '0'));
		$tipoDescuentoPromocion = $omodelo->link->real_escape_string(trim(isset($tipoDescuentoPromocion) ? $tipoDescuentoPromocion : 'Porcentaje'));
		$descuentoPromocion = $omodelo->link->real_escape_string(trim(isset($descuentoPromocion) ? $descuentoPromocion : '0'));

		if ($tipoPromocion == 'Clasificacion') {
			$fkProducto = 0;
			$tipoBeneficioPromocion = 'Descuento';
		} else {
			$fkClasificacion = 0;
		}

		if ($tipoBeneficioPromocion == 'Precio_Fijo') {
			$descuentoPromocion = 0;
		} else {
			$precioFijoPromocion = 0;
		}

		if ($modoCantidadPromocion != 'Minima' && $modoCantidadPromocion != 'Paquete') {
			$modoCantidadPromocion = 'Paquete';
		}

		$query = "INSERT INTO promociones SET 
			Nombre = '$nombrePromocion', 
			Tipo = '$tipoPromocion', 
			FK_Producto = '$fkProducto', 
			FK_Clasificacion = '$fkClasificacion', 
			Cantidad = '$cantidadPromocion', 
			Modo_Cantidad = '$modoCantidadPromocion', 
			Tipo_Beneficio = '$tipoBeneficioPromocion', 
			Precio_Fijo = '$precioFijoPromocion', 
			Tipo_Descuento = '$tipoDescuentoPromocion', 
			Descuento = '$descuentoPromocion', 
			Estado = 1, 
			Fecha_Registro = '$fecha'";
		$error = $omodelo->_insertar($query);

		if ($error == 'si') {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			echo 'Correcto';

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	public function _modificar()
	{
		$omodelo = new m_modelo();
		extract($_POST);

		$id = $omodelo->link->real_escape_string(trim($id));
		$tipo = isset($tipo) ? $omodelo->link->real_escape_string(trim($tipo)) : '';

		if ($tipo == 'estado') {
			$estado = $omodelo->link->real_escape_string(trim($estado));

			$query = "UPDATE promociones SET Estado = '$estado' WHERE ID_Promocion = '$id'";
			$error = $omodelo->_insertar($query);

			if ($error == 'si') {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				echo 'Correcto';

				$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
			}
		} else {
			$nombrePromocion = $omodelo->link->real_escape_string(trim($nombrePromocion));
			$tipoPromocion = $omodelo->link->real_escape_string(trim($tipoPromocion));
			$fkProducto = $omodelo->link->real_escape_string(trim(isset($fkProducto) ? $fkProducto : '0'));
			$fkClasificacion = $omodelo->link->real_escape_string(trim(isset($fkClasificacion) ? $fkClasificacion : '0'));
			$cantidadPromocion = $omodelo->link->real_escape_string(trim(isset($cantidadPromocion) ? $cantidadPromocion : '0'));
			$modoCantidadPromocion = $omodelo->link->real_escape_string(trim(isset($modoCantidadPromocion) ? $modoCantidadPromocion : 'Paquete'));
			$tipoBeneficioPromocion = $omodelo->link->real_escape_string(trim($tipoBeneficioPromocion));
			$precioFijoPromocion = $omodelo->link->real_escape_string(trim(isset($precioFijoPromocion) ? $precioFijoPromocion : '0'));
			$tipoDescuentoPromocion = $omodelo->link->real_escape_string(trim(isset($tipoDescuentoPromocion) ? $tipoDescuentoPromocion : 'Porcentaje'));
			$descuentoPromocion = $omodelo->link->real_escape_string(trim(isset($descuentoPromocion) ? $descuentoPromocion : '0'));

			if ($tipoPromocion == 'Clasificacion') {
				$fkProducto = 0;
				$tipoBeneficioPromocion = 'Descuento';
			} else {
				$fkClasificacion = 0;
			}

			if ($tipoBeneficioPromocion == 'Precio_Fijo') {
				$descuentoPromocion = 0;
			} else {
				$precioFijoPromocion = 0;
			}

			if ($modoCantidadPromocion != 'Minima' && $modoCantidadPromocion != 'Paquete') {
				$modoCantidadPromocion = 'Paquete';
			}

			$query = "UPDATE promociones SET 
				Nombre = '$nombrePromocion', 
				Tipo = '$tipoPromocion', 
				FK_Producto = '$fkProducto', 
				FK_Clasificacion = '$fkClasificacion', 
				Cantidad = '$cantidadPromocion', 
				Modo_Cantidad = '$modoCantidadPromocion', 
				Tipo_Beneficio = '$tipoBeneficioPromocion', 
				Precio_Fijo = '$precioFijoPromocion', 
				Tipo_Descuento = '$tipoDescuentoPromocion', 
				Descuento = '$descuentoPromocion' 
				WHERE ID_Promocion = '$id'";
			$error = $omodelo->_insertar($query);

			if ($error == 'si') {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				echo 'Correcto';

				$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
			}
		}
	}

	public function _detalles()
	{
		$omodelo = new m_modelo();
		extract($_POST);

		$id = $omodelo->link->real_escape_string(trim($id));
		$arreglo = array();

		$query = "SELECT * FROM promociones WHERE ID_Promocion = '$id' LIMIT 1";
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($row == 'si') {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			if ($numerofilas > 0) {
				$arreglo = $row[0];
				$arreglo['NombreSeleccion'] = '';

				if ($row[0]['Tipo'] == 'Producto') {
					$queryP = "SELECT Codigo, Descripcion FROM productos WHERE ID_Producto = '" . $row[0]['FK_Producto'] . "' LIMIT 1";
					$rowP = $omodelo->_consultar($queryP);
					if ($rowP != 'si' && $omodelo->numerofilas > 0) {
						$arreglo['NombreSeleccion'] = $rowP[0]['Codigo'] . ' - ' . $rowP[0]['Descripcion'];
					}
				} else {
					$queryC = "SELECT Nombre FROM clasificaciones WHERE ID_Clasificacion = '" . $row[0]['FK_Clasificacion'] . "' LIMIT 1";
					$rowC = $omodelo->_consultar($queryC);
					if ($rowC != 'si' && $omodelo->numerofilas > 0) {
						$arreglo['NombreSeleccion'] = $rowC[0]['Nombre'];
					}
				}
			}
		}

		echo json_encode($arreglo);
	}

	public function _eliminar()
	{
		$omodelo = new m_modelo();
		extract($_POST);

		$id = $omodelo->link->real_escape_string(trim($id));

		$query = "DELETE FROM promociones WHERE ID_Promocion = '$id'";
		$error = $omodelo->_insertar($query);

		if ($error == 'si') {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			echo 'Correcto';

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}
}
?>
