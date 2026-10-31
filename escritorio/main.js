// VentasTool Offline: muestra la misma interfaz web y responde las peticiones a "index.php" desde la base local.
const { app, BrowserWindow, protocol, net, Menu, shell, session } = require('electron');
const path = require('path');
const fs = require('fs');
const { pathToFileURL } = require('url');
const db = require('./servidor/db');
const rutas = require('./servidor/rutas');

const WEB = path.join(__dirname, 'web');

protocol.registerSchemesAsPrivileged([
  { scheme: 'app', privileges: { standard: true, secure: true, supportFetchAPI: true, corsEnabled: true, stream: true } }
]);

// Un solo programa abierto a la vez: dos ventanas escribiendo la misma base la dañarían
if (!app.requestSingleInstanceLock()) {
  app.quit();
}

let ventana = null;

function crearVentana() {
  ventana = new BrowserWindow({
    width: 1366,
    height: 860,
    minWidth: 1024,
    minHeight: 640,
    title: 'VentasTool',
    icon: path.join(WEB, 'vistas/assets/images/logos/v.png'),
    webPreferences: { contextIsolation: true, nodeIntegration: false, spellcheck: false }
  });
  ventana.maximize();
  ventana.loadURL('app://pos/index.php');

  ventana.webContents.setWindowOpenHandler(({ url }) => {
    // Tickets (venta, corte, reimpresión): se imprimen directo en la impresora configurada, sin diálogo ni vista previa
    if (url.startsWith('app://') && url.includes('/controladores/pdf/')) {
      imprimir(url).catch((e) => avisarError(e.message));
      return { action: 'deny' };
    }
    if (url.startsWith('app://')) {
      return { action: 'allow' };
    }
    // Ligas externas (ej. mapas) en el navegador del sistema
    shell.openExternal(url);
    return { action: 'deny' };
  });
}

function avisarError(mensaje) {
  if (ventana) {
    ventana.webContents.executeJavaScript(`Swal.fire({ icon: 'error', title: 'No se pudo imprimir', html: ${JSON.stringify(mensaje)} + '<br><br>Revisa la impresora en el menú <b>Impresora</b>.' })`).catch(() => {});
  }
}

// Carga el ticket en una ventana oculta y lo manda a la impresora con el ancho de papel configurado
async function imprimir(url) {
  const cfg = rutas.configImpresora();
  const hoja = new BrowserWindow({ show: false, width: 600, height: 900, webPreferences: { contextIsolation: true } });
  try {
    await hoja.loadURL(url + (url.includes('?') ? '&' : '?') + 'impresion=1');
    // Espera fuentes e imágenes (logo) antes de medir el alto
    const altoPx = await hoja.webContents.executeJavaScript(`document.fonts.ready
      .then(() => Promise.all([...document.images].map((i) => i.complete ? 1 : new Promise((r) => { i.onload = i.onerror = r; }))))
      .then(() => Math.ceil(document.documentElement.getBoundingClientRect().height))`);
    const altoMm = Math.max(60, Math.ceil((altoPx * 25.4) / 96) + 5);
    await new Promise((resolve, reject) => {
      hoja.webContents.print({
        silent: true,
        printBackground: true,
        deviceName: cfg.nombre || '',
        copies: cfg.copias,
        margins: { marginType: 'none' },
        pageSize: { width: cfg.ancho * 1000, height: altoMm * 1000 }
      }, (ok, motivo) => (ok ? resolve() : reject(new Error(motivo === 'Print job canceled' ? 'Se canceló la impresión.' : `La impresora respondió: ${motivo || 'error desconocido'}.`))));
    });
  } finally {
    hoja.destroy();
  }
}

function archivoEstatico(ruta) {
  // Fotos de productos, logo y clasificaciones se bajan a la carpeta de datos del equipo
  const descargadas = path.join(app.getPath('userData'), 'imagenes');
  for (const carpeta of ['productos', 'configuracion', 'clasificaciones', 'usuarios']) {
    const prefijo = `vistas/assets/images/${carpeta}/`;
    if (ruta.startsWith(prefijo)) {
      const local = path.join(descargadas, carpeta, path.basename(ruta));
      if (fs.existsSync(local)) {
        return local;
      }
    }
  }
  const archivo = path.normalize(path.join(WEB, ruta));
  return archivo.startsWith(WEB) && fs.existsSync(archivo) && fs.statSync(archivo).isFile() ? archivo : null;
}

async function manejar(request) {
  const url = new URL(request.url);
  const ruta = decodeURIComponent(url.pathname.replace(/^\/+/, '')) || 'index.php';

  if (ruta.endsWith('.php')) {
    const params = Object.fromEntries(url.searchParams);
    if (request.method === 'POST') {
      if ((request.headers.get('content-type') || '').includes('multipart/form-data')) {
        // Formularios con archivo (p. ej. comprobante de pago): los archivos no se guardan en el equipo
        for (const [clave, valor] of (await request.formData()).entries()) {
          if (typeof valor === 'string') {
            params[clave] = valor;
          }
        }
      } else {
        Object.assign(params, Object.fromEntries(new URLSearchParams(await request.text())));
      }
    }
    let cuerpo;
    try {
      cuerpo = await rutas.atender(ruta, request.method, params);
    } catch (e) {
      console.error(e);
      cuerpo = 'Error: ' + e.message;
    }
    return new Response(cuerpo, { headers: { 'content-type': 'text/html; charset=utf-8', 'cache-control': 'no-store' } });
  }

  const archivo = archivoEstatico(ruta);
  if (!archivo) {
    return new Response('No encontrado', { status: 404 });
  }
  return net.fetch(pathToFileURL(archivo).toString());
}

app.on('second-instance', () => {
  if (ventana) {
    if (ventana.isMinimized()) {
      ventana.restore();
    }
    ventana.focus();
  }
});

app.whenReady().then(async () => {
  // En Mac sin menú de Edición no funcionan copiar/pegar con Cmd
  Menu.setApplicationMenu(process.platform === 'darwin' ? Menu.buildFromTemplate([{ role: 'appMenu' }, { role: 'editMenu' }]) : null);
  await session.defaultSession.clearCache(); // al actualizar la app se usan sus archivos nuevos
  await db.abrir(path.join(app.getPath('userData'), 'ventastool.sqlite'));
  rutas.iniciar({
    carpetaDatos: app.getPath('userData'),
    web: WEB,
    version: app.getVersion(),
    impresoras: () => ventana.webContents.getPrintersAsync(),
    imprimir
  });
  protocol.handle('app', manejar);
  crearVentana();
});

app.on('window-all-closed', () => app.quit());
