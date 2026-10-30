// Estado de la caja enlazada. En la web vive en cajas.Estado / cajas.FK_Usuario; aquí se deduce de los turnos locales.
const db = require('./db');

const SIN_CIERRE = '0000-00-00 00:00:00';

function cajaId() {
  const enlace = db.ajuste('enlace');
  return enlace ? Number(enlace.caja.id) : 0;
}

function actual() {
  return db.uno('SELECT * FROM detalles_caja WHERE FK_Caja = ? ORDER BY ID_Detalle_Caja DESC LIMIT 1', [cajaId()]);
}

function abierta() {
  const t = actual();
  return !!t && t.Fecha_Cierre === SIN_CIERRE;
}

// Usuario que tiene "tomada" la caja (cajas.FK_Usuario en la web)
function usuarioEnCaja() {
  return abierta() ? Number(db.ajuste('cajaUsuario') || 0) : 0;
}

function tomar(idUsuario) {
  db.ajuste('cajaUsuario', Number(idUsuario));
}

module.exports = { SIN_CIERRE, cajaId, actual, abierta, usuarioEnCaja, tomar };
