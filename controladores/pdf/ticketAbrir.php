<?php
  session_start();
  if (!isset($_SESSION['user_punto_venta'])) return http_response_code(400);

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
      </style>
    </head>
    <body onafterprint="funcionDespues()">
      <b>Abrir</b>
    </body>
    <script>
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
