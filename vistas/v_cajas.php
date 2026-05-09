<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button class="btn btn-outline-danger btn-sm cargarVista" type="button" carga="v_cajas" titulo="Cajas" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
  </div>
</div>
<br>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 text-end">
            #bCortes#
            #bAgregar#
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaCajas" class="myDataTable table table-hover table-striped table-bordered text-center" style="font-size: 13px;" width="100%">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Estado</th>
                  <th>Detalles</th>
                  <!--<th>Sucursal</th>-->
                  <th orden="No">Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalCajas" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Guardar Caja</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formCajas">
          <div class="row">
            <div class="col-12">
              <div class="form-floating mb-3">
                <input placeholder="Nombre" id="nombreCaja" name="nombreCaja" type="text" class="form-control">
                <label for="nombreCaja">Nombre</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating mb-3">
                <textarea placeholder="Descripción" id="detallesCaja" name="detallesCaja" type="text" class="form-control"></textarea>
                <label for="detallesCaja">Descripción</label>
              </div>
            </div>
            <!--<div class="col-12">
              <div class="form-floating mb-3">
                <select name="sucursalCaja" id="sucursalCaja" class="form-select">
                  #sucursalesCaja#
                </select>
                <label for="sucursalCaja">Sucursal</label>
              </div>
            </div>-->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary">Cerrar <i class="fas fa-times"></i></button>
            <button type="submit" class="btn btn-primary" id="bGuardarCaja">Guardar <i class="fas fa-save"></i></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalAbrirCaja" tabindex="-2">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 modal-title">Abrir Caja</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAbrirCaja">
        <div class="modal-body">
          <div class="row">
            <div class="col-12 text-center">
              <div class="form-floating mb-3">
                <input type="number" step="any" class="form-control" id="montoCaja" name="montoCaja" placeholder="Monto">
                <label>Monto</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cerrar <i class="fas fa-times"></i></button>
          <button type="submit" class="btn btn-primary" id="bAbrirMontoCaja">Abrir <i class="fas fa-save"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalReportes" tabindex="-2">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 modal-title">Reportes</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="table-responsive">
            <table id="tablaReportes" class="myDataTable table table-hover table-striped table-bordered text-center" width="100">
              <thead>
                <tr>
                  <th>Caja</th>
                  <th>Fecha Abrir</th>
                  <th>Monto Abrir</th>
                  <th>Usuario Abrir</th>
                  <th>Fecha Cierre</th>
                  <th>Monto Cierre</th>
                  <th>Usuario Cierre</th>
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