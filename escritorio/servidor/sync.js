// Comunicación con el servidor (app/api/offline.php): enlazar, bajar catálogo y subir ventas.
// Nada se hace solo: el usuario decide cuándo sincronizar.
const fs = require('fs');
const path = require('path');
const db = require('./db');
const { ahora } = require('./util');

const API_PRODUCCION = 'https://bigtool.mx/ventastool/app/api/offline.php';
let carpetaDatos = null;

function iniciar(opciones) {
  carpetaDatos = opciones.carpetaDatos;
}

// Para pruebas en la computadora de desarrollo: VENTASTOOL_API=http://127.0.0.1:8099/app/api/offline.php
function urlApi() {
  return process.env.VENTASTOOL_API || API_PRODUCCION;
}

class ErrorServidor extends Error {
  constructor(mensaje, codigo) {
    super(mensaje);
    this.codigo = codigo;
  }
}

async function llamar(datos, conLlave = true) {
  const cuerpo = { ...datos };
  if (conLlave) {
    const enlace = db.ajuste('enlace');
    cuerpo.llave = enlace ? enlace.token : '';
    cuerpo.pendientes = pendientes();
  }
  let res;
  try {
    res = await fetch(urlApi(), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(cuerpo),
      signal: AbortSignal.timeout(120000)
    });
  } catch (e) {
    throw new ErrorServidor('No se pudo conectar con el servidor. Revisa tu internet; mientras tanto puedes seguir trabajando con los datos guardados.', 'sin_conexion');
  }
  let json;
  try {
    json = await res.json();
  } catch (e) {
    throw new ErrorServidor(`El servidor respondió algo inesperado (código ${res.status}).`, 'respuesta');
  }
  if (!json.ok) {
    if (json.codigo === 'desenlazado') {
      db.ajuste('desenlazado', true);
    }
    throw new ErrorServidor(json.error || 'Error del servidor', json.codigo);
  }
  return json;
}

function pendientes() {
  return Number(db.valor('SELECT COUNT(*) FROM ventas WHERE Subido = 0') || 0);
}

// Cambia el código de un uso por la llave del equipo. Si el equipo ya tenía datos (se desenlazó), los conserva.
async function enlazar(codigo) {
  let r;
  try {
    r = await llamar({ accion: 'enlazar', codigo: String(codigo || '') }, false);
  } catch (e) {
    return 'Error~' + e.message;
  }
  const anterior = db.ajuste('enlace');
  db.transaccion(() => {
    db.ajuste('enlace', { token: r.token, cuenta: r.cuenta, dispositivo: r.dispositivo, caja: r.caja, negocio: r.negocio, fecha: ahora() });
    db.ajuste('desenlazado', false);
    // Lo que se quedó en el equipo se sube a la caja nueva
    db.db.run('UPDATE detalles_caja SET FK_Caja = ?', [r.caja.id]);
    if (!anterior) {
      db.ajuste('folio', 0);
    }
  });
  try {
    await bajar();
  } catch (e) {
    return 'Correcto~Equipo enlazado, pero no se pudieron bajar los datos: ' + e.message;
  }
  return 'Correcto';
}

