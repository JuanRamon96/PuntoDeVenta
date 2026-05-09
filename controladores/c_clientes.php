<?php
class clientes {

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
        $busqueda .= "CONCAT(DATE_FORMAT(clientes.Fecha_Registro, '%d-%m-%Y %r'), Tipo, clientes.Nombre, Primer_Apellido, Segundo_Apellido, Sexo, RFC, Regimen_CFDI, Razon_Social, clientes.Calle, clientes.No_Exterior, clientes.No_Interior, clientes.Colonia, clientes.CP, clientes.Ciudad, clientes.Estado, clientes.Pais, clientes.Telefono, clientes.Segundo_Telefono, clientes.Email, Contacto, Puesto_Contacto, Telefono_Contacto, Email_Contacto, sucursales.Nombre) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $query = "SELECT ID_Cliente, FK_Sucursal, clientes.Latitud AS Latitud, clientes.Longitud AS Longitud, Tipo, clientes.Nombre AS Nombre, Primer_Apellido, Segundo_Apellido, Sexo, RFC, Regimen_CFDI, Razon_Social, clientes.Calle AS Calle, clientes.No_Exterior AS No_Exterior, clientes.No_Interior AS No_Interior, clientes.Colonia AS Colonia, clientes.CP AS CP, clientes.Ciudad AS Ciudad, clientes.Estado AS Estado, clientes.Pais AS Pais, clientes.Telefono AS Telefono, clientes.Segundo_Telefono AS Segundo_Telefono, clientes.Email AS Email, Contacto, Puesto_Contacto, Telefono_Contacto, Email_Contacto, sucursales.Nombre AS Sucursal, clientes.Fecha_Registro AS Fecha, DATE_FORMAT(clientes.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, (SELECT COUNT(*) FROM clientes LEFT JOIN sucursales ON FK_Sucursal = ID_Sucursal $busqueda) AS Num FROM clientes LEFT JOIN sucursales ON FK_Sucursal = ID_Sucursal $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        for ($i = 0; $i < $numerofilas; $i++) {
          $bModificar = '';
          $bEliminar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Clientes'][3] == '1') {
            $bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarCliente" title="Modificar Cliente" attrID="' . $row[$i]['ID_Cliente'] . '"><i class="fas fa-pencil"></i></button>';
          }
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Clientes'][4] == '1') {
            $bEliminar = '<button type="button" class="btn btn-sm btn-danger bEliminarCliente" title="Eliminar Cliente" attrID="' . $row[$i]['ID_Cliente'] . '"><i class="fas fa-trash"></i></button>';
          }

          $domicilio = $row[$i]['Calle'].' #'.$row[$i]['No_Exterior'];

          if($row[$i]['No_Interior'] != ''){
            $domicilio .= ' int.'.$row[$i]['No_Interior'];
          }

          if($row[$i]['Colonia'] != ''){
            $domicilio .= ', Col. '.$row[$i]['Colonia'];
          }

