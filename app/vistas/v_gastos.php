<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_gastos" titulo="Gastos" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
            <table id="tablaGastos" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha Registro</th>
                  <th>Monto</th>
                  <th>Descripción</th>
                  <th>Usuario</th>
                  <th>Comprobante</th>
                  <th>Fecha Gasto</th>
                  <!--<th>Sucursal</th>-->
                  <th orden="No">Acciones</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div id="modalGastos" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Gasto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formGastos">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="date" class="form-control" name="fechaGasto" id="fechaGasto" placeholder="Fecha">
                <label>Fecha de Gasto</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="descriGasto" id="descriGasto" placeholder="Descripción">
                <label>Descripción</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control input-monto" name="montoGasto" id="montoGasto" placeholder="Monto">
                <label>Monto</label>
              </div>
            </div>
            <!--<div class="col-md-4">
              <div class="form-floating">
                <select class="form-select" name="sucursalGasto" id="sucursalGasto">
                  #sucursalesGasto#
                </select>
                <label for="sucursalGasto">Sucursal</label>
              </div>
            </div>-->
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="file" class="form-control" name="comprobanteGasto" id="comprobanteGasto" placeholder="Comprobante" accept="image/*,application/pdf">
                <label for="comprobanteGasto">Comprobante</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
          <button type="submit" class="btn btn-danger" id="bGuardarGasto">Guardar <i class="fas fa-save"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>