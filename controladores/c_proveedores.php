<?php
class proveedores {

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
      $busqueda = 'AND ';
      for ($i = 0; $i < count($separa); $i++) {
        $busqueda .= "CONCAT(DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), Razon_Social, RFC, Credito, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Contacto, Puesto, Email_Contacto, Telefono_Contacto, Telefono, Segundo_Telefono, Email, Clabe, Banco, Titular) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $query = "SELECT ID_Proveedor, Razon_Social AS Nombre, RFC, Credito, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Contacto, Puesto, Email_Contacto, Telefono_Contacto, Telefono, Segundo_Telefono, Email, Clabe, Banco, Titular, Fecha_Registro AS Fecha, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, IFNULL((SELECT SUM(Total) - IFNULL((SELECT SUM(Monto) FROM compras_pagos WHERE FK_Compra = ID_Compra), 0) FROM compras WHERE FK_Proveedor = ID_Proveedor AND Tipo_Compra != 'Contado'), 0) AS Adeudo, (SELECT COUNT(*) FROM proveedores WHERE ID_Proveedor != 1 $busqueda) AS Num FROM proveedores WHERE ID_Proveedor != 1 $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        for ($i = 0; $i < $numerofilas; $i++) {
          $bModificar = '';
          $bEliminar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Proveedores'][3] == '1') {
            $bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarProveedor" title="Modificar Proveedor" attrID="' . $row[$i]['ID_Proveedor'] . '"><i class="fas fa-pencil"></i></button>';
          }
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Proveedores'][4] == '1') {
            $bEliminar = '<button type="button" class="btn btn-sm btn-danger bEliminarProveedor" title="Eliminar Proveedor" attrID="' . $row[$i]['ID_Proveedor'] . '"><i class="fas fa-trash"></i></button>';
          }

          $domicilio = '<b>'.$row[$i]['Calle'].' #'.$row[$i]['No_Exterior'].'</b> ';
          $contacto = 'Tel: <b>'.$row[$i]['Telefono'].'</b><br>';

          if ($row[$i]['No_Interior'] != "") {
            $domicilio .= 'No. Int. <b>'.$row[$i]['No_Interior'].'</b> ';
          }

          if ($row[$i]['Colonia'] != "") {
            $domicilio .= 'Col. <b>'.$row[$i]['Colonia'].'</b> ';
          }

          $domicilio .= 'C.P. <b>'.$row[$i]['CP'].'</b>, <b>'.$row[$i]['Ciudad'].'</b> <b>'.$row[$i]['Estado'].'</b>, <b>'.$row[$i]['Pais'].'</b>';
          

          if ($row[$i]['Segundo_Telefono'] != "") {
            $contacto .= 'Tel: <b>'.$row[$i]['Segundo_Telefono'].'</b><br>';
          }

          if ($row[$i]['Email'] != "") {
            $contacto .= 'Email: <b>'.$row[$i]['Email'].'</b><br>';
          }

          if ($row[$i]['Contacto'] != '') {
            $contacto .= 'Contacto: <b>'.$row[$i]['Contacto'].'</b>';
          }

          if ($row[$i]['Email_Contacto'] != '') {
            $contacto .= 'Email contacto: <b>'.$row[$i]['Email_Contacto'].'</b>';
          }

          if ($row[$i]['Telefono_Contacto'] != '') {
            $contacto .= 'Tel contacto: <b>'.$row[$i]['Telefono_Contacto'].'</b>';
          }

          $cuenta = '';
          if ($row[$i]['Clabe'] != '') {
            $cuenta .= 'CLABE Interbancaria: <b>'.$row[$i]['Clabe'].'</b>';
          }

          if ($row[$i]['Clabe'] != '') {
            $cuenta .= 'Banco: <b>'.$row[$i]['Banco'].'</b>';
          }

          if ($row[$i]['Titular'] != '') {
            $cuenta .= 'Titular: <b>'.$row[$i]['Titular'].'</b>';
          }

          $nombre = $row[$i]['Nombre'];
          if ($row[$i]['RFC'] != '') {
            $nombre .= '<br>RFC: <b>'.$row[$i]['RFC'].'</b>';
          }

          if ($row[$i]['Credito'] != '') {
            $nombre .= '<br>Crédito: <b>'.$row[$i]['Credito'].'</b><br>Adeudo: <b>'.$row[$i]['Adeudo'].'</b><br>Restante: <b class="dinero">'.($row[$i]['Credito'] - $row[$i]['Adeudo']).'</b>';
          }

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Proveedor'],
            'Fecha' => $row[$i]['Fecha_Registro'],
            'Nombre' => $nombre,
            'Domicilio' => $domicilio,
            'Contacto' => $contacto,
            'Cuenta' => $cuenta,
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

