<div class="vt-reportes">
	<div class="row align-items-center mb-3">
		<div class="col-md-8 mb-2 mb-md-0">
			<p class="vt-dashboard-kicker mb-1">Análisis</p>
			<h3 class="vt-dashboard-title mb-0">Finanzas</h3>
			<p class="text-muted mb-0">Filtra por fechas, usuario y caja para ver ventas, gastos y productos</p>
		</div>
		<div class="col-md-4 text-md-end">
			<button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_reportes" titulo="Finanzas" id="bRecargar">
				<i class="fa-solid fa-rotate-right"></i>
			</button>
		</div>
	</div>

	<div class="card vt-dash-card mb-3">
		<div class="card-body">
			<h5 class="vt-dashboard-section mb-3"><i class="fas fa-filter me-2"></i>Filtros de ventas</h5>
			<div class="row">
				<div class="col-md-6 col-lg-3 mb-3">
					<div class="form-floating">
						<input type="date" class="form-control fechasVenRep" id="fechaInicioVentas" name="fechaInicioVentas" placeholder="Fecha Inicio">
						<label>Fecha inicio</label>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-3">
					<div class="form-floating">
						<input type="date" class="form-control fechasVenRep" id="fechaFinVentas" name="fechaFinVentas" placeholder="Fecha Inicio">
						<label>Fecha Fin</label>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-3">
					<div class="form-floating">
						<select class="form-select" id="usuarioReporte" name="usuarioReporte">
							#usuariosReporte#
						</select>
						<label>Usuario</label>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-3">
					<div class="form-floating">
						<select class="form-select" id="cajaReporte" name="cajaReporte">
							#cajasReporte#
						</select>
						<label>Caja</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row" id="resumenGastos">
		<div class="col-6 col-lg-4 mb-3">
			<div class="vt-kpi vt-kpi-gastos">
				<span class="vt-kpi-icon"><i class="fas fa-money-bill"></i></span>
				<span class="vt-kpi-label">Total Gastos</span>
				<strong class="vt-kpi-value text-danger" id="totalGastosValor">$0.00</strong>
			</div>
		</div>
		<div class="col-6 col-lg-4 mb-3">
			<div class="vt-kpi vt-kpi-ganancia">
				<span class="vt-kpi-icon"><i class="fas fa-arrow-trend-up"></i></span>
				<span class="vt-kpi-label">Ganancia Ventas</span>
				<strong class="vt-kpi-value text-success" id="gananciaVentasValor">$0.00</strong>
			</div>
		</div>
		<div class="col-12 col-lg-4 mb-3">
			<div class="vt-kpi vt-kpi-neto">
				<span class="vt-kpi-icon"><i class="fas fa-scale-balanced"></i></span>
				<span class="vt-kpi-label">Diferencia (Neto)</span>
				<strong class="vt-kpi-value" id="diferenciaValor">$0.00</strong>
			</div>
		</div>
	</div>

	<div class="card vt-dash-card mb-3">
		<div class="card-body">
			<h5 class="vt-dashboard-section mb-3"><i class="fas fa-receipt me-2"></i>Ventas</h5>
			<div class="row">
				<div class="col-12 table-responsive">
					<table id="tablaVentasReportes" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Caja</th>
								<th>Folio</th>
								<th>Tipo Pago</th>
								<th>Costo</th>
								<th>Total</th>
								<th>Ganancia</th>
								<th>Usuario</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="card vt-dash-card mb-3">
		<div class="card-body">
			<h5 class="vt-dashboard-section mb-3"><i class="fas fa-wallet me-2"></i>Gastos</h5>
			<div class="row">
				<div class="col-12 table-responsive">
					<table id="tablaGastosReportes" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
						<thead>
							<tr>
								<th>Fecha Registro</th>
								<th>Monto</th>
								<th>Descripción</th>
								<th>Usuario</th>
								<th>Fecha Gasto</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="card vt-dash-card mb-3">
		<div class="card-body">
			<h5 class="vt-dashboard-section mb-3"><i class="fas fa-chart-line me-2"></i>Resumen de ventas</h5>
			<div class="row">
				<div class="col-12" id="chartVentas" style="height: 420px;"></div>
			</div>
		</div>
	</div>

	<div class="card vt-dash-card mb-3">
		<div class="card-body">
			<h5 class="vt-dashboard-section mb-3"><i class="fas fa-box me-2"></i>Productos más vendidos</h5>
			<div class="row">
				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<input type="date" class="form-control fechasProdRep" id="fechaInicioProd" name="fechaInicioProd" placeholder="Fecha Inicio">
						<label>Fecha inicio</label>
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<input type="date" class="form-control fechasProdRep" id="fechaFinProd" name="fechaFinProd" placeholder="Fecha Inicio">
						<label>Fecha Fin</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12" id="chartProductos" style="height: 420px;"></div>
			</div>
		</div>
	</div>
</div>
