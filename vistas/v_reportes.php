<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_reportes" titulo="Reportes" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
  </div>
</div>
<br>
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
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control fechasVenRep" id="fechaInicioVentas" name="fechaInicioVentas" placeholder="Fecha Inicio">
              <label>Fecha inicio</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control fechasVenRep" id="fechaFinVentas" name="fechaFinVentas" placeholder="Fecha Inicio">
              <label>Fecha Fin</label>
            </div>
          </div>
        </div>
        <br>
        <br>
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
        <br>
        <div class="row">
          <div class="col-12" id="chartVentas" style="height: 600px;">
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>