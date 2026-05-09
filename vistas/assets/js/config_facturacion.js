function v_config_facturacion() {
  $('#formFacturacion').validate({
    rules: {
      rfcFacturacion: {
        required: true
      },
      nombreFacturacion: {
        required: true
      },
      regimenFacturacion: {
        required: true
      }
    },
    messages: {
      rfcFacturacion: {
        required: "El RFC es requerido."
      },
      nombreFacturacion: {
        required: "El nombre es requerido."
      },
      regimenFacturacion: {
        required: "El régimen es requerido."
      }
    },
    submitHandler: function (form) {
      var data = new FormData(document.getElementById('formFacturacion'));
      data.append('metodo', 'insertar');
      data.append('accion', 'config_facturacion');

      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function () {
          $("#carga").show();
        }
      })
        .done(function (res) {
          if ($.trim(res) == "Correcto") {
            Swal.fire({
              icon: 'success',
              title: 'Los datos han sido guardados correctamente'
            });

            $("#contraFacturacion").val("");
            $("#certificadoFacturacion").val("");
            $("#keyFacturacion").val("");
          } else if ($.trim(res) == 'Error 2 Formato') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El formato del certificado no está permitido, el formato permitido es .cer'
            });
          } else if ($.trim(res) == 'Error 3 Peso') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El tamaño del certificado excedió el peso máximo permitido, el peso máximo es de 10MB.'
            });
          } else if ($.trim(res) == 'Error 4 Formato') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El formato de la key no está permitido, el formato permitido es .key'
            });
          } else if ($.trim(res) == 'Error 5 Peso') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El tamaño de la key excedió el peso máximo permitido, el peso máximo es de 10MB.'
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al guardar los datos.'
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

jQuery(document).ready(function ($) {
  
});