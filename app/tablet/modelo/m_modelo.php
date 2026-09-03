<?php 
date_default_timezone_set('America/Mexico_City');
include "config/conexion.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/vendor/autoload.php';

class m_modelo extends conexion{
	public $link;
	public $numerofilas;
	public $error;

	public function __construct()
    {
        $this->link = conexion::__construct();
    }

	public function _insertar($query){
		$result = $this->link->query($query);
		$this->numerofilas = $this->link->affected_rows;
		if (!$result){
			$error = 'si';
		}else{
			$error = 'no';
		}
		return $error;

		$this->link->$con->close();
	}
	
	public function _consultar($query){
		$result = $this->link->query($query);
		$this->numerofilas = $result->num_rows;
		if (!$result) {			
			$this->error = 'si';
			echo "Se produjo un error en el modelo: ".mysqli_error($this->link)." SQL: ".$query;
		}
        else{
			$this->error = 'no';
			while ($resultado[] = $result->fetch_array());		
		}
		return $resultado;
		
		$this->link->$con->close();
	}

	public function _email($destino, $asunto, $mensaje)
	{
		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
		    //Server settings
		    $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
		    //$mail->SMTPDebug = 2;                      // Enable verbose debug output
		    $mail->isSMTP();                                            // Send using SMTP
		    $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		    $mail->Username   = 'gaheto.contacto@gmail.com';                     // SMTP username
		    $mail->Password   = 'kftrumkpqjdhembg';		// SMTP password
		    $mail->SMTPSecure = 'tls';                                
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;// Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
		    $mail->Port 	  = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

		    //Recipients
		    $mail->setFrom('gaheto.contacto@gmail.com', 'Gaheto');
		    $mail->addAddress($destino);     // Add a recipient
		    $mail->FromName = "Gaheto";
		    /*$mail->addAddress('ellen@example.com');               // Name is optional
		    $mail->addReplyTo('info@example.com', 'Information');
		    $mail->addCC('cc@example.com');
		    $mail->addBCC('bcc@example.com');*/

		    // Attachments
		    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

		    // Content
		    $mail->isHTML(true);            // Set email format to HTML
		    $mail->Subject = $asunto;
		    $mail->Body    = $mensaje;
		    $mail->AltBody = $mensaje;

		    $mail->send();
		    //echo 'Message has been sent';
		} catch (Exception $e) {
		    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}

	public function _edad($actual, $fecha)
	{	
		$imprime = array();
		$start_date = new DateTime($fecha);
  		$since_start = $start_date->diff(new DateTime($actual));
  		//echo $since_start->days.' days total<br>';
  		$anos = $since_start->y;
  		$meses = $since_start->m;
  		$dias = $since_start->d;
  		$horas = $since_start->h;
  		$minutos = $since_start->i;
  		$segundos = $since_start->s;

		$conca1 = "";$conca2 = "";$conca3 = "";$conca4 = "";$conca5 = "";$conca6 = "";
		if($anos > 1 || $anos == 0){
			$conca1="s";
		}
		if($meses > 1 || $meses == 0){
			$conca2="es";
		}
		if($dias > 1 || $dias == 0){
			$conca3="s";
		}
		if($horas > 1 || $horas == 0){
			$conca4="s";
		}
		if($minutos > 1 || $minutos == 0){
			$conca5="s";
		}
		if($segundos > 1 || $segundos == 0){
			$conca6="s";
		}

		$imprime[0] = "";
		$imprime[1]="$anos~$meses~$dias~$horas~$minutos~$segundos";

		if($anos < 0){
			$imprime[0] = "N/A";
			$imprime[1] = "0~0~0~0~0~0";
		}else{
			if($anos > 0){
				$imprime[0] .= "$anos año$conca1 ";
			}
			if($meses > 0){
				$imprime[0] .= "$meses mes$conca2 ";
			}
			if($dias > 0){
				$imprime[0] .= "$dias día$conca3 ";
			}
			if($horas > 0){
				$imprime[0] .= "$horas hora$conca4 ";
			}
			if($minutos > 0){
				$imprime[0] .= "$minutos minuto$conca5 ";
			}
			if($segundos > 0){
				$imprime[0] .= "y $segundos segundo$conca6";
			}
		}

		return $imprime;
	}

	public function bisiesto($anio_actual){
		$bisiesto=false;
		//probamos si el mes de febrero del año actual tiene 29 días
		if (checkdate(2,29,$anio_actual))
		{
		    $bisiesto=true;
		}
		return $bisiesto;
	}

