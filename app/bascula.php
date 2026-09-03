<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lectura de Báscula Rhino</title>
  <style>
    #modal-carga {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 9999;
      justify-content: center;
      align-items: center;
    }

    #modal-carga div {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
    }

    .spinner {
      margin: 10px auto;
      border: 4px solid #ccc;
      border-top: 4px solid #007bff;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <h2>Peso leído: <span id="peso">0.000 kg</span></h2>
  <button id="conectar">Conectar Báscula</button>
  <button id="leer">Leer Peso</button>

  <div id="modal-carga">
    <div>
      <p>Obteniendo peso...</p>
      <div class="spinner"></div>
    </div>
  </div>

  <script>
    let port;
    let reader;
    const decoder = new TextDecoder();

    const modal = document.getElementById('modal-carga');
    const pesoLabel = document.getElementById('peso');

    const mostrarModal = () => modal.style.display = 'flex';
    const ocultarModal = () => modal.style.display = 'none';

    document.getElementById('conectar').addEventListener('click', async () => {
      try {
        port = await navigator.serial.requestPort();
        await port.open({ baudRate: 9600 });
        console.log("Puerto abierto");
        reader = port.readable.getReader();
      } catch (err) {
        console.error("Error al conectar:", err);
      }
    });

    document.getElementById('leer').addEventListener('click', async () => {
      if (!port) {
        alert("Primero conecta la báscula.");
        return;
      }

      mostrarModal();

      try {
        const writer = port.writable.getWriter();
        await writer.write(new TextEncoder().encode("P\r\n"));  // o prueba con "S\r\n" si no responde
        writer.releaseLock();

        const { value } = await reader.read();
        const text = decoder.decode(value);
        console.log('Dato recibido:', text);

        const match = text.match(/([\d.]+)\s*kg/);
        if (match) {
          const peso = parseFloat(match[1]);
          pesoLabel.innerText = `${peso.toFixed(3)} kg`;
        } else {
          pesoLabel.innerText = "Dato inválido";
        }
      } catch (err) {
        console.error("Error leyendo:", err);
        pesoLabel.innerText = "Error en lectura";
      } finally {
        ocultarModal();
      }
    });
  </script>
</body>
</html>