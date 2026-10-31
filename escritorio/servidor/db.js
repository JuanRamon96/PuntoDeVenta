// Base de datos local (SQLite vía sql.js, sin módulos nativos para que compile igual en Windows y Mac).
// Las tablas que baja el servidor llevan el mismo nombre y columnas que en MySQL, así las consultas se parecen a las del PHP.
const fs = require('fs');
const path = require('path');
const initSqlJs = require('sql.js');

let SQL = null;
let db = null;
let archivo = null;
let enTransaccion = 0;

// Tablas propias del equipo (lo que se vende y se sube). Mismo formato que MySQL + columnas de control.
const ESQUEMA_LOCAL = `
CREATE TABLE IF NOT EXISTS ajustes (Clave TEXT PRIMARY KEY, Valor TEXT);
CREATE TABLE IF NOT EXISTS detalles_caja (
  ID_Detalle_Caja INTEGER PRIMARY KEY AUTOINCREMENT, UUID TEXT NOT NULL, Cuenta_Origen INTEGER NOT NULL,
  FK_Caja INTEGER NOT NULL, Fecha_Abrir TEXT NOT NULL, Monto_Abrir REAL NOT NULL, FK_Usuario_Abrir INTEGER NOT NULL,
  Fecha_Cierre TEXT NOT NULL DEFAULT '0000-00-00 00:00:00', Monto_Cierre REAL NOT NULL DEFAULT 0, FK_Usuario_Cierre INTEGER NOT NULL DEFAULT 0,
  Subido INTEGER NOT NULL DEFAULT 0, Cierre_Subido INTEGER NOT NULL DEFAULT 0);
CREATE TABLE IF NOT EXISTS ventas (
  ID_Venta INTEGER PRIMARY KEY AUTOINCREMENT, UUID TEXT NOT NULL, Folio TEXT NOT NULL, FK_Usuario INTEGER NOT NULL, FK_Cliente INTEGER NOT NULL DEFAULT 0,
  FK_Detalles_Caja INTEGER NOT NULL, Descuento REAL NOT NULL DEFAULT 0, Total REAL NOT NULL DEFAULT 0, Tipo_Pago TEXT NOT NULL DEFAULT '',
  Pago REAL NOT NULL DEFAULT 0, Cambio REAL NOT NULL DEFAULT 0, Detalles TEXT NOT NULL DEFAULT '', Fecha_Registro TEXT NOT NULL,
  Estatus INTEGER NOT NULL DEFAULT 0, Turno TEXT NOT NULL DEFAULT '', FK_Direccion INTEGER NOT NULL DEFAULT 0, Cuenta_Origen INTEGER NOT NULL DEFAULT 0, Subido INTEGER NOT NULL DEFAULT 0);
CREATE TABLE IF NOT EXISTS detalles_ventas (
  ID_Detalle_Venta INTEGER PRIMARY KEY AUTOINCREMENT, FK_Venta INTEGER NOT NULL, FK_Producto INTEGER NOT NULL, Codigo TEXT NOT NULL DEFAULT '',
  Descripcion TEXT NOT NULL, Precio REAL NOT NULL, Costo REAL NOT NULL DEFAULT 0, Cantidad REAL NOT NULL, Descuento REAL NOT NULL DEFAULT 0, Total REAL NOT NULL);
CREATE TABLE IF NOT EXISTS impuestos_ventas (
  ID_Impuesto_Venta INTEGER PRIMARY KEY AUTOINCREMENT, FK_Detalle_Venta INTEGER NOT NULL, Nombre TEXT, Porcentaje REAL, Clave_CFDI TEXT, Clase TEXT, Tipo_Factor TEXT);
CREATE TABLE IF NOT EXISTS detalles_ventas_lotes (
  ID_Detalle_Venta_Lote INTEGER PRIMARY KEY AUTOINCREMENT, FK_Detalle_Venta INTEGER NOT NULL, FK_Lote INTEGER NOT NULL, Cantidad REAL NOT NULL);
CREATE TABLE IF NOT EXISTS movimientos_caja (
  ID_Movimiento INTEGER PRIMARY KEY AUTOINCREMENT, UUID TEXT NOT NULL, Tipo TEXT NOT NULL, Monto REAL NOT NULL, Descripcion TEXT NOT NULL,
  Fecha_Registro TEXT NOT NULL, FK_Detalle_Caja INTEGER NOT NULL, Subido INTEGER NOT NULL DEFAULT 0);

-- Igual que los triggers de MySQL (rebajaInven / devolverInve): el inventario local se mueve con las ventas locales.
-- Las ventas ya subidas no devuelven inventario al borrarse: el servidor ya las descontó.
CREATE TRIGGER IF NOT EXISTS rebaja_inventario AFTER INSERT ON detalles_ventas BEGIN
  UPDATE inventario SET Cantidad = Cantidad - NEW.Cantidad WHERE FK_Producto = NEW.FK_Producto;
END;
CREATE TRIGGER IF NOT EXISTS ajusta_inventario AFTER UPDATE OF Cantidad ON detalles_ventas BEGIN
  UPDATE inventario SET Cantidad = Cantidad - (NEW.Cantidad - OLD.Cantidad) WHERE FK_Producto = NEW.FK_Producto;
END;
CREATE TRIGGER IF NOT EXISTS devuelve_inventario BEFORE DELETE ON detalles_ventas
WHEN (SELECT Subido FROM ventas WHERE ID_Venta = OLD.FK_Venta) = 0 BEGIN
  UPDATE inventario SET Cantidad = Cantidad + OLD.Cantidad WHERE FK_Producto = OLD.FK_Producto;
END;
CREATE TRIGGER IF NOT EXISTS rebaja_lote AFTER INSERT ON detalles_ventas_lotes BEGIN
  UPDATE inventario_lotes SET Cantidad = Cantidad - NEW.Cantidad WHERE ID_Lote = NEW.FK_Lote;
END;
CREATE TRIGGER IF NOT EXISTS devuelve_lote BEFORE DELETE ON detalles_ventas_lotes
WHEN (SELECT v.Subido FROM ventas v JOIN detalles_ventas d ON d.FK_Venta = v.ID_Venta WHERE d.ID_Detalle_Venta = OLD.FK_Detalle_Venta) = 0 BEGIN
  UPDATE inventario_lotes SET Cantidad = Cantidad + OLD.Cantidad WHERE ID_Lote = OLD.FK_Lote;
END;

-- Abonos y devoluciones de ventas del equipo (mismas tablas y reglas que en MySQL)
CREATE TABLE IF NOT EXISTS ventas_pagos (
  ID_Pago INTEGER PRIMARY KEY AUTOINCREMENT, UUID TEXT NOT NULL, FK_Venta INTEGER NOT NULL, Concepto TEXT NOT NULL DEFAULT '', Monto REAL NOT NULL,
  Tipo_Pago TEXT NOT NULL DEFAULT '', Detalles TEXT NOT NULL DEFAULT '', FK_Usuario INTEGER NOT NULL, FK_Detalle_Caja INTEGER NOT NULL DEFAULT 0,
  Archivo TEXT NOT NULL DEFAULT '', Fecha_Registro TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS devoluciones (
  ID_Devolucion INTEGER PRIMARY KEY AUTOINCREMENT, FK_Detalle_Venta INTEGER NOT NULL, Cantidad REAL NOT NULL);

-- actualizar_venta / estatus_venta
CREATE TRIGGER IF NOT EXISTS pago_completa_venta AFTER INSERT ON ventas_pagos
WHEN (SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = NEW.FK_Venta) >= (SELECT Total - Pago + Cambio FROM ventas WHERE ID_Venta = NEW.FK_Venta) BEGIN
  UPDATE ventas SET Estatus = 0 WHERE ID_Venta = NEW.FK_Venta;
END;
CREATE TRIGGER IF NOT EXISTS pago_borrado_venta AFTER DELETE ON ventas_pagos
WHEN IFNULL((SELECT SUM(Monto) FROM ventas_pagos WHERE FK_Venta = OLD.FK_Venta), 0) < (SELECT Total - Pago + Cambio FROM ventas WHERE ID_Venta = OLD.FK_Venta)
  AND (SELECT Subido FROM ventas WHERE ID_Venta = OLD.FK_Venta) = 0 BEGIN
  UPDATE ventas SET Estatus = 1 WHERE ID_Venta = OLD.FK_Venta;
END;

-- trg_devoluciones_after_insert / after_delete: regresan inventario y ajustan el total de la venta
CREATE TRIGGER IF NOT EXISTS devolucion_agregada AFTER INSERT ON devoluciones
WHEN (SELECT Cantidad FROM detalles_ventas WHERE ID_Detalle_Venta = NEW.FK_Detalle_Venta) > 0 BEGIN
  UPDATE inventario SET Cantidad = Cantidad + NEW.Cantidad WHERE FK_Producto = (SELECT FK_Producto FROM detalles_ventas WHERE ID_Detalle_Venta = NEW.FK_Detalle_Venta);
  UPDATE ventas SET Total = Total - (SELECT Total / Cantidad * NEW.Cantidad FROM detalles_ventas WHERE ID_Detalle_Venta = NEW.FK_Detalle_Venta),
    Cambio = Cambio + (SELECT Total / Cantidad * NEW.Cantidad FROM detalles_ventas WHERE ID_Detalle_Venta = NEW.FK_Detalle_Venta)
  WHERE ID_Venta = (SELECT FK_Venta FROM detalles_ventas WHERE ID_Detalle_Venta = NEW.FK_Detalle_Venta);
END;
CREATE TRIGGER IF NOT EXISTS devolucion_borrada AFTER DELETE ON devoluciones
WHEN (SELECT d.Cantidad FROM detalles_ventas d WHERE d.ID_Detalle_Venta = OLD.FK_Detalle_Venta) > 0
  AND (SELECT v.Subido FROM ventas v JOIN detalles_ventas d ON d.FK_Venta = v.ID_Venta WHERE d.ID_Detalle_Venta = OLD.FK_Detalle_Venta) = 0 BEGIN
  UPDATE inventario SET Cantidad = Cantidad - OLD.Cantidad WHERE FK_Producto = (SELECT FK_Producto FROM detalles_ventas WHERE ID_Detalle_Venta = OLD.FK_Detalle_Venta);
  UPDATE ventas SET Total = Total + (SELECT Total / Cantidad * OLD.Cantidad FROM detalles_ventas WHERE ID_Detalle_Venta = OLD.FK_Detalle_Venta),
    Cambio = Cambio - (SELECT Total / Cantidad * OLD.Cantidad FROM detalles_ventas WHERE ID_Detalle_Venta = OLD.FK_Detalle_Venta)
  WHERE ID_Venta = (SELECT FK_Venta FROM detalles_ventas WHERE ID_Detalle_Venta = OLD.FK_Detalle_Venta);
END;
`;

