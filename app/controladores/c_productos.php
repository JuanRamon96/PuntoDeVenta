<?php
class productos
{

  public function _consultar()
  {
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
        $busqueda .= "CONCAT(
          DATE_FORMAT(productos.Fecha_Registro, '%d-%m-%Y %r'), 
          Codigo, 
          productos.Descripcion, 
          Costo, 
          Costo_Promedio, 
          Precio, 
          Precio_Mayoreo, 
          Clase, 
          Stock_Minimo, 
          Stock_Maximo, 
          IFNULL(clasificaciones.Nombre, ''),
          Clave_ProdServ_CFDI,
          Descripcion_Clave_CDFI,
          Clave_Unidad_CFDI,
          Nombre_Unidad_CFDI,
          Simbolo_CFDI
        ) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $query = "SELECT 
      ID_Producto, 
      Codigo, 
      productos.Descripcion AS Descripcion, 
      Clase, 
      Costo, 
      Costo_Promedio, 
      Precio, 
      Precio_Mayoreo, 
      Stock_Minimo, 
      Stock_Maximo, 
      IFNULL(clasificaciones.Nombre, '') AS Clasificacion, 
      productos.Fecha_Registro AS Fecha, 
      productos.Foto AS Foto, 
      Clave_ProdServ_CFDI,
      Descripcion_Clave_CDFI,
      Clave_Unidad_CFDI,
      Nombre_Unidad_CFDI,
      Simbolo_CFDI,
      DATE_FORMAT(productos.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, 
      (SELECT COUNT(*) FROM productos $busqueda) AS Num 
    FROM productos LEFT JOIN clasificaciones ON FK_Clasificacion = ID_Clasificacion 
    $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        for ($i = 0; $i < $numerofilas; $i++) {
          $impuestos = 'Sin impuestos';
          $query1 = "SELECT 
            ID_Impuesto_Producto, 
            FK_Impuesto,
            Nombre, 
            Porcentaje, 
            Clave_CFDI, 
            Tipo_Factor, 
            Clase 
          FROM impuestos_productos INNER JOIN impuestos ON FK_Impuesto = ID_Impuesto
          WHERE FK_Producto = '" . $row[$i]['ID_Producto'] . "' ORDER BY Nombre ASC";
          $row1 = $omodelo->_consultar($query1);
          $numerofilas1 = $omodelo->numerofilas;

          if ($row1 == 'si') {
            echo "Error 2: " . mysqli_error($omodelo->link);
          } else {
            if ($numerofilas1 > 0) {
              $impuestos = '';
              for ($j = 0; $j < $numerofilas1; $j++) {
                $impuestos .= '<b>' . $row1[$j]['Nombre'] . '</b> (<span class="cantidad">' . $row1[$j]['Porcentaje'] . '</span>%) - ' . $row1[$j]['Tipo_Factor'] . ' - ' . $row1[$j]['Clase'] . '<br>';
              }
            }
          }

          $foto = '<a href="vistas/assets/images/producto-generico.png" data-fancybox="images">
            <div style="background-image: url(' . "'" . 'vistas/assets/images/producto-generico.png' . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
          </div></a>';

          if ($row[$i]['Foto'] != '' && file_exists('vistas/assets/images/productos/' . $row[$i]['Foto'])) {
            $foto = '<a href="vistas/assets/images/productos/' . $row[$i]['Foto'] . '" data-fancybox="images">
              <div style="background-image: url(' . "'" . 'vistas/assets/images/productos/' . $row[$i]['Foto'] . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
            </div></a>';
          }

          $bModificar = '';
          $bEliminar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Productos'][3] == '1') {
            $bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarProducto" title="Modificar Producto" attrID="' . $row[$i]['ID_Producto'] . '"><i class="fas fa-pencil"></i></button>';
          }
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == 1 || $_SESSION['user_punto_venta']['Permisos']['Productos'][4] == '1') {
            $bEliminar = '<button type="button" class="btn btn-sm btn-danger bEliminarProducto" title="Eliminar Producto" attrID="' . $row[$i]['ID_Producto'] . '" foto="' . $row[$i]['Foto'] . '"><i class="fas fa-trash"></i></button>';
          }

