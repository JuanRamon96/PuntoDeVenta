// Productos: solo consulta (se cambian en la web y se bajan con "Sincronizar").
const { tabla } = require('./catalogo');
const caja = require('./caja');
const { html } = require('../util');

module.exports = {
  consultar(p) {
    return tabla(p, {
      tablas: ['productos', 'clasificaciones'],
      campos: "p.*, IFNULL(c.Nombre, 'Sin clasificación') AS Clasificacion",
      desde: 'productos p LEFT JOIN clasificaciones c ON c.ID_Clasificacion = p.FK_Clasificacion',
      buscarEn: "p.Codigo || ' ' || p.Descripcion || ' ' || IFNULL(c.Nombre, '')",
      columnas: { Codigo: 'p.Codigo', Descripcion: 'p.Descripcion', Clase: 'p.Clase', Precio: 'p.Precio', Precio_Mayoreo: 'p.Precio_Mayoreo', Clasificacion: 'Clasificacion' },
      porDefecto: 'p.Descripcion',
      fila: (r) => {
        const foto = r.Foto ? `vistas/assets/images/productos/${r.Foto}` : 'vistas/assets/images/producto-generico.png';
        const impuestos = caja.impuestosDe(r.ID_Producto);
        return {
          ID: String(r.ID_Producto),
          Codigo: `<div style="background-image: url('${foto}'), url('vistas/assets/images/producto-generico.png'); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; border-radius: 100%;"></div>${html(r.Codigo)}`,
          Descripcion: html(r.Descripcion),
          Clase: html(r.Clase),
          Precio: `<span class="dinero">${r.Precio}</span>`,
          Precio_Mayoreo: `<span class="dinero">${r.Precio_Mayoreo}</span>`,
          Clasificacion: html(r.Clasificacion),
          Impuestos: impuestos.length
            ? impuestos.map((i) => `<p class="m-0"><b>${html(i.Nombre)}</b> (<span class="${i.Tipo_Factor === 'Cuota' ? 'dinero' : 'porcentaje'}">${i.Porcentaje}</span>)</p>`).join('')
            : 'Sin impuestos'
        };
      }
    });
  }
};
