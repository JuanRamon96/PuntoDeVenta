<?php
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=cobranza.xlsx"); 
		
	require 'vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 

	function _debe($cliente){
		$con = new mysqli("localhost", "root", "", "gaheto");

		$fechaA = date('Y-m-d'); 
		$estatus = "Al corriente";

		$query = "SELECT ID_Compra, Tipo, FK_Desarrollo, FK_Lote, Total, Monto_Apartado, Fecha_Apartado, Enganche, Diferido, Fecha_Enganche, Pago_Mensual, Fecha_Inicial, No_Meses, Pago_Anual, Pago_Final, No_Pagos_Finales, Fecha_Finales, Firma_Adhesion, Fecha_Registro FROM compras LEFT JOIN copropietarios ON FK_Compra = ID_Compra WHERE compras.FK_Cliente = '$cliente' OR copropietarios.FK_Cliente = '$cliente'";

		if($res=$con->query($query)){
			if ($res->num_rows > 0) {
				while($row = $res->fetch_assoc()){ 
					$arrayPagos = null;
					$query1 = "SELECT ID_Pago, FK_Compra, Monto, Concepto, Fecha_Registro FROM pagos WHERE FK_Compra = '".$row['ID_Compra']."'";

					if($res1=$con->query($query1)){
						if ($res1->num_rows > 0) {
							while ($arrayPagos[] = $res1->fetch_assoc());		
						}
					}else{
						echo "Error: ".mysqli_error($con);
					}

					$separa = explode('-', $row['Fecha_Enganche']);
					$mes = (int) $separa[1];
					$year = (int) $separa[0];

					for ($x=0; $x < $row['Diferido']; $x++) { 
						if($fechaA > date('Y-m-d', strtotime($year.'-'.(str_pad($mes, 2, '0', STR_PAD_LEFT)).'-01'))){
							$estatus = "Atrasado";
						}

						if($arrayPagos != null){
							for ($z=0; $z < count($arrayPagos); $z++) { 
								if($arrayPagos[$z]['Concepto'] == 'Pago Enganche '.($x+1)){
									$estatus = "Al corriente";
									break;
								}	
							}
						}

						if($estatus == "Atrasado"){
							break;
						}

						if($mes == 12){
							$mes = 1;
							$year ++;
						}else{
							$mes++;
						}
					}

					if($estatus == "Atrasado"){
						break;
					}

					$separa = explode('-', $row['Fecha_Inicial']);
					$mes = (int) $separa[1];
					$year = (int) $separa[0];
					$y = 0;

					for ($x=0; $x < $row['No_Meses']; $x++) { 
						if($fechaA > date('Y-m-d', strtotime($year.'-'.(str_pad($mes, 2, '0', STR_PAD_LEFT)).'-01'))){
							$estatus = 'Atrasado';
						}

						if($arrayPagos != null){
							for ($z=0; $z < count($arrayPagos); $z++) { 
								if($arrayPagos[$z]['Concepto'] == 'Pago '.($x+1)){
									$estatus = 'AL corriente';
									break;
								}	
							}
						}

						if($estatus == "Atrasado"){
							break;
						}

						if($mes == 12){
							$y++;

							if($fechaA > date('Y-m-d', strtotime($year.'-'.(str_pad($mes, 2, '0', STR_PAD_LEFT)).'-01'))){
								$estatus = 'Atrasado';
							}

							if($arrayPagos != null){
								for ($z=0; $z < count($arrayPagos); $z++) { 
									if($arrayPagos[$z]['Concepto'] == 'Pago Anual '.($y+1)){
										$estatus = 'Al corriente';
										break;
									}	
								}
							}

							if($estatus == "Atrasado"){
								break;
							}

							$mes = 1;
							$year ++;
						}else{
							$mes++;
						}
					}

					if($estatus == "Atrasado"){
						break;
					}

					$separa = explode('-', $row['Fecha_Finales']);
					$mes = (int) $separa[1];
					$year = (int) $separa[0];

					for ($x=0; $x < $row['No_Pagos_Finales']; $x++) { 
						if($fechaA > date('Y-m-d', strtotime($year.'-'.(str_pad($mes, 2, '0', STR_PAD_LEFT)).'-01'))){
							$estatus = 'Atrasado';
						}

						if($arrayPagos != null){
							for ($z=0; $z < count($arrayPagos); $z++) { 
								if($arrayPagos[$z]['Concepto'] == 'Pago Final '.($x+1)){
									$estatus = 'Al corriente';
									break;
								}	
							}
						}

						if($estatus == "Atrasado"){
							break;
						}

						if($mes == 12){
							$mes = 1;
							$year ++;
						}else{
							$mes++;
						}
					}

					if($estatus == "Atrasado"){
						break;
					}
				}	
			}
		}else{
			echo "Error: ".mysqli_error($con);
		}

		return $estatus;
	}
	
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
				$busqueda .= "CONCAT(Tipo, Nombre_Legal, Nombre, Primer_Apellido, Segundo_Apellido, Email) REGEXP '".$separa."'";
				if($i < (count($separa)-1)){
					$busqueda .= ' AND ';
				}
			}
		}

		$solo = '';
		if($_SESSION['user_gaheto']['Tipo'] == 'Asesor' && $_SESSION['user_gaheto']['Permisos']['Reporte Cobranza'][2] == '1'){
			if(trim($busqueda) != ''){
				$solo = "AND FK_Vendedor = '". $_SESSION['user_gaheto']['ID_Usuario']."'";
			}else{
				$solo = "WHERE FK_Vendedor = '". $_SESSION['user_gaheto']['ID_Usuario']."'";
			}
		}

		$htmlString = '<table>
			<thead>
				<tr>
					<th>Cliente</th>
					<th>Email</th>
					<th>Tipo</th>
					<th>Total Adeudo</th>
					<th>Pagado</th>
					<th>Restante</th>
					<th>A favor</th>
					<th>Estatus</th>
					<th>Firma<th>
				</tr>
			</thead>
			<tbody>';

		$query = "SELECT ID_Cliente, Tipo, IF(Tipo = 'Persona Moral', Nombre_Legal, CONCAT(Nombre, ' ', Primer_Apellido, ' ', Segundo_Apellido)) AS Cliente, Email, IFNULL((SELECT SUM(Total) FROM compras WHERE FK_Cliente = ID_Cliente), 0) AS Total_Compras, IFNULL((SELECT SUM(Total) FROM compras INNER JOIN copropietarios ON FK_Compra = ID_Compra WHERE copropietarios.FK_Cliente = ID_Cliente), 0) AS Total_Copropiedad, IFNULL((SELECT SUM(Monto) FROM pagos INNER JOIN compras ON FK_Compra = ID_Compra WHERE FK_Cliente = ID_Cliente), 0) AS Pago_Compras, IFNULL((SELECT SUM(Monto) FROM pagos INNER JOIN copropietarios ON pagos.FK_Compra = copropietarios.FK_Compra WHERE FK_Cliente = ID_Cliente), 0) AS Pago_Copropiedad, (SELECT IFNULL(COUNT(*), 0) FROM compras WHERE FK_Cliente = ID_Cliente AND Firma_Adhesion = 0) AS Firma_Compras, (SELECT IFNULL(COUNT(*), 0) FROM compras INNER JOIN copropietarios ON FK_Compra = ID_Compra WHERE copropietarios.FK_Cliente = ID_Cliente AND Firma_Adhesion = 0) AS Firma_Copropiedad, (SELECT COUNT(*) FROM clientes $busqueda $solo) AS Num FROM clientes $busqueda $solo";
		
		if($res=$con->query($query)){
			if ($res->num_rows > 0) {
				while($row = $res->fetch_assoc()){
					$favor = 0;
					$restante = ($row['Total_Compras'] + $row['Total_Copropiedad']) - ($row['Pago_Compras'] - $row['Pago_Copropiedad']);
					if($restante < 0){
						$favor = $restante * -1;
						$restante = 0;
					}

					$estatus = _debe($row['ID_Cliente']);

					$firma = '';
					if(($row['Total_Compras'] + $row['Total_Copropiedad']) > 0){
						$firma = 'Si';
					}

	                if(($row['Firma_Compras'] + $row['Firma_Copropiedad']) > 0){
	                	$firma = 'No';
	                }

					$htmlString .= '<tr>
						<td>'.$row['Cliente'].'</td>
						<td>'.$row['Email'].'</td>
						<td>'.$row['Tipo'].'</td>
						<td>'.($row['Total_Compras'] + $row['Total_Copropiedad']).'</td>
						<td>'.($row['Pago_Compras'] - $row['Pago_Copropiedad']).'</td>
						<td>'.$restante.'</td>
						<td>'.$favor.'</td>
						<td>'.$estatus.'</td>
						<td>'.$firma.'</td>
			        </tr>';
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
		$sheet->setTitle('Cobranza');

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save("php://output");
	}

	exit;
?>