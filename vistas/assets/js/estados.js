jQuery(document).ready(function($) {
	$(document).on('click', '#bImprimirEstado', function() {
		window.open("controladores/pdf/estado_cuenta.php?cliente="+$(this).attr('attrID'));
	});

	$(document).on('click', '.bImprimirTablaPagos', function() {
		window.open("controladores/pdf/tabla_pagos.php?id="+$(this).attr('attrID'));
	});
});