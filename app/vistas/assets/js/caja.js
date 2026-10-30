const audio1 = new Audio('vistas/assets/media/sounds/addBip.mp3');
const audio2 = new Audio('vistas/assets/media/sounds/notBip.mp3');
let tipoDescuento = 0;
let procesandoCobro = false;

function esDispositivoIOS() {
  var ua = navigator.userAgent || '';
  if (/iPad|iPhone|iPod/.test(ua)) return true;
  return navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1;
}

function imprimirTicketCaja(src) {
  if (esDispositivoIOS()) return;
  $("#printer").html('<iframe id="iframePrint" src="' + src + '" onload="document.getElementById(\'iframePrint\').contentWindow.print()"></iframe>');
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//>>>>>>>>>>>>>>Núcleo de cálculo de filas de caja>>>>>>>>>
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
// Estas funciones centralizan el cálculo de precio/descuento/impuestos/total
// de cada fila de #tablaCaja (incluyendo promociones), para que TODOS los
// puntos donde se agrega, incrementa, resta o modifica un producto usen la
// misma lógica y no se desincronicen entre sí.

// Normaliza el arreglo de impuestos que llega de "agregarProducto" (escaneo/IntVarios)
function mapearImpuestosAgregarProducto(impuestos) {
  return (impuestos || []).map(function (imp) {
    return {
      id: imp.FK_Impuesto,
      nombre: imp.Nombre,
      valor: parseFloat(imp.Porcentaje) || 0,
      factor: imp.Tipo_Factor,
      clase: imp.Clase,
      clave: imp.Clave_CFDI
    };
  });
}

// Normaliza el arreglo de impuestos embebido en las tarjetas del grid táctil
function mapearImpuestosTouch(impuestos) {
  return (impuestos || []).map(function (imp) {
    return {
      id: imp.FK_Impuesto,
      nombre: imp.Nombre,
      valor: parseFloat(imp.Porcentaje) || 0,
      factor: imp.Tipo_Factor,
      clase: imp.Clase,
      clave: imp.Clave
    };
  });
}

// Calcula el subtotal de una fila (precio de lista x cantidad) aplicando, si
// corresponde, la promoción del producto/clasificación. No conoce nada de
// descuento manual ni mayoreo, eso lo resuelve recalcularFilaProducto().
function calcularPromocion(promocion, precioBase, cantidad) {
  precioBase = parseFloat(precioBase) || 0;
  cantidad = parseFloat(cantidad) || 0;

  var sinPromocion = { aplica: false, subtotal: precioBase * cantidad, etiqueta: '' };

  if (!promocion || !promocion.Cantidad || parseFloat(promocion.Cantidad) <= 0) {
    return sinPromocion;
  }

  var N = parseFloat(promocion.Cantidad);
  var esPaquete = promocion.Modo_Cantidad === 'Paquete';

  if (cantidad < N) {
    return sinPromocion;
  }

  function subtotalGrupo(cantidadGrupo) {
    var normal = precioBase * cantidadGrupo;
    var conBeneficio;

    if (promocion.Tipo_Beneficio === 'Precio_Fijo') {
      // En modo Paquete, Precio_Fijo es el TOTAL del paquete completo.
      // En modo Mínima (sin paquete), Precio_Fijo es el precio por pieza.
      conBeneficio = esPaquete ? parseFloat(promocion.Precio_Fijo) : (parseFloat(promocion.Precio_Fijo) * cantidadGrupo);
    } else if (promocion.Tipo_Descuento === 'Porcentaje') {
      conBeneficio = normal * (1 - (parseFloat(promocion.Descuento) / 100));
    } else {
      // Monto ($), siempre por pieza
      conBeneficio = normal - (parseFloat(promocion.Descuento) * cantidadGrupo);
    }

    return Math.max(0, conBeneficio);
  }

  var subtotal;
  if (esPaquete) {
    var paquetes = Math.floor(cantidad / N);
    var resto = cantidad - (paquetes * N);
    subtotal = (paquetes * subtotalGrupo(N)) + (resto * precioBase);
  } else {
    subtotal = subtotalGrupo(cantidad);
  }

  return { aplica: true, subtotal: subtotal, etiqueta: promocion.Nombre || 'Promoción' };
}

// Calcula el desglose de impuestos sobre un subtotal ya definido (con o sin
// promoción/descuento/mayoreo aplicado). Reutiliza el mismo formato de <p>
// que ya se usaba en todos los bloques originales para que el resto del
// código (mayoreo, descuento manual, edición manual de precio) lo pueda
// seguir leyendo del DOM sin cambios.
function calcularImpuestosSobreSubtotal(impuestos, subtotal, cantidad) {
  var totalImpuestos = 0;
  var html = '';

  (impuestos || []).forEach(function (impuesto) {
    var valor = parseFloat(impuesto.valor) || 0;
    var factor = (impuesto.factor || '').toString();
    var factorUpper = factor.toUpperCase();
    var clase = (impuesto.clase || '').toString();
    var claseUpper = clase.toUpperCase();
    var impuestoFila = 0;

    if (factorUpper === 'EXENTO') {
      impuestoFila = 0;
    } else if (factorUpper === 'CUOTA') {
      impuestoFila = cantidad * valor;
    } else {
      impuestoFila = subtotal * (valor / 100);
    }

    if (claseUpper === 'RETENCION' || claseUpper === 'RETENIDO') {
      totalImpuestos -= impuestoFila;
    } else {
      totalImpuestos += impuestoFila;
    }

    html += '<p class="m-0" attrID="' + impuesto.id + '" clave="' + (impuesto.clave || '') + '"><b>' + clase + '</b> <span>' + impuesto.nombre + '</span> <span class="valor ' + (factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + valor + '</span> (<span class="dinero">' + impuestoFila + '</span>) <b>' + factor + '</b></p>';
  });

  return { totalImpuestos: totalImpuestos, html: html };
}

// Quita una fila de #tablaCaja y deja activa la fila anterior (o la primera).
function eliminarFilaCaja(fila) {
  if (!fila || fila.length === 0) return;

  var indice = fila.index();
  fila.remove();

  var nuevoIndice = indice - 1 >= 0 ? indice - 1 : 0;
  $("#tablaCaja").children('tbody').children('tr').removeClass('activa');
  $("#tablaCaja").children('tbody').children('tr:eq(' + nuevoIndice + ')').addClass('activa');

  totalCaja();
}

// Recalcula por completo las columnas de precio/cantidad/descuento/impuestos/
// total de una fila ya existente en #tablaCaja, a partir de sus atributos
// (attrPrecioBase, attrPrecioMayoreo, attrUsaMayoreo, attrDescuentoManual,
// attrImpuestos, attrPromocion) y la nueva cantidad indicada. Si la
// promoción aplica, tiene PRIORIDAD sobre mayoreo y descuento manual.
function recalcularFilaProducto(fila, cantidad) {
  if (!fila || fila.length === 0) return;

  cantidad = parseFloat(cantidad) || 0;

  if (cantidad <= 0) {
    eliminarFilaCaja(fila);
    return;
  }

  var precioBase = parseFloat(fila.attr('attrPrecioBase')) || 0;
  var precioMayoreo = parseFloat(fila.attr('attrPrecioMayoreo')) || 0;
  var usaMayoreo = fila.attr('attrUsaMayoreo') === '1';
  var descuentoManual = parseFloat(fila.attr('attrDescuentoManual')) || 0;

  var impuestos = [];
  try { impuestos = JSON.parse(fila.attr('attrImpuestos') || '[]') || []; } catch (e) { impuestos = []; }

  var promocion = null;
  try { promocion = JSON.parse(fila.attr('attrPromocion') || 'null'); } catch (e) { promocion = null; }

  var resultadoPromo = calcularPromocion(promocion, precioBase, cantidad);

  var precioUnitarioMostrado = precioBase;
  var subtotal = 0;
  var descuentoHtml = '<span class="porcentaje">0</span> (<span class="dinero">0</span>)';
  var promoBadge = '';

  if (resultadoPromo.aplica) {
    subtotal = resultadoPromo.subtotal;
    // Se muestra (y se guarda) el precio PROMEDIO efectivo por pieza, ya
    // repartiendo el beneficio de la promoción entre todas las piezas de la
    // fila (incluyendo las que no alcanzaron a completar el paquete). Así
    // el ticket y detalles_ventas reflejan lo que realmente costó cada una,
    // no el precio de lista.
    precioUnitarioMostrado = cantidad > 0 ? (subtotal / cantidad) : precioBase;
    promoBadge = ' <span class="badge bg-success promo-badge" title="Promoción aplicada: ' + resultadoPromo.etiqueta + ' (precio de lista: $' + precioBase + ')"><i class="fas fa-tags"></i></span>';
  } else {
    var precioUnitario = (usaMayoreo && precioMayoreo > 0) ? precioMayoreo : precioBase;
    precioUnitarioMostrado = precioUnitario;

    var totalSinDescuento = precioUnitario * cantidad;
    var importeDescuento = totalSinDescuento * (descuentoManual / 100);
    subtotal = totalSinDescuento - importeDescuento;

    if (descuentoManual > 0) {
      descuentoHtml = '<span class="porcentaje">' + descuentoManual + '</span> (<span class="dinero">' + importeDescuento + '</span>)';
    }
  }

  var resImp = calcularImpuestosSobreSubtotal(impuestos, subtotal, cantidad);
  var total = subtotal + resImp.totalImpuestos;

  fila.attr('attrTienePromocion', resultadoPromo.aplica ? '1' : '0');

  fila.children('td:eq(2)').html('<span class="dinero" attrMayoreo="' + precioMayoreo + '">' + precioUnitarioMostrado + '</span>' + promoBadge);
  fila.children('td:eq(3)').html('<span class="cantidad">' + cantidad + '</span>');
  fila.children('td:eq(4)').html(descuentoHtml);
  fila.children('td:eq(5)').html('<b>SUB: <span class="dinero">' + subtotal + '</span></b><br>' + resImp.html);
  fila.children('td:eq(6)').html('<span class="dinero">' + total + '</span>');

  // Ajustar lotes en attrLotes si la cantidad fue reducida por debajo de la suma de lotes
  var lotesActualesFila = [];
  try { lotesActualesFila = JSON.parse(fila.attr('attrLotes') || '[]') || []; } catch (e) { lotesActualesFila = []; }
  if (lotesActualesFila.length > 0) {
    var sumaLotesFila = 0;
    lotesActualesFila.forEach(function (l) { sumaLotesFila += parseFloat(l.cantidad) || 0; });
    if (sumaLotesFila > cantidad) {
      var cantRestante = cantidad;
      var lotesAjustados = [];
      for (var la = 0; la < lotesActualesFila.length; la++) {
        var cLot = parseFloat(lotesActualesFila[la].cantidad) || 0;
        if (cantRestante <= 0) break;
        var tomar = Math.min(cLot, cantRestante);
        if (tomar > 0) {
          lotesActualesFila[la].cantidad = tomar;
          lotesAjustados.push(lotesActualesFila[la]);
          cantRestante -= tomar;
        }
      }
      fila.attr('attrLotes', JSON.stringify(lotesAjustados));
    }
  }
  actualizarBadgesLotesCaja(fila);

  totalCaja();
}

// Actualiza los badges visuales de lotes asignados en la celda de descripcion
function actualizarBadgesLotesCaja(fila) {
  if (!fila || fila.length === 0) return;
  var lotes = [];
  try { lotes = JSON.parse(fila.attr('attrLotes') || '[]') || []; } catch (e) { lotes = []; }
  var cont = fila.find('.contenedor-lotes-caja');
  if (cont.length === 0) {
    fila.children('td:eq(1)').append('<div class="contenedor-lotes-caja"></div>');
    cont = fila.find('.contenedor-lotes-caja');
  }
  cont.empty();
  if (lotes.length > 0) {
    var codProdFila = fila.children('td:eq(0)').text().trim();
    lotes.forEach(function (l) {
      var codScan = (codProdFila && l.idLote) ? (' [' + codProdFila + '_' + l.idLote + ']') : '';
      var txt = (l.nombre ? l.nombre : ('Lote #' + l.idLote)) + codScan + ' (' + l.cantidad + ')';
      cont.append('<span class="badge bg-info text-dark me-1" style="font-size: 10px;" title="Caducidad: ' + (l.caducidad || 'N/A') + '"><i class="fas fa-box"></i> ' + txt + '</span>');
    });
  }
}

// Suma/resta "delta" a la cantidad de una fila y recalcula. Si la cantidad
// resultante es <= 0, la fila se elimina (mismo comportamiento que siempre
// tuvo el botón de quitar producto).
function cambiarCantidadFila(fila, delta) {
  if (!fila || fila.length === 0) return;

  var cantidadActual = parseFloat(fila.children('td:eq(3)').children('span.cantidad').text().replace(',', '')) || 0;
  recalcularFilaProducto(fila, cantidadActual + delta);
}

// Verifica si una fila tiene promoción activa dada su cantidad actual.
function filaTienePromocionActiva(fila) {
  if (!fila || fila.length === 0) return false;

  var precioBase = parseFloat(fila.attr('attrPrecioBase')) || 0;
  var cantidadActual = parseFloat(fila.children('td:eq(3)').children('span.cantidad').text().replace(',', '')) || 0;

  var promocion = null;
  try { promocion = JSON.parse(fila.attr('attrPromocion') || 'null'); } catch (e) { promocion = null; }

  return calcularPromocion(promocion, precioBase, cantidadActual).aplica;
}

// Agrega un producto a #tablaCaja (o incrementa su cantidad si ya está en la
// mesa). "datos" = {id, codigo, descripcion, precio, precioMayoreo,
// existencia, impuestos (ya normalizados), promocion, cantidad, lote}
function crearFilaProducto(datos) {
  $("#tablaCaja").children('tbody').children('tr').removeClass('activa');

  var claseOculta = localStorage.getItem("cajaTouch") === "true" ? 'oculto' : '';
  var filaExistente = $("#tablaCaja").children('tbody').children('tr[attrID="' + datos.id + '"]');

  if (filaExistente.length > 0) {
    var cantidadActual = parseFloat(filaExistente.children('td:eq(3)').children('span.cantidad').text().replace(',', '')) || 0;
    filaExistente.addClass('activa');

    var lotesActuales = [];
    try { lotesActuales = JSON.parse(filaExistente.attr('attrLotes') || '[]') || []; } catch (e) { lotesActuales = []; }

    if (datos.lote && datos.lote.idLote) {
      var cantLoteInc = parseFloat(datos.cantidad) || 1;
      var enc = false;
      for (var kl = 0; kl < lotesActuales.length; kl++) {
        if (lotesActuales[kl].idLote == datos.lote.idLote) {
          lotesActuales[kl].cantidad = (parseFloat(lotesActuales[kl].cantidad) || 0) + cantLoteInc;
          enc = true;
          break;
        }
      }
      if (!enc) {
        lotesActuales.push({
          idLote: datos.lote.idLote,
          nombre: datos.lote.nombre || '',
          caducidad: datos.lote.caducidad || '',
          cantidad: cantLoteInc
        });
      }
      filaExistente.attr('attrLotes', JSON.stringify(lotesActuales));
    }

    recalcularFilaProducto(filaExistente, cantidadActual + (parseFloat(datos.cantidad) || 0));
  } else {
    var nuevaFila = $('<tr attrID="' + datos.id + '" class="activa"></tr>');
    nuevaFila.attr('attrPrecioBase', parseFloat(datos.precio) || 0);
    nuevaFila.attr('attrPrecioMayoreo', parseFloat(datos.precioMayoreo) || 0);
    nuevaFila.attr('attrUsaMayoreo', '0');
    nuevaFila.attr('attrDescuentoManual', '0');
    nuevaFila.attr('attrExistencia', datos.existencia || 0);
    nuevaFila.attr('attrImpuestos', JSON.stringify(datos.impuestos || []));
    nuevaFila.attr('attrPromocion', JSON.stringify(datos.promocion || null));

    var lotesIniciales = [];
    if (datos.lote && datos.lote.idLote) {
      lotesIniciales.push({
        idLote: datos.lote.idLote,
        nombre: datos.lote.nombre || '',
        caducidad: datos.lote.caducidad || '',
        cantidad: parseFloat(datos.cantidad) || 1
      });
    }
    nuevaFila.attr('attrLotes', JSON.stringify(lotesIniciales));

    nuevaFila.html(
      '<td class="' + claseOculta + '">' + datos.codigo + '</td>' +
      '<td><span class="desc-prod-caja">' + datos.descripcion + '</span><div class="contenedor-lotes-caja"></div></td>' +
      '<td></td><td></td><td></td><td></td><td></td>' +
      '<td class="' + claseOculta + '"><span class="cantidad">' + (datos.existencia || 0) + '</span></td>'
    );

    $("#tablaCaja").children('tbody').prepend(nuevaFila);
    recalcularFilaProducto(nuevaFila, parseFloat(datos.cantidad) || 0);
  }

  audio1.play();
}

let cantidadGranel = IMask(
  document.getElementById('cantidadGranel'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let importeGranel = IMask(
  document.getElementById('importeGranel'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let cantidadIntVatios = IMask(
  document.getElementById('cantidadIntVatios'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let cantidadProdComun = IMask(
  document.getElementById('cantidadProdComun'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let precioProdComun = IMask(
  document.getElementById('precioProdComun'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let montoEntrada = IMask(
  document.getElementById('montoEntrada'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let montoSalida = IMask(
  document.getElementById('montoSalida'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let porcentajeDescuentoCaja = IMask(
  document.getElementById('porcentajeDescuentoCaja'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let cantidadDescuentoCaja = IMask(
  document.getElementById('cantidadDescuentoCaja'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let totalPagoCobrar = IMask(
  document.getElementById('totalPagoCobrar'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

let montoCorte = IMask(
  document.getElementById('montoCorte'),
  {
    mask: Number,
    scale: 2,             // Decimales permitidos
    signed: false,        // Desactiva el signo negativo (-)
    thousandsSeparator: ',',
    padFractionalZeros: false,
    normalizeZeros: true,
    radix: '.',
    min: 0
  }
);

function display() {
  const today = new Date()

  const padTo2Digits = (num) => num.toString().padStart(2, '0')

  const month = padTo2Digits(today.getMonth() + 1)
  const day = padTo2Digits(today.getDate())
  const year = today.getFullYear()

  const hours12 = today.getHours() % 12 || 12;
  const hour = padTo2Digits(hours12);
  const minute = padTo2Digits(today.getMinutes())
  const seconds = padTo2Digits(today.getSeconds())
  const period = today.getHours() >= 12 ? 'pm' : 'am'

  return `${hour}:${minute}:${seconds} ${period} - ${day}/${month}/${year}`
}

setInterval(function () {
  $("#fechaHoraCaja").html(display());
}, 1000);

function totalCaja() {
  var suma = 0, contador = 0;
  $("#tablaCaja").children('tbody').children('tr').each(function (index, el) {
    suma += parseFloat($(this).children('td:eq(6)').children('span.dinero').text().replace('$', '').replaceAll(',', ''));
    contador++;
  });

  $("#cantidadCajaProd").html(contador);
  $("#subtotalCaja").html(suma);

  var descuento = parseFloat($("#mosDesGeP").text().replace('%', '').replaceAll(',', ''));
  $("#mosDesGeD").text(suma * (descuento / 100));
  var total = suma - (suma * (descuento / 100));

  $("#totalCaja").html(total);

  moneda();
  agregarBotonesCantidad();
}

function agregarBotonesCantidad() {
  if (localStorage.getItem("cajaTouch") !== "true") {
    $(".botonesCahaTouch").remove();
    return;
  }

  $("#tablaCaja tbody tr").each(function () {
    $(this).css('position', 'relative');
    const td = $(this).children('td:eq(3)');

    if (td.children('button').length === 0) {
      const cantidad = td.children('span.cantidad').text();
      td.html(`<span class="cantidad">${cantidad}</span>`);

      $(this).append(`
        <button class="bRestarCajaTd botonesCahaTouch" style="
          position: absolute;
          left: -10px;
          top: 0;
          height: 100%;
          width: 25px;
          background-color: #dc3545;
          color: white;
          border: none;
          font-size: 24px;
          font-weight: bold;
          cursor: pointer;
          z-index: 1;
          opacity: 0.7;
          padding: 0;
        ">−</button>

        <button class="bSumarCajaTd botonesCahaTouch" style="
          position: absolute;
          right: -10px;
          top: 0;
          height: 100%;
          width: 25px;
          background-color: #28a745;
          color: white;
          border: none;
          font-size: 24px;
          font-weight: bold;
          cursor: pointer;
          z-index: 1;
          opacity: 0.7;
          padding: 0;
        ">+</button>
      `);
    }
  });
}

function agregarProdBusqueda(fila) {
  $("#carga").show();

  let existencia = parseFloat(fila.children('td:eq(6)').text().replace(searchRegExp, '')) || 0;
  let precio = parseFloat(fila.children('td:eq(3)').text().replace('$', '').replace(searchRegExp, '')) || 0;
  let mayoreo = parseFloat(fila.children('td:eq(4)').text().replace('$', '').replace(searchRegExp, '')) || 0;
  let impuestos = [];

  fila.children('td:eq(5)').children('p').each(function (index, el) {
    impuestos.push({
      id: $(this).attr('attrID'),
      nombre: $(this).children('b:eq(0)').text().trim(), // Nombre en el primer span
      valor: parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replaceAll(',', '')) || 0,
      clave: $(this).attr('clave'),
      factor: $(this).children('b:last').text().trim(), // Factor en el último b
      clase: $(this).children('span:first').text().trim()  // Clase en el primer b
    });
  });

  var promocionTexto = fila.children('td:eq(5)').children('span.promoData').text();
  var promocionBusqueda = null;
  try { promocionBusqueda = promocionTexto ? JSON.parse(promocionTexto) : null; } catch (e) { promocionBusqueda = null; }

  if (fila.children('td:eq(2)').text() === "Granel") {
    let verImpuestos = '';
    let totalImpuestos = 0;
    impuestos.forEach((impuesto) => {
      let impuestoFila = 0;
      // Lógica Tasa/Cuota (Granel usualmente es 1 unidad base en el modal)
      if (impuesto.factor.toUpperCase() === 'CUOTA') {
        impuestoFila = impuesto.valor;
      } else {
        impuestoFila = precio * (impuesto.valor / 100);
      }

      // Lógica Traslado/Retención
      if (impuesto.clase.toUpperCase() === 'RETENCION' || impuesto.clase.toUpperCase() === 'RETENIDO') {
        totalImpuestos -= impuestoFila;
      } else {
        totalImpuestos += impuestoFila;
      }

      verImpuestos += '<p class="m-0" attrID="' + impuesto.id + '" clave="' + impuesto.clave + '"><b>' + impuesto.clase + '</b> <span>' + impuesto.nombre + '</span> <span class="valor ' + (impuesto.factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.valor + '</span> (<span class="dinero">' + impuestoFila + '</span>) - <b>' + impuesto.factor + '</b></p>';
    });

    $("#datosGranel").attr('attrID', fila.attr('id'));
    $("#datosGranel").attr('attrCodigo', fila.children('td:eq(1)').text());
    $("#datosGranel").attr('attrExistencia', existencia);
    $("#datosGranel").attr('attrPrecio', precio);
    $("#datosGranel").attr('attrImpuestos', JSON.stringify(impuestos));
    $("#datosGranel").attr('attrPromocion', JSON.stringify(promocionBusqueda));
    $("#datosGranel").html(`
      <h4 class="text-center">` + fila.children('td:eq(0)').text() + `</h4>
      <h5 class="text-center">
        <b>Precio Unitario:</b> <span class="dinero">` + precio + `</span>
      </h5>
      <div class="text-center">
        ` + verImpuestos + `
      </div>
    `);
    
    importeGranel.value = '';
    importeGranel.value = (Math.round((precio + totalImpuestos) * 100) / 100).toString();

    $('#importeGranel').attr('attrMayoreo', mayoreo);
    $("#MGranel").modal('show');

    moneda();
  } else {
    crearFilaProducto({
      id: fila.attr('id'),
      codigo: fila.children('td:eq(1)').text(),
      descripcion: fila.children('td:eq(0)').text(),
      precio: precio,
      precioMayoreo: mayoreo,
      existencia: existencia,
      impuestos: impuestos,
      promocion: promocionBusqueda,
      cantidad: 1
    });
  }

  $("#MBuscarProd").modal('hide');
  $("#carga").hide();
}

function cerrarCaja(imprimir) {
  var data = "metodo=modificar&accion=caja&tipo=cerrar&idCaja=" + String($('#modalCaja').attr('attrCaja') ?? '').trim() 
  + "&monto=" + $("#montoCorte").val().replaceAll(',', '') + "&fecha_cierre=" + String($("#fechaCierre").attr('fecha') ?? '').trim();

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $("#carga").show();
    }
  })
    .done(function (res) {
      var separa = String(res ?? '').trim().split('~');

      if (separa[0] == 'Correcto') {
        if (imprimir == false) {
          Swal.fire({
            title: 'Correcto',
            text: 'El corte de caja se ha relizado correctamente.',
            icon: 'success',
          });
        } else {
          const altura = 50;
          const anchura = 310;
          const topY = parseInt((window.screen.height / 2) - (altura / 2));
          const topX = parseInt((window.screen.width / 2) - (anchura / 2));

          window.open(`controladores/pdf/ticketCaja.php?id=${separa[1]}`, '_blank', `width=${anchura}, height=${altura}, top=${topY}, left=${topX}`);
          window.location.reload();
        }
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Error inesperado al relizar el corte de caja.'
        });

        console.log(String(res ?? '').trim());
      }
    })
    .fail(function () {
      console.log("Error ajax");
    })
    .always(function () {
      $("#carga").hide();
    });
}

const modales = [
  "#modalCaja", "#MAbrirCaja", "#MIntVarios",
  "#MGranel", "#MProdComun", "#MBuscarProd",
  "#MEntrada", "#MSalida", '#MDescuento',
  '#MCambiarTicket', '#MPendiente', '#MCobrar',
  '#MCorteCaja', '#MVentasPendientes', '#MBuscarCliente',
  '#modalClienteBus'
];

var escanerCajaIniciando = false;
var aplicandoCodigoEscanerCaja = false;
var streamEscanerCaja = null;
var timerZXingEscanerCaja = null;
var escaneandoFrameZXing = false;
var zxingModuloPromesa = null;

function overlayEscanerCajaVisible() {
  return $('#overlayEscanerCaja').length && !$('#overlayEscanerCaja').hasClass('d-none');
}

function elementoFullscreenCaja() {
  return document.fullscreenElement
    || document.webkitFullscreenElement
    || document.mozFullScreenElement
    || document.msFullscreenElement
    || null;
}

var ignorarSalidaFullscreenCaja = false;

// Sale de pantalla completa cuando la caja no se llego a abrir (sin caja asignada o error),
// sin disparar el cierre/recarga del handler de fullscreenchange.
function salirPantallaCompletaSinCerrarCaja() {
  $(document).off('.pantallaCaja');
  if (!elementoFullscreenCaja() || !document.exitFullscreen) {
    return;
  }
  // Chrome dispara webkitfullscreenchange y fullscreenchange, la bandera se limpia despues de ambos
  ignorarSalidaFullscreenCaja = true;
  document.exitFullscreen().catch(function () {}).finally(function () {
    setTimeout(function () { ignorarSalidaFullscreenCaja = false; }, 300);
  });
}

// Igual que antes: se pide pantalla completa al abrir la caja. Si el navegador la niega
// (por ejemplo despues del selector de puertos de la bascula), se reintenta en el siguiente click/tecla.
function entrarPantallaCompletaCaja() {
  if (elementoFullscreenCaja() || !document.documentElement.requestFullscreen) {
    return;
  }
  document.documentElement.requestFullscreen().catch(function (err) {
    console.log('No se pudo entrar a pantalla completa, se reintenta al siguiente click: ', err);
    $(document).off('.pantallaCaja').one('pointerdown.pantallaCaja keydown.pantallaCaja', function () {
      $(document).off('.pantallaCaja');
      if ($('#modalCaja').is(':visible') && !elementoFullscreenCaja()) {
        document.documentElement.requestFullscreen().catch(function () {});
      }
    });
  });
}

// Carga el módulo WASM completo antes de empezar a escanear (evita perder los primeros
// segundos mientras se descarga/compila el .wasm de ~1MB).
function prepararZXingCaja() {
  if (!window.ZXingWASM) {
    return Promise.reject(new Error('No se pudo cargar el lector de códigos (ZXingWASM no está disponible).'));
  }
  if (!zxingModuloPromesa) {
    zxingModuloPromesa = ZXingWASM.prepareZXingModule({
      overrides: {
        locateFile: function (path) {
          return 'vistas/assets/plugins/zxing-wasm/' + path;
        }
      },
      fireImmediately: true
    }).catch(function (err) {
      zxingModuloPromesa = null;
      throw err;
    });
  }
  return zxingModuloPromesa;
}

function mensajeErrorEscanerCaja(err) {
  var nombre = (err && err.name) ? String(err.name) : '';
  var detalle = String((err && err.message) || err || '');
  var inseguro = (window.isSecureContext === false);

  if (nombre === 'NotFoundError' || /Requested device not found/i.test(detalle)) {
    return 'No se encontró una cámara en este equipo.';
  }
  if (nombre === 'NotReadableError' || /Could not start video/i.test(detalle)) {
    return 'La cámara está ocupada por otra aplicación. Ciérrala y vuelve a intentar.';
  }
  if (inseguro) {
    return 'En el celular el navegador no pide permiso si entras por HTTP. Tiene que ser HTTPS; con http:// y la IP de la red bloquea la cámara sin preguntar.';
  }
  if (nombre === 'NotAllowedError' || /NotAllowedError|Permission/i.test(nombre + ' ' + detalle)) {
    return 'La cámara está bloqueada para este sitio. En Ajustes del navegador permite la cámara para este sitio y recarga la página.';
  }
  return 'No se pudo abrir la cámara. Permite el acceso e inténtalo de nuevo.';
}

function detenerTracksEscanerCaja() {
  if (!streamEscanerCaja) return;
  streamEscanerCaja.getTracks().forEach(function (track) {
    track.stop();
  });
  streamEscanerCaja = null;
}

function detenerEscanerCaja() {
  $('#overlayEscanerCaja').addClass('d-none').attr('aria-hidden', 'true');
  escanerCajaIniciando = false;
  escaneandoFrameZXing = false;

  if (timerZXingEscanerCaja) {
    clearInterval(timerZXingEscanerCaja);
    timerZXingEscanerCaja = null;
  }

  var video = document.getElementById('videoEscanerCaja');
  if (video) {
    video.pause();
    video.srcObject = null;
  }
  detenerTracksEscanerCaja();
  $('.vt-escaner-caja-hint').text('Apunta la cámara al código QR o de barras');
}

function aplicarCodigoEscanerCaja(codigo) {
  codigo = String(String(codigo || '') ?? '').trim();
  if (!codigo || aplicandoCodigoEscanerCaja) return;

  aplicandoCodigoEscanerCaja = true;
  detenerEscanerCaja();
  $('#barCodeV').val(codigo);
  $('#formAgreProd').trigger('submit');
  setTimeout(function () {
    aplicandoCodigoEscanerCaja = false;
  }, 500);
}

function intentarEnfoqueContinuoCaja() {
  if (!streamEscanerCaja) return;
  var track = streamEscanerCaja.getVideoTracks()[0];
  if (!track || typeof track.getCapabilities !== 'function') return;

  try {
    var capacidades = track.getCapabilities();
    if (capacidades && capacidades.focusMode && capacidades.focusMode.indexOf('continuous') !== -1) {
      track.applyConstraints({ advanced: [{ focusMode: 'continuous' }] }).catch(function () {});
    }
  } catch (e) {}
}

// Decodifica con el motor real de ZXing (C++ compilado a WebAssembly). Es notablemente más
// tolerante que los lectores en JS puro con imágenes borrosas o de poca luz, como las de
// muchas webcams de PC.
function escanearFrameZXingCaja() {
  if (escaneandoFrameZXing || aplicandoCodigoEscanerCaja || !window.ZXingWASM) return;

  var video = document.getElementById('videoEscanerCaja');
  var canvas = document.getElementById('canvasEscanerCaja');
  if (!video || !canvas || video.readyState < 2 || !video.videoWidth) return;

  escaneandoFrameZXing = true;

  var ancho = video.videoWidth;
  var alto = video.videoHeight;
  var maxAncho = 1000;
  var escala = ancho > maxAncho ? maxAncho / ancho : 1;
  canvas.width = Math.round(ancho * escala);
  canvas.height = Math.round(alto * escala);

  var ctx = canvas.getContext('2d', { willReadFrequently: true });
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

  var imageData;
  try {
    imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
  } catch (e) {
    escaneandoFrameZXing = false;
    return;
  }

  ZXingWASM.readBarcodesFromImageData(imageData, {
    tryHarder: true,
    tryRotate: true,
    tryInvert: true,
    tryDownscale: true,
    maxNumberOfSymbols: 1,
    formats: []
  }).then(function (resultados) {
    var valido = resultados && resultados.find(function (r) { return r.isValid && r.text; });
    if (valido) {
      aplicarCodigoEscanerCaja(valido.text);
    }
  }).catch(function (err) {
    console.error('Error ZXing al leer el código:', err);
  }).then(function () {
    escaneandoFrameZXing = false;
  });
}

function iniciarEscanerCaja() {
  if (overlayEscanerCajaVisible() || escanerCajaIniciando) return;

  if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
    Swal.fire({ icon: 'error', title: 'Cámara', text: 'Este navegador no permite usar la cámara.' });
    return;
  }

  escanerCajaIniciando = true;
  $('#overlayEscanerCaja').removeClass('d-none').attr('aria-hidden', 'false');
  $('.vt-escaner-caja-hint').text('Cargando lector de códigos...');

  // Una sola petición de cámara para toda la sesión de escaneo. En paralelo se termina
  // de cargar/compilar el módulo WASM del lector, así el primer frame no se pierde.
  Promise.all([
    navigator.mediaDevices.getUserMedia({
      audio: false,
      video: {
        facingMode: 'environment',
        width: { min: 640, ideal: 1280, max: 1920 },
        height: { min: 480, ideal: 720, max: 1080 }
      }
    }).catch(function () {
      return navigator.mediaDevices.getUserMedia({ audio: false, video: true });
    }),
    prepararZXingCaja()
  ]).then(function (resultados) {
    var stream = resultados[0];
    streamEscanerCaja = stream;
    var video = document.getElementById('videoEscanerCaja');
    if (!video) throw new Error('No se encontró el visor de cámara.');
    video.setAttribute('playsinline', 'true');
    video.setAttribute('webkit-playsinline', 'true');
    video.muted = true;
    video.srcObject = stream;

    var play = video.play();
    if (play && typeof play.catch === 'function') {
      play.catch(function () {});
    }

    intentarEnfoqueContinuoCaja();
    $('.vt-escaner-caja-hint').text('Apunta la cámara al código QR o de barras');
    timerZXingEscanerCaja = setInterval(escanearFrameZXingCaja, 220);
    escanerCajaIniciando = false;
  }).catch(function (err) {
    console.error('Error al iniciar el escáner:', err);
    escanerCajaIniciando = false;
    detenerEscanerCaja();
    Swal.fire({
      icon: 'error',
      title: 'Cámara',
      text: mensajeErrorEscanerCaja(err)
    });
  });
}

function tablaBuscarProductos() {
  ajaxMyDatatable({
    "table": $("#tablaBuscarProductos"),
    "colums": [
      "Descripcion",
      "Codigo",
      "Clase",
      "Precio",
      "Precio_Mayoreo",
      "Impuestos",
      "Existencia"
    ],
    "sort": [
      0,
      "asc"
    ],
    "url": "index.php"
    ,
    "params": {
      metodo: 'consultar',
      accion: 'caja',
      tipo: 'productos',
    }
  });
}

function tablaVentasPendientes() {
  ajaxMyDatatable({
    "table": $("#tablaVentasPendientes"),
    "colums": [
      "Fecha",
      "Folio",
      "Tipo",
      "Total",
      "Pago",
      "Cambio",
      "Detalles",
      "Acciones"
    ],
    "sort": [
      0,
      "desc"
    ],
    "url": "index.php"
    ,
    "params": {
      metodo: 'detalles',
      accion: 'caja',
      tipo: 'ventas',
    }
  });
}

function tablaBuscarClientes() {
  ajaxMyDatatable({
    "table": $("#tablaBuscarClientes"),
    "colums": [
      "Nombre",
      "Tipo",
      "Contacto"
    ],
    "sort": [0, "asc"],
    "url": "index.php"
    ,
    "params": {
      metodo: 'consultar',
      accion: 'caja',
      tipo: 'clientes',
    }
  });
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//>>>>>>>>>>Cambio Caja >>>>>>>>>
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

function cambiarCaja() {
  if (localStorage.getItem("cajaTouch") === "true") {
    $("#bCambiarTouch").prop('checked', true);

    $("#formAgreProd").addClass('oculto');
    $("#tituloCaja").css("position", "fixed");
    $("#tituloCaja").css("margin-top", "-31px");

    $("#bBuscarProd").addClass('oculto');
    $("#bMayoreo").addClass('oculto');

    $("#mostrarTablaProdCaja").css("height", "51vh");
    $("#mosTiketsCaja").removeClass("col-12");
    $("#mosTiketsCaja").addClass("col-md-6");

    $("#mosTiketsCaja").parent().prepend(`<div class="col-md-6" style="position: relative;">
      <div class="row" id="mosProdCajaTienda" style="padding: 30px 15px; background-color: #EEE; height: 56vh; overflow-x: hidden; overflow-y: auto;">

      </div>
      <button type="button" class="btn btn-dark btn-lg" id="bRegresarTienda" style="position: absolute; right: 0; bottom: -30px; z-index: 2;"><i class="fa-solid fa-backward"></i></button>
    </div>`);

    $("#tablaCaja thead tr th:eq(0)").addClass('oculto');
    $("#tablaCaja thead tr th:eq(6)").addClass('oculto');

    $("#mosClienteCobrarCaja").addClass('oculto');
    $("#mosBotonesCajaUP").children("div:eq(0)").children("div:eq(0)").append(`<button type="button" class="btn btn-outline-secondary" id="mosNumTicket">Num. 1</button>`);

    if (localStorage.getItem("numTicket") == null) {
      localStorage.setItem("numTicket", 1);
    } else {
      var numTicket = localStorage.getItem("numTicket");
      $("#mosNumTicket").html("Turno: " + numTicket);
    }

    // Ocultar columnas de filas existentes
    $("#tablaCaja tbody tr").each(function () {
      $(this).children('td:eq(0)').addClass('oculto');
      $(this).children('td:eq(7)').addClass('oculto');
    });

    verClaTienda();
  } else {
    $("#bCambiarTouch").prop('checked', false);

    $("#formAgreProd").removeClass('oculto');
    $("#tituloCaja").css("position", "relative");
    $("#tituloCaja").css("margin-top", "auto");

    $("#bBuscarProd").removeClass('oculto');
    $("#bMayoreo").removeClass('oculto');

    $("#mostrarTablaProdCaja").css("height", "40vh");
    $("#mosTiketsCaja").removeClass("col-md-6");
    $("#mosTiketsCaja").addClass("col-12");

    $("#mosProdCajaTienda").parent().remove();
    $("#bRegresarTienda").remove();

    $("#tablaCaja thead tr th:eq(0)").removeClass('oculto');
    $("#tablaCaja thead tr th:eq(6)").removeClass('oculto');

    $("#mosClienteCobrarCaja").removeClass('oculto');
    $("#mosNumTicket").remove();

    $("#tablaCaja").children('tbody').children('tr').children('td.oculto').removeClass('oculto');
  }

  agregarBotonesCantidad();
}

function verProdTienda(clasificacion) {
  var data = "metodo=detalles&accion=caja&tipo=prodTienda&idCaja=" + String($('#modalCaja').attr('attrCaja') ?? '').trim() + "&clasificacion=" + clasificacion;

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $("#carga").show();
    }
  })
    .done(function (res) {
      //console.log(String(res ?? '').trim());

      $("#mosProdCajaTienda").html(String(res ?? '').trim());
      moneda();
    })
    .fail(function () {
      console.log("Error ajax");
    }).always(function () {
      $("#carga").hide();
    });
}

function verClaTienda() {
  var data = "metodo=detalles&accion=caja&tipo=clasificaciones";

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $("#carga").show();
    }
  })
    .done(function (res) {
      //console.log(String(res ?? '').trim());

      $("#mosProdCajaTienda").html(String(res ?? '').trim());
    })
    .fail(function () {
      console.log("Error ajax");
    }).always(function () {
      $("#carga").hide();
    });
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

function domicilios(id) {
  $("#seleccionDomicilioBus").removeClass('oculto');
  var data = "metodo=detalles&accion=caja&tipo=domicilios&id=" + id;

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data
  })
    .done(function (res) {
      //console.log(String(res ?? '').trim());
      $('#domicilioClienteBus').html(String(res ?? '').trim());
    })
    .fail(function () {
      console.log("Error ajax");
    });
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$(document).on('keydown', function (evt) {
  evt ??= window.event
  //console.log("Key: " + evt.key + " Code: " + evt.keyCode)

  if (evt.key === "F1") {
    evt.preventDefault();
  }
  if (!$("#modalCaja").is(':visible')) return
  // Mientras se edita precio/cantidad en la tabla, que +, -, Supr y flechas no muevan otros renglones
  if ($(evt.target).is('#precioProdCaja, #cantidadProdCaja')) return

  if (overlayEscanerCajaVisible()) {
    if (evt.key === "Escape") {
      evt.preventDefault();
      detenerEscanerCaja();
    }
    return;
  }

  if ($(".buscadorMyDataTable[tabla=tablaBuscarProductos]").is(":focus")) {
    var fila = $("#tablaBuscarProductos").children('tbody').children('tr.activa').index();

    if ($("#tablaBuscarProductos").children('tbody').children('tr').length > 1) {
      if (evt.key === "ArrowUp" && (fila - 1) >= 0) {
        $("#tablaBuscarProductos").children('tbody').children('tr').removeClass('activa');
        $("#tablaBuscarProductos").children('tbody').children(`tr:eq(${(fila - 1)})`).addClass('activa');
      } else if (evt.key === "ArrowDown") {
        if ($("#tablaBuscarProductos").children('tbody').children(`tr:eq(${(fila + 1)})`).length > 0) {
          $("#tablaBuscarProductos").children('tbody').children('tr').removeClass('activa');
          $("#tablaBuscarProductos").children('tbody').children(`tr:eq(${(fila + 1)})`).addClass('activa');
        }
      }

      $("#tablaBuscarProductos").children('tbody').children('tr.activa').get(0).scrollIntoView();
    }
  } else if ($(".buscadorMyDataTable[tabla=tablaBuscarClientes]").is(":focus")) {
    var fila = $("#tablaBuscarClientes").children('tbody').children('tr.activa').index();

    if ($("#tablaBuscarClientes").children('tbody').children('tr').length > 1) {
      if (evt.key === "ArrowUp" && (fila - 1) >= 0) {
        $("#tablaBuscarClientes").children('tbody').children('tr').removeClass('activa');
        $("#tablaBuscarClientes").children('tbody').children(`tr:eq(${(fila - 1)})`).addClass('activa');
      } else if (evt.key === "ArrowDown") {
        if ($("#tablaBuscarClientes").children('tbody').children(`tr:eq(${(fila + 1)})`).length > 0) {
          $("#tablaBuscarClientes").children('tbody').children('tr').removeClass('activa');
          $("#tablaBuscarClientes").children('tbody').children(`tr:eq(${(fila + 1)})`).addClass('activa');
        }
      }

      $("#tablaBuscarClientes").children('tbody').children('tr.activa').get(0).scrollIntoView();
    }
  } else if ($(".buscadorMyDataTable[tabla=tablaVentasPendientes]").is(":focus")) {
    var fila = $("#tablaVentasPendientes").children('tbody').children('tr.activa').index();

    if ($("#tablaVentasPendientes").children('tbody').children('tr').length > 1) {
      if (evt.key === "ArrowUp" && (fila - 1) >= 0) {
        $("#tablaVentasPendientes").children('tbody').children('tr').removeClass('activa');
        $("#tablaVentasPendientes").children('tbody').children(`tr:eq(${(fila - 1)})`).addClass('activa');
      } else if (evt.key === "ArrowDown") {
        if ($("#tablaVentasPendientes").children('tbody').children(`tr:eq(${(fila + 1)})`).length > 0) {
          $("#tablaVentasPendientes").children('tbody').children('tr').removeClass('activa');
          $("#tablaVentasPendientes").children('tbody').children(`tr:eq(${(fila + 1)})`).addClass('activa');
        }
      }

      $("#tablaVentasPendientes").children('tbody').children('tr.activa').get(0).scrollIntoView();
    }
  } else if ($("#barCodeV").is(":focus") || localStorage.getItem("cajaTouch") == "true") {
    var fila = $("#tablaCaja").children('tbody').children('tr.activa').index();

    if ($("#tablaCaja").children('tbody').children('tr').length > 1) {
      if (evt.key === "ArrowUp" && (fila - 1) >= 0) {
        $("#tablaCaja").children('tbody').children('tr').removeClass('activa');
        $("#tablaCaja").children('tbody').children(`tr:eq(${(fila - 1)})`).addClass('activa');
      }
      if (evt.key === "ArrowDown") {
        if ($("#tablaCaja").children('tbody').children(`tr:eq(${(fila + 1)})`).length > 0) {
          $("#tablaCaja").children('tbody').children('tr').removeClass('activa');
          $("#tablaCaja").children('tbody').children(`tr:eq(${(fila + 1)})`).addClass('activa');
        }
      }
    }

    if ($("#tablaCaja").children('tbody').children('tr').length > 0) {
      if (evt.key === "+") {
        cambiarCantidadFila($("#tablaCaja").children('tbody').children('tr.activa'), 1);
      } else if (evt.key === "-") {
        cambiarCantidadFila($("#tablaCaja").children('tbody').children('tr.activa'), -1);
      }
    }

    if (evt.key === "Tab") {
      event.preventDefault();
      $("#barCodeV").focus();
    }
  }

  /*if(evt.key == "Delete"){
    modales.forEach(modal => {
      if(modal != '#modalCaja') {
        $(modal).modal('hide');
      }
    });
  }else */if (evt.key === "F2") {
    if ($("#MCobrar").is(':visible')) {
      $("#MBuscarCliente").modal('show');
    } else {
      $('#bIntVarios').trigger('click');
    }
  } else if (evt.key === 'F1') {
    evt.preventDefault();
    if (procesandoCobro == false && $("#MCobrar").is(':visible')) {
      $('#bCobrarImprimir').trigger('click');
    }
  } else if (evt.key === 'F4') {
    evt.preventDefault();
    if (procesandoCobro == false && $("#MCobrar").is(':visible')) {
      $('#bSoloCobrar').trigger('click');
    }
  } else if (event.altKey && evt.key === "c") {
    $('#bProdComun').trigger('click');
  } else if (evt.key === "F10") {
    $('#bBuscarProd').trigger('click');
  } else if (evt.key === "F9" && $("#barCodeV").is(":focus")) {
    $('#bMayoreo').trigger('click')
  } else if (evt.key === "F7") {
    evt.preventDefault();
    $('#bEntrada').trigger('click');
  } else if (evt.key === "F8") {
    $('#bSalida').trigger('click');
  } else if (event.altKey && evt.key === "d") {
    $('#bHacerDescuento').trigger('click');
  } else if (evt.key === "Delete" && ($('#barCodeV').is(':focus') || localStorage.getItem("cajaTouch") == "true")) {
    $('#bQuitarProducto').trigger('click');
  } else if (evt.key === "F3") {
    evt.preventDefault();
    $('#bTicketPendiente').trigger('click');
  } else if (evt.key === "F6") {
    $('#bCambiarTicket').trigger('click')
  } else if (event.altKey && evt.key === "e") {
    $('#bEliminarTicket').trigger('click');
  } else if (event.altKey && evt.key === "a") {
    console.log("Asignar");
  } else if (evt.key === "F12") {
    evt.preventDefault();
    $('#bCobrar').trigger('click')
  } else if (event.altKey && evt.key === "u") {
    console.log("Ultimo ticket");
  } else if (event.altKey && evt.key === "v") {
    $("#bVentasPendientes").trigger('click');
  } else if (event.altKey && evt.key === "g") {
    $("#bDescuentoGeneral").trigger('click');
  } else if (evt.key === "Enter") {
    if ($('.buscadorMyDataTable[tabla=tablaBuscarProductos]').is(':focus')) {
      if ($('#tablaBuscarProductos tbody tr.activa').length > 0) {
        agregarProdBusqueda($('#tablaBuscarProductos tbody tr.activa'));
      }
    } else if ($('.buscadorMyDataTable[tabla=tablaBuscarClientes]').is(':focus')) {
      if ($('#tablaBuscarClientes tbody tr.activa').length > 0) {
        var fila = $('#tablaBuscarClientes tbody tr.activa');

        $("#clienteCobrar").val(fila.children('td:eq(0)').children('span:eq(0)').text());
        $("#clienteCobrar").attr('attrID', fila.attr('id'));

        $("#MBuscarCliente").modal('hide');
      }
    } else if ($('.buscadorMyDataTable[tabla=tablaVentasPendientes]').is(':focus')) {
      var fila = $("#tablaVentasPendientes").children('tbody').children('tr.activa');
      if (fila.length > 0) {
        $('#totalCobrar').text(parseFloat(fila.children('td:eq(3)').text().replace('$', '').replaceAll(',', '')));

        totalPagoCobrar.value = '';
        totalPagoCobrar.value = parseFloat(fila.children('td:eq(4)').text().replace('$', '').replaceAll(',', '')).toString();

        $('#totalCambio').text(fila.children('td:eq(5)').text());
        $('#detallesCobrar').val(String(fila.children('td:eq(6)').children('p:eq(0)').text() ?? '').trim());

        moneda();
        $("#bCobrarImprimir").attr('attrID', fila.attr('id'));
        $("#bCobrarImprimir").attr('tipo', 'pendiente');
        $('#MCobrar').modal('show');
      }
    }
  }
});

jQuery(document).ready(function ($) {

  document.getElementById("bAbrirCaja").addEventListener("click", function () {
    var data = "metodo=detalles&accion=caja&tipo=consultarCaja";

    // La pantalla completa se pide aqui, directo en el click y antes del ajax. Si se pide
    // hasta que responde el servidor, Chrome/Edge pueden negarla (sobre todo despues de
    // haber mostrado el selector de puertos de la bascula).
    entrarPantallaCompletaCaja();

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $("#carga").show();
      }
    })
      .done(function (res) {
        //console.log(res);
        if (String(res ?? '').trim() === "No") {
          salirPantallaCompletaSinCerrarCaja();
          $('#bMenuCajas').trigger('click');
        } else {
          const resJson = JSON.parse(String(res ?? '').trim());
          $('#tituloCaja').html(resJson.Nombre);
          $('#modalCaja').attr('attrSucursal', resJson.FK_Sucursal);
          $('#modalCaja').attr('attrCaja', resJson.ID_Caja);
          $('#bDejarCaja').attr('attrID', resJson.ID_Caja);
          $('#bCorteCaja').attr('attrID', resJson.ID_Caja);
          $('#modalCaja').modal('show');

          setTimeout(() => {
            $('#barCodeV').focus();
            tablaBuscarProductos();
            tablaBuscarClientes();
            $("#carga").hide();
          }, 1000);
        }
      })
      .fail(function () {
        console.log("Error ajax");
        salirPantallaCompletaSinCerrarCaja();
        $("#carga").hide();
      });
  });

  $(document).on('click', function () {
    if ($('#modalCaja').is(':visible')) {
      const [modalCaja, ...rest] = modales;

      if ($("#MBuscarProd").is(':visible')) {
        $(".buscadorMyDataTable[tabla='tablaBuscarProductos']").focus();
      } else if ($("#MBuscarCliente").is(':visible')) {
        $(".buscadorMyDataTable[tabla='tablaBuscarClientes']").focus();
      } else if (rest.every(modal => !$(modal).is(':visible'))) {
        $("#barCodeV").focus();
      }
    }
  });

  $(document).on('hidden.bs.modal', function ({ target }) {
    if ($(".swal2-container").is(':visible')) {
      $("button.swal2-confirm").focus();
    } else if (!$('.modal:not(#modalCaja)').is(':visible')) {
      $("#barCodeV").focus();
    }
  });

  $(document).on('shown.bs.modal', function ({ target }) {
    if ($("#modalCaja").is(':visible')) {
      $('.modal').each(function () {
        if ($(this).attr('id') != $(target).attr('id') && $(this).attr('id') !== 'modalCaja') {
          $(this).modal('hide');
        }
      });
    }
  });

  $(document).on('keypress', '#barCodeV', function (event) {
    var regex = new RegExp("^[a-zA-Z0-9_]+$");
    var key = event.key;
    if (!regex.test(key)) {
      event.preventDefault();
      return false;
    }
  });

  $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function () {
    if (overlayEscanerCajaVisible() || escanerCajaIniciando) {
      return;
    }

    if (ignorarSalidaFullscreenCaja) {
      return;
    }

    if (!elementoFullscreenCaja()) {
      detenerEscanerCaja();
      modales.forEach(modal => {
        $(modal).modal('hide');
      });

      swal.close();
      $('#bRecargar').trigger('click');
    }
  });

  $(document).on('click', '#bCerrarVenCaja', function () {
    detenerEscanerCaja();
    $("#modalCaja").modal('hide');
    document.exitFullscreen();
  });

  $(document).on('click', '#bEscanerCamaraCaja', function (event) {
    event.preventDefault();
    iniciarEscanerCaja();
  });

  $(document).on('click', '#bCerrarEscanerCaja', function (event) {
    event.preventDefault();
    detenerEscanerCaja();
    $("#barCodeV").focus();
  });

  $(document).on('click', '#overlayEscanerCaja', function (event) {
    if (event.target.id === 'overlayEscanerCaja') {
      detenerEscanerCaja();
      $("#barCodeV").focus();
    }
  });

  $(document).on('hidden.bs.modal', '#modalCaja', function () {
    detenerEscanerCaja();
  });

  $(document).on('submit', '#formAgreProd', function (event) {
    event.preventDefault();
    const data = `metodo=consultar&accion=caja&tipo=agregarProducto&codigo=${String($('#barCodeV').val() ?? '').trim()}`

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data,
      beforeSend: function () {
        $("#carga").show();
      },
    })
      .done(function (res) {
        //console.log(String(res ?? '').trim());
        $("#barCodeV").val("");

        if (String(res ?? '').trim() === "No encontrado") {
          $("#noEncontrado").show();

          setTimeout(function () {
            $("#noEncontrado").hide();
          }, 1000);

          audio2.play();
        } else {
          var resA = JSON.parse(String(res ?? '').trim());
          var precio = parseFloat(resA.Precio) || 0;
          var existencia = parseFloat(resA.Existencia) || 0;
          var mayoreo = parseFloat(resA.Precio_Mayoreo) || 0;
          let impuestos = resA.Impuestos;

          if (resA.Clase == "Granel") {
            let verImpuestos = '';
            let totalImpuestos = 0;

            impuestos.forEach((impuesto) => {
              let impuestoFila = 0;
              let valor = parseFloat(impuesto.Porcentaje) || 0;
              let factor = (impuesto.Tipo_Factor || '').toUpperCase();
              let clase = (impuesto.Clase || '').toUpperCase();

              // --- Lógica de Tasa o Cuota ---
              if (factor === 'EXENTO') {
                impuestoFila = 0; // Por definición, no genera importe
              } else if (factor === 'CUOTA') {
                impuestoFila = valor;
              } else {
                impuestoFila = precio * (valor / 100);
              }

              // --- Lógica de Trasladado o Retenido para el acumulador ---
              if (clase === 'RETENCION' || clase === 'RETENIDO') {
                totalImpuestos -= impuestoFila;
              } else {
                totalImpuestos += impuestoFila;
              }

              verImpuestos += '<p class="m-0" attrID="' + impuesto.FK_Impuesto + '" clave="' + impuesto.Clave_CFDI + '"><b>' + impuesto.Clase + '</b> <span>' + impuesto.Nombre + '</span> <span class="valor ' + (impuesto.Tipo_Factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.Porcentaje + '</span> (<span class="dinero">' + impuestoFila + '</span>) - <b>' + impuesto.Tipo_Factor + '</b></p>';
            });

            let total = precio + totalImpuestos;

            $("#datosGranel").attr('attrID', resA.ID_Producto);
            $("#datosGranel").attr('attrCodigo', resA.Codigo);
            $("#datosGranel").attr('attrExistencia', existencia);
            $("#datosGranel").attr('attrPrecio', precio);
            $("#datosGranel").attr('attrImpuestos', JSON.stringify(mapearImpuestosAgregarProducto(impuestos)));
            $("#datosGranel").attr('attrPromocion', JSON.stringify(resA.Promocion || null));
            $("#datosGranel").html(`
              <h4 class="text-center">` + resA.Descripcion + `</h4>
              <h5 class="text-center">
                <b>Precio Unitario:</b> <span class="dinero">` + precio + `</span>
              </h5>
              <div class="text-center">
                ` + verImpuestos + `
              </div>
            `);

            importeGranel.value = '';
            importeGranel.value = total.toString();

            $('#importeGranel').attr('attrMayoreo', mayoreo);
            $("#MGranel").modal('show');

            moneda();
          } else {
            crearFilaProducto({
              id: resA.ID_Producto,
              codigo: resA.Codigo,
              descripcion: resA.Descripcion,
              precio: precio,
              precioMayoreo: mayoreo,
              existencia: existencia,
              impuestos: mapearImpuestosAgregarProducto(impuestos),
              promocion: resA.Promocion,
              cantidad: 1,
              lote: resA.Lote || null
            });
          }
        }

        $("#carga").hide();
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $("#carga").hide();
      });
  });

  $(document).on('click', '#bQuitarProducto', function () {
    eliminarFilaCaja($("#tablaCaja").children('tbody').children('tr.activa'));
  });

  $(document).on('click', '#tablaCaja tbody tr', function () {
    $('#tablaCaja tr').removeClass('activa');
    $(this).addClass('activa');
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>Granel>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('hidden.bs.modal', '#MGranel', function () {
    cantidadGranel.value = '';
    cantidadGranel.value = '1.00';
    importeGranel.value = '';

    var validator = $("#formGranel").validate();
    validator.resetForm();
  });

  $('#formGranel').validate({
    rules: {
      cantidadGranel: {
        required: true
      },
      importeGranel: {
        required: true
      }
    },
    messages: {
      cantidadGranel: {
        required: "La cantidad es requerida."
      },
      importeGranel: {
        required: "El importe es requerido."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      $("#carga").show();

      var mayoreo = $('#importeGranel').attr('attrMayoreo')
      var cantidadInput = parseFloat($("#cantidadGranel").val().replaceAll(',', ''));
      var precio = parseFloat($("#datosGranel").children('h5').children('span.dinero').text().replace('$', '').replace(',', ''));
      let impuestos = $("#datosGranel").children('div:eq(0)');
      let totalInput = $("#importeGranel").val().replaceAll(',', '');

      var impuestosGranelArr = [];
      try { impuestosGranelArr = JSON.parse($("#datosGranel").attr('attrImpuestos') || '[]') || []; } catch (e) { impuestosGranelArr = []; }

      var promocionGranel = null;
      try { promocionGranel = JSON.parse($("#datosGranel").attr('attrPromocion') || 'null'); } catch (e) { promocionGranel = null; }

      $("#tablaCaja").children('tbody').children('tr').removeClass('activa');

      var clase = '';
      if (localStorage.getItem("cajaTouch") === "true") {
        clase = 'class="oculto"';
      }

      var filaGranelExistente = $("#tablaCaja").children('tbody').children('tr[attrID=' + $("#datosGranel").attr('attrID') + ']');

      if (filaGranelExistente.length > 0) {
        var cantidad = parseFloat(filaGranelExistente.children('td:eq(3)').children('span.cantidad').text().replace(',', ''));
        var descuento = parseFloat(filaGranelExistente.children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(',', ''));
        var cantidadTotal = cantidad + cantidadInput;

        var resultadoPromoGranel = calcularPromocion(promocionGranel, precio, cantidadTotal);
        var subTotal = resultadoPromoGranel.subtotal;
        var descuHtml = '<span class="porcentaje">0</span>(<span class="dinero">0</span>)';

        if (!resultadoPromoGranel.aplica && descuento > 0) {
          var desDinero = ((precio * cantidadTotal) * (descuento / 100));
          subTotal = (precio * cantidadTotal) - desDinero;
          descuHtml = '<span class="porcentaje">' + descuento + '</span>(<span class="dinero">' + desDinero + '</span>)';
        }

        var totalImpuestos = 0;
        if (impuestos !== null) {
          impuestos.children('p').each(function (index, el) {
            let clase = $(this).children('b:first').text().trim().toUpperCase();
            let factor = $(this).children('b:last').text().trim().toUpperCase();

            let valor = parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replaceAll(',', '')) || 0;
            let impuestoFila = 0;

            // --- Lógica de Tasa o Cuota ---
            if (factor === 'EXENTO') {
              impuestoFila = 0; // Por definición, no genera importe
            } else if (factor === 'CUOTA') {
              impuestoFila = (parseFloat(cantidadTotal) || 0) * valor;
            } else {
              impuestoFila = subTotal * (valor / 100);
            }

            $(this).children('span.dinero').text(impuestoFila);

            // --- Lógica de Trasladado o Retenido ---
            if (clase === 'RETENCION' || clase === 'RETENIDO') {
              totalImpuestos -= impuestoFila;
            } else {
              totalImpuestos += impuestoFila;
            }
          });
        }

        let total = subTotal + totalImpuestos;
        var promoBadgeGranel = '';
        var precioMostradoGranel = precio;
        if (resultadoPromoGranel.aplica) {
          precioMostradoGranel = cantidadTotal > 0 ? (subTotal / cantidadTotal) : precio;
          promoBadgeGranel = ' <span class="badge bg-success promo-badge" title="Promoción aplicada: ' + resultadoPromoGranel.etiqueta + ' (precio de lista: $' + precio + ')"><i class="fas fa-tags"></i></span>';
        }

        filaGranelExistente.attr('attrPrecioBase', precio);
        filaGranelExistente.attr('attrPrecioMayoreo', mayoreo || 0);
        filaGranelExistente.attr('attrImpuestos', JSON.stringify(impuestosGranelArr));
        filaGranelExistente.attr('attrPromocion', JSON.stringify(promocionGranel));
        if (filaGranelExistente.attr('attrUsaMayoreo') == null) filaGranelExistente.attr('attrUsaMayoreo', '0');
        if (filaGranelExistente.attr('attrDescuentoManual') == null) filaGranelExistente.attr('attrDescuentoManual', descuento || 0);

        filaGranelExistente.addClass('activa');

        filaGranelExistente.html(`
          <td ` + clase + `>` + $("#datosGranel").attr('attrCodigo') + `</td>
          <td>` + $("#datosGranel").children('h4').text() + `</td>
          <td><span class="dinero" attrMayoreo="` + mayoreo + `">` + precioMostradoGranel + `</span>` + promoBadgeGranel + `</td>
          <td><span class="cantidad">`+ cantidadTotal + `</span></td>
          <td>` + descuHtml + `</td>
          <td><b>SUB: <span class="dinero">` + subTotal + `</span></b><br>` + impuestos.html() + `</td>
          <td><span class="dinero">`+ total + `</span></td>
          <td ` + clase + `><span class="cantidad">` + $("#datosGranel").attr('attrExistencia') + `</span></td>
        `);
      } else {
        var resultadoPromoGranelNuevo = calcularPromocion(promocionGranel, precio, cantidadInput);
        var promoBadgeGranelNuevo = '';
        var precioMostradoGranelNuevo = precio;
        if (resultadoPromoGranelNuevo.aplica) {
          precioMostradoGranelNuevo = cantidadInput > 0 ? (resultadoPromoGranelNuevo.subtotal / cantidadInput) : precio;
          promoBadgeGranelNuevo = ' <span class="badge bg-success promo-badge" title="Promoción aplicada: ' + resultadoPromoGranelNuevo.etiqueta + ' (precio de lista: $' + precio + ')"><i class="fas fa-tags"></i></span>';
        }

        var nuevaFilaGranel = $('<tr attrID="' + $("#datosGranel").attr('attrID') + '" class="activa"></tr>');
        nuevaFilaGranel.attr('attrPrecioBase', precio);
        nuevaFilaGranel.attr('attrPrecioMayoreo', mayoreo || 0);
        nuevaFilaGranel.attr('attrUsaMayoreo', '0');
        nuevaFilaGranel.attr('attrDescuentoManual', '0');
        nuevaFilaGranel.attr('attrExistencia', $("#datosGranel").attr('attrExistencia') || 0);
        nuevaFilaGranel.attr('attrImpuestos', JSON.stringify(impuestosGranelArr));
        nuevaFilaGranel.attr('attrPromocion', JSON.stringify(promocionGranel));

        nuevaFilaGranel.html(`
          <td `+ clase + `>` + $("#datosGranel").attr('attrCodigo') + `</td>
          <td>`+ $("#datosGranel").children('h4').text() + `</td>
          <td><span class="dinero" attrMayoreo="` + mayoreo + `">` + precioMostradoGranelNuevo + `</span>` + promoBadgeGranelNuevo + `</td>
          <td><span class="cantidad">` + cantidadInput + `</span></td>
          <td><span class="porcentaje">0</span>(<span class="dinero">0</span>)</td>
          <td><b>SUB: <span class="dinero">` + resultadoPromoGranelNuevo.subtotal + `</span></b>` + impuestos.html() + `</td>
          <td><span class="dinero">` + totalInput + `</span></td>
          <td `+ clase + `><span class="cantidad">` + $("#datosGranel").attr('attrExistencia') + `</span></td>
        </tr>`);

        $("#tablaCaja").children('tbody').prepend(nuevaFilaGranel);
      }

      audio1.play();

      totalCaja();
      $("#MGranel").modal('hide');
      $("#carga").hide();
    }
  });

  $(document).on('shown.bs.modal', '#MGranel', function () {
    $(this).find('#cantidadGranel').focus();
  });

  $(document).on('keypress', '#cantidadGranel', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $("#importeGranel").focus();
        break;
    }
  });

  $(document).on('keypress', '#importeGranel', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $("#formGranel").submit();
        break;
    }
  });

  $(document).on('click', '#bAgregarGranel', function () {
    $("#formGranel").submit();
  });

  $(document).on('input', '#cantidadGranel', function () {
    let precio = parseFloat($("#datosGranel").attr('attrPrecio')) || 0;
    let cantidad = parseFloat($(this).val().replaceAll(',', '')) || 0;

    let promocionGranel = null;
    try { promocionGranel = JSON.parse($("#datosGranel").attr('attrPromocion') || 'null'); } catch (e) { promocionGranel = null; }
    let subtotal = calcularPromocion(promocionGranel, precio, cantidad).subtotal;

    let totalImpuestos = 0;
    $("#datosGranel").children('div:eq(0)').children('p').each(function () {
      let clase = $(this).children('b:first').text().trim().toUpperCase();
      let factor = $(this).children('b:last').text().trim().toUpperCase();
      let valorImpuesto = parseFloat($(this).children('span:eq(1)').text().replace('%', '').replace('$', '').replaceAll(',', '')) || 0;

      let impuestoFila = 0;

      // --- Lógica de Tasa o Cuota ---
      if (factor === 'EXENTO') {
        impuestoFila = 0; // Por definición, no genera importe
      } else if (factor === 'CUOTA') {
        impuestoFila = cantidad * valorImpuesto;
      } else {
        impuestoFila = subtotal * (valorImpuesto / 100);
      }

      $(this).children('span:eq(2)').text(impuestoFila);

      // --- Lógica de Trasladado o Retenido ---
      if (clase === 'RETENCION' || clase === 'RETENIDO') {
        totalImpuestos -= impuestoFila;
      } else {
        totalImpuestos += impuestoFila;
      }
    });

    let totalFinal = subtotal + totalImpuestos;
    importeGranel.value = '';
    importeGranel.value = (Math.round(totalFinal * 100) / 100).toString();
    moneda();
  });

  $(document).on('input', '#importeGranel', function () {
    let precio = parseFloat($("#datosGranel").attr('attrPrecio')) || 0;
    let totalInput = parseFloat($(this).val().replaceAll(',', '')) || 0;

    let sumaTasas = 0;
    let sumaCuotasPorUnidad = 0;
    let filasImpuestos = $("#datosGranel").children('div:eq(0)').children('p');

    // --- PASO 1: Analizar la estructura para el desglose ---
    filasImpuestos.each(function () {
      let clase = $(this).children('b:first').text().trim().toUpperCase();
      let factor = $(this).children('b:last').text().trim().toUpperCase();
      let valor = parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replaceAll(',', '')) || 0;

      // Si es EXENTO, ignoramos el valor para el cálculo del divisor
      if (factor === 'EXENTO') {
        return true; // Continúa con el siguiente impuesto (equivalente a continue)
      }

      if (factor === 'CUOTA') {
        if (clase === 'RETENCION' || clase === 'RETENIDO') {
          sumaCuotasPorUnidad -= valor;
        } else {
          sumaCuotasPorUnidad += valor;
        }
      } else {
        // Es Tasa (IVA 16%, 8%, 0%, etc.)
        let tasa = valor / 100;
        if (clase === 'RETENCION' || clase === 'RETENIDO') {
          sumaTasas -= tasa;
        } else {
          sumaTasas += tasa;
        }
      }
    });

    // --- PASO 2: Calcular Cantidad y Subtotal ---
    let divisor = (precio + sumaCuotasPorUnidad) * (1 + sumaTasas);
    let cantidad = divisor !== 0 ? totalInput / divisor : 0;
    let subtotal = cantidad * precio;

    // --- PASO 3: Actualizar visualmente las filas ---
    filasImpuestos.each(function () {
      let factor = $(this).children('b:last').text().trim().toUpperCase();
      let valor = parseFloat($(this).children('span:eq(1)').text().replace('%', '').replace('$', '').replaceAll(',', '')) || 0;
      let impuestoFila = 0;

      if (factor === 'EXENTO') {
        impuestoFila = 0; // Siempre cero
      } else if (factor === 'CUOTA') {
        impuestoFila = cantidad * valor;
      } else {
        impuestoFila = subtotal * (valor / 100);
      }

      $(this).children('span:eq(2)').text(impuestoFila);
    });

    cantidadGranel.value = '';
    cantidadGranel.value = (Math.round(cantidad * 1000) / 1000).toString();
    moneda();
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>In.Varios>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bIntVarios', function () {
    $("#MIntVarios").modal('show')
  });

  $(document).on('shown.bs.modal', '#MIntVarios', function () {
    $(this).find('#barCodeIntVatios').focus();
  });

  $(document).on('hidden.bs.modal', '#MIntVarios', function () {
    $("#barCodeIntVatios").val("");
    cantidadIntVatios.value = '';
    var validator = $("#formIntVarios").validate();
    validator.resetForm();
  });

  $(document).on('click', '#bIntVarios', function () {
    $("#MIntVarios").modal('show');
  });

  $(document).on('keypress', '#barCodeIntVatios', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $("#cantidadIntVatios").focus();
        break;
    }
  });

  $(document).on('keypress', '#cantidadIntVatios', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $('#formIntVarios').trigger('submit');
        break;
    }
  });

  $('#formIntVarios').validate({
    rules: {
      barCodeIntVatios: {
        required: true
      },
      cantidadIntVatios: {
        required: true
      }
    },
    messages: {
      barCodeIntVatios: {
        required: "El código del producto es requerido."
      },
      cantidadIntVatios: {
        required: "La cantidad es requerida."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      const data = `metodo=consultar&accion=caja&tipo=agregarProducto&codigo=${String($('#barCodeIntVatios').val() ?? '').trim()}`

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data,
        beforeSend: function () {
          $("#carga").show();
        }
      }).done(function (response) {
        //console.log(String(response ?? '').trim());
        if (String(response ?? '').trim() === "No encontrado") {
          $("#noEncontrado").show();

          setTimeout(function () {
            $("#noEncontrado").hide();
          }, 1000);

          audio2.play();
        } else {
          const res = JSON.parse(String(response ?? '').trim());
          const cantidadInput = parseFloat($("#cantidadIntVatios").val().replaceAll(',', '')) || 0;

          if (res.Clase === "Pieza" && (cantidadInput % 1) > 0) {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El producto solo puede ser vendido en unidades o enteros, si deseas puedes configurarlo para venderlo en granel.',
              confirmButtonColor: '#3085d6',
              confirmButtonText: 'Aceptar',
            });
          } else {
            let existencia = parseFloat(res.Existencia) || 0;
            let precio = parseFloat(res.Precio) || 0;
            let mayoreo = parseFloat(res.Precio_Mayoreo) || 0;

            crearFilaProducto({
              id: res.ID_Producto,
              codigo: res.Codigo,
              descripcion: res.Descripcion,
              precio: precio,
              precioMayoreo: mayoreo,
              existencia: existencia,
              impuestos: mapearImpuestosAgregarProducto(res.Impuestos),
              promocion: res.Promocion,
              cantidad: cantidadInput,
              lote: res.Lote || null
            });
          }
        }

        $("#MIntVarios").modal('hide');
        $("#carga").hide();
      }).fail(function () {
        console.log("error")
      }).always(function () {
        $("#carga").hide();
      })
    }
  });

  $(document).on('click', '#bAgregarVarios', function () {
    $("#formIntVarios").submit();
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>In.Comun>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bProdComun', function () {
    $("#MProdComun").modal('show');
  });

  $(document).on('shown.bs.modal', '#MProdComun', function () {
    $(this).find('#descripcionProdComun').focus();
  });

  $(document).on('hidden.bs.modal', '#MProdComun', function () {
    $("#descripcionProdComun").val("");

    cantidadProdComun.value = '';
    cantidadProdComun.value = '1.00';

    precioProdComun.value = '';
    
    $("#totalProdComun").html("0");
    moneda();

    var validator = $("#formProdComun").validate();
    validator.resetForm();
  });

  $(document).on('keypress', '#descripcionProdComun', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $("#cantidadProdComun").focus();
        break;
    }
  });

  $(document).on('keypress', '#cantidadProdComun', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $("#precioProdComun").focus();
        break;
    }
  });

  $(document).on('keypress', '#precioProdComun', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $('#formProdComun').trigger('submit');
        break;
    }
  });

  $('#formProdComun').validate({
    rules: {
      descripcionProdComun: {
        required: true
      },
      cantidadProdComun: {
        required: true
      },
      precioProdComun: {
        required: true
      }
    },
    messages: {
      descripcionProdComun: {
        required: "La descripción del producto es requerida."
      },
      cantidadProdComun: {
        required: "La cantidad es requerida."
      },
      precioProdComun: {
        required: "El precio es requerido."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      $("#carga").show();

      $("#tablaCaja").children('tbody').children('tr').removeClass('activa');
      var clase = '';
      if (localStorage.getItem("cajaTouch") === "true") {
        clase = 'class="oculto"';
      }

      var precioComun = parseFloat($("#precioProdComun").val().replaceAll(",", "")) || 0;
      var cantidadComun = parseFloat($("#cantidadProdComun").val().replaceAll(",", "")) || 0;

      var nuevaFilaComun = $('<tr attrID="0" class="activa"></tr>');
      nuevaFilaComun.attr('attrPrecioBase', precioComun);
      nuevaFilaComun.attr('attrPrecioMayoreo', 0);
      nuevaFilaComun.attr('attrUsaMayoreo', '0');
      nuevaFilaComun.attr('attrDescuentoManual', '0');
      nuevaFilaComun.attr('attrExistencia', 'Ilim');
      nuevaFilaComun.attr('attrImpuestos', '[]');
      nuevaFilaComun.attr('attrPromocion', 'null');

      nuevaFilaComun.html(
        '<td ' + clase + '>0</td>' +
        '<td>' + String($("#descripcionProdComun").val() ?? '').trim() + '</td>' +
        '<td></td><td></td><td></td><td></td><td></td>' +
        '<td ' + clase + '>Ilim</td>'
      );

      $("#tablaCaja").children('tbody').prepend(nuevaFilaComun);
      recalcularFilaProducto(nuevaFilaComun, cantidadComun);

      audio1.play();

      $("#MProdComun").modal('hide');
      $("#carga").hide();
    }
  });

  $(document).on('click', '#bAgregarProdComun', function () {
    $("#formProdComun").submit();
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>Buscar>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bBuscarProd', function () {
    $("#MBuscarProd").modal('show');
  });

  $(document).on('shown.bs.modal', '#MBuscarProd', function () {
    $(this).find(".buscadorMyDataTable[tabla='tablaBuscarProductos']").focus();
  });

  $(document).on('click', '#tablaBuscarProductos tbody tr', function () {
    $('#tablaBuscarProductos tr').removeClass('activa');
    $(this).addClass('activa');
    agregarProdBusqueda($(this));
  });

  $(document).on('click', '#bRecargarBusquedaProductos', function () {
    tablaBuscarProductos();
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>Mayoreo>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bMayoreo', () => {
    const fila = $("#tablaCaja").children('tbody').children('tr.activa');

    if (fila.length === 0) return;

    if (filaTienePromocionActiva(fila)) {
      Swal.fire({
        icon: 'info',
        title: 'Promoción activa',
        text: 'Este producto ya tiene una promoción aplicada, no se puede usar el precio de mayoreo.'
      });
      return;
    }

    const mayoreo = parseFloat(fila.attr('attrPrecioMayoreo')) || 0;
    if (mayoreo <= 0) return;

    const cantidadActual = parseFloat(fila.children('td:eq(3)').children('span.cantidad').text().replace(',', '')) || 0;
    const usaMayoreo = fila.attr('attrUsaMayoreo') === '1';
    fila.attr('attrUsaMayoreo', usaMayoreo ? '0' : '1');

    recalcularFilaProducto(fila, cantidadActual);
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>Entrada>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bEntrada', function () {
    $("#MEntrada").modal('show');
  });

  $(document).on('shown.bs.modal', '#MEntrada', function () {
    $('#montoEntrada').focus();
  });

  $(document).on('hidden.bs.modal', '#MEntrada', function () {
    montoEntrada.value = '';

    var validator = $("#formEntrada").validate();
    validator.resetForm();
  });

  $(document).on('click', '#bGuardarEntrada', function () {
    $('#formEntrada').trigger('submit');
  });

  $(document).on('keypress', '#montoEntrada', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        event.preventDefault();
        $("#descripcionEntrada").focus();
        break;
    }
  });

  $(document).on('keypress', '#descripcionEntrada', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $('#formEntrada').trigger('submit');
        break;
    }
  });

  $('#formEntrada').validate({
    rules: {
      montoEntrada: {
        required: true
      },
      descripcionEntrada: {
        required: true
      }
    },
    messages: {
      montoEntrada: {
        required: "El monto es requerido."
      },
      descripcionEntrada: {
        required: "La descripción es requerida."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      var data = `metodo=insertar&accion=caja&tipo=movimiento&tipoMov=Entrada&cajaId=${String($('#modalCaja').attr('attrCaja') ?? '').trim()}&cantidad=${$('#montoEntrada').val().replaceAll(',', '')}&descripcion=${$("#descripcionEntrada").val()}`

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          if (String(res ?? '').trim() === 'Correcto') {
            Swal.fire({
              title: 'Correcto',
              text: 'Se ha registrado la entrada',
              icon: 'success',
            });

            $("#MEntrada").modal('hide');
          } else {
            Swal.fire({
              title: 'Error',
              text: 'No se ha podido registrar la entrada',
              icon: 'error',
            });

            console.log(String(res ?? '').trim());
          }
        })
        .fail(function () {
          console.log("Error ajax");
        })
        .always(function () {
          $("#carga").hide();
        });
    }
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>Salida>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bSalida', function () {
    $("#MSalida").modal('show');
  });

  $(document).on('shown.bs.modal', '#MSalida', function () {
    $('#montoSalida').focus();
  });

  $(document).on('hidden.bs.modal', '#MSalida', function () {
    montoSalida.value = '';

    var validator = $("#formSalida").validate();
    validator.resetForm();
  });

  $(document).on('click', '#bGuardarSalida', function () {
    $('#formSalida').trigger('submit');
  });

  $(document).on('keypress', '#montoSalida', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        event.preventDefault();
        $("#descripcionSalida").focus();
        break;
    }
  });

  $(document).on('keypress', '#descripcionSalida', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $('#formSalida').trigger('submit');
        break;
    }
  });

  $('#formSalida').validate({
    rules: {
      montoSalida: {
        required: true
      },
      descripcionSalida: {
        required: true
      }
    },
    messages: {
      montoSalida: {
        required: "El monto es requerido."
      },
      descripcionSalida: {
        required: "La descripción es requerida."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      const data = `metodo=insertar&accion=caja&tipo=movimiento&tipoMov=Salida&cajaId=${String($('#modalCaja').attr('attrCaja') ?? '').trim()}&cantidad=${$('#montoSalida').val().replaceAll(',', '')}&descripcion=${$("#descripcionSalida").val()}`;

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          if (String(res ?? '').trim() === 'Correcto') {
            Swal.fire({
              title: 'Correcto',
              text: 'Se ha registrado la salida',
              icon: 'success',
            });

            $("#MSalida").modal('hide');
          } else {
            Swal.fire({
              title: 'Error',
              text: 'No se ha podido registrar la salida',
              icon: 'error',
            });

            console.log(String(res ?? '').trim());
          }
        })
        .fail(function () {
          console.log("Error ajax")
        })
        .always(function () {
          $("#carga").hide();
        });
    }
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>Descuento>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bGuardarDescuento', function () {
    $('#formDescuento').submit();
  });

  $(document).on('shown.bs.modal', '#MDescuento', function () {
    $('#porcentajeDescuentoCaja').focus();
  });

  $(document).on('click', '#bHacerDescuento', function () {
    tipoDescuento = 0;
    const fila = $("#tablaCaja").children('tbody').children('tr.activa');

    if (fila.length > 0) {
      if (filaTienePromocionActiva(fila)) {
        Swal.fire({
          icon: 'info',
          title: 'Promoción activa',
          text: 'Este producto ya tiene una promoción aplicada, no se puede aplicar un descuento manual.'
        });
        return;
      }

      const descripcion = fila.children('td:eq(1)').text();
      const usaMayoreoActivo = fila.attr('attrUsaMayoreo') === '1';
      const precioUnitarioActivo = usaMayoreoActivo ? (parseFloat(fila.attr('attrPrecioMayoreo')) || 0) : (parseFloat(fila.attr('attrPrecioBase')) || 0);
      const total = precioUnitarioActivo * (parseFloat(fila.children('td:eq(3)').children('span.cantidad').text().replace(',', '')) || 0);
      const descuento = fila.children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(searchRegExp, '');

      $('#descripcionDescuento').text(descripcion);
      $('#totalDescuento').text(total);

      porcentajeDescuentoCaja.value = '';
      porcentajeDescuentoCaja.value = descuento.toString();

      $('#porcentajeDescuentoCaja').trigger('change');

      $("#MDescuento").modal('show');
    }
  });

  $(document).on('keyup change', '#porcentajeDescuentoCaja', function (event) {
    const totalDescuento = parseFloat($('#totalDescuento').text().replace(searchRegExp, '').replace('$', ''));
    const totalDescuentoOp = totalDescuento * (parseFloat($(this).val().replaceAll(',', '')) / 100);
    const total = isNaN(totalDescuento - totalDescuentoOp) ? totalDescuento : totalDescuento - totalDescuentoOp;

    cantidadDescuentoCaja.value = '';
    cantidadDescuentoCaja.value = (Math.round(totalDescuentoOp * 100) / 100).toString();

    $('#restanteDescuento').text(total);

    moneda();

    if (event.which == 13) $('#cantidadDescuentoCaja').focus();
  });

  $(document).on('keyup change', '#cantidadDescuentoCaja', function (event) {
    const cantidadDescuentoCaja = parseFloat($(this).val().replaceAll(',', '')) || 0;
    const totalDescuento = parseFloat($('#totalDescuento').text().replace(searchRegExp, '').replace('$', ''));

    const totalDescuentoOp = 100 * (cantidadDescuentoCaja / totalDescuento);
    const total = isNaN(totalDescuentoOp) ? totalDescuento : totalDescuento - cantidadDescuentoCaja;

    porcentajeDescuentoCaja.value = '';
    porcentajeDescuentoCaja.value = (Math.round(totalDescuentoOp * 100) / 100).toString();

    $('#restanteDescuento').text(total);

    moneda();
    if (event.which === 13) $('#formDescuento').submit();
  })

  $('#formDescuento').on('submit', function (event) {
    event.preventDefault();
    const [porcentaje, total] = event.target;

    const restante = parseFloat($('#restanteDescuento').text().replace(searchRegExp, '').replace('$', ''));

    if (restante < 0) {
      Swal.fire({
        title: 'Error',
        text: 'El descuento no puede ser mayor al total',
        icon: 'warning',
      });
    } else {
      if (tipoDescuento === 0) {
        let fila = $("#tablaCaja").children('tbody').children('tr.activa');
        const cantidadActual = parseFloat(fila.children('td:eq(3)').children('span.cantidad').text().replace(',', '')) || 0;

        fila.attr('attrDescuentoManual', porcentaje.value || 0);
        recalcularFilaProducto(fila, cantidadActual);
      } else {
        $("#mosDesGeP").text($("#porcentajeDescuentoCaja").val().replaceAll(',', '') || 0);
        $("#mosDesGeD").text($("#cantidadDescuentoCaja").val().replaceAll(',', '') || 0);
        totalCaja();
      }

      $("#MDescuento").modal('hide');
    }
  })

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>Cambiar ticket>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('shown.bs.modal', '#MPendiente', function () {
    $('#nombrePendiente').focus();
  });

  $(document).on('click', '#bGuardarPendiente', function () {
    $("#formPendiente").submit();
  });

  $(document).on('click', '#bTicketPendiente', function () {
    // Calcular el nombre sugerido para el nuevo ticket
    const tickets = $('#navtabTickets').children();
    const lastTicketId = $(tickets[tickets.length - 1]).attr('id').split('_')[2];
    const nextTicketName = 'Ticket ' + (parseInt(lastTicketId) + 1);
    $('#nombrePendiente').val(nextTicketName);
    $('#MPendiente').modal('show');
  });

  $(document).on('submit', '#formPendiente', function (event) {
    event.preventDefault();

    const tickets = $('#navtabTickets').children();
    const newTicketName = $('#nombrePendiente').val().trim();
    const lastTicketId = $(tickets[tickets.length - 1]).attr('id').split('_')[2];
    const activeTable = $('.tab-pane.active');
    let ticketName = parseInt(lastTicketId) + 1;

    // Validar que no exista un ticket con el mismo nombre
    let nombreDuplicado = false;
    tickets.each(function () {
      if ($(this).text().trim() === newTicketName) {
        nombreDuplicado = true;
        return false; // break
      }
    });

    if (nombreDuplicado) {
      Swal.fire({
        icon: 'warning',
        title: 'Nombre duplicado',
        text: `Ya existe un ticket con el nombre "${newTicketName}"`,
      });
      return;
    }

    activeTable.find('#tablaCaja').attr('id', '');

    $('#navtabTickets').children().removeClass('active');
    $('#nav-tabContent').children().removeClass('active');

    var clase = '';
    if (localStorage.getItem("cajaTouch") === "true") {
      clase = 'class="oculto"';
    }

    $('#navtabTickets').append(`<button class="nav-link active" id="tab_ticket_${ticketName}" data-bs-toggle="tab" data-bs-target="#nav_ticket_${ticketName}" type="button" role="tab" aria-selected="true">${newTicketName}</button>`);
    $('#nav-tabContent').append(`<div class="tab-pane fade show active" id="nav_ticket_${ticketName}" role="tabpanel" aria-labelledby="nav-profile-tab">
      <div class="row">
        <div class="col-12 table-responsive" style="height: 40vh; background-color:#F0F0F0;">
          <table class="table table-hover table-striped text-center" id="tablaCaja" style="width: 100%; font-size: 12px;">
            <thead>
              <tr>
                <th ${clase}>Código</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Descuento</th>
                <th>Total</th>
                <th ${clase}>Existencia</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>`);

    $('#MPendiente').modal('hide');
  });

  $(document).on('shown.bs.tab', 'button.nav-link', function () {
    $('#tablaCaja').attr('id', '');
    $($(this).attr('data-bs-target')).find('table').attr('id', 'tablaCaja');
    totalCaja();
  });

  $(document).on('click', '#bCambiarTicket', function () {
    if ($('#navtabTickets').children().length > 1) {
      const tickets = $('#navtabTickets').children();

      let listHTML = `<ul class="list-group">`
      tickets.each((_, element) => {
        const isActive = $(element).hasClass('active')
        listHTML += `<li class="list-group-item list-group-item-action ${isActive ? 'active' : ''}" data-ticket="${$(element).text()}">${$(element).text()}</li>`;
      });
      listHTML += `</ul>`;

      $('#cambiarTicketBody').html(listHTML);
      $('#MCambiarTicket').modal('show');
    }
  })

  $(document).on('click', '#cambiarTicketBody .list-group li', function () {
    $('#cambiarTicketBody').find('li').removeClass('active');
    $(this).addClass('active');
  })

  $(document).on('keyup', '#MCambiarTicket', function (event) {
    const tickets = $('#cambiarTicketBody > ul').children();
    const ticketActive = $('#cambiarTicketBody > ul').children('.active');
    const ticketActiveIndex = tickets.index(ticketActive);

    if (![38, 40, 13].includes(event.which)) return;
    tickets.removeClass('active');
    let eq = ticketActiveIndex;

    // up
    if (event.which === 38) eq = (ticketActiveIndex - 1) % tickets.length

    // down
    if (event.which === 40) eq = (ticketActiveIndex + 1) % tickets.length

    tickets.eq(eq).addClass('active');

    if (event.which === 13) {
      const ticket = tickets.eq(eq).attr('data-ticket');
      $('#navtabTickets').children().each((_, element) => {
        if ($(element).text() === ticket) $(element).click();
      });
      $('#MCambiarTicket').modal('hide');
    }
  });

  $(document).on('click', '#bTabCambiarTicket', function () {
    const tickets = $('#cambiarTicketBody > ul').children();
    const ticketActive = $('#cambiarTicketBody > ul').children('.active');
    const ticketActiveIndex = tickets.index(ticketActive);
    let eq = ticketActiveIndex;

    const ticket = tickets.eq(eq).attr('data-ticket');
    $('#navtabTickets').children().each((_, element) => {
      if ($(element).text() === ticket) $(element).click();
    });
    $('#MCambiarTicket').modal('hide');
  });

  $(document).on('click', '#cambiarTicketBody > ul > li.list-group-item.list-group-item-action.active', function () {
    const ticket = $(this).attr('data-ticket');
    $('#navtabTickets').children().each((_, element) => {
      if ($(element).text() === ticket) $(element).click();
    });

    $('#MCambiarTicket').modal('hide');
  });

  $(document).on('click', '#bEliminarTicket', function () {
    if ($('#navtabTickets').children().length <= 1) return;

    const tickets = $('#navtabTickets').children();
    const ticketActive = $('#navtabTickets').children('.active');
    const ticketActiveIndex = tickets.index(ticketActive);

    Swal.fire({
      title: `¿Estás seguro de eliminar el ${ticketActive.text()}?`,
      text: 'No podrás revertir esta acción',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar',
    }).then((result) => {
      if (result.isConfirmed) {
        const ticketToClick = ticketActiveIndex === 0 ? 1 : ticketActiveIndex - 1;
        const nextTab = tickets.eq(ticketToClick);

        // Reasignar #tablaCaja manualmente antes de eliminar el tab
        $('#tablaCaja').attr('id', '');
        $(nextTab.attr('data-bs-target')).find('table').attr('id', 'tablaCaja');

        nextTab.click();
        ticketActive.remove();
        $(`#nav_ticket_${ticketActive.attr('id').split('_')[2]}`).remove();
      }
    });
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>Cobrar>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('shown.bs.modal', '#MCobrar', function () {
    $('#totalPagoCobrar').focus();
  });

  $(document).on('hidden.bs.modal', '#MCobrar', function () {
    setTimeout(function () {
      if (!$("#MBuscarCliente").is(':visible')) {
        $('#formCobrar')[0].reset();
      }
    }, 5);
    $("#clienteCobrar").attr('attrID', '');
    $("#clienteCobrar").val('');
  });

  $(document).on('click', '#bCobrar', function () {
    $("#bCobrarImprimir").attr('tipo', '');

    setTimeout(function () {
      if ($('#tablaCaja').children('tbody').children('tr').length > 0) {
        const totalCaja = parseFloat($('#totalCaja').text().replace('$', '').replace(searchRegExp, ''));
        $('#totalCobrar').text(totalCaja);

        totalPagoCobrar.value = '';
        totalPagoCobrar.value = totalCaja.toString();

        $('#totalCambio').text(0);

        if (String($("#bAgregarClienteBusN").attr('attrID') ?? '').trim() != '') {
          $("#clienteCobrar").val(String($("#bAgregarClienteBusN").children('span:eq(0)').text() ?? '').trim());
          $("#clienteCobrar").attr('attrID', String($("#bAgregarClienteBusN").attr('attrID') ?? '').trim());
          $("#clienteCobrar").attr('attrDireccion', String($("#bAgregarClienteBusN").attr('attrDireccion') ?? '').trim());
        }

        moneda();
        $('#MCobrar').modal('show');
      }
    }, 300);
  });

  $(document).on('keyup change input', '#totalPagoCobrar', function (event) {
    const totalCaja = parseFloat($('#totalCobrar').text().replace('$', '').replaceAll(',', ''));
    const totalPago = parseFloat($(this).val().replaceAll(',', ''));
    var cambio = isNaN(totalPago - totalCaja) ? 0 : (totalPago - totalCaja);
    if (cambio < 0) {
      cambio = 0;
    }
    $('#totalCambio').text(cambio);
    moneda();

    switch (event.keyCode) {
      case 13: // Enter
        event.preventDefault();
        $("#metodoPago").focus();
        break;
    }
  });

  $(document).on('keydown', '#metodoPago', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $("#detallesCobrar").focus();
        break;
    }
  });

  $(document).on('change', '#metodoPago', function (event) {
    if($(this).val() == 'Crédito'){
      totalPagoCobrar.value = '';
      totalPagoCobrar.value = '0';
    }
  });

  $(document).on('keydown', '#detallesCobrar', function (event) {
    switch (event.keyCode) {
      case 13: // Enter
        $("#bCobrarImprimir").focus();
        break;
    }
  });

  var tipoCobrar = 0;
  $(document).on('click', '#bSoloCobrar', function () {
    tipoCobrar = 0;
    $('#formCobrar').submit();
  });

  $(document).on('click', '#bCobrarImprimir', function () {
    tipoCobrar = 1;
    $('#formCobrar').submit();
  });

  $('#formCobrar').validate({
    rules: {
      totalPagoCobrar: {
        required: true
      }
    },
    messages: {
      totalPagoCobrar: {
        required: "El monto de pago es requerido."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      if (procesandoCobro) {
        return; // si ya está en proceso, no hace nada
      }

      if ($('#tablaCaja tbody tr').length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'Debes agregar al menos un producto para cobrar.'
        });
        return;
      }

      procesandoCobro = true;

      setTimeout(function () {
        if ($("#metodoPago").val() == 'Crédito' && $("#clienteCobrar").attr('attrID') == '') {
          Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'La venta es a crédito, debes sellecionar un cliente.'
          });

          procesandoCobro = false;
        } else if ($("#bCobrarImprimir").attr('tipo') == 'pendiente') {
          var data = "metodo=modificar&accion=caja&tipo=pediente&idCaja=" + String($('#modalCaja').attr('attrCaja') ?? '').trim()
            + "&descuento=" + $("#mosDesGeD").text().replace('$', '').replaceAll(',', '') + "&total=" + String($("#totalCobrar").text().replace('$', '').replace(searchRegExp, '') ?? '').trim()
            + "&tipoPago=" + $("#metodoPago").val() + "&pago=" + $("#totalPagoCobrar").val().replaceAll(',', '') + "&cambio=" + String($("#totalCambio").text().replace('$', '').replace(searchRegExp, '') ?? '').trim()
            + "&detalles=" + String($("#detallesCobrar").val() ?? '').trim() + "&cliente=" + $("#clienteCobrar").attr('attrID') + "&idVenta=" + $("#bCobrarImprimir").attr('attrID');

          $.ajax({
            url: 'index.php',
            type: 'POST',
            data: data,
            beforeSend: function () {
              $("#carga").show();
            }
          })
            .done(function (res) {
              if (String(res ?? '').trim() == 'Correcto') {
                if (tipoCobrar == 0) {
                  imprimirTicketCaja('controladores/pdf/ticketAbrir.php');
                } else {
                  imprimirTicketCaja('controladores/pdf/ticketVenta.php?id=' + $("#bCobrarImprimir").attr('attrID'));
                }

                $("#MCobrar").modal('hide');
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Error inesperado al registar la venta.'
                });

                console.log(String(res ?? '').trim());
              }
            })
            .fail(function () {
              console.log("Error ajax");
            })
            .always(function () {
              $("#carga").hide();
              procesandoCobro = false;
            });
        } else {
          var total = parseFloat($("#totalCobrar").text().replace('$', '').replace(searchRegExp, ''));
          var pagado = parseFloat($("#totalPagoCobrar").val().replaceAll(',', ''));

          if (pagado < total && $("#metodoPago").val() != 'Crédito') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El monto pagado debe ser mayor o igual al total de la venta.',
            });

            procesandoCobro = false;
          } else {
            var productos = [];
            $('#tablaCaja tbody tr').each(function (index, el) {
              var impuestosProducto = [];
              $(this).children('td:eq(5)').children('p').each(function (index, el) {
                impuestosProducto.push({
                  id: $(this).attr('attrID'),
                  nombre: $(this).children('span:eq(0)').text(),
                  porcentaje: parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replaceAll(',', '')),
                  clave: $(this).attr('clave'),
                  factor: $(this).children('b:first').text().trim(),
                  clase: $(this).children('b:last').text().trim()
                });
              });

              var descripProd = $(this).find('.desc-prod-caja').length > 0 ? $(this).find('.desc-prod-caja').text().trim() : $(this).children('td:eq(1)').text().trim();
              var lotesProd = [];
              try { lotesProd = JSON.parse($(this).attr('attrLotes') || '[]') || []; } catch (e) { lotesProd = []; }

              productos.push({
                id: parseInt($(this).attr('attrid')),
                descripcion: descripProd,
                precio: parseFloat($(this).children('td:eq(2)').text().replace('$', '').replaceAll(',', '')),
                cantidad: parseFloat($(this).children('td:eq(3)').text().replaceAll(',', '')),
                descuento: $(this).children('td:eq(4)').children('span.dinero').text().replace('$', '').replaceAll(',', ''),
                total: parseFloat($(this).children('td:eq(6)').text().replace('$', '').replaceAll(',', '')),
                impuestos: impuestosProducto,
                lotes: lotesProd
              });
            });

            var numTi = '';
            if (localStorage.getItem("cajaTouch") === "true") {
              var numTicket = localStorage.getItem('numTicket');
              if (numTicket < 100) {
                numTicket++;
              } else {
                numTicket = 1;
              }

              numTi = numTicket;
            }

            var data = "metodo=insertar&accion=caja&tipo=venta&idCaja=" + String($('#modalCaja').attr('attrCaja') ?? '').trim() + "&descuento=" + $("#mosDesGeD").text().replace('$', '').replaceAll(',', '') 
            + "&total=" + String($("#totalCobrar").text().replace('$', '').replaceAll(',', '') ?? '').trim() + "&tipoPago=" + $("#metodoPago").val() 
            + "&pago=" + $("#totalPagoCobrar").val().replaceAll(',', '') + "&cambio=" + String($("#totalCambio").text().replace('$', '').replaceAll(',', '') ?? '').trim() 
            + "&cliente=" + $("#clienteCobrar").attr('attrID') + "&fkDireccion=" + $("#clienteCobrar").attr('attrDireccion') + "&detalles=" + String($("#detallesCobrar").val() ?? '').trim() 
            + "&productos=" + JSON.stringify(productos) + "&turno=" + numTi;
            
            console.log(data);

            $.ajax({
              url: 'index.php',
              type: 'POST',
              data: data,
              beforeSend: function () {
                $("#carga").show();
              }
            })
              .done(function (res) {
                var separa = String(res ?? '').trim().split('~');
                if (separa[0] == 'Correcto') {
                  if (tipoCobrar == 0) {
                    imprimirTicketCaja('controladores/pdf/ticketAbrir.php');
                  } else {
                    imprimirTicketCaja('controladores/pdf/ticketVenta.php?id=' + separa[1]);
                  }

                  if (localStorage.getItem("cajaTouch") === "true") {
                    localStorage.setItem('numTicket', numTicket);
                    $("#mosNumTicket").html('Turno: ' + numTicket);
                  }

                  const tickets = $('#navtabTickets').children();
                  if (tickets.length > 1) {
                    const ticketActive = $('#navtabTickets').children('.active');
                    const ticketActiveIndex = tickets.index(ticketActive);
                    const ticketToClick = ticketActiveIndex === 0 ? 1 : ticketActiveIndex - 1;
                    const nextTab = tickets.eq(ticketToClick);

                    // Reasignar #tablaCaja manualmente antes de eliminar el tab
                    $('#tablaCaja').attr('id', '');
                    $(nextTab.attr('data-bs-target')).find('table').attr('id', 'tablaCaja');

                    nextTab.click();
                    ticketActive.remove();
                    $(`#nav_ticket_${ticketActive.attr('id').split('_')[2]}`).remove();
                  } else {
                    $('#tablaCaja').children('tbody').html("");
                  }

                  $("#mosDesGeP").html('0');
                  totalCaja();

                  $("#bAgregarClienteBusN").html('Agregar un cliente <i class="fas fa-plus"></i>');
                  $("#bAgregarClienteBusN").attr('attrID', '');
                  $("#bAgregarClienteBusN").attr('attrDireccion', '');

                  $("#MCobrar").modal('hide');
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error inesperado al registar la venta.'
                  });

                  console.log(String(res ?? '').trim());
                }
              })
              .fail(function () {
                console.log("Error ajax");
              })
              .always(function () {
                $("#carga").hide();
                procesandoCobro = false;
              });
          }
        }
      }, 300);
    }
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>Corte caja>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bCorteCaja', function () {
    $("#tablaDetallesBalance").children('tbody').html('');
    $("#tablaTotalVentas").children('tbody').html('');
    $("#tablaMovimientos").children('tbody').html('');
    $("#tablaTotalPagosVentas").children('tbody').html('');
    $("#tablaTotalPagosCompras").children('tbody').html('');
    $("#tablaTotalVentasUsuarios").children('tbody').html('');
    $("#tablaDetallesPagosVentas").children('tbody').html('');
    $("#tablaVentasClasificaciones").children('tbody').html('');
    $("#totalCantClasificaciones").text('0');
    $("#totalDineroClasificaciones").text('0');

    const data = `metodo=consultar&accion=caja&tipo=corte&idCaja=${$(this).attr('attrID')}`;

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data,
      beforeSend: function () {
        $('#carga').show();
      }
    })
      .done(function (res) {
        //console.log(String(res ?? '').trim());
        const resA = JSON.parse(String(res ?? '').trim());
        //console.log(resA);

        $("#fechaApertura").text(resA.detalles.Fecha_Abrir);
        $("#fechaCierre").text(resA.detalles.Fecha_Cierre);
        $("#fechaCierre").attr('fecha', resA.detalles.Fecha);
        $("#usuarioApertura").text(resA.detalles.Usuario_Abrio);

        $("#tablaDetallesBalance").children('tbody').append(`<tr>
        <td>Monto de apertura</td>
        <td><span class="dinero" style="color: green;">`+ resA.detalles.Monto_Abrir + `</span></td>
      </tr>`);

        var totalVentas = 0, mas = '';
        if (resA.ventas.length > 0) {
          resA.ventas.forEach(venta => {
            if (parseFloat(venta.Total) > 0) {
              if (venta.Tipo_Pago == "Efectivo" || venta.Tipo_Pago == "Crédito") {
                totalVentas += parseFloat(venta.Total);
              }

              mas = '';
              if (venta.Tipo_Pago == "Crédito") {
                mas = ' (Abono Efectivo)';
              }

              $("#tablaTotalVentas").children('tbody').append(`<tr>
              <td>`+ venta.Tipo_Pago + `` + mas + `</td>
              <td><span class="dinero" style="color: green;">`+ venta.Total + `</span></td>
            </tr>`);
            }
          });

          $("#tablaDetallesBalance").children('tbody').append(`<tr>
          <td>Ventas Efectivo</td>
          <td><span class="dinero" style="color: green;">`+ totalVentas + `</span></td>
        </tr>`);
        } else {
          $("#tablaTotalVentas").children('tbody').append(`<tr>
          <td colspan="2">No existen registros.</td>
        </tr>`);
        }

        var totalEntradas = 0, totalSalidas = 0;
        if (resA.movimientos.length > 0) {
          resA.movimientos.forEach(mov => {
            var color = '';
            if (mov.Tipo == "Entrada") {
              totalEntradas += parseFloat(mov.Monto);
              color = 'green'
            } else {
              totalSalidas += parseFloat(mov.Monto);
              color = 'red';
            }

            $("#tablaMovimientos").children('tbody').append(`<tr>
            <td>`+ mov.Fecha_Registro + `</td>
            <td>`+ mov.Tipo + `</td>
            <td>`+ mov.Descripcion + `</td>
            <td><span class="dinero" style="color: `+ color + `;">` + mov.Monto + `</span></td>
          </tr>`);
          });

          $("#tablaDetallesBalance").children('tbody').append(`<tr>
          <td>Entradas</td>
          <td><span class="dinero" style="color: green;">`+ totalEntradas + `</span></td>
        </tr>
        <tr>
          <td>Salidas</td>
          <td><span class="dinero" style="color: red;">`+ totalSalidas + `</span></td>
        </tr>`);
        } else {
          $("#tablaMovimientos").children('tbody').append(`<tr>
          <td colspan="4">No existen registros.</td>
        </tr>`);
        }

        var totalPagosCompras = 0;
        if (resA.pagos_compras.length > 0) {
          resA.pagos_compras.forEach(pago => {
            if (pago.Tipo_Pago == "Efectivo") {
              totalPagosCompras += parseFloat(pago.Total);
            }

            $("#tablaTotalPagosCompras").children('tbody').append(`<tr>
            <td>`+ pago.Tipo_Pago + `</td>
            <td><span class="dinero" style="color: red;">`+ pago.Total + `</span></td>
          </tr>`);
          });

          $("#tablaDetallesBalance").children('tbody').append(`<tr>
          <td>Pagos Compras Efectivo</td>
          <td><span class="dinero" style="color: red;">`+ totalPagosCompras + `</span></td>
        </tr>`);
        } else {
          $("#tablaTotalPagosCompras").children('tbody').append(`<tr>
          <td colspan="2">No existen registros.</td>
        </tr>`);
        }

        var totalPagosVentas = 0;
        if (resA.pagos_ventas.length > 0) {
          resA.pagos_ventas.forEach(pago => {
            if (pago.Tipo_Pago == "Efectivo") {
              totalPagosVentas += parseFloat(pago.Total);
            }

            $("#tablaTotalPagosVentas").children('tbody').append(`<tr>
            <td>`+ pago.Tipo_Pago + `</td>
            <td><span class="dinero" style="color: green;">`+ pago.Total + `</span></td>
          </tr>`);
          });

          $("#tablaDetallesBalance").children('tbody').append(`<tr>
          <td>Pagos Ventas Efectivo</td>
          <td><span class="dinero" style="color: green;">`+ totalPagosVentas + `</span></td>
        </tr>`);
        } else {
          $("#tablaTotalPagosVentas").children('tbody').append(`<tr>
          <td colspan="2">No existen registros.</td>
        </tr>`);
        }

        if (resA.usuarios.length > 0) {
          resA.usuarios.forEach(usuario => {
            $("#tablaTotalVentasUsuarios").children('tbody').append(`<tr>
            <td>`+ usuario.Usuario + `</td>
            <td><span class="dinero" style="color: green;">`+ usuario.Total + `</span></td>
          </tr>`);
          });
        } else {
          $("#tablaTotalVentasUsuarios").children('tbody').append(`<tr>
          <td colspan="2">No existen registros.</td>
        </tr>`);
        }

        if (resA.registros_pagos_ventas.length > 0) {
          resA.registros_pagos_ventas.forEach(detalles => {
            $("#tablaDetallesPagosVentas").children('tbody').append(`<tr>
            <td>`+ detalles.Folio + `</td>
            <td>`+ detalles.Cliente + `</td>
            <td><span class="dinero" style="color: green;">`+ detalles.Monto + `</span></td>
            <td>`+ detalles.Tipo_Pago + `</td>
          </tr>`);
          });
        } else {
          $("#tablaDetallesPagosVentas").children('tbody').append(`<tr>
          <td colspan="4">No existen registros.</td>
        </tr>`);
        }

        var totalCantClasif = 0, totalDineroClasif = 0;
        if (resA.clasificaciones && resA.clasificaciones.length > 0) {
          resA.clasificaciones.forEach(clasif => {
            totalCantClasif += parseFloat(clasif.Cantidad);
            totalDineroClasif += parseFloat(clasif.Total);

            $("#tablaVentasClasificaciones").children('tbody').append(`<tr>
            <td>`+ clasif.Clasificacion + `</td>
            <td><span class="cantidad">`+ clasif.Cantidad + `</span></td>
            <td><span class="dinero" style="color: green;">`+ clasif.Total + `</span></td>
          </tr>`);
          });
        } else {
          $("#tablaVentasClasificaciones").children('tbody').append(`<tr>
          <td colspan="3">No existen registros.</td>
        </tr>`);
        }

        $("#totalCantClasificaciones").text(totalCantClasif);
        $("#totalDineroClasificaciones").text(totalDineroClasif);

        var balance = (parseFloat(resA.detalles.Monto_Abrir) + totalVentas + totalEntradas + totalPagosVentas) - (totalPagosCompras + totalSalidas);
        $("#balanceCaja").text(balance);

        montoCorte.value = '';
        montoCorte.value = balance.toString();

        $("#restanteCaja").text('0');

        moneda();
        $('#MCorteCaja').modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  $(document).on('keyup change', '#montoCorte', function () {
    const montoCorte = parseFloat($(this).val().replaceAll(',', ''));
    const totalCaja = parseFloat($('#balanceCaja').text().replace('$', '').replace(searchRegExp, ''));
    const diferencia = montoCorte - totalCaja;
    $('#restanteCaja').text(isNaN(diferencia) ? 0 : diferencia);
    const textClass = diferencia < 0 ? 'text-danger' : 'text-success';
    $('#restanteCaja').removeClass('text-danger text-success');
    $('#restanteCaja').addClass(textClass);
    moneda();
  });

  $(document).on('shown.bs.modal', '#MCorteCaja', function () {
    $('#montoCorte').focus();
  });

  $(document).on('click', '#bHacerCorte', function () {
    cerrarCaja(false);
    document.exitFullscreen();
  });

  $(document).on('click', '#bHacerCorteImprimir', function () {
    cerrarCaja(true);
    document.exitFullscreen();
  });

  $(document).on('click', '#bDejarCaja', function () {
    detenerEscanerCaja();
    const data = 'metodo=modificar&accion=caja&tipo=dejar&idCaja=' + $(this).attr('attrID');

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data,
      beforeSend: function () {
        $('#carga').show();
      }
    })
      .done(function (res) {
        if (String(res ?? '').trim() == "Correcto") {
          document.exitFullscreen();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al dejar la caja.'
          });

          console.log(String(res ?? '').trim());
        }
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>Ventas Pendientes>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  /*$(document).on('click', '#bVentasPendientes', function () {
    $("#MVentasPendientes").modal('show');
  });

  $(document).on('click', '#bRecargarVentasPendientes', function (event) {
    tablaVentasPendientes();
  });

  $(document).on('shown.bs.modal', '#MVentasPendientes', function () {
    tablaVentasPendientes();
    $(this).find(".buscadorMyDataTable[tabla='tablaVentasPendientes']").focus();
  });

  $(document).on('click', '#tablaVentasPendientes tr', function () {
    $('#tablaVentasPendientes tr').removeClass('activa');
    $(this).addClass('activa');
  });

  $(document).on('click', '#tablaVentasPendientes tr', function () {
    $('#totalCobrar').text(parseFloat($(this).children('td:eq(3)').text().replace('$', '').replace(searchRegExp, '')));
    
    totalPagoCobrar.value = '';
    totalPagoCobrar.value = parseFloat($(this).children('td:eq(4)').text().replace('$', '').replace(searchRegExp, '')).toString();

    $('#totalCambio').text($(this).children('td:eq(5)').text());
    $('#detallesCobrar').val(String($(this).children('td:eq(6)').children('p:eq(0)').text() ?? '').trim());

    moneda();
    $("#bCobrarImprimir").attr('attrID', $(this).attr('id'));
    $("#bCobrarImprimir").attr('tipo', 'pendiente');
    $('#MCobrar').modal('show');
  });

  $(document).on('click', '.bCancelarVentaPen', async function () {
    const idVenta = $(this).attr('attrID');

    Swal.fire({
      title: '¿Estás seguro de cancelar la venta?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cerrar!',
      confirmButtonText: '¡Si, cancelar!'
    }).then((result) => {
      if (result.value) {
        const data = `metodo=modificar&accion=ventas&idVenta=${idVenta}&regresarInventario=1`

        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function () {
            $("#carga").show();
          }
        }).done(function (res) {
          if (String(res ?? '').trim() == 'Correcto') {
            Swal.fire({
              icon: 'success',
              title: 'Correcto',
              text: 'Venta cancelada correctamente',
            });

            tablaVentasPendientes();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al cancelar la venta.',
            });

            console.log(String(res ?? '').trim());
          }
        }).fail(function () {
          console.log("Error ajax");
        }).always(function () {
          $("#carga").hide();
        });
      }
    });
  });

  $(document).on('click', '.bQuitarProducto', function () {
    var btn = $(this);
    Swal.fire({
      title: '¿Estás seguro de quitar el producto de la venta?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, quitar!'
    }).then((result) => {
      if (result.value) {
        const data = "metodo=eliminar&accion=caja&tipo=quitar&id=" + btn.attr('attrID');

        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function () {
            $("#carga").show();
          }
        }).done(function (res) {
          if (String(res ?? '').trim() == 'Correcto') {
            Swal.fire({
              icon: 'success',
              title: 'Correcto',
              text: 'El producto ha sido eliminado de la venta correctamente',
            });

            tablaVentasPendientes();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al quitar el producto.',
            });

            console.log(String(res ?? '').trim());
          }
        }).fail(function () {
          console.log("Error ajax");
        }).always(function () {
          $("#carga").hide();
        });
      }
    });
  });

  $(document).on('click', '.bAgregarProductoMas', function () {
    var btn = $(this);
    const data = "metodo=modificar&accion=caja&tipo=agregar&id=" + btn.attr('attrID') + "&codigo=" + String($("#codigoProdMas").val() ?? '').trim() + "&cantidad=" + $("#cantidadProdMas").val() + "&precio=" + $("#precioProdMas").val() + "&descuento=" + $("#descuentoProdMas").val();

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $("#carga").show();
      }
    }).done(function (res) {
      if (String(res ?? '').trim() == 'Correcto') {
        Swal.fire({
          icon: 'success',
          title: 'Correcto',
          text: 'El producto ha sido agregado correctamente',
        });

        tablaVentasPendientes();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Error inesperado al agregar el producto.',
        });

        console.log(String(res ?? '').trim());
      }
    }).fail(function () {
      console.log("Error ajax");
    }).always(function () {
      $("#carga").hide();
    });
  });*/

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>Buscar Cliente Cobrar>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bBuscarClienteCobrar', function () {
    $("#MBuscarCliente").modal('show');
  });

  $(document).on('hidden.bs.modal', '#MBuscarCliente', function () {
    if ($("#modalCaja").is(':visible')) {
      $('#MCobrar').modal('show');
    }
  });

  $(document).on('click', '#bRecargarBusquedaClientes', function () {
    tablaBuscarClientes();
  });

  $(document).on('click', '#bQuitarClienteCobrar', function (event) {
    $("#clienteCobrar").val("");
    $("#clienteCobrar").attr('attrID', '');

    $("#bAgregarClienteBusN").html('Agregar un cliente <i class="fas fa-plus"></i>');
    $("#bAgregarClienteBusN").attr('attrID', '');
    $("#bAgregarClienteBusN").attr('attrDireccion', '');
  });

  $(document).on('click', '#tablaBuscarClientes tbody tr', function () {
    $("#clienteCobrar").val($(this).children('td:eq(0)').children('span:eq(0)').text());
    $("#clienteCobrar").attr('attrID', $(this).attr('id'));
    $('#tablaBuscarClientes tr').removeClass('activa');
    $(this).addClass('activa');
    $("#MBuscarCliente").modal('hide');
  });

  $(document).on('shown.bs.modal', '#MBuscarCliente', function () {
    $(this).find(".buscadorMyDataTable[tabla='tablaBuscarClientes']").focus();
  });

  $(document).on('click', '#bDescuentoGeneral', function () {
    tipoDescuento = 1;
    const total = parseFloat($("#subtotalCaja").text().replace('$', '').replace(searchRegExp, ''));
    const descuento = parseFloat($("#mosDesGeP").text().replace('%', '').replace(searchRegExp, ''));

    $('#descripcionDescuento').text("Total");
    $('#totalDescuento').text(total);

    porcentajeDescuentoCaja.value = '';
    porcentajeDescuentoCaja.value = descuento.toString();

    $('#porcentajeDescuentoCaja').trigger('change');

    $("#MDescuento").modal('show');
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //:::::::::::::Tipo de Caja::::::::::::::::::::::::
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bCambiarTouch', function () {
    if ($(this).prop('checked')) {
      localStorage.setItem("cajaTouch", "true");
    } else {
      localStorage.setItem("cajaTouch", "false");
    }

    setTimeout(function () {
      cambiarCaja();
    }, 300);
  });

  cambiarCaja();

  $(document).on('click', '.prodTienda', function () {
    $("#carga").show();

    const $this = $(this);
    var idProd = $this.attr('attrID');
    var codigo = $this.attr('attrCodigo');
    var descripcion = $this.find('div p span:eq(0)').text();
    var precio = parseFloat($this.attr('attrPrecio')) || 0;
    var mayoreo = $this.attr('attrPrecioMayoreo');
    var claseProd = $this.attr('attrClase');
    var existencia = $this.attr('attrExistencia');

    // Parsear el JSON de impuestos que enviamos desde el PHP
    var listaImpuestos = JSON.parse($this.attr('attrImpuestos') || "[]");

    const calcularImpuestosFila = (subtotalFila, cant) => {
      let verImpuestos = '';
      let totalImpuestos = 0;

      listaImpuestos.forEach((impuesto) => {
        let impuestoFila = 0;
        let valor = parseFloat(impuesto.Porcentaje) || 0;
        let factor = (impuesto.Tipo_Factor || '').toUpperCase();
        let clase = (impuesto.Clase || '').toUpperCase();

        // 1. Lógica de Tasa, Cuota o Exento
        if (factor === 'EXENTO') {
          impuestoFila = 0;
        } else if (factor === 'CUOTA') {
          impuestoFila = cant * valor;
        } else {
          impuestoFila = subtotalFila * (valor / 100);
        }

        // 2. Lógica de Trasladado o Retenido (Suma o Resta)
        if (clase === 'RETENCION' || clase === 'RETENIDO') {
          totalImpuestos -= impuestoFila;
        } else {
          totalImpuestos += impuestoFila;
        }

        verImpuestos += `<p class="m-0" attrID="${impuesto.FK_Impuesto}" clave="${impuesto.Clave || ''}">
          <b>${impuesto.Clase}</b> 
          <span>${impuesto.Nombre}</span> 
          <span class="valor ${factor === 'CUOTA' ? 'dinero' : 'porcentaje'}">${impuesto.Porcentaje}</span> 
          (<span class="dinero">${impuestoFila}</span>) 
          <b>${impuesto.Tipo_Factor}</b>
        </p>`;
      });

      return { html: verImpuestos, total: totalImpuestos };
    };

    var promocionTouch = null;
    try { promocionTouch = JSON.parse($this.attr('attrPromocion') || 'null'); } catch (e) { promocionTouch = null; }

    if (claseProd === "Granel") {
      var resImp = calcularImpuestosFila(precio, 1);
      var precioFinalConImpuestos = precio + resImp.total;

      $("#datosGranel").attr({
        'attrID': idProd,
        'attrCodigo': codigo,
        'attrExistencia': existencia,
        'attrPrecio': precio,
        'attrImpuestos': JSON.stringify(mapearImpuestosTouch(listaImpuestos)),
        'attrPromocion': JSON.stringify(promocionTouch)
      });

      $("#datosGranel").html(`
        <h4 class="text-center">${descripcion}</h4>
        <h5 class="text-center"><b>Precio Unitario:</b> <span class="dinero">${precio}</span></h5>
        <div class="text-center">
          ${resImp.html}
        </div>
      `);

      importeGranel.value = '';
      importeGranel.value = precioFinalConImpuestos.toString();

      $('#importeGranel').attr('attrMayoreo', mayoreo);
      $("#MGranel").modal('show');

      moneda();
    } else {
      crearFilaProducto({
        id: idProd,
        codigo: codigo,
        descripcion: descripcion,
        precio: precio,
        precioMayoreo: mayoreo,
        existencia: existencia,
        impuestos: mapearImpuestosTouch(listaImpuestos),
        promocion: promocionTouch,
        cantidad: 1
      });
    }

    $("#MBuscarProd").modal('hide');
    $("#carga").hide();
  });

  $(document).on('click', '.claTienda', function () {
    verProdTienda($(this).attr('attrID'));
  });

  $(document).on('click', '#bRegresarTienda', function (event) {
    verClaTienda();
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>Clientes a Domicilio>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('shown.bs.modal', '#modalClienteBus', function () {
    $("#telefonoClienteBus").focus();
  });

  $('#formCliente').validate({
    rules: {
      telefonoClienteBus: {
        required: true
      },
      nombreClienteBus: {
        required: true
      },
      calleClienteBus: {
        required: true
      },
      noExteriorClienteBus: {
        required: true
      }
    },
    messages: {
      telefonoClienteBus: {
        required: "El teléfono es requerido."
      },
      nombreClienteBus: {
        required: "El nombre es requerido."
      },
      calleClienteBus: {
        required: "La calle es requerida."
      },
      noExteriorClienteBus: {
        required: "El número exterior es requerido."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      var data = "metodo=modificar&accion=caja&tipo=cliente&telefono=" + String($("#telefonoClienteBus").val() ?? '').trim() + "&nombre=" + String($("#nombreClienteBus").val() ?? '').trim() + "&calle=" + String($("#calleClienteBus").val() ?? '').trim() + "&noExterior=" + String($("#noExteriorClienteBus").val() ?? '').trim() + "&noInterior=" + String($("#noInteriorClienteBus").val() ?? '').trim() + "&cp=" + String($("#cpClienteBus").val() ?? '').trim() + "&colonia=" + String($("#coloniaClienteBus").val() ?? '').trim() + "&ciudad=" + String($("#ciudadClienteBus").val() ?? '').trim() + "&estado=" + String($("#estadoClienteBus").val() ?? '').trim() + "&pais=" + String($("#paisClienteBus").val() ?? '').trim() + "&detalles=" + String($("#detallesClienteBus").val() ?? '').trim() + "&idCliente=" + ($("#nombreClienteBus").attr('attrID') || '') + "&idDomicilio=" + ($("#tituloDomiBus").attr('attrID') || '');

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          var separa = String(res ?? '').trim().split('~');

          if (separa[0] == "Correcto") {
            var direccion = String($("#calleClienteBus").val() ?? '').trim() + ' #' + String($("#noExteriorClienteBus").val() ?? '').trim();

            if (String($("#noInteriorClienteBus").val() ?? '').trim() != '') {
              direccion += ' int.' + String($("#noInteriorClienteBus").val() ?? '').trim();
            }

            if (String($("#cpClienteBus").val() ?? '').trim() != '') {
              direccion += ', C.P. ' + String($("#cpClienteBus").val() ?? '').trim();
            }

            if (String($("#coloniaClienteBus").val() ?? '').trim() != '') {
              direccion += ' Col. ' + String($("#coloniaClienteBus").val() ?? '').trim();
            }

            if (String($("#detallesClienteBus").val() ?? '').trim() != '') {
              direccion += ' Detalles: ' + String($("#detallesClienteBus").val() ?? '').trim();
            }

            $("#bAgregarClienteBusN").html('<span>' + String($("#nombreClienteBus").val() ?? '').trim() + '</span> <i class="fas fa-times"></i><br><span style="font-size: 11px;">' + direccion + '</span>');
            $("#bAgregarClienteBusN").attr('attrID', separa[1]);
            $("#bAgregarClienteBusN").attr('attrDireccion', separa[2]);

            $(".bQuitarTel").trigger('click');
            $(".bQuitarNom").trigger('click');

            document.getElementById('formCliente').reset();
            $("#modalClienteBus").modal('hide');
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al seleccionar el cliente.'
            });

            console.log(String(res ?? '').trim());
          }
        })
        .fail(function () {
          console.log("Error ajax");
        })
        .always(function () {
          $("#carga").hide();
        });
    }
  });

  $(document).on('click', '#bAgregarClienteBusN', function () {
    if ($(this).attr('attrID') != undefined && $(this).attr('attrID') != '') {
      $(this).html('Agregar un cliente <i class="fas fa-plus"></i>');
      $(this).attr('attrID', '');
      $(this).attr('attrDireccion', '');
    } else {
      $("#modalClienteBus").modal('show');
    }
  });

  $(document).on('focus keyup', '#telefonoClienteBus', function () {
    if (String($(this).val() ?? '').trim() != "" && $(this).attr('readonly') == undefined) {
      var data = "metodo=detalles&accion=caja&tipo=buscarTelefono&buscar=" + $(this).val();

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data
      })
        .done(function (res) {
          if (String(res ?? '').trim() != "") {
            $("#telefonosClientesBus").removeClass('oculto');
          } else {
            $("#telefonosClientesBus").addClass('oculto');
          }

          $("#telefonosClientesBus").html(String(res ?? '').trim());
        })
        .fail(function () {
          console.log("Error ajax");
        });
    } else {
      $("#telefonosClientesBus").addClass('oculto');
    }
  });

  $(document).on('focusout', '#telefonoClienteBus', function () {
    setTimeout(function () {
      $("#telefonosClientesBus").addClass('oculto');
      $("#nombresClientesBus").addClass('oculto');
    }, 200);
  });

  $(document).on('focusout', '#nombreClienteBus', function () {
    setTimeout(function () {
      $("#nombresClientesBus").addClass('oculto');
      $("#telefonosClientesBus").addClass('oculto');
    }, 200);
  });

  $(document).on('click', '.itemTelefonoBus', function () {
    $("#telefonoClienteBus").prop('readonly', true);
    $("#nombreClienteBus").prop('readonly', true);
    $("#telefonoClienteBus").val($(this).children('div:eq(0)').children('span:eq(0)').text());
    $("#nombreClienteBus").val($(this).children('div:eq(0)').children('span:eq(1)').text());
    $("#nombreClienteBus").attr('attrID', $(this).attr('attrID'));

    if ($("#telefonoClienteBus").parent().children('button.bQuitarTel').length == 0) {
      $("#telefonoClienteBus").parent().append('<button type="button" class="btn bQuitarLista bQuitarTel"><i class="fas fa-times"></i></button>');
    }

    if ($("#nombreClienteBus").parent().children('button.bQuitarNom').length == 0) {
      $("#nombreClienteBus").parent().append('<button type="button" class="btn bQuitarLista bQuitarNom"><i class="fas fa-times"></i></button>');
    }

    domicilios($(this).attr('attrID'));
  });

  $(document).on('click', '.bQuitarTel', function () {
    $("#telefonoClienteBus").prop('readonly', false);
    $("#nombreClienteBus").prop('readonly', false);
    $("#telefonoClienteBus").val("");
    $("#nombreClienteBus").val("");
    $("#tituloDomiBus").attr('attrID', '');
    $("#nombreClienteBus").attr('attrID', '');
    $("#seleccionDomicilioBus").addClass('oculto');

    document.getElementById('formCliente').reset();
    $(this).remove();
    $("#nombreClienteBus").parent().children('button.bQuitarNom').remove();
  });

  $(document).on('focus keyup', '#nombreClienteBus', function () {
    if (String($(this).val() ?? '').trim() != "" && $(this).attr('readonly') == undefined) {
      var data = "metodo=detalles&accion=caja&tipo=buscarNombre&buscar=" + $(this).val();

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data
      })
        .done(function (res) {
          if (String(res ?? '').trim() != "") {
            $("#nombresClientesBus").removeClass('oculto');
          } else {
            $("#nombresClientesBus").addClass('oculto');
          }

          $("#nombresClientesBus").html(String(res ?? '').trim());
        })
        .fail(function () {
          console.log("Error ajax");
        });
    } else {
      $("#nombresClientesBus").addClass('oculto');
    }
  });

  $(document).on('focusout', '#nombreCliente', function () {
    setTimeout(function () {
      $("#nombresClientes").addClass('oculto');
    }, 200);
  });

  $(document).on('click', '.itemNombreBus', function () {
    $("#nombreClienteBus").prop('readonly', true);
    $("#telefonoClienteBus").prop('readonly', true);
    $("#nombreClienteBus").val($(this).children('div:eq(0)').children('span:eq(0)').text());
    $("#nombreClienteBus").attr('attrID', $(this).attr('attrID'));
    $("#telefonoClienteBus").val($(this).children('div:eq(0)').children('span:eq(1)').text());

    if ($("#nombreClienteBus").parent().children('button.bQuitarNom').length == 0) {
      $("#nombreClienteBus").parent().append('<button type="button" class="btn bQuitarLista bQuitarNom"><i class="fas fa-times"></i></button>');
    }

    if ($("#telefonoClienteBus").parent().children('button.bQuitarTel').length == 0) {
      $("#telefonoClienteBus").parent().append('<button type="button" class="btn bQuitarLista bQuitarTel"><i class="fas fa-times"></i></button>');
    }

    domicilios($(this).attr('attrID'));
  });

  $(document).on('click', '.bQuitarNom', function () {
    $("#nombreClienteBus").prop('readonly', false);
    $("#telefonoClienteBus").prop('readonly', false);
    $("#nombreClienteBus").val("");
    $("#telefonoClienteBus").val("");
    $("#tituloDomiBus").attr('attrID', '');
    $("#nombreClienteBus").attr('attrID', '');
    $("#seleccionDomicilioBus").addClass('oculto');

    document.getElementById('formCliente').reset();
    $(this).remove();
    $("#telefonoClienteBus").parent().children('button.bQuitarTel').remove();
  });

  $(document).on('change', '#domicilioClienteBus', function () {
    var id = $(this).val();
    var data = "metodo=detalles&accion=caja&tipo=domicilio&id=" + id + "&cliente=" + $("#nombreClienteBus").attr('attrID');

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data
    })
      .done(function (res) {
        //console.log(String(res ?? '').trim());
        var datos = JSON.parse(String(res ?? '').trim());

        $("#tituloDomiBus").attr('attrID', id);
        $("#calleClienteBus").val(datos.Calle);
        $("#noExteriorClienteBus").val(datos.No_Exterior);
        $("#noInteriorClienteBus").val(datos.No_Interior);
        $("#cpClienteBus").val(datos.CP);
        $("#coloniaClienteBus").val(datos.Colonia);
        $("#ciudadClienteBus").val(datos.Ciudad);
        $("#estadoClienteBus").val(datos.Estado);
        $("#paisClienteBus").val(datos.Pais);
        $("#detallesClienteBus").val(datos.Detalles);
      })
      .fail(function () {
        console.log("Error ajax");
      });
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>Cambiar precio>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 
  // nth-child y no :eq(): en un selector delegado :eq() solo toma la celda del primer renglón
  $(document).on('dblclick', '#tablaCaja tbody tr > td:nth-child(3)', function () {
    if ($(this).children('input').length) return;
    var fila = $(this).parent();

    if (filaTienePromocionActiva(fila)) {
      Swal.fire({
        icon: 'info',
        title: 'Promoción activa',
        text: 'Este producto ya tiene una promoción aplicada, no se puede editar el precio manualmente.'
      });
      return;
    }

    var precio = $(this).children('span.dinero').text().replace('$', '').replaceAll(',', '');
    var mayoreo = $(this).children('span.dinero').attr('attrMayoreo');
    $(this).html('<input type="number" id="precioProdCaja" value="' + precio + '" attrMayoreo="' + mayoreo + '" style="padding: 2px 0px; text-align: center; border: none; z-index: 1; -webkit-appearance: none;" />');
    $("#precioProdCaja").focus();
    $("#precioProdCaja").val('');
    $("#precioProdCaja").val(precio);
  });

  $(document).on('keypress', '#precioProdCaja', function (e) {
    if (e.which == 13) {
      e.preventDefault();
      $(this).blur();
    }
  });

  $(document).on('focusout', '#precioProdCaja', function () {
    let fila = $(this).parent().parent();
    let precio = parseFloat($(this).val()) || 0;
    let cantidadActual = parseFloat(fila.children('td:eq(3)').children('span.cantidad').text().replace(',', '')) || 0;

    fila.attr('attrPrecioBase', precio);
    fila.attr('attrUsaMayoreo', '0');
    recalcularFilaProducto(fila, cantidadActual);

    $("#barCodeV").focus();
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>Cambiar cantidad>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  $(document).on('dblclick', '#tablaCaja tbody tr > td:nth-child(4)', function () {
    if ($(this).children('input').length) return;
    var cantidad = $(this).children('span.cantidad').text().replace(',', '');
    $(this).html('<input type="number" id="cantidadProdCaja" min="0" step="any" value="' + cantidad + '" attrAnterior="' + cantidad + '" style="padding: 2px 0px; width: 70px; text-align: center; border: none; z-index: 1; -webkit-appearance: none;" />');
    $("#cantidadProdCaja").focus().select();
  });

  $(document).on('keypress', '#cantidadProdCaja', function (e) {
    if (e.which == 13) {
      e.preventDefault();
      $(this).blur();
    }
  });

  $(document).on('focusout', '#cantidadProdCaja', function () {
    let valor = parseFloat($(this).val());
    // Vacío o inválido conserva la cantidad anterior; 0 elimina el renglón (igual que la tecla -)
    if (isNaN(valor)) valor = parseFloat($(this).attr('attrAnterior'));

    recalcularFilaProducto($(this).closest('tr'), valor);
    $("#barCodeV").focus();
  });


  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>BOTONES TOUCH>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  $(document).on('click', '.bSumarCajaTd', function () {
    $('#tablaCaja tr').removeClass('activa');
    $(this).closest('tr').addClass('activa');
    // simular tecla +
    const e = $.Event('keydown', { key: '+' });
    $(document).trigger(e);
  });

  $(document).on('click', '.bRestarCajaTd', function () {
    $('#tablaCaja tr').removeClass('activa');
    $(this).closest('tr').addClass('activa');
    // simular tecla -
    const e = $.Event('keydown', { key: '-' });
    $(document).trigger(e);
  });
});