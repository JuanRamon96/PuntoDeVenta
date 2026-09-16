<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>VentasTool | Login</title>
	<meta name="description" content="" />
	<meta name="keywords" content="">
	<meta name="author" content="Phoenixcoded" />
	<!-- Favicon icon -->
	<link rel="shortcut icon" href="vistas/assets/images/logos/v.png">


	<link rel="stylesheet" href="vistas/assets/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="vistas/assets/plugins/fontawesome/css/all.css">
	<link rel="stylesheet" href="vistas/assets/css/style.css">
	<link rel="stylesheet" href="vistas/assets/css/theme-ventastool.css?v=16">
</head>

<body>
	<div class="vt-login-page">
		<div class="vt-login-brand-panel">
			<div class="vt-login-logo-box">
				<img src="vistas/assets/images/logos/largo.png" alt="VentasTool">
			</div>
			<h2>Tu negocio en buenas manos</h2>
			<p class="vt-login-tagline">Vende, controla tu inventario, factura y lleva todo tu negocio desde un solo lugar.</p>
			<ul class="vt-login-features">
				<li><i class="fas fa-cart-shopping"></i> Ventas</li>
				<li><i class="fas fa-box"></i> Inventario</li>
				<li><i class="fas fa-file-invoice"></i> Facturación CFDI</li>
				<li><i class="fas fa-users"></i> Clientes</li>
				<li><i class="fas fa-chart-line"></i> Reportes</li>
				<li><i class="fas fa-cash-register"></i> Cajas ilimitadas</li>
			</ul>
		</div>
		<div class="vt-login-form-panel">
			<div class="auth-content">
				<div class="card">
					<div class="row align-items-center text-center">
						<div class="col-md-12">
							<form id="formLogin" class="card-body">
								<p class="vt-login-welcome">Bienvenido de nuevo</p>
								<p class="vt-login-sub">Ingresa tus datos para acceder a tu cuenta</p>
								<div id="mensaAV"></div>
								<div class="form-floating mb-3">
									<input type="email" class="form-control" id="email" name="email" placeholder="Email">
									<label for="floatingInput">Email</label>
								</div>
								<div class="input-group mb-3">
									<div class="form-floating">
										<input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña">
										<label for="floatingInput">Contraseña</label>
									</div>
									<button type="button" class="btn btn-outline-secondary" id="bTogglePass" tabindex="-1" title="Mostrar/Ocultar contraseña">
										<i class="fas fa-eye" id="iconPassMostrar"></i>
										<i class="fas fa-eye-slash d-none" id="iconPassOcultar"></i>
									</button>
								</div>
								<div class="text-end">
									<a href="#" class="small" data-bs-toggle="modal" data-bs-target="#modalOlvidoContra">
										¿Olvidaste tu contraseña?
									</a>
								</div>
								<br>
								<div class="form-floating mb-3 d-grid">
									<button type="submit" class="btn btn-danger btn-lg" id="bIngresarLogin">Ingresar <i class="fas fa-check"></i></button>
								</div>
								<h6 class="mb-0 text-muted">¿No tienes cuenta? <a href="../#precios" class="f-w-600">Registrate aquí</a></h6>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- ///////////////////////////Modal olvido contrasena/////////////////////////////////// -->
	<div class="modal fade" id="modalOlvidoContra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Recuperar Contraseña</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="formReContra" novalidate="novalidate">
					<div class="modal-body">
						<div class="mb-3">
							<label for="reEmail" class="form-label">Ingresa el correo de tu cuenta para verificar su existencia</label>
							<input type="email" id="reEmail" name="reEmail" class="form-control" placeholder="nombre@ejemplo.com">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="bCanReCo">
							<i class="gd-close text-white"></i> Cancelar
						</button>
						<button type="submit" class="btn btn-primary" id="bAceReCo">
							<i class="gd-check text-white"></i> Aceptar
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="vistas/assets/js/jquery-3.6.1.min.js"></script>
	<script src="vistas/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<script src="vistas/assets/plugins/fontawesome/js/all.min.js"></script>
	<script src="vistas/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
	<script src="vistas/assets/plugins/sweetalert/dist/sweetalert2.all.min.js"></script>
	<script src="vistas/assets/js/login.js"></script>
</body>

</html>