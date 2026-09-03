<?php 
	session_cache_expire(15);
	session_start();
	require "controladores/c_controller.php";
	$controller = new controller();
	
	if(ISSET($_SESSION['user_tablet_stazione'])){
		extract($_POST);

		if (isset($metodo)) {
			if ($metodo =='cambiar'){
				echo $controller->_contenido($accion);
			}else if ($metodo =='consultar') {
				$controller->_consultar($accion);
			}else if ($metodo =='insertar') {
				$controller->_insertar($accion);
			}else if ($metodo =='modificar') {
				$controller->_modificar($accion);
			}else if ($metodo =='eliminar') {
				$controller->_eliminar($accion);
			}else if ($metodo == 'detalles') {
				$controller->_detalles($accion);
			}
		}else{
			echo $controller->_layouts();
		}

		return false;
	}else{
		if(isset($_POST['accion']) && $_POST['accion'] == 'login' && isset($_POST['correo']) && isset($_POST['contrasena'])){
			$controller->_consultar('login');
		}else{
			echo $controller->_contenido('v_login');
		}
	}	
?>