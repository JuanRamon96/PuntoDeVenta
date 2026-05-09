<?php
class sucursales {

  public function _consultar() {
    $omodelo = new m_modelo();
    extract($_POST);

    $buscar = $omodelo->link->real_escape_string($buscar);
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
        $busqueda .= "CONCAT(DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), Nombre, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Email, Telefono, Segundo_Telefono) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $query = "SELECT ID_Sucursal, Nombre, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Email, Telefono, Segundo_Telefono, Latitud, Longitud, Fecha_Registro AS Fecha, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, (SELECT COUNT(*) FROM sucursales $busqueda) AS Num FROM sucursales $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        for ($i = 0; $i < $numerofilas; $i++) {
          $bModificar = '';
          $bEliminar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Sucursales'][3] == '1') {
            $bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarSucursal" title="Modificar Sucursal" attrID="' . $row[$i]['ID_Sucursal'] . '"><i class="fas fa-pencil"></i></button>';
          }
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Sucursales'][4] == '1') {
            $bEliminar = '<button type="button" class="btn btn-sm btn-danger bEliminarSucursal" title="Eliminar Sucursal" attrID="' . $row[$i]['ID_Sucursal'] . '"><i class="fas fa-trash"></i></button>';
          }

          $contacto = '';
          if ($row[$i]['Telefono'] != "") {
            $contacto = 'Tel: <b>'.$row[$i]['Telefono'].'</b><br>';
          }
          
          $domicilio = ''; 
          if ($row[$i]['Calle'] != "") {
            $domicilio = '<b>'.$row[$i]['Calle'];
          }

          if ($row[$i]['No_Exterior'] != "") {
            $domicilio .= ' #'.$row[$i]['No_Exterior'].'</b> ';
          }

          if ($row[$i]['No_Interior'] != "") {
            $domicilio .= 'No. Int. <b>'.$row[$i]['No_Interior'].'</b> ';
          }

          if($row[$i]['CP'] != ''){
            $domicilio .= ' C.P. '.$row[$i]['CP'];
          }

          if ($row[$i]['Colonia'] != "") {
            $domicilio .= ' Col. <b>'.$row[$i]['Colonia'].'</b> ';
          }

          if($row[$i]['Ciudad'] != ''){
            $domicilio .= ', '.$row[$i]['Ciudad'];
          }

          if($row[$i]['Estado'] != ''){
            $domicilio .= ' '.$row[$i]['Estado'];
          }

          if($row[$i]['Pais'] != ''){
            $domicilio .= ', '.$row[$i]['Pais'];
          }

          if ($row[$i]['Segundo_Telefono'] != "") {
            $contacto .= 'Tel: <b>'.$row[$i]['Segundo_Telefono'].'</b><br>';
          }

          if ($row[$i]['Email'] != "") {
            $contacto .= 'Email: <b>'.$row[$i]['Email'].'</b><br>';
          }

