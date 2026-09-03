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

    //if ($cantidad > 0) {
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
          <td colspan="5" style="text-align: left;">' . $rowDetalles[$i]['Descripcion'] . ($rowDetalles[$i]["Devolucion"] > 0 ? ' <span style="color: #a7090c;">(Dev: ' . number_format($rowDetalles[$i]["Devolucion"], 2) . ')</span>' : '') . '</td>
        </tr>
        <tr>
          <td>' . (round($cantidad * 100) / 100) . '</td>
          <td>$' . number_format($rowDetalles[$i]['Precio'], 2) . '</td>
          <td>$' . number_format($rowDetalles[$i]['Descuento'], 2) . '<br><span style="font-size: 16px;">(' . ($subtotal > 0 ? number_format((($rowDetalles[$i]['Descuento'] / $subtotal) * 100), 2) : 0) . '%)</span></td>
          <td><span style="font-size: 16px;">Sub:</span> $' . number_format($subDes, 2) . $impuestos . '</td>
          <td>$' . number_format($rowDetalles[$i]['Total'], 2) . '</td>
        </tr>';
    //}
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
    if ($rowDomicilio[0]['Calle'] != '') {
      $direccion = $rowDomicilio[0]['Calle'];
    }

    if ($rowDomicilio[0]['No_Exterior'] != '') {
      $direccion .= ' #' . $rowDomicilio[0]['No_Exterior'];
    }

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
          font-family: Arial, Helvetica, sans-serif;
          margin: 0;
          padding: 0;
        }

        body {
          display: flex;
          justify-content: center;
          align-items: center;
          flex-flow: column nowrap;
          padding: 0.5rem;
          max-width: 410px;
          width: 410px;
          color: #14171C;
        }

        button {
          background-color: #E4272B;
          border: none;
          border-radius: 6px;
          color: #fff;
          cursor: pointer;
          display: block;
          font-size: 14px;
          font-weight: 700;
          padding: 10px 16px;
          text-align: center;
          margin-bottom: 2rem;
          text-transform: uppercase;
          letter-spacing: 0.03em;
        }

        .logo-negocio {
          margin-bottom: 6px;
        }

        .nombre-negocio {
          font-size: 26px;
          font-weight: 800;
          text-transform: uppercase;
          text-align: center;
          letter-spacing: 0.02em;
        }

        .domicilio {
          font-size: 20px;
          text-align: center;
          color: #333;
          margin-top: 2px;
          text-transform: uppercase;
        }

        .telefono {
          font-size: 20px;
          text-align: center;
          color: #333;
        }

        .linea-punteada {
          width: 100%;
          border-top: 2px dashed #999;
          margin: 12px 0;
        }

        .fecha {
          width: 100%;
          font-family: "Courier New", Courier, monospace;
          font-size: 15px;
          font-weight: bold;
          margin: 4px 0;
          text-align: center;
        }

        .folio-grande {
          font-size: 24px;
          font-weight: 800;
          text-align: center;
          margin: 8px 0 2px;
          font-family: "Courier New", Courier, monospace;
          text-transform: uppercase;
        }

        .turno-badge {
          text-align: center;
          font-size: 15px;
          font-weight: 700;
          text-transform: uppercase;
          margin-bottom: 6px;
        }

        .cliente-info {
          width: 100%;
          font-size: 20px;
          font-weight: bold;
          text-transform: uppercase;
          margin-top: 4px;
        }

        .cliente-info p {
          margin: 2px 0;
        }

        .direccionCliente {
          text-align: left;
          font-size: 16px;
          color: #444;
          margin-top: 4px;
        }

        table {
          width: 100%;
          margin: 4px 0;
          font-size: 18px;
          border-collapse: collapse;
          font-family: "Courier New", Courier, monospace;
        }

        .tablaDetalles tbody tr:nth-of-type(4n+1),
        .tablaDetalles tbody tr:nth-of-type(4n+2) {
          background-color: #F3F4EF;
        }

        .tablaDetalles tbody tr:nth-of-type(4n+1) td[colspan="5"] {
          background-color: #F3F4EF;
        }

        thead th {
          border-bottom: 2px solid #14171C;
          padding-bottom: 4px;
          font-size: 16px;
          text-transform: uppercase;
          letter-spacing: 0.03em;
          font-weight: bold;
        }

        td {
          text-align: center;
          font-size: 20px;
          padding: 3px 2px;
          font-weight: bold;
        }

        td[colspan="5"] {
          text-align: left;
          font-size: 22px;
          font-weight: 800;
          text-transform: uppercase;
          padding-top: 10px;
        }

        .pago {
          width: 100%;
          font-size: 22px;
          font-family: "Courier New", Courier, monospace;
          font-weight: bold;
          border-top: 2px dashed #999;
          padding-top: 10px;
          margin-top: 8px;
        }

        .pago p {
          display: flex;
          justify-content: space-between;
          text-transform: uppercase;
          margin: 4px 0;
        }

        .pago .total-final {
          font-size: 30px;
          font-weight: 800;
          border-top: 1px solid #14171C;
          padding-top: 8px;
          margin-top: 8px;
        }

        footer {
          margin-top: 1rem;
          margin-bottom: 500px;
          width: 100%;
        }

        .detalles {
          text-align: left;
          font-size: 18px;
          margin-bottom: 8px;
          color: #444;
          font-weight: bold;
        }

        .vendedor {
          font-size: 18px;
          color: #444;
          text-align: left;
          font-weight: bold;
        }

        .separador {
          text-align: center;
          border-top: 2px dashed #999;
          margin: 16px 0;
        }

        .gracias {
          font-weight: 800;
          text-transform: uppercase;
          font-size: 24px;
          text-align: center;
          margin: 12px 0;
          letter-spacing: 0.03em;
        }

        .contenedor-qr {
          width: 55%;
          margin: 10px auto 0;
          text-align: center;
        }

        .qr-label {
          font-size: 16px;
          text-transform: uppercase;
          color: #666;
          margin-bottom: 4px;
          font-weight: bold;
        }

        #qrcode img {
          width: 100%;
          height: auto;
          max-width: 300px;
        }

        .text-center {
          text-align: center;
        }

        m-0 {
          margin: 0;
        }

        @media print {
          body {
            transform: scale(0.63);
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

    <img class="logo-negocio" src="' . $logo . '" width="220px" />
    <p class="nombre-negocio">' . $rowGeneral[0]['Nombre'] . '</p>
    <p class="domicilio">' . $rowGeneral[0]['Domicilio'] . '</p>
    <p class="telefono">' . $rowGeneral[0]['Telefono'] . '</p>

    <div class="linea-punteada"></div>

    <p class="fecha"><span>Fecha:</span> <span>' . $rowVentas[0]['Fecha_Registro'] . '</span></p>
    <p class="folio-grande">Folio ' . $folio . '</p>
    ' . ($rowVentas[0]['Turno'] != '' ? '<p class="turno-badge">Turno: ' . $rowVentas[0]['Turno'] . '</p>' : '') . '

    <div class="linea-punteada"></div>

    <div class="cliente-info">
      <p><strong>Cliente:</strong> ' . $rowVentas[0]['Cliente'] . '</p>
      ' . ($rowVentas[0]['Telefono'] != '' ? '<p><strong>Tel:</strong> ' . $rowVentas[0]['Telefono'] . '</p>' : '') . '
      ' . ($direccion != '' ? '<p class="direccionCliente">' . $direccion . '</p>' : '') . '
    </div>

    <div class="linea-punteada"></div>

    ' . $tabla . '

    <div class="pago">
      <p><span>Subtotal:</span> <span>$' . number_format(($rowVentas[0]['Total'] - $rowVentas[0]['Descuento']), 2) . '</span></p>
      <p><span>Descuento:</span> <span>$' . number_format($rowVentas[0]['Descuento'], 2) . '</span></p>
      <p class="total-final"><span>Total:</span> <span>$' . number_format($rowVentas[0]['Total'], 2) . '</span></p>
      <p><span>' . $textoPago . ':</span> <span>$' . number_format($rowVentas[0]['Pago'], 2) . '</span></p>
      ' . $cambio . '
    </div>

    <footer>
      ' . (trim($rowVentas[0]['Detalles']) != '' ? '<p class="detalles"><strong>Detalles:</strong> ' . $rowVentas[0]['Detalles'] . '</p>' : '') . '
      <p class="vendedor">Atendió: ' . $rowVentas[0]['Nombre'] . '</p>

      <div class="separador"></div>
      <div class="separador"></div>
      <p class="gracias">¡Gracias por su compra!</p>
      <div class="separador"></div>
      <div class="separador"></div>
      <p class="text-center m-0">www.bigtool.mx</p>

      ' . ($rowVentas[0]['FK_Cliente'] != '0' && $rowDomicilio[0]['Latitud'] != '' && $rowDomicilio[0]['Longitud'] != '' ?
  '<div class="contenedor-qr">
        <p class="qr-label">Ver ubicación</p>
        <div id="qrcode"></div>
      </div>' : '') . '
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
