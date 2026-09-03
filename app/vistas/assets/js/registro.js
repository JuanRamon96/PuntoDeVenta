jQuery(document).ready(function ($) {
	// validate registry form

	$('#formRegistro').validate({
		rules: {
			nombre: {
				required: true
			},
			primerApellido: {
				required: true
			},
			email: {
				required: true,
				email: true
			},
			pass: {
				required: true
			},
			repitepass: {
				required: true,
				equalTo: "#pass"
			}
		},
		messages: {
			nombre: {
				required: "El nombre es requerido."
			},
			primerApellido: {
				required: "El primer apellido es requerido."
			},
			email: {
				required: "El correo electrónico es requerido.",
				email: "Introduce un correo electrónico válido."
			},
			pass: {
				required: "La contraseña es requerida."
			},
			repitepass: {
				required: "La contraseña es requerida.",
				equalTo: "Las contraseñas no coinciden."
			}
		},
		errorClass: 'is-invalid',
		errorElement: 'div',
		submitHandler: function (form) {
			var formData = new FormData(document.getElementById("formRegistro"));
			var response = grecaptcha.getResponse();

			/*if(response.length == 0){
				Swal.fire({
					icon: 'warning',
					title: 'Oops...',
					text: 'Por favor completa el captcha correctamente.'
				});
			}else{*/
			$.ajax({
				url: 'index.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					$("#mensaAV").html(`<div class="alert alert-primary alert-dismissible fade show" role="alert">
							<i class="fas fa-info-circle"></i> <strong>Cargando . . .</strong>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>`);
					$("#mensaAV").show();
					$("#bRegistro").prop('disabled', true);
					$("#bRegistro").html('Registrarme <div class="spinner-border text-light" role="status"></div>');
				}
			})
				.done(function (res) {
					if ($.trim(res) == "Correcto") {
						setTimeout(function () {
							$("#mensaAV").html(`<div class="alert alert-success alert-dismissible fade show" role="alert">
								<i class="fas fa-check-circle"></i> <strong>Cuenta creada correctamente.</strong> Ya puedes ingresar.
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>`);

							window.scrollTo({ top: 0, behavior: 'smooth' });

							setTimeout(function () {
								window.location.href = './';
							}, 1500);
						}, 1500);
					} else {
						var separa = $.trim(res).split(' ');
						if (separa[2] == 'Duplicate') {
							setTimeout(function () {
								$("#mensaAV").html(`<div class="alert alert-warning alert-dismissible fade show" role="alert">
									<i class="fas fa-exclamation-triangle"></i> <strong>El correo ya ha sido registrado.</strong>
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>`);

								window.scrollTo({ top: 0, behavior: 'smooth' });
							}, 1500);
						} else {
							setTimeout(function () {
								$("#mensaAV").html(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
									<i class="fas fa-exclamation-triangle"></i> <strong>Error Inesperado.</strong>
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>`);

								window.scrollTo({ top: 0, behavior: 'smooth' });
							}, 1500);

							console.log($.trim(res));
						}
					}
				})
				.fail(function () {
					console.log("Error ajax");
				})
				.always(function () {
					setTimeout(function () {
						$("#bRegistro").prop('disabled', false);
						$("#bRegistro").html('Registrarme <i class="fas fa-check"></i>');
					}, 2000);
				});
			//} 
		}
	});
});