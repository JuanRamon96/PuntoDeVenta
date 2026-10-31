// Port de controladores/pdf/ticketVenta.php y ticketCaja.php con datos locales.
// Los estilos se leen de los mismos archivos PHP (copiados en web/), así el ticket sale igual que en la web.
const fs = require('fs');
const path = require('path');
const db = require('./db');
const { numero, html, fechaHumana } = require('./util');

let web = null;
let carpetaDatos = null;

function iniciar(opciones) {
  web = opciones.web;
  carpetaDatos = opciones.carpetaDatos;
}

function estilos(archivo) {
  const texto = fs.readFileSync(path.join(web, 'controladores', 'pdf', archivo), 'utf8');
  const m = texto.match(/<style>([\s\S]*?)<\/style>/);
  return m ? m[1] : '';
}

const redondear = (n) => Math.round(Number(n) * 100) / 100;

function negocio() {
  const g = db.existeTabla('configuracion') ? db.uno('SELECT Nombre, Domicilio, Telefono, Foto FROM configuracion WHERE ID_Configuracion = 1') : null;
  let logo = '../../vistas/assets/images/logos/logo.png';
  if (g && String(g.Foto || '').trim() !== '' && fs.existsSync(path.join(carpetaDatos, 'imagenes', 'configuracion', path.basename(String(g.Foto).trim())))) {
    logo = '../../vistas/assets/images/configuracion/' + String(g.Foto).trim();
  }
  return { nombre: g ? g.Nombre : '', domicilio: g ? g.Domicilio : '', telefono: g ? g.Telefono : '', logo };
}

function nombreUsuario(id) {
  const u = db.uno('SELECT Nombre, Primer_Apellido, Segundo_Apellido FROM usuarios WHERE ID_Usuario = ?', [id]);
  return u ? `${u.Nombre} ${u.Primer_Apellido} ${u.Segundo_Apellido}` : '';
}

function venta(id, impresion) {
  return paraImprimir(ventaHtml(id), impresion);
}

