// Port de c_caja.php: mismas respuestas que el PHP, pero sobre la base local del equipo.
const db = require('../db');
const sesion = require('../sesion');
const turno = require('../turno');
const { ahora, fechaHumana, fechaCorta, numero, html, uuid, busqueda, orden, paginar } = require('../util');

const SOLO_WEB = 'Esta función solo está disponible en el sistema web.';

function tabla(nombre) {
  return db.existeTabla(nombre);
}

// _promocionProducto
function promocionProducto(idProducto, fkClasificacion) {
  if (!tabla('promociones')) {
    return null;
  }
  const r = db.uno(`SELECT ID_Promocion, Nombre, Tipo, Cantidad, Modo_Cantidad, Tipo_Beneficio, Precio_Fijo, Tipo_Descuento, Descuento
    FROM promociones WHERE Estado = 1 AND ((Tipo = 'Producto' AND FK_Producto = ?) OR (Tipo = 'Clasificacion' AND FK_Clasificacion = ?))
    ORDER BY (Tipo = 'Producto') DESC LIMIT 1`, [idProducto, fkClasificacion]);
  if (!r) {
    return null;
  }
  return {
    ID_Promocion: String(r.ID_Promocion), Nombre: r.Nombre, Tipo: r.Tipo, Cantidad: Number(r.Cantidad), Modo_Cantidad: r.Modo_Cantidad,
    Tipo_Beneficio: r.Tipo_Beneficio, Precio_Fijo: Number(r.Precio_Fijo), Tipo_Descuento: r.Tipo_Descuento, Descuento: Number(r.Descuento)
  };
}

function impuestosDe(idProducto) {
  if (!tabla('impuestos_productos')) {
    return [];
  }
  return db.todos(`SELECT ip.ID_Impuesto_Producto, ip.FK_Impuesto, i.Nombre, i.Porcentaje, i.Clave_CFDI, i.Tipo_Factor, i.Clase
    FROM impuestos_productos ip INNER JOIN impuestos i ON ip.FK_Impuesto = i.ID_Impuesto WHERE ip.FK_Producto = ? ORDER BY i.Nombre ASC`, [idProducto]);
}

function existencia(idProducto) {
  return Number(db.valor('SELECT IFNULL(Cantidad, 0) FROM inventario WHERE FK_Producto = ?', [idProducto]) || 0);
}

// PHP imprime los números sin ".0"; SQLite devuelve 5.0 como 5, igual que PHP
function txt(v) {
  return v === null || v === undefined ? '' : String(v);
}

function fechaDMY(f) {
  const m = String(f || '').match(/^(\d{4})-(\d{2})-(\d{2})/);
  return m ? `${m[3]}-${m[2]}-${m[1]}` : '';
}

function idTurnoActual(idCaja) {
  const t = db.uno('SELECT ID_Detalle_Caja FROM detalles_caja WHERE FK_Caja = ? ORDER BY ID_Detalle_Caja DESC LIMIT 1', [idCaja]);
  return t ? t.ID_Detalle_Caja : 0;
}

function nombreUsuario(id) {
  const u = db.uno('SELECT Nombre, Primer_Apellido, Segundo_Apellido FROM usuarios WHERE ID_Usuario = ?', [id]);
  return u ? `${u.Nombre} ${u.Primer_Apellido} ${u.Segundo_Apellido}` : '';
}

// Solo se puede vender lo que existe (decisión del negocio). Devuelve el mensaje de error o null.
function validarExistencias(productos, cantidadesPrevias = {}) {
  const pedido = {};
  for (const p of productos) {
    const id = Number(p.id);
    if (id > 0) {
      pedido[id] = (pedido[id] || 0) + Number(p.cantidad || 0);
    }
  }
  for (const id of Object.keys(pedido)) {
    const prod = db.uno('SELECT Descripcion FROM productos WHERE ID_Producto = ?', [id]);
    if (!prod) {
      continue;
    }
    const disponible = existencia(id) + (cantidadesPrevias[id] || 0);
    if (pedido[id] > disponible + 1e-9) {
      return `No hay existencia suficiente de "${prod.Descripcion}". Hay ${numero(disponible, 2).replace(/\.00$/, '')} y se quieren vender ${numero(pedido[id], 2).replace(/\.00$/, '')}.`;
    }
  }
  return null;
}

