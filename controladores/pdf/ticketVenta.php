<?php
session_start();
include '../../modelo/m_modelo.php';
if (!isset($_SESSION['user_punto_venta'])) return http_response_code(400);
extract($_GET);

$modelo = new m_modelo();
$id = $modelo->link->real_escape_string($id);
$queryVentas = "SELECT 
    ID_Venta, 
    FK_Direccion, 
    FK_Cliente, 
    IFNULL(IF(clientes.Tipo = 'Moral', Razon_Social, CONCAT(clientes.Nombre, ' ', clientes.Primer_Apellido, ' ', clientes.Segundo_Apellido)), 'Publico en General') AS Cliente, 
    IFNULL(clientes.Telefono, '') AS Telefono, 
    Turno, 
    FK_Usuario, 
    FK_Detalles_Caja, 
    Descuento, 
    Total, 
    Tipo_Pago, 
    Cambio, 
    Pago, 
    ventas.Detalles AS Detalles, 
    CONCAT(usuarios.Nombre, ' ', usuarios.Primer_Apellido, ' ', usuarios.Segundo_Apellido) AS Nombre, 
    DATE_FORMAT(ventas.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro 
  FROM ventas INNER JOIN usuarios ON FK_Usuario = usuarios.ID_Usuario LEFT JOIN clientes ON FK_Cliente = ID_Cliente WHERE ID_Venta = '$id'";
$rowVentas = $modelo->_consultar($queryVentas);
$folio = str_pad($rowVentas[0]['ID_Venta'], 6, "0", STR_PAD_LEFT);

if ($rowVentas == 'si') {
  echo "Error 1: " . mysqli_error($modelo->link);
  return;
}

$queryDetalles = "SELECT 
    ID_Detalle_Venta,
    FK_Venta, 
    Descripcion, 
    Cantidad, 
    Precio, 
    Descuento,
    Total, 
    IFNULL((SELECT Cantidad FROM devoluciones WHERE FK_Detalle_Venta = ID_Detalle_Venta), 0) AS Devolucion 
  FROM detalles_ventas WHERE FK_Venta = '$id'";
$rowDetalles = $modelo->_consultar($queryDetalles);
$numerofilas = $modelo->numerofilas;

$tabla = '
    <table class="tablaDetalles">
      <thead>
        <tr>
          <th>Cant.</th>
          <th>Precio</th>
          <th>Desc.</th>
          <th>Impto.</th>  
          <th>Total</th>
        </tr>
      </thead>
      <tbody>';

if ($rowDetalles == 'si') {
  echo "Error 2: " . mysqli_error($modelo->link);
  return;
} else {
  for ($i = 0; $i < $numerofilas; $i++) {
    $cantidad = $rowDetalles[$i]['Cantidad'] - $rowDetalles[$i]['Devolucion'];

    if ($cantidad > 0) {
      $subtotal = $rowDetalles[$i]['Precio'] * $cantidad;
      $subDes = $subtotal - $rowDetalles[$i]['Descuento'];

      $impuestos = '';
      $query = "SELECT 
        ID_Impuesto_Venta, 
        Nombre, 
        Porcentaje,
        Clave_CFDI,
        Tipo_Factor,
        Clase 
      FROM impuestos_ventas WHERE FK_Detalle_Venta = '" . $rowDetalles[$i]['ID_Detalle_Venta'] . "'";
      $row = $modelo->_consultar($query);
      $numerofilasImpuestos = $modelo->numerofilas;

      if ($row == 'si') {
        echo "Error 3: " . mysqli_error($modelo->link);
        return;
      } else {
        for ($j = 0; $j < $numerofilasImpuestos; $j++) {
          $factor = strtoupper($row[$j]['Tipo_Factor']);
          $valorBase = floatval($row[$j]['Porcentaje']);
          $impuestoFila = 0;

          // --- Lógica de Tasa o Cuota ---
          if ($factor === 'CUOTA') {
            $impuestoFila = $rowDetalles[$i]['Cantidad'] * $valorBase;
            $simbolo = '';
          } else {
            $impuestoFila = $subDes * ($valorBase / 100);
            $simbolo = '%';
          }

          //<b>' . $row[$j]['Clase'] . '</b>
          // - <b>' . $row[$j]['Tipo_Factor'] . '</b> 
          $impuestos .= '<p style="margin: 0; font-size: 16px;" attrID="' . $row[$j]['ID_Impuesto_Venta'] . '">
            <span>' . $row[$j]['Nombre'] . '</span> 
            <span class="porcentaje">' . (round($valorBase * 100) / 100) . '</span>' . $simbolo . ' 
            ($<span class="dinero">' . number_format($impuestoFila, 2) . '</span>)
          </p>';
        }
      }

      $tabla .= '
        <tr>
          <td colspan="5" style="text-align: left;">' . $rowDetalles[$i]['Descripcion'] . '</td>
        </tr>
        <tr>
          <td>' . (round($cantidad * 100) / 100) . '</td>
          <td>$' . number_format($rowDetalles[$i]['Precio'], 2) . '</td>
          <td>$' . number_format($rowDetalles[$i]['Descuento'], 2) . '<br><span style="font-size: 16px;">(' . number_format((($rowDetalles[$i]['Descuento'] / $subtotal) * 100), 2) . '%)</span></td>
          <td><span style="font-size: 16px;">Sub:</span> $' . number_format($subDes, 2) . $impuestos . '</td>
          <td>$' . number_format($rowDetalles[$i]['Total'], 2) . '</td>
        </tr>';
    }
  }
}

$tabla .= '</tbody>
  </table>';

$direccion = '';
if ($rowVentas[0]['FK_Cliente'] != '0') {
  if ($rowVentas[0]['FK_Direccion'] == '0') {
    $queryDomiciolio = "SELECT Calle, No_Exterior, No_Interior, Colonia, CP, Latitud, Longitud, Detalles FROM clientes WHERE ID_Cliente = '" . $rowVentas[0]['FK_Cliente'] . "'";
  } else {
    $queryDomiciolio = "SELECT Calle, No_Exterior, No_Interior, Colonia, CP, Latitud, Longitud, Detalles FROM direcciones_cliente WHERE ID_Direccion = '" . $rowVentas[0]['FK_Direccion'] . "'";
  }

  $rowDomicilio = $modelo->_consultar($queryDomiciolio);
  if ($rowDomicilio == 'si') {
    echo "Error 3: " . mysqli_error($modelo->link);
    return;
  } else {
    $direccion = $rowDomicilio[0]['Calle'] . ' #' . $rowDomicilio[0]['No_Exterior'];

    if ($rowDomicilio[0]['No_Interior'] != '') {
      $direccion .= ' int.' . $rowDomicilio[0]['No_Interior'];
    }

    if ($rowDomicilio[0]['CP'] != '') {
      $direccion .= ', C.P. ' . $rowDomicilio[0]['CP'];
    }

    if ($rowDomicilio[0]['Colonia'] != '') {
      $direccion .= ' Col. ' . $rowDomicilio[0]['Colonia'];
    }

    if ($rowDomicilio[0]['Detalles'] != '') {
      $direccion .= '<br> Detalles: ' . $rowDomicilio[0]['Detalles'];
    }
  }
}

$logo = '../../vistas/assets/images/logos/logo.png';
$queryGeneral = "SELECT Nombre, Domicilio, Telefono, Foto FROM configuracion WHERE ID_Configuracion = 1";
$rowGeneral = $modelo->_consultar($queryGeneral);

if ($rowGeneral == 'si') {
  echo "Error 4: " . mysqli_error($modelo->link);
  return;
} else {
  if (trim($rowGeneral[0]['Foto']) != '' && file_exists('../../vistas/assets/images/configuracion/' . trim($rowGeneral[0]['Foto']))) {
    $logo = '../../vistas/assets/images/configuracion/' . trim($rowGeneral[0]['Foto']);
  }
}

$textoPago = 'Pagó con';
$cambio = '<p>Cambio: $' . number_format($rowVentas[0]['Cambio'], 2) . '</p>';
if ($rowVentas[0]['Tipo_Pago'] == 'Crédito') {
  $textoPago = 'Anticipo';

  $restante = $rowVentas[0]['Total'] - $rowVentas[0]['Pago'];
  $restante = $restante < 0 ? 0 : $restante;

  $cambio = '<p>Restante: $' . number_format($restante, 2) . '</p>';
}

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
          font-family: calibri;
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
          font-size: 28px;
          font-weight: bold;
        }

        .domicilio {
          font-size: 26px;
          text-align: center;
        }

        .domicilio+p {
          margin-top: 0.25rem;
        }

        .telefono {
          font-size: 28px;
        }

        .fecha {
          margin: 0.5rem 0;
          font-size: 22px;
        }

        caption {
          font-size: 1.25rem;
          font-weight: bold;
          margin-bottom: 0.1rem;
        }

        table {
          width: 100%;
          margin: 0.5rem 0;
          font-size: 18px;
          border-collapse: collapse;
        }

        table th, td {
          border-collapse: collapse;
        }

        th {
          border-bottom: 3px solid #333;
        }

        td {
          text-align: center;
          font-size: 24px;
        }

        td[colspan="5"] {
          text-align: left;
          font-size: 24px;
        }

        .pago {
          width: 100%;
          font-size: 2rem;
          text-align: right;
        }

        .pago p {
          text-transform: uppercase;
          margin-right: 0.8rem;
        }

        footer {
          margin-top: 1rem;
          margin-bottom: 500px;
        }

        .separador {
          font-size: 2rem;
          font-weight: bold;
          text-align: center;
          margin: 20px 0px;
          line-height: 0.2rem;
        }

        .gracias {
          font-weight: bold;
          text-transform: uppercase;
          font-size: 1.5rem;
          text-align: center;
          margin-bottom: 0.5rem;
        }

        .detalles {
          display: flex;
          text-align: right;
          flex-flow: column nowrap;
          justify-content: flex-end;
          font-size: 16px;
          margin-right: 0.5rem;
          margin-bottom: 0.5rem;
        }

        .vendedor {
          font-size: 20px;
        }

        .turno {
          margin: 0;
          font-size: 30px;
        }

        .folio {
          margin-top: 10px;
          margin-bottom: 0px;
          font-size: 22px;
        }

        .cliente{
          font-size: 20px;
        }

        .direccionCliente{
          text-align: center;
          font-size: 16px;
          margin-top: 5px;
        }

        .contenedor-qr {
          width: 50%;
          margin: 0 auto; /* centrar horizontalmente */
          text-align: center;
        }

        #qrcode img {
          width: 100%;
          height: auto;
          max-width: 300px; /* opcional: límite de tamaño */
        }

        @media print {
          body {
            transform: scale(0.6); 
            transform-origin: top left; 
          }
          
          button {
            display: none;
          }
        }
      </style>
    </head>
    <body onafterprint="funcionDespues()">
     <button type="submit" class="oculto-impresion" onclick="imprimir()">
      Imprimir
     </button> 
    
    <img src="' . $logo . '" width="300px" style="margin-bottom: 4px;" />
    <p class="domicilio">' . $rowGeneral[0]['Nombre'] . '</p>
    <p class="domicilio">' . $rowGeneral[0]['Domicilio'] . '</p>
    <p class="telefono">' . $rowGeneral[0]['Telefono'] . '</p>
    <p class="fecha"><strong>Fecha:</strong> ' . $rowVentas[0]['Fecha_Registro'] . ' </p>

    <p class="cliente"><strong>Cliente:</strong> ' . $rowVentas[0]['Cliente'] . ' </p>
    ' . ($rowVentas[0]['Telefono'] != '' ? '<p class="cliente"><strong>Tel:</strong> ' . $rowVentas[0]['Telefono'] . ' </p>' : '') . '
    ' . ($direccion != '' ? '<p class="direccionCliente">' . $direccion . ' </p>' : '') . '

    
    <p class="folio"><strong>Folio:</strong> ' . $folio . ' </p>
    ' . ($rowVentas[0]['Turno'] != '' ? '<p class="turno"><strong>Turno:</strong> ' . $rowVentas[0]['Turno'] . ' </p>' : '') . '
    ' . $tabla . '
    <div class="pago">
      <p>Subtotal: $' . number_format(($rowVentas[0]['Total'] - $rowVentas[0]['Descuento']), 2) . '</p>
      <p>Descuento: $' . number_format($rowVentas[0]['Descuento'], 2) . '</p>
      <p>Total: $' . number_format($rowVentas[0]['Total'], 2) . '</p>
      <p>' . $textoPago . ': $' . number_format($rowVentas[0]['Pago'], 2) . '</p>
      ' . $cambio . '
    </div>
    <footer>
      <div class="detalles">
        ' . (trim($rowVentas[0]['Detalles']) != '' ? '<strong>Detalles:</strong> ' . $rowVentas[0]['Detalles'] : '') . '
        <p class="vendedor">Realizó la venta: ' . $rowVentas[0]['Nombre'] . '</p>
      </div>
      <p class="separador">**************************</p>
      <p class="separador">**************************</p>
      <p class="gracias">¡Gracias por su compra!</p>
      <p class="separador">**************************</p>
      <p class="separador">**************************</p>
      <div class="contenedor-qr">
        <div id="qrcode"></div>
      </div>
    </footer>
    </body>
    <script src="./../../vistas/assets/plugins/qrcode.min.js"></script>
    <script>
    window.print();

    function imprimir() {
      window.print();
    }

    ' . ($rowVentas[0]['FK_Cliente'] != '0' && $rowDomicilio[0]['Latitud'] != '' && $rowDomicilio[0]['Longitud'] != '' ?
    'var qr = new QRCode(document.getElementById("qrcode"), {
            text: "https://www.google.com/maps?q=' . $rowDomicilio[0]['Latitud'] . ',' . $rowDomicilio[0]['Longitud'] . '",
            width: 128,
            height: 128,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
          });'
    : '')
    . '

    function funcionDespues() {
      //parent.document.documentElement.requestFullscreen();
      setTimeout(function(){
        parent.document.getElementById("bAbrirCaja").click();
      }, 300);
    }
    </script>
  </html>';

echo $html;
