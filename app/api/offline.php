<?php
// API de la app offline (escritorio y móvil).
// POST con JSON. Todas las acciones, salvo "enlazar", llevan el encabezado Authorization: Bearer <llave del equipo>.
// La cuenta (base de datos) siempre sale de la llave; el equipo nunca la elige.

header('Content-Type: application/json; charset=utf-8');
chdir(__DIR__ . '/..');
date_default_timezone_set('America/Mexico_City');

mysqli_report(MYSQLI_REPORT_OFF); // errores como valor de retorno, no excepciones
require_once 'modelo/m_modelo.php';
require_once 'modelo/offline.php';
require_once 'controladores/c_caja.php';

function responder($datos, $status = 200)
{
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	http_response_code($status);
	echo json_encode($datos);
	exit;
}

function fallo($mensaje, $status = 400, $codigo = 'error')
{
	responder(array('ok' => false, 'codigo' => $codigo, 'error' => $mensaje), $status);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	fallo('Método no permitido', 405);
}

$entrada = json_decode(file_get_contents('php://input'), true);
if (!is_array($entrada)) {
	fallo('Petición inválida');
}
$accion = isset($entrada['accion']) ? $entrada['accion'] : '';

ob_start(); // m_modelo imprime los errores SQL; no deben romper el JSON
$omodelo = new m_modelo();
$omodelo->_insertar('USE punto_subs');
offlineCrearTablas($omodelo);
$db = $omodelo->link;

function esc($valor)
{
	global $db;
	return $db->real_escape_string((string) $valor);
}

function filas($sql)
{
	global $db;
	$res = $db->query($sql);
	if (!$res) {
		fallo('Error en el servidor: ' . $db->error, 500);
	}
	return $res->fetch_all(MYSQLI_ASSOC);
}

function suscripcion($cuenta)
{
	$r = filas("SELECT Nombre, Fecha_Vencimiento, Ilimitado FROM punto_subs.suscripciones WHERE ID_Suscripcion = '" . intval($cuenta) . "'");
	if (count($r) == 0) {
		return array('existe' => false, 'vigente' => false, 'vencimiento' => null);
	}
	$vigente = $r[0]['Ilimitado'] == 1 || strtotime($r[0]['Fecha_Vencimiento']) >= strtotime(date('Y-m-d'));
	return array('existe' => true, 'vigente' => $vigente, 'vencimiento' => $r[0]['Fecha_Vencimiento'], 'ilimitado' => $r[0]['Ilimitado'] == 1);
}

// ----- enlazar: cambia el código de un uso por la llave del equipo -----
if ($accion === 'enlazar') {
	$codigo = strtoupper(trim(isset($entrada['codigo']) ? $entrada['codigo'] : ''));
	$codigo = preg_replace('/[^A-Z0-9]/', '', $codigo);
	if (strlen($codigo) != 8) {
		fallo('El código debe tener 8 caracteres.');
	}
	$codigo = substr($codigo, 0, 4) . '-' . substr($codigo, 4);

	$disp = filas("SELECT * FROM punto_subs.dispositivos_offline WHERE Codigo = '" . esc($codigo) . "' AND Estado = 'Pendiente' AND Codigo_Expira > NOW() LIMIT 1");
	if (count($disp) == 0) {
		sleep(1); // frena intentos a ciegas
		fallo('El código no existe o ya caducó. Genera uno nuevo en Dispositivos offline.', 404, 'codigo_invalido');
	}
	$disp = $disp[0];
	$cuenta = intval($disp['FK_Cuenta']);

	if (!suscripcion($cuenta)['existe']) {
		fallo('La cuenta ya no existe.', 404);
	}

	$db->query("USE punto_venta_$cuenta");
	$caja = filas("SELECT c.ID_Caja, c.Nombre, c.Estado, c.FK_Sucursal, IFNULL(s.Nombre, '') AS Sucursal FROM cajas c LEFT JOIN sucursales s ON s.ID_Sucursal = c.FK_Sucursal WHERE c.ID_Caja = '" . intval($disp['FK_Caja']) . "'");
	if (count($caja) == 0) {
		fallo('La caja de este código ya no existe.', 404);
	}
	if ($caja[0]['Estado'] != '0') {
		fallo('La caja se abrió en la web. Ciérrala y genera un código nuevo.', 409, 'caja_abierta');
	}

	$token = bin2hex(random_bytes(32));
	$db->query("UPDATE punto_subs.dispositivos_offline SET Estado = 'Activo', Token_Hash = '" . hash('sha256', $token) . "', Codigo = NULL, Fecha_Activacion = NOW() WHERE ID_Dispositivo = '" . intval($disp['ID_Dispositivo']) . "'");

	$negocio = filas("SELECT Nombre FROM configuracion WHERE ID_Configuracion = 1");
	responder(array(
		'ok' => true,
		'token' => $token,
		'cuenta' => $cuenta,
		'dispositivo' => array('id' => intval($disp['ID_Dispositivo']), 'nombre' => $disp['Nombre']),
		'caja' => array('id' => intval($caja[0]['ID_Caja']), 'nombre' => $caja[0]['Nombre'], 'sucursal' => intval($caja[0]['FK_Sucursal']), 'nombreSucursal' => $caja[0]['Sucursal']),
		'negocio' => count($negocio) ? $negocio[0]['Nombre'] : ''
	));
}

