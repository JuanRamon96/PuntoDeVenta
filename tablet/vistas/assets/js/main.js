const searchRegExp = new RegExp(',', 'g'); 

//formato de modeda a la clase .dinero
function moneda() {
    $(".dinero").each(function(index, el) {
        if(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) < 0){
            $(this).html(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) * -1);
            $(this).html('$'+new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) * 100) / 100));
            $(this).html('-'+$(this).html());
            //$(this).css('color', 'red');
        }else{
            $(this).html('$'+new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) * 100) / 100));
        }
    });

    $(".porcentaje").each(function(index, el) {
        if(parseFloat($(this).html().replace('%', '').replace(/,/g, '')) < 0){
            $(this).html(parseFloat($(this).html().replace('%', '').replace(/,/g, '')) * -1);
            $(this).html(new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('%', '').replace(/,/g, '')) * 100) / 100)+'%');
            $(this).html('-'+$(this).html());
            $(this).css('color', 'red');
        }else{
            $(this).html(new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('%', '').replace(/,/g, '')) * 100) / 100)+'%');
        }
    });

    $(".cantidad").each(function(index, el) {
        $(this).html(new Intl.NumberFormat('en-US').format(Math.round(parseFloat($(this).html().replace('$', '').replace(/,/g, '')) * 100) / 100));
    });
}

function productos() {
    var data = "metodo=consultar&accion=ventas&buscar="+$.trim($("#buscarProductos").val());
        
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: data,
        beforeSend: function() {
            $("#carga").show();
        }
    })
    .done(function(res) {
        $("#verProductos").html($.trim(res));
        moneda();
    })
    .fail(function() {
        console.log("Error ajax");
    }).always(function() {
        $("#carga").hide();
    }); 
}

function total() {
    var total = 0;
    $("#productosBody").children('tr.filaProd').each(function(index, el) {
        total += parseFloat($(this).children('td:eq(2)').text().replace('$', '').replace(searchRegExp, ''));
    });

    $("#totalVenta").html(total);
    moneda();
}

