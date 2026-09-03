let importePagoCompra = null;

function v_compras() {
	tablaReporteCompras();

	importePagoCompra = IMask(
		document.getElementById('importePagoCompra'),
		{
			mask: Number,
			scale: 2,             // Decimales permitidos
			signed: false,        // Desactiva el signo negativo (-)
			thousandsSeparator: ',',
			padFractionalZeros: false,
			normalizeZeros: true,
			radix: '.',
			min: 0
		}
	);
}

function tablaReporteCompras() {
	ajaxMyDatatable({
		"table": $("#tablaCompras"),
		"colums": [
			"Fecha",
			"Datos",
			"Proveedor",
			"Total",
			"Detalles",
			//"Sucursal",
			"Acciones"
		],
		"totals": {
			3: "Total"
		},
		"sort": [0, "desc"],
		"url": "index.php",
		"params": {
			"metodo": "consultar",
			"accion": "compras"
		}
	});
}

function tablaVerHistorialPagos(id) {
	ajaxMyDatatable({
		"table": $("#tablaVerHistorialPagos"),
		"colums": [
			"Fecha",
			"Concepto",
			"TipoPago",
			"Monto",
			"Detalles",
			"Comprobante",
			"Accion"
		],
		"sort": [0, "desc"],
		"url": "index.php",
		"params": {
			"metodo": "detalles",
			"accion": "compras",
			"tipo": "historialPagos",
			"id": id
		}
	});
}

// Función para eliminar compra
function eliminarCompra(id) {
	var dataEliminar = "metodo=eliminar&accion=compras&id=" + id;

	$.ajax({
		url: 'index.php',
		type: 'POST',
		data: dataEliminar,
		beforeSend: function () {
			$("#carga").show();
		}
	})
		.done(function (res) {
			if ($.trim(res) == "Correcto") {
				Swal.fire({
					icon: 'success',
					title: 'Compra eliminada correctamente'
				});

				tablaReporteCompras();
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Error al eliminar la compra'
				});

				console.log($.trim(res));
			}
		})
		.fail(function () {
			console.log("Error AJAX en eliminación de compra");
		})
		.always(function () {
			$("#carga").hide();
		});
}