function ventaHtml(id) {
  const v = db.uno('SELECT * FROM ventas WHERE ID_Venta = ?', [id]);
  if (!v) {
    return 'Venta no encontrada';
  }
  const cliente = v.FK_Cliente !== 0 ? db.uno('SELECT * FROM clientes WHERE ID_Cliente = ?', [v.FK_Cliente]) : null;
  const nombreCliente = cliente
    ? (cliente.Tipo === 'Moral' ? cliente.Razon_Social : `${cliente.Nombre} ${cliente.Primer_Apellido || ''} ${cliente.Segundo_Apellido || ''}`)
    : 'Publico en General';

  let tabla = '<table class="tablaDetalles"><thead><tr><th>Cant.</th><th>Precio</th><th>Desc.</th><th>Impto.</th><th>Total</th></tr></thead><tbody>';
  for (const d of db.todos('SELECT * FROM detalles_ventas WHERE FK_Venta = ?', [id])) {
    const subtotal = d.Precio * d.Cantidad;
    const subDes = subtotal - d.Descuento;
    let impuestos = '';
    for (const i of db.todos('SELECT * FROM impuestos_ventas WHERE FK_Detalle_Venta = ?', [d.ID_Detalle_Venta])) {
      const cuota = String(i.Tipo_Factor).toUpperCase() === 'CUOTA';
      const monto = cuota ? d.Cantidad * Number(i.Porcentaje) : subDes * (Number(i.Porcentaje) / 100);
      impuestos += `<p style="margin: 0; font-size: 16px;"><span>${html(i.Nombre)}</span> <span class="porcentaje">${redondear(i.Porcentaje)}</span>${cuota ? '' : '%'} ($<span class="dinero">${numero(monto)}</span>)</p>`;
    }

    let lotes = '';
    if (db.existeTabla('inventario_lotes')) {
      const filas = db.todos(`SELECT dvl.FK_Lote, SUM(dvl.Cantidad) AS Cantidad, IFNULL(l.Nombre_Lote, 'Lote #' || dvl.FK_Lote) AS Nombre_Lote, l.Fecha_Caducidad
        FROM detalles_ventas_lotes dvl LEFT JOIN inventario_lotes l ON dvl.FK_Lote = l.ID_Lote WHERE dvl.FK_Detalle_Venta = ? GROUP BY dvl.FK_Lote ORDER BY MIN(dvl.ID_Detalle_Venta_Lote)`, [d.ID_Detalle_Venta]);
      if (filas.length) {
        lotes = '<br><span style="font-size: 13px; font-style: italic;">Lotes: ' + filas.map((l) => {
          const cad = l.Fecha_Caducidad && !String(l.Fecha_Caducidad).startsWith('0000') ? ' - Cad: ' + String(l.Fecha_Caducidad).slice(0, 10).split('-').reverse().join('/') : '';
          return `${html(l.Nombre_Lote)} [Cód: ${d.Codigo ? html(d.Codigo) + '_' : ''}${l.FK_Lote}] (${redondear(l.Cantidad)}${cad})`;
        }).join(', ') + '</span>';
      }
    }

    tabla += `<tr><td colspan="5" style="text-align: left;">${html(d.Descripcion)}${lotes}</td></tr>
      <tr><td>${redondear(d.Cantidad)}</td><td>$${numero(d.Precio)}</td>
      <td>$${numero(d.Descuento)}<br><span style="font-size: 16px;">(${subtotal > 0 ? numero((d.Descuento / subtotal) * 100) : 0}%)</span></td>
      <td><span style="font-size: 16px;">Sub:</span> $${numero(subDes)}${impuestos}</td><td>$${numero(d.Total)}</td></tr>`;
  }
  tabla += '</tbody></table>';

  let direccion = '';
  if (cliente) {
    const dom = v.FK_Direccion === 0 ? cliente : db.uno('SELECT * FROM direcciones_cliente WHERE ID_Direccion = ?', [v.FK_Direccion]);
    if (dom) {
      if (dom.Calle) direccion = dom.Calle;
      if (dom.No_Exterior) direccion += ` #${dom.No_Exterior}`;
      if (dom.No_Interior) direccion += ` int.${dom.No_Interior}`;
      if (dom.CP) direccion += `, C.P. ${dom.CP}`;
      if (dom.Colonia) direccion += ` Col. ${dom.Colonia}`;
      if (dom.Detalles) direccion += `<br> Detalles: ${html(dom.Detalles)}`;
    }
  }

  let textoPago = 'Pagó con';
  let cambio = `<p>Cambio: $${numero(v.Cambio)}</p>`;
  if (v.Tipo_Pago === 'Crédito') {
    textoPago = 'Anticipo';
    cambio = `<p>Restante: $${numero(Math.max(0, v.Total - v.Pago))}</p>`;
  }
  const n = negocio();

  return `<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Ticket</title><style>${estilos('ticketVenta.php')}</style></head>
    <body onafterprint="funcionDespues()">
    <button type="submit" class="oculto-impresion" onclick="imprimir()">Imprimir</button>
    <img class="logo-negocio" src="${n.logo}" width="220px" onerror="this.src='../../vistas/assets/images/logos/logo.png'" />
    <p class="nombre-negocio">${html(n.nombre)}</p>
    <p class="domicilio">${html(n.domicilio)}</p>
    <p class="telefono">${html(n.telefono)}</p>
    <div class="linea-punteada"></div>
    <p class="fecha"><span>Fecha:</span> <span>${fechaHumana(v.Fecha_Registro)}</span></p>
    <p class="folio-grande">Folio ${html(v.Folio)}</p>
    ${v.Turno !== '' ? `<p class="turno-badge">Turno: ${html(v.Turno)}</p>` : ''}
    <div class="linea-punteada"></div>
    <div class="cliente-info">
      <p><strong>Cliente:</strong> ${html(nombreCliente)}</p>
      ${cliente && cliente.Telefono ? `<p><strong>Tel:</strong> ${html(cliente.Telefono)}</p>` : ''}
      ${direccion !== '' ? `<p class="direccionCliente">${direccion}</p>` : ''}
    </div>
    <div class="linea-punteada"></div>
    ${tabla}
    <div class="pago">
      <p><span>Subtotal:</span> <span>$${numero(v.Total - v.Descuento)}</span></p>
      <p><span>Descuento:</span> <span>$${numero(v.Descuento)}</span></p>
      <p class="total-final"><span>Total:</span> <span>$${numero(v.Total)}</span></p>
      <p><span>${textoPago}:</span> <span>$${numero(v.Pago)}</span></p>
      ${cambio}
    </div>
    <footer>
      ${String(v.Detalles).trim() !== '' ? `<p class="detalles"><strong>Detalles:</strong> ${html(v.Detalles)}</p>` : ''}
      <p class="vendedor">Atendió: ${html(nombreUsuario(v.FK_Usuario))}</p>
      <div class="separador"></div><div class="separador"></div>
      <p class="gracias">¡Gracias por su compra!</p>
      <div class="separador"></div><div class="separador"></div>
      <p class="text-center m-0">www.bigtool.mx</p>
    </footer>
    </body>
    <script>
    window.print();
    function imprimir() { window.print(); }
    function funcionDespues() {
      setTimeout(function () {
        var b = parent.document.getElementById("bAbrirCaja");
        if (b && parent !== window) { b.click(); }
      }, 300);
    }
    </script></html>`;
}

