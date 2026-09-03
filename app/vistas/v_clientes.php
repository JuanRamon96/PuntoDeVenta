<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_clientes" titulo="Clientes" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
            <table id="tablaClientes" class="myDataTable table table-hover table-striped table-bordered text-center" width="100%" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Tipo</th>
                  <th>Nombre</th>
                  <th orden="No">Domicilio</th>
                  <th orden="No">Contacto</th>
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
<div id="modalClientes" class="modal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formDireccion"><button type="submit" id="bFormDireccion" hidden></button></form>
      <form id="formClientes">
        <div class="modal-body">
          <div class="row justify-content-md-center">
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <select class="form-select" id="tipoCliente" name="tipoCliente" aria-label="Tipo">
                  <option value="Física">Física</option>
                  <option value="Moral">Moral</option>
                </select>
                <label>Tipo</label>
              </div>
            </div>
          </div>
          <hr>
          <div class="row" id="razonFisica">
            <div class="col-md-4 mb-3">
              <div class="form-floating mb-3">
                <input type="text" class="form-control inputFisica" name="nombreCliente" id="nombreCliente" placeholder="Nombre">
                <label>Nombre</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-floating mb-3">
                <input type="text" class="form-control inputFisica" name="primerApellidoCliente" id="primerApellidoCliente" placeholder="Primer Apellido">
                <label>Primer Apellido</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-floating mb-3">
                <input type="text" class="form-control inputFisica" name="segundoApellidoCliente" id="segundoApellidoCliente" placeholder="Segundo Apellido">
                <label>Segundo Apellido</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <select class="form-select inputFisica" id="sexoCliente" name="sexoCliente" aria-label="Sexo">
                  <option value="">--Selecciona una opción--</option>
                  <option value="Masculino">Masculino</option>
                  <option value="Femenino">Femenino</option>
                </select>
                <label>Sexo</label>
              </div>
            </div>
          </div>
          <div class="row oculto" id="razonMoral">
            <div class="col-md-4 mb-3">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="razonSocialCliente" id="razonSocialCliente" placeholder="Razón Social" disabled>
                <label>Razón Social</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" name="rfcCliente" id="rfcCliente" placeholder="RFC">
                <label>RFC</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-floating mb-3">
                <select class="form-select" name="regimenCliente" id="regimenCliente">
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
                <label>Régimen Fiscal</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="tel" class="form-control" name="telefonoCliente" id="telefonoCliente" placeholder="Teléfono">
                <label>Teléfono</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="tel" class="form-control" name="segundoTelCliente" id="segundoTelCliente" placeholder="Segundo Teléfono">
                <label>Segundo Teléfono</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-floating">
                <input type="email" class="form-control" name="emailCliente" id="emailCliente" placeholder="Email">
                <label>Email</label>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <h5>Domicilio</h5>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="calleCliente" id="calleCliente" placeholder="Calle">
                <label>Calle</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="noExteriorCliente" id="noExteriorCliente" placeholder="No. Exterior">
                <label>No. Exterior</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="noInteriorCliente" id="noInteriorCliente" placeholder="No. Interior">
                <label>No. Interior</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="coloniaCliente" id="coloniaCliente" placeholder="Colonia">
                <label>Colonia</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="cpCliente" id="cpCliente" placeholder="CP">
                <label>Código Postal</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="ciudadCliente" id="ciudadCliente" placeholder="Ciudad">
                <label>Ciudad</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="estadoCliente" id="estadoCliente" placeholder="Estado">
                <label>Estado</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="paisCliente" id="paisCliente" placeholder="País" value="México">
                <label>País</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="detallesCliente" id="detallesCliente" placeholder="Detalles">
                <label>Detalles</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="latitudCliente" id="latitudCliente" placeholder="Latitud" readonly>
                <label>Latitud</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="longitudCliente" id="longitudCliente" placeholder="Longitud" readonly>
                <label>Longitud</label>
              </div>
            </div>
            <div class="col-md-4 mb-3 pt-2">
              <button type="button" class="btn btn-outline-secondary btn-lg" id="bUbicacionClienteP"><i class="fa-solid fa-location-dot"></i> Ubicación</button>
            </div>
          </div>
          <hr>
          <div class="row oculto" id="contactoMoral">
            <div class="col-12">
              <h5>Contacto</h5>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="contactoCliente" id="contactoCliente" placeholder="Nombre">
                <label>Nombre</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="puestoCliente" id="puestoCliente" placeholder="Peusto">
                <label>Puesto</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="telContactoCliente" id="telContactoCliente" placeholder="Teléfono">
                <label>Teléfono</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="emailContactoCliente" id="emailContactoCliente" placeholder="Email">
                <label>Email</label>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <h3>Domicilios</h3>
            </div>
          </div>
          <div class="row">
            <div class="col-12 table-responsive">
              <table class="table table-hover table-striped table-bordered text-center">
                <thead>
                  <tr>
                    <th>Calle</th>
                    <th>No.</th>
                    <th>Colonia</th>
                    <th>Ciudad</th>
                    <th>Detalles</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody id="verDirecciones">

                </tbody>
                <tfoot>
                  <tr>
                    <td>
                      <input type="text" form="formDireccion" class="form-control" id="calleClienteDire" name="calleClienteDire" placeholder="Calle" required>
                    </td>
                    <td>
                      <input type="text" form="formDireccion" class="form-control mb-3" id="noExteriorClienteDire" name="noExteriorClienteDire" placeholder="No. Exterior" required>
                      <input type="text" form="formDireccion" class="form-control" id="noInteriorClienteDire" name="noInteriorClienteDire" placeholder="No. Interior">
                    </td>
                    <td>
                      <input type="text" form="formDireccion" class="form-control mb-3" id="cpClienteDire" name="cpClienteDire" placeholder="Código Postal" required>
                      <input type="text" form="formDireccion" class="form-control" id="coloniaClienteDire" name="coloniaClienteDire" placeholder="Colonia">
                    </td>
                    <td>
                      <input type="text" form="formDireccion" class="form-control mb-3" id="ciudadClienteDire" name="ciudadClienteDire" placeholder="Ciudad" required>
                      <input type="text" form="formDireccion" class="form-control mb-3" id="estadoClienteDire" name="estadoClienteDire" placeholder="Estado">
                      <input type="text" form="formDireccion" class="form-control" id="paisClienteDire" name="paisClienteDire" placeholder="País" value="México">
                    </td>
                    <td>
                      <textarea form="formDireccion" class="form-control" name="detallesClienteDire" id="detallesClienteDire" rows="3" placeholder="Detalles"></textarea>
                    </td>
                    <td>
                      <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="bUbicacionDomCli"><i class="fa-solid fa-location-dot"></i> Ubicación</button>
                      <input type="text" form="formDireccion" class="form-control form-control-sm mb-3" id="latitudClienteDire" name="latitudClienteDire" placeholder="Latitud" readonly>
                      <input type="text" form="formDireccion" class="form-control form-control-sm mb-3" id="longitudClienteDire" name="longitudClienteDire" placeholder="Longitud" readonly>
                    </td>
                    <td>
                      <button type="button" class="btn btn-success" id="bAgregarDireccion"><i class="fas fa-plus"></i></button>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
          <button type="submit" class="btn btn-danger" id="bGuardarCliente">Guardar <i class="fas fa-save"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!--/////////////////////////modal/////////////////////////-->