// Reemplaza el catálogo local con el del servidor y vuelve a restar lo vendido en el equipo que aún no se sube
async function bajar() {
  const r = await llamar({ accion: 'bajar' });
  const enlace = db.ajuste('enlace');

  db.transaccion(() => {
    // Lo ya subido ya está en el servidor: se limpia (los triggers no devuelven inventario de ventas subidas)
    const subidas = db.todos('SELECT ID_Venta FROM ventas WHERE Subido = 1').map((v) => v.ID_Venta);
    for (const id of subidas) {
      db.db.run('DELETE FROM detalles_ventas_lotes WHERE FK_Detalle_Venta IN (SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?)', [id]);
      db.db.run('DELETE FROM impuestos_ventas WHERE FK_Detalle_Venta IN (SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?)', [id]);
      db.db.run('DELETE FROM detalles_ventas WHERE FK_Venta = ?', [id]);
      db.db.run('DELETE FROM ventas WHERE ID_Venta = ?', [id]);
    }
    db.db.run('DELETE FROM movimientos_caja WHERE Subido = 1');
    // Turnos cerrados y subidos completos; el último se conserva para el estado de la caja
    db.db.run(`DELETE FROM detalles_caja WHERE Subido = 1 AND Cierre_Subido = 1
      AND ID_Detalle_Caja <> (SELECT MAX(ID_Detalle_Caja) FROM detalles_caja)
      AND NOT EXISTS (SELECT 1 FROM ventas v WHERE v.FK_Detalles_Caja = detalles_caja.ID_Detalle_Caja)
      AND NOT EXISTS (SELECT 1 FROM movimientos_caja m WHERE m.FK_Detalle_Caja = detalles_caja.ID_Detalle_Caja)`);
    db.db.run('DELETE FROM clientes_nuevos WHERE NOT EXISTS (SELECT 1 FROM ventas v WHERE v.FK_Cliente = clientes_nuevos.ID_Cliente)');
    db.db.run('DELETE FROM direcciones_nuevas WHERE NOT EXISTS (SELECT 1 FROM ventas v WHERE v.FK_Direccion = direcciones_nuevas.ID_Direccion) AND NOT EXISTS (SELECT 1 FROM clientes_nuevos c WHERE c.ID_Cliente = direcciones_nuevas.FK_Cliente)');

    for (const nombre of db.TABLAS_SERVIDOR) {
      if (r.tablas[nombre]) {
        db.reemplazarTabla(nombre, r.tablas[nombre]);
      }
    }

    // Clientes y domicilios creados en el equipo que aún no se suben
    for (const c of db.todos('SELECT * FROM clientes_nuevos')) {
      db.insertarFila('clientes', { ID_Cliente: c.ID_Cliente, Tipo: 'Física', Nombre: c.Nombre, Telefono: c.Telefono });
    }
    for (const d of db.todos('SELECT * FROM direcciones_nuevas')) {
      const { UUID, ...campos } = d;
      db.insertarFila('direcciones_cliente', campos);
    }

    // Existencia local = servidor - ventas del equipo sin subir (solo las hechas con el catálogo de esta cuenta)
    db.db.run(`UPDATE inventario SET Cantidad = Cantidad - IFNULL((SELECT SUM(d.Cantidad) FROM detalles_ventas d JOIN ventas v ON v.ID_Venta = d.FK_Venta
      WHERE v.Subido = 0 AND v.Cuenta_Origen = ? AND d.FK_Producto = inventario.FK_Producto), 0)`, [r.cuenta]);
    if (db.existeTabla('inventario_lotes') && db.todos('PRAGMA table_info(inventario_lotes)').length) {
      db.db.run(`UPDATE inventario_lotes SET Cantidad = Cantidad - IFNULL((SELECT SUM(l.Cantidad) FROM detalles_ventas_lotes l JOIN detalles_ventas d ON d.ID_Detalle_Venta = l.FK_Detalle_Venta
        JOIN ventas v ON v.ID_Venta = d.FK_Venta WHERE v.Subido = 0 AND v.Cuenta_Origen = ? AND l.FK_Lote = inventario_lotes.ID_Lote), 0)`, [r.cuenta]);
    }

    db.ajuste('cuentaDatos', r.cuenta);
    db.ajuste('suscripcion', r.suscripcion);
    db.ajuste('ultimaSync', ahora());
    if (enlace && r.caja) {
      enlace.caja = { ...enlace.caja, id: Number(r.caja.ID_Caja), nombre: r.caja.Nombre };
      db.ajuste('enlace', enlace);
    }
  });

  bajarImagenes().catch(() => {}); // las fotos son opcionales y no detienen la sincronización
  return r;
}

