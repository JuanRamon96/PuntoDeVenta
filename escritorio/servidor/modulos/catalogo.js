// Utilidad común de las pantallas de solo consulta: arma la respuesta que espera myDataTable.
const db = require('../db');
const { busqueda, orden, paginar } = require('../util');

// opciones: { tablas, campos, desde, buscarEn, columnas (nombre en pantalla -> columna SQL), porDefecto, fila(r) }
function tabla(p, o) {
  if (!o.tablas.every((t) => db.existeTabla(t))) {
    return JSON.stringify({});
  }
  const b = busqueda(p.buscar, o.buscarEn, o.desde.includes(' WHERE ') ? 'AND' : 'WHERE');
  const columna = o.columnas[p.ordenColumna] || o.porDefecto;
  const ord = orden(columna, p.orden, Object.values(o.columnas), o.porDefecto);
  const filas = db.todos(`SELECT ${o.campos} FROM ${o.desde} ${b.sql} ORDER BY ${ord} ${paginar(p.limit, p.pagina)}`, b.params);
  if (!filas.length) {
    return JSON.stringify({});
  }
  const total = db.valor(`SELECT COUNT(*) FROM ${o.desde} ${b.sql}`, b.params);
  return JSON.stringify({ data: filas.map(o.fila), totales: { NumRows: String(total) } });
}

module.exports = { tabla };