function corte(id, impresion) {
  return paraImprimir(corteHtml(id), impresion);
}

function corteHtml(id) {
  const t = db.uno('SELECT * FROM detalles_caja WHERE ID_Detalle_Caja = ?', [id]);
  if (!t) {
    return 'Corte no encontrado';
  }
  const ventas = db.todos('SELECT SUM(Pago - Cambio) AS Total, Tipo_Pago FROM ventas WHERE FK_Detalles_Caja = ? GROUP BY Tipo_Pago', [id]);
  const movimientos = db.todos('SELECT Tipo, Monto, Fecha_Registro FROM movimientos_caja WHERE FK_Detalle_Caja = ? ORDER BY Fecha_Registro DESC', [id]);
  const usuarios = db.todos('SELECT SUM(Pago - Cambio) AS Total, FK_Usuario FROM ventas WHERE FK_Detalles_Caja = ? GROUP BY FK_Usuario', [id]);
  const clasificaciones = db.todos(`SELECT IFNULL(c.Nombre, 'Sin clasificación') AS Clasificacion, SUM(dv.Cantidad) AS Cantidad,
      SUM(dv.Total * ((v.Pago - v.Cambio) / NULLIF((SELECT SUM(dv2.Total) FROM detalles_ventas dv2 WHERE dv2.FK_Venta = v.ID_Venta), 0))) AS Total
    FROM detalles_ventas dv INNER JOIN ventas v ON dv.FK_Venta = v.ID_Venta
    LEFT JOIN productos p ON dv.FK_Producto = p.ID_Producto LEFT JOIN clasificaciones c ON p.FK_Clasificacion = c.ID_Clasificacion
    WHERE v.FK_Detalles_Caja = ? AND v.Estatus != 2 GROUP BY IFNULL(c.ID_Clasificacion, 0) ORDER BY Clasificacion`, [id]);

  const sinRegistros = (cols) => `<tr><td colspan="${cols}">No existen registros.</td></tr>`;
  let totalVentas = 0;
  let tablaDetalles = `<tr><td>Monto de apertura</td><td>$${numero(t.Monto_Abrir)}</td></tr>`;
  let tablaVentas = '';
  for (const v of ventas) {
    if (v.Total > 0) {
      if (v.Tipo_Pago === 'Efectivo' || v.Tipo_Pago === 'Crédito') {
        totalVentas += v.Total;
      }
      tablaVentas += `<tr><td>${html(v.Tipo_Pago)}${v.Tipo_Pago === 'Crédito' ? ' (Abono Efectivo)' : ''}</td><td>$${numero(v.Total)}</td></tr>`;
    }
  }
  if (ventas.length) {
    tablaDetalles += `<tr><td>Ventas Efectivo</td><td>$${numero(totalVentas)}</td></tr>`;
  } else {
    tablaVentas = sinRegistros(2);
  }

  let entradas = 0;
  let salidas = 0;
  let tablaMovimientos = '';
  for (const m of movimientos) {
    if (m.Tipo === 'Entrada') entradas += m.Monto; else salidas += m.Monto;
    tablaMovimientos += `<tr><td>${fechaHumana(m.Fecha_Registro)}</td><td>${html(m.Tipo)}</td><td>${numero(m.Monto)}</td></tr>`;
  }
  if (movimientos.length) {
    tablaDetalles += `<tr><td>Entradas</td><td>$${numero(entradas)}</td></tr><tr><td>Salidas</td><td>$${numero(salidas)}</td></tr>`;
  } else {
    tablaMovimientos = sinRegistros(3);
  }

  const tablaUsuarios = usuarios.length
    ? usuarios.map((u) => `<tr><td>${html(nombreUsuario(u.FK_Usuario))}</td><td>$${numero(u.Total)}</td></tr>`).join('')
    : sinRegistros(2);

  let cantClas = 0;
  let dineroClas = 0;
  const tablaClas = clasificaciones.length ? clasificaciones.map((c) => {
    cantClas += Number(c.Cantidad) || 0;
    dineroClas += Number(c.Total) || 0;
    return `<tr><td>${html(c.Clasificacion)}</td><td>${numero(c.Cantidad)}</td><td>$${numero(c.Total)}</td></tr>`;
  }).join('') : sinRegistros(3);

  // Abonos a ventas a crédito registrados en este turno
  const pagos = db.todos(`SELECT v.Folio, p.Monto, p.Tipo_Pago, CASE WHEN v.FK_Cliente = 0 THEN 'Publico en General' ELSE IFNULL(c.Nombre, '') END AS Cliente
    FROM ventas_pagos p JOIN ventas v ON v.ID_Venta = p.FK_Venta LEFT JOIN clientes c ON c.ID_Cliente = v.FK_Cliente WHERE p.FK_Detalle_Caja = ?`, [id]);
  const totalPagos = pagos.reduce((a, x) => a + Number(x.Monto), 0);
  const tablaPagos = pagos.length
    ? pagos.map((x) => `<tr><td colspan="3">${html(x.Cliente)}</td></tr><tr><td>${html(x.Folio)}</td><td>$${numero(x.Monto)}</td><td>${html(x.Tipo_Pago)}</td></tr>`).join('')
    : sinRegistros(3);

  const balance = t.Monto_Abrir + totalVentas + entradas + totalPagos - salidas;
  const cerrado = t.Fecha_Cierre !== '0000-00-00 00:00:00';

  return `<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Ticket</title><style>${estilos('ticketCaja.php')}</style></head>
  <body>
    <button type="submit" class="oculto-impresion" onclick="imprimir()">Imprimir</button>
    <p class="domicilio">CORTE DE CAJA</p><br>
    <p class="fechas"><b>Fecha apertura:</b> ${fechaHumana(t.Fecha_Abrir)}</p>
    <p class="fechas"><b>Fecha cierre:</b> ${cerrado ? fechaHumana(t.Fecha_Cierre) : ''}</p><br>
    <p class="usuarios"><b>Usuario apertura:</b> ${html(nombreUsuario(t.FK_Usuario_Abrir))}</p>
    <p class="usuarios"><b>Usuario cierre:</b> ${html(nombreUsuario(t.FK_Usuario_Cierre))}</p><br>
    <h1>Balance: $${numero(balance)}</h1>
    <h2>Monto de cierre: $${numero(t.Monto_Cierre)}</h2>
    <h2>Restante: $${numero(t.Monto_Cierre - balance)}</h2>
    <div class="tablas">
      <table><caption>Detalles</caption><thead><tr><th>Tipo</th><th>Total</th></tr></thead><tbody>${tablaDetalles}</tbody></table>
      <table><caption>Ventas y pagos</caption><thead><tr><th>Tipo Pago</th><th>Total</th></tr></thead><tbody>${tablaVentas}</tbody></table>
      <table><caption>Ventas por usuario</caption><thead><tr><th>Tipo</th><th>Total</th></tr></thead><tbody>${tablaUsuarios}</tbody></table>
      <table><caption>Movimientos</caption><thead><tr><th>Fecha</th><th>Tipo</th><th>Monto</th></tr></thead><tbody>${tablaMovimientos}</tbody></table>
      <table><caption>Detalles Pagos Ventas</caption><thead><tr><th>Folio de Venta</th><th>Monto</th><th>Tipo Pago</th></tr></thead><tbody>${tablaPagos}</tbody>
        <tfoot><tr><th>Total</th><th>$${numero(totalPagos)}</th></tr></tfoot></table>
            <table><caption>Ventas por clasificación</caption><thead><tr><th>Clasificación</th><th>Cantidad</th><th>Total</th></tr></thead><tbody>${tablaClas}</tbody>
        <tfoot><tr><th>Total</th><th>${numero(cantClas)}</th><th>$${numero(dineroClas)}</th></tr></tfoot></table>
    </div>
  </body>
  <script>window.print(); function imprimir() { window.print(); }</script></html>`;
}

