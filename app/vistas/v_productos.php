<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_productos" titulo="Productos" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
  </div>
</div>
<br>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 text-end">
            <button type="button" class="btn btn-outline-secondary" id="bImprimirCodigosVarios">Imprimir <i class="fas fa-barcode"></i></button>
            #bAgregar#
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaProductos" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Código</th>
                  <th>Descripción</th>
                  <th>Clase</th>
                  <th>Costo</th>
                  <th>Costo Promedio</th>
                  <th>Precio</th>
                  <th>Precio Mayoreo</th>
                  <th>Stock Mínimo</th>
                  <th>Stock Máximo</th>
                  <th>Clasificación</th>
                  <th>Impuestos</th>
                  <th orden="No">Acciones</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button class="btn btn-outline-primary btn-sm" id="bMarcarTodos">Marcar Todos</button>
            <button class="btn btn-outline-primary btn-sm" id="bDesmarcar">Desmarcar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div id="modalProductos" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Guardar Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formProductos">
        <div class="modal-body">
          <div class="row">
            <div class="col-12 text-center">
              <input type="file" id="fotoProducto" name="fotoProducto" style="display: none;" accept="image/*">
              <p class="text-right" style="margin-bottom: -20px;">
                <button type="button" class="btn btn-icon btn-danger rounded-circle btn-sm botones-imagenes" id="bModificarFotoP" title="Modificar">
                  <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="btn btn-icon btn-danger rounded-circle btn-sm botones-imagenes oculto" id="bCancelarFotoP" title="Cancelar">
                  <i class="fas fa-times"></i>
                </button>
              </p>
              <br>
              <a href="vistas/assets/images/producto-generico.jpg" data-fancybox="images">
                <div id="mosFotoProducto" style="background-image: url('vistas/assets/images/producto-generico.jpg'); width: 280px; height: 180px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer;"></div>
              </a>
              <br>
              <br>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="codigoProducto" id="codigoProducto" placeholder="Código">
                <label>Código</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="descripcionProducto" id="descripcionProducto" placeholder="Descripción">
                <label>Descripción</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="costoProducto" id="costoProducto" placeholder="Costo">
                <label>Costo</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="input-group">
                <div class="form-floating">
                  <input type="text" class="form-control" name="precioProducto" id="precioProducto" placeholder="Precio">
                  <label>Precio</label>
                </div>
                <button class="btn btn-outline-secondary" type="button" id="bPorcentajeGanancia"><span class="porcentaje">#porcentajeSumProd#</span></button>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="precioMayoreoProducto" id="precioMayoreoProducto" placeholder="Precio Mayoreo">
                <label>Precio Mayoreo</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <select type="text" class="form-select" name="claseProducto" id="claseProducto">
                  <option value="Pieza">Pieza</option>
                  <option value="Granel">Granel</option>
                </select>
                <label>Se vende como</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="stockMinimoProducto" id="stockMinimoProducto" placeholder="Stock Mínimo">
                <label>Stock Mínimo</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="stockMaximoProducto" id="stockMaximoProducto" placeholder="Stock Máximo">
                <label>Stock Máximo</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <select type="text" class="form-select" name="clasificacionProducto" id="clasificacionProducto">
                  <option value="0">--Selecciona una clasificación--</option>
                  #clasificacionesProducto#  
                </select>
                <label>Clasificación</label>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <h5>Datos Facturación</h5>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="input-group">
                <button class="btn btn-outline-secondary" type="button" id="bBuscarClaveProducto"><i class="fas fa-search"></i></button>
                <div class="form-floating">
                  <input type="text" class="form-control" name="claveProducto" id="claveProducto" placeholder="Clave de Producto">
                  <label for="claveProducto">Clave de Producto / Servicio</label>
                </div>  
                <button class="btn btn-outline-danger" type="button" id="bQuitarClaveProducto"><span class="fas fa-times"></span></button>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" step="any" class="form-control" name="descripcionClaveProducto" id="descripcionClaveProducto" placeholder="Descripción de Clave" readonly>
                <label for="descripcionClaveProducto">Descripción de Clave</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <button class="btn btn-outline-secondary" type="button" id="bBuscarClaveUnidadProducto"><i class="fas fa-search"></i></button>
                <div class="form-floating">
                  <input type="text" class="form-control" name="claveUnidadProducto" id="claveUnidadProducto" placeholder="Clave de Unidad">
                  <label for="claveUnidadProducto">Clave de Unidad</label>
                </div>  
                <button class="btn btn-outline-danger" type="button" id="bQuitarClaveUnidadProducto"><span class="fas fa-times"></span></button>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" step="any" class="form-control" name="nombreUnidadProducto" id="nombreUnidadProducto" placeholder="Nombre de Unidad" readonly>
                <label for="nombreUnidadProducto">Nombre de Unidad</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" step="any" class="form-control" name="simboloProducto" id="simboloProducto" placeholder="Simbolo Unidad" readonly>
                <label for="simboloProducto">Simbolo Unidad</label>
              </div>
            </div>
          </div>
          <hr>
          <br>
          <div class="row">
            <div class="col-12 table-responsive">
              <table class="table table-hover table-striped table-bordered text-center" width="100%">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Porcentaje</th>
                    <th>Clave</th>
                    <th>Clase</th>
                    <th>Factor</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody id="verImpuestosProducto">
                  #impuestosProductos#
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
          <button id="bGuardarProducto" type="submit" class="btn btn-primary">Guardar <i class="fas fa-save"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div id="modalClavesProductos" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Claves de Productos / Servicios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-hover table-striped table-bordered text-center myDataTable" id="tablaClavesProductos" width="100%">
              <thead>
                <tr>
                  <th>Clave</th>
                  <th>Descripción</th>
                  <th>Palabras</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div id="modalClavesUnidadProductos" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Claves de Unidad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-hover table-striped table-bordered text-center myDataTable" id="tablaClavesUnidadProductos" width="100%">
              <thead>
                <tr>
                  <th>Clave</th>
                  <th>Nombre</th>
                  <th>Simbolo</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>
</div>