// Búsqueda de clientes del modal "Modificar venta" (c_clientes.php): solo consulta.
const { tabla } = require('./catalogo');
const { html } = require('../util');

module.exports = {
  consultar(p) {
    return tabla(p, {
      tablas: ['clientes'],
      campos: '*',
      desde: 'clientes',
      buscarEn: "IFNULL(Nombre, '') || ' ' || IFNULL(Primer_Apellido, '') || ' ' || IFNULL(Segundo_Apellido, '') || ' ' || IFNULL(Razon_Social, '') || ' ' || IFNULL(Telefono, '')",
      columnas: { Nombre: 'Nombre', Contacto: 'Telefono' },
      porDefecto: 'Nombre',
      fila: (c) => {
        const nombre = c.Tipo === 'Moral' ? c.Razon_Social : `${c.Nombre} ${c.Primer_Apellido || ''} ${c.Segundo_Apellido || ''}`.trim();
        const domicilio = [c.Calle, c.No_Exterior ? '#' + c.No_Exterior : '', c.Colonia, c.CP].filter((x) => x).join(' ');
        return {
          ID: String(c.ID_Cliente),
          Nombre: `${html(nombre)}<br><span class="text-muted">RFC: </span>${html(c.RFC || '')}`,
          Domicilio: html(domicilio),
          Contacto: `<span class="text-muted">Tel: </span>${html(c.Telefono || '')}<br><span class="text-muted">Email: </span>${html(c.Email || '')}`
        };
      }
    });
  }
};
