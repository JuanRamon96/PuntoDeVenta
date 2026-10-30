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
  assert.match(await pedir({ metodo: 'cambiar', accion: 'v_productos_offline' }), /Sincronizar/);

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

  console.log('Todas las pruebas pasaron. Base de prueba en', carpeta);
})().catch((e) => {
  console.error(e);
  process.exit(1);
});
