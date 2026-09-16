<div class="vt-reportes">
	<div class="row align-items-center mb-3">
		<div class="col-md-8 mb-2 mb-md-0">
			<p class="vt-dashboard-kicker mb-1">Cuentas por cobrar</p>
			<h3 class="vt-dashboard-title mb-0">Pagos</h3>
			<p class="text-muted mb-0">Ventas con saldo pendiente. Abona o liquida por cliente, del ticket más antiguo al más reciente</p>
		</div>
		<div class="col-md-4 text-md-end">
			<button type="button" class="btn btn-danger me-1" id="bAbonarAdeudoPagos">
				<i class="fas fa-dollar-sign"></i> Abonar / Liquidar
			</button>
			<button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_reportes_pagos" titulo="Pagos" id="bRecargar">
				<i class="fa-solid fa-rotate-right"></i>
			</button>
		</div>
	</div>

	<div class="card vt-dash-card mb-3">
		<div class="card-body">
			<h5 class="vt-dashboard-section mb-3"><i class="fas fa-filter me-2"></i>Filtro por cliente</h5>
			<div class="row">
				<div class="col-md-8 col-lg-6">
					<label class="form-label">Cliente</label>
					<div class="input-group">
						<input type="text" class="form-control" id="filtroClientePagosNombre" placeholder="Todos los clientes" readonly style="cursor: pointer;">
						<input type="hidden" id="filtroClientePagosId" value="">
						<button type="button" class="btn btn-outline-secondary" id="bBuscarClienteFiltroPagos" title="Buscar cliente">
							<i class="fas fa-search"></i>
						</button>
						<button type="button" class="btn btn-outline-danger" id="bQuitarClienteFiltroPagos" title="Quitar filtro">
							<i class="fas fa-times"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-6 col-lg-4 mb-3">
			<div class="vt-kpi vt-kpi-gastos">
				<span class="vt-kpi-icon"><i class="fas fa-receipt"></i></span>
				<span class="vt-kpi-label">Tickets pendientes</span>
				<strong class="vt-kpi-value" id="pagosTicketsValor">0</strong>
			</div>
		</div>
		<div class="col-6 col-lg-4 mb-3">
			<div class="vt-kpi vt-kpi-ventas">
				<span class="vt-kpi-icon"><i class="fas fa-file-invoice-dollar"></i></span>
				<span class="vt-kpi-label">Total de ventas</span>
				<strong class="vt-kpi-value" id="pagosTotalValor">$0.00</strong>
			</div>
		</div>
		<div class="col-12 col-lg-4 mb-3">
			<div class="vt-kpi vt-kpi-neto">
				<span class="vt-kpi-icon"><i class="fas fa-hand-holding-dollar"></i></span>
				<span class="vt-kpi-label">Adeudo pendiente</span>
				<strong class="vt-kpi-value text-danger" id="pagosAdeudoValor">$0.00</strong>
			</div>
		</div>
	</div>

	<div class="card vt-dash-card mb-3">
		<div class="card-body">
			<h5 class="vt-dashboard-section mb-3"><i class="fas fa-list me-2"></i>Ventas con pago pendiente</h5>
			<div class="row">
				<div class="col-12 table-responsive">
					<table id="tablaPagosPendientes" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Folio</th>
								<th>Cliente</th>
								<th>Tipo Pago</th>
								<th>Total</th>
								<th>Pagado</th>
								<th>Restante</th>
								<th orden="No">Estatus</th>
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
</div>

<div class="modal" id="modalDetalles" tabindex="-2">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<p class="h5 fw-bold modal-title">Detalles de la venta</p>
				<button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12 table-responsive">
						<table class="table table-hover table-striped table-bordered text-center myDataTable" id="tablaDetalles" width="100%">
							<thead>
								<tr>
									<th>Producto</th>
									<th>Precio</th>
									<th>Cantidad</th>
									<th>Descuento</th>
									<th>Impuestos</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalVerPagosVentas" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Pagos <span id="folioVentaPagos"></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12 text-end">
						<button class="btn btn-outline-danger btn-sm" id="bRecargarPagosVenta"><i class="fas fa-rotate-right"></i></button>
					</div>
				</div>
				<div class="row">
					<div class="col-12 table-responsive">
						<table class="table table-hover table-striped table-bordered text-center myDataTable" id="tablaVerPagosVentas" width="100%" style="font-size: 12px;">
							<thead>
								<tr>
									<th>Fecha</th>
									<th>Concepto</th>
									<th>Tipo de pago</th>
									<th>Monto</th>
									<th orden="No">Detalles</th>
									<th orden="No">Comprobante</th>
									<th orden="No">Acciones</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalAbonoPagos" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title fw-bold">Abonar / Liquidar adeudo</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="formAbonoPagos">
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">Cliente</label>
						<div class="input-group">
							<input type="text" class="form-control" id="abonoClienteNombre" name="abonoClienteNombre" placeholder="Seleccione un cliente" readonly required style="cursor: pointer;">
							<input type="hidden" id="abonoClienteId" name="abonoClienteId" value="">
							<button type="button" class="btn btn-outline-secondary" id="bBuscarClienteAbonoPagos" title="Buscar cliente">
								<i class="fas fa-search"></i>
							</button>
						</div>
					</div>
					<div class="mb-3 text-center">
						<p class="text-muted mb-1">Adeudo del cliente</p>
						<h4 class="fw-bold mb-0"><span class="dinero" id="abonoAdeudoCliente">0</span></h4>
					</div>
					<div class="mb-3">
						<div class="form-floating">
							<input type="text" class="form-control" id="montoAbonoPagos" name="montoAbonoPagos" placeholder="Monto">
							<label>Monto a abonar</label>
						</div>
					</div>
					<div class="mb-3">
						<div class="form-floating">
							<select class="form-select" id="tipoPagoAbonoPagos" name="tipoPagoAbonoPagos">
								<option value="Efectivo" selected>Efectivo</option>
								<option value="Deposito">Depósito</option>
								<option value="TransferenciaBancaria">Transferencia bancaria</option>
								<option value="TarjetaCreditoDebito">Tarjeta de crédito o débito</option>
								<option value="Cheque">Cheque</option>
							</select>
							<label>Tipo de pago</label>
						</div>
					</div>
					<div class="mb-3">
						<div class="form-floating">
							<select class="form-select" id="cajaAbonoPagos" name="cajaAbonoPagos">
								<option value="">--Selecciona una caja--</option>
							</select>
							<label>Caja</label>
						</div>
					</div>
					<div class="d-grid">
						<button type="button" class="btn btn-outline-danger" id="bLiquidarAbonoPagos">
							<i class="fas fa-check-double"></i> Liquidar
						</button>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
					<button type="submit" class="btn btn-danger" id="bGuardarAbonoPagos">Abonar <i class="fas fa-check"></i></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modalClienteReportePagos" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title fw-bold">Clientes</h5>
				<button type="button" class="btn btn-outline-danger btn-sm ms-auto me-2" id="bRecargarClientesReportePagos" title="Recargar listado">
					<i class="fa-solid fa-rotate-right"></i>
				</button>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12 table-responsive">
						<table class="table table-striped table-hover text-center table-bordered myDataTable" id="tablaClientesReportePagos" width="100%">
							<thead>
								<tr>
									<th>Nombre</th>
									<th>Tipo</th>
									<th>Domicilio</th>
									<th>Contacto</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
			</div>
		</div>
	</div>
</div>
