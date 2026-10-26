function v_detalles_desarrollo() {
  Galleria.loadTheme('vistas/assets/plugins/galleria/src/themes/classic/galleria.classic.js');
  
  Galleria.run('.galleria', {
    height: parseInt($('#gallery').css('height')),
    wait: true
  });
}

jQuery(document).ready(function($) {
  
  $(document).on('keyup', '#buscadorLotes', function() {
    $(".colLote").addClass('oculto');
      if($("span.numLote:contains('"+String($(this).val() ?? '').trim()+"')").length > 0){
        $("span.numLote:contains('"+String($(this).val() ?? '').trim()+"')").parent().parent().parent().removeClass('oculto');
      }
  });

  $(document).on('click', '.checkFiltroLotes', function() {
    $(".colLote").addClass('ocultoCheck');

    var check = false;
    $('.checkFiltroLotes').each(function(index, el) {
      if($(this).prop('checked')){
        check = true;

        if($("span.estatusLote:contains('"+$(this).val()+"')").length > 0){
            $("span.estatusLote:contains('"+$(this).val()+"')").parent().parent().parent().removeClass('ocultoCheck');
          }
        }
    });

    if(check == false){
      $(".colLote").removeClass('ocultoCheck');
    }

    if(String($('#buscadorLotes').val() ?? '').trim() != ''){
      if($("span.numLote:contains('"+String($('#buscadorLotes').val() ?? '').trim()+"')").length > 0){
          $("span.numLote:contains('"+String($('#buscadorLotes').val() ?? '').trim()+"')").parent().parent().parent().removeClass('oculto');
        }
      }
  });
});