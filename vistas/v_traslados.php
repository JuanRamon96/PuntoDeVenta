<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_traslados" titulo="Traslados" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
          <d ffiv class="col-12 table-responsive">
            <table id="tablaTraslados" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha Registro</th>
                  <th>Origen</th>
                  <th>Destino</th>
                  <th>Usuario</th>
                  <th>Estatus</th>
                  <th>Fecha de traslado</th>
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
<div class="modal" id="modalTraslados" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Traslados</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formTraslado">
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" class="form-control" id="fechaTraslado" name="fechaTraslado">
              <label for="fechaTraslado">Fecha del Traslado</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select name="sucursalOrigenTras" id="sucursalOrigenTras" class="form-select">
                #sucursalesTraslado#
              </select>
              <label for="sucursalOrigenTras">Sucursal origen</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select name="sucursalDestinoTras" id="sucursalDestinoTras" class="form-select">
                #sucursalesTraslado#
              </select>
              <label for="sucursalDestinoTras">Sucursal destino</label>
            </div>
          </div>
        </div>
        <div class="row pt-3 mb-4">
          <div class="col-10">
            <div class="input-group">
              <span class="input-group-text" id="basic-addon1"><i class="fas fa-barcode"></i></span>
              <input type="text" class="form-control" id="codigoProdTras" name="codigoProdTras" placeholder="Código del producto" required>
            </div>
          </div>
          <div class="col-1">
            <button type="button" class="btn btn-success w-100" id="añadir_prod_ori_tras"><i class="fa-solid fa-plus fa-xl"></i></button>
          </div>
          <div class="col-1">
            <button type="button" class="btn btn-secondary w-100" id="buscar_prods_ori_tras"><i class="fas fa-search"></i></button>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-12 table-responsive">
            <table id="tablaHacerTraslado" class="table table-hover table-striped table-bordered text-center" width="100%">
              <thead>
                <tr>
                  <th>Codigo</th>
                  <th>Producto</th>
                  <th>Cantidad a Trasldar</th>
                  <th orden="No">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="4">Añada productos...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>  
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cerrar <i class="fas fa-times ml-2"></i></button>
        <button type="submit" class="btn btn-primary" id="bGuardarTraslado">Relizar Traslado <i class="fa-solid fa-floppy-disk ml-2"></i></button>
      </div>
      </form>
    </div>
  </div>
</div>