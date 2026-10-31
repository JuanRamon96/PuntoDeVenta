// Clientes: los del sistema web son solo de consulta; los dados de alta en la caja del equipo (ID negativo)
// se pueden modificar o eliminar hasta que se suben con "Sincronizar" o "Subir ventas".
const db = require('../db');
const { tabla } = require('./catalogo');
const { html } = require('../util');

function nombreDe(c) {
  return c.Tipo === 'Moral' ? c.Razon_Social : `${c.Nombre} ${c.Primer_Apellido || ''} ${c.Segundo_Apellido || ''}`.trim();
}

function domicilioDe(c) {
  return [c.Calle, c.No_Exterior ? '#' + c.No_Exterior : '', c.Colonia, c.CP].filter((x) => x).join(' ');
}

module.exports = {
  consultar(p) {
    const busca = "IFNULL(Nombre, '') || ' ' || IFNULL(Primer_Apellido, '') || ' ' || IFNULL(Segundo_Apellido, '') || ' ' || IFNULL(Razon_Social, '') || ' ' || IFNULL(Telefono, '')";

    // Pantalla Clientes de la app
    if (p.tipo === 'pantalla') {
      return tabla(p, {
        tablas: ['clientes'],
        campos: '*',
        desde: 'clientes',
        buscarEn: busca,
        columnas: { Nombre: 'Nombre', Telefono: 'Telefono', Origen: 'ID_Cliente' },
        porDefecto: 'Nombre',
        fila: (c) => {
          const local = Number(c.ID_Cliente) < 0;
          const direcciones = db.todos('SELECT * FROM direcciones_cliente WHERE FK_Cliente = ?', [c.ID_Cliente]).map(domicilioDe).filter((x) => x);
          const domicilio = [domicilioDe(c), ...direcciones].filter((x) => x).map(html).join('<br>');
          return {
            ID: String(c.ID_Cliente),
            Nombre: html(nombreDe(c)),
            Telefono: html(c.Telefono || ''),
            Domicilio: domicilio || '<span class="text-muted">-</span>',
            Origen: local ? '<span class="badge rounded-pill bg-warning text-dark">Nuevo, sin subir</span>' : '<span class="badge rounded-pill bg-secondary">Del sistema</span>',
            Acciones: local
              ? `<button type="button" class="btn btn-warning btn-sm bModificarClienteLocal" attrID="${c.ID_Cliente}" attrNombre="${html(c.Nombre)}" attrTelefono="${html(c.Telefono || '')}" title="Modificar"><i class="fas fa-pencil"></i></button>
                 <button type="button" class="btn btn-danger btn-sm bEliminarClienteLocal" attrID="${c.ID_Cliente}" title="Eliminar"><i class="fas fa-trash"></i></button>`
              : ''
          };
        }
      });
    }

    // Búsqueda del modal "Modificar venta" (c_clientes.php)
    return tabla(p, {
      tablas: ['clientes'],
      campos: '*',
      desde: 'clientes',
      buscarEn: busca,
      columnas: { Nombre: 'Nombre', Contacto: 'Telefono' },
      porDefecto: 'Nombre',
      fila: (c) => ({
        ID: String(c.ID_Cliente),
        Nombre: `${html(nombreDe(c))}<br><span class="text-muted">RFC: </span>${html(c.RFC || '')}`,
        Domicilio: html(domicilioDe(c)),
        Contacto: `<span class="text-muted">Tel: </span>${html(c.Telefono || '')}<br><span class="text-muted">Email: </span>${html(c.Email || '')}`
      })
    });
  },

  modificar(p) {
    const id = Number(p.id);
    if (!(id < 0) || !db.uno('SELECT ID_Cliente FROM clientes_nuevos WHERE ID_Cliente = ?', [id])) {
      return 'Offline~Este cliente ya está en el sistema web; modifícalo allá.';
    }
    const nombre = String(p.nombre || '').trim();
    if (nombre === '') {
      return 'Offline~El nombre es obligatorio.';
    }
    const telefono = String(p.telefono || '').trim();
    db.transaccion(() => {
      db.db.run('UPDATE clientes_nuevos SET Nombre = ?, Telefono = ? WHERE ID_Cliente = ?', [nombre, telefono, id]);
      db.db.run('UPDATE clientes SET Nombre = ?, Telefono = ? WHERE ID_Cliente = ?', [nombre, telefono, id]);
    });
    return 'Correcto';
  },

  eliminar(p) {
    const id = Number(p.id);
    if (!(id < 0) || !db.uno('SELECT ID_Cliente FROM clientes_nuevos WHERE ID_Cliente = ?', [id])) {
      return 'Offline~Este cliente ya está en el sistema web; elimínalo allá.';
    }
    if (db.valor('SELECT COUNT(*) FROM ventas WHERE FK_Cliente = ? AND Subido = 0', [id]) > 0) {
      return 'Offline~Este cliente tiene ventas en el equipo. Cámbialas a otro cliente o elimínalas antes de borrarlo.';
    }
    db.transaccion(() => {
      db.db.run('DELETE FROM direcciones_cliente WHERE FK_Cliente = ?', [id]);
      db.db.run('DELETE FROM direcciones_nuevas WHERE FK_Cliente = ?', [id]);
      db.db.run('DELETE FROM clientes WHERE ID_Cliente = ?', [id]);
      db.db.run('DELETE FROM clientes_nuevos WHERE ID_Cliente = ?', [id]);
    });
    return 'Correcto';
  }
};
