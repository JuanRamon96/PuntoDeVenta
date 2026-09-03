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
 				<div class="alert alert-warning alert-left-bordered border-warning alert-dismissible d-flex align-items-center p-md-4 mb-2 fade show" role="alert">
 					<i class="gd-flag-alt-2 icon-text text-warning mr-2"></i>
 					<p class="mb-0">
 						<strong>¡Has solicitado recientemente el cambio de contraseña!</strong> Para poder ingresar a tu cuenta de forma normal, debes ingresar una nueva contraseña.
 					</p>
 					<button type="button" class="close" aria-label="Close" data-dismiss="alert">
 						<i class="gd-close icon-text icon-text-xs" aria-hidden="true"></i>
 					</button>
 				</div>

 				<div class="card mb-4">
 					<div class="card-body">
 						<h4 class="card-title">CAMBIO DE CONTRASEÑA</h4>
 						<form id="formTemporal" novalidate="novalidate">
 							<div class="form-group">
 								<label for="">Contraseña</label>
 								<input class="form-control contras" type="password" name="contrasena" id="contrasena">
 								<small class="form-text text-muted">Introduce una contraseña.</small>
 							</div>
 							<div class="form-group">
 								<label for="">Confirmar contraseña</label>
 								<input class="form-control contras" type="password" name="confirmar" id="confirmar">
 								<small class="form-text text-muted">Vueleve a introducir la contraseña.</small>
 							</div>
 							<div class="form-group text-right">
 								<button type="button" class="btn btn-light btn-sm bVerContras" padre="formTemporal"><i class="fa fa-eye"></i></button>
 							</div>
 							<div class="form-group text-end">
 								<button type="submit" class="btn btn-danger btn-lg">Guardar <i class="fa fa-save"></i></button>
 							</div>
 						</form>
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

 	<script src="vistas/assets/js/main.js"></script>
	<script src="vistas/assets/js/temporal.js"></script>
 </body>

 </html>