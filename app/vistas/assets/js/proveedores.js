let creditoProveedor = null;

function v_proveedores() {
  tablaProveedores();

  creditoProveedor = IMask(
    document.getElementById('creditoProveedor'),
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

  $('#formProveedores').validate({
    rules: {
      razonProveedor: {
        required: true
      }
    },
    messages: {
      razonProveedor: {
        required: "El nombre / razón social es requerida."
      }
    },
    errorClass: 'is-invalid',
    errorElement: 'div',
    submitHandler: function (form) {
      if ($('#bGuardarProveedor').attr('tipo') === 'modificar') {
        Swal.fire({
          title: '¿Estás seguro que quieres modificar el proveedor?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '¡No, cancelar!',
          confirmButtonText: '¡Si, modificar!'
        }).then((result) => {
          if (result.value) {
            guardarProveedor();
          }
        });
      } else {
        guardarProveedor();
      }
    }
  });
}

function guardarProveedor() {
  var data = "metodo=" + $("#bGuardarProveedor").attr('tipo') + "&accion=proveedores&razon=" + $.trim($("#razonProveedor").val()) + "&rfc=" + $.trim($("#rfcProveedor").val())
    + "&telefono=" + $.trim($("#telefonoProveedor").val()) + "&segundoTelefono=" + $.trim($("#segundoTelProveedor").val()) + "&email=" + $.trim($("#emailProveedor").val())
    + "&credito=" + $("#creditoProveedor").val().replaceAll(",", "") + "&contacto=" + $.trim($("#contactoProveedor").val()) + "&puesto=" + $.trim($("#puestoProveedor").val())
    + "&telefonoContacto=" + $.trim($("#telefonoContactoProveedor").val()) + "&emailContacto=" + $.trim($("#emailContactoProveedor").val()) + "&calle=" + $.trim($("#calleProveedor").val())
    + "&noExterior=" + $.trim($("#noExteriorProveedor").val()) + "&noInterior=" + $.trim($("#noInteriorProveedor").val()) + "&colonia=" + $.trim($("#coloniaProveedor").val())
    + "&cp=" + $.trim($("#cpProveedor").val()) + "&ciudad=" + $.trim($("#ciudadProveedor").val()) + "&estado=" + $.trim($("#estadoProveedor").val()) + "&pais=" + $.trim($("#paisProveedor").val())
    + "&clabe=" + $.trim($("#clabeProveedor").val()) + "&banco=" + $.trim($("#bancoProveedor").val()) + "&titular=" + $.trim($("#titularProveedor").val()) + "&id=" + $("#bGuardarProveedor").attr('attrID');

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    beforeSend: function () {
      $('#carga').show();
    }
  })
    .done(function (res) {
      if ($.trim(res) == "Correcto") {
        if ($('#bGuardarProveedor').attr('tipo') === 'modificar') {
          Swal.fire({
            icon: 'success',
            title: 'El proveedor se ha modificado correctamente',
          });
        } else {
          Swal.fire({
            icon: 'success',
            title: 'El proveedor se ha guardado correctamente',
          });
        }

        tablaProveedores();
        $('#modalProveedores').modal('hide');
      } else {
        if ($('#bGuardarProveedor').attr('tipo') === 'modificar') {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al modificar el proveedor.'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al guardar el proveedor.'
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

function tablaProveedores() {
  ajaxMyDatatable({
    'table': $('#tablaProveedores'),
    'colums': [
      'Fecha',
      'Nombre',
      'Domicilio',
      'Contacto',
      'Cuenta',
      'Acciones'
    ],
    'sort': [0, 'desc'],
    'url': 'index.php',
    'params': {
      'metodo': 'consultar',
      'accion': 'proveedores'
    }
  });
}

jQuery(document).ready(function ($) {

  $(document).on('click', '#bAgregarProveedor', function () {
    $('#formProveedores')[0].reset();
    var validator = $("#formProveedores").validate();
    validator.resetForm();
    
    creditoProveedor.value = '';

    $('#bGuardarProveedor').attr('tipo', 'insertar');
    $('#modalProveedores').modal('show');
  });

  $(document).on('click', '.bModificarProveedor', function () {
    $('#formProveedores')[0].reset();
    var validator = $("#formProveedores").validate();
    validator.resetForm();
    const data = "metodo=detalles&accion=proveedores&tipo=proveedor&id=" + $(this).attr('attrID');

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

        $("#razonProveedor").val(datos.Razon_Social);
        $("#rfcProveedor").val(datos.RFC);
        $("#telefonoProveedor").val(datos.Telefono);
        $("#segundoTelProveedor").val(datos.Segundo_Telefono);
        $("#emailProveedor").val(datos.Email);

        creditoProveedor.value = '';
        creditoProveedor.value = datos.Credito;

        $("#contactoProveedor").val(datos.Contacto);
        $("#puestoProveedor").val(datos.Puesto);
        $("#telefonoContactoProveedor").val(datos.Telefono_Contacto);
        $("#emailContactoProveedor").val(datos.Email_Contacto);
        $("#calleProveedor").val(datos.Calle);
        $("#noExteriorProveedor").val(datos.No_Exterior);
        $("#noInteriorProveedor").val(datos.No_Interior);
        $("#coloniaProveedor").val(datos.Colonia);
        $("#cpProveedor").val(datos.CP);
        $("#ciudadProveedor").val(datos.Ciudad);
        $("#estadoProveedor").val(datos.Estado);
        $("#paisProveedor").val(datos.Pais);
        $("#clabeProveedor").val(datos.Clabe);
        $("#bancoProveedor").val(datos.Banco);
        $("#titularProveedor").val(datos.Titular);

        $('#bGuardarProveedor').attr('tipo', 'modificar');
        $('#bGuardarProveedor').attr('attrID', datos.ID_Proveedor);
        $('#modalProveedores').modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  $(document).on('click', '.bEliminarProveedor', function () {
    const btn = $(this);
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar el proveedor?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        const data = "metodo=eliminar&accion=proveedores&id=" + btn.attr('attrID');

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
              title: '¡El proveedor se ha eliminado correctamente!',
            });

            tablaProveedores();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar el proveedor.',
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
  });
});