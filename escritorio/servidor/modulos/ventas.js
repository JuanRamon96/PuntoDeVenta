// Port de c_ventas.php sobre las ventas del equipo que aún no se suben: misma pantalla y acciones de la web
// (ver detalles, modificar, cancelar, eliminar, reimprimir). Pagos y devoluciones se hacen en la web.
const db = require('../db');
const sesion = require('../sesion');
const caja = require('./caja');
const { html, ahora, fechaHumana, busqueda, orden, paginar, uuid } = require('../util');

const SOLO_WEB = 'Esta función solo está disponible en el sistema web.';

function borrarDetalles(idVenta) {
  // Primero las devoluciones (su trigger quita lo que habían regresado) y luego los renglones (los triggers devuelven inventario y lotes)
  db.db.run('DELETE FROM devoluciones WHERE FK_Detalle_Venta IN (SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?)', [idVenta]);
  db.db.run('DELETE FROM detalles_ventas_lotes WHERE FK_Detalle_Venta IN (SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?)', [idVenta]);
  db.db.run('DELETE FROM impuestos_ventas WHERE FK_Detalle_Venta IN (SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?)', [idVenta]);
  db.db.run('DELETE FROM detalles_ventas WHERE FK_Venta = ?', [idVenta]);
}

function ventaLocal(id) {
  return db.uno('SELECT * FROM ventas WHERE ID_Venta = ? AND Subido = 0', [id]);
}

function nombreCliente(c) {
  if (!c) {
    return '';
  }
  return c.Tipo === 'Moral' ? c.Razon_Social : `${c.Nombre} ${c.Primer_Apellido || ''} ${c.Segundo_Apellido || ''}`.trim();
}

