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

  // Ligas externas (ej. mapas) en el navegador del sistema
  ventana.webContents.setWindowOpenHandler(({ url }) => {
    if (url.startsWith('app://')) {
      return { action: 'allow' };
    }
    shell.openExternal(url);
    return { action: 'deny' };
  });
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
      Object.assign(params, Object.fromEntries(new URLSearchParams(await request.text())));
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
  rutas.iniciar({ carpetaDatos: app.getPath('userData'), web: WEB, version: app.getVersion() });
  protocol.handle('app', manejar);
  crearVentana();
});

app.on('window-all-closed', () => app.quit());
