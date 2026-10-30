<?php
// Solo para pruebas locales: libera la caja y genera un código de enlace como lo haría el menú "Dispositivos offline".
// Uso: php generar-codigo.php <cuenta> <idCaja>
chdir(__DIR__ . '/../../app');
$_SESSION = ['user_punto_bd' => intval($argv[1]), 'user_punto_venta' => ['ID_Usuario' => 1, 'Tipo_Usuario' => '1']];
require 'controladores/c_controller.php';
$omodelo = new m_modelo();
offlineCrearTablas($omodelo);
$omodelo->_insertar("UPDATE punto_subs.dispositivos_offline SET Estado = 'Desenlazado', Token_Hash = NULL WHERE FK_Cuenta = " . intval($argv[1]) . " AND FK_Caja = " . intval($argv[2]));
$_POST = ['nombre' => 'Equipo de prueba', 'idCaja' => $argv[2]];
ob_start();
(new dispositivos())->_insertar();
$r = trim(ob_get_clean());
echo strpos($r, 'Correcto~') === 0 ? substr($r, 9) : "ERROR $r";
