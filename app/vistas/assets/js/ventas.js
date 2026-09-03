let importePagoVenta = null;
let porcentajeDescuentoModiVenta = null;
let cantidadDescuentoModiVenta = null;
let pagoModiVenta = null;

function v_ventas() {
  tablaVentas();

  importePagoVenta = IMask(
    document.getElementById('importePagoVenta'),
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

  porcentajeDescuentoModiVenta = IMask(
    document.getElementById('porcentajeDescuentoModiVenta'),
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

  cantidadDescuentoModiVenta = IMask(
    document.getElementById('cantidadDescuentoModiVenta'),
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

  pagoModiVenta = IMask(
    document.getElementById('pagoModiVenta'),
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


  $('#formPagoVenta').validate({
    rules: {
      importePagoVenta: {
        required: true
      },
      conceptoPagoVenta: {
        required: true
      },
      tipoDePagoVenta: {
        required: true
      },
    },
    messages: {
      importePagoVenta: {
        required: 'El importe es requerido.'
      },
      conceptoPagoVenta: {
        required: 'El concepto es requerido.'
      },
      tipoDePagoVenta: {
        required: 'El tipo es requerido.'
      }
    },
    errorClass: 'is-invalid',        
    errorElement: 'div',
    submitHandler: function (form) {
      var formData = new FormData(document.querySelector("#formPagoVenta"));
      formData.append("metodo", "detalles");
      formData.append("accion", "ventas");
      formData.append("tipo", "pago");
      formData.append('id', $("#bGuardarPagoVenta").attr('attrID'));

      formData.set('importePagoVenta', importePagoVenta.unmaskedValue);

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
          $("#carga").show();
        }
      }).done(function (res) {
        if ($.trim(res) == 'Correcto') {
          Swal.fire({
            icon: 'success',
            title: 'Correcto',
            text: 'Pago agregado correctamente.',
          });

          tablaVentas();
          $("#modalPagoVenta").modal('hide');
        } else if ($.trim(res) === 'Error 2 formato') {
          Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg, .svg o pdf'
          });
        } else if ($.trim(res) === 'Error 3 peso') {
          Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al guaradar el pago.',
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
}

function tablaVentas() {
  ajaxMyDatatable({
    table: $('#tablaVentas'),
    colums: [
      'Fecha',
      'Folio',
      'Tipo',
      'Cliente',
      'Total',
      'Pago',
      'Cambio',
      'Estatus',
      'Detalles',
      //'Sucursal',
      'Acciones'
    ],
    sort: [0, 'desc'],
    url: 'index.php',
    params: {
      metodo: 'consultar',
      accion: 'ventas',
      tipo: "ventas"
    }
  });
}

function tablaPagosVentas(id) {
  ajaxMyDatatable({
    table: $('#tablaVerPagosVentas'),
    colums: [
      'Fecha',
      'Concepto',
      'Tipo_Pago',
      'Monto',
      'Detalles',
      'Comprobante',
      'Acciones'
    ],
    sort: [0, 'desc'],
    url: 'index.php',
    params: {
      metodo: 'detalles',
      accion: 'ventas',
      tipo: "pagos",
      id: id
    }
  });
}

function tablaProdModiVen() {
  ajaxMyDatatable({
    "table": $("#tablaProdModiVen"),
    "colums": [
      "Codigo",
      "Descripcion",
      "Clase",
      "Precio",
      "Precio_Mayoreo",
      "Existencia",
      "Impuestos"
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

function tablaClienteModiVen() {
  ajaxMyDatatable({
    "table": $("#tablaClienteModiVen"),
    "colums": [
      "Nombre",
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

function tablaDetalles(id) {
  ajaxMyDatatable({
    table: $('#tablaDetalles'),
    colums: [
      'Producto',
      'Precio',
      'Cantidad',
      'Descuento',
      'Impuestos',
      'Total'
    ],
    sort: [0, 'asc'],
    url: 'index.php',
    params: {
      metodo: 'detalles',
      accion: 'ventas',
      tipo: "venta",
      idVenta: id
    }
  });
}

function totalModiVenta() {
  var subtotal = 0;
  $("#tablaModiVenta").children('tbody').children('tr').each(function (index, el) {
    subtotal += parseFloat($(this).children('td:eq(6)').children('span').text().replace('$', '').replaceAll(',', '')) || 0
  });

  $("#subtotalModiVenta").text(subtotal);
  var porcentaje = parseFloat($("#porcentajeDescuentoModiVenta").val().replaceAll(',', '')) || 0;
  var descuento = Math.round(((porcentaje / 100) * subtotal) * 100) / 100;

  cantidadDescuentoModiVenta.value = '';
  cantidadDescuentoModiVenta.value = descuento.toString();

  var total = subtotal - descuento;
  $("#totalModiVenta").text(total);
  var pago = parseFloat($("#pagoModiVenta").val().replaceAll(',', '')) || 0;

  var cambio = pago - total;
  var restante = 0;
  if (cambio < 0) {
    cambio = 0;
    restante = total - pago;
  }

  $("#cambioModiVen").text(cambio);
  $("#restanteModiVen").text(restante);

  moneda();
}

$(document).ready(function () {
  $(document).on('click', '.bDetalles', function () {
    tablaDetalles($(this).attr('idVenta'));
    $('#modalDetalles').modal('show');
  });

  $(document).on('click', '.bCancelarVenta', async function () {
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
        var regresarInventario = 0;
        Swal.fire({
          title: '¿Desea regresar los productos a inventario?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: 'No',
          confirmButtonText: 'Sí',
        }).then((result) => {
          if (result.value) {
            regresarInventario = 1;
          }

          const data = `metodo=modificar&accion=ventas&idVenta=${idVenta}&regresarInventario=${regresarInventario}`

          $.ajax({
            url: 'index.php',
            type: 'POST',
            data,
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

              tablaVentas();
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
        });
      }
    });
  });

  $(document).on('click', '.bEliminarVenta', async function () {
    const idVenta = $(this).attr('attrID');

    Swal.fire({
      title: '¿Estás seguro de eliminar la venta?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        const data = `metodo=eliminar&accion=ventas&idVenta=${idVenta}`;

        $.ajax({
          url: 'index.php',
          type: 'POST',
          data,
          beforeSend: function () {
            $("#carga").show();
          },
        }).done(function (res) {
          if ($.trim(res) == 'Correcto') {
            Swal.fire({
              icon: 'success',
              title: 'Correcto',
              text: 'Venta cancelada correctamente',
            });

            tablaVentas();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar la venta.',
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

  $(document).on('click', '.bPagosVenta', function () {
    $('#formPagoVenta')[0].reset();
    var validator = $("#formPagoVenta").validate();
    validator.resetForm();
    $("#bGuardarPagoVenta").attr('attrID', $(this).attr('attrID'));

    const data = 'metodo=detalles&accion=ventas&tipo=totales&id=' + $(this).attr('attrID');

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data,
      beforeSend: function () {
        $("#carga").show();
      },
    }).done(function (res) {
      //console.log($.trim(res));
      var datos = JSON.parse($.trim(res));
      //console.log(datos);

      $("#totalVentaPago").html(datos.Total);

      var pago = parseFloat(datos.Pago) - parseFloat(datos.Cambio) + parseFloat(datos.TotalPagos);
      $("#pagosVenta").html(pago);

      var restante = parseFloat(datos.Total) - pago;
      if (restante < 0) {
        restante = 0;
      }
      $("#restanteVenta").html(restante);

      moneda();
      $("#modalPagoVenta").modal('show');
    }).fail(function () {
      console.log("Error ajax");
    }).always(function () {
      $("#carga").hide();
    });
  });

  $(document).on('click', '.bVerPagosVen', function () {
    tablaPagosVentas($(this).attr('attrID'));
    $("#bRecargarPagosVenta").attr('attrID', $(this).attr('attrID'));
    $("#modalVerPagosVentas").modal('show');
  });

  $(document).on('click', '.bEliminarPagoVenta', function () {
    var btn = $(this);
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar el pago?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        var data = "metodo=detalles&accion=ventas&tipo=eliminarPago&id=" + btn.attr('attrID') + "&archivo=" + btn.attr('archivo');

        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function () {
            $("#carga").show();
          }
        })
          .done(function (res) {
            if ($.trim(res) == "Correcto") {
              Swal.fire({
                icon: 'success',
                title: 'Pago eliminado correctamente'
              });

              tablaPagosVentas(btn.attr('attrVenta'));
              tablaVentas();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Error inesperado al eliminar el pago.'
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
  });

  $(document).on('change', '#tipoDePagoVenta', function () {
    if ($(this).val() == 'Efectivo') {
      $("#cajaPagoVenta").parent().parent().removeClass('oculto');
    } else {
      $("#cajaPagoVenta").val("");
      $("#cajaPagoVenta").parent().parent().addClass('oculto');
    }
  });

  $(document).on('click', '.bDevoluciones', function () {
    var btn = $(this);
    $('#bGuardarDevolucion').attr('attrID', $(this).attr('attrID'));

    var data = "metodo=detalles&accion=ventas&tipo=devoluciones&id=" + btn.attr('attrID');

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $("#carga").show();
      }
    })
      .done(function (res) {
        $("#tablaDevoluciones").children('tbody').html($.trim(res));

        moneda();
        $('#modalDevoluciones').modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $("#carga").hide();
      });
  });

  $(document).on('click', '#bGuardarDevolucion', function (event) {
    var btn = $(this);
    for (var i = 0; i < $("#tablaDevoluciones").children('tbody').children('tr').length; i++) {
      if (parseFloat($("#tablaDevoluciones").children('tbody').children('tr:eq(' + i + ')').children('td:eq(5)').children('input').val()) < 0 || parseFloat($("#tablaDevoluciones").children('tbody').children('tr:eq(' + i + ')').children('td:eq(5)').children('input').val()) > parseFloat($("#tablaDevoluciones").children('tbody').children('tr:eq(' + i + ')').children('td:eq(2)').text().replaceAll(',', ''))) {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'Las cantidades devueltas no deben ser menores a 0 o mayores a la cantidad de producto en la venta.'
        });
        return;
      }
    }

    var productos = [];
    $("#tablaDevoluciones").children('tbody').children('tr').each(function (index, el) {
      productos.push({ 'id': $(this).attr('attrID'), 'cantidad': $(this).children('td:eq(5)').children('input').val() });
    });

    var data = "metodo=detalles&accion=ventas&tipo=devolucion&productos=" + JSON.stringify(productos);

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $("#carga").show();
      }
    })
      .done(function (res) {
        if ($.trim(res) == "Correcto") {
          Swal.fire({
            icon: 'success',
            title: 'Las devoluciones han sido guardadas correctamente'
          });

          $('#modalDevoluciones').modal('hide');
          tablaVentas();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al guardar las devoluciones.'
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
  });

  $(document).on('click', '.bModificarVenta', function () {
    var btn = $(this);
    $("#bGuardarModiVenta").attr('attrID', $(this).attr('attrID'));

    const data = "metodo=detalles&accion=ventas&tipo=modificar&idVenta=" + btn.attr('attrID');

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data,
      beforeSend: function () {
        $("#carga").show();
      }
    }).done(function (res) {
      //console.log($.trim(res));
      var datos = JSON.parse($.trim(res));
      //console.log(datos);

      if (datos.FK_Cliente != 0) {
        $("#clienteModiVen").val(datos.Cliente);
        $("#clienteModiVen").attr('attrID', datos.FK_Cliente);
      } else {
        $("#clienteModiVen").val("Publico en General");
        $("#clienteModiVen").attr('attrID', 0);
      }

      var productos = '<tr class="noExisten"><td colspan="8">No existen productos</td></tr>';

      if (Object.keys(datos.Productos).length > 0) {
        let listaProductos = Array.isArray(datos.Productos) ? datos.Productos : [datos.Productos];
        productos = '';

        listaProductos.forEach((producto) => {
          let subAntes = parseFloat(producto.Precio) * parseFloat(producto.Cantidad);
          let subtotal = (parseFloat(producto.Precio) * parseFloat(producto.Cantidad)) - parseFloat(producto.Descuento);

          let verImpuestos = '';
          let totalImpuestos = 0;
          producto.Impuestos.forEach((impuesto) => {
            let impuestoFila = 0;
            if ((impuesto.Tipo_Factor || '').toUpperCase() === 'CUOTA') {
              impuestoFila = parseFloat(producto.Cantidad) * parseFloat(impuesto.Porcentaje);
            } else {
              impuestoFila = subtotal * (parseFloat(impuesto.Porcentaje) / 100);
            }

            // --- Lógica de Trasladado o Retenido ---
            let clase = (impuesto.Clase || '').toUpperCase();
            if (clase === 'RETENIDO') {
              totalImpuestos -= impuestoFila;
            } else {
              totalImpuestos += impuestoFila;
            }

            verImpuestos += `<p class="m-0" attrID="` + impuesto.ID_Impuesto_Venta + `" clave="` + impuesto.Clave_CFDI + `"><b>` + impuesto.Clase + `</b> <span>${impuesto.Nombre}</span> <span class="valor ` + (impuesto.Tipo_Factor == 'Cuota' ? 'dinero' : 'porcentaje') + `">${impuesto.Porcentaje}</span> (<span class="dinero">${impuestoFila}</span>) - <b>` + impuesto.Tipo_Factor + `</b></p>`;
          });

          // Calculamos el total sumando (o restando retenciones) al subtotal
          let totalFinalFila = subtotal + totalImpuestos;

          productos += `<tr attrID="` + producto.FK_Producto + `">
            <td>`+ producto.Codigo + `</td>
            <td>`+ producto.Descripcion + `</td>
            <td><span class="dinero">`+ producto.Precio + `</span></td>
            <td><input type="number" class="form-control form-control-sm cantidadModiVenTabla" step="any" value="`+ producto.Cantidad + `" /></td>
            <td>
              <div class="input-group input-group-sm">
                <input type="number" class="form-control porcentajeDesModiVenTabla" value="` + ((producto.Descuento / subAntes) * 100) + `" step="any" placeholder="Porcentaje">
                <span class="input-group-text">% = $</span>
                <input type="number" class="form-control cantidadDesModiVenTabla" value="` + producto.Descuento + `" step="any" placeholder="Cantidad">
              </div>
            </td>s
            <td><b>SUB: <span class="dinero">` + subtotal + `</span></b> ` + verImpuestos + `</td>
            <td><span class="dinero">`+ totalFinalFila + `</span></td>
            <td><button type="button" class="btn btn-danger btn-sm bQuitarProductoModiVen"><i class="fas fa-trash"></i></button></td>
          </tr>`;
        });
      }

      $("#tablaModiVenta").children('tbody').html(productos);

      var subtotal = parseFloat(datos.Total) + parseFloat(datos.Descuento);
      $("#subtotalModiVenta").html(subtotal);
      $("#totalModiVenta").html(datos.Total);

      cantidadDescuentoModiVenta.value = '';
      cantidadDescuentoModiVenta.value = datos.Descuento;

      porcentajeDescuentoModiVenta.value = '';
      porcentajeDescuentoModiVenta.value = (Math.round(((parseFloat(datos.Descuento) / subtotal) * 100) * 100) / 100).toString();

      pagoModiVenta.value = '';
      pagoModiVenta.value = datos.Pago;

      $("#cambioModiVen").text(datos.Cambio);
      $("#metodoPagoModiVenta").val(datos.Tipo_Pago);
      $("#detallesModiVenta").val(datos.Detalles);

      var cambio = parseFloat(datos.Pago) - parseFloat(datos.Total);
      var restante = 0;
      if (cambio < 0) {
        cambio = 0;
        restante = parseFloat(datos.Total) - parseFloat(datos.Pago);
      }

      $("#cambioModiVen").text(cambio);
      $("#restanteModiVen").text(restante);

      moneda();
      $("#modalModificarVenta").modal('show');
      $("#codigoProdModiVen").focus();
    }).fail(function () {
      console.log("Error ajax");
    }).always(function () {
      $("#carga").hide();
    });
  });

  $(document).on('change keyup', '.cantidadModiVenTabla', function () {
    var padre = $(this).parent().parent();
    var cantidad = parseFloat($(this).val()) || 0;
    var precio = parseFloat(padre.children('td:eq(2)').children('span').text().replace('$', '').replaceAll(',', '')) || 0;
    var porcentaje = parseFloat(padre.children('td:eq(4)').children('div').children('input.porcentajeDesModiVenTabla').val()) || 0
    var subtotal = cantidad * precio;
    var descuento = Math.round(((porcentaje / 100) * subtotal) * 100) / 100;
    var total = subtotal - descuento;
    padre.children('td:eq(5)').children('b').children('span.dinero').text(total);

    var totalImpuestos = 0;
    padre.children('td:eq(5)').children('p').each(function () {
      let clase = $(this).children('b:first').text().trim().toUpperCase();
      let factor = $(this).children('b:last').text().trim().toUpperCase();
      let valorImpuesto = parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replace(',', '')) || 0;
      let impuestoCalculado = 0;

      if (factor === 'EXENTO') {
        impuestoFila = 0; // Por definición, no genera importe
      } else if (factor === 'CUOTA') {
        let cantidad = parseFloat(padre.find('input.cantidadModiVenTabla').val()) || 0;
        impuestoCalculado = cantidad * valorImpuesto;
      } else {
        impuestoCalculado = total * (valorImpuesto / 100);
      }

      $(this).children('span.dinero').text(impuestoCalculado);

      if (clase === 'RETENCION' || clase === 'RETENIDO') {
        totalImpuestos -= impuestoCalculado;
      } else {
        totalImpuestos += impuestoCalculado;
      }
    });

    padre.children('td:eq(4)').children('div').children('input.cantidadDesModiVenTabla').val(descuento);
    padre.children('td:eq(6)').children('span').text(total + totalImpuestos);
    totalModiVenta();
  });

  $(document).on('change keyup', '.porcentajeDesModiVenTabla', function () {
    var padre = $(this).parent().parent().parent();
    var porcentaje = parseFloat($(this).val()) || 0;
    var cantidad = parseFloat(padre.children('td:eq(3)').children('input.cantidadModiVenTabla').val()) || 0;
    var precio = parseFloat(padre.children('td:eq(2)').children('span').text().replace('$', '').replaceAll(',', '')) || 0;
    var subtotal = cantidad * precio;
    var descuento = Math.round(((porcentaje / 100) * subtotal) * 100) / 100;
    var total = subtotal - descuento;
    padre.children('td:eq(5)').children('b').children('span.dinero').text(total);

    var totalImpuestos = 0;
    padre.children('td:eq(5)').children('p').each(function () {
      let clase = $(this).children('b:first').text().trim().toUpperCase();
      let factor = $(this).children('b:last').text().trim().toUpperCase();
      let valorImpuesto = parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replace(',', '')) || 0;
      let impuestoCalculado = 0;

      if (factor === 'EXENTO') {
        impuestoFila = 0; // Por definición, no genera importe
      } else if (factor === 'CUOTA') {
        let cantidad = parseFloat(padre.find('input.cantidadModiVenTabla').val()) || 0;
        impuestoCalculado = cantidad * valorImpuesto;
      } else {
        impuestoCalculado = total * (valorImpuesto / 100);
      }

      $(this).children('span.dinero').text(impuestoCalculado);

      if (clase === 'RETENCION' || clase === 'RETENIDO') {
        totalImpuestos -= impuestoCalculado;
      } else {
        totalImpuestos += impuestoCalculado;
      }
    });

    padre.children('td:eq(4)').children('div').children('input.cantidadDesModiVenTabla').val(descuento);
    padre.children('td:eq(6)').children('span').text(total + totalImpuestos);
    totalModiVenta();
  });

  $(document).on('change keyup', '.cantidadDesModiVenTabla', function () {
    var padre = $(this).parent().parent().parent();
    var descuento = parseFloat($(this).val()) || 0;
    var cantidad = parseFloat(padre.children('td:eq(3)').children('input.cantidadModiVenTabla').val()) || 0;
    var precio = parseFloat(padre.children('td:eq(2)').children('span').text().replace('$', '').replaceAll(',', '')) || 0;
    var subtotal = cantidad * precio;
    var porcentaje = Math.round(((descuento / subtotal) * 100) * 100) / 100;
    var total = subtotal - descuento;
    padre.children('td:eq(5)').children('b').children('span.dinero').text(total);

    var totalImpuestos = 0;
    padre.children('td:eq(5)').children('p').each(function () {
      let clase = $(this).children('b:first').text().trim().toUpperCase();
      let factor = $(this).children('b:last').text().trim().toUpperCase();
      let valorImpuesto = parseFloat($(this).children('span.valor').text().replace('%', '').replace('$', '').replace(',', '')) || 0;
      let impuestoCalculado = 0;

      if (factor === 'EXENTO') {
        impuestoFila = 0; // Por definición, no genera importe
      } else if (factor === 'CUOTA') {
        let cantidad = parseFloat(padre.find('input.cantidadModiVenTabla').val()) || 0;
        impuestoCalculado = cantidad * valorImpuesto;
      } else {
        impuestoCalculado = total * (valorImpuesto / 100);
      }

      $(this).children('span.dinero').text(impuestoCalculado);

      if (clase === 'RETENCION' || clase === 'RETENIDO') {
        totalImpuestos -= impuestoCalculado;
      } else {
        totalImpuestos += impuestoCalculado;
      }
    });

    padre.children('td:eq(4)').children('div').children('input.porcentajeDesModiVenTabla').val(porcentaje);
    padre.children('td:eq(6)').children('span').text(total + totalImpuestos);
    totalModiVenta();
  });

  $(document).on('change keyup', '#pagoModiVenta', function () {
    var total = parseFloat($("#totalModiVenta").text().replace('$', '').replaceAll(',', '')) || 0;
    var pago = parseFloat($(this).val().replaceAll(',', '')) || 0;

    var cambio = pago - total;
    var restante = 0;
    if (cambio < 0) {
      cambio = 0;
      restante = total - pago;
    }

    $("#cambioModiVen").text(cambio);
    $("#restanteModiVen").text(restante);

    moneda();
  });

  $(document).on('submit', '#formProdModiVen', function (event) {
    event.preventDefault();
    const data = 'metodo=consultar&accion=caja&tipo=agregarProducto&codigo=' + $.trim($('#codigoProdModiVen').val());

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
          audio2.play();

          setTimeout(function () {
            $("#noEncontrado").hide();
          }, 1000);
        } else {
          var resA = JSON.parse($.trim(res));
          var precio = parseFloat(resA.Precio) || 0;
          let impuestos = resA.Impuestos;
          var padre = $("#tablaModiVenta").children('tbody').children('tr[attrID=' + resA.ID_Producto + ']');

          if (padre.length > 0) {
            precio = parseFloat(padre.children('td:eq(2)').text().replace('$', '').replaceAll(',', '')) || 0;
            var cantidad = parseFloat(padre.children('td:eq(3)').children('input.cantidadModiVenTabla').val()) || 0;
            var porcentaje = parseFloat(padre.children('td:eq(4)').children('div').children('input.porcentajeDesModiVenTabla').val()) || 0;
            var subtotal = precio * (cantidad + 1);
            var descuento = (porcentaje / 100) * subtotal;
            var total = subtotal - descuento;

            let verImpuestos = '';
            let totalImpuestos = 0;

            impuestos.forEach((impuesto) => {
              let impuestoFila = 0;
              let porcentaje = parseFloat(impuesto.Porcentaje) || 0;
              let factor = (impuesto.Tipo_Factor || '').toUpperCase();
              let clase = (impuesto.Clase || '').toUpperCase();

              // --- Lógica de Tasa o Cuota ---
              if (factor === 'EXENTO') {
                impuestoFila = 0; // Por definición, no genera importe
              } else if (factor === 'CUOTA') {
                impuestoFila = (parseFloat(cantidad) || 0) * porcentaje;
              } else {
                impuestoFila = total * (porcentaje / 100);
              }

              // --- Lógica de Trasladado o Retenido para el acumulador ---
              if (clase === 'RETENCION' || clase === 'RETENIDO') {
                totalImpuestos -= impuestoFila;
              } else {
                totalImpuestos += impuestoFila;
              }

              verImpuestos += '<p class="m-0" attrID="' + impuesto.FK_Impuesto + '" clave="' + impuesto.Clave_CFDI + '"><b>' + impuesto.Clase + '</b> <span>' + impuesto.Nombre + '</span> <span class="valor ' + (impuesto.Tipo_Factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.Porcentaje + '</span> (<span class="dinero">' + impuestoFila + '</span>) - <b>' + impuesto.Tipo_Factor + '</b></p>';
            });

            padre.children('td:eq(5)').html('<b>SUB: <span class="dinero">' + total + '</span></b>' + verImpuestos);
            padre.children('td:eq(3)').children('input.cantidadModiVenTabla').val(cantidad + 1);
            padre.children('td:eq(6)').children('span').text(total + totalImpuestos);
          } else {
            console.log($("#tablaModiVenta").children('tbody').children('tr.noExisten'));
            if ($("#tablaModiVenta").children('tbody').children('tr.noExisten').length > 0) {
              $("#tablaModiVenta").children('tbody').html('');
            }

            let verImpuestos = '';
            let totalImpuestos = 0;

            impuestos.forEach((impuesto) => {
              let impuestoFila = 0;
              let valor = parseFloat(impuesto.Porcentaje) || 0;
              let factor = (impuesto.Tipo_Factor || '').toUpperCase();
              let clase = (impuesto.Clase || '').toUpperCase();

              // --- 1. Lógica de Tasa vs Cuota ---
              if (factor === 'EXENTO') {
                impuestoFila = 0; // Por definición, no genera importe
              } else if (factor === 'CUOTA') {
                impuestoFila = (parseFloat(cantidad) || 0) * valor;
              } else {
                impuestoFila = precio * (valor / 100);
              }

              // --- 2. Lógica de Trasladado o Retenido para el acumulador total ---
              if (clase === 'RETENCION' || clase === 'RETENIDO') {
                totalImpuestos -= impuestoFila;
              } else {
                totalImpuestos += impuestoFila;
              }

              verImpuestos += '<p class="m-0" attrID="' + impuesto.FK_Impuesto + '" clave="' + impuesto.Clave_CFDI + '"><b>' + impuesto.Clase + '</b> <span>' + impuesto.Nombre + '</span> <span class="valor ' + (impuesto.Tipo_Factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.Porcentaje + '</span> (<span class="dinero">' + impuestoFila + '</span>) - <b>' + impuesto.Tipo_Factor + '</b></p>';
            });

            let total = precio + totalImpuestos;

            $("#tablaModiVenta").children('tbody').append(`<tr attrID="` + resA.ID_Producto + `">
              <td>`+ resA.Codigo + `</td>
              <td>`+ resA.Descripcion + `</td>
              <td><span class="dinero">`+ precio + `</span></td>
              <td><input type="number" class="form-control form-control-sm cantidadModiVenTabla" step="any" value="1" /></td>
              <td>
                <div class="input-group input-group-sm">
                  <input type="number" class="form-control porcentajeDesModiVenTabla" value="0" step="any" placeholder="Porcentaje">
                  <span class="input-group-text">% = $</span>
                  <input type="number" class="form-control cantidadDesModiVenTabla" value="0" step="any" placeholder="Cantidad">
                </div>
              </td>
              <td><b>SUB: <span class="dinero">` + precio + `</span></b> ` + verImpuestos + `</td>
              <td><span class="dinero">`+ total + `</span></td>
              <td><button type="button" class="btn btn-danger btn-sm bQuitarProductoModiVen"><i class="fas fa-trash"></i></button></td>
            </tr>`);
          }

          totalModiVenta();
          audio1.play();
        }

        $("#carga").hide();
        $("#codigoProdModiVen").val('');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $("#carga").hide();
      });
  });

  $(document).on('click', '#bBuscarProdModiVen', function () {
    tablaProdModiVen();
    $("#modalProdModiVen").modal('show');
  });

  $(document).on('click', '.bQuitarProductoModiVen ', function () {
    $(this).parent().parent().remove();
    totalModiVenta();

    if ($("#tablaModiVenta").children('tbody').children('tr').length == 0) {
      $("#tablaModiVenta").children('tbody').html('<tr class="noExisten"><td colspan="8">No existen productos</td></tr>');
    }
  });

  $(document).on('click', '#tablaProdModiVen tbody tr', function () {
    var fila = $(this);
    var padre = $("#tablaModiVenta").children('tbody').children('tr[attrID=' + fila.attr('id') + ']');
    let precioFila = parseFloat(fila.children('td:eq(3)').text().replace('$', '').replaceAll(',', '')) || 0;

    let impuestos = [];
    fila.children('td:eq(6)').children('p').each(function (index, el) {
      impuestos.push({
        id: $(this).attr('attrID'),
        nombre: $(this).children('b:eq(0)').text(),
        valor: parseFloat($(this).children('span.valor').text().replace('$', '').replace('%', '').replaceAll(',', '')) || 0,
        clave: $(this).attr('clave'),
        factor: $(this).children('b:last').text().trim(),
        clase: $(this).children('span:first').text().trim()
      });
    });

    if (padre.length > 0) {
      var precio = parseFloat(padre.children('td:eq(2)').text().replace('$', '').replaceAll(',', '')) || 0;
      var cantidad = parseFloat(padre.children('td:eq(3)').children('input.cantidadModiVenTabla').val()) || 0;
      var porcentaje = parseFloat(padre.children('td:eq(4)').children('div').children('input.porcentajeDesModiVenTabla').val()) || 0;
      var subtotal = precio * (cantidad + 1);
      var descuento = (porcentaje / 100) * subtotal;
      var total = subtotal - descuento;

      let verImpuestos = '';
      let totalImpuestos = 0;
      impuestos.forEach((impuesto) => {
        let impuestoFila = 0;
        let valor = parseFloat(impuesto.valor) || 0;
        let factor = (impuesto.factor || '').toUpperCase();
        let clase = (impuesto.clase || '').toUpperCase();

        // --- Lógica de Tasa o Cuota ---
        if (factor === 'EXENTO') {
          impuestoFila = 0; // Por definición, no genera importe
        } else if (factor === 'CUOTA') {
          impuestoFila = (parseFloat(cantidad) || 0) * valor;
        } else {
          impuestoFila = total * (valor / 100);
        }

        // --- Lógica de Trasladado o Retenido para el acumulador total ---
        if (clase === 'RETENCION' || clase === 'RETENIDO') {
          totalImpuestos -= impuestoFila;
        } else {
          totalImpuestos += impuestoFila;
        }

        verImpuestos += '<p class="m-0" attrID="' + impuesto.id + '" clave="' + impuesto.clave + '"><b>' + impuesto.clase + '</b> <span>' + impuesto.nombre + '</span> <span class="valor ' + (impuesto.factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.valor + '</span> (<span class="dinero">' + impuestoFila + '</span>) - <b>' + impuesto.factor + '</b></p>';
      });

      padre.children('td:eq(5)').html('<b>SUB: <span class="dinero">' + total + '</span></b>' + verImpuestos);
      padre.children('td:eq(3)').children('input.cantidadModiVenTabla').val(cantidad + 1);
      padre.children('td:eq(6)').children('span').text(total + totalImpuestos);
    } else {
      if ($("#tablaModiVenta").children('tbody').children('tr.noExisten').length > 0) {
        $("#tablaModiVenta").children('tbody').html('');
      }

      let verImpuestos = '';
      let totalImpuestos = 0;
      impuestos.forEach((impuesto) => {
        let impuestoFila = 0;
        let valor = parseFloat(impuesto.valor) || 0;
        let factor = (impuesto.factor || '').toUpperCase();
        let clase = (impuesto.clase || '').toUpperCase();

        // --- Lógica de Tasa o Cuota ---
        if (factor === 'EXENTO') {
          impuestoFila = 0; // Por definición, no genera importe
        } else if (factor === 'CUOTA') {
          impuestoFila = (parseFloat(cantidad) || 0) * valor;
        } else {
          impuestoFila = precioFila * (valor / 100);
        }

        // --- Lógica de Trasladado o Retenido ---
        if (clase === 'RETENCION' || clase === 'RETENIDO') {
          totalImpuestos -= impuestoFila;
        } else {
          totalImpuestos += impuestoFila;
        }

        verImpuestos += '<p class="m-0" attrID="' + impuesto.id + '" clave="' + impuesto.clave + '"><b>' + impuesto.clase + '</b> <span>' + impuesto.nombre + '</span> <span class="valor ' + (impuesto.factor === 'Cuota' ? 'dinero' : 'porcentaje') + '">' + impuesto.valor + '</span> (<span class="dinero">' + impuestoFila + '</span>) - <b>' + impuesto.factor + '</b></p>';
      });

      let total = precioFila + totalImpuestos;

      $("#tablaModiVenta").children('tbody').append(`<tr attrID="` + fila.attr('id') + `">
        <td>`+ fila.children('td:eq(0)').text() + `</td>
        <td>`+ fila.children('td:eq(1)').text() + `</td>
        <td><span class="dinero">`+ fila.children('td:eq(3)').text() + `</span></td>
        <td><input type="number" class="form-control form-control-sm cantidadModiVenTabla" step="any" value="1" /></td>
        <td>
          <div class="input-group input-group-sm">
            <input type="number" class="form-control porcentajeDesModiVenTabla" value="0" step="any" placeholder="Porcentaje">
            <span class="input-group-text">% = $</span>
            <input type="number" class="form-control cantidadDesModiVenTabla" value="0" step="any" placeholder="Cantidad">
          </div>
        </td>
        <td><b>SUB: <span class="dinero">` + subtotal + `</span></b> ` + verImpuestos + `</td>
        <td><span class="dinero">`+ total + `</span></td>
        <td><button type="button" class="btn btn-danger btn-sm bQuitarProductoModiVen"><i class="fas fa-trash"></i></button></td>
      </tr>`);
    }

    totalModiVenta();
    $("#modalProdModiVen").modal('hide');
  });

  $(document).on('click', '#bQuitarClienteModiVen', function () {
    $("#clienteModiVen").val("Publico en General");
    $("#clienteModiVen").attr('attrID', 0);
  });

  $(document).on('change keyup', '#porcentajeDescuentoModiVenta', function () {
    var porcentaje = parseFloat($(this).val().replaceAll(',', '')) || 0;
    var subtotal = parseFloat($("#subtotalModiVenta").text().replace('$', '').replaceAll(',', '')) || 0;
    var descuento = Math.round(((porcentaje / 100) * subtotal) * 100) / 100;
    var total = subtotal - descuento;

    cantidadDescuentoModiVenta.value = '';
    cantidadDescuentoModiVenta.value = descuento.toString();

    $('#totalModiVenta').text(total);

    var pago = parseFloat($("#pagoModiVenta").val().replaceAll(',', '')) || 0;

    var cambio = pago - total;
    var restante = 0;
    if (cambio < 0) {
      cambio = 0;
      restante = total - pago;
    }

    $("#cambioModiVen").text(cambio);
    $("#restanteModiVen").text(restante);

    moneda();
  });

  $(document).on('change keyup', '#cantidadDescuentoModiVenta', function () {
    var descuento = parseFloat($(this).val().replaceAll(',', '')) || 0;
    var subtotal = parseFloat($("#subtotalModiVenta").text().replace('$', '').replaceAll(',', '')) || 0;
    var porcentaje = Math.round((descuento / subtotal) * 100) / 100;
    var total = subtotal - descuento;

    porcentajeDescuentoModiVenta.value = '';
    porcentajeDescuentoModiVenta.value = (porcentaje * 100).toFixed(2).toString();

    $('#totalModiVenta').text(total);

    var pago = parseFloat($("#pagoModiVenta").val().replaceAll(',', '')) || 0;

    var cambio = pago - total;
    var restante = 0;
    if (cambio < 0) {
      cambio = 0;
      restante = total - pago;
    }

    $("#cambioModiVen").text(cambio);
    $("#restanteModiVen").text(restante);

    moneda();
  });

  $(document).on('click', '#bBuscarClienteModiVen', function () {
    tablaClienteModiVen();
    $("#modalClienteModiVen").modal('show');
  });

  $(document).on('click', '#tablaClienteModiVen tbody tr', function () {
    var separa = $(this).children('td:eq(0)').html().split('<br>');

    $("#clienteModiVen").val($.trim(separa[0]));
    $("#clienteModiVen").attr('attrID', $(this).attr('id'));
    $("#modalClienteModiVen").modal('hide');
  });

  $(document).on('click', '#bGuardarModiVenta', function () {
    var btn = $(this);

    if ($("#metodoPagoModiVenta").val() == "Crédito" && $("#clienteModiVen").attr('attrID') == 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: 'Debes elegir un cliente.',
      });
    } else {
      Swal.fire({
        title: '¿Estás seguro de modificar la venta?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: '¡No, cancelar!',
        confirmButtonText: '¡Si, modificar!'
      }).then((result) => {
        if (result.value) {
          var productos = [];
          $("#tablaModiVenta").children('tbody').children('tr').each(function (index, el) {
            var impuestosProducto = [];
            $(this).children('td:eq(5)').children('p').each(function (index, el) {
              impuestosProducto.push({
                id: $(this).attr('attrID'),
                nombre: $(this).children('span:eq(0)').text(),
                porcentaje: parseFloat($(this).children('span.valor').text().replace('$', '').replace('%', '').replaceAll(',', '')),
                clave: $(this).attr('clave'),
                factor: $(this).children('b:last').text().trim(),
                clase: $(this).children('b:first').text().trim(),
              });
            });

            productos.push(
              {
                'idProd': $(this).attr('attrID'),
                'descripcion': $(this).children('td:eq(1)').text(),
                'precio': $(this).children('td:eq(2)').text().replace('$', '').replaceAll(',', ''),
                'cantidad': $(this).children('td:eq(3)').children('input.cantidadModiVenTabla').val(),
                'descuento': $(this).children('td:eq(4)').children('div').children('input.cantidadDesModiVenTabla').val(),
                'total': $(this).children('td:eq(6)').text().replace('$', '').replaceAll(',', ''),
                'impuestos': impuestosProducto
              }
            );
          });

          var datos = "metodo=detalles&accion=ventas&tipo=modificarVenta&cliente=" + $("#clienteModiVen").attr('attrID') 
          + "&descuento=" + $("#cantidadDescuentoModiVenta").val().replaceAll(',', '') + "&total=" + $("#totalModiVenta").text().replace('$', '').replaceAll(',', '') 
          + "&pago=" + $('#pagoModiVenta').val().replaceAll(',', '') + "&cambio=" + $("#cambioModiVen").text().replace('$', '').replaceAll(',', '') 
          + "&tipoPago=" + $('#metodoPagoModiVenta').val() + "&detalles=" + $.trim($('#detallesModiVenta').val()) 
          + "&id=" + btn.attr('attrID') + "&productos=" + JSON.stringify(productos);

          $.ajax({
            url: 'index.php',
            type: 'POST',
            data: datos,
            beforeSend: function () {
              $("#carga").show();
            }
          }).done(function (res) {
            if ($.trim(res) == 'Correcto') {
              Swal.fire({
                icon: 'success',
                title: 'Correcto',
                text: 'La venta ha sido modificada correctamente.',
              });

              tablaVentas();
              $("#modalModificarVenta").modal('hide');
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Error inesperado al modificar la venta.',
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
    }
  });

  $(document).on('click', '#bRecargarPagosVenta', function () {
    tablaPagosVentas($(this).attr('attrID'));
  });
});