<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button class="btn btn-outline-danger btn-sm cargarVista" carga="v_inventario" titulo="Inventario" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
  </div>
</div>  
<br>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 text-end">
            <a href="./controladores/pdf/ticketStock.php" target="_blank" class="btn btn-sm btn-outline-danger" id="bImprmirStock"><i class="fas fa-print"></i> Imprimir stock mínimo</a>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaInventario" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%">
                <thead>
                  <tr>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Existencia</th>
                    <!--<th orden="No">Sucursales</th>-->
                    <th>Costo</th>
                    <th>Precio</th>
                    <th>Precio Mayoreo</th>
                    <th orden="No">Totales</th>
                    <th>Merma</th>
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
<div class="modal" id="modalInventario" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Agregar al Inventario</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formInventario">
          <div class="row">
            <div class="col-md-12 mb-3">
              <h3 class="text-center">Existencia Actual: <span class="cantidad" id="cantidadActual">0</span></h3>
            </div>
            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="cantidadInventario" name="cantidadInventario" placeholder="Cantidad">
                <label for="cantidadInventario">Cantidad a Agregar</label>
              </div>
            </div>
            <!--<div class="col-md-12 mb-3">
              <div class="form-floating">
                <select class="form-select" name="sucursalInventario" id="sucursalInventario">
                  #sucursalesInventario#
                </select>
                <label for="sucursalInventario">Sucursal</label>
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="checkAplicarInve">
                <label class="form-check-label" for="checkAplicarInve">
                  Aplicar a existencia general y a sucursales 
                </label>
              </div>
            </div>-->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
            <button type="submit" class="btn btn-primary" id="bGuardarInventario">Agregar <i class="fas fa-plus"></i></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalInventarioRes" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Restar al Inventario</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formInventarioRes">
          <div class="row">
            <div class="col-md-12 mb-3">
              <h3 class="text-center">Existencia Actual: <span class="cantidad" id="cantidadActualRes">0</span></h3>
            </div>
            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="cantidadInventarioRes" name="cantidadInventarioRes" placeholder="Cantidad">
                <label for="cantidad">Cantidad a Restar</label>
              </div>
            </div>
            <!--<div class="col-md-12 mb-3">
              <div class="form-floating">
                <select class="form-select" name="sucursalInventarioRes" id="sucursalInventarioRes">
                  #sucursalesInventario#
                </select>
                <label for="sucursalInventarioRes">Sucursal</label>
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="checkAplicarInveRes">
                <label class="form-check-label" for="checkAplicarInveRes">
                  Aplicar a existencia general y a sucursales 
                </label>
              </div>
            </div>-->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
            <button type="submit" class="btn btn-primary" id="bGuardarInventarioRes">Restar <i class="fas fa-minus"></i></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalMerma" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Merma Inventario</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 text-end">
            <button type="button" class="btn btn-sm btn-danger" id="bNuevaMerma" title="Agregar Merma" >Agregar<i class="fa-solid fa-plus"></i></button>    
          </div>
        </div>
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaMerma" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%">
                <thead>
                  <tr>
                    <th>Fecha Registro</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Costo Promedio</th>
                    <th>Total</th>
                    <!--<th>Sucursal</th>-->
                    <th>Fecha Merma</th>
                    <th>Usuario</th>
                    <th>Afecto Inventario</th>
                    <th orden="No">Acciones</th>
                  </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalMermaNueva" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Merma al Inventario</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formInventarioMerma">
          <div class="row">
            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <input type="date" class="form-control" id="fechaMerma" name="fechaMerma" placeholder="Fecha">
                <label for="fechaMerma">Fecha</label>
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="descriMerma" name="descriMerma" placeholder="Descripción">
                <label for="descriMerma">Descripción</label>
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="cantidadMerma" name="cantidadMerma" placeholder="Cantidad">
                <label for="cantidadMerma">Cantidad de merma</label>
              </div>
            </div>
            <!--<div class="col-md-12 mb-3">
              <div class="form-floating">
                <select class="form-select" name="sucursalMerma" id="sucursalMerma">
                  #sucursalesInventario#
                </select>
                <label for="sucursalMerma">Sucursal</label>
              </div>
            </div>-->
            <div class="col-md-12 mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="checkMerma" checked>
                <label class="form-check-label" for="flexCheckDefault">
                  Afectar el inventario
                </label>
              </div>
            </div>
            <div class="col-12 mb-3">
              <label for="fotoMerma" class="form-label">Foto</label>
              <input type="file" class="form-control" id="fotoMerma" name="fotoMerma" accept="image/*">
            </div>    
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
            <button type="submit" class="btn btn-primary" id="bGuardarNuevaMerma">Guardar <i class="fa-solid fa-floppy-disk"></i></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>