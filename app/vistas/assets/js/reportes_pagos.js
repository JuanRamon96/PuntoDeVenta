var origenClienteReportePagos = 'filtro';
var adeudoClienteAbono = 0;
var montoAbonoPagosMask = null;

function limpiarModalClientesPagosHuerfano() {
  if ($('#verVista #modalClienteReportePagos').length === 0) return;
  $('body').children('#modalClienteReportePagos').remove();
}

function obtenerModalClientesPagos() {
  var $enVista = $('#verVista #modalClienteReportePagos');
  if ($enVista.length) {
    $('body').children('#modalClienteReportePagos').remove();
    return $enVista.first();
  }
  return $('body').children('#modalClienteReportePagos').add($('#modalClienteReportePagos')).first();
}

function v_reportes_pagos() {
  limpiarModalClientesPagosHuerfano();
  resumenAdeudoPagos();
  tablaPagosPendientes();
  tablaClientesReportePagos();
  initFormAbonoPagos();
}

function initFormAbonoPagos() {
  if ($('#formAbonoPagos').length === 0) return;
  if ($('#formAbonoPagos').data('validator')) {
    $('#formAbonoPagos').removeData('validator');
  }

  var inputMonto = document.getElementById('montoAbonoPagos');
  if (inputMonto) {
    if (montoAbonoPagosMask) {
      montoAbonoPagosMask.destroy();
    }
    montoAbonoPagosMask = IMask(inputMonto, {
      mask: Number,
      scale: 2,
      signed: false,
      thousandsSeparator: ',',
      padFractionalZeros: false,
      normalizeZeros: true,
      radix: '.',
      min: 0
    });
  }

  $('#formAbonoPagos').validate({
    ignore: [],
    rules: {
      abonoClienteNombre: { required: true },
      montoAbonoPagos: { required: true }
    },
    messages: {
      abonoClienteNombre: { required: 'Seleccione un cliente.' },
      montoAbonoPagos: {
        required: 'Ingrese el monto.'
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function () {
      var cliente = $('#abonoClienteId').val();
      var monto = parseFloat(montoAbonoPagosMask ? montoAbonoPagosMask.unmaskedValue : $('#montoAbonoPagos').val().replace(/,/g, '')) || 0;

      if (!cliente) {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'Seleccione el cliente al que se abonará el adeudo.'
        });
        return;
      }

      if (monto <= 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'Ingrese un monto mayor a 0.'
        });
        return;
      }

      if (monto > adeudoClienteAbono + 0.009) {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'El monto no puede ser mayor al adeudo del cliente.'
        });
        return;
      }

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: {
          metodo: 'insertar',
          accion: 'reportes',
          tipo: 'abono',
          cliente: cliente,
          monto: monto,
          tipoPago: $('#tipoPagoAbonoPagos').val(),
          caja: $('#cajaAbonoPagos').val()
        },
        beforeSend: function () {
          $('#carga').show();
        }
      })
        .done(function (res) {
          var respuesta = $.trim(res);
          if (respuesta === 'Correcto') {
            Swal.fire({
              icon: 'success',
              title: 'Abono aplicado',
              text: 'El pago se aplicó a los tickets más antiguos del cliente.'
            });
            $('#modalAbonoPagos').modal('hide');
            recargarPagos();
          } else if (respuesta === 'Error excede') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El monto no puede ser mayor al adeudo del cliente.'
            });
          } else if (respuesta === 'Error cliente') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'Seleccione un cliente.'
            });
          } else if (respuesta === 'Error monto' || respuesta === 'Error adeudo') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El cliente no tiene adeudo pendiente o el monto no es válido.'
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al aplicar el abono.'
            });
            console.log(respuesta);
          }
        })
        .fail(function () {
          console.log('error ajax abono');
        })
        .always(function () {
          $('#carga').hide();
        });
    }
  });
}

function clienteFiltroPagos() {
  return $('#filtroClientePagosId').val() || '';
}

