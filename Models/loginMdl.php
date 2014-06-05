<?php

class loginMdl {

	function __construct($driver) {
		$this -> db_driver = $driver;
	}

	function get_generica($userid,$session) {
		if ($session == 1) $tabla = 'users_student';
		else if ($session == 2) $tabla = 'users_teacher';
		else if ($session == 3) $tabla = 'users_admin';
		$prepare = "SELECT clave_generica FROM $tabla WHERE userid = ?";
		if ($query = $this -> db_driver->prepare($prepare)) {
			$query -> bind_param('s',$userid);
	    	$query->execute();
	    	$query->bind_result($clave_generica);
	    	if ($query->fetch()) {
	    		if ($clave_generica == 1) {
	    			return true;
	    		}
	    		else return false;
	    	}
		}
		return false;
	}

	function updatePassword($pass,$userid,$session) {
		if ($session == 1) $tabla = 'users_student';
		else if ($session == 2) $tabla = 'users_teacher';
		else if ($session == 3) $tabla = 'users_admin';
 		$prepare = "UPDATE $tabla SET password = ? WHERE userid = ?";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("ss",$pass,$userid);
			$query -> execute();
		}
	 	$prepare = "UPDATE $tabla SET clave_generica = 0 WHERE userid = ?";
		if ($session != 3) {
			if ($query = $this -> db_driver -> prepare($prepare)) {
				$query -> bind_param("s",$userid);
				$query -> execute();
			}
		}
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