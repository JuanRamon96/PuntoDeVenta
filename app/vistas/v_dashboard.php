<div class="vt-dashboard">
	<div class="row align-items-center mb-3">
		<div class="col-md-8 mb-2 mb-md-0">
			<p class="vt-dashboard-kicker mb-1">Panel de control</p>
			<h3 class="vt-dashboard-title mb-0">Resumen de tu negocio</h3>
			<p class="text-muted mb-0">Últimos 30 días · visible solo para administradores</p>
		</div>
		<div class="col-md-4 text-md-end">
			<button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_dashboard" titulo="Inicio" id="bRecargarDashboard">
				<i class="fa-solid fa-rotate-right"></i>
			</button>
		</div>
	</div>

	<input type="hidden" id="dashFechaInicio">
	<input type="hidden" id="dashFechaFin">

	<div class="row">
		<div class="col-6 col-lg-3 mb-3">
			<div class="vt-kpi vt-kpi-ventas">
				<span class="vt-kpi-icon"><i class="fas fa-cart-shopping"></i></span>
				<span class="vt-kpi-label">Ventas</span>
				<strong class="vt-kpi-value" id="dashTotalVentas">$0.00</strong>
			</div>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<div class="vt-kpi vt-kpi-ganancia">
				<span class="vt-kpi-icon"><i class="fas fa-arrow-trend-up"></i></span>
				<span class="vt-kpi-label">Ganancia</span>
				<strong class="vt-kpi-value" id="dashGanancia">$0.00</strong>
			</div>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<div class="vt-kpi vt-kpi-gastos">
				<span class="vt-kpi-icon"><i class="fas fa-money-bill"></i></span>
				<span class="vt-kpi-label">Gastos</span>
				<strong class="vt-kpi-value" id="dashGastos">$0.00</strong>
			</div>
		</div>
		<div class="col-6 col-lg-3 mb-3">
			<div class="vt-kpi vt-kpi-neto">
				<span class="vt-kpi-icon"><i class="fas fa-scale-balanced"></i></span>
				<span class="vt-kpi-label">Neto</span>
				<strong class="vt-kpi-value" id="dashNeto">$0.00</strong>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12 mb-2">
			<h5 class="vt-dashboard-section">Accesos directos</h5>
		</div>
		<div class="col-6 col-md-4 col-xl-2 mb-3">
			<a href="javascript:void(0)" class="vt-shortcut cargarVista" carga="v_ventas" titulo="Ventas">
				<i class="fas fa-dollar-sign"></i>
				<span>Ventas</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-xl-2 mb-3">
			<a href="javascript:void(0)" class="vt-shortcut cargarVista" carga="v_productos" titulo="Productos">
				<i class="fas fa-box"></i>
				<span>Productos</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-xl-2 mb-3">
			<a href="javascript:void(0)" class="vt-shortcut cargarVista" carga="v_inventario" titulo="Inventario">
				<i class="fas fa-clipboard-list"></i>
				<span>Inventario</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-xl-2 mb-3">
			<a href="javascript:void(0)" class="vt-shortcut cargarVista" carga="v_cajas" titulo="Cajas">
				<i class="fas fa-cash-register"></i>
				<span>Cajas</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-xl-2 mb-3">
			<a href="javascript:void(0)" class="vt-shortcut cargarVista" carga="v_clientes" titulo="Clientes">
				<i class="fas fa-people-arrows"></i>
				<span>Clientes</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-xl-2 mb-3">
			<a href="javascript:void(0)" class="vt-shortcut cargarVista" carga="v_reportes" titulo="Reportes">
				<i class="fas fa-chart-simple"></i>
				<span>Reportes</span>
			</a>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-7 mb-3">
			<div class="card vt-dash-card h-100">
				<div class="card-body">
					<h5 class="vt-dashboard-section mb-3">Ventas por día</h5>
					<div id="dashChartVentas"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-5 mb-3">
			<div class="card vt-dash-card h-100">
				<div class="card-body">
					<h5 class="vt-dashboard-section mb-3">Productos más vendidos</h5>
					<div id="dashChartProductos"></div>
				</div>
			</div>
		</div>
	</div>
</div>
