let maskCantidadPromocion = null;
let maskPrecioFijoPromocion = null;
let maskDescuentoPromocion = null;

function v_promociones() {
  tablaPromociones();

  maskCantidadPromocion = IMask(
    document.getElementById('cantidadPromocion'),
    {
      mask: Number,
      scale: 2,
      signed: false,
      thousandsSeparator: ',',
      padFractionalZeros: false,
      normalizeZeros: true,
      radix: '.',
      min: 0
    }
  );

  maskPrecioFijoPromocion = IMask(
    document.getElementById('precioFijoPromocion'),
    {
      mask: Number,
      scale: 2,
      signed: false,
      thousandsSeparator: ',',
      padFractionalZeros: false,
      normalizeZeros: true,
      radix: '.',
      min: 0
    }
  );

  maskDescuentoPromocion = IMask(
    document.getElementById('descuentoPromocion'),
    {
      mask: Number,
      scale: 2,
      signed: false,
      thousandsSeparator: ',',
      padFractionalZeros: false,
      normalizeZeros: true,
      radix: '.',
      min: 0
    }
  );

  $('#formPromociones').validate({
    rules: {
      nombrePromocion: {
        required: true
      },
      cantidadPromocion: {
        required: true
      }
    },
    messages: {
      nombrePromocion: {
        required: "El nombre es requerido."
      },
      cantidadPromocion: {
        required: "La cantidad es requerida."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      if ($("#fkProductoPromocion").val() == '' && $("#fkClasificacionPromocion").val() == '') {
        $("#errorSeleccionPromocion").removeClass('oculto');
        return;
      }

      $("#errorSeleccionPromocion").addClass('oculto');

      var data = {
        metodo: $('#bGuardarPromocion').attr('tipo'),
        accion: 'promociones',
        id: $('#bGuardarPromocion').attr('attrID'),
        nombrePromocion: $('#nombrePromocion').val(),
        tipoPromocion: $('#tipoPromocion').val(),
        fkProducto: $('#fkProductoPromocion').val(),
        fkClasificacion: $('#fkClasificacionPromocion').val(),
        cantidadPromocion: maskCantidadPromocion.unmaskedValue,
        modoCantidadPromocion: $('input[name=modoCantidadPromocion]:checked').val(),
        tipoBeneficioPromocion: $('input[name=tipoBeneficioPromocion]:checked').val(),
        precioFijoPromocion: maskPrecioFijoPromocion.unmaskedValue,
        tipoDescuentoPromocion: $('input[name=tipoDescuentoPromocion]:checked').val(),
        descuentoPromocion: maskDescuentoPromocion.unmaskedValue
      };

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          if (String(res ?? '').trim() == "Correcto") {
            var tipoAlerta = $("#bGuardarPromocion").attr("tipo") == "modificar" ? "modificada" : "guardada";

            Swal.fire({
              icon: 'success',
              title: 'Promoción ' + tipoAlerta + ' correctamente'
            });

            tablaPromociones();
            $("#modalPromocion").modal("hide");
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al ' + $("#bGuardarPromocion").attr("tipo") + ' la promoción.'
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
}

function tablaPromociones() {
  ajaxMyDatatable({
    "table": $("#tablaPromociones"),
    "colums": [
      "Nombre",
      "Tipo",
      "Cantidad",
      "Beneficio",
      "Estatus",
      "Acciones"
    ],
    "sort": [0, "asc"],
    "url": "index.php",
    "params": {
      "metodo": "consultar",
      "accion": "promociones"
    }
  });
}

function tablaPromocionSeleccionProducto() {
  ajaxMyDatatable({
    "table": $("#tablaPromocionSeleccionProducto"),
    "colums": [
      "Codigo",
      "Descripcion",
      "Precio"
    ],
    "sort": [0, "asc"],
    "url": "index.php",
    "params": {
      "metodo": "consultar",
      "accion": "productos"
    }
  });
}

function tablaPromocionSeleccionClasificacion() {
  ajaxMyDatatable({
    "table": $("#tablaPromocionSeleccionClasificacion"),
    "colums": [
      "Nombre",
      "Descripcion"
    ],
    "sort": [0, "asc"],
    "url": "index.php",
    "params": {
      "metodo": "consultar",
      "accion": "clasificaciones"
    }
  });
}

function limpiarSeleccionPromocion() {
  $("#nombreSeleccionPromocion").val('');
  $("#fkProductoPromocion").val('');
  $("#fkClasificacionPromocion").val('');
}

function actualizarSeccionBeneficioPromocion() {
  if ($("#tipoPromocion").val() == 'Clasificacion') {
    $("#beneficioPrecioFijo").prop('disabled', true);
    $("#beneficioDescuento").prop('checked', true).trigger('change');
    $("#beneficioPrecioFijo").parent().addClass('oculto');
  } else {
    $("#beneficioPrecioFijo").prop('disabled', false);
    $("#beneficioPrecioFijo").parent().removeClass('oculto');
  }
}

jQuery(document).ready(function ($) {
  $(document).on('click', '#bAgregarPromocion', function () {
    $("#formPromociones")[0].reset();
    var validator = $("#formPromociones").validate();
    validator.resetForm();

    limpiarSeleccionPromocion();
    $("#errorSeleccionPromocion").addClass('oculto');

    maskCantidadPromocion.value = '';
    maskPrecioFijoPromocion.value = '';
    maskDescuentoPromocion.value = '';

    $("#tipoPromocion").val('Producto');
    $("#modoCantidadPaquete").prop('checked', true);
    $("#beneficioPrecioFijo").prop('checked', true).trigger('change');
    $("#descuentoPorcentaje").prop('checked', true);
    actualizarSeccionBeneficioPromocion();

    $("#bGuardarPromocion").attr('tipo', 'insertar');
    $("#bGuardarPromocion").attr('attrID', '');
    $("#modalPromocion").modal('show');
  });

  $(document).on('change', '#tipoPromocion', function () {
    limpiarSeleccionPromocion();
    actualizarSeccionBeneficioPromocion();
  });

  $(document).on('change', 'input[name=tipoBeneficioPromocion]', function () {
    if ($(this).val() == 'Precio_Fijo') {
      $("#seccionPrecioFijoPromocion").removeClass('oculto');
      $("#seccionDescuentoPromocion").addClass('oculto');
    } else {
      $("#seccionPrecioFijoPromocion").addClass('oculto');
      $("#seccionDescuentoPromocion").removeClass('oculto');
    }
  });


  $(document).on('click', '#bSeleccionarAplicaPromocion', function () {
    if ($("#tipoPromocion").val() == 'Producto') {
      tablaPromocionSeleccionProducto();
      $("#modalPromocionSeleccionProducto").modal('show');
    } else {
      tablaPromocionSeleccionClasificacion();
      $("#modalPromocionSeleccionClasificacion").modal('show');
    }
  });

  $(document).on('click', '#tablaPromocionSeleccionProducto tbody tr', function () {
    var codigo = $(this).children('td:eq(0)').text();
    var descripcion = $(this).children('td:eq(1)').text();

    $("#fkProductoPromocion").val($(this).attr('id'));
    $("#fkClasificacionPromocion").val('');
    $("#nombreSeleccionPromocion").val(codigo + ' - ' + descripcion);
    $("#errorSeleccionPromocion").addClass('oculto');

    $("#modalPromocionSeleccionProducto").modal('hide');
  });

  $(document).on('click', '#tablaPromocionSeleccionClasificacion tbody tr', function () {
    var nombre = $(this).children('td:eq(0)').text();

    $("#fkClasificacionPromocion").val($(this).attr('id'));
    $("#fkProductoPromocion").val('');
    $("#nombreSeleccionPromocion").val(nombre);
    $("#errorSeleccionPromocion").addClass('oculto');

    $("#modalPromocionSeleccionClasificacion").modal('hide');
  });

  $(document).on('click', '.bModificarPromocion', function () {
    var id = $(this).attr('attrID');

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: "metodo=detalles&accion=promociones&id=" + id,
      beforeSend: function () {
        $("#carga").show();
      }
    })
      .done(function (res) {
        var datos = JSON.parse(String(res ?? '').trim());

        $("#formPromociones")[0].reset();
        var validator = $("#formPromociones").validate();
        validator.resetForm();

        $("#nombrePromocion").val(datos.Nombre);
        $("#tipoPromocion").val(datos.Tipo);

        if (datos.Tipo == 'Producto') {
          $("#fkProductoPromocion").val(datos.FK_Producto);
          $("#fkClasificacionPromocion").val('');
        } else {
          $("#fkClasificacionPromocion").val(datos.FK_Clasificacion);
          $("#fkProductoPromocion").val('');
        }

        $("#nombreSeleccionPromocion").val(datos.NombreSeleccion);

        maskCantidadPromocion.value = '';
        maskCantidadPromocion.value = String(datos.Cantidad);

        if (datos.Modo_Cantidad == 'Minima') {
          $("#modoCantidadMinima").prop('checked', true);
        } else {
          $("#modoCantidadPaquete").prop('checked', true);
        }

        if (datos.Tipo_Beneficio == 'Precio_Fijo') {
          $("#beneficioPrecioFijo").prop('checked', true).trigger('change');
        } else {
          $("#beneficioDescuento").prop('checked', true).trigger('change');
        }

        actualizarSeccionBeneficioPromocion();

        maskPrecioFijoPromocion.value = '';
        maskPrecioFijoPromocion.value = String(datos.Precio_Fijo);

        maskDescuentoPromocion.value = '';
        maskDescuentoPromocion.value = String(datos.Descuento);

        if (datos.Tipo_Descuento == 'Monto') {
          $("#descuentoMonto").prop('checked', true);
        } else {
          $("#descuentoPorcentaje").prop('checked', true);
        }

        $("#errorSeleccionPromocion").addClass('oculto');

        $("#bGuardarPromocion").attr('tipo', 'modificar');
        $("#bGuardarPromocion").attr('attrID', datos.ID_Promocion);
        $("#modalPromocion").modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $("#carga").hide();
      });
  });

  $(document).on('change', '.bEstadoPromocion', function () {
    var btn = $(this);
    var estado = btn.prop('checked') ? 1 : 0;

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: {
        metodo: 'modificar',
        accion: 'promociones',
        tipo: 'estado',
        id: btn.attr('attrID'),
        estado: estado
      },
      beforeSend: function () {
        $("#carga").show();
      }
    })
      .done(function (res) {
        if (String(res ?? '').trim() != "Correcto") {
          btn.prop('checked', !btn.prop('checked'));

          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al actualizar el estatus de la promoción.'
          });

          console.log(String(res ?? '').trim());
        }
      })
      .fail(function () {
        btn.prop('checked', !btn.prop('checked'));
        console.log("Error ajax");
      })
      .always(function () {
        $("#carga").hide();
      });
  });

  $(document).on('click', '.bEliminarPromocion', function () {
    var btn = $(this);
    Swal.fire({
      title: '¿Estás seguro de eliminar la promoción?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'No, cancelar',
      confirmButtonText: 'Si, eliminar'
    }).then((result) => {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: {
          metodo: 'eliminar',
          accion: 'promociones',
          id: btn.attr('attrID')
        },
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          if (String(res ?? '').trim() == "Correcto") {
            Swal.fire({
              icon: 'success',
              title: 'La promoción ha sido eliminada correctamente'
            });

            tablaPromociones();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar la promoción.'
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
    });
  });
});