jQuery(document).ready(function ($) {

	$(document).on('click', '.bEliminarCompra', function () {
		var btn = $(this);
		var compraID = btn.attr('attrID');
		var folio = btn.attr('folio');

		// Preguntar primero si quiere restar inventario
		Swal.fire({
			title: '¿Quieres restar los productos del inventario antes de eliminar la compra con el folio ' + folio + '?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: 'No, eliminar sin restar',
			confirmButtonText: 'Sí, restar primero'
		}).then((result) => {
			if (result.value) {
				// 1️⃣ Restar inventario
				var dataResta = "metodo=detalles&accion=compras&tipo=inventario&id=" + compraID;

				$.ajax({
					url: 'index.php',
					type: 'POST',
					data: dataResta,
					beforeSend: function () {
						$("#carga").show();
					}
				})
					.done(function (res) {
						if ($.trim(res) == "Correcto") {
							Swal.fire({
								icon: 'success',
								title: 'Los productos han sido restados del inventario'
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Oops...',
								text: 'Error al restar productos del inventario'
							});
							console.log($.trim(res));
						}
					})
					.fail(function () {
						console.log("Error AJAX en resta de inventario");
					})
					.always(function () {
						$("#carga").hide();
						eliminarCompra(compraID);
					});

			} else {
				eliminarCompra(compraID);
			}
		});
	});

	$(document).on('click', '.bVerProductosCompra', function () {
		var folio = $(this).attr("folio");
		var id = $(this).attr("attrID");
		$("#modalVerProductosCompra").modal("show");
		$("#folioCompraProductos").text(folio);

		var data = "metodo=detalles&accion=compras&tipo=productos&id=" + id;

		$.ajax({
			url: 'index.php',
			type: 'POST',
			data: data,
			beforeSend: function () {
				$("#carga").show();
			}
		})
			.done(function (res) {
				$("#tbodyVerProductosCompra").html(res);

				moneda();
			})
			.fail(function () {
				console.log("Error ajax");
			})
			.always(function () {
				$("#carga").hide();
			});
	});

	$(document).on('click', '.bVerHistorialPagos', function () {
		var id = $(this).attr("attrID");
		var folio = $(this).attr("folio");
		$("#folioCompraPagos").text(folio);

		tablaVerHistorialPagos(id);
		$("#bRecargarPagosCompra").attr("attrID", id);
		$("#modalVerHistorialPagos").modal("show");
	});

	$(document).on('click', '.bImprimirTicketCompra', function () {
		var idCompra = $(this).attr("attrID");
		window.open("controladores/pdf/ticketCompra.php?id=" + idCompra, '_blank');
	});

	$(document).on('click', '.bEliminarPago', function () {
		var btn = $(this);
		Swal.fire({
			title: '¿Estás seguro que quieres eliminar el pago?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: '¡No, cancelar!',
			confirmButtonText: '¡Si, eliminar!'
		}).then((result) => {
			if (result.value) {
				var data = "metodo=detalles&accion=compras&tipo=eliminarPago&id=" + btn.attr('attrID') + "&archivo=" + btn.attr('archivo');

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
								title: 'Pago eliminado correctamente'
							});

							tablaVerHistorialPagos($("#bRecargarPagosCompra").attr("attrID"));
							tablaReporteCompras();
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Oops...',
								text: 'Error inesperado al eliminar el pago.'
							});

							console.log($.trim(res));
						}
					})
					.fail(function () {
						console.log("Error ajax");
					})
					.always(function () {
						$("#carga").hide();
					});
			}
		});
	});

	$(document).on('click', '.bCancelarCompra', function () {
		var btn = $(this);
		Swal.fire({
			title: '¿Estás seguro que quieres cancelar la compra?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			cancelButtonText: '¡No, cerrar!',
			confirmButtonText: '¡Si, cancelar!'
		}).then((result) => {
			if (result.value) {
				var data = "metodo=modificar&accion=compras&id=" + btn.attr('attrID');

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
								title: 'Compra cancelada correctamente'
							});

							tablaReporteCompras();

							Swal.fire({
								title: '¿Quieres restar los productos del inventario?',
								icon: 'warning',
								html: '',
								showCancelButton: true,
								confirmButtonColor: '#3085d6',
								cancelButtonColor: '#d33',
								cancelButtonText: '¡No, cancelar!',
								confirmButtonText: '¡Si, restar!'
							}).then((result) => {
								if (result.value) {
									var data = "metodo=detalles&accion=compras&tipo=inventario&id=" + btn.attr('attrID');

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
													title: 'Los productos han sido restados del inventario'
												});
											} else {
												Swal.fire({
													icon: 'error',
													title: 'Oops...',
													text: 'Error inesperado al realizar la resta.'
												});

												console.log($.trim(res));
											}
										})
										.fail(function () {
											console.log("Error ajax");
										})
										.always(function () {
											$("#carga").hide();
										});
								}
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Oops...',
								text: 'Error inesperado al cancelar la compra.'
							});

							console.log($.trim(res));
						}
					})
					.fail(function () {
						console.log("Error ajax");
					})
					.always(function () {
						$("#carga").hide();
					});
			}
		});
	});

	$(document).on('click', '.bPagoCom', function () {
		document.getElementById('formPagoCompra').reset();
		var id = $(this).attr("attrID");
		var data = "metodo=detalles&accion=compras&tipo=pago&id=" + id;

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
				var datos = JSON.parse($.trim(res));

				var restante = (parseFloat(datos.Total) - parseFloat(datos.TotalPagos));
				$('#totalCompra').text(new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(datos.Total));
				$('#pagos').text(new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(datos.TotalPagos));
				$('#restante').text(new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(restante));
				$('#modalPagoCompra').modal('show');
				$('#bGuardarPago').attr('attrID', id);
			})
			.fail(function () {
				console.log("Error ajax");
			})
			.always(function () {
				$("#carga").hide();
			});
	});

	$(document).on('click', '#bRecargarPagosCompra', function () {
		tablaVerHistorialPagos($(this).attr("attrID"));
	});

	$(document).on('click', '#bGuardarPago', function () {
		var id = $(this).attr("attrID");

		$('#formPagoCompra').validate({
			rules: {
				importePagoCompra: {
					required: true
				},
				conceptoPago: {
					required: true
				},
				tipoDePago: {
					required: true
				}
			},
			messages: {
				importePagoCompra: {
					required: "El importe es obligatorio."
				},
				conceptoPago: {
					required: "El concepto es obligatorio."
				},
				tipoDePago: {
					required: "El tipo de pago es obligatorio."
				}
			},
			errorClass: 'is-invalid',
			errorElement: 'div',
			submitHandler: function (form) {
				if ($('#importePagoCompra').val() == '' || parseFloat($('#importePagoCompra').val().replaceAll(',', '')) == 0) {
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'El importe debe ser mayor a $0'
					});
				} else {
					var data = new FormData(document.getElementById('formPagoCompra'));
					data.append('metodo', 'insertar');
					data.append('accion', 'compras');
					data.append('id', id);

					data.set('importePagoCompra', importePagoCompra.unmaskedValue);

					$.ajax({
						url: 'index.php',
						type: 'POST',
						data: data,
						processData: false,
						contentType: false,
						beforeSend: function () {
							$("#carga").show();
						}
					})
						.done(function (res) {
							if ($.trim(res) == "Correcto") {
								Swal.fire({
									icon: 'success',
									title: 'Pago registrado correctamente'
								});

								tablaReporteCompras();
								$("#modalPagoCompra").modal("hide");
							} else {
								Swal.fire({
									icon: 'error',
									title: 'Oops...',
									text: 'Error inesperado al registrar la compra.'
								});

								console.log($.trim(res));
							}
						})
						.fail(function () {
							console.log("Error ajax");
						})
						.always(function () {
							$("#carga").hide();
						});
				}
			}
		});
	});

	$(document).on('change', '#tipoDePago', function () {
		if ($(this).val() == 'Efectivo') {
			$("#cajaPago").parent().parent().removeClass('oculto');
		} else {
			$("#cajaPago").val("");
			$("#cajaPago").parent().parent().addClass('oculto');
		}
	});
});