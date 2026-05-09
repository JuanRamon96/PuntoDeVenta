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
      if($("span.numLote:contains('"+$.trim($(this).val())+"')").length > 0){
        $("span.numLote:contains('"+$.trim($(this).val())+"')").parent().parent().parent().removeClass('oculto');
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

    if($.trim($('#buscadorLotes').val()) != ''){
      if($("span.numLote:contains('"+$.trim($('#buscadorLotes').val())+"')").length > 0){
          $("span.numLote:contains('"+$.trim($('#buscadorLotes').val())+"')").parent().parent().parent().removeClass('oculto');
        }
      }
  });
});