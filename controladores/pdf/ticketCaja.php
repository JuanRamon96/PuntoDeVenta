<?php
  session_start();
  include '../../modelo/m_modelo.php';
  if (!isset($_SESSION['user_punto_venta'])) return http_response_code(400);
  extract($_GET);

  $omodelo = new m_modelo();
  $id = $omodelo->link->real_escape_string($id);
  $arreglo = array();

  $arreglo['detalles'] = array();
  $query = "SELECT ID_Detalle_Caja, DATE_FORMAT(Fecha_Abrir, '%d-%m-%Y %r') AS Fecha_Abrir, FK_Caja, Monto_Abrir, Monto_Cierre, DATE_FORMAT(Fecha_Cierre, '%d-%m-%Y %r') AS Fecha_Cierre, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario_Abrir), '') AS Usuario_Abrio, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario_Cierre), '') AS Usuario_Cerro FROM detalles_caja JOIN cajas ON FK_Caja = ID_Caja WHERE ID_Detalle_Caja = '$id'";
  $row = $omodelo->_consultar($query);
  $numerofilas = $omodelo->numerofilas;
  
  if ($row == 'si') {
    echo "Error: " . mysqli_error($omodelo->link);
  }else{
    if($numerofilas > 0){
      $arreglo['detalles'] = array('Fecha_Abrir' => $row[0]['Fecha_Abrir'], 'Monto_Abrir' => $row[0]['Monto_Abrir'], 'Fecha_Cierre' => $row[0]['Fecha_Cierre'], 'Monto_Cierre' => $row[0]['Monto_Cierre'], 'Usuario_Abrio' => $row[0]['Usuario_Abrio'], 'Usuario_Cerro' => $row[0]['Usuario_Cerro']);
    } 
  }
  
  $arreglo['ventas'] = array();
  $query = "SELECT SUM(Pago - Cambio) AS Total, Tipo_Pago FROM ventas WHERE FK_Detalles_Caja = '$id' GROUP BY Tipo_Pago";
  $row = $omodelo->_consultar($query);
  $numerofilas = $omodelo->numerofilas;

  if ($row == 'si') {
    echo "Error: " . mysqli_error($omodelo->link);
  }else{
    if($numerofilas > 0){
      for ($i=0; $i < $numerofilas; $i++) { 
        $arreglo['ventas'][$i] = array('Total' => $row[$i]['Total'], 'Tipo_Pago' => $row[$i]['Tipo_Pago']);
      }
    }
  }

  $arreglo['movimientos'] = array();
  $query = "SELECT Tipo, Monto, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha FROM movimientos_caja WHERE FK_Detalle_Caja = '$id' ORDER BY Fecha_Registro DESC";
  $row = $omodelo->_consultar($query);
  $numerofilas = $omodelo->numerofilas;

  if ($row == 'si') {
    echo "Error: " . mysqli_error($omodelo->link);
  }else{
    if($numerofilas > 0){
      for ($i = 0; $i < $numerofilas; $i++) {
        $arreglo['movimientos'][$i] = array('Tipo' => $row[$i]['Tipo'], 'Monto' => $row[$i]['Monto'], 'Fecha_Registro' => $row[$i]['Fecha']);
      }
    }
  }

  $arreglo['compras'] = array();
  $query = "SELECT SUM(Monto) AS Total, Tipo_Pago FROM compras_pagos WHERE FK_Detalle_Caja = '$id' GROUP BY Tipo_Pago";
  $row = $omodelo->_consultar($query);
  $numerofilas = $omodelo->numerofilas;

  if ($row == 'si') {
    echo "Error: " . mysqli_error($omodelo->link);
  }else{
    if($numerofilas > 0){
      for ($i=0; $i < $numerofilas; $i++) { 
        $arreglo['compras'][$i] = array('Total' => $row[$i]['Total'], 'Tipo_Pago' => $row[$i]['Tipo_Pago']);
      }
    }
  }

  $arreglo['usuarios'] = array();

  $query = "SELECT SUM(Pago - Cambio) AS Total, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) FROM usuarios WHERE ID_Usuario = FK_Usuario), '') AS Usuario FROM ventas WHERE FK_Detalles_Caja = '$id' GROUP BY FK_Usuario";
  $row = $omodelo->_consultar($query);
  $numerofilas = $omodelo->numerofilas;

  if ($row == 'si') {
    echo "Error: " . mysqli_error($omodelo->link);
  }else{
    if($numerofilas > 0){
      for ($i=0; $i < $numerofilas; $i++) { 
        $arreglo['usuarios'][$i] = array('Total' => $row[$i]['Total'], 'Usuario' => $row[$i]['Usuario']);
      }
    }
  }

  $arreglo['registros_pagos_ventas'] = array();

  $query = "SELECT FK_Venta, Monto, Tipo_Pago, IFNULL((SELECT IF(Tipo = 'Moral', Razon_Social, CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido)) FROM clientes WHERE ID_Cliente = (SELECT FK_Cliente FROM ventas WHERE ID_Venta = FK_Venta)), 'Publico en General') AS Cliente FROM ventas_pagos WHERE FK_Detalle_Caja = '$id'";
  $row = $omodelo->_consultar($query);
  $numerofilas = $omodelo->numerofilas;

  if ($row == 'si') {
    echo "Error: " . mysqli_error($omodelo->link);
  }else{
    if($numerofilas > 0){
      for ($i=0; $i < $numerofilas; $i++) { 
        $arreglo['registros_pagos_ventas'][$i] = array('Folio' => $row[$i]['FK_Venta'], 'Monto' => $row[$i]['Monto'], 'Tipo_Pago' => $row[$i]['Tipo_Pago'], 'Cliente' => $row[$i]['Cliente']);
      }
    }
  }

  /*$arreglo['registros_ventas'] = array();

  $query = "SELECT ID_Venta, Total, Tipo_Pago, ((Pago - Cambio) + IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ID_Venta AND FK_Detalle_Caja = '$id'), 0)) AS Pagado, IFNULL((SELECT CONCAT(Nombre, ' ', Primer_Apellido, Segundo_Apellido) FROM usuarios WHERE ID_Usuario = ventas.FK_Usuario), '') AS Usuario FROM ventas WHERE FK_Detalles_Caja = '$id' ORDER BY ventas.FK_Usuario, ID_Venta";
  $row = $omodelo->_consultar($query);
  $numerofilas = $omodelo->numerofilas;

  if ($row == 'si') {
    echo "Error: " . mysqli_error($omodelo->link);
  }else{
    if($numerofilas > 0){
      for ($i=0; $i < $numerofilas; $i++) { 
        $arreglo['registros_ventas'][$i] = array('Folio' => $row[$i]['ID_Venta'], 'Total' => $row[$i]['Total'], 'Tipo_Pago' => $row[$i]['Tipo_Pago'], 'Pagado' => $row[$i]['Pagado'], 'Usuario' => $row[$i]['Usuario']);
      }
    }
  }*/

  $totalVentas = 0;
  $tablaVentas = '';
  $tablaDetalles = '<tr>
    <td>Monto de apertura</td>
    <td>$'.number_format($arreglo['detalles']['Monto_Abrir'], 2).'</td>
  </tr>';
  if(count($arreglo['ventas']) > 0){
    for ($i=0; $i < count($arreglo['ventas']); $i++) { 
      if($arreglo['ventas'][$i]['Total'] > 0){
        if($arreglo['ventas'][$i]['Tipo_Pago'] == 'Efectivo' || $arreglo['ventas'][$i]['Tipo_Pago'] == "Crédito"){
          $totalVentas += $arreglo['ventas'][$i]['Total'];
        }

        $mas = '';
        if($arreglo['ventas'][$i]['Tipo_Pago'] == "Crédito"){
          $mas = ' (Abono Efectivo)';
        }

        $tablaVentas .= '<tr>
          <td>'.$arreglo['ventas'][$i]['Tipo_Pago'].$mas.'</td>
          <td>$'.number_format($arreglo['ventas'][$i]['Total'], 2).'</td>
        </tr>';
      }
    }

    $tablaDetalles .= '<tr>
      <td>Ventas Efectivo</td>
      <td>$'.number_format($totalVentas, 2).'</td>
    </tr>';
  }else{
    $tablaVentas = '<tr>
      <td colspan="2">No existen registros.</td>
    </tr>';
  }

  $totalEntradas = 0;
  $totalSalidas = 0;
  $tablaMovimientos = '';
  if(count($arreglo['movimientos']) > 0){
    for ($i=0; $i < count($arreglo['movimientos']); $i++) {   
      if($arreglo['movimientos'][$i]['Tipo'] == "Entrada"){
        $totalEntradas += $arreglo['movimientos'][$i]['Monto'];
      }else{
        $totalSalidas += $arreglo['movimientos'][$i]['Monto'];
      }

      $tablaMovimientos .= '<tr>
        <td>'.$arreglo['movimientos'][$i]['Fecha_Registro'].'</td>
        <td>'.$arreglo['movimientos'][$i]['Tipo'].'</td>
        <td>'.number_format($arreglo['movimientos'][$i]['Monto'], 2).'</span></td>
      </tr>';
    }

    $tablaDetalles .= '<tr>
      <td>Entradas</td>
      <td>$'.number_format($totalEntradas, 2).'</td>
    </tr>
    <tr>
      <td>Salidas</td>
      <td>$'.number_format($totalSalidas, 2).'</td>
    </tr>';
  }else{
    $tablaMovimientos = '<tr>
      <td colspan="3">No existen registros.</td>
    </tr>';
  }

  $totalCompras = 0;
  $tablaCompras = '';
  if(count($arreglo['compras']) > 0){
    for ($i=0; $i < count($arreglo['compras']); $i++) { 
      if($arreglo['compras'][$i]['Tipo_Pago'] == 'Efectivo'){
        $totalCompras += $arreglo['compras'][$i]['Total'];
      }

      $totalCompras .= '<tr>
        <td>'.$arreglo['compras'][$i]['Tipo_Pago'].'</td>
        <td>$'.number_format($arreglo['compras'][$i]['Total'], 2).'</td>
      </tr>';
    }

    $tablaDetalles .= '<tr>
      <td>Compras Efectivo</td>
      <td>$'.number_format($totalCompras, 2).'</td>
    </tr>';
  }else{
    $tablaCompras = '<tr>
      <td colspan="2">No existen registros.</td>
    </tr>';
  }

  $tablaVentasUsu = '';
  if(count($arreglo['usuarios']) > 0){
    for ($i=0; $i < count($arreglo['usuarios']); $i++) { 
      $tablaVentasUsu .= '<tr>
        <td>'.$arreglo['usuarios'][$i]['Usuario'].'</td>
        <td>$'.number_format($arreglo['usuarios'][$i]['Total'], 2).'</td>
      </tr>';
    }
  }else{
    $tablaVentasUsu = '<tr>
      <td colspan="2">No existen registros.</td>
    </tr>';
  }

  $totalDetallesPagosVentas = 0;
  $tablaDetallesPagosVentas= '';
  if(count($arreglo['registros_pagos_ventas']) > 0){
    for ($i=0; $i < count($arreglo['registros_pagos_ventas']); $i++) { 
      $totalDetallesPagosVentas += $arreglo['registros_pagos_ventas'][$i]['Monto'];

      $tablaDetallesPagosVentas .= '<tr>
        <td colspan="3">'.$arreglo['registros_pagos_ventas'][$i]['Cliente'].'</td>
      </tr>
      <tr>
        <td>'.$arreglo['registros_pagos_ventas'][$i]['Folio'].'</td>
        <td>$'.number_format($arreglo['registros_pagos_ventas'][$i]['Monto'], 2).'</td>
        <td>'.$arreglo['registros_pagos_ventas'][$i]['Tipo_Pago'].'</td>
      </tr>';
    }
  }else{
    $tablaDetallesPagosVentas = '<tr>
      <td colspan="3">No existen registros.</td>
    </tr>';
  }

  /*$totalDetallesVentas1 = 0;
  $totalDetallesVentas2 = 0;
  $tablaDetallesVentas= '';
  if(count($arreglo['registros_ventas']) > 0){
    for ($i=0; $i < count($arreglo['registros_ventas']); $i++) { 
      $totalDetallesVentas1 += $arreglo['registros_ventas'][$i]['Total'];
      $totalDetallesVentas2 += $arreglo['registros_ventas'][$i]['Pagado'];

      $tablaDetallesVentas .= '<tr>
        <td>'.$arreglo['registros_ventas'][$i]['Folio'].'</td>
        <td>$'.number_format($arreglo['registros_ventas'][$i]['Total'], 2).'</td>
        <td>'.$arreglo['registros_ventas'][$i]['Tipo_Pago'].'</td>
        <td>$'.number_format($arreglo['registros_ventas'][$i]['Pagado'], 2).'</td>
        <td>'.$arreglo['registros_ventas'][$i]['Usuario'].'</td>
      </tr>';
    }
  }else{
    $tablaDetallesVentas = '<tr>
      <td colspan="5">No existen registros.</td>
    </tr>';
  }*/

  $totalBalance = ($arreglo['detalles']['Monto_Abrir'] + $totalVentas + $totalEntradas + $totalDetallesPagosVentas) - ($totalSalidas + $totalCompras);

