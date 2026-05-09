function v_reportes() {
	var now = new Date();
    var day = now.getDate().toString().padStart(2, '0');
    var month = (now.getMonth() + 1).toString().padStart(2, '0');
    var today = now.getFullYear()+"-"+(month)+"-"+(day);

    $("#fechaFinProd").val(today);
    $("#fechaFinVentas").val(today);
    
    now.setDate(now.getDate() - 30);
    day = now.getDate().toString().padStart(2, '0');
    month = (now.getMonth() + 1).toString().padStart(2, '0');
    today = now.getFullYear()+"-"+(month)+"-"+(day);

    $("#fechaInicioProd").val(today);
    $("#fechaInicioVentas").val(today);

    chartProductos();
    tablaVentasReportes();
    chartVentas();
}

function chartProductos() {
	if(new Date($("#fechaInicioProd").val()) > new Date($("#fechaFinProd").val())){
		Swal.fire({
			icon: 'warning',
			title: 'Oops...',
			text: 'La fecha de inicio debe ser menor a la fecha final.'
		});
	}else{
		var padre = $("#chartProductos").parent();
		$("#chartProductos").remove();
		padre.html('<div class="col-12" id="chartProductos" style="height: 600px;"></div>');

		var data = "metodo=consultar&accion=reportes&tipo=productos&fechaInicio="+$("#fechaInicioProd").val()+"&fechaFin="+$("#fechaFinProd").val();

		$.ajax({
			url: 'index.php',
			type: 'POST',
			data: data,
			beforeSend: function() {
				$("#carga").show();
			}
		})
		.done(function(res) {
			//console.log($.trim(res));
			datos = JSON.parse($.trim(res));
			//console.log(datos);

			am5.ready(function() {
				// Create root element
				// https://www.amcharts.com/docs/v5/getting-started/#Root_element
				var root = am5.Root.new("chartProductos");
				// Set themes
				// https://www.amcharts.com/docs/v5/concepts/themes/
				root.setThemes([
				  am5themes_Animated.new(root)
				]);

				// Create chart
				// https://www.amcharts.com/docs/v5/charts/xy-chart/
				var chart = root.container.children.push(am5xy.XYChart.new(root, {
				  panX: false,
				  panY: false,
				  wheelX: "panX",
				  wheelY: "zoomX",
				  layout: root.verticalLayout
				}));

				// Data
				var colors = chart.get("colors");

				var data = [];
				datos.forEach(dato => {
					data.push({
					 	country: dato.Producto,
					  	visits: parseFloat(dato.Cantidad),
					  	icon: dato.Foto,
					  	columnSettings: { fill: colors.next() }
					});
				});

				// Create axes
				// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
				var xRenderer = am5xy.AxisRendererX.new(root, {
				  minGridDistance: 30
				})

				var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
				  categoryField: "country",
				  renderer: xRenderer,
				  bullet: function(root, axis, dataItem) {
				    return am5xy.AxisBullet.new(root, {
				      location: 0.5,
				      sprite: am5.Picture.new(root, {
				        width: 24,
				        height: 24,
				        centerY: am5.p50,
				        centerX: am5.p50,
				        src: dataItem.dataContext.icon
				      })
				    });
				  }
				}));

				xRenderer.grid.template.setAll({
				  location: 1
				})

				xRenderer.labels.template.setAll({
				  paddingTop: 20
				});

				xAxis.data.setAll(data);

				var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
				  renderer: am5xy.AxisRendererY.new(root, {
				    strokeOpacity: 0.1
				  })
				}));

				// Add series
				// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
				var series = chart.series.push(am5xy.ColumnSeries.new(root, {
				  xAxis: xAxis,
				  yAxis: yAxis,
				  valueYField: "visits",
				  categoryXField: "country"
				}));

				series.columns.template.setAll({
				  tooltipText: "{categoryX}: {valueY}",
				  tooltipY: 0,
				  strokeOpacity: 0,
				  templateField: "columnSettings"
				});

				series.data.setAll(data);

				// Make stuff animate on load
				// https://www.amcharts.com/docs/v5/concepts/animations/
				series.appear();
				chart.appear(1000, 100);
			}); // end am5.ready()
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			$("#carga").hide();
		});
	}
}

