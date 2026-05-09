function v_hacerCompra() {
	$("#codigoProductoC").focus();

	$('#formCobrarCompra').validate({
	    rules: {

	    },
	    messages: {

	    },
	    submitHandler: function(form) {
	    	if(parseFloat($("#restanteCobrar").text().replace('$', '').replace(searchRegExp, '')) < 0){
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'El importe pagado dede ser menor o igual al total'
				});	
			}else {
				var productos = [];
				$("#tbodyTablaProductosAgregados tr").each(function(index, el) {
					productos.push({
						id: $(this).attr('attrID'), 
						descripcion: $(this).children('td:eq(1)').text(), 
						costo: $(this).children('td:eq(2)').children('input').val(), 
						cantidad: $(this).children('td:eq(3)').children('input').val(), 
						subtotal: $(this).children('td:eq(4)').text().replace('$', '').replace(searchRegExp, '')
					});
				});

				var data = new FormData(document.getElementById('formCobrarCompra'));
				data.append('metodo', 'insertar');
				data.append('accion', 'hacerCompra');
				data.append('total', $("#totalCobrar").text().replace('$', '').replace(searchRegExp, ''));
				data.append('idProveedor', $("#bRealizarCompra").attr("idProveedor"));
				data.append('sucursal', $("#sucursalCompra").val());
				data.append('tipoCompra', $("#tipoCompra").val());
				data.append('fechaCredito', $('#fechaCredito').val());
				data.append('productos', JSON.stringify(productos));
				data.append('descuento', $("#descuentoCompraDinero").val());
				data.append('orden', $("#bGuardarOrden").attr('attrID'));

				$.ajax({
					url: 'index.php',
					type: 'POST',
					data: data,
					processData: false,
					contentType: false,
					beforeSend: function() {
			         	$('#carga').show();
			        }
				})
				.done(function(res) {
					var separa = res.split("~");

					if ($.trim(separa[0]) == "Correcto") {
						Swal.fire({
							icon: 'success',
							title: 'Compra guardada correctamente'
						});

						$("#modalCobrarCompra").modal("hide");
						$('#cargarHacerCompra').trigger('click');
						
						window.open("controladores/pdf/ticketCompra.php?id="+$.trim(separa[1]), '_blank');	
					}else{
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: 'Error inesperado al registrar la compra.'
						});

						console.log($.trim(res));
					}
				})
				.fail(function() {
					console.log("Error ajax");
				})
				.always(function () {
			     	$('#carga').hide();
			    });
			}			  
	    }
  	});
}

function tablaOrdenesCompra(){
	ajaxMyDatatable({
		"table": $("#tablaOrdenesCompra"), 
		"colums": [
			"Datos",
			"Proveedor",
			"Total",
			"Detalles",
			"Sucursal",
			"Acciones"
		], 
		"sort": [
			0,
			"desc"
		],
		"url": "index.php", 
		"params":{
			"metodo": "consultar",
			"tipo": "consultarOrdenes",
			"accion": "hacerCompra"
		}
	});
}

function tablaProductosCompra(){
	ajaxMyDatatable({
		"table": $("#tablaProductosCompra"), 
		"colums": [
			"Fecha",
			"Codigo",
			"Descripcion",
			"Costo"
		], 
		"sort": [
			0,
			"desc"
		],
		"url": "index.php", 
		"params":{
			"metodo": "consultar",
			"tipo": "consultarProductos",
			"accion": "hacerCompra"
		}
	});
}

function tablaProveedoresCompra(){
	ajaxMyDatatable({
		"table": $("#tablaProveedoresCompra"), 
		"colums": [
			"Fecha",
			"Nombre",
			"Domicilio",
			"Contacto",
			"Cuenta"
		], 
		"sort": [
			0,
			"desc"
		],
		"url": "index.php", 
		"params":{
			"metodo": "consultar",
			"tipo": "consultarProveedores",
			"accion": "hacerCompra"
		}
	});
}