	public function _valorEnLetras($x, $mos) 
	{ 
		if ($x<0) { $signo = "menos ";} 
		else      { $signo = "";} 
		$x = abs ($x); 
		$C1 = $x; 

		$G6 = floor($x/(1000000));  // 7 y mas 

		$E7 = floor($x/(100000)); 
		$G7 = $E7-$G6*10;   // 6 

		$E8 = floor($x/1000); 
		$G8 = $E8-$E7*100;   // 5 y 4 

		$E9 = floor($x/100); 
		$G9 = $E9-$E8*10;  //  3 

		$E10 = floor($x); 
		$G10 = $E10-$E9*100;  // 2 y 1 


		$G11 = round(($x-$E10)*100,0);  // Decimales 
		////////////////////// 

		$H6 = $this->unidades($G6); 

		if($G7==1 AND $G8==0) { $H7 = "Cien "; } 
		else {    $H7 = $this->decenas($G7); } 

		$H8 = $this->unidades($G8); 

		if($G9==1 AND $G10==0) { $H9 = "Cien "; } 
		else {    $H9 = $this->decenas($G9); } 

		$H10 = $this->unidades($G10); 

		if($G11 < 10) { $H11 = "0".$G11; } 
		else { $H11 = $G11; } 

		///////////////////////////// 
		    if($G6==0) { $I6=" "; } 
		elseif($G6==1) { $I6="Millón "; } 
		         else { $I6="Millones "; } 
		          
		if ($G8==0 AND $G7==0) { $I8=" "; } 
		         else { $I8="Mil "; } 
		          
		if($mos == 1){
			$query="SELECT Moneda, Origen FROM generales WHERE ID_General = 1";
			$row = $this->_consultar($query);
			$numerofilas = $this->numerofilas;

			if($row == 'si'){
				echo "Error: ".mysqli_error($this->link);
			}else{
				if($numerofilas > 0){
					if($x > 1){
						$I10 = $row[0]['Moneda'].'S '; 
					}else{
						$I10 = $row[0]['Moneda'].' ';
					}
					
					$I11 = '/100 '.$row[0]['Origen'];
				}
			}	
		}else{
			$I10 = ""; 
			$I11 = "";
		}

		$C3 = $signo.$H6.$I6.$H7.$H8.$I8.$H9.$H10.$I10.$H11.$I11; 

		return $C3; //Retornar el resultado 
	} 

	private function decenas($d) 
	{ 
		if ($d==0)  {$rd = "";} 
		elseif ($d==1)  {$rd = "Ciento ";} 
		elseif ($d==2)  {$rd = "Doscientos ";} 
		elseif ($d==3)  {$rd = "Trescientos ";} 
		elseif ($d==4)  {$rd = "Cuatrocientos ";} 
		elseif ($d==5)  {$rd = "Quinientos ";} 
		elseif ($d==6)  {$rd = "Seiscientos ";} 
		elseif ($d==7)  {$rd = "Setecientos ";} 
		elseif ($d==8)  {$rd = "Ochocientos ";} 
		else            {$rd = "Novecientos ";} 
			
		return $rd; //Retornar el resultado 
	}

