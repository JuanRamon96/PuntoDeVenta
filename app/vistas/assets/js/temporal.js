jQuery(document).ready(function($) {

	jQuery.validator.addMethod('answercheck', function (value, element) {
        return this.optional(element) || /^\bcat\b$/.test(value)
    }, "type the correct answer -_-");

    // validate registry form
    $(function() {
    	$('#formTemporal').validate({
            rules: {
                contrasena: {
                    required: true,
                    minlength: 3
                },
                confirmar: {
                    required: true,
                    minlength: 3,
                    equalTo: "#contrasena"
                }
            },
            messages: {
                contrasena: {
                    required: "Escribe una contraseña.",
                    minlength: "La contraseña debe constar de al menos 3 caracteres."
                },
                confirmar: {
                    required: "Repite la contraseña",
                    minlength: "La contraseña debe constar de al menos 3 caracteres.",
                    equalTo: "Las contraseñas no coinciden, introduce la misma contraseña."
                }
            },
            errorClass: 'is-invalid',        
            errorElement: 'div',
            submitHandler: function(form) {
                var data="metodo=detalles&accion=login&contrasena="+$("#contrasena").val();
                
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: data,
                    beforeSend: function() {
                      $("#carga").show();
                    }
                })
                .done(function(res) {
                    if($.trim(res) == "Correcto"){
                        Swal.fire({
                            icon: 'success',
                            title: '¡Tu contraseña ha sido cambiada correctamente!'
                        }).then((result) => {
                            window.location.reload();
                        });
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error inesperado al cambiar la contraseña.',
                            footer: '¿Por qué tengo este error? Contáctanos en dentastool@gmail.com'
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
	
});