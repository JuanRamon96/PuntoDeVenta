const searchRegExp = new RegExp(',', 'g');

//formato de modeda a la clase .dinero
function moneda() {
  $(".dinero").each(function (index, el) {
    if (parseFloat($(this).html().replace('$', '').replace(/,/g, '')) < 0) {
      $(this).html(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) * -1);
      $(this).html('$' + new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) * 100) / 100));
      $(this).html('-' + $(this).html());
      //$(this).css('color', 'red');
    } else {
      $(this).html('$' + new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) * 100) / 100));
    }
  });

  $(".porcentaje").each(function (index, el) {
    if (parseFloat($(this).html().replace('%', '').replace(/,/g, '')) < 0) {
      $(this).html(parseFloat($(this).html().replace('%', '').replace(/,/g, '')) * -1);
      $(this).html(new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('%', '').replace(/,/g, '')) * 100) / 100) + '%');
      $(this).html('-' + $(this).html());
      $(this).css('color', 'red');
    } else {
      $(this).html(new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('%', '').replace(/,/g, '')) * 100) / 100) + '%');
    }
  });

  $(".cantidad").each(function (index, el) {
    $(this).html(new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) * 100) / 100));
  });
}

/*const socket = io('http://localhost:3000', {
    reconnection: true,
    reconnectionDelayMax: 1000
});

socket.on("connect", () => {
    console.log("Connected as: "+socket.id); 
});*/

