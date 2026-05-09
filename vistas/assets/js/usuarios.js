function v_usuarios() {
  tablaUsuarios();

  $('#formUsuarios').validate({
    rules: {
      nombre: {
        required: true
      },
      primerApellido: {
        required: true
      },
      correo: {
        required: true,
        email: true
      },
      password: {
        required: true
      },
      passwordConfirmar: {
        required: true,
        equalTo: '#password'
      },
      tipo: {
        required: true
      },
      estatus: {
        required: true
      }
    },
    messages: {
      nombre: {
        required: 'Ingrese un nombre'
      },
      primerApellido: {
        required: 'Ingrese un apellido'
      },
      correo: {
        required: 'Ingrese un correo',
        email: 'Ingrese un correo valido'
      },
      password: {
        required: 'Ingrese una contraseña'
      },
      passwordConfirmar: {
        required: 'Ingrese una contraseña',
        equalTo: 'Las contraseñas no coinciden'
      },
      tipo: {
        required: 'Seleccione un tipo de usuario'
      },
      estatus: {
        required: 'Seleccione un estatus'
      }
    },
    submitHandler: function(form) {
      if ($('#bGuardarUsuario').attr('tipo') === 'insertar'){
        guardarUsuario();
      }else{
        Swal.fire({
          title: '¿Estas seguro que deseas modificar el usuario?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '¡No, cancelar!',
          confirmButtonText: '¡Si, modificar!'
        }).then(({ value }) => {
          if (value){
            guardarUsuario();
          }
        });
      }
    }
  });
}

function guardarUsuario() {
  const data = new FormData(document.querySelector('#formUsuarios'));
  const guardarUsuarioBtn = $('#bGuardarUsuario');
  data.append('metodo', guardarUsuarioBtn.attr('tipo'));
  data.append('accion', 'usuarios');
  data.append('id', guardarUsuarioBtn.attr('attrID'));
  data.append('foto', $('#bGuardarProducto').attr('foto'));

  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: data,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: function() {
      $('#carga').show();
    }
  })
  .done(function(res){
    if ($.trim(res) == 'Correcto') {
      var title = guardarUsuarioBtn.attr('tipo') === 'insertar' ? '¡Usuario agregado correctamente!' : '¡Usuario modificado correctamente!'
      
      Swal.fire({
        icon: 'success',
        title: title,
      });

      tablaUsuarios();
      $('#modalUsuarios').modal('hide');
    }else if ($.trim(res).includes(`Duplicate entry '${data.get('correo')}' for key 'Correo'`)) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '¡El correo ya esta registrado!, Por favor ingrese otro correo'
      });
    } else {
      var text = guardarUsuarioBtn.attr('tipo') === 'insertar' ? '¡Error inesperado al agregar el usuario!' : '¡Error inesperado al modificar el usuario!'
      
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: text
      });

      console.log($.trim(res));
    }
  })
  .fail(function() {
    console.error('Error ajax');
  })
  .always(() => {
    $('#carga').hide();
  });
}

function tablaUsuarios(){
  ajaxMyDatatable({
    table: $('#tablaUsuarios'),
    colums: [
      'Fecha_Alta',
      'Nombre',
      'Correo',
      'Estatus',
      'Tipo_Usuario',
      'Permisos',
      'Acciones'
    ],
    sort: [0, 'desc'],
    url: 'index.php',
    params: {
      metodo: 'consultar',
      accion: 'usuarios',
    }
  });
}

const showHidePassword = (span, passwordId) => {
  const passwordInput = $(passwordId)
  const spanIcon1 = $(span).children().first()
  const spanIcon2 = $(span).children().last()
  passwordInput.attr('type') === 'password' ? passwordInput.attr('type', 'text') : passwordInput.attr('type', 'password')
  spanIcon1.toggleClass('d-none')
  spanIcon2.toggleClass('d-none')
}

