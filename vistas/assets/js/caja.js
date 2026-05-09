const audio1 = new Audio('vistas/assets/media/sounds/addBip.mp3');
const audio2 = new Audio('vistas/assets/media/sounds/notBip.mp3');
let tipoDescuento = 0;
let procesandoCobro = false;

function display() {
  const today = new Date()

  const padTo2Digits = (num) => num.toString().padStart(2, '0')

  const month = padTo2Digits(today.getMonth() + 1)
  const day = padTo2Digits(today.getDate())
  const year = today.getFullYear()

  const hour = padTo2Digits(today.getHours() > 12 ? today.getHours() - 12 : today.getHours())
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
    $("#datosGranel").html(`
      <h4 class="text-center">` + fila.children('td:eq(0)').text() + `</h4>
      <h5 class="text-center">
        <b>Precio Unitario:</b> <span class="dinero">` + precio + `</span>
      </h5>
      <div class="text-center">
        ` + verImpuestos + `
      </div>
    `);
    $("#importeGranel").val(precio + totalImpuestos);
    $('#importeGranel').attr('attrMayoreo', mayoreo);
    $("#MGranel").modal('show');

    moneda();
  } else {
    $("#tablaCaja").children('tbody').children('tr').removeClass('activa');
    if ($("#tablaCaja").children('tbody').children('tr[attrID=' + fila.attr('id') + ']').length > 0) {
      var filaCaja = $("#tablaCaja").children('tbody').children('tr[attrID=' + fila.attr('id') + ']');
      var cantidad = parseFloat(filaCaja.children('td:eq(3)').children('span.cantidad').text().replace(',', '')) + 1;
      var descuento = parseFloat(filaCaja.children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(',', '')) || 0;
      var desDinero = ((precio * cantidad) * (descuento / 100));

      var descuHtml = '<span class="porcentaje">0</span> (<span class="dinero">0</span>)';
      if (descuento > 0) {
        descuHtml = '<span class="porcentaje">' + descuento + '</span> (<span class="dinero">' + desDinero + '</span>)';
      }

      let subTotal = ((precio * cantidad) - desDinero);

      let verImpuestos = '';
      let totalImpuestos = 0;
      impuestos.forEach((impuesto) => {
        let impuestoFila = 0;
        if (impuesto.factor.toUpperCase() === 'CUOTA') {
          impuestoFila = cantidad * impuesto.valor;
        } else {
          impuestoFila = subTotal * (impuesto.valor / 100);
        }

        if (impuesto.clase.toUpperCase() === 'RETENCION' || impuesto.clase.toUpperCase() === 'RETENIDO') {
          totalImpuestos -= impuestoFila;
        } else {
          totalImpuestos += impuestoFila;
        }
        verImpuestos += '<p class="m-0" attrID="' + impuesto.id + '" clave="' + impuesto.clave + '"><b>' + impuesto.clase + '</b> <span>' + impuesto.nombre + '</span> <span class="valor ' + (impuesto.factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.valor + '</span> (<span class="dinero">' + impuestoFila + '</span>) - <b>' + impuesto.factor + '</b></p>';
      });

      let total = subTotal + totalImpuestos;

      filaCaja.addClass('activa');
      filaCaja.html(`
        <td>`+ fila.children('td:eq(1)').text() + `</td>
        <td>`+ fila.children('td:eq(0)').text() + `</td>
        <td><span class="dinero" attrMayoreo="` + mayoreo + `">` + precio + `</span></td>
        <td><span class="cantidad">`+ cantidad + `</span></td>
        <td>`+ descuHtml + `</td>
        <td><b>SUB: <span class="dinero">` + subTotal + `</span></b><br>` + verImpuestos + `</td>
        <td><span class="dinero">`+ total + `</span></td>
        <td><span class="cantidad">`+ existencia + `</span></td>
      `);
    } else {
      let verImpuestos = '';
      let totalImpuestos = 0;
      let cantidad = 1;

      impuestos.forEach((impuesto) => {
        let impuestoFila = 0;
        if (impuesto.factor.toUpperCase() === 'CUOTA') {
          impuestoFila = cantidad * impuesto.valor;
        } else {
          impuestoFila = precio * (impuesto.valor / 100);
        }

        if (impuesto.clase.toUpperCase() === 'RETENCION' || impuesto.clase.toUpperCase() === 'RETENIDO') {
          totalImpuestos -= impuestoFila;
        } else {
          totalImpuestos += impuestoFila;
        }
        verImpuestos += '<p class="m-0" attrID="' + impuesto.id + '" clave="' + impuesto.clave + '"><b>' + impuesto.clase + '</b> <span>' + impuesto.nombre + '</span> <span class="valor ' + (impuesto.factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.valor + '</span> (<span class="dinero">' + impuestoFila + '</span>) - <b>' + impuesto.factor + '</b></p>';
      });

      let total = precio + totalImpuestos;

      $("#tablaCaja").children('tbody').prepend(`<tr attrID="${fila.attr('id')}" class="activa">
        <td>` + fila.children('td:eq(1)').text() + `</td>
        <td>` + fila.children('td:eq(0)').text() + `</td>
        <td><span class="dinero" attrMayoreo="` + mayoreo + `">` + precio + `</span></td>
        <td><span class="cantidad">1</span></td>
        <td><span class="porcentaje">0</span> (<span class="dinero">0</span>)</td>
        <td><b>SUB: <span class="dinero">` + precio + `</span></b><br>` + verImpuestos + `</td>
        <td><span class="dinero">` + total + `</span></td>
        <td><span class="cantidad">` + existencia + `</span></td>
      </tr>`);
    }

    audio1.play();
    totalCaja();
  }

  $("#MBuscarProd").modal('hide');
  $("#carga").hide();
}

function cerrarCaja(imprimir) {
  var data = "metodo=modificar&accion=caja&tipo=cerrar&idCaja=" + $.trim($('#modalCaja').attr('attrcaja')) + "&monto=" + $("#montoCorte").val() + "&fecha_cierre=" + $.trim($("#fechaCierre").attr('fecha'));

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $("#carga").show();
    }
  })
    .done(function (res) {
      var separa = $.trim(res).split('~');

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

        console.log($.trim(res));
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
    $("#mosBotonesCajaUP").css("margin-top", "-31px");

    $("#bBuscarProd").addClass('oculto');
    $("#bMayoreo").addClass('oculto');

    $("#mostrarTablaProdCaja").css("height", "51vh");
    $("#mosTiketsCaja").removeClass("col-12");
    $("#mosTiketsCaja").addClass("col-md-6");

    $("#mosTiketsCaja").parent().prepend(`<div class="col-md-6" style="position: relative;">
      <div class="row" id="mosProdCajaTienda" style="padding: 30px 15px; background-color: #EEE; height: 56vh; overflow-x: hidden; overflow-y: auto;">

      </div>
      <button type="button" class="btn btn-dark btn-lg" id="bRegresarTienda" style="position: absolute; right: 0; bottom: -30px;"><i class="fa-solid fa-backward"></i></button>
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

    verClaTienda();
  } else {
    $("#bCambiarTouch").prop('checked', false);

    $("#formAgreProd").removeClass('oculto');
    $("#tituloCaja").css("position", "relative");
    $("#tituloCaja").css("margin-top", "auto");
    $("#mosBotonesCajaUP").css("margin-top", "auto");

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
}

function verProdTienda(clasificacion) {
  var data = "metodo=detalles&accion=caja&tipo=prodTienda&idCaja=" + $.trim($('#modalCaja').attr('attrcaja')) + "&clasificacion=" + clasificacion;

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $("#carga").show();
    }
  })
    .done(function (res) {
      //console.log($.trim(res));

      $("#mosProdCajaTienda").html($.trim(res));
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
      //console.log($.trim(res));

      $("#mosProdCajaTienda").html($.trim(res));
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
      //console.log($.trim(res));
      $('#domicilioClienteBus').html($.trim(res));
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
      const cantidad = parseFloat($("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(3)').children('span.cantidad').text().replace(',', ''));
      const descuento = parseFloat($("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(',', ''));
      const precio = parseFloat($("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(2)').children('span.dinero').text().replace('$', '').replace(',', ''));
      const impuestos = $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(5)');

      let descuHtml = '';
      if (evt.key === "+") {
        var desDinero = ((precio * (cantidad + 1)) * (descuento / 100));

        descuHtml = '<span class="porcentaje">0</span>(<span class="dinero">0</span>)';
        if (descuento > 0) {
          descuHtml = '<span class="porcentaje">' + descuento + '</span>(<span class="dinero">' + desDinero + '</span>)';
        }

        var subtotal = (precio * (cantidad + 1)) - desDinero;
        let totalImpuestos = 0;
        impuestos.children('p').each(function (index, el) {
          let clase = $(this).children('b:first').text().trim().toUpperCase();
          let factor = $(this).children('b:last').text().trim().toUpperCase();

          let valor = parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replace(',', '')) || 0;
          let impuestoFila = 0;

          // --- Lógica de Tasa o Cuota ---
          if (factor === 'EXENTO') {
            impuestoFila = 0; // Por definición, no genera importe
          } else if (factor === 'CUOTA') {
            impuestoFila = cantidad * valor;
          } else {
            impuestoFila = (subtotal * (valor / 100));
          }

          $(this).children('span.dinero').html(impuestoFila.toFixed(2));

          // --- Lógica de Trasladado o Retenido ---
          if (clase === 'RETENCION' || clase === 'RETENIDO') {
            totalImpuestos -= impuestoFila;
          } else {
            totalImpuestos += impuestoFila;
          }
        });

        let total = subtotal + totalImpuestos;

        $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(3)').children('span.cantidad').html(cantidad + 1);
        $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(4)').html(descuHtml);
        $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(5)').children('b:eq(0)').children('span.dinero').html(subtotal);
        $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(6)').children('span.dinero').html(total);

        totalCaja();
      } else if (evt.key === "-") {
        if ((cantidad - 1) <= 0) {
          $("#bQuitarProducto").trigger('click');
        } else {
          var desDinero = ((precio * (cantidad - 1)) * (descuento / 100));

          descuHtml = '<span class="porcentaje">0</span>(<span class="dinero">0</span>)';
          if (descuento > 0) {
            descuHtml = '<span class="porcentaje">' + descuento + '</span>(<span class="dinero">' + desDinero + '</span>)';
          }

          var subtotal = (precio * (cantidad - 1)) - desDinero;
          let totalImpuestos = 0;
          impuestos.children('p').each(function (index, el) {
            let clase = $(this).children('b:first').text().trim().toUpperCase();
            let factor = $(this).children('b:last').text().trim().toUpperCase();

            let valor = parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replace(',', '')) || 0;
            let impuestoFila = 0;

            // --- Lógica de Tasa o Cuota ---
            if (factor === 'EXENTO') {
              impuestoFila = 0; // Por definición, no genera importe
            } else if (factor === 'CUOTA') {
              impuestoFila = cantidad * valor;
            } else {
              impuestoFila = (subtotal * (valor / 100));
            }

            $(this).children('span.dinero').html(impuestoFila);

            // --- Lógica de Trasladado o Retenido ---
            if (clase === 'RETENCION' || clase === 'RETENIDO') {
              totalImpuestos -= impuestoFila;
            } else {
              totalImpuestos += impuestoFila;
            }
          });

          let total = subtotal + totalImpuestos;

          $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(3)').children('span.cantidad').html(cantidad - 1);
          $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(4)').html(descuHtml);
          $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(5)').children('b:eq(0)').children('span.dinero').html(subtotal);
          $("#tablaCaja").children('tbody').children('tr.activa').children('td:eq(6)').children('span.dinero').html(total);
        }

        totalCaja();
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
        $('#totalCobrar').text(parseFloat(fila.children('td:eq(3)').text().replace('$', '').replace(searchRegExp, '')));
        $('#totalPagoCobrar').val(parseFloat(fila.children('td:eq(4)').text().replace('$', '').replace(searchRegExp, '')));
        $('#totalPagoCobrar').attr('min', parseFloat(fila.children('td:eq(3)').text().replace('$', '').replace(searchRegExp, '')));
        $('#totalCambio').text(fila.children('td:eq(5)').text());
        $('#detallesCobrar').val($.trim(fila.children('td:eq(6)').children('p:eq(0)').text()));

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
        if ($.trim(res) === "No") {
          $('#bMenuCajas').trigger('click');
        } else {
          const resJson = JSON.parse($.trim(res));
          $('#tituloCaja').html(resJson.Nombre);
          $('#modalCaja').attr('attrSucursal', resJson.FK_Sucursal);
          $('#modalCaja').attr('attrCaja', resJson.ID_Caja);
          $('#bDejarCaja').attr('attrID', resJson.ID_Caja);
          $('#bCorteCaja').attr('attrID', resJson.ID_Caja);
          $('#modalCaja').modal('show');

          document.documentElement.requestFullscreen();

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
    var regex = new RegExp("^[a-zA-Z0-9]+$");
    var key = event.key;
    if (!regex.test(key)) {
      event.preventDefault();
      return false;
    }
  });

  $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function () {
    if (document.webkitFullscreenElement == null) {
      modales.forEach(modal => {
        $(modal).modal('hide');
      });

      swal.close();
      $('#bRecargar').trigger('click');
    }
  });

  $(document).on('click', '#bCerrarVenCaja', function () {
    $("#modalCaja").modal('hide');
    document.exitFullscreen();
  });

  $(document).on('submit', '#formAgreProd', function (event) {
    event.preventDefault();
    const data = `metodo=consultar&accion=caja&tipo=agregarProducto&codigo=${$.trim($('#barCodeV').val())}`

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data,
      beforeSend: function () {
        $("#carga").show();
      },
    })
      .done(function (res) {
        //console.log($.trim(res));
        $("#barCodeV").val("");

        if ($.trim(res) === "No encontrado") {
          $("#noEncontrado").show();

          setTimeout(function () {
            $("#noEncontrado").hide();
          }, 1000);

          audio2.play();
        } else {
          var resA = JSON.parse($.trim(res));
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
                impuestoFila = (parseFloat(cantidad) || 0) * valor;
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
            $("#datosGranel").html(`
              <h4 class="text-center">` + resA.Descripcion + `</h4>
              <h5 class="text-center">
                <b>Precio Unitario:</b> <span class="dinero">` + precio + `</span>
              </h5>
              <div class="text-center">
                ` + verImpuestos + `
              </div>
            `);
            $("#importeGranel").val(total);
            $('#importeGranel').attr('attrMayoreo', mayoreo);
            $("#MGranel").modal('show');

            moneda();
          } else {
            $("#tablaCaja").children('tbody').children('tr').removeClass('activa');

            if ($("#tablaCaja").children('tbody').children('tr[attrID=' + resA.ID_Producto + ']').length > 0) {
              precio = $("#tablaCaja").children('tbody').children('tr[attrID=' + resA.ID_Producto + ']').children('td:eq(2)').text().replace('$', '').replace(searchRegExp, '');
              var cantidad = parseFloat($("#tablaCaja").children('tbody').children('tr[attrID=' + resA.ID_Producto + ']').children('td:eq(3)').children('span.cantidad').text().replace(',', ''));
              var descuento = parseFloat($("#tablaCaja").children('tbody').children('tr[attrID=' + resA.ID_Producto + ']').children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(',', ''));
              var desDinero = ((precio * (cantidad + 1)) * (descuento / 100));

              var descuHtml = '<span class="porcentaje">0</span>(<span class="dinero">0</span>)';
              if (descuento > 0) {
                descuHtml = '<span class="porcentaje">' + descuento + '</span>(<span class="dinero">' + desDinero + '</span>)';
              }

              let subtotal = (precio * (cantidad + 1)) - desDinero;

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
                  impuestoFila = (parseFloat(cantidad) || 0) * valor;
                } else {
                  impuestoFila = subtotal * (valor / 100);
                }

                // --- Lógica de Trasladado o Retenido para el acumulador ---
                if (clase === 'RETENCION' || clase === 'RETENIDO') {
                  totalImpuestos -= impuestoFila;
                } else {
                  totalImpuestos += impuestoFila;
                }

                verImpuestos += '<p class="m-0" attrID="' + impuesto.FK_Impuesto + '" clave="' + impuesto.Clave_CFDI + '"><b>' + impuesto.Clase + '</b> <span>' + impuesto.Nombre + '</span> <span class="valor ' + (impuesto.Tipo_Factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.Porcentaje + '</span> (<span class="dinero">' + impuestoFila + '</span>) <b>' + impuesto.Tipo_Factor + '</b></p>';
              });

              let total = subtotal + totalImpuestos;

              $("#tablaCaja").children('tbody').children('tr[attrID=' + resA.ID_Producto + ']').addClass('activa');

              $("#tablaCaja").children('tbody').children('tr[attrID=' + resA.ID_Producto + ']').html(`
                <td>`+ resA.Codigo + `</td>
                <td>`+ resA.Descripcion + `</td>
                <td><span class="dinero" attrMayoreo="` + mayoreo + `">` + precio + `</span></td>
                <td><span class="cantidad">`+ (cantidad + 1) + `</span></td>
                <td>`+ descuHtml + `</td>
                <td><b>SUB: <span class="dinero">`+ subtotal + `</span></b> ` + verImpuestos + `</td>
                <td><span class="dinero">`+ total + `</span></td>
                <td><span class="cantidad">`+ existencia + `</span></td>
              `);
            } else {
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
                  impuestoFila = (parseFloat(cantidad) || 0) * valor;
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

              $("#tablaCaja").children('tbody').prepend(`<tr attrID="` + resA.ID_Producto + `" class="activa">
                <td>`+ resA.Codigo + `</td>
                <td>`+ resA.Descripcion + `</td>
                <td><span class="dinero" attrMayoreo="` + mayoreo + `">` + precio + `</span></td>
                <td><span class="cantidad">1</span></td>
                <td><span class="porcentaje">0</span>(<span class="dinero">0</span>)</td>
                <td><b>SUB: <span class="dinero">`+ precio + `</span></b> ` + verImpuestos + `</td>
                <td><span class="dinero">`+ total + `</span></td>
                <td><span class="cantidad">`+ existencia + `</span></td>
              </tr>`);
            }

            audio1.play();
            totalCaja();
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
    const fila = $("#tablaCaja").children('tbody').children('tr.activa').index();
    $("#tablaCaja").children('tbody').children('tr.activa').remove();
    const eq = fila - 1 >= 0 ? fila - 1 : 0
    $("#tablaCaja").children('tbody').children(`tr:eq(${eq})`).addClass('activa');
    totalCaja();
  });

  $(document).on('click', '#tablaCaja tbody tr', function () {
    $('#tablaCaja tr').removeClass('activa');
    $(this).addClass('activa');
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>Granel>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('hidden.bs.modal', '#MGranel', function () {
    $("#cantidadGranel").val("1.00");
    $("#importeGranel").val("");
    var validator = $("#formGranel").validate();
    validator.resetForm();
  });

  $('#formGranel').validate({
    rules: {
      cantidadGranel: {
        required: true,
        min: 0.01
      },
      importeGranel: {
        required: true
      }
    },
    messages: {
      cantidadGranel: {
        required: "La cantidad es requerida",
        min: "La cantidad debe ser al menos 0.01"
      },
      importeGranel: {
        required: "El importe es requerido"
      }
    },
    submitHandler: function (form) {
      $("#carga").show();

      var mayoreo = $('#importeGranel').attr('attrMayoreo')
      var cantidadInput = parseFloat($("#cantidadGranel").val());
      var precio = parseFloat($("#datosGranel").children('h5').children('span.dinero').text().replace('$', '').replace(',', ''));
      let impuestos = $("#datosGranel").children('div:eq(0)');
      let totalInput = $("#importeGranel").val();
      $("#tablaCaja").children('tbody').children('tr').removeClass('activa');

      var clase = '';
      if (localStorage.getItem("cajaTouch") === "true") {
        clase = 'class="oculto"';
      }

      if ($("#tablaCaja").children('tbody').children('tr[attrID=' + $("#datosGranel").attr('attrID') + ']').length > 0) {
        var cantidad = parseFloat($("#tablaCaja").children('tbody').children('tr[attrID=' + $("#datosGranel").attr('attrID') + ']').children('td:eq(3)').children('span.cantidad').text().replace(',', ''));
        var descuento = parseFloat($("#tablaCaja").children('tbody').children('tr[attrID=' + $("#datosGranel").attr('attrID') + ']').children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(',', ''));
        var desDinero = ((precio * (cantidad + cantidadInput)) * (descuento / 100));

        var descuHtml = '<span class="porcentaje">0</span>(<span class="dinero">0</span>)';
        if (descuento > 0) {
          descuHtml = '<span class="porcentaje">' + descuento + '</span>(<span class="dinero">' + desDinero + '</span>)';
        }

        let subTotal = ((precio * (cantidad + cantidadInput)) - desDinero);

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
              impuestoFila = (parseFloat(cantidad) || 0) * valor;
            } else {
              impuestoFila = subTotal * (valor / 100);
            }

            $(this).children('span.dinero').text(impuestoFila.toFixed(2));

            // --- Lógica de Trasladado o Retenido ---
            if (clase === 'RETENCION' || clase === 'RETENIDO') {
              totalImpuestos -= impuestoFila;
            } else {
              totalImpuestos += impuestoFila;
            }
          });
        }

        let total = subTotal + totalImpuestos;

        $("#tablaCaja").children('tbody').children('tr[attrID=' + $("#datosGranel").attr('attrID') + ']').addClass('activa');

        $("#tablaCaja").children('tbody').children('tr[attrID=' + $("#datosGranel").attr('attrID') + ']').html(`
          <td ` + clase + `>` + $("#datosGranel").attr('attrCodigo') + `</td>
          <td>` + $("#datosGranel").children('h4').text() + `</td>
          <td><span class="dinero" attrMayoreo="` + mayoreo + `">` + precio + `</span></td>
          <td><span class="cantidad">`+ (cantidad + cantidadInput) + `</span></td>
          <td>` + descuHtml + `</td>
          <td><b>SUB: <span class="dinero">` + subTotal + `</span></b><br>` + impuestos.html() + `</td>
          <td><span class="dinero">`+ total + `</span></td>
          <td ` + clase + `><span class="cantidad">` + $("#datosGranel").attr('attrExistencia') + `</span></td>
        `);
      } else {
        $("#tablaCaja").children('tbody').prepend(`<tr attrID="` + $("#datosGranel").attr('attrID') + `" class="activa">
          <td `+ clase + `>` + $("#datosGranel").attr('attrCodigo') + `</td>
          <td>`+ $("#datosGranel").children('h4').text() + `</td>
          <td><span class="dinero" attrMayoreo="` + mayoreo + `">` + precio + `</span></td>
          <td><span class="cantidad">` + cantidadInput + `</span></td>
          <td><span class="porcentaje">0</span>(<span class="dinero">0</span>)</td>
          <td><b>SUB: <span class="dinero">` + (precio * cantidadInput) + `</span></b>` + impuestos.html() + `</td>
          <td><span class="dinero">` + totalInput + `</span></td>
          <td `+ clase + `><span class="cantidad">` + $("#datosGranel").attr('attrExistencia') + `</span></td>
        </tr>`);
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
    let cantidad = parseFloat($(this).val()) || 0;
    let subtotal = cantidad * precio;

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
        impuestoFila = subtotal * valorImpuesto;
      } else {
        impuestoFila = subtotal * (valorImpuesto / 100);
      }

      $(this).children('span:eq(2)').text(impuestoFila.toFixed(2));

      // --- Lógica de Trasladado o Retenido ---
      if (clase === 'RETENCION' || clase === 'RETENIDO') {
        totalImpuestos -= impuestoFila;
      } else {
        totalImpuestos += impuestoFila;
      }
    });

    let totalFinal = subtotal + totalImpuestos;
    $("#importeGranel").val(Math.round(totalFinal * 100) / 100);
    moneda();
  });

  $(document).on('input', '#importeGranel', function () {
    let precio = parseFloat($("#datosGranel").attr('attrPrecio')) || 0;
    let totalInput = parseFloat($(this).val()) || 0;

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

      $(this).children('span:eq(2)').text(impuestoFila.toFixed(2));
    });

    $("#cantidadGranel").val(Math.round(cantidad * 1000) / 1000);
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
    $("#cantidadIntVatios").val("");
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
        required: true,
        min: 0.01
      }
    },
    messages: {
      barCodeIntVatios: {
        required: "El código del producto es requerido"
      },
      cantidadIntVatios: {
        required: "La cantidad es requerida",
        min: "La cantidad debe ser al menos 0.01"
      }
    },
    submitHandler: function (form) {
      const data = `metodo=consultar&accion=caja&tipo=agregarProducto&codigo=${$.trim($('#barCodeIntVatios').val())}`

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data,
        beforeSend: function () {
          $("#carga").show();
        }
      }).done(function (response) {
        //console.log($.trim(response));
        if ($.trim(response) === "No encontrado") {
          $("#noEncontrado").show();

          setTimeout(function () {
            $("#noEncontrado").hide();
          }, 1000);

          audio2.play();
        } else {
          const res = JSON.parse($.trim(response));
          const cantidadInput = parseFloat($("#cantidadIntVatios").val()) || 0;

          if (res.Clase === "Pieza" && (cantidadInput % 1) > 0) {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El producto solo puede ser vendido en unidades o enteros, si deseas puedes configurarlo para venderlo en granel.',
              confirmButtonColor: '#3085d6',
              confirmButtonText: 'Aceptar',
            });
          } else {
            $("#tablaCaja").children('tbody').children('tr').removeClass('activa');

            let existencia = parseFloat(res.Existencia) || 0;
            let precio = parseFloat(res.Precio) || 0;
            let mayoreo = parseFloat(res.Precio_Mayoreo) || 0;
            let impuestos = res.Impuestos;

            var clase = '';
            if (localStorage.getItem("cajaTouch") === "true") {
              clase = 'class="oculto"';
            }

            if ($("#tablaCaja").children('tbody').children('tr[attrID=' + res.ID_Producto + ']').length > 0) {
              precio = $("#tablaCaja").children('tbody').children('tr[attrID=' + res.ID_Producto + ']').children('td:eq(2)').text().replace('$', '').replace(searchRegExp, '');
              const cantidad = parseFloat($("#tablaCaja").children('tbody').children('tr[attrID=' + res.ID_Producto + ']').children('td:eq(3)').children('span.cantidad').text().replace(',', ''));
              const descuento = parseFloat($("#tablaCaja").children('tbody').children('tr[attrID=' + res.ID_Producto + ']').children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(',', ''));
              var desDinero = ((precio * (cantidad + cantidadInput)) * (descuento / 100));

              var descuHtml = '<span class="porcentaje">0</span>(<span class="dinero">0</span>)';
              if (descuento > 0) {
                descuHtml = '<span class="porcentaje">' + descuento + '</span>(<span class="dinero">' + desDinero + '</span>)';
              }

              let subtotal = (precio * (cantidad + cantidadInput)) - desDinero;

              let verImpuestos = '';
              let totalImpuestos = 0;

              impuestos.forEach((impuesto) => {
                let impuestoFila = 0;
                let valor = parseFloat(impuesto.Porcentaje) || 0;
                let factor = (impuesto.Tipo_Factor || '').toUpperCase();
                let clase = (impuesto.Clase || '').toUpperCase();

                // --- Lógica de Tasa o Cuota ---
                if (factor === 'EXENTO') {
                  impuestoFila = 0; // Siempre cero
                } else if (factor === 'CUOTA') {
                  impuestoFila = (parseFloat(cantidad) || 0) * valor;
                } else {
                  impuestoFila = subtotal * (valor / 100);
                }

                // --- Lógica de Trasladado o Retenido para el acumulador ---
                if (clase === 'RETENCION' || clase === 'RETENIDO') {
                  totalImpuestos -= impuestoFila;
                } else {
                  totalImpuestos += impuestoFila;
                }

                verImpuestos += '<p class="m-0" attrID="' + impuesto.ID_Impuesto + '"><b>' + impuesto.Clase + '</b> <span>' + impuesto.Nombre + '</span> <span class="valor ' + (impuesto.Tipo_Factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.Porcentaje + '</span> (<span class="dinero">' + impuestoFila.toFixed(2) + '</span>) - <b>' + impuesto.Tipo_Factor + '</b></p>';
              });

              let total = subtotal + totalImpuestos;

              $("#tablaCaja").children('tbody').children('tr[attrID=' + res.ID_Producto + ']').addClass('activa');

              $("#tablaCaja").children('tbody').children('tr[attrID=' + res.ID_Producto + ']').html(`
                <td `+ clase + `>` + res.Codigo + `</td>
                <td>`+ res.Descripcion + `</td>
                <td><span class="dinero" attrMayoreo="${mayoreo}">` + precio + `</span></td>
                <td><span class="cantidad">`+ (cantidad + cantidadInput) + `</span></td>
                <td>`+ descuHtml + `</td>
                <td><b>SUB: <span class="dinero">`+ subtotal + `</span></b> ` + verImpuestos + `</td>
                <td><span class="dinero">`+ total + `</span></td>
                <td `+ clase + `><span class="cantidad">` + existencia + `</span></td>
              `);
            } else {
              let subtotal = precio * cantidadInput;

              let verImpuestos = '';
              let totalImpuestos = 0;

              impuestos.forEach((impuesto) => {
                let impuestoFila = 0;
                let valor = parseFloat(impuesto.Porcentaje) || 0;
                let factor = (impuesto.Tipo_Factor || '').toUpperCase();
                let clase = (impuesto.Clase || '').toUpperCase();

                // --- Lógica de Tasa o Cuota ---
                if (factor === 'EXENTO') {
                  impuestoFila = 0; // Siempre cero
                } else if (factor === 'CUOTA') {
                  impuestoFila = (parseFloat(cantidad) || 0) * valor;
                } else {
                  impuestoFila = subtotal * (valor / 100);
                }

                // --- Lógica de Trasladado o Retenido para el acumulador ---
                if (clase === 'RETENCION' || clase === 'RETENIDO') {
                  totalImpuestos -= impuestoFila;
                } else {
                  totalImpuestos += impuestoFila;
                }

                verImpuestos += '<p class="m-0" attrID="' + impuesto.ID_Impuesto + '" clave="' + impuesto.Clave_CFDI + '"><b>' + impuesto.Clase + '</b> <span>' + impuesto.Nombre + '</span> <span class="valor ' + (impuesto.Tipo_Factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.Porcentaje + '</span> (<span class="dinero">' + impuestoFila + '</span>) <b>' + impuesto.Tipo_Factor + '</b></p>';
              });

              let total = subtotal + totalImpuestos;

              $("#tablaCaja").children('tbody').prepend(`<tr attrID="` + res.ID_Producto + `" class="activa">
                <td `+ clase + `>` + res.Codigo + `</td>
                <td>`+ res.Descripcion + `</td>
                <td><span class="dinero" attrMayoreo="${mayoreo}">` + precio + `</span></td>
                <td><span class="cantidad">`+ cantidadInput + `</span></td>
                <td><span class="porcentaje">0</span>(<span class="dinero">0</span>)</td>
                <td><b>SUB: <span class="dinero">`+ subtotal + `</span></b> ` + verImpuestos + `</td>
                <td><span class="dinero">`+ total + `</span></td>
                <td `+ clase + `><span class="cantidad">` + existencia + `</span></td>
              </tr>`);
            }

            audio1.play();
            totalCaja();
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
    $("#cantidadProdComun").val("1.00");
    $("#precioProdComun").val("");
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
        required: true,
        min: 0.01
      },
      precioProdComun: {
        required: true,
        min: 0.01
      }
    },
    messages: {
      descripcionProdComun: {
        required: "La descripción del producto es requerida"
      },
      cantidadProdComun: {
        required: "La cantidad es requerida",
        min: "La cantidad debe ser al menos 0.01"
      },
      precioProdComun: {
        required: "El precio es requerido",
        min: "El precio debe ser al menos 0.01"
      }
    },
    submitHandler: function (form) {
      $("#carga").show();

      $("#tablaCaja").children('tbody').children('tr').removeClass('activa');
      var clase = '';
      if (localStorage.getItem("cajaTouch") === "true") {
        clase = 'class="oculto"';
      }

      $("#tablaCaja").children('tbody').prepend(`<tr attrID="0" class="activa">
        <td `+ clase + `>0</td>
        <td>`+ $.trim($("#descripcionProdComun").val()) + `</td>
        <td><span class="dinero" attrMayoreo="${$("#precioProdComun").val()}">` + $("#precioProdComun").val() + `</span></td>
        <td><span class="cantidad">`+ $("#cantidadProdComun").val() + `</span></td>
        <td><span class="porcentaje">0</span>(<span class="dinero">0</span>)</td>
        <td><b>SUB: <span class="dinero">`+ $("#precioProdComun").val() + `</span></b></td>
        <td><span class="dinero">`+ (parseFloat($("#precioProdComun").val()) * parseFloat($("#cantidadProdComun").val())) + `</span></td>
        <td `+ clase + `>Ilim</td>
      </tr>`);

      audio1.play();

      totalCaja();
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

    if (fila.length > 0) {
      const mayoreo = parseFloat(fila.children('td:eq(2)').children('span').attr('attrMayoreo'));

      if (mayoreo > 0) {
        fila.children('td:eq(2)').children('span').html(mayoreo);
        const total = (parseFloat(fila.children('td:eq(3)').text().replace(searchRegExp, '')) * parseFloat(fila.children('td:eq(2)').text().replace('$', '').replace(searchRegExp, ''))) - parseFloat(fila.children('td:eq(4)').children('span:eq(0)').text().replace('$', '').replace(searchRegExp, ''));
        fila.children('td:eq(5)').children('span').html(total);

        totalCaja();
      }
    }
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
    $("#montoEntrada").val("");
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
        required: true,
        min: 0.01
      },
      descripcionEntrada: {
        required: true
      }
    },
    messages: {
      montoEntrada: {
        required: "El monto es requerido.",
        min: "El monto debe ser al menos 0.01"
      },
      descripcionEntrada: {
        required: "La descripción es requerida."
      }
    },
    submitHandler: function (form) {
      var data = `metodo=insertar&accion=caja&tipo=movimiento&tipoMov=Entrada&cajaId=${$.trim($('#modalCaja').attr('attrCaja'))}&cantidad=${$('#montoEntrada').val()}&descripcion=${$("#descripcionEntrada").val()}`

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          if ($.trim(res) === 'Correcto') {
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

            console.log($.trim(res));
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
    $("#montoSalida").val("");
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
        required: true,
        min: 0.01
      },
      descripcionSalida: {
        required: true
      }
    },
    messages: {
      montoSalida: {
        required: "El monto es requerido",
        min: "El monto debe ser al menos 0.01"
      },
      descripcionSalida: {
        required: "La descripción es requerida."
      }
    },
    submitHandler: function (form) {
      const data = `metodo=insertar&accion=caja&tipo=movimiento&tipoMov=Salida&cajaId=${$.trim($('#modalCaja').attr('attrcaja'))}&cantidad=${$('#montoSalida').val()}&descripcion=${$("#descripcionSalida").val()}`;

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          if ($.trim(res) === 'Correcto') {
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

            console.log($.trim(res));
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
    $('#porcentajeDescuento').focus();
  });

  $(document).on('click', '#bHacerDescuento', function () {
    tipoDescuento = 0;
    const fila = $("#tablaCaja").children('tbody').children('tr.activa');

    if (fila.length > 0) {
      const descripcion = fila.children('td:eq(1)').text();
      const total = parseFloat(fila.children('td:eq(2)').text().replace('$', '').replace(searchRegExp, '')) * parseFloat(fila.children('td:eq(3)').text().replace('$', '').replace(searchRegExp, ''));
      const descuento = fila.children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(searchRegExp, '');

      $('#descripcionDescuento').text(descripcion);
      $('#totalDescuento').text(total);
      $('#porcentajeDescuento').val(descuento).trigger('change');

      $("#MDescuento").modal('show');
    }
  });

  $('#porcentajeDescuento').on('keyup change', function (event) {
    const totalDescuento = parseFloat($('#totalDescuento').text().replace(searchRegExp, '').replace('$', ''));
    const totalDescuentoOp = totalDescuento * (parseFloat($(this).val()) / 100);
    const total = isNaN(totalDescuento - totalDescuentoOp) ? totalDescuento : totalDescuento - totalDescuentoOp;

    $('#cantidadDescuento').val(Math.round(totalDescuentoOp * 100) / 100);
    $('#restanteDescuento').text(total);

    moneda();

    if (event.which == 13) $('#cantidadDescuento').focus();
  });

  $('#cantidadDescuento').on('keyup change', function (event) {
    const cantidadDescuento = parseFloat($(this).val());
    const totalDescuento = parseFloat($('#totalDescuento').text().replace(searchRegExp, '').replace('$', ''));

    const totalDescuentoOp = 100 * (cantidadDescuento / totalDescuento);
    const total = isNaN(totalDescuentoOp) ? totalDescuento : totalDescuento - cantidadDescuento;

    $('#porcentajeDescuento').val(Math.round(totalDescuentoOp * 100) / 100);
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
        const descuentoFila = fila.children('td:eq(4)');
        descuentoFila.children('span.porcentaje').text(porcentaje.value || 0);
        descuentoFila.children('span.dinero').text(total.value || 0);
        fila.children('td:eq(5)').children('b:eq(0)').children('span.dinero').text(restante);

        let totalImpuestos = 0;
        fila.children('td:eq(5)').children('p').each(function (index, el) {
          let clase = $(this).children('b:first').text().trim().toUpperCase();
          let factor = $(this).children('b:last').text().trim().toUpperCase();

          let valor = parseFloat($(this).children('span:eq(1)').text().replace('%', '').replace('$', '').replaceAll(',', '')) || 0;

          let impuestoFila = 0;

          // --- Lógica de Tasa o Cuota ---
          if (factor === 'EXENTO') {
            impuestoFila = 0; // Siempre cero
          } else if (factor === 'CUOTA') {
            let cantidad = parseFloat(fila.find('input.cantidadModiVenTabla').val()) || 1;
            impuestoFila = cantidad * valor;
          } else {
            impuestoFila = restante * (valor / 100);
          }

          $(this).children('span:eq(2)').text(impuestoFila.toFixed(2));

          // --- Lógica de Trasladado o Retenido ---
          if (clase === 'RETENCION' || clase === 'RETENIDO') {
            totalImpuestos -= impuestoFila;
          } else {
            totalImpuestos += impuestoFila;
          }
        });

        let totalFila = restante + totalImpuestos;

        fila.children('td:eq(6)').children('span.dinero').text(totalFila);

        totalCaja();
      } else {
        $("#mosDesGeP").text($("#porcentajeDescuento").val() || 0);
        $("#mosDesGeD").text($("#cantidadDescuento").val() || 0);
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

  $(document).on('click', '#bTicketPendiente', function () {
    $('#nombrePendiente').val($('#navtabTickets').children('.active').text());
    $('#MPendiente').modal('show');
  });

  $(document).on('click', '#bGuardarPendiente', function () {
    $("#formPendiente").submit();
  });

  $(document).on('submit', '#formPendiente', function (event) {
    event.preventDefault();

    const tickets = $('#navtabTickets').children();
    const newTicketName = $('#nombrePendiente').val();
    const lastTicketId = $(tickets[tickets.length - 1]).attr('id').split('_')[2];
    const activeTable = $('.tab-pane.active');

    $('#navtabTickets').children('.active').text(newTicketName);
    $('#navtabTickets').children().removeClass('active');
    $('#nav-tabContent').children().removeClass('active');
    let ticketName = parseInt(lastTicketId) + 1;

    var clase = '';
    if (localStorage.getItem("cajaTouch") === "true") {
      clase = 'class="oculto"';
    }

    $('#navtabTickets').append(`<button class="nav-link active" id="tab_ticket_${ticketName}" data-bs-toggle="tab" data-bs-target="#nav_ticket_${ticketName}" type="button" role="tab" aria-selected="true">Ticket ${ticketName}</button>`);
    $('#nav-tabContent').append(`<div class="tab-pane fade show active" id="nav_ticket_${ticketName}" role="tabpanel" aria-labelledby="nav-profile-tab">
      <div class="row">
        <div class="col-12 table-responsive" style="height: 40vh; background-color:#F0F0F0;">
          <table class="table table-hover table-striped text-center" id="tablaCaja" style="width: 100%; font-size: 12px;">
            <thead>
              <tr>
                <th `+ clase + `>Código</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Descuento</th>
                <th>Total</th>
                <th `+ clase + `>Existencia</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>`);

    activeTable.find('#tablaCaja').attr('id', '');

    $('#MPendiente').modal('hide');
  })

  $(document).on('click', 'button.nav-link', function () {
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
        tickets.eq(ticketToClick).click();
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
        $('#totalPagoCobrar').val(totalCaja);
        $('#totalPagoCobrar').attr('min', totalCaja);
        $('#totalCambio').text(0);

        if ($.trim($("#bAgregarClienteBusN").attr('attrID')) != '') {
          $("#clienteCobrar").val($.trim($("#bAgregarClienteBusN").children('span:eq(0)').text()));
          $("#clienteCobrar").attr('attrID', $.trim($("#bAgregarClienteBusN").attr('attrID')));
          $("#clienteCobrar").attr('attrDireccion', $.trim($("#bAgregarClienteBusN").attr('attrDireccion')));
        }

        moneda();
        $('#MCobrar').modal('show');
      }
    }, 300);
  });

  $(document).on('keyup change', '#totalPagoCobrar', function () {
    const totalCaja = parseFloat($('#totalCobrar').text().replace('$', '').replace(searchRegExp, ''));
    const totalPago = parseFloat($('#totalPagoCobrar').val());
    var cambio = isNaN(totalPago - totalCaja) ? 0 : (totalPago - totalCaja);
    if (cambio < 0) {
      cambio = 0;
    }
    $('#totalCambio').text(cambio);
    moneda();
  });

  $(document).on('keypress', '#totalPagoCobrar', function (event) {
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
        required: true,
        min: 0
      }
    },
    messages: {
      totalPagoCobrar: {
        required: "El monto de pago es requerido.",
        min: "El monto debe ser mayor o igual a 0."
      }
    },
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
          var data = "metodo=modificar&accion=caja&tipo=pediente&idCaja=" + $.trim($('#modalCaja').attr('attrcaja')) + "&descuento=" + $("#mosDesGeD").text().replace('$', '').replaceAll(',', '') + "&total=" + $.trim($("#totalCobrar").text().replace('$', '').replace(searchRegExp, '')) + "&tipoPago=" + $("#metodoPago").val() + "&pago=" + $("#totalPagoCobrar").val() + "&cambio=" + $.trim($("#totalCambio").text().replace('$', '').replace(searchRegExp, '')) + "&detalles=" + $.trim($("#detallesCobrar").val()) + "&cliente=" + $("#clienteCobrar").attr('attrID') + "&idVenta=" + $("#bCobrarImprimir").attr('attrID');

          $.ajax({
            url: 'index.php',
            type: 'POST',
            data: data,
            beforeSend: function () {
              $("#carga").show();
            }
          })
            .done(function (res) {
              if ($.trim(res) == 'Correcto') {
                if (tipoCobrar == 0) {
                  $("#printer").html(`<iframe id="iframePrint" src="controladores/pdf/ticketAbrir.php" onload="document.getElementById('iframePrint').contentWindow.print()"></iframe>`);
                } else {
                  $("#printer").html(`<iframe id="iframePrint" src="controladores/pdf/ticketVenta.php?id=` + $("#bCobrarImprimir").attr('attrID') + `" onload="document.getElementById('iframePrint').contentWindow.print()"></iframe>`);
                }

                $("#MCobrar").modal('hide');
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Error inesperado al registar la venta.'
                });

                console.log($.trim(res));
              }
            })
            .fail(function () {
              console.log("Error ajax");
            })
            .always(function () {
              $("#carga").hide();
            });
        } else {
          var total = parseFloat($("#totalCobrar").text().replace('$', '').replace(searchRegExp, ''));
          var pagado = parseFloat($("#totalPagoCobrar").val());

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

              productos.push({
                id: parseInt($(this).attr('attrid')),
                descripcion: $(this).children('td:eq(1)').text(),
                precio: parseFloat($(this).children('td:eq(2)').text().replace('$', '').replaceAll(',', '')),
                cantidad: parseFloat($(this).children('td:eq(3)').text().replaceAll(',', '')),
                descuento: $(this).children('td:eq(4)').children('span.dinero').text().replace('$', '').replaceAll(',', ''),
                total: parseFloat($(this).children('td:eq(6)').text().replace('$', '').replaceAll(',', '')),
                impuestos: impuestosProducto
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

            var data = "metodo=insertar&accion=caja&tipo=venta&idCaja=" + $.trim($('#modalCaja').attr('attrcaja')) + "&descuento=" + $("#mosDesGeD").text().replace('$', '').replaceAll(',', '') + "&total=" + $.trim($("#totalCobrar").text().replace('$', '').replace(searchRegExp, '')) + "&tipoPago=" + $("#metodoPago").val() + "&pago=" + $("#totalPagoCobrar").val() + "&cambio=" + $.trim($("#totalCambio").text().replace('$', '').replace(searchRegExp, '')) + "&cliente=" + $("#clienteCobrar").attr('attrID') + "&fkDireccion=" + $("#clienteCobrar").attr('attrDireccion') + "&detalles=" + $.trim($("#detallesCobrar").val()) + "&productos=" + JSON.stringify(productos) + "&turno=" + numTi;

            $.ajax({
              url: 'index.php',
              type: 'POST',
              data: data,
              beforeSend: function () {
                $("#carga").show();
              }
            })
              .done(function (res) {
                var separa = $.trim(res).split('~');
                if (separa[0] == 'Correcto') {
                  if (tipoCobrar == 0) {
                    $("#printer").html(`<iframe id="iframePrint" src="controladores/pdf/ticketAbrir.php" onload="document.getElementById('iframePrint').contentWindow.print()"></iframe>`);
                  } else {
                    $("#printer").html(`<iframe id="iframePrint" src="controladores/pdf/ticketVenta.php?id=` + separa[1] + `" onload="document.getElementById('iframePrint').contentWindow.print()"></iframe>`);
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
                    tickets.eq(ticketToClick).click();
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

                  console.log($.trim(res));
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
        //console.log($.trim(res));
        const resA = JSON.parse($.trim(res));
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

        var balance = (parseFloat(resA.detalles.Monto_Abrir) + totalVentas + totalEntradas + totalPagosVentas) - (totalPagosCompras + totalSalidas);
        $("#balanceCaja").text(balance);
        $("#montoCorte").val(balance);
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
    const montoCorte = parseFloat($(this).val());
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
        if ($.trim(res) == "Correcto") {
          document.exitFullscreen();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al dejar la caja.'
          });

          console.log($.trim(res));
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
    $('#totalPagoCobrar').val(parseFloat($(this).children('td:eq(4)').text().replace('$', '').replace(searchRegExp, '')));
    $('#totalPagoCobrar').attr('min', parseFloat($(this).children('td:eq(3)').text().replace('$', '').replace(searchRegExp, '')));
    $('#totalCambio').text($(this).children('td:eq(5)').text());
    $('#detallesCobrar').val($.trim($(this).children('td:eq(6)').children('p:eq(0)').text()));

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
          if ($.trim(res) == 'Correcto') {
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

            console.log($.trim(res));
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
          if ($.trim(res) == 'Correcto') {
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

            console.log($.trim(res));
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
    const data = "metodo=modificar&accion=caja&tipo=agregar&id=" + btn.attr('attrID') + "&codigo=" + $.trim($("#codigoProdMas").val()) + "&cantidad=" + $("#cantidadProdMas").val() + "&precio=" + $("#precioProdMas").val() + "&descuento=" + $("#descuentoProdMas").val();

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $("#carga").show();
      }
    }).done(function (res) {
      if ($.trim(res) == 'Correcto') {
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

        console.log($.trim(res));
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
    $('#porcentajeDescuento').val(descuento).trigger('change');

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
          (<span class="dinero">${impuestoFila.toFixed(2)}</span>) 
          <b>${impuesto.Tipo_Factor}</b>
        </p>`;
      });

      return { html: verImpuestos, total: totalImpuestos };
    };

    if (claseProd === "Granel") {
      var resImp = calcularImpuestosFila(precio, 1);
      var precioFinalConImpuestos = precio + resImp.total;

      $("#datosGranel").attr({
        'attrID': idProd,
        'attrCodigo': codigo,
        'attrExistencia': existencia,
        'attrPrecio': precio,
        'attrImpuestos': $this.attr('attrImpuestos')
      });

      $("#datosGranel").html(`
        <h4 class="text-center">${descripcion}</h4>
        <h5 class="text-center"><b>Precio Unitario:</b> <span class="dinero">${precio}</span></h5>
        <div class="text-center">
          ${resImp.html}
        </div>
      `);

      $("#importeGranel").val(precioFinalConImpuestos.toFixed(2));
      $('#importeGranel').attr('attrMayoreo', mayoreo);
      $("#MGranel").modal('show');

      moneda();
    } else {
      $("#tablaCaja").find('tbody tr').removeClass('activa');
      var fila = $("#tablaCaja").find(`tbody tr[attrID="${idProd}"]`);

      if (fila.length > 0) {
        var cantidad = parseFloat(fila.find('.cantidad').first().text().replace(',', '')) + 1;
        var descuentoPorc = parseFloat(fila.find('.porcentaje').first().text().replace('%', '')) || 0;

        var subtotalAntesDescuento = precio * cantidad;
        var importeDescuento = subtotalAntesDescuento * (descuentoPorc / 100);
        var subtotalFinal = subtotalAntesDescuento - importeDescuento;

        var resImp = calcularImpuestosFila(subtotalFinal, cantidad);
        var totalFinalFila = subtotalFinal + resImp.total;

        fila.addClass('activa');
        fila.html(`
          <td class="oculto">${codigo}</td>
          <td>${descripcion}</td>
          <td><span class="dinero" attrMayoreo="${mayoreo}">${precio}</span></td>
          <td><span class="cantidad">${cantidad}</span></td>
          <td><span class="porcentaje">${descuentoPorc}</span> (<span class="dinero">${importeDescuento.toFixed(2)}</span>)</td>
          <td>${resImp.html}</td>
          <td><span class="dinero">${totalFinalFila.toFixed(2)}</span></td>
          <td class="oculto"><span class="cantidad">${existencia}</span></td>
        `);
      } else {
        var resImp = calcularImpuestosFila(precio, 1);
        var totalFinalFila = precio + resImp.total;

        $("#tablaCaja").children('tbody').prepend(`<tr attrID="${idProd}" class="activa">
          <td class="oculto">${codigo}</td>
          <td>${descripcion}</td>
          <td><span class="dinero" attrMayoreo="${mayoreo}">${precio}</span></td>
          <td><span class="cantidad">1</span></td>
          <td><span class="porcentaje">0</span> (<span class="dinero">0</span>)</td>
          <td>${resImp.html}</td>
          <td><span class="dinero">${totalFinalFila.toFixed(2)}</span></td>
          <td class="oculto"><span class="cantidad">${existencia}</span></td>
        </tr>`);
      }

      audio1.play();
      totalCaja();
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
    submitHandler: function (form) {
      var data = "metodo=modificar&accion=caja&tipo=cliente&telefono=" + $.trim($("#telefonoClienteBus").val()) + "&nombre=" + $.trim($("#nombreClienteBus").val()) + "&calle=" + $.trim($("#calleClienteBus").val()) + "&noExterior=" + $.trim($("#noExteriorClienteBus").val()) + "&noInterior=" + $.trim($("#noInteriorClienteBus").val()) + "&cp=" + $.trim($("#cpClienteBus").val()) + "&colonia=" + $.trim($("#coloniaClienteBus").val()) + "&ciudad=" + $.trim($("#ciudadClienteBus").val()) + "&estado=" + $.trim($("#estadoClienteBus").val()) + "&pais=" + $.trim($("#paisClienteBus").val()) + "&detalles=" + $.trim($("#detallesClienteBus").val()) + "&idCliente=" + ($("#nombreClienteBus").attr('attrID') || '') + "&idDomicilio=" + ($("#tituloDomiBus").attr('attrID') || '');

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          var separa = $.trim(res).split('~');

          if (separa[0] == "Correcto") {
            var direccion = $.trim($("#calleClienteBus").val()) + ' #' + $.trim($("#noExteriorClienteBus").val());

            if ($.trim($("#noInteriorClienteBus").val()) != '') {
              direccion += ' int.' + $.trim($("#noInteriorClienteBus").val());
            }

            if ($.trim($("#cpClienteBus").val()) != '') {
              direccion += ', C.P. ' + $.trim($("#cpClienteBus").val());
            }

            if ($.trim($("#coloniaClienteBus").val()) != '') {
              direccion += ' Col. ' + $.trim($("#coloniaClienteBus").val());
            }

            if ($.trim($("#detallesClienteBus").val()) != '') {
              direccion += ' Detalles: ' + $.trim($("#detallesClienteBus").val());
            }

            $("#bAgregarClienteBusN").html('<span>' + $.trim($("#nombreClienteBus").val()) + '</span> <i class="fas fa-times"></i><br><span style="font-size: 11px;">' + direccion + '</span>');
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

            console.log($.trim(res));
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
    if ($.trim($(this).val()) != "" && $(this).attr('readonly') == undefined) {
      var data = "metodo=detalles&accion=caja&tipo=buscarTelefono&buscar=" + $(this).val();

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data
      })
        .done(function (res) {
          if ($.trim(res) != "") {
            $("#telefonosClientesBus").removeClass('oculto');
          } else {
            $("#telefonosClientesBus").addClass('oculto');
          }

          $("#telefonosClientesBus").html($.trim(res));
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
    if ($.trim($(this).val()) != "" && $(this).attr('readonly') == undefined) {
      var data = "metodo=detalles&accion=caja&tipo=buscarNombre&buscar=" + $(this).val();

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data
      })
        .done(function (res) {
          if ($.trim(res) != "") {
            $("#nombresClientesBus").removeClass('oculto');
          } else {
            $("#nombresClientesBus").addClass('oculto');
          }

          $("#nombresClientesBus").html($.trim(res));
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
        //console.log($.trim(res));
        var datos = JSON.parse($.trim(res));

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
  $(document).on('dblclick', '#tablaCaja tbody tr td:eq(2)', function () {
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
    let padre = $(this).parent().parent();
    let precio = parseFloat($(this).val()) || 0;
    let mayoreo = $(this).attr('attrMayoreo');
    let cantidad = parseFloat(padre.children('td:eq(3)').children('span.cantidad').text().replace(',', ''));
    let descuento = parseFloat(padre.children('td:eq(4)').children('span.porcentaje').text().replace('%', '').replace(',', ''));
    let impuestos = padre.children('td:eq(5)');

    descuHtml = '<span class="porcentaje">0</span>(<span class="dinero">0</span>)';
    if (descuento > 0) {
      descuHtml = '<span class="porcentaje">' + descuento + '</span>(<span class="dinero">' + desDinero + '</span>)';
    }

    var subtotal = (precio * cantidad) - descuento;
    let totalImpuestos = 0;
    impuestos.children('p').each(function (index, el) {
      let clase = $(this).children('b:first').text().trim().toUpperCase();
      let factor = $(this).children('b:last').text().trim().toUpperCase();

      let valor = parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replace(',', '')) || 0;
      let impuestoFila = 0;

      // --- Lógica de Tasa o Cuota ---
      if (factor === 'EXENTO') {
        impuestoFila = 0; // Por definición, no genera importe
      } else if (factor === 'CUOTA') {
        impuestoFila = cantidad * valor;
      } else {
        impuestoFila = (subtotal * (valor / 100));
      }

      $(this).children('span.dinero').html(impuestoFila.toFixed(2));

      // --- Lógica de Trasladado o Retenido ---
      if (clase === 'RETENCION' || clase === 'RETENIDO') {
        totalImpuestos -= impuestoFila;
      } else {
        totalImpuestos += impuestoFila;
      }
    });

    let total = subtotal + totalImpuestos;

    padre.children('td:eq(2)').html('<span class="dinero" attrmayoreo="' + mayoreo + '">' + precio + '</span>');
    padre.children('td:eq(4)').html(descuHtml);
    padre.children('td:eq(5)').children('b:eq(0)').children('span.dinero').html(subtotal);
    padre.children('td:eq(6)').children('span.dinero').html(total);

    totalCaja();
    $("#barCodeV").focus();
  });
});