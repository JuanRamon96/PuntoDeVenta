<div class="row" style="margin-bottom: 30px; margin-top: -50px;">
  <div class="col-12 text-right">
    <button type="button" class="btn btn-sm btn-outline-danger cargarVista" carga="v_facturas" titulo="Facturas"><i class="fas fa-rotate-right"></i></button>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="row justify-content-end">
          <div class="col-12 text-end">
            #bAgregar#
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <label for="fechaInicioFacturas">Fecha Inicio</label>
            <input type="date" name="fechaInicioFacturas" id="fechaInicioFacturas" class="form-control fechasFacturas">
          </div>
          <div class="col-md-4">
            <label for="fechaFinFacturas">Fecha Fin</label>
            <input type="date" name="fechaFinFacturas" id="fechaFinFacturas" class="form-control fechasFacturas">
          </div>
        </div>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="row">
            <div class="col-12 table-responsive">
              <table class="table table-hover table-striped table-bordered text-center myDataTable" id="tablaFacturas" width="100%">
                <thead>
                  <tr>
                    <th>Fecha Registro</th>
                    <th>Fecha Emision</th>
                    <th>Fecha Timbrado</th>
                    <th>Folio</th>
                    <th>Emisor</th>
                    <th>Receptor</th>
                    <th>Datos</th>
                    <th>Total</th>
                    <th>Estatus</th>
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
</div>