const detalles = {
  // Tabla "Ver detalles"
  venta(p) {
    const b = busqueda(p.buscar, "Descripcion || Precio || Cantidad || Descuento || Total", 'AND');
    const ord = orden({ Producto: 'Descripcion', Precio: 'Precio', Cantidad: 'Cantidad', Descuento: 'Descuento', Total: 'Total' }[p.ordenColumna] || 'ID_Detalle_Venta',
      p.orden, ['Descripcion', 'Precio', 'Cantidad', 'Descuento', 'Total', 'ID_Detalle_Venta'], 'ID_Detalle_Venta');
    const filas = db.todos(`SELECT * FROM detalles_ventas WHERE FK_Venta = ? ${b.sql} ORDER BY ${ord} ${paginar(p.limit, p.pagina)}`, [p.idVenta, ...b.params]);
    if (!filas.length) {
      return JSON.stringify({});
    }
    const total = db.valor(`SELECT COUNT(*) FROM detalles_ventas WHERE FK_Venta = ? ${b.sql}`, [p.idVenta, ...b.params]);
    const data = filas.map((d) => {
      const bruto = d.Precio * d.Cantidad;
      const subtotal = bruto - d.Descuento;
      const imps = db.todos('SELECT * FROM impuestos_ventas WHERE FK_Detalle_Venta = ? ORDER BY Nombre', [d.ID_Detalle_Venta]);
      const impuestos = imps.length
        ? imps.map((i) => `<p class="m-0"><b>${html(i.Clase)} ${html(i.Nombre)} <span class="porcentaje">${i.Porcentaje}</span></b> (<span class="dinero">${subtotal * (i.Porcentaje / 100)}</span>) - <b>${html(i.Tipo_Factor)}</b></p>`).join('')
        : '<br>Sin impuestos';
      let lotes = '';
      if (db.existeTabla('inventario_lotes')) {
        const ls = db.todos(`SELECT dvl.FK_Lote, SUM(dvl.Cantidad) AS Cantidad, IFNULL(l.Nombre_Lote, 'Lote #' || dvl.FK_Lote) AS Nombre_Lote, l.Fecha_Caducidad
          FROM detalles_ventas_lotes dvl LEFT JOIN inventario_lotes l ON dvl.FK_Lote = l.ID_Lote WHERE dvl.FK_Detalle_Venta = ? GROUP BY dvl.FK_Lote`, [d.ID_Detalle_Venta]);
        if (ls.length) {
          lotes = '<div class="mt-1 d-flex flex-column align-items-center gap-1">' + ls.map((l) => {
            const cad = l.Fecha_Caducidad && !String(l.Fecha_Caducidad).startsWith('0000') ? ` <span class="text-muted" style="font-size: 10px;">(${String(l.Fecha_Caducidad).slice(0, 10).split('-').reverse().join('/')})</span>` : '';
            return `<div class="badge bg-light text-dark border py-1 px-2 text-start" style="font-size: 11px; font-weight: normal;"><i class="fas fa-boxes text-muted me-1"></i><b>${html(l.Nombre_Lote)}</b>
              <span class="badge bg-dark text-white font-monospace ms-1" style="font-size: 10px;">${d.Codigo ? html(d.Codigo) + '_' : ''}${l.FK_Lote}</span>: <span class="cantidad fw-bold">${l.Cantidad}</span>${cad}</div>`;
          }).join('') + '</div>';
        }
      }
      return {
        ID: String(d.ID_Detalle_Venta),
        Producto: html(d.Descripcion),
        Precio: `<span class="dinero">${d.Precio}</span>`,
        Cantidad: `<span class="cantidad">${d.Cantidad}</span>${lotes}`,
        Descuento: `<span class="dinero">${d.Descuento}</span> (<span class="porcentaje">${bruto ? (d.Descuento / bruto) * 100 : 0}%</span>)`,
        Impuestos: `<b>SUB: <span class="dinero">${subtotal}</span></b>${impuestos}`,
        Total: `<span class="dinero">${d.Total}</span>`
      };
    });
    return JSON.stringify({ data, totales: { NumRows: String(total) } });
  },

  // Datos para el modal "Modificar venta"
  modificar(p) {
    const v = db.uno('SELECT * FROM ventas WHERE ID_Venta = ?', [p.idVenta]);
    if (!v) {
      return JSON.stringify([]);
    }
    const c = v.FK_Cliente !== 0 ? db.uno('SELECT * FROM clientes WHERE ID_Cliente = ?', [v.FK_Cliente]) : null;
    const productos = db.todos('SELECT * FROM detalles_ventas WHERE FK_Venta = ?', [v.ID_Venta]).map((d) => ({
      ID_Detalle_Venta: String(d.ID_Detalle_Venta), FK_Producto: String(d.FK_Producto), Codigo: d.Codigo || '0', Descripcion: d.Descripcion,
      Precio: String(d.Precio), Cantidad: String(d.Cantidad), Descuento: String(d.Descuento), Total: String(d.Total),
      Impuestos: db.todos('SELECT * FROM impuestos_ventas WHERE FK_Detalle_Venta = ? ORDER BY Nombre', [d.ID_Detalle_Venta]).map((i) => ({
        ID_Impuesto_Venta: String(i.ID_Impuesto_Venta), Nombre: i.Nombre, Porcentaje: String(i.Porcentaje), Clave_CFDI: i.Clave_CFDI, Tipo_Factor: i.Tipo_Factor, Clase: i.Clase
      }))
    }));
    return JSON.stringify({
      ID_Venta: String(v.ID_Venta), FK_Usuario: String(v.FK_Usuario), FK_Cliente: String(v.FK_Cliente), Cliente: nombreCliente(c),
      FK_Detalles_Caja: String(v.FK_Detalles_Caja), Descuento: String(v.Descuento), Total: String(v.Total), Tipo_Pago: v.Tipo_Pago,
      Pago: String(v.Pago), Cambio: String(v.Cambio), Detalles: v.Detalles, Fecha_Registro: v.Fecha_Registro, Estatus: String(v.Estatus),
      Fecha_Cancelacion: v.Fecha_Cancelacion, Regrezo_Inventario: String(v.Regrezo_Inventario), Productos: productos
    });
  },

  // Abonos: saldo para el modal de pago
  totales(p) {
    const v = db.uno('SELECT Total, Pago, Cambio, IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = ventas.ID_Venta), 0) AS TotalPagos FROM ventas WHERE ID_Venta = ?', [p.id]);
    return JSON.stringify(v ? { Total: String(v.Total), Pago: String(v.Pago), Cambio: String(v.Cambio), TotalPagos: String(v.TotalPagos) } : []);
  },

  pago(p) {
    const v = ventaLocal(p.id);
    if (!v) {
      return 'Offline~Esta venta ya se subió al sistema web; registra el pago allá.';
    }
    const turno = db.uno('SELECT ID_Detalle_Caja FROM detalles_caja ORDER BY ID_Detalle_Caja DESC LIMIT 1');
    db.ejecutar('INSERT INTO ventas_pagos (UUID, FK_Venta, Concepto, Monto, Tipo_Pago, Detalles, FK_Usuario, FK_Detalle_Caja, Fecha_Registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
      [uuid(), v.ID_Venta, String(p.conceptoPagoVenta || '').trim(), Number(String(p.importePagoVenta || '0').replace(/,/g, '')) || 0, String(p.tipoDePagoVenta || '').trim(),
        String(p.detallesPagoVenta || '').trim(), sesion.id(), turno ? turno.ID_Detalle_Caja : 0, ahora()]);
    return 'Correcto';
  },

  pagos(p) {
    const ord = orden({ Fecha: 'p.Fecha_Registro', Concepto: 'p.Concepto', Tipo_Pago: 'p.Tipo_Pago', Monto: 'p.Monto' }[p.ordenColumna] || 'p.Fecha_Registro', p.orden,
      ['p.Fecha_Registro', 'p.Concepto', 'p.Tipo_Pago', 'p.Monto'], 'p.Fecha_Registro');
    const filas = db.todos(`SELECT p.*, IFNULL(u.Nombre || ' ' || u.Primer_Apellido || ' ' || u.Segundo_Apellido, '') AS Usuario FROM ventas_pagos p
      LEFT JOIN usuarios u ON u.ID_Usuario = p.FK_Usuario WHERE p.FK_Venta = ? ORDER BY ${ord} ${paginar(p.limit, p.pagina)}`, [p.id]);
    if (!filas.length) {
      return JSON.stringify({});
    }
    const cajaN = db.uno('SELECT Nombre FROM cajas LIMIT 1');
    const data = filas.map((r) => ({
      ID: String(r.ID_Pago),
      Fecha: fechaHumana(r.Fecha_Registro),
      Concepto: html(r.Concepto),
      Tipo_Pago: html(r.Tipo_Pago),
      Monto: `<span class="dinero">${r.Monto}</span>`,
      Detalles: html(r.Detalles) + (cajaN ? `<br><span class="text-muted">Caja:</span> ${html(cajaN.Nombre)}` : '') + (r.Usuario.trim() ? `<br><span class="text-muted">Usuario:</span> ${html(r.Usuario)}` : ''),
      Comprobante: '',
      Acciones: `<button type="button" class="btn btn-danger btn-sm bEliminarPagoVenta" attrVenta="${r.FK_Venta}" attrID="${r.ID_Pago}" archivo=""><i class="fas fa-trash"></i></button>
        <a href="controladores/pdf/ticketPago.php?id=${r.ID_Pago}" target="_blank" class="btn btn-primary btn-sm bImprimirPagoVenta"><i class="fas fa-print"></i></a>`
    }));
    return JSON.stringify({ data, totales: { NumRows: String(db.valor('SELECT COUNT(*) FROM ventas_pagos WHERE FK_Venta = ?', [p.id])) } });
  },

  eliminarPago(p) {
    const pago = db.uno('SELECT p.ID_Pago FROM ventas_pagos p JOIN ventas v ON v.ID_Venta = p.FK_Venta WHERE p.ID_Pago = ? AND v.Subido = 0', [p.id]);
    if (!pago) {
      return 'Offline~Este pago ya se subió al sistema web; elimínalo allá.';
    }
    db.ejecutar('DELETE FROM ventas_pagos WHERE ID_Pago = ?', [pago.ID_Pago]);
    return 'Correcto';
  },

  // Devoluciones: renglones con la cantidad devuelta
  devoluciones(p) {
    return db.todos(`SELECT d.*, IFNULL((SELECT SUM(Cantidad) FROM devoluciones WHERE FK_Detalle_Venta = d.ID_Detalle_Venta), 0) AS CanDev
      FROM detalles_ventas d WHERE d.FK_Venta = ? ORDER BY d.Descripcion`, [p.id]).map((d) => `<tr attrID="${d.ID_Detalle_Venta}">
        <td>${html(d.Descripcion)}</td><td><span class="dinero">${d.Precio}</span></td><td><span class="cantidad">${d.Cantidad}</span></td>
        <td><span class="dinero">${d.Descuento}</span></td><td><span class="dinero">${d.Total}</span></td>
        <td><input type="number" class="form-control" min="0" max="${d.Cantidad}" value="${d.CanDev}" /></td></tr>`).join('');
  },

  devolucion(p) {
    let productos;
    try {
      productos = JSON.parse(p.productos);
    } catch (e) {
      return 'Error: productos inválidos';
    }
    for (const x of productos) {
      const d = db.uno('SELECT d.ID_Detalle_Venta, d.Cantidad, v.Subido FROM detalles_ventas d JOIN ventas v ON v.ID_Venta = d.FK_Venta WHERE d.ID_Detalle_Venta = ?', [x.id]);
      if (!d || d.Subido) {
        return 'Offline~Esta venta ya se subió al sistema web; registra la devolución allá.';
      }
      if (Number(x.cantidad) > d.Cantidad || Number(x.cantidad) < 0) {
        return 'Offline~No se puede devolver más de lo que se vendió.';
      }
    }
    db.transaccion(() => {
      for (const x of productos) {
        // Se reemplaza la devolución anterior: los triggers quitan la vieja y aplican la nueva
        db.db.run('DELETE FROM devoluciones WHERE FK_Detalle_Venta = ?', [x.id]);
        if (Number(x.cantidad) > 0) {
          db.db.run('INSERT INTO devoluciones (FK_Detalle_Venta, Cantidad) VALUES (?, ?)', [x.id, Number(x.cantidad)]);
        }
      }
    });
    return 'Correcto';
  },

  // Guardar el modal "Modificar venta": se reemplazan los renglones y se recalcula el inventario del equipo
  modificarVenta(p) {
    const v = ventaLocal(p.id);
    if (!v) {
      return 'Offline~Esta venta ya se subió al sistema web; modifícala allá.';
    }
    let productos;
    try {
      productos = JSON.parse(p.productos);
    } catch (e) {
      return 'Error: productos inválidos';
    }
    const nuevos = productos.map((x) => ({ id: Number(x.idProd) || 0, descripcion: x.descripcion, precio: x.precio, cantidad: x.cantidad, descuento: x.descuento, total: x.total, impuestos: x.impuestos || [], lotes: [] }));

    const total = Number(p.total) || 0;
    const pago = Number(p.pago) || 0;
    const cambio = Number(p.cambio) || 0;
    db.transaccion(() => {
      db.db.run('UPDATE ventas SET FK_Cliente = ?, Descuento = ?, Total = ?, Tipo_Pago = ?, Pago = ?, Cambio = ?, Detalles = ?, Estatus = ? WHERE ID_Venta = ?',
        [Number(p.cliente) || 0, Number(p.descuento) || 0, total, String(p.tipoPago || ''), pago, cambio, String(p.detalles || ''), (pago - cambio) - total >= 0 ? 0 : 1, v.ID_Venta]);
      borrarDetalles(v.ID_Venta);
      for (const prod of nuevos) {
        caja.insertarDetalle(v.ID_Venta, prod);
      }
    });
    return 'Correcto';
  }
};

