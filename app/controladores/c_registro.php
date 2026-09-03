<?php
class registro {

	public function _insertar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s'); 
		$opciones = ['cost' => 12];

		$nombre = $omodelo->link->real_escape_string(trim($regNombre));
		$tipo = $omodelo->link->real_escape_string(trim($regNegocio));
		$otro = isset($regNegocioOtro) ? $omodelo->link->real_escape_string(trim($regNegocioOtro)) : '';
		$correo = $omodelo->link->real_escape_string(trim($regCorreo));
		$contrasena = password_hash($omodelo->link->real_escape_string($regPassword), PASSWORD_BCRYPT, $opciones);
		$captcha = $_POST['g-recaptcha-response'];

        if(!isset($nombre)){
        	echo "Error 1 Nombres";
        }else if(!isset($tipo)){
          	echo "Error 2 Tipo";
        }else if(!isset($correo)){
          	echo "Error 3 Correo";
        }else if(!isset($contrasena)){
         	echo "Error 4 Contrasena";
        }else if(!isset($captcha)){
        	echo "Error 5 Captcha";
        }else{
	        /*$url = 'https://www.google.com/recaptcha/api/siteverify?secret='.urlencode("6LcsPXktAAAAACHhPkVqEDAvYHSgkfV7NGfOKoH0").'&response='.urlencode($captcha);
	        $response = file_get_contents($url);
	        $responseKeys = json_decode($response,true);*/
	       
	        //if($responseKeys["success"]) {
				$omodelo->_insertar("USE invitia_subs");
	            $query = "INSERT INTO suscripciones SET 
					Nombre = '$nombre', 
					Tipo = '$tipo', 
					Otro = '$otro', 
					Correo = '$correo', 
					Tipo_Usuario = 'Admin', 
					Fecha_Alta = NOW(), 
					Fecha_Vencimiento = DATE_ADD(NOW(), INTERVAL 7 DAY)";
				$resultado = $omodelo->_insertar($query);

				if ($resultado == "si") {
					echo "Error 6: ".mysqli_error($omodelo->link);
				}else{
					$id = mysqli_insert_id($omodelo->link);

					$omodelo->movimiento($query, $id);
					
					$omodelo->_crear($id, $correo, $contrasena);

					echo "Correcto";

					//IMG cuando este en hosting <p style='text-align: center;'><img src='https://dentastool.com/img/dentastool.png' alt='DentasTool' width='35%'></p><br><br>
					$omodelo->_email(
						$correo,
						'Registro realizado correctamente en VentasTool',
						"<h3>Te registraste correctamente en VentasTool</h3><br><p><b>Ya puedes comienzar a usar VentasTool, por favor da click en el siguiente enlace: <a href='https://bigtool.mx/app'>VentasTool</a>.</b></p>
						<p>Si no has sido tu quien creo la cuenta o tienes alguna pregunta, puedes contactarnos a través de correo <a href='mailto:ventastool@bigtool.mx'>ventastool@bigtool.mx</a>.</p>"
					);
				}
	        /*} else {
	            echo 'Error 7 Captcha';
	        }*/
        } 
	}
}
?>
