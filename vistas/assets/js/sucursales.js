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
    var data = "metodo="+$("#bGuardarSucursal").attr('tipo')+"&accion=sucursales&nombre="+$.trim($("#nombreSucursal").val())+"&telefono="+$.trim($("#telefonoSucursal").val())+"&segundoTelefono="+$.trim($("#segundoTelSucursal").val())+"&email="+$.trim($("#emailSucursal").val())+"&calle="+$.trim($("#calleSucursal").val())+"&noExterior="+$.trim($("#noExteriorSucursal").val())+"&noInterior="+$.trim($("#noInteriorSucursal").val())+"&colonia="+$.trim($("#coloniaSucursal").val())+"&cp="+$.trim($("#cpSucursal").val())+"&ciudad="+$.trim($("#ciudadSucursal").val())+"&estado="+$.trim($("#estadoSucursal").val())+"&pais="+$.trim($("#paisSucursal").val())+"&latitud="+$.trim($("#latitudSucursal").val())+"&longitud="+$.trim($("#longitudSucursal").val())+"&id="+$("#bGuardarSucursal").attr('attrID');

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $('#carga').show();
      }
    })
    .done(function (res) {
      if($.trim(res) == "Correcto"){
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
      }else{
        if($('#bGuardarSucursal').attr('tipo') === 'modificar'){
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

  $(document).on('change keyup', '#latitudSucursal', function() {
    if($.trim($(this).val()) != "" && $.trim($("#longitudSucursal").val()) != ""){
      $("#mapaSucu").html('<iframe id="iframeUbicacion" src="http://maps.google.com/maps?q='+$.trim($(this).val())+', '+$.trim($("#longitudSucursal").val())+'&z=15&output=embed" width="100%" height="500" allowfullscreen="" loading="lazy"></iframe>');
    }else{
      $("#mapaSucu").html('');
    }
  });
  
  $(document).on('change keyup', '#longitudSucursal', function() {
    if($.trim($(this).val()) != "" && $.trim($("#latitudSucursal").val()) != ""){
      $("#mapaSucu").html('<iframe id="iframeUbicacion" src="http://maps.google.com/maps?q='+$.trim($("#latitudSucursal").val())+', '+$.trim($(this).val())+'&z=15&output=embed" width="100%" height="500" allowfullscreen="" loading="lazy"></iframe>');
    }else{
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
        //console.log($.trim(res));
        const datos = JSON.parse($.trim(res));
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
        
        if($.trim(datos.Latitud) != "" && $.trim(datos.Longitud) != ""){
          $("#mapaSucu").html('<iframe id="iframeUbicacion" src="http://maps.google.com/maps?q='+$.trim(datos.Latitud)+', '+$.trim(datos.Longitud)+'&z=15&output=embed" width="100%" height="500" allowfullscreen="" loading="lazy"></iframe>');
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
          if ($.trim(res) === 'Correcto') {
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
            console.log($.trim(res));
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