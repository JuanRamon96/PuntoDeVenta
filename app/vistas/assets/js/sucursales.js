function v_sucursales() {
  tablaSucursales();

  $('#formSucursales').validate({
    rules: {
      nombreSucursal: {
        required: true
      }
    },
    messages: {
      nombreSucursal: {
        required: "El nombre es requerido."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      if ($('#bGuardarSucursal').attr('tipo') === 'modificar') {
        Swal.fire({
          title: '¿Estás seguro que quieres modificar la sucursal?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '¡No, cancelar!',
          confirmButtonText: '¡Si, modificar!'
        }).then((result) => {
          if (result.value) {
            guardarSucursal();
          }
        });
      } else {
        guardarSucursal();
      }
    }
  });
}

function guardarSucursal() {
  var data = "metodo=" + $("#bGuardarSucursal").attr('tipo') + "&accion=sucursales&nombre=" + String($("#nombreSucursal").val() ?? '').trim() + "&telefono=" + String($("#telefonoSucursal").val() ?? '').trim() + "&segundoTelefono=" + String($("#segundoTelSucursal").val() ?? '').trim() + "&email=" + String($("#emailSucursal").val() ?? '').trim() + "&calle=" + String($("#calleSucursal").val() ?? '').trim() + "&noExterior=" + String($("#noExteriorSucursal").val() ?? '').trim() + "&noInterior=" + String($("#noInteriorSucursal").val() ?? '').trim() + "&colonia=" + String($("#coloniaSucursal").val() ?? '').trim() + "&cp=" + String($("#cpSucursal").val() ?? '').trim() + "&ciudad=" + String($("#ciudadSucursal").val() ?? '').trim() + "&estado=" + String($("#estadoSucursal").val() ?? '').trim() + "&pais=" + String($("#paisSucursal").val() ?? '').trim() + "&latitud=" + String($("#latitudSucursal").val() ?? '').trim() + "&longitud=" + String($("#longitudSucursal").val() ?? '').trim() + "&id=" + $("#bGuardarSucursal").attr('attrID');

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $('#carga').show();
    }
  })
    .done(function (res) {
      if (String(res ?? '').trim() == "Correcto") {
        if ($('#bGuardarSucursal').attr('tipo') === 'modificar') {
          Swal.fire({
            icon: 'success',
            title: 'La sucursal se ha modificado correctamente',
          });
        } else {
          Swal.fire({
            icon: 'success',
            title: 'La sucursal se ha guardado correctamente',
          });
        }

        tablaSucursales();
        $('#modalSucursales').modal('hide');
      } else {
        if ($('#bGuardarSucursal').attr('tipo') === 'modificar') {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al modificar la sucursal.'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al guardar la sucursal.'
          });
        }
        console.log(String(res ?? '').trim());
      }
    })
    .fail(function () {
      console.log("Error ajax");
    })
    .always(function () {
      $('#carga').hide();
    });
}

function tablaSucursales() {
  ajaxMyDatatable({
    'table': $('#tablaSucursales'),
    'colums': [
      'Fecha',
      'Nombre',
      'Domicilio',
      'Contacto',
      'Acciones'
    ],
    'sort': [0, 'desc'],
    'url': 'index.php',
    'params': {
      'metodo': 'consultar',
      'accion': 'sucursales'
    }
  });
}

jQuery(document).ready(function ($) {
  $(document).on('click', '#bAgregarSucursal', function () {
    $('#formSucursales')[0].reset();
    $("#mapaSucu").html('');
    var validator = $("#formSucursales").validate();
    validator.resetForm();

    $('#bGuardarSucursal').attr('tipo', 'insertar');
    $('#modalSucursales').modal('show');
  });

  $(document).on('change keyup', '#latitudSucursal', function () {
    if (String($(this).val() ?? '').trim() != "" && String($("#longitudSucursal").val() ?? '').trim() != "") {
      $("#mapaSucu").html('<iframe id="iframeUbicacion" src="http://maps.google.com/maps?q=' + String($(this).val() ?? '').trim() + ', ' + String($("#longitudSucursal").val() ?? '').trim() + '&z=15&output=embed" width="100%" height="500" allowfullscreen="" loading="lazy"></iframe>');
    } else {
      $("#mapaSucu").html('');
    }
  });

  $(document).on('change keyup', '#longitudSucursal', function () {
    if (String($(this).val() ?? '').trim() != "" && String($("#latitudSucursal").val() ?? '').trim() != "") {
      $("#mapaSucu").html('<iframe id="iframeUbicacion" src="http://maps.google.com/maps?q=' + String($("#latitudSucursal").val() ?? '').trim() + ', ' + String($(this).val() ?? '').trim() + '&z=15&output=embed" width="100%" height="500" allowfullscreen="" loading="lazy"></iframe>');
    } else {
      $("#mapaSucu").html('');
    }
  });

  $(document).on('click', '.bModificarSucursal', function () {
    $('#formSucursales')[0].reset();
    var validator = $("#formSucursales").validate();
    validator.resetForm();
    const data = `metodo=detalles&accion=sucursales&tipo=sucursal&id=${$(this).attr('attrID')}`;

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $('#carga').show();
      }
    })
      .done(function (res) {
        //console.log(String(res ?? '').trim());
        const datos = JSON.parse(String(res ?? '').trim());
        //console.log(datos);

        $("#nombreSucursal").val(datos.Nombre);
        $("#telefonoSucursal").val(datos.Telefono);
        $("#segundoTelSucursal").val(datos.Segundo_Telefono);
        $("#emailSucursal").val(datos.Email);
        $("#calleSucursal").val(datos.Calle);
        $("#noExteriorSucursal").val(datos.No_Exterior);
        $("#noInteriorSucursal").val(datos.No_Interior);
        $("#coloniaSucursal").val(datos.Colonia);
        $("#cpSucursal").val(datos.CP);
        $("#ciudadSucursal").val(datos.Ciudad);
        $("#estadoSucursal").val(datos.Estado);
        $("#paisSucursal").val(datos.Pais);
        $("#latitudSucursal").val(datos.Latitud);
        $("#longitudSucursal").val(datos.Longitud);

        if (String(datos.Latitud ?? '').trim() != "" && String(datos.Longitud ?? '').trim() != "") {
          $("#mapaSucu").html('<iframe id="iframeUbicacion" src="http://maps.google.com/maps?q=' + String(datos.Latitud ?? '').trim() + ', ' + String(datos.Longitud ?? '').trim() + '&z=15&output=embed" width="100%" height="500" allowfullscreen="" loading="lazy"></iframe>');
        }

        $('#bGuardarSucursal').attr('tipo', 'modificar');
        $('#bGuardarSucursal').attr('attrID', datos.ID_Sucursal);
        $('#modalSucursales').modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  $(document).on('click', '.bEliminarSucursal', function () {
    const btn = $(this);
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar la sucursal?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        const data = `metodo=eliminar&accion=sucursales&id=${btn.attr('attrID')}`;

        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function () {
            $('#carga').show();
          }
        }).done(function (res) {
          if (String(res ?? '').trim() === 'Correcto') {
            Swal.fire({
              icon: 'success',
              title: '¡La sucursal se ha eliminado correctamente!',
            });
            tablaSucursales();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar la sucursal.',
            });
            console.log(String(res ?? '').trim());
          }
        }).fail(function () {
          console.log("Error ajax");
        }).always(function () {
          $('#carga').hide();
        })
      }
    });
  })
});