<div class="row" style="margin-bottom: 30px; margin-top: -50px;">
	<div class="col-12 text-right">
		<button type="button" class="btn btn-sm btn-outline-danger cargarVista" carga="v_config_facturacion" titulo="Configuración"><i class="fas fa-rotate-right"></i></button>
	</div>
</div>

<div>
	<div id="content" class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-10">
					<h1 style="font-weight: bold;" id="vistaTitulo"></h1>
				</div>
			</div>
			<br>
			<form id="formFacturacion" class="row">
				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<input type="text" class="form-control" id="rfcFacturacion" name="rfcFacturacion" placeholder="RFC" value="#rfc#">
						<label>RFC</label>
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<input type="text" class="form-control" id="nombreFacturacion" name="nombreFacturacion" placeholder="Nombre/Razón social" value="#nombre#">
						<label>Nombre / Razón social</label>
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<input type="text" class="form-control" id="domicilioFacturacion" name="domicilioFacturacion" placeholder="Domicilio" value="#domicilio#">
						<label>Domicilio</label>
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<input type="text" class="form-control" id="cpFacturacion" name="cpFacturacion" placeholder="Código Postal" value="#cp#">
						<label>Código Postal</label>
					</div>
				</div>
				<div class="col-md-12 mb-3">
					<div class="form-floating mb-3">
						<select class="form-select" name="regimenFacturacion" id="regimenFacturacion">
							<option value="">- Seleccione una opción -</option>
							<option value="601 - General de Ley Personas Morales">601 - General de Ley Personas Morales</option>
							<option value="603 - Personas Morales con Fines no Lucrativos">603 - Personas Morales con Fines no Lucrativos</option>
							<option value="605 - Sueldos y Salarios e Ingresos Asimilados a Salarios">605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>
							<option value="606 - Arrendamiento">606 - Arrendamiento</option>
							<option value="607 - Régimen de Enajenación o Adquisición de Bienes">607 - Régimen de Enajenación o Adquisición de Bienes</option>
							<option value="608 - Demás ingresos">608 - Demás ingresos</option>
							<option value="610 - Residentes en el Extranjero sin Establecimiento Permanente en México">610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>
							<option value="611 - Ingresos por Dividendos (socios y accionistas)">611 - Ingresos por Dividendos (socios y accionistas)</option>
							<option value="612 - Personas Físicas con Actividades Empresariales y Profesionales">612 - Personas Físicas con Actividades Empresariales y Profesionales</option>
							<option value="614 - Ingresos por intereses">614 - Ingresos por intereses</option>
							<option value="615 - Régimen de los ingresos por obtención de premios">615 - Régimen de los ingresos por obtención de premios</option>
							<option value="616 - Sin obligaciones fiscales">616 - Sin obligaciones fiscales</option>
							<option value="620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos">620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>
							<option value="621 - Incorporación Fiscal">621 - Incorporación Fiscal</option>
							<option value="622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>
							<option value="623 - Opcional para Grupos de Sociedades">623 - Opcional para Grupos de Sociedades</option>
							<option value="624 - Coordinados">624 - Coordinados</option>
							<option value="625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas">625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>
							<option value="626 - Régimen Simplificado de Confianza">626 - Régimen Simplificado de Confianza</option>
						</select>
						<label>Régimen Fiscal</label>
					</div>
				</div>
				<div class="col-12">
					<div class="alert alert-warning d-flex align-items-center" role="alert">
						<div>
							<strong>⚠️ Importante: Asegúrate de subir tu CSD y NO tu e.firma (FIEL)</strong>
							<ul class="mb-0 mt-1">
								<li>El <strong>CSD</strong> es exclusivo para timbrar y expedir facturas.</li>
								<li>Si subes los archivos de la <strong>e.firma / FIEL</strong>, el sistema no podrá timbrar tus comprobantes.</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Certificado (.cer)</label>
					<input type="file" class="form-control" id="certificadoFacturacion" name="certificadoFacturacion" accept=".cer">
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Key (.key)</label>
					<input type="file" class="form-control" id="keyFacturacion" name="keyFacturacion" accept=".key">
				</div>
				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<input type="password" class="form-control" id="contraFacturacion" name="contraFacturacion" placeholder="Contraseñal">
						<label>Contraseña de los certificados</label>
					</div>
				</div>
				<div class="col-12 d-grid mb-3">
					<button type="submit" class="btn btn-primary btn-lg">Guardar <i class="fas fa-save"></i></button>
				</div>
			</form>
		</div>
	</div>
</div>