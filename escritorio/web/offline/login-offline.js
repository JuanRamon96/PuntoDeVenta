// Pantalla de inicio de sesión de la app: registro y recuperar contraseña necesitan internet
jQuery(function () {
  $('a[data-bs-target="#modalOlvidoContra"]').closest('.text-end').hide();
  $('a[href="../#precios"]').closest('h6').html('Entra con tu mismo usuario y contraseña de VentasTool.<br>' +
    '¿Cambiaste tu contraseña en la web? <a href="javascript:void(0)" id="bSincronizarLogin">Sincroniza los usuarios</a>.');

  $(document).on('click', '#bSincronizarLogin', function () {
    Swal.fire({ title: 'Sincronizando...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
    $.post('index.php', { metodo: 'detalles', accion: 'offline', tipo: 'sincronizar' }).done(function (res) {
      const r = JSON.parse(res);
      Swal.fire(r.ok
        ? { icon: 'success', title: 'Usuarios actualizados', text: 'Ya puedes entrar con tu contraseña nueva.' }
        : { icon: 'warning', title: 'No se pudo sincronizar', text: r.error });
    });
  });
});
