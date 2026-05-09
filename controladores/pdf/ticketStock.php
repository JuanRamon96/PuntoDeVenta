<?php
  session_start();
  include '../../modelo/m_modelo.php';
  if (!isset($_SESSION['user_punto_venta'])) return http_response_code(400);
  extract($_GET);

  $modelo = new m_modelo();

  $queryInve = "SELECT ID_Inventario, ID_Producto, Cantidad AS Existencia, Codigo, Descripcion, Costo, Precio, Stock_Minimo, Stock_Maximo FROM inventario INNER JOIN productos ON FK_Producto = ID_Producto WHERE Cantidad <= Stock_Minimo ORDER BY Existencia";
  $rowInve = $modelo->_consultar($queryInve);

  if ($rowInve == 'si') {
    echo "Error: " . mysqli_error($modelo->link);
    return;
  }

  $tabla = '
    <table>
      <thead>
        <tr>
          <th>Existencia</th>
          <th>Mínimo</th>
          <th>Máximo</th>
          <th>Costo</th>
          <th>Precio</th>
        </tr>
      </thead>
      <tbody>';

  $sumCosto = 0;
  $sumPrecio = 0;
  foreach ($rowInve as $detalle) {
    if ($detalle == null) continue;

    $tabla .= '
        <tr>
          <td colspan="3">'. $detalle['Codigo'] . ' ' . $detalle['Descripcion'] . '</td>
        </tr>
        <tr>
          <td>' . number_format($detalle['Existencia'], 2) . '</td>
          <td>' . number_format($detalle['Stock_Minimo'], 2) . '</td>
          <td>' . number_format($detalle['Stock_Maximo'], 2). '</td>
          <td>$' . number_format($detalle['Costo'], 2). '</td>
          <td>$' . number_format($detalle['Precio'], 2). '</td>
        </tr>';

    $sumCosto += $detalle['Costo'];
    $sumPrecio += $detalle['Precio'];
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
          font-size: 18px;
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
          font-size: 20px;
        }

        td[colspan="3"] {
          text-align: left;
          font-size: 22px;
        }

        .pago {
          width: 100%;
          font-size: 1.6rem;
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
          font-size: 14px;
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
    <body>
      <button type="submit" class="oculto-impresion" onclick="imprimir()">
        Imprimir
      </button> 
      <br>
      <p class="domicilio">STOCK EN MÍNIMOS</p>
      <br>
      
      ' . $tabla . '
      
      <div class="pago">
        <p>Total costo: $' . number_format($sumCosto, 2) . '</p>
        <p>Total Precio: $' . number_format($sumPrecio, 2) . '</p>
        <p>Diferencia: $' . number_format(($sumPrecio - $sumCosto), 2) . '</p>
      </div>
    </body>

    <script>
      window.print();

      function imprimir() {
        window.print();
      }
    </script>
  </html>';

  echo $html;
?>