// Descuenta lotes: primero los elegidos en la caja y luego por caducidad (igual que guardarVenta en PHP)
function descontarLotes(idDetalleVenta, producto) {
  if (!tabla('inventario_lotes')) {
    return;
  }
  const inv = db.uno('SELECT ID_Inventario FROM inventario WHERE FK_Producto = ?', [producto.id]);
  if (!inv) {
    return;
  }
  let porDescontar = Number(producto.cantidad) || 0;
  const anotar = (idLote, cantidad) => {
    // El trigger rebaja_lote descuenta inventario_lotes
    db.ejecutar('INSERT INTO detalles_ventas_lotes (FK_Detalle_Venta, FK_Lote, Cantidad) VALUES (?, ?, ?)', [idDetalleVenta, idLote, cantidad]);
    porDescontar -= cantidad;
  };

  for (const l of Array.isArray(producto.lotes) ? producto.lotes : []) {
    const idLote = parseInt(l.idLote, 10) || 0;
    const cant = Number(l.cantidad) || 0;
    if (idLote > 0 && cant > 0 && porDescontar > 0) {
      const lote = db.uno('SELECT ID_Lote FROM inventario_lotes WHERE ID_Lote = ? AND FK_Inventario = ?', [idLote, inv.ID_Inventario]);
      if (lote) {
        anotar(idLote, Math.min(cant, porDescontar));
      }
    }
  }

  if (porDescontar > 0) {
    const fifo = db.todos('SELECT ID_Lote, Cantidad FROM inventario_lotes WHERE FK_Inventario = ? AND Cantidad > 0 ORDER BY Fecha_Caducidad ASC, ID_Lote ASC', [inv.ID_Inventario]);
    for (const l of fifo) {
      if (porDescontar <= 0) {
        break;
      }
      const cant = Math.min(Number(l.Cantidad), porDescontar);
      if (cant > 0) {
        anotar(l.ID_Lote, cant);
      }
    }
  }
}

function insertarDetalle(idVenta, producto) {
  const prod = Number(producto.id) > 0 ? db.uno('SELECT Codigo, Costo FROM productos WHERE ID_Producto = ?', [producto.id]) : null;
  const idDetalle = db.ejecutar(`INSERT INTO detalles_ventas (FK_Venta, FK_Producto, Codigo, Descripcion, Precio, Costo, Cantidad, Descuento, Total)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`, [idVenta, Number(producto.id) || 0, prod ? txt(prod.Codigo) : '', String(producto.descripcion || '').trim(),
    Number(producto.precio) || 0, prod ? Number(prod.Costo) || 0 : 0, Number(producto.cantidad) || 0, Number(producto.descuento) || 0, Number(producto.total) || 0]);

  for (const imp of Array.isArray(producto.impuestos) ? producto.impuestos : []) {
    db.ejecutar('INSERT INTO impuestos_ventas (FK_Detalle_Venta, Nombre, Porcentaje, Clave_CFDI, Clase, Tipo_Factor) VALUES (?, ?, ?, ?, ?, ?)',
      [idDetalle, String(imp.nombre ?? '').trim(), Number(imp.porcentaje) || 0, String(imp.clave ?? '').trim(), String(imp.clase ?? '').trim(), String(imp.factor ?? '').trim()]);
  }
  if (Number(producto.id) > 0) {
    descontarLotes(idDetalle, producto);
  }
  return idDetalle;
}

function siguienteFolio() {
  const enlace = db.ajuste('enlace');
  const n = Number(db.ajuste('folio') || 0) + 1;
  db.ajuste('folio', n);
  return `D${enlace.dispositivo.id}-${String(n).padStart(6, '0')}`;
}

function direccionTexto(r) {
  let d = `${r.Calle} #${r.No_Exterior}`;
  if (txt(r.No_Interior) !== '') d += ` int.${r.No_Interior}`;
  if (txt(r.Colonia) !== '') d += `, ${r.Colonia}`;
  if (txt(r.CP) !== '') d += ` ${r.CP}`;
  if (txt(r.Ciudad) !== '') d += `, ${r.Ciudad}`;
  if (txt(r.Estado) !== '') d += ` ${r.Estado}`;
  if (txt(r.Pais) !== '') d += `, ${r.Pais}`;
  return d;
}

