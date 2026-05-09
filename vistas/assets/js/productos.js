var productos = [];

function v_productos() {
  productos = [];
  tablaProductos();

  $('#formProductos').validate({
    rules: {
      codigoProducto: {
        required: true
      },
      descripcionProducto: {
        required: true
      },
      costoProducto: {
        min: 0
      },
      precioProducto: {
        required: true,
        min: 0
      },
      precioMayoreoProducto: {
        min: 0
      },
      claseProducto: {
        required: true
      }
    },
    messages: {
      codigoProducto: {
        required: "El código es requerido"
      },
      descripcionProducto: {
        required: "La descripción es requerida"
      },
      costoProducto: {
        min: "El costo debe ser mayor o igual a 0"
      },
      precioProducto: {
        required: "El precio es requerido",
        min: "El precio debe ser mayor o igual a 0"
      },
      precioMayoreoProducto: {
        min: "El precio mayoreo debe ser mayor o igual a 0"
      },
      claseProducto: {
        required: "La clase es requerida"
      }
    },
    submitHandler: function (form) {
      if ($('#bGuardarProducto').attr('tipo') === 'modificar') {
        Swal.fire({
          title: '¿Estás seguro que quieres modificar el producto?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '¡No, cancelar!',
          confirmButtonText: '¡Si, modificar!'
        }).then((result) => {
          if (result.value) {
            guardarProducto();
          }
        });
      } else {
        guardarProducto();
      }
    }
  });
}

