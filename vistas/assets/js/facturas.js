let ventas = [];
let impuestosConcepto = [];
let bloqueando = false;
let impuestosPagoTemporal = [];

function v_facturas() {
  var date = new Date();

  var day = date.getDate().toString().padStart(2, '0');
  var month = (date.getMonth() + 1).toString().padStart(2, '0');
  var year = date.getFullYear();
  var today = year + "-" + month + "-" + day;

  const hours = date.getHours().toString().padStart(2, '0');
  const minutes = date.getMinutes().toString().padStart(2, '0');

  $("#fechaFinFacturas").val(today);
  $("#fechaFinVentasFacturas").val(today);

  const now = `${year}-${month}-${day}T${hours}:${minutes}`;
  $("#fechaEmision").val(now);

  date.setFullYear(date.getFullYear() - 1);

  var prevDay = date.getDate().toString().padStart(2, '0');
  var prevMonth = (date.getMonth() + 1).toString().padStart(2, '0');
  var prevYear = date.getFullYear();
  var yearAgo = prevYear + "-" + prevMonth + "-" + prevDay;

  $("#fechaInicioFacturas").val(yearAgo);
  $("#fechaInicioVentasFacturas").val(yearAgo);

  tablaFacturas();

  $('#formConcepto').validate({
    rules: {
      c_clave: { required: true },
      c_descripcion: { required: true },
      c_cantidad: { required: true, min: 1 },
      c_precio: { required: true, min: 0.01 }
    },
    messages: {
      c_clave: { required: "La clave es requerida" },
      c_descripcion: { required: "La descripción es requerida" },
      c_cantidad: { required: "La cantidad es requerida", min: "Debe ser mayor a 0" },
      c_precio: { required: "El precio es requerido", min: "Debe ser mayor a 0" }
    },
    submitHandler: function (form) {
      let clave = $('#c_clave').val();
      let unidad = $('#c_unidad').val();
      let desc = $('#c_descripcion').val();
      let cantidad = parseFloat($('#c_cantidad').val());
      let precio = parseFloat($('#c_precio').val());
      let descuPorcen = parseFloat($('#c_descuento_porcentaje').val()) || 0;
      let descuento = parseFloat($('#c_descuento_dinero').val()) || 0;

      let subtotal = cantidad * precio;
      let totalImpuestos = 0;

      impuestosConcepto.forEach(i => {
        if (i.tipo === 'Retenido') {
          totalImpuestos -= i.importe;
        } else {
          totalImpuestos += i.importe;
        }
      });

      let total = subtotal - descuento + totalImpuestos;

      let textoImpuestos = '';
      impuestosConcepto.forEach(i => {
        let nombre = '';

        switch (i.impuesto) {
          case '002': nombre = 'IVA'; break;
          case '001': nombre = 'ISR'; break;
          case '003': nombre = 'IEPS'; break;
          default: nombre = i.impuesto;
        }

        let signo = i.tipo === 'Retenido' ? '-' : '';

        let valorTexto = '';
        if (i.tipoFactor === 'Tasa') {
          valorTexto = i.valor.toFixed(2) + '%';
        } else {
          valorTexto = '$' + i.valor.toFixed(2);
        }

        textoImpuestos += `${nombre} (${i.tipoFactor} ${valorTexto}) (${signo}$${i.importe.toFixed(2)})<br>`;
      });

      let fila = `
        <tr attrID="0" data-impuestos='${JSON.stringify(impuestosConcepto)}'>
          <td>${clave}</td>
          <td>${desc}</td>
          <td>${unidad}</td>
          <td class="cantidad">${cantidad}</td>
          <td class="dinero">${precio}</td>
          <td class="dinero">${subtotal}</td>
          <td><span class="dinero">${descuento}</span> (<span class="porcentaje">${descuPorcen}</span>%)</td>
          <td class="dinero">${(subtotal - descuento)}</td>
          <td><span class="dinero">${totalImpuestos}</span><br>${textoImpuestos}</td>
          <td class="dinero">${total}</td>
          <td><button type="button" class="btn btn-danger btn-sm bEliminarConceptoFactura">X</button></td>
        </tr> 
      `;

      $("#tablaConceptos tbody").append(fila);

      resetConcepto();
      $("#modalConcepto").modal('hide');
      calcularTotalFactura();
    }
  });
}

function resetConcepto() {
  $('#formConcepto')[0].reset();
  var validator = $("#formConcepto").validate();
  validator.resetForm();

  $('#c_cantidad').val(1);
  $('#c_subtotal').val('');
  $('#c_impuestos_total').val('');
  $('#c_total').val('');

  impuestosConcepto = [];
  $("#tablaImpuestos tbody").html('');
  bloqueando = false;
}

function tablaFacturas() {
  ajaxMyDatatable({
    'table': $('#tablaFacturas'),
    'colums': [
      'Fecha_Registro',
      'Fecha_Emision',
      'Fecha_Timbrado',
      'Folio',
      'Emisor',
      'Receptor',
      'Datos',
      'Total',
      'Estatus',
      'Acciones'
    ],
    'sort': [0, 'desc'],
    'url': 'index.php',
    'params': {
      'metodo': 'consultar',
      'accion': 'facturas',
      'fechaInicio': $("#fechaInicioFacturas").val(),
      'fechaFin': $("#fechaFinFacturas").val()
    },
    "totals": {
      7: "TotalFinal"
    }
  });
}

