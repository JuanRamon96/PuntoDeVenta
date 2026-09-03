<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_compras" titulo="Compras" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
            <table id="tablaCompras" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Datos</th>
                  <th>Proveedor</th>
                  <th>Total</th>
                  <th orden="No">Detalles</th>
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

<!--//////////////////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalVerProductosCompra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Productos <span id="folioCompraProductos"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped text-center">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Costo</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody id="tbodyVerProductosCompra">

            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary has-ripple" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>
</div>

<!--//////////////////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalVerHistorialPagos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pagos <span id="folioCompraPagos"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-12 text-end">
            <button type="button" class="btn btn-outline-danger btn-sm" id="bRecargarPagosCompra"><i class="fas fa-rotate-right"></i></button>
          </div>
        </div>
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table table-hover table-striped table-bordered text-center myDataTable" id="tablaVerHistorialPagos" width="100%" style="font-size: 12px;">
              <thead>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 20%;">Concepto</th>
                <th style="width: 20%;">Tipo de pago</th>
                <th style="width: 10%;">Monto</th>
                <th style="width: 15%;" orden="No">Detalles</th>
                <th style="width: 10%;" orden="No">Comprobante</th>
                <th style="width: 10%;" orden="No">Acción</th>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> <strong>Cerrar</strong></button>
      </div>
    </div>
  </div>
</div>

<!--//////////////////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalPagoCompra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="z-index: 9999 !important;">
    <div class="modal-content">
      <div class="modal-header bg-inverse bd-inverse-darken">
        <h5 class="modal-title" style="font-weight: bold;">Hacer pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formPagoCompra">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 col-sm-12 mb-1">
              <center>
                <h4 style="font-weight: bold;">Total de la compra: <br><span id="totalCompra"></span></h4>
              </center>
            </div>
            <div class="col-md-12 col-sm-12 mb-1">
              <center>
                <h4 style="font-weight: bold;">Total de pagos: <br><span id="pagos"></span></h4>
              </center>
            </div>
            <div class="col-md-12 col-sm-12 mb-1">
              <center>
                <h4 style="font-weight: bold;">Restante: <br><span id="restante"></span></h4>
              </center>
            </div>
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="importePagoCompra" name="importePagoCompra" placeholder="Ingresa el importe a pagar">
                <label>Importe a pagar: </label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3" id='Concepto'>
              <div class="form-floating">
                <input type="text" class="form-control" id="conceptoPago" name="conceptoPago" placeholder="Ingresa el concepto del pago">
                <label>Concepto: </label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <select class="form-select" id="tipoDePago" name="tipoDePago">
                  <option value="" selected>--Seleccione una opción--</option>
                  <option value="Efectivo">Efectivo</option>
                  <option value="Deposito">Depósito</option>
                  <option value="Cheque">Cheque</option>
                  <option value="TransferenciaBancaria">Transferencia bancaria</option>
                  <option value="TarjetaCreditoDebito">Tarjeta de crédito o débito</option>
                </select>
                <label>Tipo de pago</label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3" id='Detalles'>
              <div class="form-floating">
                <input type="text" class="form-control" id="detallesPago" name="detallesPago" placeholder="Ingresa los datos del pago">
                <label>Detalles: </label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3" id='Archivo'>
              <label class="form-label">Ingresa un comprobante de pago</label>
              <input class="form-control" type="file" id="comprobantePago" name="comprobantePago">
            </div>
            <div class="col-md-12 col-sm-12 mb-3 oculto">
              <div class="form-floating">
                <select class="form-select" id="cajaPago" name="cajaPago">
                  <option value="" selected>--Seleccione una caja--</option>
                  #cajas#
                </select>
                <label>Caja</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary has-ripple" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
          <button type="submit" class="btn btn-primary" id="bGuardarPago">Guardar <i class="fa fa-check-circle"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>