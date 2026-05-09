<div class="row" style="margin-top: -40px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_impuestos" titulo="Impuestos" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
            <table id="tablaImpuestos" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th style="width: 20%;">Nombre</th>
                  <th style="width: 20%;">Porcentaje</th>
                  <th style="width: 20%;">Clave CFDI</th>
                  <th style="width: 20%;">Tipo Factor</th>
                  <th style="width: 10%;">Clase</th>
                  <th style="width: 10%;" orden="No">Acciones</th>
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

<!--//////////////////////////Modal////////////////////////////-->
<div class="modal fade" id="ModalImpuestos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="z-index: 9999 !important;">
    <div class="modal-content">
      <div class="modal-header bg-inverse bd-inverse-darken">
        <h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold;"><span id="TituloModalImpuestos"></span> Impuesto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="FormImpuestos">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <select name="ClaveImpuesto" id="ClaveImpuesto" class="form-select">
                  <option selected disabled value=""> - Seleccione - </option>
                  <option value="001">(001) ISR</option>
                  <option value="002">(002) IVA</option>
                  <option value="003">(003) IEPS</option>
                </select>
                <label for="ClaveImpuesto">Clave CFDI</label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="NombreImpuesto" name="NombreImpuesto" placeholder="Ingresa el nombre del impuesto" readonly>
                <label for="NombreImpuesto">Nombre del impuesto</label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <select name="ClaseImpuesto" id="ClaseImpuesto" class="form-select">
                  <option selected disabled value=""> - Seleccione - </option>
                  <option value="Trasladado">Trasladado</option>
                  <option value="Retenido">Retenido</option>
                </select>
                <label for="ClaseImpuesto">Clase</label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <select name="TipoFactorImpuesto" id="TipoFactorImpuesto" class="form-select">
                  <option selected disabled value=""> - Seleccione - </option>
                  <option value="Tasa">Tasa</option>
                  <option value="Cuota">Cuota</option>
                  <option value="Exento">Exento</option>
                </select>
                <label for="TipoFactorImpuesto">Tipo de Factor</label>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 mb-3">
              <div class="form-floating">
                <input type="number" min="0" step="any" class="form-control" id="PorcentajeImpuesto" name="PorcentajeImpuesto" placeholder="Ingresa el valor del impuesto">
                <label for="PorcentajeImpuesto">Valor</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> Cancelar</button>
          <button type="submit" class="btn btn-danger" id="GuardarImpuesto" attrid="" tipo="insertar"><i class="fa fa-check-circle"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>