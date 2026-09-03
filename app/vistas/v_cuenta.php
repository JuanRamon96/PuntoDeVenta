<div class="row" style="margin-top: -40px;">
    <div class="col-12 text-end">
        <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_cuenta" titulo="Cuenta" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
    </div>
</div>
<br>
<div class="row justify-content-center">
    <div class="col-12">
        <div class="row align-items-center mb-2">
            <div class="col">
                <h2 class="h5 page-title">Cuenta</h2>
            </div>
            <div class="col-auto">
                <div class="form-group">
                    <button type="button" class="btn btn-sm cargarVista" carga="v_configuracion"><span class="fe fe-refresh-ccw fe-16 text-muted"></span></button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <form id="formConfiguracionNegocio" novalidate="novalidate" style="padding-top: 20px;">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <input type="file" id="fotoNegocio" name="fotoNegocio" style="display: none;" accept="image/*">
                                    <p class="text-right">
                                        <button type="button" class="btn btn-icon btn-info rounded-circle btn-sm botonesImagenes" id="bModificarFotoNegocio" title="Modificar">
                                            <i class="fas fa-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-danger rounded-circle btn-sm botonesImagenes oculto" id="bCancelarFotoNegocio" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </p>
                                    <div id="mosFotoNegocio" style="background-image: url('#fotoNegocio#'); width: 150px; height: 150px; background-size: cover; background-position: center; margin: 0 auto;"></div>
                                </div>
                            </div>  
                            <br><br>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label>Nombre de tu negocio</label>
                                        <input type="text" class="form-control" name="nombreNegocio" id="nombreNegocio" placeholder="Nombre" value="#nombreNegocio#">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" class="form-control" name="telNegocio" id="telNegocio" placeholder="Teléfono" value="#telefonoNegocio#">
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-group">
                                        <label>Domicilio</label>
                                        <input type="text" class="form-control" name="domicilioNegocio" id="domicilioNegocio" placeholder="Domicilio" value="#domicilioNegocio#">
                                    </div>
                                </div> 
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label>Porcentaje de suma al precio</label>
                                        <input type="number" step="any" class="form-control" name="porcentajeNegocio" id="porcentajeNegocio" placeholder="Porcentaje" value="#porcentajeNegocio#">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">Guardar <i class="fas fa-save"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>      
                </div>
            </div>          
        </div>
    </div>
</div>  