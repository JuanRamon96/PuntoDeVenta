<?php
class inventario {

  public function _consultar() {
    $omodelo = new m_modelo();
    $this->_verificarTablasLotes($omodelo);
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

          $lotesHtml = '';
          $queryLotes = "SELECT ID_Lote, Nombre_Lote, Cantidad, DATE_FORMAT(Fecha_Caducidad, '%d/%m/%Y') AS Caducidad 
                         FROM inventario_lotes 
                         WHERE FK_Inventario = '".$row[$i]['ID_Inventario']."' AND Cantidad > 0 
                         ORDER BY Fecha_Caducidad ASC, ID_Lote ASC";
          $rowLotes = $omodelo->_consultar($queryLotes);
          $numerofilasLotes = $omodelo->numerofilas;
          if ($rowLotes != 'si' && $numerofilasLotes > 0) {
            $lotesHtml = '<div class="mt-2 text-start p-1 rounded" style="background-color: #f8f9fa; border: 1px solid #e2e8f0; font-size: 11px; min-width: 170px; max-width: 290px; max-height: 140px; overflow-y: auto; margin: 8px auto 0;">';
            for ($l = 0; $l < $numerofilasLotes; $l++) {
              $codigoLoteCompleto = $row[$i]['Codigo'] . '_' . $rowLotes[$l]['ID_Lote'];
              $cadText = (!empty($rowLotes[$l]['Caducidad']) && $rowLotes[$l]['Caducidad'] != '00/00/0000') ? ' <span class="text-muted" style="font-size: 10px;">(' . $rowLotes[$l]['Caducidad'] . ')</span>' : '';
              $borderClass = ($l < ($numerofilasLotes - 1)) ? 'border-bottom' : '';
              $lotesHtml .= '<div class="py-1 px-1 ' . $borderClass . ' d-flex justify-content-between align-items-center" style="line-height: 1.2;">'
                . '<div class="d-flex align-items-center text-truncate me-2" title="' . htmlspecialchars($rowLotes[$l]['Nombre_Lote'], ENT_QUOTES) . ' | Código escanear: ' . $codigoLoteCompleto . '">'
                . '<button type="button" class="btn btn-sm btn-link p-0 me-1 text-secondary bImprimirLote" attrIDLote="' . $rowLotes[$l]['ID_Lote'] . '" title="Imprimir código de este lote (' . $codigoLoteCompleto . ')"><i class="fas fa-barcode"></i></button>'
                . '<span class="badge bg-dark text-white font-monospace me-1 py-0 px-1" style="font-size: 10px;" title="Código para escanear en caja">' . $codigoLoteCompleto . '</span>'
                . '<span class="text-truncate"><b>' . htmlspecialchars($rowLotes[$l]['Nombre_Lote']) . '</b></span>' . $cadText
                . '</div>'
                . '<span class="cantidad badge bg-primary text-white" style="font-size: 11px;">' . $rowLotes[$l]['Cantidad'] . '</span>'
                . '</div>';
            }
            $lotesHtml .= '</div>';
          }

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Inventario'],
            'Codigo' => $foto . $row[$i]['Codigo'],
            'Descripcion' => $row[$i]['Descripcion'],
            'Existencia' => '<span class="cantidad" style="font-size: 18px; color: '.$color.';">' . $row[$i]['Existencia'] . '</span><br><b>Stock Mínimo: </b><span class="cantidad">' . $row[$i]['Stock_Minimo'] . '</span><br><b>Stock Máximo: </b><span class="cantidad">' . $row[$i]['Stock_Maximo'] . '</span><br><div class="mt-2 d-flex justify-content-center align-items-center gap-1"><button type="button" class="btn btn-sm btn-primary bGestionarLotes" style="width: auto !important; min-width: 82px; height: 32px; padding: 0 10px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap;" title="Gestionar Lotes" attrID="' . $row[$i]['ID_Inventario'] . '" attrExistencia="' . $row[$i]['Existencia'] . '" attrCodigo="' . htmlspecialchars($row[$i]['Codigo'], ENT_QUOTES) . '" attrDescripcion="' . htmlspecialchars($row[$i]['Descripcion'], ENT_QUOTES) . '"><i class="fas fa-boxes"></i> <span>Lotes</span></button><button type="button" class="btn btn-sm btn-secondary bImprimirTodosLotes" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Imprimir códigos de todos los lotes" attrID="' . $row[$i]['ID_Inventario'] . '"><i class="fas fa-barcode"></i></button></div>' . $lotesHtml,
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
    //$sucursal = $omodelo->link->real_escape_string($sucursal);
    //$aplicar = $omodelo->link->real_escape_string($aplicar);

    if(isset($sucursal) && trim($sucursal) != '' && trim($sucursal) != 'undefined'){
      $query1 = "SELECT COUNT(*) AS Num FROM detalles_inventario WHERE FK_Inventario = '$id'"; // AND FK_Sucursal = $sucursal";
      $row = $omodelo->_consultar($query1);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      }else {
        if ($numerofilas > 0 && $row[0]['Num'] > 0) {
          $query = "UPDATE detalles_inventario SET Cantidad = Cantidad + $cantidad WHERE FK_Inventario = '$id'"; // AND FK_Sucursal = $sucursal";
        }else{
          $query = "INSERT INTO detalles_inventario SET Cantidad = '$cantidad', FK_Inventario = '$id'"; //, FK_Sucursal = $sucursal";
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
    //$sucursal = $omodelo->link->real_escape_string($sucursal);
    //$aplicar = $omodelo->link->real_escape_string($aplicar);

    if(isset($sucursal) && trim($sucursal) != ''){
      $query1 = "SELECT COUNT(*) AS Num FROM detalles_inventario WHERE FK_Inventario = '$id'"; // AND FK_Sucursal = $sucursal";
      $row = $omodelo->_consultar($query1);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      }else {
        if ($numerofilas > 0 && $row[0]['Num'] > 0) {
          $query = "UPDATE detalles_inventario SET Cantidad = Cantidad - $cantidad WHERE FK_Inventario = '$id'"; // AND FK_Sucursal = $sucursal";
        }else{
          $query = "INSERT INTO detalles_inventario SET Cantidad = '$cantidad', FK_Inventario = '$id'"; //, FK_Sucursal = $sucursal";
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
      $query = "UPDATE inventario SET Cantidad = Cantidad - $cantidad WHERE ID_Inventario = '$id'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 3: " . mysqli_error($omodelo->link);
      }else{
        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    //}

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
              'Fecha' => $row[$i]['Fecha_Registro'],
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

      //, FK_Sucursal
      $query = "SELECT ID_Merma, FK_Producto, Descripcion, Cantidad, Costo, Total, Fecha_Merma, Fecha_Registro, Foto, FK_Usuario, Afecto_Inventario FROM merma WHERE ID_Merma = '$id'";
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
            //'FK_Sucursal' => $row[0]['FK_Sucursal'], 
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
      //$sucursalMerma = $omodelo->link->real_escape_string($sucursalMerma);
      $afecta = $omodelo->link->real_escape_string($afecta);
      $producto = $omodelo->link->real_escape_string($producto);
      $idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];

      $cantidadMerma = str_replace(',', '', $cantidadMerma);
      $query = "INSERT INTO merma SET 
        FK_Producto = '$producto', 
        Descripcion = '$descriMerma', 
        Cantidad = '$cantidadMerma', 
        Costo = IFNULL((SELECT Costo_Promedio FROM productos WHERE ID_Producto = '$producto'), 0), 
        Total = ($cantidadMerma * IFNULL((SELECT Costo_Promedio FROM productos WHERE ID_Producto = '$producto'), 0)), 
        Fecha_Merma = '$fechaMerma', 
        FK_Usuario = '$idUsuario', 
        Afecto_Inventario = '$afecta', 
        Fecha_Registro = NOW()";
        //FK_Sucursal = '$sucursalMerma', 
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
          $bd = $_SESSION['user_punto_bd'];

          if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != '') {
            echo 'Error 2 formato';
            $status = 1;
          } else if ($sizeImg > (1024 * 1024 * 10)) {
            echo 'Error 3 peso';
            $status = 1;
          } else {
            $ruta = $carpeta . $bd . '_' . $id . '_' . $nombreImg;
          }

          if ($status == 0 && $nombreImg != '') {
            $query1 = "UPDATE merma SET Foto = '" . $bd . '_' . $id . '_' . $nombreImg . "'  WHERE ID_Merma = '$id'";
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
      //$sucursalMerma = $omodelo->link->real_escape_string($sucursalMerma);
      $afecta = $omodelo->link->real_escape_string($afecta);
      $idUsuario = $_SESSION['user_punto_venta']['ID_Usuario'];

      //FK_Sucursal = '$sucursalMerma'
      $query = "UPDATE merma SET 
        Descripcion = '$descriMerma', 
        Cantidad = '$cantidadMerma', 
        Fecha_Merma = '$fechaMerma', 
        FK_Usuario = '$idUsuario', 
        Afecto_Inventario = '$afecta', 
        Total = ($cantidadMerma * Costo) 
      WHERE ID_Merma = '$id'";
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
          $bd = $_SESSION['user_punto_bd'];

          if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != '') {
            echo 'Error 2 formato';
            $status = 1;
          } else if ($sizeImg > (1024 * 1024 * 10)) {
            echo 'Error 3 peso';
            $status = 1;
          } else {
            $ruta = $carpeta . $bd . '_' . $id . '_' . $nombreImg;
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

            $query1 = "UPDATE merma SET Foto = '" . $bd . '_' . $id . '_' . $nombreImg . "'  WHERE ID_Merma = '$id'";
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
    } else if ($tipo == 'consultarLotes') {
      $this->_verificarTablasLotes($omodelo);
      $id = $omodelo->link->real_escape_string($id);

      $queryInfo = "SELECT i.ID_Inventario, i.Cantidad AS Existencia, p.Codigo, p.Descripcion FROM inventario i INNER JOIN productos p ON i.FK_Producto = p.ID_Producto WHERE i.ID_Inventario = '$id'";
      $rowInfo = $omodelo->_consultar($queryInfo);

      if ($rowInfo == 'si' || $omodelo->numerofilas == 0) {
        echo json_encode(array('error' => 'Inventario no encontrado'));
        return;
      }

      $queryLotes = "SELECT ID_Lote, FK_Inventario, Nombre_Lote, Fecha_Caducidad, Cantidad, DATE_FORMAT(Fecha_Caducidad, '%Y-%m-%d') AS Fecha_Caducidad_Input, DATE_FORMAT(Fecha_Caducidad, '%d/%m/%Y') AS Fecha_Caducidad_Visual FROM inventario_lotes WHERE FK_Inventario = '$id' ORDER BY Fecha_Caducidad ASC, ID_Lote ASC";
      $rowLotes = $omodelo->_consultar($queryLotes);
      $lotes = array();
      $totalLoteado = 0;

      if ($rowLotes != 'si' && $omodelo->numerofilas > 0) {
        for ($k = 0; $k < $omodelo->numerofilas; $k++) {
          $lotes[] = array(
            'ID_Lote' => $rowLotes[$k]['ID_Lote'],
            'Nombre_Lote' => $rowLotes[$k]['Nombre_Lote'],
            'Fecha_Caducidad' => $rowLotes[$k]['Fecha_Caducidad_Input'],
            'Fecha_Caducidad_Visual' => $rowLotes[$k]['Fecha_Caducidad_Visual'],
            'Cantidad' => floatval($rowLotes[$k]['Cantidad'])
          );
          $totalLoteado += floatval($rowLotes[$k]['Cantidad']);
        }
      }

      echo json_encode(array(
        'idInventario' => $rowInfo[0]['ID_Inventario'],
        'codigo' => $rowInfo[0]['Codigo'],
        'descripcion' => $rowInfo[0]['Descripcion'],
        'existencia' => floatval($rowInfo[0]['Existencia']),
        'totalLoteado' => $totalLoteado,
        'lotes' => $lotes
      ));
    } else if ($tipo == 'guardarLotes') {
      $this->_verificarTablasLotes($omodelo);
      $id = $omodelo->link->real_escape_string($id);
      $lotes = isset($lotes) ? json_decode($lotes, true) : array();

      $queryInfo = "SELECT Cantidad FROM inventario WHERE ID_Inventario = '$id'";
      $rowInfo = $omodelo->_consultar($queryInfo);
      if ($rowInfo == 'si' || $omodelo->numerofilas == 0) {
        echo 'Error: Inventario no encontrado.';
        return;
      }
      $existencia = floatval($rowInfo[0]['Cantidad']);

      $suma = 0;
      foreach ($lotes as $l) {
        $suma += floatval($l['cantidad']);
      }

      if ($suma > $existencia) {
        echo "Error: La suma de los lotes ($suma) supera la existencia actual ($existencia).";
        return;
      }

      foreach ($lotes as $l) {
        $idLote = isset($l['idLote']) ? intval($l['idLote']) : 0;
        $nombre = $omodelo->link->real_escape_string(trim($l['nombre']));
        $caducidad = $omodelo->link->real_escape_string(trim($l['caducidad']));
        $cantidad = floatval($l['cantidad']);

        if ($idLote > 0) {
          $queryUp = "UPDATE inventario_lotes SET Nombre_Lote = '$nombre', Fecha_Caducidad = '$caducidad', Cantidad = '$cantidad' WHERE ID_Lote = '$idLote' AND FK_Inventario = '$id'";
          $omodelo->_insertar($queryUp);
        } else {
          $queryIn = "INSERT INTO inventario_lotes SET FK_Inventario = '$id', Nombre_Lote = '$nombre', Fecha_Caducidad = '$caducidad', Cantidad = '$cantidad', Fecha_Registro = NOW()";
          $omodelo->_insertar($queryIn);
        }
      }

      echo 'Correcto';
    } else if ($tipo == 'eliminarLote') {
      $this->_verificarTablasLotes($omodelo);
      $idLote = $omodelo->link->real_escape_string($idLote);
      $queryDel = "DELETE FROM inventario_lotes WHERE ID_Lote = '$idLote'";
      $error = $omodelo->_insertar($queryDel);
      if ($error == 'si') {
        echo 'Error: ' . mysqli_error($omodelo->link);
      } else {
        echo 'Correcto';
      }
    }
  }

  private function _verificarTablasLotes($omodelo) {
    $q1 = "CREATE TABLE IF NOT EXISTS `inventario_lotes` (
      `ID_Lote` int(11) NOT NULL AUTO_INCREMENT,
      `FK_Inventario` int(11) NOT NULL,
      `Nombre_Lote` varchar(100) NOT NULL,
      `Fecha_Caducidad` date NOT NULL,
      `Cantidad` double NOT NULL,
      `Fecha_Registro` datetime NOT NULL,
      PRIMARY KEY (`ID_Lote`),
      KEY `FK_Inventario` (`FK_Inventario`),
      KEY `Fecha_Caducidad` (`Fecha_Caducidad`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    $omodelo->_insertar($q1);

    $q2 = "CREATE TABLE IF NOT EXISTS `detalles_ventas_lotes` (
      `ID_Detalle_Venta_Lote` int(11) NOT NULL AUTO_INCREMENT,
      `FK_Detalle_Venta` int(11) NOT NULL,
      `FK_Lote` int(11) NOT NULL,
      `Cantidad` double NOT NULL,
      PRIMARY KEY (`ID_Detalle_Venta_Lote`),
      KEY `FK_Detalle_Venta` (`FK_Detalle_Venta`),
      KEY `FK_Lote` (`FK_Lote`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    $omodelo->_insertar($q2);

    $q3 = "CREATE TABLE IF NOT EXISTS `detalle_compras_lotes` (
      `ID_Detalle_Compra_Lote` int(11) NOT NULL AUTO_INCREMENT,
      `FK_Detalle_Compra` int(11) NOT NULL,
      `FK_Lote` int(11) NOT NULL,
      `Cantidad` double NOT NULL,
      PRIMARY KEY (`ID_Detalle_Compra_Lote`),
      KEY `FK_Detalle_Compra` (`FK_Detalle_Compra`),
      KEY `FK_Lote` (`FK_Lote`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    $omodelo->_insertar($q3);
  }
}
?>