function tablaVentasFacturas() {
  ajaxMyDatatable({
    'table': $('#tablaVentasFacturas'),
    'colums': [
      'Fecha',
      'Folio',
      'Tipo',
      'Cliente',
      'Total',
      'Pago',
      'Cambio',
      'Estatus',
      'Detalles'
    ],
    'sort': [0, 'desc'],
    'url': 'index.php',
    'params': {
      'metodo': 'consultar',
      'accion': 'ventas',
      'factura': true
    }
  });
}

function calcularTotalFactura() {
  let subtotal = 0;
  let impuestos = 0;
  let total = 0;
  let descuento = 0;
  let totalFinal = 0;

  $("#tablaConceptos tbody tr").each(function () {
    subtotal += parseFloat($(this).children('td:eq(5)').text().replace('$', '').replaceAll(',', '')) || 0;
    descuento += parseFloat($(this).children('td:eq(6)').children('span.dinero').text().replace('$', '').replaceAll(',', '')) || 0;
    impuestos += parseFloat($(this).children('td:eq(8)').children('span.dinero').text().replace('$', '').replaceAll(',', '')) || 0;
    total += parseFloat($(this).children('td:eq(7)').text().replace('$', '').replaceAll(',', '')) || 0;
    totalFinal += parseFloat($(this).children('td:eq(9)').text().replace('$', '').replaceAll(',', '')) || 0;
  });

  $("#subtotal").html(subtotal);
  $("#descuento").html(descuento);
  $("#total").html(total);
  $("#totalImpuestos").html(impuestos);
  $("#totalFinal").html(totalFinal);

  moneda();
}

function recalcularConcepto(origen = null) {
  let cantidad = parseFloat($('#c_cantidad').val()) || 0;
  let precio = parseFloat($('#c_precio').val()) || 0;

  let subtotal = cantidad * precio;

  let descuentoPorcentaje = parseFloat($('#c_descuento_porcentaje').val()) || 0;
  let descuentoDinero = parseFloat($('#c_descuento_dinero').val()) || 0;

  if (!bloqueando) {
    bloqueando = true;

    if (origen === 'porcentaje') {
      descuentoDinero = subtotal * (descuentoPorcentaje / 100);
      $('#c_descuento_dinero').val(descuentoDinero.toFixed(2));
    }

    if (origen === 'dinero') {
      descuentoPorcentaje = subtotal > 0 ? (descuentoDinero / subtotal) * 100 : 0;
      $('#c_descuento_porcentaje').val(descuentoPorcentaje.toFixed(2));
    }

    bloqueando = false;
  }

  let totalImpuestos = 0;
  let nuevaBase = subtotal - descuentoDinero;
  $("#tablaImpuestos tbody tr").each(function (index) {
    let imp = impuestosConcepto[index];

    if (imp) {
      let nuevoImporte = 0;
      if (imp.tipoFactor === 'Tasa') {
        nuevoImporte = nuevaBase * (imp.valor / 100);
      } else {
        nuevoImporte = cantidad * imp.valor;
      }

      impuestosConcepto[index].importe = nuevoImporte;
      if (imp.tipo === 'Retenido') {
        totalImpuestos -= nuevoImporte;
      } else {
        totalImpuestos += nuevoImporte;
      }

      $(this).find('td:eq(3)').text(nuevoImporte.toFixed(2));
    }
  });

  let total = subtotal - descuentoDinero + totalImpuestos;

  $('#c_subtotal').val((subtotal - descuentoDinero).toFixed(2));
  $('#c_impuestos_total').val(totalImpuestos.toFixed(2));
  $('#c_total').val(total.toFixed(2));

  moneda();
}

