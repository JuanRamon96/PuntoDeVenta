<?php
class clasificaciones {

	public function _consultar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		
		$buscar = $omodelo->link->real_escape_string($buscar);
	    $limit = $omodelo->link->real_escape_string($limit);
	    $pagina = $omodelo->link->real_escape_string($pagina);
	    $ordenColumna = $omodelo->link->real_escape_string($ordenColumna);
	    $orden = $omodelo->link->real_escape_string($orden);
	    $arreglo = array();

	    $busqueda = '';
	    if (trim($buscar) != '') {
	      	$separa = explode(' ', trim($buscar));
	      	$busqueda = 'WHERE ';
	      	for ($i = 0; $i < count($separa); $i++) {
	        	$busqueda .= "CONCAT(Nombre, Descripcion) REGEXP '" . $separa[$i] . "'";
	        	if ($i < (count($separa) - 1)) {
	          		$busqueda .= ' AND ';
	        	}
	      	}
	    }

	    $query = "SELECT ID_Clasificacion, Nombre, Descripcion, Foto, IFNULL((SELECT COUNT(*) FROM productos WHERE FK_Clasificacion = ID_Clasificacion), 0) AS NumCat, (SELECT COUNT(*) FROM clasificaciones $busqueda) AS Num FROM clasificaciones $busqueda ORDER BY $ordenColumna $orden LIMIT $limit OFFSET " . (($pagina * $limit) - $limit);
	    $row = $omodelo->_consultar($query);
	    $numerofilas = $omodelo->numerofilas;