module.exports = {
  // Tabla de ventas, mismas columnas que la web
  consultar(p) {
    if (!db.existeTabla('clientes')) {
      return JSON.stringify({});
    }
    const b = busqueda(p.buscar, "v.Folio || ' ' || v.Fecha_Registro || ' ' || v.Tipo_Pago || ' ' || v.Total || ' ' || IFNULL(c.Nombre, '')", 'AND');
    const col = { Fecha: 'v.Fecha_Registro', Folio: 'v.ID_Venta', Tipo: 'v.Tipo_Pago', Total: 'v.Total', Pago: 'v.Pago', Cambio: 'v.Cambio', Estatus: 'v.Estatus' }[p.ordenColumna] || 'v.Fecha_Registro';
    const ord = orden(col, p.orden, ['v.Fecha_Registro', 'v.ID_Venta', 'v.Tipo_Pago', 'v.Total', 'v.Pago', 'v.Cambio', 'v.Estatus'], 'v.Fecha_Registro');
    const desde = 'ventas v LEFT JOIN clientes c ON c.ID_Cliente = v.FK_Cliente WHERE v.Subido = 0';
    const filas = db.todos(`SELECT v.*, c.Tipo AS TipoCliente, c.Nombre AS NombreC, c.Primer_Apellido, c.Segundo_Apellido, c.Razon_Social, c.RFC, c.Telefono, c.Email, c.CP
      FROM ${desde} ${b.sql} ORDER BY ${ord} ${paginar(p.limit, p.pagina)}`, b.params);
    if (!filas.length) {
      return JSON.stringify({});
    }
    const sumas = db.uno(`SELECT COUNT(*) AS Num, IFNULL(SUM(CASE WHEN v.Estatus <> 2 THEN v.Total END), 0) AS Total,
      IFNULL(SUM(CASE WHEN v.Estatus <> 2 THEN v.Pago - v.Cambio END), 0) AS Pagado FROM ${desde} ${b.sql}`, b.params);
    const caja = db.uno('SELECT Nombre FROM cajas LIMIT 1');
    const yo = sesion.usuario();
    const permiso = (i) => sesion.esAdmin() || (yo && yo.PermisosLista.Ventas && yo.PermisosLista.Ventas[i] === '1');

    const data = filas.map((v) => {
      const pagado = v.Pago - v.Cambio + Number(db.valor('SELECT IFNULL(SUM(Monto), 0) FROM ventas_pagos WHERE FK_Venta = ?', [v.ID_Venta]));
      const restante = Math.max(0, v.Total - pagado);
      const afavor = Math.max(0, pagado - v.Total);
      let estatus = "<span class='badge rounded-pill bg-success'>Completada</span>";
      if (v.Estatus === 1) estatus = "<span class='badge rounded-pill bg-warning'>Pendiente</span>";
      if (v.Estatus === 2) estatus = "<span class='badge rounded-pill bg-danger'>Cancelada</span>";

      const acciones = [];
      if (v.Estatus !== 2 && permiso(2)) acciones.push(`<button class="btn btn-warning btn-sm bModificarVenta" attrID="${v.ID_Venta}" title="Modificar Venta"><i class="fas fa-pencil"></i></button>`);
      if (permiso(3)) acciones.push(`<button class="btn btn-danger btn-sm bEliminarVenta" attrID="${v.ID_Venta}" title="Eliminar Venta"><i class="fa-solid fa-trash"></i></button>`);
      if (v.Estatus !== 2 && permiso(4)) acciones.push(`<button class="btn btn-outline-danger btn-sm bCancelarVenta" attrID="${v.ID_Venta}" title="Cancelar Venta"><i class="fa fa-ban"></i></button>`);
      if (v.Estatus !== 2 && permiso(6) && v.Tipo_Pago === 'Crédito') acciones.push(`<button class="btn btn-info btn-sm bPagosVenta" attrID="${v.ID_Venta}" title="Agregar Pago"><i class="fa fa-dollar"></i></button>`);
      if (v.Estatus !== 2 && permiso(7)) acciones.push(`<button class="btn btn-secondary btn-sm bDevoluciones" attrID="${v.ID_Venta}" title="Devoluciones"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>`);
      const verPagos = permiso(6) && v.Tipo_Pago === 'Crédito' ? `<button class="btn btn-link btn-sm bVerPagosVen" attrID="${v.ID_Venta}" title="Ver Pagos">Ver Pagos</button>` : '';
      const imprimir = permiso(5) ? `<a class="btn btn-sm btn-primary" href="controladores/pdf/ticketVenta.php?id=${v.ID_Venta}" target="_blank" title="Imprimir ticket"><i class="fa-solid fa-ticket"></i></a>` : '';

      const cliente = v.FK_Cliente !== 0 && v.NombreC !== null
        ? `<span class="nombre">${html(v.TipoCliente === 'Moral' ? v.Razon_Social : `${v.NombreC} ${v.Primer_Apellido || ''} ${v.Segundo_Apellido || ''}`)}</span><br>
          <span class="text-muted">Tel: </span>${html(v.Telefono)}`
        : 'Publico en General';

      return {
        ID: String(v.ID_Venta),
        Fecha: fechaHumana(v.Fecha_Registro) + (v.Turno ? `<br><b>Turno: </b>${html(v.Turno)}` : ''),
        Folio: html(v.Folio),
        Tipo: html(v.Tipo_Pago),
        Cliente: cliente,
        Total: `Subtotal: <span class="dinero">${v.Total + v.Descuento}</span><br>Descuento: <span class="dinero">${v.Descuento}</span><br>Total: <span class="dinero" style="font-size: 22px;">${v.Total}</span>`,
        Pago: `<span class="dinero">${v.Pago}</span>`,
        Cambio: `<span class="dinero">${v.Cambio}</span>`,
        Estatus: `${estatus}<br><b>Pagado: </b><span class="dinero">${pagado}</span><br><b>Restante: </b><span class="dinero">${restante}</span><br><b>A favor: </b><span class="dinero">${afavor}</span>`,
        Detalles: `<div class="d-flex align-items-center justify-content-evenly"><div class="d-flex flex-column"><span><strong>Caja:&nbsp;</strong>${html(caja ? caja.Nombre : '')}</span>
          <button class="btn btn-link btn-sm bDetalles" idVenta="${v.ID_Venta}" title="Ver Detalles">Ver Detalles</button> ${verPagos} ${imprimir}
          ${String(v.Detalles).trim() !== '' ? '<br>Detalles: ' + html(v.Detalles) : ''}</div></div>`,
        Acciones: acciones.join(' ')
      };
    });
    return JSON.stringify({
      data,
      totales: {
        NumRows: String(sumas.Num),
        Total: `<b>Total:</b> <span class="dinero">${sumas.Total}</span><br><b>Pagado:</b> <span class="dinero">${sumas.Pagado}</span>`
      }
    });
  },

  detalles(p) {
    return detalles[p.tipo] ? detalles[p.tipo](p) : SOLO_WEB;
  },

  // Cancelar (igual que la web): se marca cancelada y, si se pide, regresan los productos al inventario
  modificar(p) {
    const v = ventaLocal(p.idVenta);
    if (!v) {
      return 'Offline~Esta venta ya se subió al sistema web; cancélala allá.';
    }
    const regresar = String(p.regresarInventario) === '1' ? 1 : 0;
    db.transaccion(() => {
      db.db.run('UPDATE ventas SET Estatus = 2, Fecha_Cancelacion = ?, Regrezo_Inventario = ? WHERE ID_Venta = ?', [ahora(), regresar, v.ID_Venta]);
      if (regresar && v.Estatus !== 2) {
        db.db.run(`UPDATE inventario SET Cantidad = Cantidad + IFNULL((SELECT SUM(d.Cantidad) FROM detalles_ventas d WHERE d.FK_Venta = ? AND d.FK_Producto = inventario.FK_Producto), 0)
          WHERE FK_Producto IN (SELECT FK_Producto FROM detalles_ventas WHERE FK_Venta = ?)`, [v.ID_Venta, v.ID_Venta]);
      }
    });
    return 'Correcto';
  },

  // Eliminar: se borra del equipo y las existencias regresan
  eliminar(p) {
    const v = ventaLocal(p.idVenta);
    if (!v) {
      return 'Offline~Esta venta ya se subió al sistema web; elimínala allá.';
    }
    db.transaccion(() => {
      if (v.Estatus === 2 && v.Regrezo_Inventario === 1) {
        // Ya se había regresado al cancelar: que los triggers no lo regresen dos veces
        db.db.run(`UPDATE inventario SET Cantidad = Cantidad - IFNULL((SELECT SUM(d.Cantidad) FROM detalles_ventas d WHERE d.FK_Venta = ? AND d.FK_Producto = inventario.FK_Producto), 0)
          WHERE FK_Producto IN (SELECT FK_Producto FROM detalles_ventas WHERE FK_Venta = ?)`, [v.ID_Venta, v.ID_Venta]);
      }
      borrarDetalles(v.ID_Venta);
      db.db.run('DELETE FROM ventas WHERE ID_Venta = ?', [v.ID_Venta]);
    });
    return 'Correcto';
  }
};
