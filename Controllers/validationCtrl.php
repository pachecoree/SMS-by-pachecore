<?php

class validationCtrl {

	function __construct () {

	}

	function validate_nrc($nrc) {
		#Check if format was correctly input
		$pattern = '/^[0-9]{5}$/';
		if (preg_match($pattern,$nrc) == 1) {
			return true;
		}	
		#NRC format is not valid
		return false;
	}

	function validate_cicle($cicle) {
		#Check if format was correctly input
		$pattern = '/^[2][0-9]{3}[a-z]$/i';
		if (preg_match($pattern,$cicle) == 1) {
			return true;
		}
		#Cicle format is not valid
		return false;
	}

	function validate_section($section) {
		#Check if format was correctly input
		$pattern = '/^d[0-9]{2}$/i';
		if (preg_match($pattern, $section) == 1) {
			return true;
		}
		#Section format is not valid
		return false;
	}

	function validate_subject($subject) {
		#Check if format was correctly input
		$pattern = '/^[a-z ]{1,}$/i';
		if (preg_match($pattern,$subject) == 1) {
			return true;
		}
		#Subject name is not valid
		return false;
	}

	function validate_phonenumber($phonenumber) {
		#Check if format was correctly input
		$pattern = '/^33([0-9]{2}){4}$/';
		if (preg_match($pattern,$phonenumber) == 1) {
			return true;
		}
		#Phonenumber is not valid
		return false;
	}

	function validate_name($name) {
		#Check if format was correctly input
		$pattern = '/^[a-z ]{1,}$/i';
		if (preg_match($pattern,$name) == 1) {
			return true;
		}
		#Student Name is not valid
		return false;
	}

	function validate_sid($sid) {
		#Check if format was correctly input
		$pattern = '/^[0-9]{9}$/';
		if (preg_match($pattern,$sid) == 1) {
			return true;
		}
		#Student ID is not valid
		return false;
	}

	function validate_web($web) {
		#Check if format was correctly input
		$pattern = '/^[a-z0-9]{1,}([.][a-z0-9]{1,}){1,}([\/.][a-z0-9]{0,}){0,}$/i';
		if (preg_match($pattern,$web) == 1) {
			return true;
		}
		#Student ID is not valid
		return false;
	}

	function validate_email($email) {
		#Check if format was correctly input
		$pattern = '/^([a-z0-9_-]{1,}([.][a-z0-9_-]{1,}){0,}[a-z0-9_-]{0,}){1,}[@][a-z0-9]{1,}([.]([a-z0-9]){1,}){1,}$/i';
		if (preg_match($pattern,$email) == 1) {
			return true;
		}
		#Subject name is not valid
		return false;
	}
	

	function validate_github($github) {
		#Check if format was correctly input
		$pattern = '/^[a-z1-9][a-z1-9-]{1,}$/';
		if (preg_match($pattern,$github) == 1) {
			return true;
		}
		#github Account is not valid
		return false;
	}
}



?>