function chartVentas() {
	var padre = $("#chartVentas").parent();
	$("#chartVentas").remove();
	padre.html('<div class="col-12" id="chartVentas" style="height: 600px;"></div>');

	var data = "metodo=consultar&accion=reportes&tipo=chartVentas&fechaInicio="+$("#fechaInicioVentas").val()+"&fechaFin="+$("#fechaFinVentas").val();

	$.ajax({
		url: 'index.php',
		type: 'POST',
		data: data,
		beforeSend: function() {
			$("#carga").show();
		}
	})
	.done(function(res) {
		//console.log($.trim(res));
		datos = JSON.parse($.trim(res));
		//console.log(datos);
	
		am5.ready(function() {
			// Create root element
			// https://www.amcharts.com/docs/v5/getting-started/#Root_element
			var root = am5.Root.new("chartVentas");

			// Set themes
			// https://www.amcharts.com/docs/v5/concepts/themes/
			root.setThemes([
			  am5themes_Animated.new(root)
			]);

			// Create chart
			// https://www.amcharts.com/docs/v5/charts/xy-chart/
			var chart = root.container.children.push(am5xy.XYChart.new(root, {
			  panX: true,
			  panY: true,
			  wheelX: "panX",
			  wheelY: "zoomX",
			  pinchZoomX:true
			}));

			// Add cursor
			// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
			var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
			  behavior: "none"
			}));
			cursor.lineY.set("visible", false);

			var data = [];
			datos.forEach(dato => {
				data.push({
					date: new Date(dato.Fecha).getTime(),
					value: parseFloat(dato.Cantidad)
				});
			});

			// Create axes
			// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
			var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
			  baseInterval: { timeUnit: "day", count: 1 },
			  renderer: am5xy.AxisRendererX.new(root, {}),
			  tooltip: am5.Tooltip.new(root, {})
			}));

			var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
			  renderer: am5xy.AxisRendererY.new(root, {})
			}));

			// Add series
			// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
			var series = chart.series.push(am5xy.LineSeries.new(root, {
			  name: "Series",
			  xAxis: xAxis,
			  yAxis: yAxis,
			  valueYField: "value",
			  valueXField: "date",
			  tooltip: am5.Tooltip.new(root, {
			    labelText: "{valueY}"
			  })
			}));


			// Add scrollbar
			// https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
			var scrollbar = chart.set("scrollbarX", am5xy.XYChartScrollbar.new(root, {
			  orientation: "horizontal",
			  height: 60
			}));

			var sbDateAxis = scrollbar.chart.xAxes.push(am5xy.DateAxis.new(root, {
			  baseInterval: {
			    timeUnit: "day",
			    count: 1
			  },
			  renderer: am5xy.AxisRendererX.new(root, {})
			}));

			var sbValueAxis = scrollbar.chart.yAxes.push(
			  am5xy.ValueAxis.new(root, {
			    renderer: am5xy.AxisRendererY.new(root, {})
			  })
			);

			var sbSeries = scrollbar.chart.series.push(am5xy.LineSeries.new(root, {
			  valueYField: "value",
			  valueXField: "date",
			  xAxis: sbDateAxis,
			  yAxis: sbValueAxis
			}));

			series.data.setAll(data);
			sbSeries.data.setAll(data);

			// Make stuff animate on load
			// https://www.amcharts.com/docs/v5/concepts/animations/
			series.appear(1000);
			chart.appear(1000, 100);
		}); // end am5.ready()
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		$("#carga").hide();
	});
}

function tablaVentasReportes() {
	ajaxMyDatatable({
	    table: $('#tablaVentasReportes'),
	    colums: [
	      'Fecha',
	      'Caja', 
	      'Folio',
	      'Tipo_Pago',
	      'Total_Costo',
	      'Total',
	      'Ganancia'   
	    ],
	    sort: [0, 'desc'],
	    url: 'index.php',
	    params: {
	      metodo: 'consultar',
	      accion: 'reportes',
	      tipo: "ventas",
	      fechaInicio: $("#fechaInicioVentas").val(),
	      fechaFin: $("#fechaFinVentas").val()
	    },
		totals:{
			4: "Costo",
			5: "Total",
			6: "Ganancia"
		}
  	});
}

jQuery(document).ready(function($) {

	$(document).on('change', '.fechasProdRep', function() {
		chartProductos();
	});

	$(document).on('change', '.fechasVenRep', function() {
		if(new Date($("#fechaInicioVentas").val()) > new Date($("#fechaFinVentas").val())){
			Swal.fire({
				icon: 'warning',
				title: 'Oops...',
				text: 'La fecha de inicio debe ser menor a la fecha final.'
			});
		}else{
			tablaVentasReportes();
			chartVentas();
		}
	});
});