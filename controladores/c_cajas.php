<?php
class cajas {

  public function _consultar() {
    $omodelo = new m_modelo();
    extract($_POST);

    $buscar = trim($omodelo->link->real_escape_string($buscar));
    $limit = $omodelo->link->real_escape_string($limit);
    $pagina = $omodelo->link->real_escape_string($pagina);
    $ordenColumna = $omodelo->link->real_escape_string($ordenColumna);
    $orden = $omodelo->link->real_escape_string($orden);

    $arreglo = array();

    $busqueda = '';
    if (trim($buscar) != '') {
      $separa = explode(' ', trim($buscar));
      $busqueda = 'WHERE ';
      for ($i = 0; $i < count($separa); $i++) {
        $busqueda .= "CONCAT(cajas.Nombre, Detalles, IFNULL(CONCAT(usuarios.Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido), 'Ninguno'), IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = FK_Sucursal), 'NA')) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];
    $query = "SELECT ID_Caja, FK_Sucursal, IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = FK_Sucursal), 'NA') AS Sucursal, cajas.Nombre AS Nombre, Detalles, Estado, FK_Usuario, IFNULL(CONCAT(usuarios.Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido), 'Ninguno') AS Usuario, IFNULL((SELECT COUNT(*) FROM cajas WHERE FK_Usuario = '$idUsuario' AND Estado = 1), 0) AS NumUso, IFNULL((SELECT COUNT(*) FROM detalles_caja WHERE FK_Usuario_Cierre = 0 AND FK_Usuario_Abrir = '$idUsuario'), 0) AS NumAbri, (SELECT COUNT(*) FROM cajas $busqueda) AS Num FROM cajas LEFT JOIN usuarios ON FK_Usuario = ID_Usuario $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    }else{
      if($numerofilas > 0){
        for ($i = 0; $i < $numerofilas; $i++) {
          $usuarioUso = 0;
          $detalles = trim($row[$i]['Detalles']) != '' ? '<b>Detalles: </b><span>'.$row[$i]['Detalles'].'</span><br>': '';

          if ($row[$i]['Estado'] == 0) {
            $estado = '<span class="badge rounded-pill bg-danger">Cerrada</span>';
            
            if(($row[$i]['NumUso'] + $row[$i]['NumAbri']) == 0){
              $detalles .= '<br><button type="button" class="btn btn-primary btn-sm bAbrirCaja" attrID="' . $row[$i]['ID_Caja'] . '">Abrir</button>';
            }
          }else{
            $estado = '<span class="badge rounded-pill bg-success">Abierta</span>';
            $detalles .= '<br><b>Activo: </b><span style="font-size: 18px;">' . $row[$i]['Usuario'] . '</span><br>';

            $query1 = "SELECT ID_Detalle_Caja, Fecha_Abrir, Monto_Abrir, FK_Usuario_Abrir, Fecha_Cierre, Monto_Cierre, FK_Usuario_Cierre, IFNULL(CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido), '') AS Usuario_Abrir FROM detalles_caja LEFT JOIN usuarios ON FK_Usuario_Abrir = ID_Usuario WHERE FK_Caja = '".$row[$i]['ID_Caja']."' ORDER BY ID_Detalle_Caja DESC LIMIT 1";
            $row1 = $omodelo->_consultar($query1);
            $numerofilas1 = $omodelo->numerofilas;

            if ($row == 'si') {
              echo "Error: " . mysqli_error($omodelo->link);
            }else{
              if($numerofilas > 0){
                $usuarioUso = $row1[0]['FK_Usuario_Abrir'];
                $detalles .= '<b>Abrió caja: </b><span>' . $row1[0]['Usuario_Abrir'] . '</span><br>
                <b>Apertura: </b><span class="dinero" style="font-size: 26px;">' . $row1[0]['Monto_Abrir'] . '</span>';
              }
            }
          }

          $bModificar = '';
          if ($row[$i]['Estado'] == 0 && ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Cajas'][3] == '1')) {
            $bModificar = '<button attrID="' . $row[$i]['ID_Caja'] . '" class="btn btn-warning btn-sm bModificarCaja"><i class="fas fa-edit"></i></button>';
          }

          $bEliminar = '';
          if ($row[$i]['Estado'] == 0 && ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Cajas'][4] == '1')) {
            $bEliminar = '<button attrID="' . $row[$i]['ID_Caja'] . '" class="btn btn-danger btn-sm bEliminarCaja"><i class="fas fa-trash"></i></button>';
          }

          $bTomar = '';
          if($row[$i]['Estado'] == 1 && $row[$i]['FK_Usuario'] == '0' && (($row[$i]['NumUso'] + $row[$i]['NumAbri']) == 0 || $usuarioUso == $_SESSION['user_punto_venta']['ID_Usuario'])){
            $bTomar = '<br><button type="button" class="btn btn-primary btn-sm bTomarCaja" attrID="' . $row[$i]['ID_Caja'] . '">Tomar Caja</button>';
          }else if($row[$i]['Estado'] == 1 && $row[$i]['NumUso'] == 0){
            //&& $_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' ($row[$i]['NumUso'] + $row[$i]['NumAbri'])  && $usuarioUso != $_SESSION['user_punto_venta']['ID_Usuario']
            $bTomar = '<br><button type="button" class="btn btn-outline-warning btn-sm bTomarCaja" attrID="' . $row[$i]['ID_Caja'] . '"><i class="fas fa-circle-info"></i> Tomar Caja</button>';
          }

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Caja'],
            'Nombre' => '<span style="font-size: 14px;">'.$row[$i]['Nombre'].'</span>',
            'Estado' => $estado,
            'Detalles' => $detalles.$bTomar,
            'Sucursal' => '<span style="font-size: 14px;" attrID="'.$row[$i]['FK_Sucursal'].'">'.$row[$i]['Sucursal'].'</span>',
            'Acciones' => $bModificar . ' ' .$bEliminar
          );
        }

        $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
      }
    } 
      
    echo json_encode($arreglo);
  }

