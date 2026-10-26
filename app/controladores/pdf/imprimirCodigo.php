<?php
  session_start();
  include '../../modelo/m_modelo.php';
  if (!isset($_SESSION['user_punto_venta'])) return http_response_code(400);
  $omodelo = new m_modelo();
  $tipo = isset($_GET['tipo']) ? $omodelo->link->real_escape_string($_GET['tipo']) : '';
  $id = isset($_GET['id']) ? $omodelo->link->real_escape_string($_GET['id']) : '';
  
  if ($tipo == 'lote') {
    $idLote = isset($_GET['idLote']) ? $omodelo->link->real_escape_string($_GET['idLote']) : $id;
    $query = "SELECT p.Codigo, p.Descripcion, l.ID_Lote, l.Nombre_Lote, DATE_FORMAT(l.Fecha_Caducidad, '%d/%m/%Y') AS Caducidad 
              FROM inventario_lotes l 
              INNER JOIN inventario i ON l.FK_Inventario = i.ID_Inventario 
              INNER JOIN productos p ON i.FK_Producto = p.ID_Producto 
              WHERE l.ID_Lote = '$idLote'";
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        $codigoCompleto = $row[0]['Codigo'] . '_' . $row[0]['ID_Lote'];
        echo '<!DOCTYPE HTML>
          <html lang="es">
          <head>
            <meta charset="UTF-8">
            <title>Imprimir Código Lote</title>
            <script src="../../vistas/assets/plugins/JsBarcode/jquery.min.js"></script>
            <script src="../../vistas/assets/plugins/JsBarcode/JsBarcode.all.min.js"></script>
            <style>
              .etiqueta {
                position: relative;
                width: 80mm;
                height: 40mm;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                margin-top: 15px;
                page-break-after: always;
              }
              @media print {
                @page { size: 80mm 40mm; margin: 0; }
                body { margin: 0; width: 80mm; height: 40mm; font-size: 10px; }
              }
            </style>
          </head>
          <body>
              <div class="etiqueta">
                <span style="position: absolute; top: -5px; font-family: arial; font-weight: bold; font-size: 11px; text-align: center; width: 95%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'.$row[0]['Descripcion'].'</span>
                <canvas id="canvasLote" style="height: 60%;"></canvas>
                <span style="position: absolute; bottom: 2px; font-family: arial; font-size: 11px; text-align: center; width: 100%;">Lote: <b>'.$row[0]['Nombre_Lote'].'</b> | Cad: <b>'.$row[0]['Caducidad'].'</b></span>
              </div>
              <script>
                $(document).ready(function(){
                  $("#canvasLote").JsBarcode("'.$codigoCompleto.'", {displayValue: true, fontSize: 16});
                  window.print();
                });    
              </script>
          </body>
          </html>';
      } else {
        echo 'Lote no encontrado.';
      }
    }
  } else if ($tipo == 'todosLotes') {
    $idInventario = isset($_GET['idInventario']) ? $omodelo->link->real_escape_string($_GET['idInventario']) : $id;
    $query = "SELECT p.Codigo, p.Descripcion, l.ID_Lote, l.Nombre_Lote, DATE_FORMAT(l.Fecha_Caducidad, '%d/%m/%Y') AS Caducidad 
              FROM inventario_lotes l 
              INNER JOIN inventario i ON l.FK_Inventario = i.ID_Inventario 
              INNER JOIN productos p ON i.FK_Producto = p.ID_Producto 
              WHERE l.FK_Inventario = '$idInventario' 
              ORDER BY l.Fecha_Caducidad ASC, l.ID_Lote ASC";
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        echo '<!DOCTYPE HTML>
          <html lang="es">
          <head>
            <meta charset="UTF-8">
            <title>Imprimir Todos los Lotes</title>
            <script src="../../vistas/assets/plugins/JsBarcode/jquery.min.js"></script>
            <script src="../../vistas/assets/plugins/JsBarcode/JsBarcode.all.min.js"></script>
            <style>
              .etiqueta {
                position: relative;
                width: 80mm;
                height: 40mm;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                margin-top: 15px;
                page-break-after: always;
              }
              @media print {
                @page { size: 80mm 40mm; margin: 0; }
                body { margin: 0; width: 80mm; height: 40mm; font-size: 10px; }
              }
            </style>
          </head>
          <body>';

        for ($i = 0; $i < $numerofilas; $i++) {
          $codigoCompleto = $row[$i]['Codigo'] . '_' . $row[$i]['ID_Lote'];
          echo '<div class="etiqueta">
                  <span style="position: absolute; top: -5px; font-family: arial; font-weight: bold; font-size: 11px; text-align: center; width: 95%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">'.$row[$i]['Descripcion'].'</span>
                  <canvas id="canvasLote_' . $i . '" style="height: 60%;"></canvas>
                  <span style="position: absolute; bottom: 2px; font-family: arial; font-size: 11px; text-align: center; width: 100%;">Lote: <b>'.$row[$i]['Nombre_Lote'].'</b> | Cad: <b>'.$row[$i]['Caducidad'].'</b></span>
                </div>
                <script>
                  $("#canvasLote_' . $i . '").JsBarcode("' . $codigoCompleto . '", {displayValue: true, fontSize: 16});
                </script>';
        }

        echo '  <script>
                  $(document).ready(function(){
                    window.print();
                  });
                </script>
              </body>
            </html>';
      } else {
        echo 'No hay lotes registrados para este producto en inventario.';
      }
    }
  } else if ($tipo == 'uno') {
    $query = "SELECT Codigo, Descripcion FROM productos WHERE ID_Producto = '$id'";
    $row = $omodelo->_consultar($query);
    $numerofilas = $omodelo->numerofilas;

    if ($row == 'si') {
      echo "Error: " . mysqli_error($omodelo->link);
    } else {
      if ($numerofilas > 0) {
        echo '<!DOCTYPE HTML>
          <html lang="en-US">
          <head>
            <meta charset="UTF-8">
            <title></title>
            <script src="../../vistas/assets/plugins/JsBarcode/jquery.min.js"></script>
            <script src="../../vistas/assets/plugins/JsBarcode/JsBarcode.all.min.js"></script>
            <style>
              .etiqueta {
                position: relative;
                width: 80mm;
                height: 40mm;
                align-items: center;
                justify-content: center;
                margin-top: 15px;
              }

              @media print {
                @page {
                  size: 80mm 40mm; /* Tamaño de la etiqueta */
                  margin: 0; /* Sin márgenes */
                }

                body {
                  margin: 0; /* Sin márgenes en el cuerpo */
                  width: 80mm; /* Ancho de la etiqueta */
                  height: 40mm; /* Alto de la etiqueta */
                  align-items: center; /* Centra verticalmente */
                  justify-content: center; /* Centra horizontalmente */
                  font-size: 10px; /* Ajusta el tamaño de la fuente */
                }
              }
            </style>
          </head>
          <body>
              <div class="etiqueta">
                <span style="position: absolute; top: -10px; font-family: arial;">'.$row[0]['Descripcion'].'</span>
                <canvas id="1" style="height: 80%;"></canvas>
              </div>
              
              <script>
                $(document).ready(function(){
                $("#1").JsBarcode("'.$row[0]['Codigo'].'",{displayValue:true,fontSize:20});
              });    
              </script>
          </body>
          </html>';
      }
    }
  }else{
    $id_encoded = $_GET['id'];
    $id_decoded = urldecode($id_encoded);
    $id = json_decode($id_decoded, true);

    echo '<!DOCTYPE HTML>
      <html lang="en-US">
      <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="../../vistas/assets/plugins/JsBarcode/jquery.min.js"></script>
        <script src="../../vistas/assets/plugins/JsBarcode/JsBarcode.all.min.js"></script>
        <style>
            .etiqueta {
              position: relative;
              width: 80mm;
              height: 40mm;
              align-items: center;
              justify-content: center;
              margin-top: 15px;
            }

          @media print {
            @page {
              size: 80mm 40mm; /* Tamaño de la etiqueta */
              margin: 0; /* Sin márgenes */
            }

            body {
              margin: 0; /* Sin márgenes en el cuerpo */
              width: 80mm; /* Ancho de la etiqueta */
              height: 40mm; /* Alto de la etiqueta */
              align-items: center; /* Centra verticalmente */
              justify-content: center; /* Centra horizontalmente */
              font-size: 10px; /* Ajusta el tamaño de la fuente */
            }
          }
        </style>
      </head>
      <body>';

      for ($i = 0; $i < count($id); $i++) {
        $query = "SELECT Codigo, Descripcion FROM productos WHERE ID_Producto = '".$id[$i]."'";
        $row = $omodelo->_consultar($query);
        $numerofilas = $omodelo->numerofilas;

        if ($row == 'si') {
          echo "Error: " . mysqli_error($omodelo->link);
        } else {
          if ($numerofilas > 0) {
            echo '<div class="etiqueta">
              <span style="position: absolute; top: -10px; font-family: arial;">'.$row[0]['Descripcion'].'</span>
              <canvas id="' . $i . '" style="height: 80%;"></canvas>
            </div>
            <script>
              $("#' . $i . '").JsBarcode("' . $row[0]['Codigo'] . '",{displayValue:true,fontSize:20});
            </script>';
          }
        }
      }

      echo '</body>
    </html>';
  }
?>


