var fila = null;
var tipoMapa = 0;

function v_clientes() {
  tablaClientes();

  $('#formClientes').validate({
    rules: {
      tipoCliente: {
        required: true
      },
      nombreCliente: {
        required: true
      },
      primerApellidoCliente: {
        required: true
      },
      razonSocialCliente: {
        required: true
      }
    },
    messages: {
      tipoCliente: {
        required: "El tipo es requerido."
      },
      nombreCliente: {
        required: "El nombre es requerido."
      },
      primerApellidoCliente: {
        required: "El apellido es requerido."
      },
      razonSocialCliente: {
        required: "La razón social es requerida."
      }
    },
    errorClass: 'is-invalid',        
    errorElement: 'div',
    submitHandler: function (form) {
      if ($('#bGuardarCliente').attr('tipo') === 'modificar') {
        Swal.fire({
          title: '¿Estás seguro que quieres modificar el cliente?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '¡No, cancelar!',
          confirmButtonText: '¡Si, modificar!'
        }).then((result) => {
          if (result.value) {
            guardarCliente();
          }
        });
      } else {
        guardarCliente();
      }
    }
  });

  $('#formMDireccion').validate({
    rules: {
      calleClienteM:{
        required: true
      },
      noExteriorClienteM: {
        required: true
      }
    },
    messages: {
      calleClienteM: {
        required: "El nombre de la calles es requerido."
      },
      noExteriorClienteM: {
        required: "El número exterior es requerido."
      }
    },
    errorClass: 'is-invalid',        
    errorElement: 'div',
    submitHandler: function(form) {
      var googleDire = '';
      if($('#latitudClienteM').val() != '' && $('#longitudClienteM').val() != ''){
        googleDire = `<a 
          class="btn btn-link btn-sm has-ripple" 
          href="https://www.google.com/maps?q=`+$('#latitudClienteM').val()+`,`+$('#longitudClienteM').val()+`" 
          target="_blank"
          latitud="`+$('#latitudClienteM').val()+`" longitud="`+$('#longitudClienteM').val()+`">
            <i class="fa-solid fa-location-dot"></i> Ubicación
        </a>`;
      }

      fila.children('td:eq(0)').text(String($('#calleClienteM').val() ?? '').trim());
      fila.children('td:eq(1)').children('span:eq(0)').text(String($('#noExteriorClienteM').val() ?? '').trim());
      fila.children('td:eq(1)').children('span:eq(1)').text(String($('#noInteriorClienteM').val() ?? '').trim());
      fila.children('td:eq(2)').children('span:eq(0)').text(String($('#cpClienteM').val() ?? '').trim());
      fila.children('td:eq(2)').children('span:eq(1)').text(String($('#coloniaClienteM').val() ?? '').trim());
      fila.children('td:eq(3)').children('span:eq(0)').text(String($('#ciudadClienteM').val() ?? '').trim());
      fila.children('td:eq(3)').children('span:eq(1)').text(String($('#estadoClienteM').val() ?? '').trim());
      fila.children('td:eq(3)').children('span:eq(2)').text(String($('#paisClienteM').val() ?? '').trim());
      fila.children('td:eq(4)').text(String($('#detallesClienteM').val() ?? '').trim());
      fila.children('td:eq(5)').html(googleDire);
      
      $("#modalDireccion").modal("hide");
    }
  });
}

