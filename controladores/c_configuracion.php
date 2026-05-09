<?php
class configuracion {

  public function _modificar() {
    $modelo = new m_modelo();
    extract($_POST);
    $tipo = $modelo->link->real_escape_string($tipo);

    if ($tipo == 'foto') {
      $fotoPerfil = $_FILES['fotoPerfil'];
      $nombreFoto = $fotoPerfil['name'];
      $blobFoto = $fotoPerfil['tmp_name'];
      $idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];
      $rutaFoto = 'vistas/assets/images/usuarios/' . $idUsuario.'_'.$nombreFoto;
      
      $camFoto = '';
      if(trim($nombreFoto) != ''){
        $camFoto = ", Foto = '".$idUsuario.'_'.$nombreFoto."'";
      }

      $nombrePerfil = $modelo->link->real_escape_string($nombrePerfil);
      $primerApellidoPerfil = $modelo->link->real_escape_string($primerApellidoPerfil);
      $segundoApellidoPerfil = $modelo->link->real_escape_string($segundoApellidoPerfil);
      $fotoAntes = $modelo->link->real_escape_string($fotoAntes);

      $query = "UPDATE usuarios SET Nombre = '$nombrePerfil', Primer_Apellido = '$primerApellidoPerfil', Segundo_Apellido = '$segundoApellidoPerfil' $camFoto WHERE ID_Usuario = $idUsuario";
      $error = $modelo->_insertar($query);
      $status = 0;

      if ($error == 'si') {
        echo "Error: " . mysqli_error($modelo->link);
        $status = 1;
      }
      if ($status == 0) {
        if (trim($nombreFoto) != '') {
          move_uploaded_file($blobFoto, $rutaFoto);
          $_SESSION['user_punto_venta']['Foto'] = $idUsuario.'_'.$nombreFoto;

          if(trim($fotoAntes) != '' && file_exists('vistas/assets/images/usuarios/'.trim($fotoAntes))){
            unlink('vistas/assets/images/usuarios/'.trim($fotoAntes));
          }
        }

        echo 'Correcto';

        $_SESSION['user_punto_venta']['Nombre'] = $nombrePerfil;
        $_SESSION['user_punto_venta']['Primer_Apellido'] = $primerApellidoPerfil;
        $_SESSION['user_punto_venta']['Segundo_Apellido'] = $segundoApellidoPerfil;

        $modelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }else if ($tipo == 'email') {
      $email = $modelo->link->real_escape_string($email);
      $password = $modelo->link->real_escape_string($password);

      $query = "SELECT Contrasena FROM usuarios WHERE ID_Usuario = " . $_SESSION['user_punto_venta']['ID_Usuario'];
      $row = $modelo->_consultar($query);
      if ($row == 'si') {
        echo "Error: " . mysqli_error($modelo->link);
      }
      $pass = $row[0]['Contrasena'];

      if (password_verify($password, $pass)) {
        $query = "UPDATE usuarios SET Correo = '$email' WHERE ID_Usuario = " . $_SESSION['user_punto_venta']['ID_Usuario'];
        $error = $modelo->_insertar($query);
        $status = 0;

        if ($error == 'si') {
          echo "Error: " . mysqli_error($modelo->link);
          $status = 1;
        }
        if ($status == 0) {
          echo 'Correcto';
          $_SESSION['user_punto_venta']['Correo'] = $email;

          $modelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
        }
      } else {
        echo 'Error Contraseña';
      }
    }else if ($tipo == 'password') {
      $passwordActual = $modelo->link->real_escape_string(trim($passwordActual));
      $passwordNueva = $modelo->link->real_escape_string(trim($passwordNueva));

      $query = "SELECT Contrasena FROM usuarios WHERE ID_Usuario = " . $_SESSION['user_punto_venta']['ID_Usuario'];
      $row = $modelo->_consultar($query);
      if ($row == 'si') {
        echo "Error: " . mysqli_error($modelo->link);
      }
      $pass = $row[0]['Contrasena'];

      if (password_verify($passwordActual, $pass)) {
        $passwordNueva = password_hash($passwordNueva, PASSWORD_BCRYPT, ['cost' => 12]);
        $query = "UPDATE usuarios SET Contrasena = '$passwordNueva' WHERE ID_Usuario = " . $_SESSION['user_punto_venta']['ID_Usuario'];
        $error = $modelo->_insertar($query);
        $status = 0;

        if ($error == 'si') {
          echo "Error: " . mysqli_error($modelo->link);
          $status = 1;
        }
        if ($status == 0) {
          echo 'Correcto';
          $modelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
        }
      } else {
        echo 'Error Contraseña';
      }
    }
  }
}
?>