var comun = 0, imprimir = 0;
function formsVentas() {
    $('#formProdComun').validate({
        rules: {
            nombreComun: {
                required: true
            },
            cantidadComun: {
                required: true,
                min: 0.01
            },
            precioComun: {
                required: true,
                min: 0.01
            }
        },
        messages: {
            nombreComun: {
                required: "El nombre es requerido."
            },
            cantidadComun: {
                required: "La cantidad es requerida.",
                min: "El valor debe ser mayor a 0."
            },
            precioComun: {
                required: "El precio es requerido.",
                min: "El valor debe ser mayor a 0."
            }
        },
        submitHandler: function(form) {
            $("#productosBody").append(`<tr class="filaNomProd" attrID="c`+comun+`">
                <th colspan="4" class="text-left h4"><span>`+$.trim($("#nombreComun").val())+`</span></th>
            </tr>
            <tr class="filaProd" attrID="c`+comun+`">
                <td width="25%"><span class="dinero">`+$("#precioComun").val()+`</span></td>
                <td width="40%">
                    <button type="button" class="btn btn-outline-secondary bMenos"><i class="fas fa-minus"></i></button>
                    <span class="cantidad" style="padding: 8px 8px;">`+$("#cantidadComun").val()+`</span>
                    <button type="button" class="btn btn-outline-secondary bMas"><i class="fas fa-plus"></i></button>
                </td>
                <td width="30%"><span class="dinero">`+(parseFloat($("#precioComun").val()) * parseFloat($("#cantidadComun").val()))+`</span></td>
                <td width="5%"><button type="button" class="btn btn-outline-danger bQuitarProd"><i class="fas fa-trash"></i></button></td> 
            </tr>`);

            comun++;
            total();
            $("#modalProdComun").modal('hide');
        }
    });

    $('#formFinalizar').validate({
        rules: {
            metodoPago: {
                required: true
            },
            pagoCon: {
                required: true,
                min: 0.01
            }
        },
        messages: {
            metodoPago: {
                required: "El metodo de pago es requerido."
            },
            pagoCon: {
                required: "El pago es requerido.",
                min: "El valor debe ser mayor a 0."
            }
        },
        submitHandler: function(form) {
            if(parseFloat($("#totalCambio").text().replace('$', '').replace(searchRegExp, '')) >= 0){
                var detalles = [];
                $("#productosBody").children('tr.filaProd').each(function(index, el) {
                    detalles.push({
                        id: $(this).attr('attrID'),
                        nombre: $(this).parent().children('tr.filaNomProd[attrID='+$(this).attr('attrID')+']').children('th').children('span:eq(0)').text(),
                        precio: $(this).children('td:eq(0)').text().replace('$', '').replace(searchRegExp, ''),
                        cantidad: $(this).children('td:eq(1)').children('span.cantidad').text().replace(searchRegExp, ''),
                        total: $(this).children('td:eq(2)').text().replace('$', '').replace(searchRegExp, '')
                    });
                });

                var data = "metodo=insertar&accion=ventas&metodoPago="+$("#metodoPago").val()+"&pago="+$("#pagoCon").val()+"&detalles="+$.trim($("#detallesFinal").val())+"&total="+$("#totalVentaFinal").text().replace('$', '').replace(searchRegExp, '')+"&cambio="+$("#totalCambio").text().replace('$', '').replace(searchRegExp, '')+"&detallesVentas="+JSON.stringify(detalles);

                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: data,
                    beforeSend: function() {
                        $("#carga").show();
                    }
                })
                .done(function(res) {
                    var separa = $.trim(res).split('~');

                    if(separa[0] == "Correcto"){
                        Swal.fire({
                            icon: 'success',
                            title: 'La venta se ha completado correctamente'
                        });  

                        $("#productosBody").html("");
                        $("#totalVenta").html("$0");

                        if(imprimir == 1){
                            var altura = 50;
                            var anchura = 310;
                            var y = parseInt((window.screen.height/2)-(altura/2));
                            var x = parseInt((window.screen.width/2)-(anchura/2));
                            window.open("controladores/ticketVenta.php?id="+separa[1], '_blank', "width="+anchura+", height="+altura+", top="+y+", left="+x+"");
                        }
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error inesperado al guardar la compra.'
                        });  

                        console.log($.trim(res));
                    }
                })
                .fail(function() {
                    console.log("Error ajax");
                })
                .always(function() {
                    $("#carga").hide();
                    $("#modalFinalizar").modal("hide");
                });
            }else{
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'El pago debe ser mayor o igual al total.'
                });    
            }
        }
    });
}

function tablaVentasPendientes() {
  ajaxMyDatatable({
    "table": $("#tablaVentasPendientes"),
    "colums": [
      "Fecha",
      "Folio",
      "Tipo",
      "Total",
      "Pago",
      "Cambio",
      "Detalles",
      "Acciones"
    ],
    "sort": [
      0,
      "desc"
    ],
    "url": "index.php"
    ,
    "params": {
      metodo: 'detalles',
      accion: 'ventas',
      tipo: 'ventas',
    }
  });
}

/*const socket = io('http://localhost:3000', {
    reconnection: true,
    reconnectionDelayMax: 1000
});

socket.on("connect", () => {
    console.log("Connected as: "+socket.id); 
});*/