jQuery(document).ready(function ($) {
  setTimeout(function () {
    if ($("#bMenuDashboard").length > 0) {
      $("#bMenuDashboard").trigger("click");
    } else if ($("#bMenuProductos").length > 0) {
      $("#bMenuProductos").trigger("click");
    } else {
      $("#carga").hide();
    }
  }, 0);

  $(document).on('click', '#vtSidebarOverlay', function () {
    $(".pcoded-navbar").removeClass('mob-open');
    $("#mobile-collapse,#mobile-collapse1").removeClass('on');
  });

  $(document).on('click', '.pcoded-navbar .cargarVista', function () {
    if ($(window).width() < 992) {
      $(".pcoded-navbar").removeClass('mob-open');
      $("#mobile-collapse,#mobile-collapse1").removeClass('on');
    }
  });
  //moneda();

  setInterval(function () {
    var data = "metodo=renovar";

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data
    })
      .done(function (res) {
        console.log("Sesion renovada");
      })
      .fail(function () {
        console.log("error ajax");
      });
  }, 60000 * 10);

  function cargarVista(element) {
    var nombre = element.attr('carga'), titulo = element.attr('titulo'), id = element.attr('id'), atri = element.attr('atri'), pesta = element.attr('pesta');

    var data = "metodo=cambiar&accion=" + nombre + "&atri=" + atri + "&pesta=" + pesta;

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      beforeSend: function () {
        $("#carga").show();
      }
    })
      .done(function (res) {
        //console.log(String(res ?? '').trim());
        $("#verVista").html(res);
        $(".vistaTitulo").html(titulo);

        crearDataTable();

        if (typeof window[nombre] === 'function') {
          window[nombre]();
        }

        //File inputs
        $(".file").fileinput({
          language: "es",
        });

        moneda();
      })
      .fail(function () {
        console.log("Error ajax");
      }).always(function () {
        $("#carga").hide();
      });
  }

  $(".cargarVista").on('click', function () {
    cargarVista($(this));
  });

  $(document).on('click', '.cargarVista', function () {
    cargarVista($(this));
  });

  $(document).on('click', '#bCerrarSe', function () {
    var data = "metodo=eliminar&accion=login";

    $.ajax({
      url: 'index.php',
      type: 'POST',
      data: data,
      /*beforeSend: function() {
          $("#carga").show();
      }*/
    })
      .done(function (res) {
        console.log(String(res ?? '').trim());
        window.location.reload();
      })
      .fail(function () {
        console.log("Error ajax");
      }).always(function () {
        //$("#carga").hide();
      });
  });

  /*$(document).on('click', '#bNotifi', function(event) {
      if($("#venataNoti").hasClass('oculto')){
          $("#venataNoti").removeClass('oculto');

          if(!$("#numNoti").hasClass('oculto')){
              var data="metodo=modificar&accion=notificaciones&tipo=vistas";
      
              $.ajax({
                  url: 'index.php',
                  type: 'POST',
                  data: data,
                  beforeSend: function() {
                      $("#carga").show();
                  }
              })
              .done(function(res) {
                  if (String(res ?? '').trim() == "Correcto") {
                      $("#numNoti").addClass('oculto');
                  }else{
                      Swal.fire({
                          icon: 'error',
                          title: 'Oops...',
                          text: 'Error inesperado al ver las notificaciones.'
                      });

                      console.log(String(res ?? '').trim());
                  }
              })
              .fail(function() {
                  console.log("Error ajax");
              }).always(function() {
                  //$("#carga").hide();
              }); 
          }
      }else{
          $("#venataNoti").addClass('oculto');
      }
  });

  $(document).on('click', '#bEliminarTodasNoti', function() {
      var data="metodo=eliminar&accion=notificaciones&tipo=todas";
      
      $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function() {
              $("#carga").show();
          }
      })
      .done(function(res) {
          if (String(res ?? '').trim() == "Correcto") {
              $("#mostrarNoti").html('<h6 style="padding-top: 40%;" class="text-center">No hay notificaciones</h6>');
          }else{
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Error inesperado al eliminar las notificaciones.'
              });

              console.log(String(res ?? '').trim());
          }
      })
      .fail(function() {
          console.log("Error ajax");
      }).always(function() {
          $("#carga").hide();
      }); 
  });

  $(document).on('click', '.bEliminarNoti', function() {
      var btn = $(this);
      var data="metodo=eliminar&accion=notificaciones&tipo=una&id="+btn.attr('attrID');
      
      $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function() {
              $("#carga").show();
          }
      })
      .done(function(res) {
          if (String(res ?? '').trim() == "Correcto") {
              btn.parent().parent().parent().remove();

              if($("#mostrarNoti").children('div').length == 0){
                  $("#mostrarNoti").html('<h6 style="padding-top: 40%;" class="text-center">No hay notificaciones</h6>');
              }
          }else{
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Error inesperado al elimnar la notificación.'
              });

              console.log(String(res ?? '').trim());
          }
      })
      .fail(function() {
          console.log("Error ajax");
      }).always(function() {
          //$("#carga").hide();
      }); 
  });*/

  let port;
  let reader;
  let decoder = new TextDecoder();

  function errorSoporteBascula() {
    if (!window.isSecureContext) {
      return 'La conexion a la bascula solo funciona en un sitio seguro. Abre el sistema desde http://localhost o con https (no por la IP de la red).';
    }
    if (!('serial' in navigator)) {
      return 'Este navegador no permite conectar la bascula. Usa Google Chrome o Microsoft Edge en una computadora.';
    }
    return null;
  }

  async function abrirPuertoBascula(puerto) {
    try {
      await puerto.open({ baudRate: 9600 });
    } catch (err) {
      // Si el puerto ya estaba abierto lo reutilizamos
      if (!(err && err.name === 'InvalidStateError')) {
        throw err;
      }
    }

    if (reader) {
      try { reader.releaseLock(); } catch (e) {}
    }

    port = puerto;
    reader = port.readable.getReader();
    $('#bConectarBascula').html('Bascula conectada <i class="fa-solid fa-check"></i>');
  }

  // Al abrir el modal de granel se reutiliza un puerto ya autorizado (sin mostrar el selector)
  $(document).on('shown.bs.modal', '#MGranel', async () => {
    if (port || errorSoporteBascula()) {
      return;
    }

    try {
      const puertos = await navigator.serial.getPorts();
      if (puertos.length > 0) {
        await abrirPuertoBascula(puertos[0]);
      }
    } catch (err) {
      console.log('No se pudo reconectar la bascula: ', err);
    }
  });

  $(document).on('click', '#bConectarBascula', function (event) {
    event.preventDefault();

    const error = errorSoporteBascula();
    if (error) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: error
      });
      return;
    }

    if (port) {
      Swal.fire({
        icon: 'info',
        title: 'La bascula ya esta conectada'
      });
      return;
    }

    // Si el selector de puertos se abre en pantalla completa, Chrome/Edge la quitan y despues
    // la niegan ("not granted") hasta recargar. Por eso primero se sale de pantalla completa
    // (el handler de fullscreenchange de caja.js cierra la caja) y luego se piden los puertos.
    // El click sigue siendo valido para requestPort() porque la activacion dura unos segundos.
    const salirPantallaCompleta = document.fullscreenElement && document.exitFullscreen
      ? document.exitFullscreen().catch(() => {})
      : Promise.resolve();

    salirPantallaCompleta
      .then(() => navigator.serial.requestPort())
      .then(puerto => abrirPuertoBascula(puerto))
      .then(() => {
        Swal.fire({
          icon: 'success',
          title: 'La bascula se conecto correctamente',
          text: 'Presiona "Abrir caja" para continuar vendiendo.'
        });
      })
      .catch(err => {
        let texto = 'Error al conectar con la bascula: ' + err;

        if (err && err.name === 'NotFoundError') {
          texto = 'No se selecciono ningun puerto. Verifica que la bascula este conectada y encendida, y elige su puerto en la ventana del navegador.';
        } else if (err && err.name === 'SecurityError') {
          texto = 'El navegador bloqueo el acceso a los puertos. Revisa los permisos del sitio o abre el sistema desde http://localhost.';
        } else if (err && err.name === 'NetworkError') {
          texto = 'No se pudo abrir el puerto. Puede estar en uso por otro programa u otra pestana.';
        }

        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: texto
        });
      });
  });

  $(document).on('click', '#bOptenerPeso', async function (event) {
    event.preventDefault();

    if (!port) {
      Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: "Primero presiona el boton para conectar la b\u00e1scula"
      });

      return;
    }

    $("#carga").show();

    try {
      const writer = port.writable.getWriter();
      await writer.write(new TextEncoder().encode("P\r\n"));  // o prueba con "S\r\n" si no responde
      writer.releaseLock();

      const { value } = await reader.read();
      const text = decoder.decode(value);

      console.log('Dato recibido:', text);

      const match = text.match(/([\d.]+)\s*kg/);
      if (match) {
        const peso = parseFloat(match[1]);
        $("#cantidadGranel").val(peso.toFixed(3));
        $("#cantidadGranel").trigger('input');
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'Dato inválido en la bascula, intenta de nuevo por favor.'
        });
      }
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: "Error al leer el peso: " + err
      });
    } finally {
      $("#carga").hide();
    }
  });

  document.addEventListener("wheel", function (event) {
    if (document.activeElement.type === "number") {
      document.activeElement.blur();
    }
  });

  paypal.Buttons({
    createOrder: function (data, actions) {
      // This function sets up the details of the transaction, including the amount and line item details.
      return actions.order.create({
        purchase_units: [{
          amount: {
            value: '99.00'
          }
        }]
      });
    },
    onApprove: function (data, actions) {
      // This function captures the funds from the transaction.
      return actions.order.capture().then(function (details) {
        // This function shows a transaction success message to your buyer.
        //alert('Transaction completed by ' + details.payer.name.given_name);
        var data = "metodo=modificar&accion=suscripcion&tipo=mes";

        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function () {
            $("#carga").show();
          }
        })
          .done(function (res) {
            if (String(res ?? '').trim() == "Correcto") {
              Swal.fire({
                icon: 'success',
                title: '¡Tu suscripción ha sido renovada correctamente!',
                text: 'Gracias por suscribirte por un mes más con nosotros. '
              }).then((result) => {
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Error inesperado al guardar el pago.',
                footer: '¿Por qué tengo este error? Contáctanos en ventastool@bigtool.mx'
              });

              console.log(String(res ?? '').trim());
            }
          })
          .fail(function () {
            console.log("Error ajax");
          }).always(function () {
            $("#carga").hide();
          });
      });
    }
  }).render('#paypal-button-container1');
  //This function displays Smart Payment Buttons on your web page.

  paypal.Buttons({
    createOrder: function (data, actions) {
      // This function sets up the details of the transaction, including the amount and line item details.
      return actions.order.create({
        purchase_units: [{
          amount: {
            value: '948.00'
          }
        }]
      });
    },
    onApprove: function (data, actions) {
      // This function captures the funds from the transaction.
      return actions.order.capture().then(function (details) {
        // This function shows a transaction success message to your buyer.
        //alert('Transaction completed by ' + details.payer.name.given_name);
        var data = "metodo=modificar&accion=suscripcion&tipo=ano";

        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: data,
          beforeSend: function () {
            $("#carga").show();
          }
        })
          .done(function (res) {
            if (String(res ?? '').trim() == "Correcto") {
              Swal.fire({
                icon: 'success',
                title: '¡Tu suscripción ha sido renovada correctamente!',
                text: 'Gracias por suscribirte por un año más con nosotros. '
              }).then((result) => {
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Error inesperado al guardar el pago.',
                footer: '¿Por qué tengo este error? Contáctanos en ventastool@bigtool.mx'
              });

              console.log(String(res ?? '').trim());
            }
          })
          .fail(function () {
            console.log("Error ajax");
          }).always(function () {
            $("#carga").hide();
          });
      });
    }
  }).render('#paypal-button-container2');
  //This function displays Smart Payment Buttons on your web page.
});