// ticketAbrir.php: solo abre el cajón de dinero (la impresora lo abre al recibir algo que imprimir)
function abrir(impresion) {
  return paraImprimir(`<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Ticket</title>
    <style>* { box-sizing: border-box; font-family: "Consolas", "Lucida Console", "Courier New", monospace; margin: 0; padding: 0; } body { display: flex; justify-content: center; align-items: center; flex-flow: column nowrap; padding: 0.5rem; max-width: 410px; width: 410px; }</style>
    </head><body onafterprint="funcionDespues()"><b>Abrir</b></body>
    <script>function funcionDespues() { setTimeout(function () { parent.document.getElementById("bAbrirCaja").click(); }, 300); }</script></html>`, impresion);
}

// Versión para la impresora de la app: sin los scripts de impresión de la web (que abren el diálogo),
// con la escala configurada y sin el espacio en blanco largo del final
function paraImprimir(pagina, impresion) {
  if (!impresion) {
    return pagina;
  }
  const extra = `<style>
    body { transform: none !important; zoom: ${impresion.escala / 100}; margin: 0 !important; }
    .oculto-impresion, button { display: none !important; }
    footer { margin-bottom: 30px !important; }
  </style>`;
  return pagina.replace(/<script[\s\S]*?<\/script>/g, '').replace(/ onafterprint="[^"]*"/, '').replace('</head>', extra + '</head>');
}

