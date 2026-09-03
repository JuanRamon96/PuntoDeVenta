<?php
class gastos {

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
        $busqueda .= "CONCAT(DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), DATE_FORMAT(Fecha_Gasto, '%d-%m-%Y'), Monto, Descripcion, IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = FK_Sucursal), 'NA')) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $query = "SELECT ID_Gasto, FK_Sucursal, IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = FK_Sucursal), 'NA') AS Sucursal, Monto, Descripcion, Comprobante, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = gastos.FK_usuario), '') AS Usuario, Fecha_Gasto, DATE_FORMAT(Fecha_Gasto, '%d-%m-%Y') AS FechaGasto, Fecha_Registro AS Fecha, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, (SELECT SUM(Monto) FROM gastos $busqueda) AS Total, (SELECT COUNT(*) FROM gastos $busqueda) AS Num FROM gastos $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        for ($i = 0; $i < $numerofilas; $i++) {
          $comprobante = '';
          if ($row[$i]['Comprobante'] != '' && file_exists('vistas/assets/images/gastos/'.$row[$i]['Comprobante'])) {
            $comprobante = '<a style="font-size: 20px;" href="vistas/assets/images/gastos/' . $row[$i]['Comprobante'] . '" data-fancybox="file"><i class="fas fa-file"></i></a>';
          }

          $bModificar = '';
          $bEliminar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Gastos'][3] == '1') {
            $bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarGasto" title="Modificar" attrID="'.$row[$i]['ID_Gasto'].'" comprobante="'.$row[$i]['Comprobante'].'"><i class="fas fa-pencil"></i></button>';
          }
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Gastos'][4] == '1') {
            $bEliminar = '<button type="button" class="btn btn-sm btn-danger bEliminarGasto" title="Eliminar" attrID="'.$row[$i]['ID_Gasto'].'" comprobante="'.$row[$i]['Comprobante'].'"><i class="fas fa-trash"></i></button>';
          }

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Gasto'],
            'Fecha' => $row[$i]['Fecha_Registro'],
            'Monto' => '<span class="dinero">'.$row[$i]['Monto'].'<span>',
            'Descripcion' => $row[$i]['Descripcion'],
            'Usuario' => $row[$i]['Usuario'],
            'Comprobante' => $comprobante,
            'Fecha_Gasto' => $row[$i]['FechaGasto'],
            'Sucursal' => $row[$i]['Sucursal'],
            'Acciones' => $bModificar.' '.$bEliminar
          );
        }
        
        $arreglo['totales'] = array('NumRows' => $row[0]['Num'], 'Total' => '<span class="dinero">'.$row[0]['Total'].'</span>');
      }
    }

    echo json_encode($arreglo);
  }

  public function _insertar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $fecha = date('Y-m-d H:i:s');

    $fechaGasto = $omodelo->link->real_escape_string(trim($fechaGasto));
    $montoGasto = $omodelo->link->real_escape_string(trim($montoGasto));
    $descriGasto = $omodelo->link->real_escape_string($descriGasto);
    $sucursalGasto = isset($sucursalGasto) ? $omodelo->link->real_escape_string($sucursalGasto) : '';
    $idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];

    $montoGasto = str_replace(',', '', $montoGasto);
    $query = "INSERT INTO gastos SET Monto = '$montoGasto', Descripcion = '$descriGasto', Fecha_Gasto = '$fechaGasto', FK_Usuario = '$idUsuario', FK_Sucursal = '$sucursalGasto', Fecha_Registro = NOW()";
    $error = $omodelo->_insertar($query);
    $status = 0;

    if ($error == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
      $status = 1;
    } else {
      $id = $omodelo->link->insert_id;

      $nombreImg = '';
      $ruta = '';
      $rutaProvisional = '';
      $carpeta = 'vistas/assets/images/gastos/';
      if ($_FILES['comprobanteGasto']['size'] > 0 && $_FILES['comprobanteGasto']['error'] == 0) {
        $file = $_FILES['comprobanteGasto'];
        $nombreImg = $file['name'];
        $tipoImg = $file['type'];
        $rutaProvisional = $file['tmp_name'];
        $sizeImg = $file['size'];
        $bd = $_SESSION['user_punto_bd'];

        if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != 'image/svg' && $tipoImg != 'application/pdf' && $tipoImg != '') {
          echo 'Error 2 formato';
          $status = 1;
        } else if ($sizeImg > (1024 * 1024 * 10)) {
          echo 'Error 3 peso';
          $status = 1;
        } else {
          $ruta = $carpeta . $bd . '_' . $id . '_' . $nombreImg;
        }

        if ($status == 0 && $nombreImg != '') {
          $query = "UPDATE gastos SET Comprobante = '" . $bd . '_' . $id . '_' . $nombreImg . "'  WHERE ID_Gasto = '$id'";
          $error = $omodelo->_insertar($query);
          
          if ($error == 'si') {
            echo "Error 4: " . mysqli_error($omodelo->link);
          } else {
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
    $tipo = $omodelo->link->real_escape_string($tipo);

    if ($tipo == 'gasto') {
      $arreglo = array();
      $id = $omodelo->link->real_escape_string($id);

      $query = "SELECT ID_Gasto, Monto, Descripcion, Fecha_Gasto, Fecha_Registro, FK_Sucursal FROM gastos WHERE ID_Gasto = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error : ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $arreglo = array(
            'ID_Gasto' => $row[0]['ID_Gasto'],
            'Monto' => $row[0]['Monto'],
            'Descripcion' => $row[0]['Descripcion'],
            'Fecha_Gasto' => $row[0]['Fecha_Gasto'],
            'Fecha_Registro' => $row[0]['Fecha_Registro'],
            'FK_Sucursal' => $row[0]['FK_Sucursal']
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
    $fechaGasto = $omodelo->link->real_escape_string(trim($fechaGasto));
    $montoGasto = $omodelo->link->real_escape_string(trim($montoGasto));
    $descriGasto = $omodelo->link->real_escape_string($descriGasto);
    $comprobante = $omodelo->link->real_escape_string($comprobante);
    $sucursalGasto = $omodelo->link->real_escape_string($sucursalGasto);
    
    $montoGasto = str_replace(',', '', $montoGasto);
    $query = "UPDATE gastos SET Monto = '$montoGasto', Descripcion = '$descriGasto', Fecha_Gasto = '$fechaGasto', FK_Sucursal = '$sucursalGasto' WHERE ID_Gasto = '$id'";
    $error = $omodelo->_insertar($query);
    $status = 0;

    if ($error == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
      $status = 1;
    } else {
      $nombreImg = '';
      $ruta = '';
      $rutaProvisional = '';
      $carpeta = 'vistas/assets/images/gastos/';
      if ($_FILES['comprobanteGasto']['size'] > 0 && $_FILES['comprobanteGasto']['error'] == 0) {
        $file = $_FILES['comprobanteGasto'];
        $nombreImg = $file['name'];
        $tipoImg = $file['type'];
        $rutaProvisional = $file['tmp_name'];
        $sizeImg = $file['size'];
        $bd = $_SESSION['user_punto_bd'];

        if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != 'image/svg' && $tipoImg != 'application/pdf' && $tipoImg != '') {
          echo 'Error 2 formato ' . $tipoImg;
          $status = 1;
        } else if ($sizeImg > (1024 * 1024 * 10)) {
          echo 'Error 3 peso';
          $status = 1;
        } else {
          $ruta = $carpeta . $bd . '_' . $id . '_' . $nombreImg;
        }

        if ($status == 0 && $nombreImg != '') {
          $query = "UPDATE gastos SET Comprobante = '" . $bd . '_' . $id . '_' . $nombreImg . "'  WHERE ID_Gasto = '$id'";
          $error = $omodelo->_insertar($query);
          if ($error == 'si') {
            echo "Error 4: " . mysqli_error($omodelo->link);
          } else {
            if (trim($comprobante) != '' && file_exists($carpeta . $comprobante)) {
              unlink($carpeta . $comprobante);
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
    $fecha = date('Y-m-d H:i:s');

    $id = $omodelo->link->real_escape_string($id);
    $comprobante = $omodelo->link->real_escape_string($comprobante);
    $carpeta = 'vistas/assets/images/gastos/';

    $query = "DELETE FROM gastos WHERE ID_Gasto = '$id'";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
    } else {
      if (trim($comprobante) != '' && file_exists($carpeta . $comprobante)) {
        unlink($carpeta . $comprobante);
      }

      echo 'Correcto';

      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }
}
?>