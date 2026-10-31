// Acciones propias de la app: sincronizar, subir ventas, estado y reenlace (accion=offline).
const db = require('../db');
const sync = require('../sync');
const sesion = require('../sesion');
const turno = require('../turno');
const { fechaHumana } = require('../util');

function estado() {
  const enlace = db.ajuste('enlace') || {};
  const ultima = db.ajuste('ultimaSync');
  return {
    caja: enlace.caja ? enlace.caja.nombre : '',
    dispositivo: enlace.dispositivo ? enlace.dispositivo.nombre : '',
    negocio: enlace.negocio || '',
    ultimaSync: ultima ? fechaHumana(ultima) : 'nunca',
    pendientes: sync.pendientes(),
    cajaAbierta: turno.abierta(),
    desenlazado: !!db.ajuste('desenlazado')
  };
}

// Si en la web cambiaron los permisos del usuario, se aplican al sincronizar sin cerrar sesión
function refrescarSesion() {
  const u = sesion.usuario();
  if (u && db.existeTabla('usuarios')) {
    const fila = db.uno('SELECT * FROM usuarios WHERE ID_Usuario = ?', [u.ID_Usuario]);
    if (fila) {
      sesion.iniciar(fila);
    }
  }
}

async function intentar(fn) {
  try {
    const r = await fn();
    return JSON.stringify({ ok: true, ...r, estado: estado() });
  } catch (e) {
    return JSON.stringify({ ok: false, codigo: e.codigo || 'error', error: e.message, estado: estado() });
  }
}

module.exports = {
  async detalles(p) {
    if (p.tipo === 'estado') {
      return JSON.stringify({ ok: true, estado: estado() });
    }
    if (p.tipo === 'sincronizar') {
      return intentar(async () => {
        await sync.bajar();
        refrescarSesion();
        return {};
      });
    }
    if (p.tipo === 'subir') {
      return intentar(async () => {
        const r = await sync.subir();
        refrescarSesion();
        return r;
      });
    }
    if (p.tipo === 'desenlazar') {
      return intentar(async () => {
        await sync.desenlazar();
        sesion.cerrar();
        return {};
      });
    }
    if (p.tipo === 'enlazar') {
      const r = await sync.enlazar(p.codigo);
      return r.startsWith('Correcto')
        ? JSON.stringify({ ok: true, aviso: r.split('~')[1] || '', estado: estado() })
        : JSON.stringify({ ok: false, error: r.replace(/^Error~/, ''), estado: estado() });
    }
    return JSON.stringify({ ok: false, error: 'Acción desconocida' });
  }
};
