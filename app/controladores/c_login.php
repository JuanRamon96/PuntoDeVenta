<?php
class login
{

	public function _consultar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s');

		$correo = $omodelo->link->real_escape_string(trim($correo));
		$contrasena = $omodelo->link->real_escape_string(trim($contrasena));

		$omodelo->_insertar('USE punto_subs');
		$query = "SELECT ID_Suscripcion, Nombre, Tipo, Otro, Correo, Tipo_Usuario, BD, Fecha_Alta, Fecha_Vencimiento, Ilimitado, Timbres FROM suscripciones WHERE Correo = '$correo' LIMIT 1";
		$rowSub = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($rowSub == "si") {
			echo "Error sub: " . mysqli_error($omodelo->link);
		} else {
			if ($numerofilas > 0) {
				$bd = $rowSub[0]['ID_Suscripcion'];
				if ($rowSub[0]['Tipo_Usuario'] == 'Normal') {
					$bd = $rowSub[0]['BD'];
				}

				$omodelo->_insertar('USE punto_venta_'.$bd);
				$_SESSION['user_punto_bd'] = $bd;
			} else {
				echo "No existe";

				return;
			}
		}

		$query4 = "SELECT ID_Usuario, Nombre, Primer_Apellido, Segundo_Apellido, Correo, Contrasena, Tipo_Usuario, Permisos, Estatus, Intentos, Ultimo_Intento, Tiempo_Inicio, Tiempo_Final, Foto, Temporal, Activo, Conectado, Fecha_Alta FROM usuarios  WHERE Tipo_Usuario != 2 AND Correo = '$correo' AND Contrasena != '' AND Activo = 1 AND Estatus = 'Desbloqueado'";
		$row = $omodelo->_consultar($query4);
		$numerofilas = $omodelo->numerofilas;

