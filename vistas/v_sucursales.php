<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_sucursales" titulo="Sucursales" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
  </div>
</div>
<br>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 text-end">
            #bAgregar#
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaSucursales" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Nombre</th>
                  <th orden="No">Domicilio</th>
                  <th orden="No">Contacto</th>
                  <th orden="No">Acciones</th>
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

<!-- ///////////////////////////Modal////////////////////////////// -->
<div id="modalSucursales" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sucursal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formSucursales">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="nombreSucursal" id="nombreSucursal" placeholder="Nombre">
                <label>Nombre</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="tel" class="form-control" name="telefonoSucursal" id="telefonoSucursal" placeholder="Teléfono">
                <label>Teléfono</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="tel" class="form-control" name="segundoTelSucursal" id="segundoTelSucursal" placeholder="Segundo Teléfono">
                <label>Segundo Teléfono</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="email" class="form-control" name="emailSucursal" id="emailSucursal" placeholder="Email">
                <label>Email</label>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <h5>Domicilio</h5>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="calleSucursal" id="calleSucursal" placeholder="Calle">
                <label>Calle</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="noExteriorSucursal" id="noExteriorSucursal" placeholder="No. Exterior">
                <label>No. Exterior</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="noInteriorSucursal" id="noInteriorSucursal" placeholder="No. Interior">
                <label>No. Interior</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="coloniaSucursal" id="coloniaSucursal" placeholder="Colonia">
                <label>Colonia</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="cpSucursal" id="cpSucursal" placeholder="CP">
                <label>Código Postal</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="ciudadSucursal" id="ciudadSucursal" placeholder="Ciudad">
                <label>Ciudad</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="estadoSucursal" id="estadoSucursal" placeholder="Estado">
                <label>Estado</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="paisSucursal" id="paisSucursal" placeholder="País">
                <label>País</label>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <h5>Ubicación</h5>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="latitudSucursal" id="latitudSucursal" placeholder="Latitud">
                <label>Latitud</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="longitudSucursal" id="longitudSucursal" placeholder="Longitud">
                <label>Longitud</label>
              </div>
            </div>
            <div class="col-12 mt-3" id="mapaSucu">
              
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
          <button type="submit" class="btn btn-danger" id="bGuardarSucursal">Guardar <i class="fas fa-save"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>