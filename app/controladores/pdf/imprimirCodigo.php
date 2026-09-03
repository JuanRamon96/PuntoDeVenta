<?php
  session_start();
  include '../../modelo/m_modelo.php';
  if (!isset($_SESSION['user_punto_venta'])) return http_response_code(400);
  extract($_GET);

  $omodelo = new m_modelo();
  $id = $omodelo->link->real_escape_string($id);
  $tipo = $omodelo->link->real_escape_string($tipo);
  
  if ($tipo == 'uno') {
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


