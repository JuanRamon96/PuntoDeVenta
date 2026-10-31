// Prueba de punta a punta contra el servidor local (XAMPP + php -S): enlazar, iniciar sesión, abrir caja, vender, corte y subir.
// Requiere: php -S 127.0.0.1:8099 -t C:/xampp/htdocs/PuntoDeVenta  y el usuario de pruebas/usuario-prueba.local.json
// Uso: npm test
const assert = require('assert');
const fs = require('fs');
const os = require('os');
const path = require('path');
const { execFileSync } = require('child_process');

process.env.VENTASTOOL_API = process.env.VENTASTOOL_API || 'http://127.0.0.1:8099/app/api/offline.php';
const PHP = process.env.PHP || 'C:/xampp/php/php.exe';
const MYSQL = process.env.MYSQL || 'C:/xampp/mysql/bin/mysql.exe';
const CUENTA = 1;
const CAJA = 2;

const db = require('../servidor/db');
const rutas = require('../servidor/rutas');

const mysql = (sql) => execFileSync(MYSQL, ['-uroot', '-N', `punto_venta_${CUENTA}`, '-e', sql]).toString().trim();
const pedir = (p) => rutas.atender('index.php', 'POST', p);

(async () => {
  const carpeta = fs.mkdtempSync(path.join(os.tmpdir(), 'ventastool-prueba-'));
  await db.abrir(path.join(carpeta, 'prueba.sqlite'));
  rutas.iniciar({ carpetaDatos: carpeta, web: path.join(__dirname, '..', 'web'), version: 'prueba' });
  const usuario = JSON.parse(fs.readFileSync(path.join(__dirname, 'usuario-prueba.local.json'), 'utf8'));

  // 1. Sin enlazar se muestra la pantalla de enlace
  assert.match(await rutas.atender('index.php', 'GET', {}), /Enlazar este equipo/);
  assert.match(await pedir({ accion: 'enlazar', codigo: 'AAAA-AAAA' }), /^Error~/);

  // 2. Enlazar con un código real
  const codigo = execFileSync(PHP, [path.join(__dirname, 'generar-codigo.php'), String(CUENTA), String(CAJA)]).toString().trim();
  assert.match(codigo, /^[A-Z0-9]{4}-[A-Z0-9]{4}$/, codigo);
  assert.strictEqual(await pedir({ accion: 'enlazar', codigo }), 'Correcto');
  assert.match(await rutas.atender('index.php', 'GET', {}), /formLogin/, 'ya enlazado pide iniciar sesión');
  assert.ok(db.valor('SELECT COUNT(*) FROM productos') > 0, 'se bajaron productos');

  // 3. Login con la contraseña de la web
  assert.strictEqual(await pedir({ accion: 'login', correo: usuario.correo, contrasena: 'mala' }), '0');
  assert.strictEqual(await pedir({ accion: 'login', correo: 'nadie@x.com', contrasena: 'x' }), 'No existe');
  assert.strictEqual(await pedir({ accion: 'login', correo: usuario.correo, contrasena: usuario.contrasena }), 'Correcto');
  const layout = await rutas.atender('index.php', 'GET', {});
  assert.match(layout, /id="bMenuCajas"/);
  assert.doesNotMatch(layout, /carga="v_cajas"/);
  assert.doesNotMatch(layout, /paypal\.com|v_compras|v_facturas/);
  assert.match(layout, /bSincronizarOffline/);
  assert.match(layout, /bDesenlazarEquipo/);
  assert.doesNotMatch(await pedir({ metodo: 'cambiar', accion: 'v_productos_offline' }), /bSincronizarOffline/);

  // 4. Abrir caja
  assert.match(await pedir({ metodo: 'consultar', accion: 'cajas', buscar: '', limit: 10, pagina: 1 }), /bAbrirCaja/);
  assert.strictEqual(await pedir({ metodo: 'insertar', accion: 'cajas', tipo: 'abrir', fkCaja: CAJA, montoCaja: '500' }), 'Correcto');
  assert.strictEqual(JSON.parse(await pedir({ metodo: 'detalles', accion: 'caja', tipo: 'consultarCaja' })).ID_Caja, String(CAJA));

  // 5. Buscar producto y vender
  const idProd = Number(mysql("SELECT p.ID_Producto FROM productos p JOIN inventario i ON i.FK_Producto = p.ID_Producto WHERE i.Cantidad >= 5 LIMIT 1"));
  const codigoProd = mysql(`SELECT Codigo FROM productos WHERE ID_Producto = ${idProd}`);
  const existenciaServidor = Number(mysql(`SELECT Cantidad FROM inventario WHERE FK_Producto = ${idProd}`));
  const prod = JSON.parse(await pedir({ metodo: 'consultar', accion: 'caja', tipo: 'agregarProducto', codigo: codigoProd }));
  assert.strictEqual(Number(prod.Existencia), existenciaServidor);

  const venta = (cantidad) => pedir({
    metodo: 'insertar', accion: 'caja', tipo: 'venta', idCaja: CAJA, descuento: 0, total: Number(prod.Precio) * cantidad, tipoPago: 'Efectivo',
    pago: Number(prod.Precio) * cantidad, cambio: 0, cliente: 0, fkDireccion: 0, detalles: '', turno: '',
    productos: JSON.stringify([{ id: idProd, descripcion: prod.Descripcion, precio: Number(prod.Precio), cantidad, descuento: 0, total: Number(prod.Precio) * cantidad, impuestos: [], lotes: [] }])
  });
  assert.match(await venta(existenciaServidor + 1), /^Offline~No hay existencia suficiente/);
  const r1 = await venta(2);
  assert.match(r1, /^Correcto~\d+$/);
  const idVenta = Number(r1.split('~')[1]);
  assert.strictEqual(JSON.parse(await pedir({ metodo: 'consultar', accion: 'caja', tipo: 'agregarProducto', codigo: codigoProd })).Existencia, String(existenciaServidor - 2));
  assert.match(await rutas.atender('controladores/pdf/ticketVenta.php', 'GET', { id: idVenta }), /Folio D\d+-000001/);

  // Venta para eliminar: la existencia regresa
  const r2 = await venta(1);
  assert.strictEqual(await pedir({ metodo: 'eliminar', accion: 'ventas', idVenta: r2.split('~')[1] }), 'Correcto');
  assert.strictEqual(db.valor('SELECT Cantidad FROM inventario WHERE FK_Producto = ?', [idProd]), existenciaServidor - 2);
  assert.strictEqual(JSON.parse(await pedir({ metodo: 'consultar', accion: 'ventas', buscar: '', limit: 10, pagina: 1, ordenColumna: 'Folio', orden: 'desc' })).data.length, 1);

  // Cliente nuevo desde la caja
  const rc = (await pedir({ metodo: 'modificar', accion: 'caja', tipo: 'cliente', telefono: '5559990000', nombre: 'Cliente Prueba Offline', calle: 'Morelos', noExterior: '5', noInterior: '', cp: '', colonia: '', ciudad: '', estado: '', pais: '', detalles: '', idCliente: '', idDomicilio: '' })).split('~');
  assert.strictEqual(rc[0], 'Correcto');
  assert.ok(Number(rc[1]) < 0);
  const r3 = await pedir({
    metodo: 'insertar', accion: 'caja', tipo: 'venta', idCaja: CAJA, descuento: 0, total: prod.Precio, tipoPago: 'Efectivo', pago: prod.Precio, cambio: 0,
    cliente: rc[1], fkDireccion: 0, detalles: '', turno: '',
    productos: JSON.stringify([{ id: idProd, descripcion: prod.Descripcion, precio: Number(prod.Precio), cantidad: 1, descuento: 0, total: Number(prod.Precio), impuestos: [], lotes: [] }])
  });
  assert.match(r3, /^Correcto~/);

  // 6. Movimiento y corte
  assert.strictEqual(await pedir({ metodo: 'insertar', accion: 'caja', tipo: 'movimiento', tipoMov: 'Entrada', cajaId: CAJA, cantidad: '100', descripcion: 'Cambio' }), 'Correcto');
  const corte = JSON.parse(await pedir({ metodo: 'consultar', accion: 'caja', tipo: 'corte', idCaja: CAJA }));
  assert.strictEqual(corte.movimientos.length, 1);
  const rCierre = await pedir({ metodo: 'modificar', accion: 'caja', tipo: 'cerrar', idCaja: CAJA, fecha_cierre: '', monto: '900' });
  assert.match(rCierre, /^Correcto~\d+$/);
  assert.match(await rutas.atender('controladores/pdf/ticketCaja.php', 'GET', { id: rCierre.split('~')[1] }), /CORTE DE CAJA/);
  assert.strictEqual(await pedir({ metodo: 'detalles', accion: 'caja', tipo: 'consultarCaja' }), 'No');

  // 7. Subir: las ventas llegan al servidor una sola vez y el inventario se iguala
  const antes = Number(mysql('SELECT COUNT(*) FROM ventas'));
  const subida = JSON.parse(await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'subir' }));
  assert.ok(subida.ok, subida.error);
  assert.strictEqual(subida.subidas, 2);
  assert.strictEqual(Number(mysql('SELECT COUNT(*) FROM ventas')), antes + 2);
  assert.strictEqual(Number(mysql(`SELECT Cantidad FROM inventario WHERE FK_Producto = ${idProd}`)), existenciaServidor - 3);
  assert.strictEqual(db.valor('SELECT Cantidad FROM inventario WHERE FK_Producto = ?', [idProd]), existenciaServidor - 3);
  assert.strictEqual(db.valor('SELECT COUNT(*) FROM ventas'), 0, 'las ventas subidas desaparecen del equipo');
  assert.strictEqual(mysql("SELECT COUNT(*) FROM clientes WHERE Telefono = '5559990000'"), '1');
  const otra = JSON.parse(await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'subir' }));
  assert.strictEqual(otra.subidas, 0);
  assert.strictEqual(Number(mysql('SELECT COUNT(*) FROM ventas')), antes + 2, 'no se duplican');

  // 8. Desenlazar en la web: el equipo conserva lo no subido y lo sube con un código nuevo
  await pedir({ metodo: 'insertar', accion: 'cajas', tipo: 'abrir', fkCaja: CAJA, montoCaja: '100' });
  await venta(1);
  execFileSync(MYSQL, ['-uroot', '-e', `UPDATE punto_subs.dispositivos_offline SET Estado = 'Desenlazado', Token_Hash = NULL WHERE FK_Cuenta = ${CUENTA}`]);
  const fallo = JSON.parse(await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'subir' }));
  assert.strictEqual(fallo.codigo, 'desenlazado');
  assert.strictEqual(db.valor('SELECT COUNT(*) FROM ventas WHERE Subido = 0'), 1, 'no se borra nada');
  const codigo2 = execFileSync(PHP, [path.join(__dirname, 'generar-codigo.php'), String(CUENTA), String(CAJA)]).toString().trim();
  assert.ok(JSON.parse(await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'enlazar', codigo: codigo2 })).ok);
  const tras = JSON.parse(await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'subir' }));
  assert.strictEqual(tras.subidas, 1);

  // Cierra el turno que quedó abierto para dejar la caja limpia
  await pedir({ metodo: 'modificar', accion: 'caja', tipo: 'cerrar', idCaja: CAJA, fecha_cierre: '', monto: '0' });
  await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'subir' });

  // 9. Desenlazar desde el equipo: libera la caja en la web y vuelve a pedir código
  await pedir({ metodo: 'insertar', accion: 'cajas', tipo: 'abrir', fkCaja: CAJA, montoCaja: '0' });
  assert.match(await venta(1), /^Correcto~/); // queda una venta sin subir: no debe perderse
  assert.ok(JSON.parse(await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'desenlazar' })).ok);
  assert.match(await rutas.atender('index.php', 'GET', {}), /Enlazar este equipo/);
  assert.strictEqual(mysql(`SELECT COUNT(*) FROM punto_subs.dispositivos_offline WHERE FK_Cuenta = ${CUENTA} AND FK_Caja = ${CAJA} AND Estado = 'Activo'`), '0');
  assert.strictEqual(db.valor('SELECT COUNT(*) FROM ventas WHERE Subido = 0'), 1);
  const codigo3 = execFileSync(PHP, [path.join(__dirname, 'generar-codigo.php'), String(CUENTA), String(CAJA)]).toString().trim();
  assert.strictEqual(await pedir({ accion: 'enlazar', codigo: codigo3 }), 'Correcto');
  assert.strictEqual(await pedir({ accion: 'login', correo: usuario.correo, contrasena: usuario.contrasena }), 'Correcto');
  assert.strictEqual(JSON.parse(await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'subir' })).subidas, 1);

  // 10. Ventas como en la web: abono, devolución, modificar y cancelar; todo se sube igual
  await pedir({ metodo: 'insertar', accion: 'cajas', tipo: 'abrir', fkCaja: CAJA, montoCaja: '0' });
  const precio = Number(prod.Precio);
  const invServidor0 = Number(mysql(`SELECT Cantidad FROM inventario WHERE FK_Producto = ${idProd}`));
  const idCliente = db.valor('SELECT ID_Cliente FROM clientes WHERE ID_Cliente > 0 LIMIT 1');
  const vender = (cantidad, extra) => pedir(Object.assign({
    metodo: 'insertar', accion: 'caja', tipo: 'venta', idCaja: CAJA, descuento: 0, total: precio * cantidad, tipoPago: 'Efectivo', pago: precio * cantidad, cambio: 0,
    cliente: 0, fkDireccion: 0, detalles: '', turno: '',
    productos: JSON.stringify([{ id: idProd, descripcion: prod.Descripcion, precio, cantidad, descuento: 0, total: precio * cantidad, impuestos: [], lotes: [] }])
  }, extra)).then((r) => Number(r.split('~')[1]));

  const vCredito = await vender(2, { tipoPago: 'Crédito', pago: precio, cliente: idCliente });
  assert.strictEqual(db.valor('SELECT Estatus FROM ventas WHERE ID_Venta = ?', [vCredito]), 1);
  assert.match(await pedir({ metodo: 'consultar', accion: 'ventas', tipo: 'ventas', buscar: '', limit: 25, pagina: 1, ordenColumna: 'Fecha', orden: 'desc' }), /bPagosVenta/);
  assert.strictEqual(await pedir({ metodo: 'detalles', accion: 'ventas', tipo: 'pago', id: vCredito, importePagoVenta: String(precio), conceptoPagoVenta: 'Abono', tipoDePagoVenta: 'Efectivo', detallesPagoVenta: '', cajaPagoVenta: CAJA }), 'Correcto');
  assert.strictEqual(db.valor('SELECT Estatus FROM ventas WHERE ID_Venta = ?', [vCredito]), 0, 'pagada por completo');
  assert.match(await pedir({ metodo: 'detalles', accion: 'ventas', tipo: 'devoluciones', id: vCredito }), /<input/);
  const idDet = db.valor('SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?', [vCredito]);
  assert.strictEqual(await pedir({ metodo: 'detalles', accion: 'ventas', tipo: 'devolucion', productos: JSON.stringify([{ id: idDet, cantidad: 1 }]) }), 'Correcto');
  assert.strictEqual(db.valor('SELECT Total FROM ventas WHERE ID_Venta = ?', [vCredito]), precio, 'la devolución baja el total');

  const vModificar = await vender(1);
  const datosMod = JSON.parse(await pedir({ metodo: 'detalles', accion: 'ventas', tipo: 'modificar', idVenta: vModificar }));
  assert.strictEqual(datosMod.Productos.length, 1);
  assert.strictEqual(await pedir({ metodo: 'detalles', accion: 'ventas', tipo: 'modificarVenta', id: vModificar, cliente: 0, descuento: 0, total: precio * 3, pago: precio * 3, cambio: 0, tipoPago: 'Efectivo', detalles: 'Modificada',
    productos: JSON.stringify([{ idProd: idProd, descripcion: prod.Descripcion, precio, cantidad: 3, descuento: 0, total: precio * 3, impuestos: [] }]) }), 'Correcto');

  const vCancelar = await vender(1);
  assert.strictEqual(await pedir({ metodo: 'modificar', accion: 'ventas', idVenta: vCancelar, regresarInventario: 1 }), 'Correcto');
  assert.strictEqual(db.valor('SELECT Estatus FROM ventas WHERE ID_Venta = ?', [vCancelar]), 2);
  // vendido neto: 2 - 1 devuelto + 3 modificada + 0 cancelada
  assert.strictEqual(db.valor('SELECT Cantidad FROM inventario WHERE FK_Producto = ?', [idProd]), invServidor0 - 4);

  const antes10 = Number(mysql('SELECT IFNULL(MAX(ID_Venta), 0) FROM ventas'));
  assert.strictEqual(JSON.parse(await pedir({ metodo: 'detalles', accion: 'offline', tipo: 'subir' })).subidas, 3);
  assert.strictEqual(Number(mysql(`SELECT Cantidad FROM inventario WHERE FK_Producto = ${idProd}`)), invServidor0 - 4, 'el servidor queda igual que el equipo');
  assert.strictEqual(db.valor('SELECT Cantidad FROM inventario WHERE FK_Producto = ?', [idProd]), invServidor0 - 4);
  const subidas = mysql(`SELECT CONCAT_WS('|', Estatus, Total, Detalles) FROM ventas WHERE ID_Venta > ${antes10} ORDER BY ID_Venta`).split(/\r?\n/);
  assert.strictEqual(subidas.length, 3);
  assert.ok(subidas[0].startsWith(`0|${precio}|`), 'crédito pagado y con devolución: ' + subidas[0]);
  assert.match(subidas[2], /^2\|/, 'cancelada');
  assert.strictEqual(mysql(`SELECT COUNT(*) FROM ventas_pagos WHERE FK_Venta > ${antes10}`), '1');
  assert.strictEqual(mysql(`SELECT COUNT(*) FROM devoluciones d JOIN detalles_ventas x ON x.ID_Detalle_Venta = d.FK_Detalle_Venta WHERE x.FK_Venta > ${antes10}`), '1');

  // Ticket con devolución y comprobante de abono (como la web)
  // (las ventas ya se subieron: se revisa con otra venta local)
  const vDev = await vender(2);
  const idDet2 = db.valor('SELECT ID_Detalle_Venta FROM detalles_ventas WHERE FK_Venta = ?', [vDev]);
  await pedir({ metodo: 'detalles', accion: 'ventas', tipo: 'devolucion', productos: JSON.stringify([{ id: idDet2, cantidad: 1 }]) });
  assert.match(await rutas.atender('controladores/pdf/ticketVenta.php', 'GET', { id: vDev }), /\(Dev: 1\.00\)/);
  const vCred2 = await vender(1, { tipoPago: 'Crédito', pago: 0, cliente: idCliente });
  await pedir({ metodo: 'detalles', accion: 'ventas', tipo: 'pago', id: vCred2, importePagoVenta: '1', conceptoPagoVenta: 'Abono', tipoDePagoVenta: 'Efectivo', detallesPagoVenta: '', cajaPagoVenta: CAJA });
  const idPago = db.valor('SELECT ID_Pago FROM ventas_pagos WHERE FK_Venta = ?', [vCred2]);
  const tPago = await rutas.atender('controladores/pdf/ticketPago.php', 'GET', { id: idPago, impresion: '1' });
  assert.match(tPago, /Total Venta/);
  assert.match(tPago, /\*{32}/);
  assert.doesNotMatch(tPago, /<script>window\.print/);
  await pedir({ metodo: 'eliminar', accion: 'ventas', idVenta: vDev });
  await pedir({ metodo: 'eliminar', accion: 'ventas', idVenta: vCred2 });
  assert.strictEqual(db.valor('SELECT Cantidad FROM inventario WHERE FK_Producto = ?', [idProd]), invServidor0 - 4, 'eliminar con devolución deja el inventario bien');

  // 11. Impresión: versión para la impresora sin scripts del diálogo y con la escala
  const paraImpresora = await rutas.atender('controladores/pdf/ticketPrueba.php', 'GET', { impresion: '1' });
  assert.match(paraImpresora, /zoom: 0\.63/);
  assert.doesNotMatch(paraImpresora, /<script/);
  assert.strictEqual(await pedir({ metodo: 'modificar', accion: 'impresora', nombre: '', ancho: '58', escala: '', copias: '1' }), 'Correcto');
  assert.match(await rutas.atender('controladores/pdf/ticketPrueba.php', 'GET', { impresion: '1' }), /zoom: 0\.44/);

  console.log('Todas las pruebas pasaron. Base de prueba en', carpeta);
})().catch((e) => {
  console.error(e);
  process.exit(1);
});
