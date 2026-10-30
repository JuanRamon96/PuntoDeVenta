// Inventario: existencias del equipo (lo bajado del servidor menos lo vendido aquí y aún no subido).
const db = require('../db');
const { tabla } = require('./catalogo');
const { html, numero } = require('../util');

function caducidad(f) {
  const t = String(f || '');
  return t === '' || t.startsWith('0000') ? '' : ` (cad. ${t.slice(0, 10).split('-').reverse().join('/')})`;
}

module.exports = {
  consultar(p) {
    const hayLotes = db.existeTabla('inventario_lotes') && db.todos('PRAGMA table_info(inventario_lotes)').length > 0;
    return tabla(p, {
      tablas: ['productos', 'inventario'],
      campos: 'p.ID_Producto, p.Codigo, p.Descripcion, p.Costo, p.Precio, p.Precio_Mayoreo, p.Stock_Minimo, IFNULL(i.Cantidad, 0) AS Existencia, i.ID_Inventario',
      desde: 'productos p LEFT JOIN inventario i ON i.FK_Producto = p.ID_Producto',
      buscarEn: "p.Codigo || ' ' || p.Descripcion",
      columnas: { Codigo: 'p.Codigo', Descripcion: 'p.Descripcion', Existencia: 'Existencia', Costo: 'p.Costo', Precio: 'p.Precio', Precio_Mayoreo: 'p.Precio_Mayoreo' },
      porDefecto: 'p.Codigo',
      fila: (r) => {
        const bajo = Number(r.Existencia) <= Number(r.Stock_Minimo || 0);
        let lotes = '';
        if (hayLotes && r.ID_Inventario) {
          lotes = db.todos('SELECT Nombre_Lote, Fecha_Caducidad, Cantidad FROM inventario_lotes WHERE FK_Inventario = ? AND Cantidad > 0 ORDER BY Fecha_Caducidad', [r.ID_Inventario])
            .map((l) => `<p class="m-0">${html(l.Nombre_Lote)}: <span class="cantidad">${l.Cantidad}</span>${caducidad(l.Fecha_Caducidad)}</p>`).join('');
        }
        return {
          ID: String(r.ID_Producto),
          Codigo: html(r.Codigo),
          Descripcion: html(r.Descripcion),
          Existencia: `<span class="cantidad" ${bajo ? 'style="color: #dc3545; font-weight: bold;"' : ''}>${r.Existencia}</span>`,
          Costo: `<span class="dinero">${r.Costo}</span>`,
          Precio: `<span class="dinero">${r.Precio}</span>`,
          Precio_Mayoreo: `<span class="dinero">${r.Precio_Mayoreo}</span>`,
          Totales: `<b>Costo:</b> $${numero(Number(r.Existencia) * Number(r.Costo))}<br><b>Venta:</b> $${numero(Number(r.Existencia) * Number(r.Precio))}`,
          Lotes: lotes || '<span class="text-muted">-</span>'
        };
      }
    });
  }
};
