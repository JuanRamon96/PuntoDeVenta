<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button class="btn btn-outline-danger btn-sm cargarVista" carga="v_usuarios" titulo="Usuarios" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
            <table id="tablaUsuarios" class="myDataTable table table-hover table-striped table-bordered text-center" width="100">
                <thead>
                  <tr>
                    <th>Fecha de Registro</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Estatus</th>
                    <th>Tipo</th>
                    <th orden="No">Permisos</th>
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
<div class="modal" id="modalUsuarios" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 fw-bold modal-title">Guardar Usuario</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formUsuarios">
          <div class="row">
            <div class="col-12 text-center">
              <input class="d-none" type="file" accept="image/*" name="fotoUsuario" id="fotoUsuario">
              <div class="text-right mb-n5">
                <button id="bModificarFoto" type="button" class="btn btn-icon rounded-circle btn-sm botones-imagenes btn-info">
                  <i class="fas fa-pencil-alt"></i>
                </button>
                <button id="bCancelarFoto" type="button" class="btn btn-icon rounded-circle btn-sm botones-imagenes btn-danger oculto">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 text-center mb-5">
              <a href="vistas/assets/images/default.jpg" data-fancybox="images">
                <div id="mosFotoUsuario" style="background-image: url('vistas/assets/images/default.jpg'); width: 280px; height: 180px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer;"></div>
              </a>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
                <label>Nombre</label>
              </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="form-floating">
                <input type="text" name="primerApellido" id="primerApellido" class="form-control" placeholder="Lopez">
                <label>Primer Apellido</label>
              </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="form-floating">
                <input type="text" name="segundoApellido" id="segundoApellido" class="form-control" placeholder="Lopez">
                <label>Segundo Apellido</label>
              </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="form-floating">
                <input type="email" name="correo" id="correo" class="form-control" placeholder="correo@ejemplo.com" />
                <label>Correo</label>
              </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="form-floating">
                <input type="text" name="puesto" id="puesto" class="form-control" placeholder="Puesto" />
                <label>Puesto</label>
              </div>
            </div>
            <div class="col-12">
              <div class="row align-items-center">
                <div class="form-group col-5 col-lg-4 mb-3">
                  <label for="password" class="form-label">Contraseña</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <button type="button" class="input-group-text pointer" onclick="showHidePassword(this, '#password')">
                        <i class="fas fa-eye"></i>
                        <i class="fas fa-eye-slash d-none"></i>
                      </button>
                    </div>
                    <input class="form-control" type="password" name="password" id="password">
                  </div>
                </div>
                <div class="form-group col-5 col-lg-4 mb-3">
                  <label for="passwordConfirmar" class="form-label">Confirmar Contraseña</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <button type="button" class="input-group-text pointer" onclick="showHidePassword(this, '#passwordConfirmar')">
                        <i class="fas fa-eye"></i>
                        <i class="fas fa-eye-slash d-none"></i>
                      </button>
                    </div>
                    <input class="form-control" type="password" name="passwordConfirmar" id="passwordConfirmar">
                  </div>
                </div>
                <div class="d-none col-2 col-lg-4 custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="changePassword" name="changePassword">
                  <label class="custom-control-label" for="changePassword">¿Cambiar Contraseña?</label>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="form-floating">
                <select name="tipo" id="tipo" class="form-select">
                  <option value="0">Empleado</option>
                  <option value="1">Administrador</option>
                  <!--<option value="2">Tablet</option>-->
                </select>
                <label for="tipo">Tipo</label>
              </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
              <div class="form-floating">
                <select name="estatus" id="estatus" class="form-select">
                  <option value="0">Activo</option>
                  <option value="1">Inactivo</option>
                </select>
                <label for="estatus">Estatus</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary">Cerrar&nbsp;<i class="fas fa-times"></i></button>
              <button type="submit" class="btn btn-primary" id="bGuardarUsuario">Guardar&nbsp;<i class="fas fa-save"></i></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal" id="modalPermisos" tabindex="-2">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <p class="h5 modal-title">Permisos</p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body table-responsive">
        <table class="table table-hover table-bordered text-center" id="tablaPermisos">
          <thead>
            <tr>
              <th>Menú</th>
              <th>Permisos</th>
            </tr>
          </thead>
          <tbody>
            <!--<tr>
              <td>Sucursales</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
              </td>
            </tr>-->
            <tr>
              <td>Productos</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Inventario</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Restar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Merma</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Clasificaciones</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Clientes</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Ventas</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Cancelar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Reimprimir</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Pagos</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Devoluciones</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Cajas</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver cortes</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Proveedores</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Compras</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Cancelar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Pagos</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Gastos</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Usuarios</td>
              <td>
                <div class="form-check">
                  <input id="verUsuarios" type="checkbox" class="form-check-input checkPermisos">
                  <label for="verUsuarios" class="form-check-label">Ver</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Agregar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Modificar</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Eliminar</label>
                </div>
              </td>
            </tr>
            <tr>
              <td>Reportes</td>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input checkPermisos">
                  <label class="form-check-label">Ver</label>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>