<?php 

class cicleMdl {
	#Validate that the input cicle is correct
	
	function __construct() {

	}

	function add_cicle($cicle) {
		#Recieves the cicle information to be added in an array
		#Return status true if it was correctly created or false if it failed
		return true;
	}

	function modify_status($cicle,$status) {
		#Recieves the cicle information
		#Go to the DB and look for the cicle and modify its status, and modify actual Active cicle to "Pasado"
		#Return true if it was succesful or False if it failed
		return true;
	}

}


?>