<div class="row" style="margin-top: -65px;">
  <div class="col-12 text-end">
    <button type="button" class="btn btn-outline-danger btn-sm cargarVista" carga="v_clasificaciones" titulo="Clasificaciones" id="bRecargar"><i class="fa-solid fa-rotate-right"></i></button>
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
            <table class="table table table-hover table-bordered text-center myDataTable" id="tablaClasificaciones" width="100%" style="font-size: 12px;">
              <thead>
                <th>Nombre</th>
                <th>Descripción</th>
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

<!-- ///////////////////////////Modal////////////////////////////// -->
<div class="modal fade" id="modalClasificacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-inverse bd-inverse-darken">
        <h5 class="modal-title">Clasificación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formClasificaciones">
        <div class="modal-body">
          <div class="row justify-content-md-center">
            <div class="col-md-6 text-center">
              <input type="file" id="imgClasificacion" name="imgClasificacion" style="display: none;" accept="image/*">
              <p class="text-right" style="margin-bottom: -20px;">
                <button type="button" class="btn btn-icon btn-danger rounded-circle btn-sm botones-imagenes" id="bModificarImgClasificacion" title="Modificar">
                  <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="btn btn-icon btn-danger rounded-circle btn-sm botones-imagenes oculto" id="bCancelarImgClasificacion" title="Cancelar" foto="">
                  <i class="fas fa-times"></i>
                </button>
              </p>
              <br>
              <a href="vistas/assets/images/fondo.jpg" data-fancybox="images">
                <div id="mosImgClasificacion" style="background-image: url('vistas/assets/images/fondo.jpg'); width: 280px; height: 180px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer;"></div>
              </a>
              <br>
              <br>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-3">
              <div class="form-floating">
                  <input type="text" class="form-control" id="nombreClasificacion" name="nombreClasificacion" placeholder="Ingresa la marca">
                  <label>Nombre</label>
              </div>
            </div>
            <div class="col-12 mb-3">
              <div class="form-floating">
                  <input type="text" class="form-control" id="descripcionClasificacion" name="descripcionClasificacion" placeholder="Ingresa el modelo">
                  <label>Descripción</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i> Cerrar</button>
          <button type="submit" class="btn btn-primary" id="bGuardarClasificacion"><i class="fa fa-check-circle"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>