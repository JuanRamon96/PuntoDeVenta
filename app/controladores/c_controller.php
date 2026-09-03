<?php
date_default_timezone_set('America/Mexico_City');
include 'modelo/m_modelo.php';
include "controladores/c_login.php";
include "controladores/c_sucursales.php";
include "controladores/c_productos.php";
include "controladores/c_inventario.php";
include "controladores/c_clientes.php";
include "controladores/c_ventas.php";
include "controladores/c_cajas.php";
include "controladores/c_caja.php";
include "controladores/c_proveedores.php";
include "controladores/c_compras.php";
include "controladores/c_hacerCompra.php";
include "controladores/c_usuarios.php";
include "controladores/c_reportes.php";
include "controladores/c_configuracion.php";
include "controladores/c_cuenta.php";
include "controladores/c_gastos.php";
include "controladores/c_clasificaciones.php";
include "controladores/c_impuestos.php";
include "controladores/c_config_facturacion.php";
include "controladores/c_facturas.php";
include "controladores/c_registro.php";
include "controladores/c_suscripcion.php";

class controller
{

	function _layouts()
	{
		$omodelo = new m_modelo();
		$fecha = date('Y-m-d');

		if (strtotime($fecha) > strtotime($_SESSION['user_punto_venta']['Sub']['Fecha_Vencimiento']) && $_SESSION['user_punto_venta']['Sub']['Ilimitado'] == 0) {
			$pagina = file_get_contents('vistas/v_suscripcion.php');
		} else if ($_SESSION['user_punto_venta']['Temporal'] == "1") {
			$pagina = file_get_contents('vistas/v_temporal.php');
		} else {
			$pagina = file_get_contents('vistas/v_html.php');
			$pagina = $this->remplazar($pagina, 'v_html');

			$suscripcion = "";
			if ($_SESSION['user_punto_venta']['Sub']['Ilimitado'] == 1) {
				$suscripcion = '<a class="vSus" href="javascript:void(0)"><i class="fa fa-history"></i> <b>Suscripción Iimitada</b></a>';
			} else {
				$separa = explode('~', $omodelo->_edad($_SESSION['user_punto_venta']['Sub']['Fecha_Vencimiento'], $fecha)[1]);
				$color = '';
				if ($separa[0] == 0 && $separa[1] == 0 && $separa[2] <= 10) {
					$color = 'style="color: red;"';
				}
				$suscripcion = '<a ' . $color . ' class="vSus" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalSuscripcion">
					<i class="fa fa-history"></i> Suscripción: ' . $omodelo->_edad($_SESSION['user_punto_venta']['Sub']['Fecha_Vencimiento'], $fecha)[0] . '
				</a>';
			}

			$pagina = str_replace('#suscripcion#', $suscripcion, $pagina);
		}

		$foto = 'vistas/assets/images/default.jpg';
		if (trim($_SESSION['user_punto_venta']['Foto']) != '' && file_exists('vistas/assets/images/usuarios/' . $_SESSION['user_punto_venta']['Foto'])) {
			$foto = 'vistas/assets/images/usuarios/' . $_SESSION['user_punto_venta']['Foto'];
		}

		$pagina = str_replace('#fotoCuenta#', $foto, $pagina);
		$pagina = str_replace('#emailCuenta#', $_SESSION['user_punto_venta']['Correo'], $pagina);

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

		$permisos = [];
		$tipoUsuario = '';

		if (isset($_SESSION['user_punto_venta']['ID_Usuario'])) {
			$query = "SELECT Permisos, Tipo_Usuario FROM usuarios WHERE ID_Usuario = '" . $_SESSION['user_punto_venta']['ID_Usuario'] . "'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					$tipoUsuario = $row[0]['Tipo_Usuario'];
					$_SESSION['user_punto_venta']['Tipo_Usuario'] = $row[0]['Tipo_Usuario'];

					if ($row[0]['Permisos'] != "") {
						$modulosP = explode('~', $row[0]['Permisos']);

						for ($i = 0; $i < count($modulosP); $i++) {
							$separaM = explode(',', $modulosP[$i]);
							$nombreM = $separaM[0];
							unset($separaM[0]);
							$permisos[$nombreM] = $separaM;
							$_SESSION['user_punto_venta']['Permisos'] = $permisos;
						}
					}
				}
			}
		}

		if ($nombre == 'v_html') {
			$menuSucursales = '';
			$menuProductos = '';
			$menuInventario = '';
			$subMenuInventario = '';
			$subMenuTraslados = '';
			$subMenuConversiones = '';
			$menuClasificaciones = '';
			$menuVentas = '';
			$menuCajas = '';
			$menuProveedores = '';
			$menuUsuarios = '';
			$menuReportes = '';
			$menuClientes = '';
			$menuCompras = '';
			$menuFacturacion = '';
			$subMenuFacturas = '';
			$subMenuConfigFacturacion = '';
			$subMenuImpuestos = '';
			$menuGastos = '';
			$menuConfiguracion = '';

			/*if ($tipoUsuario == '1' || (isset($permisos['Sucursales'][1]) && $permisos['Sucursales'][1] == '1')) {
				$menuSucursales = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_sucursales" titulo="Sucursales" id="bMenuSucursales"><span class="pcoded-micon"><i class="fas fa-store"></i></span><span class="pcoded-mtext">Sucursales</span></a>
				</li>';
			}*/

			if ($tipoUsuario == '1' || (isset($permisos['Productos'][1]) && $permisos['Productos'][1] == '1')) {
				$menuProductos = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_productos" titulo="Productos" id="bMenuProductos"><span class="pcoded-micon"><i class="fas fa-box"></i></span><span class="pcoded-mtext">Productos</span></a>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Inventario'][1]) && $permisos['Inventario'][1] == '1')) {
				$menuInventario = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_inventario" titulo="Inventario" id="bMenuInventario"><span class="pcoded-micon"><i class="fas fa-clipboard-list"></i></span><span class="pcoded-mtext">Inventario</span></a>
				</li>';
			}

			/*if ($tipoUsuario == '1' || (isset($permisos['Inventario'][1]) && $permisos['Inventario'][1] == '1')) {
				$subMenuInventario = '<li><a href="javascript:void(0)" class="cargarVista" carga="v_inventario" titulo="Inventario" id="bMenuInventario">Inventario</a></li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Traslados'][1]) && $permisos['Traslados'][1] == '1')) {
				$subMenuTraslados = '<li><a href="javascript:void(0)" class="cargarVista" carga="v_traslados" titulo="Traslados" id="bMenuTraslados">Traslados</a></li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Conversiones'][1]) && $permisos['Conversiones'][1] == '1')) {
				$subMenuConversiones = '<li><a href="javascript:void(0)" class="cargarVista" carga="v_conversiones" titulo="Inventario" id="bMenuConversiones">Conversiones</a></li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Inventario'][1]) && $permisos['Inventario'][1] == '1') || (isset($permisos['Traslados'][1]) && $permisos['Traslados'][1] == '1') || (isset($permisos['Conversiones'][1]) && $permisos['Conversiones'][1] == '1')) {
				$menuInventario = '<li class="nav-item pcoded-hasmenu">
					<a href="javascript:void(0)" class="nav-link"><span class="pcoded-micon"><i class="fas fa-clipboard-list"></i></span><span class="pcoded-mtext">Inventario</span></a>
					<ul class="pcoded-submenu">
						'.$subMenuInventario.'
						'.$subMenuTraslados.'
						'.$subMenuConversiones.'   
					</ul>
				</li>';
			}*/

			if ($tipoUsuario == '1' || (isset($permisos['Clasificaciones'][1]) && $permisos['Clasificaciones'][1] == '1')) {
				$menuClasificaciones = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_clasificaciones" titulo="Clasificaciones" id="bMenuClasificacion"><span class="pcoded-micon"><i class="fa-solid fa-layer-group"></i></span><span class="pcoded-mtext">Clasificaciones</span></a>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Ventas'][1]) && $permisos['Ventas'][1] == '1')) {
				$menuVentas = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_ventas" titulo="Ventas" id="bMenuVentas"><span class="pcoded-micon"><i class="fas fa-dollar-sign"></i></span><span class="pcoded-mtext">Ventas</span></a>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Clientes'][1]) && $permisos['Clientes'][1] == '1')) {
				$menuClientes = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_clientes" titulo="Clientes" id="bMenuClientes"><span class="pcoded-micon"><i class="fas fa-people-arrows"></i></span><span class="pcoded-mtext">Clientes</span></a>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Cajas'][1]) && $permisos['Cajas'][1] == '1')) {
				$menuCajas = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_cajas" titulo="Cajas" id="bMenuCajas"><span class="pcoded-micon"><i class="fas fa-cash-register"></i></span><span class="pcoded-mtext">Cajas</span></a>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Proveedores'][1]) && $permisos['Proveedores'][1] == '1')) {
				$menuProveedores = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_proveedores" titulo="Proveedores" id="bMenuProveedores"><span class="pcoded-micon"><i class="fas fa-truck"></i></span><span class="pcoded-mtext">Proveedores</span></a>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Compras'][1]) && $permisos['Compras'][1] == '1')) {
				$menuCompras = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_compras" titulo="Compras" id="bMenuCompras"><span class="pcoded-micon"><i class="fas fa-basket-shopping"></i></span><span class="pcoded-mtext">Compras</span></a>
					<button type="button" id="cargarHacerCompra" class="oculto cargarVista" carga="v_hacerCompra" titulo="Hacer Compra"></button>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Facturas'][1]) && $permisos['Facturas'][1] == '1')) {
				$subMenuFacturas = '<li><a href="javascript:void(0)" class="cargarVista" carga="v_facturas" titulo="Facturas" id="bMenuFacturas">Facturas</a></li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Config_Facturacion'][1]) && $permisos['Config_Facturacion'][1] == '1')) {
				$subMenuConfigFacturacion = '<li><a href="javascript:void(0)" class="cargarVista" carga="v_config_facturacion" titulo="Configuración" id="bMenuConfigFacturacion">Configuración</a></li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Impuestos'][1]) && $permisos['Impuestos'][1] == '1')) {
				$subMenuImpuestos = '<li><a href="javascript:void(0)" class="cargarVista" carga="v_impuestos" titulo="Impuestos" id="bMenuImpuestos">Impuestos</a></li>';
			}

			if ($subMenuConfigFacturacion != '' || $subMenuImpuestos != '' || $subMenuFacturas != '') {
				$menuFacturacion = '<li class="nav-item pcoded-hasmenu">
					<a href="javascript:void(0)" class="nav-link"><span class="pcoded-micon"><i class="fas fa-clipboard-list"></i></span><span class="pcoded-mtext">Facturación CFDI 4.0</span> </a>
					<ul class="pcoded-submenu">
						' . $subMenuFacturas . '
						' . $subMenuConfigFacturacion . '
						' . $subMenuImpuestos . ' 
					</ul>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Gastos'][1]) && $permisos['Gastos'][1] == '1')) {
				$menuGastos = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_gastos" titulo="Gastos" id="bMenuUsuarios"><span class="pcoded-micon"><i class="fa-solid fa-money-bill"></i></span><span class="pcoded-mtext">Gastos</span></a>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Usuarios'][1]) && $permisos['Usuarios'][1] == '1')) {
				$menuUsuarios = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_usuarios" titulo="Usuarios" id="bMenuUsuarios"><span class="pcoded-micon"><i class="fas fa-people-group"></i></span><span class="pcoded-mtext">Usuarios</span></a>
				</li>';
			}

			if ($tipoUsuario == '1') {
				$menuConfiguracion = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_cuenta" titulo="Configuración" id="bMenuConfiguracion"><span class="pcoded-micon"><i class="fas fa-gear"></i></span><span class="pcoded-mtext">Configuración</span></a>
				</li>';
			}

			if ($tipoUsuario == '1' || (isset($permisos['Reportes'][1]) && $permisos['Reportes'][1] == '1')) {
				$menuReportes = '<li class="nav-item">
					<a href="javascript:void(0)" class="nav-link cargarVista" carga="v_reportes" titulo="Reportes" id="bMenuReportes"><span class="pcoded-micon"><i class="fas fa-chart-simple"></i></span><span class="pcoded-mtext">Reportes</span></a>
				</li>';
			}

			$pagina = str_replace('#menuSucursales#', $menuSucursales, $pagina);
			$pagina = str_replace('#menuProductos#', $menuProductos, $pagina);
			$pagina = str_replace('#menuInventario#', $menuInventario, $pagina);
			$pagina = str_replace('#menuClasificaciones#', $menuClasificaciones, $pagina);
			$pagina = str_replace('#menuClientes#', $menuClientes, $pagina);
			$pagina = str_replace('#menuVentas#', $menuVentas, $pagina);
			$pagina = str_replace('#menuCajas#', $menuCajas, $pagina);
			$pagina = str_replace('#menuProveedores#', $menuProveedores, $pagina);
			$pagina = str_replace('#menuCompras#', $menuCompras, $pagina);
			$pagina = str_replace('#menuUsuarios#', $menuUsuarios, $pagina);
			$pagina = str_replace('#menuConfiguracion#', $menuConfiguracion, $pagina);
			$pagina = str_replace('#menuReportes#', $menuReportes, $pagina);
			$pagina = str_replace('#menuGastos#', $menuGastos, $pagina);
			$pagina = str_replace('#menuFacturacion#', $menuFacturacion, $pagina);
		} else if ($nombre == 'v_configuracion') {
			if (trim($_SESSION['user_punto_venta']['Foto']) != '' && file_exists('vistas/assets/images/usuarios/' . $_SESSION['user_punto_venta']['Foto'])) {
				$pagina = str_replace(
					'#fotoPerfil#',
					'<img id="mosFotoPerfil" width="150" height="150" src="vistas/assets/images/usuarios/' . $_SESSION['user_punto_venta']['Foto'] . '" class="object-fit-cover rounded-circle" alt="Foto de perfil" foto="' . $_SESSION['user_punto_venta']['Foto'] . '">',
					$pagina
				);
			} else {
				$pagina = str_replace(
					'#fotoPerfil#',
					'<img id="mosFotoPerfil" width="150" height="150" src="vistas/assets/images/default.jpg" class="object-fit-cover rounded-circle" alt="Foto de perfil" foto="">',
					$pagina
				);
			}

			$pagina = str_replace('#correo#', $_SESSION['user_punto_venta']['Correo'], $pagina);
			$pagina = str_replace('#nombrePerfil#', $_SESSION['user_punto_venta']['Nombre'], $pagina);
			$pagina = str_replace('#primerApellidoPerfil#', $_SESSION['user_punto_venta']['Primer_Apellido'], $pagina);
			$pagina = str_replace('#segundoApellidoPerfil#', $_SESSION['user_punto_venta']['Segundo_Apellido'], $pagina);
		} else if ($nombre == "v_sucursales") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Sucursales'][2] == '1') {
				$bAgregar = '<button id="bAgregarSucursal" class="btn btn-danger" type="button">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
		} else if ($nombre == "v_productos") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Productos'][2] == '1') {
				$bAgregar = '<button id="bAgregarProducto" class="btn btn-danger" type="button">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);

			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$clasificaciones = '';
			$query = 'SELECT ID_Clasificacion, Nombre FROM clasificaciones';
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$clasificaciones .= '<option value="' . $row[$i]['ID_Clasificacion'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#clasificacionesProducto#', $clasificaciones, $pagina);

			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$porcentaje = 0;
			$query = "SELECT Porcentaje_Suma FROM configuracion WHERE ID_Configuracion = '1'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					$porcentaje = $row[0]['Porcentaje_Suma'];
				}
			}

			$pagina = str_replace('#porcentajeSumProd#', $porcentaje, $pagina);

			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$impuestos = '';
			$query = "SELECT ID_Impuesto, Nombre, Porcentaje, Clave_CFDI, Tipo_Factor, Clase FROM impuestos ORDER BY Nombre ASC";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$impuestos .= '<tr attrID="' . $row[$i]['ID_Impuesto'] . '">
							<td>' . $row[$i]['Nombre'] . '</td>
							<td class="porcentaje">' . $row[$i]['Porcentaje'] . '</td>
							<td>' . $row[$i]['Clave_CFDI'] . '</td>
							<td>' . $row[$i]['Clase'] . '</td>
							<td>' . $row[$i]['Tipo_Factor'] . '</td>
							<td><input type="checkbox" class="form-check-input" style="margin-top: -6px;" /></td>
						</tr>';
					}
				}
			}

			$pagina = str_replace('#impuestosProductos#', $impuestos, $pagina);
		} else if ($nombre == "v_clasificaciones") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Clasificaciones'][2] == '1') {
				$bAgregar = '<button id="bAgregarClasificacion" class="btn btn-danger" type="button">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
		} else if ($nombre == "v_cajas") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Cajas'][2] == '1') {
				$bAgregar = '<button type="button" class="btn btn-danger" id="bAgregarCaja"><i class="fa-solid fa-plus"></i> Agregar</button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);

			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$bCortes = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Cajas'][5] == '1') {
				$bCortes = '<button type="button" class="btn btn-info" id="bReportes" titulo="Reportes"><i class="fas fa-list-check"></i> Cortes de caja</button>';
			}

			$pagina = str_replace('#bCortes#', $bCortes, $pagina);

			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$sucursales = '<option value="0">--Selecciona una sucursal--</option>';
			$query = 'SELECT ID_Sucursal, Nombre FROM sucursales';
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$sucursales .= '<option value="' . $row[$i]['ID_Sucursal'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#sucursalesCaja#', $sucursales, $pagina);
		} else if ($nombre == "v_proveedores") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Proveedores'][2] == '1') {
				$bAgregar = '<button type="button" class="btn btn-danger" id="bAgregarProveedor"><i class="fa-solid fa-plus"></i> Agregar</button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
		} else if ($nombre == "v_compras") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Compras'][2] == '1') {
				$bAgregar = '<button type="button" class="btn btn-danger cargarVista" carga="v_hacerCompra" titulo="Hacer Compra" id="bAgregarCompra"><i class="fa-solid fa-plus"></i> Agregar</button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);

			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

			$opciones = '';
			$query = "SELECT ID_Caja, Nombre FROM cajas WHERE Estado = 1 ORDER BY Nombre";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$opciones .= '<option value="' . $row[$i]['ID_Caja'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#cajas#', $opciones, $pagina);
		} else if ($nombre == "v_usuarios") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Usuarios'][2] == '1') {
				$bAgregar = '<button id="bAgregarUsuario" class="btn btn-danger" type="button">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
		} else if ($nombre == "v_clientes") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Clientes'][2] == '1') {
				$bAgregar = '<button id="bAgregarCliente" class="btn btn-danger" type="button">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
		} else if ($nombre == "v_ventas") {
			$opciones = '';

			$query = "SELECT ID_Caja, Nombre FROM cajas WHERE Estado = 1 ORDER BY Nombre";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$opciones .= '<option value="' . $row[$i]['ID_Caja'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#cajas#', $opciones, $pagina);
		} else if ($nombre == 'v_cuenta') {
			$query = "SELECT Nombre, Domicilio, Telefono, Foto, Porcentaje_Suma FROM configuracion WHERE ID_Configuracion = '1'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					$foto = 'vistas/assets/images/fondo.jpg';
					if ($row[0]['Foto'] != "" && file_exists('vistas/assets/images/configuracion/' . $row[0]['Foto'])) {
						$foto = 'vistas/assets/images/configuracion/' . $row[0]['Foto'];
					}

					$pagina = str_replace('#fotoNegocio#', $foto, $pagina);
					$pagina = str_replace('#nombreNegocio#', $row[0]['Nombre'], $pagina);
					$pagina = str_replace('#telefonoNegocio#', $row[0]['Telefono'], $pagina);
					$pagina = str_replace('#domicilioNegocio#', $row[0]['Domicilio'], $pagina);
					$pagina = str_replace('#porcentajeNegocio#', $row[0]['Porcentaje_Suma'], $pagina);
				}
			}
		} else if ($nombre == "v_gastos") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Gastos'][2] == '1') {
				$bAgregar = '<button id="bAgregarGasto" class="btn btn-danger" type="button">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

			$sucursales = '<option value="">--Selecciona una sucursal--</option>';
			$query = 'SELECT ID_Sucursal, Nombre FROM sucursales';
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$sucursales .= '<option value="' . $row[$i]['ID_Sucursal'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#sucursalesGasto#', $sucursales, $pagina);
		} else if ($nombre == "v_inventario") {
			$sucursales = '<option value="">--Selecciona una sucursal--</option>';
			$query = 'SELECT ID_Sucursal, Nombre FROM sucursales';
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$sucursales .= '<option value="' . $row[$i]['ID_Sucursal'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#sucursalesInventario#', $sucursales, $pagina);
		} else if ($nombre == "v_hacerCompra") {
			$sucursales = '<option value="">--Selecciona una sucursal--</option>';
			$query = 'SELECT ID_Sucursal, Nombre FROM sucursales';
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$sucursales .= '<option value="' . $row[$i]['ID_Sucursal'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#sucursalesCompra#', $sucursales, $pagina);
		} else if ($nombre == "v_traslados") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Gastos'][2] == '1') {
				$bAgregar = '<button id="bAgregarTraslado" class="btn btn-danger" type="button">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

			$sucursales = '<option value="">--Selecciona una sucursal--</option>';
			$query = 'SELECT ID_Sucursal, Nombre FROM sucursales';
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$sucursales .= '<option value="' . $row[$i]['ID_Sucursal'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#sucursalesTraslado#', $sucursales, $pagina);
		} else if ($nombre == "v_impuestos") {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Impuestos'][2] == '1') {
				$bAgregar = '<button id="bAgregarImpuesto" class="btn btn-danger" type="button">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
		} else if ($nombre == 'v_config_facturacion') {
			$query = "SELECT RFC, Nombre, Regimen, CP, Domicilio FROM configuracion_facturacion LIMIT 1";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					$pagina = str_replace('#rfc#', $row[0]['RFC'], $pagina);
					$pagina = str_replace('#nombre#', $row[0]['Nombre'], $pagina);
					$pagina = str_replace('#cp#', $row[0]['CP'], $pagina);
					$pagina = str_replace('#domicilio#', $row[0]['Domicilio'], $pagina);

					echo '<script>$("#regimenFacturacion").val("' . $row[0]['Regimen'] . '");</script>';
				}
			}
		} else if ($nombre == 'v_facturas') {
			$bAgregar = '';
			if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Facturas'][2] == '1') {
				$bAgregar = '<button type="button" class="btn btn-sm btn-danger" id="bAgregarFactura">Agregar <i class="fas fa-plus"></i></button>';
			}

			$pagina = str_replace('#bAgregar#', $bAgregar, $pagina);
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$pagina = str_replace('#anioActual#', date('Y'), $pagina);
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$timbres = 0;
			$bd = $_SESSION['user_punto_bd'];
		
			$omodelo->_insertar("USE punto_subs");
			$query = "SELECT Timbres FROM suscripciones WHERE ID_Suscripcion = '$bd'";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					$timbres = $row[0]['Timbres'];
				}
			}

			$pagina = str_replace('#timbresDisponibles#', $timbres, $pagina);

			$alerta = '';
			if ($timbres < 1) {
				$alerta = '<div class="alert alert-danger d-flex align-items-center" role="alert">
					<i class="fas fa-exclamation-triangle me-2"></i>
					<div>
						No cuentas con timbres disponibles para timbrar facturas.
						<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalTimbres" class="alert-link">
            				Consulta nuestros paquetes de timbres aquí.
        				</a>
					</div>
				</div>';
			}

			$pagina = str_replace('#alertaTimbres#', $alerta, $pagina);
		} else if ($nombre == 'v_reportes') {
			$usuarios = '<option value="">-- Todos --</option>';

			$query = "SELECT ID_Usuario, Nombre, Primer_Apellido, Segundo_Apellido FROM usuarios";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$usuarios .= '<option value="' . $row[$i]['ID_Usuario'] . '">' . $row[$i]['Nombre'] . ' ' . $row[$i]['Primer_Apellido'] . ' ' . $row[$i]['Segundo_Apellido'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#usuariosReporte#', $usuarios, $pagina);
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			$cajas = '<option value="">-- Todas --</option>';

			$query = "SELECT ID_Caja, Nombre FROM cajas";
			$row = $omodelo->_consultar($query);
			$numerofilas = $omodelo->numerofilas;

			if ($row == "si") {
				echo "Error: " . mysqli_error($omodelo->link);
			} else {
				if ($numerofilas > 0) {
					for ($i = 0; $i < $numerofilas; $i++) {
						$cajas .= '<option value="' . $row[$i]['ID_Caja'] . '">' . $row[$i]['Nombre'] . '</option>';
					}
				}
			}

			$pagina = str_replace('#cajasReporte#', $cajas, $pagina);
		}

		return $pagina;
	}
}