const consultas = {
  agregarProducto(p) {
    let codigo = String(p.codigo || '').trim();
    let loteBuscado = 0;
    let codigoBase = codigo;
    const sep = codigo.includes('_') ? '_' : (codigo.includes('-') ? '-' : null);
    if (sep) {
      const i = codigo.indexOf(sep);
      codigoBase = codigo.slice(0, i).trim();
      loteBuscado = parseInt(codigo.slice(i + 1).trim(), 10) || 0;
    }

    const sql = 'SELECT p.ID_Producto, p.Codigo, p.Descripcion, p.Precio, p.Precio_Mayoreo, p.Clase, p.FK_Clasificacion, IFNULL(i.Cantidad, 0) AS Existencia FROM productos p LEFT JOIN inventario i ON i.FK_Producto = p.ID_Producto WHERE p.Codigo = ?';
    let prod = db.uno(sql, [codigoBase]);
    if (!prod && codigoBase !== codigo) {
      prod = db.uno(sql, [codigo]);
      loteBuscado = 0;
    }
    if (!prod) {
      return 'No encontrado';
    }

    const impuestos = impuestosDe(prod.ID_Producto).map((r) => ({
      ID_Impuesto_Producto: txt(r.ID_Impuesto_Producto), FK_Impuesto: txt(r.FK_Impuesto), Nombre: r.Nombre, Porcentaje: txt(r.Porcentaje),
      Clave_CFDI: r.Clave_CFDI, Tipo_Factor: r.Tipo_Factor, Clase: r.Clase
    }));

    let lote = null;
    if (loteBuscado > 0 && tabla('inventario_lotes')) {
      const l = db.uno(`SELECT l.ID_Lote, l.Nombre_Lote, l.Fecha_Caducidad FROM inventario_lotes l INNER JOIN inventario i ON l.FK_Inventario = i.ID_Inventario
        WHERE l.ID_Lote = ? AND i.FK_Producto = ?`, [loteBuscado, prod.ID_Producto]);
      if (l) {
        lote = { idLote: Number(l.ID_Lote), nombre: l.Nombre_Lote, caducidad: String(l.Fecha_Caducidad).slice(0, 10), caducidadFmt: fechaDMY(l.Fecha_Caducidad), cantidad: 1 };
      }
    }

    return JSON.stringify({
      ID_Producto: txt(prod.ID_Producto), Codigo: txt(prod.Codigo), Descripcion: prod.Descripcion, Precio: txt(prod.Precio), Precio_Mayoreo: txt(prod.Precio_Mayoreo),
      Clase: prod.Clase, Existencia: txt(prod.Existencia), Impuestos: impuestos, Promocion: promocionProducto(prod.ID_Producto, prod.FK_Clasificacion), Lote: lote
    });
  },

  productos(p) {
    const b = busqueda(p.buscar, "p.Codigo || p.Descripcion || p.Precio || p.Precio_Mayoreo");
    const ord = orden(p.ordenColumna, p.orden, ['Codigo', 'Descripcion', 'Clase', 'Existencia', 'Costo', 'Precio', 'Precio_Mayoreo'], 'Descripcion');
    const filas = db.todos(`SELECT p.ID_Producto, p.Codigo, p.Descripcion, p.Clase, p.FK_Clasificacion, IFNULL(i.Cantidad, 0) AS Existencia, p.Costo, p.Precio, p.Precio_Mayoreo, p.Foto
      FROM productos p LEFT JOIN inventario i ON i.FK_Producto = p.ID_Producto ${b.sql} ORDER BY ${ord} ${paginar(p.limit, p.pagina)}`, b.params);
    if (!filas.length) {
      return JSON.stringify({});
    }
    const total = db.valor(`SELECT COUNT(*) FROM productos p ${b.sql}`, b.params);

    const data = filas.map((r) => {
      let impuestos = 'Sin impuestos';
      const imps = impuestosDe(r.ID_Producto);
      if (imps.length) {
        impuestos = imps.map((i) => `<p class="m-0" attrID="${i.FK_Impuesto}" clave="${html(i.Clave_CFDI)}"><span class="fw-bold">${html(i.Clase)}</span> <b>${html(i.Nombre)}</b> (<span class="valor ${i.Tipo_Factor === 'Cuota' ? 'dinero' : 'porcentaje'}">${txt(i.Porcentaje)}</span>) - <b>${html(i.Tipo_Factor)}</b></p>`).join('');
      }
      const foto = txt(r.Foto) !== '' ? `vistas/assets/images/productos/${r.Foto}` : 'vistas/assets/images/producto-generico.png';
      const fotoHtml = `<div style="background-image: url('${foto}'), url('vistas/assets/images/producto-generico.png'); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;"></div>`;
      const promo = `<span class="promoData oculto" style="display:none;">${html(JSON.stringify(promocionProducto(r.ID_Producto, r.FK_Clasificacion)))}</span>`;
      return {
        ID: txt(r.ID_Producto), Codigo: fotoHtml + html(r.Codigo), Clase: r.Clase, Descripcion: html(r.Descripcion),
        Costo: `<span class="dinero">${txt(r.Costo)}</span>`, Precio: `<span class="dinero">${txt(r.Precio)}</span>`,
        Precio_Mayoreo: `<span class="dinero">${txt(r.Precio_Mayoreo)}</span>`, Impuestos: impuestos + promo, Existencia: `<span class="cantidad">${txt(r.Existencia)}</span>`
      };
    });
    return JSON.stringify({ data, totales: { NumRows: String(total) } });
  },

  corte(p) {
    const idCaja = Number(p.idCaja);
    const t = db.uno('SELECT * FROM detalles_caja WHERE FK_Caja = ? ORDER BY ID_Detalle_Caja DESC LIMIT 1', [idCaja]);
    const r = { detalles: [] };
    if (t) {
      const d = new Date();
      r.detalles = {
        Fecha_Abrir: fechaHumana(t.Fecha_Abrir), Monto_Abrir: txt(t.Monto_Abrir), Fecha_Cierre: fechaCorta(d),
        Fecha: ahora(d).slice(0, 16), Usuario_Abrio: nombreUsuario(t.FK_Usuario_Abrir)
      };
    }
    const idTurno = t ? t.ID_Detalle_Caja : 0;
    r.ventas = db.todos('SELECT SUM(Pago - Cambio) AS Total, Tipo_Pago FROM ventas WHERE FK_Detalles_Caja = ? GROUP BY Tipo_Pago', [idTurno])
      .map((v) => ({ Total: txt(v.Total), Tipo_Pago: v.Tipo_Pago }));
    r.pagos_compras = []; // las compras solo se registran en la web
    r.pagos_ventas = db.todos('SELECT SUM(Monto) AS Total, Tipo_Pago FROM ventas_pagos WHERE FK_Detalle_Caja = ? GROUP BY Tipo_Pago', [idTurno])
      .map((x) => ({ Total: txt(x.Total), Tipo_Pago: x.Tipo_Pago }));
    r.movimientos = db.todos('SELECT Tipo, Monto, Descripcion, Fecha_Registro FROM movimientos_caja WHERE FK_Detalle_Caja = ? ORDER BY Fecha_Registro DESC', [idTurno])
      .map((m) => ({ Tipo: m.Tipo, Monto: txt(m.Monto), Descripcion: m.Descripcion, Fecha_Registro: fechaHumana(m.Fecha_Registro) }));
    r.usuarios = db.todos('SELECT SUM(Pago - Cambio) AS Total, FK_Usuario FROM ventas WHERE FK_Detalles_Caja = ? GROUP BY FK_Usuario', [idTurno])
      .map((u) => ({ Total: txt(u.Total), Usuario: nombreUsuario(u.FK_Usuario) }));
    r.registros_pagos_ventas = db.todos(`SELECT p.FK_Venta, v.Folio, p.Monto, p.Tipo_Pago,
        CASE WHEN v.FK_Cliente = 0 THEN 'Publico en General' ELSE IFNULL(c.Nombre || ' ' || IFNULL(c.Primer_Apellido, '') || ' ' || IFNULL(c.Segundo_Apellido, ''), '') END AS Cliente
      FROM ventas_pagos p JOIN ventas v ON v.ID_Venta = p.FK_Venta LEFT JOIN clientes c ON c.ID_Cliente = v.FK_Cliente WHERE p.FK_Detalle_Caja = ?`, [idTurno])
      .map((x) => ({ Folio: x.Folio, Monto: txt(x.Monto), Tipo_Pago: x.Tipo_Pago, Cliente: x.Cliente }));
    r.clasificaciones = db.todos(`SELECT IFNULL(c.Nombre, 'Sin clasificación') AS Clasificacion, SUM(dv.Cantidad) AS Cantidad,
        SUM(dv.Total * ((v.Pago - v.Cambio) / NULLIF((SELECT SUM(dv2.Total) FROM detalles_ventas dv2 WHERE dv2.FK_Venta = v.ID_Venta), 0))) AS Total
      FROM detalles_ventas dv INNER JOIN ventas v ON dv.FK_Venta = v.ID_Venta
      LEFT JOIN productos p ON dv.FK_Producto = p.ID_Producto LEFT JOIN clasificaciones c ON p.FK_Clasificacion = c.ID_Clasificacion
      WHERE v.FK_Detalles_Caja = ? AND v.Estatus != 2
      GROUP BY IFNULL(c.ID_Clasificacion, 0), IFNULL(c.Nombre, 'Sin clasificación') ORDER BY Clasificacion`, [idTurno])
      .map((c) => ({ Clasificacion: c.Clasificacion, Cantidad: txt(c.Cantidad), Total: txt(c.Total) }));
    return JSON.stringify(r);
  },

  clientes(p) {
    const b = busqueda(p.buscar, "Tipo || Nombre || Primer_Apellido || Segundo_Apellido || RFC || Razon_Social || Telefono || Email");
    const ord = orden(p.ordenColumna, p.orden, ['Nombre', 'Tipo', 'Telefono', 'ID_Cliente'], 'Nombre');
    const filas = db.todos(`SELECT ID_Cliente, Tipo, Nombre, Primer_Apellido, Segundo_Apellido, RFC, Razon_Social, Telefono, Email FROM clientes ${b.sql} ORDER BY ${ord} ${paginar(p.limit, p.pagina)}`, b.params);
    if (!filas.length) {
      return JSON.stringify({});
    }
    const total = db.valor(`SELECT COUNT(*) FROM clientes ${b.sql}`, b.params);
    const data = filas.map((r) => ({
      ID: txt(r.ID_Cliente),
      Nombre: (r.Tipo === 'Moral' ? `<span>${html(r.Razon_Social)}</span>` : `<span>${html(r.Nombre)} ${html(r.Primer_Apellido)} ${html(r.Segundo_Apellido)}</span>`) + `<br><span class="text-muted">RFC: </span>${html(r.RFC)}`,
      Tipo: r.Tipo,
      Contacto: `<span class="text-muted">Tel: </span>${html(r.Telefono)}<br><span class="text-muted">Email: </span>${html(r.Email)}`
    }));
    return JSON.stringify({ data, totales: { NumRows: String(total) } });
  }
};

