// Comportamiento propio de la app de escritorio, encima de la interfaz web (sin modificar los JS de la web).

// Mensajes claros de la app: el servidor local responde "Offline~mensaje" y la caja muestra ese texto
// en lugar de su error genérico ("Error inesperado al registrar la venta").
(function () {
  let mensajePendiente = null;
  $.ajaxSetup({
    dataFilter: function (data) {
      if (typeof data === 'string' && data.trim().indexOf('Offline~') === 0) {
        mensajePendiente = data.trim().slice(8);
      }
      return data;
    }
  });
  const fireOriginal = Swal.fire.bind(Swal);
  Swal.fire = function (opciones) {
    if (mensajePendiente && opciones && typeof opciones === 'object' && opciones.icon === 'error') {
      opciones = Object.assign({}, opciones, { title: 'No se pudo completar', text: mensajePendiente });
      mensajePendiente = null;
    }
    return fireOriginal.apply(null, [opciones].concat(Array.prototype.slice.call(arguments, 1)));
  };
})();

function offlinePeticion(tipo, extra) {
  return $.ajax({
    url: 'index.php',
    type: 'POST',
    data: Object.assign({ metodo: 'detalles', accion: 'offline', tipo: tipo }, extra || {})
  }).then(function (res) {
    return JSON.parse(res);
  });
}

function offlinePintarEstado(estado) {
  if (!estado) {
    return;
  }
  $('#vtUltimaSync').text(estado.ultimaSync);
  $('.vtPendientesSubir').text(estado.pendientes);
}

// Pide un código nuevo cuando el equipo fue desenlazado; lo guardado en el equipo no se borra
function offlineReenlazar() {
  return Swal.fire({
    title: 'Este equipo fue desconectado',
    html: 'Tus ventas y cortes siguen guardados en este equipo.<br>Para subirlos, en el sistema web entra a <b>Dispositivos offline</b>, agrega este equipo con una caja cerrada y escribe aquí el código.',
    input: 'text',
    inputPlaceholder: 'XXXX-XXXX',
    inputAttributes: { maxlength: 9, style: 'text-transform: uppercase; text-align: center; font-size: 24px; letter-spacing: 3px;' },
    showCancelButton: true,
    confirmButtonText: 'Enlazar',
    cancelButtonText: 'Después',
    showLoaderOnConfirm: true,
    preConfirm: function (codigo) {
      return offlinePeticion('enlazar', { codigo: codigo }).then(function (r) {
        if (!r.ok) {
          Swal.showValidationMessage(r.error);
        }
        return r;
      });
    },
    allowOutsideClick: function () { return !Swal.isLoading(); }
  }).then(function (resultado) {
    if (resultado.value && resultado.value.ok) {
      offlinePintarEstado(resultado.value.estado);
      return true;
    }
    return false;
  });
}

function offlineAccion(tipo, textoCargando, alTerminar) {
  Swal.fire({ title: textoCargando, allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
  offlinePeticion(tipo).then(function (r) {
    offlinePintarEstado(r.estado);
    if (r.ok) {
      alTerminar(r);
    } else if (r.codigo === 'desenlazado') {
      offlineReenlazar().then(function (enlazado) {
        if (enlazado) {
          offlineAccion(tipo, textoCargando, alTerminar);
        }
      });
    } else {
      Swal.fire({ icon: 'warning', title: 'Sin conexión con el servidor', text: r.error });
    }
  }).fail(function () {
    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Error inesperado.' });
  });
}

function offlineRecargarVista() {
  $('#verVista .cargarVista[carga]').first().trigger('click');
}

$(document).on('click', '.bSincronizarOffline', function () {
  offlineAccion('sincronizar', 'Sincronizando con el servidor...', function () {
    Swal.fire({ icon: 'success', title: 'Sincronizado', text: 'Productos, inventario, promociones, clientes y usuarios están al día.', timer: 2500 });
    offlineRecargarVista();
  });
});

$(document).on('click', '.bSubirVentasOffline', function () {
  offlineAccion('subir', 'Subiendo ventas...', function (r) {
    let texto = r.subidas === 0 && !r.errores.length ? 'No había ventas por subir. Se actualizaron productos e inventario.' : 'Se subieron ' + r.subidas + ' venta(s).';
    if (r.errores && r.errores.length) {
      Swal.fire({ icon: 'warning', title: 'Algunas ventas no se subieron', html: texto + '<br>Las que fallaron siguen en el equipo para reintentar:<br><small>' + r.errores.map(function (e) { return $('<span>').text(e.error).html(); }).join('<br>') + '</small>' });
    } else {
      Swal.fire({ icon: 'success', title: 'Listo', text: texto, timer: 3000 });
    }
    offlineRecargarVista();
  });
});

// Pantalla Ventas (locales)
$(document).on('click', '.bTicketVentaLocal', function () {
  window.open('controladores/pdf/ticketVenta.php?id=' + $(this).attr('attrID'), '_blank', 'width=480,height=800');
});

$(document).on('click', '.bEliminarVentaLocal', function () {
  const id = $(this).attr('attrID');
  Swal.fire({
    title: '¿Eliminar esta venta?',
    text: 'Solo se borra de este equipo (aún no se ha subido). Las existencias se regresan al inventario.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonText: 'No, cancelar',
    confirmButtonText: 'Sí, eliminar'
  }).then(function (r) {
    if (!r.value) {
      return;
    }
    $.post('index.php', { metodo: 'eliminar', accion: 'ventas', idVenta: id }).done(function (res) {
      if (String(res).trim() === 'Correcto') {
        offlineRecargarVista();
        offlinePeticion('estado').then(function (e) { offlinePintarEstado(e.estado); });
      } else {
        Swal.fire({ icon: 'error', title: 'Oops...', text: String(res).replace(/^Offline~/, '') });
      }
    });
  });
});

// Caja cerrada: al presionar "Caja" se pide el monto de apertura y luego se abre la caja
$(document).on('click', '#bMenuCajas', function () {
  $('#carga').hide();
  Swal.fire({
    title: 'Abrir caja',
    text: 'Monto con el que inicia la caja',
    input: 'number',
    inputValue: '',
    inputAttributes: { min: 0, step: '0.01', style: 'text-align: center; font-size: 26px;' },
    showCancelButton: true,
    confirmButtonText: 'Abrir caja',
    cancelButtonText: 'Cancelar',
    inputValidator: function (valor) {
      if (valor === '' || isNaN(valor) || Number(valor) < 0) {
        return 'Escribe el monto de apertura.';
      }
    }
  }).then(function (r) {
    if (r.value === undefined) {
      return;
    }
    $.post('index.php', { metodo: 'insertar', accion: 'cajas', tipo: 'abrir', fkCaja: 0, montoCaja: r.value }).done(function (res) {
      if (String(res).trim() === 'Correcto') {
        document.getElementById('bAbrirCaja').click();
      } else {
        Swal.fire({ icon: 'error', title: 'Oops...', text: String(res) });
      }
    });
  });
});

// La app abre en la pantalla de ventas
jQuery(function () {
  setTimeout(function () {
    if ($('#bMenuVentas').length) {
      $('#bMenuVentas').trigger('click');
    }
  }, 50);
});
