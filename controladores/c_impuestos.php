<?php
class impuestos
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

		$busqueda = '';
		if (trim($buscar) != '') {
			$separa = explode(' ', trim($buscar));
			$busqueda = 'WHERE ';
			for ($i = 0; $i < count($separa); $i++) {
				$busqueda .= "CONCAT(Nombre, Porcentaje, Clave_CFDI, Tipo_Factor, Clase) REGEXP '" . $separa[$i] . "'";
				if ($i < (count($separa) - 1)) {
					$busqueda .= ' AND ';
				}
			}
		}

		$query = "SELECT 
			ID_Impuesto, 
			Nombre, 
			Porcentaje, 
			Clave_CFDI, 
			Tipo_Factor, 
			Clase, 
			(SELECT COUNT(*) FROM impuestos $busqueda) AS Num 
		FROM impuestos $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($row == "si") {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			if ($numerofilas > 0) {
				for ($i = 0; $i < $numerofilas; $i++) {
					$botonPermisosModificar = '<button class="btn btn-warning btn-sm ModificarImpuesto" attrid="' . $row[$i]['ID_Impuesto'] . '" nombre="' . $row[$i]['Nombre'] . '"><i class="fas fa-edit"></i></button>';

					$botonPermisosEliminar = '<button class="btn btn-danger btn-sm EliminarImpuesto" attrid="' . $row[$i]['ID_Impuesto'] . '" nombre="' . $row[$i]['Nombre'] . '"><i class="fas fa-trash"></i></button>';

					$arreglo['data'][$i] = array(
						'ID' => $row[$i]['ID_Impuesto'],
						'Nombre' => $row[$i]['Nombre'],
						'Porcentaje' => number_format($row[$i]['Porcentaje'], 2) . "%",
						'Clave' => $row[$i]['Clave_CFDI'],
						'Tipo' => $row[$i]['Tipo_Factor'],
						'Clase' => $row[$i]['Clase'],
						'Acciones' => $botonPermisosModificar . ' ' . $botonPermisosEliminar,
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
		$Nombre = $omodelo->link->real_escape_string($Nombre);
		$Porcentaje = $omodelo->link->real_escape_string($Porcentaje);
		$Clave = $omodelo->link->real_escape_string($Clave);
		$Clase = $omodelo->link->real_escape_string($Clase);
		$Tipo = $omodelo->link->real_escape_string($Tipo);

		$query = "INSERT INTO impuestos SET Nombre = '$Nombre', Porcentaje = '$Porcentaje', Clave_CFDI = '$Clave', Tipo_Factor = '$Tipo', Clase = '$Clase'";
		$row = $omodelo->_insertar($query);

		if ($row == "si") {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			echo "Correcto";
			$omodelo->movimiento($query,  $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	public function _modificar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$IDImpuesto = $omodelo->link->real_escape_string($IDImpuesto);
		$Nombre = $omodelo->link->real_escape_string($Nombre);
		$Porcentaje = $omodelo->link->real_escape_string($Porcentaje);
		$Clave = $omodelo->link->real_escape_string($Clave);
		$Clase = $omodelo->link->real_escape_string($Clase);
		$Tipo = $omodelo->link->real_escape_string($Tipo);

		$query = "UPDATE impuestos SET Nombre = '$Nombre', Porcentaje = '$Porcentaje', Clave_CFDI = '$Clave', Tipo_Factor = '$Tipo', Clase = '$Clase' WHERE ID_Impuesto = '$IDImpuesto'";
		$row = $omodelo->_insertar($query);

		if ($row == "si") {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			echo "Correcto";

			$omodelo->movimiento($query,  $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	public function _eliminar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$id =  $omodelo->link->real_escape_string($id);

		$query = "DELETE FROM impuestos WHERE ID_Impuesto='$id'";
		$error = $omodelo->_insertar($query);

		if ($error == "si") {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {
			echo "Correcto";

			$omodelo->movimiento($query,  $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	public function _detalles()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$arreglo = array();

		if ($tipo == "ConsultarImpuesto") {
			$IDImpuesto =  $omodelo->link->real_escape_string($IDImpuesto);

			$query = "SELECT ID_Impuesto, Nombre, Porcentaje, Clave_CFDI, Tipo_Factor, Clase FROM impuestos WHERE ID_Impuesto = '$IDImpuesto'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == 'si') {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					$arreglo = array(
						'ID_Impuesto' => $row[0]['ID_Impuesto'],
						'Nombre' => $row[0]['Nombre'],
						'Porcentaje' => $row[0]['Porcentaje'],
						'Clave' => $row[0]['Clave_CFDI'],
						'Tipo' => $row[0]['Tipo_Factor'],
						'Clase' => $row[0]['Clase']
					);
				}
			}

			echo json_encode($arreglo);
		}
	}
}
