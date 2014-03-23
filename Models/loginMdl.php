<?php

class loginMdl {

	function __construct($driver) {
		$this -> db_driver = $driver;
	}


	function get_user($userid,$password,$table,$type) {
		#Checks userid is correct
		$usuario = false;
		$prepare = "SELECT name FROM ".$table." WHERE userid = ? AND password = ?";
		if ($stmt = $this -> db_driver->prepare($prepare)) {
				$stmt -> bind_param('is',$userid,$password);
	    		$stmt->execute();
	    	$stmt->bind_result($dbuser);
	    	if ($stmt->fetch()) {
	    		$usuario = array ('usuario' => $dbuser, 'type' => $type);
	    	}
	    	$stmt->close();
		}
		return $usuario;
	}
}
?>