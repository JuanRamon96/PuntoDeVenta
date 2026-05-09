<?php
class usuarios {
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
        $busqueda .= "CONCAT(DATE_FORMAT(Fecha_Alta, '%d-%m-%Y %r'), Nombre, Primer_Apellido, Segundo_Apellido, Correo, IF(Tipo_Usuario = 1, 'Administrador', IF(Tipo_Usuario = 0, 'Empleado', 'Tablet'))) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];
    $query = "SELECT ID_Usuario, Puesto, CONCAT(Nombre, ' ',Primer_Apellido, ' ' , Segundo_Apellido) AS Nombre, Correo, Tipo_Usuario, Permisos, Estatus, Foto, Fecha_Alta AS Fecha, DATE_FORMAT(Fecha_Alta, '%d-%m-%Y %r') AS Fecha_Alta, (SELECT COUNT(*) FROM usuarios WHERE ID_Usuario != '$idUsuario' $busqueda) AS Num FROM usuarios WHERE ID_Usuario != '$idUsuario' $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    }else{
      if ($numerofilas > 0) {
        for ($i = 0; $i < $numerofilas; $i++) {
          $bModificar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Usuarios'][3] == '1') {
            $bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarUsuario" title="Modificar Usuario" attrID="' . $row[$i]['ID_Usuario'] . '"><i class="fas fa-pencil"></i></button>';
          }

          $bEliminar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Usuarios'][4] == '1') {
            $bEliminar = '<button class="btn btn-danger btn-sm bEliminarUsuario" foto="' . $row[$i]['Foto'] . '" type="button" title="Eliminar Usuario" attrID="' . $row[$i]['ID_Usuario'] . '"><i class="fas fa-trash"></i></button>';
          }

          $foto = '<a href="vistas/assets/images/default.jpg" data-fancybox="images">
            <div style="background-image: url(' . "'" . 'vistas/assets/images/default.jpg' . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
            </div>
          </a>';

          if(trim($row[$i]['Foto']) != '' && file_exists('vistas/assets/images/usuarios/'.trim($row[$i]['Foto']))){
            $foto = '<a href="vistas/assets/images/usuarios/'.trim($row[$i]['Foto']).'" data-fancybox="images">
              <div style="background-image: url(' . "'" . 'vistas/assets/images/usuarios/'.trim($row[$i]['Foto']) . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
              </div>
            </a>';
          }

          $tipoUsuario = 'Administrador';
          if($row[$i]['Tipo_Usuario'] == '0'){
            $tipoUsuario = 'Empleado';
          }else if($row[$i]['Tipo_Usuario'] == '2'){
            $tipoUsuario = 'Tablet';
          }

          $arreglo['data'][$i] = array(
            'Fecha_Alta' => $row[$i]['Fecha_Alta'],
            'ID_Usuario' => $row[$i]['ID_Usuario'],
            'Nombre' => $foto . $row[$i]['Nombre'] . '<p> <span class="fw-bold">Puesto: </span>' . $row[$i]['Puesto'] . '</p>',
            'Correo' => $row[$i]['Correo'],
            'Estatus' => $row[$i]['Estatus'] == '0' ? '<span class="badge rounded-pill bg-success">Activo</span>': '<span class="badge rounded-pill bg-danger">Inactivo</span>',
            'Tipo_Usuario' => $tipoUsuario,
            'Permisos' => $row[$i]['Tipo_Usuario'] == '0' ? '<button type="button" class="btn btn-sm btn-info bPermisos" title="Permisos" cadena="' . $row[$i]['Permisos'] . '" attrID="' . $row[$i]['ID_Usuario'] . '"><i class="fas fa-bars"></i></button>' : '',
            'Acciones' => $bModificar .' '. $bEliminar
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

    $nombre = $omodelo->link->real_escape_string(trim($nombre));
    $primerApellido = $omodelo->link->real_escape_string(trim($primerApellido));
    $segundoApellido = $omodelo->link->real_escape_string(trim($segundoApellido));
    $puesto = $omodelo->link->real_escape_string(trim($puesto));
    $correo = $omodelo->link->real_escape_string(trim($correo));
    $estatus = $omodelo->link->real_escape_string($estatus);
    $tipo = $omodelo->link->real_escape_string($tipo);
    $password = password_hash($omodelo->link->real_escape_string(trim($password)), PASSWORD_BCRYPT, ['cost' => 12]);

    $query = "INSERT INTO usuarios SET Nombre = '$nombre', Primer_Apellido = '$primerApellido', Segundo_Apellido = '$segundoApellido', Puesto = '$puesto', Correo = '$correo', Contrasena = '$password', Estatus = '$estatus', Tipo_Usuario = '$tipo', Fecha_Alta = '$fecha', Activo = 1";
    $error = $omodelo->_insertar($query);
    $status = 0;

    if ($error == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
      $status = 1;
    }else{
      $id = $omodelo->link->insert_id;
    
      $rutaFotosUsuarios = 'vistas/assets/images/usuarios/'; $nombreImg = '';$ruta = '';$rutaProvisional = '';
      if ($_FILES['fotoUsuario']['size'] > 0 && $_FILES['fotoUsuario']['error'] == 0) {
        $file = $_FILES['fotoUsuario'];
        $nombreImg = $file['name'];
        $tipoImg = $file['type'];
        $rutaProvisional = $file['tmp_name'];
        $sizeImg = $file['size'];

        if ($tipoImg != 'image/jpg' && $tipoImg != 'image/jpeg' && $tipoImg != 'image/png' && $tipoImg != 'image/gif') {
          echo 'Error 2 formato';
          $status = 1;
        }else if ($sizeImg  > (1024 * 1024 * 10)) {
          echo 'Error 3 tamaño';
          $status = 1;
        }else{
          $ruta = $rutaFotosUsuarios . $nombreImg;
        }

        if ($status == 0 && $nombreImg != '') {
          $query = "UPDATE usuarios SET Foto = '$nombreImg' WHERE ID_Usuario = $id";
          $error = $omodelo->_insertar($query);
          
          if ($error == 'si') {
            echo "Error 4: " . mysqli_error($omodelo->link);
            $status = 1;
          }else{
            move_uploaded_file($rutaProvisional, $ruta);
          }
        }
      }

      if ($status == 0) {
        echo 'Correcto';
        
        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }
  }

  public function _detalles() {
    $omodelo = new m_modelo();
    extract($_POST);
    $arreglo = array();
    $tipo = $omodelo->link->real_escape_string($tipo);

    if ($tipo == 'permisos') {
      $id = $omodelo->link->real_escape_string($id);
      $cadena = $omodelo->link->real_escape_string($cadena);

      $query = "UPDATE usuarios SET Permisos = '$cadena' WHERE ID_Usuario = $id";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      }else{
        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }else if($tipo == 'usuarios') {
      $id = $omodelo->link->real_escape_string($id);

      $query = "SELECT * FROM usuarios WHERE ID_Usuario = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      }else{
        if ($numerofilas > 0) {
          $arreglo = array(
            'ID_Usuario' => $row[0]['ID_Usuario'],
            'Nombre' => $row[0]['Nombre'],
            'Primer_Apellido' => $row[0]['Primer_Apellido'],
            'Segundo_Apellido' => $row[0]['Segundo_Apellido'],
            'Puesto' => $row[0]['Puesto'],
            'Correo' => $row[0]['Correo'],
            'Estatus' => $row[0]['Estatus'],
            'Tipo_Usuario' => $row[0]['Tipo_Usuario'],
            'Fecha_Alta' => $row[0]['Fecha_Alta'],
            'Foto' => $row[0]['Foto']
          );
        }
      }

      echo json_encode($arreglo);
    }
  }