$html = '<!DOCTYPE html>
  <html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <style>
        * {
          box-sizing: border-box;
          font-family: "Consolas", "Lucida Console", "Courier New", monospace;
          margin: 0;
          padding: 0;
          font-weight: bold;
          text-transform: uppercase;
        }

        body {
          display: flex;
          justify-content: center;
          align-items: center;
          flex-flow: column nowrap;
          padding: 0.5rem;
          max-width: 410px;
          width: 410px;
        }

        button {
          background-color: #f5f5f5;
          border: 1px solid #ccc;
          border-radius: 4px;
          color: #333;
          cursor: pointer;
          display: inline-block;
          font-size: 14px;
          font-weight: 400;
          line-height: 1.42857;
          margin-bottom: 0;
          padding: 6px 12px;
          text-align: center;
          white-space: nowrap;

          margin-bottom: 3rem;

          display: block;
        }

        .nombre {
          font-size: 2rem;
          font-weight: bold;
        }

        .domicilio {
          font-size: 1.5rem;
          text-align: center;
        }

        .domicilio+p {
          margin-top: 0.25rem;
        }

        .telefono {
          font-size: 1.5rem;
        }

        .fecha {
          margin: 0.5rem 0;
          font-size: 0.8rem;
        }

        .tablas {
          display: flex;
          flex-flow: column nowrap;
          justify-content: flex-start;
          align-items: center;
          width: 100%;
          gap: 0.5rem;
          font-size: 18px;
        }

        table {
          width: 100%;
          margin: 0.5rem 0;
        }

        caption {
          font-size: 1.25rem;
          font-weight: bold;
          margin-bottom: 0.1rem;
        }

        th {
          border-bottom: 3px solid #333;
        }

        td {
          text-align: center;
        }

        .pago {
          width: 100%;
          font-size: 1.1rem;
          font-weight: bold;
          text-align: right;
        }

        .pago p {
          text-transform: uppercase;
          margin-right: 0.8rem;
        }

        footer {
          margin-top: 1rem;
        }

        .separador {
          font-size: 1.75rem;
          font-weight: bold;
          max-width: 20ch;
          word-wrap: break-word;
          line-height: 0.5;
        }

        .gracias {
          font-weight: bold;
          text-transform: uppercase;
          font-size: 1.2rem;
          text-align: center;
          margin-bottom: 0.5rem;
        }

        .fechas{
          font-size: 16px;
          margin-top: 2px;
        }

        .usuarios{
          font-size: 20px;
          margin-top: 2px;
          text-align: center;
        }

        .detalles {
          display: flex;
          text-align: right;
          flex-flow: column nowrap;
          justify-content: flex-end;
          font-size: 0.8rem;
          margin-right: 0.5rem;
          margin-bottom: 0.5rem;
        }

        @media print {
          body {
            transform: scale(0.59); 
            transform-origin: top left; 
          }

          button {
            display: none;
          }
        } 
    </style>
  </head>
  <body>
    <button type="submit" class="oculto-impresion" onclick="imprimir()">
      Imprimir
    </button> 
    <p class="domicilio">CORTE DE CAJA</p>
    <br>
    <p class="fechas"><b>Fecha apertura:</b> '.$arreglo['detalles']['Fecha_Abrir'].'</p>
    <p class="fechas"><b>Fecha cierre:</b> '.$arreglo['detalles']['Fecha_Cierre'].'</p>
    <br>
    <p class="usuarios"><b>Usuario apertura:</b> '.$arreglo['detalles']['Usuario_Abrio'].'</p>
    <p class="usuarios"><b>Usuario cierre:</b> '.$arreglo['detalles']['Usuario_Cerro'].'</p>
    <br>
    <h1>Balance: $'.number_format($totalBalance, 2).'<h1>
    <h2>Monto de cierre: $'.number_format($arreglo['detalles']['Monto_Cierre'], 2).'</h2>
    <h2>Restante: $'.number_format(($arreglo['detalles']['Monto_Cierre'] - $totalBalance), 2).'</h2>
    <div class="tablas">
      <table>
        <caption>Detalles</caption>
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          '.$tablaDetalles.'
        </tbody>
      </table>
      <table>
        <caption>Ventas y pagos</caption>
        <thead>
          <tr>
            <th>Tipo Pago</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          '.$tablaVentas.'
        </tbody>
      </table>
      <table>
        <caption>Ventas por usuario</caption>
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          '.$tablaVentasUsu.'
        </tbody>
      </table>
      <table>
        <caption>Movimientos</caption>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Monto</th>
          </tr>
        </thead>
        <tbody>
          '.$tablaMovimientos.'
        </tbody>
      </table>
      <table>
        <caption>Compras</caption>
        <thead>
          <tr>
            <th>Tipo Pago</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          '.$tablaCompras.'
        </tbody>
      </table>
      <table>
        <caption>Detalles Pagos Ventas</caption>
        <thead>
          <tr>
            <th>Folio de Venta</th>
            <th>Monto</th>
            <th>Tipo Pago</th>
          </tr>
        </thead>
        <tbody>
          '.$tablaDetallesPagosVentas.'
        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <th>$'.number_format($totalDetallesPagosVentas, 2).'</th>
          <tr>
        </tfoot>
      </table>

      <!--//Tabla//-->

    </div>
  </body>
  <script>
  window.print();
  
  function imprimir() {
    window.print();
  }
  </script>
  </html>';

  /*
  <table style="font-size: 12px;">
        <caption>Detalles Ventas</caption>
        <thead>
          <tr>
            <th>Folio de Venta</th>
            <th>Total</th>
            <th>Tipo</th>
            <th>Pagado</th>
            <th>Usuario</th>
          </tr>
        </thead>
        <tbody>
          '.$tablaDetallesVentas.'
        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <th>$'.number_format($totalDetallesVentas1, 2).'</th>
            <th></th>
            <th>$'.number_format($totalDetallesVentas2, 2).'</th>
          <tr>
        </tfoot>
      </table>
  */
echo $html;