<?php
class conexion {
    private $_connection;
    private $_host = "localhost";
    private $_username = "root";
    private $_password = "";
    private $_database = "punto_subs";

    public function __construct()
    {   
        $NoBD = $this->_database;
        if(isset($_SESSION['user_punto_bd'])){
            $NoBD = 'punto_venta_'.$_SESSION['user_punto_bd'];
        }

        $this->_connection = new mysqli($this->_host, $this->_username, $this->_password, $NoBD);
        
        if(mysqli_connect_error())
        {
            trigger_error("Error al conectar con la Base de datos:" . mysqli_connect_error(), E_USER_ERROR);
        }else{
            return $this->_connection;
        }
    }
}
?>