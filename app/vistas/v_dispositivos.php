<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button class="btn btn-outline-danger btn-sm cargarVista" type="button" carga="v_dispositivos" titulo="Dispositivos offline"><i class="fa-solid fa-rotate-right"></i></button>
  </div>
</div>
<br>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <p class="mb-1">Enlaza la aplicación de escritorio o del celular para vender sin internet. Cada dispositivo usa una caja, y esa caja queda bloqueada en la web mientras el dispositivo esté enlazado.</p>
          </div>
          <div class="col-md-4 text-end">
            <button type="button" class="btn btn-primary" id="bAgregarDispositivo"><i class="fas fa-plus"></i> Agregar dispositivo</button>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaDispositivos" class="table table-hover table-striped table-bordered text-center" style="font-size: 13px;" width="100%">
              <thead>
                <tr>
                  <th>Dispositivo</th>
                  <th>Caja</th>
                  <th>Estado</th>
                  <th>Última sincronización</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="modalDispositivo" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Agregar dispositivo</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formDispositivo">
        <div class="modal-body">
          <div class="form-floating mb-3">
            <input placeholder="Nombre" id="nombreDispositivo" type="text" class="form-control" maxlength="100" required>
            <label for="nombreDispositivo">Nombre (ej. Laptop mostrador)</label>
          </div>
          <div class="form-floating mb-3">
            <select id="cajaDispositivo" class="form-select" required></select>
            <label for="cajaDispositivo">Caja que va a usar</label>
          </div>
          <small class="text-muted">Solo aparecen cajas cerradas y sin otro dispositivo enlazado.</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
          <button type="submit" class="btn btn-primary">Generar código <i class="fas fa-key"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function v_dispositivos() {
    cargarDispositivos();
  }

  function fechaDispositivo(f) {
    if (!f || f.indexOf('0000') === 0) {
      return '<span class="text-muted">Nunca</span>';
    }
    var d = new Date(f.replace(' ', 'T'));
    return d.toLocaleString('es-MX', { dateStyle: 'medium', timeStyle: 'short' });
  }

  function mostrarCodigoDispositivo(codigo) {
    Swal.fire({
      title: 'Código de enlace',
      html: '<div style="font-size: 40px; font-weight: bold; letter-spacing: 4px;">' + codigo + '</div>' +
        '<p class="mt-3">Escríbelo en la aplicación. Sirve una sola vez y caduca en 15 minutos.</p>',
      icon: 'info'
    });
  }

  function cargarDispositivos() {
    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: { metodo: 'consultar', accion: 'dispositivos' }
    }).done(function (res) {
      var datos;
      try {
        datos = JSON.parse(res);
      } catch (e) {
        console.log(res);
        return;
      }

      var html = '';
      datos.dispositivos.forEach(function (d) {
        var estado = '', acciones = '';
        if (d.estado === 'Activo') {
          estado = '<span class="badge rounded-pill bg-success">Activo</span>';
          if (d.pendientes > 0) {
            estado += '<br><small>' + d.pendientes + ' venta(s) sin subir</small>';
          }
          acciones = '<button type="button" class="btn btn-danger btn-sm bDesenlazarDispositivo" attrID="' + d.id + '"><i class="fas fa-link-slash"></i> Desenlazar</button>';
        } else if (d.codigoVencido) {
          estado = '<span class="badge rounded-pill bg-secondary">Código vencido</span>';
          acciones = '<button type="button" class="btn btn-primary btn-sm bCodigoDispositivo" attrID="' + d.id + '"><i class="fas fa-key"></i> Nuevo código</button> ' +
            '<button type="button" class="btn btn-outline-danger btn-sm bDesenlazarDispositivo" attrID="' + d.id + '"><i class="fas fa-trash"></i></button>';
        } else {
          estado = '<span class="badge rounded-pill bg-warning text-dark">Esperando código</span><br><b style="font-size: 16px; letter-spacing: 2px;">' + d.codigo + '</b>';
          acciones = '<button type="button" class="btn btn-outline-danger btn-sm bDesenlazarDispositivo" attrID="' + d.id + '"><i class="fas fa-trash"></i></button>';
        }

        html += '<tr><td>' + $('<span>').text(d.nombre).html() + '</td><td>' + $('<span>').text(d.caja).html() +
          (d.estado === 'Activo' ? '<br><small class="text-danger">Bloqueada: se está utilizando offline</small>' : '') +
          '</td><td>' + estado + '</td><td>' + fechaDispositivo(d.ultimaSync) + '</td><td>' + acciones + '</td></tr>';
      });

      if (html === '') {
        html = '<tr><td colspan="5">No hay dispositivos enlazados.</td></tr>';
      }
      $('#tablaDispositivos tbody').html(html);

      var opciones = '';
      datos.cajas.forEach(function (c) {
        opciones += '<option value="' + c.id + '">' + $('<span>').text(c.nombre).html() + '</option>';
      });
      $('#cajaDispositivo').html(opciones === '' ? '<option value="">No hay cajas cerradas disponibles</option>' : opciones);
    });
  }

  $(document).off('.dispositivos');

  $(document).on('click.dispositivos', '#bAgregarDispositivo', function () {
    $('#formDispositivo')[0].reset();
    cargarDispositivos();
    $('#modalDispositivo').modal('show');
  });

  $(document).on('submit.dispositivos', '#formDispositivo', function (e) {
    e.preventDefault();
    if (!$('#cajaDispositivo').val()) {
      Swal.fire({ icon: 'warning', title: 'No hay cajas disponibles', text: 'Cierra una caja o crea una nueva para enlazar el dispositivo.' });
      return;
    }

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: { metodo: 'insertar', accion: 'dispositivos', nombre: $('#nombreDispositivo').val(), idCaja: $('#cajaDispositivo').val() },
      beforeSend: function () { $('#carga').show(); }
    }).done(function (res) {
      res = String(res ?? '').trim();
      if (res.indexOf('Correcto~') === 0) {
        $('#modalDispositivo').modal('hide');
        cargarDispositivos();
        mostrarCodigoDispositivo(res.split('~')[1]);
      } else {
        Swal.fire({ icon: 'error', title: 'Oops...', text: res });
      }
    }).always(function () {
      $('#carga').hide();
    });
  });

  $(document).on('click.dispositivos', '.bCodigoDispositivo', function () {
    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: { metodo: 'modificar', accion: 'dispositivos', id: $(this).attr('attrID') }
    }).done(function (res) {
      res = String(res ?? '').trim();
      if (res.indexOf('Correcto~') === 0) {
        cargarDispositivos();
        mostrarCodigoDispositivo(res.split('~')[1]);
      } else {
        Swal.fire({ icon: 'error', title: 'Oops...', text: res });
      }
    });
  });

  $(document).on('click.dispositivos', '.bDesenlazarDispositivo', function () {
    var id = $(this).attr('attrID');
    Swal.fire({
      title: '¿Desenlazar este dispositivo?',
      html: 'La caja quedará libre para usarse en la web.<br>Si el dispositivo tiene ventas sin subir, no se pierden: se pueden subir después enlazándolo con un código nuevo.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonText: 'No, cancelar',
      confirmButtonText: 'Sí, desenlazar'
    }).then(function (result) {
      if (!result.value) {
        return;
      }
      $.ajax({
        url: 'index.php',
        type: 'POST',
        data: { metodo: 'eliminar', accion: 'dispositivos', id: id }
      }).done(function (res) {
        res = String(res ?? '').trim();
        if (res === 'Correcto') {
          cargarDispositivos();
        } else {
          Swal.fire({ icon: 'error', title: 'Oops...', text: res });
        }
      });
    });
  });
</script>