	private function unidades($u) 
	{ 
		if ($u==0)  {$ru = " ";} 
		elseif ($u==1)  {$ru = "Un ";} 
		elseif ($u==2)  {$ru = "Dos ";} 
		elseif ($u==3)  {$ru = "Tres ";} 
		elseif ($u==4)  {$ru = "Cuatro ";} 
		elseif ($u==5)  {$ru = "Cinco ";} 
		elseif ($u==6)  {$ru = "Seis ";} 
		elseif ($u==7)  {$ru = "Siete ";} 
		elseif ($u==8)  {$ru = "Ocho ";} 
		elseif ($u==9)  {$ru = "Nueve ";} 
		elseif ($u==10) {$ru = "Diez ";} 

		elseif ($u==11) {$ru = "Once ";} 
		elseif ($u==12) {$ru = "Doce ";} 
		elseif ($u==13) {$ru = "Trece ";} 
		elseif ($u==14) {$ru = "Catorce ";} 
		elseif ($u==15) {$ru = "Quince ";} 
		elseif ($u==16) {$ru = "Dieciseis ";} 
		elseif ($u==17) {$ru = "Decisiete ";} 
		elseif ($u==18) {$ru = "Dieciocho ";} 
		elseif ($u==19) {$ru = "Diecinueve ";} 
		elseif ($u==20) {$ru = "Veinte ";} 

		elseif ($u==21) {$ru = "Veintiun ";} 
		elseif ($u==22) {$ru = "Veintidos ";} 
		elseif ($u==23) {$ru = "Veintitres ";} 
		elseif ($u==24) {$ru = "Veinticuatro ";} 
		elseif ($u==25) {$ru = "Veinticinco ";} 
		elseif ($u==26) {$ru = "Veintiseis ";} 
		elseif ($u==27) {$ru = "Veintisiente ";} 
		elseif ($u==28) {$ru = "Veintiocho ";} 
		elseif ($u==29) {$ru = "Veintinueve ";} 
		elseif ($u==30) {$ru = "Treinta ";} 

		elseif ($u==31) {$ru = "Treinta y un ";} 
		elseif ($u==32) {$ru = "Treinta y dos ";} 
		elseif ($u==33) {$ru = "Treinta y tres ";} 
		elseif ($u==34) {$ru = "Treinta y cuatro ";} 
		elseif ($u==35) {$ru = "Treinta y cinco ";} 
		elseif ($u==36) {$ru = "Treinta y seis ";} 
		elseif ($u==37) {$ru = "Treinta y siete ";} 
		elseif ($u==38) {$ru = "Treinta y ocho ";} 
		elseif ($u==39) {$ru = "Treinta y nueve ";} 
		elseif ($u==40) {$ru = "Cuarenta ";} 

		elseif ($u==41) {$ru = "Cuarenta y un ";} 
		elseif ($u==42) {$ru = "Cuarenta y dos ";} 
		elseif ($u==43) {$ru = "Cuarenta y tres ";} 
		elseif ($u==44) {$ru = "Cuarenta y cuatro ";} 
		elseif ($u==45) {$ru = "Cuarenta y cinco ";} 
		elseif ($u==46) {$ru = "Cuarenta y seis ";} 
		elseif ($u==47) {$ru = "Cuarenta y siete ";} 
		elseif ($u==48) {$ru = "Cuarenta y ocho ";} 
		elseif ($u==49) {$ru = "Cuarenta y nueve ";} 
		elseif ($u==50) {$ru = "Cincuenta ";} 

		elseif ($u==51) {$ru = "Cincuenta y un ";} 
		elseif ($u==52) {$ru = "Cincuenta y dos ";} 
		elseif ($u==53) {$ru = "Cincuenta y tres ";} 
		elseif ($u==54) {$ru = "Cincuenta y cuatro ";} 
		elseif ($u==55) {$ru = "Cincuenta y cinco ";} 
		elseif ($u==56) {$ru = "Cincuenta y seis ";} 
		elseif ($u==57) {$ru = "Cincuenta y siete ";} 
		elseif ($u==58) {$ru = "Cincuenta y ocho ";} 
		elseif ($u==59) {$ru = "Cincuenta y nueve ";} 
		elseif ($u==60) {$ru = "Sesenta ";} 

		elseif ($u==61) {$ru = "Sesenta y un ";} 
		elseif ($u==62) {$ru = "Sesenta y dos ";} 
		elseif ($u==63) {$ru = "Sesenta y tres ";} 
		elseif ($u==64) {$ru = "Sesenta y cuatro ";} 
		elseif ($u==65) {$ru = "Sesenta y cinco ";} 
		elseif ($u==66) {$ru = "Sesenta y seis ";} 
		elseif ($u==67) {$ru = "Sesenta y siete ";} 
		elseif ($u==68) {$ru = "Sesenta y ocho ";} 
		elseif ($u==69) {$ru = "Sesenta y nueve ";} 
		elseif ($u==70) {$ru = "Setenta ";} 

		elseif ($u==71) {$ru = "Setenta y un ";} 
		elseif ($u==72) {$ru = "Setenta y dos ";} 
		elseif ($u==73) {$ru = "Setenta y tres ";} 
		elseif ($u==74) {$ru = "Setenta y cuatro ";} 
		elseif ($u==75) {$ru = "Setenta y cinco ";} 
		elseif ($u==76) {$ru = "Setenta y seis ";} 
		elseif ($u==77) {$ru = "Setenta y siete ";} 
		elseif ($u==78) {$ru = "Setenta y ocho ";} 
		elseif ($u==79) {$ru = "Setenta y nueve ";} 
		elseif ($u==80) {$ru = "Ochenta ";} 

		elseif ($u==81) {$ru = "Ochenta y un ";} 
		elseif ($u==82) {$ru = "Ochenta y dos ";} 
		elseif ($u==83) {$ru = "Ochenta y tres ";} 
		elseif ($u==84) {$ru = "Ochenta y cuatro ";} 
		elseif ($u==85) {$ru = "Ochenta y cinco ";} 
		elseif ($u==86) {$ru = "Ochenta y seis ";} 
		elseif ($u==87) {$ru = "Ochenta y siete ";} 
		elseif ($u==88) {$ru = "Ochenta y ocho ";} 
		elseif ($u==89) {$ru = "Ochenta y nueve ";} 
		elseif ($u==90) {$ru = "Noventa ";} 

		elseif ($u==91) {$ru = "Noventa y un ";} 
		elseif ($u==92) {$ru = "Noventa y dos ";} 
		elseif ($u==93) {$ru = "Noventa y tres ";} 
		elseif ($u==94) {$ru = "Noventa y cuatro ";} 
		elseif ($u==95) {$ru = "Noventa y cinco ";} 
		elseif ($u==96) {$ru = "Noventa y seis ";} 
		elseif ($u==97) {$ru = "Noventa y siete ";} 
		elseif ($u==98) {$ru = "Noventa y ocho ";} 
		else            {$ru = "Noventa y nueve ";} 

		return $ru; //Retornar el resultado 
	} 