function guardarFactura(metodo, timbrar) {
  if (!document.getElementById('factForm').checkValidity()) {
    document.getElementById('factForm').reportValidity();
    return;
  }

  if ($('#tablaConceptos tbody tr').length === 0 && $("#tipoComprobante").val() === 'I - Ingreso') {
    Swal.fire({
      icon: 'warning',
      title: 'Opps...',
      text: 'No hay conceptos en la factura, debes agregar al menos uno.',
    });
    return;
  }

  if ($('#tablaDocRelacionados tbody tr').length === 0 && $("#tipoComprobante").val() === 'P - Complemento de Pago') {
    Swal.fire({
      icon: 'warning',
      title: 'Opps...',
      text: 'No hay documentos relacionados en la factura, debes agregar al menos uno.',
    });
    return;
  }

  let conceptos = [];
  if ($('#tipoComprobante').val().includes('I -')) {
    $('#tablaConceptos tbody tr').each(function () {
      let fila = $(this);
      conceptos.push({
        claveProdServ: fila.find('td:eq(0)').text().trim(),
        descripcion: fila.find('td:eq(1)').text().trim(),
        unidad: fila.find('td:eq(2)').text().trim(),
        cantidad: fila.find('td:eq(3)').text().trim(),
        precioUnitario: fila.find('td:eq(4)').text().trim().replace('$', '').replaceAll(',', ''),
        subtotal: fila.find('td:eq(5)').text().trim().replace('$', '').replaceAll(',', ''),
        descuento: fila.find('td:eq(6) .dinero').text().trim().replace('$', '').replaceAll(',', ''),
        base: fila.find('td:eq(7)').text().trim().replace('$', '').replaceAll(',', ''),
        impuestos_total: fila.find('td:eq(8) .dinero').text().trim().replace('$', '').replaceAll(',', ''),
        total: fila.find('td:eq(9)').text().trim().replace('$', '').replaceAll(',', ''),
        impuestos: JSON.parse(fila.attr('data-impuestos') || '[]')
      });
    });
  }

  let documentosRelacionados = [];
  if ($('#tipoComprobante').val().includes('P -')) {
    $('#tablaDocRelacionados tbody tr').each(function () {
      let dataJson = JSON.parse($(this).attr('data-json') || '{}');
      documentosRelacionados.push(dataJson);
    });
  }

  let global = 0;
  if ($("#facturaGlobal").prop('checked')) {
    global = 1;
  }

  let general = 0;
  if ($("#publicoGeneral").prop('checked')) {
    general = 1;
  }

  let data = {
    metodo: metodo,
    accion: 'facturas',
    fechaEmision: $('#fechaEmision').val().replace('T', ' '),
    tipoComprobante: $('#tipoComprobante').val(),
    formaPago: $('#formaPago').val(),
    metodoPago: $('#metodoPago').val(),
    periodicidad: $('#periodicidad').val(),
    mes: $('#mes').val(),
    anio: $('#anio').val(),
    global: global,
    general: general,

    nombreEmisor: $('#nombreEmisor').text(),
    rfcEmisor: $('#rfcEmisor').text(),
    regimenEmisor: $('#regimenEmisor').text(),
    cpEmisor: $('#cpEmisor').text(),

    rfcReceptor: $('#rfcReceptor').val(),
    nombreReceptor: $('#nombreReceptor').val(),
    usoCfdi: $('#usoCfdi').val(),
    regimenFiscalReceptor: $('#regimenFiscalReceptor').val(),
    cpReceptor: $('#cpReceptor').val(),
    email: $("#emailReceptor").val(),

    subtotal: $('#subtotal').text().replace('$', '').replaceAll(',', ''),
    descuento: $('#descuento').text().replace('$', '').replaceAll(',', ''),
    impuestos: $('#totalImpuestos').text().replace('$', '').replaceAll(',', ''),
    total: $('#totalFinal').text().replace('$', '').replaceAll(',', ''),

    conceptos: JSON.stringify(conceptos),
    documentosRelacionados: JSON.stringify(documentosRelacionados),

    timbrar: timbrar,
    id: $('#bGuardarFactura').attr('attrID'),
    venta: $('#folioVentaFacturacion').attr('attrID') || ''
  };

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $('#carga').show();
    }
  })
    .done(function (res) {
      if ($.trim(res) === 'Correcto') {
        let msg = timbrar === false ? 'Factura guardada correctamente' : 'Factura guardada y timbrada correctamente';

        Swal.fire({
          icon: 'success',
          title: msg
        });

        $("#factForm")[0].reset();
        $("#tablaConceptos tbody").html("");
        calcularTotalFactura();

        var date = new Date();
        var day = date.getDate().toString().padStart(2, '0');
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var year = date.getFullYear();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');

        const now = `${year}-${month}-${day}T${hours}:${minutes}`;
        $("#fechaEmision").val(now);

        tablaFacturas();
        $('#modalFactura').modal('hide');
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error inesperado al guardar la factura: ' + $.trim(res)
        });

        console.log(res);
      }
    })
    .fail(function () {
      console.error('Error AJAX');
    })
    .always(function () {
      $('#carga').hide();
    });
}

function tablaClavesProdFac() {
  ajaxMyDatatable({
    'table': $('#tablaClavesProdFac'),
    'colums': [
      'Clave',
      'Descripcion',
      'Palabras',
    ],
    'sort': [0, 'asc'],
    'url': 'index.php',
    'params': {
      'metodo': 'detalles',
      'accion': 'productos',
      'tipo': 'claves'
    }
  });
}

function tablaClavesUnidadProdFac() {
  ajaxMyDatatable({
    'table': $('#tablaClavesUnidadProdFac'),
    'colums': [
      'Clave',
      'Nombre',
      'Simbolo',
    ],
    'sort': [0, 'asc'],
    'url': 'index.php',
    'params': {
      'metodo': 'detalles',
      'accion': 'productos',
      'tipo': 'unidades'
    }
  });
}

function tablaClientesFacturacion() {
  ajaxMyDatatable({
    "table": $("#tablaClientesFacturacion"),
    "colums": [
      "Nombre",
      "Tipo",
      "Domicilio",
      "Contacto"
    ],
    "sort": [
      0,
      "asc"
    ],
    "url": "index.php"
    ,
    "params": {
      metodo: 'consultar',
      accion: 'clientes'
    }
  });
}

function verDatosFacturacion() {
  const data = "metodo=detalles&accion=facturas&tipo=datosFacturacion";

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $('#carga').show();
    }
  }).done(function (res) {
    //console.log($.trim(res));
    var data = JSON.parse($.trim(res));
    //console.log(data);

    $("#nombreEmisor").html(data.Nombre);
    $("#rfcEmisor").html(data.RFC);
    $("#regimenEmisor").html(data.Regimen);
    $("#cpEmisor").html(data.CP);
  }).fail(function () {
    console.log("Error ajax");
  }).always(function () {
    $('#carga').hide();
  });
}

function verConceptosVenta() {
  const data = "metodo=detalles&accion=facturas&tipo=ventaConceptos&id=" + $('#folioVentaFacturacion').attr('attrID');

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $('#carga').show();
    }
  })
    .done(function (res) {
      //console.log($.trim(res));
      $("#tablaConceptos tbody").html($.trim(res));
      calcularTotalFactura();
    })
    .fail(function () {
      console.log("Error ajax");
    })
    .always(function () {
      $('#carga').hide();
    });
}