    $razon = $omodelo->link->real_escape_string($razon);
    $rfc = $omodelo->link->real_escape_string($rfc);
    $credito = $omodelo->link->real_escape_string($credito);
    $calle = $omodelo->link->real_escape_string($calle);
    $noExterior = $omodelo->link->real_escape_string($noExterior);
    $noInterior = $omodelo->link->real_escape_string($noInterior);
    $colonia = $omodelo->link->real_escape_string($colonia);
    $cp = $omodelo->link->real_escape_string($cp);
    $ciudad = $omodelo->link->real_escape_string($ciudad);
    $estado = $omodelo->link->real_escape_string($estado);
    $pais = $omodelo->link->real_escape_string($pais);
    $contacto = $omodelo->link->real_escape_string($contacto);
    $puesto = $omodelo->link->real_escape_string($puesto);
    $emailContacto = $omodelo->link->real_escape_string($emailContacto);
    $telefonoContacto = $omodelo->link->real_escape_string($telefonoContacto);
    $telefono = $omodelo->link->real_escape_string($telefono);
    $segundoTelefono = $omodelo->link->real_escape_string($segundoTelefono);
    $email = $omodelo->link->real_escape_string($email);
    $clabe = $omodelo->link->real_escape_string($clabe);
    $banco = $omodelo->link->real_escape_string($banco);
    $titular = $omodelo->link->real_escape_string($titular);

    $query = "INSERT INTO proveedores SET Razon_Social = '$razon', RFC = '$rfc', Credito = '$credito', Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', Colonia = '$colonia', CP = '$cp', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Contacto = '$contacto', Puesto = '$puesto', Email_Contacto = '$emailContacto', Telefono_Contacto = '$telefonoContacto', Telefono = '$telefono', Segundo_Telefono = '$segundoTelefono', Email = '$email', Clabe = '$clabe', Banco = '$banco', Titular = '$titular', Fecha_Registro = '$fecha'";
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

    if ($tipo == 'proveedor') {
      $id = $omodelo->link->real_escape_string($id);

      $query = "SELECT ID_Proveedor, Razon_Social, RFC, Credito, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Contacto, Puesto, Email_Contacto, Telefono_Contacto, Telefono, Segundo_Telefono, Email, Clabe, Banco, Titular FROM proveedores WHERE ID_Proveedor = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error : ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo = array(
              'ID_Proveedor' => $row[$i]['ID_Proveedor'],
              'Razon_Social' => $row[$i]['Razon_Social'],
              'RFC' => $row[$i]['RFC'],
              'Credito' => $row[$i]['Credito'],
              'Calle' => $row[$i]['Calle'],
              'No_Exterior' => $row[$i]['No_Exterior'],
              'No_Interior' => $row[$i]['No_Interior'],
              'Colonia' => $row[$i]['Colonia'],
              'CP' => $row[$i]['CP'],
              'Ciudad' => $row[$i]['Ciudad'],
              'Estado' => $row[$i]['Estado'],
              'Pais' => $row[$i]['Pais'],
              'Contacto' => $row[$i]['Contacto'],
              'Puesto' => $row[$i]['Puesto'],
              'Email_Contacto' => $row[$i]['Email_Contacto'],
              'Telefono_Contacto' => $row[$i]['Telefono_Contacto'],
              'Telefono' => $row[$i]['Telefono'],
              'Segundo_Telefono' => $row[$i]['Telefono'],
              'Email' => $row[$i]['Email'],
              'Clabe' => $row[$i]['Clabe'],
              'Banco' => $row[$i]['Banco'],
              'Titular' => $row[$i]['Titular']
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
    $razon = $omodelo->link->real_escape_string($razon);
    $rfc = $omodelo->link->real_escape_string($rfc);
    $credito = $omodelo->link->real_escape_string($credito);
    $calle = $omodelo->link->real_escape_string($calle);
    $noExterior = $omodelo->link->real_escape_string($noExterior);
    $noInterior = $omodelo->link->real_escape_string($noInterior);
    $colonia = $omodelo->link->real_escape_string($colonia);
    $cp = $omodelo->link->real_escape_string($cp);
    $ciudad = $omodelo->link->real_escape_string($ciudad);
    $estado = $omodelo->link->real_escape_string($estado);
    $pais = $omodelo->link->real_escape_string($pais);
    $contacto = $omodelo->link->real_escape_string($contacto);
    $puesto = $omodelo->link->real_escape_string($puesto);
    $emailContacto = $omodelo->link->real_escape_string($emailContacto);
    $telefonoContacto = $omodelo->link->real_escape_string($telefonoContacto);
    $telefono = $omodelo->link->real_escape_string($telefono);
    $segundoTelefono = $omodelo->link->real_escape_string($segundoTelefono);
    $email = $omodelo->link->real_escape_string($email);
    $clabe = $omodelo->link->real_escape_string($clabe);
    $banco = $omodelo->link->real_escape_string($banco);
    $titular = $omodelo->link->real_escape_string($titular);

    $query = "UPDATE proveedores SET Razon_Social = '$razon', RFC = '$rfc', Credito = '$credito', Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', Colonia = '$colonia', CP = '$cp', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Contacto = '$contacto', Puesto = '$puesto', Email_Contacto = '$emailContacto', Telefono_Contacto = '$telefonoContacto', Telefono = '$telefono', Segundo_Telefono = '$segundoTelefono', Email = '$email', Clabe = '$clabe', Banco = '$banco', Titular = '$titular' WHERE ID_Proveedor = '$id'";
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

    $query = "DELETE FROM proveedores WHERE ID_Proveedor = '$id'";
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