function guardarCliente() {
    var direcciones = [];
    $("#verDirecciones").children('tr').each(function(index, el) {
      direcciones.push(
        {
          'calle': $(this).children('td:eq(0)').text(), 
          'noExterior': $(this).children('td:eq(1)').children('span:eq(0)').text(), 
          'noInterior': $(this).children('td:eq(1)').children('span:eq(1)').text(), 
          'cp': $(this).children('td:eq(2)').children('span:eq(0)').text(), 
          'colonia': $(this).children('td:eq(2)').children('span:eq(1)').text(), 
          'ciudad': $(this).children('td:eq(3)').children('span:eq(0)').text(), 
          'estado': $(this).children('td:eq(3)').children('span:eq(1)').text(), 
          'pais': $(this).children('td:eq(3)').children('span:eq(2)').text(), 
          'detalles': $(this).children('td:eq(4)').text(),
          'latitud': $(this).children('td:eq(5)').children('a').attr('latitud'),
          'longitud': $(this).children('td:eq(5)').children('a').attr('longitud')
        }
      );
    });

    var data = "metodo="+$("#bGuardarCliente").attr('tipo')+"&accion=clientes&tipoCliente="+$("#tipoCliente").val()+"&nombre="+String($("#nombreCliente").val() ?? '').trim()+"&primerApellido="+String($("#primerApellidoCliente").val() ?? '').trim()+"&segundoApellido="+String($("#segundoApellidoCliente").val() ?? '').trim()+"&sexo="+$("#sexoCliente").val()+"&razonSocial="+String($("#razonSocialCliente").val() ?? '').trim()+"&rfc="+String($("#rfcCliente").val() ?? '').trim()+"&regimen="+$("#regimenCliente").val()+"&telefono="+String($("#telefonoCliente").val() ?? '').trim()+"&segundoTelefono="+String($("#segundoTelCliente").val() ?? '').trim()+"&email="+String($("#emailCliente").val() ?? '').trim()+"&calle="+String($("#calleCliente").val() ?? '').trim()+"&noExterior="+String($("#noExteriorCliente").val() ?? '').trim()+"&noInterior="+String($("#noInteriorCliente").val() ?? '').trim()+"&colonia="+String($("#coloniaCliente").val() ?? '').trim()+"&cp="+String($("#cpCliente").val() ?? '').trim()+"&ciudad="+String($("#ciudadCliente").val() ?? '').trim()+"&estado="+String($("#estadoCliente").val() ?? '').trim()+"&pais="+String($("#paisCliente").val() ?? '').trim()+"&latitud="+String($("#latitudCliente").val() ?? '').trim()+"&longitud="+String($("#longitudCliente").val() ?? '').trim()+"&contacto="+String($("#contactoCliente").val() ?? '').trim()+"&puesto="+String($("#puestoCliente").val() ?? '').trim()+"&telContacto="+String($("#telContactoCliente").val() ?? '').trim()+"&emailContacto="+String($("#emailContactoCliente").val() ?? '').trim()+"&id="+$("#bGuardarCliente").attr('attrID')+"&direcciones="+JSON.stringify(direcciones);

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $('#carga').show();
      }
    })
    .done(function (res) {
      if(String(res ?? '').trim() == "Correcto"){
        if ($('#bGuardarCliente').attr('tipo') === 'modificar') {
          Swal.fire({
            icon: 'success',
            title: 'El cliente se ha modificado correctamente',
          });
        } else {
          Swal.fire({
            icon: 'success',
            title: 'El cliente se ha guardado correctamente',
          });
        }

        tablaClientes();
        $('#modalClientes').modal('hide');
      }else{
        if($('#bGuardarCliente').attr('tipo') === 'modificar'){
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al modificar el cliente.'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Error inesperado al guardar el cliente.'
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

function tablaClientes() {
  ajaxMyDatatable({
    'table': $('#tablaClientes'),
    'colums': [
      'Fecha',
      'Tipo',
      'Nombre',
      'Domicilio',
      'Contacto',
      'Acciones'
    ],
    'sort': [0, 'desc'],
    'url': 'index.php',
    'params': {
      'metodo': 'consultar',
      'accion': 'clientes'
    }
  });
}

var map = null;
var markerGroup = null;
function GenerarMapa(coordenadas){
  if (map != undefined) map.remove();
  
  setTimeout(function(){
    map = L.map('mapaUbicacionCliente').setView(coordenadas, 12);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
    }).addTo(map);

    markerGroup = L.layerGroup().addTo(map);
    L.marker(coordenadas).addTo(markerGroup); //Añadir marcador al mapa

    map.on('click', function(e) {
      // map.clearLayers();
      let latitud = e.latlng.lat;
      let longitud = e.latlng.lng;

      if(tipoMapa == 0){  
        $("#latitudCliente").val(latitud);
        $("#longitudCliente").val(longitud);
      }else if(tipoMapa == 1){
        $("#latitudClienteDire").val(latitud);
        $("#longitudClienteDire").val(longitud);
      }else{
        $("#latitudClienteM").val(latitud);
        $("#longitudClienteM").val(longitud);
      }

      markerGroup.clearLayers();
      L.marker(e.latlng).addTo(markerGroup); //Añadir marcador al mapa
    });
  },1000);
}

jQuery(document).ready(function ($) {
  $(document).on('click', '#bAgregarCliente', function () {
    $('#formClientes')[0].reset();
    var validator = $("#formClientes").validate();
    validator.resetForm();

    $('#bGuardarCliente').attr('tipo', 'insertar');
    $('#modalClientes').modal('show');
  });

  $(document).on('change', '#tipoCliente', function() {
    if($(this).val() == 'Física'){
      $("#contactoMoral").addClass('oculto');
      $("#razonMoral").addClass('oculto');
      $("#razonFisica").removeClass('oculto');
      $(".inputFisica").prop('disabled', false);
      $("#razonSocialCliente").prop('disabled', true);
    }else{
      $("#contactoMoral").removeClass('oculto');
      $("#razonMoral").removeClass('oculto');
      $("#razonFisica").addClass('oculto');
      $(".inputFisica").prop('disabled', true);
      $("#razonSocialCliente").prop('disabled', false);
    }
  });  

  $(document).on('click', '.bModificarCliente', function () {
      $('#formClientes')[0].reset();
      var validator = $("#formClientes").validate();
      validator.resetForm();
      const data = "metodo=detalles&accion=clientes&tipo=cliente&id="+$(this).attr('attrID');

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
        
        $("#tipoCliente").val(datos.Tipo);
        $("#nombreCliente").val(datos.Nombre);
        $("#primerApellidoCliente").val(datos.Primer_Apellido);
        $("#segundoApellidoCliente").val(datos.Segundo_Apellido);
        $("#razonSocialCliente").val(datos.Razon_Social);
        $("#rfcCliente").val(datos.RFC);
        $("#regimenCliente").val(datos.Regimen_CFDI);
        $("#telefonoCliente").val(datos.Telefono);
        $("#segundoTelCliente").val(datos.Segundo_Telefono);
        $("#emailCliente").val(datos.Email);
        $("#calleCliente").val(datos.Calle);
        $("#noExteriorCliente").val(datos.No_Exterior);
        $("#noInteriorCliente").val(datos.No_Interior);
        $("#coloniaCliente").val(datos.Colonia);
        $("#cpCliente").val(datos.CP);
        $("#ciudadCliente").val(datos.Ciudad);
        $("#estadoCliente").val(datos.Estado);
        $("#paisCliente").val(datos.Pais);
        $("#contactoCliente").val(datos.Contacto);
        $("#puestoCliente").val(datos.Puesto);
        $("#telContactoCliente").val(datos.Telefono_Contacto);
        $("#emailContactoCliente").val(datos.Email_Contacto);
        $("#latitudCliente").val(datos.Latitud);
        $("#longitudCliente").val(datos.Longitud);

        $("#verDirecciones").html('');
        var direcciones = Array.isArray(datos.Direcciones) ? datos.Direcciones : [datos.Direcciones];
        direcciones.forEach((direccion) => {
          var googleDire = '';
          if(direccion.Latitud != '' && direccion.Longitud != ''){
            googleDire = `<a 
              class="btn btn-link btn-sm has-ripple" 
              href="https://www.google.com/maps?q=`+direccion.Latitud+`,`+direccion.Longitud+`" 
              target="_blank"
              latitud="`+direccion.Latitud+`" longitud="`+direccion.Longitud+`">
                <i class="fa-solid fa-location-dot"></i> Ubicación
            </a>`;
          }

          $("#verDirecciones").append(`<tr>
            <td>`+ direccion.Calle +`</td>
            <td><b>No. Exterior:</b> <span>`+ direccion.No_Exterior +`</span><br><b>No. Interior:</b> <span>`+ direccion.No_Interior +`</span></td>
            <td><b>CP: </b><span>`+ direccion.CP +`</span><br><b>Col: </b><span>`+ direccion.Colonia +`</span></td>
            <td><b>Ciudad: </b><span>`+ direccion.Ciudad +`</span><br><b>Estado: </b><span>`+ direccion.Estado +`</span><br><b>País: </b><span>`+ direccion.Pais +`</span></td>
            <td>`+ direccion.Detalles +`</td>
            <td>`+ googleDire +`</td>
            <td><button type="button" class="btn btn-warning btn-sm bModificarDireccion"><i class="fas fa-pencil"></i></button> <button type="button" class="btn btn-danger btn-sm bQuitarDireccion"><i class="fas fa-trash"></i></button></td>
          </tr>`);
        });

        $('#bGuardarCliente').attr('tipo', 'modificar');
        $('#bGuardarCliente').attr('attrID', datos.ID_Cliente);
        $('#modalClientes').modal('show');
      })
      .fail(function () {
        console.log("Error ajax");
      })
      .always(function () {
        $('#carga').hide();
      });
  });

  $(document).on('click', '.bEliminarCliente', function () {
    const btn = $(this);
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar el cliente?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
      if (result.value) {
        const data = "metodo=eliminar&accion=clientes&id="+btn.attr('attrID');
        
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
              title: '¡El cliente se ha eliminado correctamente!',
            });

            tablaClientes();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Error inesperado al eliminar el cliente.',
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
  });

  $(document).on('click', '#bUbicacionClienteP', function() {
    tipoMapa = 0;
    var coordenadas = [20.705399, -102.345097];
    if(String($("#latitudCliente").val() ?? '').trim() != '' && String($("#longitudCliente").val() ?? '').trim() != ''){
      coordenadas = [String($("#latitudCliente").val() ?? '').trim(), String($("#longitudCliente").val() ?? '').trim()];
    }

    GenerarMapa(coordenadas);
    
    if(markerGroup != null){
      markerGroup.clearLayers();
    }

    $("#modalUbicacionCliente").modal('show');
  });

  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

  $(document).on('click', '#bAgregarDireccion', function() {
    $("#bFormDireccion").trigger('click');
  });

  $(document).on('submit', '#formDireccion', function(event) {
    event.preventDefault();
    
    var googleDire = '';
    if($('#latitudClienteDire').val() != '' && $('#longitudClienteDire').val() != ''){
      googleDire = `<a 
        class="btn btn-link btn-sm has-ripple" 
        href="https://www.google.com/maps?q=`+$('#latitudClienteDire').val()+`,`+$('#longitudClienteDire').val()+`" 
        target="_blank"
        latitud="`+$('#latitudClienteDire').val()+`" longitud="`+$('#longitudClienteDire').val()+`">
          <i class="fa-solid fa-location-dot"></i> Ubicación
      </a>`;
    }

    $("#verDirecciones").append(`<tr>
      <td>`+ String($("#calleClienteDire").val() ?? '').trim() +`</td>
      <td><b>No. Exterior:</b> <span>`+ String($("#noExteriorClienteDire").val() ?? '').trim() +`</span><br><b>No. Interior:</b> <span>`+ String($("#noInteriorClienteDire").val() ?? '').trim() +`</span></td>
      <td><b>CP: </b><span>`+ String($("#cpClienteDire").val() ?? '').trim() +`</span><br><b>Col: </b><span>`+ String($("#coloniaClienteDire").val() ?? '').trim() + `</span></td>
      <td><b>Ciudad: </b><span>`+ String($("#ciudadClienteDire").val() ?? '').trim() +`</span><br><b>Estado: </b><span>`+ String($("#estadoClienteDire").val() ?? '').trim() +`</span><br><b>País: </b><span>`+ String($("#paisClienteDire").val() ?? '').trim() +`</span></td>
      <td>`+ String($("#detallesClienteDire").val() ?? '').trim() +`</td>
      <td>`+ googleDire +`</td>
      <td><button type="button" class="btn btn-warning btn-sm bModificarDireccion"><i class="fas fa-pencil"></i></button> <button type="button" class="btn btn-danger btn-sm bQuitarDireccion"><i class="fas fa-trash"></i></button></td>
    </tr>`);

    document.getElementById('formDireccion').reset();
  });

  $(document).on('click', '.bQuitarDireccion', function() {
    $(this).parent().parent().remove();
  });

  $(document).on('hidden.bs.modal', '#modalDireccion', function () {
    setTimeout(function () {
      $("#modalCliente").modal("show");
    }, 200);
  });

  $(document).on('click', '.bModificarDireccion', function() {
    fila = $(this).parent().parent();
    document.getElementById('formMDireccion').reset();

    $("#calleClienteM").val(fila.children('td:eq(0)').text());
    $("#noExteriorClienteM").val(fila.children('td:eq(1)').children('span:eq(0)').text());
    $("#noInteriorClienteM").val(fila.children('td:eq(1)').children('span:eq(1)').text());
    $("#cpClienteM").val(fila.children('td:eq(2)').children('span:eq(0)').text());
    $("#coloniaClienteM").val(fila.children('td:eq(2)').children('span:eq(1)').text());
    $("#ciudadClienteM").val(fila.children('td:eq(3)').children('span:eq(0)').text());
    $("#estadoClienteM").val(fila.children('td:eq(3)').children('span:eq(1)').text());
    $("#paisClienteM").val(fila.children('td:eq(3)').children('span:eq(2)').text());
    $("#detallesClienteM").val(fila.children('td:eq(4)').text());
    $("#latitudClienteM").val(fila.children('td:eq(5)').children('a').attr('latitud'));
    $("#longitudClienteM").val(fila.children('td:eq(5)').children('a').attr('longitud'));
    
    $("#modalDireccion").modal("show");
  });

  $(document).on('click', '#bUbicacionDomCli', function() {
    tipoMapa = 1;
    var coordenadas = [20.705399, -102.345097];
    if(String($("#latitudClienteDire").val() ?? '').trim() != '' && String($("#longitudClienteDire").val() ?? '').trim() != ''){
      coordenadas = [String($("#latitudClienteDire").val() ?? '').trim(), String($("#longitudClienteDire").val() ?? '').trim()];
    }

    GenerarMapa(coordenadas);
    
    if(markerGroup != null){
      markerGroup.clearLayers();
    }

    $("#modalUbicacionCliente").modal('show');
  });

  $(document).on('click', '#bUbicacionClienteM', function() {
    tipoMapa = 2;
    var coordenadas = [20.705399, -102.345097];
    if(String($("#latitudClienteM").val() ?? '').trim() != '' && String($("#longitudClienteM").val() ?? '').trim() != ''){
      coordenadas = [String($("#latitudClienteM").val() ?? '').trim(), String($("#longitudClienteM").val() ?? '').trim()];
    }

    GenerarMapa(coordenadas);
    
    if(markerGroup != null){
      markerGroup.clearLayers();
    }

    $("#modalUbicacionCliente").modal('show');
  });
});