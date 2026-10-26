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
              if (String(res ?? '').trim() === 'Correcto') {
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
              if (String(res ?? '').trim() === 'Correcto') {
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
      if (String(res ?? '').trim() == "Correcto") {
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

jQuery(document).ready($ => {
  $(document).on('click', '.bAgergarInventario', function () {
    $('#formInventario')[0].reset();
    var validator = $("#formInventario").validate();
    validator.resetForm();
    
    cantidadInventario.value = '';

    $('#cantidadActual').html($(this).parent().parent().children('td:eq(2)').find('span.cantidad').first().text().replace(searchRegExp, ''));

    $('#bGuardarInventario').attr('attrID', $(this).attr('attrID'));
    $('#modalInventario').modal('show');
    moneda();
  });

  $(document).on('click', '.bRestarInventario', function () {
    $('#formInventarioRes')[0].reset();
    var validator = $("#formInventarioRes").validate();
    validator.resetForm();

    cantidadInventarioRes.value = '';

    $('#cantidadActualRes').html($(this).parent().parent().children('td:eq(2)').find('span.cantidad').first().text().replace(searchRegExp, ''));

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
        //console.log(String(res ?? '').trim());
        const datos = JSON.parse(String(res ?? '').trim());
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
          if (String(res ?? '').trim() === 'Correcto') {
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

            console.log(String(res ?? '').trim());
          }
        }).fail(function () {
          console.log("Error ajax");
        }).always(function () {
          $('#carga').hide();
        });
      }
    });
  });

  // ========================================================
  // >>>>>>>>>>>>>>>>> GESTIÓN DE LOTES <<<<<<<<<<<<<<<<<<<<
  // ========================================================

  $(document).on('click', '.bGestionarLotes', function () {
    const idInventario = $(this).attr('attrID');
    cargarLotesInventarioModal(idInventario);
  });

  $(document).on('click', '#bAgregarFilaLote', function () {
    const hoy = new Date();
    const fechaDefecto = hoy.getFullYear() + '-' + String(hoy.getMonth() + 1).padStart(2, '0') + '-' + String(hoy.getDate()).padStart(2, '0');

    const nuevaFila = `
      <tr attrIDLote="0">
        <td><span class="badge bg-secondary">Nuevo</span></td>
        <td><input type="text" class="form-control form-control-sm loteNombre" placeholder="Ej. LOTE-01"></td>
        <td><input type="date" class="form-control form-control-sm loteCaducidad" value="${fechaDefecto}"></td>
        <td><input type="number" class="form-control form-control-sm loteCantidad text-center" value="1" min="0.01" step="any"></td>
        <td>
          <button type="button" class="btn btn-sm btn-outline-danger bEliminarFilaLoteTemp" title="Quitar"><i class="fas fa-trash"></i></button>
        </td>
      </tr>
    `;
    $('#tbodyLotes').append(nuevaFila);
    recalcularTotalesLotesModal();
  });

  $(document).on('input change', '.loteCantidad', function () {
    recalcularTotalesLotesModal();
  });

  $(document).on('click', '.bEliminarFilaLoteTemp', function () {
    $(this).closest('tr').remove();
    recalcularTotalesLotesModal();
  });

  $(document).on('click', '.bEliminarLote', function () {
    const btn = $(this);
    const idLote = btn.attr('attrIDLote');
    const idInventario = $('#bGuardarLotes').attr('attrID');

    Swal.fire({
      title: '¿Estás seguro que quieres eliminar este lote?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Sí, eliminar!'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: 'metodo=detalles&accion=inventario&tipo=eliminarLote&idLote=' + idLote,
          beforeSend: function () {
            $('#carga').show();
          }
        }).done(function (res) {
          if (String(res ?? '').trim() === 'Correcto') {
            Swal.fire({
              icon: 'success',
              title: 'Lote eliminado correctamente'
            });
            cargarLotesInventarioModal(idInventario);
            tablaInventario();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar el lote.'
            });
            console.log(String(res ?? '').trim());
          }
        }).fail(function () {
          console.log("Error ajax");
        }).always(function () {
          $('#carga').hide();
        });
      }
    });
  });

  $(document).on('click', '#bGuardarLotes', function () {
    const idInventario = $(this).attr('attrID');
    const existenciaTotal = parseFloat($('#lblLoteExistencia').attr('attrExistencia')) || 0;
    const lotes = [];
    let valido = true;
    let sumaCantidades = 0;

    $('#tbodyLotes tr').each(function () {
      const idLote = parseInt($(this).attr('attrIDLote')) || 0;
      const nombre = String($(this).find('.loteNombre').val() ?? '').trim();
      const caducidad = String($(this).find('.loteCaducidad').val() ?? '').trim();
      const cantidad = parseFloat($(this).find('.loteCantidad').val()) || 0;

      if (nombre === '') {
        Swal.fire({
          icon: 'warning',
          title: 'Atención',
          text: 'Todos los lotes deben tener un nombre o identificador.'
        });
        valido = false;
        return false;
      }

      if (caducidad === '') {
        Swal.fire({
          icon: 'warning',
          title: 'Atención',
          text: 'Todos los lotes deben tener una fecha de caducidad.'
        });
        valido = false;
        return false;
      }

      if (cantidad <= 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Atención',
          text: 'La cantidad del lote debe ser mayor a cero.'
        });
        valido = false;
        return false;
      }

      sumaCantidades += cantidad;
      lotes.push({
        idLote: idLote,
        nombre: nombre,
        caducidad: caducidad,
        cantidad: cantidad
      });
    });

    if (!valido) return;

    if (sumaCantidades > existenciaTotal) {
      Swal.fire({
        icon: 'error',
        title: 'Error en cantidades',
        text: `La suma de los lotes (${sumaCantidades}) no puede superar la existencia actual (${existenciaTotal}).`
      });
      return;
    }

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: {
        metodo: 'detalles',
        accion: 'inventario',
        tipo: 'guardarLotes',
        id: idInventario,
        lotes: JSON.stringify(lotes)
      },
      beforeSend: function () {
        $('#carga').show();
      }
    }).done(function (res) {
      if (String(res ?? '').trim() === 'Correcto') {
        Swal.fire({
          icon: 'success',
          title: 'Lotes guardados correctamente'
        });
        cargarLotesInventarioModal(idInventario);
        tablaInventario();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: String(res ?? '').trim()
        });
        console.log(String(res ?? '').trim());
      }
    }).fail(function () {
      console.log("Error ajax");
    }).always(function () {
      $('#carga').hide();
    });
  });

  $(document).on('click', '.bImprimirLote', function () {
    const idLote = $(this).attr('attrIDLote');
    window.open('./controladores/pdf/imprimirCodigo.php?tipo=lote&idLote=' + idLote, '_blank');
  });

  $(document).on('click', '.bImprimirTodosLotes, #bImprimirTodosLotesModal', function () {
    const idInventario = $(this).attr('attrID');
    window.open('./controladores/pdf/imprimirCodigo.php?tipo=todosLotes&idInventario=' + idInventario, '_blank');
  });
});