		if ($row == "si") {
			echo "Error 1: " . mysqli_error($omodelo->link);
		} else {
			if ($numerofilas > 0) {
				if ($row[0]['Intentos'] == '5') {
					$nuevafecha = strtotime('+15 minute', strtotime($row[0]['Ultimo_Intento']));
					if (strtotime($fecha) >= $nuevafecha) {
						$query5 = "UPDATE usuarios SET Intentos = 0 WHERE Correo = '$correo'";
						$resultado = $omodelo->_insertar($query5);

						if ($resultado == "si") {
							echo "Error 2: " . mysqli_error($omodelo->link);
						} else {
							$row[0]['Intentos'] = '0';
						}
					} else {
						echo "Supero Intentos";
					}
				}
				if ($row[0]['Intentos'] < 5) {
					$query1 = "UPDATE usuarios SET Ultimo_Intento = '$fecha', Intentos = Intentos+1 WHERE Correo = '$correo'";
					$resultado = $omodelo->_insertar($query1);
					$afectadas = $omodelo->numerofilas;

					if ($resultado == "si") {
						echo "Error 3: " . mysqli_error($omodelo->link);
					} else {
						if ($afectadas > 0) {
							if ($numerofilas > 0 && password_verify($contrasena, $row[0]['Contrasena'])) {
								$query2 = "UPDATE usuarios SET Tiempo_Inicio = '$fecha', Intentos = 0 WHERE Correo = '$correo'";
								$resultado = $omodelo->_insertar($query2);

								if ($resultado == "si") {
									echo "Error 5: " . mysqli_error($omodelo->link);
								} else {
									$_SESSION['user_punto_venta'] = $row[0];
									$_SESSION['user_punto_venta']['Sub'] = $rowSub[0];
									echo "Correcto";
								}
							} else {
								$query3 = "SELECT Intentos FROM usuarios WHERE Correo = '$correo' AND Intentos = 5";
								$row2 = $omodelo->_consultar($query3);
								$numerofilas = $omodelo->numerofilas;

								if ($row2 == "si") {
									echo "Error 6: " . mysqli_error($omodelo->link);
								} else {
									echo "0";

									if ($numerofilas > 0) {
										//IMG cuando este en hosting <p style='text-align: center;'><img src='https://100capital.com/img/100capital.png' alt='100 Capital' width='35%'></p><br><br>
										$omodelo->_email(
											$correo,
											"Superaste el número de intentos para acceder a VentasTool",
											"<h3>¡Alerta! Haz intentado acceder a 100 Capital mas de 5 veces</h3>
											<p><b>Tu cuenta ha sido bloqueada</b>, para intentar acceder de nuevo espera 15 minutos. Si no has sido tu quien intento acceder o tienes alguna pregunta, comunícate con nosotros a través del correo <b>ventastool@bigtool.mx</b></p>"
										);
									}
								}
							}
						}
					}
				}
			} else {
				echo "0";
			}

			$omodelo->movimiento("LOGIN $correo", '');
		}
	}

	public function _modificar(){
		$omodelo = new m_modelo();
		extract($_POST);
		$correo = $omodelo->link->real_escape_string(trim($correo));
		
		$omodelo->_insertar('USE punto_subs');
		$query = "SELECT ID_Suscripcion, BD, Tipo_Usuario FROM suscripciones WHERE Correo = '$correo' LIMIT 1";
		$rowSub = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($rowSub == "si") {
			echo "Error sub: " . mysqli_error($omodelo->link);
		} else {
			if ($numerofilas > 0) {
				$bd = $rowSub[0]['ID_Suscripcion'];
				if ($rowSub[0]['Tipo_Usuario'] == 'Normal') {
					$bd = $rowSub[0]['BD'];
				}

				$omodelo->_insertar('USE punto_venta_'.$bd);
				$_SESSION['user_punto_bd'] = $bd;
			} else {
				echo "Error 3 No existe";

				return;
			}
		}

		$opciones = ['cost' => 12];
		$cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$newcontra = "";
		for($i=0;$i<8;$i++) {
			$newcontra .= substr($cadena,rand(0,62),1);
		}
		$contrasena = password_hash($newcontra, PASSWORD_BCRYPT, $opciones);

		$query = "SELECT Correo FROM usuarios WHERE Correo = '$correo' AND Estatus = 0";
		$row = $omodelo->_consultar($query);
		$numerofilas = $omodelo->numerofilas;

		if ($row == "si") {
			echo "Error 1: ".mysqli_error($omodelo->link);
		}else{
			if($numerofilas > 0){
				$query1 = "UPDATE usuarios SET Contrasena = '$contrasena', Temporal = 1 WHERE Correo = '$correo'";
				$resultado = $omodelo->_insertar($query1);

				if ($resultado == "si") {
					echo "Error 2: ".mysqli_error($omodelo->link);
				}else{
					$omodelo->movimiento($query1, '');

					echo "Correcto";

					//IMG cuando este en hosting <p style='text-align: center;'><img src='https://dentastool.com/img/dentastool.png' alt='DentasTool' width='35%'></p><br><br>
					$omodelo->_email(
						$correo,
						"Restaurar contraseña en VentasTool",
						"<h3>Solicitaste un cambio de contraseña</h3><br>
						<b>Tu contraseña temporal es: <b style='color: red'>".$newcontra."</b></b><br>
						Si no has sido tu quien solicitó el cambio de contraseña o tienes alguna pregunta, comunícate con nosotros a través del correo <b>ventastool@bigtool.mx<br>
						<h4>Deberás cambiar la contraseña la próxima vez que inicies sesión en VentasTool.</h4>"
					);
				}
			}else{
				echo "Error 3 No existe";
			}
		}
	}

	public function _detalles(){
		$omodelo = new m_modelo();	
		extract($_POST);
		$opciones = ['cost' => 12];
		$contrasena = $omodelo->link->real_escape_string($contrasena);
		$contrasenaN = password_hash($contrasena, PASSWORD_BCRYPT, $opciones);
		$idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];

		$query = "UPDATE usuarios SET Contrasena = '$contrasenaN', Temporal = 0 WHERE ID_Usuario = '$idUsuario'";
		$resultado = $omodelo->_insertar($query);

		if ($resultado == "si") {
			echo "Error: ".mysqli_error($omodelo->link);
		}else{
			$_SESSION['user_punto_venta']['Temporal'] = 0;

			echo "Correcto";

			$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
		}
	}

	public function _eliminar()
	{
		$omodelo = new m_modelo();
		$fecha = date("Y-m-d H:i:s");

		$query = "UPDATE usuarios SET Tiempo_Final='$fecha' WHERE ID_Usuario = '" . $_SESSION['user_punto_venta']['ID_Usuario'] . "'";
		$resultado = $omodelo->_insertar($query);

		if ($resultado == "si") {
			echo "Error: " . mysqli_error($omodelo->link);
		} else {

			/*$_SESSION = array();
			if (ini_get("session.use_cookies")) {
			    $params = session_get_cookie_params();
			    setcookie(session_name(), '', time() - 42000,
			        $params["path"], $params["domain"],
			        $params["secure"], $params["httponly"]
			    );
			}
			session_destroy();*/
			$omodelo->movimiento("Usuario " . $_SESSION['user_punto_venta']['ID_Usuario'] . " - " . $_SESSION['user_punto_venta']['Nombre'] . " cerró sesión ", $_SESSION['user_punto_venta']['ID_Usuario']);

			unset($_SESSION['user_punto_venta']);
			unset($_SESSION['user_punto_bd']);
		}
	}
}
?>