<div class="row" style="margin-top: -40px;">
    <div class="col-12 text-end">
        <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_configuracion" titulo="Configuración" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Configuración</h4>
            </div>
            <div class="card-body">
                <!--<div class="row">
                        <div class="col-12">
                            <h4 class="card-title text-center"><a href="controladores/respaldo.php" target='_blank' class="btn btn-outline-info" style="font-size: 18px;">Crear Respaldo de Mi Base de Datos <i class="fas fa-database"></i></a></h4>
                        </div>
                    </div>
                <br>-->
                <form id="formPerfilUsuario" novalidate="novalidate">
                    <div class="row">
                        <div class="col-12 text-center">
                            #fotoPerfil#
                        </div>
                    </div>
                    <br>
                    <div class="row justify-content-md-center">
                        <div class="col-md-6 col-sm-10 col-xs-10 mb-3">
                            <input type="file" class="form-control form-control-sm" id="fotoPerfil" name="fotoPerfil">
                        </div>
                        <div class="col-2 mb-3">
                            <button type="button" class="btn btn-sm btn-danger" id="bQuitarFoto"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" class="form-control" name="nombrePerfil" id="nombrePerfil" placeholder="Nombre" value="#nombrePerfil#">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label>Primer Apellido</label>
                                <input type="text" class="form-control" name="primerApellidoPerfil" id="primerApellidoPerfil" placeholder="Primer Apellido" value="#primerApellidoPerfil#">
                            </div>
                        </div> 
                        <div class="col-md-4 mb-3">
                            <div class="form-group">
                                <label>Segundo Apellido</label>
                                <input type="text" class="form-control" name="segundoApellidoPerfil" id="segundoApellidoPerfil" placeholder="Segundo Apellido" value="#segundoApellidoPerfil#">
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-6 d-grid text-center">
                            <button type="submit" disabled="true" id="bCambiarFoto" class="btn btn-primary">Guardar <i class="fas fa-save"></i></button>
                        </div>
                    </div>
                </form>
                <br>
                <hr>
                <br>
                <div class="row">
                    <div class="col-12">
                        <form id="formCorreo" novalidate="novalidate">
                            <h5 class="text-muted">Cambiar Email</h5>
                            <br>
                            <div class="row justify-content-md-center">
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="email" class="form-control valid" name="correoPerfil" id="correoPerfil" placeholder="Email" value="#correo#" aria-invalid="false">
                                        <label>Email</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-md-center">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <div class="form-floating flex-grow-1">
                                            <input type="password" class="form-control" name="contrasenaPerfil" id="contrasenaPerfil" placeholder="Contraseña">
                                            <label>Contraseña</label>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary" id="bContraPerfil"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-md-center">
                                <div class="col-6 d-grid">
                                    <button type="submit" class="btn btn-primary">Guardar <i class="fas fa-save"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <form id="formContrasena" novalidate="novalidate">
                            <h5 class="text-muted">Cambiar Contraseña</h5>
                            <br>
                            <div class="row justify-content-md-center">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <div class="form-floating flex-grow-1">
                                            <input type="password" class="form-control" name="contrasenaActualPerfil" id="contrasenaActualPerfil" placeholder="Contraseña Actual">
                                            <label>Contraseña Actual</label>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary" id="bContraCamCon"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-md-center">
                                <div class="col-md-4 mb-3">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" name="nuevaContrasenaPerfil" id="nuevaContrasenaPerfil" placeholder="Nueva Contraseña">
                                        <label>Nueva Contraseña</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" name="repiteContrasenaPerfil" id="repiteContrasenaPerfil" placeholder="Repite Contraseña">
                                        <label>Repite Contraseña</label>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-top: 8px;">
                                    <button type="button" class="btn btn-outline-secondary" id="bContraCamDos"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <div class="row justify-content-md-center">
                                <div class="col-6 d-grid">
                                    <button type="submit" class="btn btn-primary">Guardar <i class="fas fa-save"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>