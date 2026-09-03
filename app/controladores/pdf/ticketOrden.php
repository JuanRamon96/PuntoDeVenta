<?php
  // Require composer autoload
  require_once __DIR__ . '/vendor/autoload.php';

  session_start();
  include '../../modelo/m_modelo.php';
  if (!isset($_SESSION['user_punto_venta'])) return http_response_code(400);
  extract($_GET);

  $modelo = new m_modelo();
  $id = $modelo->link->real_escape_string($id);

  $queryCompra = "SELECT ID_Orden_Compra, ID_Orden_Compra AS Folio, FK_Sucursal, IFNULL((SELECT Nombre FROM sucursales WHERE ID_Sucursal = FK_Sucursal), '') AS Sucursal, FK_Usuario, FK_Proveedor, Razon_Social, RFC, Telefono, Email, Calle, No_Exterior, No_Interior, Colonia, CP, Ciudad, Estado, Pais, ordenes_compra.Estatus AS Estatus, Descuento, Total, CONCAT(usuarios.Nombre, ' ', usuarios.Primer_Apellido, ' ', usuarios.Segundo_Apellido) AS Nombre, DATE_FORMAT(ordenes_compra.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro FROM ordenes_compra INNER JOIN usuarios ON FK_Usuario = usuarios.ID_Usuario INNER JOIN proveedores ON FK_Proveedor = ID_Proveedor WHERE ID_Orden_Compra = '$id'";
  $rowCompra = $modelo->_consultar($queryCompra);

  if ($rowCompra == 'si') {
    echo "Error: " . mysqli_error($modelo->link);
    return;
  }

  $queryDetalles = "SELECT ID_Detalle_Orden, FK_Orden, FK_Producto, Codigo, detalles_orden.Descripcion AS Descripcion, detalles_orden.Costo AS Costo, Cantidad, Subtotal FROM detalles_orden LEFT JOIN productos ON FK_Producto = ID_Producto WHERE FK_Orden = '$id'";
  $rowDetalles = $modelo->_consultar($queryDetalles);

  if ($rowDetalles == 'si') {
    echo "Error: " . mysqli_error($modelo->link);
    return;
  }

  $tabla = '
    <table>
      <thead>
        <tr>
          <th>CÓDIGO</th>
          <th>DESCRIPCIÓN</th>
          <th>COSTO</th>
          <th>CANTIDAD</th>
          <th>SUBTOTAL</th>
        </tr>
      </thead>
      <tbody>';

  foreach ($rowDetalles as $detalle) {
    if ($detalle == null) continue;
    $tabla .= '
      <tr>
        <td>'.$detalle['Codigo'].'</td>
        <td>'.$detalle['Descripcion'].'</td>
        <td>$'.number_format($detalle['Costo'], 2).'</td>
        <td>'.number_format($detalle['Cantidad'], 2).'</td>
        <td>$'.number_format($detalle['Subtotal'], 2).'</td>
      </tr>';
  }

  $tabla .= '</tbody>
    <tfoot>
      <tr>
        <th colspan="4" style="text-align: right;">Subtotal:</th>
        <td>$'.number_format(($rowCompra[0]['Descuento']+$rowCompra[0]['Total']), 2).'</td>
      </tr>
      <tr>
        <th colspan="4" style="text-align: right;">Descuento:</th>
        <td>$'.number_format($rowCompra[0]['Descuento'], 2).'</td>
      </tr>
      <tr>
        <th colspan="4" style="text-align: right;">Total:</th>
        <td>$'.number_format($rowCompra[0]['Total'], 2).'</td>
      </tr>
    </tfoot> 
  </table>';

  $domicilio = '';
  if($rowCompra[0]['Calle'] != ''){
    $domicilio = $rowCompra[0]['Calle'];
  }

  if($rowCompra[0]['No_Exterior'] != ''){
    $domicilio .= ' #'.$rowCompra[0]['No_Exterior'];
  }
  
  if($rowCompra[0]['No_Interior'] != ''){
    $domicilio .= ' - '.$row[0]['No_Interior'].',';
  }

  if($rowCompra[0]['Colonia'] != ''){
    $domicilio .= ' '.$row[0]['Colonia'];
  }

  if($rowCompra[0]['CP'] != ''){
    $domicilio .= ' C.P. '.$rowCompra[0]['CP'];
  }

  if($rowCompra[0]['Ciudad'] != ''){
    $domicilio .= ', '.$rowCompra[0]['Ciudad'];
  }

  if($rowCompra[0]['Estado'] != ''){
    $domicilio .= ', '.$rowCompra[0]['Estado'];
  }

  if($rowCompra[0]['Pais'] != ''){
    $domicilio .= ' '.$rowCompra[0]['Pais'].'.';
  }

  $logo = '../../vistas/assets/images/logos/logo.png';
  $queryGeneral = "SELECT Nombre, Domicilio, Telefono, Foto FROM configuracion WHERE ID_Configuracion = 1";
  $rowGeneral = $modelo->_consultar($queryGeneral);

  if ($rowGeneral == 'si') {
    echo "Error 3: " . mysqli_error($modelo->link);
    return;
  }else{
    if(trim($rowGeneral[0]['Foto']) != '' && file_exists('../../vistas/assets/images/configuracion/'.trim($rowGeneral[0]['Foto']))){
      $logo = '../../vistas/assets/images/configuracion/'.trim($rowGeneral[0]['Foto']);
    }
  }
  
  $html = '
  <style>
    p {
      margin: 0px;
    }

    .datos{
      font-size: 13px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
      font-family: Arial;
    }

    th, td {
      border: 1px solid #000;
      text-align: center;
      vertical-align: middle;
      padding: 5px;
    }

    th {
      background: #EEE;
    }

    td {
      font-size: 14px;
    }

    .general {
      text-align: right;
      font-size: 11px;
      margin-top: -50px;
      color: #666;
    }

    .datos {
      margin-top: 20px;
    }
  </style>
  <div style="font-family: Arial;">
    <div>
      <img src="'.$logo.'" style="width: 60px;">
      <h1 style="margin-top: -45px; margin-left: 80px;">ORDEN DE COMPRA</h1>
    </div>
    <div class="general">
      <p>'.$rowGeneral[0]['Nombre'].'</p>
      <p><b>Tel.</b>'.$rowGeneral[0]['Telefono'].'</p>
      <p>'.$rowGeneral[0]['Domicilio'].'</p>
    </div>
    <div class="datos">
      <p><b>Folio: </b> '.$rowCompra[0]['Folio'].'</p>
      <p><b>Fecha: </b> '.$rowCompra[0]['Fecha_Registro'].'</p>
      <p><b>Estatus: </b> '.$rowCompra[0]['Estatus'].'</p>
      '.($rowCompra[0]['Sucursal'] != '' ? '<p><b>Sucursal: </b> '.$rowCompra[0]['Sucursal'].'</p>': '').'
    </div>
    <hr/> 
    <div class="datos1">
      <h2 style="margin: 0px;">Proveedor</h2>
      <p><b>Razón Social: </b> '.$rowCompra[0]['Razon_Social'].'</p>
      <p><b>RFC: </b> '.$rowCompra[0]['RFC'].'</p>
      <p><b>Domicilio: </b> '.$domicilio.'</p>
      <p><b>Teléfono: </b> '.$rowCompra[0]['Telefono'].'</p>
      <p><b>Email: </b> '.$rowCompra[0]['Email'].'</p>
    </div>
    <br/>
    <br/>
    <div>
      <h3 style="text-align: center; margin: 0px;">PRODUCTOS</h3>
      '.$tabla.'
    </div>  
  </div>';

  $mpdf = new \Mpdf\Mpdf(
    [
      'mode' => 'utf-8', 
      'format' => 'Letter', 
      'margin_left' => 12,      
      'margin_right' => 12,     
      'margin_top' => 12,     
      'margin_bottom' => 12
    ]
  );

  // Write some HTML code:
  $mpdf->WriteHTML($html);

  // Output a PDF file directly to the browser
  $mpdf->Output();

  //echo $html;
?>
