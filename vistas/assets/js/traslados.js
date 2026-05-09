//let maskMontoGasto = null;

function v_traslados() {
    /*tablaTraslados();

    maskMontoGasto = IMask(
    document.getElementById('montoGasto'),
      {
        mask: Number,
        thousandsSeparator: ','
      }
    );

    $('#formGastos').validate({
        rules: {
          fechaGasto: {
            required: true
          },
          montoGasto: {
            required: true
          },
          descriGasto: {
            required: true
          }
        },
        messages: {
          fechaGasto: {
            required: "La fecha es requerida."
          },
          montoGasto: {
            required: "El monto es requerido."
          },
          descriGasto: {
            required: "La descripción es requerida."
          }
        },
        submitHandler: function (form) {
            if ($('#bGuardarGasto').attr('tipo') === 'modificar') {
                Swal.fire({
                    title: '¿Estás seguro que quieres modificar el gasto?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: '¡No, cancelar!',
                    confirmButtonText: '¡Si, modificar!'
                }).then((result) => {
                    if (result.value) {
                        guardarGasto();
                    }
                });
            } else {
                guardarGasto();
            }
        }
    });*/
}

/*function guardarGasto() {
    var formData = new FormData(document.querySelector("#formGastos"));
    formData.append("metodo", $('#bGuardarGasto').attr('tipo'));
    formData.append("accion", "gastos");
    formData.append('id', $('#bGuardarGasto').attr('attrID'));
    formData.append('comprobante', $('#bGuardarGasto').attr('comprobante'));

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      beforeSend: function () {
        $('#carga').show();
      }
    })
    .done(function (res) {
      if($.trim(res) == "Correcto"){
        if ($('#bGuardarGasto').attr('tipo') === 'modificar') {
          Swal.fire({
            icon: 'success',
            title: 'El gasto se ha modificado correctamente',
          });
        } else {
          Swal.fire({
            icon: 'success',
            title: 'El gasto se ha guardado correctamente',
          });
        }

        tablaGastos();
        $('#modalGastos').modal('hide');
      }else{
        if($('#bGuardarGasto').attr('tipo') === 'modificar'){
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al modificar el gasto.'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al guardar el gasto.'
          });
        }
        console.log($.trim(res));
      }
    })
    .fail(function () {
      console.log("Error ajax");
    })
    .always(function () {
      $('#carga').hide();
    });
}

function tablaTraslados() {
  ajaxMyDatatable({
    'table': $('#tablaTraslados'),
    'colums': [
      'Fecha',
      'Monto',
      'Descripcion',
      'Usuario',
      'Comprobante',
      'Fecha_Gasto',
      'Sucursal',
      'Acciones'
    ],
    'sort': [0, 'desc'],
    'url': 'index.php',
    'params': {
      'metodo': 'consultar',
      'accion': 'gastos'
    },
    'totals': {
      1: 'Total'
    }
  });
}*/

jQuery(document).ready(function ($) {
  $(document).on('click', '#bAgregarTraslado', function () {
    $('#formTraslado')[0].reset();
    var validator = $("#formTraslado").validate();
    validator.resetForm();

    const hoy = new Date();
    const fechaFormateada = hoy.getFullYear() + '-' 
    + String(hoy.getMonth() + 1).padStart(2, '0') + '-' 
    + String(hoy.getDate()).padStart(2, '0');
    $("#fechaTraslado").val(fechaFormateada);

    $('#bGuardarTraslado').attr('tipo', 'insertar');
    $('#modalTraslados').modal('show');
  });

  /*$(document).on('click', '.bModificarGasto', function () {
      var btn = $(this);
      $('#formGastos')[0].reset();
      var validator = $("#formGastos").validate();
      validator.resetForm();

      $('#bGuardarGasto').attr('tipo', 'modificar');
      $('#bGuardarGasto').attr('comprobante', btn.attr('comprobante'));
      $('#bGuardarGasto').attr('attrID', btn.attr('attrID'));
      
      const data = 'metodo=detalles&accion=gastos&tipo=gasto&id='+btn.attr('attrID');

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
        const datos = JSON.parse($.trim(res));
        //console.log(datos);

        $("#fechaGasto").val(datos.Fecha_Gasto);
        $("#descriGasto").val(datos.Descripcion);
        $("#sucursalGasto").val("");
        if(datos.FK_Sucursal != '0'){
          $("#sucursalGasto").val(datos.FK_Sucursal);
        }
        
        maskMontoGasto.value = '';
        maskMontoGasto.value = datos.Monto;

        $('#modalGastos').modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  $(document).on('click', '.bEliminarGasto', function () {
    const btn = $(this);
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar el gasto?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        const data = 'metodo=eliminar&accion=gastos&id='+btn.attr('attrID')+"&comprobante="+btn.attr('comprobante');
        
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
              title: '¡El gasto se ha eliminado correctamente!',
            });

            tablaGastos();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar el gasto.',
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
  });*/
});