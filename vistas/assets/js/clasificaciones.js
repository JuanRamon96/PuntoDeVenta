function v_clasificaciones() {
	tablaClasificaciones();

	$('#formClasificaciones').validate({
        rules: {
            nombreClasificacion: {
                required: true
            }
        },
        messages: {
            nombreClasificacion: {
                required: "El nombre es requerido."
            }
        },
        submitHandler: function(form) { 
            var formData = new FormData(document.querySelector("#formClasificaciones"));
            formData.append("metodo", $('#bGuardarClasificacion').attr('tipo'));
            formData.append("accion", "clasificaciones");
            formData.append('id', $('#bGuardarClasificacion').attr('attrID'));
            formData.append('foto', $('#bGuardarClasificacion').attr('foto'));

            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#carga").show();
                }
            })
            .done(function(res) {
                if ($.trim(res) == "Correcto") {
                    if ($("#bGuardarClasificacion").attr("tipo") == "modificar") {
                        var tipoAlerta = "modificada";
                    }else{
                        var tipoAlerta = "guardada";
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Clasificación '+tipoAlerta+' correctamente'
                    });

                    tablaClasificaciones(); 
                    verClaTienda();
                    $("#modalClasificacion").modal("hide");
                }else if($.trim(res) === 'Error 2 formato') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg'
                    });
                }else if($.trim(res) === 'Error 3 peso') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
                    });
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error inesperado al '+$("#bGuardarClasificacion").attr("tipo")+' la clasificación.'
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
}

function tablaClasificaciones() {
	ajaxMyDatatable({
        "table": $("#tablaClasificaciones"), 
        "colums": [
            "Nombre",
            "Descripcion",
            "Acciones"
        ], 
        "sort": [0, "asc"],
        "url": "index.php", 
        "params":{
            "metodo": "consultar",
            "accion": "clasificaciones"
        }
    });
}

jQuery(document).ready(function($) {
	$(document).on('click', '#bAgregarClasificacion', function() {
		$("#formClasificaciones")[0].reset();
        var validator = $("#formClasificaciones").validate();
        validator.resetForm();

        $("#bCancelarImgClasificacion").attr('foto', "url('vistas/assets/images/fondo.jpg')");
        $("#bModificarImgClasificacion").removeClass('oculto');
        $("#bCancelarImgClasificacion").addClass('oculto');
        $("#mosImgClasificacion").css('background-image', "url('vistas/assets/images/fondo.jpg')");
        $("#mosImgClasificacion").parent().attr('href', "vistas/assets/images/fondo.jpg");

		$("#bGuardarClasificacion").attr('tipo', 'insertar');
		$("#modalClasificacion").modal('show');
	});

    $(document).on('click', '#bCancelarImgClasificacion', function () {
        $(this).addClass('oculto');
        $("#bModificarImgClasificacion").removeClass('oculto');
        $('#imgClasificacion').val('');
        $("#mosImgClasificacion").css('background-image', $(this).attr('foto'));
        $("#mosImgClasificacion").parent().attr('href', $(this).attr('foto').replace('url(', '').replace(')', '').replace(/\"/gi, ""));
    });

    $(document).on('click', '#bModificarImgClasificacion', function () {
        $('#imgClasificacion').trigger('click');
    });

    $(document).on('change', '#imgClasificacion', function () {
        var tipo = $(this).val().substring($(this).val().lastIndexOf(".")).toLowerCase();

        if (this.files && this.files[0]) {
            if (tipo != ".jpeg" && tipo != ".png" && tipo != ".jpg") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg'
                });
            } else if ((this.files[0].size / (1024 * 1024)) > 10) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
                });
            } else {
                $("#bCancelarImgClasificacion").attr('foto', $("#mosImgClasificacion").css('background-image'));
                $("#bModificaImgClasificacion").addClass('oculto');
                $("#bCancelaImgClasificacion").removeClass('oculto');

                var reader = new FileReader();

                reader.onload = function (e) {
                    $("#mosImgClasificacion").css('background-image', "url('" + e.target.result + "')");
                    $("#mosImgClasificacion").parent().attr('href', e.target.result);
                }

                reader.readAsDataURL(this.files[0]);
            }
        }
    });

	$(document).on('click', '.bModificarClasificacion', function() {
        var btn = $(this);
		var padre = btn.parent().parent();
		$("#formClasificaciones")[0].reset();
		$("#nombreClasificacion").val(padre.children('td:eq(0)').children('span').text());
        $("#descripcionClasificacion").val(padre.children('td:eq(1)').text());

        if (btn.attr('foto') !== "") {
          $("#bCancelarImgClasificacion").attr('foto', "url('" + btn.attr('foto') + "')");
          $("#bModificarImgClasificacion").removeClass('oculto');
          $("#bCancelarImgClasificacion").addClass('oculto');
          $("#mosImgClasificacion").css('background-image', `url('vistas/assets/images/clasificaciones/${btn.attr('foto')}')`);
          $("#mosImgClasificacion").parent().attr('href', `vistas/assets/images/clasificaciones/${btn.attr('foto')}`);
        } else {
          $("#bCancelarImgClasificacion").attr('foto', "url('vistas/assets/images/fondo.jpg')");
          $("#bModificarImgClasificacion").removeClass('oculto');
          $("#bCancelarImgClasificacion").addClass('oculto');
          $("#mosImgClasificacion").css('background-image', "url('vistas/assets/images/fondo.jpg')");
          $("#mosImgClasificacion").parent().attr('href', "vistas/assets/images/fondo.jpg");
        }

        $("#bGuardarClasificacion").attr('foto', btn.attr('foto'));
		$("#bGuardarClasificacion").attr('attrID', $(this).attr('attrID'));
		$("#bGuardarClasificacion").attr('tipo', 'modificar');
		$("#modalClasificacion").modal('show');
	});

	$(document).on('click', '.bEliminarClasificacion', function() {
		var btn = $(this);
		Swal.fire({
			title: '¿Estás seguro de eliminar la clasificación?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
          	cancelButtonColor: '#d33',
			cancelButtonText: 'No, cancelar',
			confirmButtonText: 'Si, eliminar'
        }).then((result) => {
        	var data = "metodo=eliminar&accion=clasificaciones&id="+btn.attr('attrID')+"&foto="+btn.attr('foto');

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
                        title: 'La clasificación ha sido eliminada correctamente'
                    });

                    tablaClasificaciones(); 
                    verClaTienda();
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error inesperado al eliminar la clasificación.'
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
        });
	});
});