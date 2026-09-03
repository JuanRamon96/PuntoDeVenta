<?php
require_once("vendor/autoload.php");

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class facturas
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

		$fechaInicio = $omodelo->link->real_escape_string($fechaInicio);
		$fechaFin = $omodelo->link->real_escape_string($fechaFin);

		$busqueda = '';
		if (trim($buscar) != '') {
			$separa = explode(' ', trim($buscar));
			$busqueda = 'AND ';
			for ($i = 0; $i < count($separa); $i++) {
				$busqueda .= "CONCAT(
					ID_Factura,
					DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), 
					DATE_FORMAT(Fecha_Emision, '%d-%m-%Y %r'), 
					DATE_FORMAT(Fecha_Timbrado, '%d-%m-%Y %r'), 
					Folio_Fiscal,
					Metodo_Pago,
					Forma_Pago,
					Periodicidad,
					Mes,
					Ano,
					Subtotal,
					Descuento,
					Impuesto,
					Total,
					Estatus,
					IF(Global = 1, 'Factura Global', ''),
					Email,
					FK_Venta
				) REGEXP '" . $separa[$i] . "'";
				if ($i < (count($separa) - 1)) {
					$busqueda .= ' AND ';
				}
			}
		}

		$query = "SELECT 
			ID_Factura, 
			ID_Factura AS Folio,
			Folio_Fiscal, 
			Nombre_Emisor, 
			RFC_Emisor, 
			CP_Emisor, 
			Regimen_Emisor, 
			Nombre_Receptor, 
			RFC_Receptor, 
			CP_Receptor, 
			Regimen_Receptor, 
			Email,
			Version_CFDI, 
			Metodo_Pago, 
			Forma_Pago, 
			Uso_CFDI, 
			Tipo_Comprobante, 
			Moneda, 
			Periodicidad, 
			Mes, 
			Ano, 
			Subtotal,
			Descuento,
			Impuesto, 
			Total, 
			Estatus,
			Fecha_Timbrado,
			XML,
			PDF,
			DATE_FORMAT(Fecha_Timbrado, '%d-%m-%Y %r') AS FechaTimbrado,
			Fecha_Emision, 
			DATE_FORMAT(Fecha_Emision, '%d-%m-%Y %r') AS FechaEmision, 
			Fecha_Registro, 
			DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS FechaRegistro,
			Global,
			General,
			FK_Venta,
			IFNULL((SELECT SUM(Total) FROM facturas WHERE (DATE_FORMAT(Fecha_Emision, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Emision, '%Y-%m-%d') <= '$fechaFin') $busqueda), 0) AS TotalFinal,
			(SELECT COUNT(*) FROM facturas WHERE (DATE_FORMAT(Fecha_Emision, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Emision, '%Y-%m-%d') <= '$fechaFin') $busqueda) AS Num 
		FROM facturas WHERE (DATE_FORMAT(Fecha_Emision, '%Y-%m-%d') >= '$fechaInicio' AND DATE_FORMAT(Fecha_Emision, '%Y-%m-%d') <= '$fechaFin') $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($row == 'si') {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			if ($numerofilas > 0) {
				for ($i = 0; $i < $numerofilas; $i++) {
					$bModificar = '';
					if ($row[$i]['Estatus'] == 'Pendiente' && ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Facturas'][3] == '1')) {
						$bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarFactura" title="Modificar" attrID="' . $row[$i]['ID_Factura'] . '""><i class="fas fa-pencil"></i></button>';
					}
					$bEliminar = '';
					if ($row[$i]['Estatus'] == 'Pendiente' && ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Facturas'][4] == '1')) {
						$bEliminar = '<button type="button" class="btn btn-sm btn-danger bEliminarFactura" title="Eliminar" attrID="' . $row[$i]['ID_Factura'] . '"><i class="fas fa-trash"></i></button>';
					}

					$estatus = '<span class="badge text-bg-warning">Pendiente</span>';
					$imprimir = '';
					if ($row[$i]['Estatus'] == 'Timbrada') {
						$estatus = '<span class="badge text-bg-success">Timbrada</span>';
						$imprimir = '<a class="btn btn-sm btn-danger" href="./controladores/facturas/' . $row[$i]['PDF'] . '" target="_blank"><i class="fas fa-file-pdf"></i></a>
						<a class="btn btn-sm btn-primary" href="./controladores/facturas/' . $row[$i]['XML'] . '" target="_blank">XML</a>
						<a class="btn btn-sm btn-outline-primary" href="./controladores/facturas/' . $row[$i]['XML'] . '" download="' . $row[$i]['XML'] . '">Descargar XML</a>';
					}

					$arreglo['data'][$i] = array(
						'ID' => $row[$i]['ID_Factura'],
						'Fecha_Registro' => $row[$i]['FechaRegistro'],
						'Fecha_Emision' => $row[$i]['FechaEmision'],
						'Fecha_Timbrado' => $row[$i]['Estatus'] == 'Timbrada' ? $row[$i]['FechaTimbrado'] : '',
						'Folio' => '<b>Folio:</b> ' . $row[$i]['Folio'] . ($row[$i]['Folio_Fiscal'] != '' ? '<br><b>Folio Fiscal:</b> ' . $row[$i]['Folio_Fiscal'] : '') . ($row[$i]['FK_Venta'] != '0' ? '<br><b>Folio Venta: </b>' . $row[$i]['FK_Venta'] : '') . ($row[$i]['Global'] == '1' ? '<br>Factura Global' : ''),
						'Emisor' => $row[$i]['Nombre_Emisor'] . '<br><b>RFC:</b> ' . $row[$i]['RFC_Emisor'] . '<br><b>CP:</b> ' . $row[$i]['CP_Emisor'] . '<br><b>Regimen:</b> ' . $row[$i]['Regimen_Emisor'],
						'Receptor' => $row[$i]['Nombre_Receptor'] . '<br><b>RFC:</b> ' . $row[$i]['RFC_Receptor'] . '<br><b>CP:</b> ' . $row[$i]['CP_Receptor'] . '<br><b>Regimen:</b> ' . $row[$i]['Regimen_Receptor'] . '<br><b>Email:</b> ' . $row[$i]['Email'],
						'Datos' => '<b>Versión:</b> ' . $row[$i]['Version_CFDI'] . '<br><b>Método de pago:</b> ' . $row[$i]['Metodo_Pago'] . '<br><b>Forma de pago:</b> ' . $row[$i]['Forma_Pago'] . '<br><b>Uso de CFDI:</b> ' . $row[$i]['Uso_CFDI'] . '<br><b>Tipo:</b> ' . $row[$i]['Tipo_Comprobante'] . ($row[$i]['Global'] == '1' ? '<br><b>Periodicidad:</b> ' . $row[$i]['Periodicidad'] . '<br><b>Mes:</b> ' . $row[$i]['Mes'] . '<br><b>Año:</b> ' . $row[$i]['Ano'] : ''),
						'Total' => '<b>Total: </b><span class="dinero" style="font-size: 18px;">' . $row[$i]['Total'] . '</span> ' . (trim($row[$i]['Tipo_Comprobante']) == 'I - Ingreso' ? '<br><b>Subtotal: </b><span class="dinero">' . $row[$i]['Subtotal'] . '</span><br><b>Descuento: </b><span class="dinero">' . $row[$i]['Descuento'] . '</span><br><b>Impuestos: </b><span class="dinero">' . $row[$i]['Impuesto'] . '</span>' : ''),
						'Estatus' => $estatus,
						'Acciones' => $bModificar . ' ' . $bEliminar . ' ' . $imprimir
					);
				}

				$arreglo['totales'] = array('NumRows' => $row[0]['Num'], 'TotalFinal' => '<b>Total:</b> <span class="dinero">' . $row[0]['TotalFinal'] . '</span>');
			}
		}

		echo json_encode($arreglo);
	}

	public function _detalles()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$tipo = $omodelo->link->real_escape_string($tipo);

		if ($tipo == 'factura') {
			$id = $omodelo->link->real_escape_string($id);
			$arreglo = array();

			$query = "SELECT 
				ID_Factura, 
				Folio_Fiscal, 
				Nombre_Emisor, 
				RFC_Emisor, 
				CP_Emisor, 
				Regimen_Emisor, 
				Nombre_Receptor, 
				RFC_Receptor, 
				CP_Receptor, 
				Regimen_Receptor, 
				Email,
				Version_CFDI, 
				Metodo_Pago, 
				Forma_Pago, 
				Uso_CFDI, 
				Tipo_Comprobante, 
				Moneda,  
				Periodicidad, 
				Mes, 
				Ano, 
				Subtotal,
				Descuento,
				Impuesto, 
				Total, 
				Estatus,
				Fecha_Emision, 
				Fecha_Timbrado,
				Fecha_Registro,
				FK_Venta,
				Global,
				General
			FROM facturas WHERE ID_Factura = '$id'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == 'si') {
				echo "Error 1: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$ventas = '';
						$tablaDocsRelacionados = '';
						if (trim($row[$i]['Tipo_Comprobante']) == 'I - Ingreso') {
							$query1 = "SELECT 
								ID_Detalle_Factura, 
								FK_Factura,
								Producto, 
								Codigo_Producto, 
								Codigo_Unidad, 
								Precio_Unitario, 
								Cantidad, 
								Subtotal, 
								Descuento, 
								Total, 
								Impuesto, 
								Importe
							FROM detalles_facturas WHERE FK_Factura = '$id'";
							$row1 = $omodelo->_consultar($query1);
							$numerofilas1 = $omodelo->numerofilas;

							if ($row1 == 'si') {
								echo 'Error 2: ' . mysqli_error($omodelo->link);
							} else {
								if ($numerofilas1 > 0) {
									for ($x = 0; $x < $numerofilas1; $x++) {
										$impuestos = '';
										$impuestos_json = array();
										$query2 = "SELECT 
											ID_Impuesto_Factura, 
											Nombre, 
											Clave, 
											Valor, 
											Clase, 
											Factor,
											Importe
										FROM impuestos_factura WHERE FK_Detalle_Factura = '" . $row1[$x]['ID_Detalle_Factura'] . "'";
										$row2 = $omodelo->_consultar($query2);
										$numerofilas2 = $omodelo->numerofilas;

										if ($row2 == 'si') {
											echo 'Error 3: ' . mysqli_error($omodelo->link);
										} else {
											if ($numerofilas2 > 0) {
												for ($y = 0; $y < $numerofilas2; $y++) {
													$impuestos_json[] = array(
														'tipo'   => $row2[$y]['Clase'],
														'impuesto'   => $row2[$y]['Clave'],
														'tipoFactor'  => $row2[$y]['Factor'],
														'valor'   => $row2[$y]['Valor'],
														'importe' => $row2[$y]['Importe']
													);

													$valor = round($row2[$y]['Valor'] * 100) / 100 . '%';
													if ($row2[$y]['Factor'] === 'Cuota') {
														$valor = '$' . $row2[$y]['Valor'];
													}

													$signo = '';
													if ($row2[$y]['Clase'] === 'Retenido') {
														$signo = '-';
													}

													$impuestos .= $row2[$y]['Nombre'] . ' (' . $row2[$y]['Factor'] . ' ' . $valor . ') (' . $signo . '$' . (round($row2[$y]['Importe'] * 100) / 100) . ')<br>';
												}
											}
										}

										$ventas .= '<tr data-impuestos=\'' . json_encode($impuestos_json) . '\'>
											<td>' . $row1[$x]['Codigo_Producto'] . '</td>
											<td>' . $row1[$x]['Producto'] . '</td>
											<td>' . $row1[$x]['Codigo_Unidad'] . '</td>
											<td class="cantidad">' . $row1[$x]['Cantidad'] . '</td>
											<td class="dinero">' . $row1[$x]['Precio_Unitario'] . '</td>
											<td class="dinero">' . $row1[$x]['Subtotal'] . '</td>
											<td><span class="dinero">' . $row1[$x]['Descuento'] . '</span> (<span class="porcentaje">' . (($row1[$x]['Descuento'] / $row1[$x]['Subtotal']) * 100) . '</span>)</td>
											<td class="dinero">' . $row1[$x]['Total'] . '</td>
											<td><span class="dinero">' . $row1[$x]['Impuesto'] . '</span><br><span>' . $impuestos . '</span></td>
											<td class="dinero">' . $row1[$x]['Importe'] . '</td>
											<td><button type="button" class="btn btn-danger btn-sm bEliminarConceptoFactura"><i class="fas fa-times"></i></button></td>
										</tr>';
									}
								}
							}
						} else if ($row[$i]['Tipo_Comprobante'] == 'P - Complemento de Pago') {
							$query1 = "SELECT 
								ID_Documento, 
								FK_Factura,
								UUID, 
								Parcialidad, 
								Saldo_Anterior, 
								Importe_Pagado, 
								Saldo_Insoluto
							FROM docs_relacionados WHERE FK_Factura = '$id'";
							$row1 = $omodelo->_consultar($query1);
							$numerofilas1 = $omodelo->numerofilas;

							if ($row1 == 'si') {
								echo 'Error 2: ' . mysqli_error($omodelo->link);
							} else {
								if ($numerofilas1 > 0) {
									for ($x = 0; $x < $numerofilas1; $x++) {
										$idDocRel = $row1[$x]['ID_Documento'];
										$importePagado = floatval($row1[$x]['Importe_Pagado']);
										$badgesImpuestos = '';
										$impuestos_json = array();

										$query2 = "SELECT 
											ID_Impuesto_Docs, 
											FK_Detalle_Docs, 
											Nombre, 
											Clave, 
											Valor, 
											Clase, 
											Factor,
											Importe
										FROM impuestos_docs WHERE FK_Detalle_Docs = '$idDocRel'";
										$row2 = $omodelo->_consultar($query2);
										$numerofilas2 = $omodelo->numerofilas;

										if ($row2 != 'si' && $numerofilas2 > 0) {
											for ($y = 0; $y < $numerofilas2; $y++) {
												$esTasa = (strcasecmp($row2[$y]['Factor'], 'Tasa') == 0);
												$tasaOCuota = $esTasa ? (floatval($row2[$y]['Valor']) / 100) : floatval($row2[$y]['Valor']);
												$importeImp = floatval($row2[$y]['Importe']);
												$baseCalculada = ($tasaOCuota > 0) ? round($importeImp / $tasaOCuota, 2) : round($importePagado, 2);

												$impuestos_json[] = array(
													'tipo'      => $row2[$y]['Clase'],
													'impuesto'  => $row2[$y]['Clave'],
													'nombre'    => $row2[$y]['Nombre'],
													'factor'    => $row2[$y]['Factor'],
													'tasa'      => $row2[$y]['Valor'],
													'base'      => $baseCalculada,
													'importe'   => $row2[$y]['Importe']
												);

												$claseValor = $esTasa ? 'porcentaje' : 'dinero';
												$textoValor = $row2[$y]['Valor'];

												$badgesImpuestos .= '<span class="badge bg-secondary d-block mb-1">' . $row2[$y]['Nombre'] . ' (<span class="' . $claseValor . '">' . $textoValor . '</span>): <span class="dinero">' . round($row2[$y]['Importe'], 2) . '</span></span>';
											}
										}

										$datos_completos = array(
											'uuid'          => $row1[$x]['UUID'],
											'parcialidad'   => $row1[$x]['Parcialidad'],
											'saldoAnt'      => floatval($row1[$x]['Saldo_Anterior']),
											'montoPagado'   => floatval($row1[$x]['Importe_Pagado']),
											'saldoInsoluto' => floatval($row1[$x]['Saldo_Insoluto']),
											'impuestos'     => $impuestos_json
										);

										$tablaDocsRelacionados .= '<tr data-json=\'' . json_encode($datos_completos) . '\'>
											<td class="text-start small fw-bold">' . $row1[$x]['UUID'] . '</td>
											<td>' . $row1[$x]['Parcialidad'] . '</td>
											<td class="dinero">' . $row1[$x]['Saldo_Anterior'] . '</td>
											<td class="text-success fw-bold dinero">' . number_format($row1[$x]['Importe_Pagado'], 2, '.', '') . '</td>
											<td class="text-danger dinero">' . number_format($row1[$x]['Saldo_Insoluto'], 2, '.', '') . '</td>
											<td>' . $badgesImpuestos . '</td>
											<td>
												<button type="button" class="btn btn-danger btn-sm bEliminarDocRel">
													<i class="fas fa-trash"></i>
												</button>
											</td>
										</tr>';
									}
								}
							}
						}

						$arreglo = array(
							'ID_Factura' => $row[$i]['ID_Factura'],
							'Folio_Fiscal' => $row[$i]['Folio_Fiscal'],
							'Nombre_Emisor' => $row[$i]['Nombre_Emisor'],
							'RFC_Emisor' => $row[$i]['RFC_Emisor'],
							'CP_Emisor' => $row[$i]['CP_Emisor'],
							'Regimen_Emisor' => $row[$i]['Regimen_Emisor'],
							'Nombre_Receptor' => $row[$i]['Nombre_Receptor'],
							'RFC_Receptor' => $row[$i]['RFC_Receptor'],
							'CP_Receptor' => $row[$i]['CP_Receptor'],
							'Regimen_Receptor' => $row[$i]['Regimen_Receptor'],
							'Email' => $row[$i]['Email'],
							'Version_CFDI' => $row[$i]['Version_CFDI'],
							'Metodo_Pago' => $row[$i]['Metodo_Pago'],
							'Forma_Pago' => $row[$i]['Forma_Pago'],
							'Uso_CFDI' => $row[$i]['Uso_CFDI'],
							'Tipo_Comprobante' => $row[$i]['Tipo_Comprobante'],
							'Moneda' => $row[$i]['Moneda'],
							'Periodicidad' => $row[$i]['Periodicidad'],
							'Mes' => $row[$i]['Mes'],
							'Ano' => $row[$i]['Ano'],
							'Subtotal' => $row[$i]['Subtotal'],
							'Descuento' => $row[$i]['Descuento'],
							'Impuesto' => $row[$i]['Impuesto'],
							'Total' => $row[$i]['Total'],
							'Fecha_Timbrado' => $row[$i]['Fecha_Timbrado'],
							'Fecha_Emision' => $row[$i]['Fecha_Emision'],
							'FK_Venta' => $row[$i]['FK_Venta'],
							'Global' => $row[$i]['Global'],
							'General' => $row[$i]['General'],
							'Ventas' => $ventas,
							'Docs' => $tablaDocsRelacionados
						);
					}
				}
			}

			echo json_encode($arreglo);
		} else if ($tipo == 'datosFacturacion') {
			$arreglo = array();
			$query = "SELECT RFC, Nombre, Regimen, CP FROM configuracion_facturacion LIMIT 1";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					$arreglo = array(
						'RFC' => $row[0]['RFC'],
						'Nombre' => $row[0]['Nombre'],
						'Regimen' => $row[0]['Regimen'],
						'CP' => $row[0]['CP']
					);
				}
			}

			echo json_encode($arreglo);
		} else if ($tipo == 'ventaConceptos') {
			$conceptos = '';

			$query1 = "SELECT 
				ID_Detalle_Venta, 
				FK_Venta, 
				FK_Producto,
				productos.Codigo AS Codigo, 
				detalles_ventas.Descripcion AS Descripcion, 
				detalles_ventas.Precio AS Precio, 
				detalles_ventas.Costo AS Costo, 
				Cantidad, 
				Descuento, 
				Total, 
				Clave_ProdServ_CFDI, 
				Descripcion_Clave_CDFI, 
				Clave_Unidad_CFDI, 
				Nombre_Unidad_CFDI, 
				Simbolo_CFDI
			FROM detalles_ventas INNER JOIN productos ON FK_Producto = ID_Producto WHERE FK_Venta = '$id'";
			$row1 = $omodelo->_consultar($query1);
			$numerofilas1 = $omodelo->numerofilas;

			if ($row1 == 'si') {
				echo 'Error 2: ' . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas1 > 0) {
					for ($x = 0; $x < $numerofilas1; $x++) {
						$totalImpuestos = 0;
						$impuestos = '';
						$impuestos_json = array();
						$query2 = "SELECT 
							ID_Impuesto_Venta,
							Nombre AS Impuesto, 
							Porcentaje AS Valor, 
							Clave_CFDI AS Clave, 
							Tipo_Factor AS Factor, 
							Clase 
						FROM impuestos_ventas WHERE FK_Detalle_Venta = '" . $row1[$x]['ID_Detalle_Venta'] . "'";
						$row2 = $omodelo->_consultar($query2);
						$numerofilas2 = $omodelo->numerofilas;

						if ($row2 == 'si') {
							echo 'Error 3: ' . mysqli_error($omodelo->link);
						} else {
							if ($numerofilas2 > 0) {
								for ($y = 0; $y < $numerofilas2; $y++) {
									$importe = round(($row1[$x]['Total'] * ($row2[$y]['Valor'] / 100)) * 100) / 100;
									$valor = round($row2[$y]['Valor'] * 100) / 100 . '%';
									if ($row2[$y]['Factor'] === 'Cuota') {
										$valor = '$' . $row2[$y]['Valor'];
										$importe = $row2[$y]['Valor'] * $row1[$x]['Cantidad'];
									}

									$signo = '';
									if ($row2[$y]['Clase'] === 'Retenido') {
										$signo = '-';
									}

									$impuestos_json[] = array(
										'tipo' => $row2[$y]['Clase'],
										'impuesto' => $row2[$y]['Clave'],
										'tipoFactor' => $row2[$y]['Factor'],
										'valor' => $row2[$y]['Valor'],
										'importe' => $importe
									);

									$totalImpuestos += $importe;
									$impuestos .= $row2[$y]['Impuesto'] . ' (' . $row2[$y]['Factor'] . ' ' . $valor . ') (' . $signo . '$' . (round($importe * 100) / 100) . ')<br>';
								}
							}
						}

						$conceptos .= '<tr data-impuestos=\'' . json_encode($impuestos_json) . '\'>
							<td>' . $row1[$x]['Clave_ProdServ_CFDI'] . '</td>
							<td>' . $row1[$x]['Descripcion'] . '</td>
							<td>' . $row1[$x]['Clave_Unidad_CFDI'] . '</td>
							<td class="cantidad">' . $row1[$x]['Cantidad'] . '</td>
							<td class="dinero">' . $row1[$x]['Precio'] . '</td>
							<td class="dinero">' . ($row1[$x]['Precio'] * $row1[$x]['Cantidad']) . '</td>
							<td><span class="dinero">' . $row1[$x]['Descuento'] . '</span> (<span class="porcentaje">' . (($row1[$x]['Descuento'] / ($row1[$x]['Precio'] * $row1[$x]['Cantidad'])) * 100) . '</span>)</td>
							<td class="dinero">' . ($row1[$x]['Total'] - $totalImpuestos) . '</td>
							<td><span class="dinero">' . $totalImpuestos . '</span><br><span>' . $impuestos . '</span></td>
							<td class="dinero">' . $row1[$x]['Total'] . '</td>
							<td><button type="button" class="btn btn-danger btn-sm bEliminarConceptoFactura"><i class="fas fa-times"></i></button></td>
						</tr>';
					}
				}
			}

			echo $conceptos;
		}
	}

	public function _insertar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = DATE("Y-m-d H:i:s");
		$fechaEmision = $omodelo->link->real_escape_string($fechaEmision);
		$tipoComprobante = $omodelo->link->real_escape_string($tipoComprobante);
		$formaPago = $omodelo->link->real_escape_string($formaPago);
		$metodoPago = $omodelo->link->real_escape_string($metodoPago);

		$periodicidad = $omodelo->link->real_escape_string($periodicidad);
		$mes = $omodelo->link->real_escape_string($mes);
		$anio = $omodelo->link->real_escape_string($anio);

		$rfcReceptor = $omodelo->link->real_escape_string($rfcReceptor);
		$nombreReceptor = $omodelo->link->real_escape_string($nombreReceptor);
		$usoCfdi = $omodelo->link->real_escape_string($usoCfdi);
		$regimenFiscalReceptor = $omodelo->link->real_escape_string($regimenFiscalReceptor);
		$cpReceptor = $omodelo->link->real_escape_string($cpReceptor);
		$email = $omodelo->link->real_escape_string($email);

		$subtotal = $omodelo->link->real_escape_string($subtotal);
		$descuento = $omodelo->link->real_escape_string($descuento);
		$impuestos = $omodelo->link->real_escape_string($impuestos);
		$total = $omodelo->link->real_escape_string($total);

		$global = $omodelo->link->real_escape_string($global);
		$general = $omodelo->link->real_escape_string($general);

		$timbrar = $omodelo->link->real_escape_string($timbrar);
		$venta = $omodelo->link->real_escape_string($venta);

		$query = "INSERT INTO facturas SET 
			Global = '$global',
			General = '$general',

			Nombre_Emisor = (SELECT Nombre FROM configuracion_facturacion LIMIT 1), 
			RFC_Emisor = (SELECT RFC FROM configuracion_facturacion LIMIT 1), 
			CP_Emisor = (SELECT CP FROM configuracion_facturacion LIMIT 1), 
			Regimen_Emisor = (SELECT Regimen FROM configuracion_facturacion LIMIT 1), 

			Nombre_Receptor = '$nombreReceptor',
			RFC_Receptor = '$rfcReceptor',
			CP_Receptor = '$cpReceptor',
			Regimen_Receptor = '$regimenFiscalReceptor',
			Email = '$email',

			Version_CFDI = '4.0', 
			Metodo_Pago = '$metodoPago', 
			Forma_Pago = '$formaPago', 
			Uso_CFDI = '$usoCfdi', 
			Tipo_Comprobante = '$tipoComprobante', 
			Moneda = 'MXN',

			Periodicidad = '$periodicidad', 
			Mes = '$mes', 
			Ano = '$anio',

			Subtotal = '$subtotal',
			Descuento = '$descuento',
			Impuesto = '$impuestos',
			Total = '$total',

			Estatus = 'Pendiente', 
			Fecha_Emision = '$fechaEmision',
			FK_Venta = '$venta',
			Fecha_Registro = NOW()";
		$error = $omodelo->_insertar($query);

		if ($error == 'si') {
			echo "Error 1: " . mysqli_error($omodelo->link);
		} else {
			$idFactura = $omodelo->link->insert_id;

			if (trim($tipoComprobante) == 'I - Ingreso') {
				$conceptos = json_decode($conceptos, true);
				foreach ($conceptos as $concepto) {
					$claveProdServ = $omodelo->link->real_escape_string($concepto['claveProdServ']);
					$descripcion = $omodelo->link->real_escape_string($concepto['descripcion']);
					$unidad = $omodelo->link->real_escape_string($concepto['unidad']);
					$cantidad = $omodelo->link->real_escape_string($concepto['cantidad']);
					$precioUnitario = $omodelo->link->real_escape_string($concepto['precioUnitario']);
					$subtotalC = $omodelo->link->real_escape_string($concepto['subtotal']);
					$descuentoC = $omodelo->link->real_escape_string($concepto['descuento']);
					$base = $omodelo->link->real_escape_string($concepto['base']);
					$impuestos_total = $omodelo->link->real_escape_string($concepto['impuestos_total']);
					$totalC = $omodelo->link->real_escape_string($concepto['total']);

					$query1 = "INSERT INTO detalles_facturas SET 
						FK_Factura = '$idFactura', 
						Producto = '$descripcion', 
						Codigo_Producto = '$claveProdServ', 
						Codigo_Unidad = '$unidad', 
						Precio_Unitario = '$precioUnitario', 
						Cantidad = '$cantidad', 
						Subtotal = '$subtotalC', 
						Descuento = '$descuentoC', 
						Total = '$base', 
						Impuesto = '$impuestos_total', 
						Importe = '$totalC'";
					$error1 = $omodelo->_insertar($query1);

					if ($error1 == 'si') {
						echo "Error 2: " . mysqli_error($omodelo->link);
						return;
					} else {
						$idDetalle = $omodelo->link->insert_id;

						$imps = isset($concepto['impuestos']) ? $concepto['impuestos'] : [];
						foreach ($imps as $imp) {
							$nombre = '';
							switch ($imp['impuesto']) {
								case '002':
									$nombre = 'IVA';
									break;
								case '001':
									$nombre = 'ISR';
									break;
								case '003':
									$nombre = 'IEPS';
									break;
								default:
									$nombre = $imp['impuesto'];
							}

							$clave = $imp['impuesto'];
							$factor = $omodelo->link->real_escape_string($imp['tipoFactor']); // Tasa o Cuota
							$clase = $omodelo->link->real_escape_string($imp['tipo']); // Trasladado o Retenido
							$valor = $omodelo->link->real_escape_string($imp['valor']);
							$importeImp = $omodelo->link->real_escape_string($imp['importe']);

							$queryImp = "INSERT INTO impuestos_factura SET
								FK_Detalle_Factura = '$idDetalle',
								Nombre = '$nombre',
								Clave = '$clave',
								Valor = '$valor',
								Clase = '$clase',
								Factor = '$factor',
								Importe = '$importeImp'";
							$errorImp = $omodelo->_insertar($queryImp);

							if ($errorImp == 'si') {
								echo "Error 3: " . mysqli_error($omodelo->link);
								return;
							}
						}
					}
				}
			} else if (trim($tipoComprobante) == 'P - Complemento de Pago') {
				$documentosRelacionados = json_decode($documentosRelacionados, true);
				foreach ($documentosRelacionados as $docRel) {
					$uuid = $omodelo->link->real_escape_string($docRel['uuid']);
					$parcialidad = $omodelo->link->real_escape_string($docRel['parcialidad']);
					$saldoAnt = $omodelo->link->real_escape_string($docRel['saldoAnt']);
					$montoPagado = $omodelo->link->real_escape_string($docRel['montoPagado']);
					$saldoInsoluto = $omodelo->link->real_escape_string($docRel['saldoInsoluto']);

					$query1 = "INSERT INTO docs_relacionados SET 
						FK_Factura = '$idFactura',
						UUID = '$uuid', 
						Parcialidad = '$parcialidad', 
						Saldo_Anterior = '$saldoAnt', 
						Importe_Pagado = '$montoPagado', 
						Saldo_Insoluto = '$saldoInsoluto'";
					$error1 = $omodelo->_insertar($query1);

					if ($error1 == 'si') {
						echo "Error 2: " . mysqli_error($omodelo->link);
						return;
					} else {
						$idDetalle = $omodelo->link->insert_id;

						$imps = isset($docRel['impuestos']) ? $docRel['impuestos'] : [];
						foreach ($imps as $imp) {
							$clave = $imp['impuesto'];
							$nombre = $omodelo->link->real_escape_string($imp['nombre']);
							$factor = $omodelo->link->real_escape_string($imp['factor']); // Tasa o Cuota
							$clase = $omodelo->link->real_escape_string($imp['tipo']); // Trasladado o Retenido
							$valor = $omodelo->link->real_escape_string($imp['tasa']);
							$importeImp = $omodelo->link->real_escape_string($imp['importe']);

							$queryImp = "INSERT INTO impuestos_docs SET
								FK_Detalle_Docs = '$idDetalle',
								Nombre = '$nombre',
								Clave = '$clave',
								Valor = '$valor',
								Clase = '$clase',
								Factor = '$factor',
								Importe = '$importeImp'";
							$errorImp = $omodelo->_insertar($queryImp);

							if ($errorImp == 'si') {
								echo "Error 3: " . mysqli_error($omodelo->link);
								return;
							}
						}
					}
				}
			}

			if ($timbrar == 'true') {
				$this->_timbrar($idFactura);
			} else {
				echo 'Correcto';
			}

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	public function _modificar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$id = $omodelo->link->real_escape_string($id);
		$fechaEmision = $omodelo->link->real_escape_string($fechaEmision);
		$tipoComprobante = $omodelo->link->real_escape_string($tipoComprobante);
		$formaPago = $omodelo->link->real_escape_string($formaPago);
		$metodoPago = $omodelo->link->real_escape_string($metodoPago);

		$periodicidad = $omodelo->link->real_escape_string($periodicidad);
		$mes = $omodelo->link->real_escape_string($mes);
		$anio = $omodelo->link->real_escape_string($anio);

		$rfcReceptor = $omodelo->link->real_escape_string($rfcReceptor);
		$nombreReceptor = $omodelo->link->real_escape_string($nombreReceptor);
		$usoCfdi = $omodelo->link->real_escape_string($usoCfdi);
		$regimenFiscalReceptor = $omodelo->link->real_escape_string($regimenFiscalReceptor);
		$cpReceptor = $omodelo->link->real_escape_string($cpReceptor);
		$email = $omodelo->link->real_escape_string($email);

		$subtotal = $omodelo->link->real_escape_string($subtotal);
		$descuento = $omodelo->link->real_escape_string($descuento);
		$impuestos = $omodelo->link->real_escape_string($impuestos);
		$total = $omodelo->link->real_escape_string($total);

		$global = $omodelo->link->real_escape_string($global);
		$general = $omodelo->link->real_escape_string($general);

		$timbrar = $omodelo->link->real_escape_string($timbrar);
		$venta = $omodelo->link->real_escape_string($venta);

		$query = "UPDATE facturas SET 
			Global = '$global',
			General = '$general',

			Nombre_Emisor = (SELECT Nombre FROM configuracion_facturacion LIMIT 1), 
			RFC_Emisor = (SELECT RFC FROM configuracion_facturacion LIMIT 1), 
			CP_Emisor = (SELECT CP FROM configuracion_facturacion LIMIT 1), 
			Regimen_Emisor = (SELECT Regimen FROM configuracion_facturacion LIMIT 1), 

			Nombre_Receptor = '$nombreReceptor',
			RFC_Receptor = '$rfcReceptor',
			CP_Receptor = '$cpReceptor',
			Regimen_Receptor = '$regimenFiscalReceptor',
			Email = '$email',

			Version_CFDI = '4.0', 
			Metodo_Pago = '$metodoPago', 
			Forma_Pago = '$formaPago', 
			Uso_CFDI = '$usoCfdi', 
			Tipo_Comprobante = '$tipoComprobante', 
			Moneda = 'MXN',

			Periodicidad = '$periodicidad', 
			Mes = '$mes', 
			Ano = '$anio',
			
			Subtotal = '$subtotal',
			Descuento = '$descuento',
			Impuesto = '$impuestos',
			Total = '$total',

			Estatus = 'Pendiente', 
			Fecha_Emision = '$fechaEmision',
			FK_Venta = '$venta'
		WHERE ID_Factura = '$id'";
		$error = $omodelo->_insertar($query);

		if ($error == 'si') {
			echo "Error 1: " . mysqli_error($omodelo->link);
		} else {
			if (trim($tipoComprobante) == 'I - Ingreso') {
				$query2 = "DELETE FROM detalles_facturas WHERE FK_Factura = '$id'";
				$error2 = $omodelo->_insertar($query2);

				if ($error2 == 'si') {
					echo "Error 2: " . mysqli_error($omodelo->link);
				} else {
					$conceptos = json_decode($conceptos, true);

					foreach ($conceptos as $concepto) {
						$claveProdServ = $omodelo->link->real_escape_string($concepto['claveProdServ']);
						$descripcion = $omodelo->link->real_escape_string($concepto['descripcion']);
						$unidad = $omodelo->link->real_escape_string($concepto['unidad']);
						$cantidad = $omodelo->link->real_escape_string($concepto['cantidad']);
						$precioUnitario = $omodelo->link->real_escape_string($concepto['precioUnitario']);
						$subtotalC = $omodelo->link->real_escape_string($concepto['subtotal']);
						$descuentoC = $omodelo->link->real_escape_string($concepto['descuento']);
						$base = $omodelo->link->real_escape_string($concepto['base']);
						$impuestos_total = $omodelo->link->real_escape_string($concepto['impuestos_total']);
						$totalC = $omodelo->link->real_escape_string($concepto['total']);

						$query1 = "INSERT INTO detalles_facturas SET 
							FK_Factura = '$id', 
							Producto = '$descripcion', 
							Codigo_Producto = '$claveProdServ', 
							Codigo_Unidad = '$unidad', 
							Precio_Unitario = '$precioUnitario', 
							Cantidad = '$cantidad', 
							Subtotal = '$subtotalC', 
							Descuento = '$descuentoC', 
							Total = '$base', 
							Impuesto = '$impuestos_total', 
							Importe = '$totalC'";
						$error1 = $omodelo->_insertar($query1);

						if ($error1 == 'si') {
							echo "Error 3: " . mysqli_error($omodelo->link);
							return;
						} else {
							$idDetalle = $omodelo->link->insert_id;

							$imps = isset($concepto['impuestos']) ? $concepto['impuestos'] : [];
							foreach ($imps as $imp) {
								$nombre = '';
								switch ($imp['impuesto']) {
									case '002':
										$nombre = 'IVA';
										break;
									case '001':
										$nombre = 'ISR';
										break;
									case '003':
										$nombre = 'IEPS';
										break;
									default:
										$nombre = $imp['impuesto'];
								}

								$clave = $imp['impuesto'];
								$factor = $omodelo->link->real_escape_string($imp['tipoFactor']);
								$clase = $omodelo->link->real_escape_string($imp['tipo']);
								$valor = $omodelo->link->real_escape_string($imp['valor']);
								$importeImp = $omodelo->link->real_escape_string($imp['importe']);

								$queryImp = "INSERT INTO impuestos_factura SET
									FK_Detalle_Factura = '$idDetalle',
									Nombre = '$nombre',
									Clave = '$clave',
									Valor = '$valor',
									Clase = '$clase',
									Factor = '$factor',
									Importe = '$importeImp'";
								$errorImp = $omodelo->_insertar($queryImp);

								if ($errorImp == 'si') {
									echo "Error 4: " . mysqli_error($omodelo->link);
									return;
								}
							}
						}
					}
				}
			} else if (trim($tipoComprobante) == 'P - Complemento de Pago') {
				$query2 = "DELETE FROM docs_relacionados WHERE FK_Factura = '$id'";
				$error2 = $omodelo->_insertar($query2);

				if ($error2 == 'si') {
					echo "Error 2: " . mysqli_error($omodelo->link);
				} else {
					$documentosRelacionados = json_decode($documentosRelacionados, true);
					foreach ($documentosRelacionados as $docRel) {
						$uuid = $omodelo->link->real_escape_string($docRel['uuid']);
						$parcialidad = $omodelo->link->real_escape_string($docRel['parcialidad']);
						$saldoAnt = $omodelo->link->real_escape_string($docRel['saldoAnt']);
						$montoPagado = $omodelo->link->real_escape_string($docRel['montoPagado']);
						$saldoInsoluto = $omodelo->link->real_escape_string($docRel['saldoInsoluto']);

						$query1 = "INSERT INTO docs_relacionados SET 
							FK_Factura = '$id',
							UUID = '$uuid', 
							Parcialidad = '$parcialidad', 
							Saldo_Anterior = '$saldoAnt', 
							Importe_Pagado = '$montoPagado', 
							Saldo_Insoluto = '$saldoInsoluto'";
						$error1 = $omodelo->_insertar($query1);

						if ($error1 == 'si') {
							echo "Error 2: " . mysqli_error($omodelo->link);
							return;
						} else {
							$idDetalle = $omodelo->link->insert_id;

							$imps = isset($docRel['impuestos']) ? $docRel['impuestos'] : [];
							foreach ($imps as $imp) {
								$clave = $imp['impuesto'];
								$nombre = $omodelo->link->real_escape_string($imp['nombre']);
								$factor = $omodelo->link->real_escape_string($imp['factor']); // Tasa o Cuota
								$clase = $omodelo->link->real_escape_string($imp['tipo']); // Trasladado o Retenido
								$valor = $omodelo->link->real_escape_string($imp['tasa']);
								$importeImp = $omodelo->link->real_escape_string($imp['importe']);

								$queryImp = "INSERT INTO impuestos_docs SET
								FK_Detalle_Docs = '$idDetalle',
								Nombre = '$nombre',
								Clave = '$clave',
								Valor = '$valor',
								Clase = '$clase',
								Factor = '$factor',
								Importe = '$importeImp'";
								$errorImp = $omodelo->_insertar($queryImp);

								if ($errorImp == 'si') {
									echo "Error 3: " . mysqli_error($omodelo->link);
									return;
								}
							}
						}
					}
				}
			}

			if ($timbrar == 'true') {
				$this->_timbrar($id);
			} else {
				echo 'Correcto';
			}

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	public function _eliminar()
	{
		$omodelo = new m_modelo();
		extract($_POST);

		$id = $omodelo->link->real_escape_string($id);
		$carpeta = './controladores/facturas/';

		$query = "SELECT PDF, XML FROM facturas WHERE ID_Factura = '$id'";
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($row == 'si') {
			echo "Error 1: " . mysqli_error($omodelo->link);
		} else {
			if ($numerofilas > 0) {
				if ($row[0]['PDF'] != '' && file_exists($carpeta . $row[0]['PDF'])) {
					unlink($carpeta . $row[0]['PDF']);
				}

				if ($row[0]['XML'] != '' && file_exists($carpeta . $row[0]['XML'])) {
					unlink($carpeta . $row[0]['XML']);
				}
			}
		}

		$query = "DELETE FROM facturas WHERE ID_Factura = '$id' AND Estatus = 'Pendiente'";
		$error = $omodelo->_insertar($query);

		if ($error == 'si') {
			echo "Error 2: " . mysqli_error($omodelo->link);
		} else {

			echo 'Correcto';

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	private function _timbrar($idFactura)
	{
		$omodelo = new m_modelo();
		$timbres = 0;
		$bd = $_SESSION['user_punto_bd'];
		
		$omodelo->_insertar("USE punto_subs");
		$queryTim = "SELECT Timbres FROM suscripciones WHERE ID_Suscripcion = '$bd'";
		$rowTim = $omodelo->_consultar($queryTim);

		$timbres = isset($rowTim[0]['Timbres']) ? $rowTim[0]['Timbres'] : 0;
		if ($timbres < 1) {
			echo 'No hay timbres disponibles';

			return;
		}

		$omodelo->_insertar("USE punto_venta_$bd");
		// ===============================
		// TOKEN FIJO FACTURO POR TI
		// ===============================
		//Pruebas
		$token = "eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoialYrdVVUYmtWNmUxRmNZb2cvNWtGQT09IiwibmJmIjoxNzc0NjYzNTYyLCJleHAiOjE3NzcyNTU1NjIsImlzcyI6IlNjYWZhbmRyYVNlcnZpY2lvcyIsImF1ZCI6IlNjYWZhbmRyYSBTZXJ2aWNpb3MiLCJJZEVtcHJlc2EiOiJqVit1VVRia1Y2ZTFGY1lvZy81a0ZBPT0iLCJJZFVzdWFyaW8iOiJidXlaYzFMWUl5VURaSGhGR3NqaGdRPT0ifQ.3bAp7vPet756Ze4RKcqsO_C2q_shu3avhNJx5Wvt1fA";
		//Real
		//$token = "eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiZkJQQnM0dXUxY0hCMVBKckl4NXI0UT09IiwibmJmIjoxNzg2NDI0OTE0LCJleHAiOjE3ODkwMTY5MTQsImlzcyI6IlNjYWZhbmRyYVNlcnZpY2lvcyIsImF1ZCI6IlNjYWZhbmRyYSBTZXJ2aWNpb3MiLCJJZEVtcHJlc2EiOiJmQlBCczR1dTFjSEIxUEpySXg1cjRRPT0iLCJJZFVzdWFyaW8iOiJWYXlPRGZXdGdtZFFteG5vZzc3R3V3PT0ifQ.QwFB0I4tDDLsl0Ke1C0XTvvALKq6hTj9ihc-Yudjzfc";

		// ===============================
		// DATOS FACTURA
		// ===============================
		$idFactura = $omodelo->link->real_escape_string($idFactura);

		$query = "SELECT * FROM facturas WHERE ID_Factura = '$idFactura' AND Estatus = 'Pendiente' LIMIT 1";
		$factura = $omodelo->_consultar($query);

		if ($factura == 'si' || $omodelo->numerofilas == 0) {
			echo 'Error: Factura no encontrada o ya timbrada';
			return;
		}

		$f = $factura[0];

		// ===============================
		// DATOS EMISOR (CONFIGURACION)
		// ===============================
		$query = "SELECT * FROM configuracion_facturacion LIMIT 1";
		$config = $omodelo->_consultar($query);

		if ($config == 'si' || $omodelo->numerofilas == 0) {
			echo 'Error: No existe configuración del emisor';
			return;
		}

		$c = $config[0];
		$regimenEmisor = trim(substr($c['Regimen'], 0, 3));

		// ==========================================
		// DETERMINAR TIPO (POR PRIMERA LETRA)
		// ==========================================
		$tipoLetra = strtoupper(substr(trim($f['Tipo_Comprobante']), 0, 1));
		$esPago = ($tipoLetra === 'P');

		// Mapeo de Tipo CFDI
		switch ($tipoLetra) {
			case 'I':
				$tipoCFDI_Completo = "Ingreso";
				break;
			case 'E':
				$tipoCFDI_Completo = "Egreso";
				break;
			case 'P':
				$tipoCFDI_Completo = "Pago";
				break;
			case 'T':
				$tipoCFDI_Completo = "Trasladado";
				break;
			case 'N':
				$tipoCFDI_Completo = "Nomina";
				break;
			default:
				$tipoCFDI_Completo = "Ingreso";
				break;
		}

		$conceptos = [];
		$complementoJSON = null;
		$subtotal_calculado = 0;
		$descuento_total = 0;
		$impuesto_total_calculado = 0;

		if ($esPago) {
			// ==========================================
			// LÓGICA PARA COMPLEMENTO DE PAGO 2.0
			// ==========================================
			$queryRel = "SELECT * FROM docs_relacionados WHERE FK_Factura = '$idFactura'";
			$resRel = $omodelo->_consultar($queryRel);

			$documentosRelacionados = [];
			$montoTotalPagos = 0;
			$agrupacionImpuestosPago = [];
			$totalesSAT = [
				"MontoTotalPagos" => 0
			];

			if ($resRel != 'si' && $omodelo->numerofilas > 0) {
				foreach ($resRel as $dr) {
					if (!is_array($dr)) continue;
					$idDoc = $dr['ID_Documento'];
					$importePagado = floatval($dr['Importe_Pagado']);
					$montoTotalPagos += $importePagado;

					// Buscar Impuestos del Documento Relacionado
					$queryImp = "SELECT * FROM impuestos_docs WHERE FK_Detalle_Docs = '$idDoc'";
					$resImp = $omodelo->_consultar($queryImp);

					$trasladosDR = [];
					$retenidosDR = [];
					$objetoImpuesto = "01";

					if ($resImp != 'si' && $omodelo->numerofilas > 0) {
						$objetoImpuesto = "02";
						foreach ($resImp as $imp) {
							if (!is_array($imp)) continue;

							$esTasa = (strcasecmp($imp['Factor'] ?? 'Tasa', 'Tasa') == 0);
							$valorOriginal = floatval($imp['Valor']);
							$tasaOCuota = ($valorOriginal > 1) ? ($valorOriginal / 100) : $valorOriginal;
							$importeImp = floatval($imp['Importe']);

							// Cálculo de Base
							$baseGravable = ($tasaOCuota > 0) ? ($importeImp / $tasaOCuota) : $importePagado;

							$itemImp = [
								"Impuesto" => intval($imp['Clave']),
								"Factor" => $esTasa ? 1 : 2,
								"Base" => round($baseGravable, 2),
								"Tasa" => number_format($tasaOCuota, 6, '.', ''),
								"Importe" => round($importeImp, 2)
							];

							$clase = strtoupper($imp['Clase'] ?? '');
							if ($clase == 'RETENCION' || $clase == 'RETENIDO') {
								$retenidosDR[] = $itemImp;
								$llave = "R_" . $imp['Clave'] . "_" . number_format($tasaOCuota, 6, '.', '');
								// Acumulación dinámica de totales de retención
								$nombreRet = ($imp['Clave'] == 1) ? "TotalRetencionesISR" : (($imp['Clave'] == 2) ? "TotalRetencionesIVA" : "TotalRetencionesIEPS");
								if (!isset($totalesSAT[$nombreRet])) $totalesSAT[$nombreRet] = 0;
								$totalesSAT[$nombreRet] += $importeImp;
							} else {
								$trasladosDR[] = $itemImp;
								$llave = "T_" . $imp['Clave'] . "_" . number_format($tasaOCuota, 6, '.', '');
								// Acumulación dinámica de totales de traslado (Ej: TotalTrasladosBaseIVA16)
								$nomImp = ($imp['Clave'] == 2) ? "IVA" : (($imp['Clave'] == 3) ? "IEPS" : "ISR");
								$pct = round($tasaOCuota * 100);
								$keyBase = "TotalTrasladosBase" . $nomImp . $pct;
								$keyImp = "TotalTrasladosImpuesto" . $nomImp . $pct;

								if (!isset($totalesSAT[$keyBase])) $totalesSAT[$keyBase] = 0;
								if (!isset($totalesSAT[$keyImp])) $totalesSAT[$keyImp] = 0;
								$totalesSAT[$keyBase] += $baseGravable;
								$totalesSAT[$keyImp] += $importeImp;
							}

							if (!isset($agrupacionImpuestosPago[$llave])) {
								$agrupacionImpuestosPago[$llave] = array_merge($itemImp, ["Clase" => $clase]);
							} else {
								$agrupacionImpuestosPago[$llave]['Base'] += $baseGravable;
								$agrupacionImpuestosPago[$llave]['Importe'] += $importeImp;
							}
						}
					}

					$docRel = [
						"IdDocumento" => $dr['UUID'],
						"Moneda" => "MXN",
						"Equivalencia" => 1,
						"NumeroParcialidad" => intval($dr['Parcialidad']),
						"ImporteSaldoAnterior" => round(floatval($dr['Saldo_Anterior']), 2),
						"ImportePagado" => round($importePagado, 2),
						"ImporteSaldoInsoluto" => round(floatval($dr['Saldo_Insoluto']), 2),
						"ObjetoDeImpuesto" => $objetoImpuesto
					];

					if (!empty($trasladosDR)) $docRel["Impuestos"]["Trasladados"] = $trasladosDR;
					if (!empty($retenidosDR)) $docRel["Impuestos"]["Retenidos"] = $retenidosDR;

					$documentosRelacionados[] = $docRel;
				}
			}

			$nodoImpuestosGlobal = [];
			$totalesSAT["MontoTotalPagos"] = round($montoTotalPagos, 2);

			foreach ($agrupacionImpuestosPago as $ag) {
				$itemG = [
					"Impuesto" => $ag['Impuesto'],
					"Factor" => $ag['Factor'],
					"Base" => round($ag['Base'], 2),
					"Tasa" => number_format(floatval($ag['Tasa']), 6, '.', ''),
					"Importe" => round($ag['Importe'], 2)
				];
				if ($ag['Clase'] == 'RETENCION' || $ag['Clase'] == 'RETENIDO') {
					$nodoImpuestosGlobal["Retenidos"][] = $itemG;
				} else {
					$nodoImpuestosGlobal["Trasladados"][] = $itemG;
				}
			}

			$complementoJSON = [
				"TipoComplemento" => 28,
				"PagosV20" => [
					"Pagos" => [[
						"FechaPago" => str_replace(" ", "T", $f['Fecha_Emision']),
						"FormaPago" => substr($f['Forma_Pago'], 0, 2),
						"Moneda" => "MXN",
						"TipoCambio" => 1,
						"DocumentosRelacionados" => $documentosRelacionados,
						"Impuestos" => $nodoImpuestosGlobal
					]],
					"Totales" => $totalesSAT
				]
			];
			$conceptos = [];
		} else {
			// ===============================
			// LÓGICA DE INGRESO (CONCEPTOS)
			// ===============================
			$query = "SELECT * FROM detalles_facturas WHERE FK_Factura = '$idFactura'";
			$detalles = $omodelo->_consultar($query);

			foreach ($detalles as $d) {
				if (!is_array($d)) continue;
				$idDetalle = $d['ID_Detalle_Factura'];
				$cantidad = floatval($d['Cantidad']);
				$precioUnitario = round($d['Precio_Unitario'], 2);
				$importeConcepto = round($cantidad * $precioUnitario, 2);
				$descuentoConcepto = round(floatval($d['Descuento']), 2);
				$baseGravable = round($importeConcepto - $descuentoConcepto, 2);

				$subtotal_calculado += $importeConcepto;
				$descuento_total += $descuentoConcepto;

				$queryImp = "SELECT * FROM impuestos_factura WHERE FK_Detalle_Factura = '$idDetalle'";
				$resImp = $omodelo->_consultar($queryImp);
				$listaImpuestos = [];
				$objetoImpuesto = "01";

				if ($resImp != 'si' && $omodelo->numerofilas > 0) {
					$objetoImpuesto = "02";
					foreach ($resImp as $imp) {
						if (!is_array($imp)) continue;
						$valorTasa = floatval($imp['Valor']);
						$esTasa = (strcasecmp($imp['Factor'], 'Tasa') == 0);
						$tasaOCuotaFinal = $esTasa ? ($valorTasa / 100) : $valorTasa;
						$baseParaNodo = $esTasa ? $baseGravable : $cantidad;
						$imp_importe = round($baseParaNodo * $tasaOCuotaFinal, 2);

						if (strcasecmp($imp['Clase'], 'Trasladado') == 0) $impuesto_total_calculado += $imp_importe;
						else $impuesto_total_calculado -= $imp_importe;

						$listaImpuestos[] = [
							"TipoImpuesto" => (strcasecmp($imp['Clase'], 'Trasladado') == 0) ? 1 : 2,
							"Impuesto" => intval($imp['Clave']),
							"Factor" => $esTasa ? 1 : 2,
							"Base" => $baseParaNodo,
							"Tasa" => number_format($tasaOCuotaFinal, 6, '.', ''),
							"ImpuestoImporte" => $imp_importe
						];
					}
				}

				$itemConcepto = [
					"Cantidad" => $cantidad,
					"CodigoUnidad" => $d['Codigo_Unidad'],
					"Unidad" => $d['Codigo_Unidad'] == 'H87' ? "PIEZA" : ($d['Unidad'] ?? "PIEZA"),
					"CodigoProducto" => $d['Codigo_Producto'],
					"Producto" => $d['Producto'],
					"PrecioUnitario" => $precioUnitario,
					"Importe" => $importeConcepto,
					"ObjetoDeImpuesto" => $objetoImpuesto,
					"Impuestos" => $listaImpuestos
				];

				// SOLO SE AGREGA SI ES MAYOR A CERO, SI ES 0 NO SE ENVÍA LA LLAVE
				if (floatval($descuentoConcepto) > 0) {
					$itemConcepto["Descuento"] = round($descuentoConcepto, 2);
				}

				$conceptos[] = $itemConcepto;
			}
		}

		// ===============================
		// CERTIFICADOS Y LOGO
		// ===============================
		$rutaCerts = __DIR__ . "/../vistas/assets/files/certificados/";
		$cerBase64 = base64_encode(@file_get_contents($rutaCerts . $c['Certificado']));
		$keyBase64 = base64_encode(@file_get_contents($rutaCerts . $c['Key_Cer']));

		$logotipo_base64 = '';
		$logoPath = __DIR__ . '/../vistas/assets/images/logos/logo.png';

		$query = "SELECT * FROM configuracion LIMIT 1";
		$configImg = $omodelo->_consultar($query);

		if ($configImg != 'si' && $omodelo->numerofilas > 0 && trim($configImg[0]['Foto']) != '') {
			$logoPath = __DIR__ . '/../vistas/assets/images/configuracion/' . $configImg[0]['Foto'];
		}

		if (file_exists($logoPath)) $logotipo_base64 = base64_encode(file_get_contents($logoPath));

		// ===============================
		// JSON CFDI
		// ===============================
		$data = [
			"DatosGenerales" => [
				"Version" => "4.0",
				"CSD" => $cerBase64,
				"LlavePrivada" => $keyBase64,
				"CSDPassword" => $c['Contrasena'],
				"CFDI" => $esPago ? "Pago" : "Factura",
				"GeneraPDF" => true,
				"Logotipo" => $logotipo_base64,
				"TipoCFDI" => $tipoCFDI_Completo,
				"OpcionDecimales" => 2,
				"NumeroDecimales" => 2
			],
			"Encabezado" => [
				"Emisor" => [
					"RFC" => $c['RFC'],
					"NombreRazonSocial" => $c['Nombre'],
					"RegimenFiscal" => $regimenEmisor,
					"Direccion" => [["Calle" => $c['Domicilio'], "CodigoPostal" => $c['CP']]]
				],
				"Receptor" => [
					"RFC" => $f['RFC_Receptor'],
					"NombreRazonSocial" => $f['Nombre_Receptor'],
					"UsoCFDI" => $esPago ? "CP01" : trim(substr($f['Uso_CFDI'], 0, 3)),
					"RegimenFiscal" => trim(substr($f['Regimen_Receptor'], 0, 3)),
					"DomicilioFiscalReceptor" => $f['CP_Receptor']
				],
				"Fecha" => str_replace(" ", "T", $f['Fecha_Emision']),
				"Serie" => $f['Serie'] ?? "A",
				"Folio" => $idFactura,
				"MetodoPago" => $esPago ? null : trim(substr($f['Metodo_Pago'], 0, 3)),
				"FormaPago" => $esPago ? null : trim(substr($f['Forma_Pago'], 0, 2)),
				"Moneda" => $esPago ? null : $f['Moneda'],
				"LugarExpedicion" => $c['CP'],
				"SubTotal" => $esPago ? 0 : round($subtotal_calculado, 2),
				"Descuento" => $esPago ? 0 : round($descuento_total, 2),
				"Total" => $esPago ? 0 : round(($subtotal_calculado - $descuento_total) + $impuesto_total_calculado, 2)
			],
			"Conceptos" => $conceptos
		];

		if ($esPago) $data["Complemento"] = $complementoJSON;

		if ($f['Global'] === '1' && !$esPago) {
			$data["Encabezado"]["InformacionFacturaGlobal"] = [
				"Periodicidad" => substr($f['Periodicidad'], 0, 2),
				"Meses" => substr($f['Mes'], 0, 2),
				"Año" => intval($f['Ano'])
			];
		}

		if ($f['Email'] !== '') {
			$data["DatosGenerales"]["EnviaEmail"] = true;
			$data["DatosGenerales"]["ReceptorEmail"] = $f['Email'];
			$data["DatosGenerales"]["EmailMensaje"] = "Envio automático de comprobante fiscal.";
		}

		//print_r($data);
		// ===============================
		// ENVIAR AL PAC
		// ===============================
		$client = new \GuzzleHttp\Client();
		try {
			$response = $client->post("https://testapi.facturoporti.com.mx/servicios/timbrar/json", [
				"headers" => [
					"authorization" => "Bearer $token",
					"accept" => "application/json",
					"content-type" => "application/json"
				],
				"body" => json_encode($data, JSON_UNESCAPED_UNICODE),
				"timeout" => 60
			]);

			$res = json_decode($response->getBody()->getContents(), true);

			if (!isset($res['cfdiTimbrado']['respuesta']['uuid'])) {
				echo json_encode(['errorPAC' => $res]);
				return;
			}

			// ===============================
			// GUARDAR XML / PDF Y ACTUALIZAR
			// ===============================
			$uuid = $res['cfdiTimbrado']['respuesta']['uuid'];
			$xml  = $res['cfdiTimbrado']['respuesta']['cfdixml'];
			$pdf  = base64_decode($res['cfdiTimbrado']['respuesta']['pdf']);

			$rutaFacturas = __DIR__ . "/facturas/";
			if (!file_exists($rutaFacturas)) mkdir($rutaFacturas, 0777, true);

			file_put_contents($rutaFacturas . $uuid . ".xml", $xml);
			file_put_contents($rutaFacturas . $uuid . ".pdf", $pdf);

			$queryUpd = "UPDATE facturas SET 
        Estatus = 'Timbrada',
        Folio_Fiscal = '$uuid',
        Fecha_Timbrado = NOW(),
        XML = '$uuid.xml',
        PDF = '$uuid.pdf'
      WHERE ID_Factura = '$idFactura'";
			$omodelo->_insertar($queryUpd);

			$bd = $_SESSION['user_punto_bd'];

			$omodelo->_insertar("USE punto_subs");
			$querySub = "UPDATE suscripciones SET 
        Timbres = Timbres - 1
      WHERE ID_Suscripcion = '$bd'";
			$omodelo->_insertar($querySub);

			$_SESSION['user_punto_venta']['Sub']['Timbres'] = $timbres - 1;

			echo 'Correcto';
		} catch (\Exception $e) {
			echo "Error de conexión: " . $e->getMessage();
		}
	}
}
