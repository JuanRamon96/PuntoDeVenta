<?php
class Ventas
{

  public function _consultar()
  {
    $omodelo = new m_modelo();
    extract($_POST);

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
        $busqueda .= "CONCAT(ID_Venta, DATE_FORMAT(ventas.Fecha_Registro, '%d-%m-%Y %r'), ventas.Detalles, Descuento, Total, Pago, Tipo_Pago, Cambio, Turno, IFNULL(cajas.Nombre, 'NA'), IFNULL(Tipo, ''),  IFNULL(clientes.Nombre, ''),  IFNULL(Primer_Apellido, ''),  IFNULL(Segundo_Apellido, ''),  IFNULL(RFC, ''),  IFNULL(Razon_Social, ''),  IFNULL(Telefono, ''),  IFNULL(Email, ''), IF(Estatus = 0, 'Completada', IF(Estatus = 1, 'Pendiente', 'Cancelada')), IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = cajas.FK_Sucursal), 'NA')) REGEXP '" . $separa[$i] . "'";
        if ($i < (count($separa) - 1)) {
          $busqueda .= ' AND ';
        }
      }
    }

    $query = "SELECT 
      ID_Venta, 
      IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = cajas.FK_Sucursal), 'NA') AS Sucursal, 
      Turno, 
      ventas.Detalles AS Detalles, 
      Descuento, 
      ID_Venta AS Folio, 
      ventas.FK_Usuario, 
      FK_Detalles_Caja, 
      Total, 
      Pago, 
      Tipo_Pago, 
      Cambio, 
      ventas.Fecha_Registro AS Fecha, 
      DATE_FORMAT(ventas.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, 
      Estatus, 
      DATE_FORMAT(Fecha_Cancelacion, '%d-%m-%Y %r') AS Fecha_Cancelacion, 
      Regrezo_Inventario, 
      IFNULL(cajas.Nombre, 'NA') AS Caja, 
      FK_Cliente, 
      Tipo, 
      IF(Tipo = 'Moral', Razon_Social, CONCAT(clientes.Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido)) AS Cliente, 
      RFC, 
      Regimen_CFDI,
      Telefono, 
      Email, 
      CP,
      (Pago - Cambio) + IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ID_Venta), 0) AS TotalPagado, 
      (SELECT COUNT(*) FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja LEFT JOIN clientes ON FK_Cliente = ID_Cliente $busqueda) AS Num 
    FROM ventas LEFT JOIN detalles_caja ON FK_Detalles_caja = ID_Detalle_Caja LEFT JOIN cajas ON ID_Caja = FK_Caja 
    LEFT JOIN clientes ON FK_Cliente = ID_Cliente $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo 'Error: ' . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        for ($i = 0; $i < $numerofilas; $i++) {
          $bModificar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Ventas'][2] == '1') {
            $bModificar = '<button class="btn btn-warning btn-sm bModificarVenta" attrID="' . $row[$i]['ID_Venta'] . '" title="Modificar Venta"><i class="fas fa-pencil"></i></button>';
          }

          $bEliminar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Ventas'][3] == '1') {
            $bEliminar = '<button class="btn btn-danger btn-sm bEliminarVenta" attrID="' . $row[$i]['ID_Venta'] . '" title="Eliminar Venta"><i class="fa-solid fa-trash"></i></button>';
          }

          $bCancelar = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Ventas'][4] == '1') {
            $bCancelar = '<button class="btn btn-outline-danger btn-sm bCancelarVenta" attrID="' . $row[$i]['ID_Venta'] . '" title="Cancelar Venta"><i class="fa fa-ban"></i></button>';
          }

          $bImprimir = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Ventas'][5] == '1') {
            $bImprimir = '<a class="btn btn-sm btn-primary" href="controladores/pdf/ticketVenta.php?id=' . $row[$i]['ID_Venta'] . '" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-ticket"></i></a>';
          }

          $verPagos = '';
          $bPagos = '';
          if (($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Ventas'][6] == '1') && $row[$i]['Tipo_Pago'] == 'Crédito') {
            $bPagos = '<button class="btn btn-info btn-sm bPagosVenta" attrID="' . $row[$i]['ID_Venta'] . '" title="Agregar Pago"><i class="fa fa-dollar"></i></button>';

            $verPagos = '<button class="btn btn-link btn-sm bVerPagosVen" attrID="' . $row[$i]['ID_Venta'] . '" title="Ver Pagos">Ver Pagos</i></button>';
          }

          $bDevoluciones = '';
          if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Ventas'][7] == '1') {
            $bDevoluciones = '<button class="btn btn-secondary btn-sm bDevoluciones" attrID="' . $row[$i]['ID_Venta'] . '" title="Devoluciones" ><i class="fa-solid fa-arrow-right-from-bracket"></i></i></button>';
          }

          $detalles = '<div class="d-flex align-items-center justify-content-evenly">
            <div class="d-flex flex-column"><span><strong>Caja:&nbsp;</strong>' . $row[$i]['Caja'] . '</span>
              <button class="btn btn-link btn-sm bDetalles" idVenta="' . $row[$i]['ID_Venta'] . '" title="Ver Detalles">Ver Detalles</i></button>
              ' . (isset($factura) && $factura == 'true' ? '' : $verPagos . ' ' . $bImprimir) . '
              ' . (trim($row[$i]['Detalles']) != '' ? '<br>Detalles: ' . trim($row[$i]['Detalles']) : '') . '
            </div>
          </div>';

          $estatus = "<span class='badge rounded-pill bg-success'>Completada</span>";

          if ($row[$i]['Estatus'] == 1) {
            $estatus = "<span class='badge rounded-pill bg-warning'>Pendiente</span>";
          } elseif ($row[$i]['Estatus'] == 2) {
            $estatus = "<span class='badge rounded-pill bg-danger'>Cancelada</span>";
            $bCancelar = '';
            $bModificar = '';
            $bPagos = '';
            $bDevoluciones = '';
          }

          $cliente = 'Publico en General';
          if ($row[$i]['FK_Cliente'] != 0) {
            $cliente = '<span class="nombre">' . $row[$i]['Cliente'] . '</span><br>
            <span class="text-muted">Tipo: </span>' . $row[$i]['Tipo'] . '<br>
            <span class="text-muted">RFC: </span><span class="rfc">' . $row[$i]['RFC'] . '</span><br>
            ' . ($row[$i]['Regimen_CFDI'] != '' ? '<span class="text-muted">Régimen: </span><span class="regimen">' . $row[$i]['Regimen_CFDI'] . '</span><br>' : '') . '
            <span class="text-muted">CP: </span><span class="cp">' . $row[$i]['CP'] . '</span><br>
            <span class="text-muted">Tel: </span>' . $row[$i]['Telefono'] . '<br>
            <span class="text-muted">Email: </span><span class="email">' . $row[$i]['Email'] . '</span>';
          }

          $afavor = 0;
          $restante = $row[$i]['Total'] - $row[$i]['TotalPagado'];
          if ($restante < 0) {
            $restante = 0;
            $afavor = $row[$i]['TotalPagado'] - $row[$i]['Total'];
          }

          $totalPagos = '<br><b>Pagado: </b><span class="dinero">' . $row[$i]['TotalPagado'] . '</span><br><b>Restante: </b><span class="dinero">' . $restante . '</span><br><b>A favor: </b><span class="dinero">' . $afavor . '</span>';

          $arreglo['data'][$i] = array(
            'ID' => $row[$i]['ID_Venta'],
            'Fecha' => $row[$i]['Fecha_Registro'] . ($row[$i]['Turno'] != '' ? '<br><b>Turno: </b>' . $row[$i]['Turno'] : ''),
            'Folio' => $row[$i]['Folio'],
            'Tipo' => $row[$i]['Tipo_Pago'],
            'Cliente' => $cliente,
            'Total' => 'Subtotal: <span class="dinero">' . ($row[$i]['Total'] + $row[$i]['Descuento']) . '</span><br>Descuento: <span class="dinero">' . $row[$i]['Descuento'] . '</span><br>Total: <span class="dinero" style="font-size: 22px;">' . $row[$i]['Total'] . '</span>',
            'Pago' => '<span class="dinero">' . $row[$i]['Pago'] . '</span>',
            'Cambio' => '<span class="dinero">' . $row[$i]['Cambio'] . '</span>',
            'Estatus' => $estatus . $totalPagos,
            'Detalles' => $detalles,
            'Sucursal' => $row[$i]['Sucursal'],
            'Acciones' => $bModificar . ' ' . $bEliminar . ' ' . $bCancelar . ' ' . $bPagos . ' ' . $bDevoluciones
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
    $fecha = date('Y-m-d H:i:s');
    $tipo = $omodelo->link->real_escape_string($tipo);

    if ($tipo == 'venta') {
      $idVenta = $omodelo->link->real_escape_string($idVenta);

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
          $busqueda .= "CONCAT(Descripcion, Precio, Cantidad, Descuento, Total) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $query = "SELECT 
        ID_Detalle_Venta, 
        FK_Venta, 
        FK_Producto, 
        Descripcion AS Producto, 
        Precio, 
        Cantidad, 
        Descuento, 
        Total, 
        (SELECT COUNT(*) FROM detalles_ventas WHERE FK_Venta = '$idVenta' $busqueda) AS Num 
      FROM detalles_ventas WHERE FK_Venta = '$idVenta' $busqueda 
      ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error: ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $subDesc = $row[$i]['Precio'] * $row[$i]['Cantidad'];
            $subtotal = ($row[$i]['Precio'] * $row[$i]['Cantidad']) - $row[$i]['Descuento'];

            $impuestos = '<br>Sin impuestos';
            $query1 = "SELECT 
              ID_Impuesto_Venta, 
              Nombre, 
              Porcentaje,
              Clave_CFDI,
              Tipo_Factor,
              Clase
            FROM impuestos_ventas WHERE FK_Detalle_Venta = '" . $row[$i]['ID_Detalle_Venta'] . "' ORDER BY Nombre ASC";
            $row1 = $omodelo->_consultar($query1);
            $numerofilas1 = $omodelo->numerofilas;

            if ($row1 == 'si') {
              echo "Error 2: " . mysqli_error($omodelo->link);
            } else {
              if ($numerofilas1 > 0) {
                $impuestos = '';
                for ($j = 0; $j < $numerofilas1; $j++) {
                  $impuestos .= '<p class="m-0"><b>' . $row1[$j]['Clase'] . ' ' . $row1[$j]['Nombre'] . ' <span class="porcentaje">' . $row1[$j]['Porcentaje'] . '</span></b> (<span class="dinero">' . ($subtotal * ($row1[$j]['Porcentaje'] / 100)) . '</span>) - <b>' . $row1[$j]['Tipo_Factor'] . '</b></p>';
                }
              }
            }

            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Detalle_Venta'],
              'Producto' => $row[$i]['Producto'],
              'Precio' => '<span class="dinero">' . $row[$i]['Precio'] . '</span>',
              'Cantidad' => '<span class="cantidad">' . $row[$i]['Cantidad'] . '</span>',
              'Descuento' => '<span class="dinero">' . $row[$i]['Descuento'] . '</span> (<span class="porcentaje">' . (($row[$i]['Descuento'] / $subDesc) * 100) . '%</span>)',
              'Impuestos' => '<b>SUB: <span class="dinero">' . $subtotal . '</span></b>' . $impuestos,
              'Total' => '<span class="dinero">' . $row[$i]['Total'] . '</span>'
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'totales') {
      $id = $omodelo->link->real_escape_string($id);
      $arreglo = array();

      $query = "SELECT Total, Pago, Cambio, IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = '$id'), 0) AS TotalPagos FROM ventas WHERE ID_Venta = '$id'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error: ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $arreglo = array('Total' => $row[0]['Total'], 'Pago' => $row[0]['Pago'], 'Cambio' => $row[0]['Cambio'], 'TotalPagos' => $row[0]['TotalPagos']);
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'pago') {
      $id = $omodelo->link->real_escape_string($id);
      $importePagoVenta = $omodelo->link->real_escape_string(trim($importePagoVenta));
      $conceptoPagoVenta = $omodelo->link->real_escape_string(trim($conceptoPagoVenta));
      $tipoDePagoVenta = $omodelo->link->real_escape_string(trim($tipoDePagoVenta));
      $detallesPagoVenta = $omodelo->link->real_escape_string(trim($detallesPagoVenta));
      $cajaPagoVenta = $omodelo->link->real_escape_string(trim($cajaPagoVenta));

      $query = "INSERT INTO ventas_pagos SET FK_Venta = '$id', Concepto = '$conceptoPagoVenta', Monto = '$importePagoVenta', Tipo_Pago = '$tipoDePagoVenta', Detalles = '$detallesPagoVenta', FK_Detalle_Caja = IFNULL((SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = '$cajaPagoVenta' ORDER BY ID_Detalle_Caja DESC LIMIT 1), 0), Fecha_Registro = '$fecha', FK_Usuario = '" . $_SESSION['user_punto_venta']['ID_Usuario'] . "'";
      $error = $omodelo->_insertar($query);
      $status = 0;

      if ($error == 'si') {
        echo "Error 1: " . mysqli_error($omodelo->link);
        $status = 1;
      } else {
        $idPago = $omodelo->link->insert_id;

        $nombreImg = '';
        $ruta = '';
        $rutaProvisional = '';
        $carpeta = 'vistas/assets/files/pagosVentas/';
        if ($_FILES['comprobantePagoVenta']['size'] > 0 && $_FILES['comprobantePagoVenta']['error'] == 0) {
          $file = $_FILES['comprobantePagoVenta'];
          $nombreImg = $file['name'];
          $tipoImg = $file['type'];
          $rutaProvisional = $file['tmp_name'];
          $sizeImg = $file['size'];

          if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != 'application/pdf' && $tipoImg != '') {
            echo 'Error 2 formato ' . $tipoImg;
            $status = 1;
          } else if ($sizeImg > (1024 * 1024 * 10)) {
            echo 'Error 3 peso';
            $status = 1;
          } else {
            $ruta = $carpeta . $idPago . '_' . $nombreImg;
          }

          if ($status == 0 && $nombreImg != '') {
            $query = "UPDATE ventas_pagos SET Archivo = '" . $idPago . '_' . $nombreImg . "'  WHERE ID_Pago = '$idPago'";
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
    } else if ($tipo == 'pagos') {
      $id = $omodelo->link->real_escape_string($id);

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
          $busqueda .= "CONCAT(DATE_FORMAT(ventas_pagos.Fecha_Registro, '%d-%m-%Y %r'), Concepto, Monto, Tipo_Pago, ventas_pagos.Detalles, cajas.Nombre, usuarios.Nombre, Primer_Apellido, Segundo_Apellido) REGEXP '" . $separa[$i] . "'";
          if ($i < (count($separa) - 1)) {
            $busqueda .= ' AND ';
          }
        }
      }

      $query = "SELECT ID_Pago, FK_Venta, Concepto, Monto, Tipo_Pago, ventas_pagos.Detalles AS Detalles, IFNULL(cajas.Nombre, '') AS Caja, Archivo, ventas_pagos.Fecha_Registro AS Fecha, DATE_FORMAT(ventas_pagos.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, CONCAT(usuarios.Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) AS Usuario, (SELECT COUNT(*) FROM ventas_pagos LEFT JOIN detalles_caja ON FK_Detalle_Caja = ID_Detalle_Caja LEFT JOIN cajas ON FK_Caja = ID_Caja LEFT JOIN usuarios ON ventas_pagos.FK_Usuario = ID_Usuario WHERE FK_Venta = '$id' $busqueda) AS Num FROM ventas_pagos LEFT JOIN detalles_caja ON FK_Detalle_Caja = ID_Detalle_Caja LEFT JOIN cajas ON FK_Caja = ID_Caja LEFT JOIN usuarios ON ventas_pagos.FK_Usuario = ID_Usuario WHERE FK_Venta = '$id' $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error: ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $detalles = '';
            if ($row[$i]['Caja'] != '') {
              $detalles .= '<br><span class="text-muted">Caja:</span> ' . $row[$i]['Caja'];
            }

            if (trim($row[$i]['Usuario']) != '') {
              $detalles .= '<br><span class="text-muted">Usuario:</span> ' . $row[$i]['Usuario'];
            }

            $imagen = '';
            if ($row[$i]["Archivo"] != "" && file_exists("vistas/assets/files/pagosVentas/" . $row[$i]["Archivo"])) {
              $imagen = '<a href="vistas/assets/files/pagosVentas/' . $row[$i]["Archivo"] . '" data-fancybox="iframe">
                <i class="fas fa-file" style="font-size: 25px;"></i>
              </a>';
            }

            $arreglo['data'][$i] = array(
              'ID' => $row[$i]['ID_Pago'],
              'Fecha' => $row[$i]['Fecha_Registro'],
              'Concepto' => $row[$i]['Concepto'],
              'Tipo_Pago' => $row[$i]['Tipo_Pago'],
              'Monto' => '<span class="dinero">' . $row[$i]['Monto'] . '</span>',
              'Detalles' => $row[$i]['Detalles'] . $detalles,
              'Comprobante' => $imagen,
              'Acciones' => '<button type="button" class="btn btn-danger btn-sm bEliminarPagoVenta" attrVenta="' . $row[$i]['FK_Venta'] . '" attrID="' . $row[$i]['ID_Pago'] . '" archivo="' . $row[$i]["Archivo"] . '"><i class="fas fa-trash"></i></button> <a href="controladores/pdf/ticketPago.php?id=' . $row[$i]['ID_Pago'] . '" target="_blank" class="btn btn-primary btn-sm bImprimirPagoVenta"><i class="fas fa-print"></i></a>'
            );
          }

          $arreglo['totales'] = array('NumRows' => $row[0]['Num']);
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'eliminarPago') {
      $id = $omodelo->link->real_escape_string($id);
      $archivo = $omodelo->link->real_escape_string($archivo);

      $query = "DELETE FROM ventas_pagos WHERE ID_Pago = '$id'";
      $error = $omodelo->_insertar($query);

      if ($error == "si") {
        echo "Error: " . mysqli_error($omodelo->link);
      } else {
        if ($archivo != "" && file_exists("vistas/assets/files/pagosVentas/" . $archivo)) {
          unlink("vistas/assets/files/pagosVentas/" . $archivo);
        }

        echo "Correcto";
        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    } else if ($tipo == 'devoluciones') {
      $id = $omodelo->link->real_escape_string($id);

      $query = "SELECT ID_Detalle_Venta, FK_Venta, FK_Producto, Descripcion AS Producto, Precio, Cantidad, Descuento, Total, IFNULL((SELECT Cantidad FROM devoluciones WHERE FK_Detalle_Venta = ID_Detalle_Venta), 0) AS CanDev FROM detalles_ventas WHERE FK_Venta = '$id' ORDER BY Producto";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;
      $detalles = '';

      if ($row == 'si') {
        echo 'Error: ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          for ($i = 0; $i < $numerofilas; $i++) {
            $detalles .= '<tr attrID="' . $row[$i]['ID_Detalle_Venta'] . '">
              <td>' . $row[$i]['Producto'] . '</td>
              <td><span class="dinero">' . $row[$i]['Precio'] . '</span></td>
              <td><span class="cantidad">' . $row[$i]['Cantidad'] . '</span></td>
              <td><span class="dinero">' . $row[$i]['Descuento'] . '</span></td>
              <td><span class="dinero">' . $row[$i]['Total'] . '</span></td>
              <td><input type="number" class="form-control" min="0" max="' . $row[$i]['Cantidad'] . '" value="' . $row[$i]['CanDev'] . '" /></td>
            </tr>';
          }
        }
      }

      echo $detalles;
    } else if ($tipo == 'devolucion') {
      $productos = json_decode($productos, true);

      foreach ($productos as $producto) {
        $id = $omodelo->link->real_escape_string($producto['id']);
        $cantidad = $omodelo->link->real_escape_string($producto['cantidad']);

        $query = "SELECT ID_Devolucion FROM devoluciones WHERE FK_Detalle_Venta = '$id'";
        $row = $omodelo->_consultar($query);
        $numerofilas = $omodelo->numerofilas;

        if ($row == 'si') {
          echo 'Error: ' . mysqli_error($omodelo->link);
        } else {
          if ($numerofilas > 0) {
            if ($cantidad > 0) {
              $query1 = "UPDATE devoluciones SET Cantidad = '$cantidad' WHERE ID_Devolucion = '" . $row[0]['ID_Devolucion'] . "'";
              $error = $omodelo->_insertar($query1);

              if ($error == "si") {
                echo "Error: " . mysqli_error($omodelo->link);
              } else {
                $omodelo->movimiento($query1, $_SESSION['user_punto_venta']['ID_Usuario']);
              }
            } else {
              $query1 = "DELETE FROM devoluciones WHERE ID_Devolucion = '" . $row[0]['ID_Devolucion'] . "'";
              $error = $omodelo->_insertar($query1);

              if ($error == "si") {
                echo "Error: " . mysqli_error($omodelo->link);
              } else {
                $omodelo->movimiento($query1, $_SESSION['user_punto_venta']['ID_Usuario']);
              }
            }
          } else {
            $query1 = "INSERT INTO devoluciones SET Cantidad = '$cantidad', FK_Detalle_Venta = '$id'";
            $error = $omodelo->_insertar($query1);

            if ($error == "si") {
              echo "Error: " . mysqli_error($omodelo->link);
            } else {
              $omodelo->movimiento($query1, $_SESSION['user_punto_venta']['ID_Usuario']);
            }
          }
        }
      }

      echo "Correcto";
    } else if ($tipo == 'modificar') {
      $idVenta = $omodelo->link->real_escape_string($idVenta);
      $arreglo = array();

      $query = "SELECT 
        ID_Venta, 
        FK_Usuario, 
        FK_Cliente, 
        IF(clientes.Tipo = 'Moral', 'Razon_Social', CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido)) AS Cliente, 
        FK_Detalles_Caja, 
        Descuento, 
        Total, 
        Tipo_Pago,
        Pago, 
        Cambio, 
        ventas.Detalles, 
        ventas.Fecha_Registro AS Fecha_Registro, 
        Estatus, 
        Fecha_Cancelacion, 
        Regrezo_Inventario 
      FROM ventas LEFT JOIN clientes ON FK_Cliente = ID_Cliente WHERE ID_Venta = '$idVenta'";
      $row = $omodelo->_consultar($query);
      $numerofilas = $omodelo->numerofilas;

      if ($row == 'si') {
        echo 'Error 1: ' . mysqli_error($omodelo->link);
      } else {
        if ($numerofilas > 0) {
          $productos = array();
          $query1 = "SELECT 
            ID_Detalle_Venta, 
            FK_Producto, 
            Codigo, 
            detalles_ventas.Descripcion AS Descripcion, 
            detalles_ventas.Precio AS Precio, 
            Cantidad, 
            Descuento, 
            Total
          FROM detalles_ventas LEFT JOIN productos ON FK_Producto = ID_Producto WHERE FK_Venta = '$idVenta'";
          $row1 = $omodelo->_consultar($query1);
          $numerofilas1 = $omodelo->numerofilas;

          if ($row1 == 'si') {
            echo 'Error 2: ' . mysqli_error($omodelo->link);
          } else {
            if ($numerofilas1 > 0) {
              for ($i = 0; $i < $numerofilas1; $i++) {
                $impuestos = array();
                $query2 = "SELECT 
                  ID_Impuesto_Venta,
                  Nombre, 
                  Porcentaje,
                  Clave_CFDI,
                  Tipo_Factor,
                  Clase
                FROM impuestos_ventas WHERE FK_Detalle_Venta = '" . $row1[$i]['ID_Detalle_Venta'] . "' ORDER BY Nombre ASC";
                $row2 = $omodelo->_consultar($query2);
                $numerofilas2 = $omodelo->numerofilas;

                if ($row2 == 'si') {
                  echo "Error 2: " . mysqli_error($omodelo->link);
                } else {
                  if ($numerofilas2 > 0) {
                    for ($j = 0; $j < $numerofilas2; $j++) {
                      $impuestos[] = array(
                        'ID_Impuesto_Venta' => $row2[$j]['ID_Impuesto_Venta'],
                        'Nombre' => $row2[$j]['Nombre'],
                        'Porcentaje' => $row2[$j]['Porcentaje'],
                        'Clave_CFDI' => $row2[$j]['Clave_CFDI'],
                        'Tipo_Factor' => $row2[$j]['Tipo_Factor'],
                        'Clase' => $row2[$j]['Clase']
                      );
                    }
                  }
                }

                $productos[] = array(
                  'ID_Detalle_Venta' => $row1[$i]['ID_Detalle_Venta'],
                  'FK_Producto' => $row1[$i]['FK_Producto'],
                  'Codigo' => $row1[$i]['Codigo'],
                  'Descripcion' => $row1[$i]['Descripcion'],
                  'Precio' => $row1[$i]['Precio'],
                  'Cantidad' => $row1[$i]['Cantidad'],
                  'Descuento' => $row1[$i]['Descuento'],
                  'Total' => $row1[$i]['Total'],
                  'Impuestos' => $impuestos
                );
              }
            }
          }

          $arreglo = array(
            'ID_Venta' => $row[0]['ID_Venta'],
            'FK_Usuario' => $row[0]['FK_Usuario'],
            'FK_Cliente' => $row[0]['FK_Cliente'],
            'Cliente' => $row[0]['Cliente'],
            'FK_Detalles_Caja' => $row[0]['FK_Detalles_Caja'],
            'Descuento' => $row[0]['Descuento'],
            'Total' => $row[0]['Total'],
            'Tipo_Pago' => $row[0]['Tipo_Pago'],
            'Pago' => $row[0]['Pago'],
            'Cambio' => $row[0]['Cambio'],
            'Detalles' => $row[0]['Detalles'],
            'Fecha_Registro' => $row[0]['Fecha_Registro'],
            'Estatus' => $row[0]['Estatus'],
            'Fecha_Cancelacion' => $row[0]['Fecha_Cancelacion'],
            'Regrezo_Inventario' => $row[0]['Regrezo_Inventario'],
            'Productos' => $productos
          );
        }
      }

      echo json_encode($arreglo);
    } else if ($tipo == 'modificarVenta') {
      $id = $omodelo->link->real_escape_string($id);
      $cliente = $omodelo->link->real_escape_string($cliente);
      $descuento = $omodelo->link->real_escape_string($descuento);
      $total = $omodelo->link->real_escape_string($total);
      $pago = $omodelo->link->real_escape_string($pago);
      $cambio = $omodelo->link->real_escape_string($cambio);
      $tipoPago = $omodelo->link->real_escape_string($tipoPago);
      $detalles = $omodelo->link->real_escape_string($detalles);

      $query = "UPDATE ventas SET FK_Cliente = '$cliente', Descuento = '$descuento', Total = '$total', Tipo_Pago = '$tipoPago', Pago = '$pago', Cambio = '$cambio', Detalles = '$detalles', Estatus = IF((($pago - $cambio) + IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = '$id'), 0)) - $total >= 0, 0, 1) WHERE ID_Venta = '$id'";
      $error = $omodelo->_insertar($query);

      $status = 0;
      if ($error == 'si') {
        echo 'Error 1: ' . mysqli_error($omodelo->link);
        $status = 1;
      } else {
        $query1 = "DELETE FROM detalles_ventas WHERE FK_Venta = '$id'";
        $error1 = $omodelo->_insertar($query1);

        if ($error1 == 'si') {
          echo 'Error 2: ' . mysqli_error($omodelo->link);
          $status = 1;
        } else {
          $productos = json_decode($productos, true);

          foreach ($productos as $producto) {
            $producto['idProd'] = $omodelo->link->real_escape_string($producto['idProd']);
            $producto['descripcion'] = $omodelo->link->real_escape_string($producto['descripcion']);
            $producto['precio'] = $omodelo->link->real_escape_string($producto['precio']);
            $producto['cantidad'] = $omodelo->link->real_escape_string($producto['cantidad']);
            $producto['descuento'] = $omodelo->link->real_escape_string($producto['descuento']);
            $producto['total'] = $omodelo->link->real_escape_string($producto['total']);

            $query2 = "INSERT INTO detalles_ventas SET FK_Venta = '$id', FK_Producto = '$producto[idProd]', Descripcion = '$producto[descripcion]', Precio = '$producto[precio]', Cantidad = '$producto[cantidad]', Descuento = '$producto[descuento]', Total = '$producto[total]'";
            $error2 = $omodelo->_insertar($query2);

            if ($error2 == 'si') {
              echo 'Error 3: ' . mysqli_error($omodelo->link);
              $status = 1;
              return;
            } else {
              $idDetalleVenta = $omodelo->link->insert_id;

              foreach ($producto['impuestos'] as $impuesto) {
                $nombreImpuesto = $omodelo->link->real_escape_string(trim($impuesto['nombre']));
                $porcentajeImpuesto = $omodelo->link->real_escape_string(trim($impuesto['porcentaje']));
                $claveImpuesto = $omodelo->link->real_escape_string(trim($impuesto['clave']));
                $factorImpuesto = $omodelo->link->real_escape_string(trim($impuesto['factor']));
                $claseImpuesto = $omodelo->link->real_escape_string(trim($impuesto['clase']));

                $query2 = "INSERT INTO impuestos_ventas SET 
                  FK_Detalle_Venta = $idDetalleVenta, 
                  Nombre = '$nombreImpuesto', 
                  Porcentaje = '$porcentajeImpuesto',
                  Clave_CFDI = '$claveImpuesto',
                  Tipo_Factor = '$factorImpuesto',
                  Clase = '$claseImpuesto'";
                $error2 = $omodelo->_insertar($query2);

                if ($error2 == 'si') {
                  echo "Error 3: " . mysqli_error($omodelo->link);
                  $status = 1;
                  return;
                }
              }
            }
          }
        }

        if ($status == 0) {
          echo "Correcto";

          $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
        }
      }
    }
  }

  public function _modificar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $fecha = date('Y-m-d H:i:s');

    $idVenta = $omodelo->link->real_escape_string($idVenta);
    $regresarInventario = $omodelo->link->real_escape_string($regresarInventario);

    $query = "UPDATE ventas SET Estatus = 2, Fecha_Cancelacion = '$fecha', Regrezo_Inventario = '$regresarInventario' WHERE ID_Venta = '$idVenta'";
    $error = $omodelo->_insertar($query);

    if ($error == 'si') {
      echo 'Error 1: ' . mysqli_error($omodelo->link);
    } else {
      if ($regresarInventario == '1') {
        $query1 = "SELECT FK_Producto, Cantidad FROM detalles_ventas WHERE FK_Venta = '$idVenta'";
        $row = $omodelo->_consultar($query1);
        $numerofilas = $omodelo->numerofilas;

        if ($row == 'si') {
          echo 'Error 2: ' . mysqli_error($omodelo->link);
        } else {
          if ($numerofilas > 0) {
            for ($i = 0; $i < $numerofilas; $i++) {
              $query2 = "UPDATE inventario SET Cantidad = Cantidad + " . $row[$i]['Cantidad'] . " WHERE FK_Producto = '" . $row[$i]['FK_Producto'] . "'";
              $error2 = $omodelo->_insertar($query2);

              if ($error2 == 'si') {
                echo 'Error 3: ' . mysqli_error($omodelo->link);
              }
            }
          }
        }
      }

      echo 'Correcto';

      $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
    }
  }

  public function _eliminar()
  {
    $omodelo = new m_modelo();
    extract($_POST);
    $idVenta = $omodelo->link->real_escape_string($idVenta);

    $query = "SELECT Archivo FROM ventas_pagos WHERE FK_Venta = '$idVenta' AND Archivo != ''";
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == "si") {
      echo "Error 1: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        for ($i = 0; $i < $numerofilas; $i++) {
          if ($row[$i]["Archivo"] != "" && file_exists("vistas/assets/files/pagosVentas/" . $row[$i]["Archivo"] . "")) {
            unlink("vistas/assets/files/pagosVentas/" . $row[$i]["Archivo"] . "");
          }
        }
      }

      $query1 = "DELETE FROM ventas WHERE ID_Venta = '$idVenta'";
      $error = $omodelo->_insertar($query1);

      if ($error == "si") {
        echo "Error 2: " . mysqli_error($omodelo->link);
      } else {
        echo "Correcto";

        $omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
      }
    }
  }
}
