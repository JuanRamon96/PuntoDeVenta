function v_dashboard() {
	var now = new Date();
	var fin = dashFechaISO(now);
	now.setDate(now.getDate() - 30);
	var inicio = dashFechaISO(now);

	$("#dashFechaInicio").val(inicio);
	$("#dashFechaFin").val(fin);

	dashCargarResumen();
	dashCargarVentas();
	dashCargarProductos();
}

function dashFechaISO(fecha) {
	var day = fecha.getDate().toString().padStart(2, '0');
	var month = (fecha.getMonth() + 1).toString().padStart(2, '0');
	return fecha.getFullYear() + "-" + month + "-" + day;
}

function dashNumero(html) {
	var tmp = document.createElement('div');
	tmp.innerHTML = html || '0';
	return parseFloat((tmp.textContent || '0').replace(/[^0-9.-]/g, '')) || 0;
}

function dashCargarResumen() {
	var data = "metodo=consultar&accion=reportes&tipo=totalGastos&fechaInicio=" + $("#dashFechaInicio").val()
		+ "&fechaFin=" + $("#dashFechaFin").val()
		+ "&usuario=&caja=";

	$.ajax({
		url: 'index.php',
		type: 'POST',
		data: data
	})
		.done(function (res) {
			var datos = {};
			try {
				datos = JSON.parse(String(res ?? '').trim()) || {};
			} catch (e) {
				datos = {};
			}

			$("#dashTotalVentas").html(datos.TotalVentas || '<span class="dinero">0</span>');
			$("#dashGanancia").html(datos.GananciaVentas || '<span class="dinero">0</span>');
			$("#dashGastos").html(datos.TotalGastos || '<span class="dinero">0</span>');
			$("#dashNeto").html(datos.Diferencia || '<span class="dinero">0</span>');

			var neto = dashNumero(datos.Diferencia);
			$("#dashNeto").closest('.vt-kpi').toggleClass('is-negative', neto < 0);

			if (typeof moneda === 'function') {
				moneda();
			}
		})
		.fail(function () {
			console.log("error dashboard resumen");
		});
}

function dashCargarVentas() {
	var data = "metodo=consultar&accion=reportes&tipo=chartVentas&fechaInicio=" + $("#dashFechaInicio").val()
		+ "&fechaFin=" + $("#dashFechaFin").val();

	$.ajax({
		url: 'index.php',
		type: 'POST',
		data: data
	})
		.done(function (res) {
			var datos = [];
			try {
				datos = JSON.parse(String(res ?? '').trim()) || [];
			} catch (e) {
				datos = [];
			}
			if (!Array.isArray(datos)) {
				datos = [];
			}

			var categorias = [];
			var valores = [];
			datos.forEach(function (dato) {
				categorias.push(dato.Fecha);
				valores.push(parseFloat(dato.Cantidad) || 0);
			});

			dashRenderLinea('dashChartVentas', categorias, valores, 'Ventas');
		})
		.fail(function () {
			console.log("error dashboard ventas");
		});
}

function dashCargarProductos() {
	var data = "metodo=consultar&accion=reportes&tipo=productos&fechaInicio=" + $("#dashFechaInicio").val()
		+ "&fechaFin=" + $("#dashFechaFin").val();

	$.ajax({
		url: 'index.php',
		type: 'POST',
		data: data
	})
		.done(function (res) {
			var datos = [];
			try {
				datos = JSON.parse(String(res ?? '').trim()) || [];
			} catch (e) {
				datos = [];
			}
			if (!Array.isArray(datos)) {
				datos = [];
			}

			var categorias = [];
			var valores = [];
			datos.slice(0, 8).forEach(function (dato) {
				categorias.push(dato.Producto);
				valores.push(parseFloat(dato.Cantidad) || 0);
			});

			dashRenderBarras('dashChartProductos', categorias, valores);
		})
		.fail(function () {
			console.log("error dashboard productos");
		});
}

function dashRenderLinea(id, categorias, valores, nombre) {
	var el = document.getElementById(id);
	if (!el || typeof ApexCharts === 'undefined') {
		return;
	}

	el.innerHTML = '';
	if (!categorias.length) {
		el.innerHTML = '<p class="text-muted text-center mb-0 py-5">Aún no hay ventas en este periodo.</p>';
		return;
	}

	var chart = new ApexCharts(el, {
		chart: {
			type: 'area',
			height: 280,
			toolbar: { show: false },
			fontFamily: 'Poppins, Open Sans, sans-serif'
		},
		series: [{ name: nombre, data: valores }],
		xaxis: {
			categories: categorias,
			labels: { rotateAlways: false, hideOverlappingLabels: true }
		},
		yaxis: { min: 0, forceNiceScale: true },
		dataLabels: { enabled: false },
		stroke: { curve: 'smooth', width: 3 },
		fill: {
			type: 'gradient',
			gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] }
		},
		colors: ['#e5312b'],
		grid: { borderColor: '#e6eaf0' }
	});
	chart.render();
}

function dashRenderBarras(id, categorias, valores) {
	var el = document.getElementById(id);
	if (!el || typeof ApexCharts === 'undefined') {
		return;
	}

	el.innerHTML = '';
	if (!categorias.length) {
		el.innerHTML = '<p class="text-muted text-center mb-0 py-5">Aún no hay productos vendidos en este periodo.</p>';
		return;
	}

	var chart = new ApexCharts(el, {
		chart: {
			type: 'bar',
			height: 280,
			toolbar: { show: false },
			fontFamily: 'Poppins, Open Sans, sans-serif'
		},
		series: [{ name: 'Cantidad', data: valores }],
		xaxis: {
			categories: categorias,
			labels: { rotate: -35, trim: true }
		},
		plotOptions: {
			bar: { borderRadius: 6, columnWidth: '55%' }
		},
		dataLabels: { enabled: false },
		colors: ['#0f2540'],
		grid: { borderColor: '#e6eaf0' }
	});
	chart.render();
}