jQuery(document).ready(function($) {

	$(document).on('click', '#cargarProveedoresModalC', function() {
		tablaProveedoresCompra();
	});

	$(document).on('click', '#tablaProveedoresCompra tbody tr', function() {
		$(".botonLimpiarProveedor").removeClass("oculto");
		var idProveedor = $(this).attr("id");
		var proveedor = $(this).children("td:eq(1)").html();

		$("#bRealizarCompra").attr("idProveedor", idProveedor);
		$("#cargarProveedoresModalC").html('Proveedor: '+proveedor);
		$("#modalVerProveedoresC").modal("hide");
		
		if($('#tipoCompra').val() == 'Crédito'){
			$("#tipoCompra").trigger('change');
		}
	});

	$(document).on('change', '#tipoCompra', function() {
		if ($(this).val() == "Crédito") {
			$("#fechaLimiteCredito").removeClass('oculto');
			$("#limiteCredito").removeClass('oculto');
			$("#limiteCreditoRestante").removeClass('oculto');
			
			var credito = parseFloat($("#cargarProveedoresModalC").children('b:eq(1)').text().replace('$', '').replace(searchRegExp, '')) ? parseFloat($("#cargarProveedoresModalC").children('b:eq(1)').text().replace('$', '').replace(searchRegExp, '')) : 0;
			var adeudo = parseFloat($("#cargarProveedoresModalC").children('b:eq(2)').text().replace('$', '').replace(searchRegExp, '')) ? parseFloat($("#cargarProveedoresModalC").children('b:eq(2)').text().replace('$', '').replace(searchRegExp, '')) : 0;
			var total = parseFloat($('#totalCompra').text().replace('$', '').replace(searchRegExp, ''));
			
			$("#mostrarCreditoProveedor").text(credito);
			$("#mostrarCreditoRestante").text(credito - adeudo - total);
		}else{
			$("#fechaLimiteCredito").addClass('oculto');
			$("#limiteCredito").addClass('oculto');
			$("#limiteCreditoRestante").addClass('oculto');
		}

		moneda();
	});

	$(document).on('click', '#bLimpiarProveedorSeleccionado', function() {
		$("#mostrarCreditoProveedor").html('$0');
		$("#mostrarCreditoRestante").text('$0');
		
		$(".botonLimpiarProveedor").addClass("oculto");
		$("#cargarProveedoresModalC").html('<i class="fas fa-user"></i> Proveedor');
	});

	$(document).on('click', '#bVerOrdenes', function() {
    	tablaOrdenesCompra();
    	$("#modalVerOrdenes").modal('show');
    });

	$(document).on('click', '#cargarProductosModalC', function() {
		tablaProductosCompra();
	});

	$(document).on('submit', '#formAgregarProductoC', function(event) {
        event.preventDefault();
		$('#AgregarProductoCodigoC').trigger('click');
    });
    
	$(document).on('click', '#AgregarProductoCodigoC', function() {
		var data = "metodo=consultar&accion=hacerCompra&tipo=consultarProductoCodigo&codigo="+$("#codigoProductoC").val();

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
			var datos = JSON.parse(res);
			//console.log(datos);

			if(datos == null){
				Swal.fire({
				  	icon: 'error',
				  	title: 'Producto no encontrado',
				  	timer: 1200
				});
			}else{
				var fila = $("#tbodyTablaProductosAgregados tr[attrID="+datos.ID_Producto+"]");
				
				if (fila.length > 0) {
					var costo = parseFloat(fila.children("td:eq(2)").children('input').val()) ? parseFloat(fila.children("td:eq(2)").children('input').val()) : 0;
					var cantidad = parseFloat(fila.children("td:eq(3)").children('input').val()) ? parseFloat(fila.children("td:eq(3)").children('input').val()) : 0;

					fila.children("td:eq(3)").children('input').val(cantidad + 1);
					fila.children("td:eq(4)").children('span').text(costo * (cantidad + 1));
				}else{
					$("#tbodyTablaProductosAgregados").append(`<tr attrID="`+datos.ID_Producto+`">
						<td>`+datos.Codigo+`</td>
						<td>`+datos.Descripcion+`</td>
						<td><input type="number" value="`+datos.Costo+`" min="0" step="any" class="form-control campoCostoCom"></td>
						<td><input type="number" value="1" min="0" step="any" class="form-control campoCantidadCom"></td>
						<td><span class="dinero">`+datos.Costo+`</span></td>
						<td><button class="btn btn-danger btn-sm bEliminarFilaCom"><i class="fas fa-trash"></i></button></td>
					</tr>`);
				}
				
				$("#codigoProductoC").val("");
				$("#codigoProductoC").focus();
				
				calcularTotalCom();
			}	
		})
		.fail(function() {
			console.log("Error ajax");
		})
		.always(function() {
			$("#carga").hide();
		});
    });

    $(document).on('click', '.bEliminarFilaCom', function() {
		$(this).parent().parent().remove();
		calcularTotalCom();
	});

	$(document).on('keyup change', '.campoCostoCom', function() {
		var padre = $(this).parent().parent();
    	var costo = parseFloat($(this).val()) ? parseFloat($(this).val()) : 0;
		var cantidad = parseFloat(padre.children("td:eq(3)").children('input').val()) ? parseFloat(padre.children("td:eq(3)").children('input').val()) : 0;

					
		padre.children("td:eq(4)").children('span').text(costo * cantidad);
		calcularTotalCom();
    });

	$(document).on('keyup change', '.campoCantidadCom', function() {
    	var padre = $(this).parent().parent();
    	var cantidad = parseFloat($(this).val()) ? parseFloat($(this).val()) : 0;
		var costo = parseFloat(padre.children("td:eq(2)").children('input').val()) ? parseFloat(padre.children("td:eq(2)").children('input').val()) : 0;

					
		padre.children("td:eq(4)").children('span').text(costo * cantidad);
		calcularTotalCom();
    });


	function calcularTotalCom() {
		$("#cantidadProductosSpan").text($("#tbodyTablaProductosAgregados tr").length);
		
		var total = 0;
		$("#tbodyTablaProductosAgregados tr").each(function(){
			var totalFilas = parseFloat($(this).children("td:eq(4)").text().replace('$', '').replace(searchRegExp, ''));
			total += totalFilas;
		});

		$("#mostrarSubtotal").text(total);

		var descuento = parseFloat($("#descuentoCompraDinero").val()) ? parseFloat($("#descuentoCompraDinero").val()) : 0;
		
		$("#totalCompra").text(total - descuento);
		$("#tipoCompra").trigger('change');

	    //moneda();
	}

	$(document).on('change keyup', '#descuentoCompraDinero', function() {
		var descuento = parseFloat($(this).val()) ? parseFloat($(this).val()) : 0;
		var subtotal = $("#mostrarSubtotal").text().replace('$', '').replace(searchRegExp, '');

		$("#totalCompra").text(subtotal - descuento);
		moneda();
	});


	$(document).on('click', '#tablaProductosCompra tr', function(event) {
		var id = $(this).attr('id');
		var codigo = $(this).children("td:eq(1)").text();
		var descripcion = $(this).children("td:eq(2)").text();
		var costo = $(this).children("td:eq(3)").text().replace('$', '').replace(searchRegExp, '');

		var fila = $("#tbodyTablaProductosAgregados tr[attrID="+id+"]");
				
		if (fila.length > 0) {
			var costo = parseFloat(fila.children("td:eq(2)").children('input').val()) ? parseFloat(fila.children("td:eq(2)").children('input').val()) : 0;
			var cantidad = parseFloat(fila.children("td:eq(3)").children('input').val()) ? parseFloat(fila.children("td:eq(3)").children('input').val()) : 0;

			fila.children("td:eq(3)").children('input').val(cantidad + 1);
			fila.children("td:eq(4)").children('span').text(costo * (cantidad + 1));
		}else{
			$("#tbodyTablaProductosAgregados").append(`<tr attrID="`+id+`">
				<td>`+codigo+`</td>
				<td>`+descripcion+`</td>
				<td><input type="number" value="`+costo+`" min="0" step="any" class="form-control campoCostoCom"></td>
				<td><input type="number" value="1" min="0" step="any" class="form-control campoCantidadCom"></td>
				<td><span class="dinero">`+costo+`</span></td>
				<td><button class="btn btn-danger btn-sm bEliminarFilaCom"><i class="fas fa-trash"></i></button></td>
			</tr>`);
		}
				
		$("#modalVerProductosCompra").modal('hide');
		
		setTimeout(function() {
			$("#codigoProductoC").focus();
		}, 500);
				
		calcularTotalCom();
	});

	$(document).on('click', '#bRealizarCompra', function() {
		var fecha = new Date();

		if ($(this).attr("idProveedor") == "") {
			Swal.fire({
	            icon: 'warning',
	            title: 'Oops...',
	        	text: 'Seleccione un proveedor para la compra'
	        });
		}else if($("#tbodyTablaProductosAgregados tr").length <= 0){
			Swal.fire({
	            icon: 'warning',
	            title: 'Oops...',
	        	text: 'Tienes que ingresar al menos un producto para realizar la compra'
	        });
		}else if(parseFloat($("#totalCompra").text().replace('$', '').replace(searchRegExp, '')) <= 0){
			Swal.fire({
	            icon: 'warning',
	            title: 'Oops...',
	        	text: 'No se puede realizar una venta con un total de $0.00'
	        });
		}else if($("#tipoCompra").val() == "Crédito" && ($('#fechaCredito').val() == "" || new Date($('#fechaCredito').val()) < fecha)){
			Swal.fire({
	            icon: 'warning',
	            title: 'Oops...',
	        	text: 'Debes elegir una fecha límite de pago posterior al día de hoy.'
	        });
		}else{
			$("#formCobrarCompra")[0].reset();
			$("#importePagadoCobrar").attr('readonly', false);
			var total = parseFloat($('#totalCompra').text().replace('$', '').replace(searchRegExp, ''));
			$("#totalCobrar").text(total);
			$("#restanteCobrar").text(0);

			if($('#tipoCompra').val() == 'Contado'){
				$("#restanteCobrar").text(total);
				$("#importePagadoCobrar").val(total);
				$("#importePagadoCobrar").attr('readonly', true);
				$("#conceptoPagoCobrar").val('Pago Contado');
				$("#modalCobrarCompra").modal('show');
			}

			moneda();
			$("#modalCobrarCompra").modal('show');
		}
	});

	$(document).on('change keyup', '#importePagadoCobrar', function() {
		var importe = parseFloat($(this).val()) ? parseFloat($(this).val()) : 0;
		var total = parseFloat($("#totalCobrar").text().replace('$', '').replace(searchRegExp, ''));

		$("#restanteCobrar").text(total - importe);
	});

	$(document).on('click', '#bGuardarOrden', function() {
    	if($("#tbodyTablaProductosAgregados tr").length <= 0){
			Swal.fire({
	            icon: 'error',
	            title: 'Oops...',
	        	text: 'Tienes que ingresar al menos un producto para realizar la compra'
	        });
		}else{
			var tipo = 'insertarOrden';
			if($.trim($(this).attr('attrID')) != ''){
				tipo = 'modificarOrden';
			}

			var productos = [];
			$("#tbodyTablaProductosAgregados tr").each(function(index, el) {
				productos.push({
					id: $(this).attr('attrID'), 
					descripcion: $(this).children('td:eq(1)').text(), 
					costo: $(this).children('td:eq(2)').children('input').val(), 
					cantidad: $(this).children('td:eq(3)').children('input').val(), 
					subtotal: $(this).children('td:eq(4)').text().replace('$', '').replace(searchRegExp, '')
				});
			});

			var data = "metodo=modificar&accion=hacerCompra&tipo="+tipo+"&total="+$("#totalCompra").text().replace('$', '').replace(searchRegExp, '')+"&idProveedor="+$("#bRealizarCompra").attr("idProveedor")+"&productos="+JSON.stringify(productos)+"&descuento="+$("#descuentoCompraDinero").val()+"&sucursal="+$("#sucursalCompra").val()+"&id="+$.trim($(this).attr('attrID'));

			$.ajax({
				url: 'index.php',
				type: 'POST',
				data: data,
				beforeSend: function() {
			        $('#carga').show();
			    }
			})
			.done(function(res) {
				var separa = res.split("~");

				if ($.trim(separa[0]) == "Correcto") {
					Swal.fire({
						icon: 'success',
						title: 'Orden de compra realizada correctamente'
					});

					$('#cargarHacerCompra').trigger('click');
					
					window.open("controladores/pdf/ticketOrden.php?id="+$.trim(separa[1]), '_blank');
					$("#bGuardarOrden").attr('attrID', '');	
				}else{
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Error inesperado al registrar la orden.'
					});

					console.log($.trim(res));
				}
			})
			.fail(function() {
				console.log("Error ajax");
			})
			.always(function() {
				$("#carga").hide();
			});	
		}
    });

    $(document).on('click', '.bVerProductosOrden', function() {
    	var btn = $(this);
    	var data = "metodo=detalles&accion=hacerCompra&tipo=consultarProductosOrden&id="+btn.attr('attrID');

		$.ajax({
			url: 'index.php',
			type: 'POST',
			data: data,
			beforeSend: function() {
				$("#carga").show();
			}
		})
		.done(function(res) {
			//console.log($.trim(res));
			$("#verProdOrden").html($.trim(res));
			moneda();
			$("#modalVerProductosOrden").modal('show');
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			$("#carga").hide();
		});
    });

    $(document).on('click', '.bImprimirTicketOrden', function() {
    	var idCompra = $(this).attr("attrID");
	
		window.open("controladores/pdf/ticketOrden.php?id="+idCompra, '_blank');
    });

    $(document).on('click', '.bEliminarOrden', function() {
		var btn = $(this);
		Swal.fire({
	        title: '¿Estás seguro que quieres eliminar la orden de compra?',
	        icon: 'warning',
	        showCancelButton: true,
	        confirmButtonColor: '#3085d6',
	        cancelButtonColor: '#d33',
	        cancelButtonText: '¡No, cancelar!',
	        confirmButtonText: '¡Si, eliminar!'
	    }).then((result) => {
	        if (result.value) {
	        	var data = "metodo=eliminar&accion=hacerCompra&tipo=ordenCompra&id="+btn.attr('attrid');
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
						Swal.fire({
							icon: 'success',
							title: 'Orden de compra eliminada correctamente'
						});

						tablaOrdenesCompra();
					}else{
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: 'Error inesperado al eliminar la orden de compra.'
						});
						console.log($.trim(res));
					}
				})
				.fail(function() {
					console.log("Error ajax");
				})
				.always(function() {
					$("#carga").hide();
				});
			}    
		});	  
	});

    $(document).on('hidden.bs.modal', '#modalVerProveedoresC',function(){
		$("#codigoProductoC").focus();
    });

	$(document).on('click', '#bFolioOrdenCompra', function() {
		$(this).addClass('oculto');
		$('#cargarHacerCompra').trigger('click');
	});

	$(document).on('click', '.bCargarOrden', function() {
		var btn = $(this);
    	var data = "metodo=detalles&accion=hacerCompra&tipo=consultarOrden&id="+btn.attr('attrID');

		$.ajax({
			url: 'index.php',
			type: 'POST',
			data: data,
			beforeSend: function() {
				$("#carga").show();
			}
		})
		.done(function(res) {
			//console.log($.trim(res));
			var datos = JSON.parse($.trim(res));

			if(datos != null){
				console.log(datos);
				
				$("#sucursalCompra").val("");
				if(datos.FK_Sucursal != 0){
					$("#sucursalCompra").val(datos.FK_Sucursal);
				}
				
				$("#bFolioOrdenCompra").removeClass('oculto');
				$("#folioOrdenCompra").html(datos.ID_Orden_Compra.padStart(8, '0'));

				if(datos.FK_Proveedor != '1'){
					$("#bRealizarCompra").attr("idProveedor", datos.FK_Proveedor);
					$(".botonLimpiarProveedor").removeClass("oculto");

					$("#cargarProveedoresModalC").html(`Proveedor: <span>`+datos.Razon_Social+`</span>
						<br>RFC: <b>`+datos.RFC+`</b>
						<br>Crédito: <b>`+datos.Credito+`</b>
						<br>Adeudo: <b>`+datos.Adeudo+`</b>
						<br>Restante: <b class="dinero">`+datos.Restante+`</b>`);
				}

				var fila = '';
				datos.Productos.forEach(producto => {
					fila += `<tr attrID="`+producto.FK_Producto+`">
						<td>`+producto.Codigo+`</td>
						<td>`+producto.Descripcion+`</td>
						<td><input type="number" value="`+producto.Costo+`" min="0.1" step="any" class="form-control campoCostoCom"></td>
						<td><input type="number" value="`+producto.Cantidad+`" min="0.1" step="any" class="form-control campoCantidadCom"></td>
						<td><span class="dinero">`+producto.Subtotal+`</span></td>
						<td><button class='btn btn-danger btn-sm bEliminarFilaCom'><i class='fas fa-trash'></i></button></td>
					</tr>`;	
				});

				$("#tbodyTablaProductosAgregados").html(fila);
				$("#descuentoCompraDinero").val(datos.Descuento);
				calcularTotalCom();
				$("#bGuardarOrden").attr('attrID', datos.ID_Orden_Compra);
				$("#modalVerOrdenes").modal('hide');
			}
		})
		.fail(function() {
			console.log("Error ajax");
		})
		.always(function() {
			$("#carga").hide();
		});
	});
});