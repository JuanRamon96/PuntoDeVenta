<?php
  session_start();
  include '../../modelo/m_modelo.php';
  if (!isset($_SESSION['user_punto_venta'])) return http_response_code(400);
  extract($_GET);

  $modelo = new m_modelo();
  $id = $modelo->link->real_escape_string($id);
  $query = "SELECT ID_Pago, IFNULL((SELECT Pago FROM ventas WHERE ID_Venta = FK_Venta), 0) AS Abono, FK_Venta AS Venta, Concepto, Monto, ventas_pagos.Tipo_Pago AS Tipo_Pago, ventas_pagos.Detalles AS Detalles, Total, IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = Venta), 0) AS Pagado, IFNULL(cajas.Nombre, '') AS Caja, DATE_FORMAT(ventas_pagos.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, CONCAT(usuarios.Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido) AS Usuario FROM ventas_pagos INNER JOIN ventas ON FK_Venta = ID_Venta LEFT JOIN detalles_caja ON FK_Detalle_Caja = ID_Detalle_Caja LEFT JOIN cajas ON FK_Caja = ID_Caja LEFT JOIN usuarios ON ventas_pagos.FK_Usuario = ID_Usuario WHERE ID_Pago = '$id'";
  $rowPago = $modelo->_consultar($query);
  $restante = 0;
  $favor = 0;

  if ($rowPago == 'si') {
    echo "Error: " . mysqli_error($modelo->link);
    return;
  }else{
    $restante = $rowPago[0]['Total'] - $rowPago[0]['Pagado'] - $rowPago[0]['Abono'];
    
    if($restante < 0){
      $restante = 0;
      $favor = $rowPago[0]['Pagado'] + $rowPago[0]['Abono'] - $rowPago[0]['Total'];
    }
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
          font-size: 1.5rem;
          font-weight: bold;
          margin-bottom: 10px;
        }

        .domicilio {
          font-size: 1rem;
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
    <body onafterprint="funcionDespues()">
     <button type="submit" class="oculto-impresion" onclick="imprimir()">
      Imprimir
     </button> 
    
    <img src="'.$logo.'" width="300px" style="margin-bottom: 4px;" />
    <p class="domicilio">'.$rowGeneral[0]['Nombre'].'</p>
    <p class="domicilio">'.$rowGeneral[0]['Domicilio'].'</p>
    <p class="telefono">'.$rowGeneral[0]['Telefono'].'</p>
    <p class="fecha"><strong>Fecha:</strong> ' . $rowPago[0]['Fecha_Registro'] . ' </p>
    <div class="pago">
      <p>Concepto: '. $rowPago[0]['Concepto'] . '</p>
      <p>Monto: $' . number_format($rowPago[0]['Monto'], 2) . '</p>
      <p>Tipo: ' . $rowPago[0]['Tipo_Pago'] . '</p>
      <br>
      <p>Total Venta: $' . number_format($rowPago[0]['Total'], 2) . '</p>
      <p>Pagado: $' . number_format(($rowPago[0]['Pagado'] + $rowPago[0]['Abono']), 2) . '</p>
      <p>Restante: $' . number_format($restante, 2) . '</p>
      <p>A favor: $' . number_format($favor, 2) . '</p>
    </div>
    <footer>
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
      }, 300);
    }
    </script>
  </html>';

  echo $html;
?>
