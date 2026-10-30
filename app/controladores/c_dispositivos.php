<?php
require_once __DIR__ . '/../modelo/offline.php';

// Menú "Dispositivos offline": enlazar equipos de la app de escritorio/móvil a una caja.
class dispositivos
{
  private function _soloAdmin()
  {
    if (!isset($_SESSION['user_punto_venta']['Tipo_Usuario']) || $_SESSION['user_punto_venta']['Tipo_Usuario'] != '1') {
      echo 'Sin permiso';
      return false;
    }
    return true;
  }

  public function _consultar()
  {
    if (!$this->_soloAdmin()) {
      return;
    }

    $omodelo = new m_modelo();
    offlineCrearTablas($omodelo);
    $cuenta = intval($_SESSION['user_punto_bd']);

    $row = $omodelo->_consultar("SELECT d.ID_Dispositivo, d.Nombre, d.FK_Caja, IFNULL(c.Nombre, 'Caja eliminada') AS Caja, c.Estado AS Estado_Caja, d.Codigo, d.Codigo_Expira, (d.Codigo_Expira < NOW()) AS Vencido, d.Estado, d.Fecha_Alta, d.Fecha_Activacion, d.Ultima_Sync, d.Ultima_Subida, d.Pendientes
      FROM punto_subs.dispositivos_offline d LEFT JOIN cajas c ON c.ID_Caja = d.FK_Caja
      WHERE d.FK_Cuenta = '$cuenta' AND d.Estado <> 'Desenlazado'
      ORDER BY d.ID_Dispositivo DESC");

    $lista = array();
    if ($omodelo->error != 'si') {
      for ($i = 0; $i < $omodelo->numerofilas; $i++) {
        $r = $row[$i];
        $lista[] = array(
          'id' => $r['ID_Dispositivo'],
          'nombre' => $r['Nombre'],
          'caja' => $r['Caja'],
          'cajaAbierta' => $r['Estado_Caja'] == '1',
          'estado' => $r['Estado'],
          'codigo' => $r['Estado'] == 'Pendiente' ? $r['Codigo'] : '',
          'codigoVencido' => $r['Estado'] == 'Pendiente' && $r['Vencido'] == '1',
          'codigoExpira' => $r['Codigo_Expira'],
          'activacion' => $r['Fecha_Activacion'],
          'ultimaSync' => $r['Ultima_Sync'],
          'ultimaSubida' => $r['Ultima_Subida'],
          'pendientes' => intval($r['Pendientes'])
        );
      }
    }

    // Cajas disponibles para enlazar: cerradas y sin otro equipo (activo o con código vigente)
    $cajas = array();
    $rowC = $omodelo->_consultar("SELECT ID_Caja, Nombre FROM cajas WHERE Estado = 0
      AND ID_Caja NOT IN (SELECT FK_Caja FROM punto_subs.dispositivos_offline WHERE FK_Cuenta = '$cuenta' AND (Estado = 'Activo' OR (Estado = 'Pendiente' AND Codigo_Expira > NOW())))
      ORDER BY Nombre");
    if ($omodelo->error != 'si') {
      for ($i = 0; $i < $omodelo->numerofilas; $i++) {
        $cajas[] = array('id' => $rowC[$i]['ID_Caja'], 'nombre' => $rowC[$i]['Nombre']);
      }
    }

    echo json_encode(array('dispositivos' => $lista, 'cajas' => $cajas));
  }

  public function _insertar()
  {
    if (!$this->_soloAdmin()) {
      return;
    }

    $omodelo = new m_modelo();
    offlineCrearTablas($omodelo);
    $cuenta = intval($_SESSION['user_punto_bd']);
    $nombre = $omodelo->link->real_escape_string(trim(isset($_POST['nombre']) ? $_POST['nombre'] : ''));
    $idCaja = intval(isset($_POST['idCaja']) ? $_POST['idCaja'] : 0);
    $idUsuario = intval($_SESSION['user_punto_venta']['ID_Usuario']);

    if ($nombre == '' || $idCaja <= 0) {
      echo 'Falta el nombre o la caja.';
      return;
    }

    $rowCaja = $omodelo->_consultar("SELECT Estado FROM cajas WHERE ID_Caja = '$idCaja'");
    if ($omodelo->error == 'si' || $omodelo->numerofilas == 0) {
      echo 'La caja no existe.';
      return;
    }
    if ($rowCaja[0]['Estado'] != '0') {
      echo 'La caja está abierta. Ciérrala antes de enlazar un dispositivo.';
      return;
    }

    $omodelo->_consultar("SELECT ID_Dispositivo FROM punto_subs.dispositivos_offline WHERE FK_Cuenta = '$cuenta' AND FK_Caja = '$idCaja' AND (Estado = 'Activo' OR (Estado = 'Pendiente' AND Codigo_Expira > NOW()))");
    if ($omodelo->numerofilas > 0) {
      echo 'Esa caja ya está enlazada o tiene un código pendiente.';
      return;
    }

    // Códigos vencidos de esta caja ya no sirven
    $omodelo->_insertar("DELETE FROM punto_subs.dispositivos_offline WHERE FK_Cuenta = '$cuenta' AND FK_Caja = '$idCaja' AND Estado = 'Pendiente'");

    for ($intento = 0; $intento < 5; $intento++) {
      $codigo = offlineGenerarCodigo();
      $error = $omodelo->_insertar("INSERT INTO punto_subs.dispositivos_offline SET FK_Cuenta = '$cuenta', FK_Caja = '$idCaja', Nombre = '$nombre', Codigo = '$codigo', Codigo_Expira = DATE_ADD(NOW(), INTERVAL 15 MINUTE), Estado = 'Pendiente', Fecha_Alta = NOW(), FK_Usuario_Alta = '$idUsuario'");
      if ($error != 'si') {
        echo "Correcto~$codigo";
        $omodelo->movimiento("Dispositivo offline '$nombre' agregado a la caja $idCaja", $idUsuario);
        return;
      }
    }

    echo 'No se pudo generar el código, intenta de nuevo.';
  }

  public function _modificar()
  {
    if (!$this->_soloAdmin()) {
      return;
    }

    // Nuevo código para un dispositivo que aún no se activa
    $omodelo = new m_modelo();
    offlineCrearTablas($omodelo);
    $cuenta = intval($_SESSION['user_punto_bd']);
    $id = intval(isset($_POST['id']) ? $_POST['id'] : 0);

    $row = $omodelo->_consultar("SELECT d.FK_Caja, c.Estado FROM punto_subs.dispositivos_offline d LEFT JOIN cajas c ON c.ID_Caja = d.FK_Caja WHERE d.ID_Dispositivo = '$id' AND d.FK_Cuenta = '$cuenta' AND d.Estado = 'Pendiente'");
    if ($omodelo->error == 'si' || $omodelo->numerofilas == 0) {
      echo 'El dispositivo no existe o ya está activo.';
      return;
    }
    if ($row[0]['Estado'] != '0') {
      echo 'La caja está abierta. Ciérrala antes de enlazar un dispositivo.';
      return;
    }

    for ($intento = 0; $intento < 5; $intento++) {
      $codigo = offlineGenerarCodigo();
      $error = $omodelo->_insertar("UPDATE punto_subs.dispositivos_offline SET Codigo = '$codigo', Codigo_Expira = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE ID_Dispositivo = '$id'");
      if ($error != 'si') {
        echo "Correcto~$codigo";
        return;
      }
    }

    echo 'No se pudo generar el código, intenta de nuevo.';
  }

  public function _eliminar()
  {
    if (!$this->_soloAdmin()) {
      return;
    }

    // Desenlazar: solo pide que la caja esté cerrada. Lo que el equipo no haya subido se puede subir después enlazándolo con otro código.
    $omodelo = new m_modelo();
    offlineCrearTablas($omodelo);
    $cuenta = intval($_SESSION['user_punto_bd']);
    $id = intval(isset($_POST['id']) ? $_POST['id'] : 0);

    $row = $omodelo->_consultar("SELECT d.Nombre, d.Estado, c.Estado AS Estado_Caja FROM punto_subs.dispositivos_offline d LEFT JOIN cajas c ON c.ID_Caja = d.FK_Caja WHERE d.ID_Dispositivo = '$id' AND d.FK_Cuenta = '$cuenta'");
    if ($omodelo->error == 'si' || $omodelo->numerofilas == 0) {
      echo 'El dispositivo no existe.';
      return;
    }
    if ($row[0]['Estado_Caja'] == '1') {
      echo 'La caja está abierta en la web. Ciérrala antes de desenlazar.';
      return;
    }

    if ($row[0]['Estado'] == 'Pendiente') {
      $error = $omodelo->_insertar("DELETE FROM punto_subs.dispositivos_offline WHERE ID_Dispositivo = '$id'");
    } else {
      $error = $omodelo->_insertar("UPDATE punto_subs.dispositivos_offline SET Estado = 'Desenlazado', Token_Hash = NULL, Codigo = NULL WHERE ID_Dispositivo = '$id'");
    }

    if ($error == 'si') {
      echo 'Error: ' . mysqli_error($omodelo->link);
    } else {
      echo 'Correcto';
      $omodelo->movimiento("Dispositivo offline '" . $omodelo->link->real_escape_string($row[0]['Nombre']) . "' desenlazado", $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }
}
?>
