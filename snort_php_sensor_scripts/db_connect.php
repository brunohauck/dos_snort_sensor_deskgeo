<?php
//--------------------- db connect function -------------------------
class DB_CONNECT {

	function __construct(){
		$this->connect();
	}

	function __destruct(){
		$this->close();
	}

	function connect(){
		
		$con = mysql_connect("localhost", "root", "deskgeo1234567890") or die (mysql_error());
		$db = mysql_select_db("snort") or die (mysql_error()) or die (mysql_error());
		return $con;
	}
	
	function close(){
		mysql_close();
	}

}

?>