function guardarProducto() {
  let impuestosAgregar = [];
  let impuestosEliminar = [];
  $("#verImpuestosProducto").children('tr').each(function () {
    if($(this).children('td:eq(5)').children('input').is(':checked')) {
      impuestosAgregar.push($(this).attr('attrID'));
    } else {
      impuestosEliminar.push($(this).attr('attrID'));
    }
  });

  var formData = new FormData(document.querySelector("#formProductos"));
  formData.append("metodo", $('#bGuardarProducto').attr('tipo'));
  formData.append("accion", "productos");
  formData.append('id', $('#bGuardarProducto').attr('attrID'));
  formData.append('foto', $('#bGuardarProducto').attr('foto'));
  formData.append('impuestosAgregar', JSON.stringify(impuestosAgregar));
  formData.append('impuestosEliminar', JSON.stringify(impuestosEliminar));

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
        if ($('#bGuardarProducto').attr('tipo') === 'modificar') {
          Swal.fire({
            icon: 'success',
            title: 'El producto se ha modificado correctamente',
          });
        } else {
          Swal.fire({
            icon: 'success',
            title: 'El producto se ha guardado correctamente',
          });
        }

        tablaProductos();
        $('#modalProductos').modal('hide');
      } else if ($.trim(res) === 'Error 2 formato') {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg'
        })
      } else if ($.trim(res) === 'Error 3 peso') {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
        })
      } else if ($.trim(res).includes('Duplicate')) {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'El código del producto ya existe'
        })
      } else {
        if ($('#bGuardarProducto').attr('tipo') === 'modificar') {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado para modificar el producto'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado para guardar el producto'
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

function tablaProductos() {
  ajaxMyDatatable({
    'table': $('#tablaProductos'),
    'colums': [
      'Fecha',
      'Codigo',
      'Descripcion',
      'Clase',
      'Costo',
      'Costo_Promedio',
      'Precio',
      'Precio_Mayoreo',
      'Stock_Minimo',
      'Stock_Maximo',
      'Clasificacion',
      'Impuestos',
      'Acciones'
    ],
    'sort': [0, 'desc'],
    'url': 'index.php',
    'params': {
      'metodo': 'consultar',
      'accion': 'productos',
      'productos': JSON.stringify(productos)
    }
  });
}

function tablaClavesProductos() {
  ajaxMyDatatable({
    'table': $('#tablaClavesProductos'),
    'colums': [
      'Clave',
      'Descripcion',
      'Palabras',
    ],
    'sort': [0, 'asc'],
    'url': 'index.php',
    'params': {
      'metodo': 'detalles',
      'accion': 'productos',
      'tipo': 'claves'
    }
  });
}

function tablaClavesUnidadProductos() {
  ajaxMyDatatable({
    'table': $('#tablaClavesUnidadProductos'),
    'colums': [
      'Clave',
      'Nombre',
      'Simbolo',
    ],
    'sort': [0, 'asc'],
    'url': 'index.php',
    'params': {
      'metodo': 'detalles',
      'accion': 'productos',
      'tipo': 'unidades'
    }
  });
}

jQuery(document).ready(function ($) {

  $(document).on('click', '#bAgregarProducto', function () {
    $('#formProductos')[0].reset();
    $('#bCancelarFotoP').addClass('oculto');
    $("#bModificarFotoP").removeClass('oculto');
    $("#mosFotoProducto").css('background-image', "url('vistas/assets/images/producto-generico.png')");

    $('#bGuardarProducto').attr('tipo', 'insertar');
    $('#modalProductos').modal('show');
  });

  $(document).on('click', '#bCancelarFotoP', function () {
    $(this).addClass('oculto');
    $("#bModificarFotoP").removeClass('oculto');
    $('#fotoProducto').val('');
    $("#mosFotoProducto").css('background-image', $(this).attr('foto'));
    $("#mosFotoProducto").parent().attr('href', $(this).attr('foto').replace('url(', '').replace(')', '').replace(/\"/gi, ""));
  });

  $(document).on('click', '#bModificarFotoP', function () {
    $('#fotoProducto').trigger('click');

    $(document).on('change', '#fotoProducto', function () {
      var tipo = $(this).val().substring($(this).val().lastIndexOf(".")).toLowerCase();

      if (this.files && this.files[0]) {
        if (tipo != ".jpeg" && tipo != ".png" && tipo != ".jpg" && tipo != ".svg") {
          Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg'
          });
        } else if ((this.files[0].size / (1024 * 1024)) > 10) {
          Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
          });
        } else {
          $("#bCancelarFotoP").attr('foto', $("#mosFotoProducto").css('background-image'));
          $("#bModificarFotoP").addClass('oculto');
          $("#bCancelarFotoP").removeClass('oculto');

          var reader = new FileReader();

          reader.onload = function (e) {
            $("#mosFotoProducto").css('background-image', "url('" + e.target.result + "')");
            $("#mosFotoProducto").parent().attr('href', e.target.result);
          }

          reader.readAsDataURL(this.files[0]);
        }
      }
    });
  });

  $(document).on('click', '.bModificarProducto', function () {
    const data = `metodo=detalles&accion=productos&tipo=producto&id=${$(this).attr('attrID')}`;

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

        $('#formProductos')[0].reset();
        $('#codigoProducto').val(datos.Codigo);
        $('#descripcionProducto').val(datos.Descripcion);
        $('#costoProducto').val(datos.Costo);
        $('#precioProducto').val(datos.Precio);
        $('#precioMayoreoProducto').val(datos.Precio_Mayoreo);
        $('#claseProducto').val(datos.Clase);
        $('#stockMinimoProducto').val(datos.Stock_Minimo);
        $('#stockMaximoProducto').val(datos.Stock_Maximo);
        $('#clasificacionProducto').val(datos.FK_Clasificacion);

        $("#claveProducto").val(datos.Clave_ProdServ_CFDI);
        $("#descripcionClaveProducto").val(datos.Descripcion_Clave_CDFI);
        $("#claveUnidadProducto").val(datos.Clave_Unidad_CFDI);
        $("#nombreUnidadProducto").val(datos.Nombre_Unidad_CFDI);
        $("#simboloProducto").val(datos.Simbolo_CFDI);

        let filasImProd = $("#verImpuestosProducto").children('tr').length;
        datos.Impuestos.forEach((impuesto) => {
          for (let i = 0; i < filasImProd; i++) {
            if (impuesto == $("#verImpuestosProducto").children('tr').eq(i).attr('attrID')) { 
              $("#verImpuestosProducto").children('tr').eq(i).children('td:eq(5)').children('input').prop('checked', true);
              break;
            }
          }
        });

        if (datos.Foto !== "") {
          $("#bCancelarFotoP").attr('foto', "url('" + datos.Foto + "')");
          $("#bModificarFotoP").removeClass('oculto');
          $("#bCancelarFotoP").addClass('oculto');
          $("#mosFotoProducto").css('background-image', `url('vistas/assets/images/productos/${datos.Foto}')`);

          $("#mosFotoProducto").parent().attr('href', `vistas/assets/images/productos/${datos.Foto}`);
        } else {
          $("#bCancelarFotoP").attr('foto', "url('vistas/assets/images/producto-generico.png')");
          $("#bModificarFotoP").removeClass('oculto');
          $("#bCancelarFotoP").addClass('oculto');
          $("#mosFotoProducto").css('background-image', "url('vistas/assets/images/producto-generico.png')");
          $("#mosFotoProducto").parent().attr('href', "vistas/assets/images/producto-generico.png");
        }

        $('#bGuardarProducto').attr('tipo', 'modificar');
        $('#bGuardarProducto').attr('foto', datos.Foto);
        $('#bGuardarProducto').attr('attrID', datos.ID_Producto);
        $('#modalProductos').modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  $(document).on('click', '.bEliminarProducto', function () {
    const btn = $(this);
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar el producto?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        const data = `metodo=eliminar&accion=productos&id=${btn.attr('attrID')}&foto=${btn.attr('foto')}`;
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
              title: '¡El producto se ha eliminado correctamente!',
            });
            tablaProductos();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar el producto',
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

  $(document).on('click', '.checkProducto', function () {
    var padre = $(this).parent().parent();

    if ($(this).prop('checked')) {
      productos.push(padre.attr('id'));
    } else {
      productos = productos.filter(function (i) { return i !== padre.attr('id') });
    }
  });

  $(document).on('click', '#bDesmarcar', function () {
    $('.checkProducto').prop('checked', false);
    productos = [];
  });

  $(document).on('click', '#bMarcarTodos', function () {
    const data = "metodo=detalles&accion=productos&tipo=marcar&buscar=" + $("input.buscadorMyDataTable[tabla='tablaProductos']").val();

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

        datos.forEach((dato) => {
          if (dato != null) {
            productos.push(dato.ID_Producto);
          }
        });

        $('.checkProducto').prop('checked', true);
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  $(document).on('click', '.bImprimirCodigo', function () {
    var attrID = $(this).attr('attrID');
    window.open('./controladores/pdf/imprimirCodigo.php?id=' + attrID + "&tipo=uno", '_blank').print();
  });

  $(document).on('click', '#bImprimirCodigosVarios', function () {
    if ($.isEmptyObject(productos)) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "No hay productos seleccionados!"
      });
    } else {
      window.open('./controladores/pdf/imprimirCodigo.php?id=' + JSON.stringify(productos) + "&tipo=varios", '_blank').print();
    }
  });

  $(document).on('click', '#bPorcentajeGanancia', function () {
    var costo = parseFloat($("#costoProducto").val()) || 0;
    var porcentaje = parseFloat($(this).text().replace('%').replaceAll(',', ''));
    var valor = (porcentaje / 100) * costo;
    var precio = costo + valor;

    $("#precioProducto").val(Math.round(precio * 100) / 100);
  });

  $(document).on('click', '#bBuscarClaveProducto', function () {
    tablaClavesProductos();
    $('#modalClavesProductos').modal('show');
  });

  $(document).on('click', '#bQuitarClaveProducto', function () {
    $("#claveProducto").val('');
    $("#descripcionClaveProducto").val('');
  });

  $(document).on('click', '#tablaClavesProductos tbody tr', function () {
    $("#claveProducto").val($(this).children('td:eq(0)').text());
    $("#descripcionClaveProducto").val($(this).children('td:eq(1)').text());

    $('#modalClavesProductos').modal('hide');
  });

  $(document).on('click', '#bBuscarClaveUnidadProducto', function () {
    tablaClavesUnidadProductos();
    $('#modalClavesUnidadProductos').modal('show');
  });

  $(document).on('click', '#bQuitarClaveUnidadProducto', function () {
    $("#claveUnidadProducto").val('');
    $("#nombreUnidadProducto").val('');
    $("#simboloProducto").val('');
  });

  $(document).on('click', '#tablaClavesUnidadProductos tbody tr', function () {
    $("#claveUnidadProducto").val($(this).children('td:eq(0)').text());
    $("#nombreUnidadProducto").val($(this).children('td:eq(1)').text());
    $("#simboloProducto").val($(this).children('td:eq(2)').text());

    $('#modalClavesUnidadProductos').modal('hide');
  });
});