jQuery(document).ready(function($) {
    productos();
    formsVentas();
    crearDataTable();

    setInterval(function() {
        var data = "metodo=renovar";

        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: data
        })
        .done(function(res) {
            console.log("Sesion renovada");    
        })
        .fail(function(){
            console.log("error ajax");
        });
    }, 60000*10);

    $(document).on('click', '.producto', function() {
        var precio = $(this).children('b.precioProd').text().replace('$', '').replace(searchRegExp, '');

        if($("#productosBody").children('tr[attrID='+$(this).attr('attrID')+']').length > 0){
            var cantidad = parseFloat($("#productosBody").children('tr[attrID='+$(this).attr('attrID')+']').children('td:eq(1)').children('span').text().replace(searchRegExp, '')) + 1;
            
            $("#productosBody").children('tr[attrID='+$(this).attr('attrID')+']').children('td:eq(1)').children('span').html(cantidad);
            $("#productosBody").children('tr[attrID='+$(this).attr('attrID')+']').children('td:eq(2)').children('span').html(precio * cantidad);
        }else{
            $("#productosBody").append(`<tr class="filaNomProd" attrID="`+$(this).attr('attrID')+`">
                <th colspan="4" class="text-left h4"><span>`+$(this).children('p.tituloProd').children('span:eq(1)').text()+`</span> <span style="font-size: 11px;">`+$(this).children('p.tituloProd').children('span:eq(0)').html()+`</span></th>
            </tr>
            <tr class="filaProd" attrID="`+$(this).attr('attrID')+`">
                <td width="25%"><span class="dinero">`+precio+`</span></td>
                <td width="40%">
                    <button type="button" class="btn btn-outline-secondary bMenos"><i class="fas fa-minus"></i></button>
                    <span class="cantidad" style="padding: 8px 8px;">1</span>
                    <button type="button" class="btn btn-outline-secondary bMas"><i class="fas fa-plus"></i></button>
                </td>
                <td width="30%"><span class="dinero">`+precio+`</span></td>
                <td width="5%"><button type="button" class="btn btn-outline-danger bQuitarProd"><i class="fas fa-trash"></i></button></td> 
            </tr>`);
        }

        total();
    });

    $(document).on('click', '.bMenos', function() {
        padre = $(this).parent();
        var cantidad = parseFloat(padre.children('span').text().replace(searchRegExp, '')) - 1;

        if(cantidad >= 1){
            var precio = padre.parent().children('td:eq(0)').text().replace('$', '').replace(searchRegExp, '');

            padre.children('span').html(cantidad);
            padre.parent().children('td:eq(2)').children('span').text(cantidad * precio);
        }else{
            padre.parent().parent().children('tr[attrID='+padre.parent().attr('attrID')+']').remove();
        }

        total();
    });

    $(document).on('click', '.bMas', function() {
        padre = $(this).parent();
        var cantidad = parseFloat(padre.children('span').text().replace(searchRegExp, '')) + 1;

        var precio = padre.parent().children('td:eq(0)').text().replace('$', '').replace(searchRegExp, '');
        padre.children('span').html(cantidad);
        padre.parent().children('td:eq(2)').children('span').text(cantidad * precio);

        total();
    });

    $(document).on('click', '.bQuitarProd', function() {
        var padre = $(this).parent().parent();

        padre.parent().children('tr[attrID='+padre.attr('attrID')+']').remove();

        total();
    });

    $(document).on('click', '#bBorrarTodo', function() {
        $("#productosBody").html("");
        total();
    });

    $(document).on('click', '#bProdComun', function() {
        $("#totalComun").html("0");
        $("#nombreComun").val('Producto Comun');
        $("#cantidadComun").val('1');
        $("#precioComun").val('');

        $("#modalProdComun").modal('show');
        moneda();
    });

    $(document).on('click', '#bCobrar', function() {
        if(parseFloat($("#totalVenta").text().replace('$', '').replace(searchRegExp, '')) > 0){
            $("#totalVentaFinal").html($("#totalVenta").text());
            $("#pagoCon").val($("#totalVenta").text().replace('$', '').replace(searchRegExp, ''));
            $("#totalCambio").html('$0');
            //$("#detallesFinal").val("");

            $("#modalFinalizar").modal('show');
        }else{
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'El total debe ser mayor a 0.'
            });
        }
    });

    $(document).on('keyup change', '#pagoCon', function() {
        var total = parseFloat($("#totalVentaFinal").text().replace('$', '').replace(searchRegExp, ''));
        var pago = parseFloat($("#pagoCon").val()) || 0;
        
        $("#totalCambio").html(pago - total);
        moneda();
    });

    $(document).on('click', '#bFianlizar', function() {
        imprimir = 0;
    });

    $(document).on('click', '#bFianlizarImpri', function() {
        imprimir = 1;
    });

    $(document).on('keyup', '#buscarProductos', function() {
        productos();
    });

    $(document).on('click', '#bCerrarSe', function() {
        var data = "metodo=eliminar&accion=login";
        
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: data,
            /*beforeSend: function() {
                $("#carga").show();
            }*/
        })
        .done(function(res) {
            console.log($.trim(res));
            window.location.reload();
        })
        .fail(function() {
            console.log("Error ajax");
        }).always(function() {
            //$("#carga").hide();
        }); 
    });

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    $(document).on('click', '#bVerVentas', function() {
        $("#MVentasPendientes").modal('show');
    });

    $(document).on('click', '#bRecargarVentasPendientes', function(event) {
        tablaVentasPendientes();
    });

    $(document).on('shown.bs.modal', '#MVentasPendientes', function () {
        tablaVentasPendientes();
        $(this).find(".buscadorMyDataTable[tabla='tablaVentasPendientes']").focus();
    });

    $(document).on('click', '#tablaVentasPendientes tr', function () {
        $('#tablaVentasPendientes tr').removeClass('activa');
        $(this).addClass('activa');
    });

    $(document).on('dblclick', '#tablaVentasPendientes tr', function () {
        $('#totalCobrar').text(parseFloat($(this).children('td:eq(3)').text().replace('$', '').replace(searchRegExp, '')));
        $('#totalPagoCobrar').val(parseFloat($(this).children('td:eq(4)').text().replace('$', '').replace(searchRegExp, '')));
        $('#totalPagoCobrar').attr('min', parseFloat($(this).children('td:eq(3)').text().replace('$', '').replace(searchRegExp, '')));
        $('#totalCambio').text($(this).children('td:eq(5)').text());
        $('#detallesCobrar').val($.trim($(this).children('td:eq(6)').children('p:eq(0)').text()));

        moneda();
        $("#bCobrarImprimir").attr('attrID', $(this).attr('id'));
        $("#bCobrarImprimir").attr('tipo', 'pendiente');
        $('#MCobrar').modal('show');
    });

    $(document).on('click', '.bCancelarVentaPen', async function () {
        const idVenta = $(this).attr('attrID');

        Swal.fire({
            title: '¿Estás seguro de cancelar la venta?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: '¡No, cerrar!',
            confirmButtonText: '¡Si, cancelar!'
        }).then((result) => {
            if (result.value) {
                const data = `metodo=modificar&accion=ventas&tipo=cancelar&idVenta=${idVenta}&regresarInventario=1`
          
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: data,
                    beforeSend: function () {
                        $("#carga").show();
                    }
                }).done(function (res) {
                    if ($.trim(res) == 'Correcto') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Correcto',
                            text: 'Venta cancelada correctamente',
                        });
              
                        tablaVentasPendientes();
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error inesperado al cancelar la venta.',
                        });

                        console.log($.trim(res));
                    }
                }).fail(function () {
                    console.log("Error ajax");
                }).always(function () {
                    $("#carga").hide();
                });
            }
        });
    });

    $(document).on('click', '.bQuitarProductoMas', function() {
        var btn = $(this);
        Swal.fire({
            title: '¿Estás seguro de quitar el producto de la venta?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: '¡No, cancelar!',
            confirmButtonText: '¡Si, quitar!'
        }).then((result) => {
            if (result.value) {
                const data = "metodo=eliminar&accion=ventas&tipo=quitar&id="+btn.attr('attrID');
          
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: data,
                    beforeSend: function () {
                        $("#carga").show();
                    }
                }).done(function (res) {
                    if ($.trim(res) == 'Correcto') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Correcto',
                            text: 'El producto ha sido eliminado de la venta correctamente',
                        });
              
                        tablaVentasPendientes();
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error inesperado al quitar el producto.',
                        });

                        console.log($.trim(res));
                    }
                }).fail(function () {
                    console.log("Error ajax");
                }).always(function () {
                    $("#carga").hide();
                });
            }
        });
    });

    $(document).on('click', '.bAgregarProductoMas', function() {
        var btn = $(this);
        const data = "metodo=modificar&accion=ventas&tipo=agregar&id="+btn.attr('attrID')+"&codigo="+$.trim($("#codigoProdMas").val())+"&cantidad="+$("#cantidadProdMas").val()+"&precio="+$("#precioProdMas").val()+"&descuento="+$("#descuentoProdMas").val();
          
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: data,
            beforeSend: function () {
                $("#carga").show();
            }
        }).done(function (res) {
            if ($.trim(res) == 'Correcto') {
                Swal.fire({
                    icon: 'success',
                    title: 'Correcto',
                    text: 'El producto ha sido agregado correctamente',
                });
              
                tablaVentasPendientes();
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error inesperado al agregar el producto.',
                });

                console.log($.trim(res));
            }
        }).fail(function () {
            console.log("Error ajax");
        }).always(function () {
            $("#carga").hide();
        });
    });
});