	public function movimiento($sql, $id){
		$fecha = date("Y-m-d h:m:s");
		/*$ip = $_SERVER['REMOTE_ADDR']; // Esto contendrá la ip de la solicitud.
		
		$dataArray = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));

		// obteniendo el 'user_agent':
		if ( isset( $_SERVER ) ) {
		    $user_agent = $_SERVER['HTTP_USER_AGENT'];
		} else {
		    global $HTTP_SERVER_VARS;
		    if ( isset( $HTTP_SERVER_VARS ) ) {
		        $user_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
		    } else {
		        global $HTTP_USER_AGENT;
		        $user_agent = $HTTP_USER_AGENT;
		    }
		}
		  
		$os_array =  array(
		    '/windows nt 10/i'      =>  'Windows 10',
		    '/windows nt 6.3/i'     =>  'Windows 8.1',
		    '/windows nt 6.2/i'     =>  'Windows 8',
		    '/windows nt 6.1/i'     =>  'Windows 7',
		    '/windows nt 6.0/i'     =>  'Windows Vista',
		    '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
		    '/windows nt 5.1/i'     =>  'Windows XP',
		    '/windows xp/i'         =>  'Windows XP',
		    '/windows nt 5.0/i'     =>  'Windows 2000',
		    '/windows me/i'         =>  'Windows ME',
		    '/win98/i'              =>  'Windows 98',
		    '/win95/i'              =>  'Windows 95',
		    '/win16/i'              =>  'Windows 3.11',
		    '/macintosh|mac os x/i' =>  'Mac OS X',
		    '/mac_powerpc/i'        =>  'Mac OS 9',
		    '/linux/i'              =>  'Linux',
		    '/browseruntu/i'             =>  'browseruntu',
		    '/iphone/i'             =>  'iPhone',
		    '/ipod/i'               =>  'iPod',
		    '/ipad/i'               =>  'iPad',
		    '/android/i'            =>  'Android',
		    '/blackberry/i'         =>  'BlackBerry',
		    '/webos/i'              =>  'Mobile'
		);
		    
		$os_platform = "Unknown OS Platform";
		foreach ($os_array as $regex => $value) { 
		    if (preg_match($regex, $user_agent)) {
		        $os_platform = $value;
		    }
		}
		
		$browser_array = array(
		    '/msie/i'       =>  'Internet Explorer',
		    '/firefox/i'    =>  'Firefox',
		    '/safari/i'     =>  'Safari',
		    '/chrome/i'     =>  'Chrome',
		    '/edge/i'       =>  'Edge',
		    '/opera/i'      =>  'Opera',
		    '/netscape/i'   =>  'Netscape',
		    '/maxthon/i'    =>  'Maxthon',
		    '/konqueror/i'  =>  'Konqueror',
		    '/mobile/i'     =>  'Handheld Browser'
		);
		$browser = "Unknown Browser";
		foreach ($browser_array as $regex => $value) { 
		    if (preg_match($regex, $user_agent)) {
		        $browser = $value;
		    }
		}

		// finally get the correct version number
		$known = array('Version', $browser, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			 
		if (!preg_match_all($pattern, $user_agent, $matches)) {
			   // we have no matching number just continue
		}
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($user_agent,"Version") < strripos($user_agent,$browser)){
				   $version= $matches['version'][0];
			}else{
				   $version= $matches['version'][1];
			}
		}else {
			$version= $matches['version'][0];
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		$user_browser = $browser." ".$version;	*/
		$sql = $this->link->real_escape_string($sql);

		//$query="INSERT INTO movimientos VALUES(null, '$sql', '$dataArray->geoplugin_request', '$dataArray->geoplugin_countryName', '$dataArray->geoplugin_regionName', '$user_browser', '$os_platform', '$fecha', '$_SERVER[HTTP_USER_AGENT]', '$id')";
		$query="INSERT INTO movimientos SET Descripcion = '$sql', Fecha = '$fecha', FK_Usuario = '$id'";
		$error = $this->_insertar($query);

		if($error == 'si'){
			echo "Error Movimientos: ".mysqli_error($this->link);
		}		
	}