// Tablas que se reemplazan completas al sincronizar
const TABLAS_SERVIDOR = ['configuracion', 'cajas', 'sucursales', 'usuarios', 'productos', 'clasificaciones', 'inventario', 'inventario_lotes',
  'impuestos', 'impuestos_productos', 'promociones', 'clientes', 'direcciones_cliente'];

async function abrir(ruta) {
  archivo = ruta;
  SQL = await initSqlJs({ locateFile: (f) => path.join(path.dirname(require.resolve('sql.js')), f) });
  db = fs.existsSync(ruta) ? new SQL.Database(fs.readFileSync(ruta)) : new SQL.Database();

  // MySQL compara REGEXP sin distinguir mayúsculas; aquí igual
  db.create_function('regexp', (patron, valor) => {
    try {
      return new RegExp(String(patron), 'i').test(String(valor ?? '')) ? 1 : 0;
    } catch (e) {
      return String(valor ?? '').toLowerCase().includes(String(patron).toLowerCase()) ? 1 : 0;
    }
  });

  db.exec(ESQUEMA_LOCAL);
  // Columnas agregadas después de la versión 1.0.0 (bases ya creadas en equipos instalados)
  const columnasVentas = todos('PRAGMA table_info(ventas)').map((c) => c.name);
  if (!columnasVentas.includes('Fecha_Cancelacion')) {
    db.exec("ALTER TABLE ventas ADD COLUMN Fecha_Cancelacion TEXT NOT NULL DEFAULT '0000-00-00 00:00:00'");
  }
  if (!columnasVentas.includes('Regrezo_Inventario')) {
    db.exec('ALTER TABLE ventas ADD COLUMN Regrezo_Inventario INTEGER NOT NULL DEFAULT 0');
  }
  // Clientes y domicilios nuevos creados en la caja: se guardan aparte para no perderlos al reemplazar la tabla del servidor
  db.exec(`CREATE TABLE IF NOT EXISTS clientes_nuevos (ID_Cliente INTEGER PRIMARY KEY, UUID TEXT NOT NULL, Nombre TEXT NOT NULL, Telefono TEXT NOT NULL DEFAULT '');
    CREATE TABLE IF NOT EXISTS direcciones_nuevas (ID_Direccion INTEGER PRIMARY KEY, UUID TEXT NOT NULL, FK_Cliente INTEGER NOT NULL, Calle TEXT, No_Exterior TEXT, No_Interior TEXT,
      CP TEXT, Colonia TEXT, Ciudad TEXT, Estado TEXT, Pais TEXT, Detalles TEXT);`);
  guardar();
}

