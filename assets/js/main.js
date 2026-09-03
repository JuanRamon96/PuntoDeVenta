jQuery(document).ready(function () {
    // reveal on scroll
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
    }, { threshold: .12 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));

    // faq: solo un detalle abierto a la vez
    const faqs = document.querySelectorAll('.faq-item');
    faqs.forEach(item => {
        item.addEventListener('toggle', () => {
            if (item.open) {
                faqs.forEach(other => { if (other !== item) other.open = false; });
            }
        });
    });

    // toggle mensual / anual
    const sw = document.getElementById('switchPlan');
    const labelM = document.getElementById('labelMensual');
    const labelA = document.getElementById('labelAnual');
    const num = document.getElementById('precioNum');
    const periodo = document.getElementById('precioPeriodo');
    const nota = document.getElementById('notaAnual');

    function setPlan(anual) {
        sw.setAttribute('aria-checked', anual);
        labelM.classList.toggle('activo', !anual);
        labelA.classList.toggle('activo', anual);
        if (anual) {
            num.textContent = '79';
            periodo.textContent = 'MXN / mes, cobrado anualmente';
            nota.textContent = '$948 MXN al año · ahorras $240 vs. mensual';
        } else {
            num.textContent = '99';
            periodo.textContent = 'MXN / mes, cobrado mensualmente';
            nota.innerHTML = '&nbsp;';
        }
    }
    sw.addEventListener('click', () => setPlan(sw.getAttribute('aria-checked') !== 'true'));

    // mostrar campo "otro" cuando el tipo de negocio es "otro"
    const selectNegocio = document.getElementById('regNegocio');
    const grupoOtro = document.getElementById('grupoOtroNegocio');
    selectNegocio.addEventListener('change', () => {
        grupoOtro.style.display = selectNegocio.value === 'otro' ? 'block' : 'none';
    });

    // validación del formulario de registro
    const formRegistro = document.getElementById('formRegistro');
    const pass1 = document.getElementById('regPassword');
    const pass2 = document.getElementById('regPassword2');

    let entro = false;
    formRegistro.addEventListener('submit', (e) => {
        e.preventDefault();

        if (entro === true) {
            return;
        }
        entro = true;

        const coinciden = pass1.value === pass2.value && pass1.value.length >= 8;
        pass2.setCustomValidity(coinciden ? '' : 'no-coinciden');

        if (!formRegistro.checkValidity() || !coinciden) {
            e.stopPropagation();
            formRegistro.classList.add('was-validated');
            if (!coinciden) {
                pass2.classList.add('is-invalid');
            }

            entro = false;

            return;
        } else {
            pass2.classList.remove('is-invalid');
        }

        var formData = new FormData(document.getElementById("formRegistro"));
        var response = grecaptcha.getResponse();

        /*if (response.length == 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Por favor completa el captcha correctamente.'
            });

            entro = false;
        } else {*/
            $.ajax({
                url: 'app/index.php',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#bBotonRegistar").prop('disabled', true);
                    $("#bBotonRegistar").html('Por favor espera, esto puede tardar algunos minutos <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>');
                }
            })
                .done(function (res) {
                    //console.log(res.trim());
                    if (res.trim() == "Correcto") {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Felicidades! has sido registrado correctamente.',
                            text: 'Ahora ya puedes acceder a tu cuenta, espera unos segundos y serás redireccionado al login...',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            didClose: () => {
                                window.location.href = "./app/";
                            }
                        });

                        $("#formRegistro")[0].reset();
                    } else if (res.trim() == "Error 1 Nombres") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Por favor llena el campo de “Nombre completo”.'
                        }).then((result) => {
                            setTimeout(function () {
                                $("#regNombre").focus();
                            }, 300);
                        });

                        grecaptcha.reset();
                    } else if (res.trim() == "Error 2 Tipo") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Por favor llena el campo de “Tipo de negocio”.'
                        }).then((result) => {
                            setTimeout(function () {
                                $("#regNegocio").focus();
                            }, 300);
                        });

                        grecaptcha.reset();
                    } else if (res.trim() == "Error 3 Correo") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Por favor llena el campo de “Correo”.'
                        }).then((result) => {
                            setTimeout(function () {
                                $("#regCorreo").focus();
                            }, 300);
                        });

                        grecaptcha.reset();
                    } else if (res.trim() == "Error 4 Contrasena") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Por favor llena el campo de “Contraseña”.'
                        }).then((result) => {
                            setTimeout(function () {
                                $("#regPassword").focus();
                            }, 300);
                        });

                        grecaptcha.reset();
                    } else if (res.trim() == "Error 5 Captcha" || res.trim() == "Error 7 Captcha") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Por favor completa el captcha correctamente.'
                        });

                        grecaptcha.reset();
                    } else {
                        var separa = res.trim().split(' ');
                        if (separa[2] == 'Duplicate') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Oops...',
                                text: 'El correo electrónico ya ha sido registrado anteriormente, por favor utiliza otro correo.'
                            }).then((result) => {
                                setTimeout(function () {
                                    $("#regCorreo").focus();
                                }, 300);
                            });

                            grecaptcha.reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Error inesperado al completar el registro.',
                                footer: '¿Por qué tengo este error? Contáctanos en ventastool@bigtool.mx'
                            });

                            grecaptcha.reset();
                            console.log(res.trim());
                        }
                    }
                })
                .fail(function () {
                    console.log("Error ajax");
                })
                .always(function () {
                    $("#bBotonRegistar").prop('disabled', false);
                    $("#bBotonRegistar").html('Crear cuenta gratis <i class="fas fa-save"></i>');

                    entro = false;
                });
        //}
    });

    document.querySelectorAll('[data-bs-legal-tab]').forEach(link => {
        link.addEventListener('click', () => {
            const tab = link.getAttribute('data-bs-legal-tab');
            const boton = document.getElementById('tab-' + tab + '-btn');
            if (boton) {
                const tabInstancia = new bootstrap.Tab(boton);
                tabInstancia.show();
            }
        });
    });
});