<!-- //////////////Modal//////////////////////-->
<div class="modal" id="modalFactura" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Facturación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container">

          <form id="factForm">
            <div class="row justify-content-end">
              <div class="col-md-6 mb-3">
                <label for="c_clave">Buscar Venta</label>
                <div class="input-group">
                  <button class="btn btn-outline-secondary" type="button" id="bBuscarFolioFac"><i class="fas fa-search"></i></button>
                  <input type="text" class="form-control" name="folioVentaFacturacion" id="folioVentaFacturacion" placeholder="Folio" readonly>
                  <button class="btn btn-outline-danger" type="button" id="bQuitarFolioFac"><span class="fas fa-times"></span></button>
                </div>
              </div>
            </div>

            <!-- ENCABEZADO -->
            <div class="row mb-3">
              <div class="col-md-3">
                <label for="tipoComprobante" class="fs-6">Tipo de comprobante</label>
                <select name="tipoComprobante" id="tipoComprobante" class="form-select">
                  <option value="I - Ingreso">I - Ingreso</option>
                  <option value="P - Complemento de Pago">P - Complemento de Pago</option>
                </select>
              </div>
              <div class="col-md-3">
                <div class="form-check pt-4">
                  <input class="form-check-input" type="checkbox" id="facturaGlobal">
                  <label class="form-check-label" for="facturaGlobal">
                    Factura Global
                  </label>
                </div>
              </div>
            </div>

            <!-- ======== SECCIÓN COMPLETA ARRIBA ======== -->
            <div class="row g-3">
              <!-- DATOS CFDI -->
              <div class="col-12">
                <div class="card" style="box-shadow: none; border: 1px solid #EEE;">
                  <div class="card-header fs-6">Datos de la Factura</div>
                  <div class="card-body row">
                    <div class="col-md-4">
                      <label class="form-label small">Fecha de Emisión</label>
                      <input type="datetime-local" name="fechaEmision" id="fechaEmision" class="form-control mb-2" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label small">Forma de pago</label>
                      <select name="formaPago" id="formaPago" class="form-select mb-2" required>
                        <option value="">--Selecciona una forma de pago--</option>
                        <option value="01 - Efectivo">01 - Efectivo</option>
                        <option value="03 - Transferencia electrónica de fondos">03 - Transferencia electrónica de fondos</option>
                        <option value="04 - Tarjeta de crédito">04 - Tarjeta de crédito</option>
                        <option value="28 - Tarjeta de débito">28 - Tarjeta de débito</option>
                        <option value="99 - Por definir">99 - Por definir</option>
                        <option value="02 - Cheque nominativo">02 - Cheque nominativo</option>
                        <option value="05 - Monedero electrónico">05 - Monedero electrónico</option>
                        <option value="06 - Dinero electrónico">06 - Dinero electrónico</option>
                        <option value="08 - Vales de despensa">08 - Vales de despensa</option>
                        <option value="12 - Dación en pago">12 - Dación en pago</option>
                        <option value="13 - Pago por subrogación">13 - Pago por subrogación</option>
                        <option value="14 - Pago por consignación">14 - Pago por consignación</option>
                        <option value="15 - Condonación">15 - Condonación</option>
                        <option value="17 - Compensación">17 - Compensación</option>
                        <option value="23 - Novación">23 - Novación</option>
                        <option value="24 - Confusión">24 - Confusión</option>
                        <option value="25 - Remisión de deuda">25 - Remisión de deuda</option>
                        <option value="26 - Prescripción o caducidad">26 - Prescripción o caducidad</option>
                        <option value="27 - A satisfacción del acreedor">27 - A satisfacción del acreedor</option>
                        <option value="29 - Tarjeta de servicios">29 - Tarjeta de servicios</option>
                        <option value="30 - Aplicación de anticipos">30 - Aplicación de anticipos</option>
                        <option value="31 - Intermediario pagos">31 - Intermediario pagos</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label small">Metodo de Pago</label>
                      <select name="metodoPago" id="metodoPago" class="form-select mb-2" required>
                        <option value="">--Selecciona una forma de pago--</option>
                        <option value="PUE - Pago en una sola exhibición">PUE - Pago en una sola exhibición</option>
                        <option value="PPD - Pago en parcialidades o diferido">PPD - Pago en parcialidades o diferido</option>
                      </select>
                    </div>
                    <div class="col-md-4 d-none">
                      <label class="form-label small">Periodicidad</label>
                      <select name="periodicidad" id="periodicidad" class="form-select mb-2" required="true" readonly>
                        <option value="">--Selecciona la periodicidad--</option>
                        <option value="01 - Diario">01 - Diario</option>
                        <option value="02 - Semanal">02 - Semanal</option>
                        <option value="03 - Quincenal">03 - Quincenal</option>
                        <option value="04 - Mensual">04 - Mensual</option>
                      </select>
                    </div>
                    <div class="col-md-4 d-none">
                      <label class="form-label small">Mes</label>
                      <select name="mes" id="mes" class="form-select mb-2" required="true" readonly>
                        <option value="">--Selecciona un mes--</option>
                        <option value="01 - Enero">01 - Enero</option>
                        <option value="02 - Febrero">02 - Febrero</option>
                        <option value="03 - Marzo">03 - Marzo</option>
                        <option value="04 - Abril">04 - Abril</option>
                        <option value="05 - Mayo">05 - Mayo</option>
                        <option value="06 - Junio">06 - Junio</option>
                        <option value="07 - Julio">07 - Julio</option>
                        <option value="08 - Agosto">08 - Agosto</option>
                        <option value="09 - Septiembre">09 - Septiembre</option>
                        <option value="10 - Octubre">10 - Octubre</option>
                        <option value="11 - Noviembre">11 - Noviembre</option>
                        <option value="12 - Diciembre">12 - Diciembre</option>
                      </select>
                    </div>
                    <div class="col-md-4 d-none">
                      <label class="form-label small">Año</label>
                      <input type="number" name="anio" id="anio" class="form-control mb-2" value="#anioActual#" required="true" readonly>
                    </div>
                  </div>
                </div>
              </div>

              <!-- EMISOR -->
              <div class="col-md-6">
                <div class="card" style="box-shadow: none; border: 1px solid #EEE;">
                  <div class="card-body">
                    <h3>Emisor</h3>
                    <div class="row">
                      <div class="col-md-12 text-end" style="margin-top: -35px;">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="bRecargarEmisor"><i class="fas fa-refresh"></i></button>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label class="form-label small">Nombre</label>
                        <h5 id="nombreEmisor">#nombreEmisor#</h5>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label small">RFC</label>
                        <h5 id="rfcEmisor">#rfcEmisor#</h5>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label small">Régimen</label>
                        <h5 id="regimenEmisor">#regimenEmisor#</h5>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label small">Código Postal</label>
                        <h5 id="cpEmisor">#cpEmisor#</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- RECEPTOR -->
              <div class="col-md-6">
                <div class="card" style="box-shadow: none; border: 1px solid #EEE;">
                  <div class="card-body">
                    <h3>Receptor</h3>
                    <div class="row" style="margin-top: -35px;">
                      <div class="col-12 text-end">
                        <button type="button" class="btn btn-primary btn-sm" id="bBuscarReceptor"><i class="fas fa-search"></i> Buscar Cliente</button>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="publicoGeneral">
                          <label class="form-check-label" for="publicoGeneral">
                            Publico en general
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label small">RFC</label>
                        <input type="text" name="rfcReceptor" id="rfcReceptor" class="form-control form-control-sm" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label small">Nombre</label>
                        <input type="text" name="nombreReceptor" id="nombreReceptor" class="form-control form-control-sm" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label small">Uso CFDI</label>
                        <select name="usoCfdi" id="usoCfdi" class="form-select form-select-sm" required>
                          <option value="">--Selecciona un uso CFDI--</option>
                          <option value="G03 - Gastos en general">G03 - Gastos en general</option>
                          <option value="G01 - Adquisición de mercancías">G01 - Adquisición de mercancías</option>
                          <option value="G02 - Devoluciones, descuentos o bonificaciones">G02 - Devoluciones, descuentos o bonificaciones</option>
                          <option value="S01 - Sin efectos fiscales">S01 - Sin efectos fiscales</option>
                          <option value="CP01 - Pagos">CP01 - Pagos</option>
                          <option value="I01 - Construcciones">I01 - Construcciones</option>
                          <option value="I02 - Mobiliario y equipo de oficina">I02 - Mobiliario y equipo de oficina</option>
                          <option value="I03 - Equipo de transporte">I03 - Equipo de transporte</option>
                          <option value="I04 - Equipo de cómputo">I04 - Equipo de cómputo</option>
                          <option value="I05 - Dados, troqueles, moldes">I05 - Dados, troqueles, moldes</option>
                          <option value="I06 - Comunicaciones telefónicas">I06 - Comunicaciones telefónicas</option>
                          <option value="I07 - Comunicaciones satelitales">I07 - Comunicaciones satelitales</option>
                          <option value="I08 - Otra maquinaria y equipo">I08 - Otra maquinaria y equipo</option>
                          <option value="D01 - Honorarios médicos, dentales">D01 - Honorarios médicos, dentales</option>
                          <option value="D02 - Gastos médicos por incapacidad">D02 - Gastos médicos por incapacidad</option>
                          <option value="D03 - Gastos funerales">D03 - Gastos funerales</option>
                          <option value="D04 - Donativos">D04 - Donativos</option>
                          <option value="D05 - Intereses reales">D05 - Intereses reales</option>
                          <option value="D06 - Aportaciones para el retiro">D06 - Aportaciones para el retiro</option>
                          <option value="D07 - Primas de seguros">D07 - Primas de seguros</option>
                          <option value="D08 - Gastos de transportación escolar">D08 - Gastos de transportación escolar</option>
                          <option value="D09 - Depósitos en cuentas de ahorro">D09 - Depósitos en cuentas de ahorro</option>
                          <option value="D10 - Pagos por servicios educativos">D10 - Pagos por servicios educativos</option>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label small">Régimen</label>
                        <select name="regimenFiscalReceptor" id="regimenFiscalReceptor" class="form-select form-select-sm" required>
                          <option value="">--Selecciona un régimen fiscal--</option>
                          <option value="601 - General de Ley Personas Morales">601 - General de Ley Personas Morales</option>
                          <option value="612 - Personas Físicas con Actividades Empresariales y Profesionales">612 - Personas Físicas con Actividades Empresariales y Profesionales</option>
                          <option value="626 - Régimen Simplificado de Confianza">626 - Régimen Simplificado de Confianza</option>
                          <option value="605 - Sueldos y Salarios e Ingresos Asimilados a Salarios">605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>
                          <option value="616 - Sin obligaciones fiscales">616 - Sin obligaciones fiscales</option>
                          <option value="603 - Personas Morales con Fines no Lucrativos">603 - Personas Morales con Fines no Lucrativos</option>
                          <option value="606 - Arrendamiento">606 - Arrendamiento</option>
                          <option value="607 - Régimen de Enajenación o Adquisición de Bienes">607 - Régimen de Enajenación o Adquisición de Bienes</option>
                          <option value="608 - Demás ingresos">608 - Demás ingresos</option>
                          <option value="610 - Residentes en el Extranjero sin Establecimiento Permanente en México">610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>
                          <option value="611 - Ingresos por Dividendos (socios y accionistas)">611 - Ingresos por Dividendos (socios y accionistas)</option>
                          <option value="614 - Ingresos por intereses">614 - Ingresos por intereses</option>
                          <option value="615 - Régimen de los ingresos por obtención de premios">615 - Régimen de los ingresos por obtención de premios</option>
                          <option value="620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos">620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>
                          <option value="621 - Incorporación Fiscal">621 - Incorporación Fiscal</option>
                          <option value="622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>
                          <option value="623 - Opcional para Grupos de Sociedades">623 - Opcional para Grupos de Sociedades</option>
                          <option value="624 - Coordinados">624 - Coordinados</option>
                          <option value="625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas">625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label small">Código Postal</label>
                        <input type="text" name="cpReceptor" id="cpReceptor" class="form-control form-control-sm" placeholder="Código Postal" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label small">Email (para enviar factura)</label>
                        <input type="mail" name="emailReceptor" id="emailReceptor" class="form-control form-control-sm" placeholder="Email">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ======== TABLA DE DOCUMENTOS RELACIONADOS ======== -->
            <div class="card mb-3 d-none" id="cardDocRelacionados" style="box-shadow: none; border: 1px solid #EEE;">
              <div class="card-header d-flex align-items-end justify-content-between">
                <div class="fs-6">Documentos Relacionados</div>
                <button type="button" id="bAgregarDocRelacionado" class="btn btn-success btn-sm ml-3">
                  <i class="fa fa-plus"></i> Agregar Documento
                </button>
              </div>

              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm table-hover table-striped table-bordered text-center" id="tablaDocRelacionados">
                    <thead class="table-light">
                      <tr>
                        <th>UUID / Folio</th>
                        <th>Parcialidad</th>
                        <th>Saldo Anterior</th>
                        <th>Importe Pagado</th>
                        <th>Saldo Insoluto</th>
                        <th>Impuestos</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>

                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- ======== TABLA DE CONCEPTOS ======== -->
            <div class="card mb-3" id="cardConceptos" style="box-shadow: none; border: 1px solid #EEE;">
              <div class="card-header d-flex align-items-end justify-content-between">
                <div class="fs-6">Conceptos</div>
                <button type="button" id="bRecargarCoceptosVenta" class="btn btn-outline-primary btn-sm ml-3 d-none">
                  <i class="fa fa-refresh"></i>
                </button>
                <button type="button" id="bAgregarConceptoManual" class="btn btn-success btn-sm ml-3">
                  <i class="fa fa-plus"></i> Agregar Concepto
                </button>
              </div>

              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm table-hover table-striped table-bordered text-center" id="tablaConceptos">
                    <thead class="table-light">
                      <tr>
                        <th>Clave ProdServ</th>
                        <th>Descripción</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th>Descuento</th>
                        <th>Total</th>
                        <th>Impuestos</th>
                        <th>Importe</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>

                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- ======== TOTALES Y BOTONES ======== -->
            <div class="d-flex gap-3">
              <div class="flex-grow-1">
                <div class="totals-box">
                  <div id="totalesFactura">
                    <div class="d-flex justify-content-between">
                      <div class="small-muted">Subtotal</div>
                      <div id="subtotal" class="dinero">0</div>
                    </div>
                    <div class="d-flex justify-content-between">
                      <div class="small-muted">Descuento</div>
                      <div id="descuento" class="dinero">0</div>
                    </div>
                    <div class="d-flex justify-content-between">
                      <div class="small-muted">Total</div>
                      <div id="total" class="dinero">0</div>
                    </div>
                    <div class="d-flex justify-content-between">
                      <div class="small-muted">Impuestos</div>
                      <div id="totalImpuestos" class="dinero">0</div>
                    </div>
                    <div id="verImpuestos">

                    </div>
                  </div>
                  <hr>
                  <div class="d-flex justify-content-between">
                    <div class="fw-bold">TOTAL FINAL</div>
                    <div id="totalFinal" class="fs-4 dinero">0</div>
                  </div>
                </div>
              </div>

              <div style="width:240px">
                <button type="button" id="timbrarBtn" class="btn btn-primary w-100">
                  <i class="fas fa-save"></i> Timbrar y Guardar
                </button>
              </div>
            </div>

          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
        <button type="button" class="btn btn-primary ml-1" id="bGuardarFactura">Guardar <i class="fas fa-save"></i></button>
      </div>
    </div>
  </div>