module.exports = {
  promocionProducto,
  impuestosDe,
  existencia,
  validarExistencias,
  insertarDetalle,

  consultar(p) {
    return consultas[p.tipo] ? consultas[p.tipo](p) : SOLO_WEB;
  },

  insertar(p) {
    if (p.tipo === 'venta') {
      let productos;
      try {
        productos = JSON.parse(p.productos);
      } catch (e) {
        return 'Error 1: productos inválidos';
      }
      const idTurno = idTurnoActual(Number(p.idCaja));
      if (!idTurno || !turno.abierta()) {
        return 'Offline~La caja no está abierta.';
      }
      const falta = validarExistencias(productos);
      if (falta) {
        return 'Offline~' + falta;
      }

      const total = Number(p.total) || 0;
      const pago = Number(p.pago) || 0;
      const estatus = pago < total && p.tipoPago === 'Crédito' ? 1 : 0;
      const idVenta = db.transaccion(() => {
        const id = db.ejecutar(`INSERT INTO ventas (UUID, Folio, FK_Usuario, FK_Cliente, FK_Detalles_Caja, Descuento, Total, Tipo_Pago, Pago, Cambio, Fecha_Registro, Estatus, Turno, FK_Direccion, Cuenta_Origen)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`, [uuid(), siguienteFolio(), sesion.id(), Number(p.cliente) || 0, idTurno, Number(p.descuento) || 0, total,
          String(p.tipoPago || ''), pago, Number(p.cambio) || 0, ahora(), estatus, String(p.turno || ''), Number(p.fkDireccion) || 0, Number(db.ajuste('cuentaDatos') || 0)]);
        for (const prod of productos) {
          insertarDetalle(id, prod);
        }
        return id;
      });
      return `Correcto~${idVenta}`;
    }

    if (p.tipo === 'movimiento') {
      const idTurno = idTurnoActual(Number(p.cajaId));
      if (!idTurno) {
        return 'Error: la caja no está abierta';
      }
      db.ejecutar('INSERT INTO movimientos_caja (UUID, Tipo, Monto, Descripcion, Fecha_Registro, FK_Detalle_Caja) VALUES (?, ?, ?, ?, ?, ?)',
        [uuid(), String(p.tipoMov || ''), Number(p.cantidad) || 0, String(p.descripcion || ''), ahora(), idTurno]);
      return 'Correcto';
    }
    return SOLO_WEB;
  },

  modificar(p) {
    if (p.tipo === 'cerrar') {
      const idTurno = idTurnoActual(Number(p.idCaja));
      if (!idTurno) {
        return '';
      }
      db.ejecutar('UPDATE detalles_caja SET Fecha_Cierre = ?, Monto_Cierre = ?, FK_Usuario_Cierre = ? WHERE ID_Detalle_Caja = ?',
        [String(p.fecha_cierre || ahora()).replace('T', ' ').slice(0, 19), Number(p.monto) || 0, sesion.id(), idTurno]);
      turno.tomar(0);
      return `Correcto~${idTurno}`;
    }

    if (p.tipo === 'dejar') {
      turno.tomar(0);
      return 'Correcto';
    }

    if (p.tipo === 'pediente') {
      const venta = db.uno('SELECT ID_Venta FROM ventas WHERE ID_Venta = ? AND Subido = 0', [p.idVenta]);
      if (!venta) {
        return 'Offline~Esta venta ya se subió al sistema web; termínala allá.';
      }
      const total = Number(p.total) || 0;
      const pago = Number(p.pago) || 0;
      db.ejecutar('UPDATE ventas SET Estatus = ?, Total = ?, Tipo_Pago = ?, Pago = ?, Cambio = ?, Detalles = ?, FK_Cliente = ?, FK_Detalles_Caja = ? WHERE ID_Venta = ?',
        [pago < total && p.tipoPago === 'Crédito' ? 1 : 0, total, String(p.tipoPago || ''), pago, Number(p.cambio) || 0, String(p.detalles || ''), Number(p.cliente) || 0,
          idTurnoActual(Number(p.idCaja)), venta.ID_Venta]);
      return 'Correcto';
    }

    if (p.tipo === 'agregar') {
      const venta = db.uno('SELECT ID_Venta FROM ventas WHERE ID_Venta = ? AND Subido = 0', [p.id]);
      if (!venta) {
        return 'Offline~Esta venta ya se subió al sistema web; modifícala allá.';
      }
      const cantidad = Number(p.cantidad) || 0;
      const descuento = Number(p.descuento) || 0;
      const prod = db.uno('SELECT ID_Producto, Descripcion, Precio FROM productos WHERE Codigo = ?', [String(p.codigo || '').trim()]);
      if (prod) {
        const falta = validarExistencias([{ id: prod.ID_Producto, cantidad }]);
        if (falta) {
          return 'Offline~' + falta;
        }
      }
      db.transaccion(() => {
        const precio = prod ? Number(prod.Precio) : Number(p.precio) || 0;
        const total = precio * cantidad - descuento;
        db.ejecutar('UPDATE ventas SET Total = Total + ?, Cambio = Pago - (Total + ?) WHERE ID_Venta = ?', [total, total, venta.ID_Venta]);
        const existente = prod ? db.uno('SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ? AND FK_Producto = ?', [venta.ID_Venta, prod.ID_Producto]) : null;
        if (existente) {
          db.ejecutar('UPDATE detalles_ventas SET Cantidad = Cantidad + ?, Descuento = ?, Precio = ?, Total = ((Cantidad + ?) * ?) - ? WHERE ID_Detalle_Venta = ?',
            [cantidad, descuento, precio, cantidad, precio, descuento, existente.ID_Detalle_Venta]);
        } else {
          insertarDetalle(venta.ID_Venta, { id: prod ? prod.ID_Producto : 0, descripcion: prod ? prod.Descripcion : String(p.codigo || ''), precio, cantidad, descuento, total, impuestos: [] });
        }
      });
      return 'Correcto';
    }

    if (p.tipo === 'cliente') {
      // Cliente o domicilio nuevo desde la caja: se guarda en el equipo con ID negativo y se sube junto con la venta
      let id = String(p.idCliente || '');
      let idDomicilio = String(p.idDomicilio || '');
      const dom = [p.calle, p.noExterior, p.noInterior, p.cp, p.colonia, p.ciudad, p.estado, p.pais, p.detalles].map((v) => String(v ?? ''));
      db.transaccion(() => {
        if (id === '') {
          id = String(Math.min(Number(db.valor('SELECT MIN(ID_Cliente) FROM clientes_nuevos') || 0), 0) - 1);
          db.ejecutar('INSERT INTO clientes_nuevos (ID_Cliente, UUID, Nombre, Telefono) VALUES (?, ?, ?, ?)', [Number(id), uuid(), String(p.nombre || ''), String(p.telefono || '')]);
          db.insertarFila('clientes', { ID_Cliente: Number(id), Tipo: 'Física', Nombre: String(p.nombre || ''), Telefono: String(p.telefono || '') });
          idDomicilio = '0';
        }
        const campos = { Calle: dom[0], No_Exterior: dom[1], No_Interior: dom[2], CP: dom[3], Colonia: dom[4], Ciudad: dom[5], Estado: dom[6], Pais: dom[7], Detalles: dom[8] };
        if (idDomicilio === '') {
          idDomicilio = String(Math.min(Number(db.valor('SELECT MIN(ID_Direccion) FROM direcciones_nuevas') || 0), 0) - 1);
          db.ejecutar(`INSERT INTO direcciones_nuevas (ID_Direccion, UUID, FK_Cliente, Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais, Detalles)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`, [Number(idDomicilio), uuid(), Number(id), ...dom]);
          db.insertarFila('direcciones_cliente', { ID_Direccion: Number(idDomicilio), FK_Cliente: Number(id), ...campos });
        } else if (idDomicilio !== '0' && Number(idDomicilio) < 0) {
          // Solo se pueden editar domicilios creados en el equipo; los del servidor se editan en la web
          db.ejecutar('UPDATE direcciones_nuevas SET Calle=?, No_Exterior=?, No_Interior=?, CP=?, Colonia=?, Ciudad=?, Estado=?, Pais=?, Detalles=? WHERE ID_Direccion = ?', [...dom, Number(idDomicilio)]);
          db.ejecutar('UPDATE direcciones_cliente SET Calle=?, No_Exterior=?, No_Interior=?, CP=?, Colonia=?, Ciudad=?, Estado=?, Pais=?, Detalles=? WHERE ID_Direccion = ?', [...dom, Number(idDomicilio)]);
        }
      });
      return `Correcto~${id}~${idDomicilio}`;
    }
    return SOLO_WEB;
  },

  detalles(p) {
    if (p.tipo === 'consultarCaja') {
      if (!turno.abierta()) {
        return 'No';
      }
      // Un solo equipo por caja: quien presiona "Caja" la toma
      if (turno.usuarioEnCaja() !== sesion.id()) {
        turno.tomar(sesion.id());
      }
      const caja = db.uno('SELECT ID_Caja, Nombre FROM cajas WHERE ID_Caja = ?', [turno.cajaId()]);
      return caja ? JSON.stringify({ ID_Caja: txt(caja.ID_Caja), Nombre: caja.Nombre }) : 'No';
    }

    if (p.tipo === 'ventas') {
      // Ventas pendientes (a crédito) que siguen en el equipo
      const b = busqueda(p.buscar, "Folio || Fecha_Registro || Total || Pago || Cambio || Tipo_Pago || Detalles", 'AND');
      const ord = orden(p.ordenColumna, p.orden, ['ID_Venta', 'Fecha', 'Folio', 'Total', 'Pago', 'Cambio'], 'ID_Venta').replace(/^Fecha /, 'Fecha_Registro ');
      const filas = db.todos(`SELECT * FROM ventas WHERE Estatus = 1 AND Subido = 0 ${b.sql} ORDER BY ${ord} ${paginar(p.limit, p.pagina)}`, b.params);
      if (!filas.length) {
        return JSON.stringify({});
      }
      const total = db.valor(`SELECT COUNT(*) FROM ventas WHERE Estatus = 1 AND Subido = 0 ${b.sql}`, b.params);
      const data = filas.map((v) => {
        let det = `<p>${html(v.Detalles)}</p><table class="table" width="100%"><thead><tr><th>Cod.</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Descuento</th><th>Total</th><th>Acciones</th></tr></thead><tbody>`;
        for (const d of db.todos('SELECT * FROM detalles_ventas WHERE FK_Venta = ?', [v.ID_Venta])) {
          det += `<tr><td>${html(d.Codigo)}</td><td>${html(d.Descripcion)}</td><td><span class="cantidad">${d.Cantidad}</span></td><td><span class="dinero">${d.Precio}</span></td>
            <td><span class="dinero">${d.Descuento}</span></td><td><span class="dinero">${d.Total}</span></td>
            <td><button type="button" class="btn btn-sm btn-danger bQuitarProducto" attrID="${d.ID_Detalle_Venta}" title="Quitar producto"><i class="fas fa-trash"></i></button></td></tr>`;
        }
        det += `<tr><td colspan="2"><input style="border-radius: 5px;" type="text" class="form-control form-control-sm" id="codigoProdMas" placeholder="Código/Prod. Comun"></td>
          <td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="cantidadProdMas" placeholder="Cantidad" value="1"></td>
          <td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="precioProdMas" placeholder="Precio" value="0"></td>
          <td><input style="border-radius: 5px;" type="number" step="any" class="form-control form-control-sm" id="descuentoProdMas" placeholder="Descuento" value="0"></td>
          <td></td><td><button type="button" class="btn btn-sm btn-success bAgregarProductoMas" attrID="${v.ID_Venta}" title="Agregar producto"><i class="fas fa-plus"></i></button></td></tr></tbody></table>`;
        return {
          ID: String(v.ID_Venta), Fecha: fechaHumana(v.Fecha_Registro), Folio: v.Folio, Tipo: v.Tipo_Pago,
          Total: `<span class="dinero">${v.Total}</span>`, Pago: `<span class="dinero">${v.Pago}</span>`, Cambio: `<span class="dinero">${v.Cambio}</span>`,
          Estatus: "<span class='badge rounded-pill bg-warning'>Pendiente</span>", Detalles: det,
          Acciones: `<button type="button" class="btn btn-sm btn-warning bCancelarVentaPen" attrID="${v.ID_Venta}" title="Cancelar venta"><i class="fas fa-ban"></i></button>`
        };
      });
      return JSON.stringify({ data, totales: { NumRows: String(total) } });
    }

    if (p.tipo === 'prodTienda') {
      const clas = String(p.clasificacion);
      const where = clas === '0'
        ? '(p.FK_Clasificacion IS NULL OR p.FK_Clasificacion NOT IN (SELECT ID_Clasificacion FROM clasificaciones))'
        : 'p.FK_Clasificacion = ?';
      const filas = db.todos(`SELECT p.ID_Producto, p.Codigo, p.Descripcion, p.Clase, p.Costo, p.Precio, p.Precio_Mayoreo, p.Foto, p.FK_Clasificacion, IFNULL(i.Cantidad, 0) AS Existencia
        FROM productos p LEFT JOIN inventario i ON i.FK_Producto = p.ID_Producto WHERE ${where} ORDER BY p.Descripcion`, clas === '0' ? [] : [clas]);
      let salida = '';
      for (const r of filas) {
        const foto = txt(r.Foto) !== '' ? `vistas/assets/images/productos/${r.Foto}` : 'vistas/assets/images/producto-generico.png';
        const imps = impuestosDe(r.ID_Producto).map((i) => ({ FK_Impuesto: txt(i.FK_Impuesto), Nombre: i.Nombre, Porcentaje: Number(i.Porcentaje), Tipo_Factor: i.Tipo_Factor, Clase: i.Clase, Clave: i.Clave_CFDI }));
        salida += `<div class="col-6 col-sm-4 col-lg-3">
          <div class="prodTienda vt-prod-card" attrID="${r.ID_Producto}" attrCodigo="${html(r.Codigo)}" attrPrecio="${txt(r.Precio)}" attrPrecioMayoreo="${txt(r.Precio_Mayoreo)}"
            attrClase="${html(r.Clase)}" attrExistencia="${txt(r.Existencia)}" attrImpuestos='${html(JSON.stringify(imps))}' attrPromocion='${html(JSON.stringify(promocionProducto(r.ID_Producto, r.FK_Clasificacion)))}'>
            <div class="vt-prod-photo" style="background-image: url('${foto}'), url('vistas/assets/images/producto-generico.png');"></div>
            <div class="vt-prod-body">
              <p class="vt-prod-name">${html(r.Descripcion)}</p>
              <div class="vt-prod-footer">
                <span class="dinero vt-prod-price">${numero(r.Precio)}</span>
                <span class="vt-prod-add"><i class="fas fa-plus"></i></span>
              </div>
            </div>
          </div>
        </div>`;
      }
      return salida || '<div class="col-12 text-center text-muted" style="padding-top: 20vh;"><h5>No hay productos en esta clasificación.</h5></div>';
    }

    if (p.tipo === 'clasificaciones') {
      const filas = db.todos(`SELECT ID_Clasificacion, Nombre, Foto, 0 AS Orden FROM clasificaciones c WHERE (SELECT COUNT(*) FROM productos WHERE FK_Clasificacion = c.ID_Clasificacion) > 0
        UNION ALL
        SELECT 0, 'Sin clasificación', '', 1 WHERE EXISTS (SELECT 1 FROM productos WHERE FK_Clasificacion IS NULL OR FK_Clasificacion NOT IN (SELECT ID_Clasificacion FROM clasificaciones))
        ORDER BY Orden, Nombre`);
      let salida = '';
      for (const r of filas) {
        const foto = txt(r.Foto) !== '' ? `vistas/assets/images/clasificaciones/${r.Foto}` : 'vistas/assets/images/fondo.jpg';
        salida += `<div class="col-3">
          <div class="claTienda" style="background-image: url('${foto}'), url('vistas/assets/images/fondo.jpg'); background-size: cover; background-position: center; border: 1px solid #EEE; border-radius: 10px; height: 120px; cursor: pointer; color: #FFF;" attrID="${r.ID_Clasificacion}">
            <div style="height: 100%; width: 100%; background-color: rgba(0, 0, 0, 0.7); border-radius: 10px; display: flex; align-items: center;">
              <p class="text-center" style="font-size: 20px; margin: 0; width: 100%;">${html(r.Nombre)}</p>
            </div>
          </div>
        </div>`;
      }
      return salida || '<div class="col-12 text-center text-muted" style="padding-top: 20vh;"><h5>No hay productos para mostrar.</h5><p>Sincroniza los productos para verlos aquí.</p></div>';
    }

    if (p.tipo === 'buscarTelefono' || p.tipo === 'buscarNombre') {
      const campo = p.tipo === 'buscarTelefono' ? 'Telefono' : 'Nombre';
      const b = busqueda(p.buscar, campo);
      const clase = p.tipo === 'buscarTelefono' ? 'itemTelefonoBus' : 'itemNombreBus';
      return db.todos(`SELECT ID_Cliente, Telefono, Nombre FROM clientes ${b.sql} LIMIT 25`, b.params).map((r) => {
        const a = p.tipo === 'buscarTelefono' ? [r.Telefono, r.Nombre] : [r.Nombre, r.Telefono];
        return `<div class="lista ${clase} row" attrID="${r.ID_Cliente}"><div class="col-12" style="align-items: center;"><span>${html(a[0])}</span><span>${html(a[1])}</span></div></div>`;
      }).join('');
    }

    if (p.tipo === 'domicilios') {
      let salida = '';
      const c = db.uno('SELECT Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais FROM clientes WHERE ID_Cliente = ?', [p.id]);
      if (c) {
        salida += `<option value="0">${html(direccionTexto(c))}</option>`;
      }
      for (const d of db.todos('SELECT * FROM direcciones_cliente WHERE FK_Cliente = ?', [p.id])) {
        salida += `<option value="${d.ID_Direccion}">${html(direccionTexto(d))}</option>`;
      }
      return salida;
    }

    if (p.tipo === 'domicilio') {
      const r = String(p.id) !== '0'
        ? db.uno('SELECT Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais, Detalles FROM direcciones_cliente WHERE ID_Direccion = ?', [p.id])
        : db.uno('SELECT Calle, No_Exterior, No_Interior, CP, Colonia, Ciudad, Estado, Pais, Detalles FROM clientes WHERE ID_Cliente = ?', [p.cliente]);
      return JSON.stringify(r ? Object.fromEntries(Object.entries(r).map(([k, v]) => [k, txt(v)])) : []);
    }
    return SOLO_WEB;
  },

  eliminar(p) {
    if (p.tipo === 'quitar') {
      const det = db.uno('SELECT d.ID_Detalle_Venta, d.FK_Venta, d.Total, v.Subido FROM detalles_ventas d JOIN ventas v ON v.ID_Venta = d.FK_Venta WHERE d.ID_Detalle_Venta = ?', [p.id]);
      if (!det || det.Subido) {
        return 'Offline~Esta venta ya se subió al sistema web; modifícala allá.';
      }
      db.transaccion(() => {
        db.ejecutar('UPDATE ventas SET Total = Total - ?, Cambio = Pago - (Total - ?) WHERE ID_Venta = ?', [det.Total, det.Total, det.FK_Venta]);
        // Los triggers devuelven inventario y lotes (las devoluciones primero, para no regresar dos veces)
        db.ejecutar('DELETE FROM devoluciones WHERE FK_Detalle_Venta = ?', [det.ID_Detalle_Venta]);
        db.ejecutar('DELETE FROM detalles_ventas_lotes WHERE FK_Detalle_Venta = ?', [det.ID_Detalle_Venta]);
        db.ejecutar('DELETE FROM impuestos_ventas WHERE FK_Detalle_Venta = ?', [det.ID_Detalle_Venta]);
        db.ejecutar('DELETE FROM detalles_ventas WHERE ID_Detalle_Venta = ?', [det.ID_Detalle_Venta]);
      });
      return 'Correcto';
    }
    return SOLO_WEB;
  }
};