// Guarda en disco escribiendo primero un archivo temporal: si se va la luz a media escritura no se pierde la base
function guardar() {
  if (enTransaccion > 0) {
    return;
  }
  const temporal = archivo + '.tmp';
  fs.writeFileSync(temporal, Buffer.from(db.export()));
  fs.renameSync(temporal, archivo);
}

function todos(sql, params = []) {
  const st = db.prepare(sql);
  try {
    st.bind(params);
    const filas = [];
    while (st.step()) {
      filas.push(st.getAsObject());
    }
    return filas;
  } finally {
    st.free();
  }
}

function uno(sql, params = []) {
  return todos(sql, params)[0] || null;
}

function valor(sql, params = []) {
  const f = uno(sql, params);
  return f ? Object.values(f)[0] : null;
}

function ejecutar(sql, params = []) {
  db.run(sql, params);
  const id = valor('SELECT last_insert_rowid()');
  guardar();
  return id;
}

// Agrupa varias escrituras: todo o nada, y se guarda en disco una sola vez
function transaccion(fn) {
  db.exec('BEGIN');
  enTransaccion++;
  try {
    const r = fn();
    enTransaccion--;
    db.exec('COMMIT');
    guardar();
    return r;
  } catch (e) {
    enTransaccion--;
    db.exec('ROLLBACK');
    throw e;
  }
}

