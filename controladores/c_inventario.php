<?php
class inventario {

  public function _consultar() {
    $omodelo = new m_modelo();
    extract($_POST);

    $rutaFotosProductos = 'vistas/assets/images/productos/';
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
        $busqueda .= "CONCAT(Codigo, Descripcion, Costo, Precio, Precio_Mayoreo, Cantidad, Stock_Minimo, Stock_Maximo) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $query = "SELECT ID_Inventario, ID_Producto, Cantidad AS Existencia, Codigo, Descripcion, Costo, Precio, Precio_Mayoreo, Stock_Minimo, Stock_Maximo, FK_Producto, Foto, IFNULL((SELECT SUM(Cantidad) FROM merma WHERE merma.FK_Producto = inventario.FK_Producto), 0) AS Can_Merma, IFNULL((SELECT SUM(Total) FROM merma WHERE merma.FK_Producto = inventario.FK_Producto), 0) AS Cos_Merma, (SELECT COUNT(*) FROM inventario INNER JOIN productos ON FK_Producto = ID_Producto $busqueda) AS Num FROM inventario INNER JOIN productos ON FK_Producto = ID_Producto $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
    }else {
      if ($numerofilas > 0) {
        $totalExistencia = 0; $totalCosto = 0; $totalPrecio = 0; $totalPrecioMa = 0;
        for ($i = 0; $i < $numerofilas; $i++) {
          $bAgregar = ''; 
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Inventario'][2] == '1') {
            $bAgregar = '<button type="button" class="btn btn-sm btn-primary bAgergarInventario" title="Agregar Inventario" attrID="' . $row[$i]['ID_Inventario'] . '"><i class="fas fa-plus"></i></button>';
          }

          $bRestar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Inventario'][3] == '1') {
            $bRestar = '<button type="button" class="btn btn-sm btn-info bRestarInventario" title="Restar Inventario" attrID="' . $row[$i]['ID_Inventario'] . '"><i class="fas fa-minus"></i></button>';
          }

          $bMerma = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Inventario'][4] == '1') {
            $bMerma = '<br><button type="button" class="btn btn-sm btn-warning bMermaInventario" title="Agregar Merma" attrID="' . $row[$i]['FK_Producto'] . '"><i class="fas fa-minus"></i></button>';
          }

          $foto = '<a href="vistas/assets/images/producto-generico.png" data-fancybox="images">
              <div style="background-image: url(' . "'" . 'vistas/assets/images/producto-generico.png' . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
              </div>
          </a>';
          if(trim($row[$i]['Foto']) != '' && file_exists($rutaFotosProductos . trim($row[$i]['Foto']))){
            $foto = '<a href="' . $rutaFotosProductos . trim($row[$i]['Foto']) . '" data-fancybox="images">
                <div style="background-image: url(' . "'" . $rutaFotosProductos . trim($row[$i]['Foto']) . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
                </div>
            </a>';
          }

          $totalExistencia += $row[$i]['Existencia'];
          $totalCosto += $row[$i]['Costo'] * $row[$i]['Existencia'];
          $totalPrecio += $row[$i]['Precio'] * $row[$i]['Existencia'];
          $totalPrecioMa += $row[$i]['Precio_Mayoreo'] * $row[$i]['Existencia'];

          $color = 'green';
          if($row[$i]['Existencia'] <= $row[$i]['Stock_Minimo']){
            $color = 'red';
          }else if($row[$i]['Stock_Maximo'] > 0 && $row[$i]['Existencia'] >= $row[$i]['Stock_Maximo']){
            $color = 'orange';
          }

          $sucursales = 'NA';
          $query1 = "SELECT ID_Detalle_Inventario, Cantidad, Nombre AS Sucursal FROM detalles_inventario INNER JOIN sucursales ON FK_Sucursal = ID_Sucursal WHERE FK_Inventario = '".$row[$i]['ID_Inventario']."'";
          $row1 = $omodelo->_consultar($query1);
          $numerofilas1 = $omodelo->numerofilas;

