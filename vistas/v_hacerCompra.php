<div id="content" class="card">
	<div class="card-body">
        <div class="section">
            <div class="Principal">
                <div class="row">
                    <div class="col-md-4 d-grid mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerProveedoresC" id="cargarProveedoresModalC">
                            <i class="fas fa-user"></i> Proveedor
                        </button>
                    </div>
                    <div class="col-md-1 botonLimpiarProveedor oculto">
                        <button type="button" class="btn btn-outline-danger btn-sm" id="bLimpiarProveedorSeleccionado" attrid=""><i class="fas fa-times"></i></button>
                    </div>
                    <div class="col-md-4 mb-2 text-end">
                        <button type="button" class="btn btn-outline-secondary" id="bVerOrdenes">
                            <i class="fas fa-file"></i> Ver Ordenes
                        </button>
                        <br>
                        <button type="button" class="btn btn-outline-secondary btn-sm oculto mt-3" id="bFolioOrdenCompra">
                            <i class="fas fa-trash"></i> <span id="folioOrdenCompra"></span>
                        </button>
                    </div>
                    <!--<div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="sucursalCompra" name="sucursalCompra">
                                #sucursalesCompra#
                            </select>
                            <label for="sucursalCompra">Sucursal</label>
                        </div>
                    </div>-->
			    </div>
                <br>
                <form id="formAgregarProductoC" class="row">
                    <div class="col-md-6 col-sm-12 mb-3">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control" id="codigoProductoC" name="codigoProductoC" placeholder="Código del producto" required>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 d-grid mb-3">
                        <button type="button" class="btn btn-outline-danger" id="AgregarProductoCodigoC">Agregar producto <i class="fas fa-check"></i></button>
                    </div>
                    <div class="col-md-3 col-sm-6 d-grid mb-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalVerProductosCompra" id="cargarProductosModalC">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating" id="cambiartipoCompra">
                            <select class="form-select" id="tipoCompra" name="tipoCompra">
                                <option value="Contado" selected>Contado</option>
                                <option value="Crédito">Crédito</option>
                            </select>
                            <label>Tipo</label>
                        </div>
                    </div>
                    <div class="col-md-3 oculto" id='fechaLimiteCredito'>
                        <div class="form-floating">
                            <input type="date" class="form-control" id="fechaCredito" name="fechaCredito" placeholder="Ingresa la fecha límite del crédito">
                            <label>Fecha límite de crédito</label>
                        </div>
                    </div>
                    <div class="col-md-3 oculto" id="limiteCredito">
                        Límite de crédito: <h5 id="mostrarCreditoProveedor" class="dinero" style="font-weight: bold;">0</h5>
                    </div>
                    <div class="col-md-3 oculto" id="limiteCreditoRestante">
                        Crédito restante: <h5 id="mostrarCreditoRestante" class="dinero" style="font-weight: bold;">0</h5>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="table-responsive" style="height: 300px; overflow-y: scroll;">
                        <table class="table table table-hover table-striped table-bordered text-center" id="TablaProductosAgregados" width="100%" style="font-size: 12px; vertical-align: middle;">
                            <thead>
                                <th style="width: 20%;">Codigo</th>
                                <th style="width: 20%;">Descripción</th>
                                <th style="width: 15%;">Costo</th>
                                <th style="width: 15%;">Cantidad</th>
                                <th style="width: 15%;">Total</th>
                                <th style="width: 15%;"></th>
                            </thead>
                            <tbody id="tbodyTablaProductosAgregados">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-start">
                        <span id="cantidadProductosSpan">0</span> productos en la compra actual
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3 text-center">
                        <div id="verSubtotal">
                            <h5 style="font-weight: bold;">Subtotal</h5>
                            <h4 style="font-weight: bold;" class="dinero" id="mostrarSubtotal">0.00</h4>
                        </div>    
                    </div>
                    <div class="col-md-3 text-center mb-2">
                        <div id="ponerDescuento">
                            <h5 style="font-weight: bold;">Descuento</h5>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1"><b>$</b></span>
                                <input type="number" min="0" value="0" step="any" class="form-control" id="descuentoCompraDinero" name="descuentoCompraDinero" placeholder="$0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-center mb-2">
                        <div class="row" style="vertical-align: middle;">
                            <div class="col-md-6 d-grid">
                                <button class="btn btn-secondary" id="bRealizarCompra" idProveedor="1" style="font-size: 18px;"><i style="font-size: 25px;" class="fas fa-cart-plus"></i> <b>Guardar Compra</b></button>
                                <br>
                                <button class="btn btn-secondary" id="bGuardarOrden" idProveedor="1" style="font-size: 18px;"><i style="font-size: 25px;" class="fas fa-cart-plus"></i> <b>Guardar Orden</b></button>
                            </div>
                            <div class="col-md-6" id="verTotal">
                                <h5 style="font-weight: bold;">Total</h5>
                                <h4 style="font-weight: bold;" class="dinero" id="totalCompra">0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<!--///////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalVerProductosCompra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table table-hover table-striped table-bordered text-center myDataTable" id="tablaProductosCompra" width="100%" style="font-size: 12px;">
                            <thead>
                                <th>Fecha</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Costo</th>
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

