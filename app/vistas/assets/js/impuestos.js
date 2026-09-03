function v_impuestos() {
  cargarTablaImpuestos();

  $('#FormImpuestos').validate({
    rules: {
      NombreImpuesto: {
        required: true
      },
      ClaveImpuesto: {
        required: true
      },
      ClaseImpuesto: {
        required: true
      },
      TipoFactorImpuesto: {
        required: true
      },
      PorcentajeImpuesto: {
        required: true,
        min: 0
      },
    },
    messages: {
      NombreImpuesto: {
        required: "El nombre del impuesto es requerido."
      },
      ClaveImpuesto: {
        required: "La clave es requerida."
      },
      ClaseImpuesto: {
        required: "La clase es requerida."
      },
      TipoFactorImpuesto: {
        required: "El factor es requerido."
      },
      PorcentajeImpuesto: {
        required: "El porcentaje del impuesto es requerido.",
        min: "Ingrese un valor mayor o igual a 0."
      },
    },
    errorClass: 'is-invalid',        
    errorElement: 'div',
    submitHandler: function (form) {
      var data = "metodo=" + $("#GuardarImpuesto").attr("tipo") + "&accion=impuestos&IDImpuesto=" + $("#GuardarImpuesto").attr("attrid") + "&Porcentaje=" + $("#PorcentajeImpuesto").val() + "&Nombre=" + $("#NombreImpuesto").val() + "&Clave=" + $("#ClaveImpuesto").val() + "&Clase=" + $("#ClaseImpuesto").val() + "&Tipo=" + $("#TipoFactorImpuesto").val();

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
            if ($("#GuardarImpuesto").attr("tipo") == "modificar") {
              var tipoAlerta = "modificado";
            } else {
              var tipoAlerta = "guardado";
            }

            Swal.fire({
              icon: 'success',
              title: 'Impuesto ' + tipoAlerta + ' correctamente'
            });

            cargarTablaImpuestos();
            $("#ModalImpuestos").modal("hide");
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al ' + $("#GuardarImpuesto").attr("tipo") + ' impuesto.'
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
}

function cargarTablaImpuestos() {
  ajaxMyDatatable({
    table: $('#tablaImpuestos'),
    colums: [
      'Nombre',
      'Porcentaje',
      'Clave',
      'Tipo',
      'Clase',
      'Acciones'
    ],
    sort: [0, 'desc'],
    url: 'index.php',
    params: {
      metodo: 'consultar',
      accion: 'impuestos'
    }
  });
}

jQuery(document).ready($ => {
  $(document).on('click', '#bAgregarImpuesto', () => {
    $("#GuardarImpuesto").attr('tipo', "insertar");
    $("#GuardarImpuesto").attr('attrid', "");
    $("#FormImpuestos").trigger('reset');
    var validator = $("#FormImpuestos").validate();
    validator.resetForm();
    $("#TituloModalImpuestos").text("Agregar nuevo");
    $('#ModalImpuestos').modal('show');
  });

  $(document).on('click', '.EliminarImpuesto', function () {
    var btn = $(this);

    Swal.fire({
      title: '¿Estás seguro que quieres eliminar el impuesto?',
      text: "Una vez eliminado ya no podrá ser recuperado",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'No, cancelar',
      confirmButtonText: 'Si, eliminar'
    }).then((result) => {
      if (result.value) {
        var data = "metodo=eliminar&accion=impuestos&id=" + btn.attr('attrID');
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
                title: 'Impuesto eliminado correctamente'
              });

              cargarTablaImpuestos();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Error inesperado al eliminar el impuesto.'
              });
            }
          })
          .fail(function () {
            console.log("Error ajax");
          }).always(function () {
            $("#carga").hide();
          });
      }
    });
  });

  $(document).on('click', '.ModificarImpuesto', function () {
    var btn = $(this);
    $("#GuardarImpuesto").attr('attrID', btn.attr('attrID'));
    $("#TituloModalImpuestos").text("Modificar");
    $("#GuardarImpuesto").attr('tipo', 'modificar');
    var validator = $("#FormImpuestos").validate();
    validator.resetForm();
    $("#FormImpuestos").trigger('reset');

    var data = "metodo=detalles&accion=impuestos&IDImpuesto=" + btn.attr('attrID') + "&tipo=ConsultarImpuesto";
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
        var datos = JSON.parse($.trim(res));

        $("#NombreImpuesto").val(datos.Nombre);
        $("#ClaveImpuesto").val(datos.Clave);
        $("#ClaseImpuesto").val(datos.Clase);
        $("#TipoFactorImpuesto").val(datos.Tipo);
        $("#PorcentajeImpuesto").val(datos.Porcentaje);

        $("#ModalImpuestos").modal("show");
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $("#carga").hide();
      });
  });

  $(document).on('change', '#ClaveImpuesto', function () {
    let textoSeleccionado = $(this).find('option:selected').text();
    let nombreLimpio = '';

    if (textoSeleccionado.includes(') ')) {
      nombreLimpio = textoSeleccionado.split(') ')[1].trim();
    } else {
      nombreLimpio = textoSeleccionado.trim();
    }

    $('#NombreImpuesto').val(nombreLimpio);
  });
});