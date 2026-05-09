jQuery(document).ready(function($) {
	socket.on('notificacion', (msg) => {
        var data="metodo=consultar&accion=notificaciones";
        
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: data,
            /*beforeSend: function() {
                $("#carga").show();
            }*/
        })
        .done(function(res) {
            //console.log($.trim(res));
            var resA = jQuery.parseJSON(res);

            resA.forEach(noti => {
            	if(noti.FK_Usuario == $("#bUsuario").attr('attrID')){
            		if($(".bEliminarNoti[attrID="+noti.ID_Notificacion+"]").length == 0){
            			$("#mostrarNoti").prepend(`<div class="cardNoti col-12">
							<div class="row">
								<div class="col-12"><p>`+noti.Fecha+`</p></div>
								<div class="col-12">
									<h6>`+noti.Titulo+`</h6>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<p style="text-align: justify;">`+noti.Descripcion+`</p>
								</div>
								<div class="col-12 text-end">
									<button class="btn btn-danger btn-sm bEliminarNoti" attrID="`+noti.ID_Notificacion+`"><i class="fas fa-trash"></i></button>
								</div>
							</div>
						</div>`);

						$("#numNoti").children('p').html(parseInt($("#numNoti").children('p').text()) + 1);
            		}
            	}
            });
        })
        .fail(function() {
            console.log("Error ajax");
        }).always(function() {
            //$("#carga").hide();
        }); 
    });
});