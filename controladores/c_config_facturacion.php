<?php

class config_facturacion {

	public function _insertar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s');
		$fecha = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime ($fecha))); 

		$rfcFacturacion = trim($omodelo->link->real_escape_string($rfcFacturacion));
		$nombreFacturacion = trim($omodelo->link->real_escape_string($nombreFacturacion));
		$domicilioFacturacion = trim($omodelo->link->real_escape_string($domicilioFacturacion));
		$cpFacturacion = trim($omodelo->link->real_escape_string($cpFacturacion));
		$regimenFacturacion = $omodelo->link->real_escape_string($regimenFacturacion);
		$contraFacturacion = $omodelo->link->real_escape_string($contraFacturacion);

		$contra = '';
		if(trim($contraFacturacion) != ''){
			$contra = ", Contrasena = '$contraFacturacion'";
		}

		$query = "UPDATE configuracion_facturacion SET 
			RFC = '$rfcFacturacion', 
			Nombre = '$nombreFacturacion', 
			Regimen = '$regimenFacturacion',
			CP = '$cpFacturacion',
			Domicilio = '$domicilioFacturacion'
			$contra";
		$error = $omodelo->_insertar($query);

		if ($error == "si") {
			echo "Error 1: ".mysqli_error($omodelo->link);
		}else{
			$status1 = 1;$status2 = 1;
			$carpeta = "vistas/assets/files/certificados/";
			if ($_FILES['certificadoFacturacion']['size'] > 0 && $_FILES['certificadoFacturacion']['error'] == 0) {
				$file = $_FILES["certificadoFacturacion"];
				$nombreCer = $file["name"];
				$tipo = $file["type"];
				$ruta_provisionalCer = $file["tmp_name"];
				$size = $file["size"];

				if ($tipo != 'application/x-x509-ca-cert' && $tipo != ''){
					echo "Error 2 Formato";
				}else if ($size > (1024*1024*10)){
					echo "Error 3 Peso";
				}else{
					$status1 = 0;
					$ruta = $carpeta;
				}
			}

			if ($_FILES['keyFacturacion']['size'] > 0 && $_FILES['keyFacturacion']['error'] == 0) {
				$file = $_FILES["keyFacturacion"];
				$nombreKey = $file["name"];
				$tipo = $file["type"];
				$ruta_provisionalKey = $file["tmp_name"];
				$size = $file["size"];

				if ($tipo != 'application/octet-stream' && $tipo != ''){
					echo "Error 4 Formato";
				}else if ($size > (1024*1024*10)){
					echo "Error 5 Peso";
				}else{
					$status2 = 0;
					$ruta = $carpeta;
				}
			}
			
			if($status1 == 0 && $status2 == 0){
				$query2 = "SELECT Certificado, Key_Cer FROM configuracion_facturacion LIMIT 1";
				$row = $omodelo->_consultar($query2);
				$numerofilas = $omodelo->numerofilas;

				if ($row == "si") {
					echo "Error 7: ".mysqli_error($omodelo->link);
				}else{
					if ($numerofilas > 0) {
						if ($row[0]['Certificado'] != "" && file_exists($carpeta.$row[0]['Certificado'])) {
							unlink($carpeta.$row[0]['Certificado']);
						}
						if ($row[0]['Key_Cer'] != "" && file_exists($carpeta.$row[0]['Key_Cer'])) {
							unlink($carpeta.$row[0]['Key_Cer']);
						}
					}
					
					$query1 = "UPDATE configuracion_facturacion SET Certificado = '$nombreCer', Key_Cer = '$nombreKey'";
					$error1 = $omodelo->_insertar($query1);	

					if ($error1 == "si") {
						echo "Error 6: ".mysqli_error($omodelo->link); 
					}else{
						move_uploaded_file($ruta_provisionalCer,  $ruta.$nombreCer);
						move_uploaded_file($ruta_provisionalKey,  $ruta.$nombreKey);
					}
				}
			}

			echo "Correcto";
		}
	}
}
?>