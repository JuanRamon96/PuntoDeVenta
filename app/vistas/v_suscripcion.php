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

 	<!-- [ Header ] start -->
 	<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">
 		<div class="collapse navbar-collapse">
 			<ul class="navbar-nav ml-auto">
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
 	<div class="container">
 		<div class="row justify-content-center">
 			<div class="col-md-8">
 				<div class="row">
 					<div class="col-12 text-center">
 						<h2>Lo sentimos, tu suscripción ha vencido. Para seguir disfrutando de todos los beneficios del sistema, elige uno de nuestros planes y renueva tu acceso.</h2>
 					</div>
 				</div>
 				<div class="row mb-4">
 					<div class="col-12 text-center">
 						<p class="m-0 fs-6">
 							Si tienes dudas, deseas realizar tu pago mediante <strong>transferencia directa</strong>
 							o necesitas <strong>factura</strong>, ponte en contacto con soporte.
 						</p>
 						<div class="alert alert-warning text-center" role="alert">
 							<i class="fas fa-exclamation-triangle"></i> Los pagos con factura tienen un cargo adicional del 16% de IVA.
 						</div>
 						<p class="m-0 fs-6">
 							<a href="https://wa.me/523481167983?text=Hola%2C%20quiero%20información%20sobre%20Ventas%20Tool"
 								target="_blank" class="btn btn-success">
 								<i class="fab fa-whatsapp"></i> Contactar a soporte
 							</a>
 						</p>
 					</div>
 				</div>
 				<div class="row text-center">
 					<div class="col-lg-6">
 						<div class="card mb-4 ticket-precio">
 							<div class="card-body">
 								<h3 class="card-title">Plan Mensual</h3>
 								<div>
 									<h1>$ 99.00 MXN</h1>
 									<ul class="listaPlan">
 										<li><span class="chk">✓</span> <b>Cajas registradoras</b> ilimitadas</li>
 										<li><span class="chk">✓</span> <b>Productos e inventario</b> sin límite</li>
 										<li><span class="chk">✓</span> <b>Clientes y compras</b> incluidos</li>
 										<li><span class="chk">✓</span> <b>Facturación CFDI 4.0</b> con timbrado</li>
 										<li><span class="chk">✓</span> <b>Usuarios</b> con permisos por rol</li>
 										<li><span class="chk">✓</span> <b>Reportes</b> por caja y vendedor</li>
 									</ul>
 									<div id="paypal-button-container1"></div>
 								</div>
 							</div>
 						</div>
 					</div>
 					<div class="col-lg-6">
 						<div class="card mb-4 ticket-precio">
 							<div class="card-body">
 								<h3 class="card-title">Plan Anual</h3>
 								<h5>Obtienes un 20% de descuento ($79.00 mxn al mes)</h5>
 								<div>
 									<h1>$ 948.00 MXN</h1>
 									<ul class="listaPlan">
 										<li><span class="chk">✓</span> <b>Cajas registradoras</b> ilimitadas</li>
 										<li><span class="chk">✓</span> <b>Productos e inventario</b> sin límite</li>
 										<li><span class="chk">✓</span> <b>Clientes y compras</b> incluidos</li>
 										<li><span class="chk">✓</span> <b>Facturación CFDI 4.0</b> con timbrado</li>
 										<li><span class="chk">✓</span> <b>Usuarios</b> con permisos por rol</li>
 										<li><span class="chk">✓</span> <b>Reportes</b> por caja y vendedor</li>
 									</ul>
 									<div id="paypal-button-container2"></div>
 								</div>
 							</div>
 						</div>
 					</div>
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
 	<script src="vistas/assets/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
 	<script src="vistas/assets/plugins/amcharts5/index.js"></script>
 	<script src="vistas/assets/plugins/amcharts5/xy.js"></script>
 	<script src="vistas/assets/plugins/amcharts5/themes/Animated.js"></script>
 	<script src="vistas/assets/plugins/imask.js"></script>
 	<script src="vistas/assets/plugins/leaflet/leaflet.js"></script>

	<!--Sanbox
    <script src="https://www.paypal.com/sdk/js?client-id=AT_d5bAmVYT56qIXqpMocK9-q3JF-FAv8LOMphifhNnIKA1wBjRR3xyIeUWXw07j6ofGxAF9BFA7SaZj&currency=MXN"></script>-->
 	<!--Live-->
 	<script src="https://www.paypal.com/sdk/js?client-id=ARSS_P46LzNOJiIj32nIVdBiCqiWZJiwtlA8AU3q9e6nZbVDEI4R1C8mciFIxsM1xsgcQz6rNPiElJsC&currency=MXN"></script>

 	<script src="vistas/assets/js/main.js"></script>
 </body>

 </html>