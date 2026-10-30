// Ventas hechas en el equipo que aún no se suben. Se pueden ver, reimprimir y eliminar; al subirlas desaparecen de aquí.
const db = require('../db');
const { tabla } = require('./catalogo');
const { html, fechaHumana } = require('../util');

function borrarVenta(idVenta) {
  // Los triggers devuelven el inventario y los lotes
  db.transaccion(() => {
    db.db.run('DELETE FROM detalles_ventas_lotes WHERE FK_Detalle_Venta IN (SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?)', [idVenta]);
    db.db.run('DELETE FROM impuestos_ventas WHERE FK_Detalle_Venta IN (SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?)', [idVenta]);
    db.db.run('DELETE FROM detalles_ventas WHERE FK_Venta = ?', [idVenta]);
    db.db.run('DELETE FROM ventas WHERE ID_Venta = ?', [idVenta]);
  });
}

module.exports = {
  consultar(p) {
    return tabla(p, {
      tablas: ['ventas', 'clientes', 'usuarios'],
      campos: `v.*, CASE WHEN v.FK_Cliente = 0 THEN 'Publico en General' ELSE IFNULL(c.Nombre, '') END AS Cliente,
        IFNULL(u.Nombre || ' ' || u.Primer_Apellido, '') AS Usuario`,
      desde: 'ventas v LEFT JOIN clientes c ON c.ID_Cliente = v.FK_Cliente LEFT JOIN usuarios u ON u.ID_Usuario = v.FK_Usuario WHERE v.Subido = 0',
      buscarEn: "v.Folio || ' ' || v.Fecha_Registro || ' ' || v.Tipo_Pago || ' ' || IFNULL(c.Nombre, '')",
      columnas: { Folio: 'v.ID_Venta', Fecha: 'v.Fecha_Registro', Cliente: 'Cliente', Tipo: 'v.Tipo_Pago', Total: 'v.Total' },
      porDefecto: 'v.ID_Venta',
      fila: (v) => ({
        ID: String(v.ID_Venta),
        Folio: html(v.Folio),
        Fecha: fechaHumana(v.Fecha_Registro),
        Cliente: html(v.Cliente),
        Usuario: html(v.Usuario),
        Tipo: html(v.Tipo_Pago),
        Total: `<span class="dinero">${v.Total}</span>`,
        Estatus: Number(v.Estatus) === 1 ? "<span class='badge rounded-pill bg-warning'>Pendiente</span>" : "<span class='badge rounded-pill bg-success'>Completada</span>",
        Acciones: `<button type="button" class="btn btn-sm btn-info bTicketVentaLocal" attrID="${v.ID_Venta}" title="Ver ticket"><i class="fas fa-receipt"></i></button>
          <button type="button" class="btn btn-sm btn-danger bEliminarVentaLocal" attrID="${v.ID_Venta}" title="Eliminar venta"><i class="fas fa-trash"></i></button>`
      })
    });
  },

  // Cancelar venta pendiente desde la caja (metodo=modificar&accion=ventas): en el equipo se elimina
  modificar(p) {
    const v = db.uno('SELECT ID_Venta FROM ventas WHERE ID_Venta = ? AND Subido = 0', [p.idVenta]);
    if (!v) {
      return 'Offline~Esta venta ya se subió al sistema web; cancélala allá.';
    }
    borrarVenta(v.ID_Venta);
    return 'Correcto';
  },

  eliminar(p) {
    return module.exports.modificar(p);
  }
};