// ----- de aquí en adelante, el equipo se identifica con su llave -----
$auth = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '');
if (isset($entrada['llave'])) {
	$auth = 'Bearer ' . $entrada['llave']; // por si el servidor no deja pasar el encabezado Authorization
}
if (!preg_match('/^Bearer\s+([a-f0-9]{64})$/i', trim($auth), $m)) {
	fallo('Falta la llave del equipo.', 401, 'sin_llave');
}
$disp = filas("SELECT * FROM punto_subs.dispositivos_offline WHERE Token_Hash = '" . hash('sha256', strtolower($m[1])) . "' AND Estado = 'Activo' LIMIT 1");
if (count($disp) == 0) {
	fallo('Este equipo fue desconectado. Enlázalo de nuevo con un código.', 401, 'desenlazado');
}
$disp = $disp[0];
$cuenta = intval($disp['FK_Cuenta']);
$idCaja = intval($disp['FK_Caja']);
$sub = suscripcion($cuenta);
if (!$sub['existe']) {
	fallo('La cuenta ya no existe.', 404, 'sin_cuenta');
}

$pendientes = isset($entrada['pendientes']) ? intval($entrada['pendientes']) : null;
$db->query("UPDATE punto_subs.dispositivos_offline SET Ultima_Sync = NOW()" . ($pendientes !== null ? ", Pendientes = '$pendientes'" : '') . " WHERE ID_Dispositivo = '" . intval($disp['ID_Dispositivo']) . "'");
$db->query("USE punto_venta_$cuenta");

// Desenlazar desde el equipo: libera la caja en la web. Lo no subido se queda en el equipo para subirlo al reenlazar.
if ($accion === 'desenlazar') {
	$db->query("UPDATE punto_subs.dispositivos_offline SET Estado = 'Desenlazado', Token_Hash = NULL, Codigo = NULL WHERE ID_Dispositivo = '" . intval($disp['ID_Dispositivo']) . "'");
	responder(array('ok' => true));
}

if ($accion === 'estado') {
	responder(array('ok' => true, 'suscripcion' => $sub, 'servidor' => date('Y-m-d H:i:s')));
}

