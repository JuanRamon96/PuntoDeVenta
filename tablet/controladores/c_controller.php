<?php
date_default_timezone_set('America/Mexico_City');
include 'modelo/m_modelo.php';
include "controladores/c_login.php";
include "controladores/c_ventas.php";

class controller
{

	function _layouts()
	{
		$omodelo = new m_modelo();
		$fecha = date('Y-m-d');

		$pagina = file_get_contents('vistas/v_html.php');
		$pagina = str_replace('#emailCuenta#', $_SESSION['user_tablet_stazione']['Correo'], $pagina);
		$pagina = $this->remplazar($pagina, 'v_html');

		return $pagina;
	}

	function _contenido($vista)
	{
		$pagina = file_get_contents("vistas/$vista.php");
		$pagina = $this->remplazar($pagina, $vista);

		return $pagina;
	}

	function _consultar($metodo)
	{
		$objeto = new $metodo();
		$objeto->_consultar();
	}

	function _insertar($metodo)
	{
		$objeto = new $metodo();
		$objeto->_insertar();
	}

	function _modificar($metodo)
	{
		$objeto = new $metodo();
		$objeto->_modificar();
	}

	function _eliminar($metodo)
	{
		$objeto = new $metodo();
		$objeto->_eliminar();
	}

	function _detalles($metodo)
	{
		$objeto = new $metodo();
		$objeto->_detalles();
	}

	function remplazar($pagina, $nombre)
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date('Y-m-d H:i:s');

		if ($nombre == 'v_html') {
			

			//$pagina = str_replace('#productos#', $productos, $pagina);
		}

		return $pagina;
	}
}