function cargarLotesInventarioModal(idInventario) {
  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: 'metodo=detalles&accion=inventario&tipo=consultarLotes&id=' + idInventario,
    beforeSend: function () {
      $('#carga').show();
    }
  }).done(function (res) {
    try {
      const data = JSON.parse(String(res ?? '').trim());
      if (data.error) {
        Swal.fire({ icon: 'error', title: 'Oops...', text: data.error });
        return;
      }

      $('#lotesSubtituloProducto').text(data.descripcion + ' (' + data.codigo + ')');
      $('#lblLoteCodigo').text(data.codigo);
      $('#lblLoteExistencia').text(data.existencia).attr('attrExistencia', data.existencia);
      $('#bGuardarLotes').attr('attrID', data.idInventario);
      $('#bImprimirTodosLotesModal').attr('attrID', data.idInventario);

      var filasHtml = '';
      if (data.lotes && data.lotes.length > 0) {
        data.lotes.forEach(function (lote) {
          filasHtml += `
            <tr attrIDLote="${lote.ID_Lote}">
              <td><span class="badge bg-dark font-monospace">${data.codigo}_${lote.ID_Lote}</span></td>
              <td><input type="text" class="form-control form-control-sm loteNombre" value="${lote.Nombre_Lote}" placeholder="Nombre / Lote"></td>
              <td><input type="date" class="form-control form-control-sm loteCaducidad" value="${lote.Fecha_Caducidad}"></td>
              <td><input type="number" class="form-control form-control-sm loteCantidad text-center" value="${lote.Cantidad}" min="0.01" step="any"></td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-secondary bImprimirLote" attrIDLote="${lote.ID_Lote}" title="Imprimir código de barras de este lote"><i class="fas fa-barcode"></i></button>
                <button type="button" class="btn btn-sm btn-outline-danger bEliminarLote" attrIDLote="${lote.ID_Lote}" title="Eliminar lote"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          `;
        });
      }

      $('#tbodyLotes').html(filasHtml);
      recalcularTotalesLotesModal();
      $('#modalLotesInventario').modal('show');
    } catch (e) {
      console.log('Error parse json lotes', res);
    }
  }).fail(function () {
    console.log("Error ajax");
  }).always(function () {
    $('#carga').hide();
  });
}

function recalcularTotalesLotesModal() {
  const existenciaTotal = parseFloat($('#lblLoteExistencia').attr('attrExistencia')) || 0;
  let sumaLotes = 0;

  $('#tbodyLotes tr').each(function () {
    const cant = parseFloat($(this).find('.loteCantidad').val()) || 0;
    sumaLotes += cant;
  });

  const disponible = existenciaTotal - sumaLotes;
  $('#lblLoteAsignado').text(sumaLotes.toFixed(2));
  $('#lblLoteDisponible').text(disponible.toFixed(2));

  if (sumaLotes > existenciaTotal) {
    $('#alertLotesMaxCant').text(existenciaTotal);
    $('#alertLotesExceso').removeClass('d-none');
    $('#bGuardarLotes').prop('disabled', true);
    $('#lblLoteAsignado').addClass('text-danger').removeClass('text-success');
    $('#lblLoteDisponible').addClass('text-danger').removeClass('text-success');
  } else {
    $('#alertLotesExceso').addClass('d-none');
    $('#bGuardarLotes').prop('disabled', false);
    $('#lblLoteAsignado').removeClass('text-danger');
    $('#lblLoteDisponible').removeClass('text-danger').addClass('text-success');
  }
}