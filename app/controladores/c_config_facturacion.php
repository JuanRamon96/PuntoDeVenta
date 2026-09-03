<?php

class config_facturacion
{

	public function _insertar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$rfcEscaped       = $omodelo->link->real_escape_string($rfcFacturacion);
		$nombreEscaped    = $omodelo->link->real_escape_string($nombreFacturacion);
		$domicilioEscaped = $omodelo->link->real_escape_string($domicilioFacturacion);
		$cpEscaped        = $omodelo->link->real_escape_string($cpFacturacion);
		$regimenEscaped   = $omodelo->link->real_escape_string($regimenFacturacion);

		$sqlContra = '';
		if (isset($contraFacturacion) && $contraFacturacion != '') {
			$contraEscaped = $omodelo->link->real_escape_string($contraFacturacion);
			$sqlContra = ", Contrasena = '$contraEscaped'";
		}

		$query = "UPDATE configuracion_facturacion SET 
                RFC = '$rfcEscaped', 
                Nombre = '$nombreEscaped', 
                Regimen = '$regimenEscaped',
                CP = '$cpEscaped',
                Domicilio = '$domicilioEscaped'
                $sqlContra";

		if ($omodelo->_insertar($query) == "si") {
			echo "Error 1: " . mysqli_error($omodelo->link);
			return;
		}

		$bd = isset($_SESSION['user_punto_bd']) ? $_SESSION['user_punto_bd'] : '';
		$carpeta = "vistas/assets/files/certificados/";

		// 3. Helper para procesar la subida de archivos con prefijo de sesión
		$procesarArchivo = function ($inputName, $campoBD, $extEsperada) use ($omodelo, $carpeta, $bd) {
			if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
				return true; // Si no hay archivo subido, omitir
			}

			$file = $_FILES[$inputName];
			$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

			// Validar extensión real en lugar del tipo MIME
			if ($ext !== $extEsperada) {
				echo "Error de Formato: El archivo debe ser .$extEsperada";
				return false;
			}

			if ($file['size'] > (1024 * 1024 * 10)) {
				echo "Error de Peso: El archivo supera los 10MB";
				return false;
			}

			// Eliminar archivo anterior si existe
			$res = $omodelo->_consultar("SELECT $campoBD FROM configuracion_facturacion LIMIT 1");
			if ($res != "si" && $omodelo->numerofilas > 0) {
				$archivoAntiguo = $res[0][$campoBD];
				if (!empty($archivoAntiguo) && file_exists($carpeta . $archivoAntiguo)) {
					unlink($carpeta . $archivoAntiguo);
				}
			}

			$nombreArchivoFinal = $bd . '_' . $file['name'];
			$updateQuery = "UPDATE configuracion_facturacion SET $campoBD = '$nombreArchivoFinal'";
			if ($omodelo->_insertar($updateQuery) == "si") {
				echo "Error al guardar $campoBD: " . mysqli_error($omodelo->link);
				return false;
			}

			return move_uploaded_file($file['tmp_name'], $carpeta . $nombreArchivoFinal);
		};

		// Procesar .cer y .key
		if (!$procesarArchivo('certificadoFacturacion', 'Certificado', 'cer')) return;
		if (!$procesarArchivo('keyFacturacion', 'Key_Cer', 'key')) return;

		echo "Correcto";
	}
}
