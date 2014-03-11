<?php

class loginMdl {

	function get_password($password) {
		#Checks is Password is correct
		if (strcmp($password,'1234') == 0)
			return true;
		return false;
	}

	function get_userid($userid) {
		#Checks userid is correct
		if (strcmp($userid, 'pachecore') == 0)
			return true;
		return false;
	}
}
?>