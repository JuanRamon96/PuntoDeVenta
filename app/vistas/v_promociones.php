<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_promociones" titulo="Promociones" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
            <table class="table table table-hover table-bordered text-center myDataTable" id="tablaPromociones" width="100%" style="font-size: 12px;">
              <thead>
                <th>Nombre</th>
                <th>Aplica a</th>
                <th>Cantidad</th>
                <th>Beneficio</th>
                <th orden="No">Estatus</th>
                <th orden="No">Acciones</th>
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

<!-- ///////////////////////////Modal Promoción////////////////////////////// -->
<div class="modal fade" id="modalPromocion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-inverse bd-inverse-darken">
        <h5 class="modal-title">Promoción</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formPromociones">
        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="nombrePromocion" name="nombrePromocion" placeholder="Ingresa el nombre">
                <label>Nombre de la promoción</label>
              </div>
            </div>
            <div class="col-12 mb-3">
              <div class="form-floating">
                <select class="form-select" id="tipoPromocion" name="tipoPromocion">
                  <option value="Producto">Producto</option>
                  <option value="Clasificacion">Clasificación</option>
                </select>
                <label>Aplica por</label>
              </div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Producto o clasificación seleccionada</label>
              <div class="input-group">
                <input type="text" class="form-control" id="nombreSeleccionPromocion" readonly placeholder="Selecciona un producto o clasificación">
                <button type="button" class="btn btn-outline-secondary" id="bSeleccionarAplicaPromocion"><i class="fas fa-search"></i> Seleccionar</button>
              </div>
              <input type="hidden" id="fkProductoPromocion" name="fkProducto" value="">
              <input type="hidden" id="fkClasificacionPromocion" name="fkClasificacion" value="">
              <div class="invalid-feedback d-block oculto" id="errorSeleccionPromocion">Debes seleccionar un producto o clasificación.</div>
            </div>
            <div class="col-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="cantidadPromocion" name="cantidadPromocion" placeholder="Ingresa la cantidad">
                <label>Cantidad</label>
              </div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">¿Cómo se aplica la cantidad?</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="modoCantidadPromocion" id="modoCantidadPaquete" value="Paquete" checked>
                <label class="form-check-label" for="modoCantidadPaquete">Por paquete (se repite cada vez que se completa la cantidad. Ej: 3 piezas x $100, si compra 4 son 3 al precio del paquete + 1 normal, con 6 son dos paquetes)</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="modoCantidadPromocion" id="modoCantidadMinima" value="Minima">
                <label class="form-check-label" for="modoCantidadMinima">Cantidad mínima (al llegar a la cantidad, el beneficio aplica a todas las piezas de esa venta)</label>
              </div>
            </div>
            <div class="col-12 mb-3" id="seccionTipoBeneficioPromocion">
              <label class="form-label">Tipo de beneficio</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoBeneficioPromocion" id="beneficioPrecioFijo" value="Precio_Fijo" checked>
                <label class="form-check-label" for="beneficioPrecioFijo">Precio fijo</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoBeneficioPromocion" id="beneficioDescuento" value="Descuento">
                <label class="form-check-label" for="beneficioDescuento">Descuento</label>
              </div>
            </div>
            <div class="col-12 mb-3" id="seccionPrecioFijoPromocion">
              <div class="form-floating">
                <input type="text" class="form-control" id="precioFijoPromocion" name="precioFijoPromocion" placeholder="Ingresa el precio fijo">
                <label>Precio fijo</label>
              </div>
            </div>
            <div class="col-12 mb-3 oculto" id="seccionDescuentoPromocion">
              <label class="form-label">Tipo de descuento</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoDescuentoPromocion" id="descuentoPorcentaje" value="Porcentaje" checked>
                <label class="form-check-label" for="descuentoPorcentaje">Porcentaje (%)</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipoDescuentoPromocion" id="descuentoMonto" value="Monto">
                <label class="form-check-label" for="descuentoMonto">Monto ($)</label>
              </div>
              <div class="form-floating mt-2">
                <input type="text" class="form-control" id="descuentoPromocion" name="descuentoPromocion" placeholder="Ingresa el descuento">
                <label>Descuento</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> Cerrar</button>
          <button type="submit" class="btn btn-primary" id="bGuardarPromocion"><i class="fa fa-check-circle"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal Seleccionar producto////////////////////////////// -->
<div class="modal fade" id="modalPromocionSeleccionProducto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Selecciona un producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table table-hover table-striped table-bordered text-center myDataTable" id="tablaPromocionSeleccionProducto" width="100%" style="font-size: 12px;">
              <thead>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio</th>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal Seleccionar clasificación////////////////////////////// -->
<div class="modal fade" id="modalPromocionSeleccionClasificacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Selecciona una clasificación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table table-hover table-striped table-bordered text-center myDataTable" id="tablaPromocionSeleccionClasificacion" width="100%" style="font-size: 12px;">
              <thead>
                <th>Nombre</th>
                <th>Descripcion</th>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>