	public function _crear($numero){	
		/*$this->db->query("CALL crear()");
			DELIMITER $$
				CREATE DEFINER=`root`@`localhost` PROCEDURE `crear`()
				BEGIN
					SET @a = (SELECT CONCAT("CREATE DATABASE dentastool",MAX(ID_Usuario)) FROM usuarios);
					PREPARE stmt1 FROM @a;
					EXECUTE stmt1;  
				END$$
			DELIMITER ;
		*/
		
		$this->link->query("CREATE DATABASE dentastool_$numero");
			
		$this->link->query("USE dentastool_$numero");

		$this->link->query("CREATE TABLE `adeudos` (
			  `ID_Adeudo` int(11) NOT NULL,
			  `FK_Paciente` int(11) NOT NULL,
			  `Descuento` double NOT NULL,
			  `Total` double NOT NULL,
			  `Fecha` datetime NOT NULL,
			  `Estatus` tinyint(1) NOT NULL,
			  `Tipo` int(2) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");	

		$this->link->query("CREATE TABLE `adeudos_detalle` (
			  `ID_Adeudo_Detalle` int(11) NOT NULL,
			  `FK_Adeudo` int(11) NOT NULL,
			  `Tratamiento` varchar(100) NOT NULL,
			  `Pza` varchar(10) NOT NULL,
			  `Costo` double NOT NULL,
			  `Descuento` double NOT NULL,
			  `Total` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");	

		$this->link->query("CREATE TRIGGER `CambiarEstatusAdeudos` AFTER INSERT ON `adeudos_detalle` FOR EACH ROW BEGIN
			IF (SELECT SUM(Total) FROM pagos WHERE FK_Adeudo = NEW.FK_Adeudo AND Cancelado = 0) >= (SELECT Total FROM adeudos WHERE ID_Adeudo = NEW.FK_Adeudo) THEN
				UPDATE adeudos SET Estatus = 1 WHERE ID_Adeudo = NEW.FK_Adeudo;
		    ELSE 
		    	UPDATE adeudos SET Estatus = 0 WHERE ID_Adeudo = NEW.FK_Adeudo;
		    END IF;
		END;");		

		$this->link->query("CREATE TABLE `citas` (
			  `ID_Cita` int(11) NOT NULL,
			  `FK_Paciente` int(11) NOT NULL,
			  `Nombre` tinytext NOT NULL,
			  `Fecha_Inicio` datetime NOT NULL,
  			  `Fecha_Termino` datetime NOT NULL,
			  `Descripcion` tinytext NOT NULL,
			  `Estatus` varchar(20) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `galeria` (
			  `ID_Archivo` int(11) NOT NULL,
			  `FK_Paciente` int(11) NOT NULL,
			  `Titulo` varchar(30) NOT NULL,
			  `Descripcion` varchar(500) NOT NULL,
			  `Nombre` varchar(100) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `generales` (
			  `ID_General` int(11) NOT NULL,
			  `Dentista` varchar(300) NOT NULL,
			  `Nombre` varchar(60) NOT NULL,
			  `Cedula` varchar(30) NOT NULL,
			  `Domicilio` varchar(300) NOT NULL,
			  `Colonia` varchar(60) NOT NULL,
			  `Ciudad` varchar(60) NOT NULL,
			  `Estado` varchar(60) NOT NULL,
			  `CP` varchar(10) NOT NULL,
			  `Telefono` varchar(30) NOT NULL,
			  `Email` varchar(300) NOT NULL,
			  `Imagen` varchar(100) NOT NULL,
			  `Signo` varchar(10) NOT NULL,
			  `Moneda` varchar(60) NOT NULL,
			  `Origen` tinytext NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("INSERT INTO `generales` (`ID_General`, `Dentista`, `Nombre`, `Cedula`, `Domicilio`, `Colonia`, `Ciudad`, `Estado`, `CP`, `Telefono`, `Email`, `Imagen`, `Signo`, `Moneda`, `Origen`) VALUES (1, '', '', '', '', '', '', '', '', '', '', '', '".'$'."', 'PESO', 'MONEDA NACIONAL DE MEXICO');");

		$this->link->query("CREATE TABLE `notas` (
			  `ID_Nota` int(11) NOT NULL,
			  `FK_Odontograma` int(11) NOT NULL,
			  `Contenido` tinytext NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `odontogramas` (
			  `ID_Odontograma` int(11) NOT NULL,
			  `FK_Paciente` int(11) NOT NULL,
			  `Nombre` varchar(30) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TRIGGER `crearOdontograma` AFTER INSERT ON `odontogramas` FOR EACH ROW BEGIN
				INSERT INTO `odontograma_mapa` VALUES(null,New.ID_Odontograma,'0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0','0*0*0*0*0');
				INSERT INTO `odontograma_pieza` VALUES
				(null, New.ID_Odontograma, '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0', '0*0*0*0*0*0*0');
			END;");

		$this->link->query("CREATE TABLE `odontograma_mapa` (
			  `ID_Mapa` int(11) NOT NULL,
			  `FK_Odontograma` int(11) NOT NULL,
			  `ma11` varchar(30) NOT NULL,
			  `ma12` varchar(30) NOT NULL,
			  `ma13` varchar(30) NOT NULL,
			  `ma14` varchar(30) NOT NULL,
			  `ma15` varchar(30) NOT NULL,
			  `ma16` varchar(30) NOT NULL,
			  `ma17` varchar(30) NOT NULL,
			  `ma18` varchar(30) NOT NULL,
			  `ma21` varchar(30) NOT NULL,
			  `ma22` varchar(30) NOT NULL,
			  `ma23` varchar(30) NOT NULL,
			  `ma24` varchar(30) NOT NULL,
			  `ma25` varchar(30) NOT NULL,
			  `ma26` varchar(30) NOT NULL,
			  `ma27` varchar(30) NOT NULL,
			  `ma28` varchar(30) NOT NULL,
			  `ma31` varchar(30) NOT NULL,
			  `ma32` varchar(30) NOT NULL,
			  `ma33` varchar(30) NOT NULL,
			  `ma34` varchar(30) NOT NULL,
			  `ma35` varchar(30) NOT NULL,
			  `ma36` varchar(30) NOT NULL,
			  `ma37` varchar(30) NOT NULL,
			  `ma38` varchar(30) NOT NULL,
			  `ma41` varchar(30) NOT NULL,
			  `ma42` varchar(30) NOT NULL,
			  `ma43` varchar(30) NOT NULL,
			  `ma44` varchar(30) NOT NULL,
			  `ma45` varchar(30) NOT NULL,
			  `ma46` varchar(30) NOT NULL,
			  `ma47` varchar(30) NOT NULL,
			  `ma48` varchar(30) NOT NULL,
			  `ma51` varchar(30) NOT NULL,
			  `ma52` varchar(30) NOT NULL,
			  `ma53` varchar(30) NOT NULL,
			  `ma54` varchar(30) NOT NULL,
			  `ma55` varchar(30) NOT NULL,
			  `ma61` varchar(30) NOT NULL,
			  `ma62` varchar(30) NOT NULL,
			  `ma63` varchar(30) NOT NULL,
			  `ma64` varchar(30) NOT NULL,
			  `ma65` varchar(30) NOT NULL,
			  `ma71` varchar(30) NOT NULL,
			  `ma72` varchar(30) NOT NULL,
			  `ma73` varchar(30) NOT NULL,
			  `ma74` varchar(30) NOT NULL,
			  `ma75` varchar(30) NOT NULL,
			  `ma81` varchar(30) NOT NULL,
			  `ma82` varchar(30) NOT NULL,
			  `ma83` varchar(30) NOT NULL,
			  `ma84` varchar(30) NOT NULL,
			  `ma85` varchar(30) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `odontograma_pieza` (
			  `ID_Pieza` int(11) NOT NULL,
			  `FK_Odontograma` int(11) NOT NULL,
			  `p11` varchar(30) NOT NULL,
			  `p12` varchar(30) NOT NULL,
			  `p13` varchar(30) NOT NULL,
			  `p14` varchar(30) NOT NULL,
			  `p15` varchar(30) NOT NULL,
			  `p16` varchar(30) NOT NULL,
			  `p17` varchar(30) NOT NULL,
			  `p18` varchar(30) NOT NULL,
			  `p21` varchar(30) NOT NULL,
			  `p22` varchar(30) NOT NULL,
			  `p23` varchar(30) NOT NULL,
			  `p24` varchar(30) NOT NULL,
			  `p25` varchar(30) NOT NULL,
			  `p26` varchar(30) NOT NULL,
			  `p27` varchar(30) NOT NULL,
			  `p28` varchar(30) NOT NULL,
			  `p31` varchar(30) NOT NULL,
			  `p32` varchar(30) NOT NULL,
			  `p33` varchar(30) NOT NULL,
			  `p34` varchar(30) NOT NULL,
			  `p35` varchar(30) NOT NULL,
			  `p36` varchar(30) NOT NULL,
			  `p37` varchar(30) NOT NULL,
			  `p38` varchar(30) NOT NULL,
			  `p41` varchar(30) NOT NULL,
			  `p42` varchar(30) NOT NULL,
			  `p43` varchar(30) NOT NULL,
			  `p44` varchar(30) NOT NULL,
			  `p45` varchar(30) NOT NULL,
			  `p46` varchar(30) NOT NULL,
			  `p47` varchar(30) NOT NULL,
			  `p48` varchar(30) NOT NULL,
			  `p51` varchar(30) NOT NULL,
			  `p52` varchar(30) NOT NULL,
			  `p53` varchar(30) NOT NULL,
			  `p54` varchar(30) NOT NULL,
			  `p55` varchar(30) NOT NULL,
			  `p61` varchar(30) NOT NULL,
			  `p62` varchar(30) NOT NULL,
			  `p63` varchar(30) NOT NULL,
			  `p64` varchar(30) NOT NULL,
			  `p65` varchar(30) NOT NULL,
			  `p71` varchar(30) NOT NULL,
			  `p72` varchar(30) NOT NULL,
			  `p73` varchar(30) NOT NULL,
			  `p74` varchar(30) NOT NULL,
			  `p75` varchar(30) NOT NULL,
			  `p81` varchar(30) NOT NULL,
			  `p82` varchar(30) NOT NULL,
			  `p83` varchar(30) NOT NULL,
			  `p84` varchar(30) NOT NULL,
			  `p85` varchar(30) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `ortodoncia` (
			  `ID_Ortodoncia` int(11) NOT NULL,
			  `FK_Adeudo` int(11) NOT NULL,
			  `PagoInicial` double NOT NULL,
			  `MesInicial` varchar(20) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `ortodoncia_detalle` (
			  `ID_Ortodoncia_Detalle` int(11) NOT NULL,
			  `FK_Ortodoncia` int(11) NOT NULL,
			  `Cantidad` double NOT NULL,
			  `Meses` int(11) NOT NULL,
			  `Total` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `pacientes` (
			  `ID_Paciente` int(11) NOT NULL,
			  `Nombres` varchar(60) NOT NULL,
			  `Primer_Apellido` varchar(30) NOT NULL,
			  `Segundo_Apellido` varchar(30) NOT NULL,
			  `Fecha_Nacimiento` date NOT NULL,
			  `Sexo` varchar(20) NOT NULL,
			  `Alergias` tinytext NOT NULL,
  			  `Enfermedades` tinytext NOT NULL,
			  `Telefono` varchar(20) NOT NULL,
			  `Email` varchar(300) NOT NULL,
			  `Domicilio` tinytext NOT NULL,
			  `Colonia` varchar(60) NOT NULL,
			  `Ciudad` varchar(60) NOT NULL,
			  `Estado` varchar(60) NOT NULL,
			  `Pais` varchar(60) NOT NULL,
			  `CP` varchar(10) NOT NULL,
			  `Ocupacion` varchar(60) NOT NULL,
			  `Estado_Civil` varchar(20) NOT NULL,
			  `Segundo_Tel` varchar(20) NOT NULL,
			  `Foto` varchar(200) NOT NULL,
			  `Fecha_Registro` date NOT NULL,
			  `Activo` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TRIGGER `odontograma` AFTER INSERT ON `pacientes` FOR EACH ROW BEGIN
				INSERT INTO odontogramas VALUES(null, New.ID_Paciente, 'Odontograma1');
			END");

		$this->link->query("CREATE TABLE `padres` (
			  `ID_Padres` int(11) NOT NULL,
			  `NombrePadre` varchar(130) NOT NULL,
			  `NombreMadre` varchar(130) NOT NULL,
			  `Tel_Padre` varchar(20) NOT NULL,
			  `Tel_Madre` varchar(20) NOT NULL,
			  `Email_Padre` varchar(300) NOT NULL,
			  `Email_Madre` varchar(300) NOT NULL,
			  `FK_Paciente` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `pagos` (
			  `ID_Pago` int(11) NOT NULL,
			  `FK_Adeudo` int(11) NOT NULL,
			  `Persona` tinytext NOT NULL,
			  `Tipo_Pago` varchar(30) NOT NULL,
			  `Fecha` datetime NOT NULL,
			  `Total` double NOT NULL,
			  `Ingresado` tinytext NOT NULL,
			  `Cancelado` tinyint(1) NOT NULL,
			  `Concepto` tinytext NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TRIGGER `CambiarEstatusDELETE` AFTER DELETE ON `pagos` FOR EACH ROW BEGIN
			IF (SELECT SUM(Total) FROM pagos WHERE FK_Adeudo = OLD.FK_Adeudo AND Cancelado = 0) >= (SELECT Total FROM adeudos WHERE ID_Adeudo = OLD.FK_Adeudo) THEN
				UPDATE adeudos SET Estatus = 1 WHERE ID_Adeudo = OLD.FK_Adeudo;
		    ELSE 
		    	UPDATE adeudos SET Estatus = 0 WHERE ID_Adeudo = OLD.FK_Adeudo;
		    END IF;
		END;");

		$this->link->query("CREATE TRIGGER `CambiarEstatusINSERT` AFTER INSERT ON `pagos` FOR EACH ROW BEGIN
			IF (SELECT SUM(Total) FROM pagos WHERE FK_Adeudo = NEW.FK_Adeudo AND Cancelado = 0) >= (SELECT Total FROM adeudos WHERE ID_Adeudo = NEW.FK_Adeudo) THEN
				UPDATE adeudos SET Estatus = 1 WHERE ID_Adeudo = NEW.FK_Adeudo;
		    ELSE 
		    	UPDATE adeudos SET Estatus = 0 WHERE ID_Adeudo = NEW.FK_Adeudo;
		    END IF;
		END;");

		$this->link->query("CREATE TRIGGER `CambiarEstatusUPDATE` AFTER UPDATE ON `pagos` FOR EACH ROW BEGIN
			IF (SELECT SUM(Total) FROM pagos WHERE FK_Adeudo = NEW.FK_Adeudo AND Cancelado = 0) >= (SELECT Total FROM adeudos WHERE ID_Adeudo = NEW.FK_Adeudo) THEN
				UPDATE adeudos SET Estatus = 1 WHERE ID_Adeudo = NEW.FK_Adeudo;
		    ELSE
		    	UPDATE adeudos SET Estatus = 0 WHERE ID_Adeudo = NEW.FK_Adeudo;
		    END IF;
		END;");

		$this->link->query("CREATE TABLE `recetas` (
			  `ID_Receta` int(11) NOT NULL,
			  `FK_Paciente` int(11) NOT NULL,
			  `Contenido` text NOT NULL,
			  `Fecha` date NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("CREATE TABLE `tratamientos` (
			  `ID_Tratamiento` int(11) NOT NULL,
			  `Nombre` varchar(100) NOT NULL,
			  `Precio` double NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$this->link->query("ALTER TABLE `adeudos`
			  ADD PRIMARY KEY (`ID_Adeudo`),
			  ADD KEY `FK_Paciente` (`FK_Paciente`);");

		$this->link->query("ALTER TABLE `adeudos_detalle`
			  ADD PRIMARY KEY (`ID_Adeudo_Detalle`),
			  ADD KEY `FK_Adeudo` (`FK_Adeudo`);");

		$this->link->query("ALTER TABLE `citas`
			  ADD PRIMARY KEY (`ID_Cita`);");

		$this->link->query("ALTER TABLE `galeria`
			  ADD PRIMARY KEY (`ID_Archivo`),
			  ADD KEY `FK_Paciente` (`FK_Paciente`);");

		$this->link->query("ALTER TABLE `generales`
  			  ADD PRIMARY KEY (`ID_General`);");

		$this->link->query("ALTER TABLE `notas`
			  ADD PRIMARY KEY (`ID_Nota`),
			  ADD KEY `FK_Paciente` (`FK_Paciente`);");

		$this->link->query("ALTER TABLE `odontogramas`
			  ADD PRIMARY KEY (`ID_Odontograma`),
			  ADD KEY `FK_Paciente` (`FK_Paciente`);");

		$this->link->query("ALTER TABLE `odontograma_mapa`
			  ADD PRIMARY KEY (`ID_Mapa`),
			  ADD KEY `odontograma_mapa_ibfk_1` (`FK_Odontograma`);");

		$this->link->query("ALTER TABLE `odontograma_pieza`
			  ADD PRIMARY KEY (`ID_Pieza`),
			  ADD KEY `odontograma_pieza_ibfk_1` (`FK_Odontograma`);");

		$this->link->query("ALTER TABLE `ortodoncia`
			  ADD PRIMARY KEY (`ID_Ortodoncia`),
			  ADD KEY `FK_Adeudo` (`FK_Adeudo`);");

		$this->link->query("ALTER TABLE `ortodoncia_detalle`
			  ADD PRIMARY KEY (`ID_Ortodoncia_Detalle`),
			  ADD KEY `FK_Ortodoncia` (`FK_Ortodoncia`);");

		$this->link->query("ALTER TABLE `pacientes`
  			  ADD PRIMARY KEY (`ID_Paciente`);");

		$this->link->query("ALTER TABLE `padres`
			  ADD PRIMARY KEY (`ID_Padres`),
			  ADD KEY `FK_Paciente` (`FK_Paciente`);");

		$this->link->query("ALTER TABLE `pagos`
			  ADD PRIMARY KEY (`ID_Pago`),
			  ADD KEY `FK_Adeudo` (`FK_Adeudo`);");

		$this->link->query("ALTER TABLE `recetas`
			  ADD PRIMARY KEY (`ID_Receta`),
			  ADD KEY `FK_Paciente` (`FK_Paciente`);");

		$this->link->query("ALTER TABLE `tratamientos`
 	 		  ADD PRIMARY KEY (`ID_Tratamiento`);");

		$this->link->query("ALTER TABLE `adeudos`
  			  MODIFY `ID_Adeudo` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `adeudos_detalle`
  			  MODIFY `ID_Adeudo_Detalle` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `citas`
  			  MODIFY `ID_Cita` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `galeria`
			  MODIFY `ID_Archivo` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `generales`
			  MODIFY `ID_General` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");

		$this->link->query("ALTER TABLE `notas`
			  MODIFY `ID_Nota` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `odontogramas`
			  MODIFY `ID_Odontograma` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `odontograma_mapa`
			  MODIFY `ID_Mapa` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `odontograma_pieza`
			  MODIFY `ID_Pieza` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `ortodoncia`
			  MODIFY `ID_Ortodoncia` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `ortodoncia_detalle`
			  MODIFY `ID_Ortodoncia_Detalle` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `pacientes`
 			 MODIFY `ID_Paciente` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `padres`
			  MODIFY `ID_Padres` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `pagos`
			  MODIFY `ID_Pago` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `recetas`
			  MODIFY `ID_Receta` int(11) NOT NULL AUTO_INCREMENT;");

		$this->link->query("ALTER TABLE `tratamientos`
			  MODIFY `ID_Tratamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;");

		$this->link->query("ALTER TABLE `adeudos`
			  ADD CONSTRAINT `adeudos_ibfk_1` FOREIGN KEY (`FK_Paciente`) REFERENCES `pacientes` (`ID_Paciente`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `adeudos_detalle`
			  ADD CONSTRAINT `adeudos_detalle_ibfk_1` FOREIGN KEY (`FK_Adeudo`) REFERENCES `adeudos` (`ID_Adeudo`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `galeria`
			  ADD CONSTRAINT `galeria_ibfk_1` FOREIGN KEY (`FK_Paciente`) REFERENCES `pacientes` (`ID_Paciente`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `notas`
			  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`FK_Odontograma`) REFERENCES `odontogramas` (`ID_Odontograma`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `odontogramas`
			  ADD CONSTRAINT `odontogramas_ibfk_1` FOREIGN KEY (`FK_Paciente`) REFERENCES `pacientes` (`ID_Paciente`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `odontograma_mapa`
			  ADD CONSTRAINT `odontograma_mapa_ibfk_1` FOREIGN KEY (`FK_Odontograma`) REFERENCES `odontogramas` (`ID_Odontograma`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `odontograma_pieza`
			  ADD CONSTRAINT `odontograma_pieza_ibfk_1` FOREIGN KEY (`FK_Odontograma`) REFERENCES `odontogramas` (`ID_Odontograma`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `ortodoncia`
			  ADD CONSTRAINT `ortodoncia_ibfk_1` FOREIGN KEY (`FK_Adeudo`) REFERENCES `adeudos` (`ID_Adeudo`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `ortodoncia_detalle`
			  ADD CONSTRAINT `ortodoncia_detalle_ibfk_1` FOREIGN KEY (`FK_Ortodoncia`) REFERENCES `ortodoncia` (`ID_Ortodoncia`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `padres`
			  ADD CONSTRAINT `padres_ibfk_1` FOREIGN KEY (`FK_Paciente`) REFERENCES `pacientes` (`ID_Paciente`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `pagos`
			  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`FK_Adeudo`) REFERENCES `adeudos` (`ID_Adeudo`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `permisos`
			  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`FK_Usuario`) REFERENCES `usuarios` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;");

		$this->link->query("ALTER TABLE `recetas`
			  ADD CONSTRAINT `recetas_ibfk_1` FOREIGN KEY (`FK_Paciente`) REFERENCES `pacientes` (`ID_Paciente`) ON DELETE CASCADE ON UPDATE CASCADE;");
	}
}
?>