function resumenAdeudoPagos() {
  if ($('#pagosAdeudoValor').length === 0) return;

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: {
      metodo: 'consultar',
      accion: 'reportes',
      tipo: 'resumenPagos',
      cliente: clienteFiltroPagos()
    },
    beforeSend: function () {
      $('#carga').show();
    }
  })
    .done(function (res) {
      var datos = JSON.parse($.trim(res));
      $('#pagosTicketsValor').text(datos.Tickets || '0');
      $('#pagosTotalValor').html(datos.Total || '$0.00');
      $('#pagosAdeudoValor').html(datos.Adeudo || '$0.00');
      moneda();
    })
    .fail(function () {
      console.log('error resumen pagos');
    })
    .always(function () {
      $('#carga').hide();
    });
}

function tablaPagosPendientes() {
  if ($('#tablaPagosPendientes').length === 0) return;

  ajaxMyDatatable({
    table: $('#tablaPagosPendientes'),
    colums: [
      'Fecha',
      'Folio',
      'Cliente',
      'Tipo_Pago',
      'Total',
      'Pagado',
      'Restante',
      'Estatus'
    ],
    sort: [0, 'asc'],
    url: 'index.php',
    params: {
      metodo: 'consultar',
      accion: 'reportes',
      tipo: 'pagos',
      cliente: clienteFiltroPagos()
    },
    totals: {
      4: 'Total',
      5: 'Pagado',
      6: 'Restante'
    }
  });
}

function tablaClientesReportePagos() {
  var $modal = $('#modalClienteReportePagos.show').last();
  if ($modal.length === 0) {
    $modal = $('body').children('#modalClienteReportePagos').last();
  }
  if ($modal.length === 0) {
    $modal = $('#modalClienteReportePagos').last();
  }
  var $tabla = $modal.find('table.myDataTable').first();
  if ($tabla.length === 0) return;

  ajaxMyDatatable({
    table: $tabla.eq(0),
    colums: [
      'Nombre',
      'Tipo',
      'Domicilio',
      'Contacto'
    ],
    sort: [0, 'asc'],
    url: 'index.php',
    params: {
      metodo: 'consultar',
      accion: 'clientes'
    }
  });
}

function nombreClienteFilaPagos($fila) {
  var razon = $.trim($fila.children('td:eq(0)').children('b.razon').text());
  if (razon !== '') return razon;
  var span = $.trim($fila.children('td:eq(0)').children('span').first().text());
  if (span !== '') return span;
  return $.trim($fila.children('td:eq(0)').text());
}

function cargarAdeudoClienteAbono(idCliente, nombre) {
  if (!idCliente) {
    adeudoClienteAbono = 0;
    $('#abonoAdeudoCliente').text('0');
    moneda();
    return;
  }

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: {
      metodo: 'consultar',
      accion: 'reportes',
      tipo: 'adeudoCliente',
      cliente: idCliente
    },
    beforeSend: function () {
      $('#carga').show();
    }
  })
    .done(function (res) {
      var datos = JSON.parse($.trim(res));
      adeudoClienteAbono = parseFloat(datos.Adeudo) || 0;
      $('#abonoAdeudoCliente').text(adeudoClienteAbono);
      if (nombre) {
        $('#abonoClienteNombre').val(nombre);
      } else if (datos.Cliente) {
        $('#abonoClienteNombre').val(datos.Cliente);
      }
      moneda();
    })
    .fail(function () {
      console.log('error adeudo cliente');
    })
    .always(function () {
      $('#carga').hide();
    });
}

function abrirModalClientesPagos(origen) {
  origenClienteReportePagos = origen;
  var $modal = obtenerModalClientesPagos();
  if ($modal.length === 0) return;
  $modal.appendTo('body');
  $modal.modal('show');
}

function recargarPagos() {
  resumenAdeudoPagos();
  tablaPagosPendientes();
}