// Fotos de productos, clasificaciones y logo del ticket: se bajan solo las que no estén ya en el equipo
async function bajarImagenes() {
  const base = urlApi().replace(/api\/offline\.php$/, 'vistas/assets/images/');
  const pendientesFotos = [];
  const agregar = (carpeta, tabla, columna) => {
    if (!db.existeTabla(tabla)) {
      return;
    }
    for (const f of db.todos(`SELECT DISTINCT "${columna}" AS Foto FROM "${tabla}" WHERE IFNULL("${columna}", '') <> ''`)) {
      const nombre = path.basename(String(f.Foto));
      const destino = path.join(carpetaDatos, 'imagenes', carpeta, nombre);
      if (!fs.existsSync(destino)) {
        pendientesFotos.push({ url: base + carpeta + '/' + encodeURIComponent(nombre), destino });
      }
    }
  };
  agregar('productos', 'productos', 'Foto');
  agregar('clasificaciones', 'clasificaciones', 'Foto');
  agregar('configuracion', 'configuracion', 'Foto');

  for (const f of pendientesFotos) {
    try {
      const res = await fetch(f.url, { signal: AbortSignal.timeout(30000) });
      if (res.ok) {
        fs.mkdirSync(path.dirname(f.destino), { recursive: true });
        fs.writeFileSync(f.destino, Buffer.from(await res.arrayBuffer()));
      }
    } catch (e) {
      // sin internet o foto borrada: se intenta en la siguiente sincronización
    }
  }
}

function paqueteSubida() {
  const turnos = db.todos(`SELECT * FROM detalles_caja t WHERE t.Subido = 0
    OR (t.Cierre_Subido = 0 AND t.Fecha_Cierre <> '0000-00-00 00:00:00')
    OR EXISTS (SELECT 1 FROM ventas v WHERE v.FK_Detalles_Caja = t.ID_Detalle_Caja AND v.Subido = 0)
    OR EXISTS (SELECT 1 FROM movimientos_caja m WHERE m.FK_Detalle_Caja = t.ID_Detalle_Caja AND m.Subido = 0)
    ORDER BY t.ID_Detalle_Caja`);

  const nombre = (id) => {
    const u = db.existeTabla('usuarios') ? db.uno('SELECT Nombre, Primer_Apellido, Segundo_Apellido FROM usuarios WHERE ID_Usuario = ?', [id]) : null;
    return u ? `${u.Nombre} ${u.Primer_Apellido} ${u.Segundo_Apellido}`.trim() : '';
  };

  return turnos.map((t) => {
    const ventas = db.todos('SELECT * FROM ventas WHERE FK_Detalles_Caja = ? AND Subido = 0 ORDER BY ID_Venta', [t.ID_Detalle_Caja]).map((v) => {
      const productos = db.todos('SELECT * FROM detalles_ventas WHERE FK_Venta = ?', [v.ID_Venta]).map((d) => ({
        id: d.FK_Producto, codigo: d.Codigo, descripcion: d.Descripcion, precio: d.Precio, cantidad: d.Cantidad, descuento: d.Descuento, total: d.Total,
        impuestos: db.todos('SELECT * FROM impuestos_ventas WHERE FK_Detalle_Venta = ?', [d.ID_Detalle_Venta])
          .map((i) => ({ nombre: i.Nombre, porcentaje: i.Porcentaje, clave: i.Clave_CFDI, clase: i.Clase, factor: i.Tipo_Factor })),
        lotes: db.todos('SELECT FK_Lote, SUM(Cantidad) AS Cantidad FROM detalles_ventas_lotes WHERE FK_Detalle_Venta = ? GROUP BY FK_Lote', [d.ID_Detalle_Venta])
          .map((l) => ({ idLote: l.FK_Lote, cantidad: l.Cantidad }))
      }));

      const venta = {
        uuid: v.UUID, folio: v.Folio, fecha: v.Fecha_Registro, usuario: v.FK_Usuario, usuarioNombre: nombre(v.FK_Usuario), cuentaOrigen: v.Cuenta_Origen,
        total: v.Total, tipoPago: v.Tipo_Pago, pago: v.Pago, cambio: v.Cambio, cliente: v.FK_Cliente, fkDireccion: v.FK_Direccion,
        descuento: v.Descuento, turno: v.Turno, productos
      };

      // Cliente nuevo (ID negativo) o, si cambió la cuenta, sus datos para buscarlo por teléfono
      if (v.FK_Cliente < 0) {
        const c = db.uno('SELECT * FROM clientes_nuevos WHERE ID_Cliente = ?', [v.FK_Cliente]);
        const d = v.FK_Direccion < 0 ? db.uno('SELECT * FROM direcciones_nuevas WHERE ID_Direccion = ?', [v.FK_Direccion]) : null;
        if (c) {
          venta.clienteNuevo = { uuid: c.UUID, nombre: c.Nombre, telefono: c.Telefono };
          if (d) {
            venta.clienteNuevo.direccion = { uuid: d.UUID, calle: d.Calle, noExterior: d.No_Exterior, noInterior: d.No_Interior, cp: d.CP, colonia: d.Colonia, ciudad: d.Ciudad, estado: d.Estado, pais: d.Pais, detalles: d.Detalles };
          }
        }
        venta.cliente = 0;
        venta.fkDireccion = 0;
      } else if (v.FK_Cliente > 0) {
        const c = db.existeTabla('clientes') ? db.uno('SELECT Nombre, Telefono FROM clientes WHERE ID_Cliente = ?', [v.FK_Cliente]) : null;
        if (c) {
          venta.clienteDatos = { uuid: `c${v.Cuenta_Origen}-${v.FK_Cliente}`, nombre: c.Nombre, telefono: c.Telefono };
        }
      }
      return venta;
    });

    const movimientos = db.todos('SELECT * FROM movimientos_caja WHERE FK_Detalle_Caja = ? AND Subido = 0', [t.ID_Detalle_Caja])
      .map((m) => ({ uuid: m.UUID, tipo: m.Tipo, monto: m.Monto, descripcion: m.Descripcion, fecha: m.Fecha_Registro }));

    return {
      uuid: t.UUID, cuentaOrigen: t.Cuenta_Origen, fechaAbrir: t.Fecha_Abrir, montoAbrir: t.Monto_Abrir, usuarioAbrir: t.FK_Usuario_Abrir,
      movimientos, ventas,
      cierre: t.Fecha_Cierre !== '0000-00-00 00:00:00' ? { fecha: t.Fecha_Cierre, monto: t.Monto_Cierre, usuario: t.FK_Usuario_Cierre } : null
    };
  });
}

