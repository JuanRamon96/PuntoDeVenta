<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="" />
	<meta name="keywords" content="">
	<title>Stazione</title>
	<!-- Favicon icon -->
	<link rel="shortcut icon" href="vistas/assets/images/logos/logo.png">

	<link rel="stylesheet" href="vistas/assets/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="vistas/assets/plugins/fontawesome/css/all.css">
	<link rel="stylesheet" href="vistas/assets/plugins/fileinput/css/fileinput.min.css" />
	<link rel="stylesheet" href="vistas/assets/plugins/myDataTable/css/myDataTable.css">
	<link rel="stylesheet" href="vistas/assets/plugins/galleria/src/themes/classic/galleria.classic.css">
	<link rel="stylesheet" href="vistas/assets/plugins/fancybox/dist/jquery.fancybox.min.css">
	<link rel="stylesheet" href="vistas/assets/css/style.css">
</head>

<body class="bodyMenu">
	<!-- [ Pre-loader ] start -->
	<div class="loader-bg">
		<div class="loader-track">
			<div class="loader-fill"></div>
		</div>
	</div>

	<div class="carga" id="carga">
		<div class="container" style="min-height: 100vh;">
			<div class="row align-items-center" style="min-height: 100vh;">
				<div class="col-12 text-center">
					<div class="spinner-border text-primary" style="width: 8rem; height: 8rem;" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- [ Main Content ] start -->
	<div class="container-fluid">
		<div class="row">
			<div class="col-6 productos">
				<div class="row">
					<div class="col-12" style="background-color: #830000; padding: 10px 40px; padding-bottom: 25px;">
						<div class="input-group">
						  	<span class="input-group-text" style="background-color: transparent; border: none;"><i class="fas fa-search" style="color: #FFF;"></i></span>
						  	<div class="form-floating">
						    	<input type="text" class="form-control" id="buscarProductos" name="buscarProductos" placeholder="Buscar">
						    	<label style="color: #FFF;">Buscar...</label>
						  	</div>
						</div>
					</div>
				</div>	
				<div class="row" id="verProductos">
					
				</div>
			</div>
			<div class="col-6 ticket">
				<div class="row">
					<div class="col-12">
						
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						<button type="button" class="btn btn-outline-danger btn-sm" id="bVerVentas" style="margin: 5px;">Ventas <i class="fas fa-list"></i></button>
					</div>
					<div class="col-6 text-end">
						<div class="dropdown">
						 	<button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="bUser"><i class="fas fa-user"></i></button>
						  	<ul class="dropdown-menu">
						    	<li><a class="dropdown-item" href="javascript:void(0)" id="bCerrarSe"><i class="fas fa-power-off"></i> Cerrar sesión</a></li>
						  	</ul>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12" id="tituloTablaProd">
						<h6 class="text-muted text-center">PRODUCTOS</h6>
						<p class="text-center" style="width: 23%;">PRECIO</p>
						<p class="text-center" style="width: 35%;">CANTIDAD</p>
						<p class="text-center" style="width: 30%;">TOTAL</p>
					</div>
				</div>
				<div class="row">
					<div class="col-12 table-responsive tablaProd">
						<table class="table table-hover table-striped text-center" id="tablaProductos" width="100%" style="font-size: 16px;">
							<tbody id="productosBody">
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" style="padding-top: 15px; box-shadow: 0px -2px 2px 0px rgba(0, 0, 0, 0.4);">
					<div class="col-5 d-grid">
						<button type="button" class="btn btn-outline-info" id="bProdComun">Prod. Comun</button>
					</div>
					<div class="col-7 text-center">
						<h2><span style="font-size: 16px;">TOTAL:</span> <span class="dinero" id="totalVenta">0</span></h2>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-5 d-grid">
						<button type="button" class="btn btn-outline-danger" id="bBorrarTodo"><i class="fas fa-trash"></i> Borrar Todo</button>
					</div>
					<div class="col-7 d-grid">
						<button type="button" class="btn btn-primary btn-lg" id="bCobrar"><i class="fas fa-save"></i> Cobrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--/////////////////////////////////Modal////////////////////////////////////-->
	<div class="modal fade" id="modalProdComun" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-dialog-centered">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h1 class="modal-title fs-5" id="exampleModalLabel">Prod. Comun</h1>
	        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      		</div>
	      		<form id="formProdComun">
		      		<div class="modal-body">
		        		<div class="form-floating mb-3">
						  	<input type="text" class="form-control form-control-lg" name="nombreComun" id="nombreComun" placeholder="Nombre">
						  	<label>Nombre</label>
						</div>
						<div class="form-floating mb-3">
						  	<input type="number" step="any" class="form-control form-control-lg" name="cantidadComun" id="cantidadComun" placeholder="Cantidad">
						  	<label>Cantidad</label>
						</div>
						<div class="form-floating mb-3">
						  	<input type="number" step="any" class="form-control form-control-lg" name="precioComun" id="precioComun" placeholder="Precio">
						  	<label>Precio</label>
						</div>
		      		</div>
		      		<div class="modal-footer">
		        		<button type="button" class="btn btn-outline-danger btn-lg" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        		<button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> Agregar</button>
		      		</div>
	      		</form>
	    	</div>
	  	</div>
	</div>

	<!--/////////////////////////////////Modal////////////////////////////////////-->
	<div class="modal fade" id="modalFinalizar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-lg modal-dialog-centered">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h1 class="modal-title fs-5" id="exampleModalLabel">Finalizar Venta</h1>
	        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      		</div>
	      		<form id="formFinalizar">
		      		<div class="modal-body">
		      			<div class="mb-3">
		      				<h1 class="text-center dinero" id="totalVentaFinal">0</h1>
		      			</div>
		        		<div class="form-floating mb-3">
						  	<select name="metodoPago" id="metodoPago" class="form-control form-control-lg">
								<option value="Efectivo">Efectivo</option>
								<option value="Transferencia Bancaria">Transferencia Bancaria</option>
								<option value="Tarjeta de crédito o débito">Tarjeta de crédito o débito</option>
								<option value="Depósito">Depósito</option>
								<option value="Cheque">Cheque</option>
								<option value="Pago online">Pago online</option>
						  	</select>
						  	<label>Metodo de pago</label>
						</div>
						<div class="form-floating mb-3">
						  	<input type="number" step="any" class="form-control form-control-lg" name="pagoCon" id="pagoCon" placeholder="Cantidad">
						  	<label>Pago con:</label>
						</div>
						<div class="mb-3">
						  	<h1 class="text-center">Cambio: <span class="dinero" id="totalCambio">0</span></h1>
						</div>
						<div class="form-floating">
						  <textarea class="form-control" placeholder="Detalles" name="detallesFinal" id="detallesFinal" style="height: 100px"></textarea>
						  <label>Detalles</label>
						</div>
					</div>
		      		<div class="modal-footer">
		        		<button type="button" class="btn btn-outline-danger btn-lg" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        		<button type="submit" class="btn btn-info btn-lg" id="bFianlizar"><i class="fas fa-plus"></i> Cobrar</button>
		        		<!--<button type="submit" class="btn btn-primary btn-lg" id="bFianlizarImpri"><i class="fas fa-plus"></i> Cobrar e Imprimir</button>-->
		      		</div>
	      		</form>
	    	</div>
	  	</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MVentasPendientes" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Ventas Pendientes</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12 text-end">
							<button class="btn btn-secondary btn-sm" id="bRecargarVentasPendientes" type="button"><i class="fa-solid fa-rotate-right"></i></button>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-12 table-responsive">
							<table class="table table-striped text-center myDataTable" id="tablaVentasPendientes" width="100%">
								<thead>
									<tr>
										<th>Fecha</th>
										<th>Folio</th>
										<th>Tipo Pago</th>
										<th>Total</th>
										<th>Pago</th>
										<th>Cambio</th>
										<th>Detalles</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
				</div>
			</div>
		</div>
	</div>

	<script src="vistas/assets/js/jquery-3.6.1.min.js"></script>
	<script src="vistas/assets/js/vendor-all.min.js"></script>
	<script src="vistas/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="vistas/assets/js/ripple.js"></script>
	<script src="vistas/assets/js/pcoded.js"></script>
	<script src="vistas/assets/js/plugins/apexcharts.min.js"></script>
	<script src="vistas/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
	<script src="vistas/assets/plugins/sweetalert/dist/sweetalert2.all.min.js"></script>
	<script src="vistas/assets/plugins/fileinput/js/plugins/piexif.min.js"></script>
	<script src="vistas/assets/plugins/fileinput/js/plugins/sortable.min.js"></script>
	<script src="vistas/assets/plugins/fileinput/js/fileinput.min.js"></script>
	<script src="vistas/assets/plugins/fileinput/js/locales/es.js"></script>
	<script src="vistas/assets/plugins/myDataTable/js/myDataTable.js"></script>
	<script src="vistas/assets/plugins/galleria/src/galleria.js"></script>
	<script src="vistas/assets/plugins/galleria/src/themes/classic/galleria.classic.js"></script>
	<script type="text/javascript" src="vistas/assets/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
	<script src="vistas/assets/js/main.js"></script>
</body>

</html>