// Port de c_clientes.php para la pantalla Clientes de la web (misma vista y mismo modal).
// Los clientes del sistema web son solo de consulta; los dados de alta en la caja del equipo (ID negativo)
// se pueden modificar o eliminar hasta que se suben con "Sincronizar" o "Subir ventas".
const db = require('../db');
const sesion = require('../sesion');
const { tabla } = require('./catalogo');
const { html, uuid, fechaHumana } = require('../util');

const txt = (v) => (v === null || v === undefined ? '' : String(v));

function nombreDe(c) {
  return c.Tipo === 'Moral' ? txt(c.Razon_Social) : `${txt(c.Nombre)} ${txt(c.Primer_Apellido)} ${txt(c.Segundo_Apellido)}`.trim();
}

function domicilioDe(c) {
  let d = txt(c.Calle);
  if (txt(c.No_Exterior)) d += ' #' + c.No_Exterior;
  if (txt(c.No_Interior)) d += ' int.' + c.No_Interior;
  if (txt(c.Colonia)) d += ', Col. ' + c.Colonia;
  if (txt(c.CP)) d += ' C.P. ' + c.CP;
  if (txt(c.Ciudad)) d += ', ' + c.Ciudad;
  if (txt(c.Estado)) d += ' ' + c.Estado;
  if (txt(c.Pais)) d += ', ' + c.Pais;
  return d;
}

function esNuevo(id) {
  return Number(id) < 0 && !!db.uno('SELECT ID_Cliente FROM clientes_nuevos WHERE ID_Cliente = ?', [id]);
}