          if ($row[$i]['Latitud'] != '' && $row[$i]['Longitud'] != '') {
            $contacto .= 'Ubicación: <b>'.$row[$i]['Latitud'].', '.$row[$i]['Longitud'].'</b>';
          }

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Sucursal'],
            'Fecha' => $row[$i]['Fecha_Registro'],
            'Nombre' => $row[$i]['Nombre'],
            'Domicilio' => $domicilio,
            'Contacto' => $contacto,
            'Acciones' => $bModificar . ' ' . $bEliminar
          );
        }
        
        $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
      }
    }

    echo json_encode($arreglo);
  }

  public function _insertar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $fecha = date('Y-m-d H:i:s');

    $nombre = $omodelo->link->real_escape_string($nombre);
    $calle = $omodelo->link->real_escape_string($calle);
    $noExterior = $omodelo->link->real_escape_string($noExterior);
    $noInterior = $omodelo->link->real_escape_string($noInterior);
    $colonia = $omodelo->link->real_escape_string($colonia);
    $cp = $omodelo->link->real_escape_string($cp);
    $ciudad = $omodelo->link->real_escape_string($ciudad);
    $estado = $omodelo->link->real_escape_string($estado);
    $pais = $omodelo->link->real_escape_string($pais);
    $email = $omodelo->link->real_escape_string($email);
    $telefono = $omodelo->link->real_escape_string($telefono);
    $segundoTelefono = $omodelo->link->real_escape_string($segundoTelefono);
    $latitud = $omodelo->link->real_escape_string($latitud);
    $longitud = $omodelo->link->real_escape_string($longitud);

    $query = "INSERT INTO sucursales SET Nombre = '$nombre', Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', Colonia = '$colonia', CP = '$cp', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Email = '$email', Telefono = '$telefono', Segundo_Telefono = '$segundoTelefono', Latitud = '$latitud', Longitud = '$longitud', Fecha_Registro = '$fecha'";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    }else {
      echo 'Correcto';

      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }

  public function _detalles() {
    $omodelo = new m_modelo();
    extract($_POST);
    $arreglo = array();
    $tipo = $omodelo->link->real_escape_string($tipo);

    if ($tipo == 'sucursal') {
      $id = $omodelo->link->real_escape_string($id);

      $query = "SELECT ID_Sucursal, Nombre, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Email, Telefono, Segundo_Telefono, Latitud, Longitud FROM sucursales WHERE ID_Sucursal = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error : ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo = array(
              'ID_Sucursal' => $row[$i]['ID_Sucursal'],
              'Nombre' => $row[$i]['Nombre'],
              'Calle' => $row[$i]['Calle'],
              'No_Exterior' => $row[$i]['No_Exterior'],
              'No_Interior' => $row[$i]['No_Interior'],
              'Colonia' => $row[$i]['Colonia'],
              'CP' => $row[$i]['CP'],
              'Ciudad' => $row[$i]['Ciudad'],
              'Estado' => $row[$i]['Estado'],
              'Pais' => $row[$i]['Pais'],
              'Email' => $row[$i]['Email'],
              'Telefono' => $row[$i]['Telefono'],
              'Segundo_Telefono' => $row[$i]['Telefono'],
              'Latitud' => $row[$i]['Latitud'],
              'Longitud' => $row[$i]['Longitud']
            );
          }
        }
      }

      echo json_encode($arreglo);
    }
  }

  public function _modificar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $fecha = date('Y-m-d H:i:s');

    $id = $omodelo->link->real_escape_string($id);
    $nombre = $omodelo->link->real_escape_string($nombre);
    $calle = $omodelo->link->real_escape_string($calle);
    $noExterior = $omodelo->link->real_escape_string($noExterior);
    $noInterior = $omodelo->link->real_escape_string($noInterior);
    $colonia = $omodelo->link->real_escape_string($colonia);
    $cp = $omodelo->link->real_escape_string($cp);
    $ciudad = $omodelo->link->real_escape_string($ciudad);
    $estado = $omodelo->link->real_escape_string($estado);
    $pais = $omodelo->link->real_escape_string($pais);
    $email = $omodelo->link->real_escape_string($email);
    $telefono = $omodelo->link->real_escape_string($telefono);
    $segundoTelefono = $omodelo->link->real_escape_string($segundoTelefono);
    $latitud = $omodelo->link->real_escape_string($latitud);
    $longitud = $omodelo->link->real_escape_string($longitud);

    $query = "UPDATE sucursales SET Nombre = '$nombre', Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', Colonia = '$colonia', CP = '$cp', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Email = '$email', Telefono = '$telefono', Segundo_Telefono = '$segundoTelefono', Latitud = '$latitud', Longitud = '$longitud' WHERE ID_Sucursal = '$id'";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    }else{
      echo 'Correcto';

      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }

  public function _eliminar() {
    $omodelo = new m_modelo();
    extract($_POST);

    $id = $omodelo->link->real_escape_string($id);

    $query = "DELETE FROM sucursales WHERE ID_Sucursal = '$id'";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    } else {
      echo 'Correcto';

      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }
}
?>