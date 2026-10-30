// Hace el papel de app/index.php: recibe los mismos parámetros (metodo, accion, tipo...) y responde igual que el PHP.
const fs = require('fs');
const path = require('path');
const db = require('./db');
const sesion = require('./sesion');
const sync = require('./sync');
const vistas = require('./vistas');
const tickets = require('./tickets');

const modulos = {
  login: require('./modulos/login'),
  cajas: require('./modulos/cajas'),
  caja: require('./modulos/caja'),
  ventas: require('./modulos/ventas'),
  productos: require('./modulos/productos'),
  inventario: require('./modulos/inventario'),
  promociones: require('./modulos/promociones'),
  usuarios: require('./modulos/usuarios'),
  offline: require('./modulos/offline')
};

let config = null;

function iniciar(opciones) {
  config = opciones;
  vistas.iniciar(opciones);
  tickets.iniciar(opciones);
  sync.iniciar(opciones);
}

function paginaEnlace() {
  return fs.readFileSync(path.join(config.web, 'offline', 'enlace.html'), 'utf8');
}

async function atender(ruta, metodoHttp, p) {
  if (ruta === 'controladores/pdf/ticketVenta.php') {
    return sesion.usuario() ? tickets.venta(p.id) : '';
  }
  if (ruta === 'controladores/pdf/ticketCaja.php') {
    return sesion.usuario() ? tickets.corte(p.id) : '';
  }
  if (ruta === 'controladores/pdf/ticketAbrir.php') {
    return sesion.usuario() ? tickets.abrir() : '';
  }
  if (ruta !== 'index.php') {
    return 'Esta función solo está disponible en el sistema web.';
  }

  // Primer uso: el equipo aún no está enlazado a una cuenta
  if (!db.ajuste('enlace')) {
    if (p.accion === 'enlazar') {
      return sync.enlazar(p.codigo);
    }
    return paginaEnlace();
  }

  if (p.metodo === 'renovar') {
    return '';
  }

  if (sesion.usuario() && p.accion !== 'registro') {
    if (p.metodo === undefined) {
      return vistas.layout();
    }
    if (p.metodo === 'cambiar') {
      return vistas.contenido(p.accion);
    }
    const modulo = modulos[p.accion];
    const fn = modulo && modulo[p.metodo];
    if (typeof fn !== 'function') {
      return 'Esta función solo está disponible en el sistema web.';
    }
    return await fn(p);
  }

  if (p.accion === 'login' && p.correo !== undefined && p.contrasena !== undefined) {
    return modulos.login.entrar(p);
  }
  if (p.accion === 'enlazar') {
    return sync.enlazar(p.codigo); // reenlace desde la pantalla de inicio de sesión
  }
  // Sin sesión solo se permite bajar datos (p. ej. si alguien cambió su contraseña en la web)
  if (p.accion === 'offline' && p.tipo === 'sincronizar') {
    return modulos.offline.detalles(p);
  }
  return vistas.login();
}

module.exports = { iniciar, atender };