          $datosFacturacion = '';
          if ($row[$i]['Clave_ProdServ_CFDI'] != '') {
            $datosFacturacion .= '<br><b>Clave: </b> <span>' . $row[$i]['Clave_ProdServ_CFDI'] . '</span>';
          }
          if ($row[$i]['Descripcion_Clave_CDFI'] != '') {
            $datosFacturacion .= '<br><b>Descripción: </b> <span>' . $row[$i]['Descripcion_Clave_CDFI'] . '</span>';
          }
          if ($row[$i]['Clave_Unidad_CFDI'] != '') {
            $datosFacturacion .= '<br><b>Clave Unidad: </b> <span>' . $row[$i]['Clave_Unidad_CFDI'] . '</span>';
          }
          if ($row[$i]['Nombre_Unidad_CFDI'] != '') {
            $datosFacturacion .= '<br><b>Nombre Unidad: </b> <span>' . $row[$i]['Nombre_Unidad_CFDI'] . '</span>';
          }
          if ($row[$i]['Simbolo_CFDI'] != '') {
            $datosFacturacion .= '<br><b>Simbolo: </b> <span>' . $row[$i]['Simbolo_CFDI'] . '</span>';
          }

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Producto'],
            'Codigo' => $foto . $row[$i]['Codigo'],
            'Fecha' => $row[$i]['Fecha_Registro'],
            'Descripcion' => $row[$i]['Descripcion'] . $datosFacturacion,
            'Clase' => $row[$i]['Clase'],
            'Costo' => '<span class="dinero">' . $row[$i]['Costo'] . '</span>',
            'Costo_Promedio' => '<span class="dinero">' . $row[$i]['Costo_Promedio'] . '</span>',
            'Precio' => '<span class="dinero">' . $row[$i]['Precio'] . '</span>',
            'Precio_Mayoreo' => '<span class="dinero">' . $row[$i]['Precio_Mayoreo'] . '</span>',
            'Stock_Minimo' => '<span class="cantidad">' . $row[$i]['Stock_Minimo'] . '</span>',
            'Stock_Maximo' => '<span class="cantidad">' . $row[$i]['Stock_Maximo'] . '</span>',
            'Clasificacion' => ($row[$i]['Clasificacion'] == null ? '' : $row[$i]['Clasificacion']),
            'Impuestos' => $impuestos,
            'Acciones' => $bModificar . ' ' . $bEliminar . ' <button type="button" class="btn btn-outline-secondary btn-sm bImprimirCodigo" attrID="' . $row[$i]['ID_Producto'] . '"><i class="fas fa-barcode"></i></button>'
          );
        }

        $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
      }
    }

    echo json_encode($arreglo);
  }

  public function _detalles()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $tipo = $omodelo->link->real_escape_string($tipo);

    if ($tipo == 'producto') {
      $arreglo = array();
      $id = $omodelo->link->real_escape_string($id);

      $query = "SELECT 
        ID_Producto, 
        Codigo, 
        Descripcion, 
        Clase, 
        Costo, 
        Precio, 
        Precio_Mayoreo, 
        Stock_Minimo, 
        Stock_Maximo, 
        FK_Clasificacion, 
        Foto,
        Clave_ProdServ_CFDI,
        Descripcion_Clave_CDFI,
        Clave_Unidad_CFDI,
        Nombre_Unidad_CFDI,
        Simbolo_CFDI 
      FROM productos WHERE ID_Producto = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error : ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $impuestos = [];
            $query1 = "SELECT 
              FK_Impuesto
            FROM impuestos_productos WHERE FK_Producto = '" . $row[$i]['ID_Producto'] . "'";
            $row1 = $omodelo->_consultar($query1);
            $numerofilas1 = $omodelo->numerofilas;

            if ($row1 == 'si') {
              echo "Error 2: " . mysqli_error($omodelo->link);
            } else {
              if ($numerofilas1 > 0) {
                for ($j = 0; $j < $numerofilas1; $j++) {
                  $impuestos[] = $row1[$j]['FK_Impuesto'];
                }
              }
            }

            $arreglo = array(
              'ID_Producto' => $row[$i]['ID_Producto'],
              'Codigo' => $row[$i]['Codigo'],
              'Descripcion' => $row[$i]['Descripcion'],
              'Costo' => $row[$i]['Costo'],
              'Precio' => $row[$i]['Precio'],
              'Precio_Mayoreo' => $row[$i]['Precio_Mayoreo'],
              'Clase' => $row[$i]['Clase'],
              'Foto' => $row[$i]['Foto'],
              'Stock_Minimo' => $row[$i]['Stock_Minimo'],
              'Stock_Maximo' => $row[$i]['Stock_Maximo'],
              'FK_Clasificacion' => $row[$i]['FK_Clasificacion'],
              'Clave_ProdServ_CFDI' => $row[$i]['Clave_ProdServ_CFDI'],
              'Descripcion_Clave_CDFI' => $row[$i]['Descripcion_Clave_CDFI'],
              'Clave_Unidad_CFDI' => $row[$i]['Clave_Unidad_CFDI'],
              'Nombre_Unidad_CFDI' => $row[$i]['Nombre_Unidad_CFDI'],
              'Simbolo_CFDI' => $row[$i]['Simbolo_CFDI'],
              'Impuestos' => $impuestos
            );
          }
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'marcar') {
      $arreglo = array();
      $buscar = $omodelo->link->real_escape_string($buscar);

      $busqueda = '';
      if (trim($buscar) != '') {
        $separa = explode(' ', trim($buscar));
        $busqueda = 'WHERE ';
        for ($i = 0; $i < count($separa); $i++) {
          $busqueda .= "CONCAT(DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), Codigo, Descripcion, Costo, Precio, Precio_Mayoreo, Clase) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $query = "SELECT ID_Producto FROM productos $busqueda";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $arreglo = $row;
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'claves') {
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
          $busqueda .= "CONCAT(
            Clave,
            Descripcion,
            Palabras
          ) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $omodelo->_insertar('USE punto_subs');
      $query = "SELECT 
        ID_Clave, 
        Clave,
        Descripcion,
        Palabras, 
        (SELECT COUNT(*) FROM claves_productos_cfdi $busqueda) AS Num 
      FROM claves_productos_cfdi $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Clave'],
              'Clave' => $row[$i]['Clave'],
              'Descripcion' => $row[$i]['Descripcion'],
              'Palabras' => $row[$i]['Palabras']
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }

      echo json_encode($arreglo, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    } else if ($tipo == 'unidades') {
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
          $busqueda .= "CONCAT(
            Clave,
            Nombre,
            Simbolo
          ) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $omodelo->_insertar('USE punto_subs');
      $query = "SELECT 
        ID_Clave, 
        Clave,
        Nombre,
        Simbolo, 
        (SELECT COUNT(*) FROM claves_unidades_cfdi $busqueda) AS Num 
      FROM claves_unidades_cfdi $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Clave'],
              'Clave' => $row[$i]['Clave'],
              'Nombre' => $row[$i]['Nombre'],
              'Simbolo' => $row[$i]['Simbolo']
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }

      echo json_encode($arreglo, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
  }

  public function _insertar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $fecha = date('Y-m-d H:i:s');

    $codigoProducto = $omodelo->link->real_escape_string(trim($codigoProducto));
    $descripcionProducto = $omodelo->link->real_escape_string(trim($descripcionProducto));
    $costoProducto = $omodelo->link->real_escape_string($costoProducto);
    $precioProducto = $omodelo->link->real_escape_string($precioProducto);
    $precioMayoreoProducto = $omodelo->link->real_escape_string($precioMayoreoProducto);
    $claseProducto = $omodelo->link->real_escape_string($claseProducto);
    $stockMinimoProducto = $omodelo->link->real_escape_string($stockMinimoProducto);
    $stockMaximoProducto = $omodelo->link->real_escape_string($stockMaximoProducto);
    $clasificacionProducto = $omodelo->link->real_escape_string($clasificacionProducto);
    $claveProducto = $omodelo->link->real_escape_string($claveProducto);
    $descripcionClaveProducto = $omodelo->link->real_escape_string($descripcionClaveProducto);
    $claveUnidadProducto = $omodelo->link->real_escape_string($claveUnidadProducto);
    $nombreUnidadProducto = $omodelo->link->real_escape_string($nombreUnidadProducto);
    $simboloProducto = $omodelo->link->real_escape_string($simboloProducto);

    $query = "INSERT INTO productos SET 
      Codigo = '$codigoProducto', 
      Descripcion = '$descripcionProducto', 
      Costo = '$costoProducto', 
      Costo_Promedio = '$costoProducto', 
      Precio = '$precioProducto', 
      Precio_Mayoreo = '$precioMayoreoProducto', 
      Clase = '$claseProducto', 
      Stock_Minimo = '$stockMinimoProducto', 
      Stock_Maximo = '$stockMaximoProducto', 
      FK_Clasificacion = '$clasificacionProducto',
      Clave_ProdServ_CFDI = '$claveProducto', 
      Descripcion_Clave_CDFI = '$descripcionClaveProducto',
      Clave_Unidad_CFDI = '$claveUnidadProducto',
      Nombre_Unidad_CFDI = '$nombreUnidadProducto',
      Simbolo_CFDI = '$simboloProducto',
      Fecha_Registro = NOW()";
    $error = $omodelo->_insertar($query);
    $status = 0;

    if ($error == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
      $status = 1;
    } else {
      $id = $omodelo->link->insert_id;

      $impuestos = json_decode($impuestosAgregar, true);
      foreach ($impuestos as $impuesto) {
        $impuesto = $omodelo->link->real_escape_string($impuesto);

        $query = "INSERT INTO impuestos_productos SET 
          FK_Producto = '$id', 
          FK_Impuesto = '$impuesto'";
        $error = $omodelo->_insertar($query);

        if ($error == 'si') {
          echo "Error impuestos: " . mysqli_error($omodelo->link);
          $status = 1;
          return;
        }
      }

      $nombreImg = '';
      $ruta = '';
      $rutaProvisional = '';
      $carpeta = 'vistas/assets/images/productos/';
      if ($_FILES['fotoProducto']['size'] > 0 && $_FILES['fotoProducto']['error'] == 0) {
        $file = $_FILES['fotoProducto'];
        $nombreImg = $file['name'];
        $tipoImg = $file['type'];
        $rutaProvisional = $file['tmp_name'];
        $sizeImg = $file['size'];
        $bd = $_SESSION['user_punto_bd'];

        if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != 'image/svg' && $tipoImg != '') {
          echo 'Error 2 formato ' . $tipoImg;
          $status = 1;
        } else if ($sizeImg > (1024 * 1024 * 10)) {
          echo 'Error 3 peso';
          $status = 1;
        } else {
          $ruta = $carpeta . $bd . '_' . $id . '_' . $nombreImg;
        }

        if ($status == 0 && $nombreImg != '') {
          $query = "UPDATE productos SET Foto = '" . $bd . '_' . $id . '_' . $nombreImg . "'  WHERE ID_Producto = '$id'";
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

  public function _modificar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $fecha = date('Y-m-d H:i:s');

    $id = $omodelo->link->real_escape_string($id);
    $foto = $omodelo->link->real_escape_string($foto);
    $codigoProducto = $omodelo->link->real_escape_string(trim($codigoProducto));
    $descripcionProducto = $omodelo->link->real_escape_string(trim($descripcionProducto));
    $costoProducto = $omodelo->link->real_escape_string($costoProducto);
    $precioProducto = $omodelo->link->real_escape_string($precioProducto);
    $precioMayoreoProducto = $omodelo->link->real_escape_string($precioMayoreoProducto);
    $claseProducto = $omodelo->link->real_escape_string($claseProducto);
    $stockMinimoProducto = $omodelo->link->real_escape_string($stockMinimoProducto);
    $stockMaximoProducto = $omodelo->link->real_escape_string($stockMaximoProducto);
    $clasificacionProducto = $omodelo->link->real_escape_string($clasificacionProducto);
    $claveProducto = $omodelo->link->real_escape_string($claveProducto);
    $descripcionClaveProducto = $omodelo->link->real_escape_string($descripcionClaveProducto);
    $claveUnidadProducto = $omodelo->link->real_escape_string($claveUnidadProducto);
    $nombreUnidadProducto = $omodelo->link->real_escape_string($nombreUnidadProducto);
    $simboloProducto = $omodelo->link->real_escape_string($simboloProducto);

    $query = "UPDATE productos SET 
      Codigo = '$codigoProducto', 
      Descripcion = '$descripcionProducto', 
      Costo = '$costoProducto', 
      Precio = '$precioProducto', 
      Precio_Mayoreo = '$precioMayoreoProducto', 
      Clase = '$claseProducto', 
      Stock_Minimo = '$stockMinimoProducto', 
      Stock_Maximo = '$stockMaximoProducto', 
      FK_Clasificacion = '$clasificacionProducto',
      Clave_ProdServ_CFDI = '$claveProducto', 
      Descripcion_Clave_CDFI = '$descripcionClaveProducto',
      Clave_Unidad_CFDI = '$claveUnidadProducto',
      Nombre_Unidad_CFDI = '$nombreUnidadProducto',
      Simbolo_CFDI = '$simboloProducto'
    WHERE ID_Producto = '$id'";
    $error = $omodelo->_insertar($query);
    $status = 0;

    if ($error == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
      $status = 1;
    } else {
      $impuestos = json_decode($impuestosEliminar, true);
      foreach ($impuestos as $impuesto) {
        $impuesto = $omodelo->link->real_escape_string($impuesto);

        $query = "DELETE FROM impuestos_productos WHERE FK_Producto = '$id' AND FK_Impuesto = '$impuesto'";
        $error = $omodelo->_insertar($query);

        if ($error == 'si') {
          echo "Error impuestos 1: " . mysqli_error($omodelo->link);
          $status = 1;
          return;
        }
      }

      $impuestos = json_decode($impuestosAgregar, true);
      foreach ($impuestos as $impuesto) {
        $impuesto = $omodelo->link->real_escape_string($impuesto);

        $query = "INSERT INTO impuestos_productos (FK_Producto, FK_Impuesto)
          SELECT '$id', '$impuesto'
          FROM DUAL
          WHERE NOT EXISTS (
              SELECT 1 FROM impuestos_productos 
              WHERE FK_Producto = '$id' AND FK_Impuesto = '$impuesto'
          ) LIMIT 1";
        $error = $omodelo->_insertar($query);

        if ($error == 'si') {
          echo "Error impuestos 2: " . mysqli_error($omodelo->link);
          $status = 1;
          return;
        }
      }

      $nombreImg = '';
      $ruta = '';
      $rutaProvisional = '';
      $carpeta = 'vistas/assets/images/productos/';
      if ($_FILES['fotoProducto']['size'] > 0 && $_FILES['fotoProducto']['error'] == 0) {
        $file = $_FILES['fotoProducto'];
        $nombreImg = $file['name'];
        $tipoImg = $file['type'];
        $rutaProvisional = $file['tmp_name'];
        $sizeImg = $file['size'];
        $bd = $_SESSION['user_punto_bd'];

        if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != 'image/svg' && $tipoImg != '') {
          echo 'Error 2 formato ' . $tipoImg;
          $status = 1;
        } else if ($sizeImg > (1024 * 1024 * 10)) {
          echo 'Error 3 peso';
          $status = 1;
        } else {
          $ruta = $carpeta . $bd . '_' . $id . '_' . $nombreImg;
        }

        if ($status == 0 && $nombreImg != '') {
          $query = "UPDATE productos SET Foto = '" . $bd . '_' . $id . '_' . $nombreImg . "'  WHERE ID_Producto = '$id'";
          $error = $omodelo->_insertar($query);
          if ($error == 'si') {
            echo "Error 4: " . mysqli_error($omodelo->link);
          } else {
            if (trim($foto) != '' && file_exists($carpeta . $foto)) {
              unlink($carpeta . $foto);
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

  public function _eliminar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $fecha = date('Y-m-d H:i:s');

    $id = $omodelo->link->real_escape_string($id);
    $foto = $omodelo->link->real_escape_string($foto);

    $query = "DELETE FROM productos WHERE ID_Producto = '$id'";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo "Error 1: " . mysqli_error($omodelo->link);
    } else {
      if (trim($foto) != '' && file_exists('vistas/assets/images/productos/' . $foto)) {
        unlink('vistas/assets/images/productos/' . $foto);
      }

      echo 'Correcto';

      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }
}