          if ($row1 == 'si') {
            echo "Error 2: " . mysqli_error($omodelo->link);
          }else {
            if ($numerofilas1 > 0) {
              $sucursales = '';
              for ($x=0; $x < $numerofilas1; $x++) { 
                $sucursales .= '<b>'.$row1[$x]['Sucursal'].':</b> <span class="cantidad">'.$row1[$x]['Cantidad'].'</span><br>';
              }
            }
          }

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Inventario'],
            'Codigo' => $foto . $row[$i]['Codigo'],
            'Descripcion' => $row[$i]['Descripcion'],
            'Existencia' => '<span class="cantidad" style="font-size: 18px; color: '.$color.';">' . $row[$i]['Existencia'] . '</span><br><b>Stock Mínimo: </b><span class="cantidad">' . $row[$i]['Stock_Minimo'] . '</span><br><b>Stock Máximo: </b><span class="cantidad">' . $row[$i]['Stock_Maximo'] . '</span>',
            'Sucursales' => $sucursales,
            'Costo' => '<span class="dinero">' . $row[$i]['Costo'] . '</span>',
            'Precio' => '<span class="dinero">' . $row[$i]['Precio'] . '</span>',
            'Precio_Mayoreo' => '<span class="dinero">' . $row[$i]['Precio_Mayoreo'] . '</span>',
            'Totales' => '
              <p class="mb-0 fw-bold">Total Costo: <span class="dinero fw-normal">' . ($row[$i]['Costo'] * $row[$i]['Existencia']) . '</span></p>
              <p class="mb-0 fw-bold">Total Precio: <span class="dinero fw-normal">' . ($row[$i]['Precio'] * $row[$i]['Existencia']) . '</span></p>
              <p class="fw-bold">Total Precio Mayoreo: <span class="dinero fw-normal">' . ($row[$i]['Precio_Mayoreo'] * $row[$i]['Existencia']) . '</span></p>
              <p class="mb-0 fw-bold">Diferencia Precio: <span class="dinero fw-normal">' . (($row[$i]['Precio'] * $row[$i]['Existencia']) - ($row[$i]['Costo'] * $row[$i]['Existencia'])) . '</span></p>
              <p class="mb-0 fw-bold">Diferencia Precio Mayoreo: <span class="dinero fw-normal">' . (($row[$i]['Precio_Mayoreo'] * $row[$i]['Existencia']) - ($row[$i]['Costo'] * $row[$i]['Existencia'])) . '</span></p>',
            'Merma' => '<b>Cantidad:</b> <span class="cantidad">'.$row[$i]['Can_Merma'].'</span><br><b>Monto:</b> <span class="dinero">'.$row[$i]['Cos_Merma'].'</span>'.$bMerma,
            'Acciones' => $bAgregar.' '.$bRestar
          );
        }

        $arreglo['totales'] = array(
          'NumRows' => $row[0]['Num'],
          'Existencia' => '<span class="cantidad">'.$totalExistencia.'</span>',
          'Totales' => '
            <p class="mb-0 fw-bold">Total Costo: <span class="dinero fw-normal">'.$totalCosto.'</span></p>
            <p class="mb-0 fw-bold">Total Precio: <span class="dinero fw-normal">' .$totalPrecio.'</span></p>
            <p class="fw-bold">Total Precio Mayoreo: <span class="dinero fw-normal">'.$totalPrecioMa.'</span></p>
            <p class="mb-0 fw-bold">Diferencia Precio: <span class="dinero fw-normal">'.($totalPrecio - $totalCosto).'</span></p>
            <p class="mb-0 fw-bold">Diferencia Precio Mayoreo: <span class="dinero fw-normal">'.($totalPrecioMa - $totalCosto).'</span></p>'
        );
      }
    }
    
    echo json_encode($arreglo);
  }

  public function _modificar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $id = $omodelo->link->real_escape_string($id);
    $cantidad = $omodelo->link->real_escape_string($cantidad);
    $sucursal = $omodelo->link->real_escape_string($sucursal);
    $aplicar = $omodelo->link->real_escape_string($aplicar);

    if(isset($sucursal) && trim($sucursal) != '' && trim($sucursal) != 'undefined'){
      $query1 = "SELECT COUNT(*) AS Num FROM detalles_inventario WHERE FK_Inventario = '$id' AND FK_Sucursal = $sucursal";
      $row = $omodelo->_consultar($query1);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      }else {
        if ($numerofilas > 0 && $row[0]['Num'] > 0) {
          $query = "UPDATE detalles_inventario SET Cantidad = Cantidad + $cantidad WHERE FK_Inventario = '$id' AND FK_Sucursal = $sucursal";
        }else{
          $query = "INSERT INTO detalles_inventario SET Cantidad = '$cantidad', FK_Inventario = '$id', FK_Sucursal = $sucursal";
        }
      }

      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 2: " . mysqli_error($omodelo->link);
      }else{
        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }

    //if(!isset($sucursal) || trim($sucursal) == '' || $aplicar == 'true'){
      $query = "UPDATE inventario SET Cantidad = Cantidad + $cantidad WHERE ID_Inventario = '$id'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 3: " . mysqli_error($omodelo->link);
      }else{
        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    //}

    echo 'Correcto';
  }

  public function _eliminar() {
    $omodelo = new m_modelo();
    extract($_POST);
    $id = $omodelo->link->real_escape_string($id);
    $cantidad = $omodelo->link->real_escape_string($cantidad);
    $sucursal = $omodelo->link->real_escape_string($sucursal);
    $aplicar = $omodelo->link->real_escape_string($aplicar);

    if(isset($sucursal) && trim($sucursal) != ''){
      $query1 = "SELECT COUNT(*) AS Num FROM detalles_inventario WHERE FK_Inventario = '$id' AND FK_Sucursal = $sucursal";
      $row = $omodelo->_consultar($query1);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      }else {
        if ($numerofilas > 0 && $row[0]['Num'] > 0) {
          $query = "UPDATE detalles_inventario SET Cantidad = Cantidad - $cantidad WHERE FK_Inventario = '$id' AND FK_Sucursal = $sucursal";
        }else{
          $query = "INSERT INTO detalles_inventario SET Cantidad = '$cantidad', FK_Inventario = '$id', FK_Sucursal = $sucursal";
        }
      }

      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 2: " . mysqli_error($omodelo->link);
      }else{
        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }

    if(!isset($sucursal) || trim($sucursal) == '' || $aplicar == 'true'){
      $query = "UPDATE inventario SET Cantidad = Cantidad - $cantidad WHERE ID_Inventario = '$id'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 3: " . mysqli_error($omodelo->link);
      }else{
        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }

    echo 'Correcto';
  }

  public function _detalles(){
    $omodelo = new m_modelo();
    extract($_POST);
    $tipo = $omodelo->link->real_escape_string($tipo);

    if($tipo == 'merma'){
      $producto = $omodelo->link->real_escape_string($producto);
      $rutaFotosMerma = 'vistas/assets/images/merma/';
      
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
          $busqueda .= "CONCAT(DATE_FORMAT(merma.Fecha_Registro, '%d-%m-%Y %r'), merma.Descripcion, Cantidad, merma.Costo, Total, DATE_FORMAT(Fecha_Merma, '%d-%m-%Y'), IFNULL(sucursales.Nombre, 'NA'), IFNULL(usuarios.Nombre, 'NA')) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $query = "SELECT ID_Merma, FK_Producto, Afecto_Inventario, IFNULL(usuarios.Nombre, 'NA') AS Usuario, merma.Descripcion AS Descripcion, Cantidad, merma.Costo AS Costo, Total, Fecha_Merma, DATE_FORMAT(Fecha_Merma, '%d-%m-%Y') AS FechaMerma, merma.Fecha_Registro AS Fecha, DATE_FORMAT(merma.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, merma.Foto AS Foto, IFNULL(sucursales.Nombre, 'NA') AS Sucursal, FK_Sucursal, (SELECT COUNT(*) FROM merma INNER JOIN productos ON FK_Producto = ID_Producto LEFT JOIN sucursales ON FK_Sucursal = ID_Sucursal LEFT JOIN usuarios ON ID_usuario = FK_Usuario WHERE merma.FK_Producto = '$producto' $busqueda) AS Num FROM merma INNER JOIN productos ON FK_Producto = ID_Producto LEFT JOIN sucursales ON FK_Sucursal = ID_Sucursal LEFT JOIN usuarios ON ID_usuario = FK_Usuario WHERE merma.FK_Producto = '$producto' $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      }else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $foto = '';
            if(trim($row[$i]['Foto']) != '' && file_exists($rutaFotosMerma . trim($row[$i]['Foto']))){
              $foto = '<a href="' . $rutaFotosMerma . trim($row[$i]['Foto']) . '" data-fancybox="images">
                  <div style="background-image: url(' . "'" . $rutaFotosMerma . trim($row[$i]['Foto']) . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
                  </div>
              </a>';
            }

            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Merma'],
              'Descripcion' => $foto.$row[$i]['Descripcion'],
              'Cantidad' => '<span class="cantidad">'.$row[$i]['Cantidad'].'</span>',
              'Costo' => '<span class="dinero">'.$row[$i]['Costo'].'</span>',
              'Total' => '<span class="dinero">'.$row[$i]['Total'].'</span>',
              'Sucursal' => $row[$i]['Sucursal'], 
              'Fecha_Merma' => $row[$i]['FechaMerma'],
              'Usuario' => $row[$i]['Usuario'],
              'Afecto' => $row[$i]['Afecto_Inventario'],
              'Acciones' => '<button type="button" class="btn btn-warning btn-sm bModificarMerma" attrID="'.$row[$i]['ID_Merma'].'"><i class="fas fa-pencil"></i></button> <button type="button" class="btn btn-danger btn-sm bEliminarMerma" attrID="'.$row[$i]['ID_Merma'].'"><i class="fas fa-trash"></i></button>'
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }
      
      echo json_encode($arreglo);
    }else if($tipo == 'detallesMerma'){
      $id = $omodelo->link->real_escape_string($id);
      $arreglo = array();

      $query = "SELECT ID_Merma, FK_Producto, Descripcion, Cantidad, Costo, Total, Fecha_Merma, Fecha_Registro, Foto, FK_Sucursal, FK_Usuario, Afecto_Inventario FROM merma WHERE ID_Merma = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      }else {
        if ($numerofilas > 0) {
          $arreglo = array(
            'ID_Merma' => $row[0]['ID_Merma'], 
            'FK_Producto' => $row[0]['FK_Producto'], 
            'Descripcion' => $row[0]['Descripcion'], 
            'Cantidad' => $row[0]['Cantidad'], 
            'Costo' => $row[0]['Costo'], 
            'Total' => $row[0]['Total'], 
            'Fecha_Merma' => $row[0]['Fecha_Merma'], 
            'Fecha_Registro' => $row[0]['Fecha_Registro'], 
            'Foto' => $row[0]['Foto'], 
            'FK_Sucursal' => $row[0]['FK_Sucursal'], 
            'FK_Usuario' => $row[0]['FK_Usuario'],
            'Afecto_Inventario' => $row[0]['Afecto_Inventario']
          );
        }
      }

      echo json_encode($arreglo);
    }else if($tipo == 'insertarMerma'){
      $fechaMerma = $omodelo->link->real_escape_string($fechaMerma);
      $descriMerma = $omodelo->link->real_escape_string(trim($descriMerma));
      $cantidadMerma = $omodelo->link->real_escape_string($cantidadMerma);
      $sucursalMerma = $omodelo->link->real_escape_string($sucursalMerma);
      $afecta = $omodelo->link->real_escape_string($afecta);
      $producto = $omodelo->link->real_escape_string($producto);
      $idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];

      $cantidadMerma = str_replace(',', '', $cantidadMerma);
      $query = "INSERT INTO merma SET FK_Producto = '$producto', Descripcion = '$descriMerma', Cantidad = '$cantidadMerma', Costo = IFNULL((SELECT Costo_Promedio FROM productos WHERE ID_Producto = '$producto'), 0), Total = ($cantidadMerma * IFNULL((SELECT Costo_Promedio FROM productos WHERE ID_Producto = '$producto'), 0)), Fecha_Merma = '$fechaMerma', FK_Sucursal = '$sucursalMerma', FK_Usuario = '$idUsuario', Afecto_Inventario = '$afecta', Fecha_Registro = NOW()";
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
        $carpeta = 'vistas/assets/images/merma/';
        if ($_FILES['fotoMerma']['size'] > 0 && $_FILES['fotoMerma']['error'] == 0) {
          $file = $_FILES['fotoMerma'];
          $nombreImg = $file['name'];
          $tipoImg = $file['type'];
          $rutaProvisional = $file['tmp_name'];
          $sizeImg = $file['size'];

          if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != '') {
            echo 'Error 2 formato';
            $status = 1;
          } else if ($sizeImg > (1024 * 1024 * 10)) {
            echo 'Error 3 peso';
            $status = 1;
          } else {
            $ruta = $carpeta . $id . '_' . $nombreImg;
          }

          if ($status == 0 && $nombreImg != '') {
            $query1 = "UPDATE merma SET Foto = '" . $id . '_' . $nombreImg . "'  WHERE ID_Merma = '$id'";
            $error1 = $omodelo->_insertar($query1);
            
            if ($error1 == 'si') {
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
    }else if($tipo == 'modificarMerma'){
      $id = $omodelo->link->real_escape_string($id);
      $fechaMerma = $omodelo->link->real_escape_string($fechaMerma);
      $descriMerma = $omodelo->link->real_escape_string(trim($descriMerma));
      $cantidadMerma = $omodelo->link->real_escape_string($cantidadMerma);
      $sucursalMerma = $omodelo->link->real_escape_string($sucursalMerma);
      $afecta = $omodelo->link->real_escape_string($afecta);
      $idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];

      $cantidadMerma = str_replace(',', '', $cantidadMerma);
      $query = "UPDATE merma SET Descripcion = '$descriMerma', Cantidad = '$cantidadMerma', Fecha_Merma = '$fechaMerma', FK_Sucursal = '$sucursalMerma', FK_Usuario = '$idUsuario', Afecto_Inventario = '$afecta', Total = ($cantidadMerma * Costo) WHERE ID_Merma = '$id'";
      $error = $omodelo->_insertar($query);
      $status = 0;

      if ($error == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
        $status = 1;
      } else {
        $nombreImg = '';
        $ruta = '';
        $rutaProvisional = '';
        $carpeta = 'vistas/assets/images/merma/';
        if ($_FILES['fotoMerma']['size'] > 0 && $_FILES['fotoMerma']['error'] == 0) {
          $file = $_FILES['fotoMerma'];
          $nombreImg = $file['name'];
          $tipoImg = $file['type'];
          $rutaProvisional = $file['tmp_name'];
          $sizeImg = $file['size'];

          if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != '') {
            echo 'Error 2 formato';
            $status = 1;
          } else if ($sizeImg > (1024 * 1024 * 10)) {
            echo 'Error 3 peso';
            $status = 1;
          } else {
            $ruta = $carpeta . $id . '_' . $nombreImg;
          }

          if ($status == 0 && $nombreImg != '') {
            $query1 = "SELECT Foto FROM merma WHERE ID_Merma = '$id'";
            $row = $omodelo->_consultar($query1);
            $numerofilas = $omodelo->numerofilas;

            if ($row == 'si') {
              echo "Error 1: " . mysqli_error($omodelo->link);
            }else {
              if ($numerofilas > 0) {
                if (trim($row[0]['Foto']) != '' && file_exists($carpeta . $row[0]['Foto'])) {
                  unlink($carpeta . $row[0]['Foto']);
                }
              }
            }

            $query1 = "UPDATE merma SET Foto = '" . $id . '_' . $nombreImg . "'  WHERE ID_Merma = '$id'";
            $error1 = $omodelo->_insertar($query1);
            
            if ($error1 == 'si') {
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
    }else if($tipo == 'eliminarMerma'){
      $id = $omodelo->link->real_escape_string($id);
      $carpeta = 'vistas/assets/images/merma/';

      $query = "SELECT Foto FROM merma WHERE ID_Merma = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      }else {
        if ($numerofilas > 0) {
          if (trim($row[0]['Foto']) != '' && file_exists($carpeta . $row[0]['Foto'])) {
            unlink($carpeta . $row[0]['Foto']);
          }
        }
      }

      $query = "DELETE FROM merma WHERE ID_Merma = '$id'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 2: " . mysqli_error($omodelo->link);
      } else {
        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }
  }
}
?>