<?php
class conexion {
    private $_connection;
    private static $_instance; //The single instance
    private $_host = "localhost";
    private $_username = "root";
    private $_password = "";
    private $_database = "stazione";
    /*private $_username = "";
    private $_password = "";
    private $_database = "";*/

    public function __construct()
    {   
        $this->_connection = new mysqli($this->_host, $this->_username,$this->_password, $this->_database);
        
        if(mysqli_connect_error())
        {
            trigger_error("Error al conectar con la Base de datos:" . mysql_connect_error(),E_USER_ERROR);
        }else{
            return $this->_connection;
        }
    }
}
?>