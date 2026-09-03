let cantidadMerma = null;
let cantidadInventario = null;
let cantidadInventarioRes = null;

function v_inventario() {
  tablaInventario();

  cantidadMerma = IMask(
    document.getElementById('cantidadMerma'),
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

  cantidadInventario = IMask(
    document.getElementById('cantidadInventario'),
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

  cantidadInventarioRes = IMask(
    document.getElementById('cantidadInventarioRes'),
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

  $('#formInventario').validate({
    rules: {
      cantidadInventario: {
        required: true
      }
    },
    messages: {
      cantidadInventario: {
        required: 'Ingrese una cantidad.'
      }
    },
    errorClass: 'is-invalid',        
    errorElement: 'div',
    submitHandler: (form) => {
      Swal.fire({
        title: '¿Estas seguro que deseas agregar la cantidad al inventario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: '¡No, cancelar!',
        confirmButtonText: '¡Si, agregar!'
      }).then((result) => {
        if (result.value) {
          var aplicar = false;
          if ($("#checkAplicarInve").prop('checked')) {
            aplicar = true;
          }

          var data = "metodo=modificar&accion=inventario&id=" + $('#bGuardarInventario').attr('attrID') 
          + "&cantidad=" + $("#cantidadInventario").val().replaceAll(",", ""); /*+ "&sucursal=" + $("#sucursalInventario").val() 
          + "&aplicar=" + aplicar;*/

          $.ajax({
            url: 'index.php',
            type: 'POST',
            data: data,
            beforeSend: function () {
              $('#carga').show();
            }
          })
            .done(function (res) {
              if ($.trim(res) === 'Correcto') {
                Swal.fire({
                  icon: 'success',
                  title: 'Inventario agregado correctamente'
                });

                tablaInventario();
                $('#modalInventario').modal('hide');
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Error inesperado al agregar el inventario'
                });

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
      });
    }
  });

  $('#formInventarioRes').validate({
    rules: {
      cantidadInventario: {
        required: true
      }
    },
    messages: {
      cantidadInventario: {
        required: 'Ingrese una cantidad.'
      }
    },
    errorClass: 'is-invalid',        
    errorElement: 'div',
    submitHandler: (form) => {
      Swal.fire({
        title: '¿Estas seguro que deseas restar la cantidad al inventario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: '¡No, cancelar!',
        confirmButtonText: '¡Si, restar!'
      }).then((result) => {
        if (result.value) {
          var aplicar = false;
          if ($("#checkAplicarInveRes").prop('checked')) {
            aplicar = true;
          }

          var data = "metodo=eliminar&accion=inventario&id=" + $('#bGuardarInventarioRes').attr('attrID') 
          + "&cantidad=" + $("#cantidadInventarioRes").val().replaceAll(",", ""); /*+ "&sucursal=" + $("#sucursalInventarioRes").val() 
          + "&aplicar=" + aplicar;*/

          $.ajax({
            url: 'index.php',
            type: 'POST',
            data: data,
            beforeSend: function () {
              $('#carga').show();
            }
          })
            .done(function (res) {
              if ($.trim(res) === 'Correcto') {
                Swal.fire({
                  icon: 'success',
                  title: 'Inventario restado correctamente'
                });

                tablaInventario();
                $('#modalInventarioRes').modal('hide');
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Error inesperado al restar el inventario'
                });

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
      });
    }
  });

  $('#formInventarioMerma').validate({
    rules: {
      fechaMerma: {
        required: true
      },
      descriMerma: {
        required: true
      },
      cantidadMerma: {
        required: true
      }
    },
    messages: {
      fechaMerma: {
        required: "La fecha es requerida."
      },
      descriMerma: {
        required: "La descripcion es requerida."
      },
      cantidadInventario: {
        required: 'La cantidad es requerida.'
      }
    },
    errorClass: 'is-invalid',        
    errorElement: 'div',
    submitHandler: (form) => {
      if ($('#bGuardarGasto').attr('tipo') === 'modificarMerma') {
        Swal.fire({
          title: '¿Estás seguro que quieres modificar la merma?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '¡No, cancelar!',
          confirmButtonText: '¡Si, modificar!'
        }).then((result) => {
          if (result.value) {
            guardarMerma();
          }
        });
      } else {
        guardarMerma();
      }
    }
  });
}

function tablaInventario() {
  ajaxMyDatatable({
    table: $('#tablaInventario'),
    colums: [
      'Codigo',
      'Descripcion',
      'Existencia',
      //'Sucursales',
      'Costo',
      'Precio',
      'Precio_Mayoreo',
      'Totales',
      'Merma',
      'Acciones'
    ],
    totals: {
      2: "Existencia",
      7: "Totales"
    },
    sort: [0, 'asc'],
    url: 'index.php',
    params: {
      metodo: 'consultar',
      accion: 'inventario',
    }
  });
}

function tablaMerma() {
  ajaxMyDatatable({
    table: $('#tablaMerma'),
    colums: [
      'Fecha',
      'Descripcion',
      'Cantidad',
      'Costo',
      'Total',
      //'Sucursal',
      'Fecha_Merma',
      'Usuario',
      'Afecto',
      'Acciones'
    ],
    sort: [0, 'asc'],
    url: 'index.php',
    params: {
      metodo: 'detalles',
      accion: 'inventario',
      tipo: 'merma',
      producto: $("#bNuevaMerma").attr('attrID')
    }
  });
}

function guardarMerma() {
  var afecta = 'No';
  if ($("#checkMerma").prop('checked')) {
    afecta = 'Si';
  }

  var formData = new FormData(document.querySelector("#formInventarioMerma"));
  formData.append("metodo", 'detalles');
  formData.append("accion", "inventario");
  formData.append('id', $('#bGuardarNuevaMerma').attr('attrID'));
  formData.append("tipo", $('#bGuardarNuevaMerma').attr('tipo'));
  formData.append("afecta", afecta);
  formData.append('producto', $("#bNuevaMerma").attr('attrID'));

  formData.set('cantidadMerma', cantidadMerma.unmaskedValue);

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
      if ($.trim(res) == "Correcto") {
        if ($('#bGuardarNuevaMerma').attr('tipo') === 'modificarMerma') {
          Swal.fire({
            icon: 'success',
            title: 'La merma se ha modificado correctamente',
          });
        } else {
          Swal.fire({
            icon: 'success',
            title: 'La merma se ha guardado correctamente',
          });
        }

        tablaMerma();
        tablaInventario();
        $('#modalMermaNueva').modal('hide');
      } else {
        if ($('#bGuardarNuevaMerma').attr('tipo') === 'modificarMerma') {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al modificar la merma.'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al guardar la merma.'
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

jQuery(document).ready($ => {
  $(document).on('click', '.bAgergarInventario', function () {
    $('#formInventario')[0].reset();
    var validator = $("#formInventario").validate();
    validator.resetForm();
    
    cantidadInventario.value = '';

    $('#cantidadActual').html($(this).parent().parent().children('td:eq(2)').text().replace(searchRegExp, ''));

    $('#bGuardarInventario').attr('attrID', $(this).attr('attrID'));
    $('#modalInventario').modal('show');
    moneda();
  });

  $(document).on('click', '.bRestarInventario', function () {
    $('#formInventarioRes')[0].reset();
    var validator = $("#formInventarioRes").validate();
    validator.resetForm();

    cantidadInventarioRes.value = '';

    $('#cantidadActualRes').html($(this).parent().parent().children('td:eq(2)').text().replace(searchRegExp, ''));

    $('#bGuardarInventarioRes').attr('attrID', $(this).attr('attrID'));
    $('#modalInventarioRes').modal('show');
    moneda();
  });

  $(document).on('click', '.bMermaInventario', function () {
    $("#bNuevaMerma").attr('attrID', $(this).attr('attrID'));
    tablaMerma();
    $("#modalMerma").modal('show');
  });

  $(document).on('click', '#bNuevaMerma', function () {
    $('#formInventarioMerma')[0].reset();
    var validator = $("#formInventarioMerma").validate();
    validator.resetForm();

    cantidadMerma.value = '';

    const hoy = new Date();
    const fechaFormateada = hoy.getFullYear() + '-'
      + String(hoy.getMonth() + 1).padStart(2, '0') + '-'
      + String(hoy.getDate()).padStart(2, '0');
    $("#fechaMerma").val(fechaFormateada);

    $('#bGuardarNuevaMerma').attr('tipo', 'insertarMerma');
    $("#modalMermaNueva").modal('show');
  });

  $(document).on('click', '.bModificarMerma', function () {
    var btn = $(this);
    $('#formInventarioMerma')[0].reset();
    var validator = $("#formInventarioMerma").validate();
    validator.resetForm();
    $('#bGuardarNuevaMerma').attr('tipo', 'modificarMerma');
    $('#bGuardarNuevaMerma').attr('attrID', btn.attr('attrID'));

    const data = 'metodo=detalles&accion=inventario&tipo=detallesMerma&id=' + btn.attr('attrID');

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

        $("#fechaMerma").val(datos.Fecha_Merma);
        $("#descriMerma").val(datos.Descripcion);
        /*$("#sucursalMerma").val("");
        if (datos.FK_Sucursal != '0') {
          $("#sucursalMerma").val(datos.FK_Sucursal);
        }*/

        $("#checkMerma").prop("checked", false);
        if (datos.Afecto_Inventario == 'Si') {
          $("#checkMerma").prop("checked", true);
        }

        cantidadMerma.value = '';
        cantidadMerma.value = datos.Cantidad;

        $('#modalMermaNueva').modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  $(document).on('click', '.bEliminarMerma', function () {
    const btn = $(this);
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar la merma?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        const data = 'metodo=detalles&accion=inventario&tipo=eliminarMerma&id=' + btn.attr('attrID');

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
              title: 'La merma ha sido eliminada correctamente',
            });

            tablaMerma();
            tablaInventario();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar la merma.',
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
  });
});