// ----- bajar: copia de las tablas que la caja necesita, para guardarlas en el equipo -----
if ($accion === 'bajar') {
	$caja = filas("SELECT ID_Caja, Nombre, FK_Sucursal FROM cajas WHERE ID_Caja = '$idCaja'");
	if (count($caja) == 0) {
		fallo('La caja enlazada ya no existe.', 404);
	}

	$tablas = array(
		'configuracion' => "SELECT ID_Configuracion, Nombre, Domicilio, Telefono, Foto FROM configuracion",
		'cajas' => "SELECT ID_Caja, Nombre, Detalles, FK_Sucursal FROM cajas WHERE ID_Caja = '$idCaja'",
		'sucursales' => "SELECT * FROM sucursales WHERE ID_Sucursal = '" . intval($caja[0]['FK_Sucursal']) . "'",
		'usuarios' => "SELECT ID_Usuario, Nombre, Primer_Apellido, Segundo_Apellido, Correo, Contrasena, Tipo_Usuario, Permisos, Foto FROM usuarios WHERE Tipo_Usuario != 2 AND Contrasena != '' AND Activo = 1 AND Estatus = 'Desbloqueado'",
		'productos' => "SELECT ID_Producto, Codigo, Descripcion, Clase, Costo, Precio, Precio_Mayoreo, Stock_Minimo, Stock_Maximo, FK_Clasificacion, Foto FROM productos",
		'clasificaciones' => "SELECT * FROM clasificaciones",
		'inventario' => "SELECT * FROM inventario",
		'inventario_lotes' => "SELECT * FROM inventario_lotes",
		'impuestos' => "SELECT * FROM impuestos",
		'impuestos_productos' => "SELECT * FROM impuestos_productos",
		'promociones' => "SELECT * FROM promociones",
		'clientes' => "SELECT * FROM clientes",
		'direcciones_cliente' => "SELECT * FROM direcciones_cliente"
	);

	$salida = array();
	foreach ($tablas as $nombre => $sql) {
		$res = $db->query($sql);
		if (!$res) {
			// Tablas opcionales que una cuenta vieja aún no tiene (lotes, promociones)
			$salida[$nombre] = array('columnas' => array(), 'tipos' => array(), 'filas' => array());
			continue;
		}
		$columnas = array();
		$tipos = array(); // 'n' = numérica, para que el equipo compare números como números
		foreach ($res->fetch_fields() as $f) {
			$columnas[] = $f->name;
			$tipos[] = in_array($f->type, array(MYSQLI_TYPE_DECIMAL, MYSQLI_TYPE_NEWDECIMAL, MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_FLOAT, MYSQLI_TYPE_DOUBLE, MYSQLI_TYPE_LONGLONG, MYSQLI_TYPE_INT24)) ? 'n' : 't';
		}
		$salida[$nombre] = array('columnas' => $columnas, 'tipos' => $tipos, 'filas' => $res->fetch_all(MYSQLI_NUM));
	}

	responder(array('ok' => true, 'suscripcion' => $sub, 'servidor' => date('Y-m-d H:i:s'), 'cuenta' => $cuenta, 'caja' => $caja[0], 'tablas' => $salida));
}

