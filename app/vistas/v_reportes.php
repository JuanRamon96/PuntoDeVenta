<div class="row mb-4" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_reportes" titulo="Reportes" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-12 text-end">
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12">
            <h4>Ventas</h4>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <div class="form-floating">
              <input type="date" class="form-control fechasVenRep" id="fechaInicioVentas" name="fechaInicioVentas" placeholder="Fecha Inicio">
              <label>Fecha inicio</label>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="form-floating">
              <input type="date" class="form-control fechasVenRep" id="fechaFinVentas" name="fechaFinVentas" placeholder="Fecha Inicio">
              <label>Fecha Fin</label>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="form-floating">
              <select class="form-select" id="usuarioReporte" name="usuarioReporte">
                #usuariosReporte#
              </select>
              <label>Usuario</label>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="form-floating">
              <select class="form-select" id="cajaReporte" name="cajaReporte">
                #cajasReporte#
              </select>
              <label>Caja</label>
            </div>
          </div>
        </div>
        <br>
        <div class="row" id="resumenGastos">
          <div class="col-md-4 col-6 mb-3">
            <div class="card text-center border-danger">
              <div class="card-body">
                <h6 class="card-title text-muted">Total Gastos</h6>
                <h4 class="card-text text-danger" id="totalGastosValor">$0.00</h4>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6 mb-3">
            <div class="card text-center border-success">
              <div class="card-body">
                <h6 class="card-title text-muted">Ganancia Ventas</h6>
                <h4 class="card-text text-success" id="gananciaVentasValor">$0.00</h4>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6 mb-3">
            <div class="card text-center border-primary">
              <div class="card-body">
                <h6 class="card-title text-muted">Diferencia (Neto)</h6>
                <h4 class="card-text" id="diferenciaValor">$0.00</h4>
              </div>
            </div>
          </div>
        </div>
        <br>
        <div class="row mb-3">
          <div class="col-12">
            <h4>Ventas</h4>
          </div>
        </div>
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaVentasReportes" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Caja</th>
                  <th>Folio</th>
                  <th>Tipo Pago</th>
                  <th>Costo</th>
                  <th>Total</th>
                  <th>Ganancia</th>
                  <th>Usuario</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
              <tfoot>

              </tfoot>
            </table>
          </div>
        </div>
        <br>
        <div class="row mb-3">
          <div class="col-12">
            <h4>Gastos</h4>
          </div>
        </div>
        <div class="row">
          <div class="col-12 table-responsive">
            <table id="tablaGastosReportes" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha Registro</th>
                  <th>Monto</th>
                  <th>Descripción</th>
                  <th>Usuario</th>
                  <th>Fecha Gasto</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>

              </tfoot>
            </table>
          </div>
        </div>
        <br>
        <hr>
        <br>
        <div class="row">
          <div class="col-12">
            <h4>Resumen Ventas</h4>
          </div>
        </div>
        <div class="row">
          <div class="col-12" id="chartVentas" style="height: 600px;">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-12 text-end">
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-12">
            <h4>Productos más vendidos</h4>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control fechasProdRep" id="fechaInicioProd" name="fechaInicioProd" placeholder="Fecha Inicio">
              <label>Fecha inicio</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control fechasProdRep" id="fechaFinProd" name="fechaFinProd" placeholder="Fecha Inicio">
              <label>Fecha Fin</label>
            </div>
          </div>
        </div>
        <br>
        <br>
        <div class="row">
          <div class="col-12" id="chartProductos" style="height: 600px;">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>