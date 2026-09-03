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
    if ($("#bMenuProductos").length > 0) {
      $("#bMenuProductos").trigger("click");
    } else {
      $("#carga").hide();
    }
  }, 0);
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
        //console.log($.trim(res));
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
        console.log($.trim(res));
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
                  if ($.trim(res) == "Correcto") {
                      $("#numNoti").addClass('oculto');
                  }else{
                      Swal.fire({
                          icon: 'error',
                          title: 'Oops...',
                          text: 'Error inesperado al ver las notificaciones.'
                      });

                      console.log($.trim(res));
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
          if ($.trim(res) == "Correcto") {
              $("#mostrarNoti").html('<h6 style="padding-top: 40%;" class="text-center">No hay notificaciones</h6>');
          }else{
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Error inesperado al eliminar las notificaciones.'
              });

              console.log($.trim(res));
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
          if ($.trim(res) == "Correcto") {
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

              console.log($.trim(res));
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

  $(document).on('click', '#bConectarBascula', async () => {
    try {
      port = await navigator.serial.requestPort();
      await port.open({ baudRate: 9600 });

      Swal.fire({
        icon: 'success',
        title: 'La basucla se conecto correctamente'
      });

      reader = port.readable.getReader();
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: "Error al conectar con la bascula: " + err
      });
    }
  });

  $(document).on('click', '#bOptenerPeso', async () => {
    if (!port) {
      Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: "Primero presiona el boton para conectar la báscula"
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
            if ($.trim(res) == "Correcto") {
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

              console.log($.trim(res));
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
            if ($.trim(res) == "Correcto") {
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

              console.log($.trim(res));
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