function existeTabla(nombre) {
  return !!uno("SELECT name FROM sqlite_master WHERE type = 'table' AND name = ?", [nombre]);
}

// Reemplaza una tabla con lo que mandó el servidor ({columnas, tipos, filas})
function reemplazarTabla(nombre, datos) {
  const columnas = datos.columnas.length ? datos.columnas : ['_vacia'];
  const tipos = datos.tipos || [];
  db.exec(`DROP TABLE IF EXISTS "${nombre}"`);
  db.exec(`CREATE TABLE "${nombre}" (${columnas.map((c, i) => `"${c}" ${tipos[i] === 'n' ? 'NUMERIC' : 'TEXT'}`).join(', ')})`);
  if (datos.filas.length) {
    const st = db.prepare(`INSERT INTO "${nombre}" VALUES (${columnas.map(() => '?').join(', ')})`);
    try {
      for (const fila of datos.filas) {
        st.run(fila);
      }
    } finally {
      st.free();
    }
  }
}

// Inserta una fila llenando con '' las columnas que no se dan (las tablas del servidor no tienen valores por defecto)
function insertarFila(tablaNombre, valores) {
  if (!existeTabla(tablaNombre)) {
    return;
  }
  const columnas = todos(`PRAGMA table_info("${tablaNombre}")`).map((c) => c.name);
  const datos = columnas.map((c) => (valores[c] !== undefined ? valores[c] : ''));
  db.run(`INSERT INTO "${tablaNombre}" (${columnas.map((c) => `"${c}"`).join(', ')}) VALUES (${columnas.map(() => '?').join(', ')})`, datos);
  guardar();
}

function ajuste(clave, nuevo) {
  if (nuevo === undefined) {
    const f = uno('SELECT Valor FROM ajustes WHERE Clave = ?', [clave]);
    return f ? JSON.parse(f.Valor) : null;
  }
  ejecutar('INSERT OR REPLACE INTO ajustes (Clave, Valor) VALUES (?, ?)', [clave, JSON.stringify(nuevo)]);
  return nuevo;
}

module.exports = { abrir, todos, uno, valor, ejecutar, transaccion, existeTabla, reemplazarTabla, insertarFila, ajuste, guardar, TABLAS_SERVIDOR, get db() { return db; } };
