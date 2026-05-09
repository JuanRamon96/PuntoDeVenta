<?php
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=usuarios.xlsx"); 
		
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
				$busqueda .= "CONCAT(DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r'), Tipo, Nombre, Primer_Apellido, Segundo_Apellido, Correo, Estatus) REGEXP '".$separa[$i]."'";
				if($i < (count($separa)-1)){
					$busqueda .= ' AND ';
				}
			}
		}

		$htmlString = '<table>
			<thead>
				<tr>
					<th>Fecha Registro</th>
					<th>Tipo</th>
					<th>Nombre</th>
					<th>Primer Apellido</th>
					<th>Segundo Apellido</th>
					<th>Genero</th>
					<th>Correo</th>
					<th>Teléfono</th>
					<th>Estatus</th>
				</tr>
			</thead>
			<tbody>';
			//<th>Activo</th>
			//<th>Temporal</th>

		$query = "SELECT ID_Usuario, Tipo, Nombre, Primer_Apellido, Segundo_Apellido, Genero, Fecha_Nacimiento, Telefono, Correo, Estatus, Activo, Temporal, DATE_FORMAT(Fecha_Registro, '%d-%m-%Y %r') AS Fecha_Registro, (SELECT COUNT(*) FROM usuarios $busqueda) AS Num FROM usuarios $busqueda";
		
		if($res=$con->query($query)){
			if ($res->num_rows > 0) {
				while($row = $res->fetch_assoc()){
					/*$activo = 'No';
					if($row['Activo'] == '1'){
						$activo = 'Si';
					}

					$temporal = 'No';
					if($row['Temporal'] == '1'){
						$temporal = 'Si';
					}*/

					$htmlString .= '<tr>
			            <td>'.$row['Fecha_Registro'].'</td>
						<td>'.$row['Tipo'].'</td>
						<td>'.$row['Nombre'].'</td>
						<td>'.$row['Primer_Apellido'].'</td>
						<td>'.$row['Segundo_Apellido'].'</td>
						<td>'.$row['Genero'].'</td>
						<td>'.$row['Correo'].'</td>
						<td>'.$row['Telefono'].'</td>
						<td>'.$row['Estatus'].'</td>
						
			        </tr>';
			        //<td>'.$activo.'</td>
			        //<td>'.$temporal.'</td>
				}
			}
		}else{
			echo "Error: ".mysqli_error($con);
		}

			$htmlString .= '</tbody>
		</table>';

		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
		$spreadsheet = $reader->loadFromString($htmlString);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Usuarios');

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save("php://output");
	}

	exit;
?>