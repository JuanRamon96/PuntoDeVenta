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
	<link rel="stylesheet" href="vistas/assets/css/style.css">
</head>
<body>
	<div class="auth-wrapper">
		<div class="auth-content">
			<div class="card">
				<div class="row align-items-center text-center">
					<div class="col-md-12">
						<form id="formLogin" class="card-body">
							<img src="vistas/assets/images/logos/mediano.png" class="img-fluid mb-4">
							<div id="mensaAV"></div>
							<div class="form-floating mb-3">
							  	<input type="email" class="form-control" id="email" name="email" placeholder="Email">
							  	<label for="floatingInput">Email</label>
							</div>
							<div class="form-floating mb-3">
							  	<input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña">
							  	<label for="floatingInput">Contraseña</label>
							</div>
							<br>
							<div class="form-floating mb-3 d-grid">
								<button type="submit" class="btn btn-danger btn-lg" id="bIngresarLogin">Ingresar <i class="fas fa-check"></i></button>
							</div>	
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

<script src="vistas/assets/js/jquery-3.6.1.min.js"></script>
<script src="vistas/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="vistas/assets/plugins/fontawesome/js/all.min.js"></script>
<script src="vistas/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="vistas/assets/js/login.js"></script>
</body>
</html>
