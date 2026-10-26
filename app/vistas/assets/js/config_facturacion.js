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
    errorClass: 'is-invalid',        
    errorElement: 'div',
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
          if (String(res ?? '').trim() == "Correcto") {
            Swal.fire({
              icon: 'success',
              title: 'Los datos han sido guardados correctamente'
            });

            $("#contraFacturacion").val("");
            $("#certificadoFacturacion").val("");
            $("#keyFacturacion").val("");
          } else if (String(res ?? '').trim() == 'Error 2 Formato') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El formato del certificado no está permitido, el formato permitido es .cer'
            });
          } else if (String(res ?? '').trim() == 'Error 3 Peso') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El tamaño del certificado excedió el peso máximo permitido, el peso máximo es de 10MB.'
            });
          } else if (String(res ?? '').trim() == 'Error 4 Formato') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops...',
              text: 'El formato de la key no está permitido, el formato permitido es .key'
            });
          } else if (String(res ?? '').trim() == 'Error 5 Peso') {
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

jQuery(document).ready(function ($) {
  
});