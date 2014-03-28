<?php

class loginMdl {

	function __construct($driver) {
		$this -> db_driver = $driver;
	}


	function get_user($userid,$password,$table,$type) {
		#Checks userid is correct
		$usuario = false;
		$prepare = "SELECT nombre, primer_a, segundo_a FROM ".$table." WHERE userid = ? AND password = ?";
		if ($query = $this -> db_driver->prepare($prepare)) {
			$query -> bind_param('is',$userid,$password);
	    	$query->execute();
	    	$query->bind_result($nombre,$primer_a, $segundo_a);
	    	if ($query->fetch()) {
	    		$dbuser = $nombre.' '.$primer_a.' '.$segundo_a;
	    		$usuario = array ('usuario' => $dbuser, 'type' => $type);
	    	}
		}
		return $usuario;
	}
}
?>