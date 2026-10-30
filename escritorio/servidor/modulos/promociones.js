// Promociones: solo consulta, con el mismo formato de la web.
const db = require('../db');
const { tabla } = require('./catalogo');
const { html } = require('../util');

module.exports = {
  consultar(p) {
    if (!db.existeTabla('promociones') || !db.todos('PRAGMA table_info(promociones)').some((c) => c.name === 'ID_Promocion')) {
      return JSON.stringify({});
    }
    return tabla(p, {
      tablas: ['promociones', 'productos', 'clasificaciones'],
      campos: 'pr.*, p.Codigo AS CodigoProducto, p.Descripcion AS DescripcionProducto, c.Nombre AS NombreClasificacion',
      desde: 'promociones pr LEFT JOIN productos p ON p.ID_Producto = pr.FK_Producto LEFT JOIN clasificaciones c ON c.ID_Clasificacion = pr.FK_Clasificacion',
      buscarEn: "pr.Nombre || ' ' || IFNULL(p.Descripcion, '') || ' ' || IFNULL(c.Nombre, '')",
      columnas: { Nombre: 'pr.Nombre', Tipo: 'pr.Tipo', Cantidad: 'pr.Cantidad', Beneficio: 'pr.Tipo_Beneficio' },
      porDefecto: 'pr.Nombre',
      fila: (r) => {
        const aplica = r.Tipo === 'Producto'
          ? `<b>Producto:</b> ${html(r.CodigoProducto)} - ${html(r.DescripcionProducto)}`
          : `<b>Clasificación:</b> ${html(r.NombreClasificacion)}`;
        let beneficio;
        if (r.Tipo_Beneficio === 'Precio_Fijo') {
          beneficio = `Precio fijo: <span class="dinero">${r.Precio_Fijo}</span>`;
        } else if (r.Tipo_Descuento === 'Porcentaje') {
          beneficio = `Descuento: <span class="cantidad">${r.Descuento}</span>%`;
        } else {
          beneficio = `Descuento: <span class="dinero">${r.Descuento}</span> por pieza`;
        }
        return {
          ID: String(r.ID_Promocion),
          Nombre: html(r.Nombre),
          Tipo: aplica,
          Cantidad: `${r.Modo_Cantidad === 'Paquete' ? 'Paquete de' : 'Mínimo'} <span class="cantidad">${r.Cantidad}</span>`,
          Beneficio: beneficio,
          Estatus: String(r.Estado) === '1' ? '<span class="badge rounded-pill bg-success">Activa</span>' : '<span class="badge rounded-pill bg-secondary">Inactiva</span>'
        };
      }
    });
  }
};
