<?php
	session_start();
	
	if(ISSET($_SESSION['user_punto_venta'])){  
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$db_host = 'localhost'; 
		$db_name = 'punto_venta'; 
		$db_user = 'root'; 
		$db_pass = ''; 
		
		$fecha = date("Ymd-His"); 
	 
		$salida_sql = $db_name.'_'.$fecha.'.sql'; 
		
		$dump = 'C:\xampp\mysql\bin\mysqldump.exe  --password='.$db_pass.' --user='.$db_user.' '.$db_name.' > '.$salida_sql;
		system($dump, $output); 

		header ("Content-Disposition: attachment; filename=".$salida_sql."");
		header ("Content-Type: application/octet-stream");
		header ("Content-Length: ".filesize($salida_sql));
		readfile($salida_sql);

		unlink($salida_sql); 
		
		/*$zip = new ZipArchive(); 
		
		$salida_zip = $db_name.'_'.$fecha.'.zip';
		
		if($zip->open($salida_zip,ZIPARCHIVE::CREATE) === true) { 
			$zip->addFile($salida_sql); 
			$zip->addGlob('../vistas/assets/images/productos/*'); 
			//$zip->addGlob('../vistas/assets/img/pacientes/*'); 
			$zip->close(); 
			unlink($salida_sql); 
			header ("Content-Disposition: attachment; filename=".$salida_zip."");
			header ("Content-Type: application/octet-stream");
			header ("Content-Length: ".filesize($salida_zip));
			readfile($salida_zip);
			unlink($salida_zip);
		} else {
			echo 'Error'; 
		}*/
	}else{
		http_response_code(400);
	}
?>