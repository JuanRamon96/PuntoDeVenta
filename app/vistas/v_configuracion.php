<div class="vt-perfil">
    <div class="row" style="margin-top: -40px;">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_configuracion" titulo="Configuración" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
        </div>
    </div>

    <div class="card vt-perfil-hero mb-3">
        <div class="vt-perfil-cover"></div>
        <div class="vt-perfil-identity">
            <div class="vt-perfil-avatar">
                #fotoPerfil#
                <label class="vt-perfil-avatar-btn vt-perfil-avatar-edit" for="fotoPerfil" title="Cambiar foto">
                    <i class="fas fa-camera"></i>
                </label>
                <button type="button" class="vt-perfil-avatar-btn vt-perfil-avatar-remove" id="bQuitarFoto" title="Quitar foto">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="vt-perfil-meta">
                <h3 class="vt-perfil-name">#nombrePerfil# #primerApellidoPerfil# #segundoApellidoPerfil#</h3>
                <p class="vt-perfil-email mb-0"><i class="fas fa-envelope me-2"></i>#correo#</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="vt-dashboard-section mb-3"><i class="fas fa-id-card me-2"></i>Datos personales</h5>
                    <form id="formPerfilUsuario" novalidate="novalidate">
                        <input type="hidden" name="quitarFoto" id="quitarFoto" value="0">
                        <input type="file" class="d-none" id="fotoPerfil" name="fotoPerfil" accept="image/png,image/jpeg,image/jpg,image/svg+xml">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" name="nombrePerfil" id="nombrePerfil" placeholder="Nombre" value="#nombrePerfil#">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Primer apellido</label>
                                    <input type="text" class="form-control" name="primerApellidoPerfil" id="primerApellidoPerfil" placeholder="Primer Apellido" value="#primerApellidoPerfil#">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Segundo apellido</label>
                                    <input type="text" class="form-control" name="segundoApellidoPerfil" id="segundoApellidoPerfil" placeholder="Segundo Apellido" value="#segundoApellidoPerfil#">
                                </div>
                            </div>
                        </div>
                        <p class="vt-perfil-hint mb-3">La foto se cambia desde el recuadro de arriba. Recuerda guardar para aplicar los cambios.</p>
                        <div class="d-flex justify-content-end">
                            <button type="submit" disabled="true" id="bCambiarFoto" class="btn btn-danger">Guardar cambios <i class="fas fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-body">
                    <form id="formCorreo" novalidate="novalidate">
                        <h5 class="vt-dashboard-section mb-3"><i class="fas fa-at me-2"></i>Correo de acceso</h5>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="email" class="form-control valid" name="correoPerfil" id="correoPerfil" placeholder="Email" value="#correo#" aria-invalid="false">
                                <label>Email</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <div class="form-floating flex-grow-1">
                                    <input type="password" class="form-control" name="contrasenaPerfil" id="contrasenaPerfil" placeholder="Contraseña">
                                    <label>Contraseña actual</label>
                                </div>
                                <button type="button" class="btn btn-outline-secondary" id="bContraPerfil"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Guardar <i class="fas fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form id="formContrasena" novalidate="novalidate">
                        <h5 class="vt-dashboard-section mb-3"><i class="fas fa-lock me-2"></i>Contraseña</h5>
                        <div class="mb-3">
                            <div class="input-group">
                                <div class="form-floating flex-grow-1">
                                    <input type="password" class="form-control" name="contrasenaActualPerfil" id="contrasenaActualPerfil" placeholder="Contraseña Actual">
                                    <label>Contraseña actual</label>
                                </div>
                                <button type="button" class="btn btn-outline-secondary" id="bContraCamCon"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="password" class="form-control" name="nuevaContrasenaPerfil" id="nuevaContrasenaPerfil" placeholder="Nueva Contraseña">
                                <label>Nueva contraseña</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <div class="form-floating flex-grow-1">
                                    <input type="password" class="form-control" name="repiteContrasenaPerfil" id="repiteContrasenaPerfil" placeholder="Repite Contraseña">
                                    <label>Repite contraseña</label>
                                </div>
                                <button type="button" class="btn btn-outline-secondary" id="bContraCamDos"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Guardar <i class="fas fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