	    if ($row == 'si') {
	      	echo "Error: " . mysqli_error($omodelo->link);
	    } else {
	      	if ($numerofilas > 0) {
	        	for ($i = 0; $i < $numerofilas; $i++) {
	        		$foto = '<a href="vistas/assets/images/fondo.jpg" data-fancybox="images"><div style="background-image: url(' . "'" . 'vistas/assets/images/fondo.jpg' . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
		            	</div></a>';

		          	if ($row[$i]['Foto'] != '' && file_exists('vistas/assets/images/clasificaciones/' . $row[$i]['Foto'])) {
		            $foto = '<a href="vistas/assets/images/clasificaciones/'.$row[$i]['Foto'].'" data-fancybox="images"><div style="background-image: url(' . "'" . 'vistas/assets/images/clasificaciones/' . $row[$i]['Foto'] . "'" . '); width: 50px; height: 50px; background-size: cover; background-position: center; margin: 0 auto; cursor: pointer; border-radius: 100%;">
		              </div></a>';
		        	}

	          		$bModificar = '';
	          		$bEliminar = '';
	          		if ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Clasificaciones'][3] == '1') {
	            		$bModificar = '<button type="button" class="btn btn-sm btn-warning bModificarClasificacion" title="Modificar" attrID="'.$row[$i]['ID_Clasificacion'].'" foto="'.$row[$i]['Foto'].'"><i class="fas fa-pencil"></i></button>';
	          		}
	          		if ($row[$i]['NumCat'] == '0' && ($_SESSION['user_punto_venta']['Tipo_Usuario'] == '1' || $_SESSION['user_punto_venta']['Permisos']['Clasificaciones'][4] == '1')) {
	            		$bEliminar = '<button type="button" class="btn btn-sm btn-danger bEliminarClasificacion" title="Eliminar" attrID="'.$row[$i]['ID_Clasificacion'].'" foto="'.$row[$i]['Foto'].'"><i class="fas fa-trash"></i></button>';
	          		}

	          		$arreglo['data'][$i] = array(
	            		'ID' => $row[$i]['ID_Clasificacion'],
	            		'Nombre' => $foto.'<span>'.$row[$i]['Nombre'].'</span>',
	            		'Descripcion' => $row[$i]['Descripcion'],
	            		'Acciones' => $bModificar.' '.$bEliminar
	          		);
	        	}
	        
	        	$arreglo['totales'] = array('NumRows' => $row[0]['Num']);
	      	}
	    }

	    echo json_encode($arreglo);
	}

	public function _insertar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date("Y-m-d H:i:s");
		
		$nombreClasificacion = $omodelo->link->real_escape_string(trim($nombreClasificacion));
      	$descripcionClasificacion = $omodelo->link->real_escape_string(trim($descripcionClasificacion));
  
      	$omodelo->_insertar("START TRANSACTION;");
      	$query = "INSERT INTO clasificaciones SET Nombre = '$nombreClasificacion', Descripcion = '$descripcionClasificacion'";
      	$error = $omodelo->_insertar($query);
      	$status = 0;

      	if ($error == 'si') {
        	echo "Error: " . mysqli_error($omodelo->link);
        	$status = 1;
      	}else {
        	$id = $omodelo->link->insert_id;

        	$nombreImg = '';
        	$ruta = '';
        	$rutaProvisional = '';
        	$carpeta = 'vistas/assets/images/clasificaciones/';
        	if ($_FILES['imgClasificacion']['size'] > 0 && $_FILES['imgClasificacion']['error'] == 0) {
          		$file = $_FILES['imgClasificacion'];
          		$nombreImg = $file['name'];
          		$tipoImg = $file['type'];
          		$rutaProvisional = $file['tmp_name'];
          		$sizeImg = $file['size'];
				$bd = $_SESSION['user_punto_bd'];

          		if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != '') {
            		echo 'Error 2 formato';
            		$status = 1;
          		} else if($sizeImg > (1024*1024*10)){
            		echo 'Error 3 peso';
            		$status = 1;
          		}else {
            		$ruta = $carpeta . $bd . '_' . $id . '_' . $nombreImg;
          		}

          		if ($status == 0 && $nombreImg != '') {
            		$query = "UPDATE clasificaciones SET Foto = '" . $bd . '_' . $id . '_' . $nombreImg . "'  WHERE ID_Clasificacion = '$id'";
            		$error = $omodelo->_insertar($query);
              
            		if ($error == 'si') {
              			echo "Error 4: " . mysqli_error($omodelo->link);
              			$status = 1;
            		} else {
              			move_uploaded_file($rutaProvisional, $ruta);
            		}
          		}
       		}

        	if ($status == 0) {
          		echo 'Correcto';

          		$omodelo->_insertar("COMMIT;");
          		$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
        	}else{
          		$omodelo->_insertar("ROLLBACK;");
        	}
      	}
	}

	public function _modificar()
	{
		$omodelo = new m_modelo();
		extract($_POST);
		$fecha = date("Y-m-d H:i:s");
		
		$id = $omodelo->link->real_escape_string(trim($id));
		$nombreClasificacion = $omodelo->link->real_escape_string(trim($nombreClasificacion));
      	$descripcionClasificacion = $omodelo->link->real_escape_string(trim($descripcionClasificacion));
      	$foto = isset($foto) ? $omodelo->link->real_escape_string($foto) : '';
      	

      	$omodelo->_insertar("START TRANSACTION;");
      	$query = "UPDATE clasificaciones SET Nombre = '$nombreClasificacion', Descripcion = '$descripcionClasificacion' WHERE ID_Clasificacion = '$id'";
      	$error = $omodelo->_insertar($query);
      	$status = 0;

      	if ($error == 'si') {
        	echo "Error: " . mysqli_error($omodelo->link);
        	$status = 1;
      	}else {
        	$nombreImg = '';
        	$ruta = '';
        	$rutaProvisional = '';
        	$carpeta = 'vistas/assets/images/clasificaciones/';
        	if ($_FILES['imgClasificacion']['size'] > 0 && $_FILES['imgClasificacion']['error'] == 0) {
          		$file = $_FILES['imgClasificacion'];
          		$nombreImg = $file['name'];
          		$tipoImg = $file['type'];
          		$rutaProvisional = $file['tmp_name'];
          		$sizeImg = $file['size'];
				$bd = $_SESSION['user_punto_bd'];

          		if ($tipoImg != 'image/jpeg' && $tipoImg != 'image/jpg' && $tipoImg != 'image/png' && $tipoImg != '') {
            		echo 'Error 2 formato';
            		$status = 1;
          		} else if($sizeImg > (1024 * 1024)){
            		echo 'Error 3 peso';
            		$status = 1;
          		}else {
            		$ruta = $carpeta . $bd . '_' . $id . '_' . $nombreImg;
          		}

          		if ($status == 0 && $nombreImg != '') {
            		$query = "UPDATE clasificaciones SET Foto = '" . $bd . '_' . $id . '_' . $nombreImg . "'  WHERE ID_Clasificacion = '$id'";
            		$error = $omodelo->_insertar($query);
              
            		if ($error == 'si') {
              			echo "Error 4: " . mysqli_error($omodelo->link);
              			$status = 1;
            		} else {
            			if (trim($foto) != '' && file_exists($carpeta.$foto)) {
				        	unlink($carpeta.$foto);
				      	}

              			move_uploaded_file($rutaProvisional, $ruta);
            		}
          		}
       		}

        	if ($status == 0) {
          		echo 'Correcto';

          		$omodelo->_insertar("COMMIT;");
          		$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
        	}else{
          		$omodelo->_insertar("ROLLBACK;");
        	}
      	}
	}

	public function _eliminar() {
	    $omodelo = new m_modelo();
	    extract($_POST);
	    $fecha = date('Y-m-d H:i:s');

	    $id = $omodelo->link->real_escape_string($id);
	    $foto = isset($foto) ? $omodelo->link->real_escape_string($foto) : '';

	    $query = "DELETE FROM clasificaciones WHERE ID_Clasificacion = '$id'";
	    $error = $omodelo->_insertar($query);

	    if ($error == 'si') {
	      	echo "Error: " . mysqli_error($omodelo->link);
	    } else {
	      	if (trim($foto) != '' && file_exists('vistas/assets/images/clasificaciones/' . $foto)) {
	        	unlink('vistas/assets/images/clasificaciones/' . $foto);
	      	}

	      	echo 'Correcto';

	      	$omodelo->movimiento($query, $_SESSION['user_punto_venta']['ID_Usuario']);
	    }
  	}
}
?>