<?php
class cuenta {

	public function _insertar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s'); 

		$nombreNegocio = $omodelo->link->real_escape_string(trim($nombreNegocio));
		$telNegocio = $omodelo->link->real_escape_string(trim($telNegocio));
		$domicilioNegocio = $omodelo->link->real_escape_string(trim($domicilioNegocio));
		$porcentajeNegocio = $omodelo->link->real_escape_string($porcentajeNegocio);

		$status = 0; $nombreImg = ''; $ruta = ''; $ruta_provisional = ''; $carpeta = 'vistas/assets/images/configuracion/';
		if ($_FILES['fotoNegocio']['size'] > 0 && $_FILES['fotoNegocio']['error'] == 0) {
			$file = $_FILES["fotoNegocio"];
			$nombreImg = $file["name"];
			$tipo = $file["type"];
			$ruta_provisional = $file["tmp_name"];
			$size = $file["size"];
				    
			if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'image/svg' && $tipo != ''){
				echo "Error 2 Formato";
				$status = 1;
			}else if ($size > (1024*1024*10)){
			    echo "Error 3 Peso";
			    $status = 1;
			}else{
			   	$ruta = $carpeta.$nombreImg;
			}
		}

		if($status == 0){
			$query = "SELECT Foto FROM configuracion WHERE ID_Configuracion = 1";
			$row = $omodelo->_consultar($query);
			$numerofilas1 = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error 4: ".mysqli_error($omodelo->link);
			}else{
				$foto = '';
				if($numerofilas1 > 0 && $nombreImg != ''){
					if(trim($row[0]['Foto']) != "" && file_exists($carpeta.$row[0]['Foto'])){
						unlink($carpeta.$row[0]['Foto']);
					}
					$foto = ", Foto = '".$nombreImg."'";
				}

				$query1 = "UPDATE configuracion SET Nombre = '$nombreNegocio', Domicilio = '$domicilioNegocio', Telefono = '$telNegocio', Porcentaje_Suma = '$porcentajeNegocio' $foto WHERE ID_Configuracion = 1";
				$error = $omodelo->_insertar($query1);

				if ($error == "si") {
					echo "Error 5: ".mysqli_error($omodelo->link);
				}else{	
					echo "Correcto";

					if($foto != ''){
						move_uploaded_file($ruta_provisional,  $ruta);
					}
				}
			}	
		}
	}
}
?>