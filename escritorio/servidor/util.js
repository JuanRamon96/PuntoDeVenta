// Equivalentes de las funciones de PHP/MySQL que usa el sistema web.
const crypto = require('crypto');

const dos = (n) => String(n).padStart(2, '0');

// Fecha local 'Y-m-d H:i:s' (como date() de PHP)
function ahora(d = new Date()) {
  return `${d.getFullYear()}-${dos(d.getMonth() + 1)}-${dos(d.getDate())} ${dos(d.getHours())}:${dos(d.getMinutes())}:${dos(d.getSeconds())}`;
}

function aFecha(texto) {
  const m = String(texto || '').match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?/);
  if (!m) {
    return null;
  }
  return new Date(+m[1], +m[2] - 1, +m[3], +(m[4] || 0), +(m[5] || 0), +(m[6] || 0));
}

// DATE_FORMAT(x, '%d-%m-%Y %r') -> 30-10-2026 09:10:00 AM
function fechaHumana(texto) {
  const d = aFecha(texto);
  if (!d) {
    return '';
  }
  const h = d.getHours() % 12 || 12;
  return `${dos(d.getDate())}-${dos(d.getMonth() + 1)}-${d.getFullYear()} ${dos(h)}:${dos(d.getMinutes())}:${dos(d.getSeconds())} ${d.getHours() < 12 ? 'AM' : 'PM'}`;
}

// date('d-m-Y h:i A')
function fechaCorta(d = new Date()) {
  const h = d.getHours() % 12 || 12;
  return `${dos(d.getDate())}-${dos(d.getMonth() + 1)}-${d.getFullYear()} ${dos(h)}:${dos(d.getMinutes())} ${d.getHours() < 12 ? 'AM' : 'PM'}`;
}

// number_format($n, 2)
function numero(n, decimales = 2) {
  const v = Number(n) || 0;
  return v.toLocaleString('en-US', { minimumFractionDigits: decimales, maximumFractionDigits: decimales });
}

function html(texto) {
  return String(texto ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c]));
}

function uuid() {
  return crypto.randomUUID();
}

// Arma el "WHERE campo REGEXP palabra AND ..." de las búsquedas de las tablas (igual que en PHP)
function busqueda(buscar, expresion, prefijo = 'WHERE') {
  const palabras = String(buscar || '').trim().split(' ').filter((p) => p !== '');
  if (!palabras.length) {
    return { sql: '', params: [] };
  }
  return { sql: `${prefijo} ` + palabras.map(() => `(${expresion}) REGEXP ?`).join(' AND '), params: palabras };
}

// ORDER BY seguro: solo columnas conocidas
function orden(columna, direccion, permitidas, porDefecto) {
  const col = permitidas.includes(columna) ? columna : porDefecto;
  return `${col} ${String(direccion).toLowerCase() === 'desc' ? 'DESC' : 'ASC'}`;
}

function paginar(limit, pagina) {
  const l = Math.max(1, parseInt(limit, 10) || 10);
  const p = Math.max(1, parseInt(pagina, 10) || 1);
  return `LIMIT ${l} OFFSET ${(p - 1) * l}`;
}

module.exports = { ahora, aFecha, fechaHumana, fechaCorta, numero, html, uuid, busqueda, orden, paginar };
