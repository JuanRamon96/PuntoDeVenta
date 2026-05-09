 <!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="" />
	<meta name="keywords" content="">
	<meta name="author" content="Phoenixcoded" />
	<title>VentasTool</title>
	<!-- Favicon icon -->
	<link rel="shortcut icon" href="vistas/assets/images/logos/v.png">

	<link rel="stylesheet" href="vistas/assets/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="vistas/assets/plugins/fontawesome/css/all.css">
	<link rel="stylesheet" href="vistas/assets/plugins/fileinput/css/fileinput.min.css" />
	<link rel="stylesheet" href="vistas/assets/plugins/myDataTable/css/myDataTable.css">
	<link rel="stylesheet" href="vistas/assets/plugins/galleria/src/themes/classic/galleria.classic.css">
	<link rel="stylesheet" href="vistas/assets/plugins/fancybox/dist/jquery.fancybox.min.css">
	<link rel="stylesheet" href="vistas/assets/plugins/leaflet/leaflet.css">
	<link rel="stylesheet" href="vistas/assets/css/style.css">
</head>

<body class="bodyMenu">
	<!-- [ Pre-loader ] start -->
	<div class="loader-bg">
		<div class="loader-track">
			<div class="loader-fill"></div>
		</div>
	</div>

	<div id="noEncontrado">
		<div class="container-fluid" style="height: 100vh; overflow-y: auto;">
			<div class="row align-items-center justify-content-center" style="height: 100vh;">
				<div class="col-10 text-center" style="background-color: #E74C3C;">
					<h1 style="color: #FFF; padding: 30px 0px;">Producto No Encontrado</h1>
				</div>
			</div>
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

	<!-- [ Pre-loader ] End -->
	<!-- [ navigation menu ] start -->
	<nav class="pcoded-navbar menu-light ">
		<div class="navbar-wrapper  ">
			<div class="navbar-content scroll-div ">
				<div class="main-menu-header">
					<img class="img-radius" width="50" height="50" src="#fotoCuenta#" id="imagenPerfil" alt="User-Profile-Image">
				</div>
				<ul class="nav pcoded-inner-navbar">
					<li class="nav-item pcoded-menu-caption">
						<label>Producto</label>
					</li>
					#menuSucursales#
					#menuProductos#
					#menuInventario#
					#menuClasificaciones#
					<li class="nav-item pcoded-menu-caption">
						<label>Ventas</label>
					</li>
					#menuClientes#
					#menuVentas#
					#menuCajas#
					<li class="nav-item pcoded-menu-caption">
						<label>Compras</label>
					</li>
					#menuProveedores#
					#menuCompras#
					<li class="nav-item pcoded-menu-caption">
						<label>Facturación</label>
					</li>
					#menuFacturacion#
					<li class="nav-item pcoded-menu-caption">
						<label>Otros</label>
					</li>
					#menuGastos#
					#menuUsuarios#
					#menuConfiguracion#
					#menuReportes#
				</ul>
			</div>
		</div>
	</nav>
	<!-- [ navigation menu ] end -->
	<!-- [ Header ] start -->
	<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">

		<div class="m-header">
			<a class="mobile-menu" id="mobile-collapse" href="javascript:void(0)"><span></span></a>
			<a href="javascript:void(0)" class="pop-search btn btn-outline-secondary btn-sm" id="bAbrirCaja"><i class="fas fa-cart-shopping"></i> Caja</a>
			<a href="javascript:void(0)" class="b-brand">
				<!-- ========   change your logo hear   ============ -->
				<!-- <img src="vistas/assets/images/logos/logoB.png" class="logo" width="55%"> -->
			</a>
			<a href="javascript:void(0)" class="mob-toggler">
				<i class="feather icon-more-vertical"></i>
			</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<!-- <div class="search-bar">
						<input type="text" class="form-control border-0 shadow-none" placeholder="Search hear">
						<button type="button" class="close" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div> -->
				</li>
			</ul>
			<ul class="navbar-nav ml-auto">
				<!--<li>
					<div class="dropdown">
						<a class="dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon feather icon-bell"></i></a>
						<div class="dropdown-menu dropdown-menu-right notification">
							<div class="noti-head">
								<h6 class="d-inline-block m-b-0">Notificaciones</h6>
								<div class="float-right">
									<a href="#" class="m-r-10"></a>
									<a href="#">Eliminar Todas</a>
								</div>
							</div>
							<ul class="noti-body">
								<li class="n-title">
									<p class="m-b-0">NEW</p>
								</li>
								<li class="notification">
									<div class="media">
										<img class="img-radius" src="vistas/assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
										<div class="media-body">
											<p><strong>John Doe</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>5 min</span></p>
											<p>New ticket Added</p>
										</div>
									</div>
								</li>
								<li class="n-title">
									<p class="m-b-0">EARLIER</p>
								</li>
								<li class="notification">
									<div class="media">
										<img class="img-radius" src="vistas/assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
										<div class="media-body">
											<p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>10 min</span></p>
											<p>Prchace New Theme and make payment</p>
										</div>
									</div>
								</li>
								<li class="notification">
									<div class="media">
										<img class="img-radius" src="vistas/assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
										<div class="media-body">
											<p><strong>Sara Soudein</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>12 min</span></p>
											<p>currently login</p>
										</div>
									</div>
								</li>
								<li class="notification">
									<div class="media">
										<img class="img-radius" src="vistas/assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
										<div class="media-body">
											<p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>30 min</span></p>
											<p>Prchace New Theme and make payment</p>
										</div>
									</div>
								</li>
							</ul>
							<div class="noti-footer">
								<a href="javascript:void(0)">Ver todas</a>
							</div>
						</div>
					</div>
				</li>-->
				<li>
					<div class="dropdown drp-user">
						<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="feather icon-user"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right profile-notification">
							<div class="pro-head">
								<span id="cambioEmail">#emailCuenta#</span>
							</div>
							<ul class="pro-body">
								<li><a href="javascript:void(0)" class="dropdown-item cargarVista" carga="v_configuracion" titulo="Configuración" id="bMenuConfiguracion"><i class="feather icon-settings"></i> Configuración</a></li>
								<li><a href="javascript:void(0)" class="dropdown-item" id="bCerrarSe"><i class="feather icon-lock"></i> Cerrar Sesión</a></li>
							</ul>
						</div>
					</div>
				</li>
			</ul>
		</div>


	</header>
	<!-- [ Header ] end -->

	<!-- [ Main Content ] start -->
	<div class="pcoded-main-container">
		<div class="pcoded-content">
			<!-- [ breadcrumb ] start -->
			<div class="page-header">
				<div class="page-block">
					<div class="row align-items-center">
						<div class="col-md-12">
							<div class="page-header-title">
								<h5 class="m-b-10"></h5>
							</div>
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="javascript:void(0)"><i class="feather icon-home"></i></a></li>
								<li class="breadcrumb-item"><a href="javascript:void(0)" class="vistaTitulo">Productos</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<!-- [ breadcrumb ] end -->
			<!-- [ Main Content ] start -->
			<div class="row">
				<div class="col-12" id="verVista">
					#verVista#
				</div>
			</div>
			<!-- [ Main Content ] end -->
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="modalCaja" tabindex="-1" aria-labelledby="exampleModalFullscreenLabel" aria-modal="true" role="dialog" data-bs-focus="false">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
				<div class="row">
					<div class="col-12 text-end" style="padding-top: 15px;">
						<button type="button" class="btn btn-close" id="bCerrarVenCaja" aria-label="Close"></button>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<h5 class="text-muted" id="tituloCaja"></h5>
						</div>
						<div class="col-md-6 text-end">
							<button type="submit" class="btn btn-outline-secondary btn-sm bCorteCaja" id="bCorteCaja" style="z-index: 1;">Hacer corte <i class="fas fa-calculator"></i></button>
							<button type="submit" class="btn btn-outline-secondary btn-sm bDejarCaja" id="bDejarCaja" style="z-index: 1;">Dejar caja <i class="fas fa-times"></i></button>
						</div>
						<form id="formAgreProd" class="row" autocomplete="off">
							<div class="col-md-5 col-sm-7">
								<div class="input-group mb-3">
									<span class="input-group-text"><i class="fas fa-barcode"></i></span>
									<input type="text" class="form-control" placeholder="Código" name="barCodeV" id="barCodeV" autocomplete="off" required autofocus>
								</div>
							</div>
							<div class="col-md-4 col-sm-5 mb-3 d-grid">
								<button type="submit" class="btn btn-outline-danger" id="bEnterBarCode">Enter/Agregar Producto <i class="fas fa-check"></i></button>
							</div>
						</form>
						<div class="row" id="mosBotonesCajaUP">
							<div class="col-12 mb-3">
								<div class="btn-group btn-group-sm" role="group">
									<button type="button" class="btn btn-outline-secondary" id="bIntVarios"><b>F2</b> <i class="fas fa-clipboard"></i> Insert. Varios</button>
									<button type="button" class="btn btn-outline-secondary" id="bProdComun"><b>ALT + C</b> <i class="fas fa-file"></i> Prod. Común</button>
									<button type="button" class="btn btn-outline-secondary" id="bBuscarProd"><b>F10</b> <i class="fas fa-search"></i> Buscar</button>
									<button type="button" class="btn btn-outline-secondary" id="bMayoreo"><b>F9</b> <i class="fas fa-certificate"></i> Mayoreo</button>
									<button type="button" class="btn btn-outline-secondary" id="bEntrada"><b>F7</b> <i class="fas fa-plus"></i> Entrada</button>
									<button type="button" class="btn btn-outline-secondary" id="bSalida"><b>F8</b> <i class="fas fa-minus"></i> Salida</button>
									<button type="button" class="btn btn-outline-secondary" id="bHacerDescuento"><b>ALT + D</b> <i class="fas fa-percent"></i> Descuento</button>
									<button type="button" class="btn btn-outline-secondary" id="bQuitarProducto"><b>DEL</b> <i class="fas fa-trash"></i> Quitar Prod.</button>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12" id="mosTiketsCaja">
								<nav>
									<div class="nav nav-tabs" id="navtabTickets" role="tablist">
										<button class="nav-link active" id="tab_ticket_1" data-bs-toggle="tab" data-bs-target="#nav_ticket_1" type="button" role="tab" aria-selected="true">Ticket 1</button>
									</div>
								</nav>
								<div class="tab-content" id="nav-tabContent">
									<div class="tab-pane fade show active" id="nav_ticket_1" role="tabpanel" aria-labelledby="nav-home-tab">
										<div class="row">
											<div class="col-12 table-responsive" style="height: 40vh; background-color:#F0F0F0;" id="mostrarTablaProdCaja">
												<table class="table table-hover table-striped text-center" id="tablaCaja" style="width: 100%; font-size: 12px;">
													<thead>
														<tr>
															<th>Código</th>
															<th>Descripción</th>
															<th>Precio</th>
															<th>Cantidad</th>
															<th>Descuento</th>
															<th>Impuestos</th>
															<th>Total</th>
															<th>Existencia</th>
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
						<div class="row">
							<div class="col-12">
								<div class="row mb-3">
									<div class="col-md-10 col-sm-8">
										<div class="row">
											<div class="col-12">
												<h6 class="text-muted"><b class="cantidad" id="cantidadCajaProd">0</b> Productos en la venta actual</h6>
											</div>
										</div>
										<div class="row">
											<div class="col-md-9 col-sm-8">
												<div class="btn-group btn-group-sm" role="group">
													<button type="button" class="btn btn-outline-secondary" id="bTicketPendiente"><b>F3</b> <i class="fas fa-thumbtack"></i> Pendiente</button>
													<button type="button" class="btn btn-outline-secondary" id="bCambiarTicket"><b>F6</b> <i class="fas fa-exchange-alt"></i> Cambiar</button>
													<button type="button" class="btn btn-outline-secondary" id="bEliminarTicket"><b>ALT + E</b> <i class="fas fa-trash"></i> Eliminar</button>
													<button type="button" class="btn btn-outline-secondary" id="bAgregarClienteBusN" attrID="" attrDireccion="">Agregar un cliente <i class="fas fa-plus"></i></button>
													<div class="form-check form-switch ml-4 mt-1">
														<input type="checkbox" class="form-check-input" id="bCambiarTouch">
														<label class="form-check-label" for="bCambiarTouch">Touch</label>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-sm-4 d-grid">
												<button type="button" id="bCobrar" class="btn btn-secondary btn-lg">F12 <i class="fas fa-cart-plus"></i> Cobrar</button>
											</div>
										</div>
									</div>
									<div class="col-md-2 col-sm-4" style="background-color: #E9E9E9; color: blue; padding-top: 5px;">
										<p class="text-center" style="margin: 0; color: #606060;"><b>Subtotal: <span id="subtotalCaja" class="dinero">0</span></b></p>
										<p class="text-center" style="margin: 0;"><b>Descuento: <span id="mosDesGeP" class="porcentaje">0</span> (<span id="mosDesGeD" class="dinero">0</span>)</b></p>
										<h2 class="dinero text-center" style="margin: 0" id="totalCaja">0</h2>
										<p class="text-center" style="margin: 0;"><b>Total</b></p>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-12 text-end">
										<div class="btn-group btn-group-sm" role="group">
											<button type="button" class="btn btn-outline-secondary" id="bDescuentoGeneral"><b>ALT + G</b> % Descuento General</button>
											<!--<button type="button" class="btn btn-outline-secondary"><b>ALT + U</b> <i class="fas fa-print"></i> Reimprimir Último Ticket</button>
											<button type="button" class="btn btn-outline-secondary"><b>ALT + V</b> <i class="fas fa-file-alt"></i> Ventas y Devoluciones</button>-->
											<!--<button type="button" class="btn btn-outline-secondary" id="bVentasPendientes"><b>ALT + V</b> <i class="fas fa-file-alt"></i> Ventas Pendientes</button>-->
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12 text-end">
								<h6 class="text-muted" id="fechaHoraCaja"></h6>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MGranel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Cantidad</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div id="datosGranel">

					</div>
					<br>
					<form id="formGranel">
						<div class="input-group mb-3">
							<div class="form-floating">
								<input type="number" step="any" class="form-control" name="cantidadGranel" id="cantidadGranel" placeholder="Cantidad" value="1.00">
								<label for="floatingInput">Cantidad</label>
							</div>
							<button type="button" class="btn btn-outline-secondary" type="button" id="bOptenerPeso">Pesar <i class="fa-solid fa-weight-scale"></i></button>
							<button type="button" class="btn btn-outline-secondary" type="button" id="bConectarBascula">Conectar Bascula</button>
						</div>
						<div class="form-floating mb-3">
							<input type="number" step="any" class="form-control" name="importeGranel" id="importeGranel" placeholder="Importe">
							<label for="floatingInput">Importe</label>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-primary" id="bAgregarGranel">Agregar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MIntVarios" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Varios Productos</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formIntVarios">
						<div class="input-group mb-3">
							<span class="input-group-text"><i class="fas fa-barcode"></i></span>
							<input type="text" class="form-control" placeholder="Código" name="barCodeIntVatios" id="barCodeIntVatios">
						</div>
						<div class="form-floating mb-3">
							<input type="number" step="any" class="form-control" name="cantidadIntVatios" id="cantidadIntVatios" placeholder="Cantidad">
							<label for="floatingInput">Cantidad</label>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-primary" id="bAgregarVarios">Agregar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MProdComun" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Prducto Común</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formProdComun">
						<div class="form-floating mb-3">
							<input type="text" class="form-control" name="descripcionProdComun" id="descripcionProdComun" placeholder="Descripción">
							<label for="floatingInput">Descripción</label>
						</div>
						<div class="form-floating mb-3">
							<input type="number" step="any" class="form-control" name="cantidadProdComun" id="cantidadProdComun" placeholder="Cantidad" value="1.00">
							<label for="floatingInput">Cantidad</label>
						</div>
						<div class="form-floating mb-3">
							<input type="number" step="any" class="form-control" name="precioProdComun" id="precioProdComun" placeholder="Precio">
							<label for="floatingInput">Precio</label>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-primary" id="bAgregarProdComun">Agregar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MBuscarProd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Buscar</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12 text-end">
							<button class="btn btn-secondary btn-sm" id="bRecargarBusquedaProductos" type="button"><i class="fa-solid fa-rotate-right"></i></button>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-12 table-responsive">
							<table class="table table-striped text-center myDataTable" id="tablaBuscarProductos" width="100%">
								<thead>
									<tr>
										<th>Descripción</th>
										<th>Código</th>
										<th>Clase</th>
										<th>Precio</th>
										<th>Precio Mayoreo</th>
										<th>Impuestos</th>
										<th>Existencia</th>
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

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MEntrada" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Entrada</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formEntrada">
						<div class="form-floating mb-3">
							<input type="number" step="any" class="form-control" name="montoEntrada" id="montoEntrada" placeholder="Monto">
							<label for="floatingInput">Monto</label>
						</div>
						<div class="form-floating">
							<textarea class="form-control" id="descripcionEntrada" name="descripcionEntrada" placeholder="Descripción" style="height: 100px"></textarea>
							<label for="floatingTextarea2">Descripción</label>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-primary" id="bGuardarEntrada">Guardar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MSalida" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Salida</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formSalida">
						<div class="form-floating mb-3">
							<input type="number" step="any" class="form-control" name="montoSalida" id="montoSalida" placeholder="Monto">
							<label for="floatingInput">Monto</label>
						</div>
						<div class="form-floating">
							<textarea class="form-control" id="descripcionSalida" name="descripcionSalida" placeholder="Descripción" style="height: 100px"></textarea>
							<label for="floatingTextarea2">Descripción</label>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-primary" id="bGuardarSalida">Guardar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MDescuento" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Descuento</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p class="h3 mb-3 text-center" id="descripcionDescuento"></p>
					<p class="h3 mb-3 text-center" id="totalDescuento"></p>
					<form id="formDescuento">
						<div class="input-group mb-3">
							<input value="0" step="any" type="number" class="form-control" id="porcentajeDescuento" placeholder="Porcentaje" aria-label="Porcentaje">
							<span class="input-group-text">% = $</span>
							<input value="0" step="any" type="number" class="form-control" id="cantidadDescuento" placeholder="Cantidad" aria-label="Cantidad">
						</div>
						<p class="h3 text-center">Restante: <span id="restanteDescuento" class="dinero h3">0</span></p>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-primary" id="bGuardarDescuento">Guardar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MPendiente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Ticket Pendiente</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formPendiente">
						<div class="form-floating mb-3">
							<input type="text" class="form-control" name="nombrePendiente" id="nombrePendiente" placeholder="Nombre">
							<label for="nombrePendiente">Nombre Ticket</label>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-primary" id="bGuardarPendiente">Guardar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MCambiarTicket" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Cambiar Ticket</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" id="cambiarTicketBody">
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-primary" id="bTabCambiarTicket">Cambiar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MCobrar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Cobrar</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formCobrar" class="row">
						<div class="col-12 mb-3" id="mosClienteCobrarCaja">
							<label>Cliente</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="clienteCobrar" name="clienteCobrar" placeholder="Publico en general" attrID="" attrDireccion="" disabled>
								<button type="button" class="btn btn-outline-secondary" id="bBuscarClienteCobrar">F2 <i class="fas fa-search"></i></button>
								<button type="button" class="btn btn-outline-secondary" id="bQuitarClienteCobrar"><i class="fas fa-times"></i></button>
							</div>
						</div>
						<div class="col-12 text-center mb-3">
							<h1>Total: <span class="dinero" id="totalCobrar"></span></h1>
						</div>
						<div class="col-12 mb-3">
							<div class="form-floating">
								<input type="number" step="any" class="form-control form-control-lg" id="totalPagoCobrar" name="totalPagoCobrar">
								<label>Pagó con</label>
							</div>
						</div>
						<div class="col-12 text-center mb-3">
							<h1>Cambio: <span class="dinero" id="totalCambio"></span></h1>
						</div>
						<div class="col-12 mb-3">
							<div class="form-floating">
								<select class="form-select form-select-lg" name="metodoPago" id="metodoPago">
									<option value="Efectivo" selected>Efectivo</option>
									<option value="Deposito">Deposito</option>
									<option value="Cheque">Cheque</option>
									<option value="Transferencia">Transferencia</option>
									<option value="Tarjeta">Tarjeta</option>
									<option value="Pago Online">Pago Online</option>
									<option value="Crédito">Crédito</option>
								</select>
								<label>Metodo de pagó</label>
							</div>
						</div>
						<div class="col-12 mb-3">
							<div class="form-floating">
								<textarea class="form-control" placeholder="Detalles" id="detallesCobrar" name="detallesCobrar" style="height: 80px"></textarea>
								<label>Detalles</label>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger btn-lg" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
					<button type="button" class="btn btn-secondary btn-lg" id="bSoloCobrar">F4 - Solo Completar <i class="fas fa-check"></i></button>
					<button type="button" class="btn btn-primary btn-lg" id="bCobrarImprimir">F1 - Completar e imprimir<i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MCorteCaja" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Balance</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 ">
							<h3>Detalles Balance</h3>
							<div class="row">
								<div class="col-12 table-responsive">
									<table id="tablaDetallesBalance" class="table table0hover table-striped table-bordered text-center" style="font-size: 18px;" width="100%">
										<thead>
											<tr>
												<th>Tipo</th>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<h5>Fecha apertura: <span id="fechaApertura"><span></h5>
							<h5>Fecha cierre: <span id="fechaCierre"><span></h5>
							<h5>Usuario apertura: <span id="usuarioApertura"><span></h5>
							<h3>Balance: <span class="dinero" id="balanceCaja"></span></h3>
							<div class="form-floating">
								<input type="number" step="any" class="form-control form-control-lg" id="montoCorte" name="montoCorte">
								<label>Monto que hay en caja</label>
							</div>
							<h3>Restante: <span class="dinero" id="restanteCaja">0</span></h3>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-6">
							<h3>Ventas por Usuario</h3>
							<div class="row">
								<div class="col-12 table-responsive">
									<table id="tablaTotalVentasUsuarios" class="table table-hover table-striped table-bordered table-fixed text-center" style="font-size: 18px;" width="100%">
										<thead>
											<tr>
												<th>Usuario</th>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<h3>Ventas</h3>
							<div class="row">
								<div class="col-12 table-responsive">
									<table id="tablaTotalVentas" class="table table-hover table-striped table-bordered table-fixed text-center" style="font-size: 18px;" width="100%">
										<thead>
											<tr>
												<th>Tipo Pago</th>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-12">
							<p class="h3">Movimientos</p>
							<div class="row">
								<div class="col-12 table-responsive">
									<table id="tablaMovimientos" class="table table-hover table-striped table-bordered text-center" style="font-size: 18px;" width="100%">
										<thead>
											<tr>
												<th>Fecha</th>
												<th>Tipo</th>
												<th>Descripción</th>
												<th>Monto</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-6">
							<h3>Pagos Compras</h3>
							<div class="row">
								<div class="col-12 table-responsive">
									<table id="tablaTotalPagosCompras" class="table table-hover table-striped table-bordered table-fixed text-center" style="font-size: 18px;" width="100%">
										<thead>
											<tr>
												<th>Tipo Pago</th>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<h3>Pagos Ventas</h3>
							<div class="row">
								<div class="col-12 table-responsive">
									<table id="tablaTotalPagosVentas" class="table table-hover table-striped table-bordered text-center" style="font-size: 18px;" width="100%">
										<thead>
											<tr>
												<th>Tipo Pago</th>
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
					<hr>
					<div class="row">
						<div class="col-md-6">
							<h3>Detalles Pagos Ventas</h3>
							<div class="row">
								<div class="col-12 table-responsive">
									<table id="tablaDetallesPagosVentas" class="table table-hover table-striped table-bordered table-fixed text-center" style="font-size: 18px;" width="100%">
										<thead>
											<tr>
												<th>Folio Venta</th>
												<th>Cliente</th>
												<th>Monto</th>
												<th>Tipo de pago</th>
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
				<div class="modal-footer text-center">
					<button type="reset" class="btn btn-outline-danger btn-lg" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
					<!--<button type="button" class="btn btn-secondary btn-lg" id="bHacerCorte">Hacer Corte <i class="fas fa-check"></i></button>-->
					<button type="button" class="btn btn-primary btn-lg" id="bHacerCorteImprimir">Hacer Corte <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MVentasPendientes" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
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

	<!-- ///////////////////////////Modal////////////////////////////// -->
	<div class="modal fade" id="MBuscarCliente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Buscar</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12 text-end">
							<button class="btn btn-secondary btn-sm" id="bRecargarBusquedaClientes" type="button"><i class="fa-solid fa-rotate-right"></i></button>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-12 table-responsive">
							<table class="table table-striped text-center myDataTable" id="tablaBuscarClientes" width="100%">
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Tipo</th>
										<th>Contacto</th>
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

	<!--/////////////////////////////////Modal////////////////////////////////////-->
	<div class="modal fade" id="modalClienteBus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Cliente</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="formCliente">
					<div class="modal-body">
						<div class="row">
							<div class="form-group col-md-6 mb-3" style="position: relative;">
								<label>Teléfono</label>
								<input type="text" class="form-control" id="telefonoClienteBus" name="telefonoClienteBus" autocomplete="off">
								<div class="listaInput oculto" id="telefonosClientesBus"></div>
							</div>
							<div class="form-group col-md-6 mb-3" style="position: relative;">
								<label>Nombre</label>
								<input type="text" class="form-control" id="nombreClienteBus" name="nombreClienteBus" autocomplete="off">
								<div class="listaInput oculto" id="nombresClientesBus"></div>
							</div>
						</div>
						<div class="row oculto" id="seleccionDomicilioBus">
							<div class="form-group col-md-6 mb-4">
								<label>Selecciona un domicilio</label>
								<select id="domicilioClienteBus" name="domicilioClienteBus" class="form-control" multiple="true">

								</select>
							</div>
						</div>
						<hr>
						<h5 id="tituloDomiBus" attrID="">Domicilio</h5>
						<div class="row">
							<div class="form-group col-md-6 mb-3">
								<label>Calle</label>
								<input type="text" class="form-control" id="calleClienteBus" name="calleClienteBus">
							</div>
							<div class="form-group col-md-6 mb-3">
								<label>No. Exterior</label>
								<input type="text" class="form-control" id="noExteriorClienteBus" name="noExteriorClienteBus">
							</div>
							<div class="form-group col-md-6 mb-3">
								<label>No. Interior</label>
								<input type="text" class="form-control" id="noInteriorClienteBus" name="noInteriorClienteBus">
							</div>
							<div class="form-group col-md-6 mb-3">
								<label>Código postal</label>
								<input type="text" class="form-control" id="cpClienteBus" name="cpClienteBus">
							</div>
							<div class="form-group col-md-6 mb-3">
								<label>Colonia</label>
								<input type="text" class="form-control" id="coloniaClienteBus" name="coloniaClienteBus">
							</div>
							<div class="form-group col-md-6 mb-3">
								<label>Ciudad</label>
								<input type="text" class="form-control" id="ciudadClienteBus" name="ciudadClienteBus">
							</div>
							<div class="form-group col-md-6 mb-3">
								<label>Estado</label>
								<input type="text" class="form-control" id="estadoClienteBus" name="estadoClienteBus">
							</div>
							<div class="form-group col-md-6 mb-3">
								<label>País</label>
								<input type="text" class="form-control" id="paisClienteBus" name="paisClienteBus">
							</div>
							<div class="form-group col-12 mb-3">
								<label>Detalles</label>
								<textarea name="detallesClienteBus" id="detallesClienteBus" rows="3" class="form-control">

	                            </textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-danger btn-lg" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
						<button type="submit" class="btn btn-primary btn-lg" id="bAceptarClienteBus"><i class="fas fa-check"></i> Aceptar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="printer" style="display:none"></div>

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
	<script src="vistas/assets/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
	<script src="vistas/assets/plugins/amcharts5/index.js"></script>
	<script src="vistas/assets/plugins/amcharts5/xy.js"></script>
	<script src="vistas/assets/plugins/amcharts5/themes/Animated.js"></script>
	<script src="vistas/assets/plugins/imask.js"></script>
	<script src="vistas/assets/plugins/leaflet/leaflet.js"></script>

	<script src="vistas/assets/js/main.js"></script>
	<script src="vistas/assets/js/sucursales.js"></script>
	<script src="vistas/assets/js/productos.js"></script>
	<script src="vistas/assets/js/inventario.js"></script>
	<script src="vistas/assets/js/clientes.js"></script>
	<script src="vistas/assets/js/ventas.js"></script>
	<script src="vistas/assets/js/cajas.js"></script>
	<script src="vistas/assets/js/caja.js"></script>
	<script src="vistas/assets/js/proveedores.js"></script>
	<script src="vistas/assets/js/compras.js"></script>
	<script src="vistas/assets/js/hacerCompra.js"></script>
	<script src="vistas/assets/js/usuarios.js"></script>
	<script src="vistas/assets/js/reportes.js"></script>
	<script src="vistas/assets/js/configuracion.js"></script>
	<script src="vistas/assets/js/cuenta.js"></script>
	<script src="vistas/assets/js/gastos.js"></script>
	<script src="vistas/assets/js/clasificaciones.js"></script>
	<script src="vistas/assets/js/traslados.js"></script>
	<script src="vistas/assets/js/impuestos.js"></script>
	<script src="vistas/assets/js/config_facturacion.js"></script>
	<script src="vistas/assets/js/facturas.js"></script>
</body>

</html>