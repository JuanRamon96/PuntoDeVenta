<?php
class suscripcion
{

	public function _modificar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d');

		$tipo = $omodelo->link->real_escape_string($tipo);
		$fechaFin = $_SESSION['user_punto_venta']['Sub']['Fecha_Vencimiento'];
		$idBd = $_SESSION['user_punto_bd'];

		// Base: si ya venció, se parte de hoy; si no, se parte de la fecha de vencimiento actual
		if (strtotime($fecha) > strtotime($fechaFin)) {
			$fechaBase = new DateTime(); // hoy
		} else {
			$fechaBase = new DateTime($fechaFin);
		}

		// Sumar el intervalo correspondiente
		if ($tipo == "ano") {
			$fechaBase->modify('+12 months');
		} else {
			$fechaBase->modify('+1 month');
		}

		$nuevaFecha = $fechaBase->format('Y-m-d H:i:s');

		$omodelo->_insertar("USE punto_subs");
		$query = "UPDATE suscripciones SET Fecha_Vencimiento = '$nuevaFecha' WHERE ID_Suscripcion = '$idBd'";
		$error = $omodelo->_insertar($query);

		if ($error == "si") {
			echo "Error 2: " . mysqli_error($omodelo->link);
		} else {
			echo "Correcto";
			$_SESSION['user_punto_venta']['Sub']['Fecha_Vencimiento'] = $nuevaFecha;

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}
}