          if($row[$i]['CP'] != ''){
            $domicilio .= ' C.P. <b class="cp">' . $row[$i]['CP'] . '</b>';
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
          
          $contacto = 'Tel: <b>'.$row[$i]['Telefono'].'</b><br>';
          if ($row[$i]['Segundo_Telefono'] != "") {
            $contacto .= 'Segundo Tel: <b>'.$row[$i]['Segundo_Telefono'].'</b><br>';
          }

          if ($row[$i]['Email'] != "") {
            $contacto .= 'Email: <b class="email">'.$row[$i]['Email'].'</b><br>';
          }

          if ($row[$i]['Contacto'] != '') {
            $contacto .= 'Contacto: <b>'.$row[$i]['Contacto'].'</b><br>';
          }

          if ($row[$i]['Telefono_Contacto'] != '') {
            $contacto .= 'Tel. Contacto: <b>'.$row[$i]['Telefono_Contacto'].'</b><br>';
          }

          if ($row[$i]['Email_Contacto'] != '') {
            $contacto .= 'Email Contacto: <b class="email2">'.$row[$i]['Email_Contacto'].'</b><br>';
          }

          $nombre = '<span>' . $row[$i]['Nombre'].' '.$row[$i]['Primer_Apellido'].' '.$row[$i]['Segundo_Apellido'].'</span><br>';
          if($row[$i]['Tipo'] == 'Moral'){
            $nombre = 'Razón Social: <b class="razon">' . $row[$i]['Razon_Social'].'</b><br>';
          }
         
          if ($row[$i]['RFC'] != '') {
            $nombre .= 'RFC: <b class="rfc">' . $row[$i]['RFC'].'</b><br>';
          }

          if ($row[$i]['Regimen_CFDI'] != '') {
            $nombre .= 'Régimen: <b class="regimen">' . $row[$i]['Regimen_CFDI'].'</b><br>';
          }

          $ubicacion = '';
          if(trim($row[$i]['Latitud']) != '' && trim($row[$i]['Longitud']) != ''){
            $ubicacion = '<br><a class="btn btn-link btn-sm" href="https://www.google.com/maps?q='.trim($row[$i]['Latitud']).','.trim($row[$i]['Longitud']).'" target="_blank"><i class="fa-solid fa-location-dot"></i> Ubicación</a>';
          }

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Cliente'],
            'Fecha' => $row[$i]['Fecha_Registro'],
            'Tipo' => $row[$i]['Tipo'],
            'Nombre' => $nombre,
            'Domicilio' => $domicilio.$ubicacion,
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

    $tipoCliente = $omodelo->link->real_escape_string($tipoCliente);
    $nombre = $omodelo->link->real_escape_string($nombre);
    $primerApellido = $omodelo->link->real_escape_string($primerApellido);
    $segundoApellido = $omodelo->link->real_escape_string($segundoApellido);
    $sexo = $omodelo->link->real_escape_string($sexo);
    $razonSocial = $omodelo->link->real_escape_string($razonSocial);
    $rfc = $omodelo->link->real_escape_string($rfc);
    $regimen = $omodelo->link->real_escape_string($regimen);
    $telefono = $omodelo->link->real_escape_string($telefono);
    $segundoTelefono = $omodelo->link->real_escape_string($segundoTelefono);
    $email = $omodelo->link->real_escape_string($email);
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
    $telContacto = $omodelo->link->real_escape_string($telContacto);
    $emailContacto = $omodelo->link->real_escape_string($emailContacto);
    $latitud = $omodelo->link->real_escape_string($latitud);
    $longitud = $omodelo->link->real_escape_string($longitud);

    $query = "INSERT INTO clientes SET Tipo = '$tipoCliente', Nombre = '$nombre', Primer_Apellido = '$primerApellido', Segundo_Apellido = '$segundoApellido', Sexo = '$sexo', Razon_Social = '$razonSocial', RFC = '$rfc', Regimen_CFDI = '$regimen', Telefono = '$telefono', Segundo_Telefono = '$segundoTelefono', Email = '$email', Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', Colonia = '$colonia', CP = '$cp', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Contacto = '$contacto', Puesto_Contacto = '$puesto', Telefono_Contacto = '$telContacto', Email_Contacto = '$emailContacto', Latitud = '$latitud', Longitud = '$longitud', Fecha_Registro = '$fecha'";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    }else {
      $id = mysqli_insert_id($omodelo->link);

      $direcciones = json_decode($direcciones, true);
      foreach ($direcciones as $direccion) {
        $direccion['calle'] = $omodelo->link->real_escape_string($direccion['calle']);
        $direccion['noExterior'] = $omodelo->link->real_escape_string($direccion['noExterior']);
        $direccion['noInterior'] = $omodelo->link->real_escape_string($direccion['noInterior']);
        $direccion['cp'] = $omodelo->link->real_escape_string($direccion['cp']);
        $direccion['colonia'] = $omodelo->link->real_escape_string($direccion['colonia']);
        $direccion['ciudad'] = $omodelo->link->real_escape_string($direccion['ciudad']);
        $direccion['estado'] = $omodelo->link->real_escape_string($direccion['estado']);
        $direccion['pais'] = $omodelo->link->real_escape_string($direccion['pais']);
        $direccion['detalles'] = $omodelo->link->real_escape_string($direccion['detalles']);
        $direccion['latitud'] = isset($direccion['latitud']) ? $omodelo->link->real_escape_string($direccion['latitud']) : '';
          $direccion['longitud'] = isset($direccion['longitud']) ? $omodelo->link->real_escape_string($direccion['longitud']) : '';

        $query1 = "INSERT INTO direcciones_cliente SET FK_Cliente = '$id', Calle = '$direccion[calle]', No_Exterior = '$direccion[noExterior]', No_Interior = '$direccion[noInterior]', CP = '$direccion[cp]', Colonia = '$direccion[colonia]', Ciudad = '$direccion[ciudad]', Estado = '$direccion[estado]', Pais = '$direccion[pais]', Detalles = '$direccion[detalles]', Latitud = '$direccion[latitud]', Longitud = '$direccion[longitud]'";
        $error1 = $omodelo->_insertar($query1);

        if ($error1 == "si") {
          echo "Error 2: ".mysqli_error($omodelo->link);
        }
      }

      echo 'Correcto';

      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }

  public function _detalles() {
    $omodelo = new m_modelo();
    extract($_POST);
    $arreglo = array();
    $tipo = $omodelo->link->real_escape_string($tipo);

    if ($tipo == 'cliente') {
      $id = $omodelo->link->real_escape_string($id);

      $query = "SELECT ID_Cliente, FK_Sucursal, clientes.Latitud AS Latitud, clientes.Longitud AS Longitud, Tipo, Nombre, Primer_Apellido, Segundo_Apellido, Sexo, RFC, Regimen_CFDI, Razon_Social, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, Detalles, Telefono, Segundo_Telefono, Email, Contacto, Puesto_Contacto, Telefono_Contacto, Email_Contacto FROM clientes WHERE ID_Cliente = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error : ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $direcciones = array();
          $query1 = "SELECT ID_Direccion, Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais, Detalles, Latitud, Longitud FROM direcciones_cliente WHERE FK_Cliente = '$id'";
          $row1 = $omodelo->_consultar($query1);
          $numerofilas1 = $omodelo->numerofilas;

          if ($row1 == 'si') {
            echo 'Error : ' . mysqli_error($omodelo->link);
          } else {
            if ($numerofilas1 > 0) {
              for ($i=0; $i < $numerofilas1; $i++) { 
                $direcciones[] = array(
                  'ID_Direccion' => $row1[$i]['ID_Direccion'], 
                  'Calle' => $row1[$i]['Calle'], 
                  'No_Exterior' => $row1[$i]['No_Exterior'], 
                  'No_Interior' => $row1[$i]['No_Interior'], 
                  'CP' => $row1[$i]['CP'], 
                  'Colonia' => $row1[$i]['Colonia'], 
                  'Ciudad' => $row1[$i]['Ciudad'], 
                  'Estado' => $row1[$i]['Estado'], 
                  'Pais' => $row1[$i]['Pais'], 
                  'Detalles' => $row1[$i]['Detalles'],
                  'Latitud' => $row1[$i]['Latitud'],
                  'Longitud' => $row1[$i]['Longitud']
                );
              }
            }
          }

          $arreglo = array(
              'ID_Cliente' => $row[0]['ID_Cliente'],
              'FK_Sucursal' => $row[0]['FK_Sucursal'],
              'Tipo' => $row[0]['Tipo'],
              'Nombre' => $row[0]['Nombre'],
              'Primer_Apellido' => $row[0]['Primer_Apellido'],
              'Segundo_Apellido' => $row[0]['Segundo_Apellido'],
              'Sexo' => $row[0]['Sexo'],
              'RFC' => $row[0]['RFC'],
              'Regimen_CFDI' => $row[0]['Regimen_CFDI'],
              'Razon_Social' => $row[0]['Razon_Social'],
              'Calle' => $row[0]['Calle'],
              'No_Exterior' => $row[0]['No_Exterior'],
              'No_Interior' => $row[0]['No_Interior'],
              'Colonia' => $row[0]['Colonia'],
              'CP' => $row[0]['CP'],
              'Ciudad' => $row[0]['Ciudad'],
              'Estado' => $row[0]['Estado'],
              'Pais' => $row[0]['Pais'],
              'Detalles' => $row[0]['Detalles'],
              'Email' => $row[0]['Email'],
              'Telefono' => $row[0]['Telefono'],
              'Segundo_Telefono' => $row[0]['Telefono'],
              'Contacto' => $row[0]['Contacto'],
              'Puesto_Contacto' => $row[0]['Puesto_Contacto'],
              'Telefono_Contacto' => $row[0]['Telefono_Contacto'],
              'Email_Contacto' => $row[0]['Email_Contacto'],
              'Latitud' => $row[0]['Latitud'],
              'Longitud' => $row[0]['Longitud'],
              'Direcciones' => $direcciones
          );
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
    $tipoCliente = $omodelo->link->real_escape_string($tipoCliente);
    $nombre = $omodelo->link->real_escape_string($nombre);
    $primerApellido = $omodelo->link->real_escape_string($primerApellido);
    $segundoApellido = $omodelo->link->real_escape_string($segundoApellido);
    $sexo = $omodelo->link->real_escape_string($sexo);
    $razonSocial = $omodelo->link->real_escape_string($razonSocial);
    $rfc = $omodelo->link->real_escape_string($rfc);
    $regimen = $omodelo->link->real_escape_string($regimen);
    $telefono = $omodelo->link->real_escape_string($telefono);
    $segundoTelefono = $omodelo->link->real_escape_string($segundoTelefono);
    $email = $omodelo->link->real_escape_string($email);
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
    $telContacto = $omodelo->link->real_escape_string($telContacto);
    $emailContacto = $omodelo->link->real_escape_string($emailContacto);
    $latitud = $omodelo->link->real_escape_string($latitud);
    $longitud = $omodelo->link->real_escape_string($longitud);

    $query = "UPDATE clientes SET Tipo = '$tipoCliente', Nombre = '$nombre', Primer_Apellido = '$primerApellido', Segundo_Apellido = '$segundoApellido', Sexo = '$sexo', Razon_Social = '$razonSocial', RFC = '$rfc', Regimen_CFDI = '$regimen', Telefono = '$telefono', Segundo_Telefono = '$segundoTelefono', Email = '$email', Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', Colonia = '$colonia', CP = '$cp', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Contacto = '$contacto', Puesto_Contacto = '$puesto', Telefono_Contacto = '$telContacto', Email_Contacto = '$emailContacto', Latitud = '$latitud', Longitud = '$longitud' WHERE ID_Cliente = '$id'";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    }else{
      $query1 = "DELETE FROM direcciones_cliente WHERE FK_Cliente = '$id'";
      $error1 = $omodelo->_insertar($query1);

      if ($error1 == "si") {
        echo "Error 2: ".mysqli_error($omodelo->link);
      }else{
        $direcciones = json_decode($direcciones, true);

        foreach ($direcciones as $direccion) {
          $direccion['calle'] = $omodelo->link->real_escape_string($direccion['calle']);
          $direccion['noExterior'] = $omodelo->link->real_escape_string($direccion['noExterior']);
          $direccion['noInterior'] = $omodelo->link->real_escape_string($direccion['noInterior']);
          $direccion['cp'] = $omodelo->link->real_escape_string($direccion['cp']);
          $direccion['colonia'] = $omodelo->link->real_escape_string($direccion['colonia']);
          $direccion['ciudad'] = $omodelo->link->real_escape_string($direccion['ciudad']);
          $direccion['estado'] = $omodelo->link->real_escape_string($direccion['estado']);
          $direccion['pais'] = $omodelo->link->real_escape_string($direccion['pais']);
          $direccion['detalles'] = $omodelo->link->real_escape_string($direccion['detalles']);
          $direccion['latitud'] = isset($direccion['latitud']) ? $omodelo->link->real_escape_string($direccion['latitud']) : '';
          $direccion['longitud'] = isset($direccion['longitud']) ? $omodelo->link->real_escape_string($direccion['longitud']) : '';

          $query2 = "INSERT INTO direcciones_cliente SET FK_Cliente = '$id', Calle = '$direccion[calle]', No_Exterior = '$direccion[noExterior]', No_Interior = '$direccion[noInterior]', CP = '$direccion[cp]', Colonia = '$direccion[colonia]', Ciudad = '$direccion[ciudad]', Estado = '$direccion[estado]', Pais = '$direccion[pais]', Detalles = '$direccion[detalles]', Latitud = '$direccion[latitud]', Longitud = '$direccion[longitud]'";
          $error2 = $omodelo->_insertar($query2);

          if ($error2 == "si") {
            echo "Error 3: ".mysqli_error($omodelo->link);
          }
        }

        echo "Correcto";

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }
  }

  public function _eliminar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $id = $omodelo->link->real_escape_string($id);

    $query = "DELETE FROM clientes WHERE ID_Cliente = '$id'";
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