function calcularTotalesPago() {
  if (!$('#tipoComprobante').val().includes('P -')) return;

  let sumaPagos = 0;
  $('#tablaDocRelacionados tbody tr').each(function () {
    const data = JSON.parse($(this).attr('data-json'));
    sumaPagos += parseFloat(data.montoPagado) || 0;
  });

  $("#subtotal").html('0');
  $("#descuento").html('0');
  $("#totalImpuestos").html('0');
  $("#verImpuestos").html('');
  $("#total").html('0');
  $('#totalFinal').text(sumaPagos);

  moneda();
}

function recalcularImpuestosPago() {
  let montoPagado = parseFloat($('#p_monto_pagado').val()) || 0;
  if (impuestosPagoTemporal.length === 0) return;

  let tasaRetencionTotal = 0;
  let tasaTrasladoTotal = 0;

  impuestosPagoTemporal.forEach(imp => {
    if (imp.tipo === "Retenido") tasaRetencionTotal += (imp.tasa / 100);
    if (imp.tipo === "Trasladado") tasaTrasladoTotal += (imp.tasa / 100);
  });

  let divisor = 1 + tasaTrasladoTotal - tasaRetencionTotal;
  let baseUnificada = divisor !== 0 ? montoPagado / divisor : 0;

  $('#tablaImpuestosPagos tbody').empty();

  impuestosPagoTemporal.forEach((imp, index) => {
    imp.base = Math.round(baseUnificada * 100) / 100;
    imp.importe = Math.round((baseUnificada * (imp.tasa / 100)) * 100) / 100;

    let fila = `<tr>
      <td>${imp.tipo}</td>
      <td>${imp.nombre}</td>
      <td>${imp.tasa}%</td>
      <td class="dinero">${imp.importe.toFixed(2)}</td>
      <td><button type="button" class="btn btn-danger btn-sm bEliminarImpPago" data-index="${index}">X</button></td>
    </tr>`;

    $('#tablaImpuestosPagos tbody').append(fila);
  });

  moneda();
}

