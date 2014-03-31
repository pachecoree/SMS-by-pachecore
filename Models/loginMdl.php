<?php

class loginMdl {

	function __construct($driver) {
		$this -> db_driver = $driver;
	}


	function get_user($userid,$password,$table,$type) {
		#Checks userid is correct
		$usuario = false;
		$prepare = "SELECT userid,nombre, primer_a, segundo_a FROM ".$table." WHERE userid = ? AND BINARY password = ?";
		if ($query = $this -> db_driver->prepare($prepare)) {
			$query -> bind_param('ss',$userid,$password);
	    	$query->execute();
	    	$query->bind_result($userid,$nombre,$primer_a, $segundo_a);
	    	if ($query->fetch()) {
	    		$dbuser = $nombre.' '.$primer_a.' '.$segundo_a;
	    		$usuario = array ('userid' => $userid, 'usuario' => $dbuser, 'type' => $type);
	    	}
		}
		return $usuario;
	}
}
?>