<?php
// Utilidades compartidas de la app offline (web y api/offline.php).
// Las tablas viven en punto_subs porque relacionan equipos con cuentas.

function offlineCrearTablas($omodelo)
{
	static $hecho = false;
	if ($hecho) {
		return;
	}
	$hecho = true;

	$omodelo->_insertar("CREATE TABLE IF NOT EXISTS punto_subs.dispositivos_offline (
		ID_Dispositivo INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		FK_Cuenta INT NOT NULL,
		FK_Caja INT NOT NULL,
		Nombre VARCHAR(100) NOT NULL,
		Codigo VARCHAR(12) NULL,
		Codigo_Expira DATETIME NULL,
		Token_Hash CHAR(64) NULL,
		Estado VARCHAR(12) NOT NULL DEFAULT 'Pendiente',
		Fecha_Alta DATETIME NOT NULL,
		Fecha_Activacion DATETIME NULL,
		Ultima_Sync DATETIME NULL,
		Ultima_Subida DATETIME NULL,
		Pendientes INT NOT NULL DEFAULT 0,
		FK_Usuario_Alta INT NOT NULL,
		UNIQUE KEY uk_codigo (Codigo),
		KEY k_token (Token_Hash),
		KEY k_caja (FK_Cuenta, FK_Caja)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8");

	// Idempotencia: cada registro subido desde un equipo se anota con su identificador local.
	$omodelo->_insertar("CREATE TABLE IF NOT EXISTS punto_subs.offline_subidos (
		UUID CHAR(36) NOT NULL,
		FK_Cuenta INT NOT NULL,
		Tipo VARCHAR(20) NOT NULL,
		ID_Servidor INT NOT NULL,
		Fecha DATETIME NOT NULL,
		PRIMARY KEY (UUID, FK_Cuenta, Tipo)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8");
}

// Devuelve el dispositivo activo que tiene bloqueada la caja, o null.
function offlineCajaBloqueada($omodelo, $cuenta, $idCaja)
{
	offlineCrearTablas($omodelo);
	$cuenta = intval($cuenta);
	$idCaja = intval($idCaja);
	$row = $omodelo->_consultar("SELECT ID_Dispositivo, Nombre FROM punto_subs.dispositivos_offline WHERE FK_Cuenta = '$cuenta' AND FK_Caja = '$idCaja' AND Estado = 'Activo' LIMIT 1");
	if ($omodelo->error == 'si' || $omodelo->numerofilas == 0) {
		return null;
	}
	return $row[0];
}

// Lista de IDs de caja bloqueadas en la cuenta: [ID_Caja => Nombre del equipo]
function offlineCajasBloqueadas($omodelo, $cuenta)
{
	offlineCrearTablas($omodelo);
	$cuenta = intval($cuenta);
	$lista = array();
	$row = $omodelo->_consultar("SELECT FK_Caja, Nombre FROM punto_subs.dispositivos_offline WHERE FK_Cuenta = '$cuenta' AND Estado = 'Activo'");
	if ($omodelo->error != 'si') {
		for ($i = 0; $i < $omodelo->numerofilas; $i++) {
			$lista[$row[$i]['FK_Caja']] = $row[$i]['Nombre'];
		}
	}
	return $lista;
}

// Código legible sin caracteres confusos (0/O, 1/I): XXXX-XXXX
function offlineGenerarCodigo()
{
	$letras = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
	$codigo = '';
	for ($i = 0; $i < 8; $i++) {
		$codigo .= $letras[random_int(0, strlen($letras) - 1)];
		if ($i == 3) {
			$codigo .= '-';
		}
	}
	return $codigo;
}
?>