jQuery(document).ready(function ($) {

  $(document).on('change keyup', '.fechasFacturas', function () {
    tablaFacturas();
  });

  $(document).on('click', '#bAgregarFactura', function () {
    $("#bGuardarFactura").attr('tipo', 'insertar');

    if ($("#bGuardarFactura").attr('attrID') != undefined && $("#bGuardarFactura").attr('attrID') != '') {
      $("#bGuardarFactura").attr('attrID', '');
      $("#factForm")[0].reset();
      $("#folioVentaFacturacion").attr('attrID', '');
      $("#folioVentaFacturacion").parent().parent().removeClass('d-none');
      $("#facturaGlobal").parent().removeClass('d-none');
      $("#tipoComprobante").prop('disabled', false);
      $('#metodoPago').attr('disabled', false);
      $("#metodoPago").parent().removeClass('d-none');
      $("#facturaGlobal").parent().removeClass('d-none');
      if ($("#facturaGlobal").prop('checked')) {
        $("#facturaGlobal").trigger('click');
      }
      $("#formaPago").prop('disabled', false);
      $("#publicoGeneral").parent().removeClass('d-none');
      if ($("#publicoGeneral").prop('checked')) {
        $("#publicoGeneral").trigger('click');
      } else {
        $("#nombreReceptor").prop('readonly', false);
        $("#rfcReceptor").prop('readonly', false);
        $("#usoCfdi").prop('disabled', false);
        $("#regimenFiscalReceptor").prop('disabled', false);
        $("#cpReceptor").prop('readonly', false);
      }

      $("#bAgregarConceptoManual").removeClass('d-none');
      $("#cardConceptos").removeClass('d-none');
      $("#totalesFactura").removeClass('d-none');
      $("#cardDocRelacionados").addClass('d-none');
      $("#tablaConceptos tbody").html("");
      calcularTotalFactura();

      var date = new Date();
      var day = date.getDate().toString().padStart(2, '0');
      var month = (date.getMonth() + 1).toString().padStart(2, '0');
      var year = date.getFullYear();
      const hours = date.getHours().toString().padStart(2, '0');
      const minutes = date.getMinutes().toString().padStart(2, '0');

      const now = `${year}-${month}-${day}T${hours}:${minutes}`;
      $("#fechaEmision").val(now);
    }

    verDatosFacturacion();
    $("#modalFactura").modal('show');
  });

  $(document).on('click', '#bRecargarEmisor', function () {
    verDatosFacturacion();
  });

  $(document).on('click', '.bEliminarFactura', function () {
    const btn = $(this);
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar la factura?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        const data = "metodo=eliminar&accion=facturas&id=" + btn.attr('attrID');

        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function () {
            $('#carga').show();
          }
        }).done(function (res) {
          if ($.trim(res) === 'Correcto') {
            Swal.fire({
              icon: 'success',
              title: 'La factura se ha eliminado correctamente',
            });

            tablaFacturas();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar la factura.',
            });

            console.log($.trim(res));
          }
        }).fail(function () {
          console.log("Error ajax");
        }).always(function () {
          $('#carga').hide();
        });
      }
    });
  });

  $(document).on('click', '#timbrarBtn', function () {
    guardarFactura($("#bGuardarFactura").attr('tipo'), true);
  });

  $(document).on('click', '#bGuardarFactura', function () {
    guardarFactura($(this).attr('tipo'), false);
  });

  $(document).on('click', '.bModificarFactura', function () {
    var btn = $(this);
    $("#bGuardarFactura").attr('tipo', 'modificar');
    $("#bGuardarFactura").attr('attrID', btn.attr('attrID'));
    $("#factForm")[0].reset();

    const data = "metodo=detalles&accion=facturas&tipo=factura&id=" + btn.attr('attrID');

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $('#carga').show();
      }
    }).done(function (res) {
      //console.log($.trim(res));
      var data = JSON.parse($.trim(res));
      //console.log(data);

      if ($.trim(data.FK_Venta) != '' && $.trim(data.FK_Venta) != '0') {
        $("#folioVentaFacturacion").val(data.FK_Venta);
        $("#folioVentaFacturacion").attr('attrID', data.FK_Venta);

        $("#tipoComprobante").attr('disabled', true);
        $("#facturaGlobal").parent().addClass('d-none');
        $("#publicoGeneral").parent().addClass('d-none');
        $("#bAgregarConceptoManual").addClass('d-none');
        $("#bRecargarCoceptosVenta").removeClass('d-none');
      } else {
        $("#folioVentaFacturacion").attr('attrID', '');
        $("#tipoComprobante").prop('disabled', false);
        $("#facturaGlobal").parent().removeClass('d-none');
        $("#publicoGeneral").parent().removeClass('d-none');
        $("#bAgregarConceptoManual").removeClass('d-none');
        $("#bRecargarCoceptosVenta").addClass('d-none');
      }

      $("#facturaGlobal").prop('checked', false);
      if (data.Global == '1') {
        $("#facturaGlobal").trigger('click');
      }
      $("#fechaEmision").val(data.Fecha_Emision.replace(' ', 'T'));
      $("#formaPago").val(data.Forma_Pago);
      $("#metodoPago").val(data.Metodo_Pago);
      if (data.Metodo_Pago == 'PPD - Pago en parcialidades o diferido') {
        $("#formaPago").val('99 - Por definir');
        $("#formaPago").prop('disabled', true);
      } else {
        $("#formaPago").prop('disabled', false);
      }

      $("#periodicidad").val(data.Periodicidad);
      $("#mes").val(data.Mes);
      $("#anio").val(data.Ano);

      $("#nombreEmisor").html(data.Nombre_Emisor);
      $("#rfcEmisor").html(data.RFC_Emisor);
      $("#regimenEmisor").html(data.Regimen_Emisor);
      $("#cpEmisor").html(data.CP_Emisor);

      $("#publicoGeneral").prop('checked', false);
      if (data.General == '1') {
        $("#publicoGeneral").trigger('click');
      }
      $("#rfcReceptor").val(data.RFC_Receptor);
      $("#nombreReceptor").val(data.Nombre_Receptor);
      $("#usoCfdi").val(data.Uso_CFDI);
      $("#regimenFiscalReceptor").val(data.Regimen_Receptor);
      $("#cpReceptor").val(data.CP_Receptor);
      $("#emailReceptor").val(data.Email);

      $("#tipoComprobante").val(data.Tipo_Comprobante);
      if (data.Tipo_Comprobante == 'P - Complemento de Pago') {
        $("#metodoPago").val('');
        $('#metodoPago').attr('disabled', true);
        $("#metodoPago").parent().addClass('d-none');
        $("#cardConceptos").addClass('d-none');
        $("#totalesFactura").addClass('d-none');
        $("#cardDocRelacionados").removeClass('d-none');
        $("#usoCfdi").val('CP01 - Pagos');
        $("#usoCfdi").prop('disabled', true);
        $("#tablaDocRelacionados tbody").html(data.Docs);
        $("#folioVentaFacturacion").val('');
        $("#folioVentaFacturacion").attr('attrID', '');
        $("#folioVentaFacturacion").parent().parent().addClass('d-none');
        $("#facturaGlobal").parent().addClass('d-none');

        setTimeout(function () { calcularTotalesPago(); }, 500);
      } else {
        $('#metodoPago').attr('disabled', false);
        $("#metodoPago").parent().removeClass('d-none');
        $("#cardConceptos").removeClass('d-none');
        $("#totalesFactura").removeClass('d-none');
        $("#cardDocRelacionados").addClass('d-none');
        $("#tablaConceptos tbody").html(data.Ventas);
        $("#folioVentaFacturacion").parent().parent().removeClass('d-none');
        $("#facturaGlobal").parent().removeClass('d-none');

        setTimeout(function () { calcularTotalFactura(); }, 500);
      }

      

      moneda();
      $("#modalFactura").modal('show');
    }).fail(function () {
      console.log("Error ajax");
    }).always(function () {
      $('#carga').hide();
    });
  });

  $(document).on('click', '.bEliminarConceptoFactura', function () {
    $(this).parent().parent().remove();
    calcularTotalFactura();
  });

  $(document).on('click', '#facturaGlobal', function () {
    if ($(this).prop('checked')) {
      $("#tipoComprobante").val('I - Ingreso');
      $("#tipoComprobante").prop('disabled', true);
      $("#metodoPago").val('PUE - Pago en una sola exhibición');
      $("#metodoPago").prop('disabled', true);
      $("#periodicidad").parent().removeClass('d-none');
      $("#periodicidad").prop('required', false);
      $("#mes").parent().removeClass('d-none');
      $("#mes").prop('required', false);
      $("#anio").parent().removeClass('d-none');
      $("#anio").prop('readonly', false);
      $("#rfcReceptor").val('XAXX010101000');
      $("#rfcReceptor").prop('readonly', true);
      $("#nombreReceptor").val('PUBLICO EN GENERAL');
      if ($("#tipoComprobante").val() != 'P - Complemento de Pago') {
        $("#usoCfdi").val('S01 - Sin efectos fiscales');
        $("#usoCfdi").prop('disabled', true);
      }
      $("#regimenFiscalReceptor").val('616 - Sin obligaciones fiscales');
      $("#regimenFiscalReceptor").prop('disabled', true);
      $("#cpReceptor").val($("#cpEmisor").text());
      $("#cpReceptor").prop('readonly', true);
    } else {
      $("#tipoComprobante").prop('disabled', false);
      $("#metodoPago").val('');
      $("#metodoPago").prop('disabled', false);
      $("#periodicidad").parent().addClass('d-none');
      $("#periodicidad").prop('required', true);
      $("#mes").parent().addClass('d-none');
      $("#mes").prop('required', true);
      $("#anio").parent().addClass('d-none');
      $("#anio").prop('readonly', true);

      if (!$('#publicoGeneral').prop('checked')) {
        $("#rfcReceptor").val('');
        $("#rfcReceptor").prop('readonly', false);
        $("#nombreReceptor").val('');
        if ($("#tipoComprobante").val() != 'P - Complemento de Pago') {
          $("#usoCfdi").val('');
          $("#usoCfdi").prop('disabled', false);
        }
        $("#regimenFiscalReceptor").val('');
        $("#regimenFiscalReceptor").prop('disabled', false);
        $("#cpReceptor").val('');
        $("#cpReceptor").prop('readonly', false);
      }
    }
  });

  $(document).on('click', '#publicoGeneral', function () {
    if ($(this).prop('checked')) {
      $("#rfcReceptor").val('XAXX010101000');
      $("#rfcReceptor").prop('readonly', true);
      $("#nombreReceptor").val('PUBLICO EN GENERAL');
      if ($("#tipoComprobante").val() != 'P - Complemento de Pago') {
        $("#usoCfdi").val('S01 - Sin efectos fiscales');
        $("#usoCfdi").prop('disabled', true);
      }
      $("#regimenFiscalReceptor").val('616 - Sin obligaciones fiscales');
      $("#regimenFiscalReceptor").prop('disabled', true);
      $("#cpReceptor").val($("#cpEmisor").text());
      $("#cpReceptor").prop('readonly', true);
    } else {
      $("#rfcReceptor").val('');
      $("#rfcReceptor").prop('readonly', false);
      $("#nombreReceptor").val('');
      if ($("#tipoComprobante").val() != 'P - Complemento de Pago') {
        $("#usoCfdi").val('');
        $("#usoCfdi").prop('disabled', false);
      }
      $("#regimenFiscalReceptor").val('');
      $("#regimenFiscalReceptor").prop('disabled', false);
      $("#cpReceptor").val('');
      $("#cpReceptor").prop('readonly', false);
    }
  });

  $(document).on('click', '#bAgregarConceptoManual', function () {
    $("#modalConcepto").modal('show');
  });

  $(document).on('click', '#btnAgregarImpuesto', function () {
    let tipo = $('#imp_tipo').val();
    let impuesto = $('#imp_impuesto').val();
    let valor = parseFloat($('#imp_tasa').val()) || 0;
    let tipoFactor = $('#imp_tipoFactor').val();
    let base = parseFloat($('#c_subtotal').val()) || 0;

    if (!valor) {
      Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: 'Ingresa una cantidad válida para el impuesto.'
      });
      return;
    }

    let importe = 0;
    let tasa = 0;
    let cuota = 0;
    if (tipoFactor === 'Tasa') {
      tasa = valor / 100;
      importe = base * tasa;
    } else {
      cuota = valor;
      importe = cantidad * cuota;
    }

    let obj = { tipo, impuesto, tipoFactor, valor, importe };
    impuestosConcepto.push(obj);
    let labelValor = tipoFactor === 'Tasa' ? '<span class="porcentaje">' + valor + '<span>' : '<span class="dinero">' + valor + '</span>';

    let row = `
      <tr>
        <td>${tipo}</td>
        <td>${impuesto}</td>
        <td>${labelValor}</td>
        <td class="dinero">${importe}</td>
        <td><button class="btn btn-danger btn-sm eliminarImp">X</button></td>
      </tr>
    `;

    $("#tablaImpuestos tbody").append(row);
    $('#imp_tipo').val('Trasladado');
    $('#imp_impuesto').val('002');
    $('#imp_tipoFactor').val('Tasa');
    $("#imp_tasa").val('');

    recalcularConcepto();
  });

  $(document).on('click', '.eliminarImp', function () {
    let index = $(this).closest('tr').index();
    impuestosConcepto.splice(index, 1);
    $(this).closest('tr').remove();

    recalcularConcepto();
  });

  $(document).on('keyup change', '#c_cantidad, #c_precio', function () {
    recalcularConcepto();
  });

  $(document).on('keyup change', '#c_descuento_porcentaje', function () {
    recalcularConcepto('porcentaje');
  });

  $(document).on('keyup change', '#c_descuento_dinero', function () {
    recalcularConcepto('dinero');
  });

  $(document).on('click', '#bBuscarClaveProdFac', function () {
    tablaClavesProdFac();
    $('#modalClavesProdFac').modal('show');
  });

  $(document).on('click', '#bQuitarClaveProdFac', function () {
    $("#c_clave").val('');
  });

  $(document).on('click', '#tablaClavesProdFac tbody tr', function () {
    $("#c_clave").val($(this).children('td:eq(0)').text());
    $('#modalClavesProdFac').modal('hide');
  });

  $(document).on('click', '#bBuscarClaveUnidadProdFac', function () {
    tablaClavesUnidadProdFac();
    $('#modalClavesUnidadProdFac').modal('show');
  });

  $(document).on('click', '#bQuitarClaveUnidadProdFac', function () {
    $("#c_clave").val('');
  });

  $(document).on('click', '#tablaClavesUnidadProdFac tbody tr', function () {
    $("#c_unidad").val($(this).children('td:eq(0)').text());

    $('#modalClavesUnidadProdFac').modal('hide');
  });

  $(document).on('click', '#bBuscarReceptor', function () {
    tablaClientesFacturacion();
    $('#modalClienteFacturacion').modal('show');
  });

  $(document).on('click', '#tablaClientesFacturacion tbody tr', function () {
    if ($("#publicoGeneral").prop('checked')) {
      $("#publicoGeneral").trigger('click');
    }

    $("#rfcReceptor").val($(this).children('td:eq(0)').children('b.rfc').text());
    if ($(this).children('td:eq(1)').text() == 'Física') {
      $("#nombreReceptor").val($(this).children('td:eq(0)').children('span:eq(0)').text());
    } else {
      $("#nombreReceptor").val($(this).children('td:eq(0)').children('b.razon').text());
    }

    $("#regimenFiscalReceptor").val($(this).children('td:eq(0)').children('b.regimen').text());
    $("#cpReceptor").val($(this).children('td:eq(2)').children('b.cp').text());
    $("#emailReceptor").val($(this).children('td:eq(3)').children('b.email').text());

    if ($(this).children('td:eq(3)').children('b.email') == undefined) {
      $("#emailReceptor").val($(this).children('td:eq(3)').children('b.email2').text());
    }

    $('#modalClienteFacturacion').modal('hide');
  });

  $(document).on('click', '#bBuscarFolioFac', function () {
    tablaVentasFacturas();
    $('#modalVentasFacturacion').modal('show');
  });

  $(document).on('click', '#bQuitarFolioFac', function () {
    if ($("#folioVentaFacturacion").val() == '') {
      return;
    }

    $("#folioVentaFacturacion").val('');
    $("#folioVentaFacturacion").attr('attrID', '');
    $("#tipoComprobante").attr('disabled', false);
    $("#facturaGlobal").parent().removeClass('d-none');
    $("#publicoGeneral").parent().removeClass('d-none');
    $("#tablaConceptos tbody").html('');

    if (!$("#publicoGeneral").prop('checked')) {
      $("#nombreReceptor").val('');
      $("#nombreReceptor").prop('readonly', false);
      $("#rfcReceptor").val('');
      $("#rfcReceptor").prop('readonly', false);
      $("#usoCfdi").val('');
      $("#usoCfdi").prop('disabled', false);
      $("#regimenFiscalReceptor").val('');
      $("#regimenFiscalReceptor").prop('disabled', false);
      $("#cpReceptor").val('');
      $("#cpReceptor").prop('readonly', false);
      $("#emailReceptor").val('');
    }

    $("#bAgregarConceptoManual").removeClass('d-none');
    $("#bRecargarCoceptosVenta").addClass('d-none');
  });

  $(document).on('click', '#tablaVentasFacturas tbody tr', function (e) {
    if (e.target.classList.contains('bDetalles')) {
      return;
    }

    $("#folioVentaFacturacion").val($(this).children('td:eq(1)').text());
    $("#folioVentaFacturacion").attr('attrID', $(this).attr('id'));
    $("#tipoComprobante").val('I - Ingreso');
    $("#tipoComprobante").attr('disabled', true);

    if ($("#facturaGlobal").prop('checked')) {
      $("#facturaGlobal").trigger('click');
    }
    $("#facturaGlobal").parent().addClass('d-none');

    if ($.trim($(this).children('td:eq(3)').text()).toUpperCase() == 'PUBLICO EN GENERAL') {
      $("#publicoGeneral").parent().removeClass('d-none');

      if (!$("#publicoGeneral").prop('checked')) {
        $("#publicoGeneral").trigger('click');
      }
    } else {
      if ($("#publicoGeneral").prop('checked')) {
        $("#publicoGeneral").trigger('click');
      }
      $("#publicoGeneral").parent().addClass('d-none');

      $("#nombreReceptor").val($(this).children('td:eq(3)').children('span.nombre').text());
      $("#nombreReceptor").prop('readonly', true);
      $("#rfcReceptor").val($(this).children('td:eq(3)').children('span.rfc').text());
      $("#rfcReceptor").prop('readonly', true);
      $("#usoCfdi").prop('disabled', false);
      $("#regimenFiscalReceptor").val($(this).children('td:eq(3)').children('span.regimen').text());
      $("#regimenFiscalReceptor").prop('disabled', true);
      $("#cpReceptor").val($(this).children('td:eq(3)').children('span.cp').text());
      $("#cpReceptor").prop('readonly', true);
      $("#emailReceptor").val($(this).children('td:eq(3)').children('span.email').text());
    }

    $("#bAgregarConceptoManual").addClass('d-none');
    $("#bRecargarCoceptosVenta").removeClass('d-none');
    verConceptosVenta();
    $('#modalVentasFacturacion').modal('hide');
  });

  $(document).on('click', '#bRecargarCoceptosVenta', function () {
    verConceptosVenta();
  });

  $(document).on('change', '#metodoPago', function () {
    if ($(this).val() == 'PPD - Pago en parcialidades o diferido') {
      $("#formaPago").val('99 - Por definir');
      $("#formaPago").prop('disabled', true);
    } else {
      $("#formaPago").prop('disabled', false);
    }
  });

  $(document).on('change', '#tipoComprobante', function () {
    if ($(this).val() == 'P - Complemento de Pago') {
      $("#metodoPago").val('');
      $('#metodoPago').attr('disabled', true);
      $("#metodoPago").parent().addClass('d-none');
      $("#cardConceptos").addClass('d-none');
      $("#totalesFactura").addClass('d-none');
      $("#cardDocRelacionados").removeClass('d-none');
      $("#usoCfdi").val('CP01 - Pagos');
      $("#usoCfdi").prop('disabled', true);
      $("#folioVentaFacturacion").val('');
      $("#folioVentaFacturacion").attr('attrID', '');
      $("#folioVentaFacturacion").parent().parent().addClass('d-none');
      $("#facturaGlobal").parent().addClass('d-none');
    } else {
      $('#metodoPago').attr('disabled', false);
      $("#metodoPago").parent().removeClass('d-none');
      $("#cardConceptos").removeClass('d-none');
      $("#totalesFactura").removeClass('d-none');
      $("#cardDocRelacionados").addClass('d-none');
      $("#usoCfdi").prop('disabled', false);
      $("#folioVentaFacturacion").parent().parent().removeClass('d-none');
      $("#facturaGlobal").parent().removeClass('d-none');
    }
  });

  // Abrir modal de Agregar Documento
  $(document).on('click', '#bAgregarDocRelacionado', function () {
    $('#formDoctoRel')[0].reset();
    impuestosPagoTemporal = [];
    $('#tablaImpuestosPagos tbody').empty();
    $('#modalDoctoRelacionado').modal('show');
  });

  // Calcular Saldo Insoluto automáticamente
  $(document).on('input', '.calcPago', function () {
    const saldoAnt = parseFloat($('#p_saldo_ant').val()) || 0;
    const montoPagado = parseFloat($('#p_monto_pagado').val()) || 0;
    let insoluto = saldoAnt - montoPagado;
    $('#p_saldo_insoluto').val(insoluto.toFixed(2));
  });

  $(document).on('click', '#btnAgregarImpuestoPago', function () {
    let tipo = $('#p_imp_tipo').val();
    let impuesto = $('#p_imp_impuesto').val();
    let nombre = $('#p_imp_impuesto option:selected').text();
    let factor = $('#p_imp_tipoFactor').val();
    let tasa = parseFloat($('#p_imp_tasa').val()) || 0;
    let montoPagado = parseFloat($('#p_monto_pagado').val()) || 0;

    if (!montoPagado || !tasa) {
      Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Ingresa el monto pagado y la tasa.' });
      return;
    }

    impuestosPagoTemporal.push({ tipo, impuesto, nombre, factor, tasa, base: 0, importe: 0 });

    recalcularImpuestosPago();

    $('#p_imp_tasa').val('');
  });

  // Recalcular si el usuario cambia el monto pagado manualmente
  $(document).on('change keyup', '#p_monto_pagado', function () {
    recalcularImpuestosPago();
  });

  // Eliminar impuesto y recalcular
  $(document).on('click', '.bEliminarImpPago', function () {
    let index = $(this).data('index');
    impuestosPagoTemporal.splice(index, 1);
    recalcularImpuestosPago();
  });

  // Formulario: Agregar a la lista de Documentos Relacionados
  $(document).on('submit', '#formDoctoRel', function (e) {
    e.preventDefault();

    const datos = {
      uuid: $('#p_uuid').val(),
      parcialidad: $('#p_parcialidad').val(),
      saldoAnt: parseFloat($('#p_saldo_ant').val()),
      montoPagado: parseFloat($('#p_monto_pagado').val()),
      saldoInsoluto: parseFloat($('#p_saldo_insoluto').val()),
      impuestos: [...impuestosPagoTemporal]
    };

    let badgeImpuestos = datos.impuestos.map(i =>
      `<span class="badge bg-secondary d-block mb-1">${i.nombre} (<span class="${(i.factor === 'Tasa' ? 'porcentaje' : 'dinero')}">${i.tasa}</span>): <span class="dinero">${i.importe.toFixed(2)}</span></span>`
    ).join('');

    const fila = `
      <tr data-json='${JSON.stringify(datos)}'>
        <td class="text-start small fw-bold">${datos.uuid}</td>
        <td>${datos.parcialidad}</td>
        <td class="dinero">${datos.saldoAnt}</td>
        <td class="text-success fw-bold dinero">${datos.montoPagado.toFixed(2)}</td>
        <td class="text-danger dinero">${datos.saldoInsoluto.toFixed(2)}</td>
        <td>${badgeImpuestos}</td>
        <td>
          <button type="button" class="btn btn-danger btn-sm bEliminarDocRel">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>`;

    $('#tablaDocRelacionados tbody').append(fila);

    $('#modalDoctoRelacionado').modal('hide');
    calcularTotalesPago();
  });

  // Eliminar fila de documento relacionado de la tabla principal
  $(document).on('click', '.bEliminarDocRel', function () {
    $(this).closest('tr').remove();
    calcularTotalesPago();
  });
});