function cargarCajasAbiertasPagos() {
  var $select = $('#cajaAbonoPagos');
  if ($select.length === 0) return;

  $select.html('<option value="">--Selecciona una caja--</option>');

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: {
      metodo: 'consultar',
      accion: 'reportes',
      tipo: 'cajasAbiertas'
    }
  })
    .done(function (res) {
      var datos = [];
      try {
        datos = JSON.parse($.trim(res));
      } catch (e) {
        datos = [];
      }

      if (Array.isArray(datos) && datos.length > 0) {
        datos.forEach(function (caja) {
          $select.append('<option value="' + caja.ID_Caja + '">' + caja.Nombre + '</option>');
        });
      }
    })
    .fail(function () {
      console.log('error cajas abiertas');
    });
}

jQuery(document).ready(function ($) {
  $(document).on('click', '#bRecargarClientesReportePagos', function () {
    tablaClientesReportePagos();
  });

  $(document).on('click', '#bBuscarClienteFiltroPagos, #filtroClientePagosNombre', function () {
    abrirModalClientesPagos('filtro');
  });

  $(document).on('click', '#bQuitarClienteFiltroPagos', function () {
    $('#filtroClientePagosId').val('');
    $('#filtroClientePagosNombre').val('');
    recargarPagos();
  });

  $(document).on('click', '#bAbonarAdeudoPagos', function () {
    var idFiltro = clienteFiltroPagos();
    var nombreFiltro = $('#filtroClientePagosNombre').val();
    $('#formAbonoPagos')[0].reset();
    $('#tipoPagoAbonoPagos').val('Efectivo');
    $('#abonoClienteId').val(idFiltro);
    $('#abonoClienteNombre').val(nombreFiltro);
    adeudoClienteAbono = 0;
    $('#abonoAdeudoCliente').text('0');
    if (montoAbonoPagosMask) {
      montoAbonoPagosMask.value = '';
    }
    cargarCajasAbiertasPagos();
    $('#modalAbonoPagos').modal('show');
    if (idFiltro) {
      cargarAdeudoClienteAbono(idFiltro, nombreFiltro);
    }
  });

  $(document).on('click', '#bBuscarClienteAbonoPagos, #abonoClienteNombre', function () {
    abrirModalClientesPagos('abono');
  });

  $(document).on('click', '#bLiquidarAbonoPagos', function () {
    if (!$('#abonoClienteId').val()) {
      Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: 'Seleccione un cliente para liquidar.'
      });
      return;
    }
    if (adeudoClienteAbono <= 0) {
      Swal.fire({
        icon: 'info',
        title: 'Sin adeudo',
        text: 'Este cliente no tiene saldo pendiente.'
      });
      return;
    }
    $('#montoAbonoPagos').val('');
    if (montoAbonoPagosMask) {
      montoAbonoPagosMask.unmaskedValue = String(adeudoClienteAbono);
    } else {
      $('#montoAbonoPagos').val(adeudoClienteAbono.toFixed(2));
    }
  });

  $(document).on('click', '#tablaClientesReportePagos tbody tr', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).attr('id');
    if (!id) return;
    var nombre = nombreClienteFilaPagos($(this));
    var $modal = $(this).closest('.modal');

    if (origenClienteReportePagos === 'filtro') {
      $('#filtroClientePagosId').val(id);
      $('#filtroClientePagosNombre').val(nombre);
      $modal.modal('hide');
      recargarPagos();
      return;
    }

    $('#abonoClienteId').val(id);
    $('#abonoClienteNombre').val(nombre);
    $modal.modal('hide');
    cargarAdeudoClienteAbono(id, nombre);
  });

  $(document).on('shown.bs.modal', '#modalClienteReportePagos', function () {
    $(this).css('z-index', 1080);
    $('.modal-backdrop').last().css('z-index', 1075);
  });

  $(document).on('hidden.bs.modal', '#modalClienteReportePagos', function () {
    if ($('#modalAbonoPagos').hasClass('show')) {
      $('body').addClass('modal-open');
    }
  });
});
