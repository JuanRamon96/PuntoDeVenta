<?php
  session_start();
  include '../../modelo/m_modelo.php';
  if (!isset($_SESSION['user_tablet_stazione'])) return http_response_code(400);
  extract($_GET);

  $modelo = new m_modelo();
  $id = $modelo->link->real_escape_string($id);
  $queryVentas = "SELECT ID_Venta, FK_Usuario, FK_Detalles_Caja, Total, Tipo_Pago, Cambio, Detalles, CONCAT(usuarios.Nombre, ' ', usuarios.Primer_Apellido, ' ', usuarios.Segundo_Apellido) AS Nombre, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro FROM ventas INNER JOIN usuarios ON FK_Usuario = usuarios.ID_Usuario WHERE ID_Venta = '$id'";
  $rowVentas = $modelo->_consultar($queryVentas);
  $folio = str_pad($rowVentas[0]['ID_Venta'], 6, "0", STR_PAD_LEFT);

  if ($rowVentas == 'si') {
    echo "Error: " . mysqli_error($modelo->link);
    return;
  }

  $queryDetalles = "SELECT FK_Venta, Descripcion, Cantidad, Precio, Total FROM detalles_ventas WHERE FK_Venta = '$id'";
  $rowDetalles = $modelo->_consultar($queryDetalles);

  if ($rowDetalles == 'si') {
    echo "Error: " . mysqli_error($modelo->link);
    return;
  }

  $tabla = '
    <table>
      <thead>
        <tr>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>';

  foreach ($rowDetalles as $detalle) {
    if ($detalle == null) continue;
    $tabla .= '
      <tr>
        <td colspan="3">' . $detalle['Descripcion'] . '</td>
      </tr>
      <tr>
        <td>' . number_format($detalle['Cantidad'], 2) . '</td>
        <td>$' . number_format($detalle['Precio'], 2) . '</td>
        <td>$' . number_format($detalle['Total'], 2). '</td>
      </tr>';
  }

  $tabla .= '</tbody></table>';

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
        }

        .domicilio+p {
          margin-top: 0.25rem;
        }

        .telefono {
          font-size: 1.5rem;
        }

        .fecha {
          margin: 0.5rem 0;
          font-size: 1.1rem;
        }

        .tablas {
          display: flex;
          flex-flow: column nowrap;
          justify-content: flex-start;
          align-items: center;
          width: 100%;
          gap: 0.5rem;
        }

        table {
          width: 100%;
          margin: 0.5rem 0;
          font-size: 1rem;
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

        td[colspan="3"] {
          text-align: left;
          font-size: 1.25rem;
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
          font-size: 0.8rem;
          margin-right: 0.5rem;
          margin-bottom: 0.5rem;
        }

        @media print {
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
    
    <img src="../../vistas/assets/images/logos/logo.png" width="300px" style="margin-bottom: 10px;" />
    <p class="domicilio">Calle Santuario #29, Centro</p>
    <p class="domicilio">San Miguel el Alto, Jal.</p>
    <p class="telefono"><b>Tel:</b> (347) 109 1990</p>
    <p class="fecha"><strong>Fecha:</strong> ' . $rowVentas[0]['Fecha_Registro'] . ' </p>
    <p class="fecha"><strong>Folio:</strong> ' . $folio . ' </p>
    ' . $tabla . '
    <div class="pago">
      <p>Total: $' . number_format($rowVentas[0]['Total'], 2) . '</p>
      <p>Pagó con: $' . number_format(($rowVentas[0]['Total'] + $rowVentas[0]['Cambio']), 2) . '</p>
      <p>Cambio: $' . number_format($rowVentas[0]['Cambio'], 2) . '</p>
    </div>
    <footer>
      <div class="detalles">
        <strong>Detalles:</strong> '.$rowVentas[0]['Detalles'].'
        <p class="vendedor">Realizó la venta: ' . $rowVentas[0]['Nombre'] . '</p>
      </div>
      <p class="separador">********************************</p>
      <p class="separador">********************************</p>
      <p class="gracias">¡Gracias por su compra!</p>
      <p class="separador">********************************</p>
      <p class="separador">********************************</p>
    </footer>
    </body>
    <script>
    window.print();

    function imprimir() {
      window.print();
    }

    function funcionDespues() {
      //parent.document.documentElement.requestFullscreen();
      setTimeout(function(){
        parent.document.getElementById("bAbrirCaja").click();
      }, 150);
    }
    </script>
  </html>';

  echo $html;
?>
