<?php
class conexion {
    private $_connection;
    private static $_instance; //The single instance
    private $_host = "localhost";
    private $_username = "root";
    private $_password = "";
    private $_database = "dentastool";
    /*private $_username = "";
    private $_password = "";
    private $_database = "";*/

    public function __construct()
    {   
        $NoBD = "";
        if(isset($_SESSION['user_dentastool'])){
            if($_SESSION['user_dentastool']['Tipo_Usuario'] == 1){
                $NoBD = '_'.$_SESSION['user_dentastool']['ID_Usuario'];
            }else if($_SESSION['user_dentastool']['BD'] != 0){
                $NoBD = '_'.$_SESSION['user_dentastool']['BD'];
            }
        }

        $this->_connection = new mysqli($this->_host, $this->_username,$this->_password, $this->_database.$NoBD);
        
        if(mysqli_connect_error())
        {
            trigger_error("Error al conectar con la Base de datos:" . mysql_connect_error(),E_USER_ERROR);
        }else{
             return $this->_connection;
        }
    }
}
?>