<!--///////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalVerProveedoresC" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Proveedores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table table-hover table-striped table-bordered text-center myDataTable" id="tablaProveedoresCompra" width="100%" style="font-size: 12px;">
                            <thead>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th orden="No">Domicilio</th>
                                <th orden="No">Contacto</th>
                                <th orden="No">Cuenta</th>
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

<!--///////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalCobrarCompra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="z-index: 9999 !important;">
        <div class="modal-content">
            <div class="modal-header bg-inverse bd-inverse-darken">
                <h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold;">Cobrar compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCobrarCompra">
	            <div class="modal-body">
	       	       <div class="row">
                        <div class="col-12 mb-3">
                            <center><h4 style="font-weight: bold;">Total: <br><span class="dinero" id="totalCobrar">0</span></h4></center>
		                </div>
                        <div class="col-12 mb-3">
                            <center><h4 style="font-weight: bold;">Restante: <br><span class="dinero" id="restanteCobrar">0</span></h4></center>
                        </div>
	       		        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" min='1' step="any" id="importePagadoCobrar" name="importePagadoCobrar" placeholder="Ingresa el importe a pagar">
                                <label>Importe pagado</label>
		                    </div>
		                </div>
                        <div class="col-md-12 col-sm-12 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="conceptoPagoCobrar" name="conceptoPagoCobrar" placeholder="Ingresa el concepto del pago">
                                <label>Concepto</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <select class="form-select" id="tipoPagoCobrar" name="tipoPagoCobrar">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Deposito">Depósito</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="TransferenciaBancaria">Transferencia bancaria</option>
                                    <option value="TarjetaCreditoDebito">Tarjeta de crédito o débito</option>
                                </select>
                                <label for="TipoPago">Tipo de pago</label>
                            </div>
                        </div>
        		        <div class="col-12 mb-3">
        		        	<div class="form-floating">
        		               	<input type="text" class="form-control" id="detallesPagoCobrar" name="detallesPagoCobrar" placeholder="Ingresa los datos del pago">
        		                <label>Detalles</label>
        		            </div>
        		        </div>
                        <div class="col-md-12 col-sm-12 mb-3" id='Archivo'>
        		        	<label>Comprobante de pago</label>
                            <input type="file" class="form-control" id="comprobantePagoCobrar" name="comprobantePagoCobrar" placeholder="Ingresa un comprobante de pago">
        		        </div>
	       	        </div>
	            </div>
	            <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> Cancelar</button>
	                <button type="submit" class="btn btn-primary" id="bGuardarCompra"><i class="fa fa-check-circle"></i> Guardar</button>
	            </div>
  		    </form>
        </div>
    </div>
</div> 

<!--///////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalVerOrdenes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ordenes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table table-hover table-striped table-bordered text-center myDataTable" id="tablaOrdenesCompra" width="100%" style="font-size: 12px;">
                    <thead>
                        <th>Datos</th>
                        <th>Proveedor</th>
                        <th>Total</th>
                        <th orden="No">Detalles</th>
                        <th>Sucursal</th>
                        <th orden="No">Acciones</th>
                    </thead>
                    <tbody>
                               
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> <strong>Cerrar</strong></button>
            </div>
        </div>
    </div>
</div>

<!--///////////////////////////////////////////////////////////-->
<div class="modal fade" id="modalVerProductosOrden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
                        <thead>
                            <th>Código</th>
                            <th>Descripcion</th>
                            <th>Cantidad</th>
                            <th>Costo</th>
                            <th>Total</th>
                        </thead>
                        <tbody id="verProdOrden">
                               
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> <strong>Cerrar</strong></button>
            </div>
        </div>
    </div>
</div>