jQuery(document).ready($ => {
  $(document).on('click', '#bModificarFoto', () => {
    $('#fotoUsuario').trigger('click')
    $(document).on('change', '#fotoUsuario', function () {
      const tipo = $(this).val().substring($(this).val().lastIndexOf('.')).toLowerCase()
      if (!this.files?.[0]) return

      let text = ''
      if (!['.png', '.jpg', '.jpeg'].includes(tipo)) {
        text = 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg'
      }
      if ((this.files[0]?.size / (1024 * 1024)) > 10) {
        text = 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
      }
      if (text) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text
        })
        return
      }

      $('#bCancelarFoto').attr('foto', $('#mosFotoUsuario').css('background-image'))
      $('#bModificarFoto').addClass('oculto')
      $('#bCancelarFoto').removeClass('oculto')

      const reader = new FileReader()

      reader.onload = e => {
        $('#mosFotoUsuario').css('background-image', `url('${e.target.result}')`)
        $('#mosFotoUsuario').parent().attr('href', e.target.result)
      }

      reader.readAsDataURL(this.files[0])
    })
  })
  $(document).on('click', '#bAgregarUsuario', () => {
    $('#formUsuarios')[0].reset()
    $('#changePassword').parent().addClass('d-none')
    $('#password').attr('disabled', false)
    $('#passwordConfirmar').attr('disabled', false)
    $('#bCancelarFoto').addClass('oculto')
    $('#bModificarFoto').removeClass('oculto')
    $('#mosFotoUsuario').css('background-image', 'url("vistas/assets/images/default.jpg")')
    $('#bGuardarUsuario').attr('tipo', 'insertar')
    $('#modalUsuarios').modal('show')
  })

  $(document).on('click', '#bCancelarFoto', function () {
    $(this).addClass('oculto')
    $('#bModificarFoto').removeClass('oculto')
    $('#fotoUsuario').val('')
    $('#mosFotoUsuario').css('background-image', $(this).attr('foto'))
    $('#mosFotoUsuario').parent().attr('href', $(this).attr('foto'))
  })

  $(document).on('click', '.bEliminarUsuario', function () {
    const btn = $(this);
    Swal.fire({
      title: '¿Estás seguro de eliminar este usuario?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: '¡No, cancelar!',
      confirmButtonText: '¡Si, eliminar!'
    }).then(({ value }) => {
      if (value) {
        const data = `metodo=eliminar&accion=usuarios&id=${btn.attr('attrID')}&foto=${btn.attr('foto')}`;
        
        $.ajax({
          url: 'index.php',
          type: 'POST',
          data,
          beforeSend: function() { 
            $('#carga').show();
          }
        })
        .done(function(res) {
          if($.trim(res) === 'Correcto'){
            Swal.fire({
              icon: 'success',
              title: '¡Usuario eliminado correctamente!'
            });

            tablaUsuarios();
            $("#modalUsuarios").modal('hide');
          }else{
            Swal.fire({
              icon: 'success',
              title: 'Oops',
              text: 'Error inesperado al eliminar el usuario'
            });

            console.log($.trim(res));
          }
        })
        .fail(function() {
          console.error('Error ajax');
        })
        .always(function() { 
          $('#carga').hide() ;
        });
      }
    });
  });

  $(document).on('click', '.bModificarUsuario', function () {
    $('#bModificarFoto').removeClass('oculto');
    $('#bCancelarFoto').addClass('oculto');
    
    const data = `metodo=detalles&accion=usuarios&tipo=usuarios&id=${$(this).attr('attrID')}`

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function() {
        $('#carga').show();
      }
    })
    .done(function(res) {
      //console.log($.trim(res));
      var resA = JSON.parse($.trim(res));

      $('#formUsuarios')[0].reset();
      $('#nombre').val(resA.Nombre);
      $('#primerApellido').val(resA.Primer_Apellido);
      $('#segundoApellido').val(resA.Segundo_Apellido);
      $('#correo').val(resA.Correo);
      $('#estatus').val(resA.Estatus);
      $('#tipo').val(resA.Tipo_Usuario);
      $('#puesto').val(resA.Puesto);

      $('#changePassword').parent().removeClass('d-none');
      $('#password').attr('disabled', true);
      $('#passwordConfirmar').attr('disabled', true);
      $('#bCancelarFoto').addClass('oculto');
      $('#bModificarFoto').removeClass('oculto');
      const fotoURL = resA.Foto !== '' ? `vistas/assets/images/usuarios/${resA.Foto}` : 'vistas/assets/images/default.jpg';

      $('#mosFotoUsuario').css('background-image', `url('${fotoURL}')`);
      $('#mosFotoUsuario').parent().attr('href', fotoURL);
      $('#bCancelarFoto').attr('foto', `url('${fotoURL}')`)
      $('#mosFotoUsuario').css('background-image', `url('${fotoURL}')`)
      $('#mosFotoUsuario').parent().attr('href', fotoURL);
      
      $('#bGuardarUsuario').attr('tipo', 'modificar')
      $('#bGuardarUsuario').attr('attrID', resA.ID_Usuario)
      $('#bGuardarUsuario').attr('foto', resA.Foto)
      $('#modalUsuarios').modal('show');
    })
    .fail(function() {
      console.error('Error ajax');
    })
    .always(function() { 
      $('#carga').hide(); 
    });
  });

  $(document).on('click', '#mostrarPassword', () => {
    const pwd = $('#password');
    const pwdRepeat = $('#passwordConfirmar');
    pwd.attr('type', pwd.attr('type') === 'password' ? 'text' : 'password');
    pwdRepeat.attr('type', pwdRepeat.attr('type') === 'password' ? 'text' : 'password');
  });

  $(document).on('click', '#changePassword', () => {
    const password = $('#password');
    const passwordConfirmar = $('#passwordConfirmar');
    password.attr('disabled', !password.attr('disabled'));
    passwordConfirmar.attr('disabled', !passwordConfirmar.attr('disabled'));
  });

  var botonP = null;
  $(document).on('click', '.bPermisos', function() {
    $("#carga").show();
    botonP = $(this);
    $(".checkPermisos").prop('checked', false);

    if($(this).attr('cadena') != ""){
      var cadena = $(this).attr('cadena').split('~'), x = 0, y = 1;

      $("#tablaPermisos").children('tbody').children('tr').each(function(index, el) {
        if(cadena[x] != undefined){
          var separa = cadena[x].split(',');
          y = 1;

          if(separa[0] == $.trim($(this).children('td:eq(0)').text())){
            $(this).children('td:eq(1)').children('div.form-check').each(function(index, el) {
              if(separa[y] == '1'){
                $(this).children('input.form-check-input').prop('checked', true);
              }else{
                $(this).children('input.form-check-input').prop('checked', false);
              }

              y++;
            });
          }
        }
        
        x++;
      });
    }

    $("#modalPermisos").modal("show");
    $("#carga").hide();
  });

  $(document).on('click', '.checkPermisos', function() {
    if($.trim($(this).parent().children('label').text()) == "Ver" && $(this).prop("checked") == false){
      $(this).parent().parent().children('div.form-check').children('input.form-check-input').prop('checked', false);
    }else if($.trim($(this).parent().children('label').text()) != "Ver" && $(this).prop("checked") == true && $.trim($(this).parent().parent().children('div.form-check:eq(0)').children('label').text()) == "Ver"){
      $(this).parent().parent().children('div.form-check:eq(0)').children('input.form-check-input').prop('checked', true);
    }

    var cadena = "", x = 0, y = 0;
    $("#tablaPermisos").children('tbody').children('tr').each(function(index, el) {
      y = 0;
      x++;
      cadena += $.trim($(this).children('td:eq(0)').text())+',';

      var padre = $(this);
      padre.children('td:eq(1)').children('div.form-check').each(function(index, el) {
        y++;

        if($(this).children('input.form-check-input').prop('checked')){
          cadena += '1';
        }else{
          cadena += '0';
        }

        if(padre.children('td:eq(1)').children('div.form-check').length > y){
          cadena += ',';
        }
      });

      if($("#tablaPermisos").children('tbody').children('tr').length > x){
        cadena += '~';
      }
    });

    botonP.attr('cadena', cadena);
    var data = "metodo=detalles&accion=usuarios&tipo=permisos&id="+botonP.attr('attrID')+"&cadena="+cadena;

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function() {
        $("#carga").show();
      }
    })
    .done(function(res) {
      if($.trim(res) != "Correcto"){
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Error inesperado al camibar los permisos.'
        });

        console.log($.trim(res));
      }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      $("#carga").hide();
    });
  });
});