// ticketPago.php: comprobante de un abono
function pago(id, impresion) {
  const p = db.uno('SELECT * FROM ventas_pagos WHERE ID_Pago = ?', [id]);
  if (!p) {
    return 'Pago no encontrado';
  }
  const v = db.uno('SELECT * FROM ventas WHERE ID_Venta = ?', [p.FK_Venta]);
  const c = v && v.FK_Cliente !== 0 ? db.uno('SELECT * FROM clientes WHERE ID_Cliente = ?', [v.FK_Cliente]) : null;
  const abonado = Number(db.valor('SELECT IFNULL(SUM(Monto), 0) FROM ventas_pagos WHERE FK_Venta = ?', [p.FK_Venta]));
  const restante = v ? Math.max(0, v.Total - (v.Pago - v.Cambio) - abonado) : 0;
  const n = negocio();
  return paraImprimir(`<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Ticket</title><style>${estilos('ticketVenta.php')}</style></head><body>
    <button type="submit" class="oculto-impresion" onclick="window.print()">Imprimir</button>
    <img class="logo-negocio" src="${n.logo}" width="220px" onerror="this.src='../../vistas/assets/images/logos/logo.png'" />
    <p class="nombre-negocio">${html(n.nombre)}</p><p class="domicilio">${html(n.domicilio)}</p><p class="telefono">${html(n.telefono)}</p>
    <div class="linea-punteada"></div>
    <p class="folio-grande">Comprobante de pago</p>
    <p class="fecha"><span>Fecha:</span> <span>${fechaHumana(p.Fecha_Registro)}</span></p>
    <div class="linea-punteada"></div>
    <div class="cliente-info"><p><strong>Cliente:</strong> ${html(c ? `${c.Nombre} ${c.Primer_Apellido || ''}` : 'Publico en General')}</p>
      <p><strong>Venta:</strong> ${html(v ? v.Folio : '')}</p></div>
    <div class="pago">
      <p><span>Concepto:</span> <span>${html(p.Concepto)}</span></p>
      <p><span>Tipo de pago:</span> <span>${html(p.Tipo_Pago)}</span></p>
      <p class="total-final"><span>Abono:</span> <span>$${numero(p.Monto)}</span></p>
      <p><span>Restante:</span> <span>$${numero(restante)}</span></p>
    </div>
    <footer><p class="vendedor">Atendió: ${html(nombreUsuario(p.FK_Usuario))}</p><p class="gracias">¡Gracias!</p></footer>
    </body><script>window.print();</script></html>`, impresion);
}

// Ticket de muestra para la configuración de impresora
function prueba(impresion) {
  const n = negocio();
  const filas = [['Producto de ejemplo', 2, 25.5], ['Otro producto con nombre largo', 1, 120]]
    .map(([d, c, p]) => `<tr><td colspan="5" style="text-align: left;">${d}</td></tr><tr><td>${c}</td><td>$${numero(p)}</td><td>$0.00</td><td>Sub: $${numero(c * p)}</td><td>$${numero(c * p)}</td></tr>`).join('');
  const total = 2 * 25.5 + 120;
  return paraImprimir(`<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Ticket</title><style>${estilos('ticketVenta.php')}</style></head><body>
    <img class="logo-negocio" src="${n.logo}" width="220px" onerror="this.src='../../vistas/assets/images/logos/logo.png'" />
    <p class="nombre-negocio">${html(n.nombre)}</p><p class="domicilio">${html(n.domicilio)}</p><p class="telefono">${html(n.telefono)}</p>
    <div class="linea-punteada"></div>
    <p class="folio-grande">Ticket de prueba</p>
    <div class="linea-punteada"></div>
    <table class="tablaDetalles"><thead><tr><th>Cant.</th><th>Precio</th><th>Desc.</th><th>Impto.</th><th>Total</th></tr></thead><tbody>${filas}</tbody></table>
    <div class="pago"><p class="total-final"><span>Total:</span> <span>$${numero(total)}</span></p></div>
    <footer><p class="gracias">¡Gracias por su compra!</p><p class="text-center m-0">www.bigtool.mx</p></footer>
    </body></html>`, impresion);
}

module.exports = { iniciar, venta, corte, abrir, prueba, pago };