module.exports = {
  // Tabla de la pantalla Clientes y del buscador del modal "Modificar venta" (usan columnas distintas de la misma respuesta)
  consultar(p) {
    return tabla(p, {
      tablas: ['clientes'],
      campos: '*',
      desde: 'clientes',
      buscarEn: "IFNULL(Tipo, '') || ' ' || IFNULL(Nombre, '') || ' ' || IFNULL(Primer_Apellido, '') || ' ' || IFNULL(Segundo_Apellido, '') || ' ' || IFNULL(Razon_Social, '') || ' ' || IFNULL(RFC, '') || ' ' || IFNULL(Telefono, '') || ' ' || IFNULL(Email, '')",
      columnas: { Nombre: 'Nombre', Tipo: 'Tipo', Fecha: 'Fecha_Registro', Domicilio: 'Calle', Contacto: 'Telefono', Acciones: 'ID_Cliente' },
      porDefecto: 'Nombre',
      fila: (c) => {
        const nuevo = Number(c.ID_Cliente) < 0;
        let contacto = '';
        if (txt(c.Telefono)) contacto += `Tel: <b>${html(c.Telefono)}</b><br>`;
        if (txt(c.Segundo_Telefono)) contacto += `Segundo Tel: <b>${html(c.Segundo_Telefono)}</b><br>`;
        if (txt(c.Email)) contacto += `Email: <b class="email">${html(c.Email)}</b><br>`;
        if (txt(c.Contacto)) contacto += `Contacto: <b>${html(c.Contacto)}</b><br>`;
        const acciones = nuevo
          ? (sesion.puede('Clientes', 3) ? `<button type="button" class="btn btn-sm btn-warning bModificarCliente" title="Modificar Cliente" attrID="${c.ID_Cliente}"><i class="fas fa-pencil"></i></button> ` : '') +
            (sesion.puede('Clientes', 4) ? `<button type="button" class="btn btn-sm btn-danger bEliminarCliente" title="Eliminar Cliente" attrID="${c.ID_Cliente}"><i class="fas fa-trash"></i></button>` : '')
          : '';
        return {
          ID: String(c.ID_Cliente),
          Fecha: nuevo ? '<span class="badge rounded-pill bg-warning text-dark">Nuevo, sin subir</span>' : fechaHumana(c.Fecha_Registro),
          Tipo: html(c.Tipo),
          Nombre: `${html(nombreDe(c))}<br><span class="text-muted">RFC: </span>${html(c.RFC)}`,
          Domicilio: html(domicilioDe(c)),
          Contacto: contacto || `<span class="text-muted">Tel: </span>${html(c.Telefono)}`,
          Acciones: acciones
        };
      }
    });
  },

  // Datos para el modal (detalles tipo=cliente)
  detalles(p) {
    if (p.tipo !== 'cliente') {
      return 'Esta función solo está disponible en el sistema web.';
    }
    const c = db.uno('SELECT * FROM clientes WHERE ID_Cliente = ?', [p.id]);
    if (!c) {
      return JSON.stringify([]);
    }
    const datos = {};
    for (const [k, v] of Object.entries(c)) {
      datos[k] = txt(v);
    }
    datos.Puesto = txt(c.Puesto_Contacto);
    datos.Direcciones = db.todos('SELECT * FROM direcciones_cliente WHERE FK_Cliente = ?', [p.id]).map((d) => {
      const r = {};
      for (const [k, v] of Object.entries(d)) {
        r[k] = txt(v);
      }
      return r;
    });
    return JSON.stringify(datos);
  },

  // Guardar el modal: solo clientes nuevos del equipo
  modificar(p) {
    if (!sesion.puede('Clientes', 3)) {
      return 'Offline~Tu usuario no tiene permiso para modificar clientes.';
    }
    const id = Number(p.id);
    if (!esNuevo(id)) {
      return 'Offline~Este cliente ya está en el sistema web; modifícalo allá.';
    }
    let direcciones = [];
    try {
      direcciones = JSON.parse(p.direcciones || '[]');
    } catch (e) {
      direcciones = [];
    }
    const campos = {
      Tipo: p.tipoCliente, Nombre: p.nombre, Primer_Apellido: p.primerApellido, Segundo_Apellido: p.segundoApellido, Sexo: p.sexo,
      Razon_Social: p.razonSocial, RFC: p.rfc, Regimen_CFDI: p.regimen, Telefono: p.telefono, Segundo_Telefono: p.segundoTelefono, Email: p.email,
      Calle: p.calle, No_Exterior: p.noExterior, No_Interior: p.noInterior, Colonia: p.colonia, CP: p.cp, Ciudad: p.ciudad, Estado: p.estado, Pais: p.pais,
      Contacto: p.contacto, Puesto_Contacto: p.puesto, Telefono_Contacto: p.telContacto, Email_Contacto: p.emailContacto, Latitud: p.latitud, Longitud: p.longitud
    };
    const columnas = db.todos('PRAGMA table_info(clientes)').map((c) => c.name);
    const usar = Object.keys(campos).filter((k) => columnas.includes(k) && campos[k] !== undefined);

    db.transaccion(() => {
      db.db.run(`UPDATE clientes SET ${usar.map((k) => `"${k}" = ?`).join(', ')} WHERE ID_Cliente = ?`, [...usar.map((k) => String(campos[k] ?? '').trim()), id]);
      db.db.run('UPDATE clientes_nuevos SET Nombre = ?, Telefono = ? WHERE ID_Cliente = ?', [String(p.nombre || '').trim(), String(p.telefono || '').trim(), id]);

      // Domicilios: se reemplazan como en la web; las ventas del equipo que usaban uno lo conservan por posición
      const anteriores = db.todos('SELECT ID_Direccion FROM direcciones_cliente WHERE FK_Cliente = ? ORDER BY ID_Direccion', [id]).map((d) => d.ID_Direccion);
      db.db.run('DELETE FROM direcciones_cliente WHERE FK_Cliente = ?', [id]);
      db.db.run('DELETE FROM direcciones_nuevas WHERE FK_Cliente = ?', [id]);
      direcciones.forEach((d, i) => {
        const nuevoId = Math.min(Number(db.valor('SELECT MIN(ID_Direccion) FROM direcciones_nuevas') || 0), Number(db.valor('SELECT MIN(ID_Direccion) FROM direcciones_cliente') || 0), 0) - 1;
        const valores = [d.calle, d.noExterior, d.noInterior, d.cp, d.colonia, d.ciudad, d.estado, d.pais, d.detalles].map((v) => String(v ?? ''));
        db.db.run(`INSERT INTO direcciones_nuevas (ID_Direccion, UUID, FK_Cliente, Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais, Detalles)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`, [nuevoId, uuid(), id, ...valores]);
        db.insertarFila('direcciones_cliente', {
          ID_Direccion: nuevoId, FK_Cliente: id, Calle: valores[0], No_Exterior: valores[1], No_Interior: valores[2], CP: valores[3], Colonia: valores[4],
          Ciudad: valores[5], Estado: valores[6], Pais: valores[7], Detalles: valores[8], Latitud: String(d.latitud ?? ''), Longitud: String(d.longitud ?? '')
        });
        if (anteriores[i] !== undefined) {
          db.db.run('UPDATE ventas SET FK_Direccion = ? WHERE FK_Direccion = ? AND Subido = 0', [nuevoId, anteriores[i]]);
        }
      });
      // Domicilios que ya no existen: esas ventas usan el domicilio principal del cliente
      for (const viejo of anteriores.slice(direcciones.length)) {
        db.db.run('UPDATE ventas SET FK_Direccion = 0 WHERE FK_Direccion = ? AND Subido = 0', [viejo]);
      }
    });
    return 'Correcto';
  },

  eliminar(p) {
    if (!sesion.puede('Clientes', 4)) {
      return 'Offline~Tu usuario no tiene permiso para eliminar clientes.';
    }
    const id = Number(p.id);
    if (!esNuevo(id)) {
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
  },

  insertar() {
    return 'Offline~Los clientes se dan de alta desde la caja, al cobrar.';
  }
};
