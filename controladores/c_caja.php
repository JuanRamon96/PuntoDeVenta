<?php
class caja
{

  public function _consultar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $tipo = $omodelo->link->real_escape_string($tipo);
    $arreglo = array();

    if ($tipo == 'agregarProducto') {
      $codigo = $omodelo->link->real_escape_string($codigo);
      $query = "SELECT ID_Producto, Codigo, Descripcion, Precio, Precio_Mayoreo, Clase, IFNULL(Cantidad, 0) AS Existencia FROM productos LEFT JOIN inventario ON FK_Producto = ID_Producto WHERE Codigo = '$codigo'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error: ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $impuestos = array();
          $query1 = "SELECT 
            ID_Impuesto_Producto, 
            FK_Impuesto,
            Nombre, 
            Porcentaje, 
            Clave_CFDI, 
            Tipo_Factor, 
            Clase 
          FROM impuestos_productos INNER JOIN impuestos ON FK_Impuesto = ID_Impuesto
          WHERE FK_Producto = '" . $row[0]['ID_Producto'] . "' ORDER BY Nombre ASC";
          $row1 = $omodelo->_consultar($query1);
          $numerofilas1 = $omodelo->numerofilas;

          if ($row1 == 'si') {
            echo "Error 2: " . mysqli_error($omodelo->link);
          } else {
            if ($numerofilas1 > 0) {
              for ($j = 0; $j < $numerofilas1; $j++) {
                $impuestos[] = array(
                  'ID_Impuesto_Producto' => $row1[$j]['ID_Impuesto_Producto'],
                  'FK_Impuesto' => $row1[$j]['FK_Impuesto'],
                  'Nombre' => $row1[$j]['Nombre'],
                  'Porcentaje' => $row1[$j]['Porcentaje'],
                  'Clave_CFDI' => $row1[$j]['Clave_CFDI'],
                  'Tipo_Factor' => $row1[$j]['Tipo_Factor'],
                  'Clase' => $row1[$j]['Clase']
                );
              }
            }
          }

          $arreglo = array(
            'ID_Producto' => $row[0]['ID_Producto'],
            'Codigo' => $row[0]['Codigo'],
            'Descripcion' => $row[0]['Descripcion'],
            'Precio' => $row[0]['Precio'],
            'Precio_Mayoreo' => $row[0]['Precio_Mayoreo'],
            'Clase' => $row[0]['Clase'],
            'Existencia' => $row[0]['Existencia'],
            'Impuestos' => $impuestos
          );

          echo json_encode($arreglo);
        } else {
          echo 'No encontrado';
        }
      }
    } else if ($tipo == 'productos') {
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
            Codigo, 
            Descripcion, 
            Precio, 
            Precio_Mayoreo
          ) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $query = "SELECT 
        ID_Producto, 
        Codigo, 
        Descripcion, 
        Clase, 
        IFNULL(Cantidad, 0) AS Existencia, 
        Costo, 
        Precio, 
        Precio_Mayoreo, 
        Foto, 
        (SELECT COUNT(*) FROM productos $busqueda) AS Num 
      FROM productos LEFT JOIN inventario ON FK_Producto = ID_Producto 
      $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
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
                  $tipo = 'porcentaje';
                  if ($row1[$j]['Tipo_Factor'] == 'Cuota') {
                    $tipo = 'dinero';
                  }

                  $impuestos .= '<p class="m-0" attrID="' . $row1[$j]['FK_Impuesto'] . '" clave="' . $row1[$j]['Clave_CFDI'] . '"><span class="fw-bold">' . $row1[$j]['Clase'] . '</span> <b>' . $row1[$j]['Nombre'] . '</b> (<span class="valor ' . $tipo . '">' . $row1[$j]['Porcentaje'] . '</span>) - <b>' . $row1[$j]['Tipo_Factor'] . '</b></p>';
                }
              }
            }

            $foto = '<div style="background-image: url(' . "'" . 'vistas/assets/images/producto-generico.png' . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
            </div>';

            if ($row[$i]['Foto'] != '' && file_exists('vistas/assets/images/productos/' . $row[$i]['Foto'])) {
              $foto = '<div style="background-image: url(' . "'" . 'vistas/assets/images/productos/' . $row[$i]['Foto'] . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
              </div>';
            }

            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Producto'],
              'Codigo' => $foto . $row[$i]['Codigo'],
              'Clase' => $row[$i]['Clase'],
              'Descripcion' => $row[$i]['Descripcion'],
              'Costo' => '<span class="dinero">' . $row[$i]['Costo'] . '</span>',
              'Precio' => '<span class="dinero">' . $row[$i]['Precio'] . '</span>',
              'Precio_Mayoreo' => '<span class="dinero">' . $row[$i]['Precio_Mayoreo'] . '</span>',
              'Impuestos' => $impuestos,
              'Existencia' => '<span class="cantidad">' . $row[$i]['Existencia'] . '</span>',
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'corte') {
      $idCaja = $omodelo->link->real_escape_string($idCaja);
      $arreglo['detalles'] = array();

      $query = "SELECT ID_Detalle_Caja, DATE_FORMAT(Fecha_Abrir, '%d-%m-%Y %r') AS Fecha_Abrir, Monto_Abrir, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario_Abrir), '') AS Usuario_Abrio FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $arreglo['detalles'] = array('Fecha_Abrir' => $row[0]['Fecha_Abrir'], 'Monto_Abrir' => $row[0]['Monto_Abrir'], 'Fecha_Cierre' => date('d-m-Y h:i A'), 'Fecha' => date('Y-m-d H:i'), 'Usuario_Abrio' => $row[0]['Usuario_Abrio']);
        }
      }

      $arreglo['ventas'] = array();

      $query = "SELECT SUM(Pago - Cambio) AS Total, Tipo_Pago FROM ventas WHERE FK_Detalles_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1) GROUP BY Tipo_Pago";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo['ventas'][$i] = array('Total' => $row[$i]['Total'], 'Tipo_Pago' => $row[$i]['Tipo_Pago']);
          }
        }
      }

      $arreglo['pagos_compras'] = array();

      $query = "SELECT SUM(Monto) AS Total, Tipo_Pago FROM compras_pagos WHERE FK_Detalle_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1) GROUP BY Tipo_Pago";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo['pagos_compras'][$i] = array('Total' => $row[$i]['Total'], 'Tipo_Pago' => $row[$i]['Tipo_Pago']);
          }
        }
      }

      $arreglo['pagos_ventas'] = array();

      $query = "SELECT SUM(Monto) AS Total, Tipo_Pago FROM ventas_pagos WHERE FK_Detalle_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1) GROUP BY Tipo_Pago";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo['pagos_ventas'][$i] = array('Total' => $row[$i]['Total'], 'Tipo_Pago' => $row[$i]['Tipo_Pago']);
          }
        }
      }

      $arreglo['movimientos'] = array();

      $query = "SELECT Tipo, Monto, Descripcion, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha FROM movimientos_caja WHERE FK_Detalle_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1) ORDER BY Fecha_Registro DESC";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo['movimientos'][$i] = array('Tipo' => $row[$i]['Tipo'], 'Monto' => $row[$i]['Monto'], 'Descripcion' => $row[$i]['Descripcion'], 'Fecha_Registro' => $row[$i]['Fecha']);
          }
        }
      }

      $arreglo['usuarios'] = array();

      $query = "SELECT SUM(Pago - Cambio) AS Total, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario), '') AS Usuario FROM ventas WHERE FK_Detalles_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1) GROUP BY FK_Usuario";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo['usuarios'][$i] = array('Total' => $row[$i]['Total'], 'Usuario' => $row[$i]['Usuario']);
          }
        }
      }

      $arreglo['registros_pagos_ventas'] = array();

      $query = "SELECT FK_Venta, Monto, Tipo_Pago, IFNULL((SELECT IF(Tipo = 'Moral', Razon_Social, CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido)) FROM clientes WHERE ID_Cliente = (SELECT FK_Cliente FROM ventas WHERE ID_Venta = FK_Venta)), 'Publico en General') AS Cliente FROM ventas_pagos WHERE FK_Detalle_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1)";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $arreglo['registros_pagos_ventas'][$i] = array('Folio' => $row[$i]['FK_Venta'], 'Monto' => $row[$i]['Monto'], 'Tipo_Pago' => $row[$i]['Tipo_Pago'], 'Cliente' => $row[$i]['Cliente']);
          }
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'clientes') {
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
          $busqueda .= "CONCAT(Tipo, Nombre, Primer_Apellido, Segundo_Apellido, RFC, Razon_Social, Telefono, Email) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $query = "SELECT ID_Cliente, Tipo, Nombre, Primer_Apellido, Segundo_Apellido, RFC, Razon_Social, Telefono, Email, (SELECT COUNT(*) FROM clientes $busqueda) AS Num FROM clientes $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $nombre = '<span>' . $row[$i]['Nombre'] . ' ' . $row[$i]['Primer_Apellido'] . ' ' . $row[$i]['Segundo_Apellido'] . '</span>';
            if ($row[$i]['Tipo'] == 'Moral') {
              $nombre = '<span>' . $row[$i]['Razon_Social'] . '</span>';
            }

            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Cliente'],
              'Nombre' => $nombre . '<br><span class="text-muted">RFC: </span>' . $row[$i]['RFC'],
              'Tipo' => $row[$i]['Tipo'],
              'Contacto' => '<span class="text-muted">Tel: </span>' . $row[$i]['Telefono'] . '<br><span class="text-muted">Email: </span>' . $row[$i]['Email']
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }

      echo json_encode($arreglo);
    }
  }

  public function _insertar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $tipo = $omodelo->link->real_escape_string($tipo);
    $fecha = date('Y-m-d H:i:s');

    if ($tipo == 'venta') {
      $idCaja = $omodelo->link->real_escape_string($idCaja);
      $total = $omodelo->link->real_escape_string($total);
      $tipoPago = $omodelo->link->real_escape_string($tipoPago);
      $pago = $omodelo->link->real_escape_string($pago);
      $cambio = $omodelo->link->real_escape_string($cambio);
      $cliente = $omodelo->link->real_escape_string($cliente);
      $fkDireccion = $omodelo->link->real_escape_string($fkDireccion);
      $descuento = $omodelo->link->real_escape_string($descuento);
      $turno = isset($turno) ? $omodelo->link->real_escape_string($turno) : '';

      $estatus = 0;
      if ($pago < $total && $tipoPago == 'Crédito') {
        $estatus = 1;
      }

      $query = "INSERT INTO ventas SET 
        Turno = '$turno', 
        Descuento = '$descuento', 
        Total = '$total', 
        Tipo_Pago = '$tipoPago', 
        Pago = '$pago', 
        Cambio = '$cambio', 
        Fecha_Registro = '$fecha', 
        Estatus = '$estatus', 
        FK_Cliente = '$cliente', 
        FK_Direccion = '$fkDireccion', 
        FK_Detalles_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1), 
        FK_Usuario = '" . $_SESSION['user_punto_venta']['ID_Usuario'] . "'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      } else {
        $idVenta = $omodelo->link->insert_id;

        $productos = json_decode($productos, true);
        foreach ($productos as $producto) {
          $producto['id'] = $omodelo->link->real_escape_string(trim($producto['id']));
          $producto['descripcion'] = $omodelo->link->real_escape_string(trim($producto['descripcion']));
          $producto['precio'] = $omodelo->link->real_escape_string(trim($producto['precio']));
          $producto['cantidad'] = $omodelo->link->real_escape_string(trim($producto['cantidad']));
          $producto['descuento'] = $omodelo->link->real_escape_string(trim($producto['descuento']));
          $producto['total'] = $omodelo->link->real_escape_string(trim($producto['total']));

          $query1 = "INSERT INTO detalles_ventas SET 
            FK_Venta = '$idVenta', FK_Producto = '$producto[id]', 
            Descripcion = '$producto[descripcion]', 
            Precio = '$producto[precio]', 
            Cantidad = '$producto[cantidad]', 
            Descuento = '$producto[descuento]', 
            Total = '$producto[total]', 
            Costo = IFNULL((SELECT Costo FROM productos WHERE ID_Producto = '$producto[id]'), 0)";
          $error1 = $omodelo->_insertar($query1);

          if ($error1 == 'si') {
            echo "Error 2: " . mysqli_error($omodelo->link);
            return;
          } else {
            $idDetalleVenta = $omodelo->link->insert_id;

            foreach ($producto['impuestos'] as $impuesto) {
              $nombreImpuesto = $omodelo->link->real_escape_string(trim($impuesto['nombre']));
              $porcentajeImpuesto = $omodelo->link->real_escape_string(trim($impuesto['porcentaje']));
              $claveCFDI = $omodelo->link->real_escape_string(trim($impuesto['clave']));
              $clase = $omodelo->link->real_escape_string(trim($impuesto['clase']));
              $tipoFactor = $omodelo->link->real_escape_string(trim($impuesto['factor']));

              $query2 = "INSERT INTO impuestos_ventas SET 
                FK_Detalle_Venta = $idDetalleVenta,
                Nombre = '$nombreImpuesto', 
                Porcentaje = '$porcentajeImpuesto',
                Clave_CFDI = '$claveCFDI',
                Clase = '$clase',
                Tipo_Factor = '$tipoFactor'";
              $error2 = $omodelo->_insertar($query2);

              if ($error2 == 'si') {
                echo "Error 3: " . mysqli_error($omodelo->link);
                return;
              }
            }
          }
        }

        echo "Correcto~$idVenta";

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    } else if ($tipo == 'movimiento') {
      $cajaId = $omodelo->link->real_escape_string($cajaId);
      $cantidad = $omodelo->link->real_escape_string($cantidad);
      $tipoMov = $omodelo->link->real_escape_string($tipoMov);
      $descripcion = $omodelo->link->real_escape_string($descripcion);

      $query = "INSERT INTO movimientos_caja SET Monto = '$cantidad', Descripcion = '$descripcion', Fecha_Registro = '$fecha', Tipo = '$tipoMov', FK_Detalle_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$cajaId' ORDER BY ID_Detalle_Caja DESC LIMIT 1)";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }
  }

  public function _modificar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $tipo = $omodelo->link->real_escape_string($tipo);
    $fecha = date('Y-m-d H:i:s');

    if ($tipo == "cerrar") {
      $idCaja = $omodelo->link->real_escape_string($idCaja);
      $fecha_cierre = $omodelo->link->real_escape_string($fecha_cierre);
      $monto = $omodelo->link->real_escape_string($monto);

      $query = "SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $query1 = "UPDATE detalles_caja SET Fecha_Cierre = '$fecha_cierre', Monto_Cierre = '$monto', FK_Usuario_Cierre = '" . $_SESSION['user_punto_venta']['ID_Usuario'] . "' WHERE ID_Detalle_Caja = '" . $row[0]['ID_Detalle_Caja'] . "'";
          $error = $omodelo->_insertar($query1);

          if ($error == 'si') {
            echo "Error 2: " . mysqli_error($omodelo->link);
          } else {
            $query2 = "UPDATE cajas SET Estado = 0, FK_Usuario = 0 WHERE ID_Caja = '$idCaja'";
            $error1 = $omodelo->_insertar($query2);

            if ($error1 == 'si') {
              echo "Error 3: " . mysqli_error($omodelo->link);
            } else {
              echo 'Correcto~' . $row[0]['ID_Detalle_Caja'];

              $omodelo->movimiento($query1, $_SESSION['user_punto_venta']['ID_Usuario']);
            }
          }
        }
      }
    } else if ($tipo == 'dejar') {
      $idCaja = $omodelo->link->real_escape_string($idCaja);

      $query = "UPDATE cajas SET FK_Usuario = 0 WHERE ID_Caja = '$idCaja'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        //historial caja
        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    } else if ($tipo == 'pediente') {
      $idCaja = $omodelo->link->real_escape_string($idCaja);
      $total = $omodelo->link->real_escape_string($total);
      $tipoPago = $omodelo->link->real_escape_string($tipoPago);
      $pago = $omodelo->link->real_escape_string($pago);
      $cambio = $omodelo->link->real_escape_string($cambio);
      $detalles = $omodelo->link->real_escape_string($detalles);
      $cliente = $omodelo->link->real_escape_string($cliente);
      $idVenta = $omodelo->link->real_escape_string($idVenta);

      $estatus = 0;
      if ($pago < $total && $tipoPago == 'Crédito') {
        $estatus = 1;
      }

      $query = "UPDATE ventas SET Estatus = '$estatus', Total = '$total', Tipo_Pago = '$tipoPago', Pago = '$pago', Cambio = '$cambio', Detalles = '$detalles', FK_Cliente = '$cliente', FK_Detalles_Caja = (SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$idCaja' ORDER BY ID_Detalle_Caja DESC LIMIT 1) WHERE ID_Venta = '$idVenta'";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        echo 'Correcto';

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    } else if ($tipo == 'agregar') {
      $id = $omodelo->link->real_escape_string($id);
      $codigo = $omodelo->link->real_escape_string($codigo);
      $cantidad = $omodelo->link->real_escape_string($cantidad);
      $precio = $omodelo->link->real_escape_string($precio);
      $descuento = $omodelo->link->real_escape_string($descuento);

      $query = "SELECT ID_Producto, Descripcion, Precio, IFNULL((SELECT COUNT(*) FROM detalles_ventas INNER JOIN productos ON FK_Producto = ID_Producto WHERE FK_Venta = '$id' AND Codigo = '$codigo'), '0') AS Encontrado FROM productos WHERE Codigo = '$codigo'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      } else {
        $total = ($precio * $cantidad) - $descuento;
        if ($numerofilas > 0) {
          $total = ($row[0]['Precio'] * $cantidad) - $descuento;
        }

        $query1 = "UPDATE ventas SET Total = Total + $total, Cambio = Pago - Total WHERE ID_Venta = '$id'";
        $error = $omodelo->_insertar($query1);

        if ($error == 'si') {
          echo "Error 2: " . mysqli_error($omodelo->link);
        } else {
          if ($numerofilas > 0) {
            if ($row[0]['Encontrado'] == 0) {
              $query2 = "INSERT INTO detalles_ventas SET FK_Venta = '$id', Cantidad = '$cantidad', Descuento = '$descuento', Total = '$total', Descripcion = '" . $row[0]['Descripcion'] . "', Precio = '" . $row[0]['Precio'] . "', FK_Producto = '" . $row[0]['ID_Producto'] . "', Costo = IFNULL((SELECT Costo FROM productos WHERE ID_Producto = '" . $row[0]['ID_Producto'] . "'), 0)";
            } else {
              $query2 = "UPDATE detalles_ventas SET Cantidad = Cantidad + '$cantidad', Descuento = '$descuento', Precio = '" . $row[0]['Precio'] . "', Total = (Cantidad * Precio) - Descuento, Descripcion = '" . $row[0]['Descripcion'] . "' WHERE FK_Venta = '$id' AND FK_Producto = '" . $row[0]['ID_Producto'] . "'";
            }
            $error1 = $omodelo->_insertar($query2);

            if ($error1 == 'si') {
              echo "Error 3: " . mysqli_error($omodelo->link);
            } else {
              echo "Correcto";

              $omodelo->movimiento($query2, $_SESSION['user_punto_venta']['ID_Usuario']);
            }
          } else {
            $query1 = "INSERT INTO detalles_ventas SET FK_Venta = '$id', Descripcion = '$codigo', Cantidad = '$cantidad', Precio = '$precio', Descuento = '$descuento', Total = '$total'";
            $error = $omodelo->_insertar($query1);

            if ($error == 'si') {
              echo "Error 2: " . mysqli_error($omodelo->link);
            } else {
              echo "Correcto";

              $omodelo->movimiento($query1, $_SESSION['user_punto_venta']['ID_Usuario']);
            }
          }
        }
      }
    } else if ($tipo == 'cliente') {
      $idCliente = $omodelo->link->real_escape_string($idCliente);
      $idDomicilio = $omodelo->link->real_escape_string($idDomicilio);
      $telefono = $omodelo->link->real_escape_string($telefono);
      $nombre = $omodelo->link->real_escape_string($nombre);
      $calle = $omodelo->link->real_escape_string($calle);
      $noExterior = $omodelo->link->real_escape_string($noExterior);
      $noInterior = $omodelo->link->real_escape_string($noInterior);
      $cp = $omodelo->link->real_escape_string($cp);
      $colonia = $omodelo->link->real_escape_string($colonia);
      $ciudad = $omodelo->link->real_escape_string($ciudad);
      $estado = $omodelo->link->real_escape_string($estado);
      $pais = $omodelo->link->real_escape_string($pais);
      $detalles = $omodelo->link->real_escape_string($detalles);

      $id = $idCliente;
      if ($idCliente == '') {
        $query = "INSERT INTO clientes SET Tipo = 'Física', Nombre = '$nombre', Telefono = '$telefono', Fecha_Registro = NOW()";
        $error = $omodelo->_insertar($query);

        if ($error == 'si') {
          echo "Error 1: " . mysqli_error($omodelo->link);
        } else {
          $id = mysqli_insert_id($omodelo->link);
          $idDomicilio = '0';

          $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
        }
      }

      if ($idDomicilio == '') {
        $query = "INSERT INTO direcciones_cliente SET FK_Cliente = '$id', Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', CP = '$cp', Colonia = '$colonia', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Detalles = '$detalles'";
        $error = $omodelo->_insertar($query);

        if ($error == 'si') {
          echo "Error 2: " . mysqli_error($omodelo->link);
        } else {
          $idDomicilio = mysqli_insert_id($omodelo->link);

          $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
        }
      } else {
        if ($idDomicilio != '0') {
          $query = "UPDATE direcciones_cliente SET Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', CP = '$cp', Colonia = '$colonia', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Detalles = '$detalles' WHERE ID_Direccion = '$idDomicilio' AND FK_Cliente = '$id'";
        } else {
          $query = "UPDATE clientes SET Calle = '$calle', No_Exterior = '$noExterior', No_Interior = '$noInterior', CP = '$cp', Colonia = '$colonia', Ciudad = '$ciudad', Estado = '$estado', Pais = '$pais', Detalles = '$detalles' WHERE ID_Cliente = '$id'";
        }

        $error = $omodelo->_insertar($query);

        if ($error == 'si') {
          echo "Error 3: " . mysqli_error($omodelo->link);
        } else {
          $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
        }
      }

      echo "Correcto~$id~$idDomicilio";
    }
  }

  public function _detalles()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $arreglo = array();
    $tipo = $omodelo->link->real_escape_string($tipo);

    if ($tipo == 'consultarCaja') {
      $query = "SELECT ID_Caja, Nombre FROM cajas WHERE Estado = 1 AND FK_Usuario = '" . $_SESSION['user_punto_venta']['ID_Usuario'] . "'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $arreglo = array(
            'ID_Caja' => $row[0]['ID_Caja'],
            'Nombre' => $row[0]['Nombre']
          );

          echo json_encode($arreglo);
        } else {
          echo 'No';
        }
      }
    } else if ($tipo == 'ventas') {
      $buscar = trim($omodelo->link->real_escape_string($buscar));
      $limit = $omodelo->link->real_escape_string($limit);
      $pagina = $omodelo->link->real_escape_string($pagina);
      $ordenColumna = $omodelo->link->real_escape_string($ordenColumna);
      $orden = $omodelo->link->real_escape_string($orden);

      $busqueda = '';
      if (trim($buscar) != '') {
        $separa = explode(' ', trim($buscar));
        $busqueda = 'AND ';
        for ($i = 0; $i < count($separa); $i++) {
          $busqueda .= "CONCAT(
            ID_Venta, 
            DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), 
            Total, 
            Pago, 
            Cambio, 
            Tipo_Pago, 
            Detalles) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $query = "SELECT 
        ID_Venta, 
        ID_Venta AS Folio, 
        Total, 
        Pago, 
        Cambio, 
        Tipo_Pago, 
        Fecha_Registro AS Fecha, 
        DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, 
        Estatus, 
        Detalles, 
        (SELECT COUNT(*) FROM ventas WHERE Estatus = 1 $busqueda) AS Num 
      FROM ventas WHERE Estatus = 1 $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error 1: ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $detalles = '<p>' . $row[$i]['Detalles'] . '</p>
            <table class="table" width="100%">
              <thead>
                <tr>
                  <th>Cod.</th>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Precio</th>
                  <th>Descuento</th>
                  <th>Total</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>';
            $query1 = "SELECT ID_Detalle_Venta, IFNULL(Codigo, '') AS Codigo, detalles_ventas.Descripcion AS Descripcion, detalles_ventas.Precio AS Precio, Cantidad, Descuento, Total FROM detalles_ventas LEFT JOIN productos ON FK_Producto = ID_Producto WHERE FK_Venta = '" . $row[$i]['ID_Venta'] . "'";
            $row1 = $omodelo->_consultar($query1);
            $numerofilas1 = $omodelo->numerofilas;

            if ($row1 == 'si') {
              echo 'Error 2: ' . mysqli_error($omodelo->link);
            } else {
              if ($numerofilas1 > 0) {
                for ($x = 0; $x < $numerofilas1; $x++) {
                  $detalles .= '<tr>
                    <td>' . $row1[$x]['Codigo'] . '</td>
                    <td>' . $row1[$x]['Descripcion'] . '</td>
                    <td><span class="cantidad">' . $row1[$x]['Cantidad'] . '</span></td>
                    <td><span class="dinero">' . $row1[$x]['Precio'] . '</span></td>
                    <td><span class="dinero">' . $row1[$x]['Descuento'] . '</span></td>
                    <td><span class="dinero">' . $row1[$x]['Total'] . '</span></td>
                    <td><button type="button" class="btn btn-sm btn-danger bQuitarProducto" attrID="' . $row1[$x]['ID_Detalle_Venta'] . '" title="Quitar producto"><i class="fas fa-trash"></i></button></td>
                  </tr>';
                }
              }
            }

            $detalles .= '<tr>
                  <td colspan="2"><input style="border-radius: 5px;" type="text" class="form-control form-control-sm" id="codigoProdMas" placeholder="Código/Prod. Comun"></td>
                  <td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="cantidadProdMas" placeholder="Cantidad" value="1"></td>
                  <td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="precioProdMas" placeholder="Precio" value="0"></td>
                  <td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="descuentoProdMas" placeholder="Descuento" value="0"></td>
                  <td></td>
                  <td><button type="button" class="btn btn-sm btn-success bAgregarProductoMas" attrID="' . $row[$i]['ID_Venta'] . '" title="Agregar producto"><i class="fas fa-plus"></i></button></td>
                </tr>
              </tbody>
            </table>';

            $estatus = "<span class='badge rounded-pill bg-success'>Completada</span>";

            if ($row[$i]['Estatus'] == 1) {
              $estatus = "<span class='badge rounded-pill bg-warning'>Pendiente</span>";
            } elseif ($row[$i]['Estatus'] == 2) {
              $estatus = "<span class='badge rounded-pill bg-danger'>Cancelada</span>";
            }

            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Venta'],
              'Fecha' => $row[$i]['Fecha_Registro'],
              'Folio' => $row[$i]['Folio'],
              'Tipo' => $row[$i]['Tipo_Pago'],
              'Total' => '<span class="dinero">' . $row[$i]['Total'] . '</span>',
              'Pago' => '<span class="dinero">' . $row[$i]['Pago'] . '</span>',
              'Cambio' => '<span class="dinero">' . $row[$i]['Cambio'] . '</span>',
              'Estatus' => $estatus,
              'Detalles' => $detalles,
              'Acciones' => '<button type="button" class="btn btn-sm btn-warning bCancelarVentaPen" attrID="' . $row[$i]['ID_Venta'] . '" title="Cancelar venta"><i class="fas fa-ban"></i></button>'
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'prodTienda') {
      $idCaja = $omodelo->link->real_escape_string($idCaja);
      $clasificacion = $omodelo->link->real_escape_string($clasificacion);
      $productos = "";

      $query = "SELECT 
        ID_Producto, 
        Codigo, 
        Descripcion, 
        Clase, 
        Costo, 
        Precio, 
        Precio_Mayoreo, 
        Foto, 
        IFNULL(Cantidad, 0) AS Existencia 
      FROM productos LEFT JOIN inventario ON FK_Producto = ID_Producto 
      WHERE FK_Clasificacion = '$clasificacion' ORDER BY Descripcion";

      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $foto = 'vistas/assets/images/producto-generico.png';
            if ($row[$i]['Foto'] != '' && file_exists('vistas/assets/images/productos/' . $row[$i]['Foto'])) {
              $foto = 'vistas/assets/images/productos/' . $row[$i]['Foto'];
            }

            $idProd = $row[$i]['ID_Producto'];
            $qImpuestos = "SELECT 
              i.ID_Impuesto, 
              i.Nombre, 
              i.Porcentaje, 
              i.Tipo_Factor, 
              i.Clase, 
              i.Clave_CFDI 
            FROM impuestos_productos ip
            INNER JOIN impuestos i ON ip.FK_Impuesto = i.ID_Impuesto 
            WHERE ip.FK_Producto = '$idProd'";

            $resImpuestos = $omodelo->_consultar($qImpuestos);
            $numImp = $omodelo->numerofilas;

            $arrayImpuestos = [];
            if ($resImpuestos != 'no' && $resImpuestos != 'si') {
              for ($j = 0; $j < $numImp; $j++) {
                $arrayImpuestos[] = [
                  'FK_Impuesto' => $resImpuestos[$j]['ID_Impuesto'],
                  'Nombre'      => $resImpuestos[$j]['Nombre'],
                  'Porcentaje'  => floatval($resImpuestos[$j]['Porcentaje']),
                  'Tipo_Factor' => $resImpuestos[$j]['Tipo_Factor'],
                  'Clase'       => $resImpuestos[$j]['Clase'],
                  'Clave'       => $resImpuestos[$j]['Clave_CFDI']
                ];
              }
            }

            $jsonImpuestos = htmlspecialchars(json_encode($arrayImpuestos), ENT_QUOTES, 'UTF-8');

            $productos .= '<div class="col-3">
              <div class="prodTienda"
                attrID="' . $row[$i]['ID_Producto'] . '" 
                attrCodigo="' . $row[$i]['Codigo'] . '" 
                attrPrecio="' . $row[$i]['Precio'] . '" 
                attrPrecioMayoreo="' . $row[$i]['Precio_Mayoreo'] . '" 
                attrClase="' . $row[$i]['Clase'] . '" 
                attrExistencia="' . $row[$i]['Existencia'] . '" 
                attrImpuestos=\'' . $jsonImpuestos . '\'
                style="background-image: url(' . "'" . $foto . "'" . '); background-size: cover; background-position: center; border: 1px solid #EEE; border-radius: 10px; height: 120px; cursor: pointer; color: #FFF; margin-bottom: 15px;">

                <div style="height: 100%; width: 100%; background-color: rgba(0, 0, 0, 0.7); border-radius: 10px; display: flex; align-items: center;">
                  <p class="text-center" style="font-size: 14px; margin: 0; width: 100%;">
                    <span>' . $row[$i]['Descripcion'] . '</span><br>
                    <span class="dinero" style="font-size: 20px;">' . number_format($row[$i]['Precio'], 2) . '</span>
                  </p>
                </div>

              </div>
            </div>';
          }
        }
      }
      echo $productos;
    } else if ($tipo == 'clasificaciones') {
      $clasificaciones = '';

      $query = "SELECT ID_Clasificacion, Nombre, Foto FROM clasificaciones WHERE (SELECT COUNT(*) FROM productos WHERE FK_Clasificacion = ID_Clasificacion) > 0 ORDER BY Nombre";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $foto = 'vistas/assets/images/fondo.jpg';

            if ($row[$i]['Foto'] != '' && file_exists('vistas/assets/images/clasificaciones/' . $row[$i]['Foto'])) {
              $foto = 'vistas/assets/images/clasificaciones/' . $row[$i]['Foto'];
            }

            $clasificaciones .= '<div class="col-3">
              <div class="claTienda" style="background-image: url(' . "'" . $foto . "'" . '); background-size: cover; background-position: center; border: 1px solid #EEE; border-radius: 10px; height: 120px; cursor: pointer; color: #FFF;" attrID="' . $row[$i]['ID_Clasificacion'] . '"">
                <div style="height: 100%; width: 100%; background-color: rgba(0, 0, 0, 0.7); border-radius: 10px; display: flex; align-items: center;">
                  <p class="text-center" style="font-size: 20px; margin: 0; width: 100%;">' . $row[$i]['Nombre'] . '</p>
                </div>
              </div>
            </div>';
          }
        }
      }

      echo $clasificaciones;
    } else if ($tipo == 'buscarTelefono') {
      $buscar = $omodelo->link->real_escape_string($buscar);

      $busqueda = '';
      if (trim($buscar) != '') {
        $separa = explode(' ', trim($buscar));
        $busqueda = 'WHERE ';
        for ($i = 0; $i < count($separa); $i++) {
          $busqueda .= "CONCAT(Telefono) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $clientes = '';
      $query = "SELECT ID_Cliente, Telefono, Nombre FROM clientes $busqueda LIMIT 25";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == "si") {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $clientes .= '<div class="lista itemTelefonoBus row" attrID="' . $row[$i]['ID_Cliente'] . '">
              <div class="col-12" style="align-items: center;"><span>' . $row[$i]['Telefono'] . '</span><span>' . $row[$i]['Nombre'] . '</span></div>
            </div>';
          }
        }
      }

      echo $clientes;
    } else if ($tipo == 'buscarNombre') {
      $buscar = $omodelo->link->real_escape_string($buscar);

      $busqueda = '';
      if (trim($buscar) != '') {
        $separa = explode(' ', trim($buscar));
        $busqueda = 'WHERE ';
        for ($i = 0; $i < count($separa); $i++) {
          $busqueda .= "CONCAT(Nombre) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $clientes = '';
      $query = "SELECT ID_Cliente, Telefono, Nombre FROM clientes $busqueda LIMIT 25";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == "si") {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $clientes .= '<div class="lista itemNombreBus row" attrID="' . $row[$i]['ID_Cliente'] . '">
              <div class="col-12" style="align-items: center;"><span>' . $row[$i]['Nombre'] . '</span><span>' . $row[$i]['Telefono'] . '</span></div>
            </div>';
          }
        }
      }

      echo $clientes;
    } else if ($tipo == 'domicilios') {
      $id = $omodelo->link->real_escape_string($id);

      $domicilios = '';
      $query = "SELECT Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais FROM clientes WHERE ID_Cliente = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == "si") {
        echo "Error 1: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $direccion = $row[0]['Calle'] . ' #' . $row[0]['No_Exterior'];

          if ($row[0]['No_Interior'] != '') {
            $direccion .= ' int.' . $row[0]['No_Interior'];
          }

          if ($row[0]['Colonia'] != '') {
            $direccion .= ', ' . $row[0]['Colonia'];
          }

          if ($row[0]['CP'] != '') {
            $direccion .= ' ' . $row[0]['CP'];
          }

          if ($row[0]['Ciudad'] != '') {
            $direccion .= ', ' . $row[0]['Ciudad'];
          }

          if ($row[0]['Estado'] != '') {
            $direccion .= ' ' . $row[0]['Estado'];
          }

          if ($row[0]['Pais'] != '') {
            $direccion .= ', ' . $row[0]['Pais'];
          }

          $domicilios .= '<option value="0">' . $direccion . '</option>';
        }
      }

      $query = "SELECT ID_Direccion, Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais FROM direcciones_cliente WHERE FK_Cliente = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == "si") {
        echo "Error 2: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $direccion = $row[$i]['Calle'] . ' #' . $row[$i]['No_Exterior'];

            if ($row[$i]['No_Interior'] != '') {
              $direccion .= ' int.' . $row[$i]['No_Interior'];
            }

            if ($row[$i]['Colonia'] != '') {
              $direccion .= ', ' . $row[$i]['Colonia'];
            }

            if ($row[$i]['CP'] != '') {
              $direccion .= ' ' . $row[$i]['CP'];
            }

            if ($row[$i]['Ciudad'] != '') {
              $direccion .= ', ' . $row[$i]['Ciudad'];
            }

            if ($row[$i]['Estado'] != '') {
              $direccion .= ' ' . $row[$i]['Estado'];
            }

            if ($row[$i]['Pais'] != '') {
              $direccion .= ', ' . $row[$i]['Pais'];
            }

            $domicilios .= '<option value="' . $row[$i]['ID_Direccion'] . '">' . $direccion . '</option>';
          }
        }
      }

      echo $domicilios;
    } else if ($tipo == 'domicilio') {
      $id = $omodelo->link->real_escape_string($id);
      $cliente = $omodelo->link->real_escape_string($cliente);

      if ($id != '0') {
        $query = "SELECT Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais, Detalles FROM direcciones_cliente WHERE ID_Direccion = '$id'";
      } else {
        $query = "SELECT Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais, Detalles FROM clientes WHERE ID_Cliente = '$cliente'";
      }

      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == "si") {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $arreglo = array('Calle' => $row[0]['Calle'], 'No_Exterior' => $row[0]['No_Exterior'], 'No_Interior' => $row[0]['No_Interior'], 'CP' => $row[0]['CP'], 'Colonia' => $row[0]['Colonia'], 'Ciudad' => $row[0]['Ciudad'], 'Estado' => $row[0]['Estado'], 'Pais' => $row[0]['Pais'], 'Detalles' => $row[0]['Detalles']);
        }

        echo json_encode($arreglo);
      }
    }
  }

  public function _eliminar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $tipo = $omodelo->link->real_escape_string($tipo);
    $fecha = date('Y-m-d H:i:s');

    if ($tipo == 'quitar') {
      $id = $omodelo->link->real_escape_string($id);

      $query = "UPDATE ventas SET Total = Total - (SELECT Total FROM detalles_ventas WHERE ID_Detalle_Venta = '$id'), Cambio = Pago - Total WHERE ID_Venta = (SELECT FK_Venta FROM detalles_ventas WHERE ID_Detalle_Venta = '$id')";
      $error = $omodelo->_insertar($query);

      if ($error == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
      } else {
        $query1 = "DELETE FROM detalles_ventas WHERE ID_Detalle_Venta = '$id'";
        $error1 = $omodelo->_insertar($query1);

        if ($error1 == 'si') {
          echo "Error 2: " . mysqli_error($omodelo->link);
        } else {
          echo 'Correcto';

          $omodelo->movimiento($query1, $_SESSION['user_punto_venta']['ID_Usuario']);
        }
      }
    }
  }
}