</div>

<!--/////////////// MODAL AGREGAR CONCEPTO //////////////////////-->
<div class="modal fade" id="modalConcepto" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Concepto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formConcepto">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="c_clave">Clave Prod/Serv</label>
              <div class="input-group">
                <button class="btn btn-outline-secondary" type="button" id="bBuscarClaveProdFac"><i class="fas fa-search"></i></button>
                <input type="text" class="form-control" name="c_clave" id="c_clave" placeholder="Clave de Producto">
                <button class="btn btn-outline-danger" type="button" id="bQuitarClaveProdFac"><span class="fas fa-times"></span></button>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="c_unidad">Clave de Unidad</label>
              <div class="input-group">
                <button class="btn btn-outline-secondary" type="button" id="bBuscarClaveUnidadProdFac"><i class="fas fa-search"></i></button>
                <input type="text" class="form-control" name="c_unidad" id="c_unidad" placeholder="Clave de Unidad">
                <button class="btn btn-outline-danger" type="button" id="bQuitarClaveUnidadProdFac"><span class="fas fa-times"></span></button>
              </div>
            </div>

            <div class="col-md-4 mb-3">
              <label>Descripción</label>
              <input type="text" id="c_descripcion" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
              <label>Cantidad</label>
              <input type="number" id="c_cantidad" class="form-control calcConcepto" value="1">
            </div>

            <div class="col-md-4 mb-3">
              <label>Precio Unitario</label>
              <input type="number" id="c_precio" class="form-control calcConcepto">
            </div>

            <div class="col-md-6 mb-3">
              <label>Descuento (%)</label>
              <input type="number" id="c_descuento_porcentaje" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label>Descuento ($)</label>
              <input type="number" id="c_descuento_dinero" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
              <label>Subtotal</label>
              <input type="text" id="c_subtotal" class="form-control" readonly>
            </div>

            <div class="col-md-4 mb-3">
              <label>Impuestos</label>
              <input type="text" id="c_impuestos_total" class="form-control" readonly>
            </div>

            <div class="col-md-4 mb-3">
              <label>Total</label>
              <input type="text" id="c_total" class="form-control fw-bold" readonly>
            </div>
          </div>
          <hr>
          <h6>Impuestos</h6>
          <div class="row">
            <div class="col-md-3">
              <select id="imp_tipo" class="form-select">
                <option value="Trasladado">Trasladado</option>
                <option value="Retenido">Retención</option>
              </select>
            </div>

            <div class="col-md-3">
              <select id="imp_impuesto" class="form-select">
                <option value="002">IVA</option>
                <option value="001">ISR</option>
                <option value="003">IEPS</option>
              </select>
            </div>

            <div class="col-md-3">
              <select id="imp_tipoFactor" class="form-select">
                <option value="Tasa">Tasa</option>
                <option value="Cuota">Cuota</option>
              </select>
            </div>

            <div class="col-md-3">
              <input type="number" id="imp_tasa" class="form-control" placeholder="Cantidad Tasa / Cuota">
            </div>
          </div>

          <button type="button" id="btnAgregarImpuesto" class="btn btn-sm btn-primary mt-2">
            Agregar impuesto
          </button>

          <table class="table mt-3" id="tablaImpuestos">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Impuesto</th>
                <th>Tasa</th>
                <th>Importe</th>
                <th></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
          <button type="submit" id="btnGuardarConcepto" class="btn btn-success">Agregar concepto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div id="modalClavesProdFac" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Claves de Productos / Servicios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-hover table-striped table-bordered text-center myDataTable" id="tablaClavesProdFac" width="100%">
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
<div id="modalClavesUnidadProdFac" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Claves de Unidad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-hover table-striped table-bordered text-center myDataTable" id="tablaClavesUnidadProdFac" width="100%">
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

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalClienteFacturacion" tabindex="-2">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Clientes</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-striped table-hover text-center table-bordered myDataTable" id="tablaClientesFacturacion" width="100%">
              <thead>
                <tr>
                <tr>
                  <th>Nombre</th>
                  <th>Tipo</th>
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

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalVentasFacturacion" tabindex="-2">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Clientes</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-striped table-hover text-center table-bordered myDataTable" id="tablaVentasFacturas" width="100%">
              <thead>
                <tr>
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

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal fade" id="modalDoctoRelacionado" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Relacionar Factura (PPD)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formDoctoRel">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="fw-bold">UUID del Documento Relacionado</label>
              <input type="text" id="p_uuid" class="form-control" placeholder="Ej: 550e8400-e29b-41d4-a716-446655440000" required>
            </div>

            <div class="col-md-4 mb-3">
              <label>Núm. Parcialidad</label>
              <input type="number" id="p_parcialidad" class="form-control" value="1" min="1" required>
            </div>

            <div class="col-md-4 mb-3">
              <label class="text-primary">Saldo Anterior</label>
              <input type="number" id="p_saldo_ant" class="form-control calcPago" step="0.01" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="text-success">Importe Pagado</label>
              <input type="number" id="p_monto_pagado" class="form-control calcPago" step="0.01" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="text-danger">Saldo Insoluto</label>
              <input type="number" id="p_saldo_insoluto" class="form-control" step="0.01" readonly>
            </div>
          </div>

          <h6>Impuestos</h6>
          <div class="row">
            <div class="col-md-3">
              <select id="p_imp_tipo" class="form-select">
                <option value="Trasladado">Trasladado</option>
                <option value="Retenido">Retenido</option>
              </select>
            </div>

            <div class="col-md-3">
              <select id="p_imp_impuesto" class="form-select">
                <option value="002">IVA</option>
                <option value="001">ISR</option>
                <option value="003">IEPS</option>
              </select>
            </div>

            <div class="col-md-3">
              <select id="p_imp_tipoFactor" class="form-select">
                <option value="Tasa">Tasa</option>
                <option value="Cuota">Cuota</option>
              </select>
            </div>

            <div class="col-md-3">
              <input type="number" id="p_imp_tasa" class="form-control" placeholder="Cantidad Tasa / Cuota">
            </div>
          </div>

          <button type="button" id="btnAgregarImpuestoPago" class="btn btn-sm btn-primary mt-2">
            Agregar impuesto
          </button>

          <table class="table mt-3" id="tablaImpuestosPagos">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Impuesto</th>
                <th>Tasa</th>
                <th>Importe</th>
                <th></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-info">Agregar <i class="fas fa-plus"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>