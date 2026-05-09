<?php
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=clientes.xlsx"); 
		
	require 'vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 
	
	session_start();

	if (isset($_SESSION['user_gaheto'])) {
		$con = new mysqli("localhost", "root", "", "gaheto");

	 	extract($_GET);

		$buscar =  $con->real_escape_string($buscar);

		$busqueda = '';
		if(trim($buscar) != ''){
			$separa = explode(' ', trim($buscar));
			$busqueda = 'WHERE ';
			for ($i=0; $i < count($separa); $i++) { 
				$busqueda .= "CONCAT(DATE_FORMAT(clientes.Fecha_Registro, '%d-%m-%Y %r'), clientes.Tipo, Desarrollo_Interes, Lote_Interes, clientes.Nombre, clientes.Primer_Apellido, clientes.Segundo_Apellido, Nacionalidad, CURP, RFC, No_Pasaporte, Calle, No_Interior, No_Exterior, Colonia, CP, Ciudad, Estado, Pais, clientes.Telefono, Segundo_Telefono, Email, Actividad_Preponderante, Nombre_Legal, Calle_Fiscal, No_Interior_Fiscal, No_Exterior_Fiscal, Colonia_Fiscal, CP_Fiscal, Ciudad_Fiscal, Estado_Fiscal, Pais_Fiscal, Telefono_Representante, Segundo_Telefono_Representante, Email_Representante) REGEXP '".$separa[$i]."'";
				if($i < (count($separa)-1)){
					$busqueda .= ' AND ';
				}
			}
		}

		$solo = '';
		if($_SESSION['user_gaheto']['Tipo'] == 'Asesor' && $_SESSION['user_gaheto']['Permisos']['Clientes'][8] == '1'){
			if(trim($busqueda) != ''){
				$solo = "AND FK_Vendedor = '". $_SESSION['user_gaheto']['ID_Usuario']."'";
			}else{
				$solo = "WHERE FK_Vendedor = '". $_SESSION['user_gaheto']['ID_Usuario']."'";
			}
		}

		$firstHtmlString = '<table>
			<thead>
				<tr>
					<th>Fecha Registro</th>
					<th>Tipo</th>
					<th>Desarrollo de Interes</th>
					<th>Lote de Interes</th>
					<th>Nombre</th>
					<th>Sexo</th>
					<th>Nacionalidad</th>
					<th>No. de Pasaporte</th>
					<th>Fecha de Nacimiento</th>
					<th>Lugar de Nacimiento</th>
					<th>Estado Civil</th>
					<th>Cónyuge o Councubina</th>
					<th>Régimen Matrimonial</th>
					<th>CURP</th>
					<th>RFC</th>
					<th>Calle</th>
					<th>No. Interior</th>
					<th>No. Exterior</th>
					<th>Colonia</th>
					<th>CP</th>
					<th>Ciudad</th>
					<th>Estado</th>
					<th>País</th>
					<th>Teléfono</th>
					<th>Segundo Teléfono</th>
					<th>Actividad Preponderante</th>
					<th>Email</th>
					<th>Valida</th>
					<th>Motivo</th>
					<th>Como Conoció el Desarrollo</th>
					<th>Estatus</th>
					<th>Activo</th>
					<th>Temporal</th>
					<th>Asesor</th>
				</tr>
			</thead>
			<tbody>';

		$secondHtmlString = '<table>
			<thead>
				<tr>
					<th>Fecha Registro</th>
					<th>Tipo</th>
					<th>Desarrollo de Interes</th>
					<th>Lote de Interes</th>
					<th>Nombre</th>
					<th>Fecha de Constitución</th>
					<th>RFC</th>
					<th>Actividad Preponderante</th>
					<th>FME</th>
					<th>Teléfono</th>
					<th>Segundo Teléfono</th>
					<th>Calle (Comercial)</th>
					<th>No. Interior</th>
					<th>No. Exterior</th>
					<th>Colonia</th>
					<th>CP</th>
					<th>Ciudad</th>
					<th>Estado</th>
					<th>País</th>
					<th>Calle (Fiscal)</th>
					<th>No. Interior</th>
					<th>No. Exterior</th>
					<th>Colonia</th>
					<th>CP</th>
					<th>Ciudad</th>
					<th>Estado</th>
					<th>País</th>
					<th>Nombre Fedatario Público</th>
					<th>No. Fedatario Público</th>
					<th>Plaza Fedatario Público</th>
					<th>No. Escritura</th>
					<th>Folio Escritura</th>
					<th>Libro Escritura</th>
					<th>Sección Escritura</th>
					<th>Nombre Representante</th>
					<th>Primer Apellido</th>
					<th>Segundo Apellido</th>
					<th>Sexo</th>
					<th>Nacionalidad</th>
					<th>Fecha de Nacimiento</th>
					<th>Lugar de Nacimiento</th>
					<th>Estado Civil</th>
					<th>Cónyuge o Councubina</th>
					<th>Régimen Matrimonial</th>
					<th>CURP</th>
					<th>RFC</th>
					<th>Calle (Representante)</th>
					<th>No. Interior</th>
					<th>No. Exterior</th>
					<th>Colonia</th>
					<th>CP</th>
					<th>Ciudad</th>
					<th>Estado</th>
					<th>País</th>
					<th>Teléfono (Representante)</th>
					<th>Segundo Teléfono</th>
					<th>Email (Representante)</th>
					<th>Como Conoció el Desarrollo</th>
					<th>Email</th>
					<th>Valida</th>
					<th>Motivo</th>
					<th>Estatus</th>
					<th>Activo</th>
					<th>Temporal</th>
					<th>Asesor</th>
				</tr>
			</thead>
			<tbody>';

		$query = "SELECT ID_Cliente, DATE_FORMAT(clientes.Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, DATE_FORMAT(Fecha_Constitucion, '%d-%m-%Y %r') AS Fecha_Constitucion, clientes.Tipo AS Tipo, Desarrollo_Interes, Lote_Interes, IF(clientes.Tipo = 'Persona Moral', Nombre_Legal, CONCAT(clientes.Nombre, ' ', clientes.Primer_Apellido, ' ', clientes.Segundo_Apellido)) AS Nombre, clientes.Primer_Apellido AS Primer_Apellido, clientes.Segundo_Apellido AS Segundo_Apellido, clientes.Fecha_Nacimiento AS Fecha_Nacimiento, Lugar_Nacimiento, clientes.Sexo AS Sexo, Nacionalidad, Estado_Civil, Nombre_Pareja, Regimen_Matrimonial, CURP, RFC, No_Pasaporte, Calle, No_Interior, No_Exterior, Colonia, CP, Ciudad, Estado, Pais, clientes.Telefono AS Telefono, Segundo_Telefono, Email, Actividad_Preponderante, clientes.Foto AS Foto, Valida, Motivo, Calle_Fiscal, No_Interior_Fiscal, No_Exterior_Fiscal, Colonia_Fiscal, CP_Fiscal, Ciudad_Fiscal, Estado_Fiscal, Pais_Fiscal, RFC_Representante, Calle_Representante, No_Interior_Representante, No_Exterior_Representante, Colonia_Representante, CP_Representante, Ciudad_Representante, Estado_Representante, Pais_Representante, Telefono_Representante, Segundo_Telefono_Representante, Email_Representante, Folio_Mercantil, Nombre_Fedatario, Numero_Fedatario, Plaza_Fedatario, Numero_Escritura, Folio_Escritura, Libro_Escritura, Seccion_Escritura, Como_Conocio, clientes.Estatus AS Estatus, clientes.Activo AS Activo, clientes.Temporal AS Temporal, CONCAT(usuarios.Nombre, ' ', usuarios.Primer_Apellido, ' ', usuarios.Segundo_Apellido) AS Vendedor, (SELECT COUNT(*) FROM clientes $busqueda $solo) AS Num FROM clientes LEFT JOIN usuarios ON FK_Vendedor = ID_Usuario AND usuarios.Tipo = 'Asesor' $busqueda $solo";
		
		if($res=$con->query($query)){
			if ($res->num_rows > 0) {
				while($row = $res->fetch_assoc()){
					$valida = 'No';
					if($row['Valida'] == '1'){
						$valida = 'Si';
					}

					$activo = 'No';
					if($row['Activo'] == '1'){
						$activo = 'Si';
					}

					$temporal = 'No';
					if($row['Temporal'] == '1'){
						$temporal = 'Si';
					}

					if($row['Tipo'] == 'Pesona Física'){
						$firstHtmlString .= '<tr>
			                <td>'.$row['Fecha_Registro'].'</td>
							<td>'.$row['Tipo'].'</td>
							<td>'.$row['Desarrollo_Interes'].'</td>
							<td>'.$row['Lote_Interes'].'</td>
							<td>'.$row['Nombre'].'</td>
							<td>'.$row['Sexo'].'</td>
							<td>'.$row['Nacionalidad'].'</td>
							<td>'.$row['No_Pasaporte'].'</td>
							<td>'.$row['Fecha_Nacimiento'].'</td>
							<td>'.$row['Lugar_Nacimiento'].'</td>
							<td>'.$row['Estado_Civil'].'</td>
							<td>'.$row['Nombre_Pareja'].'</td>
							<td>'.$row['Regimen_Matrimonial'].'</td>
							<td>'.$row['CURP'].'</td>
							<td>'.$row['RFC'].'</td>
							<td>'.$row['Calle'].'</td>
							<td>'.$row['No_Interior'].'</td>
							<td>'.$row['No_Exterior'].'</td>
							<td>'.$row['Colonia'].'</td>
							<td>'.$row['CP'].'</td>
							<td>'.$row['Ciudad'].'</td>
							<td>'.$row['Estado'].'</td>
							<td>'.$row['Pais'].'</td>
							<td>'.$row['Telefono'].'</td>
							<td>'.$row['Segundo_Telefono'].'</td>
							<td>'.$row['Actividad_Preponderante'].'</td>
							<td>'.$row['Email'].'</td>
							<td>'.$valida.'</td>
							<td>'.$row['Motivo'].'</td>
							<td>'.$row['Como_Conocio'].'</td>
							<td>'.$row['Estatus'].'</td>
							<td>'.$activo.'</td>
							<td>'.$temporal.'</td>
							<td>'.$row['Vendedor'].'</td>
			           	</tr>';
					}else{
						$secondHtmlString .= '<tr>
							<td>'.$row['Fecha_Registro'].'</td>
							<td>'.$row['Tipo'].'</td>
							<td>'.$row['Desarrollo_Interes'].'</td>
							<td>'.$row['Lote_Interes'].'</td>
							<td>'.$row['Nombre'].'</td>
							<td>'.$row['Fecha_Constitucion'].'</td>
							<td>'.$row['RFC'].'</td>
							<td>'.$row['Actividad_Preponderante'].'</td>
							<td>'.$row['Folio_Mercantil'].'</td>
							<td>'.$row['Telefono'].'</td>
							<td>'.$row['Segundo_Telefono'].'</td>
							<td>'.$row['Calle'].'</td>
							<td>'.$row['No_Interior'].'</td>
							<td>'.$row['No_Exterior'].'</td>
							<td>'.$row['Colonia'].'</td>
							<td>'.$row['CP'].'</td>
							<td>'.$row['Ciudad'].'</td>
							<td>'.$row['Estado'].'</td>
							<td>'.$row['Pais'].'</td>
							<td>'.$row['Calle_Fiscal'].'</td>
							<td>'.$row['No_Interior_Fiscal'].'</td>
							<td>'.$row['No_Exterior_Fiscal'].'</td>
							<td>'.$row['Colonia_Fiscal'].'</td>
							<td>'.$row['CP_Fiscal'].'</td>
							<td>'.$row['Ciudad_Fiscal'].'</td>
							<td>'.$row['Estado_Fiscal'].'</td>
							<td>'.$row['Pais_Fiscal'].'</td>
							<td>'.$row['Nombre_Fedatario'].'</td>
							<td>'.$row['Numero_Fedatario'].'</td>
							<td>'.$row['Plaza_Fedatario'].'</td>
							<td>'.$row['Numero_Escritura'].'</td>
							<td>'.$row['Folio_Escritura'].'</td>
							<td>'.$row['Libro_Escritura'].'</td>
							<td>'.$row['Seccion_Escritura'].'</td>
							<td>'.$row['Nombre'].'</td>
							<td>'.$row['Primer_Apellido'].'</td>
							<td>'.$row['Segundo_Apellido'].'</td>
							<td>'.$row['Sexo'].'</td>
							<td>'.$row['Nacionalidad'].'</td>
							<td>'.$row['Fecha_Nacimiento'].'</td>
							<td>'.$row['Lugar_Nacimiento'].'</td>
							<td>'.$row['Estado_Civil'].'</td>
							<td>'.$row['Nombre_Pareja'].'</td>
							<td>'.$row['Regimen_Matrimonial'].'</td>
							<td>'.$row['CURP'].'</td>
							<td>'.$row['RFC_Representante'].'</td>
							<td>'.$row['Calle_Representante'].'</td>
							<td>'.$row['No_Interior_Representante'].'</td>
							<td>'.$row['No_Exterior_Representante'].'</td>
							<td>'.$row['Colonia_Representante'].'</td>
							<td>'.$row['CP_Representante'].'</td>
							<td>'.$row['Ciudad_Representante'].'</td>
							<td>'.$row['Estado_Representante'].'</td>
							<td>'.$row['Pais_Representante'].'</td>
							<td>'.$row['Telefono_Representante'].'</td>
							<td>'.$row['Segundo_Telefono_Representante'].'</td>
							<td>'.$row['Email_Representante'].'</td>
							<td>'.$row['Como_Conocio'].'</td>
							<td>'.$row['Email'].'</td>
							<td>'.$row['Valida'].'</td>
							<td>'.$row['Motivo'].'</td>
							<td>'.$row['Estatus'].'</td>
							<td>'.$row['Activo'].'</td>
							<td>'.$row['Temporal'].'</td>
							<td>'.$row['Vendedor'].'</td>
						</tr>';
					}
				}
			}
		}else{
			echo "Error: ".mysqli_error($con);
		}

			$firstHtmlString .= '</tbody>
		</table>';
			$secondHtmlString .= '</tbody>
		</table>';

		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
		$spreadsheet = $reader->loadFromString($firstHtmlString);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Pesonas Físicas');
		$reader->setSheetIndex(1);
		$spreadhseet = $reader->loadFromString($secondHtmlString, $spreadsheet);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Pesonas Morales');

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save("php://output");
	}

	exit;
?>