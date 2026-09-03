<div class="row" style="margin-top: -65px;">
  <div class="col text-end">
    <button class="btn btn-outline-danger btn-sm cargarVista" carga="v_ventas" titulo="Ventas" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
  </div>
</div>
<br>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaVentas" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Folio</th>
                  <th>Tipo Pago</th>
                  <th>Cliente</th>
                  <th>Total</th>
                  <th>Pago</th>
                  <th>Cambio</th>
                  <th>Estatus</th>
                  <th orden="No">Detalles</th>
                  <!--<th>Sucursal</th>-->
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
<div class="modal" id="modalDetalles" tabindex="-2">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Detalles de la venta</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-hover table-striped table-bordered text-center myDataTable" id="tablaDetalles" width="100%">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th>Cantidad</th>
                  <th>Descuento</th>
                  <th>Impuestos</th>
                  <th>Total</th>
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

<!--//////////////////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalVerPagosVentas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pagos <span id="folioVentaPagos"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 text-end">
            <button class="btn btn-outline-danger btn-sm" id="bRecargarPagosVenta"><i class="fas fa-rotate-right"></i></button>
          </div>
        </div>
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table table-hover table-striped table-bordered text-center myDataTable" id="tablaVerPagosVentas" width="100%" style="font-size: 12px;">
              <thead>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 20%;">Concepto</th>
                <th style="width: 20%;">Tipo de pago</th>
                <th style="width: 10%;">Monto</th>
                <th style="width: 15%;" orden="No">Detalles</th>
                <th style="width: 10%;" orden="No">Comprobante</th>
                <th style="width: 10%;" orden="No">Acciones</th>
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
<div class="modal fade" id="modalPagoVenta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="z-index: 9999 !important;">
    <div class="modal-content">
      <div class="modal-header bg-inverse bd-inverse-darken">
        <h5 class="modal-title" style="font-weight: bold;">Hacer pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formPagoVenta">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 col-sm-12 mb-1">
              <center>
                <h4 style="font-weight: bold;">Total de la venta: <br><span class="dinero" id="totalVentaPago"></span></h4>
              </center>
            </div>
            <div class="col-md-12 col-sm-12 mb-1">
              <center>
                <h4 style="font-weight: bold;">Total de pagos: <br><span class="dinero" id="pagosVenta"></span></h4>
              </center>
            </div>
            <div class="col-md-12 col-sm-12 mb-1">
              <center>
                <h4 style="font-weight: bold;">Restante: <br><span class="dinero" id="restanteVenta"></span></h4>
              </center>
            </div>
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="importePagoVenta" name="importePagoVenta" placeholder="Ingresa el importe a pagar">
                <label>Importe a pagar: </label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3" id='Concepto'>
              <div class="form-floating">
                <input type="text" class="form-control" id="conceptoPagoVenta" name="conceptoPagoVenta" placeholder="Ingresa el concepto del pago">
                <label>Concepto: </label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <select class="form-select" id="tipoDePagoVenta" name="tipoDePagoVenta">
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
                <input type="text" class="form-control" id="detallesPagoVenta" name="detallesPagoVenta" placeholder="Ingresa los datos del pago">
                <label>Detalles: </label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3" id='Archivo'>
              <label class="form-label">Ingresa un comprobante de pago</label>
              <input class="form-control" type="file" id="comprobantePagoVenta" name="comprobantePagoVenta">
            </div>
            <div class="col-md-12 col-sm-12 mb-3 oculto">
              <div class="form-floating">
                <select class="form-select" id="cajaPagoVenta" name="cajaPagoVenta">
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
          <button type="submit" class="btn btn-primary" id="bGuardarPagoVenta">Guardar <i class="fa fa-check-circle"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalDevoluciones" tabindex="-2">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Devoluciones</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-hover table-striped table-bordered text-center" id="tablaDevoluciones" width="100%">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th>Cantidad</th>
                  <th>Descuento</th>
                  <th>Total</th>
                  <th>Cantidad a Devolver</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary has-ripple" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
        <button type="submit" class="btn btn-primary" id="bGuardarDevolucion">Guardar <i class="fa fa-check-circle"></i></button>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalModificarVenta" tabindex="-2">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Modificar Venta</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <form id="formProdModiVen" class="col-md-6">
            <div class="input-group mb-3">
              <input type="text" class="form-control" id="codigoProdModiVen" name="codigoProdModiVen" placeholder="Código del producto">
              <button type="submit" class="btn btn-outline-secondary" id="bAgregarProdModi">Agregar <i class="fa-solid fa-right-to-bracket"></i></button>
              <button type="button" class="btn btn-outline-secondary" id="bBuscarProdModiVen"><i class="fas fa-search"></i></button>
            </div>
          </form>
          <div class="col-md-6">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Cliente" id="clienteModiVen" name="clienteModiVen" readonly>
              <button type="button" class="btn btn-outline-secondary" id="bQuitarClienteModiVen"><i class="fas fa-times"></i></button>
              <button type="button" class="btn btn-outline-secondary" id="bBuscarClienteModiVen"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-striped table-hover text-center" id="tablaModiVenta" width="100%">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th style="width: 10%;">Cantidad</th>
                  <th style="width: 20%;">Descuento</th>
                  <th style="width: 10%;">Impuestos</th>
                  <th style="width: 20%;">Total</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
              <tfoot style="font-size: 20px;">
                <tr>
                  <td colspan="6" class="text-end">Subtotal</td>
                  <td><span class="dinero" id="subtotalModiVenta">0</span></td>
                </tr>
                <tr>
                  <td colspan="6" class="text-end">Descuento:</td>
                  <td>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control" value="0" id="porcentajeDescuentoModiVenta" placeholder="Porcentaje">
                      <span class="input-group-text">% = $</span>
                      <input type="text" class="form-control" value="0" id="cantidadDescuentoModiVenta" placeholder="Cantidad">
                    </div>
                  </td>
                </tr>
                <tr>
                  <td colspan="6" class="text-end">Total</td>
                  <td><span class="dinero" id="totalModiVenta">0</span></td>
                </tr>
                <tr>
                  <td colspan="6" class="text-end">Pago</td>
                  <td><input type="text" class="form-control" id="pagoModiVenta" name="pagoModiVenta" placeholder="Pago"></td>
                </tr>
                <tr>
                  <td colspan="6" class="text-end">Cambio</td>
                  <td><span class="dinero" id="cambioModiVen">0</span></td>
                </tr>
                <tr>
                  <td colspan="6" class="text-end">Restante</td>
                  <td><span class="dinero" id="restanteModiVen">0</span></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-12 mb-3">
            <div class="form-floating">
              <select class="form-select form-select-lg valid" name="metodoPagoModiVenta" id="metodoPagoModiVenta" aria-invalid="false">
                <option value="Efectivo" selected="">Efectivo</option>
                <option value="Deposito">Deposito</option>
                <option value="Cheque">Cheque</option>
                <option value="Transferencia">Transferencia</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Pago Online">Pago Online</option>
                <option value="Crédito">Crédito</option>
              </select>
              <label>Metodo de pagó</label>
            </div>
          </div>
          <div class="col-12 mb-3">
            <label for="detallesModiVenta">Detalles: </label>
            <textarea name="detallesModiVenta" id="detallesModiVenta" class="form-control" rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary has-ripple" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
        <button type="button" class="btn btn-primary" id="bGuardarModiVenta">Guardar <i class="fa fa-check-circle"></i></button>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalProdModiVen" tabindex="-2">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Productos</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-striped table-hover text-center myDataTable" id="tablaProdModiVen" width="100%">
              <thead>
                <tr>
                <tr>
                  <th>Código</th>
                  <th>Descripción</th>
                  <th>Clase</th>
                  <th>Precio</th>
                  <th>Precio Mayoreo</th>
                  <th>Existencia</th>
                  <th>Impuestos</th>
                </tr>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary has-ripple" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalClienteModiVen" tabindex="-2">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Cliente</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-striped table-hover text-center myDataTable" id="tablaClienteModiVen" width="100%">
              <thead>
                <tr>
                <tr>
                  <th>Nombre</th>
                  <th>Domicilio</th>
                  <th>Contacto</th>
                </tr>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary has-ripple" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>
</div>