// ----- subir: turnos de caja con sus ventas, movimientos y corte -----
if ($accion === 'subir') {
	$turnos = isset($entrada['turnos']) && is_array($entrada['turnos']) ? $entrada['turnos'] : array();
	$caja = new caja();
	$subidos = array();
	$errores = array();

	$yaSubido = function ($uuid, $tipo) use ($cuenta) {
		$r = filas("SELECT ID_Servidor FROM punto_subs.offline_subidos WHERE UUID = '" . esc($uuid) . "' AND FK_Cuenta = '$cuenta' AND Tipo = '" . esc($tipo) . "'");
		return count($r) ? intval($r[0]['ID_Servidor']) : 0;
	};
	$anotar = function ($uuid, $tipo, $idServidor) use ($cuenta, $db) {
		$db->query("INSERT INTO punto_subs.offline_subidos SET UUID = '" . esc($uuid) . "', FK_Cuenta = '$cuenta', Tipo = '" . esc($tipo) . "', ID_Servidor = '" . intval($idServidor) . "', Fecha = NOW()");
	};

	$idsUsuarios = array();
	foreach (filas("SELECT ID_Usuario FROM usuarios") as $u) {
		$idsUsuarios[intval($u['ID_Usuario'])] = true;
	}
	// Datos que vienen de otra cuenta (el equipo se reenlazó a otra): sus IDs no sirven aquí.
	$mapearUsuario = function ($id, $origen) use ($cuenta, $idsUsuarios, $disp) {
		$id = intval($id);
		if (intval($origen) == $cuenta && isset($idsUsuarios[$id])) {
			return $id;
		}
		return intval($disp['FK_Usuario_Alta']);
	};

	$clienteServidor = function ($cli) use ($db, $yaSubido, $anotar) {
		// $cli: {uuid, nombre, telefono, direccion:{uuid,...}}; se busca por teléfono antes de crear
		$idCliente = $yaSubido($cli['uuid'], 'cliente');
		if (!$idCliente) {
			$tel = esc(isset($cli['telefono']) ? $cli['telefono'] : '');
			$r = $tel != '' ? filas("SELECT ID_Cliente FROM clientes WHERE Telefono = '$tel' LIMIT 1") : array();
			if (count($r)) {
				$idCliente = intval($r[0]['ID_Cliente']);
			} else {
				$db->query("INSERT INTO clientes SET Tipo = 'Física', Nombre = '" . esc($cli['nombre']) . "', Telefono = '$tel', Fecha_Registro = NOW()");
				$idCliente = $db->insert_id;
			}
			$anotar($cli['uuid'], 'cliente', $idCliente);
		}

		$idDireccion = 0;
		if (!empty($cli['direccion']) && !empty($cli['direccion']['uuid'])) {
			$d = $cli['direccion'];
			$idDireccion = $yaSubido($d['uuid'], 'direccion');
			if (!$idDireccion) {
				$campos = array('Calle' => 'calle', 'No_Exterior' => 'noExterior', 'No_Interior' => 'noInterior', 'CP' => 'cp', 'Colonia' => 'colonia', 'Ciudad' => 'ciudad', 'Estado' => 'estado', 'Pais' => 'pais', 'Detalles' => 'detalles');
				$set = "FK_Cliente = '$idCliente'";
				foreach ($campos as $col => $k) {
					$set .= ", $col = '" . esc(isset($d[$k]) ? $d[$k] : '') . "'";
				}
				$db->query("INSERT INTO direcciones_cliente SET $set");
				$idDireccion = $db->insert_id;
				$anotar($d['uuid'], 'direccion', $idDireccion);
			}
		}
		return array($idCliente, $idDireccion);
	};

	foreach ($turnos as $t) {
		$origen = isset($t['cuentaOrigen']) ? intval($t['cuentaOrigen']) : $cuenta;
		$mismaCuenta = $origen == $cuenta;

		// 1. Apertura
		$idTurno = $yaSubido($t['uuid'], 'turno');
		if (!$idTurno) {
			$ok = $db->query("INSERT INTO detalles_caja SET FK_Caja = '$idCaja', Fecha_Abrir = '" . esc($t['fechaAbrir']) . "', Monto_Abrir = '" . floatval($t['montoAbrir']) . "', FK_Usuario_Abrir = '" . $mapearUsuario($t['usuarioAbrir'], $origen) . "'");
			if (!$ok) {
				$errores[] = array('uuid' => $t['uuid'], 'error' => 'No se pudo registrar la apertura: ' . $db->error);
				continue;
			}
			$idTurno = $db->insert_id;
			$anotar($t['uuid'], 'turno', $idTurno);
		}
		$subidos[] = $t['uuid'];

		// 2. Movimientos de efectivo
		foreach (isset($t['movimientos']) ? $t['movimientos'] : array() as $mv) {
			if (!$yaSubido($mv['uuid'], 'movimiento')) {
				$ok = $db->query("INSERT INTO movimientos_caja SET Monto = '" . floatval($mv['monto']) . "', Descripcion = '" . esc($mv['descripcion']) . "', Fecha_Registro = '" . esc($mv['fecha']) . "', Tipo = '" . esc($mv['tipo']) . "', FK_Detalle_Caja = '$idTurno'");
				if (!$ok) {
					$errores[] = array('uuid' => $mv['uuid'], 'error' => $db->error);
					continue;
				}
				$anotar($mv['uuid'], 'movimiento', $db->insert_id);
			}
			$subidos[] = $mv['uuid'];
		}

		// 3. Ventas, cada una en su transacción
		foreach (isset($t['ventas']) ? $t['ventas'] : array() as $v) {
			if ($yaSubido($v['uuid'], 'venta')) {
				$subidos[] = $v['uuid'];
				continue;
			}

			$db->begin_transaction();

			// El catálogo con que se hizo la venta puede ser de otra cuenta si el equipo se reenlazó
			$origenVenta = isset($v['cuentaOrigen']) ? intval($v['cuentaOrigen']) : $origen;
			$mismaCuenta = $origenVenta == $cuenta;

			$fkDireccion = intval(isset($v['fkDireccion']) ? $v['fkDireccion'] : 0);
			$cliente = intval(isset($v['cliente']) ? $v['cliente'] : 0);
			if (!empty($v['clienteNuevo'])) {
				list($cliente, $dirNueva) = $clienteServidor($v['clienteNuevo']);
				if ($dirNueva) {
					$fkDireccion = $dirNueva;
				}
			} else if (!$mismaCuenta && $cliente != 0) {
				$cliente = 0;
				$fkDireccion = 0;
				if (!empty($v['clienteDatos'])) {
					list($cliente, $dirNueva) = $clienteServidor($v['clienteDatos']);
				}
			}

			$productos = array();
			foreach ($v['productos'] as $p) {
				if (!$mismaCuenta) {
					$r = filas("SELECT ID_Producto FROM productos WHERE Codigo = '" . esc($p['codigo']) . "' LIMIT 1");
					$p['id'] = count($r) ? $r[0]['ID_Producto'] : 0; // sin producto no se descuenta inventario
					$p['lotes'] = array();
				}
				if (!isset($p['impuestos'])) {
					$p['impuestos'] = array();
				}
				$productos[] = $p;
			}

			$usuario = $mapearUsuario($v['usuario'], $origenVenta);
			$datosVenta = array(
				'total' => $v['total'], 'tipoPago' => $v['tipoPago'], 'pago' => $v['pago'], 'cambio' => $v['cambio'],
				'cliente' => $cliente, 'fkDireccion' => $fkDireccion, 'descuento' => $v['descuento'],
				'turno' => isset($v['turno']) ? $v['turno'] : '', 'productos' => $productos,
				'detallesOffline' => trim((isset($v['detalles']) ? $v['detalles'] . ' ' : '') . 'Venta offline' . (isset($v['folio']) ? ' ' . $v['folio'] : '') . ($usuario != intval($v['usuario']) || !$mismaCuenta ? ' - Cajero: ' . (isset($v['usuarioNombre']) ? $v['usuarioNombre'] : '') : ''))
			);

			$res = $caja->guardarVenta($omodelo, $datosVenta, $usuario, $v['fecha'], intval($idTurno));
			if (!$res['ok']) {
				$db->rollback();
				$errores[] = array('uuid' => $v['uuid'], 'error' => $res['mensaje']);
				continue;
			}
			$anotar($v['uuid'], 'venta', $res['id']);
			$db->commit();
			$subidos[] = $v['uuid'];
		}

		// 4. Corte (cierre del turno)
		if (!empty($t['cierre']) && !$yaSubido($t['uuid'], 'cierre')) {
			$c = $t['cierre'];
			$ok = $db->query("UPDATE detalles_caja SET Fecha_Cierre = '" . esc($c['fecha']) . "', Monto_Cierre = '" . floatval($c['monto']) . "', FK_Usuario_Cierre = '" . $mapearUsuario($c['usuario'], $origen) . "' WHERE ID_Detalle_Caja = '$idTurno'");
			if ($ok) {
				$anotar($t['uuid'], 'cierre', $idTurno);
				$subidos[] = $t['uuid'] . ':cierre';
			} else {
				$errores[] = array('uuid' => $t['uuid'] . ':cierre', 'error' => $db->error);
			}
		} else if (!empty($t['cierre'])) {
			$subidos[] = $t['uuid'] . ':cierre';
		}
	}

	$db->query("UPDATE punto_subs.dispositivos_offline SET Ultima_Subida = NOW() WHERE ID_Dispositivo = '" . intval($disp['ID_Dispositivo']) . "'");
	responder(array('ok' => true, 'subidos' => $subidos, 'errores' => $errores));
}

fallo('Acción desconocida');