  public function _detalles(){
    $omodelo = new m_modelo();
    extract($_POST);
    $tipo = $omodelo->link->real_escape_string($tipo);

    if($tipo == 'cortes'){
      $buscar = trim($omodelo->link->real_escape_string($buscar));
      $limit = $omodelo->link->real_escape_string($limit);
      $pagina = $omodelo->link->real_escape_string($pagina);
      $ordenColumna = $omodelo->link->real_escape_string($ordenColumna);
      $orden = $omodelo->link->real_escape_string($orden);

      $arreglo = array();

      $busqueda = '';
      if (trim($buscar) != '') {
        $separa = explode(' ', trim($buscar));
        $busqueda = 'AND ';
        for ($i = 0; $i < count($separa); $i++) {
          $busqueda .= "CONCAT(Nombre, Monto_Abrir, Monto_Cierre, DATE_FORMAT(Fecha_Abrir, '%d-%m-%Y %r'), DATE_FORMAT(Fecha_Cierre, '%d-%m-%Y %r'), IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario_Abrir), ''), IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario_Cierre), '')) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $query = "SELECT ID_Detalle_Caja, FK_Caja, Nombre AS Caja, Fecha_Abrir, DATE_FORMAT(Fecha_Abrir, '%d-%m-%Y %r') AS FechaAbrir, Monto_Abrir, FK_Usuario_Abrir, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario_Abrir), '') AS Usuario_Abrir, Fecha_Cierre, DATE_FORMAT(Fecha_Cierre, '%d-%m-%Y %r') AS FechaCierre, Monto_Cierre, FK_Usuario_Cierre, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario_Cierre), '') AS Usuario_Cierre, (SELECT COUNT(*) FROM detalles_caja INNER JOIN cajas ON FK_Caja = ID_Caja $busqueda) AS Num FROM detalles_caja INNER JOIN cajas ON FK_Caja = ID_Caja WHERE FK_Usuario_Cierre != 0 $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      }else{
        if($numerofilas > 0){
          for ($i=0; $i < $numerofilas; $i++) { 
            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Detalle_Caja'],
              'Caja' => $row[$i]['Caja'],
              'Fecha_Abrir' => $row[$i]['FechaAbrir'],
              'Monto_Abrir' => '<span class="dinero">'.$row[$i]['Monto_Abrir'].'</span>',
              'Usuario_Abrir' => $row[$i]['Usuario_Abrir'],
              'Fecha_Cierre' => $row[$i]['FechaCierre'],
              'Monto_Cierre' => '<span class="dinero">'.$row[$i]['Monto_Cierre'].'</span>',
              'Usuario_Cierre' => $row[$i]['Usuario_Cierre'],
              'Acciones' => '<a href="controladores/pdf/ticketCaja.php?id='.$row[$i]['ID_Detalle_Caja'].'" target="_blank" class="btn btn-sm btn-info bReimprimirCorte" title="Imprimir Ticket"><i class="fas fa-file"></i></a>'
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }

      echo json_encode($arreglo);
    }
  }

  public function _insertar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $fecha = date('Y-m-d H:i:s');
    $tipo = $omodelo->link->real_escape_string($tipo);

    if($tipo == 'guardarCaja'){
      $nombreCaja = $omodelo->link->real_escape_string($nombreCaja);
      $detallesCaja = $omodelo->link->real_escape_string($detallesCaja);
      $sucursal = $omodelo->link->real_escape_string($sucursal);

      $query = "INSERT INTO cajas SET Nombre = '$nombreCaja', Detalles = '$detallesCaja', FK_Sucursal = '$sucursal'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      }else{
        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }else if ($tipo == 'abrir') {
      $montoCaja = $omodelo->link->real_escape_string(trim($montoCaja));
      $fkCaja = $omodelo->link->real_escape_string(trim($fkCaja));

      $query = "UPDATE cajas SET Estado = 1, FK_Usuario = '".$_SESSION['user_punto_venta']['ID_Usuario']."' WHERE ID_Caja = $fkCaja";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      }else{
        $query1 = "INSERT INTO detalles_caja SET FK_Caja = '$fkCaja', Fecha_Abrir = '$fecha', Monto_Abrir = '$montoCaja', FK_Usuario_Abrir = '".$_SESSION['user_punto_venta']['ID_Usuario']."'";
        $error1 = $omodelo->_insertar($query1);

        if ($error1 == 'si') {
          echo "Error 2: " . mysqli_error($omodelo->link);
        }

        echo "Correcto";
        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }
  }

  public function _modificar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $tipo = $omodelo->link->real_escape_string($tipo);

    if($tipo == 'guardarCaja'){
      $idCaja = $omodelo->link->real_escape_string($idCaja);
      $nombreCaja = $omodelo->link->real_escape_string($nombreCaja);
      $detallesCaja = $omodelo->link->real_escape_string($detallesCaja);
      $sucursal = $omodelo->link->real_escape_string($sucursal);

      $query = "UPDATE cajas SET Nombre = '$nombreCaja', Detalles = '$detallesCaja', FK_Sucursal = '$sucursal' WHERE ID_Caja = '$idCaja'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      }else{
        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }else if($tipo == 'tomarCaja'){
      $idCaja = $omodelo->link->real_escape_string($idCaja);

      $query = "UPDATE cajas SET FK_Usuario = '".$_SESSION['user_punto_venta']['ID_Usuario']."' WHERE ID_Caja = '$idCaja'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      }else{
        //historialCaja

        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }
  }

  public function _eliminar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $idCaja = $omodelo->link->real_escape_string(trim($idCaja));

    $query = "DELETE FROM cajas WHERE ID_Caja = '$idCaja'";
    $row = $omodelo->_insertar($query);

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    }else{
      echo 'Correcto';

      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }
}
?>