  public function _modificar() {
    $omodelo = new m_modelo();
    extract($_POST);

    $id = $omodelo->link->real_escape_string($id);
    $nombre = $omodelo->link->real_escape_string(trim($nombre));
    $primerApellido = $omodelo->link->real_escape_string(trim($primerApellido));
    $segundoApellido = $omodelo->link->real_escape_string(trim($segundoApellido));
    $puesto = $omodelo->link->real_escape_string(trim($puesto));
    $correo = $omodelo->link->real_escape_string(trim($correo));
    $estatus = $omodelo->link->real_escape_string($estatus);
    $tipo = $omodelo->link->real_escape_string($tipo);
    $password = $omodelo->link->real_escape_string(trim($password ?? ''));

    $pass = '';
    if ($password != '') {
      $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
      $pass = ", Contrasena = '$password'";
    }

    $query = "UPDATE usuarios SET Nombre = '$nombre', Primer_Apellido = '$primerApellido', Segundo_Apellido = '$segundoApellido', Puesto = '$puesto', Correo = '$correo', Estatus = '$estatus', Tipo_Usuario = '$tipo' $pass WHERE ID_Usuario = $id";
    $error = $omodelo->_insertar($query);
    $status = 0;

    if($error == 'si'){
      echo "Error 1: " . mysqli_error($omodelo->link);
      $status = 1;
    }else{
      $rutaFotosUsuarios = 'vistas/assets/images/usuarios/'; $nombreImg = ''; $ruta = ''; $rutaProvisional = '';
      if ($_FILES['fotoUsuario']['size'] > 0 && $_FILES['fotoUsuario']['error'] == 0) {
        $file = $_FILES['fotoUsuario'];
        $nombreImg = $file['name'];
        $tipoImg = $file['type'];
        $rutaProvisional = $file['tmp_name'];
        $sizeImg = $file['size'];

        if ($tipoImg != 'image/jpg' && $tipoImg != 'image/jpeg' && $tipoImg != 'image/png' && $tipoImg != 'image/gif') {
          echo 'Error 2 formato';
          $status = 1;
        }else if ($sizeImg > (1024 * 1024 * 10)) {
          echo 'Error 3 tamaño';
          $status = 1;
        }else{
          $ruta = $rutaFotosUsuarios . $nombreImg;
        }
        
        if ($status == 0 && $nombreImg != '') {
          $query = "UPDATE usuarios SET Foto = '$nombreImg' WHERE ID_Usuario = $id";
          $error = $omodelo->_insertar($query);
          
          if ($error == 'si') {
            echo "Error 4: " . mysqli_error($omodelo->link);
            $status = 1;
          }else{
            if (trim($foto) != '' && file_exists('vistas/assets/images/usuarios/' . $foto)) {
              unlink('vistas/assets/images/usuarios/' . $foto);
            }

            move_uploaded_file($rutaProvisional, $ruta);
          }
        }
      }

      if ($status == 0) {
        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }
  }

  public function _eliminar() {
    $omodelo = new m_modelo();
    extract($_POST);

    $id = $omodelo->link->real_escape_string($id);
    $foto = $omodelo->link->real_escape_string($foto);

    $query = "DELETE FROM usuarios WHERE ID_Usuario = $id";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    }else{
      if (trim($foto) != '' && file_exists('vistas/assets/images/usuarios/' . $foto)) {
        unlink('vistas/assets/images/usuarios/' . $foto);
      }

      echo 'Correcto';
      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }
}
?>