<div id="modalDireccion" class="modal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Dirección</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formMDireccion">
        <div class="modal-body">
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label>Calle</label>
                <input type="text" id="calleClienteM" name="calleClienteM" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label>No. Exterior</label>
                <input type="number" id="noExteriorClienteM" name="noExteriorClienteM" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label>No. Interior</label>
                <input type="text" id="noInteriorClienteM" name="noInteriorClienteM" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label>CP</label>
                <input type="text" id="cpClienteM" name="cpClienteM" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label>Colonia</label>
                <input type="text" id="coloniaClienteM" name="coloniaClienteM" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label>Ciudad</label>
                <input type="text" id="ciudadClienteM" name="ciudadClienteM" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label>Estado</label>
                <input type="text" id="estadoClienteM" name="estadoClienteM" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label>País</label>
                <input type="text" id="paisClienteM" name="paisClienteM" class="form-control" value="México">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-5">
              <div class="form-group mb-3">
                <label>Latitud</label>
                <input type="text" id="latitudClienteM" name="latitudClienteM" class="form-control" readonly>
              </div>
            </div>
            <div class="col-5">
              <div class="form-group mb-3">
                <label>Longitud</label>
                <input type="text" id="longitudClienteM" name="longitudClienteM" class="form-control" readonly>
              </div>
            </div>
            <div class="col-2" style="padding-top: 30px;">
              <button type="button" class="btn btn-outline-secondary btn-sm" id="bUbicacionClienteM"><i class="fa-solid fa-location-dot"></i> Ubicación</button>
            </div>
            <div class="col-12">
              <div class="form-group mb-3">
                <label>Detalles</label>
                <textarea id="detallesClienteM" name="detallesClienteM" class="form-control"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-outline-secondary" data-dismiss="modal">Cerrar <i class="fe fe-x"></i></button>
          <button type="submit" class="btn mb-2 btn-danger" id="bGuardarDireccion">Guardar <i class="fe fe-save"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div id="modalUbicacionCliente" class="modal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ubicación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div id="mapaUbicacionCliente" style="width:100%; height:400px;">

            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar <i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>
</div>