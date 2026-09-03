<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_proveedores" titulo="Proveedores" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
            <table id="tablaProveedores" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Nombre</th>
                  <th orden="No">Domicilio</th>
                  <th orden="No">Contacto</th>
                  <th orden="No">Cuenta</th>
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
<div id="modalProveedores" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Proveedor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formProveedores">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="razonProveedor" id="razonProveedor" placeholder="Razón social">
                <label>Nombre / Razón social</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="rfcProveedor" id="rfcProveedor" placeholder="RFC">
                <label>RFC</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="tel" class="form-control" name="telefonoProveedor" id="telefonoProveedor" placeholder="Teléfono">
                <label>Teléfono</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="tel" class="form-control" name="segundoTelProveedor" id="segundoTelProveedor" placeholder="Segundo Teléfono">
                <label>Segundo Teléfono</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="email" class="form-control" name="emailProveedor" id="emailProveedor" placeholder="Email">
                <label>Email</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="creditoProveedor" id="creditoProveedor" placeholder="Credito">
                <label>Credito</label>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <h5>Contacto</h5>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="contactoProveedor" id="contactoProveedor" placeholder="Nombre">
                <label>Nombre</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="puestoProveedor" id="puestoProveedor" placeholder="Puesto">
                <label>Puesto</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="tel" class="form-control" name="telefonoContactoProveedor" id="telefonoContactoProveedor" placeholder="Teléfono">
                <label>Teléfono</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="email" class="form-control" name="emailContactoProveedor" id="emailContactoProveedor" placeholder="Email">
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
                <input type="text" class="form-control" name="calleProveedor" id="calleProveedor" placeholder="Calle">
                <label>Calle</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="noExteriorProveedor" id="noExteriorProveedor" placeholder="No. Exterior">
                <label>No. Exterior</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="noInteriorProveedor" id="noInteriorProveedor" placeholder="No. Interior">
                <label>No. Interior</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="coloniaProveedor" id="coloniaProveedor" placeholder="Colonia">
                <label>Colonia</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="cpProveedor" id="cpProveedor" placeholder="CP">
                <label>Código Postal</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="ciudadProveedor" id="ciudadProveedor" placeholder="Ciudad">
                <label>Ciudad</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="estadoProveedor" id="estadoProveedor" placeholder="Estado">
                <label>Estado</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="paisProveedor" id="paisProveedor" placeholder="País">
                <label>País</label>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <h5>Cuenta bancaria</h5>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="clabeProveedor" id="clabeProveedor" placeholder="CLABE">
                <label>CLABE Interbancaria</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="bancoProveedor" id="bancoProveedor" placeholder="Banco">
                <label>Banco</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="titularProveedor" id="titularProveedor" placeholder="Titular">
                <label>Titular</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
          <button type="submit" class="btn btn-danger" id="bGuardarProveedor">Guardar <i class="fas fa-save"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>