// Sube todo lo pendiente; lo que el servidor confirma se marca como subido y se borra al bajar el catálogo
async function subir() {
  const turnos = paqueteSubida();
  if (!turnos.length) {
    await bajar();
    return { subidas: 0, errores: [] };
  }
  const r = await llamar({ accion: 'subir', turnos });
  const ok = new Set(r.subidos);
  let subidas = 0;
  db.transaccion(() => {
    for (const t of db.todos('SELECT ID_Detalle_Caja, UUID FROM detalles_caja')) {
      if (ok.has(t.UUID)) {
        db.db.run('UPDATE detalles_caja SET Subido = 1 WHERE ID_Detalle_Caja = ?', [t.ID_Detalle_Caja]);
      }
      if (ok.has(t.UUID + ':cierre')) {
        db.db.run('UPDATE detalles_caja SET Cierre_Subido = 1 WHERE ID_Detalle_Caja = ?', [t.ID_Detalle_Caja]);
      }
    }
    for (const v of db.todos('SELECT ID_Venta, UUID FROM ventas WHERE Subido = 0')) {
      if (ok.has(v.UUID)) {
        db.db.run('UPDATE ventas SET Subido = 1 WHERE ID_Venta = ?', [v.ID_Venta]);
        subidas++;
      }
    }
    for (const m of db.todos('SELECT ID_Movimiento, UUID FROM movimientos_caja WHERE Subido = 0')) {
      if (ok.has(m.UUID)) {
        db.db.run('UPDATE movimientos_caja SET Subido = 1 WHERE ID_Movimiento = ?', [m.ID_Movimiento]);
      }
    }
  });
  db.ajuste('ultimaSubida', ahora());

  try {
    await bajar();
  } catch (e) {
    // Las ventas ya quedaron arriba; el catálogo se actualizará en la siguiente sincronización
  }
  return { subidas, errores: r.errores };
}

// Libera la caja en la web y deja el equipo sin enlazar. Las ventas y cortes sin subir se conservan
// y se suben cuando el equipo se vuelva a enlazar.
async function desenlazar() {
  try {
    await llamar({ accion: 'desenlazar' });
  } catch (e) {
    if (e.codigo !== 'desenlazado') {
      throw e; // sin internet no se puede liberar la caja en la web
    }
  }
  db.ajuste('enlace', null);
  db.ajuste('desenlazado', false);
}

module.exports = { iniciar, enlazar, bajar, subir, desenlazar, pendientes, ErrorServidor };
